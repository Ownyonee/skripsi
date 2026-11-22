<?php
session_start();
include "../config/koneksi.php";
if(!isset($_SESSION['user']) || $_SESSION['role'] != 'admin'){ header("Location: login.php"); exit; }

/* ============ KONFIGURASI (sesuaikan dengan database kamu) ============ */
$TABEL      = 'ulasan';     // nama tabel ulasan
$KOLOM_MENU = 'id_menu';    // kolom di tabel ulasan yang berisi id dari daftar_menu
/* ======================================================================= */

$filter_menu = isset($_GET['menu']) ? (int)$_GET['menu'] : 0;

// Daftar menu untuk dropdown filter
$list_menu = [];
$qm = mysqli_query($conn, "SELECT id, nama_menu FROM daftar_menu ORDER BY nama_menu ASC");
if($qm){ while($m = mysqli_fetch_assoc($qm)){ $list_menu[] = $m; } }

// Ambil data ulasan + nama menu
$where = $filter_menu > 0 ? "WHERE u.`$KOLOM_MENU` = $filter_menu" : "";
$sql = "SELECT u.*, m.nama_menu AS _nama_menu
        FROM `$TABEL` u
        LEFT JOIN daftar_menu m ON m.id = u.`$KOLOM_MENU`
        $where
        ORDER BY u.id DESC";
$result = mysqli_query($conn, $sql);
$error  = $result ? '' : mysqli_error($conn);

$rows = []; $cols = [];
if($result){
    foreach(mysqli_fetch_fields($result) as $f){
        // kolom id, kolom relasi menu, dan alias nama menu ditangani terpisah
        if(!in_array($f->name, ['id', $KOLOM_MENU, '_nama_menu'])){ $cols[] = $f->name; }
    }
    while($r = mysqli_fetch_assoc($result)){ $rows[] = $r; }
}

// Statistik ringkas
$total_ulasan = count($rows);
$menu_diulas  = count(array_unique(array_column($rows, $KOLOM_MENU)));
$rata_rating  = null;
$menu_top     = '-';
$menu_top_val = null;
if(in_array('rating', $cols) && $total_ulasan > 0){
    $rata_rating = array_sum(array_map('floatval', array_column($rows, 'rating'))) / $total_ulasan;

    // menu dengan rata-rata rating tertinggi
    $agg = [];
    foreach($rows as $r){
        $k = $r['_nama_menu'] ?? '(menu dihapus)';
        $agg[$k][] = (float)$r['rating'];
    }
    foreach($agg as $nama => $vals){
        $avg = array_sum($vals) / count($vals);
        if($menu_top_val === null || $avg > $menu_top_val){ $menu_top_val = $avg; $menu_top = $nama; }
    }
}

