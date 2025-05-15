<?php
session_start();
require_once '../../../config/koneksi.php';
require_once '../../../config/base_url.php';

if (!isset($_SESSION['pelanggan_id'])) {
  echo "
  <!DOCTYPE html>
  <html lang='id'>
  <head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Akses Ditolak</title>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
  </head>
  <body>
    <script>
      Swal.fire({
        icon: 'warning',
        title: 'Akses Ditolak',
        text: 'Silakan login terlebih dahulu untuk checkout.',
        showCancelButton: true,
        confirmButtonText: 'Login Sekarang',
        cancelButtonText: 'Kembali'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = '../auth/login.php';
        } else {
          window.location.href = '../index.php';
        }
      });
    </script>
  </body>
  </html>";
  exit;
}


$pelanggan_id = $_SESSION['pelanggan_id'];
$pelanggan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pelanggan WHERE id = $pelanggan_id"));
$produk = mysqli_query($conn, "SELECT * FROM produk ORDER BY nama_produk ASC");

include '../header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>
  .produk-card:hover {
    transform: scale(1.02);
    transition: 0.3s ease;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
  }
  .card-img-top {
    height: 140px;
    object-fit: cover;
  }
</style>

<main class="main py-5">
  <div class="container">
    <div class="row">
      <!-- Produk -->
      <div class="col-md-8 mb-4">
        <div class="bg-white p-4 shadow rounded">
          <h4 class="mb-4">Pilih Produk</h4>
          <div class="row" id="produk-list"></div>
        </div>
      </div>

      <!-- Keranjang -->
      <div class="col-md-4">
        <div class="bg-white p-4 shadow rounded">
          <h4 class="mb-3">Keranjang</h4>
          <form id="formCheckout" method="POST" enctype="multipart/form-data">
            <table class="table table-sm table-bordered mb-3" id="tabel-keranjang">
              <thead class="text-center">
                <tr>
                  <th>Produk</th>
                  <th>Qty</th>
                  <th>Subtotal</th>
                  <th></th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>

            <div class="mb-3 d-flex justify-content-between align-items-center">
              <span class="fw-bold">Total:</span>
              <span id="total" class="fw-bold text-primary">Rp 0</span>
            </div>


            <div class="mb-3">
              <label>Metode Bayar</label>
              <select name="metode" id="metodeBayar" class="form-control" required>
                <option value="">-- Pilih --</option>
                <option value="Transfer">Transfer</option>
                <option value="QRIS">QRIS</option>
              </select>
            </div>

            <div class="mb-3" id="buktiDiv" style="display: none;">
              <label>Upload Bukti Pembayaran</label>
              <input type="file" name="bukti" id="inputBukti" class="form-control" accept="image/*">
            </div>

            <div class="mb-3">
              <label>Alamat Pengiriman</label>
              <textarea name="alamat" class="form-control" required><?= htmlspecialchars($pelanggan['alamat'] ?? '') ?></textarea>
            </div>

            <input type="hidden" name="keranjang" id="inputKeranjang">
            <input type="hidden" name="total_harga" id="inputTotal">

            <div class="d-flex justify-content-between">
              <a href="<?= BASE_URL ?>views/pelanggan/index.php" class="btn btn-secondary">Kembali</a>
              <button type="submit" class="btn btn-success">Kirim Pesanan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include '../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let dataProduk = <?php
$daftar = [];
while ($p = mysqli_fetch_assoc($produk)) {
  $daftar[] = [
    'id' => (int)$p['id'],
    'nama_produk' => $p['nama_produk'],
    'harga' => (int)$p['harga'],
    'stok' => (int)$p['stok'],
    'gambar' => $p['gambar'] ?? 'default.png'
  ];
}
echo json_encode($daftar);
?>;

let keranjang = [];
let total_harga = 0;

