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
        $user = $userModel->findByid($_SESSION['user_id']);
        if (!$userModel->isProfileComplete($user['id_user'], $user['role'])) {
            if ($user['role'] === ROLE_IBU) {
                header('Location: /master/ibu/create');
                exit;
            }

            if ($user['role'] === ROLE_PETANI) {
                $petaniModel = new PetaniLokal();
                $petani = $petaniModel->findByUserId($user['id_user']);

                if (!$petani) {
                    header('Location: /master/petani/create');
                    exit;
                }

                header('Location: /master/petani/lahan/create');
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
}
