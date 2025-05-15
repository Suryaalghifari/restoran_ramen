<?php
session_start();
require_once '../../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama       = trim($_POST['nama_lengkap']);
    $username   = trim($_POST['username']);
    $email      = trim($_POST['email']);
    $no_hp      = trim($_POST['no_hp']);
    $alamat     = trim($_POST['alamat']);
    $password   = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi'];

    if ($password !== $konfirmasi) {
        $_SESSION['register_error'] = "Konfirmasi password tidak cocok!";
    } else {
        $cek = mysqli_query($conn, "SELECT * FROM pelanggan WHERE email = '$email'");
        if (!$cek) {
            die("Query Error: " . mysqli_error($conn));
        }

        if (mysqli_num_rows($cek) > 0) {
            $_SESSION['register_error'] = "Email sudah terdaftar!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $simpan = mysqli_query($conn, "INSERT INTO pelanggan (
                nama_lengkap, username, email, password, no_hp, alamat
            ) VALUES (
                '$nama', '$username', '$email', '$hash', '$no_hp', '$alamat'
            )");

            if ($simpan) {
                $_SESSION['pelanggan_id'] = mysqli_insert_id($conn);
                $_SESSION['pelanggan_nama'] = $nama;
                echo "<script>
                    localStorage.setItem('login_success', '" . addslashes($nama) . "');
                    window.location.href = '../index.php';
                </script>";
                exit;
            } else {
                $_SESSION['register_error'] = "Gagal menyimpan data. Coba lagi.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Registrasi Pelanggan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="../../../sb-admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../../../sb-admin/css/sb-admin-2.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gradient-primary">

<div class="container pt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow my-5">
        <div class="card-body">
          <div class="text-center mb-4">
            <h4 class="text-gray-900">Registrasi Akun</h4>
            <p>Isi formulir untuk membuat akun pelanggan</p>
          </div>
          <form method="POST">
            <div class="form-group">
              <label>Nama Lengkap</label>
              <input type="text" name="nama_lengkap" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Username</label>
              <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
              <label>No HP</label>
              <input type="text" name="no_hp" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Alamat</label>
              <textarea name="alamat" class="form-control" required></textarea>
            </div>
            <div class="form-group">
              <label>Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Konfirmasi Password</label>
              <input type="password" name="konfirmasi" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success btn-block">Daftar Sekarang</button>
            <div class="text-center mt-3">
              <a href="login.php">Sudah punya akun? Login</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if (isset($_SESSION['register_error'])): ?>
<script>
  Swal.fire("Gagal", <?= json_encode($_SESSION['register_error']) ?>, "error");
</script>
<?php unset($_SESSION['register_error']); endif; ?>

<script src="../../../sb-admin/vendor/jquery/jquery.min.js"></script>
<script src="../../../sb-admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../../sb-admin/js/sb-admin-2.min.js"></script>
</body>
</html>
