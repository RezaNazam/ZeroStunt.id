-- =============================================================
-- DUMMY DATA DEMO PRODUCTION - zerostunt_db
-- Generated automatically. Struktur tabel, trigger, procedure,
-- function, view, relasi FK TIDAK diubah.
-- Data yang tetap dipertahankan: standar_pertumbuhan, paket_gizi,
-- paket_gizi_detail, satuan, dan 7 komoditas awal (id 1-7) karena
-- direferensikan oleh paket_gizi_detail.
-- =============================================================
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS=0;
START TRANSACTION;
SET time_zone = "+00:00";

-- -------------------------------------------------------------
-- 1. HAPUS SELURUH DATA OPERASIONAL (urutan aman terhadap FK)
-- -------------------------------------------------------------
DELETE FROM `t_penyerahan_detail`;
DELETE FROM `t_penyerahan`;
DELETE FROM `t_distribusi_detail`;
DELETE FROM `t_distribusi`;
DELETE FROM `t_pengadaan_detail`;
DELETE FROM `t_pengadaan`;
DELETE FROM `t_pemeriksaan`;
DELETE FROM `stok_log`;
DELETE FROM `anak`;
DELETE FROM `ibu`;
DELETE FROM `petani_lahan_komoditas`;
DELETE FROM `petani_lokal`;
DELETE FROM `users`;
DELETE FROM `gudang`;
-- Komoditas TIDAK dihapus total: 7 komoditas awal (id 1-7) dipertahankan
-- karena direferensikan oleh paket_gizi_detail yang tidak boleh diubah.
DELETE FROM `komoditas_pangan` WHERE `id_komoditas` NOT IN (1,2,3,4,5,6,7);

-- Reset AUTO_INCREMENT tabel operasional
ALTER TABLE `anak` AUTO_INCREMENT = 1;
ALTER TABLE `gudang` AUTO_INCREMENT = 1;
ALTER TABLE `petani_lahan_komoditas` AUTO_INCREMENT = 1;
ALTER TABLE `petani_lokal` AUTO_INCREMENT = 1;
ALTER TABLE `stok_log` AUTO_INCREMENT = 1;
ALTER TABLE `t_distribusi` AUTO_INCREMENT = 1;
ALTER TABLE `t_distribusi_detail` AUTO_INCREMENT = 1;
ALTER TABLE `t_pemeriksaan` AUTO_INCREMENT = 1;
ALTER TABLE `t_pengadaan` AUTO_INCREMENT = 1;
ALTER TABLE `t_pengadaan_detail` AUTO_INCREMENT = 1;
ALTER TABLE `t_penyerahan` AUTO_INCREMENT = 1;
ALTER TABLE `t_penyerahan_detail` AUTO_INCREMENT = 1;
ALTER TABLE `users` AUTO_INCREMENT = 1;

-- -------------------------------------------------------------
-- 2. GUDANG
-- -------------------------------------------------------------
INSERT INTO `gudang` (`id_gudang`, `nama_gudang`, `lokasi_gudang`, `jenis_gudang`, `alamat_lengkap`, `nama_pengelola`, `tgl_created`, `is_deleted`) VALUES
(1, 'Gudang Pusat Zerostunt', 'Cikarang Pusat', 'Pusat', 'Jl. Industri Raya No. 1, Cikarang Pusat, Bekasi', 'Admin Pusat', '2026-01-05 08:00:00', 0),
(2, 'Posyandu Melati', 'Cikarang Utara', 'Posyandu', 'Jl. Melati Indah No. 5, Cikarang Utara, Bekasi', 'Kader Melati', '2026-01-05 08:00:00', 0),
(3, 'Posyandu Mawar', 'Cikarang Selatan', 'Posyandu', 'Jl. Mawar Merah No. 9, Cikarang Selatan, Bekasi', 'Kader Mawar', '2026-01-05 08:00:00', 0),
(4, 'Posyandu Anggrek', 'Cikarang Barat', 'Posyandu', 'Jl. Anggrek Ungu No. 12, Cikarang Barat, Bekasi', 'Kader Anggrek', '2026-01-05 08:00:00', 0),
(5, 'Posyandu Dahlia', 'Cikarang Timur', 'Posyandu', 'Jl. Dahlia Putih No. 3, Cikarang Timur, Bekasi', 'Kader Dahlia', '2026-01-05 08:00:00', 0);

-- -------------------------------------------------------------
-- 3. USERS (password semua akun: admin123)
-- -------------------------------------------------------------
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

-- -------------------------------------------------------------
-- 4. PETANI_LOKAL
-- -------------------------------------------------------------
INSERT INTO `petani_lokal` (`id_petani`, `id_user`, `nama_lahan`, `alamat_lahan`, `luas_lahan`, `satuan_luas`, `jenis_usaha`, `status_lahan`, `no_rekening`, `kapasitas_panen_bulan`, `deskripsi_lahan`, `tgl_created`, `tgl_updated`, `deleted_at`) VALUES
(7, 7, 'Lahan Tani Jaya', 'Jl. Sawah Jaya No. 21, Cikarang Pusat, Bekasi', 4.20, 'ha', 'Perikanan dan sayuran', 'Aktif', '1122334455', 520.00, 'Mitra penyedia ikan, sayuran hijau, dan umbi-umbian.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(8, 8, 'Kebun Sumber Subur', 'Jl. Tani Subur No. 30, Cikarang Timur, Bekasi', 3.10, 'ha', 'Peternakan dan palawija', 'Aktif', '5544332211', 410.00, 'Mitra penyedia unggas, telur, tempe/tahu, dan kacang-kacangan.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL);

-- -------------------------------------------------------------
-- 5. KOMODITAS_PANGAN (tambahan, id 1-7 sudah ada & tidak diubah)
-- -------------------------------------------------------------
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

