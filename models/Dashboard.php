<?php

class Dashboard
{
    protected $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function getPetaniDashboard($idUser)
    {
        $petani = $this->getPetaniByUserId($idUser);

        if (!$petani) {
            return [
                'petani' => null,
                'komoditas_lahan' => [],
                'kontrak' => [],
                'riwayat_ekonomi' => [],
                'metrics' => [
                    'pengadaan_aktif' => 0,
                    'total_pendapatan' => 0,
                    'total_pending' => 0,
                    'kontrak_lunas' => 0,
                    'kapasitas_panen' => 0,
                ]
            ];
        }

        $idPetani = (int) $petani['id_petani'];

        $komoditasLahan = $this->getKomoditasLahanPetani($idPetani);
        $kontrak = $this->getKontrakPetani($idPetani);

        $pengadaanAktif = 0;
        $totalPendapatan = 0;
        $totalPending = 0;
        $kontrakLunas = 0;

        foreach ($kontrak as $item) {
            $totalNilai = (float) ($item['total_nilai'] ?? 0);

            if (($item['status_bayar'] ?? '') === 'Lunas') {
                $totalPendapatan += $totalNilai;
                $kontrakLunas++;
            } else {
                $totalPending += $totalNilai;
            }

            if (($item['status_bayar'] ?? '') !== 'Lunas') {
                $pengadaanAktif++;
            }
        }

        return [
            'petani' => $petani,
            'komoditas_lahan' => $komoditasLahan,
            'kontrak' => $kontrak,
            'riwayat_ekonomi' => $kontrak,
            'metrics' => [
                'pengadaan_aktif' => $pengadaanAktif,
                'total_pendapatan' => $totalPendapatan,
                'total_pending' => $totalPending,
                'kontrak_lunas' => $kontrakLunas,
                'kapasitas_panen' => (float) ($petani['kapasitas_panen_bulan'] ?? 0),
            ]
        ];
    }

