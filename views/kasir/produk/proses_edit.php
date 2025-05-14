<?php
session_start();
require_once '../../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) $_POST['id'];
    $nama = trim($_POST['nama_produk']);
    $kategori_id = (int) $_POST['kategori_id'];
    $stok = (int) $_POST['stok'];
    $harga = (int) $_POST['harga'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    $produk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT gambar FROM produk WHERE id = $id"));
    $gambar_lama = $produk['gambar'];
    $gambar_name = $gambar_lama;

    if (!empty($_FILES['gambar']['name'])) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar_name = uniqid('produk_') . '.' . $ext;
        $upload_path = __DIR__ . '/../../../public/img/produk/' . $gambar_name;
        move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path);

        // Hapus gambar lama jika ada
        if (!empty($gambar_lama)) {
            $old_path = __DIR__ . '/../../../public/img/produk/' . $gambar_lama;
            if (file_exists($old_path)) unlink($old_path);
        }
    }

    $stmt = mysqli_prepare($conn, "
        UPDATE produk SET 
            nama_produk = ?, 
            kategori_id = ?, 
            harga = ?, 
            stok = ?, 
            deskripsi = ?, 
            gambar = ? 
        WHERE id = ?
    ");
    mysqli_stmt_bind_param($stmt, "siiissi", $nama, $kategori_id, $harga, $stok, $deskripsi, $gambar_name, $id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Produk berhasil diubah.";
    } else {
        $_SESSION['error'] = "Gagal mengubah produk.";
    }

    header("Location: index.php");
    exit;
}
