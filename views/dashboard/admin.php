<?php
require_once '../views/components/metric-card.php';

$pageTitle = 'Dashboard Admin';
$pageSubtitle = 'Ringkasan pemantauan gizi, stok pangan, dan distribusi wilayah puskesmas.';

$metrics = [
    [
        'title' => 'Total Anak Dipantau',
        'value' => '1.247',
        'caption' => '+24 anak bulan ini',
        'icon' => '👶',
        'tone' => 'teal',
    ],
    [
        'title' => 'Berisiko Stunting',
        'value' => '156',
        'caption' => 'Butuh prioritas penanganan',
        'icon' => '⚠️',
        'tone' => 'red',
    ],
    [
        'title' => 'Stok Gudang Pusat',
        'value' => '145.3 kg',
        'caption' => 'Komoditas siap distribusi',
        'icon' => '📦',
        'tone' => 'amber',
    ],
    [
        'title' => 'Petani Aktif',
        'value' => '47',
        'caption' => 'Mitra pemasok pangan lokal',
        'icon' => '🌾',
        'tone' => 'green',
    ],
];

$recentChecks = [
    ['nama' => 'Aisyah Putri', 'posyandu' => 'Posyandu Melati', 'status' => 'Normal', 'badge' => 'green'],
    ['nama' => 'Raka Pratama', 'posyandu' => 'Posyandu Mawar', 'status' => 'Gizi Kurang', 'badge' => 'amber'],
    ['nama' => 'Nabila Zahra', 'posyandu' => 'Posyandu Kenanga', 'status' => 'Berisiko Stunting', 'badge' => 'red'],
];

$procurements = [
    ['komoditas' => 'Ikan Nila', 'petani' => 'Kelompok Tani Mina Jaya', 'jumlah' => '40 kg', 'status' => 'Diterima'],
    ['komoditas' => 'Telur Ayam', 'petani' => 'Peternak Makmur', 'jumlah' => '300 butir', 'status' => 'Diproses'],
    ['komoditas' => 'Sayur Hijau', 'petani' => 'Tani Sejahtera', 'jumlah' => '25 kg', 'status' => 'Diterima'],
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

<!-- Content Grid -->
<section class="grid xl:grid-cols-3 gap-6">
    <!-- Chart Placeholder -->
    <div class="xl:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">
                    Tren Pemeriksaan Anak
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Contoh visual sebelum disambungkan ke Chart.js.
                </p>
            </div>

            <span class="px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-xs font-bold">
                Bulanan
            </span>
        </div>

        <div class="h-72 flex items-end gap-3">
            <?php foreach ([45, 60, 52, 70, 68, 80, 76, 88, 92, 85, 96, 100] as $index => $height): ?>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full rounded-t-xl bg-teal-500/80 hover:bg-teal-600 transition"
                        style="height: <?= $height * 2; ?>px;">
                    </div>
                    <span class="text-xs text-gray-400">
                        <?= $index + 1; ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Quick Status -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-xl font-extrabold text-gray-900 mb-5">
            Status Prioritas
        </h2>

        <div class="space-y-4">
            <div class="rounded-2xl bg-red-50 p-4">
                <p class="text-2xl font-extrabold text-red-700">37</p>
                <p class="text-sm text-red-700/80 mt-1">Prioritas 1</p>
            </div>

            <div class="rounded-2xl bg-amber-50 p-4">
                <p class="text-2xl font-extrabold text-amber-700">119</p>
                <p class="text-sm text-amber-700/80 mt-1">Prioritas 2</p>
            </div>

            <div class="rounded-2xl bg-green-50 p-4">
                <p class="text-2xl font-extrabold text-green-700">1.091</p>
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
                Data contoh untuk tampilan awal.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Nama</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Posyandu</th>
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
                                <?= htmlspecialchars($row['posyandu']); ?>
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

    <!-- Pengadaan Terbaru -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-extrabold text-gray-900">
                Pengadaan Terbaru
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Data contoh rantai pasok pangan.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Komoditas</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Petani</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Jumlah</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($procurements as $row): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                <?= htmlspecialchars($row['komoditas']); ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($row['petani']); ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($row['jumlah']); ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-xs font-bold">
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