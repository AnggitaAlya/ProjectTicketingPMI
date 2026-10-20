<?php
// File: dashboard/admin_request.php
session_start();
$timeout_duration = 1800;

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../auth/login.php?timeout=1");
    exit();
}
$_SESSION['last_activity'] = time();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include '../config/database.php';

// Ambil data request dengan PDO
$stmt = $pdo->prepare("SELECT * FROM requests ORDER BY id DESC");
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Daftar Request Layanan</title>
    <!-- Link Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<!-- HEADER -->
<div class="dashboard-header position-relative d-flex justify-content-center align-items-center px-4 py-3">
    <h1 class="dashboard-title m-0">Daftar Request Layanan</h1>
    <img src="../assets/images/logo.png" alt="Palang Merah Indonesia" class="logo-pmi position-absolute end-0 me-4">
</div>

<div class="container my-5" style="max-width: 80%; padding: 0 15px;">
    <div class="d-flex justify-content-between mb-3">
        <a href="../request/input_request.php" class="btn btn-success">Tambah Request Baru</a>
        <div>
            <a href="../dashboard/dashboard.php" class="btn btn-secondary me-2">← Dashboard</a>
            <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <?php if (!$requests): ?>
        <div class="alert alert-warning" role="alert">
            Gagal mengambil data request atau tidak ada data.
        </div>
    <?php else: ?>

    <div class="table-responsive">
        <table class="custom-dashboard-table">
            <thead>
                <tr>
                    <th>Unit</th>
                    <th>Layanan</th>
                    <th>Keterangan</th>
                    <th>Status Permohonan</th>
                    <th>Lakukan Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['unit_kerja']) ?></td>
                        <td><?= htmlspecialchars($row['layanan']) ?></td>
                        <td><?= htmlspecialchars($row['keterangan']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td class="text-nowrap" style="vertical-align: middle;">
                            <?php if ($row['status'] == 'Belum Dikerjakan'): ?>
                                <a href="../request/edit_request.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <?php else: ?>
                                <a href="#" class="btn btn-warning btn-sm disabled" aria-disabled="true" style="pointer-events: none">Edit</a>
                            <?php endif; ?>
                            
                            <a href="../request/request_delete.php?id=<?= $row['id'] ?>" 
                                onclick="return confirm('Yakin ingin menghapus data ini?')" 
                                class="btn btn-danger btn-sm ms-1">
                                Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php endif; ?>

</div>

<!-- Optional Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>