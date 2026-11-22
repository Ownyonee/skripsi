<?php
// index.php - Halaman publik (TANPA session, TANPA login user)
include "config/koneksi.php";

// --- FILTER KATEGORI ---
$kategori_aktif = isset($_GET['kategori']) ? $_GET['kategori'] : 'Semua';

if($kategori_aktif == 'Semua') {
    $query = mysqli_query($conn, "SELECT * FROM daftar_menu ORDER BY nama_menu ASC");
} else {
    $kategori_escaped = mysqli_real_escape_string($conn, $kategori_aktif);
    $query = mysqli_query($conn, "SELECT * FROM daftar_menu WHERE kategori='$kategori_escaped' ORDER BY nama_menu ASC");
}

$total_menu = mysqli_num_rows($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raja Mentai - Daftar Menu Makanan</title>

    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root{
            --primary:#e85d04;
            --primary-2:#f48c06;
            --dark:#21140f;
            --muted:#786b65;
            --cream:#fffaf5;
            --card:#ffffff;
            --line:#f0e4da;
            --soft:#fff1e6;
        }

        *{margin:0;padding:0;box-sizing:border-box}
        html{scroll-behavior:smooth}
        body{
            background:
                radial-gradient(circle at 90% 5%, rgba(244,140,6,.10), transparent 22%),
                linear-gradient(180deg,#fffaf5 0%,#f8f8f7 45%,#fff 100%);
            font-family:'Plus Jakarta Sans',sans-serif;
            color:var(--dark);
        }

        /* SIDEBAR */
        .sidebar{
            width:260px;height:100vh;position:fixed;left:0;top:0;z-index:100;
            padding:28px 18px;color:#fff;display:flex;flex-direction:column;
            background:linear-gradient(180deg,#21140f 0%,#321a10 100%);
            box-shadow:8px 0 35px rgba(33,20,15,.10);
        }
        .sidebar .brand{display:flex;align-items:center;gap:12px;margin:0 8px 38px}
        .sidebar .brand img{
            width:44px;height:44px;border-radius:12px;object-fit:cover;
            border:2px solid rgba(255,255,255,.2);
        }
        .sidebar .brand .name{font-size:18px;font-weight:800;color:#fff;line-height:1.2}
        .sidebar .brand .tag{font-size:11px;color:#bbaaa0;font-weight:500}
        .sidebar a{
            display:flex;align-items:center;gap:13px;color:#d9cbc4;
            text-decoration:none;padding:13px 15px;border-radius:14px;
            margin-bottom:7px;transition:.25s;font-size:14px;font-weight:600;
        }
        .sidebar a i{width:20px;text-align:center}
        .sidebar a:hover{background:rgba(255,255,255,.08);color:#fff;transform:translateX(3px)}
        .sidebar a.active{
            color:#fff;background:linear-gradient(135deg,var(--primary),var(--primary-2));
            box-shadow:0 10px 25px rgba(232,93,4,.25);
        }
        .sidebar .sidebar-bottom{
            margin-top:auto;padding:15px;border:1px solid rgba(255,255,255,.08);
            border-radius:16px;background:rgba(255,255,255,.04);font-size:12px;color:#bbaaa0;
        }

        /* MAIN */
        .main{margin-left:260px;padding:34px 48px 60px;min-height:100vh;width:calc(100% - 260px)}
        .topbar{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:25px}
        .topbar h1{font-weight:800;color:var(--dark);font-size:30px;letter-spacing:-.7px}
        .topbar .subtitle{color:var(--muted);margin-top:6px;font-size:14px}
        .admin-btn{
            padding:11px 17px;background:#fff;color:var(--primary);border:1px solid #f4d8d4;
            border-radius:12px;text-decoration:none;font-weight:700;font-size:13px;
            display:flex;align-items:center;gap:8px;transition:.2s;
        }
        .admin-btn:hover{background:#fff4f2;transform:translateY(-2px);color:var(--primary)}

        /* HERO */
        .banner-gradient{
            background:
                radial-gradient(circle at 85% 10%,rgba(255,183,3,.28),transparent 25%),
                linear-gradient(135deg,#2b1710 0%,#6a2c13 55%,#8d3a0e 100%);
            color:white;border-radius:24px;padding:34px 38px;display:flex;
            justify-content:space-between;align-items:center;gap:25px;margin-bottom:27px;
            box-shadow:0 18px 40px rgba(100,43,15,.14);position:relative;overflow:hidden;
        }
        .banner-gradient::after{
            content:"🍣";position:absolute;right:7%;bottom:-35px;font-size:130px;
            opacity:.10;transform:rotate(-12deg);
        }
        .banner-gradient h2{font-size:25px;font-weight:800;margin-bottom:10px;color:#fff}
        .banner-gradient p{color:#f6ddd0;line-height:1.7;max-width:680px;margin:0;font-size:13px}
        .badge-total{
            position:relative;z-index:2;background:rgba(255,255,255,.10);color:#fff;
            border:1px solid rgba(255,255,255,.18);padding:13px 18px;border-radius:14px;
            font-weight:800;font-size:13px;white-space:nowrap;backdrop-filter:blur(8px);
        }

        /* TOOLBAR */
        .menu-toolbar{display:flex;justify-content:space-between;align-items:center;gap:18px;margin-bottom:17px}
        .search-box-modern{
            flex:1;max-width:520px;display:flex;align-items:center;gap:11px;
            background:#fff;padding:13px 17px;border-radius:14px;border:1px solid var(--line);
            box-shadow:0 6px 18px rgba(65,38,24,.04);
        }
        .search-box-modern i{color:var(--primary)}
        .search-box-modern input{border:none;width:100%;outline:none;font-size:13px;color:#43352e;background:transparent}
        .search-box-modern input::placeholder{color:#a99b93}

        /* CATEGORY */
        .category-filter-container{
            display:flex;gap:9px;margin-bottom:27px;overflow-x:auto;padding:2px 2px 8px;
            scrollbar-width:thin;
        }
        .btn-kategori{
            background:#fff;border:1px solid var(--line);color:#695b53;padding:9px 17px;
            border-radius:30px;font-size:12px;font-weight:800;text-decoration:none;
            transition:.2s;white-space:nowrap;
        }
        .btn-kategori:hover{border-color:#f4b183;color:var(--primary);transform:translateY(-1px)}
        .btn-kategori.active{
            background:linear-gradient(135deg,var(--primary),var(--primary-2));
            color:#fff;border-color:transparent;box-shadow:0 7px 16px rgba(232,93,4,.20)
        }

        /* CARDS */
        .card-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(285px,1fr));gap:22px}
        .menu-card-modern{
            background:var(--card);border:1px solid var(--line);border-radius:20px;
            overflow:hidden;box-shadow:0 7px 20px rgba(65,38,24,.045);
            display:flex;flex-direction:column;transition:.3s;position:relative;
        }
        .menu-card-modern:hover{
            transform:translateY(-7px);box-shadow:0 20px 38px rgba(65,38,24,.12);
            border-color:#f2c5a7;
        }
        .image-container{width:100%;height:210px;background:#f5eee8;position:relative;overflow:hidden}
        .image-container::after{
            content:"";position:absolute;inset:0;
            background:linear-gradient(180deg,transparent 60%,rgba(0,0,0,.12));
            pointer-events:none;
        }
        .menu-card-img{width:100%;height:100%;object-fit:cover;transition:transform .55s ease}
        .menu-card-modern:hover .menu-card-img{transform:scale(1.08)}
        .placeholder-img{
            width:100%;height:100%;display:flex;flex-direction:column;align-items:center;
            justify-content:center;color:#b5a79e;background:linear-gradient(135deg,#fff7ef,#f4ece5)
        }
        .menu-card-body-content{padding:20px 21px 18px;display:flex;flex-direction:column;flex-grow:1}
        .menu-meta{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
        .menu-id{font-size:10px;font-weight:800;color:#a39288;background:#f8f1ec;padding:4px 8px;border-radius:7px}
        .menu-price{font-size:18px;font-weight:900;color:var(--primary)}
        .menu-title{font-size:18px;font-weight:800;color:#271913;margin-bottom:8px;line-height:1.35}
        .menu-desc{
            font-size:12px;color:#81736b;line-height:1.65;margin-bottom:18px;
            display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;flex-grow:1
        }
        .menu-card-footer-action{
            display:flex;align-items:center;justify-content:space-between;border-top:1px solid #f5eee8;
            padding-top:14px;margin-top:auto;gap:10px
        }
        .tag-category{padding:6px 10px;border-radius:8px;background:#fff0e5;color:#b94700;font-size:10px;font-weight:800}
        .btn-view-details{
            text-decoration:none;color:#4d4039;font-weight:800;font-size:12px;transition:.2s;
            display:flex;align-items:center;gap:5px
        }
        .btn-view-details:hover{color:var(--primary);gap:8px}

        /* EMPTY */
        .empty-state-card{
            background:#fff;padding:60px 40px;border-radius:20px;text-align:center;
            border:1px solid var(--line);max-width:500px;margin:40px auto;box-shadow:0 10px 30px rgba(65,38,24,.05)
        }
        .empty-state-card .icon-placeholder{
            width:70px;height:70px;background:#fff0e5;color:var(--primary);border-radius:50%;
            display:inline-flex;align-items:center;justify-content:center;font-size:28px;margin-bottom:18px
        }
        .empty-state-card h2{font-size:20px;font-weight:800;color:var(--dark);margin-bottom:9px}
        .empty-state-card p{color:#81736b;font-size:13px;line-height:1.6}

        @media(max-width:991px){
            .menu-toolbar{flex-direction:column;align-items:stretch}
            .search-box-modern{max-width:100%}
        }
        @media(max-width:768px){
            .sidebar{position:relative;width:100%;height:auto;min-height:auto;padding:18px}
            .sidebar .brand{margin-bottom:16px}
            .sidebar .sidebar-bottom{display:none}
            .main{margin-left:0;padding:25px 17px 45px;width:100%}
            .topbar{align-items:flex-start}
            .topbar h1{font-size:25px}
            .admin-btn{padding:9px 12px}
            .banner-gradient{flex-direction:column;align-items:flex-start;padding:27px 24px}
            .banner-gradient h2{font-size:21px}
            .banner-gradient::after{right:-8px;font-size:95px}
            .card-grid{grid-template-columns:1fr}
        }
        @media(max-width:480px){
            .topbar{flex-direction:column}
            .admin-btn{width:100%;justify-content:center}
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="brand">
            <img src="images/logo.jpeg" alt="Raja Mentai">
            <div>
                <div class="name">Raja Mentai</div>
                <div class="tag">Menu mentai paling pas buatmu</div>
            </div>
        </div>

        <a href="index.php" class="active"><i class="fa-solid fa-house"></i> Daftar Menu</a>
        <a href="rekomendasi.php"><i class="fa-solid fa-utensils"></i> Rekomendasi Makanan</a>
        <a href="riwayat_perhitungan.php"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Perhitungan</a>
        <a href="login_admin.php"><i class="fa-solid fa-user-shield"></i> Login Admin</a>

        <div class="sidebar-bottom">
            <i class="fa-solid fa-heart" style="color:#ffb703"></i>
            Pilih makanan favoritmu dan nikmati pengalaman kuliner yang lebih mudah.
        </div>
    </div>

    <div class="main">

        <div class="topbar">
            <div>
                <h1>Menu Makanan</h1>
                <p class="subtitle">Pilih menu favoritmu dan lihat informasi lengkap setiap makanan.</p>
            </div>
            <a href="login_admin.php" class="admin-btn">
                <i class="fa-solid fa-user-shield"></i> Login Admin
            </a>
        </div>

        <div class="banner-gradient">
            <div>
                <h2>Koleksi Menu Lezat untuk Kamu</h2>
                <p>Temukan rekomendasi makanan berdasarkan nilai rasa, harga, porsi, dan popularitas. Semua menu tersedia lengkap di sini untuk membantu kamu memilih dengan mudah.</p>
            </div>
            <div class="badge-total">
                <i class="fa-solid fa-layer-group me-1"></i> <?php echo $total_menu; ?> Menu Ditemukan
            </div>
        </div>

        <div class="menu-toolbar">
            <div class="search-box-modern">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="menuSearch" placeholder="Cari nama makanan..." autocomplete="off">
            </div>
        </div>

        <div class="category-filter-container">
            <a href="index.php?kategori=Semua" class="btn-kategori <?= $kategori_aktif == 'Semua' ? 'active' : ''; ?>">Semua Menu</a>
            <a href="index.php?kategori=Gyoza" class="btn-kategori <?= $kategori_aktif == 'Gyoza' ? 'active' : ''; ?>">Gyoza</a>
            <a href="index.php?kategori=Gunkan" class="btn-kategori <?= $kategori_aktif == 'Gunkan' ? 'active' : ''; ?>">Gunkan</a>
            <a href="index.php?kategori=Sushi" class="btn-kategori <?= $kategori_aktif == 'Sushi' ? 'active' : ''; ?>">Sushi</a>
            <a href="index.php?kategori=Rice Mentai" class="btn-kategori <?= $kategori_aktif == 'Rice Mentai' ? 'active' : ''; ?>">Rice Mentai</a>
            <a href="index.php?kategori=Dimsum" class="btn-kategori <?= $kategori_aktif == 'Dimsum' ? 'active' : ''; ?>">Dimsum</a>
        </div>

        <?php if($total_menu > 0): ?>
        <div class="card-grid">
            <?php while($row = mysqli_fetch_assoc($query)): ?>
            <div class="menu-card-modern menu-item-card" data-menu-name="<?= htmlspecialchars(strtolower($row['nama_menu'])); ?>">

                <div class="image-container">
                    <?php if(!empty($row['gambar'])): ?>
                        <img src="<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['nama_menu']); ?>" class="menu-card-img">
                    <?php else: ?>
                        <div class="placeholder-img">
                            <i class="fa-solid fa-bowl-food fa-2x mb-2"></i>
                            <span style="font-size:12px;font-weight:500;">Tidak ada gambar</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="menu-card-body-content">
                    <div class="menu-meta">
                        <span class="menu-id">ID: #<?= (int)$row['id']; ?></span>
                        <span class="menu-price">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></span>
                    </div>

                    <div class="menu-title"><?= htmlspecialchars($row['nama_menu']); ?></div>
                    <div class="menu-desc"><?= nl2br(htmlspecialchars($row['deskripsi'])); ?></div>

                    <div class="menu-card-footer-action">
                        <span class="tag-category">
                            <i class="fa-solid fa-utensils me-1"></i>
                            <?= htmlspecialchars($row['kategori'] ?? 'Umum'); ?>
                        </span>
                        <a href="detail_menu.php?id=<?= (int)$row['id']; ?>" class="btn-view-details">
                            Detail & Review <i class="fa-solid fa-arrow-right-long"></i>
                        </a>
                    </div>
                </div>

            </div>
            <?php endwhile; ?>
        </div>

        <?php else: ?>
        <div class="empty-state-card">
            <div class="icon-placeholder">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <h2>Tidak Ada Menu Ditemukan</h2>
            <p>Data makanan untuk kategori <strong>"<?= htmlspecialchars($kategori_aktif); ?>"</strong> belum tersedia saat ini.</p>
            <a href="index.php" class="btn btn-primary mt-3" style="border-radius:10px;">Lihat Semua Menu</a>
        </div>
        <?php endif; ?>

        <div id="searchEmpty" class="empty-state-card" style="display:none;">
            <div class="icon-placeholder">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <h2>Menu Tidak Ditemukan</h2>
            <p>Coba gunakan kata kunci lain untuk mencari makanan favoritmu.</p>
        </div>

    </div>

    <script>
        const searchInput = document.getElementById('menuSearch');
        const menuCards = document.querySelectorAll('.menu-item-card');
        const searchEmpty = document.getElementById('searchEmpty');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const keyword = this.value.toLowerCase().trim();
                let visible = 0;

                menuCards.forEach(card => {
                    const name = card.dataset.menuName || '';
                    const show = name.includes(keyword);
                    card.style.display = show ? '' : 'none';
                    if (show) visible++;
                });

                if (searchEmpty) {
                    searchEmpty.style.display = (keyword && visible === 0) ? 'block' : 'none';
                }
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>