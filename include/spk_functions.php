<?php
/**
 * Satu-satunya sumber perhitungan SPK (AHP + TOPSIS).
 * Dipanggil oleh perhitungan_ahp.php, perhitungan_topsis.php, hasil_akhir.php
 * supaya hasil di ketiga halaman selalu sama.
 *
 * Asumsi tabel:
 *  daftar_menu(id, nama_menu, harga, kategori, ...)
 *  ulasan(id, menu_id, rating_harga, rating_rasa, rating_porsi, rating_popularitas,
 *         rating_tekstur, rating_variasi, rating_kepraktisan, rating_value, ...)
 *  kriteria(kode_kriteria, nama_kriteria, tipe)
 */

const SPK_MAP = [
    'C1' => 'rating_harga',      'C2' => 'rating_rasa',
    'C3' => 'rating_porsi',      'C4' => 'rating_popularitas',
    'C5' => 'rating_tekstur',    'C6' => 'rating_variasi',
    'C7' => 'rating_kepraktisan','C8' => 'rating_value',
];

// Random Index Saaty (n = 1..10)
const SPK_RI = [1=>0, 2=>0, 3=>0.58, 4=>0.90, 5=>1.12, 6=>1.24, 7=>1.32, 8=>1.41, 9=>1.45, 10=>1.49];

function spk_valid_sql() {
    // baris ulasan yang seluruh ratingnya 0 dianggap data kosong/uji
    return "(" . implode(' + ', array_values(SPK_MAP)) . ") > 0";
}

function spk_rank($scores) {
    // rank 1 = skor tertinggi; skor kembar mendapat rata-rata rank
    arsort($scores);
    $keys = array_keys($scores); $vals = array_values($scores);
    $n = count($keys); $rank = []; $i = 0;
    while ($i < $n) {
        $j = $i;
        while ($j + 1 < $n && abs($vals[$j + 1] - $vals[$i]) < 1e-12) $j++;
        $r = ($i + $j) / 2 + 1;
        for ($k = $i; $k <= $j; $k++) $rank[$keys[$k]] = $r;
        $i = $j + 1;
    }
    return $rank;
}

function spk_pearson($a, $b) {
    $n = count($a); if ($n < 2) return null;
    $ma = array_sum($a) / $n; $mb = array_sum($b) / $n;
    $num = 0; $da = 0; $db = 0;
    for ($i = 0; $i < $n; $i++) {
        $num += ($a[$i] - $ma) * ($b[$i] - $mb);
        $da  += pow($a[$i] - $ma, 2);
        $db  += pow($b[$i] - $mb, 2);
    }
    return ($da > 0 && $db > 0) ? $num / sqrt($da * $db) : null;
}

