<?php
session_start();
require_once '../../../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'kasir') {
    header("Location: /restoran_ramen/views/auth/login.php");
    exit;
}

// Ambil kategori
$kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

include_once '../../layouts/header.php';
include_once '../../layouts/sidebar.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Tambah Produk</h1>

    <form action="proses_tambah.php" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="form-group">
            <label>Nama Produk</label>
            <input type="text" name="nama_produk" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Kategori</label>
            <select name="kategori_id" class="form-control" required>
                <option value="">-- Pilih Kategori --</option>
                <?php while ($row = mysqli_fetch_assoc($kategori)) : ?>
                    <option value="<?= $row['kategori_id'] ?>"><?= htmlspecialchars($row['nama_kategori']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Harga</label>
            <input type="text" name="harga" id="harga" class="form-control" required oninput="formatRupiah(this)">
        </div>

        <div class="form-group">
            <label>Stok</label>
            <input type="number" name="stok" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3" required></textarea>
        </div>

        <div class="form-group">
            <label>Gambar Produk</label>
            <input type="file" name="gambar" class="form-control-file" accept="image/*">
        </div>

        <button type="submit" class="btn btn-success" id="submitBtn">
            <i class="fas fa-save"></i> Simpan
        </button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<?php include_once '../../layouts/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function formatRupiah(input) {
    let value = input.value.replace(/[^\d]/g, '');
    input.value = value ? 'Rp ' + new Intl.NumberFormat('id-ID').format(value) : '';
}

document.getElementById('submitBtn')?.addEventListener('click', function () {
    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
});
</script>
