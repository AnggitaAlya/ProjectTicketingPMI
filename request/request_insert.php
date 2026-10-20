<?php
session_start();
require '../config/database.php';

// Cek login dan role (boleh pegawai atau admin)
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['pegawai', 'admin'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Validasi metode request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: input_request.php");
    exit();
}

// Ambil dan bersihkan input
$unit_kerja = trim($_POST['unit_kerja'] ?? '');
$unit_lain = trim($_POST['unit_lain'] ?? '');
$layanan = trim($_POST['layanan'] ?? '');
$keterangan = trim($_POST['keterangan'] ?? '');
$user_id = $_SESSION['user_id'];

// Validasi wajib isi
if (empty($unit_kerja) || empty($layanan) || empty($keterangan)) {
    $_SESSION['error'] = "Semua field wajib diisi.";
    header("Location: input_request.php");
    exit();
}

// Jika pilih "Lainnya", wajib isi unit_lain
if ($unit_kerja === 'Lainnya') {
    if (empty($unit_lain)) {
        $_SESSION['error'] = "Silakan isi nama unit kerja lainnya.";
        header("Location: input_request.php");
        exit();
    }
    $unit_kerja = $unit_lain;
}

// Pastikan variabel $pdo tersedia
// Jika belum ada, buat koneksi PDO secara langsung
if (!isset($pdo)) {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=ticketing_db", "root", ""); // Ganti sesuai config
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Koneksi gagal: " . $e->getMessage());
    }
}

try {
    $stmt = $pdo->prepare("INSERT INTO requests (user_id, unit_kerja, layanan, keterangan, status, created_at)
                           VALUES (?, ?, ?, ?, 'Belum Dikerjakan', NOW())");
    $stmt->execute([$user_id, $unit_kerja, $layanan, $keterangan]);

    $_SESSION['success'] = "Request berhasil dikirim dan menunggu konfirmasi.";
    header("Location: ../dashboard/dashboard.php");
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = "Gagal mengirim request: " . $e->getMessage();
    header("Location: input_request.php");
    exit();
}
?>