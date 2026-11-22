
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
| Fungsi untuk menampilkan matriks TOPSIS
|--------------------------------------------------------------------------
*/
function tabel_matriks($H, $M, $dec = 4, $extra = null)
{
    ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center align-middle">
            <thead>
                <tr>
                    <th class="text-start">Alternatif</th>
                    <?php foreach ($H['kode'] as $c): ?>
                        <th><?= htmlspecialchars($c); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($M as $id => $row): ?>
                    <tr>
                        <td class="text-start fw-bold">
                            <?= htmlspecialchars($H['alt'][$id]['label']); ?>
                        </td>

                        <?php foreach ($H['kode'] as $c): ?>
                            <td><?= spk_fmt($row[$c], $dec); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>

                <?php if ($extra): ?>
                    <?php foreach ($extra as $lbl => $data): ?>
                        <?php
                        [$cls, $vals] = $data;
                        ?>
                        <tr class="<?= htmlspecialchars($cls); ?> fw-bold">
                            <td class="text-start">
                                <?= htmlspecialchars($lbl); ?>
                            </td>

                            <?php foreach ($H['kode'] as $c): ?>
                                <td><?= spk_fmt($vals[$c], $dec); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Perhitungan TOPSIS | SPK AHP TOPSIS</title>

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

        .table-success td {
            background-color: #eaf8ef !important;
            color: #176b3a !important;
        }

        .table-danger td {
            background-color: #fff0ef !important;
            color: #a52b2b !important;
        }

        .table-primary th {
            background: #fff0e2 !important;
            color: #8c4b24 !important;
        }

        .info-text {
            color: var(--muted);
            font-size: 12px;
            margin-top: 14px;
        }

        .alert-custom {
            background: #fff5e9;
            border: 1px solid #f5d6b7;
            color: #92501f;
            border-radius: 15px;
            padding: 18px 20px;
            font-size: 13px;
        }

        .ranking-card {
            border-left: 4px solid var(--primary);
        }

        .ranking-card .card-header h5 {
            color: var(--primary);
        }

        .rank-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 9px;
            background: #fff0e2;
            color: var(--primary);
            font-weight: 800;
        }

        .score-value {
            color: var(--primary);
            font-weight: 800;
        }

        .empty-state {
            text-align: center;
            padding: 35px 15px;
            color: var(--muted);
        }

        .empty-state i {
            font-size: 35px;
            color: #e6c8b3;
            margin-bottom: 12px;
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

            .page-heading {
                align-items: flex-start;
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
        }
    </style>
</head>

<body>

<?php include "../include/sidebar.php"; ?>

<div class="main">

    <div class="page-heading">
        <div>
            <h3 class="page-title">
                <i class="fa-solid fa-chart-line me-2" style="color:var(--primary)"></i>
                Perhitungan Ranking TOPSIS
            </h3>
            <p class="page-subtitle">
                Proses normalisasi, pembobotan, solusi ideal, hingga perangkingan alternatif menu.
            </p>
        </div>

        <div class="page-actions d-flex gap-2 flex-wrap">
            <a href="perhitungan_ahp.php" class="btn-outline-custom">
                <i class="fas fa-arrow-left me-1"></i>
                Kembali ke AHP
            </a>

            <a href="hasil_akhir.php" class="btn-orange">
                <i class="fas fa-trophy me-1"></i>
                Lihat Hasil Akhir
            </a>
        </div>
    </div>

    <?php if (!empty($H['error'])): ?>

        <div class="alert-custom">
            <i class="fas fa-circle-exclamation me-2"></i>
            <?= htmlspecialchars($H['error']); ?>
        </div>

    <?php else: ?>

        <!-- 1. Matriks Keputusan -->
        <div class="card-custom">
            <div class="card-header">
                <h5>
                    <span class="section-number">1</span>
                    Matriks Keputusan (X)
                </h5>
            </div>

            <div class="card-body">
                <?php if (!empty($H['alt'])): ?>

                    <?php tabel_matriks($H, $H['X'], 3); ?>

                    <p class="info-text">
                        <i class="fas fa-info-circle me-1"></i>
                        Matriks berisi rata-rata rating setiap menu berdasarkan ulasan valid.
                        Jumlah alternatif menu:
                        <strong><?= count($H['alt']); ?></strong>.
                    </p>

                <?php else: ?>

                    <div class="empty-state">
                        <i class="fas fa-table d-block"></i>
                        <strong>Belum ada data alternatif.</strong>
                        <p class="mb-0 mt-2">Belum tersedia menu dengan ulasan valid untuk dihitung.</p>
                    </div>

                <?php endif; ?>
            </div>
        </div>

        <!-- 2. Matriks Normalisasi -->
        <div class="card-custom">
            <div class="card-header">
                <h5>
                    <span class="section-number">2</span>
                    Matriks Ternormalisasi (R)
                </h5>
            </div>

            <div class="card-body">
                <?php tabel_matriks($H, $H['top_R'], 4); ?>

                <p class="info-text">
                    <i class="fas fa-info-circle me-1"></i>
                    Matriks normalisasi digunakan untuk menyamakan skala nilai pada setiap kriteria.
                </p>
            </div>
        </div>

        <!-- 3. Matriks Terbobot dan Solusi Ideal -->
        <div class="card-custom">
            <div class="card-header">
                <h5>
                    <span class="section-number">3</span>
                    Matriks Ternormalisasi Terbobot (Y) dan Solusi Ideal
                </h5>
            </div>

            <div class="card-body">
                <?php
                tabel_matriks($H, $H['top_Y'], 5, [
                    'Ideal Positif (A+)' => [
                        'table-success',
                        $H['Ap']
                    ],
                    'Ideal Negatif (A-)' => [
                        'table-danger',
                        $H['Am']
                    ],
                ]);
                ?>

                <div class="d-flex flex-wrap gap-3 mt-3">
                    <small class="text-muted">
                        <span class="badge bg-success me-1">A+</span>
                        Solusi ideal positif
                    </small>

                    <small class="text-muted">
                        <span class="badge bg-danger me-1">A-</span>
                        Solusi ideal negatif
                    </small>
                </div>
            </div>
        </div>

        <!-- 4. Ranking TOPSIS -->
        <div class="card-custom ranking-card">
            <div class="card-header">
                <h5>
                    <i class="fas fa-trophy me-2"></i>
                    4. Jarak dan Nilai Preferensi (Ranking TOPSIS)
                </h5>
            </div>

            <div class="card-body">

                <?php
                $ids = array_keys($H['top_rank']);

                usort($ids, function ($a, $b) use ($H) {
                    return $H['top_skor'][$b] <=> $H['top_skor'][$a];
                });
                ?>

                <?php if (!empty($ids)): ?>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center align-middle">
                            <thead>
                                <tr>
                                    <th width="80">Ranking</th>
                                    <th class="text-start">Alternatif Menu</th>
                                    <th>D<sup>+</sup></th>
                                    <th>D<sup>-</sup></th>
                                    <th>V<sub>i</sub></th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($ids as $id): ?>
                                    <tr>
                                        <td>
                                            <span class="rank-number">
                                                <?= htmlspecialchars((string)$H['top_rank'][$id]); ?>
                                            </span>
                                        </td>

                                        <td class="text-start fw-bold">
                                            <?= htmlspecialchars($H['alt'][$id]['label']); ?>
                                        </td>

                                        <td>
                                            <?= spk_fmt($H['dp'][$id], 5); ?>
                                        </td>

                                        <td>
                                            <?= spk_fmt($H['dm'][$id], 5); ?>
                                        </td>

                                        <td class="score-value">
                                            <?= spk_fmt($H['top_skor'][$id], 5); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <p class="info-text">
                        <i class="fas fa-info-circle me-1"></i>
                        Alternatif dengan nilai preferensi (V<sub>i</sub>) tertinggi
                        menjadi alternatif dengan peringkat terbaik menurut metode TOPSIS.
                    </p>

                <?php else: ?>

                    <div class="empty-state">
                        <i class="fas fa-trophy d-block"></i>
                        <strong>Belum ada hasil perangkingan.</strong>
                        <p class="mb-0 mt-2">Data alternatif belum tersedia untuk dihitung.</p>
                    </div>

                <?php endif; ?>

            </div>
        </div>

        <div class="text-end mt-4">
            <a href="hasil_akhir.php" class="btn-orange">
                Lihat Perbandingan Hasil AHP dan TOPSIS
                <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>

    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
