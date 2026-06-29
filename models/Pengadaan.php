<?php

class Pengadaan
{
    protected $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    // --- Transaksi Pengadaan func Model ---

    // Mengambil seluruh riwayat pengadaan (Untuk Hak Akses Admin)
    public function all(): array
    {
        $query = "SELECT p.*, g.nama_gudang, pl.nama_lahan as nama_petani 
                  FROM t_pengadaan p
                  JOIN gudang g ON p.id_gudang = g.id_gudang
                  LEFT JOIN petani_lokal pl ON p.id_petani = pl.id_petani
                  ORDER BY p.id_pengadaan DESC";

        $result = mysqli_query($this->db, $query);
        if (!$result) {
            return [];
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['details'] = $this->getDetails($row['id_pengadaan']);
            $data[] = $row;
        }

        return $data;
    }

    // Mengambil item komoditas detail berdasarkan ID Pengadaan (Header)
    public function getDetails($id_pengadaan): array
    {
        $stmt = mysqli_prepare($this->db, "
            SELECT pd.*, k.nama_komoditas, s.singkat as satuan
            FROM t_pengadaan_detail pd
            JOIN komoditas_pangan k ON pd.id_komoditas = k.id_komoditas
            JOIN satuan s ON k.id_satuan = s.id_satuan
            WHERE pd.id_pengadaan = ?
        ");

        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'i', $id_pengadaan);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $details = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);

