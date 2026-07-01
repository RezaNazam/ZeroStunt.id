<?php

class PetaniLokal
{
    protected $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function create($idUser, $namaLahan, $alamatLahan, $noRekening, $kapasitasPanenBulan)
    {
        $query = "
        INSERT INTO petani_lokal (
            id_petani,
            id_user,
            nama_lahan,
            alamat_lahan,
            no_rekening,
            kapasitas_panen_bulan
        ) VALUES (?, ?, ?, ?, ?, ?)
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            'iisssd',
            $idUser,
            $idUser,
            $namaLahan,
            $alamatLahan,
            $noRekening,
            $kapasitasPanenBulan
        );

        $executed = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $executed;
    }

    public function findByUserId($idUser)
    {
        $query = "
            SELECT *
            FROM petani_lokal
            WHERE id_user = ?
              AND deleted_at IS NULL
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

    public function findByIdPetani($idPetani)
    {
        $query = "
            SELECT *
            FROM petani_lokal
            WHERE id_petani = ?
              AND deleted_at IS NULL
            LIMIT 1
        ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $idPetani);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        return $data ?: null;
    }

    public function updateDetailLahan($idPetani, $luasLahan, $satuanLuas, $jenisUsaha, $statusLahan, $deskripsiLahan)
    {
        $query = "
            UPDATE petani_lokal
            SET luas_lahan = ?,
                satuan_luas = ?,
                jenis_usaha = ?,
                status_lahan = ?,
                deskripsi_lahan = ?
            WHERE id_petani = ?
        ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            'dssssi',
            $luasLahan,
            $satuanLuas,
            $jenisUsaha,
            $statusLahan,
            $deskripsiLahan,
            $idPetani
        );

        $executed = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $executed;
    }

    public function getKomoditasOptions()
    {
        $query = "
        SELECT 
            k.id_komoditas,
            k.nama_komoditas,
            COALESCE(s.singkat, s.nama_satuan, '-') AS satuan
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

    public function replaceKomoditas($idPetani, array $details)
    {
        mysqli_begin_transaction($this->db);

        try {
            $deleteQuery = "
                DELETE FROM petani_lahan_komoditas
                WHERE id_petani = ?
            ";

            $deleteStmt = mysqli_prepare($this->db, $deleteQuery);

            if (!$deleteStmt) {
                throw new Exception('Gagal menyiapkan query hapus komoditas petani.');
            }

            mysqli_stmt_bind_param($deleteStmt, 'i', $idPetani);
            mysqli_stmt_execute($deleteStmt);
            mysqli_stmt_close($deleteStmt);

            $insertQuery = "
                INSERT INTO petani_lahan_komoditas (
                    id_petani,
                    id_komoditas,
                    luas_area,
                    estimasi_panen,
                    catatan
                ) VALUES (?, ?, ?, ?, ?)
            ";

            $insertStmt = mysqli_prepare($this->db, $insertQuery);

            if (!$insertStmt) {
                throw new Exception('Gagal menyiapkan query tambah komoditas petani.');
            }

            foreach ($details as $detail) {
                $idKomoditas = (int) ($detail['id_komoditas'] ?? 0);
                $luasArea = (float) ($detail['luas_area'] ?? 0);
                $estimasiPanen = (float) ($detail['estimasi_panen'] ?? 0);
                $catatan = trim($detail['catatan'] ?? '');

                if ($idKomoditas <= 0) {
                    continue;
                }

                mysqli_stmt_bind_param(
                    $insertStmt,
                    'iidds',
                    $idPetani,
                    $idKomoditas,
                    $luasArea,
                    $estimasiPanen,
                    $catatan
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

    public function getKomoditasByPetani($idPetani)
    {
        $query = "
        SELECT 
            pk.id_petani_komoditas,
            pk.id_petani,
            pk.id_komoditas,
            pk.luas_area,
            pk.satuan_luas,
            pk.estimasi_panen,
            pk.catatan,
            k.nama_komoditas,
            COALESCE(s.singkat, s.nama_satuan, '-') AS satuan
        FROM petani_lahan_komoditas pk
        JOIN komoditas_pangan k 
            ON pk.id_komoditas = k.id_komoditas
        LEFT JOIN satuan s
            ON k.id_satuan = s.id_satuan
        WHERE pk.id_petani = ?
          AND pk.deleted_at IS NULL
        ORDER BY k.nama_komoditas ASC
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'i', $idPetani);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_stmt_close($stmt);

        return $data;
    }
}
