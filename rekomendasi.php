<?php
// rekomendasi.php - Halaman publik (TANPA session)
include "config/koneksi.php";
include "include/spk_functions.php";

$H = spk_hitung($conn);
$pesan = '';

// Urutan TOPSIS
$ids = [];
if(!$H['error']){
    $ids = array_keys($H['alt']);
    usort($ids, fn($a,$b)=>$H['top_skor'][$b] <=> $H['top_skor'][$a]);
}

$kategori_aktif = $_GET['kategori'] ?? 'Semua';
$kategori_list  = ['Semua','Gyoza','Gunkan','Sushi','Rice Mentai','Dimsum'];

// Simpan 10 teratas ke riwayat (tanpa login -> username diisi 'pengunjung')
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan']) && $ids){
    $username = 'pengunjung';
    $kode = 'HIT-' . date('YmdHis');
    $tgl  = date('Y-m-d H:i:s');
    $stmt = mysqli_prepare($conn, "INSERT INTO riwayat_perhitungan (username, kode_perhitungan, nama_menu, nilai_preferensi, ranking, tanggal) VALUES (?,?,?,?,?,?)");
    $r = 0;
    foreach(array_slice($ids, 0, 10) as $id){
        $r++;
        $nama = $H['alt'][$id]['label']; $nilai = (float)$H['top_skor'][$id];
        mysqli_stmt_bind_param($stmt, "sssdis", $username, $kode, $nama, $nilai, $r, $tgl);
        mysqli_stmt_execute($stmt);
    }
    // Post/Redirect/Get supaya refresh tidak menyimpan dua kali
    header("Location: rekomendasi.php?kategori=" . urlencode($kategori_aktif) . "&tersimpan=" . urlencode($kode));
    exit;
}

if(isset($_GET['tersimpan'])){
    $pesan = "Top 10 rekomendasi disimpan ke riwayat (" . $_GET['tersimpan'] . ").";
}

$judul = "Rekomendasi Makanan"; $judul_halaman = "Rekomendasi Makanan";
$subjudul = "Peringkat menu berdasarkan ulasan pelanggan (8 kriteria, metode AHP + TOPSIS).";
include "include/header_publik.php";
?>
<?php if($H['error']): ?>
    <div class="alert alert-warning"><?= htmlspecialchars($H['error']); ?></div>
<?php else: ?>
<?php if($pesan): ?><div class="alert alert-success"><?= htmlspecialchars($pesan); ?></div><?php endif; ?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
    <div class="d-flex gap-2 flex-wrap">
        <?php foreach($kategori_list as $k): ?>
            <a class="btn-kategori <?= $kategori_aktif==$k ? 'active':''; ?>" href="rekomendasi.php?kategori=<?= urlencode($k); ?>"><?= $k; ?></a>
        <?php endforeach; ?>
    </div>
    <form method="post"><button name="simpan" value="1" class="btn-orange"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Top 10 ke Riwayat</button></form>
</div>

<div class="panel"><div class="table-responsive">
<table class="table table-hover align-middle mb-0">
    <thead class="table-light"><tr>
        <th class="text-center" width="80">Rank</th><th>Menu</th><th>Kategori</th>
        <th>Harga</th><th class="text-center">Rata-rata Rating</th><th class="text-center">Nilai Preferensi</th><th></th></tr></thead>
    <tbody>
    <?php $n = 0; $rank = 0;
    foreach($ids as $id):
        $rank++;
        $a = $H['alt'][$id];
        if($kategori_aktif != 'Semua' && $a['kategori'] != $kategori_aktif) continue;
        $n++;
        $rt = array_sum($H['X'][$id]) / count($H['kode']);
        $cls = $rank==1 ? 'rank-1' : ($rank==2 ? 'rank-2' : ($rank==3 ? 'rank-3' : '')); ?>
        <tr>
            <td class="text-center"><span class="rank-badge <?= $cls; ?>"><?= $rank; ?></span></td>
            <td class="fw-bold"><?= htmlspecialchars($a['nama']); ?> <small class="text-muted">#<?= $id; ?></small>
                <?php if($rank==1): ?><i class="fa-solid fa-crown text-warning ms-1"></i><?php endif; ?></td>
            <td><?= htmlspecialchars($a['kategori'] ?? 'Umum'); ?></td>
            <td class="price">Rp <?= number_format($a['harga'], 0, ',', '.'); ?></td>
            <td class="text-center"><i class="fa-solid fa-star text-warning"></i> <?= spk_fmt($rt, 2); ?></td>
            <td class="text-center fw-bold"><?= spk_fmt($H['top_skor'][$id], 4); ?></td>
            <td><a href="detail_menu.php?id=<?= $id; ?>" class="btn btn-sm btn-outline-secondary">Detail</a></td>
        </tr>
    <?php endforeach;
    if($n == 0) echo '<tr><td colspan="7" class="text-center text-muted py-4">Belum ada menu berulasan di kategori ini.</td></tr>'; ?>
    </tbody>
</table></div></div>
<small class="text-muted">Rank adalah peringkat di seluruh <?= count($ids); ?> menu yang memiliki ulasan, bukan hanya di kategori terpilih.</small>
<?php endif; ?>
<?php include "include/footer_publik.php"; ?>