<?php
session_start();
require_once '../../../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'kasir') {
    header("Location: /post_web/views/auth/login.php");
    exit;
}

include_once '../../layouts/header.php';
include_once '../../layouts/sidebar.php';

$notif_success = $_SESSION['success'] ?? '';
$notif_error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

$valid_query = mysqli_query($conn, "
    SELECT t.id AS transaksi_id, t.waktu, t.total_harga, t.status,
           u.nama_lengkap AS kasir, p.nama_lengkap AS pelanggan
    FROM transaksi t
    LEFT JOIN users u ON t.kasir_id = u.id
    LEFT JOIN pelanggan p ON t.pelanggan_id = p.id
    WHERE t.status = 'valid'
    ORDER BY t.id DESC
");

$pending_query = mysqli_query($conn, "
    SELECT t.id AS transaksi_id, t.waktu, t.total_harga, t.status,
           u.nama_lengkap AS kasir, p.nama_lengkap AS pelanggan
    FROM transaksi t
    LEFT JOIN users u ON t.kasir_id = u.id
    LEFT JOIN pelanggan p ON t.pelanggan_id = p.id
    WHERE t.status = 'pending'
    ORDER BY t.id DESC
");
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Histori Pemesanan</h1>

    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white">
            <strong>✅ Transaksi Valid</strong>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Waktu</th>
                        <th>Penginput</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($t = mysqli_fetch_assoc($valid_query)) : ?>
                        <?php
                        $penginput = '-';
                        if (!empty($t['kasir'])) {
                            $penginput = 'Kasir: ' . htmlspecialchars($t['kasir']);
                        } elseif (!empty($t['pelanggan'])) {
                            $penginput = 'Pelanggan: ' . htmlspecialchars($t['pelanggan']);
                        }
                        ?>
                        <tr>
                            <td><?= $t['transaksi_id'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($t['waktu'])) ?></td>
                            <td><?= $penginput ?></td>
                            <td>Rp <?= number_format($t['total_harga']) ?></td>
                            <td><span class="text-success"><i class="fas fa-check-circle"></i> Valid</span></td>
                            <td>
                                <a href="detail.php?id=<?= $t['transaksi_id'] ?>" class="btn btn-sm btn-info">Detail</a>
                                <button onclick="hapusTransaksi(<?= $t['transaksi_id'] ?>)" class="btn btn-sm btn-danger">Hapus</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <strong>⏳ Transaksi Belum Valid</strong>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Waktu</th>
                        <th>Penginput</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($p = mysqli_fetch_assoc($pending_query)) : ?>
                        <?php
                        $penginput = '-';
                        if (!empty($p['kasir'])) {
                            $penginput = 'Kasir: ' . htmlspecialchars($p['kasir']);
                        } elseif (!empty($p['pelanggan'])) {
                            $penginput = 'Pelanggan: ' . htmlspecialchars($p['pelanggan']);
                        }
                        ?>
                        <tr>
                            <td><?= $p['transaksi_id'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($p['waktu'])) ?></td>
                            <td><?= $penginput ?></td>
                            <td>Rp <?= number_format($p['total_harga']) ?></td>
                            <td><span class="text-warning"><i class="fas fa-clock"></i> Pending</span></td>
                            <td>
                                <a href="detail.php?id=<?= $p['transaksi_id'] ?>" class="btn btn-sm btn-info">Detail</a>
                                <a href="cetak.php?id=<?= $p['transaksi_id'] ?>" target="_blank" class="btn btn-sm btn-secondary">Cetak</a>
                                <a href="validasi.php?id=<?= $p['transaksi_id'] ?>" class="btn btn-sm btn-success">Validasi</a>
                                <button onclick="hapusTransaksi(<?= $p['transaksi_id'] ?>)" class="btn btn-sm btn-danger">Hapus</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function hapusTransaksi(id) {
    Swal.fire({
        title: 'Hapus Transaksi?',
        text: "Data akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'hapus_transaksi.php?id=' + id;
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const success = <?= json_encode($notif_success) ?>;
    const error = <?= json_encode($notif_error) ?>;

    if (success) {
        Swal.fire("Berhasil", success, "success");
    } else if (error) {
        Swal.fire("Gagal", error, "error");
    }
});
</script>

<?php include_once '../../layouts/footer.php'; ?>
