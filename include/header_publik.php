<?php
// include/header_publik.php - header + sidebar untuk halaman publik (TANPA session)
$halaman       = basename($_SERVER['PHP_SELF']);
$judul         = $judul ?? 'Raja Mentai';
$judul_halaman = $judul_halaman ?? $judul;
$subjudul      = $subjudul ?? '';
$extra_head    = $extra_head ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($judul); ?> - Raja Mentai</title>

    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root{
            --primary:#e85d04; --primary-2:#f48c06; --dark:#21140f; --muted:#786b65;
            --cream:#fffaf5; --line:#f0e4da; --soft:#fff1e6;
        }
        *{margin:0;padding:0;box-sizing:border-box}
        html{scroll-behavior:smooth}
        body{
            background:
                radial-gradient(circle at 90% 5%, rgba(244,140,6,.10), transparent 22%),
                linear-gradient(180deg,#fffaf5 0%,#f8f8f7 45%,#fff 100%);
            font-family:'Plus Jakarta Sans',sans-serif;color:var(--dark);
        }

        /* SIDEBAR */
        .sidebar{
            width:260px;height:100vh;position:fixed;left:0;top:0;z-index:100;
            padding:28px 18px;color:#fff;display:flex;flex-direction:column;
            background:linear-gradient(180deg,#21140f 0%,#321a10 100%);
            box-shadow:8px 0 35px rgba(33,20,15,.10);
        }
        .sidebar .brand{display:flex;align-items:center;gap:12px;margin:0 8px 38px}
        .sidebar .brand img{width:44px;height:44px;border-radius:12px;object-fit:cover;border:2px solid rgba(255,255,255,.2)}
        .sidebar .brand .name{font-size:18px;font-weight:800;color:#fff;line-height:1.2}
        .sidebar .brand .tag{font-size:11px;color:#bbaaa0;font-weight:500}
        .sidebar a{
            display:flex;align-items:center;gap:13px;color:#d9cbc4;text-decoration:none;
            padding:13px 15px;border-radius:14px;margin-bottom:7px;transition:.25s;font-size:14px;font-weight:600;
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
        .main{margin-left:260px;padding:34px 48px 60px;min-height:100vh}
        .topbar{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:25px}
        .topbar h1{font-weight:800;color:var(--dark);font-size:28px;letter-spacing:-.7px}
        .topbar .subtitle{color:var(--muted);margin-top:6px;font-size:14px}
        .admin-btn{
            padding:11px 17px;background:#fff;color:var(--primary);border:1px solid #f4d8d4;
            border-radius:12px;text-decoration:none;font-weight:700;font-size:13px;
            display:flex;align-items:center;gap:8px;transition:.2s;white-space:nowrap;
        }
        .admin-btn:hover{background:#fff4f2;transform:translateY(-2px);color:var(--primary)}

        /* KOMPONEN UMUM */
        .panel{background:#fff;border:1px solid var(--line);border-radius:20px;box-shadow:0 8px 24px rgba(65,38,24,.05);overflow:hidden;margin-bottom:18px}
        .btn-orange{
            background:linear-gradient(135deg,var(--primary),var(--primary-2));color:#fff;border:none;
            padding:11px 18px;border-radius:12px;font-weight:800;font-size:13px;
            box-shadow:0 8px 18px rgba(232,93,4,.20);transition:.2s;
        }
        .btn-orange:hover{color:#fff;transform:translateY(-2px)}
        .btn-kategori{
            background:#fff;border:1px solid var(--line);color:#695b53;padding:9px 17px;border-radius:30px;
            font-size:12px;font-weight:800;text-decoration:none;transition:.2s;white-space:nowrap;
        }
        .btn-kategori:hover{border-color:#f4b183;color:var(--primary)}
        .btn-kategori.active{background:linear-gradient(135deg,var(--primary),var(--primary-2));color:#fff;border-color:transparent}
        .rank-badge{
            display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;
            border-radius:50%;background:#f5eee8;color:#6b5a50;font-weight:800;font-size:13px;
        }
        .rank-1{background:#fbbf24;color:#fff}
        .rank-2{background:#94a3b8;color:#fff}
        .rank-3{background:#d97706;color:#fff}
        .price{font-weight:800;color:var(--primary)}
        .alert{border-radius:13px;border:0;font-size:13px;font-weight:600}

        @media(max-width:991px){
            .sidebar{position:relative;width:100%;height:auto;padding:18px}
            .sidebar .brand{margin-bottom:16px}
            .sidebar .sidebar-bottom{display:none}
            .main{margin-left:0;padding:25px 17px 45px}
        }
        @media(max-width:576px){
            .topbar{flex-direction:column;align-items:stretch}
            .admin-btn{justify-content:center}
        }
    </style>
    <?= $extra_head; ?>
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

        <a href="index.php" class="<?= in_array($halaman, ['index.php','detail_menu.php']) ? 'active' : ''; ?>"><i class="fa-solid fa-house"></i> Daftar Menu</a>
        <a href="rekomendasi.php" class="<?= $halaman == 'rekomendasi.php' ? 'active' : ''; ?>"><i class="fa-solid fa-utensils"></i> Rekomendasi Makanan</a>
        <a href="riwayat_perhitungan.php" class="<?= $halaman == 'riwayat_perhitungan.php' ? 'active' : ''; ?>"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Perhitungan</a>
        <a href="login_admin.php"><i class="fa-solid fa-user-shield"></i> Login Admin</a>

        <div class="sidebar-bottom">
            <i class="fa-solid fa-heart" style="color:#ffb703"></i>
            Pilih makanan favoritmu dan nikmati pengalaman kuliner yang lebih mudah.
        </div>
    </div>

    <div class="main">
        <div class="topbar">
            <div>
                <h1><?= htmlspecialchars($judul_halaman); ?></h1>
                <?php if($subjudul): ?><p class="subtitle"><?= htmlspecialchars($subjudul); ?></p><?php endif; ?>
            </div>
            <a href="login_admin.php" class="admin-btn"><i class="fa-solid fa-user-shield"></i> Login Admin</a>
        </div>