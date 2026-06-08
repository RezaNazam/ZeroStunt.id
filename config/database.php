<?php
// --- Koneksi Database ---
$host = "localhost";
$user = "root";
$pass = "";
$db   = "zerostunt_db";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// --- Konstanta Role ---
define('ROLE_ADMIN',  'Admin');
define('ROLE_KADER',  'Kader');
define('ROLE_PETANI', 'Petani');
define('ROLE_IBU',    'Ibu');