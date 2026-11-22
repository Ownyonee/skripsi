<?php
// detail_menu.php - Halaman publik (TANPA session, TANPA login user)
include "config/koneksi.php";

// Cek ID Menu
if(!isset($_GET['id'])){
    header("Location: index.php");
    exit;
}
$menu_id = intval($_GET['id']);

// Ambil data menu
$stmt = mysqli_prepare($conn, "SELECT * FROM daftar_menu WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $menu_id);
mysqli_stmt_execute($stmt);
$menu = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if(!$menu){
    header("Location: index.php");
    exit;
}

// Daftar kriteria: kunci => [label, [pilihan nilai 5..1], ikon]
$kriteria = [
    'harga'        => ['Harga', ['Sangat Terjangkau','Terjangkau','Standar','Mahal','Sangat Mahal'], 'fa-tag text-success'],
    'rasa'         => ['Rasa', ['Sangat Enak','Enak','Biasa','Kurang','Tidak Enak'], 'fa-utensils text-warning'],
    'porsi'        => ['Porsi', ['Sangat Banyak','Banyak','Sedang','Sedikit','Sangat Sedikit'], 'fa-box-open text-primary'],
    'popularitas'  => ['Popularitas', ['Sangat Populer','Populer','Biasa','Kurang Populer','Tidak Dikenal'], 'fa-fire text-danger'],
    'tekstur'      => ['Tekstur', ['Sangat Baik','Baik','Cukup','Kurang','Sangat Kurang'], 'fa-layer-group text-info'],
    'variasi'      => ['Variasi Menu', ['Sangat Variatif','Variatif','Cukup','Kurang','Tidak Variatif'], 'fa-bars-staggered text-secondary'],
    'kepraktisan'  => ['Kepraktisan', ['Sangat Praktis','Praktis','Cukup','Kurang Praktis','Ribet'], 'fa-bolt text-warning'],
    'value'        => ['Value for Money', ['Sangat Sepadan','Sepadan','Cukup','Kurang Sepadan','Tidak Sepadan'], 'fa-gem text-success'],
];

// Proses Simpan Review
$pesan = '';
if(isset($_GET['status']) && $_GET['status'] === 'sukses'){
    $pesan = '<div class="alert alert-success">Terima kasih! Ulasan beserta penilaian kriteria Anda berhasil dikirim.</div>';
}

if(isset($_POST['kirim_review'])){
    // Nama pengulas (tanpa login) - boleh dikosongkan
    $nama = trim($_POST['nama'] ?? '');
    if($nama === '') $nama = 'Anonim';
    $nama = substr($nama, 0, 50);

    // Ambil 8 nilai kriteria, dibatasi 1..5
    $n = [];
    foreach(array_keys($kriteria) as $k){
        $n[$k] = max(1, min(5, intval($_POST['rating_'.$k] ?? 0)));
    }
    $rating_overall = (int) round(array_sum($n) / 8);
    $komentar = trim($_POST['komentar'] ?? '');

    $ins = mysqli_prepare($conn, "INSERT INTO ulasan
        (menu_id, username, rating, rating_harga, rating_rasa, rating_porsi, rating_popularitas, rating_tekstur, rating_variasi, rating_kepraktisan, rating_value, komentar)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
    mysqli_stmt_bind_param($ins, "isiiiiiiiiis",
        $menu_id, $nama, $rating_overall,
        $n['harga'], $n['rasa'], $n['porsi'], $n['popularitas'],
        $n['tekstur'], $n['variasi'], $n['kepraktisan'], $n['value'],
        $komentar
    );

    if(mysqli_stmt_execute($ins)){
        // Post/Redirect/Get supaya refresh tidak mengirim ulasan dua kali
        header("Location: detail_menu.php?id=$menu_id&status=sukses#ulasan");
        exit;
    } else {
        $pesan = '<div class="alert alert-danger">Gagal mengirim ulasan. Pastikan tabel database sudah diupdate.</div>';
    }
}

// Hitung Rata-rata Rating Keseluruhan
$rata_rata = 0;
$total_review = 0;
$query_rating = mysqli_query($conn, "SELECT AVG(rating) as rata_rata, COUNT(id) as total_review FROM ulasan WHERE menu_id=$menu_id");
if($query_rating){
    $data_rating = mysqli_fetch_assoc($query_rating);
    $rata_rata = $data_rating['rata_rata'] ? round($data_rating['rata_rata'], 1) : 0;
    $total_review = $data_rating['total_review'];
}

// CSS khusus halaman detail
$extra_head = <<<'HTML'
<style>
    .back-btn{color:#695b53!important;font-weight:800;font-size:13px;transition:.2s;text-decoration:none}
    .back-btn:hover{color:var(--primary)!important}
    .card-modern{background:#fff;border:1px solid var(--line);border-radius:20px;box-shadow:0 8px 24px rgba(65,38,24,.05);margin-bottom:22px;overflow:hidden}
    .food-image-wrap{height:360px;background:#f5eee8;position:relative;overflow:hidden}
    .img-detail{width:100%;height:100%;object-fit:cover;display:block;transition:transform .5s ease}
    .card-modern:hover .img-detail{transform:scale(1.03)}
    .food-image-wrap::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,transparent 55%,rgba(33,20,15,.18));pointer-events:none}
    .category-pill{position:absolute;left:18px;top:18px;z-index:2;padding:7px 12px;border-radius:30px;background:rgba(255,255,255,.92);color:#b94700;font-size:11px;font-weight:800;backdrop-filter:blur(8px)}
    .detail-content{padding:24px}
    .menu-name{font-size:25px;font-weight:900;color:var(--dark);margin-bottom:5px}
    .menu-price{font-size:21px;font-weight:900;color:var(--primary);margin-bottom:9px}
    .star-rating{color:#f59e0b}
    .rating-summary{display:inline-flex;align-items:center;gap:8px;background:#fff7ef;padding:7px 11px;border-radius:10px;margin-bottom:17px;font-size:12px}
    .description{color:#81736b;line-height:1.75;font-size:13px;padding-top:16px;border-top:1px solid #f5eee8}
    .section-title{font-size:17px;font-weight:900;color:var(--dark);margin-bottom:5px}
    .section-desc{font-size:12px;color:#94847b;margin-bottom:20px}
    .title-icon{width:35px;height:35px;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;background:#fff0e5;color:var(--primary);margin-right:8px}
    .label-kriteria{font-size:11px!important;color:#66584f!important;font-weight:800!important;margin-bottom:7px}
    .form-select,.form-control{border:1px solid var(--line);border-radius:11px;padding:10px 12px;font-size:12px;color:#493a32;background-color:#fff}
    .form-select:focus,.form-control:focus{border-color:#f4b183;box-shadow:0 0 0 3px rgba(232,93,4,.09)}
    .btn-primary-custom{background:linear-gradient(135deg,var(--primary),var(--primary-2));color:#fff;border:none;padding:12px 20px;border-radius:12px;font-weight:800;font-size:13px;box-shadow:0 8px 18px rgba(232,93,4,.20);transition:.2s}
    .btn-primary-custom:hover{color:#fff;transform:translateY(-2px);box-shadow:0 12px 24px rgba(232,93,4,.28)}
    .review-box{background:#fffaf5;border:1px solid var(--line);border-radius:15px;padding:17px;margin-bottom:13px;transition:.2s}
    .review-box:hover{border-color:#efc5a9;box-shadow:0 7px 18px rgba(65,38,24,.04)}
    .review-user{font-weight:800;color:var(--dark);font-size:13px}
    .review-date{font-size:10px;color:#9a8b82}
    .review-text{margin-top:10px;font-size:12px;color:#594b43;line-height:1.7;background:#fff;border-radius:10px;padding:10px 12px;border:1px solid #f4e9e1}
    .badge-kriteria{font-size:10px;font-weight:800;padding:5px 8px;border-radius:8px;border:1px solid var(--line);background:#fff;color:#62544c}
    .review-empty{background:#fffaf5;border:1px dashed #e6cfc0;border-radius:15px;padding:35px 20px}
    @media(max-width:991px){.food-image-wrap{height:300px}}
    @media(max-width:768px){.detail-content{padding:20px}.food-image-wrap{height:260px}}
</style>
HTML;

$judul = "Detail - " . $menu['nama_menu'];
$judul_halaman = "Detail & Penilaian Menu";
$subjudul = "Kenali menu lebih dekat dan bagikan pengalamanmu.";
include "include/header_publik.php";
?>

<a href="index.php" class="back-btn d-inline-block mb-3">
    <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Menu
</a>

<?= $pesan; ?>

<div class="row">
    <div class="col-lg-4">
        <div class="card-modern">
            <div class="food-image-wrap">
                <?php if(!empty($menu['gambar'])): ?>
                    <img src="<?= htmlspecialchars($menu['gambar']); ?>" class="img-detail" alt="<?= htmlspecialchars($menu['nama_menu']); ?>">
                <?php else: ?>
                    <div class="h-100 d-flex align-items-center justify-content-center text-muted" style="background:linear-gradient(135deg,#fff7ef,#f4ece5);">
                        <div class="text-center">
                            <i class="fa-solid fa-bowl-food fa-3x mb-3"></i>
                            <div style="font-size:12px;font-weight:700;">Belum ada gambar</div>
                        </div>
                    </div>
                <?php endif; ?>
                <span class="category-pill">
                    <i class="fa-solid fa-utensils me-1"></i>
                    <?= htmlspecialchars($menu['kategori'] ?? 'Menu'); ?>
                </span>
            </div>

            <div class="detail-content">
                <div class="menu-name"><?= htmlspecialchars($menu['nama_menu']); ?></div>
                <div class="menu-price">Rp <?= number_format($menu['harga'], 0, ',', '.'); ?></div>

                <div class="rating-summary">
                    <span class="star-rating"><i class="fa-solid fa-star"></i> <strong><?= $rata_rata; ?></strong></span>
                    <span class="text-muted"><?= $total_review; ?> ulasan pelanggan</span>
                </div>

                <div class="description"><?= nl2br(htmlspecialchars($menu['deskripsi'])); ?></div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">

        <div class="card-modern p-4">
            <div class="section-title">
                <span class="title-icon"><i class="fa-solid fa-pen-to-square"></i></span>
                Form Penilaian Kriteria Menu
            </div>
            <div class="section-desc">Berikan nilai berdasarkan pengalamanmu. Penilaianmu membantu pengguna lain memilih menu.</div>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary label-kriteria">Nama (opsional)</label>
                    <input type="text" name="nama" class="form-control form-control-sm" maxlength="50" placeholder="Kosongkan untuk tampil sebagai Anonim">
                </div>

                <div class="row">
                    <?php foreach($kriteria as $key => [$label, $opsi]): ?>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold text-secondary label-kriteria"><?= $label; ?></label>
                        <select name="rating_<?= $key; ?>" class="form-select form-select-sm" required>
                            <option value="">Pilih...</option>
                            <?php foreach($opsi as $i => $teks): ?>
                                <option value="<?= 5 - $i; ?>"><?= 5 - $i; ?> - <?= $teks; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary label-kriteria">Komentar Tambahan</label>
                    <textarea name="komentar" class="form-control form-control-sm" rows="2" placeholder="Tuliskan pengalamanmu mengenai menu ini..." required></textarea>
                </div>
                <button type="submit" name="kirim_review" class="btn-primary-custom w-100">Kirim Penilaian</button>
            </form>
        </div>

        <div class="card-modern p-4" id="ulasan">
            <div class="section-title mb-4">
                <span class="title-icon"><i class="fa-solid fa-comments"></i></span>
                Ulasan Pengguna
            </div>

            <?php
            $query_ulasan = mysqli_query($conn, "SELECT * FROM ulasan WHERE menu_id=$menu_id ORDER BY tanggal DESC");

            if($query_ulasan && mysqli_num_rows($query_ulasan) > 0):
                while($ulasan = mysqli_fetch_assoc($query_ulasan)):
            ?>
                <div class="review-box">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="review-user"><i class="fa-solid fa-circle-user text-secondary me-1"></i> <?= htmlspecialchars($ulasan['username']); ?></span>
                        <div class="star-rating" style="font-size:13px;">
                            <?php for($i=1; $i<=5; $i++){
                                echo $i <= $ulasan['rating'] ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
                            } ?>
                        </div>
                    </div>

                    <?php if(isset($ulasan['rating_harga'])): ?>
                    <div class="mt-2 mb-3 d-flex flex-wrap gap-2">
                        <?php foreach($kriteria as $key => [$label, $opsi, $ikon]): ?>
                            <span class="badge-kriteria" title="<?= $label; ?>"><i class="fa-solid <?= $ikon; ?>"></i> <?= (int)$ulasan['rating_'.$key]; ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="review-text">"<?= nl2br(htmlspecialchars($ulasan['komentar'])); ?>"</div>
                    <div class="review-date mt-2"><?= date('d M Y H:i', strtotime($ulasan['tanggal'])); ?></div>
                </div>
            <?php
                endwhile;
            else:
            ?>
                <div class="review-empty text-center">
                    <i class="fa-regular fa-comment-dots fa-3x mb-3" style="color:#e8a477;"></i>
                    <p class="mb-0" style="font-size:12px;color:#81736b;">
                        Belum ada ulasan untuk menu ini.<br>
                        <strong>Jadilah yang pertama memberikan penilaian!</strong>
                    </p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include "include/footer_publik.php"; ?>