-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 15 Bulan Mei 2025 pada 03.39
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restoran_ramen`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `kategori_id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`kategori_id`, `nama_kategori`) VALUES
(1, 'Makanan'),
(2, 'Minuman'),
(3, 'Ramen'),
(4, 'Savory Snack'),
(5, 'Beverage');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `foto_profil` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pelanggan`
--

INSERT INTO `pelanggan` (`id`, `nama_lengkap`, `email`, `username`, `password`, `no_hp`, `alamat`, `foto_profil`, `created_at`) VALUES
(5, 'UyaSky', 'lily@rifkiidr.id', 'Genjor', '$2y$10$V20DAJiFlJ.Hv1DydeY4IOe4GF77cmlubc7bSE8.NGbBLPPBhwdy.', '085792438608', 'Jalan Dayung 43', NULL, '2025-05-14 23:03:54'),
(6, 'Karin', 'karin@gmail.com', 'adelkarin', '$2y$10$yoZ5hgZorZcXhH8P7dB4TeJuofWxG3jZpHcJLEgBvOB36njKsLDNq', '085792438608', 'Gkost no 110', NULL, '2025-05-15 07:01:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaturan_toko`
--

CREATE TABLE `pengaturan_toko` (
  `nama` varchar(100) NOT NULL,
  `nilai` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` int(11) NOT NULL,
  `stok` int(11) DEFAULT 0,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `kategori_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id`, `nama_produk`, `deskripsi`, `harga`, `stok`, `gambar`, `created_at`, `kategori_id`) VALUES
(4, 'Odeng', 'Odeng Lezat dan Gurih', 18000, 10, 'produk_6825441744137.JPG', '2025-05-14 10:09:17', 4),
(9, 'Minuman', 'Minuma Segar', 8000, 9, 'produk_682544015e25f.JPG', '2025-05-14 18:19:25', 5),
(13, 'Sushi', 'Sushi dengan Kelezatan', 35000, 10, 'sushi.JPG', '2025-05-15 00:48:50', 4),
(14, 'Udang Tempura', 'Udang Tempura Yang Gurih', 15000, 10, 'udang_tempura.JPG', '2025-05-15 00:49:34', 4),
(15, 'Dimsum Kukus', 'Dimsum Yummy ', 20000, 10, 'dimsum_kukus.JPG', '2025-05-15 00:50:26', 4),
(16, 'Dimsum Goreng', 'Dimsum Goreng Lezat', 20000, 10, 'dimsum_goreng.JPG', '2025-05-15 00:51:04', 4),
(17, 'Dakbal', 'Dakbal Lezat', 25000, 10, 'dakbal.JPG', '2025-05-15 00:52:00', 4),
(18, 'Teokbokki', 'Teokbokki Lezat', 30000, 10, 'teokbokki.JPG', '2025-05-15 00:52:45', 4),
(19, 'Takoyaki', 'Takoyaki Lezat', 20000, 10, 'takoyaki.JPG', '2025-05-15 00:53:17', 4),
(20, 'Shio Ramen', 'Chicken broth with spices and soy sauce', 28000, 10, 'shio_ramen.JPG', '2025-05-15 00:55:01', 3),
(21, 'Shoyu Ramen', 'Chicken broth with spices and soy sauce', 30000, 10, 'shoyu_ramen.jpg', '2025-05-15 00:55:46', 3),
(22, 'Miso Ramen', 'Miso paste lends a savory flavor to the ramen broth', 25000, 10, 'miso_ramen.jpg', '2025-05-15 00:56:42', 3),
(23, 'Tonkotsu Ramen', 'White ramen with a thick texture', 32000, 10, 'tonkotsu_ramen.jpg', '2025-05-15 00:57:59', 3),
(24, 'Ramen Kobe', 'Beef stuffed with pickled radish and chives', 28000, 10, 'ramen_kobe.jpg', '2025-05-15 00:58:28', 3),
(25, 'Hakodate Ramen', 'Cheese powder as a flavoring', 27000, 10, 'hakodate_ramen.jpg', '2025-05-15 00:59:06', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `kasir_id` int(11) DEFAULT NULL,
  `pelanggan_id` int(11) DEFAULT NULL,
  `waktu` datetime DEFAULT current_timestamp(),
  `metode_pembayaran` enum('Tunai','Transfer','QRIS') NOT NULL,
  `total_harga` int(11) NOT NULL,
  `jumlah_dibayar` int(11) NOT NULL,
  `kembalian` int(11) NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `alamat_pengiriman` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id`, `kasir_id`, `pelanggan_id`, `waktu`, `metode_pembayaran`, `total_harga`, `jumlah_dibayar`, `kembalian`, `status`, `bukti_pembayaran`, `alamat_pengiriman`) VALUES
(9, 1, NULL, '2025-05-15 07:11:48', 'Transfer', 23000, 25000, 2000, 'pending', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_detail`
--

CREATE TABLE `transaksi_detail` (
  `id` int(11) NOT NULL,
  `transaksi_id` int(11) DEFAULT NULL,
  `produk_id` int(11) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi_detail`
--

INSERT INTO `transaksi_detail` (`id`, `transaksi_id`, `produk_id`, `harga`, `jumlah`) VALUES
(12, 9, 4, 15000, 1),
(13, 9, 9, 8000, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('kasir') DEFAULT 'kasir',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `username`, `password`, `role`, `created_at`, `foto`) VALUES
(1, 'Siwarto Kasir', 'kasir1', '$2y$10$PXdHNDPXUYFjN6VXSGQfCOXCxH9Ql/789gWYgOATMsu99cQqz7ApW', 'kasir', '2025-05-14 09:07:42', 'kasir_1747268836.png');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`kategori_id`);

--
-- Indeks untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `pengaturan_toko`
--
ALTER TABLE `pengaturan_toko`
  ADD PRIMARY KEY (`nama`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_kategori` (`kategori_id`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pelanggan_transaksi` (`pelanggan_id`);

--
-- Indeks untuk tabel `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksi_id` (`transaksi_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `kategori_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `fk_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`kategori_id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `fk_pelanggan_transaksi` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`);

--
-- Ketidakleluasaan untuk tabel `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD CONSTRAINT `transaksi_detail_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_detail_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
