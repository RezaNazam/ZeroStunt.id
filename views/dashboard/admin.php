<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Dashboard Admin</title>
</head>

<body>
    <h1>Dashboard Admin</h1>
    <p>Selamat datang, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>!</p>
    <p>Role Anda: <?= htmlspecialchars($_SESSION['role'] ?? 'Tidak diketahui'); ?></p>
    <p><a href="/master/gudang">Kelola Gudang</a></p>
    <p><a href="/master/gudang/create">Tambah Gudang</a></p>
    <p><a href="/auth/logout">Logout</a></p>
</body>

</html>