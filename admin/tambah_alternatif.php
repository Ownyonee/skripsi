<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['user']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

if(isset($_POST['simpan'])){
    $nama        = mysqli_real_escape_string($conn, $_POST['nama_makanan']);
    $harga       = $_POST['harga'];
    $rasa        = $_POST['rasa'];
    $porsi       = $_POST['porsi'];
    $popularitas = $_POST['popularitas'];
    $tekstur     = $_POST['tekstur'];
    $variasi     = $_POST['variasi_menu'];
    $praktis     = $_POST['kepraktisan_makanan'];
    $value       = $_POST['value_for_money'];

    $query = mysqli_query($conn, "INSERT INTO makanan 
    (nama_makanan, harga, rasa, porsi, popularitas, tekstur, variasi_menu, kepraktisan_makanan, value_for_money) 
    VALUES 
    ('$nama', '$harga', '$rasa', '$porsi', '$popularitas', '$tekstur', '$variasi', '$praktis', '$value')");
    
    if($query){
        echo "<script>alert('Data Alternatif & Penilaian berhasil disimpan!'); window.location='data_alternatif.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Penilaian Alternatif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8f9fc; display: flex; min-height: 100vh; }
        .sidebar { width: 240px; background-color: #4e73df; color: white; transition: all 0.3s; }
        .sidebar-header { padding: 20px; font-weight: 700; font-size: 1.1rem; text-align: center; }
        .sidebar a { display: flex; align-items: center; color: rgba(255,255,255,0.8); padding: 12px 20px; text-decoration: none; font-size: 0.9rem; }
        .sidebar a i { margin-right: 10px; width: 20px; }
        .sidebar a:hover, .sidebar a.active { color: white; background: rgba(255,255,255,0.1); }
        .main-content { flex: 1; }
        .topbar { background: white; height: 70px; display: flex; align-items: center; justify-content: flex-end; padding: 0 25px; box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15); margin-bottom: 25px; }
        .card-custom { border: none; box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15); border-radius: 8px; }
    </style>
</head>
<body>

<?php include '../include/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <div class="d-flex align-items-center">
            <span class="me-2 small fw-bold">ADMIN</span>
            <img src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff" class="rounded-circle" width="32">
        </div>
    </div>

    <div class="container-fluid">
        <h3 class="mb-4"><i class="fas fa-plus-circle text-primary me-2"></i> Tambah Penilaian Makanan (Alternatif)</h3>

        <div class="card card-custom p-4 w-75">
            <form method="POST">
                <div class="mb-4">
                    <label class="form-label fw-bold">Nama Menu Makanan</label>
                    <input type="text" name="nama_makanan" class="form-control" required placeholder="Contoh: Sushi Saus Mentai">
                </div>
                
                <h5 class="mb-3 fw-bold text-primary border-bottom pb-2">Input Nilai Kriteria</h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control" placeholder="Contoh: 15000" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Rasa</label>
                        <input type="number" step="0.1" name="rasa" class="form-control" placeholder="Nilai (Cth: 8)" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Porsi</label>
                        <input type="number" step="0.1" name="porsi" class="form-control" placeholder="Nilai (Cth: 8)" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Popularitas</label>
                        <input type="number" step="0.1" name="popularitas" class="form-control" placeholder="Nilai (Cth: 9)" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tekstur</label>
                        <input type="number" step="0.1" name="tekstur" class="form-control" placeholder="Nilai (Cth: 8)" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Variasi Menu</label>
                        <input type="number" step="0.1" name="variasi_menu" class="form-control" placeholder="Nilai (Cth: 7)" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Kepraktisan</label>
                        <input type="number" step="0.1" name="kepraktisan_makanan" class="form-control" placeholder="Nilai (Cth: 9)" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Value for Money</label>
                        <input type="number" step="0.1" name="value_for_money" class="form-control" placeholder="Nilai (Cth: 8)" required>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <button type="submit" name="simpan" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Penilaian</button>
                    <a href="data_alternatif.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>