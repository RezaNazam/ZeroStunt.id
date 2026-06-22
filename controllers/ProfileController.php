<?php

class ProfileController
{
    public function index()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /landing');
            exit;
        }

        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);

        if (!$user) {
            $_SESSION['error'] = 'Data akun tidak ditemukan.';
            header('Location: /dashboard');
            exit;
        }

        require '../views/profile/index.php';
    }

    public function edit()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /landing');
            exit;
        }

        $id_user = $_SESSION['user_id'];
        $role = $_SESSION['role'] ?? '';

        $userModel = new User();
        $user = $userModel->findById($id_user);

        if (!$user) {
            $_SESSION['error'] = 'Data akun tidak ditemukan.';
            header('Location: /profile');
            exit;
        }

        $ibu = null;
        $petani = null;
        $kader = null;

        if ($role === ROLE_IBU) {
            $ibuModel = new Ibu();
            $ibu = $ibuModel->findByIdIbu($id_user);
        }

        if ($role === ROLE_PETANI) {
            $petaniModel = new PetaniLokal();
            $petani = $petaniModel->findByIdPetani($id_user);
        }

        if ($role === ROLE_KADER) {
            $kaderModel = new user();
            $kader = $kaderModel->findById($id_user);
        }

        require '../views/profile/edit.php';
    }

    public function update()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /landing');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profile/edit');
            exit;
        }

        $id_user = $_SESSION['user_id'];
        $username = trim($_POST['username'] ?? '');

        if ($username === '') {
            $_SESSION['error'] = 'Username wajib diisi.';
            header('Location: /profile/edit');
            exit;
        }

        if (strlen($username) < 4 || strlen($username) > 30) {
            $_SESSION['error'] = 'Username harus terdiri dari 4 sampai 30 karakter.';
            header('Location: /profile/edit');
            exit;
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $_SESSION['error'] = 'Username hanya boleh berisi huruf, angka, dan underscore.';
            header('Location: /profile/edit');
            exit;
        }

        $userModel = new User();

        // Sesuaikan nama fungsi ini dengan yang ada di User model kamu
        if ($userModel->updateProfile($id_user, $username)) {
            $_SESSION['username'] = $username;
            $_SESSION['success'] = 'Profil berhasil diperbarui.';
            header('Location: /profile');
            exit;
        }

        $_SESSION['error'] = 'Gagal memperbarui profil.';
        header('Location: /profile/edit');
        exit;
    }

    public function delete()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /landing');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profile');
            exit;
        }

        $id_user = $_SESSION['user_id'];

        $userModel = new User();

        // Ini tinggal panggil fungsi delete/deactivate yang sudah ada di User model kamu
        if ($userModel->delete($id_user)) {
            session_unset();
            session_destroy();

            header('Location: /landing');
            exit;
        }

        $_SESSION['error'] = 'Gagal menghapus akun.';
        header('Location: /profile');
        exit;
    }
}
