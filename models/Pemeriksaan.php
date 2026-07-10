<?php

class Pemeriksaan
{
    protected $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    // =====================================================
    // Hitung status gizi dan skala prioritas otomatis
    // berdasarkan standar pertumbuhan WHO (BB/U & TB/U)
    // Logika:
    //   - Cek BB/U dan TB/U, klasifikasi berbasis ambang batas SD (perkiraan)
    //   - Z < -3 SD → Gizi Buruk/Stunting (Prioritas 1)
    //   - -3 SD <= Z < -2 SD → Gizi Kurang (Prioritas 2)
    //   - Z >= -2 SD → Normal (Prioritas 3)
    //   - Z > +2 SD → Gizi Lebih (Prioritas 3)
    //   - Jika KEDUANYA (BB/U dan TB/U) di bawah -3SD → Stunting+Gizi Buruk
    // =====================================================
    public function hitungStatusGizi($usiaBulan, $jenisKelamin, $beratBadan, $tinggiBadan)
    {
        // Lookup standar untuk BB/U dan TB/U
        $standarBBU = $this->lookupStandar($usiaBulan, $jenisKelamin, 'BB/U');
        $standarTBU = $this->lookupStandar($usiaBulan, $jenisKelamin, 'TB/U');

        $statusGizi = 'Prioritas 3';
        $skalaPrioritas = 3;

        if ($standarBBU && $standarTBU) {
            $bbBelowMinus3 = $beratBadan < $standarBBU['sd_minus_3'];
            $bbBelowMinus2 = $beratBadan < $standarBBU['sd_minus_2'];
            $tbBelowMinus3 = $tinggiBadan < $standarTBU['sd_minus_3'];
            $tbBelowMinus2 = $tinggiBadan < $standarTBU['sd_minus_2'];

            if ($tbBelowMinus3 || $bbBelowMinus3) {
                $statusGizi = 'Prioritas 1';
                $skalaPrioritas = 1;
            } elseif ($bbBelowMinus2 || $tbBelowMinus2) {
                $statusGizi = 'Prioritas 2';
                $skalaPrioritas = 2;
            } else {
                $statusGizi = 'Prioritas 3';
                $skalaPrioritas = 3;
            }
        } elseif ($standarBBU) {
            if ($beratBadan < $standarBBU['sd_minus_3']) {
                $statusGizi = 'Prioritas 1';
                $skalaPrioritas = 1;
            } elseif ($beratBadan < $standarBBU['sd_minus_2']) {
                $statusGizi = 'Prioritas 2';
                $skalaPrioritas = 2;
            } else {
                $statusGizi = 'Prioritas 3';
                $skalaPrioritas = 3;
            }
        } elseif ($standarTBU) {
            if ($tinggiBadan < $standarTBU['sd_minus_3']) {
                $statusGizi = 'Prioritas 1';
                $skalaPrioritas = 1;
            } elseif ($tinggiBadan < $standarTBU['sd_minus_2']) {
                $statusGizi = 'Prioritas 2';
                $skalaPrioritas = 2;
            } else {
                $statusGizi = 'Prioritas 3';
                $skalaPrioritas = 3;
            }
        }

        return [
            'status_gizi' => $statusGizi,
            'skala_prioritas' => $skalaPrioritas,
        ];
    }

