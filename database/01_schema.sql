-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 07, 2026 at 07:07 AM
-- Server version: 8.0.30
-- PHP Version: 8.4.20

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
  `tgl_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `tgl_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `tgl_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `tgl_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `satuan`
--

CREATE TABLE `satuan` (
  `id_satuan` int NOT NULL,
  `nama_satuan` varchar(50) NOT NULL,
  `singkat` varchar(10) NOT NULL,
  `tgl_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `standar_pertumbuhan`
--

CREATE TABLE `standar_pertumbuhan` (
  `usia_bulan` tinyint UNSIGNED NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `tipe_standar` enum('BB/U','TB/U') NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `pengadaan`
--

CREATE TABLE `pengadaan` (
    id_pengadaan INT AUTO_INCREMENT PRIMARY KEY,
    id_petani INT NULL,
    id_komoditas INT NOT NULL,
    jumlah DECIMAL(10,2) NOT NULL,
    posyandu VARCHAR(100) NOT NULL,

    status ENUM(
        'Tersedia',
        'Sudah Diambil'
    ) DEFAULT 'Tersedia',

    tanggal_pengadaan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_pengadaan_petani
        FOREIGN KEY (id_petani)
        REFERENCES petani_lokal(id_petani),

    CONSTRAINT fk_pengadaan_komoditas
        FOREIGN KEY (id_komoditas)
        REFERENCES komoditas_pangan(id_komoditas)
);

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anak`
--
ALTER TABLE `anak`
  ADD PRIMARY KEY (`id_anak`),
  ADD KEY `fk_anak_ibu` (`id_ibu`);

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
  ADD PRIMARY KEY (`usia_bulan`,`jenis_kelamin`,`tipe_standar`);

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
  MODIFY `id_anak` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gudang`
--
ALTER TABLE `gudang`
  MODIFY `id_gudang` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `komoditas_pangan`
--
ALTER TABLE `komoditas_pangan`
  MODIFY `id_komoditas` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `satuan`
--
ALTER TABLE `satuan`
  MODIFY `id_satuan` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT;

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
-- Constraints for table `petani_lokal`
--
ALTER TABLE `petani_lokal`
  ADD CONSTRAINT `fk_petani_user` FOREIGN KEY (`id_petani`) REFERENCES `users` (`id_user`) ON DELETE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
