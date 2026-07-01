<?php
require_once '../views/components/metric-card.php';

$pageTitle = 'Dashboard Kader';
$pageSubtitle = 'Ringkasan anak, pemeriksaan, stok posyandu, dan penyerahan paket gizi.';

$data = $data ?? [];

$metricsData = $data['metrics'] ?? [];
$prioritas = $data['prioritas'] ?? [];
$pemeriksaanTerbaru = $data['pemeriksaan_terbaru'] ?? [];
$stokPosyandu = $data['stok_posyandu'] ?? [];
$penyerahanTerbaru = $data['penyerahan_terbaru'] ?? [];
$user = $data['user'] ?? [];

$formatAngka = function ($value) {
    $value = (float) $value;

    if (floor($value) == $value) {
        return number_format($value, 0, ',', '.');
    }

    return number_format($value, 2, ',', '.');
};

$getBadgePrioritas = function ($prioritas) {
    if ((int) $prioritas === 1) {
        return 'bg-red-50 text-red-700';
    }

    if ((int) $prioritas === 2) {
        return 'bg-amber-50 text-amber-700';
    }

    return 'bg-green-50 text-green-700';
};

$getBadgeStatus = function ($status) {
    $status = strtolower($status ?? '');

    if (str_contains($status, 'normal') || str_contains($status, 'diserahkan') || str_contains($status, 'diterima')) {
        return 'bg-green-50 text-green-700';
    }

    if (str_contains($status, 'kurang') || str_contains($status, 'diproses') || str_contains($status, 'pending')) {
        return 'bg-amber-50 text-amber-700';
    }

    if (str_contains($status, 'stunting') || str_contains($status, 'risiko')) {
        return 'bg-red-50 text-red-700';
    }

    return 'bg-gray-100 text-gray-700';
};

$hitungUmur = function ($tglLahir) {
    if (empty($tglLahir)) {
        return '-';
    }

    $lahir = new DateTime($tglLahir);
    $sekarang = new DateTime();
    $diff = $lahir->diff($sekarang);

    if ($diff->y > 0) {
        return $diff->y . ' tahun ' . $diff->m . ' bulan';
    }

    return $diff->m . ' bulan';
};

$metrics = [
    [
        'title' => 'Anak Posyandu',
        'value' => $metricsData['total_anak'] ?? 0,
        'caption' => 'Terdaftar di posyandu saya',
        'icon' => '<i class="fa-solid fa-children"></i>',
        'tone' => 'teal',
    ],
    [
        'title' => 'Anak Prioritas 1',
        'value' => $metricsData['prioritas_1'] ?? 0,
        'caption' => 'Berdasarkan data anak aktif',
        'icon' => '<i class="fa-solid fa-triangle-exclamation"></i>',
        'tone' => 'red',
    ],
    [
        'title' => 'Jenis Stok Tersedia',
        'value' => $metricsData['jenis_stok'] ?? 0,
        'caption' => 'Komoditas di posyandu',
        'icon' => '<i class="fa-solid fa-cubes-stacked"></i>',
        'tone' => 'amber',
    ],
    [
        'title' => 'Pemeriksaan Bulan Ini',
        'value' => $metricsData['pemeriksaan_bulan_ini'] ?? 0,
        'caption' => 'Data pemeriksaan terbaru',
        'icon' => '<i class="fa-solid fa-user-doctor"></i>',
        'tone' => 'green',
    ],
];

ob_start();
?>

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

