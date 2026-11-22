<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['user']) || $_SESSION['role'] != 'user'){
    header("Location: ../index.php");
    exit;
}

$username = $_SESSION['user'];

// Deteksi nama file aktif untuk saklar otomatis warna biru di sidebar
$current_page = basename($_SERVER['PHP_SELF']);

$message = '';
$error = '';

if(isset($_POST['save_profile'])){
    $new_username = trim($_POST['username'] ?? '');
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if($new_username === ''){
        $error = 'Username tidak boleh kosong.';
    } else {
        $new_username = mysqli_real_escape_string($conn, $new_username);

        if($new_username !== $username){
            $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$new_username'");
            if(mysqli_num_rows($cek) > 0){
                $error = 'Username sudah digunakan oleh user lain.';
            }
        }

        if(!$error && $new_password !== ''){
            if($new_password !== $confirm_password){
                $error = 'Password baru dan konfirmasi password tidak cocok.';
            }
        }

        if(!$error){
            $updateQuery = "UPDATE users SET username='$new_username'";
            if($new_password !== ''){
                $updateQuery .= ", password='".md5($new_password)."'";
            }
            $updateQuery .= " WHERE username='$username'";

            if(mysqli_query($conn, $updateQuery)){
                $message = 'Profil berhasil diperbarui.';
                if($new_username !== $username){
                    $_SESSION['user'] = $new_username;
                    $username = $new_username;
                }
            } else {
                $error = 'Gagal menyimpan perubahan. Silakan coba lagi.';
            }
        }
    }
}

