<?php
session_start();
require_once '../../../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'kasir') {
    header("Location: /post_web/views/auth/login.php");
    exit;
}

include_once '../../layouts/header.php';
include_once '../../layouts/sidebar.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo "<script>alert('ID tidak valid');window.location='index.php';</script>";
    exit;
}

// Ambil info transaksi
$transaksi = mysqli_query($conn, "
    SELECT t.*, u.nama_lengkap AS kasir, p.nama_lengkap AS pelanggan
    FROM transaksi t
    LEFT JOIN users u ON t.kasir_id = u.id
    LEFT JOIN pelanggan p ON t.pelanggan_id = p.id
    WHERE t.id = $id
");
$data = mysqli_fetch_assoc($transaksi);
if (!$data) {
    echo "<script>alert('Transaksi tidak ditemukan');window.location='index.php';</script>";
    exit;
}

// Ambil detail produk
$detail = mysqli_query($conn, "
    SELECT d.*, pr.nama_produk
    FROM transaksi_detail d
    LEFT JOIN produk pr ON d.produk_id = pr.id
    WHERE d.transaksi_id = $id
");
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Detail Transaksi #<?= $id ?></h1>

    <div class="card mb-4">
    <div class="card-header bg-info text-white">
        <strong>Informasi Transaksi</strong>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th style="width: 30%">Metode Pembayaran</th>
                    <td><?= htmlspecialchars($data['metode_pembayaran']) ?></td>
                </tr>
                <tr>
                    <th>Total</th>
                    <td>Rp <?= number_format($data['total_harga']) ?></td>
                </tr>
                <tr>
                    <th>Jumlah Dibayar</th>
                    <td>Rp <?= number_format($data['jumlah_dibayar']) ?></td>
                </tr>
                <tr>
                    <th>Kembalian</th>
                    <td>Rp <?= number_format($data['kembalian']) ?></td>
                </tr>
                
                <tr>
                    <th>Status</th>
                    <td>
                        <?= $data['status'] === 'valid'
                            ? '<span class="badge badge-success">Valid</span>'
                            : '<span class="badge badge-warning">Pending</span>' ?>
                    </td>
                </tr>
                <tr>
                    <th>Penginput</th>
                    <td>
                        <?= $data['kasir']
                            ? 'Kasir: ' . htmlspecialchars($data['kasir'])
                            : 'Pelanggan: ' . htmlspecialchars($data['pelanggan']) ?>
                    </td>
                </tr>
                <tr>
                    <th>Waktu</th>
                    <td><?= date('d/m/Y H:i', strtotime($data['waktu'])) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


    <div class="card">
        <div class="card-header bg-primary text-white">
            <strong>Rincian Produk</strong>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = mysqli_fetch_assoc($detail)) : ?>
                        <tr>
                            <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                            <td>Rp <?= number_format($item['harga']) ?></td>
                            <td><?= $item['jumlah'] ?></td>
                            <td>Rp <?= number_format($item['harga'] * $item['jumlah']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once '../../layouts/footer.php'; ?>