$judul = "Data Ulasan - Admin";
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
        /* ===== BAGIAN INI SAMA PERSIS DENGAN dashboard.php ===== */
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

        /* PANEL */
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

        /* ===== TAMBAHAN KHUSUS HALAMAN ULASAN (warna mengikuti dashboard) ===== */
        .filter-bar{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:18px}
        .filter-bar .form-control,.filter-bar .form-select{
            border-radius:12px;border:1px solid var(--line);font-size:13px;font-weight:600;
            padding:10px 14px;font-family:inherit;color:var(--dark);
        }
        .filter-bar .form-control:focus,.filter-bar .form-select:focus{
            border-color:var(--primary-2);box-shadow:0 0 0 .2rem rgba(244,140,6,.15);
        }
        .filter-bar .search{flex:1;min-width:200px}
        .filter-bar .menu-select{min-width:220px;max-width:280px}
        .btn-reset{
            background:#fff;color:var(--muted);border:1px solid var(--line);border-radius:12px;
            font-weight:700;font-size:13px;padding:10px 16px;text-decoration:none;transition:.2s;
            display:inline-flex;align-items:center;
        }
        .btn-reset:hover{background:#fff7f0;color:var(--primary)}

        .tbl{width:100%;border-collapse:separate;border-spacing:0;font-size:13.5px}
        .tbl thead th{
            background:#fff4ea;color:var(--primary);font-weight:800;font-size:12px;
            padding:13px 14px;border-bottom:2px solid #f6d9c3;white-space:nowrap;text-align:left;
        }
        .tbl thead th:first-child{border-top-left-radius:12px}
        .tbl thead th:last-child{border-top-right-radius:12px}
        .tbl tbody td{padding:13px 14px;border-bottom:1px solid #f5eee8;vertical-align:top}
        .tbl tbody tr:hover{background:#fffaf5}
        .tbl .col-no{width:50px;text-align:center;color:var(--muted);font-weight:700}
        .tbl .col-aksi{width:90px;text-align:center}
        .tbl .menu-name{font-weight:800;color:var(--dark)}
        .badge-rating{
            display:inline-flex;align-items:center;gap:5px;background:#fff4ea;color:var(--primary);
            font-weight:800;font-size:12px;padding:5px 10px;border-radius:999px;border:1px solid #f6d9c3;
        }
        .badge-rating i{color:#ffb703}
        .btn-del{
            display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;
            border-radius:10px;background:#fff4f2;color:#9f2d22;border:1px solid #f4d8d4;
            text-decoration:none;transition:.2s;
        }
        .btn-del:hover{background:#9f2d22;color:#fff;transform:translateY(-2px)}
        .empty-state{text-align:center;padding:40px 10px;color:var(--muted)}
        .empty-state i{font-size:34px;color:#f2c5a7;margin-bottom:12px;display:block}
        @media(max-width:768px){ .filter-bar .menu-select{max-width:none;width:100%} }
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
                <h1>Data Ulasan</h1>
                <p class="subtitle">Daftar ulasan pelanggan yang dipakai sebagai dasar perhitungan AHP + TOPSIS.</p>
            </div>
            <a href="logout.php" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i> Keluar
            </a>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger">
                <b>Query gagal:</b> <?= htmlspecialchars($error); ?><br>
                <small>Cek nama tabel (<code><?= htmlspecialchars($TABEL); ?></code>) dan kolom relasi menu (<code><?= htmlspecialchars($KOLOM_MENU); ?></code>) di bagian konfigurasi atas file ini.</small>
            </div>
        <?php endif; ?>

        <div class="row g-3 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="stat-card accent-blue">
                    <div class="label">Total Ulasan</div>
                    <div class="value"><?= $total_ulasan; ?> Ulasan</div>
                    <small><?= $filter_menu > 0 ? 'Sesuai filter menu' : 'Semua menu'; ?></small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card accent-green">
                    <div class="label">Menu Diulas</div>
                    <div class="value"><?= $menu_diulas; ?> Menu</div>
                    <small>dari <?= count($list_menu); ?> menu terdaftar</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card accent-info">
                    <div class="label">Rata-rata Rating</div>
                    <div class="value"><?= $rata_rating === null ? '-' : number_format($rata_rating, 2, ',', '.'); ?></div>
                    <small><?= $rata_rating === null ? 'Kolom rating tidak ditemukan' : 'dari seluruh ulasan yang tampil'; ?></small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card accent-warn">
                    <div class="label">Rating Tertinggi</div>
                    <div class="value sm"><?= htmlspecialchars($menu_top); ?></div>
                    <div class="note"><?= $menu_top_val === null ? 'Belum ada data rating' : 'Rata-rata ' . number_format($menu_top_val, 2, ',', '.'); ?></div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head"><i class="fa-solid fa-comments me-2"></i>Daftar Ulasan</div>
            <div class="panel-body">

                <form method="get" class="filter-bar">
                    <input type="text" id="cari" class="form-control search" placeholder="Cari ulasan di tabel...">
                    <select name="menu" class="form-select menu-select" onchange="this.form.submit()">
                        <option value="0">Semua Menu</option>
                        <?php foreach($list_menu as $m): ?>
                            <option value="<?= (int)$m['id']; ?>" <?= $filter_menu == $m['id'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($m['nama_menu']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if($filter_menu > 0): ?>
                        <a href="ulasan.php" class="btn-reset"><i class="fa-solid fa-rotate-left me-1"></i> Reset</a>
                    <?php endif; ?>
                </form>

                <div class="table-responsive">
                    <table class="tbl" id="tabelUlasan">
                        <thead>
                            <tr>
                                <th class="col-no">No</th>
                                <th>Menu</th>
                                <?php foreach($cols as $c): ?>
                                    <th><?= htmlspecialchars(ucwords(str_replace('_', ' ', $c))); ?></th>
                                <?php endforeach; ?>
                                <th class="col-aksi" style="text-align:center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(empty($rows)): ?>
                            <tr>
                                <td colspan="<?= count($cols) + 3; ?>">
                                    <div class="empty-state">
                                        <i class="fa-regular fa-comment-dots"></i>
                                        Belum ada ulasan<?= $filter_menu > 0 ? ' untuk menu ini' : ''; ?>.
                                    </div>
                                </td>
                            </tr>
                        <?php else: $no = 1; foreach($rows as $row): ?>
                            <tr>
                                <td class="col-no"><?= $no++; ?></td>
                                <td class="menu-name"><?= htmlspecialchars($row['_nama_menu'] ?? '(menu dihapus)'); ?></td>
                                <?php foreach($cols as $c): $v = $row[$c]; ?>
                                    <td>
                                    <?php if(stripos($c, 'rating') !== false && is_numeric($v)): ?>
                                        <span class="badge-rating"><i class="fa-solid fa-star"></i> <?= htmlspecialchars($v); ?></span>
                                    <?php else: ?>
                                        <?= nl2br(htmlspecialchars((string)$v)); ?>
                                    <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                                <td class="col-aksi">
                                    <a href="hapus_ulasan.php?id=<?= (int)$row['id']; ?>" class="btn-del"
                                       title="Hapus ulasan"
                                       onclick="return confirm('Yakin ingin menghapus ulasan ini?');">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-warning mt-4 mb-0" style="border-radius:14px;font-size:13px">
                    <i class="fa-solid fa-circle-info me-2"></i>
                    Data ulasan menjadi masukan untuk menghitung nilai tiap kriteria pada setiap menu. Menghapus ulasan akan mengubah hasil perhitungan AHP dan TOPSIS.
                </div>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Pencarian cepat di dalam tabel (tanpa reload)
    document.getElementById('cari').addEventListener('input', function(){
        var q = this.value.toLowerCase();
        document.querySelectorAll('#tabelUlasan tbody tr').forEach(function(tr){
            tr.style.display = tr.textContent.toLowerCase().indexOf(q) > -1 ? '' : 'none';
        });
    });
    </script>
</body>
</html>