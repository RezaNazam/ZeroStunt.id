<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Lengkapi Profil Ibu</title>
</head>

<body>
    <h1>Lengkapi Profil Ibu</h1>

    <?php
    /** @var array<int,array{id_gudang:int,nama_gudang:string,jenis_gudang:string}> $gudangs */
    $gudangs = $gudangs ?? [];
    ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color: red;">
            <?= htmlspecialchars($_SESSION['error']); ?>
        </p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="post" action="/master/ibu/store">
        <div>
            <label>NIK (Nomor Induk Kependudukan)<br>
                <input type="text" name="nik_ibu" maxlength="16" required>
            </label>
        </div>

        <div>
            <label>Nama Lengkap<br>
                <input type="text" name="nama_ibu" required>
            </label>
        </div>

        <div>
            <label>Nomor Telepon<br>
                <input type="text" name="no_telp">
            </label>
        </div>

        <div>
            <label>Alamat<br>
                <textarea name="alamat" rows="3"></textarea>
            </label>
        </div>

        <div>
            <label>
                <input type="checkbox" name="is_pregnant"> Sedang Hamil
            </label>
        </div>
        <div>
            <label>Pilih Gudang/Posyandu<br>
                <select name="id_gudang" required>
                    <option value="">--Pilih Gudang--</option>
                    <?php foreach ($gudangs as $gudang): ?>
                        <option value="<?= htmlspecialchars($gudang['id_gudang']) ?>">
                            <?= htmlspecialchars($gudang['nama_gudang'] . ' (' . $gudang['jenis_gudang'] . ')') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>

        <button type="submit">Simpan Profil</button>
    </form>

    <p><a href="/dashboard">Kembali ke Dashboard</a></p>
</body>

</html>