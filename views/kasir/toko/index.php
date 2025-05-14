<?php
session_start();
require_once '../../../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'kasir') {
    header("Location: /restoran_ramen/views/auth/login.php");
    exit;
}

// Ambil data pengaturan toko
function getPengaturan($nama) {
    global $conn;
    $q = mysqli_query($conn, "SELECT nilai FROM pengaturan_toko WHERE nama = '$nama'");
    $d = mysqli_fetch_assoc($q);
    return $d['nilai'] ?? '';
}

$nama_toko     = getPengaturan('nama_toko');
$logo          = getPengaturan('logo');
$logo_sidebar  = getPengaturan('logo_sidebar');

include_once '../../layouts/header.php';
include_once '../../layouts/sidebar.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Kelola Toko</h1>

    <form action="proses_update.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Nama Toko</label>
            <input type="text" name="nama_toko" class="form-control" value="<?= htmlspecialchars($nama_toko) ?>" required>
        </div>

        <div class="form-group">
            <label>Logo (favicon)</label><br>
            <?php if ($logo): ?>
                <img src="/restoran_ramen/public/img/toko/<?= $logo ?>" width="50" class="mb-2">
            <?php endif; ?>
            <input type="file" name="logo" class="form-control-file" accept="image/*">
        </div>

        <div class="form-group">
            <label>Logo Sidebar</label><br>
            <?php if ($logo_sidebar): ?>
                <img src="/restoran_ramen/public/img/toko/<?= $logo_sidebar ?>" width="50" class="mb-2">
            <?php endif; ?>
            <input type="file" name="logo_sidebar" class="form-control-file" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Simpan Perubahan
        </button>
    </form>
</div>

<?php include_once '../../layouts/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_SESSION['success'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '<?= $_SESSION['success'] ?>',
    timer: 2000,
    showConfirmButton: false
});
</script>
<?php unset($_SESSION['success']); endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Gagal!',
    text: '<?= $_SESSION['error'] ?>'
});
</script>
<?php unset($_SESSION['error']); endif; ?>
