<?php
// Halaman yang sedang dibuka (nama variabel dibuat unik agar tidak menimpa $cur milik halaman)
$sidebarPage = basename($_SERVER['PHP_SELF']);

$sbActive = function(array $files) use ($sidebarPage) {
    return in_array($sidebarPage, $files) ? 'active' : '';
};
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* ===== BODY ===== */
    body{
        font-family:'Plus Jakarta Sans',sans-serif;
        background-color:#f8f9fc;
        display:flex;
        min-height:100vh;
    }

    /* ===== SIDEBAR ===== */
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

    .sidebar-header{
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

    .sidebar-section-title{
        font-size:11px;
        font-weight:800;
        color:#a98f80;
        text-transform:uppercase;
        letter-spacing:.8px;
        margin:16px 10px 8px;
        padding:0;
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
        background:transparent;
    }

    .sidebar a i{
        width:20px;
        margin:0;
        text-align:center;
    }

    .sidebar a:hover{
        background:rgba(255,255,255,.08);
        color:#fff;
        transform:translateX(3px);
    }

    .sidebar a.active{
        color:#fff;
        background:linear-gradient(135deg,#e85d04,#f48c06);
        box-shadow:0 10px 25px rgba(232,93,4,.25);
    }

    .sidebar a.sidebar-logout{
        margin-top:auto;
        color:#ff6b5e;
        border-top:1px solid rgba(255,255,255,.12);
        border-radius:0 0 14px 14px;
        padding-top:16px;
    }

    .sidebar a.sidebar-logout:hover{
        background:rgba(255,107,94,.10);
        color:#ff6b5e;
    }

    /* ===== AREA KONTEN (dipakai dashboard) ===== */
    .main-content{
        flex:1;
        min-width:0;
        margin-left:260px;
    }

    /* .topbar bawaan dashboard, dibatasi hanya di dalam .main-content
       supaya tidak bentrok dengan .topbar milik halaman lain */
    .main-content > .topbar{
        background:white;
        height:70px;
        display:flex;
        align-items:center;
        justify-content:flex-end;
        padding:0 25px;
        box-shadow:0 .15rem 1.75rem 0 rgba(58,59,69,.15);
        margin-bottom:25px;
    }

    /* ===== KARTU DASHBOARD ===== */
    .card-custom{
        border:none;
        border-left:4px solid;
        box-shadow:0 .15rem 1.75rem 0 rgba(58,59,69,.15);
        border-radius:5px;
    }
    .card-custom .card-title{
        font-size:0.7rem;
        font-weight:700;
        text-transform:uppercase;
        margin-bottom:5px;
    }
    .card-blue{ border-left-color:#4e73df; color:#4e73df; }
    .card-green{ border-left-color:#1cc88a; color:#1cc88a; }
    .card-cyan{ border-left-color:#36b9cc; color:#36b9cc; }
    .card-orange{ border-left-color:#f6c23e; color:#f6c23e; }

    .card-value{ color:#5a5c69; font-weight:700; font-size:1.2rem; }

    /* ===== RESPONSIVE ===== */
    @media(max-width:768px){
        body{
            display:block;
        }

        .sidebar{
            position:relative;
            width:100%;
            height:auto;
            padding:18px;
        }

        .sidebar a.sidebar-logout{
            margin-top:12px;
        }

        .main-content{
            margin-left:0;
        }
    }
</style>

<div class="sidebar">
    <div class="sidebar-header">SPK AHP TOPSIS</div>
    <hr>

    <a href="dashboard.php" class="<?= $sbActive(['dashboard.php']) ?>">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>

    <div class="sidebar-section-title">Master Data</div>

    <a href="kriteria.php" class="<?= $sbActive(['kriteria.php','tambah_kriteria.php','edit_kriteria.php']) ?>">
        <i class="fas fa-database"></i> Data Kriteria
    </a>

    <a href="data_menu.php" class="<?= $sbActive(['data_menu.php','tambah_menu.php','edit_menu.php']) ?>">
        <i class="fas fa-book-open"></i> Data Menu
    </a>

    <a href="ulasan.php" class="<?= $sbActive(['ulasan.php','tambah_ulasan.php','edit_ulasan.php']) ?>">
        <i class="fas fa-comments"></i> Data Ulasan
    </a>

    <div class="sidebar-section-title">Perhitungan</div>

    <a href="perhitungan_ahp.php" class="<?= $sbActive(['perhitungan_ahp.php']) ?>">
        <i class="fas fa-chart-line"></i> Perhitungan AHP
    </a>

    <a href="perhitungan_topsis.php" class="<?= $sbActive(['perhitungan_topsis.php']) ?>">
        <i class="fas fa-bullseye"></i> Perhitungan TOPSIS
    </a>

    <a href="hasil_akhir.php" class="<?= $sbActive(['hasil_akhir.php']) ?>">
        <i class="fas fa-trophy"></i> Hasil Akhir (AHP vs TOPSIS)
    </a>

    <div class="sidebar-section-title">Master User</div>

    <a href="data_user.php" class="<?= $sbActive(['data_user.php']) ?>">
        <i class="fas fa-user-cog"></i> Data User
    </a>

    <a href="../index.php" class="sidebar-logout">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</div>