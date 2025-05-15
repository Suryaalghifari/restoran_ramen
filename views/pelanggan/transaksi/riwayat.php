<?php
session_start();
require_once '../../../config/koneksi.php';
require_once '../../../config/base_url.php';

if (!isset($_SESSION['pelanggan_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$pelanggan_id = $_SESSION['pelanggan_id'];
$pelanggan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pelanggan WHERE id = $pelanggan_id"));

$transaksi = mysqli_query($conn, "SELECT * FROM transaksi WHERE pelanggan_id = $pelanggan_id ORDER BY waktu DESC");

include_once '../header.php';
?>

<!-- CSS styling sesuai contoh -->
<style>
main {
  flex: 1;
}
.table-container {
  background: #ffffff;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 4px 16px rgba(0,0,0,0.08); /* agak lebih gelap */
  color: #212529; /* Tambahan keamanan */
}

.table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}
.table th, .table td {
  border: 1px solid #dee2e6;
  padding: 12px;
  text-align: center;
}
.table thead {
  background-color: #f8f9fa;
}
.badge {
  display: inline-block;
  padding: 5px 10px;
  border-radius: 10px;
  font-weight: 500;
  font-size: 0.85rem;
}
.badge-valid {
  background-color: #198754;
  color: #fff;
}
.badge-pending {
  background-color: #ffc107;
  color: #000;
}
</style>

<main class="main py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-10">
        <div class="table-container">
          <h4 class="mb-3">Riwayat Pesanan Saya</h4>
          <a href="<?= BASE_URL ?>views/pelanggan/index.php" class="btn btn-sm btn-outline-secondary mb-3">
            ← Kembali ke Beranda
          </a>

          <p class="text-muted">Berikut adalah daftar semua transaksi pesanan kamu di Pos Web. Silakan pantau status validasi dari kasir.</p>

          <?php if (mysqli_num_rows($transaksi) === 0): ?>
            <div class="alert alert-info">Kamu belum memiliki transaksi.</div>
          <?php else: ?>
            <p class="mb-3">Total pesanan: <strong><?= mysqli_num_rows($transaksi) ?></strong></p>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Waktu</th>
                    <th>Total</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($t = mysqli_fetch_assoc($transaksi)) : ?>
                    <tr>
                      <td><?= $t['id'] ?></td>
                      <td><?= date('d/m/Y H:i', strtotime($t['waktu'])) ?></td>
                      <td>Rp <?= number_format($t['total_harga']) ?></td>
                      <td>
                        <?php if ($t['status'] === 'valid'): ?>
                          <span class="badge badge-valid">Valid</span>
                        <?php else: ?>
                          <span class="badge badge-pending">Pending</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include_once '../footer.php'; ?>
