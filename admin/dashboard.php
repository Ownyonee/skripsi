<?php
// admin/dashboard.php
// Proteksi admin (session + no-cache) harus dipanggil paling awal, sebelum output apa pun
require_once __DIR__ . "/auth.php";
include "../config/koneksi.php";
include "../include/spk_functions.php";

$H = spk_hitung($conn);
$total_menu = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM daftar_menu"))['n'];
$total_kriteria = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM kriteria"))['n'];

$ok = !$H['error'];
$top_labels = []; $top_vals = []; $w_labels = []; $w_vals = [];
$best_top = '-'; $best_ahp = '-';
if($ok){
    $ids = array_keys($H['alt']);
    usort($ids, fn($a,$b)=>$H['top_skor'][$b] <=> $H['top_skor'][$a]);
    $best_top = $H['alt'][$ids[0]]['nama'];
    foreach(array_slice($ids, 0, 10) as $id){
        $top_labels[] = $H['alt'][$id]['nama'] . ' (#' . $id . ')';
        $top_vals[]   = round($H['top_skor'][$id], 5);
    }
    $ia = array_keys($H['alt']);
    usort($ia, fn($a,$b)=>$H['ahp_skor'][$b] <=> $H['ahp_skor'][$a]);
    $best_ahp = $H['alt'][$ia[0]]['nama'];
    foreach($H['kode'] as $c){
        $w_labels[] = $c . ' ' . $H['kriteria'][$c]['nama'];
        $w_vals[]   = round($H['w'][$c], 5);
    }
}
$judul = "Dashboard - Admin";
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

        /* KARTU STATISTIK */
        .stat-card{
            background:var(--card);border:1px solid var(--line);border-radius:20px;
            box-shadow:0 7px 20px rgba(65,38,24,.045);padding:20px 22px;height:100%;
            transition:.3s;position:relative;overflow:hidden;
        }
        .stat-card:hover{transform:translateY(-5px);box-shadow:0 20px 38px rgba(65,38,24,.12);border-color:#f2c5a7}
        .stat-card::before{content:"";position:absolute;left:0;top:0;bottom:0;width:5px;background:var(--accent,var(--primary))}
        .stat-card .label{color:#9a8c84;font-size:11px;font-weight:800;text-transform:uppercase;margin-bottom:6px;letter-spacing:.3px}
        .stat-card .value{font-size:20px;font-weight:800;color:var(--dark)}
        .stat-card .value.sm{font-size:15px;line-height:1.4}
        .stat-card small,.stat-card .note{color:var(--muted);font-size:12px}
        .accent-blue{--accent:#e85d04}
        .accent-green{--accent:#2a9d8f}
        .accent-info{--accent:#f48c06}
        .accent-warn{--accent:#ffb703}

        /* PANEL / CHART */
        .panel{
            background:#fff;border:1px solid var(--line);border-radius:20px;
            box-shadow:0 7px 20px rgba(65,38,24,.045);overflow:hidden;margin-bottom:24px;height:100%;
        }
        .panel-head{padding:18px 24px;border-bottom:1px solid #f5eee8;font-weight:800;font-size:14px;color:var(--primary)}
        .panel-body{padding:22px 24px}

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

        <a href="../logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>

    <div class="main">

        <div class="topbar">
            <div>
                <h1>Dashboard</h1>
                <p class="subtitle">Ringkasan data dan hasil perhitungan metode AHP + TOPSIS.</p>
            </div>
            <a href="../logout.php" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i> Keluar
            </a>
        </div>

        <?php if(!$ok): ?><div class="alert alert-warning"><?= htmlspecialchars($H['error']); ?></div><?php endif; ?>

        <div class="row g-3 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="stat-card accent-blue">
                    <div class="label">Jumlah Alternatif</div>
                    <div class="value"><?= $total_menu; ?> Menu</div>
                    <?php if($ok): ?><small><?= count($H['alt']); ?> menu punya ulasan &amp; dihitung</small><?php endif; ?>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card accent-green">
                    <div class="label">Jumlah Kriteria</div>
                    <div class="value"><?= $total_kriteria; ?> Kriteria</div>
                    <?php if($ok): ?><small><?= $H['total_ulasan']; ?> ulasan valid</small><?php endif; ?>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card accent-info">
                    <div class="label">Metode</div>
                    <div class="value">AHP + TOPSIS</div>
                    <?php if($ok): ?><small>CR = <?= spk_fmt($H['CR'], 4); ?> | Spearman = <?= $H['spearman'] === null ? '-' : spk_fmt($H['spearman'], 4); ?></small><?php endif; ?>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card accent-warn">
                    <div class="label">Alternatif Terbaik</div>
                    <div class="value sm">TOPSIS: <?= htmlspecialchars($best_top); ?></div>
                    <div class="note">AHP: <?= htmlspecialchars($best_ahp); ?></div>
                </div>
            </div>
        </div>

        <?php if($ok): ?>
        <div class="row g-3">
            <div class="col-lg-7">
                <div class="panel">
                    <div class="panel-head">Top 10 Nilai Preferensi TOPSIS (V<sub>i</sub>)</div>
                    <div class="panel-body"><canvas id="chartTop" height="150"></canvas></div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="panel">
                    <div class="panel-head">Distribusi Bobot Kriteria (AHP)</div>
                    <div class="panel-body"><canvas id="chartBobot" height="220"></canvas></div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        new Chart(document.getElementById('chartTop'), {
            type: 'bar',
            data: { labels: <?= json_encode($top_labels); ?>, datasets: [{ label: 'Nilai Preferensi', data: <?= json_encode($top_vals); ?>, backgroundColor: '#e85d04', borderRadius: 6 }] },
            options: { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { min: 0, max: 1 } } }
        });
        new Chart(document.getElementById('chartBobot'), {
            type: 'doughnut',
            data: { labels: <?= json_encode($w_labels); ?>, datasets: [{ data: <?= json_encode($w_vals); ?>,
                backgroundColor: ['#e85d04','#f48c06','#ffb703','#2a9d8f','#9c4a1a','#c9a27e','#6a2c13','#8d99ae'] }] },
            options: { plugins: { legend: { position: 'bottom' } } }
        });
        </script>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>