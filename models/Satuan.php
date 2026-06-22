<?php

class Satuan
{
    private $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function all()
    {
        $query = mysqli_query(
            $this->db,
            "SELECT * FROM satuan WHERE is_deleted = 0 ORDER BY nama_satuan ASC"
        );

        return mysqli_fetch_all($query, MYSQLI_ASSOC);
    }

    public function create($nama, $singkat)
    {
        $stmt = mysqli_prepare(
            $this->db,
            "INSERT INTO satuan (nama_satuan, singkat)
             VALUES (?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ss",
            $nama,
            $singkat
        );

        return mysqli_stmt_execute($stmt);
    }

    public function find($id)
    {
        $stmt = mysqli_prepare(
            $this->db,
            "SELECT * FROM satuan WHERE id_satuan = ? AND is_deleted = 0 LIMIT 1"
        );

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }

    public function update($id, $nama, $singkat)
    {
        $stmt = mysqli_prepare(
            $this->db,
            "UPDATE satuan
         SET nama_satuan = ?, singkat = ?
         WHERE id_satuan = ? AND is_deleted = 0"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssi",
            $nama,
            $singkat,
            $id
        );

        return mysqli_stmt_execute($stmt);
    }

    public function delete($id)
    {
        $stmt = mysqli_prepare(
            $this->db,
            "UPDATE satuan
             SET is_deleted = 1
             WHERE id_satuan = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $id
        );

        return mysqli_stmt_execute($stmt);
    }
}
