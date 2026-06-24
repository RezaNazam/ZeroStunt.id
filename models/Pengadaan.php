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

    public function create($data)
    {
    $sql = "INSERT INTO t_pengadaan
    (no_kontrak, nama_gudang, jumlah, harga_satuan, total_bayar, deadline, keterangan, status_kontrak, status_bayar)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param(
        "ssiiissss",
        $data['no_kontrak'],
        $data['nama_gudang'],
        $data['jumlah'],
        $data['harga_satuan'],
        $data['total_bayar'],
        $data['deadline'],
        $data['keterangan'],
        $data['status_kontrak'],
        $data['status_bayar']
    );

    return $stmt->execute();
    }
    
    public function getRiwayatPendapatan($idPetani)
    {
    $sql = "
        SELECT
            p.tgl_pengadaan,
            p.total_bayar,
            p.status_bayar,
            d.jumlah,
            k.nama_komoditas
        FROM t_pengadaan p
        JOIN t_pengadaan_detail d
            ON p.id_pengadaan = d.id_pengadaan
        JOIN komoditas_pangan k
            ON d.id_komoditas = k.id_komoditas
        WHERE p.id_petani = ?
        AND p.status_bayar = 'Lunas'
        ORDER BY p.id_pengadaan DESC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$idPetani]);

    $result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

return $data;
    }
}