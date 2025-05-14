<?php
session_start();
require_once '../../config/koneksi.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' AND role = 'kasir'");
    $user = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        $_SESSION['success_login'] = "Selamat datang, {$user['nama_lengkap']}!";
        header("Location: ../kasir/index.php");
        exit;
    } else {
        $error = "Username atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Kasir</title>
    <link rel="stylesheet" href="../../sb-admin/css/sb-admin-2.min.css">
    <link rel="stylesheet" href="../../sb-admin/vendor/fontawesome-free/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gradient-primary">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-body">
                        <h4 class="text-center mb-3">Login Kasir</h4>
                        <form method="POST">
                            <div class="form-group">
                                <label>Username</label>
                                <input name="username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input name="password" type="password" class="form-control" required>
                            </div>
                            <button class="btn btn-primary btn-block">Login</button>
                        </form>
                        <?php if ($error): ?>
                            <script>
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Login Gagal',
                                    text: '<?= $error ?>'
                                });
                            </script>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['logout_success'])): ?>
                            <script>
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Logout Berhasil!',
                                    text: '<?= $_SESSION['logout_success'] ?>',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            </script>
                            <?php unset($_SESSION['logout_success']); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
