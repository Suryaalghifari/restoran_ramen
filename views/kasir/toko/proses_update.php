<?php
session_start();
require_once '../../../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'kasir') {
    header("Location: /restoran_ramen/views/auth/login.php");
    exit;
}

// Fungsi menyimpan/update ke tabel
function simpanPengaturan($nama, $nilai) {
    global $conn;
    $nama  = mysqli_real_escape_string($conn, $nama);
    $nilai = mysqli_real_escape_string($conn, $nilai);
    mysqli_query($conn, "
        INSERT INTO pengaturan_toko (nama, nilai)
        VALUES ('$nama', '$nilai')
        ON DUPLICATE KEY UPDATE nilai = '$nilai'
    ");
}

// Upload folder
$uploadDir = __DIR__ . '/../../../public/img/toko/';

try {
    // Simpan nama toko
    if (isset($_POST['nama_toko'])) {
        simpanPengaturan('nama_toko', $_POST['nama_toko']);
    }

    // Upload logo (favicon)
    if (!empty($_FILES['logo']['name'])) {
        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $namaLogo = 'logo.' . $ext;
        move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $namaLogo);
        simpanPengaturan('logo', $namaLogo);
    }

    // Upload logo sidebar
    if (!empty($_FILES['logo_sidebar']['name'])) {
        $ext2 = pathinfo($_FILES['logo_sidebar']['name'], PATHINFO_EXTENSION);
        $namaSidebar = 'logo_sidebar.' . $ext2;
        move_uploaded_file($_FILES['logo_sidebar']['tmp_name'], $uploadDir . $namaSidebar);
        simpanPengaturan('logo_sidebar', $namaSidebar);
    }

    $_SESSION['success'] = "Pengaturan toko berhasil diperbarui.";
} catch (Exception $e) {
    $_SESSION['error'] = "Gagal menyimpan pengaturan toko.";
}

header("Location: index.php");
exit;
