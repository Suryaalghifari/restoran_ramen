<?php
require_once __DIR__ . '/../../config/koneksi.php';
$user = $_SESSION['user'];
$role = $user['role'];

// Handle foto default jika belum ada
$foto_user = $user['foto'] ?? 'default.png';


// Contoh nama toko default
$nama_toko = 'Ramen DapurKarin';
?>

<!-- Sidebar -->
<ul class="navbar-nav bg-danger sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-store"></i>
        </div>
        <div class="sidebar-brand-text mx-3"><?= $nama_toko ?></div>
    </a>

    <hr class="sidebar-divider">

    <?php if ($role === 'kasir'): ?>
        <li class="nav-item">
            <a class="nav-link" href="/restoran_ramen/views/kasir/index.php">
                <i class="fas fa-home"></i><span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/restoran_ramen/views/kasir/transaksi/index.php">
                <i class="fas fa-cash-register"></i><span>Transaksi</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/restoran_ramen/views/kasir/histori/index.php">
                <i class="fas fa-history"></i><span>Histori Transaksi</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/restoran_ramen/views/kasir/produk/index.php">
                <i class="fas fa-box-open"></i><span>Kelola Produk</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/restoran_ramen/views/kasir/toko/index.php">
                <i class="fas fa-shopping-bag"></i><span>Kelola Toko</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/restoran_ramen/views/kasir/profile/index.php">
                <i class="fas fa-shopping-bag"></i><span>Kelola Kasir</span>
            </a>
        </li>
    <?php endif; ?>

    <hr class="sidebar-divider d-none d-md-block">

    <li class="nav-item">
        <a class="nav-link" href="/restoran_ramen/views/auth/logout.php">
            <i class="fas fa-sign-out-alt"></i><span>Logout</span>
        </a>
    </li>
</ul>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

    <!-- Topbar -->

<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <button id="sidebarToggle" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    <?= htmlspecialchars($user['nama_lengkap']) ?> (<?= ucfirst($role) ?>)
                </span>
                <img class="img-profile rounded-circle"
                     src="/restoran_ramen/public/img/user/<?= $foto_user ?>" width="30" height="30">
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="/restoran_ramen/views/kasir/profil/index.php">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Profil Saya
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="/restoran_ramen/views/auth/logout.php">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