$userData = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE username='$username'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil User</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        *{margin:0;padding:0;box-sizing:border-box}

        :root{
            --orange:#f97316;
            --orange-dark:#ea580c;
            --orange-soft:#fff7ed;
            --cream:#fffaf5;
            --brown:#3b2417;
            --brown-soft:#6b4f3a;
            --text:#292524;
            --muted:#78716c;
            --border:#fed7aa;
            --white:#ffffff;
        }

        body{
            background:linear-gradient(135deg,#fffaf5 0%,#fff7ed 100%);
            font-family:'Plus Jakarta Sans',sans-serif;
            color:var(--text);
            min-height:100vh;
        }

        /* SIDEBAR */
        .sidebar{
            width:270px;
            height:100vh;
            background:linear-gradient(180deg,#3b2417 0%,#24150e 100%);
            position:fixed;
            left:0;
            top:0;
            padding:28px 18px;
            color:#fff;
            z-index:100;
            display:flex;
            flex-direction:column;
            box-shadow:8px 0 30px rgba(59,36,23,.10);
        }

        .sidebar h2{
            font-size:20px;
            font-weight:800;
            margin-bottom:36px;
            padding:0 12px;
            color:#fff7ed;
            display:flex;
            align-items:center;
            gap:10px;
        }

        .sidebar h2 i{color:#fb923c}

        .sidebar a{
            display:flex;
            align-items:center;
            gap:13px;
            color:#d6c3b5;
            text-decoration:none;
            padding:13px 15px;
            border-radius:12px;
            margin-bottom:7px;
            transition:.25s ease;
            font-size:14px;
            font-weight:600;
        }

        .sidebar a i{
            width:20px;
            text-align:center;
            font-size:17px;
        }

        .sidebar a:hover{
            background:rgba(249,115,22,.12);
            color:#fff;
            transform:translateX(2px);
        }

        .sidebar a.active{
            background:linear-gradient(135deg,#f97316,#ea580c);
            color:#fff;
            box-shadow:0 7px 20px rgba(249,115,22,.28);
        }

        .sidebar-footer{
            margin-top:auto;
            padding:15px 12px 5px;
            border-top:1px solid rgba(255,255,255,.10);
            color:#a98f7e;
            font-size:11px;
            line-height:1.6;
        }

        /* MAIN */
        .main{
            margin-left:270px;
            padding:38px 48px 55px;
            min-height:100vh;
            width:calc(100% - 270px);
        }

        .topbar{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:18px;
            margin-bottom:28px;
        }

        .topbar h1{
            font-weight:800;
            color:var(--brown);
            font-size:28px;
            letter-spacing:-.7px;
            margin:0;
        }

        .topbar h1 span{
            color:var(--orange);
        }

        .logout-btn{
            padding:10px 18px;
            background:#fff;
            color:#b91c1c;
            border:1px solid #fecaca;
            border-radius:11px;
            text-decoration:none;
            font-weight:700;
            font-size:13px;
            display:flex;
            align-items:center;
            gap:8px;
            transition:.2s ease;
        }

        .logout-btn:hover{
            background:#fef2f2;
            color:#991b1b;
            transform:translateY(-1px);
        }

        /* PAGE INTRO */
        .page-intro{
            background:linear-gradient(135deg,#fff1df 0%,#fffaf5 100%);
            border:1px solid #fed7aa;
            border-radius:18px;
            padding:24px 26px;
            margin-bottom:22px;
            position:relative;
            overflow:hidden;
        }

        .page-intro:after{
            content:"";
            position:absolute;
            width:130px;
            height:130px;
            border-radius:50%;
            background:rgba(249,115,22,.08);
            right:-30px;
            top:-45px;
        }

        .page-intro h2{
            font-size:21px;
            font-weight:800;
            color:var(--brown);
            margin-bottom:6px;
        }

        .page-intro p{
            color:var(--muted);
            font-size:13px;
            margin:0;
            max-width:650px;
        }

        /* PROFILE CARD */
        .profile-card-modern{
            background:var(--white);
            border:1px solid var(--border)!important;
            border-radius:18px;
            box-shadow:0 12px 35px rgba(92,48,20,.07);
            overflow:hidden;
            max-width:860px;
        }

        .card-header{
            font-weight:800;
            padding:20px 28px;
            font-size:16px;
            background:#fff!important;
            border-bottom:1px solid #f3e2d4;
            color:var(--brown)!important;
            display:flex;
            align-items:center;
            gap:10px;
        }

        .card-header i{
            color:var(--orange)!important;
        }

        .card-body{padding:30px 28px}

        .form-grid-layout{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:17px;
        }

        .profile-input-box{
            background:#fffaf5;
            padding:18px;
            border-radius:14px;
            border:1px solid #f3dfcf;
            transition:.2s ease;
        }

        .profile-input-box:hover{
            border-color:#fdba74;
            box-shadow:0 5px 16px rgba(249,115,22,.06);
        }

        .profile-input-box label{
            display:block;
            font-weight:800;
            color:#57534e;
            margin-bottom:8px;
            font-size:11px;
            text-transform:uppercase;
            letter-spacing:.6px;
        }

        .profile-input-box input{
            width:100%;
            border-radius:10px;
            border:1px solid #e7d5c6;
            padding:11px 13px;
            font-size:14px;
            color:#3b3028;
            background:#fff;
            transition:.2s ease;
        }

        .profile-input-box input::placeholder{color:#aaa09a}

        .profile-input-box input:focus{
            outline:none;
            box-shadow:0 0 0 4px rgba(249,115,22,.12);
            border-color:var(--orange);
        }

        .role-text-badge{
            font-size:13px;
            font-weight:800;
            color:#c2410c;
            background:#ffedd5;
            padding:8px 13px;
            border-radius:9px;
            display:inline-flex;
            align-items:center;
        }

        /* ALERTS */
        .alert-modern{
            padding:13px 17px;
            border-radius:11px;
            margin-bottom:20px;
            font-size:13px;
            font-weight:600;
            display:flex;
            align-items:center;
            gap:9px;
        }

        .alert-success-modern{
            background:#f0fdf4;
            color:#166534;
            border:1px solid #bbf7d0;
        }

        .alert-error-modern{
            background:#fef2f2;
            color:#991b1b;
            border:1px solid #fecaca;
        }

        /* BUTTONS */
        .action-flex-gap{
            margin-top:25px;
            display:flex;
            gap:10px;
            flex-wrap:wrap;
        }

        .btn-submit-premium{
            background:linear-gradient(135deg,#f97316 0%,#ea580c 100%);
            border:none;
            color:#fff;
            padding:12px 20px;
            border-radius:11px;
            font-weight:800;
            font-size:13px;
            box-shadow:0 6px 17px rgba(249,115,22,.22);
            transition:.25s ease;
        }

        .btn-submit-premium:hover{
            transform:translateY(-2px);
            box-shadow:0 9px 23px rgba(249,115,22,.28);
            background:linear-gradient(135deg,#ea580c 0%,#c2410c 100%);
        }

        .btn-link-secondary{
            background:#fff7ed;
            color:#9a3412;
            text-decoration:none;
            padding:12px 20px;
            border-radius:11px;
            font-weight:800;
            font-size:13px;
            border:1px solid #fed7aa;
            transition:.2s ease;
            display:inline-flex;
            align-items:center;
        }

        .btn-link-secondary:hover{
            background:#ffedd5;
            color:#7c2d12;
            transform:translateY(-1px);
        }

        @media(max-width:900px){
            .sidebar{
                width:235px;
            }
            .main{
                margin-left:235px;
                width:calc(100% - 235px);
                padding:30px;
            }
        }

        @media(max-width:768px){
            .sidebar{
                position:relative;
                width:100%;
                height:auto;
                padding:18px;
            }

            .sidebar h2{margin-bottom:18px}

            .sidebar a{
                display:inline-flex;
                width:auto;
                margin-right:4px;
            }

            .sidebar-footer{display:none}

            .main{
                margin-left:0;
                padding:25px 18px 40px;
                width:100%;
            }

            .topbar{
                align-items:flex-start;
            }

            .topbar h1{font-size:22px}

            .form-grid-layout{
                grid-template-columns:1fr;
            }

            .profile-card-modern{
                max-width:100%;
            }
        }

        @media(max-width:520px){
            .topbar{
                flex-direction:column;
            }

            .logout-btn{
                align-self:flex-start;
            }

            .page-intro{
                padding:20px;
            }

            .card-body{
                padding:22px 18px;
            }

            .card-header{
                padding:17px 18px;
            }

            .sidebar a{
                font-size:12px;
                padding:10px 11px;
            }

            .action-flex-gap>*{
                width:100%;
                justify-content:center;
            }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2><i class="fa-solid fa-circle-nodes"></i> User Panel</h2>
        <a href="dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'active' : ''; ?>"><i class="fa-solid fa-house"></i> Dashboard</a>
        <a href="rekomendasi.php" class="<?= ($current_page == 'rekomendasi.php') ? 'active' : ''; ?>"><i class="fa-solid fa-utensils"></i> Rekomendasi Makanan</a>
        <a href="riwayat_perhitungan.php" class="<?= ($current_page == 'riwayat_perhitungan.php') ? 'active' : ''; ?>"><i class="fa-solid fa-history"></i> Riwayat Perhitungan</a>
        <a href="profil.php" class="<?= ($current_page == 'profil.php') ? 'active' : ''; ?>"><i class="fa-solid fa-user"></i> Profil</a>

        <div class="sidebar-footer">
            <i class="fa-solid fa-utensils"></i> Sistem Rekomendasi Makanan<br>
            Temukan pilihan makanan yang sesuai dengan preferensimu.
        </div>
    </div>

    <div class="main">
        
        <div class="topbar">
            <h1>Hai, <span><?= htmlentities($username) ?></span> 👋</h1>
            <a href="../logout.php" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i> Keluar
            </a>
        </div>

        <div class="page-intro">
            <h2>Kelola Profil Kamu 👤</h2>
            <p>Perbarui username atau kata sandi akun dengan mudah. Data profilmu tetap tersimpan aman di sistem.</p>
        </div>

        <div class="profile-card-modern">
            <div class="card-header">
                <i class="fa-solid fa-user-gear text-primary"></i> Pengaturan Profil Pengguna
            </div>
            <div class="card-body">
                
                <?php if($message): ?>
                    <div class="alert-modern alert-success-modern">
                        <i class="fa-solid fa-circle-check"></i> <?= $message ?>
                    </div>
                <?php endif; ?>
                
                <?php if($error): ?>
                    <div class="alert-modern alert-error-modern">
                        <i class="fa-solid fa-circle-exclamation"></i> <?= $error ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-grid-layout">
                        
                        <div class="profile-input-box">
                            <label for="username">Username Sistem</label>
                            <input type="text" id="username" name="username" value="<?= htmlentities($userData['username'] ?? $username) ?>" required>
                        </div>
                        
                        <div class="profile-input-box">
                            <label for="password">Kata Sandi Baru</label>
                            <input type="password" id="password" name="password" placeholder="Biarkan kosong jika tidak ingin ganti">
                        </div>
                        
                        <div class="profile-input-box">
                            <label for="confirm_password">Konfirmasi Kata Sandi</label>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Ketikan ulang kata sandi baru">
                        </div>
                        
                        <div class="profile-input-box">
                            <label>Hak Akses Sistem (Role)</label>
                            <span class="role-text-badge">
                                <i class="fa-solid fa-shield-halved me-1"></i> <?= htmlentities($userData['role'] ?? 'user') ?>
                            </span>
                        </div>
                        
                    </div>

                    <div class="action-flex-gap">
                        <button type="submit" name="save_profile" class="btn-submit-premium">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                        </button>
                        <a href="rekomendasi.php" class="btn-link-secondary">
                            <i class="fa-solid fa-utensils me-2"></i> Mulai Hitung Rekomendasi
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>