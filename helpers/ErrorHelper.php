<?php

class ErrorHelper
{
    public static function show(
        int $code,
        string $message,
        ?string $actionUrl = null,
        ?string $actionLabel = null
    ): void {
        http_response_code($code);

        $titles = [
            400 => 'Permintaan Tidak Valid',
            401 => 'Belum Terautentikasi',
            403 => 'Akses Ditolak',
            404 => 'Halaman Tidak Ditemukan',
            405 => 'Metode Tidak Diizinkan',
            500 => 'Terjadi Kesalahan',
        ];

        $errorCode = $code;
        $errorTitle = $titles[$code] ?? 'Terjadi Kesalahan';
        $errorMessage = $message;

        if ($actionUrl === null) {
            $actionUrl = !empty($_SESSION['user_id'])
                ? '/dashboard'
                : '/landing';
        }

        if ($actionLabel === null) {
            $actionLabel = !empty($_SESSION['user_id'])
                ? 'Kembali ke Dashboard'
                : 'Kembali ke Landing';
        }

        require __DIR__ . '/../views/error/error.php';
        exit;
    }
}