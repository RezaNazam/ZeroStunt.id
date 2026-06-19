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
        $stmt = mysqli_prepare($this->db, "SELECT id_user, username, role, is_active FROM users WHERE deleted_at IS NULL ORDER BY role ASC");
        if (!$stmt) {
            return [];
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $users = mysqli_fetch_all(result: $result, mode: MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);

        return $users;
    }

    // func register dengan default role admin

    // fungsi ini bisa buat regis maupun buat admin dan kader
    public function create($username, $password, $role = 'Admin')
    {
        if ($this->findByUsername($username)) {
            return false;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = mysqli_prepare($this->db, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'sss', $username, $hash, $role);
        $executed = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $executed;
    }

    // ambik data user by id buat edit sama validas
    public function findByid($id_user)
    {
        $stmt = mysqli_prepare($this->db, "SELECT id_user, username, role, is_active FROM users WHERE id_user = ? AND deleted_at IS NULL LIMIT 1");
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
    public function update($id_user)
    {
        $stmt = mysqli_prepare($this->db, "UPDATE users SET username = ?, role = ? WHERE id_user = ? AND deleted_at IS NULL");
        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'ssi', $username, $role, $id_user);
        $executed = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $executed;
    }

    // buat delete user
    public function delete($id_user)
    {
        $stmt = mysqli_prepare($this->db, "UPDATE users SET deleted_at = NOW(), is_active = 0 WHERE id_user = ? AND deleted_at IS NULL");
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
            $stmt = mysqli_prepare($this->db, "SELECT id_ibu FROM ibu WHERE id_ibu = ? AND deleted_at IS NULL LIMIT 1");

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
            $stmt = mysqli_prepare($this->db, "SELECT id_petani FROM petani_lokal WHERE id_petani = ? LIMIT 1");

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

        return true;
    }

    // cari by username buat login
    public function findByUsername($username)
    {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM users WHERE username = ? AND deleted_at IS NULL LIMIT 1");
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


    // seeder admin kalo blom ada
    public function ensureAdminExists()
    {
        $admin = $this->findByUsername('admin');
        if ($admin) {
            return $admin;
        }

        $hash = '$2y$12$eoq1NXMct2oLGC1LbLydZ.HzVB0wZQq5U3UtjJ9wia/JL1YjJh/rm';
        $stmt = mysqli_prepare($this->db, "INSERT INTO users (username, password, role, is_active) VALUES (?, ?, 'Admin', 1)");
        if (!$stmt) {
            return null;
        }

        $username = 'admin';
        mysqli_stmt_bind_param($stmt, 'ss', $username, $hash);
        $executed = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($executed) {
            return $this->findByUsername('admin');
        }

        return null;
    }
}
