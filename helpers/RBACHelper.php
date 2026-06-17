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
            ErrorHelper::show(
                403,
                'Akunmu tidak memiliki izin untuk mengakses halaman ini.'
            );
        }
    }

    public static function require_any_role(array $allowed_roles)
    {
        self::require_login();

        $currentRole = $_SESSION['role'] ?? null;

        if (!in_array($currentRole, $allowed_roles, true)) {
            ErrorHelper::show(
                403,
                'Fitur ini tidak tersedia untuk role akun yang sedang kamu gunakan.'
            );
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
            ErrorHelper::show(
                403,
                'Akunmu tidak memiliki izin untuk mengakses halaman ini.'
            );
        }
    }
}
