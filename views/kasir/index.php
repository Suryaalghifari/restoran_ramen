<?php
include_once '../layouts/header.php';
include_once '../layouts/sidebar.php';
?>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_SESSION['success_login'])): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Login Berhasil!',
        text: '<?= $_SESSION['success_login'] ?>',
        timer: 2500,
        showConfirmButton: false
    });
</script>
<?php unset($_SESSION['success_login']); endif; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Dashboard Kasir</h1>
    <p>Selamat datang, <strong><?= htmlspecialchars($_SESSION['user']['nama_lengkap']) ?></strong>!</p>
</div>

<?php include_once '../layouts/footer.php'; ?>
