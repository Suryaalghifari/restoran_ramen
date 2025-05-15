<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../config/base_url.php';

// 🧪 DEBUG: Cek apakah session aktif dan apa isinya

if (isset($_SESSION['pelanggan_id'])) {
    $id = $_SESSION['pelanggan_id'];

    $query = "SELECT * FROM pelanggan WHERE id = $id";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Query gagal: " . mysqli_error($conn));  // ✅ Lihat pesan error sebenarnya
    }

    $pelanggan = mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ramen No Kami</title>
  <link rel="icon" type="image/png" href="/restoran_ramen/public/img/logoweb.png">
  <link rel="stylesheet" href="<?= BASE_URL ?>views/pelanggan/css/style.css?v=<?= time(); ?>">
</head>
<body class="about-page">

<header class="main-header">
  <div class="container d-flex justify-content-between align-items-center py-3">
    <div class="logo">
      <h1 class="m-0 text-white">Ramen No Kami</h1>
    </div>
    <nav class="nav">
      <a href="<?= BASE_URL ?>views/pelanggan/transaksi/checkout.php">Pesan Sekarang</a>
      <a href="#tentang">Tentang</a>
      <a href="#kontak">Kontak</a>
      <?php if ($pelanggan): ?>
        <span style="color: #ffd080;">Halo, <?= htmlspecialchars($pelanggan['nama_lengkap']) ?></span>
        <a href="<?= BASE_URL ?>views/pelanggan/auth/logout.php" style="color: #ff8080;">Logout</a>
        <a href="<?= BASE_URL ?>views/pelanggan/transaksi/riwayat.php">Riwayat</a>
      <?php else: ?>
        <a href="<?= BASE_URL ?>views/pelanggan/auth/login.php">Login</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
