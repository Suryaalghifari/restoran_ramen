<?php
session_start();
require_once '../../config/koneksi.php';
require_once '../../config/base_url.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'kasir') {
    header("Location: ../auth/login.php");
    exit;
}

include_once '../layouts/header.php';
include_once '../layouts/sidebar.php';

// Hari ini
$tanggal_hari_ini = date('Y-m-d');

// Ringkasan data
$transaksi_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi WHERE DATE(waktu) = '$tanggal_hari_ini'"));
$pendapatan_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) AS total FROM transaksi WHERE DATE(waktu) = '$tanggal_hari_ini' AND status = 'valid'"));
$produk_habis = mysqli_query($conn, "SELECT nama_produk FROM produk WHERE stok <= 0");

// Transaksi pending terakhir
$pending_dashboard = mysqli_query($conn, "
    SELECT t.id AS transaksi_id, t.waktu, t.total_harga, t.status,
           u.nama_lengkap AS kasir, p.nama_lengkap AS pelanggan
    FROM transaksi t
    LEFT JOIN users u ON t.kasir_id = u.id
    LEFT JOIN pelanggan p ON t.pelanggan_id = p.id
    WHERE t.status = 'pending'
    ORDER BY t.id DESC
    LIMIT 5
");

// Grafik transaksi
$grafik = mysqli_query($conn, "
    SELECT DATE(waktu) AS tanggal, COUNT(*) AS total
    FROM transaksi
    GROUP BY DATE(waktu)
    ORDER BY tanggal DESC
    LIMIT 7
");
$labelBar = []; $dataBar = [];
while ($row = mysqli_fetch_assoc($grafik)) {
    $labelBar[] = date('d M', strtotime($row['tanggal']));
    $dataBar[] = (int) $row['total'];
}
$labelBar = array_reverse($labelBar);
$dataBar = array_reverse($dataBar);
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Dashboard Kasir</h1>

    <?php if (isset($_SESSION['success_login'])): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Login Berhasil!',
            text: '<?= $_SESSION['success_login']; ?>',
            timer: 3000,
            showConfirmButton: false
        });
    </script>
    <?php unset($_SESSION['success_login']); endif; ?>

    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-left-primary shadow h-100 p-3">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Transaksi Hari Ini</div>
                <div class="h5 mb-0 font-weight-bold"><?= $transaksi_hari_ini['total'] ?? 0 ?> transaksi</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-left-success shadow h-100 p-3">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pendapatan Hari Ini</div>
                <div class="h5 mb-0 font-weight-bold">Rp <?= number_format($pendapatan_hari_ini['total'] ?? 0) ?></div>
            </div>
        </div>
        <div class="col-md-4 mb-3 d-flex flex-column justify-content-between">
            <a href="<?= BASE_URL ?>views/kasir/transaksi/index.php" class="btn btn-success btn-lg mb-2">
                <i class="fas fa-cash-register"></i> Transaksi Baru
            </a>
            <a href="<?= BASE_URL ?>views/kasir/transaksi/riwayat.php" class="btn btn-secondary btn-lg">
                <i class="fas fa-history"></i> Riwayat Penjualan
            </a>
        </div>
    </div>

    <!-- Produk Habis -->
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">Produk Stok Habis</div>
        <div class="card-body">
            <?php if (mysqli_num_rows($produk_habis) > 0): ?>
                <ul class="list-group">
                    <?php while ($p = mysqli_fetch_assoc($produk_habis)) : ?>
                        <li class="list-group-item"><?= htmlspecialchars($p['nama_produk']) ?></li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p class="text-success">Semua produk tersedia.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Transaksi Pending -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">⏳ Transaksi Belum Valid</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm">
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
                    <?php if (mysqli_num_rows($pending_dashboard) > 0): ?>
                        <?php while ($p = mysqli_fetch_assoc($pending_dashboard)) : ?>
                            <tr>
                                <td><?= $p['transaksi_id'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($p['waktu'])) ?></td>
                                <td><?= $p['kasir'] ? 'Kasir: ' . htmlspecialchars($p['kasir']) : 'Pelanggan: ' . htmlspecialchars($p['pelanggan']) ?></td>
                                <td>Rp <?= number_format($p['total_harga']) ?></td>
                                <td><span class="text-warning"><i class="fas fa-clock"></i> Pending</span></td>
                                <td>
                                    <a href="<?= BASE_URL ?>views/kasir/transaksi/detail.php?id=<?= $p['transaksi_id'] ?>" class="btn btn-sm btn-info">Detail</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted">Tidak ada transaksi pending.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Grafik -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-info">
            <h6 class="m-0 font-weight-bold text-white">📊 Jumlah Transaksi (7 Hari Terakhir)</h6>
        </div>
        <div class="card-body">
            <canvas id="myBarChart"></canvas>
        </div>
    </div>
</div>

<?php include_once '../layouts/footer.php'; ?>

<!-- Chart.js + Grafik -->
<script src="<?= BASE_URL ?>sb-admin/vendor/chart.js/Chart.min.js"></script>
<script>
    const ctx = document.getElementById("myBarChart");
    const myBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labelBar) ?>,
            datasets: [{
                label: "Transaksi",
                backgroundColor: "#4e73df",
                borderColor: "#4e73df",
                data: <?= json_encode($dataBar) ?>
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
