<?php
require_once '../views/components/metric-card.php';

$pageTitle = 'Dashboard Ibu';
$pageSubtitle = 'Ringkasan data anak, status gizi, dan riwayat bantuan.';

$metrics = [
    [
        'title' => 'Nama Anak',
        'value' => 'Aisyah',
        'caption' => 'Usia 18 bulan',
        'icon' => '👶',
        'tone' => 'teal',
    ],
    [
        'title' => 'Z-Score Terakhir',
        'value' => '-1.4 SD',
        'caption' => 'Status masih normal',
        'icon' => '📈',
        'tone' => 'green',
    ],
    [
        'title' => 'Status Prioritas',
        'value' => 'Normal',
        'caption' => 'Monitoring rutin',
        'icon' => '✅',
        'tone' => 'blue',
    ],
    [
        'title' => 'Bantuan Diterima',
        'value' => '3x',
        'caption' => 'Dalam 6 bulan terakhir',
        'icon' => '🎁',
        'tone' => 'amber',
    ],
];

$growthHistory = [
    ['bulan' => 'Januari', 'berat' => '8.1 kg', 'tinggi' => '72 cm', 'zscore' => '-1.8 SD', 'status' => 'Normal', 'badge' => 'green'],
    ['bulan' => 'Februari', 'berat' => '8.3 kg', 'tinggi' => '73 cm', 'zscore' => '-1.7 SD', 'status' => 'Normal', 'badge' => 'green'],
    ['bulan' => 'Maret', 'berat' => '8.5 kg', 'tinggi' => '74 cm', 'zscore' => '-1.6 SD', 'status' => 'Normal', 'badge' => 'green'],
    ['bulan' => 'April', 'berat' => '8.7 kg', 'tinggi' => '75 cm', 'zscore' => '-1.4 SD', 'status' => 'Normal', 'badge' => 'green'],
];

$aidHistory = [
    ['tanggal' => '05 Juni 2025', 'paket' => 'Paket Prioritas 3', 'isi' => '0.5 kg sayur + 3 telur', 'status' => 'Diterima', 'badge' => 'green'],
    ['tanggal' => '05 Mei 2025', 'paket' => 'Paket Prioritas 3', 'isi' => '0.5 kg sayur + 3 telur', 'status' => 'Diterima', 'badge' => 'green'],
    ['tanggal' => '05 April 2025', 'paket' => 'Paket Prioritas 3', 'isi' => '0.5 kg sayur + 3 telur', 'status' => 'Diterima', 'badge' => 'green'],
];

ob_start();
?>

<!-- Metric Cards -->
<section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <?php foreach ($metrics as $metric): ?>
        <?php renderMetricCard(
            $metric['title'],
            $metric['value'],
            $metric['caption'],
            $metric['icon'],
            $metric['tone']
        ); ?>
    <?php endforeach; ?>
</section>

<!-- Main Grid -->
<section class="grid xl:grid-cols-3 gap-6">
    <!-- Growth Chart Placeholder -->
    <div class="xl:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">
                    Perkembangan Anak
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Contoh tren berat badan anak per bulan.
                </p>
            </div>

            <span class="px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-xs font-bold">
                4 Bulan
            </span>
        </div>

        <div class="h-72 flex items-end gap-3">
            <?php foreach ([58, 63, 69, 75] as $index => $height): ?>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full rounded-t-xl bg-teal-500/80 hover:bg-teal-600 transition"
                        style="height: <?= $height * 2; ?>px;">
                    </div>
                    <span class="text-xs text-gray-400">
                        <?= ['Jan', 'Feb', 'Mar', 'Apr'][$index]; ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Child Summary -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-xl font-extrabold text-gray-900 mb-5">
            Ringkasan Anak
        </h2>

        <div class="space-y-4">
            <div class="rounded-2xl bg-teal-50 p-4">
                <p class="text-sm text-teal-700/80">Nama Anak</p>
                <p class="text-2xl font-extrabold text-teal-700 mt-1">Aisyah</p>
            </div>

            <div class="rounded-2xl bg-green-50 p-4">
                <p class="text-sm text-green-700/80">Status Gizi</p>
                <p class="text-2xl font-extrabold text-green-700 mt-1">Normal</p>
            </div>

            <div class="rounded-2xl bg-blue-50 p-4">
                <p class="text-sm text-blue-700/80">Posyandu</p>
                <p class="text-2xl font-extrabold text-blue-700 mt-1">Melati</p>
            </div>
        </div>
    </div>
</section>

<!-- Tables -->
<section class="grid xl:grid-cols-2 gap-6 mt-8">
    <!-- Riwayat Pemeriksaan -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-extrabold text-gray-900">
                Riwayat Pemeriksaan
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Data pemeriksaan anak berdasarkan kunjungan posyandu.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Bulan</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Berat</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Tinggi</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Z-Score</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($growthHistory as $row): ?>
                        <?php
                        $badgeClass = [
                            'green' => 'bg-green-50 text-green-700',
                            'amber' => 'bg-amber-50 text-amber-700',
                            'red' => 'bg-red-50 text-red-700',
                        ][$row['badge']];
                        ?>

                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                <?= htmlspecialchars($row['bulan']); ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($row['berat']); ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($row['tinggi']); ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($row['zscore']); ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $badgeClass; ?>">
                                    <?= htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Histori Bantuan -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-extrabold text-gray-900">
                Histori Bantuan
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Riwayat paket gizi yang sudah diterima.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Tanggal</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Paket</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Isi</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($aidHistory as $row): ?>
                        <?php
                        $badgeClass = [
                            'green' => 'bg-green-50 text-green-700',
                            'amber' => 'bg-amber-50 text-amber-700',
                            'red' => 'bg-red-50 text-red-700',
                        ][$row['badge']];
                        ?>

                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                <?= htmlspecialchars($row['tanggal']); ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($row['paket']); ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($row['isi']); ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $badgeClass; ?>">
                                    <?= htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';