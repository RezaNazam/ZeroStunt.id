-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 19, 2026 at 03:22 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zerostunt_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `anak`
--

CREATE TABLE `anak` (
  `id_anak` int NOT NULL,
  `id_ibu` int NOT NULL,
  `NIK_anak` char(16) DEFAULT NULL,
  `nama_anak` varchar(100) NOT NULL,
  `tgl_lahir` date NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `st_gizi_skrg` varchar(50) DEFAULT NULL,
  `skala_prioritas` enum('1','2','3') DEFAULT NULL,
  `tgl_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `anak`
--

INSERT INTO `anak` (`id_anak`, `id_ibu`, `NIK_anak`, `nama_anak`, `tgl_lahir`, `jenis_kelamin`, `st_gizi_skrg`, `skala_prioritas`, `tgl_created`, `deleted_at`) VALUES
(1, 3, '6894867', 'Asep maulana', '2021-01-13', 'L', NULL, NULL, '2026-06-15 01:45:01', NULL),
(2, 3, '68967485', 'anaga', '2026-06-15', 'P', NULL, NULL, '2026-06-15 01:49:11', '2026-06-19 03:15:16'),
(3, 6, '6894867475758454', 'anaga', '2026-06-16', 'L', NULL, NULL, '2026-06-16 14:02:05', NULL),
(4, 3, '6894867475758454', 'anaga', '2026-07-11', 'L', NULL, NULL, '2026-06-19 03:14:07', '2026-06-19 03:15:11'),
(5, 3, '6894867475758454', 'anaga', '2026-06-15', 'L', NULL, NULL, '2026-06-19 03:14:30', NULL),
(6, 3, '6894867475758454', 'Asep maulana', '2026-06-23', 'L', NULL, NULL, '2026-06-19 03:15:03', '2026-06-19 03:15:15');

-- --------------------------------------------------------

--
-- Table structure for table `gudang`
--

CREATE TABLE `gudang` (
  `id_gudang` int NOT NULL,
  `nama_gudang` varchar(100) NOT NULL,
  `lokasi_gudang` text,
  `jenis_gudang` enum('Pusat','Posyandu') NOT NULL,
  `alamat_lengkap` text,
  `nama_pengelola` varchar(100) DEFAULT NULL,
  `tgl_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `gudang`
--

INSERT INTO `gudang` (`id_gudang`, `nama_gudang`, `lokasi_gudang`, `jenis_gudang`, `alamat_lengkap`, `nama_pengelola`, `tgl_created`, `is_deleted`) VALUES
(1, 'gudang suka maju', 'jalan suka maju', 'Pusat', 'jalan suka maju aja', 'kader', '2026-06-07 17:03:38', 0),
(2, 'gudang hebat', 'bekasi', 'Posyandu', 'jalan jalan hebat', 'reza', '2026-06-19 03:09:08', 0);

-- --------------------------------------------------------

--
-- Table structure for table `ibu`
--

CREATE TABLE `ibu` (
  `id_ibu` int NOT NULL,
  `NIK_ibu` char(16) NOT NULL,
  `nama_ibu` varchar(100) NOT NULL,
  `no_telp` varchar(15) DEFAULT NULL,
  `alamat` text,
  `id_gudang` int DEFAULT NULL,
  `is_pregnant` tinyint(1) NOT NULL DEFAULT '0',
  `tgl_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ibu`
--

INSERT INTO `ibu` (`id_ibu`, `NIK_ibu`, `nama_ibu`, `no_telp`, `alamat`, `id_gudang`, `is_pregnant`, `tgl_created`, `deleted_at`) VALUES
(3, '01test0128', 'testibuform', '0821038', 'testalamat', NULL, 1, '2026-06-07 12:36:56', NULL),
(5, '0920250021', 'ibu', '0138746563728', 'ibu', 1, 1, '2026-06-11 00:54:09', NULL),
(6, '1234567890123452', 'ibu', '89575345435', 'bekasi', 1, 0, '2026-06-16 14:01:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `komoditas_pangan`
--

CREATE TABLE `komoditas_pangan` (
  `id_komoditas` int NOT NULL,
  `nama_komoditas` varchar(100) NOT NULL,
  `kategori_gizi` varchar(50) DEFAULT NULL,
  `id_satuan` int NOT NULL,
  `deskripsi` text,
  `tgl_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `komoditas_pangan`
--

INSERT INTO `komoditas_pangan` (`id_komoditas`, `nama_komoditas`, `kategori_gizi`, `id_satuan`, `deskripsi`, `tgl_created`, `is_deleted`) VALUES
(2, 'ikan', 'Protein Hewani', 2, 'ikan segar', '2026-06-19 03:10:25', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pengadaan`
--

CREATE TABLE `pengadaan` (
  `id_pengadaan` int NOT NULL,
  `id_petani` int DEFAULT NULL,
  `id_komoditas` int NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `posyandu` varchar(100) NOT NULL,
  `status` enum('Tersedia','Sudah Diambil') DEFAULT 'Tersedia',
  `tanggal_pengadaan` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `petani_lokal`
--

CREATE TABLE `petani_lokal` (
  `id_petani` int NOT NULL,
  `nama_lahan` varchar(100) NOT NULL,
  `alamat_lahan` text,
  `no_rekening` varchar(30) DEFAULT NULL,
  `kapasitas_panen_bulan` decimal(10,2) DEFAULT NULL,
  `tgl_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `petani_lokal`
--

INSERT INTO `petani_lokal` (`id_petani`, `nama_lahan`, `alamat_lahan`, `no_rekening`, `kapasitas_panen_bulan`, `tgl_created`) VALUES
(4, 'lahan suka maju', 'jalan suka maju', '0129392', '100.00', '2026-06-07 13:41:59');

-- --------------------------------------------------------

--
-- Table structure for table `satuan`
--

CREATE TABLE `satuan` (
  `id_satuan` int NOT NULL,
  `nama_satuan` varchar(50) NOT NULL,
  `singkat` varchar(10) NOT NULL,
  `tgl_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `satuan`
--

INSERT INTO `satuan` (`id_satuan`, `nama_satuan`, `singkat`, `tgl_created`, `is_deleted`) VALUES
(2, 'Kilogram', 'Kg', '2026-06-19 03:10:10', 0);

-- --------------------------------------------------------

--
-- Table structure for table `standar_pertumbuhan`
--

CREATE TABLE `standar_pertumbuhan` (
  `id_standar` int UNSIGNED NOT NULL,
  `usia_bulan` tinyint UNSIGNED NOT NULL,
  `jenis_kelamin` enum('L','P') COLLATE utf8mb4_general_ci NOT NULL,
  `tipe_standar` enum('BB/U','TB/U') COLLATE utf8mb4_general_ci NOT NULL,
  `median` decimal(6,3) NOT NULL,
  `sd_plus_1` decimal(6,3) NOT NULL,
  `sd_minus_1` decimal(6,3) NOT NULL,
  `sd_minus_2` decimal(6,3) NOT NULL,
  `sd_minus_3` decimal(6,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Kader','Petani','Ibu') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `role`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin', '$2y$12$eoq1NXMct2oLGC1LbLydZ.HzVB0wZQq5U3UtjJ9wia/JL1YjJh/rm', 'Admin', 1, '2026-06-07 07:20:10', '2026-06-07 07:20:10', NULL),
(3, 'ibuTest', '$2y$12$JbFlTwwiXvoMmrBOXwY.ueFMJktMM4stGS8std32yxVUyrWLPz8v6', 'Ibu', 1, '2026-06-07 12:32:28', '2026-06-07 12:32:28', NULL),
(4, 'petaniTest', '$2y$12$xSBT.byrEMjONcIV4SGTfekgDJagzrHxyM/7DstNyoQkTaqTqoT5u', 'Petani', 1, '2026-06-07 12:33:41', '2026-06-07 12:33:41', NULL),
(5, 'ibu2', '$2y$12$bv1m0/6XjXnPPi3jJMSjjOkIpD8rp1383hfyusIiopLVDL3PJ0xb6', 'Ibu', 1, '2026-06-11 00:52:30', '2026-06-11 00:52:30', NULL),
(6, 'ibuku', '$2y$10$0bZmguPBcerJQT1x.i0BsedA8Eh4FsXxWrx1sAXbigDjAxZg0Eu7S', 'Ibu', 0, '2026-06-16 13:23:28', '2026-06-19 02:50:51', '2026-06-19 02:50:51'),
(7, 'ibu_', '$2y$10$SlTVfu0FTviwc.p6ERFdIurXGTvh6WRpTE1nmcQhnZAbM/qSLT2/S', 'Ibu', 1, '2026-06-19 02:11:41', '2026-06-19 02:11:58', NULL),
(8, 'kader', '$2y$10$CB8nEEWOViMizuREKJfuC.5YaxExZjaev/Xl.Ya0rNySufOKVtaoi', 'Kader', 1, '2026-06-19 03:07:16', '2026-06-19 03:07:16', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anak`
--
ALTER TABLE `anak`
  ADD PRIMARY KEY (`id_anak`),
  ADD KEY `fk_anak_ibu` (`id_ibu`),
  ADD UNIQUE KEY `unique_nik_anak` (`NIK_anak`);

--
-- Indexes for table `gudang`
--
ALTER TABLE `gudang`
  ADD PRIMARY KEY (`id_gudang`);

--
-- Indexes for table `ibu`
--
ALTER TABLE `ibu`
  ADD PRIMARY KEY (`id_ibu`),
  ADD UNIQUE KEY `NIK_ibu` (`NIK_ibu`),
  ADD KEY `fk_ibu_gudang` (`id_gudang`);

--
-- Indexes for table `komoditas_pangan`
--
ALTER TABLE `komoditas_pangan`
  ADD PRIMARY KEY (`id_komoditas`),
  ADD UNIQUE KEY `nama_komoditas` (`nama_komoditas`),
  ADD KEY `fk_komoditas_satuan` (`id_satuan`);

--
-- Indexes for table `pengadaan`
--
ALTER TABLE `pengadaan`
  ADD PRIMARY KEY (`id_pengadaan`),
  ADD KEY `fk_pengadaan_petani` (`id_petani`),
  ADD KEY `fk_pengadaan_komoditas` (`id_komoditas`);

--
-- Indexes for table `petani_lokal`
--
ALTER TABLE `petani_lokal`
  ADD PRIMARY KEY (`id_petani`);

--
-- Indexes for table `satuan`
--
ALTER TABLE `satuan`
  ADD PRIMARY KEY (`id_satuan`),
  ADD UNIQUE KEY `nama_satuan` (`nama_satuan`);

--
-- Indexes for table `standar_pertumbuhan`
--
ALTER TABLE `standar_pertumbuhan`
  ADD PRIMARY KEY (`id_standar`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anak`
--
ALTER TABLE `anak`
  MODIFY `id_anak` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `gudang`
--
ALTER TABLE `gudang`
  MODIFY `id_gudang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `komoditas_pangan`
--
ALTER TABLE `komoditas_pangan`
  MODIFY `id_komoditas` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pengadaan`
--
ALTER TABLE `pengadaan`
  MODIFY `id_pengadaan` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `satuan`
--
ALTER TABLE `satuan`
  MODIFY `id_satuan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

---
-- AUTO_INCREMENT for table `standar_pertumbuhan`
--
ALTER TABLE `standar_pertumbuhan`
  MODIFY `id_standar` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anak`
--
ALTER TABLE `anak`
  ADD CONSTRAINT `fk_anak_ibu` FOREIGN KEY (`id_ibu`) REFERENCES `ibu` (`id_ibu`) ON DELETE CASCADE;

--
-- Constraints for table `ibu`
--
ALTER TABLE `ibu`
  ADD CONSTRAINT `fk_ibu_gudang` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id_gudang`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ibu_user` FOREIGN KEY (`id_ibu`) REFERENCES `users` (`id_user`) ON DELETE RESTRICT;

--
-- Constraints for table `komoditas_pangan`
--
ALTER TABLE `komoditas_pangan`
  ADD CONSTRAINT `fk_komoditas_satuan` FOREIGN KEY (`id_satuan`) REFERENCES `satuan` (`id_satuan`) ON DELETE RESTRICT;

--
-- Constraints for table `pengadaan`
--
ALTER TABLE `pengadaan`
  ADD CONSTRAINT `fk_pengadaan_komoditas` FOREIGN KEY (`id_komoditas`) REFERENCES `komoditas_pangan` (`id_komoditas`),
  ADD CONSTRAINT `fk_pengadaan_petani` FOREIGN KEY (`id_petani`) REFERENCES `petani_lokal` (`id_petani`);

--
-- Constraints for table `petani_lokal`
--
ALTER TABLE `petani_lokal`
  ADD CONSTRAINT `fk_petani_user` FOREIGN KEY (`id_petani`) REFERENCES `users` (`id_user`) ON DELETE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
