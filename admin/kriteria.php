
<?php
session_start();
include '../config/koneksi.php';

if(!isset($_SESSION['user']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

// Ambil data kriteria
$query = mysqli_query($conn, "SELECT * FROM kriteria ORDER BY id ASC");

$judul = "Data Kriteria - Admin";
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
            --line:#f0e4da;
        }

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            background:
                radial-gradient(circle at 90% 5%, rgba(244,140,6,.10), transparent 22%),
                linear-gradient(180deg,#fffaf5 0%,#f8f8f7 45%,#fff 100%);
            font-family:'Plus Jakarta Sans',sans-serif;
            color:var(--dark);
        }

        /* SIDEBAR */
        .sidebar{
            width:260px;
            height:100vh;
            position:fixed;
            left:0;
            top:0;
            z-index:100;
            padding:28px 18px;
            color:#fff;
            display:flex;
            flex-direction:column;
            background:linear-gradient(180deg,#21140f 0%,#321a10 100%);
            box-shadow:8px 0 35px rgba(33,20,15,.10);
            overflow-y:auto;
        }

        .sidebar h2{
            font-size:20px;
            font-weight:800;
            margin:0 8px 18px;
            color:#fff;
            text-align:center;
        }

        .sidebar hr{
            border:0;
            border-top:1px solid rgba(255,255,255,.12);
            margin:0 4px 14px;
            opacity:1;
        }

        .sidebar .menu-label{
            font-size:11px;
            font-weight:800;
            color:#a98f80;
            text-transform:uppercase;
            letter-spacing:.8px;
            margin:16px 10px 8px;
        }

        .sidebar a{
            display:flex;
            align-items:center;
            gap:13px;
            color:#d9cbc4;
            text-decoration:none;
            padding:12px 15px;
            border-radius:14px;
            margin-bottom:5px;
            transition:.25s;
            font-size:14px;
            font-weight:600;
        }

        .sidebar a i{
            width:20px;
            text-align:center;
        }

        .sidebar a:hover{
            background:rgba(255,255,255,.08);
            color:#fff;
            transform:translateX(3px);
        }

        .sidebar a.active{
            color:#fff;
            background:linear-gradient(135deg,var(--primary),var(--primary-2));
            box-shadow:0 10px 25px rgba(232,93,4,.25);
        }

        .sidebar .logout-link{
            margin-top:auto;
            color:#ff6b5e;
            border-top:1px solid rgba(255,255,255,.12);
            border-radius:0 0 14px 14px;
            padding-top:16px;
        }

        /* MAIN */
        .main{
            margin-left:260px;
            padding:34px 48px 60px;
            min-height:100vh;
            width:calc(100% - 260px);
        }

        .topbar{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:20px;
            margin-bottom:28px;
        }

        .topbar h1{
            font-weight:800;
            color:var(--dark);
            font-size:30px;
            letter-spacing:-.7px;
        }

        .topbar .subtitle{
            color:var(--muted);
            margin-top:6px;
            font-size:14px;
        }

        /* CARD */
        .panel{
            background:#fff;
            border:1px solid var(--line);
            border-radius:20px;
            box-shadow:0 7px 20px rgba(65,38,24,.045);
            overflow:hidden;
            margin-bottom:24px;
        }

        .panel-head{
            padding:20px 24px;
            border-bottom:1px solid #f5eee8;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:15px;
            flex-wrap:wrap;
        }

        .panel-head h5{
            margin:0;
            font-weight:800;
            font-size:16px;
            color:var(--primary);
        }

        .panel-body{
            padding:24px;
        }

        /* BUTTON */
        .btn-orange{
            background:linear-gradient(135deg,var(--primary),var(--primary-2));
            color:#fff;
            border:none;
            border-radius:11px;
            padding:11px 17px;
            font-weight:700;
            font-size:13px;
            text-decoration:none;
            transition:.2s;
        }

        .btn-orange:hover{
            color:#fff;
            transform:translateY(-2px);
            box-shadow:0 8px 18px rgba(232,93,4,.22);
        }

        .btn-back{
            background:#fff;
            color:var(--muted);
            border:1px solid var(--line);
            border-radius:11px;
            padding:10px 16px;
            font-weight:700;
            font-size:13px;
            text-decoration:none;
        }

        .btn-back:hover{
            background:#fff7ef;
            color:var(--primary);
        }

        .btn-edit{
            background:#fff3dc;
            color:#bc7900;
            border:1px solid #ffe0a3;
            border-radius:9px;
            padding:7px 11px;
            font-size:12px;
            font-weight:700;
            text-decoration:none;
        }

        .btn-delete{
            background:#fff0ee;
            color:#c0392b;
            border:1px solid #f8d0cb;
            border-radius:9px;
            padding:7px 11px;
            font-size:12px;
            font-weight:700;
            text-decoration:none;
        }

        /* TABLE */
        .table-responsive{
            border-radius:12px;
        }

        .table{
            margin-bottom:0;
            vertical-align:middle;
            font-size:13px;
        }

        .table thead th{
            background:#fff6ee;
            color:#8c4b24;
            font-size:12px;
            font-weight:800;
            text-transform:uppercase;
            padding:15px 12px;
            border-bottom:1px solid var(--line);
            white-space:nowrap;
        }

        .table tbody td{
            padding:14px 12px;
            color:#5d5049;
            border-color:#f5eee8;
        }

        .table tbody tr:hover{
            background:#fffaf5;
        }

        .badge-benefit{
            background:#e5f7ed;
            color:#218653;
            padding:7px 12px;
            border-radius:20px;
        }

        .badge-cost{
            background:#fff0ee;
            color:#c0392b;
            padding:7px 12px;
            border-radius:20px;
        }

        /* KETERANGAN */
        .info-box{
            background:#fff9f2;
            border:1px solid #f5dfc9;
            border-left:5px solid var(--primary);
            border-radius:13px;
            padding:17px 20px;
            color:#786b65;
            font-size:13px;
            line-height:1.8;
        }

        .info-box strong{
            color:var(--dark);
        }

        /* RESPONSIVE */
        @media(max-width:768px){
            .sidebar{
                position:relative;
                width:100%;
                height:auto;
                min-height:auto;
                padding:18px;
            }

            .sidebar .logout-link{
                margin-top:12px;
            }

            .main{
                margin-left:0;
                padding:25px 17px 45px;
                width:100%;
            }

            .topbar h1{
                font-size:25px;
            }

            .panel-body{
                padding:16px;
            }
        }

        @media(max-width:480px){
            .topbar{
                align-items:flex-start;
            }

            .panel-head{
                align-items:flex-start;
            }
        }
    </style>
</head>

<body>

    <?php include '../include/sidebar.php'; ?>

    <div class="main">

        <!-- TOPBAR -->
        <div class="topbar">
            <div>
                <h1>Data Kriteria</h1>
                <p class="subtitle">
                    Kelola data kriteria untuk perhitungan metode AHP dan TOPSIS.
                </p>
            </div>
        </div>

        <!-- PANEL DATA -->
        <div class="panel">

            <div class="panel-head">
                <h5>
                    <i class="fa-solid fa-database me-2"></i>
                    Data Kriteria AHP & TOPSIS
                </h5>

                <a href="tambah_kriteria.php" class="btn-orange">
                    <i class="fa-solid fa-plus me-2"></i>
                    Tambah Data
                </a>
            </div>

            <div class="panel-body">

                <div class="mb-3">
                    <a href="dashboard.php" class="btn-back">
                        <i class="fa-solid fa-arrow-left me-2"></i>
                        Kembali
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr class="text-center">
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Kriteria</th>
                                <th>Bobot</th>
                                <th>Tipe</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php
                        $no = 1;

                        if(mysqli_num_rows($query) > 0):
                            while($data = mysqli_fetch_assoc($query)):
                        ?>

                            <tr>
                                <td class="text-center">
                                    <?= $no++; ?>
                                </td>

                                <td class="text-center fw-bold">
                                    <?= htmlspecialchars($data['kode_kriteria']); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($data['nama_kriteria']); ?>
                                </td>

                                <td class="text-center fw-bold">
                                    <?= number_format((float)$data['bobot'],4); ?>
                                </td>

                                <td class="text-center">
                                    <?php if($data['tipe'] == 'benefit'): ?>
                                        <span class="badge-benefit">
                                            <i class="fa-solid fa-arrow-up me-1"></i>
                                            Benefit
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-cost">
                                            <i class="fa-solid fa-arrow-down me-1"></i>
                                            Cost
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">

                                        <a href="edit_kriteria.php?id=<?= (int)$data['id']; ?>"
                                           class="btn-edit">
                                            <i class="fa-solid fa-pen-to-square me-1"></i>
                                            Edit
                                        </a>

                                        <a href="hapus_kriteria.php?id=<?= (int)$data['id']; ?>"
                                           class="btn-delete"
                                           onclick="return confirm('Yakin ingin hapus data ini?')">
                                            <i class="fa-solid fa-trash me-1"></i>
                                            Hapus
                                        </a>

                                    </div>
                                </td>
                            </tr>

                        <?php
                            endwhile;
                        else:
                        ?>

                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-folder-open me-2"></i>
                                    Belum ada data kriteria.
                                </td>
                            </tr>

                        <?php endif; ?>

                        </tbody>
                    </table>
                </div>

                <!-- KETERANGAN -->
                <div class="info-box mt-4">
                    <strong>
                        <i class="fa-solid fa-circle-info me-2"></i>
                        Keterangan:
                    </strong>

                    <br>

                    <i class="fa-solid fa-arrow-down text-danger me-2"></i>
                    <strong>Cost</strong> = Nilai lebih kecil lebih baik (contoh: Harga).

                    <br>

                    <i class="fa-solid fa-arrow-up text-success me-2"></i>
                    <strong>Benefit</strong> = Nilai lebih besar lebih baik (contoh: Rasa, Popularitas).
                </div>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
