<?php

$pageTitle = 'Riwayat Pendapatan';
$pageSubtitle = 'Riwayat pemasukan dari hasil pengadaan komoditas.';

$riwayat = $riwayat ?? [];

$totalPendapatan = array_sum(array_column($riwayat, 'total_bayar'));
$totalTransaksi = count($riwayat);
$totalLunas = count(array_filter($riwayat, function ($item) {
    return $item['status_bayar'] === 'Lunas';
}));

ob_start();
?>

<div class="space-y-6">

    <!-- Summary -->
    <div class="grid md:grid-cols-3 gap-4">
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
            <p class="text-sm text-gray-500">Total Pendapatan</p>
            <h2 class="text-3xl font-extrabold text-green-600 mt-2">
                <?= 'Rp ' . number_format($totalPendapatan, 0, ',', '.') ?>
            </h2>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
            <p class="text-sm text-gray-500">Transaksi</p>
            <h2 class="text-3xl font-extrabold text-teal-600 mt-2">
                <?= $totalTransaksi ?>
            </h2>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
            <p class="text-sm text-gray-500">Status Lunas</p>
            <h2 class="text-3xl font-extrabold text-amber-500 mt-2">
                <?= $totalLunas ?>
            </h2>
        </div>
    </div>

    <!-- Tabel -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-extrabold text-gray-900">
                Riwayat Pendapatan
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Data pemasukan dari hasil pengadaan komoditas.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left">Tanggal</th>
                        <th class="px-6 py-4 text-left">Komoditas</th>
                        <th class="px-6 py-4 text-left">Jumlah</th>
                        <th class="px-6 py-4 text-left">Pendapatan</th>
                        <th class="px-6 py-4 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($riwayat)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Belum ada riwayat pendapatan.
                        </td>
                    </tr>
                    <?php else: ?>

                    <?php foreach ($riwayat as $item): ?>
                    <tr class="border-t border-gray-100">

                        <td class="px-6 py-4">
                            <?= date('d M Y', strtotime($item['tgl_pengadaan'])) ?>
                        </td>

                        <td class="px-6 py-4">
                            <?= htmlspecialchars($item['nama_komoditas']) ?>
                        </td>

                        <td class="px-6 py-4">
                            <?= number_format($item['jumlah'], 0, ',', '.') ?>
                        </td>

                        <td class="px-6 py-4 font-semibold text-green-600">
                            Rp <?= number_format($item['total_bayar'], 0, ',', '.') ?>
                        </td>

                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                <?= $item['status_bayar'] ?>
                            </span>
                        </td>

                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>