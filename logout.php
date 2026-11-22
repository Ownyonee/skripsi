<?php
// logout.php - Hapus session admin sepenuhnya, lalu kembali ke daftar menu
session_start();

// 1. Kosongkan data session
$_SESSION = [];

// 2. Hapus cookie session di browser
if (ini_get("session.use_cookies")) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

// 3. Hancurkan session di server
session_destroy();

// 4. Cegah halaman admin muncul lagi dari cache saat tombol Back ditekan
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// 5. Kembali ke halaman publik (ganti ke login_admin.php kalau mau langsung ke login)
header("Location: index.php");
exit;