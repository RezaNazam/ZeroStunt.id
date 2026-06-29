<?php

class Stok
{
    private $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function getByJenisGudang($jenisGudang)
    {
        $query = "
            SELECT
                sl.id_stok_log,
                sl.id_gudang,
                g.nama_gudang,
                g.jenis_gudang,
                sl.id_komoditas,
                k.nama_komoditas,
                s.singkat,

                sl.qty_in,
                sl.qty_out,
                sl.qty_current,

                sl.qty_current AS jumlah_stok,
                sl.last_updated AS tgl_update

            FROM stok_log sl
            JOIN gudang g 
                ON sl.id_gudang = g.id_gudang
            JOIN komoditas_pangan k 
                ON sl.id_komoditas = k.id_komoditas
            LEFT JOIN satuan s 
                ON k.id_satuan = s.id_satuan

            WHERE LOWER(g.jenis_gudang) = LOWER(?)

            ORDER BY g.nama_gudang ASC, k.nama_komoditas ASC
        ";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, 's', $jenisGudang);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getByGudang($idGudang)
    {
        $query = "
            SELECT
                sl.id_stok_log,
                sl.id_gudang,
                g.nama_gudang,
                g.jenis_gudang,
                sl.id_komoditas,
                k.nama_komoditas,
                s.singkat,

                sl.qty_in,
                sl.qty_out,
                sl.qty_current,

                sl.qty_current AS jumlah_stok,
                sl.last_updated AS tgl_update

            FROM stok_log sl
            JOIN gudang g 
                ON sl.id_gudang = g.id_gudang
            JOIN komoditas_pangan k 
                ON sl.id_komoditas = k.id_komoditas
            LEFT JOIN satuan s 
                ON k.id_satuan = s.id_satuan

            WHERE sl.id_gudang = ?

            ORDER BY k.nama_komoditas ASC
        ";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, 'i', $idGudang);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}