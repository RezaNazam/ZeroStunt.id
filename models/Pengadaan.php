<?php

class Pengadaan
{
    private $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function getAll($limit = 5, $offset = 0)
    {
    $stmt = $this->db->prepare("
        SELECT p.*, k.nama_komoditas
        FROM pengadaan p
        JOIN komoditas_pangan k
            ON k.id_komoditas = p.id_komoditas
        ORDER BY p.id_pengadaan DESC
        LIMIT ? OFFSET ?
    ");

    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();

    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function ambil($idPengadaan, $idPetani)
    {
    $stmt = mysqli_prepare($this->db, "
        UPDATE pengadaan
        SET status='Sudah Diambil',
            id_petani=?
        WHERE id_pengadaan=?
    ");

    mysqli_stmt_bind_param($stmt, 'ii', $idPetani, $idPengadaan);

    mysqli_stmt_execute($stmt);

    return mysqli_stmt_affected_rows($stmt);
    }

    public function countAktif($idPetani)
    {
    $stmt = mysqli_prepare($this->db, "
        SELECT COUNT(*) as total
        FROM pengadaan
        WHERE id_petani = ?
    ");

    mysqli_stmt_bind_param($stmt, 'i', $idPetani);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    return $row['total'] ?? 0;
    }

    public function getByPetani($idPetani)
    {
    $stmt = mysqli_prepare($this->db, "
        SELECT
            p.*,
            k.nama_komoditas
        FROM pengadaan p
        JOIN komoditas_pangan k
            ON k.id_komoditas = p.id_komoditas
        WHERE p.id_petani = ?
        ORDER BY p.id_pengadaan DESC
    ");

    mysqli_stmt_bind_param($stmt, 'i', $idPetani);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getIdPetaniByUsername($username)
    {
    $stmt = mysqli_prepare($this->db, "
        SELECT pl.id_petani
        FROM users u
        JOIN petani_lokal pl ON pl.id_petani = u.id_user
        WHERE u.username = ?
        LIMIT 1
    ");

    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    return $row['id_petani'] ?? null;
    }

    public function countAll()
    {
    $result = mysqli_query($this->db, "
        SELECT COUNT(*) as total FROM pengadaan
    ");

    return mysqli_fetch_assoc($result)['total'];
    }   

    public function getPaginated($limit, $offset)
    {
    $stmt = $this->db->prepare("
        SELECT p.*, k.nama_komoditas
        FROM pengadaan p
        JOIN komoditas_pangan k
            ON k.id_komoditas = p.id_komoditas
        ORDER BY p.id_pengadaan DESC
        LIMIT ? OFFSET ?
    ");

    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

}
