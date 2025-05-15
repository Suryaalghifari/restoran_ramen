<?php
// Aktifkan error log PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../../../config/koneksi.php';

header('Content-Type: application/json');

// Cek role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'kasir') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

// Ambil data JSON dari body
$input = json_decode(file_get_contents('php://input'), true);

// Data dari sesi dan body
$kasir_id = $_SESSION['user']['id']; // Sesuaikan jika field-nya user_id
$keranjang = $input['keranjang'] ?? [];
$metode = mysqli_real_escape_string($conn, $input['metode'] ?? '');
$jumlah_dibayar = (int)($input['jumlah_dibayar'] ?? 0);
$status = 'pending';

// Validasi input
if (empty($keranjang) || !$metode || $jumlah_dibayar <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit;
}

// Hitung total
$total_harga = 0;
foreach ($keranjang as $item) {
    if (!isset($item['produk_id'], $item['harga'], $item['jumlah'])) {
        echo json_encode(['status' => 'error', 'message' => 'Item keranjang tidak valid.']);
        exit;
    }
    $total_harga += (int)$item['harga'] * (int)$item['jumlah'];
}

$kembalian = $jumlah_dibayar - $total_harga;
$waktu = date('Y-m-d H:i:s');

// Proses transaksi
mysqli_begin_transaction($conn);

try {
    // Simpan ke tabel transaksi
    $stmtTransaksi = mysqli_prepare($conn, "
        INSERT INTO transaksi (kasir_id, waktu, metode_pembayaran, total_harga, jumlah_dibayar, kembalian, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    if (!$stmtTransaksi) {
        throw new Exception("Prepare transaksi gagal: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmtTransaksi, "issiiis", $kasir_id, $waktu, $metode, $total_harga, $jumlah_dibayar, $kembalian, $status);
    if (!mysqli_stmt_execute($stmtTransaksi)) {
        throw new Exception("Gagal menyimpan transaksi: " . mysqli_error($conn));
    }

    $transaksi_id = mysqli_insert_id($conn);

    // Simpan detail transaksi
    $stmtDetail = mysqli_prepare($conn, "
        INSERT INTO transaksi_detail (transaksi_id, produk_id, harga, jumlah)
        VALUES (?, ?, ?, ?)
    ");
    if (!$stmtDetail) {
        throw new Exception("Prepare detail transaksi gagal: " . mysqli_error($conn));
    }

    foreach ($keranjang as $item) {
        $produk_id = (int)$item['produk_id'];
        $harga = (int)$item['harga'];
        $jumlah = (int)$item['jumlah'];

        mysqli_stmt_bind_param($stmtDetail, "iiii", $transaksi_id, $produk_id, $harga, $jumlah);
        if (!mysqli_stmt_execute($stmtDetail)) {
            throw new Exception("Gagal simpan detail: " . mysqli_error($conn));
        }

        // Update stok produk
        $updateStok = mysqli_query($conn, "
            UPDATE produk SET stok = stok - $jumlah WHERE id = $produk_id
        ");
        if (!$updateStok) {
            throw new Exception("Gagal update stok produk: " . mysqli_error($conn));
        }
    }

    // Sukses semua
    mysqli_commit($conn);
    echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil disimpan.']);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
exit;
