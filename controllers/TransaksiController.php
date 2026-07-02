<?php

// buat transaksi pengadaan (petani ke gudang pusat)
require_once '../models/Pengadaan.php';
require_once '../models/Distribusi.php';

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

            //----------------------------------------
            // lowongan tersedia
            //----------------------------------------

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

            //----------------------------------------
            // Kontrak yng udah diambil petani
            //----------------------------------------
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
        require '../views/transaksi/pengadaan/pengadaan.php';
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

    // Fungsi menampilkan halaman form input pengadaan baru (Sisi Admin)
    public function buatPengadaan()
    {
        // Proteksi Hak Akses: Hanya Admin yang boleh masuk ke halaman ini
        if (empty($_SESSION['user_id']) || strtolower($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: /transaksi/pengadaan');
            exit;
        }

        global $koneksi;

        // Ambil data gudang pusat untuk bahan pilihan select option di form view
        $ambilGudang = mysqli_query($koneksi, "SELECT id_gudang, nama_gudang FROM gudang WHERE jenis_gudang = 'Pusat' ORDER BY nama_gudang ASC");
        $data['daftar_gudang'] = mysqli_fetch_all($ambilGudang, MYSQLI_ASSOC);

        // Ambil data komoditas pangan untuk bahan pilihan barang yang akan diminta
        $ambilKomoditas = mysqli_query($koneksi, "SELECT id_komoditas, nama_komoditas FROM komoditas_pangan ORDER BY nama_komoditas ASC");
        $data['daftar_komoditas'] = mysqli_fetch_all($ambilKomoditas, MYSQLI_ASSOC);

        // buat nampilin komoditas (satuan) saat buat pengadaan
        $queryKomoditas = "
            SELECT 
                k.id_komoditas, 
                k.nama_komoditas,
                s.singkat AS nama_satuan 
            FROM komoditas_pangan k
            JOIN satuan s ON k.id_satuan = s.id_satuan
            ORDER BY k.nama_komoditas ASC
        ";

        $ambilKomoditas = mysqli_query($koneksi, $queryKomoditas);
        $data['daftar_komoditas'] = mysqli_fetch_all($ambilKomoditas, MYSQLI_ASSOC);

        // Panggil file tampilan UI Form Admin
        require '../views/transaksi/pengadaan/buatPengadaan.php';
    }

    // Fungsi memproses kiriman data dari form dinamis (Sisi Admin)
    public function simpanPengadaan()
    {
        // Validasi metode pengiriman data
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user_id'])) {
            header('Location: /transaksi/pengadaan');
            exit;
        }

        // Ikat data utama ke dalam paket array Header
        $paketDataHeader = [
            'id_gudang' => (int) ($_POST['id_gudang'] ?? 0),
            'tanggal_pengadaan' => $_POST['tgl_pengadaan'] ?? date('Y-m-d'),
            'keterangan' => $_POST['keterangan'] ?? null
        ];

        // Ikat array dinamis ke dalam paket array Detail (sesuai input name="...")
        $paketDataDetail = [
            'id_komoditas' => $_POST['id_komoditas'] ?? [],
            'jumlah' => $_POST['jumlah'] ?? [],
            'harga_satuan' => $_POST['harga_satuan'] ?? []
        ];

        // Validasi awal di level controller sebelum masuk ke mesin model
        if ($paketDataHeader['id_gudang'] <= 0 || empty($paketDataDetail['id_komoditas'])) {
            $_SESSION['error'] = 'Gudang wajib dipilih dan minimal harus mengisi satu baris komoditas pangan.';
            header('Location: /transaksi/pengadaan/buat');
            exit;
        }

        $pengadaanModel = new Pengadaan();

        // Kirimkan kedua paket data ke dalam fungsi model bermesin transaksi
        if ($pengadaanModel->simpanPengadaanBaru($paketDataHeader, $paketDataDetail)) {
            $_SESSION['success'] = 'Berhasil mempublikasikan lowongan kontrak pengadaan pangan baru untuk mitra petani.';
            header('Location: /transaksi/pengadaan');
        } else {
            // Jika gagal, pesan kesalahan otomatis tertangkap dari exception model
            header('Location: /transaksi/pengadaan/buat');
        }
        exit;
    }

    // Menampilkan lembar rincian nota pengadaan (Invoice View)
    public function detailPengadaan()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $id_pengadaan = (int) ($_GET['id'] ?? 0);
        $pengadaanModel = new Pengadaan();
        $nota = $pengadaanModel->find($id_pengadaan);

        if (empty($nota)) {
            $_SESSION['error'] = 'Data kontrak pengadaan tidak ditemukan.';
            header('Location: /transaksi/pengadaan');
            exit;
        }

        require '../views/transaksi/pengadaan/detailPengadaan.php';
    }

    public function lunasiPengadaan()
    {
        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST' ||
            empty($_SESSION['user_id']) ||
            strtolower($_SESSION['role'] ?? '') !== 'admin'
        ) {
            header('Location: /transaksi/pengadaan');
            exit;
        }

        $idPengadaan = (int) ($_POST['id_pengadaan'] ?? 0);

        if ($idPengadaan <= 0) {
            $_SESSION['error'] = 'ID transaksi tidak valid.';
            header('Location: /transaksi/pengadaan');
            exit;
        }

        $pengadaanModel = new Pengadaan();

        if ($pengadaanModel->prosesPelunasanKontrak($idPengadaan)) {
            $_SESSION['success'] = 'Kontrak pengadaan telah diverifikasi fisik dan status pembayaran berhasil diubah menjadi Lunas.';
        } else {
            $_SESSION['error'] = 'Gagal memperbarui status pembayaran. Pastikan kontrak sudah disetujui, masih Pending, dan memiliki detail komoditas.';
        }

        header('Location: /transaksi/pengadaan/detail?id=' . $idPengadaan);
        exit;
    }


    //----------------------------------------
    // --- TRANSAKSI PENYERAHAN ---
    //----------------------------------------
    public function penyerahan()
    {
        $penyerahanModel = new Penyerahan();

        $penyerahan = $penyerahanModel->all();

        require '../views/transaksi/penyerahan/index.php';
    }

    public function createPenyerahan()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $penyerahanModel = new Penyerahan();
        $paketModel = new PaketGizi();

        $ibus = $penyerahanModel->getIbuOptions();
        $anaks = $penyerahanModel->getAnakOptions();
        $gudangs = $penyerahanModel->getGudangPosyanduOptions();

        $pakets = array_values(array_filter(
            $paketModel->getAllWithDetails(),
            function ($paket) {
                return !empty($paket['is_active']);
            }
        ));

        require '../views/transaksi/penyerahan/create.php';
    }

    public function storePenyerahan()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /transaksi/penyerahan/create');
            exit;
        }

        $idIbu = (int) ($_POST['id_ibu'] ?? 0);
        $idAnak = (int) ($_POST['id_anak'] ?? 0);
        $idGudang = (int) ($_POST['id_gudang'] ?? 0);
        $tanggalPenyerahan = $_POST['tanggal_penyerahan'] ?? '';
        $catatan = trim($_POST['catatan'] ?? '');

        if ($idIbu <= 0 || $idAnak <= 0 || $idGudang <= 0 || $tanggalPenyerahan === '') {
            $_SESSION['error'] = 'Data penyerahan belum lengkap.';
            header('Location: /transaksi/penyerahan/create');
            exit;
        }

        try {
            $penyerahanModel = new Penyerahan();

            $idPenyerahan = $penyerahanModel->createFromPrioritasAnak(
                $idIbu,
                $idAnak,
                $idGudang,
                $tanggalPenyerahan,
                $catatan
            );

            if ($idPenyerahan <= 0) {
                throw new Exception('Gagal membuat penyerahan berdasarkan prioritas anak.');
            }

            $_SESSION['success'] = 'Penyerahan bantuan berhasil dibuat berdasarkan prioritas anak.';
            header('Location: /transaksi/penyerahan');
            exit;
        } catch (Throwable $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /transaksi/penyerahan/create');
            exit;
        }
    }

    public function detailPenyerahan()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $idPenyerahan = (int) ($_GET['id'] ?? 0);

        if ($idPenyerahan <= 0) {
            $_SESSION['error'] = 'ID penyerahan tidak valid.';
            header('Location: /transaksi/penyerahan');
            exit;
        }

        $penyerahanModel = new Penyerahan();
        $penyerahan = $penyerahanModel->getDetailById($idPenyerahan);

        if (!$penyerahan) {
            $_SESSION['error'] = 'Data penyerahan tidak ditemukan.';
            header('Location: /transaksi/penyerahan');
            exit;
        }

        $role = $_SESSION['role'] ?? '';

        if ($role === ROLE_KADER) {
            $userModel = new User();
            $currentUser = $userModel->findByid($_SESSION['user_id']);
            $idGudangUser = (int) ($currentUser['id_gudang'] ?? 0);

            if ((int) ($penyerahan['id_gudang'] ?? 0) !== $idGudangUser) {
                $_SESSION['error'] = 'Anda tidak memiliki akses ke detail penyerahan ini.';
                header('Location: /transaksi/penyerahan');
                exit;
            }
        }

        if ($role === ROLE_IBU) {
            if ((int) ($penyerahan['id_ibu'] ?? 0) !== (int) $_SESSION['user_id']) {
                $_SESSION['error'] = 'Anda tidak memiliki akses ke detail bantuan ini.';
                header('Location: /master/ibu/histori-bantuan');
                exit;
            }
        }

        require '../views/transaksi/penyerahan/detail.php';
    }

    public function serahkanPenyerahan()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user_id'])) {
            header('Location: /transaksi/penyerahan');
            exit;
        }

        $role = $_SESSION['role'] ?? '';
        $isKader = defined('ROLE_KADER') ? $role === ROLE_KADER : strtolower($role) === 'kader';

        if (!$isKader) {
            $_SESSION['error'] = 'Anda tidak memiliki akses untuk memperbarui penyerahan.';
            header('Location: /transaksi/penyerahan');
            exit;
        }

        $idPenyerahan = (int) ($_POST['id_penyerahan'] ?? 0);

        if ($idPenyerahan <= 0) {
            $_SESSION['error'] = 'ID penyerahan tidak valid.';
            header('Location: /transaksi/penyerahan');
            exit;
        }

        $penyerahanModel = new Penyerahan();
        $penyerahan = $penyerahanModel->getDetailById($idPenyerahan);

        if (!$penyerahan) {
            $_SESSION['error'] = 'Data penyerahan tidak ditemukan.';
            header('Location: /transaksi/penyerahan');
            exit;
        }

        $userModel = new User();
        $currentUser = $userModel->findByid($_SESSION['user_id']);
        $idGudangUser = (int) ($currentUser['id_gudang'] ?? 0);

        if ((int) ($penyerahan['id_gudang'] ?? 0) !== $idGudangUser) {
            $_SESSION['error'] = 'Anda tidak memiliki akses ke penyerahan ini.';
            header('Location: /transaksi/penyerahan');
            exit;
        }

        if (($penyerahan['status_penyerahan'] ?? '') === 'Diserahkan') {
            $_SESSION['success'] = 'Penyerahan ini sudah berstatus Diserahkan.';
            header('Location: /transaksi/penyerahan');
            exit;
        }

        try {
            if ($penyerahanModel->markAsDiserahkan($idPenyerahan)) {
                $_SESSION['success'] = 'Penyerahan berhasil ditandai sebagai Diserahkan.';
            } else {
                $_SESSION['error'] = 'Gagal memperbarui status penyerahan.';
            }
        } catch (Throwable $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: /transaksi/penyerahan');
        exit;
    }

    //----------------------------------------
    //--- TRANSAKSI DISTRIBUSI ---
    //----------------------------------------

    public function distribusi()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $role = $_SESSION['role'] ?? '';

        $isAdmin = defined('ROLE_ADMIN')
            ? $role === ROLE_ADMIN
            : strtolower($role) === 'admin';

        $isKader = defined('ROLE_KADER')
            ? $role === ROLE_KADER
            : strtolower($role) === 'kader';

        if (!$isAdmin && !$isKader) {
            header('Location: /dashboard');
            exit;
        }

        $distribusiModel = new Distribusi();

        $limit = 20;
        $search = trim($_GET['q'] ?? '');

        $totalDataAwal = $distribusiModel->countAll();

        $allDistribusi = $totalDataAwal > 0
            ? $distribusiModel->getPaginated($totalDataAwal, 0)
            : [];

        $allDistribusi = array_map(function ($d) {
            $detailSearch = '';

            foreach (($d['details'] ?? []) as $det) {
                $detailSearch .= ' ' . ($det['nama_komoditas'] ?? '');
                $detailSearch .= ' ' . ($det['jumlah'] ?? '');
                $detailSearch .= ' ' . ($det['satuan'] ?? '');
            }

            $d['detail_search'] = trim($detailSearch);

            return $d;
        }, $allDistribusi);

        if ($isKader) {
            $userModel = new User();
            $currentUser = $userModel->findByid($_SESSION['user_id']);

            $idGudangUser = (int) ($currentUser['id_gudang'] ?? 0);

            $allDistribusi = array_values(array_filter($allDistribusi, function ($d) use ($idGudangUser) {
                $status = $d['status_distribusi'] ?? '';

                return (int) ($d['id_gudang_tujuan'] ?? 0) === $idGudangUser
                    && in_array($status, ['Dikirim', 'Diterima'], true);
            }));
        }

        $searchedDistribusi = SearchHelper::searchArray($allDistribusi, $search, [
            'no_distribusi',
            'gudang_asal',
            'gudang_tujuan',
            'detail_search',
            'tanggal_distribusi',
            'status_distribusi',
            'dibuat_oleh',
            'diterima_oleh'
        ]);

        $pagination = PaginationHelper::paginateArray($searchedDistribusi, $limit);

        $data['distribusi'] = $pagination['data'];
        $data['current_page'] = $pagination['halaman_aktif'];
        $data['total_pages'] = $pagination['total_halaman'];
        $data['total_data'] = $pagination['total_data'];
        $data['per_halaman'] = $pagination['per_halaman'];
        $data['search'] = $search;

        require '../views/transaksi/distribusi/index.php';
    }

    public function createDistribusi()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $role = $_SESSION['role'] ?? '';
        $isAdmin = defined('ROLE_ADMIN') ? $role === ROLE_ADMIN : strtolower($role) === 'admin';

        if (!$isAdmin) {
            header('Location: /dashboard');
            exit;
        }

        $distribusiModel = new Distribusi();

        $data['gudang_pusat'] = $distribusiModel->getGudangByJenis('pusat');
        $data['gudang_posyandu'] = $distribusiModel->getGudangByJenis('posyandu');
        $data['komoditas'] = $distribusiModel->getKomoditasOptions();

        require '../views/transaksi/distribusi/create.php';
    }

    public function storeDistribusi()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user_id'])) {
            header('Location: /transaksi/distribusi');
            exit;
        }

        $role = $_SESSION['role'] ?? '';
        $isAdmin = defined('ROLE_ADMIN') ? $role === ROLE_ADMIN : strtolower($role) === 'admin';

        if (!$isAdmin) {
            header('Location: /dashboard');
            exit;
        }

        $idGudangAsal = (int) ($_POST['id_gudang_asal'] ?? 0);
        $idGudangTujuan = (int) ($_POST['id_gudang_tujuan'] ?? 0);
        $tanggalDistribusi = $_POST['tanggal_distribusi'] ?? '';
        $catatan = trim($_POST['catatan'] ?? '');

        $komoditasIds = $_POST['id_komoditas'] ?? [];
        $jumlahs = $_POST['jumlah'] ?? [];

        if ($idGudangAsal <= 0 || $idGudangTujuan <= 0 || empty($tanggalDistribusi)) {
            $_SESSION['error'] = 'Gudang asal, gudang tujuan, dan tanggal distribusi wajib diisi.';
            header('Location: /transaksi/distribusi/create');
            exit;
        }

        if ($idGudangAsal === $idGudangTujuan) {
            $_SESSION['error'] = 'Gudang asal dan gudang tujuan tidak boleh sama.';
            header('Location: /transaksi/distribusi/create');
            exit;
        }

        $distribusiModel = new Distribusi();

        $gudangAsal = $distribusiModel->findGudangById($idGudangAsal);
        $gudangTujuan = $distribusiModel->findGudangById($idGudangTujuan);

        if (!$gudangAsal || !$gudangTujuan) {
            $_SESSION['error'] = 'Gudang asal atau gudang tujuan tidak valid.';
            header('Location: /transaksi/distribusi/create');
            exit;
        }

        $jenisAsal = strtolower($gudangAsal['jenis_gudang'] ?? '');
        $jenisTujuan = strtolower($gudangTujuan['jenis_gudang'] ?? '');

        if ($jenisAsal !== 'pusat') {
            $_SESSION['error'] = 'Gudang asal harus berjenis Pusat / Puskesmas.';
            header('Location: /transaksi/distribusi/create');
            exit;
        }

        if ($jenisTujuan !== 'posyandu') {
            $_SESSION['error'] = 'Gudang tujuan harus berjenis Posyandu.';
            header('Location: /transaksi/distribusi/create');
            exit;
        }

        $details = [];

        foreach ($komoditasIds as $index => $idKomoditas) {
            $idKomoditas = (int) $idKomoditas;
            $jumlah = (float) ($jumlahs[$index] ?? 0);

            if ($idKomoditas <= 0 || $jumlah <= 0) {
                continue;
            }

            $details[] = [
                'id_komoditas' => $idKomoditas,
                'jumlah' => $jumlah
            ];
        }

        if (empty($details)) {
            $_SESSION['error'] = 'Minimal harus ada 1 komoditas distribusi.';
            header('Location: /transaksi/distribusi/create');
            exit;
        }

        $data = [
            'no_distribusi' => $distribusiModel->generateNoDistribusi(),
            'id_gudang_asal' => $idGudangAsal,
            'id_gudang_tujuan' => $idGudangTujuan,
            'tanggal_distribusi' => $tanggalDistribusi,
            'status_distribusi' => 'Dikirim',
            'catatan' => $catatan,
            'created_by' => $_SESSION['user_id']
        ];

        if ($distribusiModel->createWithDetails($data, $details)) {
            $_SESSION['success'] = 'Distribusi stok berhasil dibuat.';
            header('Location: /transaksi/distribusi');
            exit;
        }

        $_SESSION['error'] = 'Gagal membuat distribusi stok.';
        header('Location: /transaksi/distribusi/create');
        exit;
    }

    public function terimaDistribusi()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user_id'])) {
            header('Location: /transaksi/distribusi');
            exit;
        }

        $role = $_SESSION['role'] ?? '';
        $isKader = defined('ROLE_KADER') ? $role === ROLE_KADER : strtolower($role) === 'kader';

        if (!$isKader) {
            $_SESSION['error'] = 'Hanya kader yang dapat menerima distribusi.';
            header('Location: /transaksi/distribusi');
            exit;
        }

        $idDistribusi = (int) ($_POST['id_distribusi'] ?? 0);

        if ($idDistribusi <= 0) {
            $_SESSION['error'] = 'Data distribusi tidak valid.';
            header('Location: /transaksi/distribusi');
            exit;
        }

        $distribusiModel = new Distribusi();

        try {
            if ($distribusiModel->markAsReceived($idDistribusi, $_SESSION['user_id'])) {
                $_SESSION['success'] = 'Distribusi berhasil ditandai sebagai diterima.';
            } else {
                $_SESSION['error'] = 'Gagal menerima distribusi. Kemungkinan status sudah bukan Dikirim.';
            }
        } catch (mysqli_sql_exception $e) {
            $_SESSION['error'] = $e->getMessage();
        } catch (Throwable $e) {
            $_SESSION['error'] = 'Terjadi kesalahan saat menerima distribusi.';
        }

        header('Location: /transaksi/distribusi');
        exit;
    }

    public function batalDistribusi()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user_id'])) {
            header('Location: /transaksi/distribusi');
            exit;
        }

        $role = $_SESSION['role'] ?? '';
        $isAdmin = defined('ROLE_ADMIN') ? $role === ROLE_ADMIN : strtolower($role) === 'admin';

        if (!$isAdmin) {
            $_SESSION['error'] = 'Hanya admin yang dapat membatalkan distribusi.';
            header('Location: /transaksi/distribusi');
            exit;
        }

        $idDistribusi = (int) ($_POST['id_distribusi'] ?? 0);

        if ($idDistribusi <= 0) {
            $_SESSION['error'] = 'Data distribusi tidak valid.';
            header('Location: /transaksi/distribusi');
            exit;
        }

        $distribusiModel = new Distribusi();

        if ($distribusiModel->cancelDistribusi($idDistribusi)) {
            $_SESSION['success'] = 'Distribusi berhasil dibatalkan.';
        } else {
            $_SESSION['error'] = 'Gagal membatalkan distribusi. Kemungkinan status sudah bukan Dikirim.';
        }

        header('Location: /transaksi/distribusi');
        exit;
    }

    public function pemeriksaan()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $role = $_SESSION['role'] ?? '';
        $isAdmin = defined('ROLE_ADMIN') ? $role === ROLE_ADMIN : strtolower($role) === 'admin';
        $isKader = defined('ROLE_KADER') ? $role === ROLE_KADER : strtolower($role) === 'kader';
        $isIbu = defined('ROLE_IBU') ? $role === ROLE_IBU : strtolower($role) === 'ibu';

        if (!$isAdmin && !$isKader && !$isIbu) {
            header('Location: /dashboard');
            exit;
        }

        $pemeriksaanModel = new Pemeriksaan();
        $userModel = new User();

        $id_gudang = null;
        $id_ibu = null;
        if ($isKader) {
            $currentUser = $userModel->findByid($_SESSION['user_id']);
            $id_gudang = $currentUser['id_gudang'] ?? null;
        } elseif ($isIbu) {
            $id_ibu = $_SESSION['user_id'];
        }

        $limit = 20;
        $search = trim($_GET['q'] ?? '');

        $totalDataAwal = $pemeriksaanModel->countAll($id_gudang, $id_ibu);

        $allPemeriksaan = $totalDataAwal > 0
            ? $pemeriksaanModel->getPaginated($totalDataAwal, 0, $id_gudang, $id_ibu)
            : [];

        $allPemeriksaan = array_map(function ($p) {
            $p['berat_badan_label'] = $p['berat_badan'] !== null ? number_format($p['berat_badan'], 1, ',', '.') . ' Kg' : '-';
            $p['tinggi_badan_label'] = $p['tinggi_badan'] !== null ? number_format($p['tinggi_badan'], 1, ',', '.') . ' Cm' : '-';
            $p['tanggal_pemeriksaan_label'] = !empty($p['tanggal_pemeriksaan']) ? date('d/m/Y', strtotime($p['tanggal_pemeriksaan'])) : '-';
            return $p;
        }, $allPemeriksaan);

        $searchedPemeriksaan = SearchHelper::searchArray($allPemeriksaan, $search, [
            'nama_anak',
            'nama_ibu',
            'status_gizi',
            'tanggal_pemeriksaan_label',
            'nama_posyandu',
            'nama_kader'
        ]);

        $pagination = PaginationHelper::paginateArray($searchedPemeriksaan, $limit);

        $data['pemeriksaan'] = $pagination['data'];
        $data['current_page'] = $pagination['halaman_aktif'];
        $data['total_pages'] = $pagination['total_halaman'];
        $data['total_data'] = $pagination['total_data'];
        $data['per_halaman'] = $pagination['per_halaman'];
        $data['search'] = $search;
        $data['role'] = $role;

        require '../views/transaksi/pemeriksaan/index.php';
    }

    public function createPemeriksaan()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $role = $_SESSION['role'] ?? '';
        $isKader = defined('ROLE_KADER') ? $role === ROLE_KADER : strtolower($role) === 'kader';

        if (!$isKader) {
            $_SESSION['error'] = 'Hanya Kader yang dapat menambahkan data pemeriksaan.';
            header('Location: /transaksi/pemeriksaan');
            exit;
        }

        $pemeriksaanModel = new Pemeriksaan();
        $userModel = new User();

        $currentUser = $userModel->findByid($_SESSION['user_id']);
        $id_gudang = $currentUser['id_gudang'] ?? null;

        if ($id_gudang) {
            $data['anak'] = $pemeriksaanModel->getAnakByPosyandu($id_gudang);
        } else {
            $data['anak'] = [];
        }

        $data['standar_json'] = $pemeriksaanModel->getAllStandarAsJson();
        $data['anak_data'] = json_encode($data['anak']);

        require '../views/transaksi/pemeriksaan/create.php';
    }

    public function storePemeriksaan()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user_id'])) {
            header('Location: /transaksi/pemeriksaan');
            exit;
        }

        $role = $_SESSION['role'] ?? '';
        $isKader = defined('ROLE_KADER') ? $role === ROLE_KADER : strtolower($role) === 'kader';

        if (!$isKader) {
            header('Location: /transaksi/pemeriksaan');
            exit;
        }

        // Ambil id_gudang kader dari session/db
        $userModel = new User();
        $currentUser = $userModel->findByid($_SESSION['user_id']);
        $id_gudang_kader = $currentUser['id_gudang'] ?? null;

        $idAnak = (int) ($_POST['id_anak'] ?? 0);
        $tanggalPemeriksaan = $_POST['tanggal_pemeriksaan'] ?? '';
        $beratBadan = isset($_POST['berat_badan']) && $_POST['berat_badan'] !== '' ? (float) $_POST['berat_badan'] : null;
        $tinggiBadan = isset($_POST['tinggi_badan']) && $_POST['tinggi_badan'] !== '' ? (float) $_POST['tinggi_badan'] : null;
        $catatan = trim($_POST['catatan'] ?? '');

        if ($idAnak <= 0 || empty($tanggalPemeriksaan) || $beratBadan === null || $tinggiBadan === null) {
            $_SESSION['error'] = 'Semua kolom bertanda bintang wajib diisi.';
            header('Location: /transaksi/pemeriksaan/create');
            exit;
        }

        $anakModel = new Anak();
        $anak = $anakModel->findById($idAnak);
        if (!$anak) {
            $_SESSION['error'] = 'Data anak tidak valid.';
            header('Location: /transaksi/pemeriksaan/create');
            exit;
        }

        // Validasi keamanan: pastikan anak berasal dari posyandu kader ini
        if ($id_gudang_kader !== null) {
            $ibuModel = new Ibu();
            $ibuAnak = $ibuModel->findByIdIbu($anak['id_ibu']);
            if (!$ibuAnak || (int)($ibuAnak['id_gudang'] ?? 0) !== (int)$id_gudang_kader) {
                $_SESSION['error'] = 'Anda tidak memiliki akses untuk memeriksa anak dari posyandu lain.';
                header('Location: /transaksi/pemeriksaan/create');
                exit;
            }
        }
        $tglLahir = $anak['tgl_lahir'];
        $diff = date_diff(date_create($tglLahir), date_create($tanggalPemeriksaan));
        $usiaBulan = ($diff->y * 12) + $diff->m;

        $pemeriksaanModel = new Pemeriksaan();

        if ($pemeriksaanModel->existsInSameMonth($idAnak, $tanggalPemeriksaan)) {
            $_SESSION['error'] = 'Anak ini sudah melakukan pemeriksaan pada bulan yang sama. Pemeriksaan hanya boleh dilakukan satu kali setiap bulan.';
            header('Location: /transaksi/pemeriksaan/create');
            exit;
        }

        $hasil = $pemeriksaanModel->hitungStatusGizi($usiaBulan, $anak['jenis_kelamin'], $beratBadan, $tinggiBadan);
        $statusGizi = $hasil['status_gizi'];
        $skalaPrioritas = $hasil['skala_prioritas'];

        $dataPemeriksaan = [
            'id_anak' => $idAnak,
            'id_kader' => $_SESSION['user_id'],
            'tanggal_pemeriksaan' => $tanggalPemeriksaan,
            'berat_badan' => $beratBadan,
            'tinggi_badan' => $tinggiBadan,
            'usia_bulan' => $usiaBulan,
            'status_gizi' => $statusGizi,
            'skala_prioritas' => $skalaPrioritas,
            'catatan' => $catatan
        ];

        if ($pemeriksaanModel->create($dataPemeriksaan)) {
            $pemeriksaanModel->updateStatusAnak($idAnak, $statusGizi, $skalaPrioritas);

            $_SESSION['success'] = 'Data pemeriksaan anak berhasil disimpan dan status anak diperbarui.';
            header('Location: /transaksi/pemeriksaan');
            exit;
        }

        $_SESSION['error'] = 'Gagal menyimpan data pemeriksaan.';
        header('Location: /transaksi/pemeriksaan/create');
        exit;
    }

    public function deletePemeriksaan()
    {
        $isAdmin = defined('ROLE_ADMIN') ? ($_SESSION['role'] ?? '') === ROLE_ADMIN : strtolower($_SESSION['role'] ?? '') === 'admin';
        if (empty($_SESSION['user_id']) || !$isAdmin) {
            header('Location: /transaksi/pemeriksaan');
            exit;
        }

        $idPemeriksaan = isset($_GET['id']) ? (int) $_GET['id'] : null;

        if ($idPemeriksaan) {
            $pemeriksaanModel = new Pemeriksaan();
            if ($pemeriksaanModel->delete($idPemeriksaan)) {
                $_SESSION['success'] = 'Data pemeriksaan berhasil dihapus.';
            } else {
                $_SESSION['error'] = 'Gagal menghapus data pemeriksaan.';
            }
        }

        header('Location: /transaksi/pemeriksaan');
        exit;
    }

    private function getSkalaPrioritasByStatusGizi($statusGizi)
    {
        $status = strtolower(trim($statusGizi));

        if (str_contains($status, 'stunting') || str_contains($status, 'risiko')) {
            return 1;
        }

        if (str_contains($status, 'kurang')) {
            return 2;
        }

        return 3;
    }

    public function kalkulasiGizi()
    {
        header('Content-Type: application/json');

        if (empty($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $idAnak = (int) ($_POST['id_anak'] ?? 0);
        $tanggalPemeriksaan = $_POST['tanggal_pemeriksaan'] ?? '';
        $beratBadan = $_POST['berat_badan'] !== '' ? (float) $_POST['berat_badan'] : null;
        $tinggiBadan = $_POST['tinggi_badan'] !== '' ? (float) $_POST['tinggi_badan'] : null;

        if ($idAnak <= 0 || empty($tanggalPemeriksaan) || $beratBadan === null || $tinggiBadan === null) {
            echo json_encode(['error' => 'Input tidak lengkap']);
            exit;
        }

        $anakModel = new Anak();
        $anak = $anakModel->findById($idAnak);
        if (!$anak) {
            echo json_encode(['error' => 'Anak tidak ditemukan']);
            exit;
        }

        $tglLahir = $anak['tgl_lahir'];
        $diff = date_diff(date_create($tglLahir), date_create($tanggalPemeriksaan));
        $usiaBulan = ($diff->y * 12) + $diff->m;

        $pemeriksaanModel = new Pemeriksaan();
        $hasil = $pemeriksaanModel->hitungStatusGizi($usiaBulan, $anak['jenis_kelamin'], $beratBadan, $tinggiBadan);

        echo json_encode([
            'success' => true,
            'usia_bulan' => $usiaBulan,
            'status_gizi' => $hasil['status_gizi'],
            'skala_prioritas' => $hasil['skala_prioritas']
        ]);
        exit;
    }
}
