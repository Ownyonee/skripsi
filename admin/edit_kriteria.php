<?php
session_start();
include '../config/koneksi.php';

// ambil id dari URL
$id = $_GET['id'];

// ambil data kriteria berdasarkan id
$query = mysqli_query($conn, "SELECT * FROM kriteria WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

if(isset($_POST['update'])){

    $kode   = $_POST['kode_kriteria'];
    $nama   = $_POST['nama_kriteria'];
    $bobot  = $_POST['bobot'];
    $tipe   = $_POST['tipe'];

    mysqli_query($conn, "UPDATE kriteria SET 
    kode_kriteria = '$kode',
    nama_kriteria = '$nama',
    bobot = '$bobot',
    tipe = '$tipe'
    WHERE id = '$id'");

    echo "
    <script>
        alert('Data kriteria berhasil diupdate');
        window.location='kriteria.php';
    </script>
    ";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kriteria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '../include/sidebar.php'; ?>

<div class="container mt-5">

    <div class="card shadow">
        <div class="card-header bg-warning text-white">
            <h4 class="mb-0">Edit Data Kriteria</h4>
        </div>

        <div class="card-body">

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Kode Kriteria</label>
                    <input type="text" name="kode_kriteria" class="form-control" 
                           value="<?= $data['kode_kriteria']; ?>" placeholder="Contoh: C1" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Kriteria</label>
                    <input type="text" name="nama_kriteria" class="form-control" 
                           value="<?= $data['nama_kriteria']; ?>" placeholder="Contoh: Harga" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Bobot</label>
                    <input type="number" step="0.0001" name="bobot" class="form-control" 
                           value="<?= $data['bobot']; ?>" placeholder="Contoh: 0.2000" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipe</label>
                    <select name="tipe" class="form-control" required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="benefit" <?= ($data['tipe'] == 'benefit') ? 'selected' : ''; ?>>Benefit</option>
                        <option value="cost" <?= ($data['tipe'] == 'cost') ? 'selected' : ''; ?>>Cost</option>
                    </select>
                </div>

                <button type="submit" name="update" class="btn btn-warning">
                    Update
                </button>

                <a href="kriteria.php" class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </div>
    </div>

</div>

</body>
</html>