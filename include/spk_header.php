<?php /* butuh variabel: $judul, $icon */ ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $judul; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8f9fc; display: flex; min-height: 100vh; }
        .main-content { flex: 1; overflow-x: hidden; }
        .topbar { background: white; height: 70px; display: flex; align-items: center; justify-content: flex-end; padding: 0 25px; box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15); margin-bottom: 25px; }
        .card-custom { border: none; box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15); border-radius: 8px; margin-bottom: 30px; }
        .table-responsive { font-size: 0.85rem; }
        .winner-card { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); color: white; padding: 30px; text-align: center; border-radius: 12px; margin-bottom: 30px; }
        @media print { .no-print, .sidebar { display: none !important; } }
    </style>
</head>
<body>
<?php include __DIR__ . '/sidebar.php'; ?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex align-items-center">
            <span class="me-2 small fw-bold">ADMIN</span>
            <img src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff" class="rounded-circle" width="32">
        </div>
    </div>
    <div class="container-fluid">