<?php

class Laporan
{
    protected $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function getAdminLaporan($startDate, $endDate)
    {
        $pemeriksaan = $this->getPemeriksaanAdmin($startDate, $endDate);
        $pengadaan = $this->getPengadaanAdmin($startDate, $endDate);
        $distribusi = $this->getDistribusiAdmin($startDate, $endDate);
        $penyerahan = $this->getPenyerahanAdmin($startDate, $endDate);
        $stok = $this->getStokPusatAdmin();

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'admin_info' => [
                'nama_sistem' => 'ZeroStunt.id',
                'cakupan' => 'Seluruh data sistem',
            ],
            'summary' => [
                'total_anak' => $this->countAllAnakAdmin(),
                'prioritas_1' => $this->countAllAnakPrioritasAdmin(1),
                'prioritas_2' => $this->countAllAnakPrioritasAdmin(2),
                'prioritas_3' => $this->countAllAnakPrioritasAdmin(3),
                'total_pemeriksaan' => count($pemeriksaan),
                'total_pengadaan' => count($pengadaan),
                'total_distribusi' => count($distribusi),
                'total_penyerahan' => count($penyerahan),
                'jenis_stok_pusat' => count($stok),
            ],
            'pemeriksaan' => $pemeriksaan,
            'pengadaan' => $pengadaan,
            'distribusi' => $distribusi,
            'penyerahan' => $penyerahan,
            'stok' => $stok,
        ];
    }

    public function getKaderLaporan($idUser, $startDate, $endDate)
    {
        $kaderInfo = $this->getKaderInfo($idUser);
        $idGudang = (int) ($kaderInfo['id_gudang'] ?? 0);

        if ($idGudang <= 0) {
            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'kader_info' => $kaderInfo,
                'summary' => [
                    'total_anak' => 0,
                    'total_pemeriksaan' => 0,
                    'total_penyerahan' => 0,
                    'jenis_stok' => 0,
                    'prioritas_1' => 0,
                    'prioritas_2' => 0,
                    'prioritas_3' => 0,
                ],
                'pemeriksaan' => [],
                'stok' => [],
                'distribusi' => [],
                'penyerahan' => [],
            ];
        }

        $pemeriksaan = $this->getPemeriksaanKader($idGudang, $startDate, $endDate);
        $stok = $this->getStokKader($idGudang);
        $distribusi = $this->getDistribusiKader($idGudang, $startDate, $endDate);
        $penyerahan = $this->getPenyerahanKader($idGudang, $startDate, $endDate);

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'kader_info' => $kaderInfo,
            'summary' => [
                'total_anak' => $this->countAnakKader($idGudang),
                'total_pemeriksaan' => count($pemeriksaan),
                'total_penyerahan' => count($penyerahan),
                'jenis_stok' => count($stok),
                'prioritas_1' => $this->countAnakKaderByPrioritas($idGudang, 1),
                'prioritas_2' => $this->countAnakKaderByPrioritas($idGudang, 2),
                'prioritas_3' => $this->countAnakKaderByPrioritas($idGudang, 3),
            ],
            'pemeriksaan' => $pemeriksaan,
            'stok' => $stok,
            'distribusi' => $distribusi,
            'penyerahan' => $penyerahan,
        ];
    }

    private function getKaderInfo($idUser)
    {
        $query = "
        SELECT 
            u.id_user,
            u.username,
            u.role,
            u.id_gudang,
            g.nama_gudang,
            g.jenis_gudang,
            g.alamat_lengkap
        FROM users u
        LEFT JOIN gudang g 
            ON u.id_gudang = g.id_gudang
        WHERE u.id_user = ?
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

    private function countAnakKader($idGudang)
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

    private function countAnakKaderByPrioritas($idGudang, $prioritas)
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

    private function getPemeriksaanKader($idGudang, $startDate, $endDate)
    {
        $query = "
        SELECT 
            p.id_pemeriksaan,
            p.tanggal_pemeriksaan,
            p.berat_badan,
            p.tinggi_badan,
            p.usia_bulan,
            p.status_gizi,
            p.catatan,
            a.nama_anak,
            a.jenis_kelamin,
            a.skala_prioritas,
            i.nama_ibu
        FROM t_pemeriksaan p
        JOIN anak a ON p.id_anak = a.id_anak
        JOIN ibu i ON a.id_ibu = i.id_ibu
        WHERE i.id_gudang = ?
          AND a.deleted_at IS NULL
          AND p.deleted_at IS NULL
          AND DATE(p.tanggal_pemeriksaan) BETWEEN ? AND ?
        ORDER BY p.tanggal_pemeriksaan DESC, p.id_pemeriksaan DESC
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'iss', $idGudang, $startDate, $endDate);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_stmt_close($stmt);

        return $data;
    }

    private function getStokKader($idGudang)
    {
        $query = "
        SELECT
            sl.id_stok_log,
            sl.qty_in,
            sl.qty_out,
            sl.qty_current,
            k.nama_komoditas,
            COALESCE(s.singkat, s.nama_satuan, '-') AS satuan
        FROM stok_log sl
        JOIN komoditas_pangan k 
            ON sl.id_komoditas = k.id_komoditas
        LEFT JOIN satuan s 
            ON k.id_satuan = s.id_satuan
        WHERE sl.id_gudang = ?
        ORDER BY k.nama_komoditas ASC
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

    private function getDistribusiKader($idGudang, $startDate, $endDate)
    {
        $query = "
        SELECT
            d.id_distribusi,
            d.no_distribusi,
            d.tanggal_distribusi,
            d.status_distribusi,
            asal.nama_gudang AS gudang_asal,
            tujuan.nama_gudang AS gudang_tujuan,
            GROUP_CONCAT(
                CONCAT(
                    k.nama_komoditas,
                    ' ',
                    dd.jumlah,
                    ' ',
                    COALESCE(s.singkat, s.nama_satuan, '-')
                )
                SEPARATOR ', '
            ) AS detail_distribusi
        FROM t_distribusi d
        LEFT JOIN t_distribusi_detail dd 
            ON d.id_distribusi = dd.id_distribusi
        LEFT JOIN komoditas_pangan k 
            ON dd.id_komoditas = k.id_komoditas
        LEFT JOIN satuan s 
            ON k.id_satuan = s.id_satuan
        LEFT JOIN gudang asal 
            ON d.id_gudang_asal = asal.id_gudang
        LEFT JOIN gudang tujuan 
            ON d.id_gudang_tujuan = tujuan.id_gudang
        WHERE d.id_gudang_tujuan = ?
          AND DATE(d.tanggal_distribusi) BETWEEN ? AND ?
        GROUP BY
            d.id_distribusi,
            d.no_distribusi,
            d.tanggal_distribusi,
            d.status_distribusi,
            asal.nama_gudang,
            tujuan.nama_gudang
        ORDER BY d.tanggal_distribusi DESC, d.id_distribusi DESC
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'iss', $idGudang, $startDate, $endDate);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_stmt_close($stmt);

        return $data;
    }

    private function getPenyerahanKader($idGudang, $startDate, $endDate)
    {
        $query = "
        SELECT 
            p.id_penyerahan,
            p.no_penyerahan,
            p.tanggal_penyerahan,
            p.status_penyerahan,
            p.catatan,
            a.nama_anak,
            i.nama_ibu,
            pg.nama_paket,
            GROUP_CONCAT(
                CONCAT(
                    k.nama_komoditas,
                    ' ',
                    pd.jumlah,
                    ' ',
                    COALESCE(s.singkat, s.nama_satuan, '-')
                )
                SEPARATOR ', '
            ) AS isi_paket
        FROM t_penyerahan p
        JOIN ibu i 
            ON p.id_ibu = i.id_ibu
        LEFT JOIN anak a 
            ON p.id_anak = a.id_anak
        LEFT JOIN paket_gizi pg 
            ON p.id_paket = pg.id_paket
        LEFT JOIN t_penyerahan_detail pd 
            ON p.id_penyerahan = pd.id_penyerahan
        LEFT JOIN komoditas_pangan k 
            ON pd.id_komoditas = k.id_komoditas
        LEFT JOIN satuan s 
            ON k.id_satuan = s.id_satuan
        WHERE p.id_gudang = ?
          AND DATE(p.tanggal_penyerahan) BETWEEN ? AND ?
        GROUP BY
            p.id_penyerahan,
            p.no_penyerahan,
            p.tanggal_penyerahan,
            p.status_penyerahan,
            p.catatan,
            a.nama_anak,
            i.nama_ibu,
            pg.nama_paket
        ORDER BY p.tanggal_penyerahan DESC, p.id_penyerahan DESC
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'iss', $idGudang, $startDate, $endDate);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_stmt_close($stmt);

        return $data;
    }

    public function getIbuLaporan($idIbu, $startDate, $endDate)
    {
        $ibuInfo = $this->getIbuInfo($idIbu);
        $anak = $this->getAnakIbu($idIbu);
        $pemeriksaan = $this->getPemeriksaanIbu($idIbu, $startDate, $endDate);
        $bantuan = $this->getBantuanIbu($idIbu, $startDate, $endDate);

        $prioritas = [
            1 => 0,
            2 => 0,
            3 => 0,
        ];

        foreach ($anak as $item) {
            $p = (int) ($item['skala_prioritas'] ?? 3);

            if (isset($prioritas[$p])) {
                $prioritas[$p]++;
            }
        }

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'ibu_info' => $ibuInfo,
            'summary' => [
                'total_anak' => count($anak),
                'total_pemeriksaan' => count($pemeriksaan),
                'total_bantuan' => count($bantuan),
                'prioritas_1' => $prioritas[1],
                'prioritas_2' => $prioritas[2],
                'prioritas_3' => $prioritas[3],
            ],
            'anak' => $anak,
            'pemeriksaan' => $pemeriksaan,
            'bantuan' => $bantuan,
        ];
    }

    private function getIbuInfo($idIbu)
    {
        $query = "
        SELECT 
            i.id_ibu,
            i.nama_ibu,
            i.NIK_ibu,
            i.alamat,
            i.no_telp,
            g.nama_gudang AS nama_posyandu
        FROM ibu i
        LEFT JOIN gudang g 
            ON i.id_gudang = g.id_gudang
        WHERE i.id_ibu = ?
        LIMIT 1
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $idIbu);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        return $data ?: null;
    }

    private function getAnakIbu($idIbu)
    {
        $query = "
        SELECT 
            a.*,
            i.nama_ibu,
            p.tanggal_pemeriksaan AS tanggal_pemeriksaan_terakhir,
            p.berat_badan AS berat_badan_terakhir,
            p.tinggi_badan AS tinggi_badan_terakhir,
            p.usia_bulan AS usia_bulan_terakhir,
            p.status_gizi AS status_gizi_terakhir
        FROM anak a
        JOIN ibu i 
            ON a.id_ibu = i.id_ibu
        LEFT JOIN t_pemeriksaan p
            ON p.id_anak = a.id_anak
            AND p.id_pemeriksaan = (
                SELECT MAX(p2.id_pemeriksaan)
                FROM t_pemeriksaan p2
                WHERE p2.id_anak = a.id_anak
            )
        WHERE a.id_ibu = ?
          AND a.deleted_at IS NULL
        ORDER BY a.nama_anak ASC
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

    private function getPemeriksaanIbu($idIbu, $startDate, $endDate)
    {
        $query = "
        SELECT 
            p.*,
            a.nama_anak,
            a.jenis_kelamin,
            i.nama_ibu
        FROM t_pemeriksaan p
        JOIN anak a 
            ON p.id_anak = a.id_anak
        JOIN ibu i 
            ON a.id_ibu = i.id_ibu
        WHERE a.id_ibu = ?
          AND a.deleted_at IS NULL
          AND DATE(p.tanggal_pemeriksaan) BETWEEN ? AND ?
        ORDER BY p.tanggal_pemeriksaan DESC, p.id_pemeriksaan DESC
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'iss', $idIbu, $startDate, $endDate);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_stmt_close($stmt);

        return $data;
    }

    private function getBantuanIbu($idIbu, $startDate, $endDate)
    {
        $query = "
        SELECT 
            p.id_penyerahan,
            p.no_penyerahan,
            p.tanggal_penyerahan,
            p.status_penyerahan,
            p.catatan,
            a.nama_anak,
            i.nama_ibu,
            g.nama_gudang AS nama_posyandu,
            pg.nama_paket,
            GROUP_CONCAT(
                CONCAT(
                    k.nama_komoditas,
                    ' ',
                    pd.jumlah,
                    ' ',
                    COALESCE(s.singkat, s.nama_satuan, '-')
                )
                SEPARATOR ', '
            ) AS isi_paket
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
          AND DATE(p.tanggal_penyerahan) BETWEEN ? AND ?
        GROUP BY 
            p.id_penyerahan,
            p.no_penyerahan,
            p.tanggal_penyerahan,
            p.status_penyerahan,
            p.catatan,
            a.nama_anak,
            i.nama_ibu,
            g.nama_gudang,
            pg.nama_paket
        ORDER BY p.tanggal_penyerahan DESC, p.id_penyerahan DESC
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'iss', $idIbu, $startDate, $endDate);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_stmt_close($stmt);

        return $data;
    }

    public function getPetaniLaporan($idUser, $startDate, $endDate)
    {
        $petani = $this->getPetaniInfo($idUser);

        if (!$petani) {
            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'petani_info' => null,
                'summary' => [
                    'total_kontrak' => 0,
                    'total_lunas' => 0,
                    'total_pending' => 0,
                    'pendapatan_lunas' => 0,
                    'pendapatan_pending' => 0,
                ],
                'komoditas_lahan' => [],
                'pengadaan' => [],
            ];
        }

        $idPetani = (int) $petani['id_petani'];

        $komoditasLahan = $this->getKomoditasLahanPetani($idPetani);
        $pengadaan = $this->getPengadaanPetani($idPetani, $startDate, $endDate);

        $totalLunas = 0;
        $totalPending = 0;
        $pendapatanLunas = 0;
        $pendapatanPending = 0;

        foreach ($pengadaan as $item) {
            $totalNilai = (float) ($item['total_nilai'] ?? 0);

            if (($item['status_bayar'] ?? '') === 'Lunas') {
                $totalLunas++;
                $pendapatanLunas += $totalNilai;
            } else {
                $totalPending++;
                $pendapatanPending += $totalNilai;
            }
        }

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'petani_info' => $petani,
            'summary' => [
                'total_kontrak' => count($pengadaan),
                'total_lunas' => $totalLunas,
                'total_pending' => $totalPending,
                'pendapatan_lunas' => $pendapatanLunas,
                'pendapatan_pending' => $pendapatanPending,
            ],
            'komoditas_lahan' => $komoditasLahan,
            'pengadaan' => $pengadaan,
        ];
    }

    private function getPetaniInfo($idUser)
    {
        $query = "
        SELECT *
        FROM petani_lokal
        WHERE id_petani = ?
           OR id_user = ?
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

    private function getPengadaanPetani($idPetani, $startDate, $endDate)
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
                    ' ',
                    pd.jumlah,
                    ' ',
                    COALESCE(s.singkat, s.nama_satuan, '-'),
                    ' x Rp ',
                    FORMAT(pd.harga_satuan, 0)
                )
                SEPARATOR ', '
            ) AS detail_komoditas
        FROM t_pengadaan p
        LEFT JOIN t_pengadaan_detail pd 
            ON p.id_pengadaan = pd.id_pengadaan
        LEFT JOIN komoditas_pangan k 
            ON pd.id_komoditas = k.id_komoditas
        LEFT JOIN satuan s 
            ON k.id_satuan = s.id_satuan
        LEFT JOIN gudang g 
            ON p.id_gudang = g.id_gudang
        WHERE p.id_petani = ?
          AND DATE(p.tgl_pengadaan) BETWEEN ? AND ?
        GROUP BY 
            p.id_pengadaan,
            p.no_kontrak,
            p.tgl_pengadaan,
            p.status_bayar,
            p.status_kontrak,
            p.keterangan,
            g.nama_gudang
        ORDER BY p.tgl_pengadaan DESC, p.id_pengadaan DESC
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'iss', $idPetani, $startDate, $endDate);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_stmt_close($stmt);

        return $data;
    }

    private function countAllAnakAdmin()
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

    private function countAllAnakPrioritasAdmin($prioritas)
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

    private function getPemeriksaanAdmin($startDate, $endDate)
    {
        $query = "
        SELECT 
            p.id_pemeriksaan,
            p.tanggal_pemeriksaan,
            p.berat_badan,
            p.tinggi_badan,
            p.usia_bulan,
            p.status_gizi,
            a.nama_anak,
            a.skala_prioritas,
            i.nama_ibu,
            g.nama_gudang AS nama_posyandu,
            u.username AS nama_kader
        FROM t_pemeriksaan p
        JOIN anak a ON p.id_anak = a.id_anak
        JOIN ibu i ON a.id_ibu = i.id_ibu
        LEFT JOIN gudang g ON i.id_gudang = g.id_gudang
        LEFT JOIN users u ON p.id_kader = u.id_user
        WHERE a.deleted_at IS NULL
          AND DATE(p.tanggal_pemeriksaan) BETWEEN ? AND ?
        ORDER BY p.tanggal_pemeriksaan DESC, p.id_pemeriksaan DESC
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'ss', $startDate, $endDate);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_stmt_close($stmt);

        return $data;
    }

    private function getPengadaanAdmin($startDate, $endDate)
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
            pl.nama_lahan AS nama_petani,
            SUM(COALESCE(NULLIF(pd.subtotal, 0), pd.jumlah * pd.harga_satuan)) AS total_nilai,
            GROUP_CONCAT(
                CONCAT(
                    k.nama_komoditas,
                    ' ',
                    pd.jumlah,
                    ' ',
                    COALESCE(s.singkat, s.nama_satuan, '-')
                )
                SEPARATOR ', '
            ) AS detail_komoditas
        FROM t_pengadaan p
        LEFT JOIN t_pengadaan_detail pd ON p.id_pengadaan = pd.id_pengadaan
        LEFT JOIN komoditas_pangan k ON pd.id_komoditas = k.id_komoditas
        LEFT JOIN satuan s ON k.id_satuan = s.id_satuan
        LEFT JOIN gudang g ON p.id_gudang = g.id_gudang
        LEFT JOIN petani_lokal pl ON p.id_petani = pl.id_petani
        WHERE DATE(p.tgl_pengadaan) BETWEEN ? AND ?
        GROUP BY
            p.id_pengadaan,
            p.no_kontrak,
            p.tgl_pengadaan,
            p.status_bayar,
            p.status_kontrak,
            p.keterangan,
            g.nama_gudang,
            pl.nama_lahan
        ORDER BY p.tgl_pengadaan DESC, p.id_pengadaan DESC
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'ss', $startDate, $endDate);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_stmt_close($stmt);

        return $data;
    }

    private function getDistribusiAdmin($startDate, $endDate)
    {
        $query = "
        SELECT
            d.id_distribusi,
            d.no_distribusi,
            d.tanggal_distribusi,
            d.status_distribusi,
            asal.nama_gudang AS gudang_asal,
            tujuan.nama_gudang AS gudang_tujuan,
            GROUP_CONCAT(
                CONCAT(
                    k.nama_komoditas,
                    ' ',
                    dd.jumlah,
                    ' ',
                    COALESCE(s.singkat, s.nama_satuan, '-')
                )
                SEPARATOR ', '
            ) AS detail_distribusi
        FROM t_distribusi d
        LEFT JOIN t_distribusi_detail dd ON d.id_distribusi = dd.id_distribusi
        LEFT JOIN komoditas_pangan k ON dd.id_komoditas = k.id_komoditas
        LEFT JOIN satuan s ON k.id_satuan = s.id_satuan
        LEFT JOIN gudang asal ON d.id_gudang_asal = asal.id_gudang
        LEFT JOIN gudang tujuan ON d.id_gudang_tujuan = tujuan.id_gudang
        WHERE DATE(d.tanggal_distribusi) BETWEEN ? AND ?
        GROUP BY
            d.id_distribusi,
            d.no_distribusi,
            d.tanggal_distribusi,
            d.status_distribusi,
            asal.nama_gudang,
            tujuan.nama_gudang
        ORDER BY d.tanggal_distribusi DESC, d.id_distribusi DESC
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'ss', $startDate, $endDate);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_stmt_close($stmt);

        return $data;
    }

    private function getPenyerahanAdmin($startDate, $endDate)
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
            pg.nama_paket,
            GROUP_CONCAT(
                CONCAT(
                    k.nama_komoditas,
                    ' ',
                    pd.jumlah,
                    ' ',
                    COALESCE(s.singkat, s.nama_satuan, '-')
                )
                SEPARATOR ', '
            ) AS isi_paket
        FROM t_penyerahan p
        JOIN ibu i ON p.id_ibu = i.id_ibu
        LEFT JOIN anak a ON p.id_anak = a.id_anak
        LEFT JOIN gudang g ON p.id_gudang = g.id_gudang
        LEFT JOIN paket_gizi pg ON p.id_paket = pg.id_paket
        LEFT JOIN t_penyerahan_detail pd ON p.id_penyerahan = pd.id_penyerahan
        LEFT JOIN komoditas_pangan k ON pd.id_komoditas = k.id_komoditas
        LEFT JOIN satuan s ON k.id_satuan = s.id_satuan
        WHERE DATE(p.tanggal_penyerahan) BETWEEN ? AND ?
        GROUP BY
            p.id_penyerahan,
            p.no_penyerahan,
            p.tanggal_penyerahan,
            p.status_penyerahan,
            a.nama_anak,
            i.nama_ibu,
            g.nama_gudang,
            pg.nama_paket
        ORDER BY p.tanggal_penyerahan DESC, p.id_penyerahan DESC
    ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'ss', $startDate, $endDate);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_stmt_close($stmt);

        return $data;
    }

    private function getStokPusatAdmin()
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
        ORDER BY g.nama_gudang ASC, k.nama_komoditas ASC
    ";

        $result = mysqli_query($this->db, $query);

        if (!$result) {
            return [];
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
