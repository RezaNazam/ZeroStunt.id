<?php

$pageTitle = 'Profil Lahan';
$pageSubtitle = 'Informasi lahan dan komoditas yang dikelola petani.';

$lahan = [
    'pemilik' => 'Bharata',
    'lokasi' => 'Bekasi Timur',
    'luas' => '2 Hektar',
    'jenis' => 'Pertanian',
    'status' => 'Aktif',
];

$komoditas = [
    [
        'nama' => 'Padi',
        'luas' => '1 Ha',
        'panen' => '5 Ton'
    ],
    [
        'nama' => 'Cabai',
        'luas' => '0.5 Ha',
        'panen' => '1 Ton'
    ],
    [
        'nama' => 'Tomat',
        'luas' => '0.5 Ha',
        'panen' => '800 Kg'
    ],
];

ob_start();
?>

<div class="space-y-6">

    <!-- Statistik -->
    <div class="grid md:grid-cols-3 gap-4">

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
            <p class="text-sm text-gray-500">Total Luas Lahan</p>
            <h2 class="text-3xl font-extrabold text-teal-600 mt-2">
                2 Ha
            </h2>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
            <p class="text-sm text-gray-500">Komoditas Aktif</p>
            <h2 class="text-3xl font-extrabold text-emerald-600 mt-2">
                3
            </h2>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
            <p class="text-sm text-gray-500">Estimasi Panen</p>
            <h2 class="text-3xl font-extrabold text-amber-500 mt-2">
                6.8 Ton
            </h2>
        </div>

    </div>

    <!-- Informasi Lahan -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm">

        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-extrabold text-gray-900">
                Informasi Lahan
            </h2>
        </div>

        <div class="grid md:grid-cols-2 gap-6 p-6">

            <div>
                <p class="text-sm text-gray-500">Nama Pemilik</p>
                <p class="font-bold text-gray-900">
                    <?= $lahan['pemilik']; ?>
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Lokasi</p>
                <p class="font-bold text-gray-900">
                    <?= $lahan['lokasi']; ?>
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Jenis Usaha</p>
                <p class="font-bold text-gray-900">
                    <?= $lahan['jenis']; ?>
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Luas Lahan</p>
                <p class="font-bold text-gray-900">
                    <?= $lahan['luas']; ?>
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Status</p>

                <span class="inline-flex px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">
                    <?= $lahan['status']; ?>
                </span>
            </div>

        </div>

    </div>

    <!-- Komoditas -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-extrabold text-gray-900">
                Komoditas yang Dibudidayakan
            </h2>
        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left">Komoditas</th>
                        <th class="px-6 py-4 text-left">Luas Area</th>
                        <th class="px-6 py-4 text-left">Estimasi Panen</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($komoditas as $item): ?>
                        <tr class="border-t border-gray-100">
                            <td class="px-6 py-4 font-medium">
                                <?= $item['nama']; ?>
                            </td>

                            <td class="px-6 py-4">
                                <?= $item['luas']; ?>
                            </td>

                            <td class="px-6 py-4 text-green-600 font-bold">
                                <?= $item['panen']; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

    <!-- Deskripsi -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">

        <h2 class="text-xl font-extrabold text-gray-900 mb-4">
            Deskripsi Lahan
        </h2>

        <p class="text-gray-600 leading-relaxed">
            Lahan pertanian seluas 2 hektar yang digunakan untuk
            budidaya padi, cabai, dan tomat. Memiliki akses
            irigasi yang baik dan aktif mendukung program
            pengadaan komoditas pangan daerah.
        </p>

    </div>

</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>