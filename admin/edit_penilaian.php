<?php
session_start();
include "../config/koneksi.php";

if(!isset($_SESSION['user']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location: input_penilaian.php");
    exit;
}

$id = intval($_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM makanan WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

if(!$data){
    echo "<script>alert('Data tidak ditemukan!'); window.location='input_penilaian.php';</script>";
    exit;
}

if(isset($_POST['update'])){
    $harga       = intval($_POST['harga']);
    $rasa        = floatval($_POST['rasa']);
    $porsi       = floatval($_POST['porsi']);
    $popularitas = floatval($_POST['popularitas']);
    $tekstur     = floatval($_POST['tekstur']);
    $variasi     = floatval($_POST['variasi_menu']);
    $praktis     = floatval($_POST['kepraktisan_makanan']);
    $value       = floatval($_POST['value_for_money']);

    $update = mysqli_query($conn, "UPDATE makanan SET 
        harga='$harga', 
        rasa='$rasa', 
        porsi='$porsi', 
        popularitas='$popularitas', 
        tekstur='$tekstur', 
        variasi_menu='$variasi', 
        kepraktisan_makanan='$praktis', 
        value_for_money='$value' 
        WHERE id='$id'");
    
    if($update){
        echo "<script>alert('Data Penilaian Alternatif berhasil diperbarui!'); window.location='input_penilaian.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Penilaian Alternatif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8f9fc; display: flex; min-height: 100vh; }
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
        <h3 class="mb-4"><i class="fas fa-edit text-warning me-2"></i> Edit Penilaian Makanan (Alternatif)</h3>

        <div class="card card-custom p-4 w-75">
            <form method="POST">
                <div class="mb-4">
                    <label class="form-label fw-bold">Nama Menu Makanan</label>
                    <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($data['nama_makanan']); ?>" readonly>
                </div>
                
                <h5 class="mb-3 fw-bold text-primary border-bottom pb-2">Edit Nilai Kriteria (Skala 1 - 10)</h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control" value="<?= htmlspecialchars($data['harga']); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Rasa</label>
                        <input type="number" step="0.1" min="1" max="10" name="rasa" class="form-control" value="<?= htmlspecialchars($data['rasa']); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Porsi</label>
                        <input type="number" step="0.1" min="1" max="10" name="porsi" class="form-control" value="<?= htmlspecialchars($data['porsi']); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Popularitas</label>
                        <input type="number" step="0.1" min="1" max="10" name="popularitas" class="form-control" value="<?= htmlspecialchars($data['popularitas']); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tekstur</label>
                        <input type="number" step="0.1" min="1" max="10" name="tekstur" class="form-control" value="<?= htmlspecialchars($data['tekstur']); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Variasi Menu</label>
                        <input type="number" step="0.1" min="1" max="10" name="variasi_menu" class="form-control" value="<?= htmlspecialchars($data['variasi_menu']); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Kepraktisan</label>
                        <input type="number" step="0.1" min="1" max="10" name="kepraktisan_makanan" class="form-control" value="<?= htmlspecialchars($data['kepraktisan_makanan']); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Value for Money</label>
                        <input type="number" step="0.1" min="1" max="10" name="value_for_money" class="form-control" value="<?= htmlspecialchars($data['value_for_money']); ?>" required>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <button type="submit" name="update" class="btn btn-warning"><i class="fas fa-save me-1"></i> Perbarui Penilaian</button>
                    <a href="input_penilaian.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
