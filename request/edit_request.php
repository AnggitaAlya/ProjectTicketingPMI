<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require '../config/database.php';

if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan.";
    exit();
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM requests WHERE id = ?");
$stmt->execute([$id]);
$request = $stmt->fetch();

if (!$request) {
    echo "Data tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Request</title>
    <!-- Link Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="dashboard-header position-relative d-flex justify-content-center align-items-center px-4 py-3">
    <h1 class="dashboard-title m-0">Form Edit Request</h1>
    <img src="../assets/images/logo.png" alt="Palang Merah Indonesia" class="logo-pmi position-absolute end-0 me-4">
</div>

<div class="container">
    <div>
        <div class="card p-4" style="max-width:600px; margin: 0 auto;">
        <p class="text-muted">Silahkan ubah data di bawah ini sesuai kebutuhan layanan Anda.</p>
            <form action="request_update.php" method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($request['id']) ?>">

                <div class="form-group">
                    <label for="unit_kerja" class="form-label">Unit Kerja:</label>
                    <select id="unit_kerja" name="unit_kerja" class="form-control" onchange="toggleUnitLain(this)" required>
                        <option value="" disabled>-- Pilih Unit --</option>
                        <option value="Markas" <?= ($request['unit_kerja'] == 'Markas') ? 'selected' : '' ?>>Markas</option>
                        <option value="Diklat" <?= ($request['unit_kerja'] == 'Diklat') ? 'selected' : '' ?>>Diklat</option>
                        <option value="UDD" <?= ($request['unit_kerja'] == 'UDD') ? 'selected' : '' ?>>UDD</option>
                        <option value="PT PMI" <?= ($request['unit_kerja'] == 'PT PMI') ? 'selected' : '' ?>>PT PMI</option>
                        <option value="Polbritada" <?= ($request['unit_kerja'] == 'Polbritada') ? 'selected' : '' ?>>Polbritada</option>
                        <option value="Lainnya" <?= (!in_array($request['unit_kerja'], ['Markas','Diklat','UDD','PT PMI','Polbritada'])) ? 'selected' : '' ?>>Lainnya</option>
                    </select>
                </div>

                <!-- Field untuk input unit kerja lainnya (akan muncul jika memilih Lainnya) -->
                <div class="form-group" id="unit_lain_field" style="display: <?= (!in_array($request['unit_kerja'], ['Markas','Diklat','UDD','PT PMI','Polbritada'])) ? 'block' : 'none' ?>;">
                    <label for="unit_lain" class="form-label">Unit Lainnya</label>
                    <input type="text" id="unit_lain" name="unit_lain" class="form-control" value="<?= (!in_array($request['unit_kerja'], ['Markas','Diklat','UDD','PT PMI','Polbritada'])) ? htmlspecialchars($request['unit_kerja']) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="layanan" class="form-label">Layanan:</label>
                    <input type="text" id="layanan" name="layanan" class="form-control" value="<?= htmlspecialchars($request['layanan']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="keterangan" class="form-label">Keterangan:</label>
                    <textarea id="keterangan" name="keterangan" class="form-control" rows="4" required><?= htmlspecialchars($request['keterangan']) ?></textarea>
                </div>

                <button type="submit" class="btn btn-custom w-100 py-2">Update</button>
            </form>
        </div>

        <div class="d-flex justify-content-between mt-4" style="max-width:600px; margin: 50px auto;">
            <a href="../dashboard/admin_request.php" class="btn btn-secondary">← Daftar Request</a>
            <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</div>

<script>
function toggleUnitLain(select) {
    const unitLainField = document.getElementById('unit_lain_field');
    if (select.value === 'Lainnya') {
        unitLainField.style.display = 'block';
        document.getElementById('unit_lain').required = true;
    } else {
        unitLainField.style.display = 'none';
        document.getElementById('unit_lain').required = false;
    }
}
</script>

<!-- Optional Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>