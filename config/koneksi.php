<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "restoran_ramen"; // pastikan sesuai dengan nama database kamu

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
