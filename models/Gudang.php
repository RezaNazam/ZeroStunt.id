<?php

class Gudang
{
    protected $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function create($nama_gudang, $lokasi_gudang, $jenis_gudang, $alamat_lengkap, $nama_pengelola)
    {
        $stmt = mysqli_prepare(
            $this->db,
            "INSERT INTO gudang (nama_gudang, lokasi_gudang, jenis_gudang, alamat_lengkap, nama_pengelola)
             VALUES (?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            'sssss',
            $nama_gudang,
            $lokasi_gudang,
            $jenis_gudang,
            $alamat_lengkap,
            $nama_pengelola
        );

        $executed = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $executed;
    }

    public function all()
    {
        $result = mysqli_query($this->db, "SELECT * FROM gudang WHERE is_deleted = 0 ORDER BY id_gudang DESC");
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function findById($id_gudang)
    {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM gudang WHERE id_gudang = ? AND is_deleted = 0 LIMIT 1");

        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $id_gudang);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data;
    }

    public function update($id_gudang, $nama_gudang, $lokasi_gudang, $jenis_gudang, $alamat_lengkap, $nama_pengelola)
    {
        $stmt = mysqli_prepare(
            $this->db,
            "UPDATE gudang 
            SET nama_gudang = ?, lokasi_gudang = ?, jenis_gudang = ?, alamat_lengkap = ?, nama_pengelola = ? 
            WHERE id_gudang = ? AND is_deleted = 0"
        );

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            'sssssi',
            $nama_gudang,
            $lokasi_gudang,
            $jenis_gudang,
            $alamat_lengkap,
            $nama_pengelola,
            $id_gudang
        );

        $executed = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $executed;
    }

    public function delete($id_gudang)
    {
        $stmt = mysqli_prepare($this->db, "UPDATE gudang SET is_deleted = 1 WHERE id_gudang = ?");

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'i', $id_gudang);
        $executed = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $executed;
    }
}