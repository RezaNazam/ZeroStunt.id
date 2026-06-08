<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Register</title>
</head>

<body>
    <h1>Daftar</h1>
    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color: red;"><?= htmlspecialchars($_SESSION['error']); ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <form method="post" action="/auth/register">
        <div>
            <label>
                <input type="radio" name="role" value="Ibu" checked> Saya Ibu
            </label>
            <label>
                <input type="radio" name="role" value="Petani"> Saya Petani
            </label>
        </div>
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
        <div>
            <label>Konfirmasi Password<br>
                <input type="password" name="confirm_password" required>
            </label>
        </div>
        <button type="submit">Daftar</button>
    </form>
    <p><a href="/auth/login">Sudah punya akun? Login</a></p>
</body>

</html>