        return $details;
    }

    // Mengambil data requirement yang status kontraknya masih 'Mencari Petani'
    public function getAvailable(): array
    {
        $query = "SELECT p.*, g.nama_gudang 
                  FROM t_pengadaan p
                  JOIN gudang g ON p.id_gudang = g.id_gudang
                  WHERE p.status_kontrak = 'Mencari Petani'
                  ORDER BY p.id_pengadaan DESC";

        $result = mysqli_query($this->db, $query);
        if (!$result) {
            return [];
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['details'] = $this->getDetails($row['id_pengadaan']);
            $data[] = $row;
        }

        return $data;
    }

    // Mengambil riwayat pengadaan yang sukses diambil/di-ACC oleh Petani tertentu
    public function getByPetani($idPetani): array
    {
        $stmt = mysqli_prepare($this->db, "
            SELECT p.*, g.nama_gudang 
            FROM t_pengadaan p
            JOIN gudang g ON p.id_gudang = g.id_gudang
            WHERE p.id_petani = ?
            ORDER BY p.id_pengadaan DESC
        ");

        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'i', $idPetani);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['details'] = $this->getDetails($row['id_pengadaan']);
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);

        return $data;
    }

    // Aksi Petani mengambil/mengunci lowongan kerja sama pengadaan (ACC)
    public function ambil($idPengadaan, $idPetani): bool
    {
        $stmt = mysqli_prepare($this->db, "
            UPDATE t_pengadaan
            SET id_petani = ?, status_kontrak = 'Disetujui'
            WHERE id_pengadaan = ? AND status_kontrak = 'Mencari Petani'
        ");

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'ii', $idPetani, $idPengadaan);
        $executed = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $executed;
    }

    // -- fungsi pagination pengadaan atmint --

    // --- baut itung jumlah pengadaan --
    public function countAll(): int
    {
        $query = "SELECT COUNT(*) as total FROM t_pengadaan";
        $result = mysqli_query($this->db, $query);

        if (!$result) {
            return 0;
        }

        $row = mysqli_fetch_assoc($result);
        return (int) ($row['total'] ?? 0);
    }

    // Mengambil data pengadaan yang sudah dibatasi halaman (Paginated) beserta detailnya
    public function getPaginated($limit, $offset): array
    {
        $stmt = mysqli_prepare($this->db, "
            SELECT p.*, g.nama_gudang, pl.nama_lahan as nama_petani 
            FROM t_pengadaan p
            JOIN gudang g ON p.id_gudang = g.id_gudang
            LEFT JOIN petani_lokal pl ON p.id_petani = pl.id_petani
            ORDER BY p.id_pengadaan DESC
            LIMIT ? OFFSET ?
        ");

        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'ii', $limit, $offset);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Tetap panggil detail item komoditasnya agar tidak kosong di tabel view
            $row['details'] = $this->getDetails($row['id_pengadaan']);
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);

        return $data;
    }

    // Menghitung total riwayat kontrak milik petani tertentu
    public function countByPetani($idPetani): int
    {
        $stmt = mysqli_prepare($this->db, "SELECT COUNT(*) as total FROM t_pengadaan WHERE id_petani = ?");
        if (!$stmt) {
            return 0;
        }
        mysqli_stmt_bind_param($stmt, 'i', $idPetani);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return (int) ($row['total'] ?? 0);
    }

    // Menghitung total lowongan pengadaan yang masih tersedia
    public function countAvailable(): int
    {
        $query = "SELECT COUNT(*) as total FROM t_pengadaan WHERE status_kontrak = 'Mencari Petani'";
        $result = mysqli_query($this->db, $query);
        if (!$result) {
            return 0;
        }
        $row = mysqli_fetch_assoc($result);
        return (int) ($row['total'] ?? 0);
    }

    // paginate lowongan pengadaan tersedia
    public function getAvailablePaginated($limit, $offset): array
    {
        $stmt = mysqli_prepare($this->db, "
            SELECT p.*, g.nama_gudang 
            FROM t_pengadaan p
            JOIN gudang g ON p.id_gudang = g.id_gudang
            WHERE p.status_kontrak = 'Mencari Petani'
            ORDER BY p.id_pengadaan DESC
            LIMIT ? OFFSET ?
        ");

        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'ii', $limit, $offset);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['details'] = $this->getDetails($row['id_pengadaan']);
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);

        return $data;
    }

    // paginate data kontrak kerjasama petani
    public function getByPetaniPaginated($idPetani, $limit, $offset): array
    {
        $stmt = mysqli_prepare($this->db, "
            SELECT p.*, g.nama_gudang 
            FROM t_pengadaan p
            JOIN gudang g ON p.id_gudang = g.id_gudang
            WHERE p.id_petani = ?
            ORDER BY p.id_pengadaan DESC
            LIMIT ? OFFSET ?
        ");

        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'iii', $idPetani, $limit, $offset);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['details'] = $this->getDetails($row['id_pengadaan']);
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);

        return $data;
    }

    // simpen data ke dua tabel sekaligus (header dan detail)
    // simpan pengadaan baru

    public function simpanPengadaanBaru(array $dataHeader, array $dataDetail)
    {
        mysqli_begin_transaction($this->db);

        try {
            // --- buat no pengadaan otomatis ---
            $bulanTahunAktif = date('Ym', strtotime($dataHeader['tanggal_pengadaan']));
            $formatPrefix = "REQ-" . $bulanTahunAktif . "-";

            // cek nomor
            $kueriCekNomor = "SELECT no_kontrak FROM t_pengadaan WHERE no_kontrak LIKE '$formatPrefix%' ORDER BY id_pengadaan DESC LIMIT 1";
            $eksekusiCekNomor = mysqli_query($this->db, $kueriCekNomor);

            $nomorUrutBerikutnya = "001";
            if ($eksekusiCekNomor && mysqli_num_rows($eksekusiCekNomor) > 0) {
                // Perbaikan: mysqli_assoc diubah menjadi mysqli_fetch_assoc
                $barisData = mysqli_fetch_assoc($eksekusiCekNomor);

                $nomorTerakhir = (int) substr($barisData['no_kontrak'], -3);
                $nomorUrutBerikutnya = str_pad($nomorTerakhir + 1, 3, "0", STR_PAD_LEFT);
            }

            $nomorKontrakOtomatis = $formatPrefix . $nomorUrutBerikutnya;


            // --- SIMPAN DATA KE TABEL HEADER (t_pengadaan) ---
            $kueriHeader = "INSERT INTO t_pengadaan (no_kontrak, id_gudang, tgl_pengadaan, status_bayar, status_kontrak, keterangan) VALUES (?, ?, ?, 'Pending', 'Mencari Petani', ?)";
            $stmtHeader = mysqli_prepare($this->db, $kueriHeader);

            if (!$stmtHeader) {
                throw new Exception("Gagal menyiapkan sistem pencatatan data utama (Header).");
            }

            mysqli_stmt_bind_param($stmtHeader, 'siss', $nomorKontrakOtomatis, $dataHeader['id_gudang'], $dataHeader['tanggal_pengadaan'], $dataHeader['keterangan']);
            $suksesSimpanHeader = mysqli_stmt_execute($stmtHeader);

            if (!$suksesSimpanHeader) {
                throw new Exception("Gagal mengeksekusi penyimpanan data utama pengadaan.");
            }

            // Ambil ID primary key yang baru saja digenerate oleh auto_increment tabel header
            $idPengadaanBaru = mysqli_insert_id($this->db);
            mysqli_stmt_close($stmtHeader);


            // --- SIMPAN DATA RINCIAN KE TABEL DETAIL (t_pengadaan_detail) ---
            $kueriDetail = "INSERT INTO t_pengadaan_detail (id_pengadaan, id_komoditas, jumlah, harga_satuan) VALUES (?, ?, ?, ?)";
            $stmtDetail = mysqli_prepare($this->db, $kueriDetail);

            if (!$stmtDetail) {
                throw new Exception("Gagal menyiapkan sistem pencatatan rincian komoditas (Detail).");
            }

            $totalBarisKomoditas = count($dataDetail['id_komoditas']);

            for ($i = 0; $i < $totalBarisKomoditas; $i++) {
                $idKomoditas = (int) $dataDetail['id_komoditas'][$i];
                $jumlahBarang = (float) $dataDetail['jumlah'][$i];
                $hargaSatuan = (float) $dataDetail['harga_satuan'][$i];

                // --- VALIDASI LEVEL APLIKASI (DATA INTEGRITY BOUNDARY) ---
                if ($idKomoditas <= 0 || $jumlahBarang <= 0 || $hargaSatuan < 0) {
                    throw new Exception("Data barang pada baris ke-" . ($i + 1) . " tidak valid. Jumlah harus lebih dari 0 dan harga tidak boleh minus.");
                }

                mysqli_stmt_bind_param($stmtDetail, 'iidd', $idPengadaanBaru, $idKomoditas, $jumlahBarang, $hargaSatuan);
                $suksesSimpanDetail = mysqli_stmt_execute($stmtDetail);

                if (!$suksesSimpanDetail) {
                    throw new Exception("Gagal menyimpan rincian barang pada baris ke-" . ($i + 1));
                }
            }

            mysqli_stmt_close($stmtDetail);

            // Jika sampai di sini semua baris data sukses tersimpan, kunci data secara permanen
            mysqli_commit($this->db);
            return true;

        } catch (Exception $kesalahan) {
            // Jika terjadi kegagalan di dalam blok try, batalkan semua operasi insert di atas
            mysqli_rollback($this->db);
            $_SESSION['error'] = $kesalahan->getMessage();
            return false;
        }
    }

    // Mencari data tunggal induk pengadaan berdasarkan ID primary key
    public function find($id_pengadaan): array
    {
        $stmt = mysqli_prepare($this->db, "
            SELECT p.*, g.nama_gudang, pl.nama_lahan as nama_petani 
            FROM t_pengadaan p
            JOIN gudang g ON p.id_gudang = g.id_gudang
            LEFT JOIN petani_lokal pl ON p.id_petani = pl.id_petani
            WHERE p.id_pengadaan = ?
        ");

        if (!$stmt)
            return [];

        mysqli_stmt_bind_param($stmt, 'i', $id_pengadaan);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result) ?: [];
        mysqli_stmt_close($stmt);

        if (!empty($data)) {
            $data['details'] = $this->getDetails($id_pengadaan);
        }

        return $data;
    }

    // proses verifikasi dan pelunasan ketika status kontrak = disetujui

    public function prosesPelunasanKontrak($idPengadaan): bool
    {
        $stmt = mysqli_prepare($this->db, "UPDATE t_pengadaan SET status_bayar = 'Lunas' WHERE id_pengadaan = ? AND status_kontrak = 'Disetujui'
        ");

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'i', $idPengadaan);
        $eksekusi = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $eksekusi;
    }
}