<?php
require_once '../../config/base_url.php';
include_once 'produk_dinamis.php';
include_once 'header.php';
?>

<!-- HERO SECTION -->
<section class="about-hero with-bg">
  <div class="overlay"></div>
  <div class="about-text">
    <h1>All we need<br><span>is ramen</span></h1>
    <p>
      We cook, simmer, and serve our ramen with heart.
      Every bowl tells a story of Japanese flavor and tradition.
    </p>
  </div>
</section>

<!-- PRODUK SECTION -->
<section id="Ramen" class="menu-section">
  <h1>Ramen</h1>
  <div class="menu-grid">
    <?php tampilkanProduk($data_ramen); ?>
  </div>

  <h1>Savory Snack</h1>
  <div class="beverage-row">
    <?php tampilkanProduk($data_snack, 'beverage'); ?>
  </div>

  <h1>Beverage</h1>
  <div class="beverage-row">
    <?php tampilkanProduk($data_minuman, 'beverage'); ?>
  </div>
</section>

<!-- TENTANG KAMI -->
<section id="tentang">
  <div class="container">
    <h2>Tentang Kami</h2>
    <p>
      Ramen no Kami menyajikan ramen autentik Jepang dengan kuah kaldu yang kaya rasa,
      mie kenyal, dan bahan segar pilihan. Kami hadir untuk memberi pengalaman makan
      yang hangat, lezat, dan berkesan dalam setiap mangkuknya.
    </p>
  </div>
</section>

<!-- KONTAK -->
<section id="kontak">
  <div class="container">
    <h2>Kontak</h2>
    <p>Email: Ramenokamihahaha@gmail.com</p>
    <p>Alamat: Jalanin Dulu Aja</p>
    <p>Instagram: @Ramennukamuu</p>
  </div>
</section>

<!-- ANGGOTA -->
<section id="anggota">
  <div class="container">
    <h2>Anggota Kelompok</h2>
    <p>Adelia Putri Karin - 240111001</p>
    <p>Azmi Nurul Aini - 240111008</p>
    <p>Febby Rizka Syawaldianti - 240111009</p>
    <p>Annisa Ainurrohmah - 240111030</p>
    <p>Tita Abelia Rosmansyah - 240111073</p>
    <p>Mohammad Syafiq - 240111158</p>
  </div>
</section>

<?php include_once 'footer.php'; ?>

<!-- SWEETALERT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // ✅ Login Success
  const successName = localStorage.getItem('login_success');
  if (successName) {
    Swal.fire({
      icon: 'success',
      title: 'Login Berhasil',
      text: 'Selamat datang, ' + successName,
      timer: 3000,
      showConfirmButton: false
    });
    localStorage.removeItem('login_success');
  }

  // ✅ Logout Success
  const logoutMsg = localStorage.getItem('logout_success');
  if (logoutMsg) {
    Swal.fire({
      icon: 'success',
      title: 'Logout Berhasil',
      text: logoutMsg,
      timer: 2500,
      showConfirmButton: false
    });
    localStorage.removeItem('logout_success');
  }

  // ✅ Akses Ditolak (jika belum login & coba checkout)
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('akses') === 'ditolak') {
    Swal.fire({
      icon: 'warning',
      title: 'Akses Ditolak',
      text: 'Silakan login terlebih dahulu untuk melakukan checkout.',
      confirmButtonText: 'Login Sekarang'
    }).then(() => {
      window.location.href = 'auth/login.php';
    });
  }
</script>