    private function getPetaniByUserId($idUser)
    {
        $query = "
            SELECT *
            FROM petani_lokal
            WHERE id_user = ?
               OR id_petani = ?
            LIMIT 1
        ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'ii', $idUser, $idUser);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        return $data ?: null;
    }

    private function getKomoditasLahanPetani($idPetani)
    {
        $query = "
            SELECT 
                plk.id_petani_komoditas,
                plk.id_petani,
                plk.id_komoditas,
                plk.luas_area,
                plk.estimasi_panen,
                plk.catatan,
                k.nama_komoditas,
                COALESCE(s.singkat, s.nama_satuan, '-') AS satuan
            FROM petani_lahan_komoditas plk
            JOIN komoditas_pangan k 
                ON plk.id_komoditas = k.id_komoditas
            LEFT JOIN satuan s
                ON k.id_satuan = s.id_satuan
            WHERE plk.id_petani = ?
              AND plk.deleted_at IS NULL
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

    private function getKontrakPetani($idPetani)
    {
        $query = "
            SELECT 
                p.id_pengadaan,
                p.no_kontrak,
                p.tgl_pengadaan,
                p.status_bayar,
                p.status_kontrak,
                p.keterangan,
                g.nama_gudang,
                SUM(COALESCE(NULLIF(pd.subtotal, 0), pd.jumlah * pd.harga_satuan)) AS total_nilai,
                GROUP_CONCAT(
                    CONCAT(
                        k.nama_komoditas,
                        '||',
                        pd.jumlah,
                        '||',
                        COALESCE(s.singkat, s.nama_satuan, '-'),
                        '||',
                        pd.harga_satuan,
                        '||',
                        COALESCE(NULLIF(pd.subtotal, 0), pd.jumlah * pd.harga_satuan)
                    )
                    SEPARATOR ';;'
                ) AS detail_komoditas
            FROM t_pengadaan p
            JOIN t_pengadaan_detail pd
                ON p.id_pengadaan = pd.id_pengadaan
            JOIN komoditas_pangan k
                ON pd.id_komoditas = k.id_komoditas
            LEFT JOIN satuan s
                ON k.id_satuan = s.id_satuan
            LEFT JOIN gudang g
                ON p.id_gudang = g.id_gudang
            WHERE p.id_petani = ?
            GROUP BY 
                p.id_pengadaan,
                p.no_kontrak,
                p.tgl_pengadaan,
                p.status_bayar,
                p.status_kontrak,
                p.keterangan,
                g.nama_gudang
            ORDER BY p.tgl_pengadaan DESC, p.id_pengadaan DESC
            LIMIT 5
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

    public function getKaderDashboard($idUser)
    {
        $user = $this->getUserById($idUser);
        $idGudang = (int) ($user['id_gudang'] ?? 0);

        if ($idGudang <= 0) {
            return [
                'user' => $user,
                'id_gudang' => 0,
                'metrics' => [
                    'total_anak' => 0,
                    'prioritas_1' => 0,
                    'total_stok' => 0,
                    'pemeriksaan_bulan_ini' => 0,
                ],
                'prioritas' => [
                    'prioritas_1' => 0,
                    'prioritas_2' => 0,
                    'prioritas_3' => 0,
                ],
                'pemeriksaan_terbaru' => [],
                'stok_posyandu' => [],
                'penyerahan_terbaru' => [],
            ];
        }

        return [
            'user' => $user,
            'id_gudang' => $idGudang,
            'metrics' => [
                'total_anak' => $this->countAnakByGudang($idGudang),
                'prioritas_1' => $this->countAnakByPrioritas($idGudang, 1),
                'jenis_stok' => $this->countJenisStokByGudang($idGudang),
                'pemeriksaan_bulan_ini' => $this->countPemeriksaanBulanIni($idGudang),
            ],
            'prioritas' => [
                'prioritas_1' => $this->countAnakByPrioritas($idGudang, 1),
                'prioritas_2' => $this->countAnakByPrioritas($idGudang, 2),
                'prioritas_3' => $this->countAnakByPrioritas($idGudang, 3),
            ],
            'pemeriksaan_terbaru' => $this->getPemeriksaanTerbaruByGudang($idGudang),
            'stok_posyandu' => $this->getStokByGudang($idGudang),
            'penyerahan_terbaru' => $this->getPenyerahanTerbaruByGudang($idGudang),
        ];
    }

    private function getUserById($idUser)
    {
        $query = "
        SELECT *
        FROM users
        WHERE id_user = ?
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

    private function countAnakByGudang($idGudang)
    {
        $query = "
        SELECT COUNT(*) AS total
        FROM anak a
        JOIN ibu i ON a.id_ibu = i.id_ibu
        WHERE i.id_gudang = ?
          AND a.deleted_at IS NULL
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return 0;
        }

        mysqli_stmt_bind_param($stmt, 'i', $idGudang);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        return (int) ($row['total'] ?? 0);
    }

    private function countAnakByPrioritas($idGudang, $prioritas)
    {
        $query = "
        SELECT COUNT(*) AS total
        FROM anak a
        JOIN ibu i ON a.id_ibu = i.id_ibu
        WHERE i.id_gudang = ?
          AND a.skala_prioritas = ?
          AND a.deleted_at IS NULL
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return 0;
        }

        mysqli_stmt_bind_param($stmt, 'ii', $idGudang, $prioritas);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        return (int) ($row['total'] ?? 0);
    }

    private function sumStokByGudang($idGudang)
    {
        $query = "
        SELECT COALESCE(SUM(qty_current), 0) AS total
        FROM stok_log
        WHERE id_gudang = ?
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return 0;
        }

        mysqli_stmt_bind_param($stmt, 'i', $idGudang);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        return (float) ($row['total'] ?? 0);
    }

    private function countPemeriksaanBulanIni($idGudang)
    {
        $query = "
        SELECT COUNT(*) AS total
        FROM t_pemeriksaan p
        JOIN anak a ON p.id_anak = a.id_anak
        JOIN ibu i ON a.id_ibu = i.id_ibu
        WHERE i.id_gudang = ?
          AND MONTH(p.tanggal_pemeriksaan) = MONTH(CURDATE())
          AND YEAR(p.tanggal_pemeriksaan) = YEAR(CURDATE())
          AND p.deleted_at IS NULL
          AND a.deleted_at IS NULL
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return 0;
        }

        mysqli_stmt_bind_param($stmt, 'i', $idGudang);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        return (int) ($row['total'] ?? 0);
    }

    private function getPemeriksaanTerbaruByGudang($idGudang)
    {
        $query = "
        SELECT 
            p.id_pemeriksaan,
            p.tanggal_pemeriksaan,
            p.berat_badan,
            p.tinggi_badan,
            p.status_gizi,
            a.nama_anak,
            a.tgl_lahir,
            a.skala_prioritas,
            i.nama_ibu
        FROM t_pemeriksaan p
        JOIN anak a ON p.id_anak = a.id_anak
        JOIN ibu i ON a.id_ibu = i.id_ibu
        WHERE i.id_gudang = ?
          AND p.deleted_at IS NULL
          AND a.deleted_at IS NULL
        ORDER BY p.tanggal_pemeriksaan DESC, p.id_pemeriksaan DESC
        LIMIT 5
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

    private function getStokByGudang($idGudang)
    {
        $query = "
        SELECT
            sl.id_stok_log,
            sl.id_gudang,
            sl.id_komoditas,
            sl.qty_in,
            sl.qty_out,
            sl.qty_current,
            k.nama_komoditas,
            COALESCE(s.singkat, s.nama_satuan, '-') AS satuan
        FROM stok_log sl
        JOIN komoditas_pangan k ON sl.id_komoditas = k.id_komoditas
        LEFT JOIN satuan s ON k.id_satuan = s.id_satuan
        WHERE sl.id_gudang = ?
        ORDER BY k.nama_komoditas ASC
        LIMIT 5
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

    private function getPenyerahanTerbaruByGudang($idGudang)
    {
        $query = "
        SELECT 
            p.id_penyerahan,
            p.no_penyerahan,
            p.tanggal_penyerahan,
            p.status_penyerahan,
            a.nama_anak,
            i.nama_ibu,
            pg.nama_paket
        FROM t_penyerahan p
        JOIN ibu i ON p.id_ibu = i.id_ibu
        LEFT JOIN anak a ON p.id_anak = a.id_anak
        LEFT JOIN paket_gizi pg ON p.id_paket = pg.id_paket
        WHERE p.id_gudang = ?
        ORDER BY p.tanggal_penyerahan DESC, p.id_penyerahan DESC
        LIMIT 5
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

    private function countJenisStokByGudang($idGudang)
    {
        $query = "
        SELECT COUNT(*) AS total
        FROM stok_log
        WHERE id_gudang = ?
          AND qty_current > 0
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return 0;
        }

        mysqli_stmt_bind_param($stmt, 'i', $idGudang);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        return (int) ($row['total'] ?? 0);
    }

    public function getAdminDashboard()
    {
        return [
            'metrics' => [
                'total_anak' => $this->countAllAnak(),
                'prioritas_1' => $this->countAllAnakByPrioritas(1),
                'jenis_stok_pusat' => $this->countJenisStokGudangPusat(),
                'petani_aktif' => $this->countPetaniAktif(),
            ],
            'prioritas' => [
                'prioritas_1' => $this->countAllAnakByPrioritas(1),
                'prioritas_2' => $this->countAllAnakByPrioritas(2),
                'prioritas_3' => $this->countAllAnakByPrioritas(3),
            ],
            'pemeriksaan_bulanan' => $this->getPemeriksaanBulananAdmin(),
            'pemeriksaan_terbaru' => $this->getPemeriksaanTerbaruAdmin(),
            'pengadaan_terbaru' => $this->getPengadaanTerbaruAdmin(),
            'stok_pusat' => $this->getStokGudangPusatAdmin(),
            'penyerahan_terbaru' => $this->getPenyerahanTerbaruAdmin(),
        ];
    }

    private function countAllAnak()
    {
        $query = "
        SELECT COUNT(*) AS total
        FROM anak
        WHERE deleted_at IS NULL
    ";

        $result = mysqli_query($this->db, $query);
        $row = mysqli_fetch_assoc($result);

        return (int) ($row['total'] ?? 0);
    }

    private function countAllAnakByPrioritas($prioritas)
    {
        $query = "
        SELECT COUNT(*) AS total
        FROM anak
        WHERE skala_prioritas = ?
          AND deleted_at IS NULL
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return 0;
        }

        mysqli_stmt_bind_param($stmt, 'i', $prioritas);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        return (int) ($row['total'] ?? 0);
    }

    private function countJenisStokGudangPusat()
    {
        $query = "
        SELECT COUNT(*) AS total
        FROM stok_log sl
        JOIN gudang g ON sl.id_gudang = g.id_gudang
        WHERE LOWER(g.jenis_gudang) = 'pusat'
          AND sl.qty_current > 0
    ";

        $result = mysqli_query($this->db, $query);
        $row = mysqli_fetch_assoc($result);

        return (int) ($row['total'] ?? 0);
    }

    private function countPetaniAktif()
    {
        $query = "
        SELECT COUNT(*) AS total
        FROM petani_lokal
        WHERE deleted_at IS NULL
    ";

        $result = mysqli_query($this->db, $query);
        $row = mysqli_fetch_assoc($result);

        return (int) ($row['total'] ?? 0);
    }

    private function getPemeriksaanBulananAdmin()
    {
        $query = "
        SELECT 
            DATE_FORMAT(tanggal_pemeriksaan, '%Y-%m') AS bulan,
            COUNT(*) AS total
        FROM t_pemeriksaan
        WHERE tanggal_pemeriksaan >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
          AND deleted_at IS NULL
        GROUP BY DATE_FORMAT(tanggal_pemeriksaan, '%Y-%m')
        ORDER BY bulan ASC
    ";

        $result = mysqli_query($this->db, $query);

        if (!$result) {
            return [];
        }

        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $map = [];

        foreach ($rows as $row) {
            $map[$row['bulan']] = (int) $row['total'];
        }

        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $key = date('Y-m', strtotime("-{$i} month"));
            $data[] = [
                'bulan' => date('M', strtotime($key . '-01')),
                'total' => $map[$key] ?? 0,
            ];
        }

        return $data;
    }

    private function getPemeriksaanTerbaruAdmin()
    {
        $query = "
        SELECT 
            p.id_pemeriksaan,
            p.tanggal_pemeriksaan,
            p.berat_badan,
            p.tinggi_badan,
            p.status_gizi,
            a.nama_anak,
            a.skala_prioritas,
            i.nama_ibu,
            g.nama_gudang AS nama_posyandu
        FROM t_pemeriksaan p
        JOIN anak a ON p.id_anak = a.id_anak
        JOIN ibu i ON a.id_ibu = i.id_ibu
        LEFT JOIN gudang g ON i.id_gudang = g.id_gudang
        WHERE p.deleted_at IS NULL
          AND a.deleted_at IS NULL
        ORDER BY p.tanggal_pemeriksaan DESC, p.id_pemeriksaan DESC
        LIMIT 5
    ";

        $result = mysqli_query($this->db, $query);

        if (!$result) {
            return [];
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    private function getPengadaanTerbaruAdmin()
    {
        $query = "
        SELECT 
            p.id_pengadaan,
            p.no_kontrak,
            p.tgl_pengadaan,
            p.status_bayar,
            p.status_kontrak,
            g.nama_gudang,
            pl.nama_lahan AS nama_petani,
            SUM(COALESCE(NULLIF(pd.subtotal, 0), pd.jumlah * pd.harga_satuan)) AS total_nilai,
            GROUP_CONCAT(
                CONCAT(
                    k.nama_komoditas,
                    '||',
                    pd.jumlah,
                    '||',
                    COALESCE(s.singkat, s.nama_satuan, '-')
                )
                SEPARATOR ';;'
            ) AS detail_komoditas
        FROM t_pengadaan p
        LEFT JOIN t_pengadaan_detail pd ON p.id_pengadaan = pd.id_pengadaan
        LEFT JOIN komoditas_pangan k ON pd.id_komoditas = k.id_komoditas
        LEFT JOIN satuan s ON k.id_satuan = s.id_satuan
        LEFT JOIN gudang g ON p.id_gudang = g.id_gudang
        LEFT JOIN petani_lokal pl ON p.id_petani = pl.id_petani
        GROUP BY 
            p.id_pengadaan,
            p.no_kontrak,
            p.tgl_pengadaan,
            p.status_bayar,
            p.status_kontrak,
            g.nama_gudang,
            pl.nama_lahan
        ORDER BY p.tgl_pengadaan DESC, p.id_pengadaan DESC
        LIMIT 5
    ";

        $result = mysqli_query($this->db, $query);

        if (!$result) {
            return [];
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    private function getStokGudangPusatAdmin()
    {
        $query = "
        SELECT 
            sl.id_stok_log,
            sl.qty_in,
            sl.qty_out,
            sl.qty_current,
            g.nama_gudang,
            k.nama_komoditas,
            COALESCE(s.singkat, s.nama_satuan, '-') AS satuan
        FROM stok_log sl
        JOIN gudang g ON sl.id_gudang = g.id_gudang
        JOIN komoditas_pangan k ON sl.id_komoditas = k.id_komoditas
        LEFT JOIN satuan s ON k.id_satuan = s.id_satuan
        WHERE LOWER(g.jenis_gudang) = 'pusat'
        ORDER BY sl.qty_current DESC, k.nama_komoditas ASC
        LIMIT 5
    ";

        $result = mysqli_query($this->db, $query);

        if (!$result) {
            return [];
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    private function getPenyerahanTerbaruAdmin()
    {
        $query = "
        SELECT 
            p.id_penyerahan,
            p.no_penyerahan,
            p.tanggal_penyerahan,
            p.status_penyerahan,
            a.nama_anak,
            i.nama_ibu,
            g.nama_gudang AS nama_posyandu,
            pg.nama_paket
        FROM t_penyerahan p
        JOIN ibu i ON p.id_ibu = i.id_ibu
        LEFT JOIN anak a ON p.id_anak = a.id_anak
        LEFT JOIN gudang g ON p.id_gudang = g.id_gudang
        LEFT JOIN paket_gizi pg ON p.id_paket = pg.id_paket
        ORDER BY p.tanggal_penyerahan DESC, p.id_penyerahan DESC
        LIMIT 5
    ";

        $result = mysqli_query($this->db, $query);

        if (!$result) {
            return [];
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
