<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM daftar_menu ORDER BY id ASC");

$judul = "Data Menu - Admin";
$favicon = "../images/logo.jpeg";
$cur = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($judul); ?></title>
    <link rel="icon" type="image/jpeg" href="<?= htmlspecialchars($favicon); ?>?v=2">
    <link rel="shortcut icon" type="image/jpeg" href="<?= htmlspecialchars($favicon); ?>?v=2">
    <link rel="apple-touch-icon" href="<?= htmlspecialchars($favicon); ?>?v=2">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root{
            --primary:#e85d04;
            --primary-2:#f48c06;
            --dark:#21140f;
            --muted:#786b65;
            --card:#ffffff;
            --line:#f0e4da;
            --border:#f0e4da;
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
            overflow-y:auto;scrollbar-width:thin;
        }
        .sidebar h2{
            font-size:20px;font-weight:800;margin:0 8px 18px;
            color:#fff;text-align:center;letter-spacing:.3px;
        }
        .sidebar hr{border:0;border-top:1px solid rgba(255,255,255,.12);margin:0 4px 14px;opacity:1}
        .sidebar .menu-label{
            font-size:11px;font-weight:800;color:#a98f80;text-transform:uppercase;
            letter-spacing:.8px;margin:16px 10px 8px;
        }
        .sidebar a{
            display:flex;align-items:center;gap:13px;color:#d9cbc4;
            text-decoration:none;padding:12px 15px;border-radius:14px;
            margin-bottom:5px;transition:.25s;font-size:14px;font-weight:600;
        }
        .sidebar a i{width:20px;text-align:center;flex-shrink:0}
        .sidebar a:hover{background:rgba(255,255,255,.08);color:#fff;transform:translateX(3px)}
        .sidebar a.active{
            color:#fff;background:linear-gradient(135deg,var(--primary),var(--primary-2));
            box-shadow:0 10px 25px rgba(232,93,4,.25);
        }
        .sidebar .logout-link{
            margin-top:auto;color:#ff6b5e;border-top:1px solid rgba(255,255,255,.12);
            border-radius:0 0 14px 14px;padding-top:16px;
        }
        .sidebar .logout-link:hover{background:rgba(255,107,94,.10);color:#ff8479}

        /* MAIN */
        .main{margin-left:260px;padding:34px 48px 60px;min-height:100vh;width:calc(100% - 260px)}
        .topbar{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:25px}
        .topbar h1{font-weight:800;color:var(--dark);font-size:30px;letter-spacing:-.7px}
        .topbar .subtitle{color:var(--muted);margin-top:6px;font-size:14px}
        .logout-btn{
            padding:11px 17px;background:#fff;color:#9f2d22;border:1px solid #f4d8d4;
            border-radius:12px;text-decoration:none;font-weight:700;font-size:13px;
            display:flex;align-items:center;gap:8px;transition:.2s;
        }
        .logout-btn:hover{background:#fff4f2;transform:translateY(-2px);color:#9f2d22}

        /* PANEL */
        .panel{
            background:#fff;border:1px solid var(--line);border-radius:20px;
            box-shadow:0 7px 20px rgba(65,38,24,.045);overflow:hidden;margin-bottom:24px;
        }
        .panel-body{padding:22px 24px}

        /* TOMBOL */
        .btn{border-radius:10px;font-weight:700}
        .btn-orange{
            background:linear-gradient(135deg,var(--primary),var(--primary-2));
            color:#fff;border:0;font-size:13px;
        }
        .btn-orange:hover{color:#fff;opacity:.92}

        /* TABEL */
        .table{font-size:13px;margin-bottom:0}
        .table thead th{
            background:#fff5ec;color:#7a3a14;font-weight:800;font-size:12px;
            text-transform:uppercase;letter-spacing:.3px;border-color:var(--line);
        }
        .table td{border-color:var(--line)}
        .table-hover tbody tr:hover{background:#fffaf5}
        .price{font-weight:800;color:var(--primary)}
        .img-thumb{
            width:120px;height:80px;object-fit:cover;border-radius:12px;
            border:1px solid var(--border);
        }
        .btn-sm i{margin-right:3px}

        .note-box{
            background:#fff5ec;border:1px solid #f6d9c3;color:#7a3a14;
            border-radius:14px;padding:14px 18px;font-size:13px;margin-top:18px;
        }

        @media(max-width:768px){
            .sidebar{position:relative;width:100%;height:auto;min-height:auto;padding:18px}
            .sidebar .logout-link{margin-top:12px}
            .main{margin-left:0;padding:25px 17px 45px;width:100%}
            .topbar{align-items:flex-start}
            .topbar h1{font-size:25px}
            .logout-btn{padding:9px 12px}
        }
        @media(max-width:480px){
            .topbar{flex-direction:column}
            .logout-btn{width:100%;justify-content:center}
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h2>SPK AHP TOPSIS</h2>
        <hr>

        <a href="dashboard.php" class="<?= $cur=='dashboard.php' ? 'active':''; ?>"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>

        <div class="menu-label">Master Data</div>
        <a href="kriteria.php" class="<?= $cur=='kriteria.php' ? 'active':''; ?>"><i class="fa-solid fa-database"></i> Data Kriteria</a>
        <a href="data_menu.php" class="<?= $cur=='data_menu.php' ? 'active':''; ?>"><i class="fa-solid fa-book-open"></i> Data Menu</a>
        <a href="ulasan.php" class="<?= $cur=='ulasan.php' ? 'active':''; ?>"><i class="fa-solid fa-comments"></i> Data Ulasan</a>

        <div class="menu-label">Perhitungan</div>
        <a href="perhitungan_ahp.php" class="<?= $cur=='perhitungan_ahp.php' ? 'active':''; ?>"><i class="fa-solid fa-chart-line"></i> Perhitungan AHP</a>
        <a href="perhitungan_topsis.php" class="<?= $cur=='perhitungan_topsis.php' ? 'active':''; ?>"><i class="fa-solid fa-bullseye"></i> Perhitungan TOPSIS</a>
        <a href="hasil_akhir.php" class="<?= $cur=='hasil_akhir.php' ? 'active':''; ?>"><i class="fa-solid fa-trophy"></i> Hasil Akhir (AHP vs TOPSIS)</a>

        <div class="menu-label">Master User</div>
        <a href="data_user.php" class="<?= $cur=='data_user.php' ? 'active':''; ?>"><i class="fa-solid fa-user-gear"></i> Data User</a>

        <a href="logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>

    <div class="main">

        <div class="topbar">
            <div>
                <h1>Data Menu</h1>
                <p class="subtitle">Kelola daftar menu makanan: nama, harga, deskripsi, dan gambar.</p>
            </div>
            <a href="logout.php" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i> Keluar
            </a>
        </div>

        <div class="panel">
            <div class="panel-body">

                <div class="mb-3 d-flex flex-wrap gap-2">
                    <a href="dashboard.php" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <a href="tambah_menu.php" class="btn btn-orange btn-sm">
                        <i class="fas fa-plus"></i> Tambah Menu
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="text-center">
                            <tr>
                                <th>No</th>
                                <th>Gambar</th>
                                <th>Nama Menu</th>
                                <th>Harga</th>
                                <th>Deskripsi</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $no = 1;
                        while($row = mysqli_fetch_assoc($query)){
                        ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td class="text-center">
                                    <?php if(!empty($row['gambar'])): ?>
                                        <img src="../<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['nama_menu']); ?>" class="img-thumb">
                                    <?php else: ?>
                                        <span class="text-muted">Tidak ada gambar</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?= htmlspecialchars($row['nama_menu']); ?></td>
                                <td class="text-center price">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                                <td><?= nl2br(htmlspecialchars($row['deskripsi'])); ?></td>
                                <td class="text-center">
                                    <a href="edit_menu.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm mb-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="hapus_menu.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Yakin ingin hapus menu ini?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="note-box">
                    <i class="fas fa-info-circle me-2"></i><b>Keterangan:</b> Data Menu menampilkan daftar menu makanan yang terdiri dari nama, harga, deskripsi, dan gambar.
                </div>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>