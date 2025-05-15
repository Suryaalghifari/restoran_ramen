<?php
session_start();

// 🔥 Kosongkan session semua role
session_unset();
session_destroy();
session_start();

require_once '../../../config/koneksi.php';

// ✅ Jika sudah login, arahkan ke index pelanggan
if (isset($_SESSION['pelanggan_id'])) {
    header("Location: ../index.php");
    exit;
}

// ✅ Tangani form login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // ✅ Cari di tabel pelanggan, bukan users
    $query = mysqli_query($conn, "SELECT * FROM pelanggan WHERE email = '$email'");
    $user = mysqli_fetch_assoc($query);



    // ✅ Login berhasil
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['pelanggan_id'] = $user['id'];
        $_SESSION['pelanggan_nama'] = $user['nama_lengkap'];

        echo "<script>
            localStorage.setItem('login_success', '" . addslashes($user['nama_lengkap']) . "');
            window.location.href = '../index.php';
        </script>";
        exit;
    } else {
        $_SESSION['login_error'] = "Email atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Pelanggan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../../../sb-admin/css/sb-admin-2.min.css">
    <link rel="stylesheet" href="../../../sb-admin/vendor/fontawesome-free/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gradient-primary">

<div class="container pt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow my-5">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h4 class="text-gray-900">Login Pelanggan</h4>
                        <p>Masuk untuk melanjutkan pemesanan</p>
                    </div>
                    <form method="POST">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                        <hr>
                        <div class="text-center">
                            <a href="register.php">Belum punya akun? Daftar di sini</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="../../../sb-admin/vendor/jquery/jquery.min.js"></script>
<script src="../../../sb-admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../../sb-admin/js/sb-admin-2.min.js"></script>

<!-- SweetAlert error login -->
<?php if (isset($_SESSION['login_error'])): ?>
<script>
    Swal.fire({
        icon: 'error',
        title: 'Login Gagal',
        text: <?= json_encode($_SESSION['login_error']) ?>
    });
</script>
<?php unset($_SESSION['login_error']); endif; ?>
</body>
</html>
