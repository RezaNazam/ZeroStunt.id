<?php

class PaginationHelper
{
    /**
     * Memotong array data berdasarkan halaman aktif (Pagination Array Global)
     */
    public static function paginateArray(array $dataArray, int $perHalaman = 30, string $pageParam = 'page'): array
    {
        $totalData = count($dataArray);
        $totalHalaman = (int) ceil($totalData / $perHalaman);

        // Ambil nomor halaman aktif dari URL (?page=X)
        $halamanAktif = isset($_GET[$pageParam]) ? (int) $_GET[$pageParam] : 1;
        if ($halamanAktif < 1) {$halamanAktif = 1;}
        if ($halamanAktif > $totalHalaman && $totalHalaman > 0) {$halamanAktif = $totalHalaman;}

        // Hitung index awal pemotongan
        $offset = ($halamanAktif - 1) * $perHalaman;

        // Potong array
        $paginatedData = array_slice($dataArray, $offset, $perHalaman);

        // Kembalikan data bundle terstruktur
        return [
            'data' => $paginatedData,
            'halaman_aktif' => $halamanAktif,
            'total_halaman' => $totalHalaman,
            'total_data' => $totalData,
            'per_halaman' => $perHalaman,
            'page_param' => $pageParam
        ];
    }
}