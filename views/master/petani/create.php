<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Lengkapi Profil Petani</title>
</head>

<body>
    <h1>Lengkapi Profil Petani</h1>

    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color: red;">
            <?= htmlspecialchars($_SESSION['error']); ?>
        </p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="post" action="/master/petani/store">
        <div>
            <label>Nama Lahan<br>
                <input type="text" name="nama_lahan" required>
            </label>
        </div>

        <div>
            <label>Alamat Lahan<br>
                <textarea name="alamat_lahan" rows="3"></textarea>
            </label>
        </div>

        <div>
            <label>Nomor Rekening<br>
                <input type="text" name="no_rekening">
            </label>
        </div>

        <div>
            <label>Kapasitas Panen per Bulan (Kg)<br>
                <input type="number" name="kapasitas_panen_bulan" step="0.01">
            </label>
        </div>

        <button type="submit">Simpan Profil</button>
    </form>

    <p><a href="/dashboard">Kembali ke Dashboard</a></p>
</body>

</html>