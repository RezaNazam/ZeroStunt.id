<?php

$pageTitle = 'Master Satuan';
$pageSubtitle = 'Kelola satuan komoditas pangan';

ob_start();
?>

<div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">

    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">

        <div>
            <h2 class="text-xl font-extrabold text-gray-900">
                Daftar Satuan
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Data satuan yang digunakan pada komoditas pangan.
            </p>
        </div>

        <a href="/master/satuan/create"
            class="px-4 py-2 rounded-xl bg-teal-600 text-white font-semibold hover:bg-teal-700 transition">

            + Tambah Satuan

        </a>

    </div>

    <div class="overflow-x-auto">

        <table class="w-full text-sm">

            <thead class="bg-gray-50">

                <tr>
                    <th class="text-left px-6 py-4 font-bold text-gray-600">ID</th>
                    <th class="text-left px-6 py-4 font-bold text-gray-600">Nama Satuan</th>
                    <th class="text-left px-6 py-4 font-bold text-gray-600">Singkatan</th>
                    <th class="text-left px-6 py-4 font-bold text-gray-600">Aksi</th>
                </tr>

            </thead>

            <tbody class="divide-y divide-gray-100">

                <?php foreach ($satuans as $satuan): ?>

                    <tr class="hover:bg-gray-50 transition">

                        <td class="px-6 py-4 font-semibold text-gray-900">
                            <?= htmlspecialchars($satuan['id_satuan']) ?>
                        </td>

                        <td class="px-6 py-4 text-gray-700">
                            <?= htmlspecialchars($satuan['nama_satuan']) ?>
                        </td>

                        <td class="px-6 py-4">

                            <span class="px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-xs font-bold">

                                <?= htmlspecialchars($satuan['singkat']) ?>

                            </span>

                        </td>

                        <td class="px-6 py-4">

                            <div class="flex gap-2">

                                <a href="/master/satuan/edit?id=<?= $satuan['id_satuan'] ?>"
                                    class="px-3 py-1 rounded-lg bg-amber-100 text-amber-700 text-xs font-bold hover:bg-amber-200">

                                    ✏️ Edit

                                </a>

                                <a href="/master/satuan/delete?id=<?= $satuan['id_satuan'] ?>"
                                    onclick="return confirm('Yakin ingin menghapus satuan ini?')"
                                    class="px-3 py-1 rounded-lg bg-red-100 text-red-700 text-xs font-bold hover:bg-red-200">

                                    🗑️ Hapus

                                </a>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';