<?php

class Anak
{
    protected $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function isNikExists($nik_anak)
    {
        $stmt = mysqli_prepare(
            $this->db,
            "SELECT id_anak
         FROM anak
         WHERE NIK_anak = ?
         LIMIT 1"
        );

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 's', $nik_anak);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        return $row !== null;
    }

    public function create($id_ibu, $nik_anak, $nama_anak, $tgl_lahir, $jenis_kelamin)
    {
        $stmt = mysqli_prepare($this->db, "INSERT INTO anak (id_ibu, NIK_anak, nama_anak, tgl_lahir, jenis_kelamin) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'issss', $id_ibu, $nik_anak, $nama_anak, $tgl_lahir, $jenis_kelamin);
        $executed = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $executed;
    }

    public function findById($id_anak)
    {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM anak WHERE id_anak = ? AND deleted_at IS NULL LIMIT 1");
        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $id_anak);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $anak = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return $anak;
    }

    public function findByIbu($id_ibu)
    {
        $stmt = mysqli_prepare(
            $this->db,
            "SELECT anak.*, ibu.nama_ibu FROM anak
             LEFT JOIN ibu ON anak.id_ibu = ibu.id_ibu AND ibu.deleted_at IS NULL
             WHERE anak.id_ibu = ? AND anak.deleted_at IS NULL
             ORDER BY anak.tgl_lahir DESC"
        );
        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'i', $id_ibu);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $anaks = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $anaks[] = $row;
        }
        mysqli_stmt_close($stmt);

        return $anaks;
    }

    public function all()
    {
        $result = mysqli_query(
            $this->db,
            "SELECT anak.*, ibu.nama_ibu 
             FROM anak 
             LEFT JOIN ibu ON anak.id_ibu = ibu.id_ibu AND ibu.deleted_at IS NULL
             WHERE anak.deleted_at IS NULL
             ORDER BY anak.tgl_lahir DESC"
        );
        $anaks = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $anaks[] = $row;
        }
        return $anaks;
    }

    public function update($id_anak, $id_ibu, $nik_anak, $nama_anak, $tgl_lahir, $jenis_kelamin)
    {
        $stmt = mysqli_prepare($this->db, "UPDATE anak SET id_ibu = ?, NIK_anak = ?, nama_anak = ?, tgl_lahir = ?, jenis_kelamin = ? WHERE id_anak = ? AND deleted_at IS NULL");
        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'issssi', $id_ibu, $nik_anak, $nama_anak, $tgl_lahir, $jenis_kelamin, $id_anak);
        $executed = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $executed;
    }

    public function delete($id_anak)
    {
        $stmt = mysqli_prepare($this->db, "UPDATE anak SET deleted_at = NOW() WHERE id_anak = ? AND deleted_at IS NULL");
        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'i', $id_anak);
        $executed = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $executed;
    }
}
