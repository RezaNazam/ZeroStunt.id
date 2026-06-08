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

// --- Redirect root ke login ---
if ($uri === '/') {
    header('Location: /auth/login');
    exit;
}

// --- Cari route yang cocok ---
if (array_key_exists($uri, $routes)) {
    [$controller, $method] = $routes[$uri];
    (new $controller)->$method();
} else {
    // Route tidak ditemukan
    http_response_code(404);
    echo "404 - Halaman tidak ditemukan.";
}