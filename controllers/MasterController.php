<?php

class MasterController
{

    // --- MASTER DATA ---


    // --- Master: User ---

    // fungsi tampilin semua pengguna
    public function indexUsers()
    {
        // validasi biar cuma Admin yang bisa masuk
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
            header('Location: /auth/login');
            exit;
        }

        $userModel = new User();
        $users = $userModel->all();

        require '../views/master/user/index.php';
    }

    // form tambah user baru (admin bisa tambah kader, admin bisa tambah admin laionnya)
    public function createUsers()
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
            header('Location: /auth/login');
            exit;
        }

        require '../views/master/user/create.php';
    }


    // fungsi stor ke database
    public function storeUsers()
    {
        // pastiiin yg login admin
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
            header('Location: /auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $roleInput = $_POST['role'] ?? 'Admin';

            // Validasi input kosong
            if ($username === '' || $password === '') {
                $_SESSION['error'] = 'Username dan Password wajib diisi.';
                header('Location: /master/users/create');
                exit;
            }

            // Validasi keamanan: Kunci role input hanya boleh Admin atau Kader
            if (!in_array($roleInput, ['Admin', 'Kader'])) {
                $_SESSION['error'] = 'Role tidak valid.';
                header('Location: /master/users/create');
                exit;
            }

            $userModel = new User();

            // Validasi duplikasi, Cek apakah username sudah terdaftar
            if ($userModel->findByUsername($username)) {
                $_SESSION['error'] = 'Username sudah digunakan, silakan cari nama lain.';
                header('Location: /master/users/create');
                exit;
            }

            // fungsi buat akun create di model user
            if ($userModel->create($username, $password, $roleInput)) {
                $_SESSION['success'] = "Pengguna dengan peran {$roleInput} berhasil ditambahkan.";
                header('Location: /master/users');
                exit;
            } else {
                $_SESSION['error'] = 'Gagal menyimpan data pengguna.';
                header('Location: /master/users/create');
                exit;
            }
        }
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
            $_SESSION['error'] = 'Data pengguna tidak ditemukan.';
            header('Location: /master/users');
            exit;
        }

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

            if ($userModel->update($id_user, $username, $roleInput)) {
                $_SESSION['success'] = 'Data pengguna berhasil diperbarui.';
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

        $id_user = isset($_GET['id']) ? (int) $_GET['id'] : null;

        // cegah Admin menghapus dirinya sendiri
        if ($id_user === (int) $_SESSION['user_id']) {
            $_SESSION['error'] = 'Anda tidak bisa menghapus akun Anda sendiri yang sedang digunakan.';
            header('Location: /master/users');
            exit;
        }

        if ($id_user) {
            $userModel = new User();
            if ($userModel->delete($id_user)) {
                $_SESSION['success'] = 'Pengguna berhasil dihapus dari sistem.';
            } else {
                $_SESSION['error'] = 'Gagal menghapus pengguna.';
            }
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
        $gudangs = $gudangModel->all();
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

    // --- Master: Petani ---
    public function createPetani()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $koneksi;

            $id_petani = $_SESSION['user_id'];
            $nama_lahan = trim($_POST['nama_lahan'] ?? '');
            $alamat_lahan = trim($_POST['alamat_lahan'] ?? '');
            $no_rekening = trim($_POST['no_rekening'] ?? '');
            $kapasitas = $_POST['kapasitas_panen_bulan'] ?? 0;

            // validasi input petani
            if ($nama_lahan === '') {
                $_SESSION['error'] = 'Nama lahan wajib diisi.';
                header('Location: /master/petani/create');
                exit;
            }


            // insert data petani ke tabel petani_lokal
            // insert data ibu ke tabel ibu
            $petaniModel = new PetaniLokal();
            if ($petaniModel->create($id_petani, $nama_lahan, $alamat_lahan, $no_rekening, $kapasitas)) {

                // validasi jika berhasil disimpan dan redirect ke dashboard, jika gagal kembali ke form dengan pesan error
                $_SESSION['success'] = 'Profil berhasil disimpan.';
                header('Location: /dashboard');
                exit;
            } else {
                $_SESSION['error'] = 'Gagal menyimpan profil.';
                header('Location: /master/petani/create');
                exit;
            }
        }

        header('Location: /master/petani/create');
        exit;
    }

    public function riwayatEkonomi()
    {
        require '../views/master/petani/riwayat-ekonomi.php';
    }

    public function profilLahan()
    {
        require '../views/master/petani/profil-lahan.php';
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

        $gudangs = (new Gudang())->all();
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

        $komoditas = (new Komoditas())->all();
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
            $kategori_gizi = trim($_POST['kategori_gizi'] ?? '');
            $id_satuan = isset($_POST['id_satuan']) ? (int) $_POST['id_satuan'] : 0;
            $deskripsi = trim($_POST['deskripsi'] ?? '');

            if ($nama_komoditas === '' || $kategori_gizi === '' || $id_satuan <= 0) {
                $_SESSION['error'] = 'Nama komoditas, kategori gizi, dan satuan wajib diisi.';
                header('Location: /master/komoditas/create');
                exit;
            }

            $komoditasModel = new Komoditas();

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
        $satuans = $satuanModel->all();

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

        // Jika role Ibu: tampilkan hanya anak miliknya sendiri
        // Jika role Admin/Kader: tampilkan semua anak
        if ($_SESSION['role'] === ROLE_IBU) {
            $id_ibu = $_SESSION['user_id'];
            $anaks = $anakModel->findByIbu($id_ibu);
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
}
