<?php

class Komoditas
{
    private $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function all()
    {
        $query = "SELECT komoditas_pangan.*, satuan.nama_satuan FROM komoditas_pangan LEFT JOIN satuan 
                    ON komoditas_pangan.id_satuan = satuan.id_satuan AND satuan.is_deleted = 0
                    WHERE komoditas_pangan.is_deleted = 0 ORDER BY komoditas_pangan.id_komoditas DESC";
        $result = mysqli_query($this->db, $query);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }

    public function findById($id_komoditas)
    {
        $query = "SELECT * FROM komoditas_pangan WHERE id_komoditas = ? AND is_deleted = 0 LIMIT 1";
        $stmt = mysqli_prepare($this->db, $query);

        mysqli_stmt_bind_param($stmt, "i", $id_komoditas);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    public function create($nama_komoditas, $kategori_gizi, $id_satuan, $deskripsi)
    {
        $query = "
            INSERT INTO komoditas_pangan 
            (nama_komoditas, kategori_gizi, id_satuan, deskripsi)
            VALUES (?, ?, ?, ?)
        ";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param(
            $stmt,
            "ssis",
            $nama_komoditas,
            $kategori_gizi,
            $id_satuan,
            $deskripsi
        );

        return mysqli_stmt_execute($stmt);
    }

    public function update($id_komoditas, $nama_komoditas, $kategori_gizi, $id_satuan, $deskripsi)
    {
        $query = "
            UPDATE komoditas_pangan
            SET 
                nama_komoditas = ?,
                kategori_gizi = ?,
                id_satuan = ?,
                deskripsi = ?
            WHERE id_komoditas = ? AND is_deleted = 0
        ";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param(
            $stmt,
            "ssisi",
            $nama_komoditas,
            $kategori_gizi,
            $id_satuan,
            $deskripsi,
            $id_komoditas
        );

        return mysqli_stmt_execute($stmt);
    }

    public function delete($id_komoditas)
    {
        $query = "UPDATE komoditas_pangan SET is_deleted = 1 WHERE id_komoditas = ?";
        $stmt = mysqli_prepare($this->db, $query);

        mysqli_stmt_bind_param($stmt, "i", $id_komoditas);

        return mysqli_stmt_execute($stmt);
    }
}
