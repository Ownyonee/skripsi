<?php
// riwayat_perhitungan.php - Halaman publik (TANPA session)
include "config/koneksi.php";

$kode = $_GET['kode'] ?? '';

$judul = "Riwayat Perhitungan"; $judul_halaman = "Riwayat Perhitungan";
$subjudul = "Rekomendasi yang pernah disimpan.";
include "include/header_publik.php";

if($kode !== ''):
    $stmt = mysqli_prepare($conn, "SELECT nama_menu, nilai_preferensi, ranking, tanggal FROM riwayat_perhitungan WHERE kode_perhitungan=? ORDER BY ranking ASC");
    mysqli_stmt_bind_param($stmt, "s", $kode);
    mysqli_stmt_execute($stmt);
    $res  = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
?>
    <a href="riwayat_perhitungan.php" class="btn btn-sm btn-outline-secondary mb-3"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
    <div class="panel"><div class="p-3 fw-bold"><?= htmlspecialchars($kode); ?>
        <?php if($rows): ?><small class="text-muted ms-2"><?= date('d M Y H:i', strtotime($rows[0]['tanggal'])); ?></small><?php endif; ?></div>
    <div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th class="text-center" width="80">Rank</th><th>Menu</th><th class="text-center">Nilai Preferensi</th></tr></thead>
        <tbody>
        <?php foreach($rows as $r): $cls = $r['ranking']==1?'rank-1':($r['ranking']==2?'rank-2':($r['ranking']==3?'rank-3':'')); ?>
            <tr><td class="text-center"><span class="rank-badge <?= $cls; ?>"><?= (int)$r['ranking']; ?></span></td>
                <td class="fw-bold"><?= htmlspecialchars($r['nama_menu']); ?></td>
                <td class="text-center"><?= number_format($r['nilai_preferensi'], 4, ',', '.'); ?></td></tr>
        <?php endforeach; if(!$rows) echo '<tr><td colspan="3" class="text-center text-muted py-4">Data tidak ditemukan.</td></tr>'; ?>
        </tbody></table></div></div>
<?php else:
    $res = mysqli_query($conn, "SELECT kode_perhitungan, MIN(tanggal) AS tgl, COUNT(*) AS jml,
        MAX(CASE WHEN ranking=1 THEN nama_menu END) AS juara
        FROM riwayat_perhitungan GROUP BY kode_perhitungan ORDER BY tgl DESC");
    $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
?>
    <div class="panel"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Kode</th><th>Tanggal</th><th>Peringkat 1</th><th class="text-center">Menu Tersimpan</th><th></th></tr></thead>
        <tbody>
        <?php foreach($rows as $r): ?>
            <tr><td class="fw-bold"><?= htmlspecialchars($r['kode_perhitungan']); ?></td>
                <td><?= date('d M Y H:i', strtotime($r['tgl'])); ?></td>
                <td><?= htmlspecialchars($r['juara'] ?? '-'); ?></td>
                <td class="text-center"><?= (int)$r['jml']; ?></td>
                <td><a class="btn btn-sm btn-outline-secondary" href="riwayat_perhitungan.php?kode=<?= urlencode($r['kode_perhitungan']); ?>">Lihat</a></td></tr>
        <?php endforeach;
        if(!$rows) echo '<tr><td colspan="5" class="text-center text-muted py-4">Belum ada riwayat. Simpan dari halaman <a href="rekomendasi.php">Rekomendasi Makanan</a>.</td></tr>'; ?>
        </tbody></table></div></div>
<?php endif; ?>
<?php include "include/footer_publik.php"; ?>