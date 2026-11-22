<?php
// login_admin.php - Halaman login KHUSUS ADMIN (satu-satunya yang memakai session di sisi publik)
session_start();
include "config/koneksi.php";

// Setiap membuka halaman ini (bukan saat submit form), session lama dihapus
// sehingga login SELALU diminta dan tidak langsung masuk ke halaman admin.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = md5($_POST['password']); // sesuai database kamu (disarankan nanti pakai password_hash)

    $stmt = mysqli_prepare($conn, "SELECT username, role FROM users WHERE username=? AND password=? AND role='admin' LIMIT 1");
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($data) {
        session_regenerate_id(true);
        $_SESSION['user'] = $data['username'];
        $_SESSION['role'] = $data['role'];
        header("Location: admin/dashboard.php");
        exit;
    } else {
        $error = "Username / Password admin salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Raja Mentai</title>
    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{
            --orange:#f97316; --orange-dark:#ea580c; --brown:#3b2417;
            --muted:#78716c; --border:#fed7aa;
        }
        *{margin:0;padding:0;box-sizing:border-box}
        body{
            font-family:'Plus Jakarta Sans',sans-serif;
            background:linear-gradient(135deg,#fffaf5 0%,#fff7ed 100%);
            min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;
        }
        .box{
            width:100%;max-width:420px;background:#fff;padding:40px;border-radius:24px;
            border:1px solid var(--border);box-shadow:0 15px 40px rgba(92,48,20,.08);
        }
        .logo{display:block;width:64px;height:64px;border-radius:14px;object-fit:cover;
            border:2px solid var(--border);margin:0 auto 16px}
        h2{text-align:center;color:var(--brown);font-weight:800;font-size:26px;margin-bottom:8px}
        .desc{text-align:center;color:var(--muted);font-size:14px;line-height:1.6;margin-bottom:26px}
        .error{
            background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;border-radius:12px;
            padding:10px 14px;font-size:13px;font-weight:600;margin-bottom:16px;text-align:center;
        }
        .input-group{position:relative;margin-bottom:16px}
        .input-group i{position:absolute;left:16px;top:50%;transform:translateY(-50%);color:#d6bca7}
        input{
            width:100%;padding:14px 16px 14px 45px;border:1px solid #f3dfcf;background:#fffaf5;
            border-radius:14px;outline:none;color:var(--brown);font-family:inherit;font-weight:500;transition:.2s;
        }
        input:focus{border-color:var(--orange);box-shadow:0 0 0 .2rem rgba(249,115,22,.14);background:#fff}
        button{
            width:100%;padding:14px;border:none;border-radius:14px;
            background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;font-size:15px;font-weight:800;
            cursor:pointer;box-shadow:0 7px 18px rgba(249,115,22,.23);transition:.25s;margin-top:6px;font-family:inherit;
        }
        button:hover{background:linear-gradient(135deg,#ea580c,#c2410c);transform:translateY(-2px)}
        .back{display:block;text-align:center;margin-top:20px;font-size:14px;color:var(--orange-dark);
            text-decoration:none;font-weight:700}
        .back:hover{text-decoration:underline}
    </style>
</head>
<body>
    <div class="box">
        <img src="images/logo.jpeg" alt="Raja Mentai" class="logo">
        <h2>Login Admin</h2>
        <p class="desc">Halaman ini khusus untuk admin pengelola menu.</p>

        <?php if ($error): ?>
            <div class="error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="username" placeholder="Username Admin" required autofocus>
            </div>
            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" name="login"><i class="fa-solid fa-right-to-bracket"></i> Login</button>
        </form>

        <a href="index.php" class="back"><i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Menu</a>
    </div>
</body>
</html>