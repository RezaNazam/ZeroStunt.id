<?php

class StandarPertumbuhan
{
    protected $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    // ambil semua dara standar pertumbuhan
    public function all(): array
    {
        $query = "SELECT * FROM standar_pertumbuhan ORDER BY tipe_standar DESC, jenis_kelamin ASC, usia_bulan ASC";
        $result = mysqli_query($this->db, $query);

        if (!$result) {
            return [];
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}