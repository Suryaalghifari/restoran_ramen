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

// Cek apakah transaksi ada
$cek = mysqli_query($conn, "SELECT * FROM transaksi WHERE id = $id");
$transaksi = mysqli_fetch_assoc($cek);
if (!$transaksi) {
    $_SESSION['error'] = "Transaksi tidak ditemukan.";
    header("Location: index.php");
    exit;
}

// Jalankan penghapusan transaksi dan detailnya (karena ON DELETE CASCADE di DB)
$hapus = mysqli_query($conn, "DELETE FROM transaksi WHERE id = $id");
if ($hapus) {
    $_SESSION['success'] = "Transaksi berhasil dihapus.";
} else {
    $_SESSION['error'] = "Gagal menghapus transaksi.";
}

header("Location: index.php");
exit;
