<?php
session_start();
require_once '../../../config/koneksi.php';

// Cek role kasir
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'kasir') {
    $_SESSION['error'] = "Akses ditolak.";
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['error'] = "ID tidak valid.";
    header("Location: index.php");
    exit;
}

// Validasi status transaksi
$cek = mysqli_query($conn, "SELECT status FROM transaksi WHERE id = $id");
$transaksi = mysqli_fetch_assoc($cek);
if (!$transaksi) {
    $_SESSION['error'] = "Transaksi tidak ditemukan.";
    header("Location: index.php");
    exit;
}

if ($transaksi['status'] === 'valid') {
    $_SESSION['error'] = "Transaksi sudah divalidasi sebelumnya.";
    header("Location: index.php");
    exit;
}

// Update status
$query = mysqli_query($conn, "UPDATE transaksi SET status = 'valid' WHERE id = $id");
if ($query) {
    $_SESSION['success'] = "Transaksi berhasil divalidasi.";
} else {
    $_SESSION['error'] = "Gagal memvalidasi transaksi.";
}

header("Location: index.php");
exit;
