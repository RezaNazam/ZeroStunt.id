<?php

$pageTitle = 'Profil Lahan';
$pageSubtitle = 'Informasi lahan dan komoditas yang dikelola petani.';

$petani = $petani ?? [];
$komoditas = $komoditas ?? [];

$formatAngka = function ($value) {
    $value = (float) $value;

    if (floor($value) == $value) {
        return number_format($value, 0, ',', '.');
    }

    return number_format($value, 2, ',', '.');
};

$luasLahan = (float) ($petani['luas_lahan'] ?? 0);
$totalLuasTerpakai = (float) ($totalLuasTerpakai ?? 0);
$sisaLahan = (float) ($sisaLahan ?? 0);
$satuanLuas = $petani['satuan_luas'] ?? 'ha';

ob_start();
?>

<div class="space-y-6">

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="rounded-2xl border border-green-100 bg-green-50 px-5 py-4 text-sm font-bold text-green-700">
            <?= htmlspecialchars($_SESSION['success']); ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="rounded-2xl border border-red-100 bg-red-50 px-5 py-4 text-sm font-bold text-red-700">
            <?= htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Statistik -->
    <div class="grid md:grid-cols-3 gap-4">
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
            <p class="text-sm text-gray-500">Total Luas Lahan</p>
            <h2 class="text-3xl font-extrabold text-teal-600 mt-2">
                <?= $formatAngka($luasLahan); ?> <?= htmlspecialchars($satuanLuas); ?>
            </h2>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
            <p class="text-sm text-gray-500">Luas Terpakai</p>
            <h2 class="text-3xl font-extrabold text-emerald-600 mt-2">
                <?= $formatAngka($totalLuasTerpakai); ?> <?= htmlspecialchars($satuanLuas); ?>
            </h2>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
            <p class="text-sm text-gray-500">Komoditas Aktif</p>
            <h2 class="text-3xl font-extrabold text-amber-500 mt-2">
                <?= count($komoditas); ?>
            </h2>
        </div>
    </div>

    <!-- Informasi Lahan -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">
                    Informasi Lahan
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Data utama lahan yang digunakan untuk pengadaan pangan lokal.
                </p>
            </div>

            <span class="inline-flex px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">
                <?= htmlspecialchars($petani['status_lahan'] ?? 'Aktif'); ?>
            </span>
        </div>

        <div class="grid md:grid-cols-2 gap-6 p-6">
            <div>
                <p class="text-sm text-gray-500">Nama Lahan</p>
                <p class="font-bold text-gray-900">
                    <?= htmlspecialchars($petani['nama_lahan'] ?? '-'); ?>
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Pemilik Akun</p>
                <p class="font-bold text-gray-900">
                    <?= htmlspecialchars($_SESSION['username'] ?? '-'); ?>
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Alamat / Lokasi</p>
                <p class="font-bold text-gray-900">
                    <?= htmlspecialchars($petani['alamat_lahan'] ?? '-'); ?>
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Jenis Usaha</p>
                <p class="font-bold text-gray-900">
                    <?= htmlspecialchars($petani['jenis_usaha'] ?? '-'); ?>
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Kapasitas Panen per Bulan</p>
                <p class="font-bold text-gray-900">
                    <?= $formatAngka($petani['kapasitas_panen_bulan'] ?? 0); ?> Kg
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Sisa Lahan Belum Dialokasikan</p>
                <p class="font-bold text-gray-900">
                    <?= $formatAngka($sisaLahan); ?> <?= htmlspecialchars($satuanLuas); ?>
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Nomor Rekening</p>
                <p class="font-bold text-gray-900">
                    <?= htmlspecialchars($petani['no_rekening'] ?? '-'); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Komoditas -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-extrabold text-gray-900">
                Komoditas yang Dibudidayakan
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Estimasi panen mengikuti satuan dari master komoditas.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Komoditas</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Luas Area</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Estimasi Panen</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Catatan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($komoditas)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                Belum ada komoditas yang dibudidayakan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($komoditas as $item): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-900">
                                        <?= htmlspecialchars($item['nama_komoditas'] ?? '-'); ?>
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Satuan panen: <?= htmlspecialchars($item['satuan'] ?? '-'); ?>
                                    </p>
                                </td>

                                <td class="px-6 py-4 text-gray-700 font-medium">
                                    <?= $formatAngka($item['luas_area'] ?? 0); ?> <?= htmlspecialchars($satuanLuas); ?>
                                </td>

                                <td class="px-6 py-4 text-green-600 font-bold">
                                    <?= $formatAngka($item['estimasi_panen'] ?? 0); ?>
                                    <?= htmlspecialchars($item['satuan'] ?? '-'); ?>/bulan
                                </td>

                                <td class="px-6 py-4 text-gray-500">
                                    <?= htmlspecialchars($item['catatan'] ?: '-'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
            <?= nl2br(htmlspecialchars($petani['deskripsi_lahan'] ?: 'Belum ada deskripsi lahan.')); ?>
        </p>
    </div>

</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>