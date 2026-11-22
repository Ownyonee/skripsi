<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}

$uploadDir = '../images/daftar_menu/';
if(!is_dir($uploadDir)){
    mkdir($uploadDir, 0755, true);
}

if(isset($_POST['simpan'])){
    $nama      = mysqli_real_escape_string($conn, $_POST['nama_menu']);
    $harga     = mysqli_real_escape_string($conn, $_POST['harga']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $gambar    = '';

    if(!empty($_FILES['gambar']['name'])){
        $fileName = basename($_FILES['gambar']['name']);
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed  = ['jpg','jpeg','png','webp'];
        if(in_array($fileExt, $allowed)){
            $newFile = uniqid('menu_', true) . '.' . $fileExt;
            if(move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $newFile)){
                $gambar = 'images/daftar_menu/' . $newFile;
            }
        }
    }

    mysqli_query($conn, "INSERT INTO daftar_menu (nama_menu, harga, gambar, deskripsi) VALUES ('$nama','$harga','$gambar','$deskripsi')");

    echo "<script>alert('Menu berhasil ditambahkan'); window.location='data_menu.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Menu</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
body{
    font-family:'Nunito',sans-serif;
    background:#f8f9fc;
    display:flex;
    min-height:100vh;
}

.main-content{
    flex:1;
}

.topbar{
    background:white;
    height:70px;
    display:flex;
    align-items:center;
    justify-content:flex-end;
    padding:0 25px;
    box-shadow:0 .15rem 1.75rem 0 rgba(58,59,69,.15);
    margin-bottom:25px;
}

.card-custom{
    border:none;
    box-shadow:0 .15rem 1.75rem 0 rgba(58,59,69,.15);
    border-radius:8px;
}
</style>
</head>

<body>

<?php include "../include/sidebar.php"; ?>

<div class="main-content">

    <div class="topbar">
        <div class="d-flex align-items-center">
            <span class="me-2 small">ADMIN</span>
            <img src="https://ui-avatars.com/api/?name=Admin&background=4e73df&color=fff" class="rounded-circle" width="35">
        </div>
    </div>

    <div class="container-fluid">

        <h3 class="mb-4">
            <i class="fas fa-plus-circle me-2 text-success"></i>Tambah Menu
        </h3>

        <div class="card card-custom">
            <div class="card-body">

                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label class="form-label">Nama Menu</label>
                        <input type="text" name="nama_menu" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" name="harga" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" rows="4" class="form-control" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Gambar</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*" required>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="data_menu.php" class="btn btn-secondary ms-2">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>

                </form>

            </div>
        </div>

    </div>
</div>

</body>
</html>
