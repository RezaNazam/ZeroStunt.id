<?php

class Ibu
{
    protected $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function isNikExists($nik)
{
    $stmt = mysqli_prepare(
        $this->db,
        "SELECT id_ibu 
         FROM ibu 
         WHERE NIK_ibu = ? 
         LIMIT 1"
    );

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, 's', $nik);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return $row !== null;
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
            'issssii', // i=id_ibu, s=NIK, s=nama, s=telp, s=alamat, i=id_gudang, i=is_pregnant
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

    public function all()
    {
        $result = mysqli_query($this->db, "SELECT * FROM ibu ORDER BY nama_ibu ASC");
        $ibus = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $ibus[] = $row;
        }
        return $ibus;
    }

    public function getIbuDashboard($idUser)
    {
        // 1. Ambil data Ibu berdasarkan ID User
        $ibu = $this->getIbuByUserId($idUser);

        if (!$ibu) {
            return [
                'ibu' => null,
                'anaks' => []
            ];
        }

        // 2. Ambil data anak-anak dari ibu tersebut
        $idIbu = (int) $ibu['id_ibu'];
        $anaks = $this->getAnakByIbuId($idIbu);

        return [
            'ibu' => $ibu,
            'anaks' => $anaks
        ];
    }

    private function getIbuByUserId($idUser)
    {
        // Pastikan kolom is_pregnant ikut terambil (pake SELECT * sudah aman asal kolomnya ada di DB)
        $query = "
            SELECT *
            FROM ibu
            WHERE id_ibu = ?
            LIMIT 1
        ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $idUser);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        return $data ?: null;
    }

    private function getAnakByIbuId($idIbu)
    {
        // Ambil data anak beserta log pemeriksaan terakhir
        // Disesuaikan dengan kebutuhan view kamu yang butuh data tinggi, berat, status gizi, dll.
        $query = "
            SELECT 
                a.*,
                (SELECT berat_badan FROM t_pemeriksaan WHERE id_anak = a.id_anak ORDER BY tanggal_pemeriksaan DESC LIMIT 1) as berat_badan_terakhir,
                (SELECT tinggi_badan FROM t_pemeriksaan WHERE id_anak = a.id_anak ORDER BY tanggal_pemeriksaan DESC LIMIT 1) as tinggi_badan_terakhir,
                (SELECT status_gizi FROM t_pemeriksaan WHERE id_anak = a.id_anak ORDER BY tanggal_pemeriksaan DESC LIMIT 1) as st_gizi_skrg,
                (SELECT tanggal_pemeriksaan FROM t_pemeriksaan WHERE id_anak = a.id_anak ORDER BY tanggal_pemeriksaan DESC LIMIT 1) as tanggal_pemeriksaan_terakhir,
                (SELECT usia_bulan FROM t_pemeriksaan WHERE id_anak = a.id_anak ORDER BY tanggal_pemeriksaan DESC LIMIT 1) as usia_bulan_terakhir
            FROM anak a
            WHERE a.id_ibu = ? 
              AND a.deleted_at IS NULL
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
}