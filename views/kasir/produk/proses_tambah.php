<?php
session_start();
require_once '../../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_produk']);
    $kategori_id = (int) $_POST['kategori_id'];
    $stok = (int) $_POST['stok'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga = (int) str_replace(['Rp', '.', ' '], '', $_POST['harga']);

    $gambar_name = null;
    if (!empty($_FILES['gambar']['name'])) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $nama_slug = strtolower(str_replace(' ', '_', $nama)); // buat nama file dari nama produk
        $gambar_name = $nama_slug . '.' . $ext;
        $upload_path = __DIR__ . '/../../../public/img/produk/' . $gambar_name;
        move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path);
    }
    

    $stmt = mysqli_prepare($conn, "
        INSERT INTO produk (nama_produk, kategori_id, harga, stok, deskripsi, gambar) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    mysqli_stmt_bind_param($stmt, "siiiss", $nama, $kategori_id, $harga, $stok, $deskripsi, $gambar_name);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Produk berhasil ditambahkan.";
    } else {
        $_SESSION['error'] = "Gagal menambahkan produk.";
    }

    header("Location: index.php");
    exit;
}