    // Ambil standar pertumbuhan untuk usia dan jenis kelamin tertentu
    public function lookupStandar($usiaBulan, $jenisKelamin, $tipeStandar)
    {
        // Clamp usia 0-60 bulan
        $usiaBulan = max(0, min(60, (int) $usiaBulan));

        $stmt = mysqli_prepare(
            $this->db,
            "SELECT median, sd_plus_1, sd_minus_1, sd_minus_2, sd_minus_3
             FROM standar_pertumbuhan
             WHERE usia_bulan = ? AND jenis_kelamin = ? AND tipe_standar = ?
             LIMIT 1"
        );

        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'iss', $usiaBulan, $jenisKelamin, $tipeStandar);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return $row ?: null;
    }

    // Ambil semua standar untuk client-side JS (dikirim ke form create)
    public function getAllStandarAsJson()
    {
        $result = mysqli_query(
            $this->db,
            "SELECT usia_bulan, jenis_kelamin, tipe_standar, median, sd_plus_1, sd_minus_1, sd_minus_2, sd_minus_3
             FROM standar_pertumbuhan
             ORDER BY jenis_kelamin, tipe_standar, usia_bulan"
        );

        if (!$result) {
            return '{}';
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $key = $row['jenis_kelamin'] . '_' . $row['tipe_standar'] . '_' . $row['usia_bulan'];
            $data[$key] = [
                'median' => (float) $row['median'],
                'sd_plus_1' => (float) $row['sd_plus_1'],
                'sd_minus_1' => (float) $row['sd_minus_1'],
                'sd_minus_2' => (float) $row['sd_minus_2'],
                'sd_minus_3' => (float) $row['sd_minus_3'],
            ];
        }

        return json_encode($data);
    }

    // =====================================================
    // COUNT
    // =====================================================
    public function countAll($id_gudang = null, $id_ibu = null)
    {
        $where = ["p.deleted_at IS NULL"];

        if ($id_gudang !== null) {
            $where[] = "i.id_gudang = " . (int) $id_gudang;
        }
        if ($id_ibu !== null) {
            // id_ibu di tabel ibu = id_user (shared PK dengan users)
            $where[] = "i.id_ibu = " . (int) $id_ibu;
        }

        $whereClause = implode(' AND ', $where);

        $query = "
            SELECT COUNT(*) AS total
            FROM t_pemeriksaan p
            JOIN anak a ON p.id_anak = a.id_anak
            JOIN ibu i ON a.id_ibu = i.id_ibu
            WHERE {$whereClause}
        ";

        $result = mysqli_query($this->db, $query);
        if (!$result)
            return 0;
        $row = mysqli_fetch_assoc($result);

        return (int) ($row['total'] ?? 0);
    }

    // =====================================================
    // PAGINATED LIST
    // =====================================================
    public function getPaginated($limit, $offset, $id_gudang = null, $id_ibu = null)
    {
        $where = ["p.deleted_at IS NULL"];

        if ($id_gudang !== null) {
            $where[] = "i.id_gudang = " . (int) $id_gudang;
        }
        if ($id_ibu !== null) {
            $where[] = "i.id_ibu = " . (int) $id_ibu;
        }

        $whereClause = implode(' AND ', $where);

        $query = "
            SELECT
                p.*,
                a.nama_anak,
                a.tgl_lahir,
                a.jenis_kelamin,
                a.skala_prioritas,
                i.nama_ibu,
                u.username AS nama_kader,
                g.nama_gudang AS nama_posyandu
            FROM t_pemeriksaan p
            JOIN anak a ON p.id_anak = a.id_anak
            JOIN ibu i ON a.id_ibu = i.id_ibu
            LEFT JOIN users u ON p.id_kader = u.id_user
            LEFT JOIN gudang g ON i.id_gudang = g.id_gudang
            WHERE {$whereClause}
            ORDER BY p.tanggal_pemeriksaan DESC, p.id_pemeriksaan DESC
            LIMIT ? OFFSET ?
        ";

        $stmt = mysqli_prepare($this->db, $query);
        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'ii', $limit, $offset);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);

        return $data;
    }

    // =====================================================
    // DROPDOWN: Anak by posyandu (untuk Kader)
    // =====================================================
    public function getAnakByPosyandu($id_gudang)
    {
        $query = "
            SELECT a.id_anak, a.nama_anak, a.tgl_lahir, a.jenis_kelamin, i.nama_ibu
            FROM anak a
            JOIN ibu i ON a.id_ibu = i.id_ibu
            WHERE i.id_gudang = ?
            ORDER BY a.nama_anak ASC
        ";

        $stmt = mysqli_prepare($this->db, $query);
        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'i', $id_gudang);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);

        return $data;
    }

    // DROPDOWN: Semua Anak (untuk Admin)
    public function getAllAnak()
    {
        $query = "
            SELECT a.id_anak, a.nama_anak, a.tgl_lahir, a.jenis_kelamin, i.nama_ibu, g.nama_gudang AS nama_posyandu
            FROM anak a
            JOIN ibu i ON a.id_ibu = i.id_ibu
            LEFT JOIN gudang g ON i.id_gudang = g.id_gudang
            ORDER BY a.nama_anak ASC
        ";

        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // =====================================================
    // INSERT + update anak.st_gizi_skrg & skala_prioritas
    // =====================================================
    public function create($data)
    {
        $query = "
            INSERT INTO t_pemeriksaan (
                id_anak,
                id_kader,
                tanggal_pemeriksaan,
                berat_badan,
                tinggi_badan,
                usia_bulan,
                status_gizi,
                catatan
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = mysqli_prepare($this->db, $query);
        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            'iisddiss',
            $data['id_anak'],
            $data['id_kader'],
            $data['tanggal_pemeriksaan'],
            $data['berat_badan'],
            $data['tinggi_badan'],
            $data['usia_bulan'],
            $data['status_gizi'],
            $data['catatan']
        );

        $executed = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Update status gizi dan skala prioritas di tabel anak
        if ($executed && isset($data['skala_prioritas'])) {
            $this->updateAnakStatus(
                $data['id_anak'],
                $data['status_gizi'],
                $data['skala_prioritas']
            );
        }

        return $executed;
    }

    public function updateAnakStatus($id_anak, $status_gizi, $skala_prioritas)
    {
        $query = "UPDATE anak SET st_gizi_skrg = ?, skala_prioritas = ? WHERE id_anak = ?";
        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sii', $status_gizi, $skala_prioritas, $id_anak);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    // =====================================================
    // SOFT DELETE
    // =====================================================
    public function delete($id_pemeriksaan)
    {
        $query = "UPDATE t_pemeriksaan SET deleted_at = NOW() WHERE id_pemeriksaan = ?";
        $stmt = mysqli_prepare($this->db, $query);
        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'i', $id_pemeriksaan);
        $executed = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $executed;
    }

    public function existsInSameMonth($idAnak, $tanggalPemeriksaan, $excludeId = null)
    {
        $query = "
        SELECT id_pemeriksaan
        FROM t_pemeriksaan
        WHERE id_anak = ?
          AND YEAR(tanggal_pemeriksaan) = YEAR(?)
          AND MONTH(tanggal_pemeriksaan) = MONTH(?)
          AND deleted_at IS NULL
    ";

        if ($excludeId !== null) {
            $query .= " AND id_pemeriksaan != ?";
        }

        $query .= " LIMIT 1";

        $stmt = mysqli_prepare($this->db, $query);

        if ($excludeId !== null) {
            mysqli_stmt_bind_param($stmt, 'issi', $idAnak, $tanggalPemeriksaan, $tanggalPemeriksaan, $excludeId);
        } else {
            mysqli_stmt_bind_param($stmt, 'iss', $idAnak, $tanggalPemeriksaan, $tanggalPemeriksaan);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result) !== null;
    }

    public function getRiwayatByIbuUser($idUser)
    {
        $query = "
        SELECT 
            p.*,
            a.nama_anak,
            a.tgl_lahir,
            a.jenis_kelamin,
            i.nama_ibu,
            g.nama_gudang AS nama_posyandu
        FROM t_pemeriksaan p
        JOIN anak a ON p.id_anak = a.id_anak
        JOIN ibu i ON a.id_ibu = i.id_ibu
        LEFT JOIN gudang g ON i.id_gudang = g.id_gudang
        WHERE i.id_user = ?
          AND p.deleted_at IS NULL
        ORDER BY p.tanggal_pemeriksaan DESC, p.id_pemeriksaan DESC
    ";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, 'i', $idUser);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function updateStatusAnak($idAnak, $statusGizi, $skalaPrioritas)
    {
        $query = "
        UPDATE anak
        SET 
            st_gizi_skrg = ?,
            skala_prioritas = ?
        WHERE id_anak = ?
          AND deleted_at IS NULL
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'sii', $statusGizi, $skalaPrioritas, $idAnak);
        $executed = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        return $executed;
    }

    // Ambil riwayat berat/tinggi badan per anak, untuk grafik tren
    public function getRiwayatUntukGrafik($idAnak)
    {
        $query = "
        SELECT tanggal_pemeriksaan, berat_badan, tinggi_badan
        FROM t_pemeriksaan
        WHERE id_anak = ? AND deleted_at IS NULL
        ORDER BY tanggal_pemeriksaan ASC
    ";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, 'i', $idAnak);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