-- -------------------------------------------------------------
-- 6. PETANI_LAHAN_KOMODITAS
-- -------------------------------------------------------------
INSERT INTO `petani_lahan_komoditas` (`id_petani_komoditas`, `id_petani`, `id_komoditas`, `luas_area`, `satuan_luas`, `estimasi_panen`, `catatan`, `tgl_created`, `tgl_updated`, `deleted_at`) VALUES
(1, 7, 1, 1.19, 'ha', 214.49, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(2, 7, 3, 1.38, 'ha', 191.45, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(3, 7, 5, 1.03, 'ha', 122.52, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(4, 7, 8, 1.62, 'ha', 301.7, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(5, 7, 9, 0.62, 'ha', 86.28, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(6, 7, 13, 0.79, 'ha', 87.01, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(7, 7, 14, 1.47, 'ha', 138.63, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(8, 7, 16, 0.93, 'ha', 121.14, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(9, 7, 17, 0.88, 'ha', 102.99, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(10, 7, 22, 0.46, 'ha', 66.39, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(11, 7, 23, 0.57, 'ha', 119.2, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(12, 8, 2, 0.39, 'ha', 47.21, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(13, 8, 4, 1.05, 'ha', 214.16, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(14, 8, 6, 0.73, 'ha', 78.05, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(15, 8, 7, 0.38, 'ha', 65.58, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(16, 8, 10, 1.23, 'ha', 268.06, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(17, 8, 11, 1.33, 'ha', 267.74, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(18, 8, 12, 0.76, 'ha', 109.04, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(19, 8, 18, 1.3, 'ha', 133.6, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(20, 8, 19, 0.73, 'ha', 126.89, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(21, 8, 27, 1.14, 'ha', 200.29, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(22, 8, 28, 0.39, 'ha', 65.87, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(23, 8, 29, 0.94, 'ha', 107.42, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL),
(24, 8, 30, 0.85, 'ha', 100.12, 'Lahan aktif produksi rutin.', '2026-01-05 08:00:00', '2026-01-05 08:00:00', NULL);

-- -------------------------------------------------------------
-- 7. IBU
-- -------------------------------------------------------------
INSERT INTO `ibu` (`id_ibu`, `NIK_ibu`, `nama_ibu`, `no_telp`, `alamat`, `id_gudang`, `is_pregnant`, `tgl_created`, `deleted_at`) VALUES
(9, '327500012704950001', 'Siti Nurhaliza', '081234500000', 'Jl. Kenanga No. 15, RT 01/RW 06, Cikarang', 2, 0, '2026-01-05 08:00:00', NULL),
(10, '327500021204990002', 'Rina Marlina', '081234510001', 'Jl. Cempaka No. 14, RT 08/RW 07, Cikarang', 3, 0, '2026-01-05 08:00:00', NULL),
(11, '327500032403940003', 'Dewi Ramadhani', '081234520002', 'Jl. Melati No. 16, RT 09/RW 09, Cikarang', 4, 0, '2026-01-05 08:00:00', NULL),
(12, '327500042807990004', 'Ani Puspita', '081234530003', 'Jl. Flamboyan No. 24, RT 04/RW 03, Cikarang', 5, 0, '2026-01-05 08:00:00', NULL),
(13, '327500051201910005', 'Sri Nuraini', '081234540004', 'Jl. Melati No. 11, RT 07/RW 02, Cikarang', 2, 0, '2026-01-05 08:00:00', NULL),
(14, '327500062409940006', 'Wati Handayani', '081234550005', 'Jl. Seroja No. 1, RT 02/RW 09, Cikarang', 3, 0, '2026-01-05 08:00:00', NULL),
(15, '327500072002940007', 'Yuni Utami', '081234560006', 'Jl. Flamboyan No. 11, RT 08/RW 01, Cikarang', 4, 0, '2026-01-05 08:00:00', NULL),
(16, '327500081809920008', 'Fitri Oktaviani', '081234570007', 'Jl. Seroja No. 7, RT 05/RW 09, Cikarang', 5, 0, '2026-01-05 08:00:00', NULL),
(17, '327500091406920009', 'Indah Setiawati', '081234580008', 'Jl. Seroja No. 34, RT 01/RW 06, Cikarang', 2, 0, '2026-01-05 08:00:00', NULL),
(18, '327500101306940010', 'Nur Pratiwi', '081234590009', 'Jl. Anggrek No. 4, RT 04/RW 02, Cikarang', 3, 1, '2026-01-05 08:00:00', NULL),
(19, '327500112502980011', 'Maya Anggraini', '081234600010', 'Jl. Melati No. 9, RT 08/RW 09, Cikarang', 4, 0, '2026-01-05 08:00:00', NULL),
(20, '327500122610960012', 'Lestari Permata', '081234610011', 'Jl. Anggrek No. 35, RT 04/RW 05, Cikarang', 5, 0, '2026-01-05 08:00:00', NULL),
(21, '327500132108980013', 'Puji Yulianti', '081234620012', 'Jl. Teratai No. 8, RT 04/RW 04, Cikarang', 2, 1, '2026-01-05 08:00:00', NULL),
(22, '327500141010980014', 'Ratna Susanti', '081234630013', 'Jl. Anggrek No. 38, RT 04/RW 01, Cikarang', 3, 1, '2026-01-05 08:00:00', NULL),
(23, '327500151104910015', 'Endang Kartika', '081234640014', 'Jl. Kenanga No. 22, RT 02/RW 09, Cikarang', 4, 0, '2026-01-05 08:00:00', NULL),
(24, '327500162504980016', 'Titin Widyawati', '081234650015', 'Jl. Melati No. 37, RT 08/RW 04, Cikarang', 5, 0, '2026-01-05 08:00:00', NULL),
(25, '327500172304910017', 'Yanti Lestari', '081234660016', 'Jl. Mawar No. 28, RT 06/RW 07, Cikarang', 2, 0, '2026-01-05 08:00:00', NULL),
(26, '327500181111910018', 'Sari Damayanti', '081234670017', 'Jl. Kenanga No. 26, RT 06/RW 02, Cikarang', 3, 0, '2026-01-05 08:00:00', NULL),
(27, '327500191609970019', 'Ika Safitri', '081234680018', 'Jl. Melati No. 28, RT 03/RW 05, Cikarang', 4, 0, '2026-01-05 08:00:00', NULL),
(28, '327500201208980020', 'Novi Maharani', '081234690019', 'Jl. Mawar No. 4, RT 09/RW 01, Cikarang', 5, 0, '2026-01-05 08:00:00', NULL),
(29, '327500211703960021', 'Rahma Wulandari', '081234700020', 'Jl. Teratai No. 31, RT 04/RW 07, Cikarang', 2, 0, '2026-01-05 08:00:00', NULL),
(30, '327500221507900022', 'Wulan Rahmawati', '081234710021', 'Jl. Flamboyan No. 17, RT 08/RW 05, Cikarang', 3, 0, '2026-01-05 08:00:00', NULL),
(31, '327500232711970023', 'Dian Ramadhani', '081234720022', 'Jl. Melati No. 13, RT 05/RW 04, Cikarang', 4, 0, '2026-01-05 08:00:00', NULL),
(32, '327500242812980024', 'Eka Puspita', '081234730023', 'Jl. Kenanga No. 21, RT 01/RW 01, Cikarang', 5, 0, '2026-01-05 08:00:00', NULL),
(33, '327500252609920025', 'Tri Nuraini', '081234740024', 'Jl. Kenanga No. 33, RT 02/RW 03, Cikarang', 2, 1, '2026-01-05 08:00:00', NULL),
(34, '327500261211930026', 'Umi Handayani', '081234750025', 'Jl. Flamboyan No. 8, RT 04/RW 01, Cikarang', 3, 0, '2026-01-05 08:00:00', NULL),
(35, '327500272311990027', 'Vera Utami', '081234760026', 'Jl. Tanjung No. 34, RT 06/RW 05, Cikarang', 4, 0, '2026-01-05 08:00:00', NULL),
(36, '327500282004940028', 'Wiwik Oktaviani', '081234770027', 'Jl. Flamboyan No. 9, RT 05/RW 08, Cikarang', 5, 0, '2026-01-05 08:00:00', NULL),
(37, '327500291201970029', 'Yeni Setiawati', '081234780028', 'Jl. Tanjung No. 37, RT 02/RW 02, Cikarang', 2, 0, '2026-01-05 08:00:00', NULL),
(38, '327500302605920030', 'Zahra Pratiwi', '081234790029', 'Jl. Cempaka No. 5, RT 04/RW 06, Cikarang', 3, 0, '2026-01-05 08:00:00', NULL);

-- -------------------------------------------------------------
-- 8. ANAK
-- -------------------------------------------------------------
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

-- -------------------------------------------------------------
-- 9. T_PEMERIKSAAN
-- -------------------------------------------------------------
INSERT INTO `t_pemeriksaan` (`id_pemeriksaan`, `id_anak`, `id_kader`, `tanggal_pemeriksaan`, `berat_badan`, `tinggi_badan`, `usia_bulan`, `status_gizi`, `catatan`, `tgl_created`, `tgl_updated`, `deleted_at`) VALUES
(1, 16, 5, '2026-01-04', 4.01, 49.65, 0, 'Prioritas 3', 'Orang tua diminta rutin membawa anak ke posyandu.', '2026-01-04 09:00:00', '2026-01-04 09:00:00', NULL),
(2, 17, 5, '2026-01-06', 18.63, 105.93, 76, 'Prioritas 3', 'Kondisi gizi membaik dibanding pemeriksaan sebelumnya.', '2026-01-06 09:00:00', '2026-01-06 09:00:00', NULL),
(3, 21, 4, '2026-01-11', 11.12, 82.9, 34, 'Prioritas 1', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-01-11 09:00:00', '2026-01-11 09:00:00', NULL),
(4, 6, 4, '2026-01-18', 19.39, 111.41, 95, 'Prioritas 2', 'Berat dan tinggi perlu pemantauan intensif.', '2026-01-18 09:00:00', '2026-01-18 09:00:00', NULL),
(5, 38, 4, '2026-01-19', 5.32, 55.04, 2, 'Prioritas 3', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-01-19 09:00:00', '2026-01-19 09:00:00', NULL),
(6, 26, 3, '2026-01-23', 16.5, 104.6, 84, 'Prioritas 1', 'Berat dan tinggi perlu pemantauan intensif.', '2026-01-23 09:00:00', '2026-01-23 09:00:00', NULL),
(7, 23, 5, '2026-01-31', 20.36, 112.34, 89, 'Prioritas 3', 'Perlu dukungan pangan tambahan bulan ini.', '2026-01-31 09:00:00', '2026-01-31 09:00:00', NULL),
(8, 7, 5, '2026-02-08', 9.26, 75.71, 18, 'Prioritas 2', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-02-08 09:00:00', '2026-02-08 09:00:00', NULL),
(9, 4, 6, '2026-02-08', 17.98, 107.75, 96, 'Prioritas 1', 'Pertumbuhan dalam pemantauan rutin posyandu.', '2026-02-08 09:00:00', '2026-02-08 09:00:00', NULL),
(10, 18, 6, '2026-02-10', 14.17, 89.83, 42, 'Prioritas 2', 'Berat dan tinggi perlu pemantauan intensif.', '2026-02-10 09:00:00', '2026-02-10 09:00:00', NULL),
(11, 10, 4, '2026-02-14', 15.02, 92.0, 43, 'Prioritas 3', 'Berat dan tinggi perlu pemantauan intensif.', '2026-02-14 09:00:00', '2026-02-14 09:00:00', NULL),
(12, 35, 5, '2026-02-15', 5.02, 52.76, 3, 'Prioritas 1', 'Orang tua diminta rutin membawa anak ke posyandu.', '2026-02-15 09:00:00', '2026-02-15 09:00:00', NULL),
(13, 17, 5, '2026-02-15', 18.74, 106.83, 77, 'Prioritas 3', 'Perlu dukungan pangan tambahan bulan ini.', '2026-02-15 09:00:00', '2026-02-15 09:00:00', NULL),
(14, 27, 3, '2026-02-19', 5.55, 54.76, 3, 'Prioritas 3', 'Perlu dukungan pangan tambahan bulan ini.', '2026-02-19 09:00:00', '2026-02-19 09:00:00', NULL),
(15, 2, 4, '2026-02-26', 11.19, 81.25, 29, 'Prioritas 2', 'Orang tua diminta rutin membawa anak ke posyandu.', '2026-02-26 09:00:00', '2026-02-26 09:00:00', NULL),
(16, 20, 3, '2026-02-27', 15.67, 91.76, 45, 'Prioritas 3', 'Pertumbuhan dalam pemantauan rutin posyandu.', '2026-02-27 09:00:00', '2026-02-27 09:00:00', NULL),
(17, 3, 5, '2026-03-04', 9.18, 69.55, 10, 'Prioritas 3', 'Pertumbuhan dalam pemantauan rutin posyandu.', '2026-03-04 09:00:00', '2026-03-04 09:00:00', NULL),
(18, 31, 6, '2026-03-06', 21.76, 118.5, 113, 'Prioritas 2', 'Berat dan tinggi perlu pemantauan intensif.', '2026-03-06 09:00:00', '2026-03-06 09:00:00', NULL),
(19, 8, 6, '2026-03-14', 3.25, 47.83, 0, 'Prioritas 1', 'Orang tua diminta rutin membawa anak ke posyandu.', '2026-03-14 09:00:00', '2026-03-14 09:00:00', NULL),
(20, 32, 3, '2026-03-14', 5.64, 57.17, 4, 'Prioritas 3', 'Perlu dukungan pangan tambahan bulan ini.', '2026-03-14 09:00:00', '2026-03-14 09:00:00', NULL),
(21, 19, 3, '2026-03-18', 17.3, 105.78, 91, 'Prioritas 1', 'Pertumbuhan dalam pemantauan rutin posyandu.', '2026-03-18 09:00:00', '2026-03-18 09:00:00', NULL),
(22, 34, 5, '2026-03-19', 14.53, 91.65, 47, 'Prioritas 2', 'Berat dan tinggi perlu pemantauan intensif.', '2026-03-19 09:00:00', '2026-03-19 09:00:00', NULL),
(23, 36, 6, '2026-03-21', 9.66, 78.49, 25, 'Prioritas 1', 'Perlu dukungan pangan tambahan bulan ini.', '2026-03-21 09:00:00', '2026-03-21 09:00:00', NULL),
(24, 13, 6, '2026-03-23', 12.07, 84.06, 28, 'Prioritas 3', 'Kondisi gizi membaik dibanding pemeriksaan sebelumnya.', '2026-03-23 09:00:00', '2026-03-23 09:00:00', NULL),
(25, 26, 3, '2026-03-26', 17.01, 104.5, 86, 'Prioritas 1', 'Perlu dukungan pangan tambahan bulan ini.', '2026-03-26 09:00:00', '2026-03-26 09:00:00', NULL),
(26, 28, 4, '2026-03-26', 13.33, 88.09, 39, 'Prioritas 2', 'Kondisi gizi membaik dibanding pemeriksaan sebelumnya.', '2026-03-26 09:00:00', '2026-03-26 09:00:00', NULL),
(27, 12, 5, '2026-03-29', 12.44, 84.86, 35, 'Prioritas 2', 'Berat dan tinggi perlu pemantauan intensif.', '2026-03-29 09:00:00', '2026-03-29 09:00:00', NULL),
(28, 37, 3, '2026-03-30', 21.25, 117.42, 121, 'Prioritas 1', 'Pertumbuhan dalam pemantauan rutin posyandu.', '2026-03-30 09:00:00', '2026-03-30 09:00:00', NULL),
(29, 10, 4, '2026-03-30', 15.34, 92.91, 44, 'Prioritas 3', 'Perlu dukungan pangan tambahan bulan ini.', '2026-03-30 09:00:00', '2026-03-30 09:00:00', NULL),
(30, 31, 6, '2026-04-01', 22.19, 117.34, 114, 'Prioritas 2', 'Pertumbuhan dalam pemantauan rutin posyandu.', '2026-04-01 09:00:00', '2026-04-01 09:00:00', NULL),
(31, 18, 6, '2026-04-02', 13.94, 90.6, 44, 'Prioritas 2', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-04-02 09:00:00', '2026-04-02 09:00:00', NULL),
(32, 28, 4, '2026-04-03', 13.39, 87.74, 40, 'Prioritas 2', 'Berat dan tinggi perlu pemantauan intensif.', '2026-04-03 09:00:00', '2026-04-03 09:00:00', NULL),
(33, 5, 3, '2026-04-05', 13.03, 85.05, 31, 'Prioritas 3', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-04-05 09:00:00', '2026-04-05 09:00:00', NULL),
(34, 19, 3, '2026-04-06', 18.1, 106.66, 92, 'Prioritas 1', 'Kondisi gizi membaik dibanding pemeriksaan sebelumnya.', '2026-04-06 09:00:00', '2026-04-06 09:00:00', NULL),
(35, 11, 4, '2026-04-10', 21.54, 116.24, 111, 'Prioritas 2', 'Perlu dukungan pangan tambahan bulan ini.', '2026-04-10 09:00:00', '2026-04-10 09:00:00', NULL),
(36, 13, 6, '2026-04-16', 12.33, 83.72, 29, 'Prioritas 3', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-04-16 09:00:00', '2026-04-16 09:00:00', NULL),
(37, 23, 5, '2026-04-16', 20.63, 112.32, 92, 'Prioritas 3', 'Berat dan tinggi perlu pemantauan intensif.', '2026-04-16 09:00:00', '2026-04-16 09:00:00', NULL),
(38, 21, 4, '2026-04-16', 12.28, 83.88, 37, 'Prioritas 1', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-04-16 09:00:00', '2026-04-16 09:00:00', NULL),
(39, 15, 4, '2026-04-18', 12.15, 85.81, 34, 'Prioritas 2', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-04-18 09:00:00', '2026-04-18 09:00:00', NULL),
(40, 35, 5, '2026-04-26', 5.77, 57.26, 5, 'Prioritas 1', 'Perlu dukungan pangan tambahan bulan ini.', '2026-04-26 09:00:00', '2026-04-26 09:00:00', NULL),
(41, 4, 6, '2026-04-28', 18.51, 110.7, 98, 'Prioritas 1', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-04-28 09:00:00', '2026-04-28 09:00:00', NULL),
(42, 22, 4, '2026-04-28', 14.3, 89.37, 39, 'Prioritas 3', 'Perlu dukungan pangan tambahan bulan ini.', '2026-04-28 09:00:00', '2026-04-28 09:00:00', NULL),
(43, 30, 5, '2026-05-06', 6.21, 58.61, 5, 'Prioritas 2', 'Orang tua diminta rutin membawa anak ke posyandu.', '2026-05-06 09:00:00', '2026-05-06 09:00:00', NULL),
(44, 7, 5, '2026-05-08', 9.89, 76.94, 21, 'Prioritas 2', 'Perlu dukungan pangan tambahan bulan ini.', '2026-05-08 09:00:00', '2026-05-08 09:00:00', NULL),
(45, 35, 5, '2026-05-09', 5.9, 58.91, 6, 'Prioritas 1', 'Kondisi gizi membaik dibanding pemeriksaan sebelumnya.', '2026-05-09 09:00:00', '2026-05-09 09:00:00', NULL),
(46, 24, 6, '2026-05-16', 18.26, 109.33, 88, 'Prioritas 2', 'Orang tua diminta rutin membawa anak ke posyandu.', '2026-05-16 09:00:00', '2026-05-16 09:00:00', NULL),
(47, 38, 4, '2026-05-17', 6.83, 62.43, 6, 'Prioritas 3', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-05-17 09:00:00', '2026-05-17 09:00:00', NULL),
(48, 25, 6, '2026-05-27', 10.72, 79.16, 22, 'Prioritas 3', 'Perlu dukungan pangan tambahan bulan ini.', '2026-05-27 09:00:00', '2026-05-27 09:00:00', NULL),
(49, 33, 4, '2026-05-28', 13.5, 87.61, 45, 'Prioritas 1', 'Pertumbuhan dalam pemantauan rutin posyandu.', '2026-05-28 09:00:00', '2026-05-28 09:00:00', NULL),
(50, 13, 6, '2026-05-28', 12.45, 85.1, 30, 'Prioritas 3', 'Berat dan tinggi perlu pemantauan intensif.', '2026-05-28 09:00:00', '2026-05-28 09:00:00', NULL),
(51, 19, 3, '2026-06-01', 17.66, 109.12, 94, 'Prioritas 1', 'Orang tua diminta rutin membawa anak ke posyandu.', '2026-06-01 09:00:00', '2026-06-01 09:00:00', NULL),
(52, 29, 5, '2026-06-10', 17.01, 97.77, 52, 'Prioritas 3', 'Orang tua diminta rutin membawa anak ke posyandu.', '2026-06-10 09:00:00', '2026-06-10 09:00:00', NULL),
(53, 14, 3, '2026-06-11', 14.87, 90.85, 43, 'Prioritas 3', 'Kondisi gizi membaik dibanding pemeriksaan sebelumnya.', '2026-06-11 09:00:00', '2026-06-11 09:00:00', NULL),
(54, 32, 3, '2026-06-15', 7.64, 63.1, 7, 'Prioritas 3', 'Orang tua diminta rutin membawa anak ke posyandu.', '2026-06-15 09:00:00', '2026-06-15 09:00:00', NULL),
(55, 11, 4, '2026-06-18', 22.01, 116.92, 113, 'Prioritas 2', 'Pertumbuhan dalam pemantauan rutin posyandu.', '2026-06-18 09:00:00', '2026-06-18 09:00:00', NULL),
(56, 33, 4, '2026-06-22', 13.15, 88.28, 46, 'Prioritas 1', 'Kondisi gizi membaik dibanding pemeriksaan sebelumnya.', '2026-06-22 09:00:00', '2026-06-22 09:00:00', NULL),
(57, 15, 4, '2026-06-23', 12.68, 86.21, 36, 'Prioritas 2', 'Kondisi gizi membaik dibanding pemeriksaan sebelumnya.', '2026-06-23 09:00:00', '2026-06-23 09:00:00', NULL),
(58, 9, 3, '2026-06-24', 7.01, 62.44, 6, 'Prioritas 3', 'Orang tua diminta rutin membawa anak ke posyandu.', '2026-06-24 09:00:00', '2026-06-24 09:00:00', NULL),
(59, 1, 3, '2026-06-26', 13.21, 88.0, 41, 'Prioritas 2', 'Kondisi gizi membaik dibanding pemeriksaan sebelumnya.', '2026-06-26 09:00:00', '2026-06-26 09:00:00', NULL),
(60, 26, 3, '2026-06-30', 17.18, 105.21, 89, 'Prioritas 1', 'Masuk kategori prioritas untuk bantuan tambahan.', '2026-06-30 09:00:00', '2026-06-30 09:00:00', NULL);

-- -------------------------------------------------------------
-- 10. T_PENGADAAN (insert status_bayar awal = 'Pending' semua,
--     lalu di-UPDATE ke 'Lunas' supaya trigger stok benar-benar
--     jalan -- bukan stok yang dikarang manual)
-- -------------------------------------------------------------
INSERT INTO `t_pengadaan` (`id_pengadaan`, `id_petani`, `id_gudang`, `no_kontrak`, `tgl_pengadaan`, `total_bayar`, `status_bayar`, `status_kontrak`, `keterangan`, `tgl_created`, `tgl_updated`) VALUES
(2, 7, 1, 'REQ-202601-002', '2026-01-24', 5450715.0, 'Pending', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-01-24 08:30:00', '2026-01-24 08:30:00'),
(15, 8, 1, 'REQ-202601-015', '2026-01-25', 8257740.0, 'Pending', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-01-25 08:30:00', '2026-01-25 08:30:00'),
(1, 8, 1, 'REQ-202601-001', '2026-01-26', 25482550.0, 'Pending', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-01-26 08:30:00', '2026-01-26 08:30:00'),
(13, NULL, 1, 'REQ-202601-013', '2026-01-27', 6885510.0, 'Pending', 'Mencari Petani', 'Lowongan pengadaan terbuka, menunggu petani mitra.', '2026-01-27 08:30:00', '2026-01-27 08:30:00'),
(24, 7, 1, 'REQ-202602-024', '2026-02-08', 675520.0, 'Pending', 'Dibatalkan', 'Kontrak dibatalkan karena kendala pasokan.', '2026-02-08 08:30:00', '2026-02-08 08:30:00'),
(6, NULL, 1, 'REQ-202602-006', '2026-02-09', 3685400.0, 'Pending', 'Mencari Petani', 'Lowongan pengadaan terbuka, menunggu petani mitra.', '2026-02-09 08:30:00', '2026-02-09 08:30:00'),
(21, NULL, 1, 'REQ-202602-021', '2026-02-11', 271310.0, 'Pending', 'Mencari Petani', 'Lowongan pengadaan terbuka, menunggu petani mitra.', '2026-02-11 08:30:00', '2026-02-11 08:30:00'),
(28, 8, 1, 'REQ-202602-028', '2026-02-15', 1552600.0, 'Pending', 'Dibatalkan', 'Kontrak dibatalkan karena kendala pasokan.', '2026-02-15 08:30:00', '2026-02-15 08:30:00'),
(30, 7, 1, 'REQ-202602-030', '2026-02-16', 384020.0, 'Pending', 'Disetujui', 'Kontrak disetujui, menunggu pelunasan pembayaran.', '2026-02-16 08:30:00', '2026-02-16 08:30:00'),
(3, 7, 1, 'REQ-202602-003', '2026-02-23', 18118855.0, 'Pending', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-02-23 08:30:00', '2026-02-23 08:30:00'),
(8, 7, 1, 'REQ-202603-008', '2026-03-02', 670410.0, 'Pending', 'Disetujui', 'Kontrak disetujui, menunggu pelunasan pembayaran.', '2026-03-02 08:30:00', '2026-03-02 08:30:00'),
(14, 8, 1, 'REQ-202603-014', '2026-03-04', 1166480.0, 'Pending', 'Dibatalkan', 'Kontrak dibatalkan karena kendala pasokan.', '2026-03-04 08:30:00', '2026-03-04 08:30:00'),
(12, 8, 1, 'REQ-202603-012', '2026-03-16', 23686820.0, 'Pending', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-03-16 08:30:00', '2026-03-16 08:30:00'),
(26, 7, 1, 'REQ-202603-026', '2026-03-27', 2299520.0, 'Pending', 'Disetujui', 'Kontrak disetujui, menunggu pelunasan pembayaran.', '2026-03-27 08:30:00', '2026-03-27 08:30:00'),
(7, 8, 1, 'REQ-202603-007', '2026-03-31', 2281600.0, 'Pending', 'Disetujui', 'Kontrak disetujui, menunggu pelunasan pembayaran.', '2026-03-31 08:30:00', '2026-03-31 08:30:00'),
(18, 7, 1, 'REQ-202604-018', '2026-04-11', 25075585.0, 'Pending', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-04-11 08:30:00', '2026-04-11 08:30:00'),
(11, 8, 1, 'REQ-202604-011', '2026-04-13', 27663780.0, 'Pending', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-04-13 08:30:00', '2026-04-13 08:30:00'),
(27, 8, 1, 'REQ-202604-027', '2026-04-19', 567990.0, 'Pending', 'Disetujui', 'Kontrak disetujui, menunggu pelunasan pembayaran.', '2026-04-19 08:30:00', '2026-04-19 08:30:00'),
(9, 7, 1, 'REQ-202604-009', '2026-04-22', 2017025.0, 'Pending', 'Dibatalkan', 'Kontrak dibatalkan karena kendala pasokan.', '2026-04-22 08:30:00', '2026-04-22 08:30:00'),
(23, 7, 1, 'REQ-202604-023', '2026-04-29', 8543155.0, 'Pending', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-04-29 08:30:00', '2026-04-29 08:30:00'),
(16, 7, 1, 'REQ-202605-016', '2026-05-01', 7529280.0, 'Pending', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-05-01 08:30:00', '2026-05-01 08:30:00'),
(20, 8, 1, 'REQ-202605-020', '2026-05-11', 4116740.0, 'Pending', 'Dibatalkan', 'Kontrak dibatalkan karena kendala pasokan.', '2026-05-11 08:30:00', '2026-05-11 08:30:00'),
(10, 8, 1, 'REQ-202605-010', '2026-05-12', 3263966.0, 'Pending', 'Disetujui', 'Kontrak disetujui, menunggu pelunasan pembayaran.', '2026-05-12 08:30:00', '2026-05-12 08:30:00'),
(17, NULL, 1, 'REQ-202605-017', '2026-05-23', 4190860.0, 'Pending', 'Mencari Petani', 'Lowongan pengadaan terbuka, menunggu petani mitra.', '2026-05-23 08:30:00', '2026-05-23 08:30:00'),
(4, NULL, 1, 'REQ-202605-004', '2026-05-31', 6655320.0, 'Pending', 'Mencari Petani', 'Lowongan pengadaan terbuka, menunggu petani mitra.', '2026-05-31 08:30:00', '2026-05-31 08:30:00'),
(5, 7, 1, 'REQ-202606-005', '2026-06-02', 16655875.0, 'Pending', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-06-02 08:30:00', '2026-06-02 08:30:00'),
(25, 8, 1, 'REQ-202606-025', '2026-06-02', 41707370.0, 'Pending', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-06-02 08:30:00', '2026-06-02 08:30:00'),
(19, 8, 1, 'REQ-202606-019', '2026-06-06', 13318470.0, 'Pending', 'Disetujui', 'Pengadaan disetujui dan pembayaran telah lunas.', '2026-06-06 08:30:00', '2026-06-06 08:30:00'),
(22, 8, 1, 'REQ-202606-022', '2026-06-07', 5461560.0, 'Pending', 'Disetujui', 'Kontrak disetujui, menunggu pelunasan pembayaran.', '2026-06-07 08:30:00', '2026-06-07 08:30:00'),
(29, NULL, 1, 'REQ-202606-029', '2026-06-16', 325765.0, 'Pending', 'Mencari Petani', 'Lowongan pengadaan terbuka, menunggu petani mitra.', '2026-06-16 08:30:00', '2026-06-16 08:30:00');

INSERT INTO `t_pengadaan_detail` (`id_pengadaan_detail`, `id_pengadaan`, `id_komoditas`, `jumlah`, `harga_satuan`) VALUES
(1, 1, 18, 416.52, 6500),
(2, 1, 29, 357.72, 32000),
(3, 1, 12, 315.61, 9000),
(4, 1, 28, 165.63, 28000),
(5, 2, 22, 149.91, 10000),
(6, 2, 13, 393.13, 5500),
(7, 2, 3, 124.6, 6500),
(8, 2, 16, 163.25, 6000),
(9, 3, 9, 314.95, 26000),
(10, 3, 13, 375.53, 5500),
(11, 3, 14, 375.7, 8000),
(12, 4, 28, 136.49, 28000),
(13, 4, 29, 88.55, 32000),
(14, 5, 3, 344.03, 6500),
(15, 5, 8, 250.19, 24000),
(16, 5, 9, 149.53, 26000),
(17, 5, 22, 310.12, 10000),
(18, 6, 9, 35.52, 26000),
(19, 6, 1, 125.54, 22000),
(20, 7, 12, 59.3, 9000),
(21, 7, 6, 79.45, 22000),
(22, 8, 3, 103.14, 6500),
(23, 9, 22, 57.86, 10000),
(24, 9, 5, 106.55, 13500),
(25, 10, 2, 111.31, 2600),
(26, 10, 18, 48.96, 6500),
(27, 10, 10, 83.01, 32000),
(28, 11, 12, 373.59, 9000),
(29, 11, 29, 179.77, 32000),
(30, 11, 10, 235.41, 32000),
(31, 11, 18, 264.96, 6500),
(32, 12, 11, 126.58, 18000),
(33, 12, 27, 369.01, 22000),
(34, 12, 10, 238.74, 32000),
(35, 12, 28, 122.43, 28000),
(36, 13, 30, 102.85, 55000),
(37, 13, 7, 72.28, 17000),
(38, 14, 28, 41.66, 28000),
(39, 15, 2, 339.71, 2600),
(40, 15, 27, 147.04, 22000),
(41, 15, 11, 208.53, 18000),
(42, 16, 5, 382.94, 13500),
(43, 16, 17, 229.76, 7000),
(44, 17, 9, 146.09, 26000),
(45, 17, 16, 65.42, 6000),
(46, 18, 3, 378.04, 6500),
(47, 18, 5, 337.27, 13500),
(48, 18, 8, 413.98, 24000),
(49, 19, 30, 124.18, 55000),
(50, 19, 12, 280.97, 9000),
(51, 20, 27, 128.69, 22000),
(52, 20, 12, 142.84, 9000),
(53, 21, 2, 104.35, 2600),
(54, 22, 7, 106.25, 17000),
(55, 22, 28, 85.71, 28000),
(56, 22, 4, 114.13, 11000),
(57, 23, 1, 211.31, 22000),
(58, 23, 23, 267.79, 4500),
(59, 24, 14, 84.44, 8000),
(60, 25, 29, 316.48, 32000),
(61, 25, 30, 370.11, 55000),
(62, 25, 6, 304.7, 22000),
(63, 25, 12, 401.72, 9000),
(64, 26, 13, 83.36, 5500),
(65, 26, 8, 58.33, 24000),
(66, 26, 16, 73.52, 6000),
(67, 27, 12, 63.11, 9000),
(68, 28, 28, 55.45, 28000),
(69, 29, 13, 59.23, 5500),
(70, 30, 17, 54.86, 7000),
(71, 3, 1, 138.69, 22000),
(72, 1, 4, 350.0, 11000),
(73, 18, 1, 126.39, 22000),
(74, 11, 2, 111.95, 2600),
(75, 25, 4, 82.28, 11000),
(76, 5, 5, 105.64, 13500),
(77, 11, 6, 215.34, 22000),
(78, 3, 1, 82.18, 22000),
(79, 19, 2, 180.0, 2600),
(80, 11, 4, 159.21, 11000),
(81, 12, 6, 101.02, 22000),
(82, 23, 1, 122.24, 22000),
(83, 15, 2, 148.49, 2600),
(84, 19, 6, 158.72, 22000),
(85, 18, 1, 139.19, 22000),
(86, 16, 3, 115.58, 6500),
(87, 11, 4, 228.51, 11000),
(88, 18, 5, 169.4, 13500);

-- Pelunasan pembayaran -> trigger `after_update_lunas_pengadaan` otomatis mengisi stok_log
UPDATE `t_pengadaan` SET `status_bayar` = 'Lunas' WHERE `id_pengadaan` IN (1,2,3,5,11,12,15,16,18,19,23,25);

-- -------------------------------------------------------------
-- 11. T_DISTRIBUSI (insert status awal 'Dikirim' untuk yang akan
--     'Diterima', lalu di-UPDATE supaya trigger stok jalan)
-- -------------------------------------------------------------
INSERT INTO `t_distribusi` (`id_distribusi`, `no_distribusi`, `id_gudang_asal`, `id_gudang_tujuan`, `tanggal_distribusi`, `status_distribusi`, `catatan`, `created_by`, `received_by`, `received_at`, `tgl_created`, `tgl_updated`, `deleted_at`) VALUES
(28, 'DIST-20260214-028', 1, 4, '2026-02-14', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-02-14 07:30:00', '2026-02-14 07:30:00', NULL),
(16, 'DIST-20260226-016', 1, 4, '2026-02-26', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-02-26 07:30:00', '2026-02-26 07:30:00', NULL),
(26, 'DIST-20260303-026', 1, 3, '2026-03-03', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 2, NULL, NULL, '2026-03-03 07:30:00', '2026-03-03 07:30:00', NULL),
(3, 'DIST-20260309-003', 1, 5, '2026-03-09', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-03-09 07:30:00', '2026-03-09 07:30:00', NULL),
(9, 'DIST-20260312-009', 1, 4, '2026-03-12', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-03-12 07:30:00', '2026-03-12 07:30:00', NULL),
(18, 'DIST-20260315-018', 1, 5, '2026-03-15', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 2, NULL, NULL, '2026-03-15 07:30:00', '2026-03-15 07:30:00', NULL),
(7, 'DIST-20260318-007', 1, 3, '2026-03-18', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 2, NULL, NULL, '2026-03-18 07:30:00', '2026-03-18 07:30:00', NULL),
(21, 'DIST-20260323-021', 1, 5, '2026-03-23', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 2, NULL, NULL, '2026-03-23 07:30:00', '2026-03-23 07:30:00', NULL),
(20, 'DIST-20260331-020', 2, 5, '2026-03-31', 'Dibatalkan', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-03-31 07:30:00', '2026-03-31 07:30:00', NULL),
(6, 'DIST-20260419-006', 1, 2, '2026-04-19', 'Dibatalkan', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-04-19 07:30:00', '2026-04-19 07:30:00', NULL),
(13, 'DIST-20260424-013', 1, 4, '2026-04-24', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-04-24 07:30:00', '2026-04-24 07:30:00', NULL),
(19, 'DIST-20260426-019', 1, 2, '2026-04-26', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 2, NULL, NULL, '2026-04-26 07:30:00', '2026-04-26 07:30:00', NULL),
(12, 'DIST-20260428-012', 1, 5, '2026-04-28', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 2, NULL, NULL, '2026-04-28 07:30:00', '2026-04-28 07:30:00', NULL),
(29, 'DIST-20260430-029', 1, 4, '2026-04-30', 'Dibatalkan', 'Distribusi rutin kebutuhan pangan posyandu.', 2, NULL, NULL, '2026-04-30 07:30:00', '2026-04-30 07:30:00', NULL),
(10, 'DIST-20260504-010', 1, 2, '2026-05-04', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-05-04 07:30:00', '2026-05-04 07:30:00', NULL),
(5, 'DIST-20260505-005', 1, 2, '2026-05-05', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 2, NULL, NULL, '2026-05-05 07:30:00', '2026-05-05 07:30:00', NULL),
(30, 'DIST-20260505-030', 1, 2, '2026-05-05', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-05-05 07:30:00', '2026-05-05 07:30:00', NULL),
(17, 'DIST-20260506-017', 2, 3, '2026-05-06', 'Dibatalkan', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-05-06 07:30:00', '2026-05-06 07:30:00', NULL),
(11, 'DIST-20260510-011', 1, 4, '2026-05-10', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 2, NULL, NULL, '2026-05-10 07:30:00', '2026-05-10 07:30:00', NULL),
(15, 'DIST-20260520-015', 1, 4, '2026-05-20', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-05-20 07:30:00', '2026-05-20 07:30:00', NULL),
(27, 'DIST-20260520-027', 1, 2, '2026-05-20', 'Dibatalkan', 'Distribusi rutin kebutuhan pangan posyandu.', 2, NULL, NULL, '2026-05-20 07:30:00', '2026-05-20 07:30:00', NULL),
(24, 'DIST-20260527-024', 1, 2, '2026-05-27', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-05-27 07:30:00', '2026-05-27 07:30:00', NULL),
(4, 'DIST-20260531-004', 1, 3, '2026-05-31', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-05-31 07:30:00', '2026-05-31 07:30:00', NULL),
(23, 'DIST-20260601-023', 1, 3, '2026-06-01', 'Dibatalkan', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-06-01 07:30:00', '2026-06-01 07:30:00', NULL),
(1, 'DIST-20260611-001', 1, 2, '2026-06-11', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-06-11 07:30:00', '2026-06-11 07:30:00', NULL),
(8, 'DIST-20260614-008', 1, 5, '2026-06-14', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 2, NULL, NULL, '2026-06-14 07:30:00', '2026-06-14 07:30:00', NULL),
(22, 'DIST-20260622-022', 2, 2, '2026-06-22', 'Dibatalkan', 'Distribusi rutin kebutuhan pangan posyandu.', 2, NULL, NULL, '2026-06-22 07:30:00', '2026-06-22 07:30:00', NULL),
(2, 'DIST-20260623-002', 1, 2, '2026-06-23', 'Dibatalkan', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-06-23 07:30:00', '2026-06-23 07:30:00', NULL),
(14, 'DIST-20260624-014', 1, 5, '2026-06-24', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 2, NULL, NULL, '2026-06-24 07:30:00', '2026-06-24 07:30:00', NULL),
(25, 'DIST-20260625-025', 1, 3, '2026-06-25', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-06-25 07:30:00', '2026-06-25 07:30:00', NULL),
(31, 'DIST-20260628-031', 1, 2, '2026-06-28', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-06-28 07:30:00', '2026-06-28 07:30:00', NULL),
(32, 'DIST-20260628-032', 1, 3, '2026-06-28', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-06-28 07:30:00', '2026-06-28 07:30:00', NULL),
(33, 'DIST-20260628-033', 1, 4, '2026-06-28', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-06-28 07:30:00', '2026-06-28 07:30:00', NULL),
(34, 'DIST-20260628-034', 1, 5, '2026-06-28', 'Dikirim', 'Distribusi rutin kebutuhan pangan posyandu.', 1, NULL, NULL, '2026-06-28 07:30:00', '2026-06-28 07:30:00', NULL);

INSERT INTO `t_distribusi_detail` (`id_distribusi_detail`, `id_distribusi`, `id_komoditas`, `jumlah`) VALUES
(1, 1, 22, 4.31),
(2, 1, 27, 9.05),
(3, 2, 2, 14.98),
(4, 3, 3, 143.79),
(5, 3, 6, 88.47),
(6, 3, 2, 108.9),
(7, 3, 8, 186.09),
(8, 4, 1, 82.73),
(9, 4, 5, 215.28),
(10, 4, 6, 48.55),
(11, 4, 30, 87.01),
(12, 5, 2, 39.92),
(13, 5, 1, 54.98),
(14, 5, 5, 98.58),
(15, 5, 14, 88.6),
(16, 6, 23, 6.71),
(17, 6, 25, 7.08),
(18, 7, 6, 30.43),
(19, 7, 5, 111.16),
(20, 7, 3, 200.18),
(21, 7, 9, 94.95),
(22, 8, 6, 28.86),
(23, 8, 1, 40.81),
(24, 8, 5, 52.38),
(25, 8, 13, 154.58),
(26, 9, 3, 133.05),
(27, 9, 5, 70.88),
(28, 9, 1, 40.05),
(29, 9, 12, 237.38),
(30, 10, 6, 27.32),
(31, 10, 2, 32.99),
(32, 10, 1, 33.12),
(33, 10, 16, 54.07),
(34, 11, 4, 113.0),
(35, 11, 3, 101.82),
(36, 11, 1, 17.71),
(37, 11, 8, 78.24),
(38, 12, 2, 3.89),
(39, 12, 13, 8.54),
(40, 12, 22, 3.45),
(41, 13, 3, 67.2),
(42, 13, 6, 14.96),
(43, 13, 2, 31.51),
(44, 13, 9, 87.78),
(45, 14, 13, 7.47),
(46, 15, 30, 3.63),
(47, 15, 28, 10.57),
(48, 16, 30, 6.02),
(49, 16, 14, 13.22),
(50, 16, 11, 14.57),
(51, 17, 30, 7.2),
(52, 18, 6, 22.45),
(53, 18, 2, 31.25),
(54, 18, 5, 33.58),
(55, 18, 30, 72.3),
(56, 19, 4, 71.21),
(57, 19, 2, 29.19),
(58, 19, 3, 39.48),
(59, 19, 28, 56.99),
(60, 20, 15, 6.38),
(61, 20, 25, 7.58),
(62, 21, 5, 10.69),
(63, 21, 10, 13.49),
(64, 21, 2, 9.65),
(65, 22, 3, 4.92),
(66, 22, 25, 8.39),
(67, 23, 27, 8.41),
(68, 24, 6, 10.47),
(69, 24, 1, 12.17),
(70, 24, 4, 27.5),
(71, 24, 14, 57.76),
(72, 25, 5, 28.81),
(73, 25, 4, 20.79),
(74, 25, 1, 15.09),
(75, 25, 11, 88.51),
(76, 26, 3, 9.53),
(77, 26, 22, 12.44),
(78, 27, 21, 7.64),
(79, 28, 4, 18.49),
(80, 28, 6, 6.32),
(81, 28, 5, 33.76),
(82, 28, 11, 80.68),
(83, 29, 9, 12.04),
(84, 29, 2, 5.29),
(85, 30, 1, 13.82),
(86, 30, 2, 14.13),
(87, 30, 6, 4.46),
(88, 30, 28, 65.2),
(89, 31, 1, 65.91),
(90, 31, 2, 63.77),
(91, 31, 3, 140.52),
(92, 31, 4, 81.29),
(93, 31, 5, 81.42),
(94, 31, 6, 137.75),
(95, 32, 1, 82.18),
(96, 32, 2, 180.0),
(97, 32, 4, 159.21),
(98, 32, 6, 101.02),
(99, 33, 1, 122.24),
(100, 33, 2, 148.49),
(101, 33, 4, 48.51),
(102, 33, 5, 75.36),
(103, 33, 6, 158.72),
(104, 34, 1, 139.19),
(105, 34, 2, 39.85),
(106, 34, 3, 36.21),
(107, 34, 4, 180.0),
(108, 34, 5, 94.04),
(109, 34, 6, 40.22);

-- Penerimaan distribusi -> trigger `trg_t_distribusi_after_terima` otomatis mengisi stok_log
UPDATE `t_distribusi` SET `status_distribusi` = 'Diterima', `received_by` = 5, `received_at` = '2026-02-14 10:00:00' WHERE `id_distribusi` = 28;
UPDATE `t_distribusi` SET `status_distribusi` = 'Diterima', `received_by` = 6, `received_at` = '2026-03-09 10:00:00' WHERE `id_distribusi` = 3;
UPDATE `t_distribusi` SET `status_distribusi` = 'Diterima', `received_by` = 5, `received_at` = '2026-03-12 10:00:00' WHERE `id_distribusi` = 9;
UPDATE `t_distribusi` SET `status_distribusi` = 'Diterima', `received_by` = 6, `received_at` = '2026-03-15 10:00:00' WHERE `id_distribusi` = 18;
UPDATE `t_distribusi` SET `status_distribusi` = 'Diterima', `received_by` = 4, `received_at` = '2026-03-18 10:00:00' WHERE `id_distribusi` = 7;
UPDATE `t_distribusi` SET `status_distribusi` = 'Diterima', `received_by` = 5, `received_at` = '2026-04-24 10:00:00' WHERE `id_distribusi` = 13;
UPDATE `t_distribusi` SET `status_distribusi` = 'Diterima', `received_by` = 3, `received_at` = '2026-04-26 10:00:00' WHERE `id_distribusi` = 19;
UPDATE `t_distribusi` SET `status_distribusi` = 'Diterima', `received_by` = 3, `received_at` = '2026-05-04 10:00:00' WHERE `id_distribusi` = 10;
UPDATE `t_distribusi` SET `status_distribusi` = 'Diterima', `received_by` = 3, `received_at` = '2026-05-05 10:00:00' WHERE `id_distribusi` = 5;
UPDATE `t_distribusi` SET `status_distribusi` = 'Diterima', `received_by` = 3, `received_at` = '2026-05-05 10:00:00' WHERE `id_distribusi` = 30;
UPDATE `t_distribusi` SET `status_distribusi` = 'Diterima', `received_by` = 5, `received_at` = '2026-05-10 10:00:00' WHERE `id_distribusi` = 11;
UPDATE `t_distribusi` SET `status_distribusi` = 'Diterima', `received_by` = 3, `received_at` = '2026-05-27 10:00:00' WHERE `id_distribusi` = 24;
UPDATE `t_distribusi` SET `status_distribusi` = 'Diterima', `received_by` = 4, `received_at` = '2026-05-31 10:00:00' WHERE `id_distribusi` = 4;
UPDATE `t_distribusi` SET `status_distribusi` = 'Diterima', `received_by` = 6, `received_at` = '2026-06-14 10:00:00' WHERE `id_distribusi` = 8;
UPDATE `t_distribusi` SET `status_distribusi` = 'Diterima', `received_by` = 4, `received_at` = '2026-06-25 10:00:00' WHERE `id_distribusi` = 25;
UPDATE `t_distribusi` SET `status_distribusi` = 'Diterima', `received_by` = 3, `received_at` = '2026-06-28 10:00:00' WHERE `id_distribusi` = 31;
UPDATE `t_distribusi` SET `status_distribusi` = 'Diterima', `received_by` = 4, `received_at` = '2026-06-28 10:00:00' WHERE `id_distribusi` = 32;
UPDATE `t_distribusi` SET `status_distribusi` = 'Diterima', `received_by` = 5, `received_at` = '2026-06-28 10:00:00' WHERE `id_distribusi` = 33;
UPDATE `t_distribusi` SET `status_distribusi` = 'Diterima', `received_by` = 6, `received_at` = '2026-06-28 10:00:00' WHERE `id_distribusi` = 34;

-- -------------------------------------------------------------
-- 12. T_PENYERAHAN (insert status awal 'Diproses' untuk yang akan
--     'Diserahkan', lalu di-UPDATE supaya trigger stok jalan)
-- -------------------------------------------------------------
INSERT INTO `t_penyerahan` (`id_penyerahan`, `no_penyerahan`, `id_ibu`, `id_anak`, `id_gudang`, `id_paket`, `tanggal_penyerahan`, `status_penyerahan`, `catatan`, `created_by`, `tgl_created`, `tgl_updated`, `deleted_at`) VALUES
(18, 'PNY-20260403092532-568', 21, 14, 2, 3, '2026-04-03', 'Diproses', 'Penyerahan paket gizi bulanan.', 3, '2026-04-03 09:15:00', '2026-04-03 09:15:00', NULL),
(6, 'PNY-20260404090241-889', 23, 16, 4, 3, '2026-04-04', 'Diproses', 'Bantuan tambahan untuk mendukung pertumbuhan anak.', 5, '2026-04-04 09:15:00', '2026-04-04 09:15:00', NULL),
(34, 'PNY-20260407151033-744', 28, 25, 5, 3, '2026-04-07', 'Diproses', '', 6, '2026-04-07 09:15:00', '2026-04-07 09:15:00', NULL),
(38, 'PNY-20260408103313-674', 34, 33, 3, 1, '2026-04-08', 'Dibatalkan', 'Bantuan tambahan untuk mendukung pertumbuhan anak.', 4, '2026-04-08 09:15:00', '2026-04-08 09:15:00', NULL),
(25, 'PNY-20260417092523-913', 18, 10, 3, 3, '2026-04-17', 'Diproses', 'Bantuan tambahan untuk mendukung pertumbuhan anak.', 4, '2026-04-17 09:15:00', '2026-04-17 09:15:00', NULL),
(35, 'PNY-20260419110236-817', 25, 19, 2, 1, '2026-04-19', 'Diproses', 'Bantuan rutin sesuai skala prioritas anak.', 3, '2026-04-19 09:15:00', '2026-04-19 09:15:00', NULL),
(10, 'PNY-20260421152630-575', 20, 13, 5, 3, '2026-04-21', 'Diproses', 'Penyerahan paket gizi bulanan.', 6, '2026-04-21 09:15:00', '2026-04-21 09:15:00', NULL),
(36, 'PNY-20260425082820-528', 29, 26, 2, 1, '2026-04-25', 'Diproses', 'Penyerahan paket gizi bulanan.', 3, '2026-04-25 09:15:00', '2026-04-25 09:15:00', NULL),
(19, 'PNY-20260428130303-388', 26, 22, 3, 3, '2026-04-28', 'Diproses', '', 4, '2026-04-28 09:15:00', '2026-04-28 09:15:00', NULL),
(14, 'PNY-20260502100603-397', 30, 28, 3, 2, '2026-05-02', 'Diproses', '', 4, '2026-05-02 09:15:00', '2026-05-02 09:15:00', NULL),
(1, 'PNY-20260509101020-826', 15, 7, 4, 2, '2026-05-09', 'Diproses', 'Penyerahan paket gizi bulanan.', 5, '2026-05-09 09:15:00', '2026-05-09 09:15:00', NULL),
(13, 'PNY-20260511112907-373', 32, 31, 5, 2, '2026-05-11', 'Diproses', '', 6, '2026-05-11 09:15:00', '2026-05-11 09:15:00', NULL),
(29, 'PNY-20260512131025-731', 23, 17, 4, 3, '2026-05-12', 'Dibatalkan', 'Bantuan tambahan untuk mendukung pertumbuhan anak.', 5, '2026-05-12 09:15:00', '2026-05-12 09:15:00', NULL),
(32, 'PNY-20260513092637-415', 25, 20, 2, 3, '2026-05-13', 'Diproses', 'Bantuan rutin sesuai skala prioritas anak.', 3, '2026-05-13 09:15:00', '2026-05-13 09:15:00', NULL),
(11, 'PNY-20260514102055-835', 36, 36, 5, 1, '2026-05-14', 'Diproses', 'Bantuan tambahan untuk mendukung pertumbuhan anak.', 6, '2026-05-14 09:15:00', '2026-05-14 09:15:00', NULL),
(2, 'PNY-20260515125405-356', 22, 15, 3, 2, '2026-05-15', 'Diproses', 'Penyerahan paket gizi bulanan.', 4, '2026-05-15 09:15:00', '2026-05-15 09:15:00', NULL),
(12, 'PNY-20260515140848-479', 14, 6, 3, 2, '2026-05-15', 'Dibatalkan', 'Bantuan rutin sesuai skala prioritas anak.', 4, '2026-05-15 09:15:00', '2026-05-15 09:15:00', NULL),
(23, 'PNY-20260517120424-616', 9, 1, 2, 2, '2026-05-17', 'Diproses', '', 3, '2026-05-17 09:15:00', '2026-05-17 09:15:00', NULL),
(22, 'PNY-20260522130225-152', 35, 34, 4, 2, '2026-05-22', 'Diproses', 'Penyerahan paket gizi bulanan.', 5, '2026-05-22 09:15:00', '2026-05-22 09:15:00', NULL),
(37, 'PNY-20260523112632-893', 33, 32, 2, 3, '2026-05-23', 'Dibatalkan', '', 3, '2026-05-23 09:15:00', '2026-05-23 09:15:00', NULL),
(21, 'PNY-20260526105616-844', 17, 9, 2, 3, '2026-05-26', 'Diproses', 'Bantuan tambahan untuk mendukung pertumbuhan anak.', 3, '2026-05-26 09:15:00', '2026-05-26 09:15:00', NULL),
(40, 'PNY-20260529133422-792', 24, 18, 5, 2, '2026-05-29', 'Diproses', 'Bantuan tambahan untuk mendukung pertumbuhan anak.', 6, '2026-05-29 09:15:00', '2026-05-29 09:15:00', NULL),
(28, 'PNY-20260531150938-627', 11, 3, 4, 3, '2026-05-31', 'Diproses', 'Penyerahan paket gizi bulanan.', 5, '2026-05-31 09:15:00', '2026-05-31 09:15:00', NULL),
(8, 'PNY-20260603124119-594', 13, 5, 2, 3, '2026-06-03', 'Diproses', 'Penyerahan paket gizi bulanan.', 3, '2026-06-03 09:15:00', '2026-06-03 09:15:00', NULL),
(4, 'PNY-20260604103737-257', 18, 11, 3, 2, '2026-06-04', 'Diproses', 'Penyerahan paket gizi bulanan.', 4, '2026-06-04 09:15:00', '2026-06-04 09:15:00', NULL),
(27, 'PNY-20260604080202-240', 35, 35, 4, 1, '2026-06-04', 'Diproses', 'Bantuan tambahan untuk mendukung pertumbuhan anak.', 5, '2026-06-04 09:15:00', '2026-06-04 09:15:00', NULL),
(17, 'PNY-20260605153156-415', 23, 16, 4, 3, '2026-06-05', 'Diproses', '', 5, '2026-06-05 09:15:00', '2026-06-05 09:15:00', NULL),
(24, 'PNY-20260610125239-796', 29, 27, 2, 3, '2026-06-10', 'Diproses', 'Bantuan rutin sesuai skala prioritas anak.', 3, '2026-06-10 09:15:00', '2026-06-10 09:15:00', NULL),
(26, 'PNY-20260611134809-303', 37, 37, 2, 1, '2026-06-11', 'Diproses', '', 3, '2026-06-11 09:15:00', '2026-06-11 09:15:00', NULL),
(16, 'PNY-20260613131248-263', 31, 29, 4, 3, '2026-06-13', 'Diproses', '', 5, '2026-06-13 09:15:00', '2026-06-13 09:15:00', NULL),
(30, 'PNY-20260615133253-621', 19, 12, 4, 2, '2026-06-15', 'Diproses', '', 5, '2026-06-15 09:15:00', '2026-06-15 09:15:00', NULL),
(20, 'PNY-20260616151834-108', 16, 8, 5, 1, '2026-06-16', 'Diproses', 'Bantuan rutin sesuai skala prioritas anak.', 6, '2026-06-16 09:15:00', '2026-06-16 09:15:00', NULL),
(33, 'PNY-20260616151641-901', 12, 4, 5, 1, '2026-06-16', 'Diproses', 'Penyerahan paket gizi bulanan.', 6, '2026-06-16 09:15:00', '2026-06-16 09:15:00', NULL),
(15, 'PNY-20260618141555-263', 27, 23, 4, 3, '2026-06-18', 'Dibatalkan', 'Bantuan tambahan untuk mendukung pertumbuhan anak.', 5, '2026-06-18 09:15:00', '2026-06-18 09:15:00', NULL),
(3, 'PNY-20260621120840-410', 28, 24, 5, 2, '2026-06-21', 'Diproses', 'Bantuan rutin sesuai skala prioritas anak.', 6, '2026-06-21 09:15:00', '2026-06-21 09:15:00', NULL),
(7, 'PNY-20260623114936-526', 15, 7, 4, 2, '2026-06-23', 'Diproses', 'Bantuan rutin sesuai skala prioritas anak.', 5, '2026-06-23 09:15:00', '2026-06-23 09:15:00', NULL),
(5, 'PNY-20260624135359-677', 31, 30, 4, 2, '2026-06-24', 'Diproses', 'Bantuan rutin sesuai skala prioritas anak.', 5, '2026-06-24 09:15:00', '2026-06-24 09:15:00', NULL),
(39, 'PNY-20260624153324-421', 10, 2, 3, 2, '2026-06-24', 'Dibatalkan', 'Penyerahan paket gizi bulanan.', 4, '2026-06-24 09:15:00', '2026-06-24 09:15:00', NULL),
(9, 'PNY-20260627141929-174', 26, 21, 3, 1, '2026-06-27', 'Diproses', 'Bantuan rutin sesuai skala prioritas anak.', 4, '2026-06-27 09:15:00', '2026-06-27 09:15:00', NULL),
(31, 'PNY-20260630123052-117', 38, 38, 3, 3, '2026-06-30', 'Diproses', 'Bantuan tambahan untuk mendukung pertumbuhan anak.', 4, '2026-06-30 09:15:00', '2026-06-30 09:15:00', NULL);

INSERT INTO `t_penyerahan_detail` (`id_penyerahan_detail`, `id_penyerahan`, `id_komoditas`, `jumlah`) VALUES
(1, 1, 1, 1.0),
(2, 1, 2, 8.0),
(3, 1, 3, 1.0),
(4, 1, 5, 3.0),
(5, 1, 6, 1.0),
(6, 2, 1, 1.0),
(7, 2, 2, 8.0),
(8, 2, 3, 1.0),
(9, 2, 5, 3.0),
(10, 2, 6, 1.0),
(11, 3, 1, 1.0),
(12, 3, 2, 8.0),
(13, 3, 3, 1.0),
(14, 3, 5, 3.0),
(15, 3, 6, 1.0),
(16, 4, 1, 1.0),
(17, 4, 2, 8.0),
(18, 4, 3, 1.0),
(19, 4, 5, 3.0),
(20, 4, 6, 1.0),
(21, 5, 1, 1.0),
(22, 5, 2, 8.0),
(23, 5, 3, 1.0),
(24, 5, 5, 3.0),
(25, 5, 6, 1.0),
(26, 6, 5, 2.0),
(27, 6, 3, 1.0),
(28, 6, 2, 4.0),
(29, 7, 1, 1.0),
(30, 7, 2, 8.0),
(31, 7, 3, 1.0),
(32, 7, 5, 3.0),
(33, 7, 6, 1.0),
(34, 8, 5, 2.0),
(35, 8, 3, 1.0),
(36, 8, 2, 4.0),
(37, 9, 5, 5.0),
(38, 9, 1, 1.5),
(39, 9, 3, 2.0),
(40, 9, 2, 10.0),
(41, 9, 4, 1.0),
(42, 10, 5, 2.0),
(43, 10, 3, 1.0),
(44, 10, 2, 4.0),
(45, 11, 5, 5.0),
(46, 11, 1, 1.5),
(47, 11, 3, 2.0),
(48, 11, 2, 10.0),
(49, 11, 4, 1.0),
(50, 12, 1, 1.0),
(51, 12, 2, 8.0),
(52, 12, 3, 1.0),
(53, 12, 5, 3.0),
(54, 12, 6, 1.0),
(55, 13, 1, 1.0),
(56, 13, 2, 8.0),
(57, 13, 3, 1.0),
(58, 13, 5, 3.0),
(59, 13, 6, 1.0),
(60, 14, 1, 1.0),
(61, 14, 2, 8.0),
(62, 14, 3, 1.0),
(63, 14, 5, 3.0),
(64, 14, 6, 1.0),
(65, 15, 5, 2.0),
(66, 15, 3, 1.0),
(67, 15, 2, 4.0),
(68, 16, 5, 2.0),
(69, 16, 3, 1.0),
(70, 16, 2, 4.0),
(71, 17, 5, 2.0),
(72, 17, 3, 1.0),
(73, 17, 2, 4.0),
(74, 18, 5, 2.0),
(75, 18, 3, 1.0),
(76, 18, 2, 4.0),
(77, 19, 5, 2.0),
(78, 19, 3, 1.0),
(79, 19, 2, 4.0),
(80, 20, 5, 5.0),
(81, 20, 1, 1.5),
(82, 20, 3, 2.0),
(83, 20, 2, 10.0),
(84, 20, 4, 1.0),
(85, 21, 5, 2.0),
(86, 21, 3, 1.0),
(87, 21, 2, 4.0),
(88, 22, 1, 1.0),
(89, 22, 2, 8.0),
(90, 22, 3, 1.0),
(91, 22, 5, 3.0),
(92, 22, 6, 1.0),
(93, 23, 1, 1.0),
(94, 23, 2, 8.0),
(95, 23, 3, 1.0),
(96, 23, 5, 3.0),
(97, 23, 6, 1.0),
(98, 24, 5, 2.0),
(99, 24, 3, 1.0),
(100, 24, 2, 4.0),
(101, 25, 5, 2.0),
(102, 25, 3, 1.0),
(103, 25, 2, 4.0),
(104, 26, 5, 5.0),
(105, 26, 1, 1.5),
(106, 26, 3, 2.0),
(107, 26, 2, 10.0),
(108, 26, 4, 1.0),
(109, 27, 5, 5.0),
(110, 27, 1, 1.5),
(111, 27, 3, 2.0),
(112, 27, 2, 10.0),
(113, 27, 4, 1.0),
(114, 28, 5, 2.0),
(115, 28, 3, 1.0),
(116, 28, 2, 4.0),
(117, 29, 5, 2.0),
(118, 29, 3, 1.0),
(119, 29, 2, 4.0),
(120, 30, 1, 1.0),
(121, 30, 2, 8.0),
(122, 30, 3, 1.0),
(123, 30, 5, 3.0),
(124, 30, 6, 1.0),
(125, 31, 5, 2.0),
(126, 31, 3, 1.0),
(127, 31, 2, 4.0),
(128, 32, 5, 2.0),
(129, 32, 3, 1.0),
(130, 32, 2, 4.0),
(131, 33, 5, 5.0),
(132, 33, 1, 1.5),
(133, 33, 3, 2.0),
(134, 33, 2, 10.0),
(135, 33, 4, 1.0),
(136, 34, 5, 2.0),
(137, 34, 3, 1.0),
(138, 34, 2, 4.0),
(139, 35, 5, 5.0),
(140, 35, 1, 1.5),
(141, 35, 3, 2.0),
(142, 35, 2, 10.0),
(143, 35, 4, 1.0),
(144, 36, 5, 5.0),
(145, 36, 1, 1.5),
(146, 36, 3, 2.0),
(147, 36, 2, 10.0),
(148, 36, 4, 1.0),
(149, 37, 5, 2.0),
(150, 37, 3, 1.0),
(151, 37, 2, 4.0),
(152, 38, 5, 5.0),
(153, 38, 1, 1.5),
(154, 38, 3, 2.0),
(155, 38, 2, 10.0),
(156, 38, 4, 1.0),
(157, 39, 1, 1.0),
(158, 39, 2, 8.0),
(159, 39, 3, 1.0),
(160, 39, 5, 3.0),
(161, 39, 6, 1.0),
(162, 40, 1, 1.0),
(163, 40, 2, 8.0),
(164, 40, 3, 1.0),
(165, 40, 5, 3.0),
(166, 40, 6, 1.0);

-- Penyerahan selesai -> trigger `trg_t_penyerahan_after_diserahkan` otomatis mengisi stok_log
UPDATE `t_penyerahan` SET `status_penyerahan` = 'Diserahkan' WHERE `id_penyerahan` IN (18,6,34,35,10,19,1,32,2,23,22,40,28,27,17,26,16,30,20,33,3,7,5,31);

-- -------------------------------------------------------------
-- 13. Samakan AUTO_INCREMENT dengan data yang baru dimasukkan
-- -------------------------------------------------------------
ALTER TABLE `gudang` AUTO_INCREMENT = 6;
ALTER TABLE `users` AUTO_INCREMENT = 39;
ALTER TABLE `petani_lokal` AUTO_INCREMENT = 9;
ALTER TABLE `komoditas_pangan` AUTO_INCREMENT = 31;
ALTER TABLE `petani_lahan_komoditas` AUTO_INCREMENT = 25;
ALTER TABLE `anak` AUTO_INCREMENT = 39;
ALTER TABLE `t_pemeriksaan` AUTO_INCREMENT = 61;
ALTER TABLE `t_pengadaan` AUTO_INCREMENT = 31;
ALTER TABLE `t_pengadaan_detail` AUTO_INCREMENT = 89;
ALTER TABLE `t_distribusi` AUTO_INCREMENT = 35;
ALTER TABLE `t_distribusi_detail` AUTO_INCREMENT = 110;
ALTER TABLE `t_penyerahan` AUTO_INCREMENT = 41;
ALTER TABLE `t_penyerahan_detail` AUTO_INCREMENT = 167;

SET FOREIGN_KEY_CHECKS=1;
COMMIT;
