<?php

$pageTitle = 'Riwayat Pendapatan';
$pageSubtitle = 'Riwayat pemasukan dari hasil pengadaan komoditas.';

$riwayat = [
    [
        'tanggal' => '05 Juni 2026',
        'komoditas' => 'Ikan Nila',
        'jumlah' => '40 Kg',
        'pendapatan' => 'Rp 2.800.000',
        'status' => 'Lunas'
    ],
    [
        'tanggal' => '10 Juni 2026',
        'komoditas' => 'Telur Ayam',
        'jumlah' => '150 Butir',
        'pendapatan' => 'Rp 1.750.000',
        'status' => 'Lunas'
    ],
    [
        'tanggal' => '13 Juni 2026',
        'komoditas' => 'Sayur Hijau',
        'jumlah' => '25 Kg',
        'pendapatan' => 'Rp 1.250.000',
        'status' => 'Diproses'
    ],
];

ob_start();
?>

<div class="space-y-6">

    <!-- Summary -->
    <div class="grid md:grid-cols-3 gap-4">
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
            <p class="text-sm text-gray-500">Total Pendapatan</p>
            <h2 class="text-3xl font-extrabold text-green-600 mt-2">
                Rp 5.800.000
            </h2>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
            <p class="text-sm text-gray-500">Transaksi</p>
            <h2 class="text-3xl font-extrabold text-teal-600 mt-2">
                3
            </h2>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
            <p class="text-sm text-gray-500">Status Lunas</p>
            <h2 class="text-3xl font-extrabold text-amber-500 mt-2">
                2
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
                    <?php foreach ($riwayat as $item): ?>
                        <tr class="border-t border-gray-100">
                            <td class="px-6 py-4"><?= $item['tanggal']; ?></td>
                            <td class="px-6 py-4"><?= $item['komoditas']; ?></td>
                            <td class="px-6 py-4"><?= $item['jumlah']; ?></td>
                            <td class="px-6 py-4 font-bold text-green-600">
                                <?= $item['pendapatan']; ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold
                                    <?= $item['status'] === 'Lunas'
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-amber-100 text-amber-700'; ?>">
                                    <?= $item['status']; ?>
                                </span>
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
?>