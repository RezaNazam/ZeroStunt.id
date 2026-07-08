<?php
class User
{
    protected $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    // --- Master User func Model ---

    // ambil semua data user
    public function all(): array
    {
        $stmt = mysqli_prepare($this->db, "SELECT u.id_user, u.username, u.role, u.is_active, u.created_at, u.id_gudang, g.nama_gudang FROM users u LEFT JOIN gudang g ON u.id_gudang = g.id_gudang WHERE u.role IN ('Admin','Kader') ORDER BY u.role ASC");
        if (!$stmt) {
            return [];
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);

        return $users;
    }

    // func register dengan default role admin

    // fungsi ini bisa buat regis maupun buat admin dan kader
    public function create($username, $password, $role = 'Admin', $id_gudang = null)
    {
        if ($this->findByUsername($username)) {
            return false;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = mysqli_prepare($this->db, "INSERT INTO users (username, password, role, id_gudang) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'sssi', $username, $hash, $role, $id_gudang);
        $executed = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $executed;
    }

    // ambik data user by id buat edit sama validas
    public function findByid($id_user)
    {
        $stmt = mysqli_prepare($this->db, "SELECT id_user, username, role, is_active, created_at, id_gudang FROM users WHERE id_user = ? LIMIT 1");
        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $id_user);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return $user;
    }


    // buat updatte user
    public function update($id_user, $username, $role, $id_gudang = null, $password = null, $is_active = 1)
    {
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = mysqli_prepare($this->db, "UPDATE users SET username = ?, role = ?, id_gudang = ?, password = ?, is_active = ? WHERE id_user = ?");
            if (!$stmt) {
                return false;
            }
            mysqli_stmt_bind_param($stmt, 'sssiii', $username, $role, $id_gudang, $hash, $is_active, $id_user);
        } else {
            $stmt = mysqli_prepare($this->db, "UPDATE users SET username = ?, role = ?, id_gudang = ?, is_active = ? WHERE id_user = ?");
            if (!$stmt) {
                return false;
            }
            mysqli_stmt_bind_param($stmt, 'ssiii', $username, $role, $id_gudang, $is_active, $id_user);
        }

        $executed = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $executed;
    }

    // buat delete user
    public function delete($id_user)
    {
        $stmt = mysqli_prepare($this->db, "UPDATE users SET is_active = 0 WHERE id_user = ?");
        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'i', $id_user);
        $executed = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $executed;
    }

    // --- AUTH N REGIS ---

    // lengkapin profil
    public function isProfileComplete($userId, $role)
    {
        if ($role === ROLE_IBU) {
            $stmt = mysqli_prepare($this->db, "SELECT id_ibu FROM ibu WHERE id_ibu = ? LIMIT 1");

            if (!$stmt) {
                return false;
            }


            mysqli_stmt_bind_param($stmt, 'i', $userId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $complete = mysqli_num_rows($result) > 0;
            mysqli_stmt_close($stmt);

            return $complete;
        }

        if ($role === ROLE_PETANI) {
            $query = "
        SELECT 
            p.id_petani,
            p.luas_lahan,
            p.jenis_usaha,
            p.status_lahan,
            COUNT(pk.id_petani_komoditas) AS total_komoditas
        FROM petani_lokal p
        LEFT JOIN petani_lahan_komoditas pk 
            ON p.id_petani = pk.id_petani
            AND pk.deleted_at IS NULL
        WHERE p.id_petani = ?
          AND p.deleted_at IS NULL
        GROUP BY 
            p.id_petani,
            p.luas_lahan,
            p.jenis_usaha,
            p.status_lahan
        LIMIT 1
    ";

            $stmt = mysqli_prepare($this->db, $query);

            if (!$stmt) {
                return false;
            }

            mysqli_stmt_bind_param($stmt, 'i', $userId);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $petani = mysqli_fetch_assoc($result);

            mysqli_stmt_close($stmt);

            if (!$petani) {
                return false;
            }

            return
                !empty($petani['luas_lahan']) &&
                !empty($petani['jenis_usaha']) &&
                !empty($petani['status_lahan']) &&
                (int) $petani['total_komoditas'] > 0;
        }

        return true;
    }

    // cari by username buat login
    public function findByUsername($username)
    {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM users WHERE username = ? AND is_active = 1 LIMIT 1");
        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return $user;
    }

    // additional method to find user by username without checking is_active
    public function findByUsernameWithoutActiveCheck($username)
    {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM users WHERE username = ? LIMIT 1");
        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return $user;
    }

    public function updateProfile($id_user, $username)
    {
        $stmt = mysqli_prepare(
            $this->db,
            "UPDATE users
         SET username = ?
         WHERE id_user = ?
         AND is_active = 1"
        );

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'si', $username, $id_user);
        $executed = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $executed;
    }
}
