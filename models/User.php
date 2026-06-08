<?php
class User
{
    protected $db;

    public function __construct()
    {
        global $koneksi;
        $this->db = $koneksi;
    }

    public function create($username, $password, $role = 'admin')
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

    public function isProfileComplete($userId, $role)
    {
        if ($role === ROLE_IBU) {
            $stmt = mysqli_prepare($this->db, "SELECT id_ibu FROM ibu WHERE id_ibu = ? ");

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
            $stmt = mysqli_prepare($this->db, "SELECT id_petani FROM petani_lokal WHERE id_petani = ? ");

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

    public function findByUsername($username)
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
