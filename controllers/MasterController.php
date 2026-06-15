<?php

class MasterController
{

    // --- MASTER DATA ---

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

    // --- Master: Gudang ---
    public function createGudang()
    {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== ROLE_ADMIN) {
            header('Location: /auth/login');
            exit;
        }

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
            $nama_pengelola = trim($_POST['nama_pengelola'] ?? '');

            if ($nama_gudang === '' || $lokasi_gudang === '' || $alamat_lengkap === '') {
                $_SESSION['error'] = 'Nama gudang, lokasi, dan alamat wajib diisi.';
                header('Location: /master/gudang/create');
                exit;
            }

            $gudangModel = new Gudang();
            if ($gudangModel->create($nama_gudang, $lokasi_gudang, $jenis_gudang, $alamat_lengkap, $nama_pengelola)) {
                $_SESSION['success'] = 'Gudang berhasil ditambahkan.';
                header('Location: /dashboard');
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
            $nama_pengelola = trim($_POST['nama_pengelola'] ?? '');

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
}
