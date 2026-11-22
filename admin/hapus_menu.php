<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}

if(!isset($_GET['id'])){
    header('Location: data_menu.php');
    exit;
}

$id = intval($_GET['id']);
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM daftar_menu WHERE id='$id'"));
if($data){
    if(!empty($data['gambar']) && file_exists('../' . $data['gambar'])){
        @unlink('../' . $data['gambar']);
    }
    mysqli_query($conn, "DELETE FROM daftar_menu WHERE id='$id'");
}

header('Location: data_menu.php');
exit;
