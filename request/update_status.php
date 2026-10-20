<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'tim_it' && $_SESSION['role'] != 'admin')) {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'];
$status = $_GET['status'];
$user_id = $_SESSION['user_id'];

if (!$id || !$status) {
    $_SESSION['error'] = 'Permintaan tidak valid.';
    header("Location: ../dashboard/dashboard.php");
    exit();
}

try {
    if ($status == 'Sedang Dikerjakan') {
        $stmt = $pdo->prepare("UPDATE requests SET status = ?, dikerjakan_oleh = ? WHERE id = ?");
        $stmt->execute([$status, $user_id, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE requests SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }

    $_SESSION['success'] = 'Status berhasil diperbarui.';
} catch (PDOException $e) {
    $_SESSION['error'] = 'Gagal memperbarui status: ' . $e->getMessage();
}

header("Location: ../dashboard/dashboard.php");
exit();
?>