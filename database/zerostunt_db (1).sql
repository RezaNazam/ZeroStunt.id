-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 13, 2026 at 01:21 AM
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

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_buat_penyerahan_dari_paket` (IN `p_id_ibu` INT, IN `p_id_anak` INT, IN `p_id_gudang` INT, IN `p_id_paket` INT, IN `p_tanggal_penyerahan` DATE, IN `p_catatan` TEXT, OUT `p_id_penyerahan` INT)   BEGIN
    DECLARE v_paket_aktif INT DEFAULT 0;
    DECLARE v_total_detail INT DEFAULT 0;
    DECLARE v_stok_kurang INT DEFAULT 0;
    DECLARE v_anak_valid INT DEFAULT 0;
    DECLARE v_no_penyerahan VARCHAR(50);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SELECT COUNT(*)
    INTO v_paket_aktif
    FROM paket_gizi
    WHERE id_paket = p_id_paket
      AND is_active = 1;

    IF v_paket_aktif = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Paket gizi tidak aktif atau tidak ditemukan.';
    END IF;

    SELECT COUNT(*)
    INTO v_total_detail
    FROM paket_gizi_detail
    WHERE id_paket = p_id_paket;

    IF v_total_detail = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Paket gizi belum memiliki detail komoditas.';
    END IF;

    IF p_id_anak IS NOT NULL AND p_id_anak > 0 THEN
        SELECT COUNT(*)
        INTO v_anak_valid
        FROM anak
        WHERE id_anak = p_id_anak
          AND id_ibu = p_id_ibu;

        IF v_anak_valid = 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Anak tidak sesuai dengan ibu penerima.';
        END IF;
    END IF;

    SELECT COUNT(*)
    INTO v_stok_kurang
    FROM paket_gizi_detail pgd
    LEFT JOIN stok_log sl
        ON sl.id_gudang = p_id_gudang
        AND sl.id_komoditas = pgd.id_komoditas
    WHERE pgd.id_paket = p_id_paket
      AND COALESCE(sl.qty_current, 0) < pgd.jumlah;

    IF v_stok_kurang > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stok posyandu tidak mencukupi untuk paket yang dipilih.';
    END IF;

    SET v_no_penyerahan = CONCAT(
        'PNY-',
        DATE_FORMAT(NOW(), '%Y%m%d%H%i%s'),
        '-',
        LPAD(FLOOR(RAND() * 1000), 3, '0')
    );

    INSERT INTO t_penyerahan (
        no_penyerahan,
        id_ibu,
        id_anak,
        id_gudang,
        id_paket,
        tanggal_penyerahan,
        status_penyerahan,
        catatan
    ) VALUES (
        v_no_penyerahan,
        p_id_ibu,
        NULLIF(p_id_anak, 0),
        p_id_gudang,
        p_id_paket,
        p_tanggal_penyerahan,
        'Diproses',
        p_catatan
    );

    SET p_id_penyerahan = LAST_INSERT_ID();

    INSERT INTO t_penyerahan_detail (
        id_penyerahan,
        id_komoditas,
        jumlah
    )
    SELECT
        p_id_penyerahan,
        id_komoditas,
        jumlah
    FROM paket_gizi_detail
    WHERE id_paket = p_id_paket;

    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_buat_penyerahan_dari_prioritas_anak` (IN `p_id_ibu` INT, IN `p_id_anak` INT, IN `p_id_gudang` INT, IN `p_tanggal_penyerahan` DATE, IN `p_catatan` TEXT, OUT `p_id_penyerahan` INT)   BEGIN
    DECLARE v_skala_prioritas INT DEFAULT 0;
    DECLARE v_id_paket INT DEFAULT 0;
    DECLARE v_total_detail INT DEFAULT 0;
    DECLARE v_stok_kurang INT DEFAULT 0;
    DECLARE v_no_penyerahan VARCHAR(50);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- ==========================================
    -- 1. PENENTUAN PRIORITAS (BARU)
    -- ==========================================
    -- Cek jika p_id_anak adalah NULL atau 0 (Artinya ini untuk Ibu Hamil)
    IF p_id_anak IS NULL OR p_id_anak = 0 THEN
        SET v_skala_prioritas = 1;
    ELSE
        -- Jika ada id_anak, cari prioritas dari tabel anak
        SELECT COALESCE(skala_prioritas, 0)
        INTO v_skala_prioritas
        FROM anak
        WHERE id_anak = p_id_anak
          AND id_ibu = p_id_ibu
          AND deleted_at IS NULL
        LIMIT 1;
    END IF;

    IF v_skala_prioritas NOT IN (1, 2, 3) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Skala prioritas penerima tidak valid atau belum diisi.';
    END IF;

    -- ==========================================
    -- 2. PENGECEKAN PAKET
    -- ==========================================
    SELECT COALESCE(id_paket, 0)
    INTO v_id_paket
    FROM paket_gizi
    WHERE kode_prioritas = CONCAT('PRIORITAS_', v_skala_prioritas)
      AND is_active = 1
    LIMIT 1;

    IF v_id_paket <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Paket gizi untuk prioritas ini belum tersedia atau tidak aktif.';
    END IF;

    SELECT COUNT(*)
    INTO v_total_detail
    FROM paket_gizi_detail
    WHERE id_paket = v_id_paket;

    IF v_total_detail = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Paket gizi belum memiliki detail komoditas.';
    END IF;

    -- ==========================================
    -- 3. PENGECEKAN STOK
    -- ==========================================
    SELECT COUNT(*)
    INTO v_stok_kurang
    FROM paket_gizi_detail pgd
    LEFT JOIN stok_log sl
        ON sl.id_gudang = p_id_gudang
        AND sl.id_komoditas = pgd.id_komoditas
    WHERE pgd.id_paket = v_id_paket
      AND COALESCE(sl.qty_current, 0) < pgd.jumlah;

    IF v_stok_kurang > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stok posyandu tidak mencukupi untuk paket prioritas ini.';
    END IF;

    -- ==========================================
    -- 4. INSERT DATA PENYERAHAN
    -- ==========================================
    SET v_no_penyerahan = CONCAT(
        'PNY-',
        DATE_FORMAT(NOW(), '%Y%m%d%H%i%s'),
        '-',
        LPAD(FLOOR(RAND() * 1000), 3, '0')
    );

    INSERT INTO t_penyerahan (
        no_penyerahan,
        id_ibu,
        id_anak,
        id_gudang,
        id_paket,
        tanggal_penyerahan,
        status_penyerahan,
        catatan
    ) VALUES (
        v_no_penyerahan,
        p_id_ibu,
        NULLIF(p_id_anak, 0), -- Akan menjadi NULL di database jika dikirim 0/NULL
        p_id_gudang,
        v_id_paket,
        p_tanggal_penyerahan,
        'Diproses',
        p_catatan
    );

    SET p_id_penyerahan = LAST_INSERT_ID();

    -- ==========================================
    -- 5. INSERT DATA DETAIL PENYERAHAN
    -- ==========================================
    INSERT INTO t_penyerahan_detail (
        id_penyerahan,
        id_komoditas,
        jumlah
    )
    SELECT
        p_id_penyerahan,
        id_komoditas,
        jumlah
    FROM paket_gizi_detail
    WHERE id_paket = v_id_paket;

    COMMIT;
END$$

