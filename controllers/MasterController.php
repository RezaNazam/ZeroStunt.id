<?php

class MasterController
{

    // --- MASTER DATA ---


    // --- Master: User ---

    // fungsi tampilin semua pengguna
    public function indexUsers()
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== ROLE_ADMIN) {
            header('Location: /auth/login');
            exit;
        }

        $userModel = new User();
        $allUsers = $userModel->all();

        $search = trim($_GET['q'] ?? '');

        $allUsers = array_map(function ($user) {
            $user['status_label'] = !empty($user['is_active']) ? 'Aktif' : 'Tidak Aktif';
            return $user;
        }, $allUsers);

        $searchedUsers = SearchHelper::searchArray($allUsers, $search, [
            'id_user',
            'username',
            'role',
            'status_label'
        ]);

        $pagination = PaginationHelper::paginateArray($searchedUsers, 25);

        $users = $pagination['data'];
        $tablePagination = $pagination;

        require '../views/master/user/index.php';
    }

    // form tambah user baru (admin bisa tambah kader, admin bisa tambah admin laionnya)
    public function createUsers()
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== ROLE_ADMIN) {
            header('Location: /auth/login');
            exit;
        }

        $gudangModel = new Gudang();

        $gudangPusat = $gudangModel->getByJenis('pusat');
        $posyandus = $gudangModel->getByJenis('posyandu');

        require '../views/master/user/create.php';
    }


    // fungsi stor ke database
    public function storeUsers()
    {
        // pastikan yang login admin
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== ROLE_ADMIN) {
            header('Location: /auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /master/users/create');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $roleInput = $_POST['role'] ?? '';
        $idGudang = (int) ($_POST['id_gudang'] ?? 0);

        // simpan input lama biar kalau error, username + role tidak hilang
        $_SESSION['old'] = [
            'username' => $username,
            'role' => $roleInput
        ];

        $allowedRoles = [ROLE_ADMIN, ROLE_KADER];

        if ($username === '' || $password === '' || $confirmPassword === '' || $roleInput === '') {
            $_SESSION['error'] = 'Semua field wajib diisi.';
            header('Location: /master/users/create');
            exit;
        }

        if ($idGudang <= 0) {
            $_SESSION['error'] = 'Gudang wajib dipilih sesuai role akun.';
            header('Location: /master/users/create');
            exit;
        }

        if (!in_array($roleInput, $allowedRoles, true)) {
            $_SESSION['error'] = 'Role tidak valid.';
            header('Location: /master/users/create');
            exit;
        }

        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'Konfirmasi password tidak sesuai.';
            header('Location: /master/users/create');
            exit;
        }

        if (strlen($password) < 8) {
            $_SESSION['error'] = 'Password minimal 8 karakter.';
            header('Location: /master/users/create');
            exit;
        }

        $userModel = new User();

        if ($userModel->findByUsername($username)) {
            $_SESSION['error'] = 'Username sudah digunakan, silakan cari nama lain.';
            header('Location: /master/users/create');
            exit;
        }

        if ($userModel->create($username, $password, $roleInput, $idGudang)) {
            unset($_SESSION['old']);

            $_SESSION['success'] = "Petugas dengan peran {$roleInput} berhasil ditambahkan.";
            header('Location: /master/users');
            exit;
        }

        $_SESSION['error'] = 'Gagal menyimpan data petugas.';
        header('Location: /master/users/create');
        exit;
    }

    // nampilin form edit user
    public function editUsers()
    {
        // pastiin yg login admin
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
            header('Location: /auth/login');
            exit;
        }

        // Ambil ID User dari parameter URL (?id=...)
        $id_user = isset($_GET['id']) ? (int) $_GET['id'] : null;
        if (!$id_user) {
            header('Location: /master/users');
            exit;
        }

        $userModel = new User();
        $user = $userModel->findById($id_user);

        if (!$user) {
            $_SESSION['error'] = 'Data petugas tidak ditemukan.';
            header('Location: /master/users');
            exit;
        }

        $gudangModel = new Gudang();
        $allGudang = $gudangModel->all();
        $posyandus = array_filter($allGudang, function ($g) {
            return strtolower($g['jenis_gudang'] ?? '') === 'posyandu';
        });

        require '../views/master/user/edit.php';
    }

    // proses updatdata user
    public function updateUsers()
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
            header('Location: /auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_user = (int) ($_POST['id_user'] ?? 0);
            $username = trim($_POST['username'] ?? '');
            $roleInput = $_POST['role'] ?? '';

            $userModel = new User();
            $userLama = $userModel->findById($id_user);

            $idGudang = $userLama['id_gudang'];

            if (
                $userLama['role'] === 'Kader'
                && isset($_POST['id_gudang'])
                && $_POST['id_gudang'] !== ''
            ) {
                $idGudang = (int)$_POST['id_gudang'];
            }

            $password = $_POST['password'] ?? '';

            if ($password !== '') {
                // Validasi panjang password
                $passwordLength = strlen($password);

                if ($passwordLength < 8) {
                    $_SESSION['error'] = 'Password minimal harus terdiri dari 8 karakter.';
                    header("Location: /master/users/edit?id={$id_user}");
                    exit;
                }

                if ($passwordLength > 64) {
                    $_SESSION['error'] = 'Password maksimal terdiri dari 64 karakter.';
                    header("Location: /master/users/edit?id={$id_user}");
                    exit;
                }

                // Password harus mengandung huruf dan angka
                $hasLetter = preg_match('/[a-zA-Z]/', $password);
                $hasNumber = preg_match('/[0-9]/', $password);

                if (!$hasLetter || !$hasNumber) {
                    $_SESSION['error'] = 'Password harus mengandung kombinasi huruf dan angka.';
                    header("Location: /master/users/edit?id={$id_user}");
                    exit;
                }
            }

            $isDelete = $userLama['deleted_at'] !== null;

            if ($isDelete) {
                $_SESSION['error'] = 'Data petugas sudah dihapus dan tidak bisa diperbarui.';
                header("Location: /master/users");
                exit;
            }

            $isActive = (int) ($_POST['is_active'] ?? 1);

            if ($id_user === 0 || $username === '' || !in_array($roleInput, ['Admin', 'Kader'])) {
                $_SESSION['error'] = 'Data input tidak valid.';
                header("Location: /master/users/edit?id={$id_user}");
                exit;
            }

            $userModel = new User();

            // cegah Admin yang sedang aktif menurunkan role-nya sendiri secara tidak sengaja
            if ($id_user === (int) $_SESSION['user_id'] && $roleInput !== 'Admin') {
                $_SESSION['error'] = 'Anda tidak diperbolehkan menurunkan hak akses akun Anda sendiri.';
                header("Location: /master/users/edit?id={$id_user}");
                exit;
            }

            if ($id_user === (int) $_SESSION['user_id'] && $isActive !== 1) {
                $_SESSION['error'] = 'Anda tidak bisa menonaktifkan akun Anda sendiri.';
                header("Location: /master/users/edit?id={$id_user}");
                exit;
            }

            if ($userModel->update($id_user, $username, $roleInput, $idGudang, $password, $isActive)) {
                $_SESSION['success'] = 'Data petugas berhasil diperbarui.';
                header('Location: /master/users');
                exit;
            }

            $_SESSION['error'] = 'Gagal memperbarui data pengguna.';
            header("Location: /master/users/edit?id={$id_user}");
            exit;
        }
    }

    // hapus user (admin/kader)
    public function deleteUsers()
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
            header('Location: /auth/login');
            exit;
        }

        $id_user = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id_user <= 0) {
            $_SESSION['error'] = 'ID user tidak valid.';
            header('Location: /master/users');
            exit;
        }

        // cegah admin menghapus dirinya sendiri
        if ($id_user === (int) $_SESSION['user_id']) {
            $_SESSION['error'] = 'Anda tidak bisa menghapus akun Anda sendiri yang sedang digunakan.';
            header('Location: /master/users');
            exit;
        }

        $userModel = new User();

        if ($userModel->delete($id_user)) {
            $_SESSION['success'] = 'Pengguna berhasil dihapus dari sistem.';
        } else {
            $_SESSION['error'] = 'Gagal menghapus pengguna.';
        }

        header('Location: /master/users');
        exit;
    }


    // --- Master: Ibu ---

    // fungsi create ibu
    public function createIbu()
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== ROLE_IBU) {
            header('Location: /auth/login');
            exit;
        }


        $gudangModel = new Gudang();
        $gudangs = $gudangModel->getPosyanduOnly();
        require '../views/master/ibu/create.php';
    }

    // fungsi store ibu
    public function storeIbu()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $koneksi;

            $id_ibu = $_SESSION['user_id'];
            $nik = trim($_POST['nik_ibu'] ?? '');
            $nama = trim($_POST['nama_ibu'] ?? '');
            $telp = trim($_POST['no_telp'] ?? '');
            $alamat = trim($_POST['alamat'] ?? '');
            $is_pregnant = isset($_POST['is_pregnant']) ? 1 : 0;
            $id_gudang = $_POST['id_gudang'] ?? null;

            // validasi input ibu
            if ($nik === '' || $nama === '' || $id_gudang === null) {
                $_SESSION['error'] = 'NIK, Nama dan Gudang wajib diisi.';
                header('Location: /master/ibu/create');
                exit;
            }

            if (!preg_match('/^\d{16}$/', $nik)) {
                $_SESSION['error'] = 'NIK harus terdiri dari 16 digit angka.';
                header('Location: /master/ibu/create');
                exit;
            }

            $ibuModel = new Ibu();

            if ($ibuModel->isNikExists($nik)) {
                $_SESSION['error'] = 'NIK ibu sudah digunakan.';
                header('Location: /master/ibu/create');
                exit;
            }

            // insert data ibu ke tabel ibu
            $ibuModel = new Ibu();
            if ($ibuModel->create($id_ibu, $nik, $nama, $telp, $alamat, $is_pregnant, $id_gudang)) {

                // validasi jika berhasil disimpan dan redirect ke dashboard, jika gagal kembali ke form dengan pesan error
                $_SESSION['success'] = 'Profil berhasil disimpan.';
                header('Location: /dashboard');
                exit;
            } else {
                $_SESSION['error'] = 'Gagal menyimpan profil.';
                header('Location: /master/ibu/create');
                exit;
            }
        }

        header('Location: /master/ibu/create');
        exit;
    }


    // sidebar ibu n kader
    public function riwayatPeriksa()
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== ROLE_IBU) {
            header('Location: /dashboard');
            exit;
        }

        $pemeriksaanModel = new Pemeriksaan();
        $id_ibu = $_SESSION['user_id'];

        $limit = 10;
        $search = trim($_GET['q'] ?? '');

        // Fetch all pemeriksaan for this Ibu
        $totalDataAwal = $pemeriksaanModel->countAll(null, $id_ibu);
        $allPemeriksaan = $totalDataAwal > 0
            ? $pemeriksaanModel->getPaginated($totalDataAwal, 0, null, $id_ibu)
            : [];

        // Format labels
        $allPemeriksaan = array_map(function ($p) {
            $p['berat_badan_label'] = $p['berat_badan'] !== null ? number_format($p['berat_badan'], 1, ',', '.') . ' Kg' : '-';
            $p['tinggi_badan_label'] = $p['tinggi_badan'] !== null ? number_format($p['tinggi_badan'], 1, ',', '.') . ' Cm' : '-';
            $p['tanggal_pemeriksaan_label'] = !empty($p['tanggal_pemeriksaan']) ? date('d/m/Y', strtotime($p['tanggal_pemeriksaan'])) : '-';
            return $p;
        }, $allPemeriksaan);

        $searchedPemeriksaan = SearchHelper::searchArray($allPemeriksaan, $search, [
            'nama_anak',
            'status_gizi',
            'tanggal_pemeriksaan_label'
        ]);

        $pagination = PaginationHelper::paginateArray($searchedPemeriksaan, $limit);

        $pemeriksaans = $pagination['data'];
        $tablePagination = [
            'total_data' => $pagination['total_data'],
            'total_halaman' => $pagination['total_halaman'],
            'halaman_aktif' => $pagination['halaman_aktif'],
            'per_halaman' => $pagination['per_halaman'],
            'parameter_page' => 'page',
            'parameter_search' => 'q'
        ];
        require '../views/master/ibu/riwayat-periksa.php';
    }

    public function historiBantuan()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $role = $_SESSION['role'] ?? '';

        $isIbu = defined('ROLE_IBU')
            ? $role === ROLE_IBU
            : strtolower($role) === 'ibu';

        if (!$isIbu) {
            header('Location: /dashboard');
            exit;
        }

        $penyerahanModel = new Penyerahan();

        $search = trim($_GET['q'] ?? '');

        $allBantuans = $penyerahanModel->getRiwayatBantuanByIbuUser($_SESSION['user_id']);

        $allBantuans = array_map(function ($b) {
            $b['tanggal_bantuan_label'] = !empty($b['tanggal_penyerahan'])
                ? date('d/m/Y', strtotime($b['tanggal_penyerahan']))
                : '-';

            $b['jumlah_label'] = trim(($b['jumlah'] ?? '-') . ' ' . ($b['satuan'] ?? ''));

            return $b;
        }, $allBantuans);

        $searchedBantuans = SearchHelper::searchArray($allBantuans, $search, [
            'nama_anak',
            'nama_komoditas',
            'jumlah_label',
            'status_penyerahan',
            'tanggal_bantuan_label',
            'nama_posyandu'
        ]);

        $pagination = PaginationHelper::paginateArray($searchedBantuans, 20);

        $data['bantuans'] = $pagination['data'];
        $data['pagination_bantuan'] = $pagination;

        require '../views/master/ibu/histori-bantuan.php';
    }

    public function stokPosyandu()
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

        $stokModel = new Stok();
        $search = trim($_GET['q'] ?? '');

        $userModel = new User();
        $currentUser = $userModel->findByid($_SESSION['user_id']);

        $idGudangUser = (int) ($currentUser['id_gudang'] ?? 0);

        if ($idGudangUser <= 0) {
            $_SESSION['error'] = 'Akun ini belum terhubung dengan gudang/posyandu.';
            $allStoks = [];
        } else {
            $allStoks = $stokModel->getByGudang($idGudangUser);
        }

        $allStoks = array_map(function ($row) {
            $jumlah = (float) ($row['jumlah_stok'] ?? $row['qty_current'] ?? 0);

            if ($jumlah <= 0) {
                $row['status_label'] = 'Habis';
            } elseif ($jumlah <= 10) {
                $row['status_label'] = 'Menipis';
            } else {
                $row['status_label'] = 'Tersedia';
            }

            return $row;
        }, $allStoks);

        $searchedStoks = SearchHelper::searchArray($allStoks, $search, [
            'nama_komoditas',
            'nama_gudang',
            'jenis_gudang',
            'jumlah_stok',
            'status_label'
        ]);

        $pagination = PaginationHelper::paginateArray($searchedStoks, 20);

        $data['stoks'] = $pagination['data'];
        $data['pagination_stok'] = $pagination;

        require '../views/master/kader/stok.php';
    }

    // --- fungsi nampilih daftar ibu dan anak ---

    public function callIbuDanAnak()
    {
        if (empty($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Admin'])) {
            header('Location: /auth/login');
            exit;
        }

        global $koneksi;

        // =========================
        // AMBIL DATA IBU
        // =========================
        $queryIbu = "
        SELECT i.*, u.username 
        FROM ibu i
        JOIN users u ON i.id_ibu = u.id_user
        ORDER BY i.nama_ibu ASC
    ";

        $hasilIbu = mysqli_query($koneksi, $queryIbu);
        $allIbu = mysqli_fetch_all($hasilIbu, MYSQLI_ASSOC);

        // =========================
        // AMBIL DATA ANAK
        // =========================
        $queryAnak = "
        SELECT a.*, i.nama_ibu, g.nama_gudang 
        FROM anak a
        JOIN ibu i ON a.id_ibu = i.id_ibu
        JOIN gudang g ON i.id_gudang = g.id_gudang
        ORDER BY a.nama_anak ASC
    ";

        $hasilAnak = mysqli_query($koneksi, $queryAnak);
        $allAnak = mysqli_fetch_all($hasilAnak, MYSQLI_ASSOC);

        // =========================
        // AMBIL SEARCH PARAM
        // =========================
        $searchIbu = trim($_GET['q_ibu'] ?? '');
        $searchAnak = trim($_GET['q_anak'] ?? '');

        // =========================
        // TAMBAH FIELD LABEL BIAR SEARCH FLEXIBLE
        // =========================
        $allIbu = array_map(function ($ibu) {
            $ibu['status_kehamilan_label'] = !empty($ibu['is_pregnant'])
                ? 'Hamil'
                : 'Tidak Hamil Menyusui';

            return $ibu;
        }, $allIbu);

        $allAnak = array_map(function ($anak) {
            $anak['jenis_kelamin_label'] = $anak['jenis_kelamin'] === 'L'
                ? 'Laki-laki'
                : 'Perempuan';

            return $anak;
        }, $allAnak);

        // =========================
        // SEARCH IBU
        // =========================
        $searchedIbu = SearchHelper::searchArray($allIbu, $searchIbu, [
            'NIK_ibu',
            'nama_ibu',
            'no_telp',
            'status_kehamilan_label',
            'alamat',
            'username'
        ]);

        // =========================
        // SEARCH ANAK
        // =========================
        $searchedAnak = SearchHelper::searchArray($allAnak, $searchAnak, [
            'NIK_anak',
            'nama_anak',
            'jenis_kelamin',
            'jenis_kelamin_label',
            'tgl_lahir',
            'nama_ibu',
            'nama_gudang'
        ]);

        // =========================
        // PAGINATION TERPISAH
        // =========================
        $paginationIbu = PaginationHelper::paginateArray($searchedIbu, 15, 'page_ibu');
        $paginationAnak = PaginationHelper::paginateArray($searchedAnak, 15, 'page_anak');

        // =========================
        // BUNDLE KE VIEW
        // =========================
        $data['ibu'] = $paginationIbu['data'];
        $data['anak'] = $paginationAnak['data'];

        $data['pagination_ibu'] = $paginationIbu;
        $data['pagination_anak'] = $paginationAnak;

        $data['search_ibu'] = $searchIbu;
        $data['search_anak'] = $searchAnak;

        require '../views/master/ibu/ibuAnak.php';
    }

    // --- Master: Petani ---
    public function createPetani()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $petaniModel = new PetaniLokal();
        $petani = $petaniModel->findByUserId($_SESSION['user_id']);

        if ($petani) {
            header('Location: /master/petani/lahan/create');
            exit;
        }

        require '../views/master/petani/create.php';
    }

    public function storePetani()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /master/petani/create');
            exit;
        }

        $idUser = (int) $_SESSION['user_id'];

        $nama_lahan = trim($_POST['nama_lahan'] ?? '');
        $alamat_lahan = trim($_POST['alamat_lahan'] ?? '');
        $no_rekening = trim($_POST['no_rekening'] ?? '');
        $kapasitas = (float) ($_POST['kapasitas_panen_bulan'] ?? 0);

        if ($nama_lahan === '') {
            $_SESSION['error'] = 'Nama lahan wajib diisi.';
            header('Location: /master/petani/create');
            exit;
        }

        $petaniModel = new PetaniLokal();

        if ($petaniModel->create($idUser, $nama_lahan, $alamat_lahan, $no_rekening, $kapasitas)) {
            $_SESSION['success'] = 'Profil dasar berhasil disimpan. Lanjut lengkapi detail lahan.';
            header('Location: /master/petani/lahan/create');
            exit;
        }

        $_SESSION['error'] = 'Gagal menyimpan profil.';
        header('Location: /master/petani/create');
        exit;
    }

    public function riwayatEkonomi()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $petaniModel = new PetaniLokal();
        $petani = $petaniModel->findByUserId($_SESSION['user_id']);

        if (!$petani) {
            $_SESSION['error'] = 'Lengkapi profil petani terlebih dahulu.';
            header('Location: /master/petani/create');
            exit;
        }

        $pengadaanModel = new Pengadaan();
        $riwayat = $pengadaanModel->getRiwayatEkonomiByPetani($petani['id_petani']);

        require '../views/master/petani/riwayat-ekonomi.php';
    }

    public function profilLahan()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $petaniModel = new PetaniLokal();
        $petani = $petaniModel->findByUserId($_SESSION['user_id']);

        if (!$petani) {
            $_SESSION['error'] = 'Lengkapi profil petani terlebih dahulu.';
            header('Location: /master/petani/create');
            exit;
        }

        $komoditas = $petaniModel->getKomoditasByPetani($petani['id_petani']);

        $detailBelumLengkap =
            empty($petani['luas_lahan']) ||
            empty($petani['jenis_usaha']) ||
            empty($petani['status_lahan']) ||
            empty($komoditas);

        if ($detailBelumLengkap) {
            $_SESSION['error'] = 'Lengkapi detail lahan terlebih dahulu.';
            header('Location: /master/petani/lahan/create');
            exit;
        }

        $totalLuasTerpakai = 0;

        foreach ($komoditas as $item) {
            $totalLuasTerpakai += (float) ($item['luas_area'] ?? 0);
        }

        $luasLahan = (float) ($petani['luas_lahan'] ?? 0);
        $sisaLahan = max(0, $luasLahan - $totalLuasTerpakai);

        require '../views/master/petani/profil-lahan.php';
    }

    public function createLahanPetani()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $petaniModel = new PetaniLokal();
        $petani = $petaniModel->findByUserId($_SESSION['user_id']);

        if (!$petani) {
            $_SESSION['error'] = 'Lengkapi profil petani terlebih dahulu.';
            header('Location: /master/petani/create');
            exit;
        }

        $komoditasPetani = $petaniModel->getKomoditasByPetani($petani['id_petani']);

        $sudahLengkap =
            !empty($petani['luas_lahan']) &&
            !empty($petani['jenis_usaha']) &&
            !empty($petani['status_lahan']) &&
            !empty($komoditasPetani);

        if ($sudahLengkap) {
            header('Location: /master/petani/profil-lahan');
            exit;
        }

        $komoditas = $petaniModel->getKomoditasOptions();

        require '../views/master/petani/lahan/create.php';
    }

    public function storeLahanPetani()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /master/petani/lahan/create');
            exit;
        }

        $petaniModel = new PetaniLokal();
        $petani = $petaniModel->findByUserId($_SESSION['user_id']);

        if (!$petani) {
            $_SESSION['error'] = 'Data petani tidak ditemukan.';
            header('Location: /master/petani/create');
            exit;
        }

        $idPetani = (int) $petani['id_petani'];

        $luasLahan = (float) ($_POST['luas_lahan'] ?? 0);
        $satuanLuas = $_POST['satuan_luas'] ?? 'ha';
        $jenisUsaha = trim($_POST['jenis_usaha'] ?? '');
        $statusLahan = $_POST['status_lahan'] ?? 'Aktif';
        $deskripsiLahan = trim($_POST['deskripsi_lahan'] ?? '');

        $idKomoditasList = $_POST['id_komoditas'] ?? [];
        $luasAreaList = $_POST['luas_area'] ?? [];
        $estimasiPanenList = $_POST['estimasi_panen'] ?? [];
        $catatanList = $_POST['catatan_komoditas'] ?? [];

        if ($luasLahan <= 0) {
            $_SESSION['error'] = 'Luas lahan wajib diisi.';
            header('Location: /master/petani/lahan/create');
            exit;
        }

        if ($jenisUsaha === '') {
            $_SESSION['error'] = 'Jenis usaha wajib diisi.';
            header('Location: /master/petani/lahan/create');
            exit;
        }

        $details = [];
        $totalLuasArea = 0;

        foreach ($idKomoditasList as $index => $idKomoditas) {
            $idKomoditas = (int) $idKomoditas;
            $luasArea = (float) ($luasAreaList[$index] ?? 0);
            $estimasiPanen = (float) ($estimasiPanenList[$index] ?? 0);
            $catatan = trim($catatanList[$index] ?? '');

            if ($idKomoditas > 0) {
                if ($luasArea <= 0) {
                    $_SESSION['error'] = 'Luas area setiap komoditas wajib lebih dari 0.';
                    header('Location: /master/petani/lahan/create');
                    exit;
                }

                if ($estimasiPanen < 0) {
                    $_SESSION['error'] = 'Estimasi panen tidak boleh minus.';
                    header('Location: /master/petani/lahan/create');
                    exit;
                }

                $totalLuasArea += $luasArea;

                $details[] = [
                    'id_komoditas' => $idKomoditas,
                    'luas_area' => $luasArea,
                    'estimasi_panen' => $estimasiPanen,
                    'catatan' => $catatan
                ];
            }
        }

        if (empty($details)) {
            $_SESSION['error'] = 'Minimal pilih 1 komoditas yang dibudidayakan.';
            header('Location: /master/petani/lahan/create');
            exit;
        }

        if ($totalLuasArea > $luasLahan) {
            $_SESSION['error'] = 'Total luas area komoditas tidak boleh melebihi total luas lahan.';
            header('Location: /master/petani/lahan/create');
            exit;
        }

        if (
            $petaniModel->updateDetailLahan($idPetani, $luasLahan, $satuanLuas, $jenisUsaha, $statusLahan, $deskripsiLahan)
            && $petaniModel->replaceKomoditas($idPetani, $details)
        ) {
            $_SESSION['success'] = 'Detail lahan berhasil disimpan.';
            header('Location: /master/petani/profil-lahan');
            exit;
        }

        $_SESSION['error'] = 'Gagal menyimpan detail lahan.';
        header('Location: /master/petani/lahan/create');
        exit;
    }

    // --- Master: Gudang ---
    public function createGudang()
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== ROLE_ADMIN) {
            header('Location: /auth/login');
            exit;
        }

        $userModel = new User();
        $allUsers = $userModel->all();
        $kaders = array_filter($allUsers, fn($user) => $user['role'] === ROLE_KADER);

        require '../views/master/gudang/create.php';
    }

    public function storeGudang()
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== ROLE_ADMIN) {
            header('Location: /auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_gudang = trim($_POST['nama_gudang'] ?? '');
            $lokasi_gudang = trim($_POST['lokasi_gudang'] ?? '');
            $jenis_gudang = $_POST['jenis_gudang'] ?? 'Pusat';
            $alamat_lengkap = trim($_POST['alamat_lengkap'] ?? '');
            $nama_pengelola_select = $_POST['nama_pengelola_select'] ?? '';
            $nama_pengelola_text = trim($_POST['nama_pengelola_text'] ?? '');
            $nama_pengelola = ($nama_pengelola_select === 'other') ? $nama_pengelola_text : $nama_pengelola_select;

            if ($nama_gudang === '' || $lokasi_gudang === '' || $alamat_lengkap === '') {
                $_SESSION['error'] = 'Nama gudang, lokasi, dan alamat wajib diisi.';
                header('Location: /master/gudang/create');
                exit;
            }

            if ($nama_pengelola_select === '') {
                $_SESSION['error'] = 'Pengelola wajib dipilih.';
                header('Location: /master/gudang/create');
                exit;
            }

            $gudangModel = new Gudang();
            if ($gudangModel->create($nama_gudang, $lokasi_gudang, $jenis_gudang, $alamat_lengkap, $nama_pengelola)) {
                $_SESSION['success'] = 'Gudang berhasil ditambahkan.';
                header('Location: /master/gudang');
                exit;
            }

            $_SESSION['error'] = 'Gagal menyimpan gudang.';
            header('Location: /master/gudang/create');
            exit;
        }

        header('Location: /master/gudang/create');
        exit;
    }

    public function indexGudang()
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== ROLE_ADMIN) {
            header('Location: /auth/login');
            exit;
        }

        $gudangModel = new Gudang();
        $allGudangs = $gudangModel->all();

        $search = trim($_GET['q'] ?? '');

        $searchedGudangs = SearchHelper::searchArray($allGudangs, $search, [
            'id_gudang',
            'nama_gudang',
            'lokasi_gudang',
            'jenis_gudang',
            'nama_pengelola',
            'alamat_lengkap'
        ]);

        $pagination = PaginationHelper::paginateArray($searchedGudangs, 25);

        $gudangs = $pagination['data'];
        $tablePagination = $pagination;
        $searchValue = $search;

        require '../views/master/gudang/index.php';
    }

    public function editGudang()
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== ROLE_ADMIN) {
            header('Location: /auth/login');
            exit;
        }

        $id_gudang = isset($_GET['id']) ? (int) $_GET['id'] : null;
        if (!$id_gudang) {
            header('Location: /master/gudang');
            exit;
        }

        $gudang = (new Gudang())->findById($id_gudang);
        if (!$gudang) {
            header('Location: /master/gudang');
            exit;
        }

        $userModel = new User();
        $allUsers = $userModel->all();
        $kaders = array_filter($allUsers, fn($user) => $user['role'] === ROLE_KADER);

        require '../views/master/gudang/edit.php';
    }

    public function updateGudang()
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== ROLE_ADMIN) {
            header('Location: /auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_gudang = isset($_POST['id_gudang']) ? (int) $_POST['id_gudang'] : null;
            $nama_gudang = trim($_POST['nama_gudang'] ?? '');
            $lokasi_gudang = trim($_POST['lokasi_gudang'] ?? '');
            $jenis_gudang = $_POST['jenis_gudang'] ?? 'Pusat';
            $alamat_lengkap = trim($_POST['alamat_lengkap'] ?? '');
            $nama_pengelola_select = $_POST['nama_pengelola_select'] ?? '';
            $nama_pengelola_text = trim($_POST['nama_pengelola_text'] ?? '');
            $nama_pengelola = ($nama_pengelola_select === 'other') ? $nama_pengelola_text : $nama_pengelola_select;

            if (!$id_gudang || $nama_gudang === '' || $lokasi_gudang === '' || $alamat_lengkap === '') {
                $_SESSION['error'] = 'Data gudang tidak lengkap.';
                header("Location: /master/gudang/edit?id={$id_gudang}");
                exit;
            }

            $gudangModel = new Gudang();
            if ($gudangModel->update($id_gudang, $nama_gudang, $lokasi_gudang, $jenis_gudang, $alamat_lengkap, $nama_pengelola)) {
                $_SESSION['success'] = 'Gudang berhasil diperbarui.';
                header('Location: /master/gudang');
                exit;
            }

            $_SESSION['error'] = 'Gagal memperbarui gudang.';
            header("Location: /master/gudang/edit?id={$id_gudang}");
            exit;
        }

        header('Location: /master/gudang');
        exit;
    }

    public function deleteGudang()
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== ROLE_ADMIN) {
            header('Location: /auth/login');
            exit;
        }

        $id_gudang = isset($_GET['id']) ? (int) $_GET['id'] : null;
        if ($id_gudang) {
            (new Gudang())->delete($id_gudang);
        }

        header('Location: /master/gudang');
        exit;
    }

    // --- Master: Komoditas Pangan ---
    public function indexKomoditas()
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== ROLE_ADMIN) {
            header('Location: /auth/login');
            exit;
        }

        $komoditasModel = new Komoditas();
        $allKomoditas = $komoditasModel->all();

        $search = trim($_GET['q'] ?? '');

        $searchedKomoditas = SearchHelper::searchArray($allKomoditas, $search, [
            'id_komoditas',
            'nama_komoditas',
            'kategori_gizi',
            'nama_satuan',
            'deskripsi',
            'tgl_created'
        ]);

        $totalAktif = count(array_filter($searchedKomoditas, function ($item) {
            return empty($item['is_deleted']);
        }));

        $totalNonaktif = count(array_filter($searchedKomoditas, function ($item) {
            return !empty($item['is_deleted']);
        }));

        usort($searchedKomoditas, function ($a, $b) {
            $statusA = !empty($a['is_deleted']) ? 1 : 0;
            $statusB = !empty($b['is_deleted']) ? 1 : 0;

            if ($statusA !== $statusB) {
                return $statusA <=> $statusB;
            }

            return strcmp($a['nama_komoditas'] ?? '', $b['nama_komoditas'] ?? '');
        });

        $pagination = PaginationHelper::paginateArray($searchedKomoditas, 25);

        $komoditas = $pagination['data'];
        $tablePagination = $pagination;
        $totalAktif = $totalAktif;
        $totalNonaktif = $totalNonaktif;

        require '../views/master/komoditas/index.php';
    }

    public function createKomoditas()
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== ROLE_ADMIN) {
            header('Location: /auth/login');
            exit;
        }

        $satuanModel = new Satuan();
        $satuans = $satuanModel->all();

        require '../views/master/komoditas/create.php';
    }

    public function storeKomoditas()
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== ROLE_ADMIN) {
            header('Location: /auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_komoditas = trim($_POST['nama_komoditas'] ?? '');
            $kategori_gizi_select = trim($_POST['kategori_gizi'] ?? '');
            $kategori_gizi_text = trim($_POST['kategori_gizi_lain'] ?? '');
            $kategori_gizi = ($kategori_gizi_select === 'Lainnya') ? $kategori_gizi_text : $kategori_gizi_select;
            $id_satuan = isset($_POST['id_satuan']) ? (int) $_POST['id_satuan'] : 0;
            $deskripsi = trim($_POST['deskripsi'] ?? '');

            if ($nama_komoditas === '' || $kategori_gizi === '' || $id_satuan <= 0) {
                $_SESSION['error'] = 'Nama komoditas, kategori gizi, dan satuan wajib diisi.';
                header('Location: /master/komoditas/create');
                exit;
            }

            $komoditasModel = new Komoditas();

            if ($komoditasModel->findByNama($nama_komoditas)) {
                $_SESSION['error'] = 'Nama komoditas sudah digunakan.';
                header('Location: /master/komoditas/create');
                exit;
            }

            if ($komoditasModel->create($nama_komoditas, $kategori_gizi, $id_satuan, $deskripsi)) {
                $_SESSION['success'] = 'Komoditas berhasil ditambahkan.';
                header('Location: /master/komoditas');
                exit;
            }

            $_SESSION['error'] = 'Gagal menyimpan komoditas.';
            header('Location: /master/komoditas/create');
            exit;
        }

        header('Location: /master/komoditas/create');
        exit;
    }

    public function editKomoditas()
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== ROLE_ADMIN) {
            header('Location: /auth/login');
            exit;
        }

        $id_komoditas = isset($_GET['id']) ? (int) $_GET['id'] : null;

        if (!$id_komoditas) {
            header('Location: /master/komoditas');
            exit;
        }

        $komoditas = (new Komoditas())->findById($id_komoditas);

        if (!$komoditas) {
            $_SESSION['error'] = 'Data komoditas tidak ditemukan.';
            header('Location: /master/komoditas');
            exit;
        }

        $satuanModel = new Satuan();
        $satuans = $satuanModel->all();

        require '../views/master/komoditas/edit.php';
    }

    public function updateKomoditas()
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== ROLE_ADMIN) {
            header('Location: /auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_komoditas = isset($_POST['id_komoditas']) ? (int) $_POST['id_komoditas'] : null;
            $nama_komoditas = trim($_POST['nama_komoditas'] ?? '');
            $kategori_gizi = trim($_POST['kategori_gizi'] ?? '');
            $id_satuan = isset($_POST['id_satuan']) ? (int) $_POST['id_satuan'] : 0;
            $deskripsi = trim($_POST['deskripsi'] ?? '');

            if (!$id_komoditas || $nama_komoditas === '' || $kategori_gizi === '' || $id_satuan <= 0) {
                $_SESSION['error'] = 'Data komoditas tidak lengkap.';
                header("Location: /master/komoditas/edit?id={$id_komoditas}");
                exit;
            }

            $komoditasModel = new Komoditas();

            if ($komoditasModel->update($id_komoditas, $nama_komoditas, $kategori_gizi, $id_satuan, $deskripsi)) {
                $_SESSION['success'] = 'Komoditas berhasil diperbarui.';
                header('Location: /master/komoditas');
                exit;
            }

            $_SESSION['error'] = 'Gagal memperbarui komoditas.';
            header("Location: /master/komoditas/edit?id={$id_komoditas}");
            exit;
        }

        header('Location: /master/komoditas');
        exit;
    }

    public function deleteKomoditas()
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== ROLE_ADMIN) {
            header('Location: /auth/login');
            exit;
        }

        $id_komoditas = isset($_GET['id']) ? (int) $_GET['id'] : null;

        if ($id_komoditas) {
            (new Komoditas())->delete($id_komoditas);
            $_SESSION['success'] = 'Komoditas berhasil dihapus.';
        }

        header('Location: /master/komoditas');
        exit;
    }

    public function restoreKomoditas()
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== ROLE_ADMIN) {
            header('Location: /auth/login');
            exit;
        }

        $id_komoditas = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id_komoditas <= 0) {
            $_SESSION['error'] = 'ID komoditas tidak valid.';
            header('Location: /master/komoditas');
            exit;
        }

        $komoditasModel = new Komoditas();

        if ($komoditasModel->restore($id_komoditas)) {
            $_SESSION['success'] = 'Komoditas berhasil dipulihkan.';
        } else {
            $_SESSION['error'] = 'Gagal memulihkan komoditas.';
        }

        header('Location: /master/komoditas');
        exit;
    }

    // --- Master: Satuan ---
    public function indexSatuan()
    {
        if (
            empty($_SESSION['user_id']) ||
            $_SESSION['role'] !== ROLE_ADMIN
        ) {
            header('Location: /auth/login');
            exit;
        }

        $satuanModel = new Satuan();
        $allSatuans = $satuanModel->all();

        $search = trim($_GET['q'] ?? '');

        $searchedSatuans = SearchHelper::searchArray($allSatuans, $search, [
            'id_satuan',
            'nama_satuan',
            'singkat'
        ]);

        $pagination = PaginationHelper::paginateArray($searchedSatuans, 25);

        $satuans = $pagination['data'];
        $tablePagination = $pagination;

        require '../views/master/satuan/index.php';
    }

    public function createSatuan()
    {
        if (
            empty($_SESSION['user_id']) ||
            $_SESSION['role'] !== ROLE_ADMIN
        ) {
            header('Location: /auth/login');
            exit;
        }

        require '../views/master/satuan/create.php';
    }

    public function storeSatuan()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nama = trim($_POST['nama_satuan']);
            $singkat = trim($_POST['singkat']);

            $satuanModel = new Satuan();

            if ($satuanModel->create($nama, $singkat)) {

                $_SESSION['success'] =
                    'Satuan berhasil ditambahkan';

                header('Location: /master/satuan');
                exit;
            }
        }

        header('Location: /master/satuan/create');
    }

    public function editSatuan()
    {
        $id = $_GET['id'] ?? 0;

        $satuanModel = new Satuan();

        $satuan = $satuanModel->find($id);

        require '../views/master/satuan/edit.php';
    }

    public function updateSatuan()
    {
        $id = $_POST['id_satuan'];

        $nama = trim($_POST['nama_satuan']);
        $singkat = trim($_POST['singkat']);

        $satuanModel = new Satuan();

        $satuanModel->update(
            $id,
            $nama,
            $singkat
        );

        header('Location: /master/satuan');
        exit;
    }

    public function deleteSatuan()
    {
        $id = $_GET['id'] ?? 0;

        $satuanModel = new Satuan();

        $satuanModel->delete($id);

        header('Location: /master/satuan');
        exit;
    }

    // --- Master: Anak ---
    public function indexAnak()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $anakModel = new Anak();

        if ($_SESSION['role'] === ROLE_IBU) {
            $idIbu = (int) $_SESSION['user_id'];

            // pakai query terbaru
            $anaks = $anakModel->getByIbuIdWithLatestPemeriksaan($idIbu);
        } else {

            $anaks = $anakModel->all();
        }

        require '../views/master/anak/index.php';
    }

    public function createAnak()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        // Jika Ibu: kirim data ibu sendiri agar form langsung terkunci ke akunnya
        // Jika Admin/Kader: kirim semua data ibu untuk dropdown pilihan
        if ($_SESSION['role'] === ROLE_IBU) {
            $ibuModel = new Ibu();
            $ibu_login = $ibuModel->findByIdIbu($_SESSION['user_id']);
            $ibus = [];
        } else {
            $ibu_login = null;
            $ibuModel = new Ibu();
            $ibus = $ibuModel->all();
        }

        require '../views/master/anak/create.php';
    }

    public function storeAnak()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // KEAMANAN: Jika role Ibu, id_ibu WAJIB dari session (tidak bisa dimanipulasi)
            // Jika Admin/Kader, id_ibu diambil dari form dropdown pilihan
            if ($_SESSION['role'] === ROLE_IBU) {
                $id_ibu = (int) $_SESSION['user_id'];
            } else {
                $id_ibu = (int) ($_POST['id_ibu'] ?? 0);
            }

            $nik_anak = trim($_POST['NIK_anak'] ?? '');
            $nama_anak = trim($_POST['nama_anak'] ?? '');
            $tgl_lahir = trim($_POST['tgl_lahir'] ?? '');
            $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';

            // validasi input anak
            if ($id_ibu === 0 || $nik_anak === '' || $nama_anak === '' || $tgl_lahir === '' || $jenis_kelamin === '') {
                $_SESSION['error'] = 'Semua field wajib diisi.';
                header('Location: /master/anak/create');
                exit;
            }

            if (!preg_match('/^\d{16}$/', $nik_anak)) {
                $_SESSION['error'] = 'NIK harus terdiri dari 16 digit angka.';
                header('Location: /master/anak/create');
                exit;
            }

            $anakModel = new Anak();

            if ($anakModel->isNikExists($nik_anak)) {
                $_SESSION['error'] = 'NIK anak sudah digunakan. Gunakan NIK lain.';
                header('Location: /master/anak/create');
                exit;
            }

            // insert data anak ke tabel anak
            $anakModel = new Anak();
            if ($anakModel->create($id_ibu, $nik_anak, $nama_anak, $tgl_lahir, $jenis_kelamin)) {
                $_SESSION['success'] = 'Data anak berhasil disimpan.';
                header('Location: /master/anak');
                exit;
            } else {
                $_SESSION['error'] = 'Gagal menyimpan data anak.';
                header('Location: /master/anak/create');
                exit;
            }
        }

        header('Location: /master/anak/create');
        exit;
    }

    public function editAnak()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $id_anak = isset($_GET['id']) ? (int) $_GET['id'] : null;
        if (!$id_anak) {
            $_SESSION['error'] = 'ID Anak tidak valid.';
            header('Location: /master/anak');
            exit;
        }

        $anakModel = new Anak();
        $anak = $anakModel->findById($id_anak);
        if (!$anak) {
            $_SESSION['error'] = 'Data anak tidak ditemukan.';
            header('Location: /master/anak');
            exit;
        }

        // Keamanan: Jika role Ibu, pastikan anak ini adalah miliknya
        if ($_SESSION['role'] === ROLE_IBU && (int) $anak['id_ibu'] !== (int) $_SESSION['user_id']) {
            $_SESSION['error'] = 'Anda tidak memiliki akses ke data anak ini.';
            header('Location: /master/anak');
            exit;
        }

        // Siapkan data ibu untuk form
        if ($_SESSION['role'] === ROLE_IBU) {
            $ibuModel = new Ibu();
            $ibu_login = $ibuModel->findByIdIbu($_SESSION['user_id']);
            $ibus = [];
        } else {
            $ibu_login = null;
            $ibuModel = new Ibu();
            $ibus = $ibuModel->all();
        }

        require '../views/master/anak/edit.php';
    }

    public function updateAnak()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_anak = isset($_POST['id_anak']) ? (int) $_POST['id_anak'] : null;
            if (!$id_anak) {
                $_SESSION['error'] = 'ID Anak tidak valid.';
                header('Location: /master/anak');
                exit;
            }

            $anakModel = new Anak();
            $anak = $anakModel->findById($id_anak);
            if (!$anak) {
                $_SESSION['error'] = 'Data anak tidak ditemukan.';
                header('Location: /master/anak');
                exit;
            }

            // Keamanan: Jika role Ibu, pastikan anak ini adalah miliknya dan id_ibu dipaksa dari session
            if ($_SESSION['role'] === ROLE_IBU) {
                if ((int) $anak['id_ibu'] !== (int) $_SESSION['user_id']) {
                    $_SESSION['error'] = 'Anda tidak memiliki akses ke data anak ini.';
                    header('Location: /master/anak');
                    exit;
                }
                $id_ibu = (int) $_SESSION['user_id'];
            } else {
                $id_ibu = (int) ($_POST['id_ibu'] ?? $anak['id_ibu']);
            }

            $nik_anak = trim($_POST['NIK_anak'] ?? '');
            $nama_anak = trim($_POST['nama_anak'] ?? '');
            $tgl_lahir = trim($_POST['tgl_lahir'] ?? '');
            $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';

            if ($id_ibu === 0 || $nik_anak === '' || $nama_anak === '' || $tgl_lahir === '' || $jenis_kelamin === '') {
                $_SESSION['error'] = 'Semua field wajib diisi.';
                header("Location: /master/anak/edit?id={$id_anak}");
                exit;
            }

            if (!preg_match('/^\d{16}$/', $nik_anak)) {
                $_SESSION['error'] = 'NIK harus terdiri dari 16 digit angka.';
                header("Location: /master/anak/edit?id={$id_anak}");
                exit;
            }

            if ($anakModel->update($id_anak, $id_ibu, $nik_anak, $nama_anak, $tgl_lahir, $jenis_kelamin)) {
                $_SESSION['success'] = 'Data anak berhasil diperbarui.';
                header("Location: /master/anak/edit?id={$id_anak}");
                exit;
            } else {
                $_SESSION['error'] = 'Gagal memperbarui data anak.';
                header("Location: /master/anak/edit?id={$id_anak}");
                exit;
            }
        }

        header('Location: /master/anak');
        exit;
    }

    public function deleteAnak()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $id_anak = isset($_GET['id']) ? (int) $_GET['id'] : null;
        if (!$id_anak) {
            $_SESSION['error'] = 'ID Anak tidak valid.';
            header('Location: /master/anak');
            exit;
        }

        $anakModel = new Anak();
        $anak = $anakModel->findById($id_anak);
        if (!$anak) {
            $_SESSION['error'] = 'Data anak tidak ditemukan.';
            header('Location: /master/anak');
            exit;
        }

        // Keamanan: Jika role Ibu, pastikan anak ini adalah miliknya
        if ($_SESSION['role'] === ROLE_IBU && (int) $anak['id_ibu'] !== (int) $_SESSION['user_id']) {
            $_SESSION['error'] = 'Anda tidak memiliki akses untuk menghapus data anak ini.';
            header('Location: /master/anak');
            exit;
        }

        if ($anakModel->delete($id_anak)) {
            $_SESSION['success'] = 'Data anak berhasil dihapus.';
        } else {
            $_SESSION['error'] = 'Gagal menghapus data anak.';
        }

        header('Location: /master/anak');
        exit;
    }

    // --- Master: standar_pertumbuhan ---

    public function callStandarPertumbuhan()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $standarPertumbuhanModel = new StandarPertumbuhan();
        $allData = $standarPertumbuhanModel->all();

        $filterJk = $_GET['jk'] ?? '';
        $filterTipe = $_GET['tipe'] ?? '';
        $search = trim($_GET['q'] ?? '');

        $filteredData = array_filter($allData, function ($item) use ($filterJk, $filterTipe) {
            $samainJK = empty($filterJk) || $item['jenis_kelamin'] === $filterJk;
            $samainTipe = empty($filterTipe) || $item['tipe_standar'] === $filterTipe;

            return $samainJK && $samainTipe;
        });

        $filteredData = array_values($filteredData);
        $filteredData = array_map(function ($item) {
            $item['jenis_kelamin_label'] = $item['jenis_kelamin'] === 'L'
                ? 'Laki-laki'
                : ($item['jenis_kelamin'] === 'P' ? 'Perempuan' : $item['jenis_kelamin']);

            return $item;
        }, $filteredData);

        $searchedData = SearchHelper::searchArray($filteredData, $search, [
            'tipe_standar',
            'jenis_kelamin',
            'jenis_kelamin_label',
            'usia_bulan',
            'median',
            'sd_plus_1',
            'sd_minus_1',
            'sd_minus_2',
            'sd_minus_3'
        ]);

        $pagination = PaginationHelper::paginateArray($searchedData, 25);

        $data['standar'] = $pagination['data'];
        $data['halaman_aktif'] = $pagination['halaman_aktif'];
        $data['total_halaman'] = $pagination['total_halaman'];
        $data['total_data'] = $pagination['total_data'];
        $data['per_halaman'] = $pagination['per_halaman'];

        $data['filter_jk'] = $filterJk;
        $data['filter_tipe'] = $filterTipe;
        $data['search'] = $search;

        require '../views/master/standar_pertumbuhan/index.php';
    }

    // --- Master: Paket Gizi ---
    public function paketGizi()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $paketModel = new PaketGizi();
        $pakets = $paketModel->getAllWithDetails();

        require '../views/master/paket-gizi/index.php';
    }

    public function editPaketGizi()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $idPaket = (int) ($_GET['id'] ?? 0);

        if ($idPaket <= 0) {
            $_SESSION['error'] = 'ID paket tidak valid.';
            header('Location: /master/paket-gizi');
            exit;
        }

        $paketModel = new PaketGizi();
        $paket = $paketModel->findWithDetails($idPaket);

        if (!$paket) {
            $_SESSION['error'] = 'Paket gizi tidak ditemukan.';
            header('Location: /master/paket-gizi');
            exit;
        }

        $komoditas = $paketModel->getKomoditasOptions();

        require '../views/master/paket-gizi/edit.php';
    }

    public function updatePaketGizi()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /master/paket-gizi');
            exit;
        }

        $idPaket = (int) ($_POST['id_paket'] ?? 0);
        $namaPaket = trim($_POST['nama_paket'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $idKomoditasList = $_POST['id_komoditas'] ?? [];
        $jumlahList = $_POST['jumlah'] ?? [];

        if ($idPaket <= 0 || $namaPaket === '') {
            $_SESSION['error'] = 'Data paket tidak valid.';
            header('Location: /master/paket-gizi');
            exit;
        }

        $details = [];

        foreach ($idKomoditasList as $index => $idKomoditas) {
            $idKomoditas = (int) $idKomoditas;
            $jumlah = (float) ($jumlahList[$index] ?? 0);

            if ($idKomoditas > 0) {
                if ($jumlah <= 0) {
                    $_SESSION['error'] = 'Jumlah setiap komoditas wajib lebih dari 0.';
                    header('Location: /master/paket-gizi/edit?id=' . $idPaket);
                    exit;
                }

                $details[] = [
                    'id_komoditas' => $idKomoditas,
                    'jumlah' => $jumlah
                ];
            }
        }

        if (empty($details)) {
            $_SESSION['error'] = 'Minimal isi 1 komoditas dalam paket.';
            header('Location: /master/paket-gizi/edit?id=' . $idPaket);
            exit;
        }

        $paketModel = new PaketGizi();

        $updateHeader = $paketModel->updatePaket($idPaket, $namaPaket, $deskripsi, $isActive);
        $updateDetail = $paketModel->replaceDetails($idPaket, $details);

        if ($updateHeader && $updateDetail) {
            $_SESSION['success'] = 'Paket gizi berhasil diperbarui.';
            header('Location: /master/paket-gizi');
            exit;
        }

        $_SESSION['error'] = 'Gagal memperbarui paket gizi.';
        header('Location: /master/paket-gizi/edit?id=' . $idPaket);
        exit;
    }
}
