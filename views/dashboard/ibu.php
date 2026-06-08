<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Dashboard Ibu</title>
</head>

<body>
    <h1>Dashboard Ibu</h1>
    <p>Selamat datang, <?= htmlspecialchars($_SESSION['username'] ?? 'Ibu'); ?>!</p>
    <p><a href="/auth/logout">Logout</a></p>
</body>

</html>