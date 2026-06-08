<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Tambah Gudang</title>
</head>

<body>
    <h1>Tambah Gudang</h1>

    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color: red;">
            <?= htmlspecialchars($_SESSION['error']); ?>
        </p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="post" action="/master/gudang/store">
        <div>
            <label>Nama Gudang<br>
                <input type="text" name="nama_gudang" required>
            </label>
        </div>

        <div>
            <label>Lokasi Gudang<br>
                <input type="text" name="lokasi_gudang" required>
            </label>
        </div>

        <div>
            <label>Jenis Gudang<br>
                <select name="jenis_gudang" required>
                    <option value="Pusat">Pusat</option>
                    <option value="Posyandu">Posyandu</option>
                </select>
            </label>
        </div>

        <div>
            <label>Alamat Lengkap<br>
                <textarea name="alamat_lengkap" rows="3" required></textarea>
            </label>
        </div>

        <div>
            <label>Nama Pengelola<br>
                <input type="text" name="nama_pengelola">
            </label>
        </div>

        <button type="submit">Simpan Gudang</button>
    </form>

    <p><a href="/dashboard">Kembali ke Dashboard</a></p>
</body>

</html>