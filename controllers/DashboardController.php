<?php
class DashboardController
{
    public function index()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        // Cek profil udah lengkap atau belum
        $userModel = new User();
        if (!$userModel->isProfileComplete($_SESSION['user_id'], $_SESSION['role'])) {

            // jika ibu, redirect ke form ibu
            if ($_SESSION['role'] === ROLE_IBU) {
                header('Location: /master/ibu/create');
                exit;
            }

            // jika petani, redirect ke form petani
            if ($_SESSION['role'] === ROLE_PETANI) {
                header('Location: /master/petani/create');
                exit;
            }
        }

        // Kalo rolenya admin, redirect ke dashboard admin
        if ($_SESSION['role'] === ROLE_ADMIN) {
            require '../views/dashboard/admin.php';
            exit;
        }

        // Kalo rolenya ibu, redirect ke dashboard ibu
        if ($_SESSION['role'] === ROLE_IBU) {
            require '../views/dashboard/ibu.php';
            exit;
        }

        // Kalo rolenya petani, redirect ke dashboard petani
        if ($_SESSION['role'] === ROLE_PETANI) {
            require '../views/dashboard/petani.php';
            exit;
        }

        // Kalo rolenya kader, redirect ke dashboard kader
        if ($_SESSION['role'] === ROLE_KADER) {
            require '../views/dashboard/kader.php';
            exit;
        }

        // Default to admin dashboard
        require '../views/dashboard/admin.php';
    }

    public function pengadaan()
    {
    require '../views/dashboard/pengadaan.php';
    }

    public function riwayatEkonomi()
{
    require '../views/dashboard/riwayat-ekonomi.php';
}

public function profilLahan()
{
    require '../views/dashboard/profil-lahan.php';
}

}