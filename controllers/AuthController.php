<?php
class AuthController
{
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validasi input
            if ($username === '' || $password === '' || $confirmPassword === '') {
                $_SESSION['error'] = 'Semua field wajib diisi.';
                header('Location: /auth/register');
                exit;
            }

            // Validasi konfirmasi password
            if ($password !== $confirmPassword) {
                $_SESSION['error'] = 'Password dan konfirmasi harus sama.';
                header('Location: /auth/register');
                exit;
            }

            $role = $_POST['role'] ?? 'Ibu';

            if (!in_array($role, ['Ibu', 'Petani'])) {
                $_SESSION['error'] = 'Role tidak valid.';
                header('Location: /auth/register');
                exit;
            }

            $userModel = new User();
            if ($userModel->create($username, $password, $role)) {
                $_SESSION['success'] = 'Pendaftaran berhasil. Silakan login.';
                header('Location: /auth/login');
                exit;
            }

            $_SESSION['error'] = 'Username sudah dipakai atau terjadi kesalahan.';
            header('Location: /auth/register');
            exit;
        }

        require '../views/auth/register.php';
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($username === '' || $password === '') {
                $_SESSION['error'] = 'Username dan password wajib diisi.';
                header('Location: /auth/login');
                exit;
            }

            $userModel = new User();
            $userModel->ensureAdminExists();
            $user = $userModel->findByUsername($username);
            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header('Location: /dashboard');
                exit;
            }

            $_SESSION['error'] = 'Login gagal. Periksa username dan password.';
            header('Location: /auth/login');
            exit;
        }

        require '../views/auth/login.php';
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: /auth/login');
        exit;
    }
}
