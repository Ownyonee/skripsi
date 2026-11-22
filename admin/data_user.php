<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['role'] != 'admin'){
    header("Location: ../index.php");
    exit;
}
include "../config/koneksi.php";

// Hapus user
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    echo "<script>alert('User berhasil dihapus'); window.location='data_user.php';</script>";
}

// Update role
if(isset($_POST['update_role'])){
    $id = $_POST['user_id'];
    $role = $_POST['role'];
    mysqli_query($conn, "UPDATE users SET role='$role' WHERE id='$id'");
    echo "<script>alert('Role berhasil diubah'); window.location='data_user.php';</script>";
}

// Ambil semua user
$query = mysqli_query($conn, "SELECT * FROM users ORDER BY role DESC, username ASC");
$jumlah = mysqli_num_rows($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data User - SPK AHP TOPSIS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fc;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 240px;
            background-color: #4e73df;
            color: white;
            transition: all 0.3s;
        }
        .sidebar-header {
            padding: 20px;
            font-weight: 700;
            font-size: 1.1rem;
            text-align: center;
        }
        .sidebar-section-title {
            font-size: 0.75rem;
            padding: 10px 20px;
            text-transform: uppercase;
            font-weight: 800;
            color: rgba(255,255,255,0.4);
        }
        .sidebar a {
            display: flex;
            align-items: center;
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .sidebar a i { margin-right: 10px; width: 20px; }
        .sidebar a:hover, .sidebar a.active {
            color: white;
            background: rgba(255,255,255,0.1);
        }

        .main-content { flex: 1; }
        .topbar {
            background: white;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 25px;
            box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15);
            margin-bottom: 25px;
        }

        .card-custom {
            border: none;
            box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15);
            border-radius: 5px;
        }

        .badge-admin {
            background-color: #4e73df;
        }
        .badge-user {
            background-color: #1cc88a;
        }

        table td {
            vertical-align: middle;
        }

        .btn-sm {
            padding: 5px 12px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 30px;
            border: 1px solid #888;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .modal-content h5 {
            margin-bottom: 20px;
            color: #333;
        }
        .modal-content form label {
            display: block;
            margin-bottom: 12px;
            font-weight: 600;
            color: #555;
        }
        .modal-content form select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 1rem;
        }
        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .close-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close-modal:hover {
            color: #000;
        }
    </style>
</head>
<body>

<?php include '../include/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <div class="d-flex align-items-center">
            <span class="me-2 text-gray-600 small">ADMIN</span>
            <img src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff" class="rounded-circle" width="32">
        </div>
    </div>

    <div class="container-fluid">
        <h3 class="h4 mb-4 text-gray-800"><i class="fas fa-user-cog me-2"></i>Data User</h3>

        <div class="card card-custom">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-users me-2"></i>Daftar Pengguna (Total: <?= $jumlah ?> user)
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while($d = mysqli_fetch_assoc($query)){ 
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <i class="fas fa-user me-2 text-primary"></i>
                                    <?= htmlentities($d['username']) ?>
                                </td>
                                <td>
                                    <?php if($d['role'] == 'admin'): ?>
                                        <span class="badge badge-admin">Admin</span>
                                    <?php else: ?>
                                        <span class="badge badge-user">User</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-warning btn-sm" onclick="openEditModal(<?= $d['id'] ?>, '<?= htmlentities($d['username']) ?>', '<?= $d['role'] ?>')"><i class="fas fa-edit"></i> Edit</a>
                                    <?php if($d['role'] != 'admin'): ?>
                                        <a href="data_user.php?hapus=<?= $d['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus user ini?')"><i class="fas fa-trash"></i> Hapus</a>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled><i class="fas fa-lock"></i> Terlindungi</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <?php if($jumlah == 0): ?>
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-2"></i>Tidak ada data user.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Role -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeEditModal()">&times;</span>
        <h5><i class="fas fa-edit me-2"></i>Edit Role User</h5>
        <form method="POST">
            <input type="hidden" id="user_id" name="user_id">
            
            <label for="username_display">Username:</label>
            <input type="text" id="username_display" class="form-control" readonly style="background-color: #f0f0f0; margin-bottom: 20px;">
            
            <label for="role_select">Ubah Role:</label>
            <select id="role_select" name="role" required>
                <option value="">-- Pilih Role --</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            
            <div class="modal-buttons">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeEditModal()">Batal</button>
                <button type="submit" name="update_role" class="btn btn-primary btn-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, username, role) {
        document.getElementById('user_id').value = id;
        document.getElementById('username_display').value = username;
        document.getElementById('role_select').value = role;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Tutup modal jika klik di luar modal
    window.onclick = function(event) {
        const modal = document.getElementById('editModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
</script>

</body>
</html>