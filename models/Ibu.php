<?php

class Ibu
{
    protected $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function create($id_ibu, $nik, $nama, $telp, $alamat, $is_pregnant, $id_gudang = null)
    {
        $stmt = mysqli_prepare(
            $this->db,
            "INSERT INTO ibu (id_ibu, NIK_ibu, nama_ibu, no_telp, alamat, id_gudang, is_pregnant) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            'issssi',
            $id_ibu,
            $nik,
            $nama,
            $telp,
            $alamat,
            $id_gudang,
            $is_pregnant
        );
        $executed = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $executed;
    }

    public function findByIdIbu($id_ibu)
    {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM ibu WHERE id_ibu = ? LIMIT 1");

        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param(
            $stmt,
            'i',
            $id_ibu
        );
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return $data;
    }
}