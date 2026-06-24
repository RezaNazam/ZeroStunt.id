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

            $searchAvail = trim($_GET['q_avail'] ?? '');
            $searchTaken = trim($_GET['q_taken'] ?? '');

            /*
    |--------------------------------------------------------------------------
    | Lowongan tersedia
    |--------------------------------------------------------------------------
    */
            $totalAvailAwal = $pengadaanModel->countAvailable();
            $allAvailable = $totalAvailAwal > 0
                ? $pengadaanModel->getAvailablePaginated($totalAvailAwal, 0)
                : [];

            $allAvailable = array_map(function ($item) {
                $detailSearch = '';

                foreach (($item['details'] ?? []) as $det) {
                    $detailSearch .= ' ' . ($det['nama_komoditas'] ?? '');
                    $detailSearch .= ' ' . ($det['jumlah'] ?? '');
                    $detailSearch .= ' ' . ($det['satuan'] ?? '');
                }

                $item['detail_search'] = trim($detailSearch);
                $item['total_bayar_label'] = 'Rp ' . number_format($item['total_bayar'] ?? 0, 0, ',', '.');

                return $item;
            }, $allAvailable);

            $searchedAvailable = SearchHelper::searchArray($allAvailable, $searchAvail, [
                'no_kontrak',
                'nama_gudang',
                'detail_search',
                'total_bayar_label'
            ]);

            $paginationAvail = PaginationHelper::paginateArray($searchedAvailable, $limit, 'p_avail');

            $data['available'] = $paginationAvail['data'];
            $data['total_pages_avail'] = $paginationAvail['total_halaman'];
            $data['current_page_avail'] = $paginationAvail['halaman_aktif'];
            $data['total_data_avail'] = $paginationAvail['total_data'];
            $data['per_halaman_avail'] = $paginationAvail['per_halaman'];
            $data['search_avail'] = $searchAvail;

            /*
    |--------------------------------------------------------------------------
    | Kontrak yang sudah diambil petani
    |--------------------------------------------------------------------------
    */
            $totalTakenAwal = $pengadaanModel->countByPetani($id_petani);
            $allTaken = $totalTakenAwal > 0
                ? $pengadaanModel->getByPetaniPaginated($id_petani, $totalTakenAwal, 0)
                : [];

            $allTaken = array_map(function ($item) {
                $detailSearch = '';

                foreach (($item['details'] ?? []) as $det) {
                    $detailSearch .= ' ' . ($det['nama_komoditas'] ?? '');
                    $detailSearch .= ' ' . ($det['jumlah'] ?? '');
                    $detailSearch .= ' ' . ($det['satuan'] ?? '');
                }

                $item['detail_search'] = trim($detailSearch);
                $item['total_bayar_label'] = 'Rp ' . number_format($item['total_bayar'] ?? 0, 0, ',', '.');

                return $item;
            }, $allTaken);

            $searchedTaken = SearchHelper::searchArray($allTaken, $searchTaken, [
                'no_kontrak',
                'detail_search',
                'total_bayar_label',
                'status_bayar'
            ]);

            $paginationTaken = PaginationHelper::paginateArray($searchedTaken, $limit, 'p_taken');

            $data['taken'] = $paginationTaken['data'];
            $data['total_pages_taken'] = $paginationTaken['total_halaman'];
            $data['current_page_taken'] = $paginationTaken['halaman_aktif'];
            $data['total_data_taken'] = $paginationTaken['total_data'];
            $data['per_halaman_taken'] = $paginationTaken['per_halaman'];
            $data['search_taken'] = $searchTaken;
        } elseif ($isAdmin) {
            $limit = 20;

            $search = trim($_GET['q'] ?? '');

            /*
            | Ambil semua data dulu
            | Karena kalau pakai getPaginated langsung, search cuma berlaku di halaman itu.
            */
            $totalDataAwal = $pengadaanModel->countAll();

            if ($totalDataAwal > 0) {
                $allPengadaan = $pengadaanModel->getPaginated($totalDataAwal, 0);
            } else {
                $allPengadaan = [];
            }

            /*
            | Bikin kolom bantu buat search
            | SearchHelper tidak bisa membaca array nested seperti details,
            | jadi details kita ubah dulu jadi string biasa.
            */
            $allPengadaan = array_map(function ($p) {
                $detailSearch = '';

                if (!empty($p['details'])) {
                    foreach ($p['details'] as $det) {
                        $detailSearch .= ' ' . ($det['nama_komoditas'] ?? '');
                        $detailSearch .= ' ' . ($det['jumlah'] ?? '');
                        $detailSearch .= ' ' . ($det['satuan'] ?? '');
                    }
                }

                $p['detail_search'] = trim($detailSearch);
                $p['total_bayar_label'] = 'Rp ' . number_format($p['total_bayar'] ?? 0, 0, ',', '.');
                $p['nama_petani_label'] = $p['nama_petani'] ?? 'Belum Ada Lowongan';

                return $p;
            }, $allPengadaan);

            $searchedPengadaan = SearchHelper::searchArray($allPengadaan, $search, [
                'no_kontrak',
                'nama_gudang',
                'detail_search',
                'nama_petani',
                'nama_petani_label',
                'total_bayar_label',
                'status_kontrak'
            ]);

            $pagination = PaginationHelper::paginateArray($searchedPengadaan, $limit);

            $data['all_pengadaan'] = $pagination['data'];

            $data['current_page'] = $pagination['halaman_aktif'];
            $data['total_pages'] = $pagination['total_halaman'];
            $data['total_data'] = $pagination['total_data'];
            $data['per_halaman'] = $pagination['per_halaman'];
            $data['page_param'] = $pagination['page_param'] ?? 'page';
            $data['search'] = $search;
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

    public function penyerahan()
    {
        $penyerahanModel = new Penyerahan();

        $penyerahan = $penyerahanModel->all();

        require '../views/transaksi/penyerahan.php';
    }

    public function storePenyerahan()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /transaksi/penyerahan');
            exit;
        }

        try {

            // =========================
            // VALIDASI INPUT WAJIB
            // =========================
            if (empty($_POST['id_ibu'])) {
                throw new Exception('Ibu wajib dipilih.');
            }

            if (empty($_POST['tanggal_penyerahan'])) {
                throw new Exception('Tanggal penyerahan wajib diisi.');
            }

            $idIbu = $_POST['id_ibu'];

            // =========================
            // AMBIL DATA ANAK
            // =========================
            $anakModel = new Anak();
            $anakList = $anakModel->findByIbu($idIbu);

            if (empty($anakList)) {
                throw new Exception('Ibu belum memiliki data anak.');
            }

            $anak = $anakList[0];

            if (empty($anak['id_anak'])) {
                throw new Exception('Data anak tidak valid.');
            }

            // =========================
            // DATA PENYERAHAN
            // =========================
            $data = [
                'id_ibu' => $idIbu,
                'id_anak' => $anak['id_anak'],
                // FIX: gudang otomatis dari ibu (biar gak error input hilang)
                'id_gudang' => $anak['id_gudang'] ?? 1,
                'tanggal_penyerahan' => $_POST['tanggal_penyerahan'],
                'catatan' => $_POST['catatan'] ?? null
            ];

            // =========================
            // SIMPAN HEADER
            // =========================
            $penyerahanModel = new Penyerahan();
            $idPenyerahan = $penyerahanModel->create($data);

            if (!$idPenyerahan) {
                throw new Exception('Gagal menyimpan data penyerahan.');
            }

            // =========================
            // DETAIL DEFAULT (TRIGGER READY)
            // =========================
            $penyerahanModel->createDetail($idPenyerahan, 2, 1.5);
            $penyerahanModel->createDetail($idPenyerahan, 3, 10);

            // =========================
            // SUCCESS
            // =========================
            $_SESSION['success'] = 'Data penyerahan berhasil disimpan.';

            header('Location: /transaksi/penyerahan');
            exit;

        } catch (Throwable $e) {

            $_SESSION['error'] = $e->getMessage();

            header('Location: /transaksi/penyerahan/create');
            exit;
        }
    }


    public function createPenyerahan()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $ibuModel = new Ibu();

        $ibus = $ibuModel->all();

        $gudangModel = new Gudang();
        
        $gudangs = $gudangModel->all();

        $anakModel = new Anak();
        
        $anaks = $anakModel->all();

        require '../views/transaksi/create_penyerahan.php';
    }

    public function serahkanPenyerahan()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            header('Location: /transaksi/penyerahan');
            exit;
        }

        $idPenyerahan = $_POST['id_penyerahan'];

        $penyerahanModel = new Penyerahan();

        try {

            if ($penyerahanModel->serahkan($idPenyerahan)) {

                $_SESSION['success'] =
                    'Penyerahan berhasil diselesaikan.';

            } else {

                $_SESSION['error'] =
                    'Gagal mengubah status penyerahan.';
            }

        } catch (Exception $e) {

            $_SESSION['error'] =
                $e->getMessage();
        }

        header('Location: /transaksi/penyerahan');
        exit;
    }

}
