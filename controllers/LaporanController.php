<?php

class LaporanController
{
    public function index()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $role = $_SESSION['role'] ?? '';
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        $laporanModel = new Laporan();

        if ($role === ROLE_ADMIN) {
            $data = $laporanModel->getAdminLaporan($startDate, $endDate);
            require '../views/laporan/admin.php';
            exit;
        }

        if ($role === ROLE_KADER) {
            $data = $laporanModel->getKaderLaporan($_SESSION['user_id'], $startDate, $endDate);
            require '../views/laporan/kader.php';
            exit;
        }

        if ($role === ROLE_IBU) {
            $data = $laporanModel->getIbuLaporan($_SESSION['user_id'], $startDate, $endDate);
            require '../views/laporan/ibu.php';
            exit;
        }

        if ($role === ROLE_PETANI) {
            $data = $laporanModel->getPetaniLaporan($_SESSION['user_id'], $startDate, $endDate);
            require '../views/laporan/petani.php';
            exit;
        }

        header('Location: /dashboard');
        exit;
    }

    public function downloadPdf()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        $role = $_SESSION['role'] ?? '';
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        $laporanModel = new Laporan();

        if ($role === ROLE_ADMIN) {
            $data = $laporanModel->getAdminLaporan($startDate, $endDate);
            $view = '../views/laporan/pdf/admin.php';
        } elseif ($role === ROLE_KADER) {
            $data = $laporanModel->getKaderLaporan($_SESSION['user_id'], $startDate, $endDate);
            $view = '../views/laporan/pdf/kader.php';
        } elseif ($role === ROLE_IBU) {
            $data = $laporanModel->getIbuLaporan($_SESSION['user_id'], $startDate, $endDate);
            $view = '../views/laporan/pdf/ibu.php';
        } elseif ($role === ROLE_PETANI) {
            $data = $laporanModel->getPetaniLaporan($_SESSION['user_id'], $startDate, $endDate);
            $view = '../views/laporan/pdf/petani.php';
        } else {
            header('Location: /dashboard');
            exit;
        }

        require_once '../vendor/autoload.php';

        ob_start();
        require $view;
        $html = ob_get_clean();

        $orientation = ($role === ROLE_ADMIN) ? 'landscape' : 'portrait';

        $dompdf = new Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', $orientation);
        $dompdf->render();

        $filename = 'laporan-' . strtolower($role) . '-' . date('Ymd') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }
}
