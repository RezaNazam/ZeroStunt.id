<?php

class PaketGizi
{
    protected $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function getAllWithDetails()
    {
        $query = "
            SELECT 
                p.id_paket,
                p.kode_prioritas,
                p.nama_paket,
                p.deskripsi,
                p.is_active,
                pd.id_paket_detail,
                pd.id_komoditas,
                pd.jumlah,
                k.nama_komoditas,
                COALESCE(s.singkat, '-') AS satuan
            FROM paket_gizi p
            LEFT JOIN paket_gizi_detail pd
                ON p.id_paket = pd.id_paket
            LEFT JOIN komoditas_pangan k
                ON pd.id_komoditas = k.id_komoditas
            LEFT JOIN satuan s
                ON k.id_satuan = s.id_satuan
            ORDER BY p.id_paket ASC, k.nama_komoditas ASC
        ";

        $result = mysqli_query($this->db, $query);

        if (!$result) {
            return [];
        }

        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $pakets = [];

        foreach ($rows as $row) {
            $idPaket = $row['id_paket'];

            if (!isset($pakets[$idPaket])) {
                $pakets[$idPaket] = [
                    'id_paket' => $row['id_paket'],
                    'kode_prioritas' => $row['kode_prioritas'],
                    'nama_paket' => $row['nama_paket'],
                    'deskripsi' => $row['deskripsi'],
                    'is_active' => $row['is_active'],
                    'details' => []
                ];
            }

            if (!empty($row['id_paket_detail'])) {
                $pakets[$idPaket]['details'][] = [
                    'id_paket_detail' => $row['id_paket_detail'],
                    'id_komoditas' => $row['id_komoditas'],
                    'nama_komoditas' => $row['nama_komoditas'],
                    'jumlah' => $row['jumlah'],
                    'satuan' => $row['satuan']
                ];
            }
        }

        return array_values($pakets);
    }

    public function findWithDetails($idPaket)
    {
        $query = "
            SELECT 
                p.id_paket,
                p.kode_prioritas,
                p.nama_paket,
                p.deskripsi,
                p.is_active,
                pd.id_paket_detail,
                pd.id_komoditas,
                pd.jumlah,
                k.nama_komoditas,
                COALESCE(s.singkat, '-') AS satuan
            FROM paket_gizi p
            LEFT JOIN paket_gizi_detail pd
                ON p.id_paket = pd.id_paket
            LEFT JOIN komoditas_pangan k
                ON pd.id_komoditas = k.id_komoditas
            LEFT JOIN satuan s
                ON k.id_satuan = s.id_satuan
            WHERE p.id_paket = ?
            ORDER BY k.nama_komoditas ASC
        ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $idPaket);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_stmt_close($stmt);

        if (empty($rows)) {
            return null;
        }

        $paket = [
            'id_paket' => $rows[0]['id_paket'],
            'kode_prioritas' => $rows[0]['kode_prioritas'],
            'nama_paket' => $rows[0]['nama_paket'],
            'deskripsi' => $rows[0]['deskripsi'],
            'is_active' => $rows[0]['is_active'],
            'details' => []
        ];

        foreach ($rows as $row) {
            if (!empty($row['id_paket_detail'])) {
                $paket['details'][] = [
                    'id_paket_detail' => $row['id_paket_detail'],
                    'id_komoditas' => $row['id_komoditas'],
                    'nama_komoditas' => $row['nama_komoditas'],
                    'jumlah' => $row['jumlah'],
                    'satuan' => $row['satuan']
                ];
            }
        }

        return $paket;
    }

    public function getKomoditasOptions()
    {
        $query = "
            SELECT 
                k.id_komoditas,
                k.nama_komoditas,
                COALESCE(s.singkat, '-') AS satuan
            FROM komoditas_pangan k
            LEFT JOIN satuan s
                ON k.id_satuan = s.id_satuan
            WHERE COALESCE(k.is_deleted, 0) = 0
            ORDER BY k.nama_komoditas ASC
        ";

        $result = mysqli_query($this->db, $query);

        if (!$result) {
            return [];
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function updatePaket($idPaket, $namaPaket, $deskripsi, $isActive)
    {
        $query = "
            UPDATE paket_gizi
            SET 
                nama_paket = ?,
                deskripsi = ?,
                is_active = ?
            WHERE id_paket = ?
        ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'ssii', $namaPaket, $deskripsi, $isActive, $idPaket);
        $executed = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        return $executed;
    }

    public function replaceDetails($idPaket, array $details)
    {
        mysqli_begin_transaction($this->db);

        try {
            $deleteQuery = "
                DELETE FROM paket_gizi_detail
                WHERE id_paket = ?
            ";

            $deleteStmt = mysqli_prepare($this->db, $deleteQuery);

            if (!$deleteStmt) {
                throw new Exception('Gagal menyiapkan query hapus detail paket.');
            }

            mysqli_stmt_bind_param($deleteStmt, 'i', $idPaket);
            mysqli_stmt_execute($deleteStmt);
            mysqli_stmt_close($deleteStmt);

            $insertQuery = "
                INSERT INTO paket_gizi_detail (
                    id_paket,
                    id_komoditas,
                    jumlah
                ) VALUES (?, ?, ?)
            ";

            $insertStmt = mysqli_prepare($this->db, $insertQuery);

            if (!$insertStmt) {
                throw new Exception('Gagal menyiapkan query tambah detail paket.');
            }

            foreach ($details as $detail) {
                $idKomoditas = (int) ($detail['id_komoditas'] ?? 0);
                $jumlah = (float) ($detail['jumlah'] ?? 0);

                if ($idKomoditas <= 0 || $jumlah <= 0) {
                    continue;
                }

                mysqli_stmt_bind_param(
                    $insertStmt,
                    'iid',
                    $idPaket,
                    $idKomoditas,
                    $jumlah
                );

                mysqli_stmt_execute($insertStmt);
            }

            mysqli_stmt_close($insertStmt);

            mysqli_commit($this->db);
            return true;

        } catch (Throwable $e) {
            mysqli_rollback($this->db);
            return false;
        }
    }
}