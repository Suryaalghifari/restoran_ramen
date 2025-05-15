<?php
session_start();
require_once __DIR__ . '/../../../config/koneksi.php';
require_once __DIR__ . '/../../../config/base_url.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'kasir') {
    header("Location: ../../auth/login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$result = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
if (!$result) die("Query gagal: " . mysqli_error($conn));
$data = mysqli_fetch_assoc($result);

include_once __DIR__ . '/../../layouts/header.php';
include_once __DIR__ . '/../../layouts/sidebar.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Profil</h1>

    <form action="proses_update.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $data['id'] ?>">

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?= $data['username'] ?>" required>
        </div>

        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" class="form-control" value="<?= $data['nama_lengkap'] ?>" required>
        </div>

        <div class="form-group">
            <label>Password Baru <small>(Kosongkan jika tidak ingin diganti)</small></label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="form-group">
            <label>Foto Profil</label><br>
            <img src="<?= BASE_URL ?>public/img/user/<?= $data['foto'] ?? 'default.png' ?>" width="100" class="img-thumbnail mb-2">
            <input type="file" name="foto" class="form-control-file">
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (isset($_SESSION['success'])): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= $_SESSION['success'] ?>',
        timer: 3000,
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
