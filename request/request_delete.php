<?php
// File: request/request_delete.php
session_start();

// Cek apakah sudah login dan role adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require '../config/database.php';
include '../config/session_check.php';

// Validasi input ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID tidak valid.";
    header("Location: ../dashboard/admin_request.php");
    exit();
}

$id = (int) $_GET['id'];

// Proses delete
$stmt = $pdo->prepare("DELETE FROM requests WHERE id = ?");
if ($stmt->execute([$id])) {
    $_SESSION['success'] = "Request berhasil dihapus.";
} else {
    $_SESSION['error'] = "Gagal menghapus request.";
}

// Redirect kembali ke halaman admin
header("Location: ../dashboard/admin_request.php");
exit();
?>