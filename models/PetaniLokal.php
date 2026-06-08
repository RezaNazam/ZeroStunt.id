<?php

class PetaniLokal
{
    protected $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function create($id_petani, $nama_lahan, $alamat_lahan, $no_rekening, $kapasitas_panen_bulan)
    {
        $stmt = mysqli_prepare(
            $this->db,
            "INSERT INTO petani_lokal (id_petani, nama_lahan, alamat_lahan, no_rekening, kapasitas_panen_bulan) VALUES (?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            return false;
        }


        mysqli_stmt_bind_param(
            $stmt,
            'isssd',
            $id_petani,
            $nama_lahan,
            $alamat_lahan,
            $no_rekening,
            $kapasitas_panen_bulan
        );
        $executed = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $executed;
    }

    public function findByIdPetani($id_petani)
    {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM petani_lokal WHERE id_petani = ? LIMIT 1");

        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $id_petani);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return $data;

    }
}
