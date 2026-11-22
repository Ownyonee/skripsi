-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 29, 2026 at 06:04 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spk_makanan`
--

-- --------------------------------------------------------

--
-- Table structure for table `daftar_menu`
--

CREATE TABLE `daftar_menu` (
  `id` int(11) NOT NULL,
  `nama_menu` varchar(100) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `kategori` varchar(50) DEFAULT 'Umum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `daftar_menu`
--

INSERT INTO `daftar_menu` (`id`, `nama_menu`, `gambar`, `harga`, `deskripsi`, `kategori`) VALUES
(1, 'Gyoza Saus Mentai', 'images/daftar_menu/menu_6a0481f7e67673.26913453.jpg', 10000, 'Gyoza crispy dengan isian ayam dan saus mentai creamy yang gurih.', 'Umum'),
(2, 'Sushi Saus Mentai', 'images/daftar_menu/menu_6a04820407f752.31120000.jpg', 13000, 'Sushi lembut dengan topping saus mentai khas yang creamy dan lezat.', 'Umum'),
(3, 'Gunkan Saus BBQ', 'images/daftar_menu/menu_6a0482403e3740.60732764.jpg', 14000, 'Gunkan dengan topping saus BBQ manis gurih dan tekstur lembut.', 'Umum'),
(4, 'Dimsum Saus Mentai', 'images/daftar_menu/menu_6a0484561020d5.43491059.jpg', 21000, 'Dimsum ayam isi 6 pcs dengan pedas mentai gurih dan aroma khas.', 'Umum'),
(5, 'Rice Mentai Katsu', 'images/daftar_menu/menu_6a04825dbab6d6.79503786.jpg', 15000, 'Nasi hangat dengan chicken katsu crispy dan saus mentai premium.', 'Umum'),
(6, 'Gyoza Saus Mentai', NULL, 14000, 'Sajian lezat Gyoza Saus Mentai yang menggugah selera.', 'Gyoza'),
(7, 'Gyoza Saus Saeweed', NULL, 14000, 'Sajian lezat Gyoza Saus Saeweed yang menggugah selera.', 'Gyoza'),
(8, 'Gyoza Saus Chili Oil', NULL, 14000, 'Sajian lezat Gyoza Saus Chili Oil yang menggugah selera.', 'Gyoza'),
(9, 'Sushi Saus Mentai', NULL, 13000, 'Sajian lezat Sushi Saus Mentai yang menggugah selera.', 'Sushi'),
(10, 'Sushi Saus Saeweed', NULL, 13000, 'Sajian lezat Sushi Saus Saeweed yang menggugah selera.', 'Sushi'),
(11, 'Sushi Saus BBQ', NULL, 13000, 'Sajian lezat Sushi Saus BBQ yang menggugah selera.', 'Sushi'),
(12, 'Sushi Saus Tomat', NULL, 13000, 'Sajian lezat Sushi Saus Tomat yang menggugah selera.', 'Sushi'),
(13, 'Sushi Saus Spicy', NULL, 13000, 'Sajian lezat Sushi Saus Spicy yang menggugah selera.', 'Sushi'),
(14, 'Sushi Saus Keju', NULL, 13000, 'Sajian lezat Sushi Saus Keju yang menggugah selera.', 'Sushi'),
(15, 'Sushi Chiken Nugget', NULL, 13000, 'Sajian lezat Sushi Chiken Nugget yang menggugah selera.', 'Sushi'),
(16, 'Sushi Wortel', NULL, 13000, 'Sajian lezat Sushi Wortel yang menggugah selera.', 'Sushi'),
(17, 'Sushi Timun', NULL, 13000, 'Sajian lezat Sushi Timun yang menggugah selera.', 'Sushi'),
(18, 'Gunkan Saus Mentai', NULL, 14000, 'Sajian lezat Gunkan Saus Mentai yang menggugah selera.', 'Gunkan'),
(19, 'Gunkan Saus Saeweed', NULL, 14000, 'Sajian lezat Gunkan Saus Saeweed yang menggugah selera.', 'Gunkan'),
(20, 'Gunkan Saus BBQ', NULL, 14000, 'Sajian lezat Gunkan Saus BBQ yang menggugah selera.', 'Gunkan'),
(21, 'Gunkan Saus Tomat', NULL, 14000, 'Sajian lezat Gunkan Saus Tomat yang menggugah selera.', 'Gunkan'),
(22, 'Gunkan Saus Spicy', NULL, 14000, 'Sajian lezat Gunkan Saus Spicy yang menggugah selera.', 'Gunkan'),
(23, 'Gunkan Saus Keju', NULL, 14000, 'Sajian lezat Gunkan Saus Keju yang menggugah selera.', 'Gunkan'),
(24, 'Gunkan Chiken Nuget', NULL, 14000, 'Sajian lezat Gunkan Chiken Nuget yang menggugah selera.', 'Gunkan'),
(25, 'Gunkan Sosis', NULL, 14000, 'Sajian lezat Gunkan Sosis yang menggugah selera.', 'Gunkan'),
(26, 'Gunkan Crabstik', NULL, 14000, 'Sajian lezat Gunkan Crabstik yang menggugah selera.', 'Gunkan'),
(27, 'Dimsum Saus Mentai isi 4 pcs', NULL, 14000, 'Sajian lezat Dimsum Saus Mentai isi 4 pcs yang menggugah selera.', 'Dimsum'),
(28, 'Dimsum Saus Saeweed isi 4 pcs', NULL, 14000, 'Sajian lezat Dimsum Saus Saeweed isi 4 pcs yang menggugah selera.', 'Dimsum'),
(29, 'Dimsum Saus Chili oil isi 4 pcs', NULL, 14000, 'Sajian lezat Dimsum Saus Chili oil isi 4 pcs yang menggugah selera.', 'Dimsum'),
(30, 'Dimsum Saus Mentai isi 6 pcs', NULL, 21000, 'Sajian lezat Dimsum Saus Mentai isi 6 pcs yang menggugah selera.', 'Dimsum'),
(31, 'Dimsum Saus Saeweed isi 6 pcs', NULL, 21000, 'Sajian lezat Dimsum Saus Saeweed isi 6 pcs yang menggugah selera.', 'Dimsum'),
(32, 'Dimsum Saus Chili Oil isi 6 pcs', NULL, 21000, 'Sajian lezat Dimsum Saus Chili Oil isi 6 pcs yang menggugah selera.', 'Dimsum'),
(33, 'Dimsum Saus Mentai isi 8 pcs', NULL, 28000, 'Sajian lezat Dimsum Saus Mentai isi 8 pcs yang menggugah selera.', 'Dimsum'),
(34, 'Dimsum Saus Saeweed isi 8 pcs', NULL, 28000, 'Sajian lezat Dimsum Saus Saeweed isi 8 pcs yang menggugah selera.', 'Dimsum'),
(35, 'Dimsum Saus Chili Oil isi 8 pcs', NULL, 28000, 'Sajian lezat Dimsum Saus Chili Oil isi 8 pcs yang menggugah selera.', 'Dimsum'),
(36, 'Rice Mentai Chiken Nuget', '', 21000, 'Sajian lezat Rice Mentai Chiken Nuget yang menggugah selera.', 'Rice Mentai'),
(37, 'Rice Mentai Sosis', NULL, 13000, 'Sajian lezat Rice Mentai Sosis yang menggugah selera.', 'Rice Mentai'),
(38, 'Rice Mentai Crabstik', NULL, 14000, 'Sajian lezat Rice Mentai Crabstik yang menggugah selera.', 'Rice Mentai'),
(39, 'Rice Mentai Katsu', NULL, 15000, 'Sajian lezat Rice Mentai Katsu yang menggugah selera.', 'Rice Mentai'),
(40, 'Rice Saeweed Chiken Nuget', '', 25000, 'Sajian lezat Rice Saeweed Chiken Nuget yang menggugah selera.', 'Rice Mentai'),
(41, 'Rice Saeweed Sosis', NULL, 13000, 'Sajian lezat Rice Saeweed Sosis yang menggugah selera.', 'Rice Mentai'),
(42, 'Rice Saeweed Crabstik', NULL, 14000, 'Sajian lezat Rice Saeweed Crabstik yang menggugah selera.', 'Rice Mentai'),
(43, 'Rice Saeweed Katsu', NULL, 15000, 'Sajian lezat Rice Saeweed Katsu yang menggugah selera.', 'Rice Mentai'),
(44, 'Rice BBQ Chiken Nuget', NULL, 12000, 'Sajian lezat Rice BBQ Chiken Nuget yang menggugah selera.', 'Rice Mentai'),
(45, 'Rice BBQ Sosis', NULL, 13000, 'Sajian lezat Rice BBQ Sosis yang menggugah selera.', 'Rice Mentai'),
(46, 'Rice BBQ Crabstik', NULL, 14000, 'Sajian lezat Rice BBQ Crabstik yang menggugah selera.', 'Rice Mentai'),
(47, 'Rice BBQ Katsu', NULL, 15000, 'Sajian lezat Rice BBQ Katsu yang menggugah selera.', 'Rice Mentai'),
(48, 'Rice Keju Chiken Nuget', NULL, 12000, 'Sajian lezat Rice Keju Chiken Nuget yang menggugah selera.', 'Rice Mentai'),
(49, 'Rice Keju Sosis', NULL, 13000, 'Sajian lezat Rice Keju Sosis yang menggugah selera.', 'Rice Mentai'),
(50, 'Rice Keju Crabstik', NULL, 14000, 'Sajian lezat Rice Keju Crabstik yang menggugah selera.', 'Rice Mentai'),
(51, 'Rice Keju Katsu', NULL, 15000, 'Sajian lezat Rice Keju Katsu yang menggugah selera.', 'Rice Mentai');

-- --------------------------------------------------------

--
-- Table structure for table `kriteria`
--

CREATE TABLE `kriteria` (
  `id` int(11) NOT NULL,
  `kode_kriteria` varchar(5) DEFAULT NULL,
  `nama_kriteria` varchar(50) DEFAULT NULL,
  `bobot` float DEFAULT NULL,
  `tipe` enum('benefit','cost') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kriteria`
