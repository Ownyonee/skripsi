<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Mengambil data ulasan dari database
$query = "SELECT id AS id_ulasan,
                 username AS nama_pelanggan,
                 tanggal AS tanggal_ulasan,
                 komentar AS teks_ulasan,
                 rating,
                 rating_rasa,
                 rating_harga,
                 rating_porsi,
                 rating_popularitas,
                 rating_tekstur,
                 rating_variasi,
                 rating_kepraktisan,
                 rating_value
          FROM ulasan
          ORDER BY tanggal DESC, id DESC";
$result = mysqli_query($conn, $query);
$total_ulasan = $result ? mysqli_num_rows($result) : 0;
$cur = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Ulasan - SPK AHP TOPSIS</title>

    <!-- Bootstrap 5 & FontAwesome CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS DITANAM LANGSUNG DI SINI (ANTI ERROR PATH 404) -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        :root {
            --primary: #e85d04;
            --primary-2: #f48c06;
            --dark: #21140f;
            --muted: #786b65;
            --line: #f0e4da;
            --soft: #fff6ee;
            --white: #ffffff;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--dark);
            background:
                radial-gradient(circle at 90% 5%, rgba(244,140,6,.10), transparent 22%),
                linear-gradient(180deg, #fffaf5 0%, #f8f8f7 45%, #fff 100%);
        }

        a { text-decoration: none; }

        /* SIDEBAR STYLING */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
            padding: 28px 18px;
            color: #fff;
            display: flex;
            flex-direction: column;
            background: linear-gradient(180deg, #21140f 0%, #321a10 100%);
            box-shadow: 8px 0 35px rgba(33,20,15,.10);
            overflow-y: auto;
            scrollbar-width: thin;
        }

        .sidebar h2 {
            font-size: 20px;
            font-weight: 800;
            margin: 0 8px 18px;
            color: #fff;
            text-align: center;
            letter-spacing: .3px;
        }

        .sidebar hr {
            border: 0;
            border-top: 1px solid rgba(255,255,255,.12);
            margin: 0 4px 14px;
            opacity: 1;
        }

        .sidebar .menu-label {
            font-size: 11px;
            font-weight: 800;
            color: #a98f80;
            text-transform: uppercase;
            letter-spacing: .8px;
            margin: 16px 10px 8px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 13px;
            color: #d9cbc4;
            padding: 12px 15px;
            border-radius: 14px;
            margin-bottom: 5px;
            transition: .25s;
            font-size: 14px;
            font-weight: 600;
        }

        .sidebar a i {
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar a:hover {
            background: rgba(255,255,255,.08);
            color: #fff;
            transform: translateX(3px);
        }

        .sidebar a.active {
            color: #fff;
            background: linear-gradient(135deg, #e85d04, #f48c06);
            box-shadow: 0 10px 25px rgba(232,93,4,.25);
        }

        .sidebar .logout-link {
            margin-top: auto;
            color: #ff6b5e;
            border-top: 1px solid rgba(255,255,255,.12);
            border-radius: 0 0 14px 14px;
            padding-top: 16px;
        }

        .sidebar .logout-link:hover {
            background: rgba(255,107,94,.10);
            color: #ff8479;
        }

        /* MAIN CONTENT STYLING */
        .main {
            margin-left: 260px;
            padding: 34px 48px 60px;
            min-height: 100vh;
            width: calc(100% - 260px);
        }

        .page-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 18px;
            margin-bottom: 28px;
        }

        .page-title {
            font-size: 25px;
            font-weight: 800;
            margin: 0;
            color: var(--dark);
        }

        .page-subtitle {
            color: var(--muted);
            font-size: 13px;
            margin-top: 7px;
            margin-bottom: 0;
        }

        /* BUTTON STYLING */
        .btn-orange {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary), var(--primary-2));
            color: white;
            border: none;
            border-radius: 11px;
            padding: 10px 16px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: .2s;
        }

        .btn-orange:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 7px 18px rgba(232,93,4,.2);
        }

        .btn-outline-custom {
            display: inline-block;
            background: white;
            color: var(--dark);
            border: 1px solid var(--line);
            border-radius: 11px;
            padding: 10px 16px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: .2s;
        }

        .btn-outline-custom:hover {
            color: var(--primary);
            border-color: var(--primary);
        }

        /* CARD STYLING */
        .card-custom {
            background: white;
            border: 1px solid var(--line);
            border-radius: 19px;
            box-shadow: 0 8px 28px rgba(55,35,20,.045);
            margin-bottom: 23px;
            overflow: hidden;
        }

        .card-custom .card-header {
            background: white;
            border-bottom: 1px solid var(--line);
            padding: 19px 23px;
        }

        .card-custom .card-header h5 {
            font-size: 15px;
            font-weight: 800;
            color: var(--dark);
            margin: 0;
        }

        .card-custom .card-body {
            padding: 23px;
        }

        /* TABLE STYLING */
        .table {
            margin-bottom: 0;
            font-size: 12px;
            white-space: nowrap;
        }

        .table thead th {
            background: #fff6ee;
            color: #8c4b24;
            font-weight: 800;
            border-color: var(--line);
            padding: 13px 15px;
            vertical-align: middle;
        }

        .table tbody td {
            padding: 12px 15px;
            border-color: var(--line);
            color: #51433c;
            vertical-align: middle;
        }

        .table tbody tr:hover td {
            background-color: #fffaf5;
        }

        /* ALERT STYLING */
        .alert-custom {
            background: #fff5e9;
            border: 1px solid #f5d6b7;
            color: #92501f;
            border-radius: 15px;
            padding: 18px 20px;
            font-size: 13px;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                min-height: auto;
                padding: 18px;
            }
            .main {
                margin-left: 0;
                padding: 25px 17px 45px;
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- SIDEBAR DITANAM LANGSUNG DI SINI -->
    <div class="sidebar">
        <h2>SPK AHP TOPSIS</h2>
        <hr>

        <a href="dashboard.php" class="<?= $cur == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-gauge-high"></i> Dashboard
        </a>

        <div class="menu-label">Master Data</div>
        <a href="kriteria.php" class="<?= $cur == 'kriteria.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-database"></i> Data Kriteria
        </a>
        <a href="data_menu.php" class="<?= $cur == 'data_menu.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-book-open"></i> Data Menu
        </a>
        <a href="ulasan.php" class="<?= $cur == 'ulasan.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-comments"></i> Data Ulasan
        </a>

        <div class="menu-label">Perhitungan</div>
        <a href="perhitungan_ahp.php" class="<?= $cur == 'perhitungan_ahp.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-chart-line"></i> Perhitungan AHP
        </a>
        <a href="perhitungan_topsis.php" class="<?= $cur == 'perhitungan_topsis.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-bullseye"></i> Perhitungan TOPSIS
        </a>
        <a href="hasil_akhir.php" class="<?= $cur == 'hasil_akhir.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-trophy"></i> Hasil Akhir (AHP vs TOPSIS)
        </a>

        <div class="menu-label">Master User</div>
        <a href="data_user.php" class="<?= $cur == 'data_user.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-user-gear"></i> Data User
        </a>

        <a href="logout.php" class="logout-link">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>

    <!-- UTAMA (MAIN CONTENT) -->
    <div class="main">

        <div class="page-heading">
            <div>
                <h3 class="page-title">Data Ulasan Pelanggan</h3>
                <p class="page-subtitle">Kelola dan pantau ulasan pelanggan untuk perhitungan matriks AHP.</p>
            </div>
            <a href="perhitungan_ahp.php" class="btn-orange">
                <i class="fas fa-calculator me-1"></i> Lanjut Hitung AHP
            </a>
        </div>

        <div class="card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-comments me-2 text-warning"></i> Daftar Ulasan Masuk</h5>
                <span class="badge" style="background: var(--primary-2); padding: 6px 10px; border-radius: 8px;">
                    Total: <?= $total_ulasan; ?> Ulasan
                </span>
            </div>
            
            <div class="card-body">
                <a href="dashboard.php" class="btn-outline-custom mb-4">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 5%;">NO</th>
                                <th style="width: 15%;">TANGGAL</th>
                                <th style="width: 20%;">NAMA PELANGGAN</th>
                                <th style="width: 25%;">KRITERIA DISOROT</th>
                                <th>TEKS ULASAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if($result && mysqli_num_rows($result) > 0):
                                $no = 1;
                                while($row = mysqli_fetch_assoc($result)): 
                                    $kriteria_map = [
                                        'rating_harga' => 'Harga',
                                        'rating_rasa' => 'Rasa',
                                        'rating_porsi' => 'Porsi',
                                        'rating_popularitas' => 'Popularitas',
                                        'rating_tekstur' => 'Tekstur',
                                        'rating_variasi' => 'Variasi Menu',
                                        'rating_kepraktisan' => 'Kepraktisan',
                                        'rating_value' => 'Value for Money'
                                    ];
                                    $kriteria_terlihat = [];
                                    foreach ($kriteria_map as $field => $label) {
                                        $nilai = isset($row[$field]) ? (int)$row[$field] : 0;
                                        if ($nilai > 0) {
                                            $kriteria_terlihat[] = $label . ' (' . $nilai . ')';
                                        }
                                    }
                                    $kriteria_text = !empty($kriteria_terlihat) ? implode(', ', $kriteria_terlihat) : 'Tidak ada penilaian kriteria';
                            ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= date('d M Y', strtotime($row['tanggal_ulasan'])); ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
                                    <td>
                                        <span class="badge text-wrap lh-base" style="background: #fff0e2; color: #e85d04; border: 1px solid #f0e4da; font-weight:600; text-align: left;">
                                            <?= htmlspecialchars($kriteria_text); ?>
                                        </span>
                                    </td>
                                    <td><span class="text-muted fst-italic">"<?= htmlspecialchars($row['teks_ulasan']); ?>"</span></td>
                                </tr>
                            <?php 
                                endwhile; 
                            else: 
                            ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-folder-open mb-2" style="font-size: 24px; color: #e6c8b3;"></i><br>
                                        Belum ada data ulasan dari pelanggan.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="alert-custom mt-4 d-flex align-items-center">
                    <i class="fas fa-info-circle me-3" style="font-size: 20px;"></i>
                    <div>Data ulasan ini nantinya akan dihitung totalnya per kriteria secara otomatis oleh sistem untuk dijadikan nilai matriks perbandingan pada perhitungan AHP.</div>
                </div>
                
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>