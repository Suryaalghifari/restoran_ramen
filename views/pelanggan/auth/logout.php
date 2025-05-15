<?php
session_start();
unset($_SESSION['pelanggan_id']);
unset($_SESSION['pelanggan_nama']);
?>

<!-- Set pesan logout ke localStorage dan redirect ke index pelanggan -->
<script>
  localStorage.setItem('logout_success', 'Sampai jumpa kembali!');
  window.location.href = '../index.php';
</script>
