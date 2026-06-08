<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Dashboard Petani</title>
</head>

<body>
    <h1>Dashboard Petani</h1>
    <p>Selamat datang, <?= htmlspecialchars($_SESSION['username'] ?? 'Petani'); ?>!</p>
    <p><a href="/auth/logout">Logout</a></p>
</body>

</html>