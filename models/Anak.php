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
        $stmt = mysqli_prepare($this->db, "SELECT * FROM anak WHERE id_anak = ? LIMIT 1");
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
             LEFT JOIN ibu ON anak.id_ibu = ibu.id_ibu
             WHERE anak.id_ibu = ?
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
             LEFT JOIN ibu ON anak.id_ibu = ibu.id_ibu 
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
        $stmt = mysqli_prepare($this->db, "UPDATE anak SET id_ibu = ?, NIK_anak = ?, nama_anak = ?, tgl_lahir = ?, jenis_kelamin = ? WHERE id_anak = ?");
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
        $stmt = mysqli_prepare($this->db, "UPDATE anak SET deleted_at = NOW() WHERE id_anak = ?");
        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'i', $id_anak);
        $executed = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $executed;
    }

    public function getPrioritasTerbaruByIbu($id_ibu)
    {
        $stmt = mysqli_prepare(
            $this->db,
            "SELECT *,
            TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) as umur
            FROM anak
            WHERE id_ibu = ?
            ORDER BY tgl_lahir DESC
            LIMIT 1"
        );

        mysqli_stmt_bind_param($stmt, 'i', $id_ibu);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }

    public function getByIbuIdWithLatestPemeriksaan($idIbu)
    {
        $query = "
        SELECT 
            a.*,
            i.nama_ibu,
            p.tanggal_pemeriksaan AS tanggal_pemeriksaan_terakhir,
            p.berat_badan AS berat_badan_terakhir,
            p.tinggi_badan AS tinggi_badan_terakhir,
            p.usia_bulan AS usia_bulan_terakhir,
            p.status_gizi AS status_gizi_terakhir
        FROM anak a
        JOIN ibu i 
            ON a.id_ibu = i.id_ibu
        LEFT JOIN t_pemeriksaan p
            ON p.id_anak = a.id_anak
            AND p.id_pemeriksaan = (
                SELECT MAX(p2.id_pemeriksaan)
                FROM t_pemeriksaan p2
                WHERE p2.id_anak = a.id_anak
            )
        WHERE a.id_ibu = ?
          AND a.deleted_at IS NULL
        ORDER BY a.nama_anak ASC
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'i', $idIbu);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_stmt_close($stmt);

        return $data;
    }

    public function isAnakInGudang($idAnak, $idGudang)
{
    $query = "
        SELECT 
            a.id_anak
        FROM anak a
        JOIN ibu i 
            ON a.id_ibu = i.id_ibu
        WHERE a.id_anak = ?
          AND i.id_gudang = ?
        LIMIT 1
    ";

    $stmt = mysqli_prepare($this->db, $query);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, 'ii', $idAnak, $idGudang);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return !empty($data);
}
}