function spk_hitung($conn) {
    $out = ['error' => null];

    // ---- 1. Kriteria ----
    $kriteria = [];
    $q = mysqli_query($conn, "SELECT * FROM kriteria ORDER BY kode_kriteria ASC");
    while ($r = mysqli_fetch_assoc($q)) {
        $k = strtoupper($r['kode_kriteria']);
        if (isset(SPK_MAP[$k])) $kriteria[$k] = ['nama' => $r['nama_kriteria'], 'tipe' => strtolower($r['tipe'])];
    }
    $kode = array_keys($kriteria); $n = count($kode);
    if ($n < 2) { $out['error'] = 'Data kriteria belum lengkap.'; return $out; }

    // ---- 2. Rata-rata rating tiap kriteria (seluruh ulasan valid) ----
    $sel = [];
    foreach (SPK_MAP as $c => $col) $sel[] = "AVG(NULLIF($col,0)) AS $c";
    $q = mysqli_query($conn, "SELECT COUNT(*) AS total, " . implode(',', $sel) . " FROM ulasan WHERE " . spk_valid_sql());
    $row = mysqli_fetch_assoc($q);
    $out['total_ulasan'] = (int)$row['total'];
    $avg = [];
    foreach ($kode as $c) $avg[$c] = (float)$row[$c];
    if ($out['total_ulasan'] == 0) { $out['error'] = 'Belum ada ulasan valid.'; return $out; }
    $out['avg_kriteria'] = $avg;

    // ---- 3. Konversi selisih rata-rata -> skala Saaty (min-max), matriks berpasangan ----
    $min = min($avg); $max = max($avg); $range = $max - $min;
    $A = [];
    foreach ($kode as $i) {
        foreach ($kode as $j) {
            if ($i === $j || $range <= 0) { $A[$i][$j] = 1; continue; }
            $d = $avg[$i] - $avg[$j];
            $s = round(1 + 8 * abs($d) / $range);
            $s = max(1, min(9, $s));
            $A[$i][$j] = ($d >= 0) ? $s : 1 / $s;
        }
    }
    $col = [];
    foreach ($kode as $j) { $col[$j] = 0; foreach ($kode as $i) $col[$j] += $A[$i][$j]; }
    $N = []; $w = [];
    foreach ($kode as $i) {
        $sum = 0;
        foreach ($kode as $j) { $N[$i][$j] = $A[$i][$j] / $col[$j]; $sum += $N[$i][$j]; }
        $w[$i] = $sum / $n;
    }
    // konsistensi
    $lambda = 0;
    foreach ($kode as $i) {
        $aw = 0; foreach ($kode as $j) $aw += $A[$i][$j] * $w[$j];
        $lambda += $aw / $w[$i];
    }
    $lambda /= $n;
    $CI = ($lambda - $n) / ($n - 1);
    $RI = SPK_RI[$n] ?? 1.49;
    $CR = $RI > 0 ? $CI / $RI : 0;
    $out += ['kode' => $kode, 'kriteria' => $kriteria, 'A' => $A, 'N' => $N, 'w' => $w,
             'lambda' => $lambda, 'CI' => $CI, 'RI' => $RI, 'CR' => $CR, 'konsisten' => $CR <= 0.1];

    // ---- 4. Matriks keputusan: rata-rata rating per menu ----
    $sel = [];
    foreach (SPK_MAP as $c => $colname) $sel[] = "AVG(NULLIF(u.$colname,0)) AS $c";
    $sql = "SELECT m.id, m.nama_menu, m.kategori, m.harga, COUNT(u.id) AS jml, " . implode(',', $sel) . "
            FROM daftar_menu m JOIN ulasan u ON u.menu_id = m.id
            WHERE " . str_replace('rating_', 'u.rating_', spk_valid_sql()) . "
            GROUP BY m.id, m.nama_menu, m.kategori, m.harga ORDER BY m.id";
    $q = mysqli_query($conn, $sql);
    if (!$q) { $out['error'] = mysqli_error($conn); return $out; }
    $alt = []; $X = [];
    while ($r = mysqli_fetch_assoc($q)) {
        $ok = true; foreach ($kode as $c) if ($r[$c] === null) $ok = false;
        if (!$ok) continue;
        $id = $r['id'];
        $alt[$id] = ['label' => $r['nama_menu'] . ' (ID ' . $id . ')', 'nama' => $r['nama_menu'],
                     'kategori' => $r['kategori'], 'harga' => $r['harga'], 'jml' => (int)$r['jml']];
        foreach ($kode as $c) $X[$id][$c] = (float)$r[$c];
    }
    if (count($alt) < 2) { $out['error'] = 'Minimal 2 menu dengan ulasan.'; return $out; }
    $out['alt'] = $alt; $out['X'] = $X;

    // ---- 5. Ranking AHP (sintesis: sum bobot x nilai ternormalisasi) ----
    $cs = [];
    foreach ($kode as $c) {
        $cs[$c] = 0;
        foreach ($X as $id => $v) $cs[$c] += ($kriteria[$c]['tipe'] === 'cost') ? 1 / $v[$c] : $v[$c];
    }
    $ahpR = []; $ahp = [];
    foreach ($X as $id => $v) {
        $s = 0;
        foreach ($kode as $c) {
            $x = ($kriteria[$c]['tipe'] === 'cost') ? 1 / $v[$c] : $v[$c];
            $ahpR[$id][$c] = $x / $cs[$c];
            $s += $w[$c] * $ahpR[$id][$c];
        }
        $ahp[$id] = $s;
    }
    $out['ahp_norm'] = $ahpR; $out['ahp_skor'] = $ahp; $out['ahp_rank'] = spk_rank($ahp);

    // ---- 6. TOPSIS ----
    $div = [];
    foreach ($kode as $c) { $ss = 0; foreach ($X as $v) $ss += $v[$c] * $v[$c]; $div[$c] = sqrt($ss); }
    $R = []; $Y = [];
    foreach ($X as $id => $v) foreach ($kode as $c) {
        $R[$id][$c] = $div[$c] > 0 ? $v[$c] / $div[$c] : 0;
        $Y[$id][$c] = $R[$id][$c] * $w[$c];
    }
    $Ap = []; $Am = [];
    foreach ($kode as $c) {
        $vals = array_column($Y, $c);
        if ($kriteria[$c]['tipe'] === 'cost') { $Ap[$c] = min($vals); $Am[$c] = max($vals); }
        else { $Ap[$c] = max($vals); $Am[$c] = min($vals); }
    }
    $top = []; $dp = []; $dm = [];
    foreach ($Y as $id => $v) {
        $sp = 0; $sm = 0;
        foreach ($kode as $c) { $sp += pow($v[$c] - $Ap[$c], 2); $sm += pow($v[$c] - $Am[$c], 2); }
        $dp[$id] = sqrt($sp); $dm[$id] = sqrt($sm);
        $top[$id] = ($dp[$id] + $dm[$id]) > 0 ? $dm[$id] / ($dp[$id] + $dm[$id]) : 0;
    }
    $out += ['top_R' => $R, 'top_Y' => $Y, 'Ap' => $Ap, 'Am' => $Am, 'dp' => $dp, 'dm' => $dm,
             'top_skor' => $top, 'top_rank' => spk_rank($top)];

    // ---- 7. Perbandingan: Spearman ----
    $ids = array_keys($X); $ra = []; $rt = [];
    foreach ($ids as $id) { $ra[] = $out['ahp_rank'][$id]; $rt[] = $out['top_rank'][$id]; }
    $out['spearman'] = spk_pearson($ra, $rt);
    $sama = 0; foreach ($ids as $id) if ($out['ahp_rank'][$id] == $out['top_rank'][$id]) $sama++;
    $out['rank_sama'] = $sama;
    return $out;
}

function spk_fmt($v, $d = 4) { return number_format((float)$v, $d, ',', '.'); }