DELIMITER ;

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
(1, 9, '32752201230001', 'Citra Aulia', '2023-01-22', 'P', 'Prioritas 2', '2', '2026-01-06 08:00:00', NULL),
(2, 10, '32751709230002', 'Bima Nugraha', '2023-09-17', 'L', 'Prioritas 2', '2', '2026-01-06 08:00:00', NULL),
(3, 11, '32752705250003', 'Daffa Saputra', '2025-05-27', 'L', 'Prioritas 3', '3', '2026-01-06 08:00:00', NULL),
(4, 12, '32751602180004', 'Fajar Nugraha', '2018-02-16', 'L', 'Prioritas 1', '1', '2026-01-06 08:00:00', NULL),
(5, 13, '32750609230005', 'Gilang Ramadhan', '2023-09-06', 'L', 'Prioritas 3', '3', '2026-01-06 08:00:00', NULL),
(6, 14, '32752402180006', 'Eka Wijaya', '2018-02-24', 'P', 'Prioritas 2', '2', '2026-01-06 08:00:00', NULL),
(7, 15, '32750308240007', 'Hafiz Ramadhan', '2024-08-03', 'L', 'Prioritas 2', '2', '2026-01-06 08:00:00', NULL),
(8, 16, '32752404260008', 'Gita Aulia', '2026-04-24', 'P', 'Prioritas 1', '1', '2026-01-06 08:00:00', NULL),
(9, 17, '32753112250009', 'Hana Ramadhani', '2025-12-31', 'P', 'Prioritas 3', '3', '2026-01-06 08:00:00', NULL),
(10, 18, '32752707220010', 'Irfan Nugraha', '2022-07-27', 'L', 'Prioritas 3', '3', '2026-01-06 08:00:00', NULL),
(11, 18, '32753001170011', 'Intan Aulia', '2017-01-30', 'P', 'Prioritas 2', '2', '2026-01-06 08:00:00', NULL),
(12, 19, '32752704230012', 'Joko Ramadhan', '2023-04-27', 'L', 'Prioritas 2', '2', '2026-01-06 08:00:00', NULL),
(13, 20, '32750411230013', 'Jihan Wijaya', '2023-11-04', 'P', 'Prioritas 3', '3', '2026-01-06 08:00:00', NULL),
(14, 21, '32752111220014', 'Kevin Ramadhan', '2022-11-21', 'L', 'Prioritas 3', '3', '2026-01-06 08:00:00', NULL),
(15, 22, '32750606230015', 'Kirana Ramadhani', '2023-06-06', 'P', 'Prioritas 2', '2', '2026-01-06 08:00:00', NULL),
(16, 23, '32750704260016', 'Lala Wijaya', '2026-04-07', 'P', 'Prioritas 3', '3', '2026-01-06 08:00:00', NULL),
(17, 23, '32752809190017', 'Mira Anjani', '2019-09-28', 'P', 'Prioritas 3', '3', '2026-01-06 08:00:00', NULL),
(18, 24, '32750608220018', 'Nadia Anjani', '2022-08-06', 'P', 'Prioritas 2', '2', '2026-01-06 08:00:00', NULL),
(19, 25, '32751708180019', 'Luthfi Ramadhan', '2018-08-17', 'L', 'Prioritas 1', '1', '2026-01-06 08:00:00', NULL),
(20, 25, '32751405220020', 'Putri Wijaya', '2022-05-14', 'P', 'Prioritas 3', '3', '2026-01-06 08:00:00', NULL),
(21, 26, '32751603230021', 'Qonita Anjani', '2023-03-16', 'P', 'Prioritas 1', '1', '2026-01-06 08:00:00', NULL),
(22, 26, '32750401230022', 'Rizky Wijaya', '2023-01-04', 'L', 'Prioritas 3', '3', '2026-01-06 08:00:00', NULL),
(23, 27, '32750708180023', 'Satria Pratama', '2018-08-07', 'L', 'Prioritas 3', '3', '2026-01-06 08:00:00', NULL),
(24, 28, '32752901190024', 'Rara Aulia', '2019-01-29', 'P', 'Prioritas 2', '2', '2026-01-06 08:00:00', NULL),
(25, 28, '32751807240025', 'Salsa Salsabila', '2024-07-18', 'P', 'Prioritas 3', '3', '2026-01-06 08:00:00', NULL),
(26, 29, '32750501190026', 'Teguh Pratama', '2019-01-05', 'L', 'Prioritas 1', '1', '2026-01-06 08:00:00', NULL),
(27, 29, '32752411250027', 'Umar Nugraha', '2025-11-24', 'L', 'Prioritas 3', '3', '2026-01-06 08:00:00', NULL),
(28, 30, '32751812220028', 'Wahyu Nugraha', '2022-12-18', 'L', 'Prioritas 2', '2', '2026-01-06 08:00:00', NULL),
(29, 31, '32751102220029', 'Yusuf Saputra', '2022-02-11', 'L', 'Prioritas 3', '3', '2026-01-06 08:00:00', NULL),
(30, 31, '32750712250030', 'Zaki Pratama', '2025-12-07', 'L', 'Prioritas 2', '2', '2026-01-06 08:00:00', NULL),
(31, 32, '32750110160031', 'Arya Wijaya', '2016-10-01', 'L', 'Prioritas 2', '2', '2026-01-06 08:00:00', NULL),
(32, 33, '32751011250032', 'Bagas Pratama', '2025-11-10', 'L', 'Prioritas 3', '3', '2026-01-06 08:00:00', NULL),
(33, 34, '32751308220033', 'Tiara Wijaya', '2022-08-13', 'P', 'Prioritas 1', '1', '2026-01-06 08:00:00', NULL),
(34, 35, '32750804220034', 'Vania Anjani', '2022-04-08', 'P', 'Prioritas 2', '2', '2026-01-06 08:00:00', NULL),
(35, 35, '32750911250035', 'Winda Wijaya', '2025-11-09', 'P', 'Prioritas 1', '1', '2026-01-06 08:00:00', NULL),
(36, 36, '32752902240036', 'Yasmin Anjani', '2024-02-29', 'P', 'Prioritas 1', '1', '2026-01-06 08:00:00', NULL),
(37, 37, '32752002160037', 'Bima Ramadhan', '2016-02-20', 'L', 'Prioritas 1', '1', '2026-01-06 08:00:00', NULL),
(38, 38, '32751211250038', 'Daffa Ramadhan', '2025-11-12', 'L', 'Prioritas 3', '3', '2026-01-06 08:00:00', NULL);

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
(1, 'Gudang Pusat Zerostunt', 'Cikarang Pusat', 'Pusat', 'Jl. Industri Raya No. 1, Cikarang Pusat, Bekasi', 'Admin Pusat', '2026-01-05 08:00:00', 0),
(2, 'Posyandu Melati', 'Cikarang Utara', 'Posyandu', 'Jl. Melati Indah No. 5, Cikarang Utara, Bekasi', 'Kader Melati', '2026-01-05 08:00:00', 0),
(3, 'Posyandu Mawar', 'Cikarang Selatan', 'Posyandu', 'Jl. Mawar Merah No. 9, Cikarang Selatan, Bekasi', 'Kader Mawar', '2026-01-05 08:00:00', 0),
(4, 'Posyandu Anggrek', 'Cikarang Barat', 'Posyandu', 'Jl. Anggrek Ungu No. 12, Cikarang Barat, Bekasi', 'Kader Anggrek', '2026-01-05 08:00:00', 0),
(5, 'Posyandu Dahlia', 'Cikarang Timur', 'Posyandu', 'Jl. Dahlia Putih No. 3, Cikarang Timur, Bekasi', 'Kader Dahlia', '2026-01-05 08:00:00', 0);

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
(9, '3275000127049500', 'Siti Nurhaliza', '081234500000', 'Jl. Kenanga No. 15, RT 01/RW 06, Cikarang', 2, 0, '2026-01-05 08:00:00', NULL),
(10, '3275000212049900', 'Rina Marlina', '081234510001', 'Jl. Cempaka No. 14, RT 08/RW 07, Cikarang', 3, 0, '2026-01-05 08:00:00', NULL),
(11, '3275000324039400', 'Dewi Ramadhani', '081234520002', 'Jl. Melati No. 16, RT 09/RW 09, Cikarang', 4, 0, '2026-01-05 08:00:00', NULL),
(12, '3275000428079900', 'Ani Puspita', '081234530003', 'Jl. Flamboyan No. 24, RT 04/RW 03, Cikarang', 5, 0, '2026-01-05 08:00:00', NULL),
(13, '3275000512019100', 'Sri Nuraini', '081234540004', 'Jl. Melati No. 11, RT 07/RW 02, Cikarang', 2, 0, '2026-01-05 08:00:00', NULL),
(14, '3275000624099400', 'Wati Handayani', '081234550005', 'Jl. Seroja No. 1, RT 02/RW 09, Cikarang', 3, 0, '2026-01-05 08:00:00', NULL),
(15, '3275000720029400', 'Yuni Utami', '081234560006', 'Jl. Flamboyan No. 11, RT 08/RW 01, Cikarang', 4, 0, '2026-01-05 08:00:00', NULL),
(16, '3275000818099200', 'Fitri Oktaviani', '081234570007', 'Jl. Seroja No. 7, RT 05/RW 09, Cikarang', 5, 0, '2026-01-05 08:00:00', NULL),
(17, '3275000914069200', 'Indah Setiawati', '081234580008', 'Jl. Seroja No. 34, RT 01/RW 06, Cikarang', 2, 0, '2026-01-05 08:00:00', NULL),
(18, '3275001013069400', 'Nur Pratiwi', '081234590009', 'Jl. Anggrek No. 4, RT 04/RW 02, Cikarang', 3, 1, '2026-01-05 08:00:00', NULL),
(19, '3275001125029800', 'Maya Anggraini', '081234600010', 'Jl. Melati No. 9, RT 08/RW 09, Cikarang', 4, 0, '2026-01-05 08:00:00', NULL),
(20, '3275001226109600', 'Lestari Permata', '081234610011', 'Jl. Anggrek No. 35, RT 04/RW 05, Cikarang', 5, 0, '2026-01-05 08:00:00', NULL),
(21, '3275001321089800', 'Puji Yulianti', '081234620012', 'Jl. Teratai No. 8, RT 04/RW 04, Cikarang', 2, 1, '2026-01-05 08:00:00', NULL),
(22, '3275001410109800', 'Ratna Susanti', '081234630013', 'Jl. Anggrek No. 38, RT 04/RW 01, Cikarang', 3, 1, '2026-01-05 08:00:00', NULL),
(23, '3275001511049100', 'Endang Kartika', '081234640014', 'Jl. Kenanga No. 22, RT 02/RW 09, Cikarang', 4, 0, '2026-01-05 08:00:00', NULL),
(24, '3275001625049800', 'Titin Widyawati', '081234650015', 'Jl. Melati No. 37, RT 08/RW 04, Cikarang', 5, 0, '2026-01-05 08:00:00', NULL),
(25, '3275001723049100', 'Yanti Lestari', '081234660016', 'Jl. Mawar No. 28, RT 06/RW 07, Cikarang', 2, 0, '2026-01-05 08:00:00', NULL),
(26, '3275001811119100', 'Sari Damayanti', '081234670017', 'Jl. Kenanga No. 26, RT 06/RW 02, Cikarang', 3, 0, '2026-01-05 08:00:00', NULL),
(27, '3275001916099700', 'Ika Safitri', '081234680018', 'Jl. Melati No. 28, RT 03/RW 05, Cikarang', 4, 0, '2026-01-05 08:00:00', NULL),
(28, '3275002012089800', 'Novi Maharani', '081234690019', 'Jl. Mawar No. 4, RT 09/RW 01, Cikarang', 5, 0, '2026-01-05 08:00:00', NULL),
(29, '3275002117039600', 'Rahma Wulandari', '081234700020', 'Jl. Teratai No. 31, RT 04/RW 07, Cikarang', 2, 0, '2026-01-05 08:00:00', NULL),
(30, '3275002215079000', 'Wulan Rahmawati', '081234710021', 'Jl. Flamboyan No. 17, RT 08/RW 05, Cikarang', 3, 0, '2026-01-05 08:00:00', NULL),
(31, '3275002327119700', 'Dian Ramadhani', '081234720022', 'Jl. Melati No. 13, RT 05/RW 04, Cikarang', 4, 0, '2026-01-05 08:00:00', NULL),
(32, '3275002428129800', 'Eka Puspita', '081234730023', 'Jl. Kenanga No. 21, RT 01/RW 01, Cikarang', 5, 0, '2026-01-05 08:00:00', NULL),
(33, '3275002526099200', 'Tri Nuraini', '081234740024', 'Jl. Kenanga No. 33, RT 02/RW 03, Cikarang', 2, 1, '2026-01-05 08:00:00', NULL),
(34, '3275002612119300', 'Umi Handayani', '081234750025', 'Jl. Flamboyan No. 8, RT 04/RW 01, Cikarang', 3, 0, '2026-01-05 08:00:00', NULL),
(35, '3275002723119900', 'Vera Utami', '081234760026', 'Jl. Tanjung No. 34, RT 06/RW 05, Cikarang', 4, 0, '2026-01-05 08:00:00', NULL),
(36, '3275002820049400', 'Wiwik Oktaviani', '081234770027', 'Jl. Flamboyan No. 9, RT 05/RW 08, Cikarang', 5, 0, '2026-01-05 08:00:00', NULL),
(37, '3275002912019700', 'Yeni Setiawati', '081234780028', 'Jl. Tanjung No. 37, RT 02/RW 02, Cikarang', 2, 0, '2026-01-05 08:00:00', NULL),
(38, '3275003026059200', 'Zahra Pratiwi', '081234790029', 'Jl. Cempaka No. 5, RT 04/RW 06, Cikarang', 3, 0, '2026-01-05 08:00:00', NULL);

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
(8, 'Ikan Lele', 'Protein Hewani', 1, 'Ikan lele segar hasil budidaya kolam lokal.', '2026-01-05 08:00:00', 0),
(9, 'Ikan Tongkol', 'Protein Hewani', 1, 'Ikan tongkol segar sumber protein dan omega-3.', '2026-01-05 08:00:00', 0),
(10, 'Daging Ayam', 'Protein Hewani', 1, 'Daging ayam segar untuk kebutuhan protein keluarga.', '2026-01-05 08:00:00', 0),
(11, 'Hati Ayam', 'Protein Hewani', 1, 'Hati ayam kaya zat besi untuk pencegahan anemia.', '2026-01-05 08:00:00', 0),
(12, 'Tahu', 'Protein Nabati', 1, 'Tahu putih segar sebagai sumber protein nabati.', '2026-01-05 08:00:00', 0),
(13, 'Kangkung', 'Vitamin dan Mineral', 3, 'Sayur kangkung segar kaya serat dan zat besi.', '2026-01-05 08:00:00', 0),
(14, 'Wortel', 'Vitamin dan Mineral', 1, 'Wortel segar sumber vitamin A untuk kesehatan mata.', '2026-01-05 08:00:00', 0),
(15, 'Brokoli', 'Vitamin dan Mineral', 1, 'Brokoli segar kaya vitamin C dan serat.', '2026-01-05 08:00:00', 0),
(16, 'Pisang', 'Karbohidrat', 1, 'Pisang matang sumber energi dan kalium.', '2026-01-05 08:00:00', 0),
(17, 'Pepaya', 'Vitamin dan Mineral', 1, 'Pepaya matang kaya vitamin A dan serat pencernaan.', '2026-01-05 08:00:00', 0),
(18, 'Jagung', 'Karbohidrat', 1, 'Jagung manis sumber karbohidrat kompleks.', '2026-01-05 08:00:00', 0),
(19, 'Kentang', 'Karbohidrat', 1, 'Kentang segar sebagai alternatif sumber energi.', '2026-01-05 08:00:00', 0),
(20, 'Labu Kuning', 'Vitamin dan Mineral', 1, 'Labu kuning kaya vitamin A untuk MPASI.', '2026-01-05 08:00:00', 0),
(21, 'Alpukat', 'Lemak Baik', 1, 'Alpukat matang sumber lemak sehat untuk tumbuh kembang.', '2026-01-05 08:00:00', 0),
(22, 'Jeruk', 'Vitamin dan Mineral', 1, 'Jeruk segar sumber vitamin C untuk daya tahan tubuh.', '2026-01-05 08:00:00', 0),
(23, 'Semangka', 'Vitamin dan Mineral', 1, 'Semangka segar sumber cairan dan vitamin.', '2026-01-05 08:00:00', 0),
(24, 'Daun Singkong', 'Vitamin dan Mineral', 3, 'Daun singkong sumber zat besi dan folat.', '2026-01-05 08:00:00', 0),
(25, 'Terong', 'Vitamin dan Mineral', 1, 'Terong ungu segar kaya antioksidan.', '2026-01-05 08:00:00', 0),
(26, 'Tomat', 'Vitamin dan Mineral', 1, 'Tomat segar sumber vitamin C dan likopen.', '2026-01-05 08:00:00', 0),
(27, 'Kacang Tanah', 'Protein Nabati', 1, 'Kacang tanah untuk tambahan protein dan lemak baik.', '2026-01-05 08:00:00', 0),
(28, 'Minyak Kelapa', 'Lemak Baik', 4, 'Minyak kelapa untuk tambahan kalori sehat pada MPASI.', '2026-01-05 08:00:00', 0),
(29, 'Gula Aren', 'Karbohidrat', 1, 'Gula aren alami untuk tambahan energi makanan bayi.', '2026-01-05 08:00:00', 0),
(30, 'Madu', 'Karbohidrat', 4, 'Madu murni untuk anak di atas usia 1 tahun.', '2026-01-05 08:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `paket_gizi`
--

CREATE TABLE `paket_gizi` (
  `id_paket` int NOT NULL,
  `kode_prioritas` varchar(20) NOT NULL,
  `nama_paket` varchar(100) NOT NULL,
  `deskripsi` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `tgl_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `tgl_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `paket_gizi`
--

INSERT INTO `paket_gizi` (`id_paket`, `kode_prioritas`, `nama_paket`, `deskripsi`, `is_active`, `tgl_created`, `tgl_updated`) VALUES
(1, 'PRIORITAS_1', 'Paket Prioritas 1', 'Paket untuk anak berisiko tinggi dan membutuhkan intervensi gizi intensif.', 1, '2026-07-02 06:22:01', '2026-07-02 06:22:01'),
(2, 'PRIORITAS_2', 'Paket Prioritas 2', 'Paket untuk anak yang perlu pemantauan dan dukungan gizi sedang.', 1, '2026-07-02 06:22:01', '2026-07-02 06:22:01'),
(3, 'PRIORITAS_3', 'Paket Prioritas 3', 'Paket pemenuhan rutin untuk menjaga status gizi tetap baik.', 1, '2026-07-02 06:22:01', '2026-07-02 06:22:01');

-- --------------------------------------------------------

--
-- Table structure for table `paket_gizi_detail`
--

CREATE TABLE `paket_gizi_detail` (
  `id_paket_detail` int NOT NULL,
  `id_paket` int NOT NULL,
  `id_komoditas` int NOT NULL,
  `jumlah` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tgl_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `petani_lahan_komoditas`
--

CREATE TABLE `petani_lahan_komoditas` (
  `id_petani_komoditas` int NOT NULL,
  `id_petani` int NOT NULL,
  `id_komoditas` int NOT NULL,
  `luas_area` decimal(10,2) DEFAULT NULL,
  `satuan_luas` enum('m2','ha') NOT NULL DEFAULT 'ha',
  `estimasi_panen` decimal(12,2) NOT NULL DEFAULT '0.00',
  `catatan` text,
  `tgl_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `tgl_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `petani_lahan_komoditas`
--

INSERT INTO `petani_lahan_komoditas` (`id_petani_komoditas`, `id_petani`, `id_komoditas`, `luas_area`, `satuan_luas`, `estimasi_panen`, `catatan`, `tgl_created`, `tgl_updated`, `deleted_at`) VALUES
(1, 7, 1, '1.19', 'ha', '214.49', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(2, 7, 3, '1.38', 'ha', '191.45', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(3, 7, 5, '1.03', 'ha', '122.52', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(4, 7, 8, '1.62', 'ha', '301.70', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(5, 7, 9, '0.62', 'ha', '86.28', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(6, 7, 13, '0.79', 'ha', '87.01', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(7, 7, 14, '1.47', 'ha', '138.63', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(8, 7, 16, '0.93', 'ha', '121.14', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(9, 7, 17, '0.88', 'ha', '102.99', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(10, 7, 22, '0.46', 'ha', '66.39', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(11, 7, 23, '0.57', 'ha', '119.20', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(12, 8, 2, '0.39', 'ha', '47.21', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(13, 8, 4, '1.05', 'ha', '214.16', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(14, 8, 6, '0.73', 'ha', '78.05', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(15, 8, 7, '0.38', 'ha', '65.58', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(16, 8, 10, '1.23', 'ha', '268.06', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(17, 8, 11, '1.33', 'ha', '267.74', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(18, 8, 12, '0.76', 'ha', '109.04', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(19, 8, 18, '1.30', 'ha', '133.60', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(20, 8, 19, '0.73', 'ha', '126.89', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(21, 8, 27, '1.14', 'ha', '200.29', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(22, 8, 28, '0.39', 'ha', '65.87', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(23, 8, 29, '0.94', 'ha', '107.42', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(24, 8, 30, '0.85', 'ha', '100.12', 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `petani_lokal`
--

CREATE TABLE `petani_lokal` (
  `id_petani` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `nama_lahan` varchar(100) NOT NULL,
  `alamat_lahan` text,
  `luas_lahan` decimal(10,2) DEFAULT NULL,
  `satuan_luas` enum('m2','ha') NOT NULL DEFAULT 'ha',
  `jenis_usaha` varchar(100) DEFAULT NULL,
  `status_lahan` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  `no_rekening` varchar(30) DEFAULT NULL,
  `kapasitas_panen_bulan` decimal(10,2) DEFAULT NULL,
  `deskripsi_lahan` text,
  `tgl_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `tgl_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `petani_lokal`
--

INSERT INTO `petani_lokal` (`id_petani`, `id_user`, `nama_lahan`, `alamat_lahan`, `luas_lahan`, `satuan_luas`, `jenis_usaha`, `status_lahan`, `no_rekening`, `kapasitas_panen_bulan`, `deskripsi_lahan`, `tgl_created`, `tgl_updated`, `deleted_at`) VALUES
(7, 7, 'Lahan Tani Jaya', 'Jl. Sawah Jaya No. 21, Cikarang Pusat, Bekasi', '4.20', 'ha', 'Perikanan dan sayuran', 'Aktif', '1122334455', '520.00', 'Mitra penyedia ikan, sayuran hijau, dan umbi-umbian.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(8, 8, 'Kebun Sumber Subur', 'Jl. Tani Subur No. 30, Cikarang Timur, Bekasi', '3.10', 'ha', 'Peternakan dan palawija', 'Aktif', '5544332211', '410.00', 'Mitra penyedia unggas, telur, tempe/tahu, dan kacang-kacangan.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL);

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
(1, 'Kilogram', 'Kg', '2026-07-02 06:22:01', 0),
(2, 'Butir', 'Butir', '2026-07-02 06:22:01', 0),
(3, 'Ikat', 'Ikat', '2026-07-02 06:22:01', 0),
(4, 'Liter', 'Liter', '2026-07-02 06:22:01', 0);

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
(1, 1, 18, '681.48', '0.00', '2026-07-13 01:16:16'),
(2, 1, 29, '853.97', '0.00', '2026-07-13 01:16:16'),
(3, 1, 12, '1371.89', '237.38', '2026-07-13 01:16:16'),
(4, 1, 28, '288.06', '122.19', '2026-07-13 01:16:16'),
(5, 1, 4, '820.00', '720.00', '2026-07-13 01:16:16'),
(8, 1, 22, '460.03', '0.00', '2026-07-13 01:16:16'),
(9, 1, 13, '768.66', '154.58', '2026-07-13 01:16:16'),
(10, 1, 3, '962.25', '862.25', '2026-07-13 01:16:16'),
(11, 1, 16, '163.25', '54.07', '2026-07-13 01:16:16'),
(15, 1, 9, '464.48', '182.73', '2026-07-13 01:16:16'),
(16, 1, 14, '375.70', '146.36', '2026-07-13 01:16:16'),
(17, 1, 1, '820.00', '720.00', '2026-07-13 01:16:16'),
(22, 1, 8, '664.17', '264.33', '2026-07-13 01:16:16'),
(23, 1, 5, '995.25', '895.25', '2026-07-13 01:16:16'),
(25, 1, 10, '474.15', '0.00', '2026-07-13 01:16:16'),
(26, 1, 2, '780.15', '720.00', '2026-07-13 01:16:16'),
(27, 1, 6, '779.78', '720.00', '2026-07-13 01:16:16'),
(32, 1, 11, '335.11', '169.19', '2026-07-13 01:16:16'),
(33, 1, 27, '516.05', '0.00', '2026-07-13 01:16:16'),
(36, 1, 17, '229.76', '0.00', '2026-07-13 01:16:16'),
(40, 1, 30, '494.29', '159.31', '2026-07-13 01:16:16'),
(43, 1, 23, '267.79', '0.00', '2026-07-13 01:16:16'),
(48, 4, 4, '180.00', '1.00', '2026-07-13 01:16:16'),
(49, 4, 6, '180.00', '5.00', '2026-07-13 01:16:16'),
(50, 4, 5, '180.00', '28.00', '2026-07-13 01:16:16'),
(51, 4, 11, '80.68', '0.00', '2026-07-13 01:16:16'),
(56, 5, 3, '180.00', '8.00', '2026-07-13 01:16:16'),
(57, 5, 6, '180.00', '2.00', '2026-07-13 01:16:16'),
(58, 5, 2, '180.00', '44.00', '2026-07-13 01:16:16'),
(59, 5, 8, '186.09', '0.00', '2026-07-13 01:16:16'),
(64, 4, 3, '302.07', '11.00', '2026-07-13 01:16:16'),
(65, 4, 1, '180.00', '6.50', '2026-07-13 01:16:16'),
(66, 4, 12, '237.38', '0.00', '2026-07-13 01:16:16'),
(68, 5, 5, '180.00', '20.00', '2026-07-13 01:16:16'),
(69, 5, 30, '72.30', '0.00', '2026-07-13 01:16:16'),
(72, 3, 6, '180.00', '1.00', '2026-07-13 01:16:16'),
(73, 3, 5, '355.25', '7.00', '2026-07-13 01:16:16'),
(74, 3, 3, '200.18', '3.00', '2026-07-13 01:16:16'),
(75, 3, 9, '94.95', '0.00', '2026-07-13 01:16:16'),
(80, 4, 2, '180.00', '66.00', '2026-07-13 01:16:16'),
(81, 4, 9, '87.78', '0.00', '2026-07-13 01:16:16'),
(84, 2, 4, '180.00', '2.00', '2026-07-13 01:16:16'),
(85, 2, 2, '180.00', '36.00', '2026-07-13 01:16:16'),
(86, 2, 3, '180.00', '7.00', '2026-07-13 01:16:16'),
(87, 2, 28, '122.19', '0.00', '2026-07-13 01:16:16'),
(92, 2, 6, '180.00', '1.00', '2026-07-13 01:16:16'),
(93, 2, 1, '180.00', '4.00', '2026-07-13 01:16:16'),
(94, 2, 16, '54.07', '0.00', '2026-07-13 01:16:16'),
(96, 2, 5, '180.00', '17.00', '2026-07-13 01:16:16'),
(97, 2, 14, '146.36', '0.00', '2026-07-13 01:16:16'),
(102, 4, 8, '78.24', '0.00', '2026-07-13 01:16:16'),
(106, 3, 1, '180.00', '1.00', '2026-07-13 01:16:16'),
(107, 3, 30, '87.01', '0.00', '2026-07-13 01:16:16'),
(110, 5, 1, '180.00', '5.00', '2026-07-13 01:16:16'),
(111, 5, 13, '154.58', '0.00', '2026-07-13 01:16:16'),
(114, 3, 4, '180.00', '0.00', '2026-07-13 01:16:16'),
(115, 3, 11, '88.51', '0.00', '2026-07-13 01:16:16'),
(120, 3, 2, '180.00', '16.00', '2026-07-13 01:16:16'),
(126, 5, 4, '180.00', '2.00', '2026-07-13 01:16:16');

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
(1, 'DIST-20260611-001', 1, 2, '2026-06-11', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-06-11 07:30:00', '2026-06-11 07:30:00', NULL),
(2, 'DIST-20260623-002', 1, 2, '2026-06-23', 'Dibatalkan', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-06-23 07:30:00', '2026-06-23 07:30:00', NULL),
(3, 'DIST-20260309-003', 1, 5, '2026-03-09', 'Diterima', 'Distribusi rutin kebutuhan pangan posyandu.', 1, 6, '2026-03-09 10:00:00', '2026-03-09 07:30:00', '2026-07-13 01:16:16', NULL),
(4, 'DIST-20260531-004', 1, 3, '2026-05-31', 'Diterima', 'Distribusi rutin kebutuhan pangan posyandu.', 1, 4, '2026-05-31 10:00:00', '2026-05-31 07:30:00', '2026-07-13 01:16:16', NULL),
(5, 'DIST-20260505-005', 1, 2, '2026-05-05', 'Diterima', 'Distribusi rutin kebutuhan pangan posyandu.', 2, 3, '2026-05-05 10:00:00', '2026-05-05 07:30:00', '2026-07-13 01:16:16', NULL),
(6, 'DIST-20260419-006', 1, 2, '2026-04-19', 'Dibatalkan', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-04-19 07:30:00', '2026-04-19 07:30:00', NULL),
(7, 'DIST-20260318-007', 1, 3, '2026-03-18', 'Diterima', 'Distribusi rutin kebutuhan pangan posyandu.', 2, 4, '2026-03-18 10:00:00', '2026-03-18 07:30:00', '2026-07-13 01:16:16', NULL),
(8, 'DIST-20260614-008', 1, 5, '2026-06-14', 'Diterima', 'Distribusi rutin kebutuhan pangan posyandu.', 2, 6, '2026-06-14 10:00:00', '2026-06-14 07:30:00', '2026-07-13 01:16:16', NULL),
(9, 'DIST-20260312-009', 1, 4, '2026-03-12', 'Diterima', 'Distribusi rutin kebutuhan pangan posyandu.', 1, 5, '2026-03-12 10:00:00', '2026-03-12 07:30:00', '2026-07-13 01:16:16', NULL),
(10, 'DIST-20260504-010', 1, 2, '2026-05-04', 'Diterima', 'Distribusi rutin kebutuhan pangan posyandu.', 1, 3, '2026-05-04 10:00:00', '2026-05-04 07:30:00', '2026-07-13 01:16:16', NULL),
(11, 'DIST-20260510-011', 1, 4, '2026-05-10', 'Diterima', 'Distribusi rutin kebutuhan pangan posyandu.', 2, 5, '2026-05-10 10:00:00', '2026-05-10 07:30:00', '2026-07-13 01:16:16', NULL),
(12, 'DIST-20260428-012', 1, 5, '2026-04-28', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 2, NULL, NULL, '2026-04-28 07:30:00', '2026-04-28 07:30:00', NULL),
(13, 'DIST-20260424-013', 1, 4, '2026-04-24', 'Diterima', 'Distribusi rutin kebutuhan pangan posyandu.', 1, 5, '2026-04-24 10:00:00', '2026-04-24 07:30:00', '2026-07-13 01:16:16', NULL),
(14, 'DIST-20260624-014', 1, 5, '2026-06-24', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 2, NULL, NULL, '2026-06-24 07:30:00', '2026-06-24 07:30:00', NULL),
(15, 'DIST-20260520-015', 1, 4, '2026-05-20', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-05-20 07:30:00', '2026-05-20 07:30:00', NULL),
(16, 'DIST-20260226-016', 1, 4, '2026-02-26', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-02-26 07:30:00', '2026-02-26 07:30:00', NULL),
(17, 'DIST-20260506-017', 2, 3, '2026-05-06', 'Dibatalkan', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-05-06 07:30:00', '2026-05-06 07:30:00', NULL),
(18, 'DIST-20260315-018', 1, 5, '2026-03-15', 'Diterima', 'Distribusi rutin kebutuhan pangan posyandu.', 2, 6, '2026-03-15 10:00:00', '2026-03-15 07:30:00', '2026-07-13 01:16:16', NULL),
(19, 'DIST-20260426-019', 1, 2, '2026-04-26', 'Diterima', 'Distribusi rutin kebutuhan pangan posyandu.', 2, 3, '2026-04-26 10:00:00', '2026-04-26 07:30:00', '2026-07-13 01:16:16', NULL),
(20, 'DIST-20260331-020', 2, 5, '2026-03-31', 'Dibatalkan', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-03-31 07:30:00', '2026-03-31 07:30:00', NULL),
(21, 'DIST-20260323-021', 1, 5, '2026-03-23', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 2, NULL, NULL, '2026-03-23 07:30:00', '2026-03-23 07:30:00', NULL),
(22, 'DIST-20260622-022', 2, 2, '2026-06-22', 'Dibatalkan', 'Distribusi rutin kebutuhan pangan posyandu.', 2, NULL, NULL, '2026-06-22 07:30:00', '2026-06-22 07:30:00', NULL),
(23, 'DIST-20260601-023', 1, 3, '2026-06-01', 'Dibatalkan', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-06-01 07:30:00', '2026-06-01 07:30:00', NULL),
(24, 'DIST-20260527-024', 1, 2, '2026-05-27', 'Diterima', 'Distribusi rutin kebutuhan pangan posyandu.', 1, 3, '2026-05-27 10:00:00', '2026-05-27 07:30:00', '2026-07-13 01:16:16', NULL),
(25, 'DIST-20260625-025', 1, 3, '2026-06-25', 'Diterima', 'Distribusi rutin kebutuhan pangan posyandu.', 1, 4, '2026-06-25 10:00:00', '2026-06-25 07:30:00', '2026-07-13 01:16:16', NULL),
(26, 'DIST-20260303-026', 1, 3, '2026-03-03', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 2, NULL, NULL, '2026-03-03 07:30:00', '2026-03-03 07:30:00', NULL),
(27, 'DIST-20260520-027', 1, 2, '2026-05-20', 'Dibatalkan', 'Distribusi rutin kebutuhan pangan posyandu.', 2, NULL, NULL, '2026-05-20 07:30:00', '2026-05-20 07:30:00', NULL),
(28, 'DIST-20260214-028', 1, 4, '2026-02-14', 'Diterima', 'Distribusi rutin kebutuhan pangan posyandu.', 1, 5, '2026-02-14 10:00:00', '2026-02-14 07:30:00', '2026-07-13 01:16:16', NULL),
(29, 'DIST-20260430-029', 1, 4, '2026-04-30', 'Dibatalkan', 'Distribusi rutin kebutuhan pangan posyandu.', 2, NULL, NULL, '2026-04-30 07:30:00', '2026-04-30 07:30:00', NULL),
(30, 'DIST-20260505-030', 1, 2, '2026-05-05', 'Diterima', 'Distribusi rutin kebutuhan pangan posyandu.', 1, 3, '2026-05-05 10:00:00', '2026-05-05 07:30:00', '2026-07-13 01:16:16', NULL),
(31, 'DIST-20260628-031', 1, 2, '2026-06-28', 'Diterima', 'Distribusi rutin kebutuhan pangan posyandu.', 1, 3, '2026-06-28 10:00:00', '2026-06-28 07:30:00', '2026-07-13 01:16:16', NULL),
(32, 'DIST-20260628-032', 1, 3, '2026-06-28', 'Diterima', 'Distribusi rutin kebutuhan pangan posyandu.', 1, 4, '2026-06-28 10:00:00', '2026-06-28 07:30:00', '2026-07-13 01:16:16', NULL),
(33, 'DIST-20260628-033', 1, 4, '2026-06-28', 'Diterima', 'Distribusi rutin kebutuhan pangan posyandu.', 1, 5, '2026-06-28 10:00:00', '2026-06-28 07:30:00', '2026-07-13 01:16:16', NULL),
(34, 'DIST-20260628-034', 1, 5, '2026-06-28', 'Diterima', 'Distribusi rutin kebutuhan pangan posyandu.', 1, 6, '2026-06-28 10:00:00', '2026-06-28 07:30:00', '2026-07-13 01:16:16', NULL);

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
(1, 1, 22, '4.31', '2026-07-13 01:16:16'),
(2, 1, 27, '9.05', '2026-07-13 01:16:16'),
(3, 2, 2, '14.98', '2026-07-13 01:16:16'),
(4, 3, 3, '143.79', '2026-07-13 01:16:16'),
(5, 3, 6, '88.47', '2026-07-13 01:16:16'),
(6, 3, 2, '108.90', '2026-07-13 01:16:16'),
(7, 3, 8, '186.09', '2026-07-13 01:16:16'),
(8, 4, 1, '82.73', '2026-07-13 01:16:16'),
(9, 4, 5, '215.28', '2026-07-13 01:16:16'),
(10, 4, 6, '48.55', '2026-07-13 01:16:16'),
(11, 4, 30, '87.01', '2026-07-13 01:16:16'),
(12, 5, 2, '39.92', '2026-07-13 01:16:16'),
(13, 5, 1, '54.98', '2026-07-13 01:16:16'),
(14, 5, 5, '98.58', '2026-07-13 01:16:16'),
(15, 5, 14, '88.60', '2026-07-13 01:16:16'),
(16, 6, 23, '6.71', '2026-07-13 01:16:16'),
(17, 6, 25, '7.08', '2026-07-13 01:16:16'),
(18, 7, 6, '30.43', '2026-07-13 01:16:16'),
(19, 7, 5, '111.16', '2026-07-13 01:16:16'),
(20, 7, 3, '200.18', '2026-07-13 01:16:16'),
(21, 7, 9, '94.95', '2026-07-13 01:16:16'),
(22, 8, 6, '28.86', '2026-07-13 01:16:16'),
(23, 8, 1, '40.81', '2026-07-13 01:16:16'),
(24, 8, 5, '52.38', '2026-07-13 01:16:16'),
(25, 8, 13, '154.58', '2026-07-13 01:16:16'),
(26, 9, 3, '133.05', '2026-07-13 01:16:16'),
(27, 9, 5, '70.88', '2026-07-13 01:16:16'),
(28, 9, 1, '40.05', '2026-07-13 01:16:16'),
(29, 9, 12, '237.38', '2026-07-13 01:16:16'),
(30, 10, 6, '27.32', '2026-07-13 01:16:16'),
(31, 10, 2, '32.99', '2026-07-13 01:16:16'),
(32, 10, 1, '33.12', '2026-07-13 01:16:16'),
(33, 10, 16, '54.07', '2026-07-13 01:16:16'),
(34, 11, 4, '113.00', '2026-07-13 01:16:16'),
(35, 11, 3, '101.82', '2026-07-13 01:16:16'),
(36, 11, 1, '17.71', '2026-07-13 01:16:16'),
(37, 11, 8, '78.24', '2026-07-13 01:16:16'),
(38, 12, 2, '3.89', '2026-07-13 01:16:16'),
(39, 12, 13, '8.54', '2026-07-13 01:16:16'),
(40, 12, 22, '3.45', '2026-07-13 01:16:16'),
(41, 13, 3, '67.20', '2026-07-13 01:16:16'),
(42, 13, 6, '14.96', '2026-07-13 01:16:16'),
(43, 13, 2, '31.51', '2026-07-13 01:16:16'),
(44, 13, 9, '87.78', '2026-07-13 01:16:16'),
(45, 14, 13, '7.47', '2026-07-13 01:16:16'),
(46, 15, 30, '3.63', '2026-07-13 01:16:16'),
(47, 15, 28, '10.57', '2026-07-13 01:16:16'),
(48, 16, 30, '6.02', '2026-07-13 01:16:16'),
(49, 16, 14, '13.22', '2026-07-13 01:16:16'),
(50, 16, 11, '14.57', '2026-07-13 01:16:16'),
(51, 17, 30, '7.20', '2026-07-13 01:16:16'),
(52, 18, 6, '22.45', '2026-07-13 01:16:16'),
(53, 18, 2, '31.25', '2026-07-13 01:16:16'),
(54, 18, 5, '33.58', '2026-07-13 01:16:16'),
(55, 18, 30, '72.30', '2026-07-13 01:16:16'),
(56, 19, 4, '71.21', '2026-07-13 01:16:16'),
(57, 19, 2, '29.19', '2026-07-13 01:16:16'),
(58, 19, 3, '39.48', '2026-07-13 01:16:16'),
(59, 19, 28, '56.99', '2026-07-13 01:16:16'),
(60, 20, 15, '6.38', '2026-07-13 01:16:16'),
(61, 20, 25, '7.58', '2026-07-13 01:16:16'),
(62, 21, 5, '10.69', '2026-07-13 01:16:16'),
(63, 21, 10, '13.49', '2026-07-13 01:16:16'),
(64, 21, 2, '9.65', '2026-07-13 01:16:16'),
(65, 22, 3, '4.92', '2026-07-13 01:16:16'),
(66, 22, 25, '8.39', '2026-07-13 01:16:16'),
(67, 23, 27, '8.41', '2026-07-13 01:16:16'),
(68, 24, 6, '10.47', '2026-07-13 01:16:16'),
(69, 24, 1, '12.17', '2026-07-13 01:16:16'),
(70, 24, 4, '27.50', '2026-07-13 01:16:16'),
(71, 24, 14, '57.76', '2026-07-13 01:16:16'),
(72, 25, 5, '28.81', '2026-07-13 01:16:16'),
(73, 25, 4, '20.79', '2026-07-13 01:16:16'),
(74, 25, 1, '15.09', '2026-07-13 01:16:16'),
(75, 25, 11, '88.51', '2026-07-13 01:16:16'),
(76, 26, 3, '9.53', '2026-07-13 01:16:16'),
(77, 26, 22, '12.44', '2026-07-13 01:16:16'),
(78, 27, 21, '7.64', '2026-07-13 01:16:16'),
(79, 28, 4, '18.49', '2026-07-13 01:16:16'),
(80, 28, 6, '6.32', '2026-07-13 01:16:16'),
(81, 28, 5, '33.76', '2026-07-13 01:16:16'),
(82, 28, 11, '80.68', '2026-07-13 01:16:16'),
(83, 29, 9, '12.04', '2026-07-13 01:16:16'),
(84, 29, 2, '5.29', '2026-07-13 01:16:16'),
(85, 30, 1, '13.82', '2026-07-13 01:16:16'),
(86, 30, 2, '14.13', '2026-07-13 01:16:16'),
(87, 30, 6, '4.46', '2026-07-13 01:16:16'),
(88, 30, 28, '65.20', '2026-07-13 01:16:16'),
(89, 31, 1, '65.91', '2026-07-13 01:16:16'),
(90, 31, 2, '63.77', '2026-07-13 01:16:16'),
(91, 31, 3, '140.52', '2026-07-13 01:16:16'),
(92, 31, 4, '81.29', '2026-07-13 01:16:16'),
(93, 31, 5, '81.42', '2026-07-13 01:16:16'),
(94, 31, 6, '137.75', '2026-07-13 01:16:16'),
(95, 32, 1, '82.18', '2026-07-13 01:16:16'),
(96, 32, 2, '180.00', '2026-07-13 01:16:16'),
(97, 32, 4, '159.21', '2026-07-13 01:16:16'),
(98, 32, 6, '101.02', '2026-07-13 01:16:16'),
(99, 33, 1, '122.24', '2026-07-13 01:16:16'),
(100, 33, 2, '148.49', '2026-07-13 01:16:16'),
(101, 33, 4, '48.51', '2026-07-13 01:16:16'),
(102, 33, 5, '75.36', '2026-07-13 01:16:16'),
(103, 33, 6, '158.72', '2026-07-13 01:16:16'),
(104, 34, 1, '139.19', '2026-07-13 01:16:16'),
(105, 34, 2, '39.85', '2026-07-13 01:16:16'),
(106, 34, 3, '36.21', '2026-07-13 01:16:16'),
(107, 34, 4, '180.00', '2026-07-13 01:16:16'),
(108, 34, 5, '94.04', '2026-07-13 01:16:16'),
(109, 34, 6, '40.22', '2026-07-13 01:16:16');

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
  `deleted_at` datetime DEFAULT NULL,
  `periode_pemeriksaan` char(7) GENERATED ALWAYS AS (date_format(`tanggal_pemeriksaan`,_utf8mb4'%Y-%m')) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `t_pemeriksaan`
--

INSERT INTO `t_pemeriksaan` (`id_pemeriksaan`, `id_anak`, `id_kader`, `tanggal_pemeriksaan`, `berat_badan`, `tinggi_badan`, `lingkar_kepala`, `usia_bulan`, `status_gizi`, `catatan`, `tgl_created`, `tgl_updated`, `deleted_at`) VALUES
(1, 16, 5, '2026-01-04', '4.01', '49.65', NULL, 0, 'Prioritas 3', 'Orang tua diminta rutin membawa anak ke posyandu.', '2026-01-04 09:00:00', '2026-01-04 09:00:00', NULL),
(2, 17, 5, '2026-01-06', '18.63', '105.93', NULL, 76, 'Prioritas 3', 'Kondisi gizi membaik dibanding pemeriksaan sebelumnya.', '2026-01-06 09:00:00', '2026-01-06 09:00:00', NULL),
(3, 21, 4, '2026-01-11', '11.12', '82.90', NULL, 34, 'Prioritas 1', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-01-11 09:00:00', '2026-01-11 09:00:00', NULL),
(4, 6, 4, '2026-01-18', '19.39', '111.41', NULL, 95, 'Prioritas 2', 'Berat dan tinggi perlu pemantauan intensif.', '2026-01-18 09:00:00', '2026-01-18 09:00:00', NULL),
(5, 38, 4, '2026-01-19', '5.32', '55.04', NULL, 2, 'Prioritas 3', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-01-19 09:00:00', '2026-01-19 09:00:00', NULL),
(6, 26, 3, '2026-01-23', '16.50', '104.60', NULL, 84, 'Prioritas 1', 'Berat dan tinggi perlu pemantauan intensif.', '2026-01-23 09:00:00', '2026-01-23 09:00:00', NULL),
(7, 23, 5, '2026-01-31', '20.36', '112.34', NULL, 89, 'Prioritas 3', 'Perlu dukungan pangan tambahan bulan ini.', '2026-01-31 09:00:00', '2026-01-31 09:00:00', NULL),
(8, 7, 5, '2026-02-08', '9.26', '75.71', NULL, 18, 'Prioritas 2', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-02-08 09:00:00', '2026-02-08 09:00:00', NULL),
(9, 4, 6, '2026-02-08', '17.98', '107.75', NULL, 96, 'Prioritas 1', 'Pertumbuhan dalam pemantauan rutin posyandu.', '2026-02-08 09:00:00', '2026-02-08 09:00:00', NULL),
(10, 18, 6, '2026-02-10', '14.17', '89.83', NULL, 42, 'Prioritas 2', 'Berat dan tinggi perlu pemantauan intensif.', '2026-02-10 09:00:00', '2026-02-10 09:00:00', NULL),
(11, 10, 4, '2026-02-14', '15.02', '92.00', NULL, 43, 'Prioritas 3', 'Berat dan tinggi perlu pemantauan intensif.', '2026-02-14 09:00:00', '2026-02-14 09:00:00', NULL),
(12, 35, 5, '2026-02-15', '5.02', '52.76', NULL, 3, 'Prioritas 1', 'Orang tua diminta rutin membawa anak ke posyandu.', '2026-02-15 09:00:00', '2026-02-15 09:00:00', NULL),
(13, 17, 5, '2026-02-15', '18.74', '106.83', NULL, 77, 'Prioritas 3', 'Perlu dukungan pangan tambahan bulan ini.', '2026-02-15 09:00:00', '2026-02-15 09:00:00', NULL),
(14, 27, 3, '2026-02-19', '5.55', '54.76', NULL, 3, 'Prioritas 3', 'Perlu dukungan pangan tambahan bulan ini.', '2026-02-19 09:00:00', '2026-02-19 09:00:00', NULL),
(15, 2, 4, '2026-02-26', '11.19', '81.25', NULL, 29, 'Prioritas 2', 'Orang tua diminta rutin membawa anak ke posyandu.', '2026-02-26 09:00:00', '2026-02-26 09:00:00', NULL),
(16, 20, 3, '2026-02-27', '15.67', '91.76', NULL, 45, 'Prioritas 3', 'Pertumbuhan dalam pemantauan rutin posyandu.', '2026-02-27 09:00:00', '2026-02-27 09:00:00', NULL),
(17, 3, 5, '2026-03-04', '9.18', '69.55', NULL, 10, 'Prioritas 3', 'Pertumbuhan dalam pemantauan rutin posyandu.', '2026-03-04 09:00:00', '2026-03-04 09:00:00', NULL),
(18, 31, 6, '2026-03-06', '21.76', '118.50', NULL, 113, 'Prioritas 2', 'Berat dan tinggi perlu pemantauan intensif.', '2026-03-06 09:00:00', '2026-03-06 09:00:00', NULL),
(19, 8, 6, '2026-03-14', '3.25', '47.83', NULL, 0, 'Prioritas 1', 'Orang tua diminta rutin membawa anak ke posyandu.', '2026-03-14 09:00:00', '2026-03-14 09:00:00', NULL),
(20, 32, 3, '2026-03-14', '5.64', '57.17', NULL, 4, 'Prioritas 3', 'Perlu dukungan pangan tambahan bulan ini.', '2026-03-14 09:00:00', '2026-03-14 09:00:00', NULL),
(21, 19, 3, '2026-03-18', '17.30', '105.78', NULL, 91, 'Prioritas 1', 'Pertumbuhan dalam pemantauan rutin posyandu.', '2026-03-18 09:00:00', '2026-03-18 09:00:00', NULL),
(22, 34, 5, '2026-03-19', '14.53', '91.65', NULL, 47, 'Prioritas 2', 'Berat dan tinggi perlu pemantauan intensif.', '2026-03-19 09:00:00', '2026-03-19 09:00:00', NULL),
(23, 36, 6, '2026-03-21', '9.66', '78.49', NULL, 25, 'Prioritas 1', 'Perlu dukungan pangan tambahan bulan ini.', '2026-03-21 09:00:00', '2026-03-21 09:00:00', NULL),
(24, 13, 6, '2026-03-23', '12.07', '84.06', NULL, 28, 'Prioritas 3', 'Kondisi gizi membaik dibanding pemeriksaan sebelumnya.', '2026-03-23 09:00:00', '2026-03-23 09:00:00', NULL),
(25, 26, 3, '2026-03-26', '17.01', '104.50', NULL, 86, 'Prioritas 1', 'Perlu dukungan pangan tambahan bulan ini.', '2026-03-26 09:00:00', '2026-03-26 09:00:00', NULL),
(26, 28, 4, '2026-03-26', '13.33', '88.09', NULL, 39, 'Prioritas 2', 'Kondisi gizi membaik dibanding pemeriksaan sebelumnya.', '2026-03-26 09:00:00', '2026-03-26 09:00:00', NULL),
(27, 12, 5, '2026-03-29', '12.44', '84.86', NULL, 35, 'Prioritas 2', 'Berat dan tinggi perlu pemantauan intensif.', '2026-03-29 09:00:00', '2026-03-29 09:00:00', NULL),
(28, 37, 3, '2026-03-30', '21.25', '117.42', NULL, 121, 'Prioritas 1', 'Pertumbuhan dalam pemantauan rutin posyandu.', '2026-03-30 09:00:00', '2026-03-30 09:00:00', NULL),
(29, 10, 4, '2026-03-30', '15.34', '92.91', NULL, 44, 'Prioritas 3', 'Perlu dukungan pangan tambahan bulan ini.', '2026-03-30 09:00:00', '2026-03-30 09:00:00', NULL),
(30, 31, 6, '2026-04-01', '22.19', '117.34', NULL, 114, 'Prioritas 2', 'Pertumbuhan dalam pemantauan rutin posyandu.', '2026-04-01 09:00:00', '2026-04-01 09:00:00', NULL),
(31, 18, 6, '2026-04-02', '13.94', '90.60', NULL, 44, 'Prioritas 2', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-04-02 09:00:00', '2026-04-02 09:00:00', NULL),
(32, 28, 4, '2026-04-03', '13.39', '87.74', NULL, 40, 'Prioritas 2', 'Berat dan tinggi perlu pemantauan intensif.', '2026-04-03 09:00:00', '2026-04-03 09:00:00', NULL),
(33, 5, 3, '2026-04-05', '13.03', '85.05', NULL, 31, 'Prioritas 3', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-04-05 09:00:00', '2026-04-05 09:00:00', NULL),
(34, 19, 3, '2026-04-06', '18.10', '106.66', NULL, 92, 'Prioritas 1', 'Kondisi gizi membaik dibanding pemeriksaan sebelumnya.', '2026-04-06 09:00:00', '2026-04-06 09:00:00', NULL),
(35, 11, 4, '2026-04-10', '21.54', '116.24', NULL, 111, 'Prioritas 2', 'Perlu dukungan pangan tambahan bulan ini.', '2026-04-10 09:00:00', '2026-04-10 09:00:00', NULL),
(36, 13, 6, '2026-04-16', '12.33', '83.72', NULL, 29, 'Prioritas 3', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-04-16 09:00:00', '2026-04-16 09:00:00', NULL),
(37, 23, 5, '2026-04-16', '20.63', '112.32', NULL, 92, 'Prioritas 3', 'Berat dan tinggi perlu pemantauan intensif.', '2026-04-16 09:00:00', '2026-04-16 09:00:00', NULL),
(38, 21, 4, '2026-04-16', '12.28', '83.88', NULL, 37, 'Prioritas 1', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-04-16 09:00:00', '2026-04-16 09:00:00', NULL),
(39, 15, 4, '2026-04-18', '12.15', '85.81', NULL, 34, 'Prioritas 2', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-04-18 09:00:00', '2026-04-18 09:00:00', NULL),
(40, 35, 5, '2026-04-26', '5.77', '57.26', NULL, 5, 'Prioritas 1', 'Perlu dukungan pangan tambahan bulan ini.', '2026-04-26 09:00:00', '2026-04-26 09:00:00', NULL),
(41, 4, 6, '2026-04-28', '18.51', '110.70', NULL, 98, 'Prioritas 1', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-04-28 09:00:00', '2026-04-28 09:00:00', NULL),
(42, 22, 4, '2026-04-28', '14.30', '89.37', NULL, 39, 'Prioritas 3', 'Perlu dukungan pangan tambahan bulan ini.', '2026-04-28 09:00:00', '2026-04-28 09:00:00', NULL),
(43, 30, 5, '2026-05-06', '6.21', '58.61', NULL, 5, 'Prioritas 2', 'Orang tua diminta rutin membawa anak ke posyandu.', '2026-05-06 09:00:00', '2026-05-06 09:00:00', NULL),
(44, 7, 5, '2026-05-08', '9.89', '76.94', NULL, 21, 'Prioritas 2', 'Perlu dukungan pangan tambahan bulan ini.', '2026-05-08 09:00:00', '2026-05-08 09:00:00', NULL),
(45, 35, 5, '2026-05-09', '5.90', '58.91', NULL, 6, 'Prioritas 1', 'Kondisi gizi membaik dibanding pemeriksaan sebelumnya.', '2026-05-09 09:00:00', '2026-05-09 09:00:00', NULL),
(46, 24, 6, '2026-05-16', '18.26', '109.33', NULL, 88, 'Prioritas 2', 'Orang tua diminta rutin membawa anak ke posyandu.', '2026-05-16 09:00:00', '2026-05-16 09:00:00', NULL),
(47, 38, 4, '2026-05-17', '6.83', '62.43', NULL, 6, 'Prioritas 3', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-05-17 09:00:00', '2026-05-17 09:00:00', NULL),
(48, 25, 6, '2026-05-27', '10.72', '79.16', NULL, 22, 'Prioritas 3', 'Perlu dukungan pangan tambahan bulan ini.', '2026-05-27 09:00:00', '2026-05-27 09:00:00', NULL),
(49, 33, 4, '2026-05-28', '13.50', '87.61', NULL, 45, 'Prioritas 1', 'Pertumbuhan dalam pemantauan rutin posyandu.', '2026-05-28 09:00:00', '2026-05-28 09:00:00', NULL),
(50, 13, 6, '2026-05-28', '12.45', '85.10', NULL, 30, 'Prioritas 3', 'Berat dan tinggi perlu pemantauan intensif.', '2026-05-28 09:00:00', '2026-05-28 09:00:00', NULL),
(51, 19, 3, '2026-06-01', '17.66', '109.12', NULL, 94, 'Prioritas 1', 'Orang tua diminta rutin membawa anak ke posyandu.', '2026-06-01 09:00:00', '2026-06-01 09:00:00', NULL),
(52, 29, 5, '2026-06-10', '17.01', '97.77', NULL, 52, 'Prioritas 3', 'Orang tua diminta rutin membawa anak ke posyandu.', '2026-06-10 09:00:00', '2026-06-10 09:00:00', NULL),
(53, 14, 3, '2026-06-11', '14.87', '90.85', NULL, 43, 'Prioritas 3', 'Kondisi gizi membaik dibanding pemeriksaan sebelumnya.', '2026-06-11 09:00:00', '2026-06-11 09:00:00', NULL),
(54, 32, 3, '2026-06-15', '7.64', '63.10', NULL, 7, 'Prioritas 3', 'Orang tua diminta rutin membawa anak ke posyandu.', '2026-06-15 09:00:00', '2026-06-15 09:00:00', NULL),
(55, 11, 4, '2026-06-18', '22.01', '116.92', NULL, 113, 'Prioritas 2', 'Pertumbuhan dalam pemantauan rutin posyandu.', '2026-06-18 09:00:00', '2026-06-18 09:00:00', NULL),
(56, 33, 4, '2026-06-22', '13.15', '88.28', NULL, 46, 'Prioritas 1', 'Kondisi gizi membaik dibanding pemeriksaan sebelumnya.', '2026-06-22 09:00:00', '2026-06-22 09:00:00', NULL),
(57, 15, 4, '2026-06-23', '12.68', '86.21', NULL, 36, 'Prioritas 2', 'Kondisi gizi membaik dibanding pemeriksaan sebelumnya.', '2026-06-23 09:00:00', '2026-06-23 09:00:00', NULL),
(58, 9, 3, '2026-06-24', '7.01', '62.44', NULL, 6, 'Prioritas 3', 'Orang tua diminta rutin membawa anak ke posyandu.', '2026-06-24 09:00:00', '2026-06-24 09:00:00', NULL),
(59, 1, 3, '2026-06-26', '13.21', '88.00', NULL, 41, 'Prioritas 2', 'Kondisi gizi membaik dibanding pemeriksaan sebelumnya.', '2026-06-26 09:00:00', '2026-06-26 09:00:00', NULL),
(60, 26, 3, '2026-06-30', '17.18', '105.21', NULL, 89, 'Prioritas 1', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-06-30 09:00:00', '2026-06-30 09:00:00', NULL);

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
(1, 8, 1, 'REQ-202601-001', '2026-01-26', '25482550.00', 'Lunas', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-01-26 08:30:00', '2026-07-13 01:16:16'),
(2, 7, 1, 'REQ-202601-002', '2026-01-24', '5450715.00', 'Lunas', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-01-24 08:30:00', '2026-07-13 01:16:16'),
(3, 7, 1, 'REQ-202602-003', '2026-02-23', '18118855.00', 'Lunas', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-02-23 08:30:00', '2026-07-13 01:16:16'),
(4, NULL, 1, 'REQ-202605-004', '2026-05-31', '6655320.00', 'Pending', 'Mencari Petani', 'Lowongan pengadaan terbuka, menunggu petani mitra.', '2026-05-31 08:30:00', '2026-05-31 08:30:00'),
(5, 7, 1, 'REQ-202606-005', '2026-06-02', '16655875.00', 'Lunas', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-06-02 08:30:00', '2026-07-13 01:16:16'),
(6, NULL, 1, 'REQ-202602-006', '2026-02-09', '3685400.00', 'Pending', 'Mencari Petani', 'Lowongan pengadaan terbuka, menunggu petani mitra.', '2026-02-09 08:30:00', '2026-02-09 08:30:00'),
(7, 8, 1, 'REQ-202603-007', '2026-03-31', '2281600.00', 'Pending', 'Disetujui', 'Kontrak disetujui, menunggu pelunasan pembayaran.', '2026-03-31 08:30:00', '2026-03-31 08:30:00'),
(8, 7, 1, 'REQ-202603-008', '2026-03-02', '670410.00', 'Pending', 'Disetujui', 'Kontrak disetujui, menunggu pelunasan pembayaran.', '2026-03-02 08:30:00', '2026-03-02 08:30:00'),
(9, 7, 1, 'REQ-202604-009', '2026-04-22', '2017025.00', 'Pending', 'Dibatalkan', 'Kontrak dibatalkan karena kendala pasokan.', '2026-04-22 08:30:00', '2026-04-22 08:30:00'),
(10, 8, 1, 'REQ-202605-010', '2026-05-12', '3263966.00', 'Pending', 'Disetujui', 'Kontrak disetujui, menunggu pelunasan pembayaran.', '2026-05-12 08:30:00', '2026-05-12 08:30:00'),
(11, 8, 1, 'REQ-202604-011', '2026-04-13', '27663780.00', 'Lunas', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-04-13 08:30:00', '2026-07-13 01:16:16'),
(12, 8, 1, 'REQ-202603-012', '2026-03-16', '23686820.00', 'Lunas', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-03-16 08:30:00', '2026-07-13 01:16:16'),
(13, NULL, 1, 'REQ-202601-013', '2026-01-27', '6885510.00', 'Pending', 'Mencari Petani', 'Lowongan pengadaan terbuka, menunggu petani mitra.', '2026-01-27 08:30:00', '2026-01-27 08:30:00'),
(14, 8, 1, 'REQ-202603-014', '2026-03-04', '1166480.00', 'Pending', 'Dibatalkan', 'Kontrak dibatalkan karena kendala pasokan.', '2026-03-04 08:30:00', '2026-03-04 08:30:00'),
(15, 8, 1, 'REQ-202601-015', '2026-01-25', '8257740.00', 'Lunas', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-01-25 08:30:00', '2026-07-13 01:16:16'),
(16, 7, 1, 'REQ-202605-016', '2026-05-01', '7529280.00', 'Lunas', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-05-01 08:30:00', '2026-07-13 01:16:16'),
(17, NULL, 1, 'REQ-202605-017', '2026-05-23', '4190860.00', 'Pending', 'Mencari Petani', 'Lowongan pengadaan terbuka, menunggu petani mitra.', '2026-05-23 08:30:00', '2026-05-23 08:30:00'),
(18, 7, 1, 'REQ-202604-018', '2026-04-11', '25075585.00', 'Lunas', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-04-11 08:30:00', '2026-07-13 01:16:16'),
(19, 8, 1, 'REQ-202606-019', '2026-06-06', '13318470.00', 'Lunas', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-06-06 08:30:00', '2026-07-13 01:16:16'),
(20, 8, 1, 'REQ-202605-020', '2026-05-11', '4116740.00', 'Pending', 'Dibatalkan', 'Kontrak dibatalkan karena kendala pasokan.', '2026-05-11 08:30:00', '2026-05-11 08:30:00'),
(21, NULL, 1, 'REQ-202602-021', '2026-02-11', '271310.00', 'Pending', 'Mencari Petani', 'Lowongan pengadaan terbuka, menunggu petani mitra.', '2026-02-11 08:30:00', '2026-02-11 08:30:00'),
(22, 8, 1, 'REQ-202606-022', '2026-06-07', '5461560.00', 'Pending', 'Disetujui', 'Kontrak disetujui, menunggu pelunasan pembayaran.', '2026-06-07 08:30:00', '2026-06-07 08:30:00'),
(23, 7, 1, 'REQ-202604-023', '2026-04-29', '8543155.00', 'Lunas', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-04-29 08:30:00', '2026-07-13 01:16:16'),
(24, 7, 1, 'REQ-202602-024', '2026-02-08', '675520.00', 'Pending', 'Dibatalkan', 'Kontrak dibatalkan karena kendala pasokan.', '2026-02-08 08:30:00', '2026-02-08 08:30:00'),
(25, 8, 1, 'REQ-202606-025', '2026-06-02', '41707370.00', 'Lunas', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-06-02 08:30:00', '2026-07-13 01:16:16'),
(26, 7, 1, 'REQ-202603-026', '2026-03-27', '2299520.00', 'Pending', 'Disetujui', 'Kontrak disetujui, menunggu pelunasan pembayaran.', '2026-03-27 08:30:00', '2026-03-27 08:30:00'),
(27, 8, 1, 'REQ-202604-027', '2026-04-19', '567990.00', 'Pending', 'Disetujui', 'Kontrak disetujui, menunggu pelunasan pembayaran.', '2026-04-19 08:30:00', '2026-04-19 08:30:00'),
(28, 8, 1, 'REQ-202602-028', '2026-02-15', '1552600.00', 'Pending', 'Dibatalkan', 'Kontrak dibatalkan karena kendala pasokan.', '2026-02-15 08:30:00', '2026-02-15 08:30:00'),
(29, NULL, 1, 'REQ-202606-029', '2026-06-16', '325765.00', 'Pending', 'Mencari Petani', 'Lowongan pengadaan terbuka, menunggu petani mitra.', '2026-06-16 08:30:00', '2026-06-16 08:30:00'),
(30, 7, 1, 'REQ-202602-030', '2026-02-16', '384020.00', 'Pending', 'Disetujui', 'Kontrak disetujui, menunggu pelunasan pembayaran.', '2026-02-16 08:30:00', '2026-02-16 08:30:00');

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
(1, 1, 18, '416.52', '6500.00'),
(2, 1, 29, '357.72', '32000.00'),
(3, 1, 12, '315.61', '9000.00'),
(4, 1, 28, '165.63', '28000.00'),
(5, 2, 22, '149.91', '10000.00'),
(6, 2, 13, '393.13', '5500.00'),
(7, 2, 3, '124.60', '6500.00'),
(8, 2, 16, '163.25', '6000.00'),
(9, 3, 9, '314.95', '26000.00'),
(10, 3, 13, '375.53', '5500.00'),
(11, 3, 14, '375.70', '8000.00'),
(12, 4, 28, '136.49', '28000.00'),
(13, 4, 29, '88.55', '32000.00'),
(14, 5, 3, '344.03', '6500.00'),
(15, 5, 8, '250.19', '24000.00'),
(16, 5, 9, '149.53', '26000.00'),
(17, 5, 22, '310.12', '10000.00'),
(18, 6, 9, '35.52', '26000.00'),
(19, 6, 1, '125.54', '22000.00'),
(20, 7, 12, '59.30', '9000.00'),
(21, 7, 6, '79.45', '22000.00'),
(22, 8, 3, '103.14', '6500.00'),
(23, 9, 22, '57.86', '10000.00'),
(24, 9, 5, '106.55', '13500.00'),
(25, 10, 2, '111.31', '2600.00'),
(26, 10, 18, '48.96', '6500.00'),
(27, 10, 10, '83.01', '32000.00'),
(28, 11, 12, '373.59', '9000.00'),
(29, 11, 29, '179.77', '32000.00'),
(30, 11, 10, '235.41', '32000.00'),
(31, 11, 18, '264.96', '6500.00'),
(32, 12, 11, '126.58', '18000.00'),
(33, 12, 27, '369.01', '22000.00'),
(34, 12, 10, '238.74', '32000.00'),
(35, 12, 28, '122.43', '28000.00'),
(36, 13, 30, '102.85', '55000.00'),
(37, 13, 7, '72.28', '17000.00'),
(38, 14, 28, '41.66', '28000.00'),
(39, 15, 2, '339.71', '2600.00'),
(40, 15, 27, '147.04', '22000.00'),
(41, 15, 11, '208.53', '18000.00'),
(42, 16, 5, '382.94', '13500.00'),
(43, 16, 17, '229.76', '7000.00'),
(44, 17, 9, '146.09', '26000.00'),
(45, 17, 16, '65.42', '6000.00'),
(46, 18, 3, '378.04', '6500.00'),
(47, 18, 5, '337.27', '13500.00'),
(48, 18, 8, '413.98', '24000.00'),
(49, 19, 30, '124.18', '55000.00'),
(50, 19, 12, '280.97', '9000.00'),
(51, 20, 27, '128.69', '22000.00'),
(52, 20, 12, '142.84', '9000.00'),
(53, 21, 2, '104.35', '2600.00'),
(54, 22, 7, '106.25', '17000.00'),
(55, 22, 28, '85.71', '28000.00'),
(56, 22, 4, '114.13', '11000.00'),
(57, 23, 1, '211.31', '22000.00'),
(58, 23, 23, '267.79', '4500.00'),
(59, 24, 14, '84.44', '8000.00'),
(60, 25, 29, '316.48', '32000.00'),
(61, 25, 30, '370.11', '55000.00'),
(62, 25, 6, '304.70', '22000.00'),
(63, 25, 12, '401.72', '9000.00'),
(64, 26, 13, '83.36', '5500.00'),
(65, 26, 8, '58.33', '24000.00'),
(66, 26, 16, '73.52', '6000.00'),
(67, 27, 12, '63.11', '9000.00'),
(68, 28, 28, '55.45', '28000.00'),
(69, 29, 13, '59.23', '5500.00'),
(70, 30, 17, '54.86', '7000.00'),
(71, 3, 1, '138.69', '22000.00'),
(72, 1, 4, '350.00', '11000.00'),
(73, 18, 1, '126.39', '22000.00'),
(74, 11, 2, '111.95', '2600.00'),
(75, 25, 4, '82.28', '11000.00'),
(76, 5, 5, '105.64', '13500.00'),
(77, 11, 6, '215.34', '22000.00'),
(78, 3, 1, '82.18', '22000.00'),
(79, 19, 2, '180.00', '2600.00'),
(80, 11, 4, '159.21', '11000.00'),
(81, 12, 6, '101.02', '22000.00'),
(82, 23, 1, '122.24', '22000.00'),
(83, 15, 2, '148.49', '2600.00'),
(84, 19, 6, '158.72', '22000.00'),
(85, 18, 1, '139.19', '22000.00'),
(86, 16, 3, '115.58', '6500.00'),
(87, 11, 4, '228.51', '11000.00'),
(88, 18, 5, '169.40', '13500.00');

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
  `id_paket` int DEFAULT NULL,
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

INSERT INTO `t_penyerahan` (`id_penyerahan`, `no_penyerahan`, `id_ibu`, `id_anak`, `id_gudang`, `id_paket`, `tanggal_penyerahan`, `status_penyerahan`, `catatan`, `created_by`, `tgl_created`, `tgl_updated`, `deleted_at`) VALUES
(1, 'PNY-20260509101020-826', 15, 7, 4, 2, '2026-05-09', 'Diserahkan', 'Penyerahan paket gizi bulanan.', 5, '2026-05-09 09:15:00', '2026-07-13 01:16:16', NULL),
(2, 'PNY-20260515125405-356', 22, 15, 3, 2, '2026-05-15', 'Diserahkan', 'Penyerahan paket gizi bulanan.', 4, '2026-05-15 09:15:00', '2026-07-13 01:16:16', NULL),
(3, 'PNY-20260621120840-410', 28, 24, 5, 2, '2026-06-21', 'Diserahkan', 'Bantuan rutin sesuai skala prioritas anak.', 6, '2026-06-21 09:15:00', '2026-07-13 01:16:16', NULL),
(4, 'PNY-20260604103737-257', 18, 11, 3, 2, '2026-06-04', 'Diproses', 'Penyerahan paket gizi bulanan.', 4, '2026-06-04 09:15:00', '2026-06-04 09:15:00', NULL),
(5, 'PNY-20260624135359-677', 31, 30, 4, 2, '2026-06-24', 'Diserahkan', 'Bantuan rutin sesuai skala prioritas anak.', 5, '2026-06-24 09:15:00', '2026-07-13 01:16:16', NULL),
(6, 'PNY-20260404090241-889', 23, 16, 4, 3, '2026-04-04', 'Diserahkan', 'Bantuan tambahan untuk mendukung pertumbuhan anak.', 5, '2026-04-04 09:15:00', '2026-07-13 01:16:16', NULL),
(7, 'PNY-20260623114936-526', 15, 7, 4, 2, '2026-06-23', 'Diserahkan', 'Bantuan rutin sesuai skala prioritas anak.', 5, '2026-06-23 09:15:00', '2026-07-13 01:16:16', NULL),
(8, 'PNY-20260603124119-594', 13, 5, 2, 3, '2026-06-03', 'Diproses', 'Penyerahan paket gizi bulanan.', 3, '2026-06-03 09:15:00', '2026-06-03 09:15:00', NULL),
(9, 'PNY-20260627141929-174', 26, 21, 3, 1, '2026-06-27', 'Diproses', 'Bantuan rutin sesuai skala prioritas anak.', 4, '2026-06-27 09:15:00', '2026-06-27 09:15:00', NULL),
(10, 'PNY-20260421152630-575', 20, 13, 5, 3, '2026-04-21', 'Diserahkan', 'Penyerahan paket gizi bulanan.', 6, '2026-04-21 09:15:00', '2026-07-13 01:16:16', NULL),
(11, 'PNY-20260514102055-835', 36, 36, 5, 1, '2026-05-14', 'Diproses', 'Bantuan tambahan untuk mendukung pertumbuhan anak.', 6, '2026-05-14 09:15:00', '2026-05-14 09:15:00', NULL),
(12, 'PNY-20260515140848-479', 14, 6, 3, 2, '2026-05-15', 'Dibatalkan', 'Bantuan rutin sesuai skala prioritas anak.', 4, '2026-05-15 09:15:00', '2026-05-15 09:15:00', NULL),
(13, 'PNY-20260511112907-373', 32, 31, 5, 2, '2026-05-11', 'Diproses', '', 6, '2026-05-11 09:15:00', '2026-05-11 09:15:00', NULL),
(14, 'PNY-20260502100603-397', 30, 28, 3, 2, '2026-05-02', 'Diproses', '', 4, '2026-05-02 09:15:00', '2026-05-02 09:15:00', NULL),
(15, 'PNY-20260618141555-263', 27, 23, 4, 3, '2026-06-18', 'Dibatalkan', 'Bantuan tambahan untuk mendukung pertumbuhan anak.', 5, '2026-06-18 09:15:00', '2026-06-18 09:15:00', NULL),
(16, 'PNY-20260613131248-263', 31, 29, 4, 3, '2026-06-13', 'Diserahkan', '', 5, '2026-06-13 09:15:00', '2026-07-13 01:16:16', NULL),
(17, 'PNY-20260605153156-415', 23, 16, 4, 3, '2026-06-05', 'Diserahkan', '', 5, '2026-06-05 09:15:00', '2026-07-13 01:16:16', NULL),
(18, 'PNY-20260403092532-568', 21, 14, 2, 3, '2026-04-03', 'Diserahkan', 'Penyerahan paket gizi bulanan.', 3, '2026-04-03 09:15:00', '2026-07-13 01:16:16', NULL),
(19, 'PNY-20260428130303-388', 26, 22, 3, 3, '2026-04-28', 'Diserahkan', '', 4, '2026-04-28 09:15:00', '2026-07-13 01:16:16', NULL),
(20, 'PNY-20260616151834-108', 16, 8, 5, 1, '2026-06-16', 'Diserahkan', 'Bantuan rutin sesuai skala prioritas anak.', 6, '2026-06-16 09:15:00', '2026-07-13 01:16:16', NULL),
(21, 'PNY-20260526105616-844', 17, 9, 2, 3, '2026-05-26', 'Diproses', 'Bantuan tambahan untuk mendukung pertumbuhan anak.', 3, '2026-05-26 09:15:00', '2026-05-26 09:15:00', NULL),
(22, 'PNY-20260522130225-152', 35, 34, 4, 2, '2026-05-22', 'Diserahkan', 'Penyerahan paket gizi bulanan.', 5, '2026-05-22 09:15:00', '2026-07-13 01:16:16', NULL),
(23, 'PNY-20260517120424-616', 9, 1, 2, 2, '2026-05-17', 'Diserahkan', '', 3, '2026-05-17 09:15:00', '2026-07-13 01:16:16', NULL),
(24, 'PNY-20260610125239-796', 29, 27, 2, 3, '2026-06-10', 'Diproses', 'Bantuan rutin sesuai skala prioritas anak.', 3, '2026-06-10 09:15:00', '2026-06-10 09:15:00', NULL),
(25, 'PNY-20260417092523-913', 18, 10, 3, 3, '2026-04-17', 'Diproses', 'Bantuan tambahan untuk mendukung pertumbuhan anak.', 4, '2026-04-17 09:15:00', '2026-04-17 09:15:00', NULL),
(26, 'PNY-20260611134809-303', 37, 37, 2, 1, '2026-06-11', 'Diserahkan', '', 3, '2026-06-11 09:15:00', '2026-07-13 01:16:16', NULL),
(27, 'PNY-20260604080202-240', 35, 35, 4, 1, '2026-06-04', 'Diserahkan', 'Bantuan tambahan untuk mendukung pertumbuhan anak.', 5, '2026-06-04 09:15:00', '2026-07-13 01:16:16', NULL),
(28, 'PNY-20260531150938-627', 11, 3, 4, 3, '2026-05-31', 'Diserahkan', 'Penyerahan paket gizi bulanan.', 5, '2026-05-31 09:15:00', '2026-07-13 01:16:16', NULL),
(29, 'PNY-20260512131025-731', 23, 17, 4, 3, '2026-05-12', 'Dibatalkan', 'Bantuan tambahan untuk mendukung pertumbuhan anak.', 5, '2026-05-12 09:15:00', '2026-05-12 09:15:00', NULL),
(30, 'PNY-20260615133253-621', 19, 12, 4, 2, '2026-06-15', 'Diserahkan', '', 5, '2026-06-15 09:15:00', '2026-07-13 01:16:16', NULL),
(31, 'PNY-20260630123052-117', 38, 38, 3, 3, '2026-06-30', 'Diserahkan', 'Bantuan tambahan untuk mendukung pertumbuhan anak.', 4, '2026-06-30 09:15:00', '2026-07-13 01:16:16', NULL),
(32, 'PNY-20260513092637-415', 25, 20, 2, 3, '2026-05-13', 'Diserahkan', 'Bantuan rutin sesuai skala prioritas anak.', 3, '2026-05-13 09:15:00', '2026-07-13 01:16:16', NULL),
(33, 'PNY-20260616151641-901', 12, 4, 5, 1, '2026-06-16', 'Diserahkan', 'Penyerahan paket gizi bulanan.', 6, '2026-06-16 09:15:00', '2026-07-13 01:16:16', NULL),
(34, 'PNY-20260407151033-744', 28, 25, 5, 3, '2026-04-07', 'Diserahkan', '', 6, '2026-04-07 09:15:00', '2026-07-13 01:16:16', NULL),
(35, 'PNY-20260419110236-817', 25, 19, 2, 1, '2026-04-19', 'Diserahkan', 'Bantuan rutin sesuai skala prioritas anak.', 3, '2026-04-19 09:15:00', '2026-07-13 01:16:16', NULL),
(36, 'PNY-20260425082820-528', 29, 26, 2, 1, '2026-04-25', 'Diproses', 'Penyerahan paket gizi bulanan.', 3, '2026-04-25 09:15:00', '2026-04-25 09:15:00', NULL),
(37, 'PNY-20260523112632-893', 33, 32, 2, 3, '2026-05-23', 'Dibatalkan', '', 3, '2026-05-23 09:15:00', '2026-05-23 09:15:00', NULL),
(38, 'PNY-20260408103313-674', 34, 33, 3, 1, '2026-04-08', 'Dibatalkan', 'Bantuan tambahan untuk mendukung pertumbuhan anak.', 4, '2026-04-08 09:15:00', '2026-04-08 09:15:00', NULL),
(39, 'PNY-20260624153324-421', 10, 2, 3, 2, '2026-06-24', 'Dibatalkan', 'Penyerahan paket gizi bulanan.', 4, '2026-06-24 09:15:00', '2026-06-24 09:15:00', NULL),
(40, 'PNY-20260529133422-792', 24, 18, 5, 2, '2026-05-29', 'Diserahkan', 'Bantuan tambahan untuk mendukung pertumbuhan anak.', 6, '2026-05-29 09:15:00', '2026-07-13 01:16:16', NULL);

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
(1, 1, 1, '1.00', '2026-07-13 01:16:16'),
(2, 1, 2, '8.00', '2026-07-13 01:16:16'),
(3, 1, 3, '1.00', '2026-07-13 01:16:16'),
(4, 1, 5, '3.00', '2026-07-13 01:16:16'),
(5, 1, 6, '1.00', '2026-07-13 01:16:16'),
(6, 2, 1, '1.00', '2026-07-13 01:16:16'),
(7, 2, 2, '8.00', '2026-07-13 01:16:16'),
(8, 2, 3, '1.00', '2026-07-13 01:16:16'),
(9, 2, 5, '3.00', '2026-07-13 01:16:16'),
(10, 2, 6, '1.00', '2026-07-13 01:16:16'),
(11, 3, 1, '1.00', '2026-07-13 01:16:16'),
(12, 3, 2, '8.00', '2026-07-13 01:16:16'),
(13, 3, 3, '1.00', '2026-07-13 01:16:16'),
(14, 3, 5, '3.00', '2026-07-13 01:16:16'),
(15, 3, 6, '1.00', '2026-07-13 01:16:16'),
(16, 4, 1, '1.00', '2026-07-13 01:16:16'),
(17, 4, 2, '8.00', '2026-07-13 01:16:16'),
(18, 4, 3, '1.00', '2026-07-13 01:16:16'),
(19, 4, 5, '3.00', '2026-07-13 01:16:16'),
(20, 4, 6, '1.00', '2026-07-13 01:16:16'),
(21, 5, 1, '1.00', '2026-07-13 01:16:16'),
(22, 5, 2, '8.00', '2026-07-13 01:16:16'),
(23, 5, 3, '1.00', '2026-07-13 01:16:16'),
(24, 5, 5, '3.00', '2026-07-13 01:16:16'),
(25, 5, 6, '1.00', '2026-07-13 01:16:16'),
(26, 6, 5, '2.00', '2026-07-13 01:16:16'),
(27, 6, 3, '1.00', '2026-07-13 01:16:16'),
(28, 6, 2, '4.00', '2026-07-13 01:16:16'),
(29, 7, 1, '1.00', '2026-07-13 01:16:16'),
(30, 7, 2, '8.00', '2026-07-13 01:16:16'),
(31, 7, 3, '1.00', '2026-07-13 01:16:16'),
(32, 7, 5, '3.00', '2026-07-13 01:16:16'),
(33, 7, 6, '1.00', '2026-07-13 01:16:16'),
(34, 8, 5, '2.00', '2026-07-13 01:16:16'),
(35, 8, 3, '1.00', '2026-07-13 01:16:16'),
(36, 8, 2, '4.00', '2026-07-13 01:16:16'),
(37, 9, 5, '5.00', '2026-07-13 01:16:16'),
(38, 9, 1, '1.50', '2026-07-13 01:16:16'),
(39, 9, 3, '2.00', '2026-07-13 01:16:16'),
(40, 9, 2, '10.00', '2026-07-13 01:16:16'),
(41, 9, 4, '1.00', '2026-07-13 01:16:16'),
(42, 10, 5, '2.00', '2026-07-13 01:16:16'),
(43, 10, 3, '1.00', '2026-07-13 01:16:16'),
(44, 10, 2, '4.00', '2026-07-13 01:16:16'),
(45, 11, 5, '5.00', '2026-07-13 01:16:16'),
(46, 11, 1, '1.50', '2026-07-13 01:16:16'),
(47, 11, 3, '2.00', '2026-07-13 01:16:16'),
(48, 11, 2, '10.00', '2026-07-13 01:16:16'),
(49, 11, 4, '1.00', '2026-07-13 01:16:16'),
(50, 12, 1, '1.00', '2026-07-13 01:16:16'),
(51, 12, 2, '8.00', '2026-07-13 01:16:16'),
(52, 12, 3, '1.00', '2026-07-13 01:16:16'),
(53, 12, 5, '3.00', '2026-07-13 01:16:16'),
(54, 12, 6, '1.00', '2026-07-13 01:16:16'),
(55, 13, 1, '1.00', '2026-07-13 01:16:16'),
(56, 13, 2, '8.00', '2026-07-13 01:16:16'),
(57, 13, 3, '1.00', '2026-07-13 01:16:16'),
(58, 13, 5, '3.00', '2026-07-13 01:16:16'),
(59, 13, 6, '1.00', '2026-07-13 01:16:16'),
(60, 14, 1, '1.00', '2026-07-13 01:16:16'),
(61, 14, 2, '8.00', '2026-07-13 01:16:16'),
(62, 14, 3, '1.00', '2026-07-13 01:16:16'),
(63, 14, 5, '3.00', '2026-07-13 01:16:16'),
(64, 14, 6, '1.00', '2026-07-13 01:16:16'),
(65, 15, 5, '2.00', '2026-07-13 01:16:16'),
(66, 15, 3, '1.00', '2026-07-13 01:16:16'),
(67, 15, 2, '4.00', '2026-07-13 01:16:16'),
(68, 16, 5, '2.00', '2026-07-13 01:16:16'),
(69, 16, 3, '1.00', '2026-07-13 01:16:16'),
(70, 16, 2, '4.00', '2026-07-13 01:16:16'),
(71, 17, 5, '2.00', '2026-07-13 01:16:16'),
(72, 17, 3, '1.00', '2026-07-13 01:16:16'),
(73, 17, 2, '4.00', '2026-07-13 01:16:16'),
(74, 18, 5, '2.00', '2026-07-13 01:16:16'),
(75, 18, 3, '1.00', '2026-07-13 01:16:16'),
(76, 18, 2, '4.00', '2026-07-13 01:16:16'),
(77, 19, 5, '2.00', '2026-07-13 01:16:16'),
(78, 19, 3, '1.00', '2026-07-13 01:16:16'),
(79, 19, 2, '4.00', '2026-07-13 01:16:16'),
(80, 20, 5, '5.00', '2026-07-13 01:16:16'),
(81, 20, 1, '1.50', '2026-07-13 01:16:16'),
(82, 20, 3, '2.00', '2026-07-13 01:16:16'),
(83, 20, 2, '10.00', '2026-07-13 01:16:16'),
(84, 20, 4, '1.00', '2026-07-13 01:16:16'),
(85, 21, 5, '2.00', '2026-07-13 01:16:16'),
(86, 21, 3, '1.00', '2026-07-13 01:16:16'),
(87, 21, 2, '4.00', '2026-07-13 01:16:16'),
(88, 22, 1, '1.00', '2026-07-13 01:16:16'),
(89, 22, 2, '8.00', '2026-07-13 01:16:16'),
(90, 22, 3, '1.00', '2026-07-13 01:16:16'),
(91, 22, 5, '3.00', '2026-07-13 01:16:16'),
(92, 22, 6, '1.00', '2026-07-13 01:16:16'),
(93, 23, 1, '1.00', '2026-07-13 01:16:16'),
(94, 23, 2, '8.00', '2026-07-13 01:16:16'),
(95, 23, 3, '1.00', '2026-07-13 01:16:16'),
(96, 23, 5, '3.00', '2026-07-13 01:16:16'),
(97, 23, 6, '1.00', '2026-07-13 01:16:16'),
(98, 24, 5, '2.00', '2026-07-13 01:16:16'),
(99, 24, 3, '1.00', '2026-07-13 01:16:16'),
(100, 24, 2, '4.00', '2026-07-13 01:16:16'),
(101, 25, 5, '2.00', '2026-07-13 01:16:16'),
(102, 25, 3, '1.00', '2026-07-13 01:16:16'),
(103, 25, 2, '4.00', '2026-07-13 01:16:16'),
(104, 26, 5, '5.00', '2026-07-13 01:16:16'),
(105, 26, 1, '1.50', '2026-07-13 01:16:16'),
(106, 26, 3, '2.00', '2026-07-13 01:16:16'),
(107, 26, 2, '10.00', '2026-07-13 01:16:16'),
(108, 26, 4, '1.00', '2026-07-13 01:16:16'),
(109, 27, 5, '5.00', '2026-07-13 01:16:16'),
(110, 27, 1, '1.50', '2026-07-13 01:16:16'),
(111, 27, 3, '2.00', '2026-07-13 01:16:16'),
(112, 27, 2, '10.00', '2026-07-13 01:16:16'),
(113, 27, 4, '1.00', '2026-07-13 01:16:16'),
(114, 28, 5, '2.00', '2026-07-13 01:16:16'),
(115, 28, 3, '1.00', '2026-07-13 01:16:16'),
(116, 28, 2, '4.00', '2026-07-13 01:16:16'),
(117, 29, 5, '2.00', '2026-07-13 01:16:16'),
(118, 29, 3, '1.00', '2026-07-13 01:16:16'),
(119, 29, 2, '4.00', '2026-07-13 01:16:16'),
(120, 30, 1, '1.00', '2026-07-13 01:16:16'),
(121, 30, 2, '8.00', '2026-07-13 01:16:16'),
(122, 30, 3, '1.00', '2026-07-13 01:16:16'),
(123, 30, 5, '3.00', '2026-07-13 01:16:16'),
(124, 30, 6, '1.00', '2026-07-13 01:16:16'),
(125, 31, 5, '2.00', '2026-07-13 01:16:16'),
(126, 31, 3, '1.00', '2026-07-13 01:16:16'),
(127, 31, 2, '4.00', '2026-07-13 01:16:16'),
(128, 32, 5, '2.00', '2026-07-13 01:16:16'),
(129, 32, 3, '1.00', '2026-07-13 01:16:16'),
(130, 32, 2, '4.00', '2026-07-13 01:16:16'),
(131, 33, 5, '5.00', '2026-07-13 01:16:16'),
(132, 33, 1, '1.50', '2026-07-13 01:16:16'),
(133, 33, 3, '2.00', '2026-07-13 01:16:16'),
(134, 33, 2, '10.00', '2026-07-13 01:16:16'),
(135, 33, 4, '1.00', '2026-07-13 01:16:16'),
(136, 34, 5, '2.00', '2026-07-13 01:16:16'),
(137, 34, 3, '1.00', '2026-07-13 01:16:16'),
(138, 34, 2, '4.00', '2026-07-13 01:16:16'),
(139, 35, 5, '5.00', '2026-07-13 01:16:16'),
(140, 35, 1, '1.50', '2026-07-13 01:16:16'),
(141, 35, 3, '2.00', '2026-07-13 01:16:16'),
(142, 35, 2, '10.00', '2026-07-13 01:16:16'),
(143, 35, 4, '1.00', '2026-07-13 01:16:16'),
(144, 36, 5, '5.00', '2026-07-13 01:16:16'),
(145, 36, 1, '1.50', '2026-07-13 01:16:16'),
(146, 36, 3, '2.00', '2026-07-13 01:16:16'),
(147, 36, 2, '10.00', '2026-07-13 01:16:16'),
(148, 36, 4, '1.00', '2026-07-13 01:16:16'),
(149, 37, 5, '2.00', '2026-07-13 01:16:16'),
(150, 37, 3, '1.00', '2026-07-13 01:16:16'),
(151, 37, 2, '4.00', '2026-07-13 01:16:16'),
(152, 38, 5, '5.00', '2026-07-13 01:16:16'),
(153, 38, 1, '1.50', '2026-07-13 01:16:16'),
(154, 38, 3, '2.00', '2026-07-13 01:16:16'),
(155, 38, 2, '10.00', '2026-07-13 01:16:16'),
(156, 38, 4, '1.00', '2026-07-13 01:16:16'),
(157, 39, 1, '1.00', '2026-07-13 01:16:16'),
(158, 39, 2, '8.00', '2026-07-13 01:16:16'),
(159, 39, 3, '1.00', '2026-07-13 01:16:16'),
(160, 39, 5, '3.00', '2026-07-13 01:16:16'),
(161, 39, 6, '1.00', '2026-07-13 01:16:16'),
(162, 40, 1, '1.00', '2026-07-13 01:16:16'),
(163, 40, 2, '8.00', '2026-07-13 01:16:16'),
(164, 40, 3, '1.00', '2026-07-13 01:16:16'),
(165, 40, 5, '3.00', '2026-07-13 01:16:16'),
(166, 40, 6, '1.00', '2026-07-13 01:16:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Kader','Petani','Ibu') NOT NULL,
  `id_gudang` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `role`, `id_gudang`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin_pusat', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Admin', 1, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(2, 'admin_cabang', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Admin', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(3, 'kader_melati', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Kader', 2, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(4, 'kader_mawar', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Kader', 3, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(5, 'kader_anggrek', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Kader', 4, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(6, 'kader_dahlia', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Kader', 5, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(7, 'petani_jaya', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Petani', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(8, 'petani_subur', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Petani', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(9, 'ibu_siti', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(10, 'ibu_rina', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(11, 'ibu_dewi', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(12, 'ibu_ani', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(13, 'ibu_sri', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(14, 'ibu_wati', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(15, 'ibu_yuni', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(16, 'ibu_fitri', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(17, 'ibu_indah', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(18, 'ibu_nur', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(19, 'ibu_maya', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(20, 'ibu_lestari', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(21, 'ibu_puji', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(22, 'ibu_ratna', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(23, 'ibu_endang', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(24, 'ibu_titin', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(25, 'ibu_yanti', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(26, 'ibu_sari', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(27, 'ibu_ika', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(28, 'ibu_novi', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(29, 'ibu_rahma', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(30, 'ibu_wulan', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(31, 'ibu_dian', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(32, 'ibu_eka', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(33, 'ibu_tri', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(34, 'ibu_umi', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(35, 'ibu_vera', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(36, 'ibu_wiwik', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(37, 'ibu_yeni', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(38, 'ibu_zahra', '$2y$12$y7daeVOsdQ.kHXYeRtl0b.TU9GP9aXAZC6pE7E6D2pzYWYoLG7RNW', 'Ibu', NULL, 1, '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL);

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
-- Indexes for table `paket_gizi`
--
ALTER TABLE `paket_gizi`
  ADD PRIMARY KEY (`id_paket`),
  ADD UNIQUE KEY `kode_prioritas` (`kode_prioritas`);

--
-- Indexes for table `paket_gizi_detail`
--
ALTER TABLE `paket_gizi_detail`
  ADD PRIMARY KEY (`id_paket_detail`),
  ADD KEY `fk_paket_gizi_detail_paket` (`id_paket`),
  ADD KEY `fk_paket_gizi_detail_komoditas` (`id_komoditas`);

--
-- Indexes for table `petani_lahan_komoditas`
--
ALTER TABLE `petani_lahan_komoditas`
  ADD PRIMARY KEY (`id_petani_komoditas`),
  ADD KEY `idx_petani_lahan_komoditas_petani` (`id_petani`),
  ADD KEY `idx_petani_lahan_komoditas_komoditas` (`id_komoditas`);

--
-- Indexes for table `petani_lokal`
--
ALTER TABLE `petani_lokal`
  ADD PRIMARY KEY (`id_petani`),
  ADD UNIQUE KEY `uq_petani_lokal_user` (`id_user`);

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
  ADD KEY `idx_t_penyerahan_tanggal` (`tanggal_penyerahan`),
  ADD KEY `fk_t_penyerahan_paket` (`id_paket`);

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
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_users_gudang` (`id_gudang`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anak`
--
ALTER TABLE `anak`
  MODIFY `id_anak` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `gudang`
--
ALTER TABLE `gudang`
  MODIFY `id_gudang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `komoditas_pangan`
--
ALTER TABLE `komoditas_pangan`
  MODIFY `id_komoditas` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `paket_gizi`
--
ALTER TABLE `paket_gizi`
  MODIFY `id_paket` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `paket_gizi_detail`
--
ALTER TABLE `paket_gizi_detail`
  MODIFY `id_paket_detail` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `petani_lahan_komoditas`
--
ALTER TABLE `petani_lahan_komoditas`
  MODIFY `id_petani_komoditas` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `satuan`
--
ALTER TABLE `satuan`
  MODIFY `id_satuan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `standar_pertumbuhan`
--
ALTER TABLE `standar_pertumbuhan`
  MODIFY `id_standar` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=245;

--
-- AUTO_INCREMENT for table `stok_log`
--
ALTER TABLE `stok_log`
  MODIFY `id_stok_log` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT for table `t_distribusi`
--
ALTER TABLE `t_distribusi`
  MODIFY `id_distribusi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `t_distribusi_detail`
--
ALTER TABLE `t_distribusi_detail`
  MODIFY `id_distribusi_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `t_pemeriksaan`
--
ALTER TABLE `t_pemeriksaan`
  MODIFY `id_pemeriksaan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `t_pengadaan`
--
ALTER TABLE `t_pengadaan`
  MODIFY `id_pengadaan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `t_pengadaan_detail`
--
ALTER TABLE `t_pengadaan_detail`
  MODIFY `id_pengadaan_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `t_penyerahan`
--
ALTER TABLE `t_penyerahan`
  MODIFY `id_penyerahan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `t_penyerahan_detail`
--
ALTER TABLE `t_penyerahan_detail`
  MODIFY `id_penyerahan_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

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
-- Constraints for table `paket_gizi_detail`
--
ALTER TABLE `paket_gizi_detail`
  ADD CONSTRAINT `fk_paket_gizi_detail_komoditas` FOREIGN KEY (`id_komoditas`) REFERENCES `komoditas_pangan` (`id_komoditas`),
  ADD CONSTRAINT `fk_paket_gizi_detail_paket` FOREIGN KEY (`id_paket`) REFERENCES `paket_gizi` (`id_paket`);

--
-- Constraints for table `petani_lahan_komoditas`
--
ALTER TABLE `petani_lahan_komoditas`
  ADD CONSTRAINT `fk_petani_lahan_komoditas_komoditas` FOREIGN KEY (`id_komoditas`) REFERENCES `komoditas_pangan` (`id_komoditas`),
  ADD CONSTRAINT `fk_petani_lahan_komoditas_petani` FOREIGN KEY (`id_petani`) REFERENCES `petani_lokal` (`id_petani`);

--
-- Constraints for table `petani_lokal`
--
ALTER TABLE `petani_lokal`
  ADD CONSTRAINT `fk_petani_lokal_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
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
  ADD CONSTRAINT `fk_t_penyerahan_ibu` FOREIGN KEY (`id_ibu`) REFERENCES `ibu` (`id_ibu`),
  ADD CONSTRAINT `fk_t_penyerahan_paket` FOREIGN KEY (`id_paket`) REFERENCES `paket_gizi` (`id_paket`);

--
-- Constraints for table `t_penyerahan_detail`
--
ALTER TABLE `t_penyerahan_detail`
  ADD CONSTRAINT `fk_t_penyerahan_detail_komoditas` FOREIGN KEY (`id_komoditas`) REFERENCES `komoditas_pangan` (`id_komoditas`),
  ADD CONSTRAINT `fk_t_penyerahan_detail_penyerahan` FOREIGN KEY (`id_penyerahan`) REFERENCES `t_penyerahan` (`id_penyerahan`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_gudang` FOREIGN KEY (`id_gudang`) REFERENCES `gudang` (`id_gudang`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
