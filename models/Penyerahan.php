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

    public function getAnakOptions()
    {
        $query = "
        SELECT 
            a.id_anak,
            a.id_ibu,
            a.nama_anak,
            a.st_gizi_skrg,
            a.skala_prioritas,
            i.nama_ibu
        FROM anak a
        JOIN ibu i ON a.id_ibu = i.id_ibu
        WHERE a.deleted_at IS NULL
        ORDER BY i.nama_ibu ASC, a.nama_anak ASC
    ";

        $result = mysqli_query($this->db, $query);

        if (!$result) {
            return [];
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function createFromPaket($idIbu, $idAnak, $idGudang, $idPaket, $tanggalPenyerahan, $catatan)
    {
        $query = "CALL sp_buat_penyerahan_dari_paket(?, ?, ?, ?, ?, ?, @id_penyerahan)";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            throw new Exception('Gagal menyiapkan stored procedure penyerahan.');
        }

        mysqli_stmt_bind_param(
            $stmt,
            'iiiiss',
            $idIbu,
            $idAnak,
            $idGudang,
            $idPaket,
            $tanggalPenyerahan,
            $catatan
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        while (mysqli_more_results($this->db)) {
            mysqli_next_result($this->db);

            if ($result = mysqli_store_result($this->db)) {
                mysqli_free_result($result);
            }
        }

        $result = mysqli_query($this->db, "SELECT @id_penyerahan AS id_penyerahan");
        $row = mysqli_fetch_assoc($result);

        return (int) ($row['id_penyerahan'] ?? 0);
    }

    public function getRiwayatBantuanByIbuUser($idUser)
    {
        $query = "
        SELECT 
            p.id_penyerahan,
            p.no_penyerahan,
            p.id_ibu,
            p.id_anak,
            p.id_gudang,
            p.tanggal_penyerahan,
            p.status_penyerahan,
            p.catatan,
            p.tgl_created,

            a.nama_anak,
            i.nama_ibu,
            g.nama_gudang AS nama_posyandu,

            pd.id_penyerahan_detail,
            pd.id_komoditas,
            pd.jumlah,

            k.nama_komoditas,
            s.singkat AS satuan
        FROM t_penyerahan p
        JOIN ibu i 
            ON p.id_ibu = i.id_ibu
        LEFT JOIN anak a 
            ON p.id_anak = a.id_anak
        LEFT JOIN gudang g 
            ON p.id_gudang = g.id_gudang
        LEFT JOIN t_penyerahan_detail pd 
            ON p.id_penyerahan = pd.id_penyerahan
        LEFT JOIN komoditas_pangan k 
            ON pd.id_komoditas = k.id_komoditas
        LEFT JOIN satuan s 
            ON k.id_satuan = s.id_satuan
        WHERE i.id_ibu = ?
          AND p.status_penyerahan = 'Diserahkan'
        ORDER BY p.tanggal_penyerahan DESC, p.id_penyerahan DESC
    ";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, 'i', $idUser);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function createFromPrioritasAnak($idIbu, $idAnak, $idGudang, $tanggalPenyerahan, $catatan)
    {
        $query = "CALL sp_buat_penyerahan_dari_prioritas_anak(?, ?, ?, ?, ?, @id_penyerahan)";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            throw new Exception('Gagal menyiapkan stored procedure penyerahan.');
        }

        mysqli_stmt_bind_param(
            $stmt,
            'iiiss',
            $idIbu,
            $idAnak,
            $idGudang,
            $tanggalPenyerahan,
            $catatan
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        while (mysqli_more_results($this->db)) {
            mysqli_next_result($this->db);

            if ($result = mysqli_store_result($this->db)) {
                mysqli_free_result($result);
            }
        }

        $result = mysqli_query($this->db, "SELECT @id_penyerahan AS id_penyerahan");
        $row = mysqli_fetch_assoc($result);

        return (int) ($row['id_penyerahan'] ?? 0);
    }
}
