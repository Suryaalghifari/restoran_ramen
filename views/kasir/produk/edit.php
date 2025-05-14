<?php
session_start();
require_once '../../../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'kasir') {
    header("Location: /restoran_ramen/views/auth/login.php");
    exit;
}

$id = $_GET['id'] ?? 0;
$result = mysqli_query($conn, "SELECT * FROM produk WHERE id = $id");
$produk = mysqli_fetch_assoc($result);

if (!$produk) {
    $_SESSION['error'] = "Produk tidak ditemukan.";
    header("Location: index.php");
    exit;
}

$kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

include_once '../../layouts/header.php';
include_once '../../layouts/sidebar.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Produk</h1>

    <form action="proses_edit.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $produk['id'] ?>">

        <div class="form-group">
            <label>Nama Produk</label>
            <input type="text" name="nama_produk" class="form-control" value="<?= htmlspecialchars($produk['nama_produk']) ?>" required>
        </div>

        <div class="form-group">
            <label>Kategori</label>
            <select name="kategori_id" class="form-control" required>
                <?php while ($row = mysqli_fetch_assoc($kategori)) : ?>
                    <option value="<?= $row['kategori_id'] ?>" <?= $row['kategori_id'] == $produk['kategori_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['nama_kategori']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Harga</label>
            <input type="number" name="harga" class="form-control" value="<?= $produk['harga'] ?>" required>
        </div>

        <div class="form-group">
            <label>Stok</label>
            <input type="number" name="stok" class="form-control" value="<?= $produk['stok'] ?>" required>
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($produk['deskripsi']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Gambar Saat Ini</label><br>
            <?php if (!empty($produk['gambar'])) : ?>
                <img src="/restoran_ramen/public/img/produk/<?= $produk['gambar'] ?>" width="100"><br><br>
            <?php endif; ?>
            <input type="file" name="gambar" class="form-control-file">
            <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti gambar.</small>
        </div>

        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<?php include_once '../../layouts/footer.php'; ?>
