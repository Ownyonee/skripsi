<?php
session_start();
include '../config/koneksi.php';

if(isset($_POST['simpan'])){

    $kode   = $_POST['kode_kriteria'];
    $nama   = $_POST['nama_kriteria'];
    $bobot  = $_POST['bobot'];
    $tipe   = $_POST['tipe'];

    mysqli_query($conn, "INSERT INTO kriteria 
    (kode_kriteria, nama_kriteria, bobot, tipe)
    VALUES
    ('$kode','$nama','$bobot','$tipe')");

    echo "
    <script>
        alert('Data kriteria berhasil ditambahkan');
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
    <title>Tambah Kriteria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '../include/sidebar.php'; ?>

<div class="container mt-5">

    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Tambah Data Kriteria</h4>
        </div>

        <div class="card-body">

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Kode Kriteria</label>
                    <input type="text" name="kode_kriteria" class="form-control" placeholder="Contoh: C1" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Kriteria</label>
                    <input type="text" name="nama_kriteria" class="form-control" placeholder="Contoh: Harga" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Bobot</label>
                    <input type="number" step="0.0001" name="bobot" class="form-control" placeholder="Contoh: 0.2000" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipe</label>
                    <select name="tipe" class="form-control" required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="benefit">Benefit</option>
                        <option value="cost">Cost</option>
                    </select>
                </div>

                <button type="submit" name="simpan" class="btn btn-success">
                    Simpan
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