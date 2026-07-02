-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 26, 2026 at 03:09 AM
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
(1, 3, '6894867', 'Asep maulana', '2021-01-13', 'L', NULL, NULL, '2026-06-15 01:45:01', NULL);

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
(2, 'gudang hebat', 'bekasi', 'Posyandu', 'jalan jalan hebat', 'reza', '2026-06-19 03:09:08', 0),
(3, 'gudang mantap', 'mantap', 'Pusat', 'dadasds', 'kader', '2026-06-26 01:58:25', 0);

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
(3, '01test0128', 'testibuform', '0821038', 'testalamat', 1, 1, '2026-06-07 12:36:56', NULL),
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
  `jenis_kelamin` enum('L','P') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tipe_standar` enum('BB/U','TB/U') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `median` decimal(6,3) NOT NULL,
  `sd_plus_1` decimal(6,3) NOT NULL,
  `sd_minus_1` decimal(6,3) NOT NULL,
  `sd_minus_2` decimal(6,3) NOT NULL,
  `sd_minus_3` decimal(6,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `standar_pertumbuhan`
--

INSERT INTO `standar_pertumbuhan` (`id_standar`, `usia_bulan`, `jenis_kelamin`, `tipe_standar`, `median`, `sd_plus_1`, `sd_minus_1`, `sd_minus_2`, `sd_minus_3`) VALUES
(1, 0, 'L', 'TB/U', '49.900', '51.800', '48.000', '46.100', '44.200'),
(2, 1, 'L', 'TB/U', '54.700', '56.700', '52.800', '50.800', '48.900'),
(3, 2, 'L', 'TB/U', '58.400', '60.400', '56.400', '54.400', '52.400'),
(4, 3, 'L', 'TB/U', '61.400', '63.500', '59.400', '57.300', '55.300'),
(5, 4, 'L', 'TB/U', '63.900', '66.000', '61.800', '59.700', '57.600'),
(6, 5, 'L', 'TB/U', '65.900', '68.000', '63.800', '61.700', '59.600'),
(7, 6, 'L', 'TB/U', '67.600', '69.800', '65.500', '63.300', '61.200'),
(8, 7, 'L', 'TB/U', '69.200', '71.300', '67.000', '64.800', '62.700'),
(9, 8, 'L', 'TB/U', '70.600', '72.800', '68.400', '66.200', '64.000'),
(10, 9, 'L', 'TB/U', '72.000', '74.200', '69.700', '67.500', '65.200'),
(11, 10, 'L', 'TB/U', '73.300', '75.600', '71.000', '68.700', '66.400'),
(12, 11, 'L', 'TB/U', '74.500', '76.900', '72.200', '69.900', '67.600'),
(13, 12, 'L', 'TB/U', '75.700', '78.100', '73.400', '71.000', '68.600'),
(14, 13, 'L', 'TB/U', '76.900', '79.300', '74.500', '72.100', '69.600'),
(15, 14, 'L', 'TB/U', '78.000', '80.500', '75.600', '73.100', '70.600'),
(16, 15, 'L', 'TB/U', '79.100', '81.700', '76.600', '74.100', '71.600'),
(17, 16, 'L', 'TB/U', '80.200', '82.800', '77.600', '75.000', '72.500'),
(18, 17, 'L', 'TB/U', '81.200', '83.900', '78.600', '76.000', '73.300'),
(19, 18, 'L', 'TB/U', '82.300', '85.000', '79.600', '76.900', '74.200'),
(20, 19, 'L', 'TB/U', '83.200', '86.000', '80.500', '77.700', '75.000'),
(21, 20, 'L', 'TB/U', '84.200', '87.000', '81.400', '78.600', '75.800'),
(22, 21, 'L', 'TB/U', '85.100', '88.000', '82.300', '79.400', '76.500'),
(23, 22, 'L', 'TB/U', '86.000', '89.000', '83.100', '80.200', '77.200'),
(24, 23, 'L', 'TB/U', '86.900', '89.900', '83.900', '81.000', '78.000'),
(25, 24, 'L', 'TB/U', '87.100', '90.200', '84.100', '81.000', '78.000'),
(26, 25, 'L', 'TB/U', '88.000', '91.100', '84.900', '81.700', '78.600'),
(27, 26, 'L', 'TB/U', '88.800', '92.000', '85.600', '82.500', '79.300'),
(28, 27, 'L', 'TB/U', '89.600', '92.900', '86.400', '83.100', '79.900'),
(29, 28, 'L', 'TB/U', '90.400', '93.700', '87.100', '83.800', '80.500'),
(30, 29, 'L', 'TB/U', '91.200', '94.500', '87.800', '84.500', '81.100'),
(31, 30, 'L', 'TB/U', '91.900', '95.300', '88.500', '85.100', '81.700'),
(32, 31, 'L', 'TB/U', '92.700', '96.100', '89.200', '85.700', '82.300'),
(33, 32, 'L', 'TB/U', '93.400', '96.900', '89.900', '86.400', '82.800'),
(34, 33, 'L', 'TB/U', '94.100', '97.600', '90.500', '86.900', '83.400'),
(35, 34, 'L', 'TB/U', '94.800', '98.400', '91.100', '87.500', '83.900'),
(36, 35, 'L', 'TB/U', '95.400', '99.100', '91.800', '88.100', '84.400'),
(37, 36, 'L', 'TB/U', '96.100', '99.800', '92.400', '88.700', '85.000'),
(38, 37, 'L', 'TB/U', '96.700', '100.500', '93.000', '89.200', '85.500'),
(39, 38, 'L', 'TB/U', '97.400', '101.200', '93.600', '89.800', '86.000'),
(40, 39, 'L', 'TB/U', '98.000', '101.800', '94.200', '90.300', '86.500'),
(41, 40, 'L', 'TB/U', '98.600', '102.500', '94.700', '90.900', '87.000'),
(42, 41, 'L', 'TB/U', '99.200', '103.200', '95.300', '91.400', '87.500'),
(43, 42, 'L', 'TB/U', '99.900', '103.800', '95.900', '91.900', '88.000'),
(44, 43, 'L', 'TB/U', '100.400', '104.500', '96.400', '92.400', '88.400'),
(45, 44, 'L', 'TB/U', '101.000', '105.100', '97.000', '93.000', '88.900'),
(46, 45, 'L', 'TB/U', '101.600', '105.700', '97.500', '93.500', '89.400'),
(47, 46, 'L', 'TB/U', '102.200', '106.300', '98.100', '94.000', '89.800'),
(48, 47, 'L', 'TB/U', '102.800', '106.900', '98.600', '94.400', '90.300'),
(49, 48, 'L', 'TB/U', '103.300', '107.500', '99.100', '94.900', '90.700'),
(50, 49, 'L', 'TB/U', '103.900', '108.100', '99.700', '95.400', '91.200'),
(51, 50, 'L', 'TB/U', '104.400', '108.700', '100.200', '95.900', '91.600'),
(52, 51, 'L', 'TB/U', '105.000', '109.300', '100.700', '96.400', '92.100'),
(53, 52, 'L', 'TB/U', '105.600', '109.900', '101.200', '96.900', '92.500'),
(54, 53, 'L', 'TB/U', '106.100', '110.500', '101.700', '107.400', '93.000'),
(55, 54, 'L', 'TB/U', '106.700', '111.100', '102.300', '97.800', '93.400'),
(56, 55, 'L', 'TB/U', '107.200', '111.700', '102.800', '98.300', '93.900'),
(57, 56, 'L', 'TB/U', '107.800', '112.300', '103.300', '98.800', '94.300'),
(58, 57, 'L', 'TB/U', '108.300', '112.800', '103.800', '99.300', '94.700'),
(59, 58, 'L', 'TB/U', '108.900', '113.400', '104.300', '99.700', '95.200'),
(60, 59, 'L', 'TB/U', '109.400', '114.000', '104.800', '100.200', '95.600'),
(61, 60, 'L', 'TB/U', '110.000', '114.600', '105.300', '100.700', '96.100'),
(62, 0, 'P', 'TB/U', '49.100', '51.000', '47.300', '45.400', '43.600'),
(63, 1, 'P', 'TB/U', '53.700', '55.600', '51.700', '49.800', '47.800'),
(64, 2, 'P', 'TB/U', '57.100', '59.100', '55.000', '53.000', '51.000'),
(65, 3, 'P', 'TB/U', '59.800', '61.900', '57.700', '55.600', '53.500'),
(66, 4, 'P', 'TB/U', '62.100', '64.300', '59.900', '57.800', '55.600'),
(67, 5, 'P', 'TB/U', '64.000', '66.200', '61.800', '59.600', '57.400'),
(68, 6, 'P', 'TB/U', '65.700', '68.000', '63.500', '61.200', '58.900'),
(69, 7, 'P', 'TB/U', '67.300', '69.600', '65.000', '62.700', '60.300'),
(70, 8, 'P', 'TB/U', '68.700', '71.100', '66.400', '64.000', '61.700'),
(71, 9, 'P', 'TB/U', '70.100', '72.600', '67.700', '65.300', '62.900'),
(72, 10, 'P', 'TB/U', '71.500', '73.900', '69.000', '66.500', '64.100'),
(73, 11, 'P', 'TB/U', '72.800', '75.300', '70.300', '67.700', '65.200'),
(74, 12, 'P', 'TB/U', '74.000', '76.600', '71.400', '68.900', '66.300'),
(75, 13, 'P', 'TB/U', '75.200', '77.800', '72.600', '70.000', '67.300'),
(76, 14, 'P', 'TB/U', '76.400', '79.100', '73.700', '71.000', '68.300'),
(77, 15, 'P', 'TB/U', '77.500', '80.200', '74.800', '72.000', '69.300'),
(78, 16, 'P', 'TB/U', '78.600', '81.400', '75.800', '73.000', '70.200'),
(79, 17, 'P', 'TB/U', '79.700', '82.500', '76.800', '74.000', '71.100'),
(80, 18, 'P', 'TB/U', '80.700', '83.600', '77.800', '74.900', '72.000'),
(81, 19, 'P', 'TB/U', '81.700', '84.700', '78.800', '75.800', '72.800'),
(82, 20, 'P', 'TB/U', '82.700', '85.700', '79.700', '76.700', '73.700'),
(83, 21, 'P', 'TB/U', '83.700', '86.700', '80.600', '77.500', '74.500'),
(84, 22, 'P', 'TB/U', '84.600', '87.700', '81.500', '78.400', '75.200'),
(85, 23, 'P', 'TB/U', '85.500', '88.700', '82.300', '79.200', '76.000'),
(86, 24, 'P', 'TB/U', '85.700', '88.900', '82.500', '79.300', '76.000'),
(87, 25, 'P', 'TB/U', '86.600', '89.900', '83.300', '80.000', '76.800'),
(88, 26, 'P', 'TB/U', '87.400', '90.800', '84.100', '80.800', '77.500'),
(89, 27, 'P', 'TB/U', '88.300', '91.700', '84.900', '81.500', '78.100'),
(90, 28, 'P', 'TB/U', '89.100', '92.500', '85.700', '82.200', '78.800'),
(91, 29, 'P', 'TB/U', '89.900', '93.400', '86.400', '82.900', '79.500'),
(92, 30, 'P', 'TB/U', '90.700', '94.200', '87.100', '83.600', '80.100'),
(93, 31, 'P', 'TB/U', '91.400', '95.000', '87.900', '84.300', '80.700'),
(94, 32, 'P', 'TB/U', '92.200', '95.800', '88.600', '84.900', '81.300'),
(95, 33, 'P', 'TB/U', '92.900', '96.600', '89.300', '85.600', '81.900'),
(96, 34, 'P', 'TB/U', '93.600', '97.400', '89.900', '86.200', '82.500'),
(97, 35, 'P', 'TB/U', '94.400', '98.100', '90.600', '86.800', '83.100'),
(98, 36, 'P', 'TB/U', '95.100', '98.900', '91.200', '87.400', '83.600'),
(99, 37, 'P', 'TB/U', '95.700', '99.600', '91.900', '88.000', '84.200'),
(100, 38, 'P', 'TB/U', '96.400', '100.300', '92.500', '88.600', '84.700'),
(101, 39, 'P', 'TB/U', '97.100', '101.000', '93.100', '89.200', '85.300'),
(102, 40, 'P', 'TB/U', '97.700', '101.700', '93.800', '89.800', '85.800'),
(103, 41, 'P', 'TB/U', '98.400', '102.400', '94.400', '90.400', '86.300'),
(104, 42, 'P', 'TB/U', '99.000', '103.100', '95.000', '90.900', '86.800'),
(105, 43, 'P', 'TB/U', '99.700', '103.800', '95.600', '91.500', '87.400'),
(106, 44, 'P', 'TB/U', '100.300', '104.500', '96.200', '92.000', '87.900'),
(107, 45, 'P', 'TB/U', '100.900', '105.100', '96.700', '92.500', '88.400'),
(108, 46, 'P', 'TB/U', '101.500', '105.800', '97.300', '93.100', '88.900'),
(109, 47, 'P', 'TB/U', '102.100', '106.400', '97.900', '93.600', '89.300'),
(110, 48, 'P', 'TB/U', '102.700', '107.000', '98.400', '94.100', '89.800'),
(111, 49, 'P', 'TB/U', '103.300', '107.700', '99.000', '94.600', '90.300'),
(112, 50, 'P', 'TB/U', '103.900', '108.300', '99.500', '95.100', '90.700'),
(113, 51, 'P', 'TB/U', '104.500', '108.900', '100.100', '95.600', '91.200'),
(114, 52, 'P', 'TB/U', '105.000', '109.500', '100.600', '96.100', '91.700'),
(115, 53, 'P', 'TB/U', '105.600', '110.100', '101.100', '96.600', '92.100'),
(116, 54, 'P', 'TB/U', '106.200', '110.700', '101.600', '97.100', '92.600'),
(117, 55, 'P', 'TB/U', '106.700', '111.300', '102.200', '97.600', '93.000'),
(118, 56, 'P', 'TB/U', '107.300', '111.900', '102.700', '98.100', '93.500'),
(119, 57, 'P', 'TB/U', '107.800', '112.500', '103.200', '98.500', '93.900'),
(120, 58, 'P', 'TB/U', '108.400', '113.000', '103.700', '99.000', '94.300'),
(121, 59, 'P', 'TB/U', '108.900', '113.600', '104.200', '99.500', '94.800'),
(122, 60, 'P', 'TB/U', '109.400', '114.200', '104.700', '100.000', '95.200'),
(123, 0, 'L', 'BB/U', '3.300', '3.900', '2.900', '2.500', '2.100'),
(124, 1, 'L', 'BB/U', '4.500', '5.100', '3.900', '3.400', '2.900'),
(125, 2, 'L', 'BB/U', '5.600', '6.300', '4.900', '4.300', '3.800'),
(126, 3, 'L', 'BB/U', '6.400', '7.200', '5.700', '5.000', '4.400'),
(127, 4, 'L', 'BB/U', '7.000', '7.800', '6.200', '5.600', '4.900'),
(128, 5, 'L', 'BB/U', '7.500', '8.400', '6.700', '6.000', '5.300'),
(129, 6, 'L', 'BB/U', '7.900', '8.800', '7.100', '6.400', '5.700'),
(130, 7, 'L', 'BB/U', '8.300', '9.200', '7.400', '6.700', '6.000'),
(131, 8, 'L', 'BB/U', '8.600', '9.600', '7.700', '6.900', '6.200'),
(132, 9, 'L', 'BB/U', '8.900', '9.900', '8.000', '7.100', '6.400'),
(133, 10, 'L', 'BB/U', '9.200', '10.200', '8.200', '7.400', '6.600'),
(134, 11, 'L', 'BB/U', '9.400', '10.500', '8.400', '7.600', '6.800'),
(135, 12, 'L', 'BB/U', '9.600', '10.800', '8.600', '7.700', '6.900'),
(136, 13, 'L', 'BB/U', '9.900', '11.000', '8.800', '7.900', '7.100'),
(137, 14, 'L', 'BB/U', '10.100', '11.300', '9.000', '8.100', '7.200'),
(138, 15, 'L', 'BB/U', '10.300', '11.500', '9.200', '8.300', '7.400'),
(139, 16, 'L', 'BB/U', '10.500', '11.700', '9.400', '8.400', '7.500'),
(140, 17, 'L', 'BB/U', '10.700', '12.000', '9.600', '8.600', '7.700'),
(141, 18, 'L', 'BB/U', '10.900', '12.200', '9.800', '8.800', '7.800'),
(142, 19, 'L', 'BB/U', '11.100', '12.500', '10.000', '8.900', '8.000'),
(143, 20, 'L', 'BB/U', '11.300', '12.700', '10.100', '9.100', '8.100'),
(144, 21, 'L', 'BB/U', '11.500', '12.900', '10.300', '9.200', '8.200'),
(145, 22, 'L', 'BB/U', '11.800', '13.200', '10.500', '9.400', '8.400'),
(146, 23, 'L', 'BB/U', '12.000', '13.400', '10.700', '9.500', '8.500'),
(147, 24, 'L', 'BB/U', '12.200', '13.600', '10.800', '9.700', '8.600'),
(148, 25, 'L', 'BB/U', '12.400', '13.900', '11.000', '9.800', '8.800'),
(149, 26, 'L', 'BB/U', '12.500', '14.100', '11.200', '10.000', '8.900'),
(150, 27, 'L', 'BB/U', '12.700', '14.300', '11.300', '10.100', '9.000'),
(151, 28, 'L', 'BB/U', '12.900', '14.500', '11.500', '10.200', '9.100'),
(152, 29, 'L', 'BB/U', '13.100', '14.800', '11.700', '10.400', '9.200'),
(153, 30, 'L', 'BB/U', '13.300', '15.000', '11.800', '10.500', '9.400'),
(154, 31, 'L', 'BB/U', '13.500', '15.200', '12.000', '10.700', '9.500'),
(155, 32, 'L', 'BB/U', '13.700', '15.400', '12.100', '10.800', '9.600'),
(156, 33, 'L', 'BB/U', '13.800', '15.600', '12.300', '10.900', '9.700'),
(157, 34, 'L', 'BB/U', '14.000', '15.800', '12.400', '11.000', '9.800'),
(158, 35, 'L', 'BB/U', '14.200', '16.000', '12.600', '11.200', '9.900'),
(159, 36, 'L', 'BB/U', '14.300', '16.200', '12.700', '11.300', '10.000'),
(160, 37, 'L', 'BB/U', '14.500', '16.400', '12.900', '11.400', '10.100'),
(161, 38, 'L', 'BB/U', '14.700', '16.600', '13.000', '11.500', '10.200'),
(162, 39, 'L', 'BB/U', '14.900', '16.800', '13.100', '11.600', '10.300'),
(163, 40, 'L', 'BB/U', '15.000', '17.000', '13.300', '11.800', '10.400'),
(164, 41, 'L', 'BB/U', '15.200', '17.200', '13.400', '11.900', '10.500'),
(165, 42, 'L', 'BB/U', '15.400', '17.400', '13.600', '12.000', '10.600'),
(166, 43, 'L', 'BB/U', '15.500', '17.600', '13.700', '12.100', '10.700'),
(167, 44, 'L', 'BB/U', '15.700', '17.800', '13.800', '12.200', '10.800'),
(168, 45, 'L', 'BB/U', '15.900', '18.000', '14.000', '12.400', '10.900'),
(169, 46, 'L', 'BB/U', '16.000', '18.200', '14.100', '12.500', '11.000'),
(170, 47, 'L', 'BB/U', '16.200', '18.400', '14.300', '12.600', '11.100'),
(171, 48, 'L', 'BB/U', '16.400', '18.600', '14.400', '12.700', '11.200'),
(172, 49, 'L', 'BB/U', '16.600', '18.800', '14.500', '12.800', '11.300'),
(173, 50, 'L', 'BB/U', '16.700', '19.000', '14.700', '12.900', '11.400'),
(174, 51, 'L', 'BB/U', '16.900', '19.200', '14.800', '13.100', '11.500'),
(175, 52, 'L', 'BB/U', '17.100', '19.400', '15.000', '13.200', '11.600'),
(176, 53, 'L', 'BB/U', '17.200', '19.600', '15.100', '13.300', '11.700'),
(177, 54, 'L', 'BB/U', '17.400', '19.800', '15.200', '13.400', '11.800'),
(178, 55, 'L', 'BB/U', '17.600', '20.000', '15.400', '13.500', '11.900'),
(179, 56, 'L', 'BB/U', '17.700', '20.200', '15.500', '13.600', '12.000'),
(180, 57, 'L', 'BB/U', '17.900', '20.400', '15.600', '13.700', '12.100'),
(181, 58, 'L', 'BB/U', '18.100', '20.600', '15.800', '13.800', '12.200'),
(182, 59, 'L', 'BB/U', '18.200', '20.800', '15.900', '14.000', '12.300'),
(183, 60, 'L', 'BB/U', '18.400', '21.000', '16.000', '14.100', '12.400'),
(184, 0, 'P', 'BB/U', '3.200', '3.700', '2.800', '2.400', '2.000'),
(185, 1, 'P', 'BB/U', '4.200', '4.800', '3.600', '3.200', '2.700'),
(186, 2, 'P', 'BB/U', '5.100', '5.800', '4.500', '3.900', '3.400'),
(187, 3, 'P', 'BB/U', '5.800', '6.600', '5.200', '4.500', '4.000'),
(188, 4, 'P', 'BB/U', '6.400', '7.300', '5.700', '5.000', '4.400'),
(189, 5, 'P', 'BB/U', '6.900', '7.800', '6.100', '5.400', '4.800'),
(190, 6, 'P', 'BB/U', '7.300', '8.200', '6.500', '5.700', '5.100'),
(191, 7, 'P', 'BB/U', '7.600', '8.600', '6.800', '6.000', '5.300'),
(192, 8, 'P', 'BB/U', '7.900', '9.000', '7.000', '6.300', '5.600'),
(193, 9, 'P', 'BB/U', '8.200', '9.300', '7.300', '6.500', '5.800'),
(194, 10, 'P', 'BB/U', '8.500', '9.600', '7.500', '6.700', '5.900'),
(195, 11, 'P', 'BB/U', '8.700', '9.900', '7.700', '6.900', '6.100'),
(196, 12, 'P', 'BB/U', '8.900', '10.100', '7.900', '7.000', '6.300'),
(197, 13, 'P', 'BB/U', '9.200', '10.400', '8.100', '7.200', '6.400'),
(198, 14, 'P', 'BB/U', '9.400', '10.600', '8.300', '7.400', '6.600'),
(199, 15, 'P', 'BB/U', '9.600', '10.900', '8.500', '7.600', '6.700'),
(200, 16, 'P', 'BB/U', '9.800', '11.100', '8.700', '7.700', '6.900'),
(201, 17, 'P', 'BB/U', '10.000', '11.400', '8.900', '7.900', '7.000'),
(202, 18, 'P', 'BB/U', '10.200', '11.600', '9.100', '8.100', '7.200'),
(203, 19, 'P', 'BB/U', '10.400', '11.800', '9.200', '8.200', '7.300'),
(204, 20, 'P', 'BB/U', '10.600', '12.100', '9.400', '8.400', '7.500'),
(205, 21, 'P', 'BB/U', '10.900', '12.300', '9.600', '8.600', '7.600'),
(206, 22, 'P', 'BB/U', '11.100', '12.500', '9.800', '8.700', '7.800'),
(207, 23, 'P', 'BB/U', '11.300', '12.800', '10.000', '8.900', '7.900'),
(208, 24, 'P', 'BB/U', '11.500', '13.000', '10.200', '9.100', '8.100'),
(209, 25, 'P', 'BB/U', '11.700', '13.300', '10.300', '9.200', '8.200'),
(210, 26, 'P', 'BB/U', '11.900', '13.500', '10.500', '9.400', '8.400'),
(211, 27, 'P', 'BB/U', '12.100', '13.700', '10.700', '9.600', '8.500'),
(212, 28, 'P', 'BB/U', '12.300', '14.000', '10.900', '9.700', '8.600'),
(213, 29, 'P', 'BB/U', '12.500', '14.200', '11.100', '9.800', '8.800'),
(214, 30, 'P', 'BB/U', '12.700', '14.400', '11.200', '10.000', '8.900'),
(215, 31, 'P', 'BB/U', '12.900', '14.700', '11.400', '10.100', '9.000'),
(216, 32, 'P', 'BB/U', '13.100', '14.900', '11.600', '10.300', '9.100'),
(217, 33, 'P', 'BB/U', '13.300', '15.100', '11.700', '10.400', '9.300'),
(218, 34, 'P', 'BB/U', '13.500', '15.400', '11.900', '10.500', '9.400'),
(219, 35, 'P', 'BB/U', '13.700', '15.600', '12.000', '10.700', '9.500'),
(220, 36, 'P', 'BB/U', '13.900', '15.800', '12.200', '10.800', '9.600'),
(221, 37, 'P', 'BB/U', '14.000', '16.000', '12.400', '10.900', '9.700'),
(222, 38, 'P', 'BB/U', '14.200', '16.300', '12.500', '11.100', '9.800'),
(223, 39, 'P', 'BB/U', '14.400', '16.500', '12.700', '11.200', '9.900'),
(224, 40, 'P', 'BB/U', '14.600', '16.700', '12.800', '11.300', '10.100'),
(225, 41, 'P', 'BB/U', '14.800', '16.900', '13.000', '11.500', '10.200'),
(226, 42, 'P', 'BB/U', '15.000', '17.200', '13.100', '11.600', '10.300'),
(227, 43, 'P', 'BB/U', '15.200', '17.400', '13.300', '11.700', '10.400'),
(228, 44, 'P', 'BB/U', '15.300', '17.600', '13.400', '11.800', '10.500'),
(229, 45, 'P', 'BB/U', '15.500', '17.800', '13.600', '12.000', '10.700'),
(230, 46, 'P', 'BB/U', '15.700', '18.100', '13.700', '12.100', '10.700'),
(231, 47, 'P', 'BB/U', '15.900', '18.300', '13.900', '12.200', '10.800'),
(232, 48, 'P', 'BB/U', '16.100', '18.500', '14.000', '12.300', '10.900'),
(233, 49, 'P', 'BB/U', '16.300', '18.800', '14.200', '12.400', '11.200'),
(234, 50, 'P', 'BB/U', '16.400', '19.000', '14.300', '12.600', '11.300'),
(235, 51, 'P', 'BB/U', '16.600', '19.200', '14.500', '12.700', '11.400'),
(236, 52, 'P', 'BB/U', '16.800', '19.400', '14.600', '12.800', '11.600'),
(237, 53, 'P', 'BB/U', '17.000', '19.700', '14.800', '12.900', '11.700'),
(238, 54, 'P', 'BB/U', '17.200', '19.900', '14.900', '13.000', '11.800'),
(239, 55, 'P', 'BB/U', '17.300', '20.100', '15.100', '13.200', '11.900'),
(240, 56, 'P', 'BB/U', '17.500', '20.300', '15.200', '13.300', '12.000'),
(241, 57, 'P', 'BB/U', '17.700', '20.600', '15.300', '13.400', '12.100'),
(242, 58, 'P', 'BB/U', '17.900', '20.800', '15.500', '13.500', '12.200'),
(243, 59, 'P', 'BB/U', '18.000', '21.000', '15.600', '13.600', '12.400'),
(244, 60, 'P', 'BB/U', '18.200', '21.100', '15.800', '13.700', '12.500');

-- --------------------------------------------------------

--
-- Table structure for table `stok_log`
--

CREATE TABLE `stok_log` (
  `id_stok_log` int NOT NULL,
  `id_gudang` int NOT NULL,
  `id_komoditas` int NOT NULL,
  `qty_in` decimal(10,2) NOT NULL DEFAULT '0.00',
  `qty_out` decimal(10,2) NOT NULL DEFAULT '0.00',
  `qty_current` decimal(10,2) GENERATED ALWAYS AS ((`qty_in` - `qty_out`)) STORED,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `stok_log`
--

INSERT INTO `stok_log` (`id_stok_log`, `id_gudang`, `id_komoditas`, `qty_in`, `qty_out`, `last_updated`) VALUES
(3, 3, 2, '100.00', '10.00', '2026-06-26 02:47:15'),
(5, 2, 2, '10.00', '0.00', '2026-06-26 02:47:15');

-- --------------------------------------------------------

--
-- Table structure for table `t_distribusi`
--

CREATE TABLE `t_distribusi` (
  `id_distribusi` int NOT NULL,
  `no_distribusi` varchar(50) NOT NULL,
  `id_gudang_asal` int NOT NULL,
  `id_gudang_tujuan` int NOT NULL,
  `tanggal_distribusi` date NOT NULL,
  `status_distribusi` enum('Dikirim','Diterima','Dibatalkan') NOT NULL DEFAULT 'Dikirim',
  `catatan` text,
  `created_by` int DEFAULT NULL,
  `received_by` int DEFAULT NULL,
  `received_at` datetime DEFAULT NULL,
  `tgl_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `tgl_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `t_distribusi`
--

INSERT INTO `t_distribusi` (`id_distribusi`, `no_distribusi`, `id_gudang_asal`, `id_gudang_tujuan`, `tanggal_distribusi`, `status_distribusi`, `catatan`, `created_by`, `received_by`, `received_at`, `tgl_created`, `tgl_updated`, `deleted_at`) VALUES
(4, 'DIST-20260626-002', 3, 2, '2026-06-26', 'Diterima', '', 1, 8, '2026-06-26 09:47:15', '2026-06-26 02:46:54', '2026-06-26 02:47:15', NULL);

--
-- Triggers `t_distribusi`
--
DELIMITER $$
CREATE TRIGGER `trg_t_distribusi_after_terima` AFTER UPDATE ON `t_distribusi` FOR EACH ROW BEGIN
    IF OLD.status_distribusi = 'Dikirim'
       AND NEW.status_distribusi = 'Diterima' THEN

        -- Gudang asal keluar barang
        INSERT INTO stok_log (
            id_gudang,
            id_komoditas,
            qty_in,
            qty_out
        )
        SELECT
            NEW.id_gudang_asal,
            dd.id_komoditas,
            0.00,
            dd.jumlah
        FROM t_distribusi_detail dd
        WHERE dd.id_distribusi = NEW.id_distribusi
        ON DUPLICATE KEY UPDATE
            qty_out = qty_out + VALUES(qty_out);

        -- Gudang tujuan masuk barang
        INSERT INTO stok_log (
            id_gudang,
            id_komoditas,
            qty_in,
            qty_out
        )
        SELECT
            NEW.id_gudang_tujuan,
            dd.id_komoditas,
            dd.jumlah,
            0.00
        FROM t_distribusi_detail dd
        WHERE dd.id_distribusi = NEW.id_distribusi
        ON DUPLICATE KEY UPDATE
            qty_in = qty_in + VALUES(qty_in);

    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_t_distribusi_before_terima` BEFORE UPDATE ON `t_distribusi` FOR EACH ROW BEGIN
    IF OLD.status_distribusi = 'Dikirim'
       AND NEW.status_distribusi = 'Diterima' THEN

        IF EXISTS (
            SELECT 1
            FROM t_distribusi_detail dd
            LEFT JOIN stok_log sl
                ON sl.id_gudang = OLD.id_gudang_asal
                AND sl.id_komoditas = dd.id_komoditas
            WHERE dd.id_distribusi = OLD.id_distribusi
              AND (COALESCE(sl.qty_in, 0) - COALESCE(sl.qty_out, 0)) < dd.jumlah
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Stok gudang asal tidak mencukupi untuk menerima distribusi.';
        END IF;

    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `t_distribusi_detail`
--

CREATE TABLE `t_distribusi_detail` (
  `id_distribusi_detail` int NOT NULL,
  `id_distribusi` int NOT NULL,
  `id_komoditas` int NOT NULL,
  `jumlah` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tgl_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `t_distribusi_detail`
--

INSERT INTO `t_distribusi_detail` (`id_distribusi_detail`, `id_distribusi`, `id_komoditas`, `jumlah`, `tgl_created`) VALUES
(4, 4, 2, '10.00', '2026-06-26 02:46:54');

-- --------------------------------------------------------

--
-- Table structure for table `t_pemeriksaan`
--

CREATE TABLE `t_pemeriksaan` (
  `id_pemeriksaan` int NOT NULL,
  `id_anak` int NOT NULL,
  `id_kader` int DEFAULT NULL,
  `tanggal_pemeriksaan` date NOT NULL,
  `berat_badan` decimal(5,2) DEFAULT NULL,
  `tinggi_badan` decimal(5,2) DEFAULT NULL,
  `lingkar_kepala` decimal(5,2) DEFAULT NULL,
  `usia_bulan` int DEFAULT NULL,
  `status_gizi` varchar(50) DEFAULT NULL,
  `catatan` text,
  `tgl_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `tgl_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `t_pengadaan`
--

CREATE TABLE `t_pengadaan` (
  `id_pengadaan` int NOT NULL,
  `id_petani` int DEFAULT NULL,
  `id_gudang` int NOT NULL,
  `no_kontrak` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_pengadaan` date NOT NULL,
  `total_bayar` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status_bayar` enum('Pending','Lunas') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `status_kontrak` enum('Mencari Petani','Disetujui','Dibatalkan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Mencari Petani',
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tgl_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `tgl_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `t_pengadaan`
--

INSERT INTO `t_pengadaan` (`id_pengadaan`, `id_petani`, `id_gudang`, `no_kontrak`, `tgl_pengadaan`, `total_bayar`, `status_bayar`, `status_kontrak`, `keterangan`, `tgl_created`, `tgl_updated`) VALUES
(1, 4, 2, 'REQ-202606-001', '2026-06-24', '0.00', 'Lunas', 'Disetujui', NULL, '2026-06-23 17:14:41', '2026-06-24 02:33:28'),
(2, 4, 1, 'REQ-202606-002', '2026-06-23', '0.00', 'Lunas', 'Disetujui', 'Tambahan saftey untuk telur karena mudah pecah', '2026-06-23 17:26:19', '2026-06-24 07:11:29'),
(3, 4, 2, 'REQ-202606-003', '2026-06-23', '0.00', 'Pending', 'Disetujui', 'Ikan Tuna ya', '2026-06-23 17:53:30', '2026-06-23 17:55:26'),
(4, NULL, 2, 'REQ-202606-004', '2026-06-23', '0.00', 'Pending', 'Mencari Petani', 'yang segar', '2026-06-23 17:57:30', '2026-06-23 17:57:30'),
(5, 4, 1, 'REQ-202606-005', '2026-06-24', '0.00', 'Lunas', 'Disetujui', 'test', '2026-06-24 07:15:36', '2026-06-24 07:18:26'),
(6, 4, 3, 'REQ-202606-006', '2026-06-26', '0.00', 'Lunas', 'Disetujui', '', '2026-06-26 02:39:43', '2026-06-26 02:40:44');

--
-- Triggers `t_pengadaan`
--
DELIMITER $$
CREATE TRIGGER `after_update_lunas_pengadaan` AFTER UPDATE ON `t_pengadaan` FOR EACH ROW BEGIN
    -- 1. Cek syarat: Hanya jalan jika status_bayar berubah dari Pending ke Lunas
    IF OLD.status_bayar = 'Pending' AND NEW.status_bayar = 'Lunas' THEN
        
        -- 2. Ambil data dari detail, langsung tembak/pindahkan ke stok_log
        INSERT INTO stok_log (id_gudang, id_komoditas, qty_in, qty_out)
        SELECT NEW.id_gudang, pd.id_komoditas, pd.jumlah, 0.00
        FROM t_pengadaan_detail pd
        WHERE pd.id_pengadaan = NEW.id_pengadaan
        -- 3. Jika duplikat (kombinasi gudang & barang sudah ada), otomatis update qty_in
        ON DUPLICATE KEY UPDATE qty_in = qty_in + VALUES(qty_in);

    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `t_pengadaan_detail`
--

CREATE TABLE `t_pengadaan_detail` (
  `id_pengadaan_detail` int NOT NULL,
  `id_pengadaan` int NOT NULL,
  `id_komoditas` int NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `harga_satuan` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) GENERATED ALWAYS AS ((`jumlah` * `harga_satuan`)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `t_pengadaan_detail`
--

INSERT INTO `t_pengadaan_detail` (`id_pengadaan_detail`, `id_pengadaan`, `id_komoditas`, `jumlah`, `harga_satuan`) VALUES
(1, 1, 2, '100.00', '15000.00'),
(2, 2, 3, '200.00', '5000.00'),
(3, 3, 2, '10.00', '20000.00'),
(4, 4, 3, '10.00', '50000.00'),
(5, 5, 3, '10.00', '50000.00'),
(6, 6, 2, '100.00', '3000.00');

-- --------------------------------------------------------

--
-- Table structure for table `t_penyerahan`
--

CREATE TABLE `t_penyerahan` (
  `id_penyerahan` int NOT NULL,
  `no_penyerahan` varchar(50) NOT NULL,
  `id_ibu` int NOT NULL,
  `id_anak` int DEFAULT NULL,
  `id_gudang` int NOT NULL,
  `tanggal_penyerahan` date NOT NULL,
  `status_penyerahan` enum('Diproses','Diserahkan','Dibatalkan') NOT NULL DEFAULT 'Diproses',
  `catatan` text,
  `created_by` int DEFAULT NULL,
  `tgl_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `tgl_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `t_penyerahan`
--

INSERT INTO `t_penyerahan` (`id_penyerahan`, `no_penyerahan`, `id_ibu`, `id_anak`, `id_gudang`, `tanggal_penyerahan`, `status_penyerahan`, `catatan`, `created_by`, `tgl_created`, `tgl_updated`, `deleted_at`) VALUES
(1, 'PNY-20260626022846', 3, 1, 1, '2026-06-26', 'Diproses', '', 1, '2026-06-26 02:28:46', '2026-06-26 02:28:46', NULL);

--
-- Triggers `t_penyerahan`
--
DELIMITER $$
CREATE TRIGGER `trg_t_penyerahan_after_diserahkan` AFTER UPDATE ON `t_penyerahan` FOR EACH ROW BEGIN
    IF OLD.status_penyerahan = 'Diproses'
       AND NEW.status_penyerahan = 'Diserahkan' THEN

        INSERT INTO stok_log (
            id_gudang,
            id_komoditas,
            qty_in,
            qty_out
        )
        SELECT
            NEW.id_gudang,
            pd.id_komoditas,
            0.00,
            pd.jumlah
        FROM t_penyerahan_detail pd
        WHERE pd.id_penyerahan = NEW.id_penyerahan
        ON DUPLICATE KEY UPDATE
            qty_out = qty_out + VALUES(qty_out);

    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_t_penyerahan_before_diserahkan` BEFORE UPDATE ON `t_penyerahan` FOR EACH ROW BEGIN
    IF OLD.status_penyerahan = 'Diproses'
       AND NEW.status_penyerahan = 'Diserahkan' THEN

        IF EXISTS (
            SELECT 1
            FROM t_penyerahan_detail pd
            LEFT JOIN stok_log sl
                ON sl.id_gudang = OLD.id_gudang
                AND sl.id_komoditas = pd.id_komoditas
            WHERE pd.id_penyerahan = OLD.id_penyerahan
              AND (COALESCE(sl.qty_in, 0) - COALESCE(sl.qty_out, 0)) < pd.jumlah
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Stok posyandu tidak mencukupi untuk penyerahan bantuan.';
        END IF;

    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `t_penyerahan_detail`
--

CREATE TABLE `t_penyerahan_detail` (
  `id_penyerahan_detail` int NOT NULL,
  `id_penyerahan` int NOT NULL,
  `id_komoditas` int NOT NULL,
  `jumlah` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tgl_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `t_penyerahan_detail`
--

INSERT INTO `t_penyerahan_detail` (`id_penyerahan_detail`, `id_penyerahan`, `id_komoditas`, `jumlah`, `tgl_created`) VALUES
(1, 1, 2, '1.50', '2026-06-26 02:28:46');

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
(8, 'kader', '$2y$10$CB8nEEWOViMizuREKJfuC.5YaxExZjaev/Xl.Ya0rNySufOKVtaoi', 'Kader', 1, '2026-06-19 03:07:16', '2026-06-19 03:07:16', NULL),
(12, 'petani1', '$2y$10$ySgBi.xEcsf2S/Cv4cvwf.uod8WI4W41LtbIvM9IhxsbLh4ISHVr.', 'Petani', 1, '2026-06-26 02:56:44', '2026-06-26 02:56:44', NULL);

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
  ADD PRIMARY KEY (`id_standar`);

--
-- Indexes for table `stok_log`
--
ALTER TABLE `stok_log`
  ADD PRIMARY KEY (`id_stok_log`),
  ADD UNIQUE KEY `uk_stok` (`id_gudang`,`id_komoditas`),
  ADD UNIQUE KEY `uq_stok_log_gudang_komoditas` (`id_gudang`,`id_komoditas`),
  ADD KEY `fk_stok_komoditas` (`id_komoditas`);

--
-- Indexes for table `t_distribusi`
--
ALTER TABLE `t_distribusi`
  ADD PRIMARY KEY (`id_distribusi`),
  ADD UNIQUE KEY `no_distribusi` (`no_distribusi`),
  ADD KEY `fk_t_distribusi_created_by` (`created_by`),
  ADD KEY `fk_t_distribusi_received_by` (`received_by`),
  ADD KEY `idx_t_distribusi_status` (`status_distribusi`),
  ADD KEY `idx_t_distribusi_tanggal` (`tanggal_distribusi`),
  ADD KEY `idx_t_distribusi_asal` (`id_gudang_asal`),
  ADD KEY `idx_t_distribusi_tujuan` (`id_gudang_tujuan`);

--
-- Indexes for table `t_distribusi_detail`
--
ALTER TABLE `t_distribusi_detail`
  ADD PRIMARY KEY (`id_distribusi_detail`),
  ADD KEY `fk_t_distribusi_detail_distribusi` (`id_distribusi`),
  ADD KEY `idx_t_distribusi_detail_komoditas` (`id_komoditas`);

--
-- Indexes for table `t_pemeriksaan`
--
ALTER TABLE `t_pemeriksaan`
  ADD PRIMARY KEY (`id_pemeriksaan`),
  ADD KEY `idx_t_pemeriksaan_anak` (`id_anak`),
  ADD KEY `idx_t_pemeriksaan_kader` (`id_kader`),
  ADD KEY `idx_t_pemeriksaan_tanggal` (`tanggal_pemeriksaan`),
  ADD KEY `idx_t_pemeriksaan_status` (`status_gizi`);

--
-- Indexes for table `t_pengadaan`
--
ALTER TABLE `t_pengadaan`
  ADD PRIMARY KEY (`id_pengadaan`),
  ADD UNIQUE KEY `no_kontrak` (`no_kontrak`),
  ADD KEY `fk_t_pengadaan_petani` (`id_petani`),
  ADD KEY `fk_t_pengadaan_gudang` (`id_gudang`);

--
-- Indexes for table `t_pengadaan_detail`
--
ALTER TABLE `t_pengadaan_detail`
  ADD PRIMARY KEY (`id_pengadaan_detail`),
  ADD KEY `fk_t_detail_komoditas` (`id_komoditas`),
  ADD KEY `fk_detail_pengadaan_induk` (`id_pengadaan`);

--
-- Indexes for table `t_penyerahan`
--
ALTER TABLE `t_penyerahan`
  ADD PRIMARY KEY (`id_penyerahan`),
  ADD UNIQUE KEY `no_penyerahan` (`no_penyerahan`),
  ADD KEY `fk_t_penyerahan_created_by` (`created_by`),
  ADD KEY `idx_t_penyerahan_ibu` (`id_ibu`),
  ADD KEY `idx_t_penyerahan_anak` (`id_anak`),
  ADD KEY `idx_t_penyerahan_gudang` (`id_gudang`),
  ADD KEY `idx_t_penyerahan_status` (`status_penyerahan`),
  ADD KEY `idx_t_penyerahan_tanggal` (`tanggal_penyerahan`);

--
-- Indexes for table `t_penyerahan_detail`
--
ALTER TABLE `t_penyerahan_detail`
  ADD PRIMARY KEY (`id_penyerahan_detail`),
  ADD KEY `fk_t_penyerahan_detail_penyerahan` (`id_penyerahan`),
  ADD KEY `idx_t_penyerahan_detail_komoditas` (`id_komoditas`);

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
  MODIFY `id_gudang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `komoditas_pangan`
--
ALTER TABLE `komoditas_pangan`
  MODIFY `id_komoditas` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `satuan`
--
ALTER TABLE `satuan`
  MODIFY `id_satuan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `standar_pertumbuhan`
--
ALTER TABLE `standar_pertumbuhan`
  MODIFY `id_standar` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=245;

--
-- AUTO_INCREMENT for table `stok_log`
--
ALTER TABLE `stok_log`
  MODIFY `id_stok_log` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `t_distribusi`
--
ALTER TABLE `t_distribusi`
  MODIFY `id_distribusi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `t_distribusi_detail`
--
ALTER TABLE `t_distribusi_detail`
  MODIFY `id_distribusi_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `t_pemeriksaan`
--
ALTER TABLE `t_pemeriksaan`
  MODIFY `id_pemeriksaan` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_pengadaan`
--
ALTER TABLE `t_pengadaan`
  MODIFY `id_pengadaan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `t_pengadaan_detail`
--
ALTER TABLE `t_pengadaan_detail`
  MODIFY `id_pengadaan_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `t_penyerahan`
--
ALTER TABLE `t_penyerahan`
  MODIFY `id_penyerahan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `t_penyerahan_detail`
--
ALTER TABLE `t_penyerahan_detail`
  MODIFY `id_penyerahan_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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

--
-- Constraints for table `stok_log`
--
ALTER TABLE `stok_log`
  ADD CONSTRAINT `fk_stok_gudang` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id_gudang`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_stok_komoditas` FOREIGN KEY (`id_komoditas`) REFERENCES `komoditas_pangan` (`id_komoditas`) ON DELETE RESTRICT;

--
-- Constraints for table `t_distribusi`
--
ALTER TABLE `t_distribusi`
  ADD CONSTRAINT `fk_t_distribusi_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `fk_t_distribusi_gudang_asal` FOREIGN KEY (`id_gudang_asal`) REFERENCES `gudang` (`id_gudang`),
  ADD CONSTRAINT `fk_t_distribusi_gudang_tujuan` FOREIGN KEY (`id_gudang_tujuan`) REFERENCES `gudang` (`id_gudang`),
  ADD CONSTRAINT `fk_t_distribusi_received_by` FOREIGN KEY (`received_by`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `t_distribusi_detail`
--
ALTER TABLE `t_distribusi_detail`
  ADD CONSTRAINT `fk_t_distribusi_detail_distribusi` FOREIGN KEY (`id_distribusi`) REFERENCES `t_distribusi` (`id_distribusi`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_t_distribusi_detail_komoditas` FOREIGN KEY (`id_komoditas`) REFERENCES `komoditas_pangan` (`id_komoditas`);

--
-- Constraints for table `t_pemeriksaan`
--
ALTER TABLE `t_pemeriksaan`
  ADD CONSTRAINT `fk_t_pemeriksaan_anak` FOREIGN KEY (`id_anak`) REFERENCES `anak` (`id_anak`),
  ADD CONSTRAINT `fk_t_pemeriksaan_kader` FOREIGN KEY (`id_kader`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `t_pengadaan`
--
ALTER TABLE `t_pengadaan`
  ADD CONSTRAINT `fk_t_pengadaan_gudang` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id_gudang`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_t_pengadaan_petani` FOREIGN KEY (`id_petani`) REFERENCES `petani_lokal` (`id_petani`) ON DELETE RESTRICT;

--
-- Constraints for table `t_penyerahan`
--
ALTER TABLE `t_penyerahan`
  ADD CONSTRAINT `fk_t_penyerahan_anak` FOREIGN KEY (`id_anak`) REFERENCES `anak` (`id_anak`),
  ADD CONSTRAINT `fk_t_penyerahan_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `fk_t_penyerahan_gudang` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id_gudang`),
  ADD CONSTRAINT `fk_t_penyerahan_ibu` FOREIGN KEY (`id_ibu`) REFERENCES `ibu` (`id_ibu`);

--
-- Constraints for table `t_penyerahan_detail`
--
ALTER TABLE `t_penyerahan_detail`
  ADD CONSTRAINT `fk_t_penyerahan_detail_komoditas` FOREIGN KEY (`id_komoditas`) REFERENCES `komoditas_pangan` (`id_komoditas`),
  ADD CONSTRAINT `fk_t_penyerahan_detail_penyerahan` FOREIGN KEY (`id_penyerahan`) REFERENCES `t_penyerahan` (`id_penyerahan`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
