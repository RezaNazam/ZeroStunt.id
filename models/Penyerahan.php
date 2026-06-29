<?php

class Penyerahan
{
    protected $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function all()
    {
        $result = mysqli_query(
            $this->db,
            "SELECT
    p.*,
    ibu.nama_ibu
    FROM t_penyerahan p
    LEFT JOIN ibu
    ON p.id_ibu = ibu.id_ibu
    ORDER BY p.id_penyerahan DESC"
        );

        $data = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }


    public function create($data)
    {
        $stmt = mysqli_prepare(
            $this->db,
            "INSERT INTO t_penyerahan
            (no_penyerahan, id_ibu, id_anak, id_gudang, tanggal_penyerahan, catatan, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $no = 'PNY-' . date('YmdHis');

        mysqli_stmt_bind_param(
            $stmt,
            'siiissi',
            $no,
            $data['id_ibu'],
            $data['id_anak'],
            $data['id_gudang'],
            $data['tanggal_penyerahan'],
            $data['catatan'],
            $_SESSION['user_id']
        );

        $ok = mysqli_stmt_execute($stmt);

        if (!$ok) return false;

        return mysqli_insert_id($this->db);
    }


    public function createDetail(
        $id_penyerahan,
        $id_komoditas,
        $jumlah
    ) {
        $stmt = mysqli_prepare(
            $this->db,
            "INSERT INTO t_penyerahan_detail
    (
    id_penyerahan,
    id_komoditas,
    jumlah
    )
    VALUES (?, ?, ?)"
        );
        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            'iid',
            $id_penyerahan,
            $id_komoditas,
            $jumlah
        );

        $executed = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        return $executed;
    }

    public function serahkan($id_penyerahan)
    {
        $stmt = mysqli_prepare(
            $this->db,
            "UPDATE t_penyerahan
    SET status_penyerahan = 'Diserahkan'
    WHERE id_penyerahan = ?"
        );

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            'i',
            $id_penyerahan
        );

        $executed = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        return $executed;
    }

    public function getIbuOptions()
    {
        $query = "
        SELECT 
            i.id_ibu,
            i.nama_ibu
        FROM ibu i
        ORDER BY i.nama_ibu ASC
    ";

        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getGudangPosyanduOptions()
    {
        $query = "
        SELECT 
            id_gudang,
            nama_gudang,
            jenis_gudang
        FROM gudang
        WHERE LOWER(jenis_gudang) = 'posyandu'
        ORDER BY nama_gudang ASC
    ";

        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getKomoditasOptions()
    {
        $query = "
        SELECT 
            k.id_komoditas,
            k.nama_komoditas,
            s.singkat AS satuan
        FROM komoditas_pangan k
        LEFT JOIN satuan s ON k.id_satuan = s.id_satuan
        WHERE COALESCE(k.is_deleted, 0) = 0
        ORDER BY k.nama_komoditas ASC
    ";

        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
