<!doctype html>

<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Daftar Gudang</title>
</head>

<body>
    <h1>Daftar Gudang</h1>

    <p><a href="/master/gudang/create">Tambah Gudang</a></p>

    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Lokasi</th>
                <th>Jenis</th>
                <th>Pengelola</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($gudangs as $gudang): ?>
                <tr>
                    <td><?= htmlspecialchars($gudang['id_gudang']) ?></td>
                    <td><?= htmlspecialchars($gudang['nama_gudang']) ?></td>
                    <td><?= htmlspecialchars($gudang['lokasi_gudang']) ?></td>
                    <td><?= htmlspecialchars($gudang['jenis_gudang']) ?></td>
                    <td><?= htmlspecialchars($gudang['nama_pengelola']) ?></td>
                    <td>
                        <a href="/master/gudang/edit?id=<?= urlencode($gudang['id_gudang']) ?>">Edit</a>
                        |
                        <a href="/master/gudang/delete?id=<?= urlencode($gudang['id_gudang']) ?>"
                            onclick="return confirm('Hapus gudang?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p><a href="/dashboard">Kembali</a></p>
</body>

</html>