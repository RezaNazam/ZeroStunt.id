<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color: red;"><?= htmlspecialchars($_SESSION['error']); ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['success'])): ?>
        <p style="color: green;"><?= htmlspecialchars($_SESSION['success']); ?></p>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <form method="post" action="/auth/login">
        <div>
            <label>Username<br>
                <input type="text" name="username" required>
            </label>
        </div>
        <div>
            <label>Password<br>
                <input type="password" name="password" required>
            </label>
        </div>
        <button type="submit">Login</button>
    </form>
    <p><a href="/auth/register">Belum punya akun? Daftar</a></p>
</body>
</html>
