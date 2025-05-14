<?php
session_start();
require_once '../../../config/koneksi.php';

$id = $_GET['id'] ?? 0;

// Cek data produk dan gambar lama
$result = mysqli_query($conn, "SELECT gambar FROM produk WHERE id = $id");
$data = mysqli_fetch_assoc($result);

// Hapus gambar jika ada
if ($data && $data['gambar']) {
    $path = "../../../public/img/produk/" . $data['gambar'];
    if (file_exists($path)) {
        unlink($path);
    }
}

// Hapus dari database
$delete = mysqli_query($conn, "DELETE FROM produk WHERE id = $id");

if ($delete) {
    $_SESSION['success'] = "Produk berhasil dihapus.";
} else {
    $_SESSION['error'] = "Gagal menghapus produk.";
}

header("Location: index.php");
exit;
