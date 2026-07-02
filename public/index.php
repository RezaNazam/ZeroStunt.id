<?php
// ============================================================
// ENTRY POINT
// Semua request masuk ke sini dulu sebelum ke controller
// ============================================================

session_start();

// --- Load Config ---
require_once '../config/database.php';

// --- Load Helpers ---
require_once '../helpers/AuthHelper.php';
require_once '../helpers/RBACHelper.php';
require_once '../helpers/ErrorHelper.php';
require_once '../helpers/PaginationHelper.php';
require_once '../helpers/SearchHelper.php';
require_once '../helpers/NumberHelper.php';

// --- Load Models ---
require_once '../models/PaketGizi.php';
require_once '../models/Dashboard.php';
require_once '../models/Laporan.php';

// --- Load Vendor Libraries ---
require_once '../vendor/autoload.php';

// --- Load Models ---
foreach (glob('../models/*.php') as $file) {
    require_once $file;
}

// --- Load Controllers ---
foreach (glob('../controllers/*.php') as $file) {
    require_once $file;
}

// --- Load Routes ---
$routes = require_once '../routes.php';

// --- Ambil URL yang diminta user ---
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Route publik
$publicRoutes = [
    '/',
    '/landing',
    '/auth/login',
    '/auth/register',
];

$isLoggedIn = !empty($_SESSION['user_id']);

if (!$isLoggedIn && !in_array($uri, $publicRoutes, true)) {
    header('Location: /landing');
    exit;
}

if ($isLoggedIn && in_array($uri, ['/auth/login', '/auth/register'], true)) {
    header('Location: /dashboard');
    exit;
}

// Root selalu ke landing
if ($uri === '/') {
    header('Location: /landing');
    exit;
}

// --- Cari route yang cocok ---
if (array_key_exists($uri, $routes)) {
    $route = $routes[$uri];

    $controller = $route[0];
    $method = $route[1];
    $requiredRole = $route[2] ?? null;

    // Jalankan pengecekan role sebelum controller
    if ($requiredRole !== null) {
        if (is_array($requiredRole)) {
            RBACHelper::require_any_role($requiredRole);
        } else {
            RBACHelper::require_role($requiredRole);
        }
    }

    (new $controller())->$method();
    exit;
}