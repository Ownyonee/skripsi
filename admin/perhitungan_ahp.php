
<?php
session_start();

include "../config/koneksi.php";
include "../include/spk_functions.php";

if(!isset($_SESSION['user']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

$H = spk_hitung($conn);
$judul = "Perhitungan AHP - Admin";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($judul); ?></title>

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

        /* MAIN */
        .main{
            margin-left:260px;
            padding:34px 48px 60px;
            min-height:100vh;
            width:calc(100% - 260px);
        }

        /* TOPBAR */
        .topbar{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:20px;
            margin-bottom:28px;
            flex-wrap:wrap;
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
            display:inline-flex;
            align-items:center;
            gap:8px;
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
            padding:11px 16px;
            font-weight:700;
            font-size:13px;
            text-decoration:none;
        }

        .btn-back:hover{
            background:#fff7ef;
            color:var(--primary);
        }

        /* PANEL */
        .panel{
            background:#fff;
            border:1px solid var(--line);
            border-radius:20px;
            box-shadow:0 7px 20px rgba(65,38,24,.045);
            overflow:hidden;
            margin-bottom:24px;
        }

        .panel-head{
            padding:19px 24px;
            border-bottom:1px solid #f5eee8;
            display:flex;
            align-items:center;
            gap:12px;
        }

        .panel-head h5{
            margin:0;
            font-weight:800;
            font-size:15px;
            color:var(--primary);
        }

        .panel-body{
            padding:24px;
        }

        .section-number{
            width:32px;
            height:32px;
            border-radius:10px;
            display:flex;
            align-items:center;
            justify-content:center;
            background:#fff0e3;
            color:var(--primary);
            font-weight:800;
            font-size:13px;
            flex-shrink:0;
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
            padding:14px 12px;
            border-bottom:1px solid var(--line);
            white-space:nowrap;
        }

        .table tbody td{
            padding:13px 12px;
            color:#5d5049;
            border-color:#f5eee8;
        }

        .table tbody tr:hover{
            background:#fffaf5;
        }

        .table tfoot td{
            background:#fff8f1;
            font-weight:800;
            color:var(--dark);
        }

        /* INFO */
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

        /* STATUS */
        .status-success{
            background:#e7f7ed;
            border:1px solid #c4ead1;
            color:#218653;
            padding:15px 18px;
            border-radius:12px;
            font-weight:800;
            display:flex;
            align-items:center;
            gap:12px;
        }

        .status-danger{
            background:#fff0ee;
            border:1px solid #f5ceca;
            color:#c0392b;
            padding:15px 18px;
            border-radius:12px;
            font-weight:800;
            display:flex;
            align-items:center;
            gap:12px;
        }

        .status-success i,
        .status-danger i{
            font-size:22px;
        }

        .badge-benefit{
            background:#e5f7ed;
            color:#218653;
            padding:6px 10px;
            border-radius:20px;
            font-size:11px;
            font-weight:800;
        }

        .badge-cost{
            background:#fff0ee;
            color:#c0392b;
            padding:6px 10px;
            border-radius:20px;
            font-size:11px;
            font-weight:800;
        }

        .weight-value{
            color:var(--primary);
            font-weight:800;
        }

        .star-icon{
            color:#f4a900;
        }

        .cr-value{
            color:var(--primary);
            font-weight:800;
        }

        /* RESPONSIVE */
        @media(max-width:768px){
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

            .panel-head{
                padding:17px;
            }
        }

        @media(max-width:480px){
            .topbar{
                align-items:flex-start;
            }

            .btn-orange{
                width:100%;
                justify-content:center;
            }
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <?php include "../include/sidebar.php"; ?>

    <!-- MAIN CONTENT -->
    <div class="main">

        <!-- HEADER -->
        <div class="topbar">
            <div>
                <h1>Perhitungan AHP</h1>
                <p class="subtitle">
                    Proses perhitungan bobot kriteria menggunakan metode Analytical Hierarchy Process.
                </p>
            </div>

            <a href="perhitungan_topsis.php" class="btn-orange">
                Lanjut ke TOPSIS
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <?php if($H['error']): ?>

            <div class="alert alert-warning rounded-4 shadow-sm">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <?= htmlspecialchars($H['error']); ?>
            </div>

        <?php else: ?>

            <?php $kode = $H['kode']; ?>

            <!-- INFORMASI -->
            <div class="info-box mb-4">
                <strong>
                    <i class="fa-solid fa-circle-info me-2"></i>
                    Informasi Perhitungan AHP
                </strong>

                <br>

                Bobot diturunkan dari
                <strong><?= $H['total_ulasan']; ?></strong> ulasan valid.
                Selisih rata-rata rating antar kriteria diskalakan
                (min-max) ke skala Saaty 1-9, lalu dibulatkan ke
                bilangan bulat sebelum dihitung AHP.
            </div>

            <!-- 1. RATA-RATA DAN BOBOT -->
            <div class="panel">

                <div class="panel-head">
                    <div class="section-number">1</div>
                    <h5>
                        Rata-rata Rating & Bobot Prioritas (W)
                    </h5>
                </div>

                <div class="panel-body">

                    <div class="table-responsive">

                        <table class="table table-hover">

                            <thead>
                                <tr class="text-center">
                                    <th>Kode</th>
                                    <th>Kriteria</th>
                                    <th>Tipe</th>
                                    <th>Rata-rata Rating</th>
                                    <th>Bobot (W)</th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php foreach($kode as $c): ?>

                                <tr>

                                    <td class="text-center fw-bold">
                                        <?= htmlspecialchars($c); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($H['kriteria'][$c]['nama']); ?>
                                    </td>

                                    <td class="text-center">

                                        <?php if($H['kriteria'][$c]['tipe'] == 'benefit'): ?>

                                            <span class="badge-benefit">Benefit</span>

                                        <?php else: ?>

                                            <span class="badge-cost">Cost</span>

                                        <?php endif; ?>

                                    </td>

                                    <td class="text-center">
                                        <?= spk_fmt($H['avg_kriteria'][$c], 3); ?>
                                        <i class="fa-solid fa-star star-icon"></i>
                                    </td>

                                    <td class="text-center weight-value">
                                        <?= spk_fmt($H['w'][$c], 5); ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>

                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end">
                                        Total Bobot
                                    </td>

                                    <td class="text-center">
                                        <?= spk_fmt(array_sum($H['w']), 5); ?>
                                    </td>
                                </tr>
                            </tfoot>

                        </table>

                    </div>

                </div>
            </div>

            <!-- 2. MATRIKS PERBANDINGAN -->
            <div class="panel">

                <div class="panel-head">
                    <div class="section-number">2</div>
                    <h5>
                        Matriks Perbandingan Berpasangan (Skala Saaty)
                    </h5>
                </div>

                <div class="panel-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover text-center">

                            <thead>
                                <tr>
                                    <th>Kriteria</th>

                                    <?php foreach($kode as $c): ?>
                                        <th><?= htmlspecialchars($c); ?></th>
                                    <?php endforeach; ?>

                                </tr>
                            </thead>

                            <tbody>

                            <?php foreach($kode as $i): ?>

                                <tr>

                                    <td class="fw-bold" style="background:#fff8f1;">
                                        <?= htmlspecialchars($i); ?>
                                    </td>

                                    <?php foreach($kode as $j): ?>

                                        <td>
                                            <?= spk_fmt($H['A'][$i][$j], 3); ?>
                                        </td>

                                    <?php endforeach; ?>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                </div>
            </div>

            <!-- 3. MATRIKS NORMALISASI -->
            <div class="panel">

                <div class="panel-head">
                    <div class="section-number">3</div>
                    <h5>
                        Matriks Ternormalisasi
                    </h5>
                </div>

                <div class="panel-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover text-center">

                            <thead>
                                <tr>
                                    <th>Kriteria</th>

                                    <?php foreach($kode as $c): ?>
                                        <th><?= htmlspecialchars($c); ?></th>
                                    <?php endforeach; ?>

                                    <th>Bobot</th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php foreach($kode as $i): ?>

                                <tr>

                                    <td class="fw-bold" style="background:#fff8f1;">
                                        <?= htmlspecialchars($i); ?>
                                    </td>

                                    <?php foreach($kode as $j): ?>

                                        <td>
                                            <?= spk_fmt($H['N'][$i][$j], 4); ?>
                                        </td>

                                    <?php endforeach; ?>

                                    <td class="weight-value">
                                        <?= spk_fmt($H['w'][$i], 5); ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                </div>
            </div>

            <!-- 4. UJI KONSISTENSI -->
            <div class="panel">

                <div class="panel-head">
                    <div class="section-number">4</div>
                    <h5>
                        Uji Konsistensi AHP
                    </h5>
                </div>

                <div class="panel-body">

                    <div class="table-responsive">

                        <table class="table table-bordered" style="max-width:600px;">

                            <tbody>

                                <tr>
                                    <td class="fw-semibold">
                                        &lambda;<sub>max</sub>
                                    </td>

                                    <td class="text-end">
                                        <?= spk_fmt($H['lambda'], 5); ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="fw-semibold">
                                        CI = (&lambda;<sub>max</sub> - n) / (n - 1)
                                    </td>

                                    <td class="text-end">
                                        <?= spk_fmt($H['CI'], 5); ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="fw-semibold">
                                        RI (n = <?= count($kode); ?>)
                                    </td>

                                    <td class="text-end">
                                        <?= spk_fmt($H['RI'], 5); ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="fw-semibold">
                                        CR = CI / RI
                                    </td>

                                    <td class="text-end cr-value">
                                        <?= spk_fmt($H['CR'], 5); ?>
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                    <div class="mt-3">

                        <?php if($H['konsisten']): ?>

                            <div class="status-success">
                                <i class="fa-solid fa-circle-check"></i>

                                <div>
                                    Konsisten
                                    <div class="small fw-normal">
                                        CR &le; 0,1. Matriks perbandingan memenuhi batas konsistensi AHP.
                                    </div>
                                </div>
                            </div>

                        <?php else: ?>

                            <div class="status-danger">
                                <i class="fa-solid fa-circle-exclamation"></i>

                                <div>
                                    Tidak Konsisten
                                    <div class="small fw-normal">
                                        CR &gt; 0,1. Matriks perbandingan belum memenuhi batas konsistensi AHP.
                                    </div>
                                </div>
                            </div>

                        <?php endif; ?>

                    </div>

                </div>
            </div>

            <!-- TOMBOL LANJUT -->
            <div class="d-flex justify-content-end mb-4">

                <a href="perhitungan_topsis.php" class="btn-orange">
                    Lanjut Perhitungan TOPSIS
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

            </div>

        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