<section class="grid xl:grid-cols-3 gap-6">
    <!-- Pemeriksaan Terbaru -->
    <div class="xl:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">
                    Pemeriksaan Terbaru
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Anak yang terakhir diperiksa di posyandu ini.
                </p>
            </div>

            <a href="/transaksi/pemeriksaan"
                class="hidden sm:inline-flex rounded-xl bg-teal-50 px-3 py-2 text-xs font-bold text-teal-700 hover:bg-teal-100">
                Lihat Semua
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Anak</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Umur</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Pemeriksaan</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Prioritas</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($pemeriksaanTerbaru)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                Belum ada data pemeriksaan.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($pemeriksaanTerbaru as $row): ?>
                        <?php $badgeClass = $getBadgePrioritas($row['skala_prioritas'] ?? 3); ?>

                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900">
                                    <?= htmlspecialchars($row['nama_anak'] ?? '-'); ?>
                                </p>
                                <p class="text-xs text-gray-500">
                                    Ibu: <?= htmlspecialchars($row['nama_ibu'] ?? '-'); ?>
                                </p>
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                <?= htmlspecialchars($hitungUmur($row['tgl_lahir'] ?? null)); ?>
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                <p>
                                    <?= !empty($row['tanggal_pemeriksaan'])
                                        ? date('d M Y', strtotime($row['tanggal_pemeriksaan']))
                                        : '-'; ?>
                                </p>
                                <p class="text-xs text-gray-500">
                                    BB <?= $formatAngka($row['berat_badan'] ?? 0); ?> kg ·
                                    TB <?= $formatAngka($row['tinggi_badan'] ?? 0); ?> cm
                                </p>
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $badgeClass; ?>">
                                    Prioritas <?= htmlspecialchars($row['skala_prioritas'] ?? '-'); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Prioritas Anak -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-xl font-extrabold text-gray-900 mb-5">
            Distribusi Prioritas Anak
        </h2>

        <div class="space-y-4">
            <div class="rounded-2xl bg-red-50 p-4">
                <p class="text-sm text-red-700/80">Prioritas 1</p>
                <p class="text-3xl font-extrabold text-red-700 mt-1">
                    <?= $prioritas['prioritas_1'] ?? 0; ?>
                </p>
                <p class="text-xs text-red-700/70 mt-1">
                    Paling membutuhkan paket gizi.
                </p>
            </div>

            <div class="rounded-2xl bg-amber-50 p-4">
                <p class="text-sm text-amber-700/80">Prioritas 2</p>
                <p class="text-3xl font-extrabold text-amber-700 mt-1">
                    <?= $prioritas['prioritas_2'] ?? 0; ?>
                </p>
                <p class="text-xs text-amber-700/70 mt-1">
                    Butuh pemantauan lanjutan.
                </p>
            </div>

            <div class="rounded-2xl bg-green-50 p-4">
                <p class="text-sm text-green-700/80">Prioritas 3</p>
                <p class="text-3xl font-extrabold text-green-700 mt-1">
                    <?= $prioritas['prioritas_3'] ?? 0; ?>
                </p>
                <p class="text-xs text-green-700/70 mt-1">
                    Monitoring dan pemenuhan rutin.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="grid xl:grid-cols-2 gap-6 mt-8">
    <!-- Stok Posyandu -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">
                    Stok Posyandu
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Komoditas yang tersedia untuk penyerahan paket.
                </p>
            </div>

            <a href="/stok/posyandu"
                class="hidden sm:inline-flex rounded-xl bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 hover:bg-amber-100">
                Detail
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Komoditas</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Masuk</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Keluar</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Stok</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($stokPosyandu)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                Belum ada stok posyandu.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($stokPosyandu as $row): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-bold text-gray-900">
                                <?= htmlspecialchars($row['nama_komoditas'] ?? '-'); ?>
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                <?= $formatAngka($row['qty_in'] ?? 0); ?>
                                <?= htmlspecialchars($row['satuan'] ?? '-'); ?>
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                <?= $formatAngka($row['qty_out'] ?? 0); ?>
                                <?= htmlspecialchars($row['satuan'] ?? '-'); ?>
                            </td>

                            <td class="px-6 py-4 font-extrabold text-teal-700">
                                <?= $formatAngka($row['qty_current'] ?? 0); ?>
                                <?= htmlspecialchars($row['satuan'] ?? '-'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Penyerahan Terbaru -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">
                    Penyerahan Paket Terbaru
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Riwayat paket gizi yang dibuat dari posyandu ini.
                </p>
            </div>

            <a href="/transaksi/penyerahan"
                class="hidden sm:inline-flex rounded-xl bg-green-50 px-3 py-2 text-xs font-bold text-green-700 hover:bg-green-100">
                Lihat Semua
            </a>
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
                    <?php if (empty($penyerahanTerbaru)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                Belum ada penyerahan paket.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($penyerahanTerbaru as $row): ?>
                        <?php $badgeClass = $getBadgeStatus($row['status_penyerahan'] ?? ''); ?>

                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                <?= !empty($row['tanggal_penyerahan'])
                                    ? date('d M Y', strtotime($row['tanggal_penyerahan']))
                                    : '-'; ?>
                            </td>

                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900">
                                    <?= htmlspecialchars($row['nama_anak'] ?? '-'); ?>
                                </p>
                                <p class="text-xs text-gray-500">
                                    Ibu: <?= htmlspecialchars($row['nama_ibu'] ?? '-'); ?>
                                </p>
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                <?= htmlspecialchars($row['nama_paket'] ?? '-'); ?>
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $badgeClass; ?>">
                                    <?= htmlspecialchars($row['status_penyerahan'] ?? '-'); ?>
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
?>