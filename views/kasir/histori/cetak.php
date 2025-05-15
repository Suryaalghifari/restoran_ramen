<?php
session_start();
require_once '../../../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'kasir') {
    die("Akses ditolak");
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die("ID tidak valid");
}

$transaksi = mysqli_query($conn, "
    SELECT t.*, u.nama_lengkap AS kasir, p.nama_lengkap AS pelanggan
    FROM transaksi t
    LEFT JOIN users u ON t.kasir_id = u.id
    LEFT JOIN pelanggan p ON t.pelanggan_id = p.id
    WHERE t.id = $id
");
$data = mysqli_fetch_assoc($transaksi);
if (!$data) die("Transaksi tidak ditemukan");

$detail = mysqli_query($conn, "
    SELECT d.*, pr.nama_produk
    FROM transaksi_detail d
    LEFT JOIN produk pr ON d.produk_id = pr.id
    WHERE d.transaksi_id = $id
");

// Informasi toko
$nama_toko = "Ramen Dapur Karin";
$alamat_toko = "Jl. Contoh Raya No. 123, Bandung";
$telepon = "0812-3456-7890";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Struk Transaksi</title>
    <link rel="icon" type="image/png" href="/restoran_ramen/public/img/logoweb.png">
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            color: #000;
        }
        .struk {
            width: 320px;
            margin: auto;
            padding: 10px;
            border: 1px dashed #000;
        }
        .text-center {
            text-align: center;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 4px;
            text-align: left;
        }
        .line {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .right {
            text-align: right;
        }
        .summary td {
            padding: 2px 4px;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="struk">
        <div class="text-center">
            <div class="title"><?= $nama_toko ?></div>
            <div><?= $alamat_toko ?><br>Telp: <?= $telepon ?></div>
        </div>

        <div class="line"></div>

        <table class="summary">
            <tr><td><strong>ID</strong></td><td class="right">#<?= $data['id'] ?></td></tr>
            <tr><td><strong>Waktu</strong></td><td class="right"><?= date('d/m/Y H:i', strtotime($data['waktu'])) ?></td></tr>
            <tr><td><strong>Kasir</strong></td><td class="right"><?= htmlspecialchars($data['kasir'] ?? '-') ?></td></tr>
        </table>

        <div class="line"></div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="right">Qty</th>
                    <th class="right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = mysqli_fetch_assoc($detail)) : ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                        <td class="right"><?= $item['jumlah'] ?></td>
                        <td class="right">Rp <?= number_format($item['harga'] * $item['jumlah']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="line"></div>

        <table class="summary">
            <tr><td><strong>Total</strong></td><td class="right">Rp <?= number_format($data['total_harga']) ?></td></tr>
            <tr><td><strong>Dibayar</strong></td><td class="right">Rp <?= number_format($data['jumlah_dibayar']) ?></td></tr>
            <tr><td><strong>Kembalian</strong></td><td class="right">Rp <?= number_format($data['kembalian']) ?></td></tr>
        </table>

        <div class="text-center" style="margin-top: 10px">
            <p>Terima kasih telah berbelanja!<br>~ <?= $nama_toko ?> ~</p>
        </div>
    </div>
</body>
</html>