function renderProduk() {
  const el = document.getElementById('produk-list');
  el.innerHTML = '';
  dataProduk.forEach(p => {
    el.innerHTML += `
      <div class="col-md-4 mb-3">
        <div class="card produk-card" onclick='tambahKeKeranjang(${JSON.stringify(p)})' style="cursor:pointer;">
          <img src="<?= BASE_URL ?>public/img/produk/${p.gambar}" class="card-img-top">
          <div class="card-body text-center">
            <h6 class="mb-1">${p.nama_produk}</h6>
            <p class="text-muted">Rp ${p.harga.toLocaleString()}</p>
          </div>
        </div>
      </div>`;
  });
}

function tambahKeKeranjang(p) {
  const idx = keranjang.findIndex(k => k.id === p.id);
  if (p.stok === 0) {
    Swal.fire("Stok Habis", "Produk tidak tersedia.", "warning");
    return;
  }
  if (idx !== -1) keranjang[idx].jumlah++;
  else keranjang.push({ ...p, jumlah: 1 });
  renderKeranjang();
}

function renderKeranjang() {
  const tbody = document.querySelector("#tabel-keranjang tbody");
  let total = 0;
  tbody.innerHTML = '';
  keranjang.forEach(p => {
    const subtotal = p.harga * p.jumlah;
    total += subtotal;
    tbody.innerHTML += `
      <tr data-id="${p.id}">
        <td>${p.nama_produk}</td>
        <td><input type="number" class="form-control form-control-sm input-qty text-center" value="${p.jumlah}" min="0" max="${p.stok}"></td>
        <td>Rp ${subtotal.toLocaleString()}</td>
        <td><button type="button" class="btn btn-sm btn-danger btn-hapus">x</button></td>
      </tr>`;
  });
  total_harga = total;
  document.getElementById('total').innerText = "Rp " + total.toLocaleString();
  document.getElementById('inputKeranjang').value = JSON.stringify(keranjang);
  document.getElementById('inputTotal').value = total;
}

document.addEventListener('DOMContentLoaded', () => {
  renderProduk();

  document.querySelector('#tabel-keranjang tbody').addEventListener('change', function (e) {
    if (e.target.classList.contains('input-qty')) {
      const tr = e.target.closest('tr');
      const id = parseInt(tr.getAttribute('data-id'));
      const qty = parseInt(e.target.value);
      const index = keranjang.findIndex(p => p.id === id);
      if (qty <= 0) {
        keranjang = keranjang.filter(p => p.id !== id);
      } else if (index !== -1) {
        keranjang[index].jumlah = qty;
      }
      renderKeranjang();
    }
  });

  document.querySelector('#tabel-keranjang tbody').addEventListener('click', function (e) {
    if (e.target.classList.contains('btn-hapus')) {
      const id = parseInt(e.target.closest('tr').getAttribute('data-id'));
      Swal.fire({
        title: "Hapus Produk?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Hapus"
      }).then(result => {
        if (result.isConfirmed) {
          keranjang = keranjang.filter(p => p.id !== id);
          renderKeranjang();
        }
      });
    }
  });

  document.getElementById('metodeBayar').addEventListener('change', function () {
    const div = document.getElementById('buktiDiv');
    const fileInput = document.getElementById('inputBukti');
    const show = this.value === 'Transfer' || this.value === 'QRIS';
    div.style.display = show ? 'block' : 'none';
    if (!show) fileInput.value = '';
  });

  document.getElementById("formCheckout").addEventListener("submit", function (e) {
    e.preventDefault();
    if (keranjang.length === 0) {
      Swal.fire("Oops", "Keranjang masih kosong.", "error");
      return;
    }

    const metode = document.getElementById("metodeBayar").value;
    const bukti = document.getElementById("inputBukti");

    if ((metode === "Transfer" || metode === "QRIS") && bukti.files.length === 0) {
      return Swal.fire("Gagal", "Mohon unggah bukti pembayaran!", "error");
    }

    const formData = new FormData(this);
    formData.set("keranjang", JSON.stringify(keranjang));
    formData.set("total_harga", total_harga);

    fetch("proses_checkout.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === "success") {
        Swal.fire("Berhasil", data.message, "success").then(() => {
          window.location.href = "../index.php";
        });
      } else {
        Swal.fire("Gagal", data.message, "error");
      }
    })
    .catch(() => Swal.fire("Error", "Terjadi kesalahan server.", "error"));
  });
});
</script>
