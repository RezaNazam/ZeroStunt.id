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

            // validasi username
            if (strlen($username) < 4 || strlen($username) > 30) {
                $_SESSION['error'] = 'Username harus terdiri dari 4 sampai 30 karakter.';
                header('Location: /auth/register');
                exit;
            }

            // validasi simbol di username
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $_SESSION['error'] = 'Username hanya boleh berisi huruf, angka, dan underscore.';
                header('Location: /auth/register');
                exit;
            }

            // Validasi panjang password
            $passwordLength = strlen($password);

            if ($passwordLength < 8) {
                $_SESSION['error'] = 'Password minimal harus terdiri dari 8 karakter.';
                header('Location: /auth/register');
                exit;
            }

            if ($passwordLength > 64) {
                $_SESSION['error'] = 'Password maksimal terdiri dari 64 karakter.';
                header('Location: /auth/register');
                exit;
            }

            // Password harus mengandung huruf dan angka
            $hasLetter = preg_match('/[a-zA-Z]/', $password);
            $hasNumber = preg_match('/[0-9]/', $password);

            if (!$hasLetter || !$hasNumber) {
                $_SESSION['error'] = 'Password harus mengandung kombinasi huruf dan angka.';
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

            // penentuan role Ibu/Petani   
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

        if (!$user) {
            $_SESSION['error'] = 'Akun tidak ditemukan. Silakan register.';
            header('Location: /auth/login');
            exit;
        }

        if (!empty($user['deleted_at'])) {
            $_SESSION['error'] = 'Akun tidak ditemukan. Silakan register.';
            header('Location: /auth/login');
            exit;
        }

        if ((int) $user['is_active'] === 0) {
            $_SESSION['error'] = 'Akun anda tidak aktif. Hubungi admin untuk aktivasi.';
            header('Location: /auth/login');
            exit;
        }

        if (!password_verify($password, $user['password'])) {
            $_SESSION['error'] = 'Login gagal. Periksa username dan password.';
            header('Location: /auth/login');
            exit;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header('Location: /dashboard');
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
