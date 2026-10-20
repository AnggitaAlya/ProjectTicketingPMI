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

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'tim_it') {
    header("Location: ../auth/login.php");
    exit();
}

$role = $_SESSION['role'];

include '../config/database.php';

$stmt = $pdo->prepare("
    SELECT 
        r.*, 
        u.username AS pembuat, 
        d.username AS pelaksana
    FROM requests r
    LEFT JOIN users u ON r.user_id = u.id
    LEFT JOIN users d ON r.dikerjakan_oleh = d.id
    ORDER BY r.created_at DESC
");
$stmt->execute();

$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Request IT</title>
    <!-- Link Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<!-- HEADER -->
<div class="dashboard-header position-relative d-flex justify-content-center align-items-center px-4 py-3">
    <h1 class="dashboard-title m-0">Validasi Request IT</h1>
    <img src="../assets/images/logo.png" alt="Palang Merah Indonesia" class="logo-pmi position-absolute end-0 me-4">
</div>

<div class="container my-5" style="max-width: 80%; padding: 0 15px;">

    <?php if (!$requests): ?>
        <div class="alert alert-warning" role="alert">
            Tidak ada data request.
        </div>
    <?php else: ?>

        <div>
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <a href="../dashboard/dashboard.php" class="btn btn-secondary me-2">← Dashboard</a>
                </div>
                <div>
                    <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
        
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
                        <?php if ($role == 'admin' || $role == 'tim_it') echo "<th>Aksi</th>"; ?>
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

                            <td class="text-nowrap">
                                <?php if ($req['status'] == 'Belum Dikerjakan'): ?>
                                    <a href="../request/update_status.php?id=<?= $req['id'] ?>&status=Sedang Dikerjakan" 
                                    class="btn btn-sm btn-primary">Kerjakan Sekarang</a>
                                <?php elseif ($req['status'] == 'Sedang Dikerjakan'): ?>
                                    <a href="../request/update_status.php?id=<?= $req['id'] ?>&status=Selesai" 
                                    class="btn btn-sm btn-success">Selesaikan</a>
                                <?php elseif ($req['status'] == 'Selesai'): ?>
                                    <button class="btn btn-sm btn-secondary" disabled>Selesai</button>
                                <?php else: ?>
                                    <a href="../request/update_status.php?id=<?= $req['id'] ?>&status=Belum Dikerjakan" 
                                    class="btn btn-sm btn-warning">Reset Status</a>
                                <?php endif; ?>
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