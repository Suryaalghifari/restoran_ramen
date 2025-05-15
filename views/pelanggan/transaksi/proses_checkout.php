<?php
session_start();
header('Content-Type: application/json');
require_once '../../../config/koneksi.php';

if (!isset($_SESSION['pelanggan_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']);
    exit;
}

$pelanggan_id = $_SESSION['pelanggan_id'];
$keranjang = json_decode($_POST['keranjang'] ?? '[]', true);
$total_harga = (int)($_POST['total_harga'] ?? 0);
$alamat = mysqli_real_escape_string($conn, $_POST['alamat'] ?? '');
$metode = $_POST['metode'] ?? 'Tunai';

// Validasi input
if (empty($keranjang) || $total_harga <= 0 || empty($alamat) || empty($metode)) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit;
}

// Upload bukti jika metode transfer/QRIS
$bukti_nama = null;
if (($metode === 'Transfer' || $metode === 'QRIS') && isset($_FILES['bukti']) && $_FILES['bukti']['error'] === 0) {
    $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
    $bukti_nama = 'bukti_' . time() . '.' . $ext;
    $upload_dir = '../../../public/img/bukti/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    move_uploaded_file($_FILES['bukti']['tmp_name'], $upload_dir . $bukti_nama);
}

// Insert transaksi (tanpa kasir_id)
$query = "INSERT INTO transaksi (
    kasir_id, pelanggan_id, waktu, metode_pembayaran, total_harga, jumlah_dibayar, kembalian, status, bukti_pembayaran, alamat_pengiriman
) VALUES (
    NULL, $pelanggan_id, NOW(), '$metode', $total_harga, 0, 0, 'pending', " .
    ($bukti_nama ? "'$bukti_nama'" : "NULL") . ", '$alamat'
)";

if (!mysqli_query($conn, $query)) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan transaksi.']);
    exit;
}

$transaksi_id = mysqli_insert_id($conn);

// Simpan ke transaksi_detail
foreach ($keranjang as $item) {
    $produk_id = (int)$item['id'];
    $harga = (int)$item['harga'];
    $jumlah = (int)$item['jumlah'];
    mysqli_query($conn, "INSERT INTO transaksi_detail (transaksi_id, produk_id, harga, jumlah) 
                         VALUES ($transaksi_id, $produk_id, $harga, $jumlah)");
}

// Sukses
echo json_encode([
    'status' => 'success',
    'message' => 'Pesanan berhasil dikirim! Menunggu validasi.'
]);
