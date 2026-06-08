<?php
class RBACHelper
{

    // Permission matrix: role => [allowed_actions]
    private static $permissions = [
        ROLE_ADMIN => [
            'dashboard.admin',
            'master.*',
            'pemeriksaan.read',
            'pengadaan.*',
            'distribusi.*',
            'penyerahan.read',
            'report.*',
        ],
        ROLE_KADER => [
            'dashboard.kader',
            'master.read',
            'pemeriksaan.*',
            'distribusi.update_status',
            'penyerahan.*',
            'report.stok',
            'report.tren',
        ],
        ROLE_PETANI => [
            'dashboard.petani',
            'master.petani.update_own',
            'pengadaan.read_own',
            'report.ekonomi_own',
        ],
        ROLE_IBU => [
            'dashboard.ibu',
            'master.ibu.read_own',
            'master.anak.read_own',
            'penyerahan.read_own',
        ],
    ];

    public static function require_login()
    {
        if (!AuthHelper::is_logged_in()) {
            header("Location: /auth/login");
            exit;
        }
    }

    public static function require_role($required_role)
    {
        self::require_login();
        if ($_SESSION['role'] !== $required_role) {
            http_response_code(403);
            die("403 - Access Denied: You don't have permission to access this page.");
        }
    }

    public static function check_permission($role, $action)
    {
        if (!isset(self::$permissions[$role])) {
            return false;
        }
        $allowed = self::$permissions[$role];
        foreach ($allowed as $permission) {
            // Wildcard match: 'master.*' allows 'master.list', 'master.create', etc
            if ($permission === $action)
                return true;
            $prefix = rtrim($permission, '*');
            if (strpos($permission, '*') !== false && strpos($action, $prefix) === 0) {
                return true;
            }
        }
        return false;
    }

    public static function can($action)
    {
        $role = AuthHelper::current_role();
        return self::check_permission($role, $action);
    }

    public static function enforce($action)
    {
        if (!self::can($action)) {
            http_response_code(403);
            die("403 - Access Denied.");
        }
    }
}
?>