<?php
session_start();
require_once __DIR__ . '/../../../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'kasir') {
    header("Location: ../../auth/login.php");
    exit;
}

$id = $_POST['id'];
$username = mysqli_real_escape_string($conn, $_POST['username']);
$nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
$password = $_POST['password'];
$foto = null;

// Upload foto jika ada
if (isset($_FILES['foto']) && $_FILES['foto']['name']) {
    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $nama_file = 'kasir_' . time() . '.' . $ext;
    $upload_path = __DIR__ . '/../../../public/img/user/' . $nama_file;

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
        $foto = $nama_file;
    }
}

// SQL update
$set_password = $password ? ", password = '" . password_hash($password, PASSWORD_DEFAULT) . "'" : "";
$set_foto = $foto ? ", foto = '$foto'" : "";

$query = "
    UPDATE users 
    SET username = '$username', nama_lengkap = '$nama' 
    $set_password
    $set_foto
    WHERE id = $id
";

if (mysqli_query($conn, $query)) {
    $_SESSION['success'] = "Profil berhasil diperbarui.";
    $_SESSION['user']['nama_lengkap'] = $nama;
    $_SESSION['user']['username'] = $username;

    // ⬅️ Tambahkan ini jika foto berhasil diupload
    if ($foto) {
        $_SESSION['user']['foto'] = $foto;
    }

    header("Location: index.php");
} else {
    $_SESSION['error'] = "Gagal memperbarui profil.";
    header("Location: index.php");
}
exit;

