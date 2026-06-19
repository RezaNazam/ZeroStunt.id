<?php
$pageTitle = 'Master Komoditas Pangan';
$pageSubtitle = 'Kelola data komoditas pangan untuk pengadaan, stok, dan distribusi.';

$komoditas = $komoditas ?? [];

ob_start();
?>

<div class="space-y-6">
    <!-- Header Action -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900">
                Data Komoditas Pangan
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Daftar bahan pangan yang digunakan dalam sistem ZeroStunt.id.
            </p>
        </div>

        <a href="/master/komoditas/create"
            class="inline-flex items-center justify-center rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-700">
            + Tambah Komoditas
        </a>
    </div>

    <!-- Flash Message -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="rounded-2xl border border-green-100 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
            <?= htmlspecialchars($_SESSION['success']); ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            <?= htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Table Card -->
    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-5">
            <h3 class="text-lg font-extrabold text-gray-900">
                Tabel Komoditas
            </h3>
            <p class="text-sm text-gray-500 mt-1">
                Total data: <?= count($komoditas); ?> komoditas
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">ID</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Nama Komoditas</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Kategori Gizi</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Satuan</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Deskripsi</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Tanggal Dibuat</th>
                        <th class="px-6 py-4 text-right font-bold text-gray-600">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($komoditas)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="mx-auto max-w-sm">
                                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-teal-50 text-2xl">
                                        🥬
                                    </div>
                                    <p class="font-bold text-gray-900">
                                        Belum ada komoditas
                                    </p>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Tambahkan data komoditas pangan pertama untuk mulai mengisi master data.
                                    </p>
                                    <a href="/master/komoditas/create"
                                        class="mt-5 inline-flex rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white hover:bg-teal-700">
                                        Tambah Komoditas
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($komoditas as $row): ?>
                        <tr class="transition hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($row['id_komoditas']); ?>
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">
                                    <?= htmlspecialchars($row['nama_komoditas']); ?>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full bg-teal-50 px-3 py-1 text-xs font-bold text-teal-700">
                                    <?= htmlspecialchars($row['kategori_gizi']); ?>
                                </span>
                            </td>

                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($row['nama_satuan'] ?? '-'); ?>
                            </td>

                            <td class="max-w-xs px-6 py-4 text-gray-500">
                                <p class="truncate">
                                    <?= htmlspecialchars($row['deskripsi'] ?: '-'); ?>
                                </p>
                            </td>

                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($row['tgl_created'] ?? '-'); ?>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="/master/komoditas/edit?id=<?= urlencode($row['id_komoditas']); ?>"
                                        class="rounded-xl bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">
                                        Edit
                                    </a>

                                    <a href="/master/komoditas/delete?id=<?= urlencode($row['id_komoditas']); ?>"
                                        onclick="return confirm('Yakin ingin menghapus komoditas ini?')"
                                        class="rounded-xl bg-red-50 px-3 py-2 text-xs font-bold text-red-700 transition hover:bg-red-100">
                                        Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';