<?php

class Distribusi
{
    private $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function countAll()
    {
        $query = "
            SELECT COUNT(*) AS total
            FROM t_distribusi
            WHERE deleted_at IS NULL
        ";

        $result = mysqli_query($this->db, $query);
        $row = mysqli_fetch_assoc($result);

        return (int) ($row['total'] ?? 0);
    }

    public function getPaginated($limit, $offset)
    {
        $query = "
            SELECT 
                d.*,
                ga.nama_gudang AS gudang_asal,
                gt.nama_gudang AS gudang_tujuan,
                u1.username AS dibuat_oleh,
                u2.username AS diterima_oleh
            FROM t_distribusi d
            JOIN gudang ga ON d.id_gudang_asal = ga.id_gudang
            JOIN gudang gt ON d.id_gudang_tujuan = gt.id_gudang
            LEFT JOIN users u1 ON d.created_by = u1.id_user
            LEFT JOIN users u2 ON d.received_by = u2.id_user
            WHERE d.deleted_at IS NULL
            ORDER BY d.tanggal_distribusi DESC, d.id_distribusi DESC
            LIMIT ? OFFSET ?
        ";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, 'ii', $limit, $offset);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $distribusi = mysqli_fetch_all($result, MYSQLI_ASSOC);

        foreach ($distribusi as &$row) {
            $row['details'] = $this->getDetails($row['id_distribusi']);
        }

        return $distribusi;
    }

    public function getDetails($idDistribusi)
    {
        $query = "
            SELECT 
                dd.*,
                k.nama_komoditas,
                s.singkat AS satuan
            FROM t_distribusi_detail dd
            JOIN komoditas_pangan k ON dd.id_komoditas = k.id_komoditas
            LEFT JOIN satuan s ON k.id_satuan = s.id_satuan
            WHERE dd.id_distribusi = ?
            ORDER BY k.nama_komoditas ASC
        ";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, 'i', $idDistribusi);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getGudangOptions()
    {
        $query = "
        SELECT id_gudang, nama_gudang, jenis_gudang, lokasi_gudang
        FROM gudang
        WHERE is_deleted = 0
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
        WHERE k.is_deleted = 0
        ORDER BY k.nama_komoditas ASC
    ";

        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function generateNoDistribusi()
    {
        $prefix = 'DIST-' . date('Ymd') . '-';

        $query = "
        SELECT no_distribusi
        FROM t_distribusi
        WHERE no_distribusi LIKE ?
        ORDER BY no_distribusi DESC
        LIMIT 1
    ";

        $like = $prefix . '%';

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, 's', $like);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if (!$row) {
            return $prefix . '001';
        }

        $lastNumber = (int) substr($row['no_distribusi'], -3);
        $nextNumber = $lastNumber + 1;

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function createWithDetails(array $data, array $details)
    {
        mysqli_begin_transaction($this->db);

        try {
            $query = "
            INSERT INTO t_distribusi (
                no_distribusi,
                id_gudang_asal,
                id_gudang_tujuan,
                tanggal_distribusi,
                status_distribusi,
                catatan,
                created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ";

            $stmt = mysqli_prepare($this->db, $query);

            mysqli_stmt_bind_param(
                $stmt,
                'siisssi',
                $data['no_distribusi'],
                $data['id_gudang_asal'],
                $data['id_gudang_tujuan'],
                $data['tanggal_distribusi'],
                $data['status_distribusi'],
                $data['catatan'],
                $data['created_by']
            );

            mysqli_stmt_execute($stmt);

            $idDistribusi = mysqli_insert_id($this->db);

            $detailQuery = "
            INSERT INTO t_distribusi_detail (
                id_distribusi,
                id_komoditas,
                jumlah
            ) VALUES (?, ?, ?)
        ";

            $detailStmt = mysqli_prepare($this->db, $detailQuery);

            foreach ($details as $detail) {
                mysqli_stmt_bind_param(
                    $detailStmt,
                    'iid',
                    $idDistribusi,
                    $detail['id_komoditas'],
                    $detail['jumlah']
                );

                mysqli_stmt_execute($detailStmt);
            }

            mysqli_commit($this->db);
            return true;
        } catch (Throwable $e) {
            mysqli_rollback($this->db);
            return false;
        }
    }
    public function getGudangByJenis($jenisGudang)
    {
        $query = "
        SELECT id_gudang, nama_gudang, jenis_gudang, lokasi_gudang
        FROM gudang
        WHERE is_deleted = 0
        AND LOWER(jenis_gudang) = LOWER(?)
        ORDER BY nama_gudang ASC
    ";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, 's', $jenisGudang);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function findGudangById($idGudang)
    {
        $query = "
        SELECT id_gudang, nama_gudang, jenis_gudang, lokasi_gudang
        FROM gudang
        WHERE id_gudang = ?
        AND is_deleted = 0
        LIMIT 1
    ";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, 'i', $idGudang);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    public function findById($idDistribusi)
    {
        $query = "
        SELECT 
            d.*,
            ga.nama_gudang AS gudang_asal,
            gt.nama_gudang AS gudang_tujuan,
            u1.username AS dibuat_oleh,
            u2.username AS diterima_oleh
        FROM t_distribusi d
        JOIN gudang ga 
            ON d.id_gudang_asal = ga.id_gudang
        JOIN gudang gt 
            ON d.id_gudang_tujuan = gt.id_gudang
        LEFT JOIN users u1 
            ON d.created_by = u1.id_user
        LEFT JOIN users u2 
            ON d.received_by = u2.id_user
        WHERE d.id_distribusi = ?
          AND d.deleted_at IS NULL
        LIMIT 1
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $idDistribusi);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        if (!$data) {
            return null;
        }

        $data['details'] = $this->getDetails($idDistribusi);

        return $data;
    }

    public function markAsReceived($idDistribusi, $receivedBy)
    {
        $query = "
        UPDATE t_distribusi
        SET 
            status_distribusi = 'Diterima',
            received_by = ?,
            received_at = NOW()
        WHERE id_distribusi = ?
        AND status_distribusi = 'Dikirim'
        AND deleted_at IS NULL
    ";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, 'ii', $receivedBy, $idDistribusi);

        return mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0;
    }

    public function cancelDistribusi($idDistribusi)
    {
        $query = "
        UPDATE t_distribusi
        SET status_distribusi = 'Dibatalkan'
        WHERE id_distribusi = ?
        AND status_distribusi = 'Dikirim'
        AND deleted_at IS NULL
    ";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, 'i', $idDistribusi);

        return mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0;
    }
}
