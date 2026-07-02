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
        $query = "
        SELECT 
            p.*,
            i.nama_ibu,
            a.nama_anak,
            g.nama_gudang,
            pg.nama_paket
        FROM t_penyerahan p
        JOIN ibu i 
            ON p.id_ibu = i.id_ibu
        LEFT JOIN anak a 
            ON p.id_anak = a.id_anak
        LEFT JOIN gudang g 
            ON p.id_gudang = g.id_gudang
        LEFT JOIN paket_gizi pg 
            ON p.id_paket = pg.id_paket
        ORDER BY p.tanggal_penyerahan DESC, p.id_penyerahan DESC";

        $result = mysqli_query($this->db, $query);

        if (!$result) {
            return [];
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
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

    public function getRiwayatBantuanByIbuUser($idIbu)
    {
        $query = "
        SELECT 
            p.id_penyerahan,
            p.no_penyerahan,
            p.id_ibu,
            p.id_anak,
            p.id_gudang,
            p.id_paket,
            p.tanggal_penyerahan,
            p.status_penyerahan,
            p.catatan,

            i.nama_ibu,
            a.nama_anak,
            g.nama_gudang AS nama_posyandu,
            pg.nama_paket,

            COUNT(pd.id_penyerahan_detail) AS total_item,

            GROUP_CONCAT(
                CONCAT(
                    k.nama_komoditas,
                    ' ',
                    pd.jumlah,
                    ' ',
                    COALESCE(s.singkat, '-')
                )
                ORDER BY k.nama_komoditas ASC
                SEPARATOR '||'
            ) AS detail_bantuan
        FROM t_penyerahan p
        JOIN ibu i 
            ON p.id_ibu = i.id_ibu
        LEFT JOIN anak a 
            ON p.id_anak = a.id_anak
        LEFT JOIN gudang g 
            ON p.id_gudang = g.id_gudang
        LEFT JOIN paket_gizi pg 
            ON p.id_paket = pg.id_paket
        LEFT JOIN t_penyerahan_detail pd 
            ON p.id_penyerahan = pd.id_penyerahan
        LEFT JOIN komoditas_pangan k 
            ON pd.id_komoditas = k.id_komoditas
        LEFT JOIN satuan s 
            ON k.id_satuan = s.id_satuan
        WHERE p.id_ibu = ?
        GROUP BY
            p.id_penyerahan,
            p.no_penyerahan,
            p.id_ibu,
            p.id_anak,
            p.id_gudang,
            p.id_paket,
            p.tanggal_penyerahan,
            p.status_penyerahan,
            p.catatan,
            i.nama_ibu,
            a.nama_anak,
            g.nama_gudang,
            pg.nama_paket
        ORDER BY p.tanggal_penyerahan DESC, p.id_penyerahan DESC
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

    public function getDetailById($idPenyerahan)
    {
        $query = "
        SELECT 
            p.id_penyerahan,
            p.no_penyerahan,
            p.id_ibu,
            p.id_anak,
            p.id_gudang,
            p.id_paket,
            p.tanggal_penyerahan,
            p.status_penyerahan,
            p.catatan,
            p.tgl_created,

            i.nama_ibu,
            i.NIK_ibu,
            i.no_telp,
            i.alamat,

            a.nama_anak,
            a.NIK_anak,
            a.tgl_lahir,
            a.jenis_kelamin,
            a.st_gizi_skrg,
            a.skala_prioritas,

            g.nama_gudang AS nama_posyandu,
            pg.nama_paket
        FROM t_penyerahan p
        JOIN ibu i ON p.id_ibu = i.id_ibu
        LEFT JOIN anak a ON p.id_anak = a.id_anak
        LEFT JOIN gudang g ON p.id_gudang = g.id_gudang
        LEFT JOIN paket_gizi pg ON p.id_paket = pg.id_paket
        WHERE p.id_penyerahan = ?
        LIMIT 1
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $idPenyerahan);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $penyerahan = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        if (!$penyerahan) {
            return null;
        }

        $detailQuery = "
        SELECT 
            pd.id_penyerahan_detail,
            pd.id_komoditas,
            pd.jumlah,
            k.nama_komoditas,
            s.singkat AS satuan
        FROM t_penyerahan_detail pd
        JOIN komoditas_pangan k ON pd.id_komoditas = k.id_komoditas
        LEFT JOIN satuan s ON k.id_satuan = s.id_satuan
        WHERE pd.id_penyerahan = ?
        ORDER BY k.nama_komoditas ASC
    ";

        $detailStmt = mysqli_prepare($this->db, $detailQuery);

        if (!$detailStmt) {
            $penyerahan['details'] = [];
            return $penyerahan;
        }

        mysqli_stmt_bind_param($detailStmt, 'i', $idPenyerahan);
        mysqli_stmt_execute($detailStmt);

        $detailResult = mysqli_stmt_get_result($detailStmt);
        $penyerahan['details'] = mysqli_fetch_all($detailResult, MYSQLI_ASSOC);

        mysqli_stmt_close($detailStmt);

        return $penyerahan;
    }

    public function markAsDiserahkan($idPenyerahan)
    {
        $query = "
        UPDATE t_penyerahan
        SET status_penyerahan = 'Diserahkan'
        WHERE id_penyerahan = ?
          AND status_penyerahan = 'Diproses'
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'i', $idPenyerahan);
        $executed = mysqli_stmt_execute($stmt);

        $affectedRows = mysqli_stmt_affected_rows($stmt);

        mysqli_stmt_close($stmt);

        return $executed && $affectedRows > 0;
    }

    public function getIbuOptionsByGudang($idGudang)
    {
        $query = "
        SELECT 
            id_ibu,
            nama_ibu,
            NIK_ibu,
            alamat,
            no_telp,
            id_gudang
        FROM ibu
        WHERE id_gudang = ?
        ORDER BY nama_ibu ASC
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'i', $idGudang);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_stmt_close($stmt);

        return $data;
    }

    public function getAnakOptionsByGudang($idGudang)
    {
        $query = "
        SELECT 
            a.id_anak,
            a.id_ibu,
            a.nama_anak,
            a.NIK_anak,
            a.tgl_lahir,
            a.jenis_kelamin,
            a.st_gizi_skrg,
            a.skala_prioritas,
            i.nama_ibu,
            i.id_gudang
        FROM anak a
        JOIN ibu i 
            ON a.id_ibu = i.id_ibu
        WHERE i.id_gudang = ?
        ORDER BY a.nama_anak ASC
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'i', $idGudang);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_stmt_close($stmt);

        return $data;
    }

    public function getGudangById($idGudang)
    {
        $query = "
        SELECT 
            id_gudang,
            nama_gudang,
            jenis_gudang,
            alamat_lengkap
        FROM gudang
        WHERE id_gudang = ?
        LIMIT 1
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $idGudang);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        return $data ?: null;
    }

    public function isIbuInGudang($idIbu, $idGudang)
    {
        $query = "
        SELECT id_ibu
        FROM ibu
        WHERE id_ibu = ?
          AND id_gudang = ?
        LIMIT 1
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'ii', $idIbu, $idGudang);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        return !empty($data);
    }

    public function isAnakValidForPenyerahan($idAnak, $idIbu, $idGudang)
    {
        $query = "
        SELECT 
            a.id_anak
        FROM anak a
        JOIN ibu i 
            ON a.id_ibu = i.id_ibu
        WHERE a.id_anak = ?
          AND a.id_ibu = ?
          AND i.id_gudang = ?
        LIMIT 1
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'iii', $idAnak, $idIbu, $idGudang);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        return !empty($data);
    }
}
