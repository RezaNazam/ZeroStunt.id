<?php
require_once '../views/components/metric-card.php';

$pageTitle = 'Dashboard Kader';
$pageSubtitle = 'Ringkasan pemeriksaan anak, stok posyandu, dan penyerahan paket gizi.';

$metrics = [
    [
        'title' => 'Anak Posyandu',
        'value' => '324',
        'caption' => 'Terdaftar di wilayah ini',
        'icon' => '<i class="fa-solid fa-children"></i>',
        'tone' => 'teal',
    ],
    [
        'title' => 'Berisiko Stunting',
        'value' => '28',
        'caption' => 'Butuh pemantauan khusus',
        'icon' => '<i class="fa-solid fa-triangle-exclamation"></i>',
        'tone' => 'red',
    ],
    [
        'title' => 'Stok Posyandu',
        'value' => '38.5 kg',
        'caption' => 'Komoditas tersedia',
        'icon' => '<i class="fa-solid fa-cubes-stacked"></i>',
        'tone' => 'amber',
    ],
    [
        'title' => 'Pemeriksaan Bulan Ini',
        'value' => '87',
        'caption' => '+12 dari minggu lalu',
        'icon' => '<i class="fa-solid fa-user-doctor"></i>',
        'tone' => 'green',
    ],
];

$recentChecks = [
    ['nama' => 'Aisyah Putri', 'umur' => '18 bulan', 'zscore' => '-1.4 SD', 'status' => 'Normal', 'badge' => 'green'],
    ['nama' => 'Raka Pratama', 'umur' => '22 bulan', 'zscore' => '-2.4 SD', 'status' => 'Gizi Kurang', 'badge' => 'amber'],
    ['nama' => 'Nabila Zahra', 'umur' => '14 bulan', 'zscore' => '-3.2 SD', 'status' => 'Berisiko Stunting', 'badge' => 'red'],
];

$stockItems = [
    ['komoditas' => 'Ikan Nila', 'stok' => '12.5 kg', 'kebutuhan' => '8 kg', 'status' => 'Aman', 'badge' => 'green'],
    ['komoditas' => 'Telur Ayam', 'stok' => '110 butir', 'kebutuhan' => '90 butir', 'status' => 'Aman', 'badge' => 'green'],
    ['komoditas' => 'Sayur Hijau', 'stok' => '6 kg', 'kebutuhan' => '10 kg', 'status' => 'Menipis', 'badge' => 'amber'],
];

$deliveries = [
    ['tanggal' => '07 Juni 2025', 'penerima' => 'Aisyah Putri', 'paket' => 'Prioritas 3', 'status' => 'Diserahkan', 'badge' => 'green'],
    ['tanggal' => '07 Juni 2025', 'penerima' => 'Raka Pratama', 'paket' => 'Prioritas 2', 'status' => 'Diserahkan', 'badge' => 'green'],
    ['tanggal' => '08 Juni 2025', 'penerima' => 'Nabila Zahra', 'paket' => 'Prioritas 1', 'status' => 'Menunggu', 'badge' => 'amber'],
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
    <!-- Pemeriksaan Chart Placeholder -->
    <div class="xl:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">
                    Pemeriksaan Mingguan
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Contoh jumlah pemeriksaan anak per minggu.
                </p>
            </div>

            <span class="px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-xs font-bold">
                Bulan Ini
            </span>
        </div>

        <div class="h-72 flex items-end gap-3">
            <?php foreach ([45, 70, 58, 87] as $index => $height): ?>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full rounded-t-xl bg-teal-500/80 hover:bg-teal-600 transition"
                        style="height: <?= $height * 2; ?>px;">
                    </div>
                    <span class="text-xs text-gray-400">
                        M<?= $index + 1; ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Prioritas Hari Ini -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-xl font-extrabold text-gray-900 mb-5">
            Prioritas Hari Ini
        </h2>

        <div class="space-y-4">
            <div class="rounded-2xl bg-red-50 p-4">
                <p class="text-2xl font-extrabold text-red-700">7</p>
                <p class="text-sm text-red-700/80 mt-1">Anak Prioritas 1</p>
            </div>

            <div class="rounded-2xl bg-amber-50 p-4">
                <p class="text-2xl font-extrabold text-amber-700">21</p>
                <p class="text-sm text-amber-700/80 mt-1">Anak Prioritas 2</p>
            </div>

            <div class="rounded-2xl bg-green-50 p-4">
                <p class="text-2xl font-extrabold text-green-700">296</p>
                <p class="text-sm text-green-700/80 mt-1">Normal / Monitoring</p>
            </div>
        </div>
    </div>
</section>

<!-- Tables -->
<section class="grid xl:grid-cols-2 gap-6 mt-8">
    <!-- Pemeriksaan Terbaru -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-extrabold text-gray-900">
                Pemeriksaan Terbaru
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Data anak yang baru diperiksa.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Nama</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Umur</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Z-Score</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($recentChecks as $row): ?>
                        <?php
                        $badgeClass = [
                            'green' => 'bg-green-50 text-green-700',
                            'amber' => 'bg-amber-50 text-amber-700',
                            'red' => 'bg-red-50 text-red-700',
                        ][$row['badge']];
                        ?>

                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                <?= htmlspecialchars($row['nama']); ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($row['umur']); ?>
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

    <!-- Stok Posyandu -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-extrabold text-gray-900">
                Stok Posyandu
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Stok pangan yang tersedia untuk distribusi.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Komoditas</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Stok</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Kebutuhan</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($stockItems as $row): ?>
                        <?php
                        $badgeClass = [
                            'green' => 'bg-green-50 text-green-700',
                            'amber' => 'bg-amber-50 text-amber-700',
                            'red' => 'bg-red-50 text-red-700',
                        ][$row['badge']];
                        ?>

                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                <?= htmlspecialchars($row['komoditas']); ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($row['stok']); ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($row['kebutuhan']); ?>
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

<!-- Penyerahan Paket -->
<section class="mt-8">
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">
                    Penyerahan Paket Gizi
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Daftar paket yang diserahkan atau menunggu penyerahan.
                </p>
            </div>

            <span class="hidden sm:inline-block px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-bold">
                Hari Ini
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Tanggal</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Penerima</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Paket</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($deliveries as $row): ?>
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
                                <?= htmlspecialchars($row['penerima']); ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($row['paket']); ?>
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