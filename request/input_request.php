<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['pegawai', 'admin'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Input Request</title>
    <!-- Link Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="dashboard-header position-relative d-flex justify-content-center align-items-center px-4 py-3">
    <h1 class="dashboard-title m-0">Form Input Request</h1>
    <img src="../assets/images/logo.png" alt="Palang Merah Indonesia" class="logo-pmi position-absolute end-0 me-4">
</div>

<div class="container">
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>
    <div>
        <div class="card p-4" style="max-width:600px; margin: 0 auto;">
        <p class="text-muted">Silahkan isi data di bawah ini sesuai kebutuhan layanan Anda.</p>
            <form action="request_insert.php" method="POST">
                <div class="form-group">
                    <label for="unit_kerja">Unit Kerja:</label>
                    <select id="unit_kerja" name="unit_kerja" class="form-control" onchange="toggleUnitLain(this)" required>
                        <option value="" disabled selected>-- Pilih Unit --</option>
                        <option value="Markas">Markas</option>
                        <option value="Diklat">Diklat</option>
                        <option value="UDD">UDD</option>
                        <option value="PT PMI">PT PMI</option>
                        <option value="Polbritada">Polbritada</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="form-group" id="unit_lain_field" style="display: none;">
                    <label for="unit_lain">Unit Lain:</label>
                    <input type="text" name="unit_lain" class="form-control" placeholder="Masukkan nama unit">
                </div>

                <div class="form-group">
                    <label for="layanan">Layanan:</label>
                    <input type="text" name="layanan" class="form-control" placeholder="Contoh: Perbaikan jaringan" required>
                </div>

                <div class="form-group">
                    <label for="keterangan">Keterangan:</label>
                    <textarea name="keterangan" class="form-control" rows="4" placeholder="Deskripsikan permintaan Anda" required></textarea>
                </div>

                <button type="submit" class="btn btn-custom w-100 py-2">Kirim Request</button>
            </form>
        </div>

        <div class="d-flex justify-content-between mt-4" style="max-width:600px; margin: 50px auto;">
            <a href="../dashboard/dashboard.php" class="btn btn-secondary">← Dashboard</a>
            <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</div>

<script>
function toggleUnitLain(select) {
    var field = document.getElementById('unit_lain_field');
    field.style.display = (select.value === 'Lainnya') ? 'block' : 'none';
}
</script>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>