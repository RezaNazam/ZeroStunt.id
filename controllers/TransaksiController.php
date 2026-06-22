<?php

// buat transaksi pengadaan (petani ke gudang pusat)
require_once '../models/Pengadaan.php';

class TransaksiController
{
    // --- TRANSAKSI DATA CONTROLLER ---


    // --- Transaksi: Pengadaan Pangan ---

    // Fungsi menampilkan dashboard pengadaan (Mendukung Multi-Role: Admin & Petani)
    public function pengadaan()
    {
        // Validasi perlindungan sesi masuk
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $role = $_SESSION['role'] ?? '';
        $isAdmin = defined('ROLE_ADMIN') ? $role === ROLE_ADMIN : strtolower($role) === 'admin';
        $isPetani = defined('ROLE_PETANI') ? $role === ROLE_PETANI : strtolower($role) === 'petani';

        $pengadaanModel = new Pengadaan();
        $data = [];

        // Penyaluran pasokan data array sesuai peran aktif pengguna
        if ($isPetani) {
            $id_petani = $_SESSION['user_id'];
            $limit = 5;

            // --- Pagination buatyt tabel lowongan tersedia ---
            $page_avail = isset($_GET['p_avail']) ? (int) $_GET['p_avail'] : 1;
            $offset_avail = ($page_avail - 1) * $limit;

            $data['available'] = $pengadaanModel->getAvailablePaginated($limit, $offset_avail);

            $totalAvail = $pengadaanModel->countAvailable();
            $data['total_pages_avail'] = ceil($totalAvail / $limit);
            $data['current_page_avail'] = $page_avail;


            // --- Pagination baut tabel riwayar kontrak petani ---
            $page_taken = isset($_GET['p_taken']) ? (int) $_GET['p_taken'] : 1;
            $offset_taken = ($page_taken - 1) * $limit;

            $data['taken'] = $pengadaanModel->getByPetaniPaginated($id_petani, $limit, $offset_taken);

            $totalTaken = $pengadaanModel->countByPetani($id_petani);
            $data['total_pages_taken'] = ceil($totalTaken / $limit);
            $data['current_page_taken'] = $page_taken;
        } elseif ($isAdmin) {
            // Tentukan limit dan ambil halaman aktif dari URL (?page=1)
            $limit = 5;
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Panggil fungsi pagination
            $data['all_pengadaan'] = $pengadaanModel->getPaginated($limit, $offset);

            // kasih total halaman ke view untuk merender tombol Next/Prev
            $totalData = $pengadaanModel->countAll();
            $data['total_pages'] = ceil($totalData / $limit);
            $data['current_page'] = $page;
        }

        // Memanggil satu pintu tampilan visual terpadu
        require '../views/transaksi/pengadaan.php';
    }

    // Proses konfirmasi/ACC pengambilan pemenuhan komoditas pangan oleh Petani
    public function ambilPengadaan()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user_id'])) {
            header('Location: /transaksi/pengadaan');
            exit;
        }

        $idPengadaan = (int) ($_POST['id_pengadaan'] ?? 0);
        $idPetani = (int) $_SESSION['user_id'];

        if ($idPengadaan <= 0) {
            $_SESSION['error'] = 'Identifikasi transaksi pengadaan tidak valid.';
            header('Location: /transaksi/pengadaan');
            exit;
        }

        $pengadaanModel = new Pengadaan();

        if ($pengadaanModel->ambil($idPengadaan, $idPetani)) {
            $_SESSION['success'] = 'Kontrak pemenuhan komoditas berhasil Anda ambil. Segera siapkan bahan pangan.';
        } else {
            $_SESSION['error'] = 'Gagal menyetujui kontrak. Kemungkinan besar penawaran ini sudah diambil oleh petani lain.';
        }

        header('Location: /transaksi/pengadaan');
        exit;
    }
}