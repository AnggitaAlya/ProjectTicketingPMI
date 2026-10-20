<?php
session_start();
$timeout_duration = 1800;

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../auth/login.php?timeout=1");
    exit();
}
$_SESSION['last_activity'] = time();

require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Pegawai sekarang melihat semua data
$query = "
    SELECT 
        r.*, 
        u.username AS pembuat, 
        d.username AS pelaksana
    FROM requests r
    LEFT JOIN users u ON r.user_id = u.id
    LEFT JOIN users d ON r.dikerjakan_oleh = d.id
    ORDER BY r.created_at DESC"
;
$stmt = $pdo->prepare($query);
$stmt->execute();

$requests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= ucfirst($role) ?></title>
    <!-- Link Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<!-- HEADER -->
<div class="dashboard-header position-relative d-flex justify-content-center align-items-center px-4 py-3">
    <h1 class="dashboard-title m-0">Dashboard - Admin</h1>
    <img src="../assets/images/logo.png" alt="Palang Merah Indonesia" class="logo-pmi position-absolute end-0 me-4">
</div>
<div class="container my-5" style="max-width: 80%; padding: 0 15px;">

    <!-- Notifikasi Success/Error -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Navigasi berdasarkan role -->
    <div class="d-flex justify-content-between mb-3">
        <div>
            <?php if ($role == 'pegawai'): ?>
                <a href="../request/input_request.php" class="btn btn-primary">Input Request Baru</a>
            <?php elseif ($role == 'admin'): ?>
                <a href="admin_request.php" class="btn btn-warning">Kelola Request (Admin)</a>
            <?php elseif ($role == 'tim_it'): ?>
                <a href="it_request.php" class="btn btn-info">Validasi Request IT</a>
            <?php endif; ?>
        </div>

        <div>
        <?php if ($role == 'admin'): ?>
            <a href="../auth/register.php" class="btn btn-primary">Daftar Akun</a>
            <?php endif; ?>
            <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <?php if (!$requests): ?>
        <div class="alert alert-warning" role="alert">
            Belum ada data request.
        </div>
    <?php else: ?>

        <div class="table-responsive">
            <table class="custom-dashboard-table">
                <thead>
                    <tr>
                        <th>Unit Kerja</th>
                        <th>Layanan</th>
                        <th>Keterangan</th>
                        <th>Tanggal</th>
                        <th>Status Permohonan</th>
                        <th>Dibuat Oleh</th>
                        <th>Dikerjakan Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                    <tr>
                        <td><?= htmlspecialchars($req['unit_kerja']) ?></td>
                        <td><?= htmlspecialchars($req['layanan']) ?></td>
                        <td><?= htmlspecialchars($req['keterangan']) ?></td>
                        <td><?= date('d-m-Y H:i', strtotime($req['created_at'])) ?></td>
                        <td>
                            <?php
                                $badgeClass = match($req['status']) {
                                    "Belum Dikerjakan" => "badge bg-danger",
                                    "Sedang Dikerjakan" => "badge bg-warning text-dark",
                                    "Selesai" => "badge bg-success",
                                };
                                echo "<span class='$badgeClass'>{$req['status']}</span>";
                            ?>
                        </td>
                        <td><?= htmlspecialchars($req['pembuat'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($req['pelaksana'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
    <?php endif; ?>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>