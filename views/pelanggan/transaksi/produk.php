<?php
require_once '../../../config/koneksi.php';

// Ambil produk dari database dan kelompokkan berdasarkan kategori
$produk = mysqli_query($conn, "
  SELECT p.*, k.nama_kategori 
  FROM produk p 
  JOIN kategori k ON p.kategori_id = k.kategori_id
  ORDER BY k.nama_kategori, p.nama_produk
");

$data_ramen = [];
$data_snack = [];
$data_minuman = [];

while ($row = mysqli_fetch_assoc($produk)) {
  $kategori = strtolower($row['nama_kategori']);
  if ($kategori == 'ramen') {
    $data_ramen[] = $row;
  } elseif ($kategori == 'snack') {
    $data_snack[] = $row;
  } else {
    $data_minuman[] = $row;
  }
}

function tampilkanProduk($data, $tipe = 'menu') {
  foreach ($data as $item) {
    $gambar = 'img/' . htmlspecialchars($item['gambar']);
    $nama = htmlspecialchars($item['nama_produk']);
    $deskripsi = htmlspecialchars($item['deskripsi']);
    $harga = 'Rp' . number_format($item['harga'], 0, ',', '.');

    echo $tipe == 'menu' ? "
      <div class='menu-card'>
        <img src='$gambar' alt='$nama'>
        <h2>$nama</h2>
        <p>$deskripsi</p>
        <span class='price'>$harga</span>
      </div>
    " : "
      <div class='beverage-card'>
        <img src='$gambar' alt='$nama'>
        <h3>$nama</h3>
        <p>$deskripsi</p>
        <span class='price'>$harga</span>
      </div>
    ";
  }
}
?>
