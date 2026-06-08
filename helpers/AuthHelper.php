<?php
class AuthHelper
{

    public static function login($username, $password)
    {
        $db = new Database();
        $user = $db->fetch_one(
            "SELECT * FROM users WHERE username = ? AND is_active = 1",
            [$username]
        );

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Store gudang_id if Kader (for data isolation)
            if ($user['role'] === ROLE_KADER) {
                $kader = $db->fetch_one(
                    "SELECT id_gudang FROM users_gudang WHERE id_user = ?",
                    [$user['id_user']]
                );
                $_SESSION['gudang_id'] = $kader['id_gudang'] ?? null;
            }

            return true;
        }
        return false;
    }

    public static function logout()
    {
        session_unset();
        session_destroy();
        header("Location: /auth/login");
        exit;
    }

    public static function is_logged_in()
    {
        return isset($_SESSION['user_id']);
    }

    public static function current_user()
    {
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'role' => $_SESSION['role'] ?? null,
        ];
    }

    public static function current_role()
    {
        return $_SESSION['role'] ?? null;
    }

    public static function hash_password($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
?>