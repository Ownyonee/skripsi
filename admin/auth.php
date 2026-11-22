<?php
// admin/auth.php - Proteksi halaman admin.
// Pakai di baris paling atas setiap halaman admin:
//     require_once __DIR__ . "/auth.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cegah halaman admin disimpan di cache (tombol Back setelah logout)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Wajib login sebagai admin
if (!isset($_SESSION['user']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../login_admin.php");
    exit;
}