--

INSERT INTO `kriteria` (`id`, `kode_kriteria`, `nama_kriteria`, `bobot`, `tipe`) VALUES
(1, 'C1', 'Harga', 0.1, 'cost'),
(2, 'C2', 'Rasa', 0.25, 'benefit'),
(3, 'C3', 'Porsi', 0.15, 'benefit'),
(4, 'C4', 'Popularitas', 0.1, 'benefit'),
(5, 'C5', 'Tekstur', 0.1, 'benefit'),
(6, 'C6', 'Variasi Menu', 0.08, 'benefit'),
(7, 'C7', 'Kepraktisan Menu', 0.07, 'benefit'),
(8, 'C8', 'Value for Money', 0.15, 'benefit');

-- --------------------------------------------------------

--
-- Table structure for table `penilaian`
--

CREATE TABLE `penilaian` (
  `id` int(11) NOT NULL,
  `id_makanan` int(11) DEFAULT NULL,
  `id_kriteria` int(11) DEFAULT NULL,
  `nilai` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_perhitungan`
--

CREATE TABLE `riwayat_perhitungan` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `kode_perhitungan` varchar(30) NOT NULL,
  `nama_menu` varchar(100) NOT NULL,
  `nilai_preferensi` float NOT NULL,
  `ranking` int(11) NOT NULL,
  `tanggal` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `ulasan`
--

CREATE TABLE `ulasan` (
  `id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL,
  `rating_rasa` int(11) NOT NULL DEFAULT 0,
  `rating_harga` int(11) NOT NULL DEFAULT 0,
  `rating_porsi` int(11) NOT NULL DEFAULT 0,
  `rating_popularitas` int(11) NOT NULL DEFAULT 0,
  `rating_tekstur` int(11) NOT NULL DEFAULT 0,
  `rating_variasi` int(11) NOT NULL DEFAULT 0,
  `rating_kepraktisan` int(11) NOT NULL DEFAULT 0,
  `rating_value` int(11) NOT NULL DEFAULT 0,
  `komentar` text NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ulasan`
--

INSERT INTO `ulasan` (`id`, `menu_id`, `username`, `rating`, `rating_rasa`, `rating_harga`, `rating_porsi`, `rating_popularitas`, `rating_tekstur`, `rating_variasi`, `rating_kepraktisan`, `rating_value`, `komentar`, `tanggal`) VALUES
(1, 4, 'ditto', 5, 0, 0, 0, 0, 0, 0, 0, 0, 'enakk banget', '2026-05-29 16:31:38'),
(2, 4, 'hengky', 5, 5, 5, 4, 5, 4, 4, 5, 5, 'enak banget', '2026-05-30 08:43:42'),
(3, 4, 'asep', 4, 5, 4, 4, 4, 3, 4, 5, 4, 'HARUS COBAIN LAGII BUAT', '2026-06-27 16:48:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500', 'admin'),
(2, 'hengky', 'e10adc3949ba59abbe56e057f20f883e', 'user'),
(3, 'ditto', 'e10adc3949ba59abbe56e057f20f883e', 'user'),
(4, 'asep', '202cb962ac59075b964b07152d234b70', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `daftar_menu`
--
ALTER TABLE `daftar_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kriteria`
--
ALTER TABLE `kriteria`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `penilaian`
--
ALTER TABLE `penilaian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_makanan` (`id_makanan`),
  ADD KEY `id_kriteria` (`id_kriteria`);

--
-- Indexes for table `riwayat_perhitungan`
--
ALTER TABLE `riwayat_perhitungan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `daftar_menu`
--
ALTER TABLE `daftar_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `kriteria`
--
ALTER TABLE `kriteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `penilaian`
--
ALTER TABLE `penilaian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `riwayat_perhitungan`
--
ALTER TABLE `riwayat_perhitungan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ulasan`
--
ALTER TABLE `ulasan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `penilaian`
--
ALTER TABLE `penilaian`
  ADD CONSTRAINT `penilaian_ibfk_1` FOREIGN KEY (`id_makanan`) REFERENCES `makanan` (`id`),
  ADD CONSTRAINT `penilaian_ibfk_2` FOREIGN KEY (`id_kriteria`) REFERENCES `kriteria` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
