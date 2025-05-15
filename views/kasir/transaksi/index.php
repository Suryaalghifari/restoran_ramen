<?php
session_start();
require_once '../../../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'kasir') {
    header("Location: /restoran_ramen/views/auth/login.php");
    exit;
}

include_once '../../layouts/header.php';
include_once '../../layouts/sidebar.php';

$produk = mysqli_query($conn, "
    SELECT p.*, k.nama_kategori 
    FROM produk p 
    LEFT JOIN kategori k ON p.kategori_id = k.kategori_id 
    ORDER BY p.nama_produk ASC
");
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Transaksi Penjualan</h1>

    <div class="row">
        <!-- Produk -->
        <div class="col-md-9">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Gambar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($p = mysqli_fetch_assoc($produk)) :
                            $icon = 'fa-utensils';
                            if (strtolower($p['nama_kategori']) === 'minuman') $icon = 'fa-coffee';
                            elseif (strtolower($p['nama_kategori']) === 'makanan') $icon = 'fa-hamburger';
                        ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($p['nama_produk']) ?></strong><br>
                                    <small class="text-muted"><?= nl2br(htmlspecialchars($p['deskripsi'])) ?></small><br>
                                    <span class="badge badge-info mt-1">
                                        <i class="fas <?= $icon ?>"></i> <?= htmlspecialchars($p['nama_kategori'] ?? '-') ?>
                                    </span>
                                </td>
                                <td>Rp <?= number_format($p['harga']) ?></td>
                                <td><?= $p['stok'] ?></td>
                                <td>
                                    <?php if (!empty($p['gambar'])): ?>
                                        <img src="/restoran_ramen/public/img/produk/<?= $p['gambar'] ?>" width="50">
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick='tambahKeKeranjang(<?= json_encode($p) ?>)'>Tambah</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Keranjang -->
        <div class="col-md-3">
            <form id="formTransaksi" method="POST">
                <div class="card mb-3 shadow">
                    <div class="card-header">
                        <strong>Keranjang</strong>
                    </div>
                    <div class="card-body p-2">
                        <table class="table table-sm" id="keranjang">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Qty</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <input type="hidden" name="data_keranjang" id="data_keranjang">
                        <button type="button" class="btn btn-danger btn-sm btn-block" onclick="konfirmasiKosongkan()">Kosongkan</button>
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-body">
                        <h5>Total: <span id="total">Rp 0</span></h5>
                        <input type="hidden" id="total_hidden" name="total_harga">

                        <div class="form-group">
                            <label>Metode Pembayaran</label>
                            <select name="metode" class="form-control" required>
                                <option value="">-- Pilih Metode --</option>
                                <option value="Tunai">Tunai</option>
                                <option value="Transfer">Transfer</option>
                                <option value="QRIS">QRIS</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Jumlah Dibayar</label>
                            <input type="text" id="jumlah_dibayar" class="form-control text-right" autocomplete="off" required>
                        </div>

                        <div class="form-group">
                            <label>Kembalian</label>
                            <input type="text" id="kembalian" class="form-control text-right" readonly>
                        </div>

                        <button type="submit" class="btn btn-success btn-block">Simpan Transaksi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once '../../layouts/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let keranjang = [];

function parseNumber(str) {
    return parseInt(str.replace(/[^\d]/g, '')) || 0;
}

function tambahKeKeranjang(produk) {
    produk.produk_id = parseInt(produk.produk_id || produk.id);
    let index = keranjang.findIndex(p => p.produk_id === produk.produk_id);
    if (index !== -1) {
        if (keranjang[index].jumlah < produk.stok) {
            keranjang[index].jumlah += 1;
        } else {
            Swal.fire("Stok Habis", "Stok produk tidak mencukupi.", "warning");
        }
    } else {
        if (produk.stok > 0) {
            keranjang.push({ ...produk, jumlah: 1 });
        } else {
            Swal.fire("Stok Habis", "Stok produk tidak mencukupi.", "warning");
        }
    }
    renderKeranjang();
}

function ubahJumlahManual(id, jumlahBaru) {
    let index = keranjang.findIndex(p => p.produk_id === id);
    if (index !== -1) {
        jumlahBaru = parseInt(jumlahBaru);
        if (jumlahBaru > 0 && jumlahBaru <= keranjang[index].stok) {
            keranjang[index].jumlah = jumlahBaru;
        } else {
            Swal.fire("Stok Tidak Cukup", "Jumlah melebihi stok tersedia.", "warning");
        }
        renderKeranjang();
    }
}

function hapusDariKeranjang(id) {
    keranjang = keranjang.filter(p => p.produk_id !== id);
    renderKeranjang();
}

function kosongkanKeranjang() {
    keranjang = [];
    renderKeranjang();
}

function konfirmasiKosongkan() {
    Swal.fire({
        title: 'Kosongkan Keranjang?',
        text: "Semua item akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kosongkan!'
    }).then((result) => {
        if (result.isConfirmed) kosongkanKeranjang();
    });
}

function renderKeranjang() {
    let tbody = document.querySelector("#keranjang tbody");
    let total = 0;
    tbody.innerHTML = "";
    keranjang.forEach(p => {
        total += p.harga * p.jumlah;
        tbody.innerHTML += `
            <tr>
                <td>${p.nama_produk}</td>
                <td><input type="number" class="form-control form-control-sm" value="${p.jumlah}" min="1" max="${p.stok}" onchange="ubahJumlahManual(${p.produk_id}, this.value)"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="hapusDariKeranjang(${p.produk_id})">x</button></td>
            </tr>`;
    });
    document.getElementById("total").innerText = "Rp " + total.toLocaleString('id-ID');
    document.getElementById("total_hidden").value = total;
    document.getElementById("data_keranjang").value = JSON.stringify(keranjang);
}

// Format input jumlah dibayar & hitung kembalian
document.getElementById('jumlah_dibayar').addEventListener('input', function () {
    const bayar = parseNumber(this.value);
    const total = parseNumber(document.getElementById('total').innerText);
    const kembali = bayar - total;
    this.value = bayar.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });
    document.getElementById('kembalian').value = kembali >= 0 ? 'Rp ' + kembali.toLocaleString('id-ID') : '-';
});

document.getElementById("formTransaksi").addEventListener("submit", function(e) {
    e.preventDefault();
    const bayarRaw = document.getElementById("jumlah_dibayar").value;
    const bayar = parseNumber(bayarRaw);
    const total = parseNumber(document.getElementById("total").innerText);

    if (keranjang.length === 0) {
        return Swal.fire("Gagal", "Keranjang masih kosong!", "warning");
    }

    if (!this.metode.value || bayar <= 0) {
        return Swal.fire("Gagal", "Lengkapi data pembayaran.", "warning");
    }

    if (bayar < total) {
        return Swal.fire("Gagal", "Jumlah dibayar kurang dari total.", "error");
    }

    fetch("proses_tambah_pesanan.php", {
        method: "POST",
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            keranjang,
            metode: this.metode.value,
            jumlah_dibayar: bayar
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire("Berhasil", data.message, "success").then(() => {
                window.location.href = "../histori/index.php";
            });
        } else {
            Swal.fire("Gagal", data.message, "error");
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire("Error", "Terjadi kesalahan pada server.", "error");
    });
});
</script>
