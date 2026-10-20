<?php
session_start();
require '../config/database.php';

// Cek autentikasi admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Validasi metode request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Metode request tidak valid";
    header("Location: ../dashboard/admin_request.php");
    exit();
}

// Validasi input
if (!isset($_POST['id']) || !isset($_POST['unit_kerja']) || !isset($_POST['layanan']) || !isset($_POST['keterangan'])) {
    $_SESSION['error'] = "Data tidak lengkap";
    header("Location: ../dashboard/admin_request.php");
    exit();
}

$id = $_POST['id'];
$layanan = trim($_POST['layanan']);
$keterangan = trim($_POST['keterangan']);

// Handle unit kerja
$unit_kerja = trim($_POST['unit_kerja']);
if ($unit_kerja === 'Lainnya') {
    if (empty(trim($_POST['unit_lain']))) {
        $_SESSION['error'] = "Harap isi unit kerja lainnya";
        header("Location: ../request/edit_request.php?id=".$id);
        exit();
    }
    $unit_kerja = trim($_POST['unit_lain']);
}

try {
    // Update data request
    $stmt = $pdo->prepare("UPDATE requests SET 
                          unit_kerja = :unit_kerja,
                          layanan = :layanan,
                          keterangan = :keterangan,
                          updated_at = NOW()
                          WHERE id = :id");
    
    $stmt->execute([
        ':unit_kerja' => $unit_kerja,
        ':layanan' => $layanan,
        ':keterangan' => $keterangan,
        ':id' => $id
    ]);

    $_SESSION['success'] = "Request berhasil diupdate";
    header("Location: ../dashboard/admin_request.php");
    exit();

} catch (PDOException $e) {
    $_SESSION['error'] = "Gagal mengupdate request: " . $e->getMessage();
    header("Location: ../request/edit_request.php?id=".$id);
    exit();
}