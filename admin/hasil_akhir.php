
<?php
session_start();

include "../config/koneksi.php";
include "../include/spk_functions.php";

if (
    !isset($_SESSION['user']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] != 'admin'
) {
    header("Location: login.php");
    exit;
}

$H = spk_hitung($conn);

/*
|--------------------------------------------------------------------------
| Fungsi interpretasi korelasi Spearman
|--------------------------------------------------------------------------
*/
function tafsir_rho($r)
{
    if ($r === null) {
        return '-';
    }

    $a = abs($r);

    $k = $a >= 0.9
        ? 'Sangat kuat'
        : ($a >= 0.7
            ? 'Kuat'
            : ($a >= 0.4 ? 'Sedang' : 'Lemah'));

    return $k . ($r < 0 ? ' (berlawanan arah)' : '');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hasil Akhir AHP vs TOPSIS | SPK</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #e85d04;
            --primary-2: #f48c06;
            --dark: #21140f;
            --muted: #786b65;
            --line: #f0e4da;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--dark);

            background:
                radial-gradient(circle at 90% 5%, rgba(244,140,6,.10), transparent 22%),
                linear-gradient(180deg, #fffaf5 0%, #f8f8f7 45%, #fff 100%);
        }

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

        .btn-orange {
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

        .card-custom {
            background: white;
            border: 1px solid var(--line);
            border-radius: 19px;
            box-shadow: 0 8px 28px rgba(55, 35, 20, .045);
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

        .winner-card {
            height: 100%;
            min-height: 205px;
            padding: 25px 20px;
            border-radius: 19px;
            text-align: center;
            color: white;
            background: linear-gradient(135deg, #321a10, #633018);
            box-shadow: 0 10px 25px rgba(50, 26, 16, .10);
            margin-bottom: 23px;
        }

        .winner-card i {
            color: #ffbd59;
        }

        .winner-card h6 {
            font-size: 13px;
            margin-bottom: 16px;
        }

        .winner-name {
            font-size: 18px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 5px;
            overflow-wrap: anywhere;
        }

        .winner-score {
            font-size: 12px;
            color: var(--muted);
        }

        .section-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 9px;
            color: white;
            background: linear-gradient(135deg, var(--primary), var(--primary-2));
            font-size: 12px;
            margin-right: 9px;
        }

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
            padding: 14px 15px;
            vertical-align: middle;
        }

        .table tbody td {
            padding: 13px 15px;
            border-color: var(--line);
            color: #51433c;
            vertical-align: middle;
        }

        .table tbody tr:hover td {
            background-color: #fffaf5;
        }

        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 30px;
            height: 30px;
            padding: 0 8px;
            border-radius: 9px;
            background: #fff0e2;
            color: var(--primary);
            font-weight: 800;
        }

        .score-value {
            color: var(--primary);
            font-weight: 800;
        }

        .analysis-table {
            max-width: 650px;
        }

        .analysis-table td:first-child {
            font-weight: 600;
            color: var(--muted);
            width: 65%;
        }

        .analysis-table td:last-child {
            font-weight: 800;
            color: var(--dark);
        }

        .alert-custom {
            background: #fff5e9;
            border: 1px solid #f5d6b7;
            color: #92501f;
            border-radius: 15px;
            padding: 18px 20px;
            font-size: 13px;
        }

        .note-text {
            font-size: 12px;
            color: var(--muted);
            line-height: 1.8;
        }

        .print-title {
            display: none;
        }

        @media (max-width: 768px) {
            .main {
                margin-left: 0;
                padding: 25px 17px 45px;
                width: 100%;
            }

            .page-title {
                font-size: 21px;
            }

            .page-actions {
                width: 100%;
                display: flex;
                flex-wrap: wrap;
            }

            .card-custom .card-header,
            .card-custom .card-body {
                padding: 16px;
            }

            .winner-card {
                min-height: 180px;
            }
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 12mm;
            }

            body {
                background: white !important;
                color: #000 !important;
                font-family: Arial, sans-serif;
            }

            .sidebar,
            .no-print,
            .page-actions {
                display: none !important;
            }

            .main {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .print-title {
                display: block;
                text-align: center;
                margin-bottom: 20px;
            }

            .card-custom,
            .winner-card {
                box-shadow: none !important;
                break-inside: avoid;
                border: 1px solid #ccc !important;
            }

            .winner-card {
                background: white !important;
                color: #000 !important;
            }

            .winner-card i {
                color: #000 !important;
            }

            .winner-name,
            .winner-score {
                color: #000 !important;
            }

            .table {
                font-size: 10px !important;
            }

            .table th,
            .table td {
                padding: 7px !important;
            }

            .card-custom .card-header {
                background: #f5f5f5 !important;
            }

            .table-responsive {
                overflow: visible !important;
            }

            .badge {
                border: 1px solid #999;
                color: #000 !important;
                background: white !important;
            }
        }
    </style>
</head>

<body>

<?php include "../include/sidebar.php"; ?>

<div class="main">

    <div class="print-title">
        <h2>LAPORAN HASIL AKHIR SPK AHP VS TOPSIS</h2>
        <p>Sistem Pendukung Keputusan Pemilihan Menu Terbaik</p>
        <hr>
    </div>

    <div class="page-heading">
        <div>
            <h3 class="page-title">
                <i class="fas fa-award me-2" style="color:var(--primary)"></i>
                Hasil Akhir AHP vs TOPSIS
            </h3>

            <p class="page-subtitle">
                Perbandingan hasil perangkingan alternatif menggunakan metode AHP dan TOPSIS.
            </p>
        </div>

        <div class="page-actions d-flex gap-2 flex-wrap no-print">
            <a href="perhitungan_topsis.php" class="btn-outline-custom">
                <i class="fas fa-list-ol me-1"></i>
                Detail Perhitungan
            </a>

            <button onclick="window.print()" class="btn-orange">
                <i class="fas fa-print me-1"></i>
                Cetak Laporan
            </button>
        </div>
    </div>

    <?php if (!empty($H['error'])): ?>

        <div class="alert-custom">
            <i class="fas fa-circle-exclamation me-2"></i>
            <?= htmlspecialchars($H['error']); ?>
        </div>

    <?php else: ?>

        <?php
        $ids = array_keys($H['alt']);

        $by_ahp = $ids;
        usort($by_ahp, function ($a, $b) use ($H) {
            return $H['ahp_skor'][$b] <=> $H['ahp_skor'][$a];
        });

        $by_top = $ids;
        usort($by_top, function ($a, $b) use ($H) {
            return $H['top_skor'][$b] <=> $H['top_skor'][$a];
        });

        $ada_alternatif = !empty($ids);
        ?>

        <?php if ($ada_alternatif): ?>

            <?php
            $win_a = $by_ahp[0];
            $win_t = $by_top[0];
            ?>

            <!-- Kartu Pemenang -->
            <div class="row">

                <div class="col-md-6">
                    <div class="winner-card">
                        <i class="fas fa-trophy fa-2x mb-3"></i>

                        <h6 class="fw-bold">
                            Alternatif Terbaik Menurut AHP
                        </h6>

                        <div class="bg-white p-3 rounded-3 d-inline-block">
                            <h4 class="winner-name">
                                <?= htmlspecialchars($H['alt'][$win_a]['nama']); ?>
                            </h4>

                            <div class="winner-score">
                                Skor AHP:
                                <?= spk_fmt($H['ahp_skor'][$win_a], 5); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="winner-card">
                        <i class="fas fa-trophy fa-2x mb-3"></i>

                        <h6 class="fw-bold">
                            Alternatif Terbaik Menurut TOPSIS
                        </h6>

                        <div class="bg-white p-3 rounded-3 d-inline-block">
                            <h4 class="winner-name">
                                <?= htmlspecialchars($H['alt'][$win_t]['nama']); ?>
                            </h4>

                            <div class="winner-score">
                                Nilai Preferensi (V<sub>i</sub>):
                                <?= spk_fmt($H['top_skor'][$win_t], 5); ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Tabel Perbandingan Ranking -->
            <div class="card-custom">

                <div class="card-header">
                    <h5>
                        <span class="section-number">1</span>
                        <i class="fas fa-table me-2" style="color:var(--primary)"></i>
                        Tabel Perbandingan Ranking
                    </h5>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">

                        <table class="table table-hover align-middle text-center">
                            <thead>
                                <tr>
                                    <th class="text-start">Alternatif Menu</th>
                                    <th>Rata-rata Rating*</th>
                                    <th>Skor AHP</th>
                                    <th>Rank AHP</th>
                                    <th>Skor TOPSIS</th>
                                    <th>Rank TOPSIS</th>
                                    <th>Selisih Rank</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($by_top as $id): ?>

                                    <?php
                                    $sel = abs(
                                        $H['ahp_rank'][$id] -
                                        $H['top_rank'][$id]
                                    );

                                    $rt = array_sum($H['X'][$id]) / count($H['kode']);
                                    ?>

                                    <tr>
                                        <td class="text-start fw-bold">
                                            <?= htmlspecialchars($H['alt'][$id]['label']); ?>
                                        </td>

                                        <td>
                                            <?= spk_fmt($rt, 3); ?>
                                        </td>

                                        <td class="score-value">
                                            <?= spk_fmt($H['ahp_skor'][$id], 5); ?>
                                        </td>

                                        <td>
                                            <span class="rank-badge">
                                                <?= htmlspecialchars((string)$H['ahp_rank'][$id]); ?>
                                            </span>
                                        </td>

                                        <td class="score-value">
                                            <?= spk_fmt($H['top_skor'][$id], 5); ?>
                                        </td>

                                        <td>
                                            <span class="rank-badge">
                                                <?= htmlspecialchars((string)$H['top_rank'][$id]); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge <?= $sel == 0
                                                ? 'bg-success'
                                                : ($sel <= 2
                                                    ? 'bg-warning text-dark'
                                                    : 'bg-danger'); ?>">
                                                <?= $sel; ?>
                                            </span>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>
                            </tbody>
                        </table>

                    </div>
                </div>

                <div class="card-body pt-3">
                    <p class="note-text mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        *Rata-rata rating merupakan rata-rata dari seluruh kriteria
                        dan hanya digunakan sebagai pembanding kasar.
                        Urutan tabel mengikuti ranking TOPSIS.
                    </p>
                </div>

            </div>

            <!-- Analisis Kesamaan Ranking -->
            <div class="card-custom">

                <div class="card-header">
                    <h5>
                        <span class="section-number">2</span>
                        <i class="fas fa-chart-bar me-2" style="color:var(--primary)"></i>
                        Analisis Kesamaan Ranking
                    </h5>
                </div>

                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-bordered analysis-table">

                            <tbody>
                                <tr>
                                    <td>Jumlah Alternatif</td>
                                    <td><?= count($ids); ?></td>
                                </tr>

                                <tr>
                                    <td>Ranking Identik di Kedua Metode</td>
                                    <td>
                                        <?= $H['rank_sama']; ?>
                                        dari <?= count($ids); ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Korelasi Spearman (&rho;)</td>
                                    <td>
                                        <?= $H['spearman'] === null
                                            ? '-'
                                            : spk_fmt($H['spearman'], 4); ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Tingkat Kesamaan Ranking</td>
                                    <td>
                                        <?= tafsir_rho($H['spearman']); ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Konsistensi Bobot AHP (CR)</td>
                                    <td>
                                        <?= spk_fmt($H['CR'], 5); ?>

                                        <span class="badge ms-1 <?= $H['konsisten']
                                            ? 'bg-success'
                                            : 'bg-danger'; ?>">
                                            <?= $H['konsisten']
                                                ? 'Konsisten'
                                                : 'Tidak Konsisten'; ?>
                                        </span>
                                    </td>
                                </tr>
                            </tbody>

                        </table>
                    </div>

                    <div class="alert-custom mt-3">
                        <strong>
                            <i class="fas fa-lightbulb me-2"></i>
                            Keterangan Analisis
                        </strong>

                        <p class="note-text mb-0 mt-2">
                            Kedua metode menggunakan bobot kriteria yang sama
                            dari AHP. Perbedaan hasil ranking berasal dari cara
                            pengolahan nilai alternatif. AHP menggunakan
                            penjumlahan nilai berbobot, sedangkan TOPSIS
                            mengukur jarak alternatif terhadap solusi ideal
                            positif dan negatif.
                        </p>
                    </div>

                </div>
            </div>

        <?php else: ?>

            <div class="alert-custom">
                <i class="fas fa-circle-info me-2"></i>
                Belum ada alternatif menu yang memiliki ulasan valid.
                Silakan periksa data menu dan ulasan terlebih dahulu.
            </div>

        <?php endif; ?>

    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
