<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Edit Gudang</title>
</head>

<body>
    <h1>Edit Gudang</h1>

    <?php
    /** @var array<string,mixed> $gudang */
    $gudang = $gudang ?? [
        'id_gudang' => '',
        'nama_gudang' => '',
        'lokasi_gudang' => '',
        'jenis_gudang' => 'Pusat',
        'alamat_lengkap' => '',
        'nama_pengelola' => '',
    ];
    ?>

    <?php if (!empty($_SESSION['error'])): ?>
            <p style="color:red;"><?= htmlspecialchars($_SESSION['error']) ?></p>
            <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="post" action="/master/gudang/update">
        <input type="hidden" name="id_gudang" value="<?= htmlspecialchars($gudang['id_gudang']) ?>">

        <div>
            <label>Nama Gudang<br>
                <input type="text" name="nama_gudang" value="<?= htmlspecialchars($gudang['nama_gudang']) ?>" required>
            </label>
        </div>

        <div>
            <label>Lokasi Gudang<br>
                <input type="text" name="lokasi_gudang" value="<?= htmlspecialchars($gudang['lokasi_gudang']) ?>"
                    required>
            </label>
        </div>

        <div>
            <label>Jenis Gudang<br>
                <select name="jenis_gudang" required>
                    <option value="Pusat" <?= $gudang['jenis_gudang'] === 'Pusat' ? 'selected' : '' ?>>Pusat</option>
                    <option value="Posyandu" <?= $gudang['jenis_gudang'] === 'Posyandu' ? 'selected' : '' ?>>Posyandu
                    </option>
                </select>
            </label>
        </div>

        <div>
            <label>Alamat Lengkap<br>
                <textarea name="alamat_lengkap" rows="3"
                    required><?= htmlspecialchars($gudang['alamat_lengkap']) ?></textarea>
            </label>
        </div>

        <div>
            <label>Nama Pengelola<br>
                <input type="text" name="nama_pengelola" value="<?= htmlspecialchars($gudang['nama_pengelola']) ?>">
            </label>
        </div>

        <button type="submit">Simpan</button>
    </form>

    <p><a href="/master/gudang">Kembali</a></p>
</body>

</html>