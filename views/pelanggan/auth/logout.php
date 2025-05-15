<?php
session_start();

// ✅ Hapus session khusus pelanggan
unset($_SESSION['pelanggan_id']);
unset($_SESSION['pelanggan_nama']);

// ✅ Tambahan opsional: jika hanya pelanggan yang login
// session_destroy(); // (opsional, gunakan jika tidak ada session lain yang dipakai)

header("Location: login.php");
exit;
