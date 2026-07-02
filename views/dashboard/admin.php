<?php
require_once '../views/components/metric-card.php';

$pageTitle = 'Dashboard Admin';
$pageSubtitle = 'Ringkasan pemantauan gizi, stok pangan, dan distribusi wilayah puskesmas.';

$data = $data ?? [];

$metricsData = $data['metrics'] ?? [];
$prioritas = $data['prioritas'] ?? [];
$pemeriksaanBulanan = $data['pemeriksaan_bulanan'] ?? [];
$pemeriksaanTerbaru = $data['pemeriksaan_terbaru'] ?? [];
$pengadaanTerbaru = $data['pengadaan_terbaru'] ?? [];
$stokPusat = $data['stok_pusat'] ?? [];
$penyerahanTerbaru = $data['penyerahan_terbaru'] ?? [];

$formatAngka = function ($value) {
    $value = (float) $value;

    if (floor($value) == $value) {
        return number_format($value, 0, ',', '.');
    }

    return number_format($value, 1, ',', '.');
};

$formatRupiah = function ($value) {
    return 'Rp ' . number_format((float) $value, 0, ',', '.');
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
    $status = strtolower(trim($status ?? ''));

    if (
        str_contains($status, 'lunas') ||
        str_contains($status, 'disetujui') ||
        str_contains($status, 'diterima') ||
        str_contains($status, 'diserahkan')
    ) {
        return 'bg-green-50 text-green-700';
    }

    if (
        str_contains($status, 'pending') ||
        str_contains($status, 'diproses') ||
        str_contains($status, 'dikirim')
    ) {
        return 'bg-amber-50 text-amber-700';
    }

    if (
        str_contains($status, 'batal') ||
        str_contains($status, 'tolak')
    ) {
        return 'bg-red-50 text-red-700';
    }

    return 'bg-gray-100 text-gray-700';
};

$maxPemeriksaan = 1;

foreach ($pemeriksaanBulanan as $item) {
    $maxPemeriksaan = max($maxPemeriksaan, (int) ($item['total'] ?? 0));
}

$metrics = [
    [
        'title' => 'Total Anak Dipantau',
        'value' => $metricsData['total_anak'] ?? 0,
        'caption' => 'Anak aktif dalam sistem',
        'icon' => '<i class="fa-solid fa-baby"></i>',
        'tone' => 'teal',
    ],
    [
        'title' => 'Anak Prioritas 1',
        'value' => $metricsData['prioritas_1'] ?? 0,
        'caption' => 'Butuh perhatian utama',
        'icon' => '<i class="fa-solid fa-triangle-exclamation"></i>',
        'tone' => 'red',
    ],
    [
        'title' => 'Jenis Stok Pusat',
        'value' => $metricsData['jenis_stok_pusat'] ?? 0,
        'caption' => 'Komoditas tersedia di gudang pusat',
        'icon' => '<i class="fa-solid fa-box-archive"></i>',
        'tone' => 'amber',
    ],
    [
        'title' => 'Petani Aktif',
        'value' => $metricsData['petani_aktif'] ?? 0,
        'caption' => 'Mitra pemasok pangan lokal',
        'icon' => '<i class="fa-solid fa-wheat-awn"></i>',
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
    <div class="xl:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">
                    Tren Pemeriksaan Anak
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Jumlah pemeriksaan anak dalam 6 bulan terakhir.
                </p>
            </div>

            <span class="px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-xs font-bold">
                6 Bulan
            </span>
        </div>

        <div class="h-72 flex items-end gap-3">
            <?php if (empty($pemeriksaanBulanan)): ?>
                <div class="w-full h-full flex items-center justify-center text-gray-400">
                    Belum ada data pemeriksaan.
                </div>
            <?php endif; ?>

            <?php foreach ($pemeriksaanBulanan as $item): ?>
                <?php
                $total = (int) ($item['total'] ?? 0);
                $height = max(12, ($total / $maxPemeriksaan) * 220);
                ?>

                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full rounded-t-xl bg-teal-500/80 hover:bg-teal-600 transition"
                        title="<?= $total; ?> pemeriksaan"
                        style="height: <?= $height; ?>px;">
                    </div>
                    <span class="text-xs text-gray-400">
                        <?= htmlspecialchars($item['bulan'] ?? '-'); ?>
                    </span>
                    <span class="text-xs font-bold text-gray-600">
                        <?= $total; ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

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
                <p class="text-xs text-red-700/70 mt-1">Butuh perhatian utama.</p>
            </div>

            <div class="rounded-2xl bg-amber-50 p-4">
                <p class="text-sm text-amber-700/80">Prioritas 2</p>
                <p class="text-3xl font-extrabold text-amber-700 mt-1">
                    <?= $prioritas['prioritas_2'] ?? 0; ?>
                </p>
                <p class="text-xs text-amber-700/70 mt-1">Perlu pemantauan gizi.</p>
            </div>

            <div class="rounded-2xl bg-green-50 p-4">
                <p class="text-sm text-green-700/80">Prioritas 3</p>
                <p class="text-3xl font-extrabold text-green-700 mt-1">
                    <?= $prioritas['prioritas_3'] ?? 0; ?>
                </p>
                <p class="text-xs text-green-700/70 mt-1">Monitoring rutin.</p>
            </div>
        </div>
    </div>
</section>

<section class="grid xl:grid-cols-2 gap-6 mt-8">
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">
                    Pemeriksaan Terbaru
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Data pemeriksaan terbaru dari seluruh posyandu.
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
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Posyandu</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Tanggal</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Prioritas</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($pemeriksaanTerbaru)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                Belum ada pemeriksaan.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($pemeriksaanTerbaru as $row): ?>
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
                                <?= htmlspecialchars($row['nama_posyandu'] ?? '-'); ?>
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                <?= !empty($row['tanggal_pemeriksaan'])
                                    ? date('d M Y', strtotime($row['tanggal_pemeriksaan']))
                                    : '-'; ?>
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $getBadgePrioritas($row['skala_prioritas'] ?? 3); ?>">
                                    Prioritas <?= htmlspecialchars($row['skala_prioritas'] ?? '-'); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">
                    Pengadaan Terbaru
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Kontrak pasokan pangan dari mitra petani.
                </p>
            </div>

            <a href="/transaksi/pengadaan"
                class="hidden sm:inline-flex rounded-xl bg-green-50 px-3 py-2 text-xs font-bold text-green-700 hover:bg-green-100">
                Lihat Semua
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Kontrak</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Petani</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Nilai</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($pengadaanTerbaru)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                Belum ada pengadaan.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($pengadaanTerbaru as $row): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <p class="font-mono font-bold text-gray-900">
                                    <?= htmlspecialchars($row['no_kontrak'] ?? '-'); ?>
                                </p>
                                <p class="text-xs text-gray-500">
                                    <?= htmlspecialchars($row['nama_gudang'] ?? '-'); ?>
                                </p>
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                <?= htmlspecialchars($row['nama_petani'] ?? '-'); ?>
                            </td>

                            <td class="px-6 py-4 font-bold text-green-600">
                                <?= $formatRupiah($row['total_nilai'] ?? 0); ?>
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $getBadgeStatus($row['status_bayar'] ?? '-'); ?>">
                                    <?= htmlspecialchars($row['status_bayar'] ?? '-'); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="grid xl:grid-cols-2 gap-6 mt-8">
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">
                    Stok Gudang Pusat
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Ringkasan stok komoditas di gudang pusat.
                </p>
            </div>

            <a href="/stok/pusat"
                class="hidden sm:inline-flex rounded-xl bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 hover:bg-amber-100">
                Detail
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Komoditas</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Gudang</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Stok</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($stokPusat)): ?>
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-gray-500">
                                Belum ada stok gudang pusat.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($stokPusat as $row): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-bold text-gray-900">
                                <?= htmlspecialchars($row['nama_komoditas'] ?? '-'); ?>
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                <?= htmlspecialchars($row['nama_gudang'] ?? '-'); ?>
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

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">
                    Penyerahan Paket Terbaru
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Paket bantuan gizi yang dibuat oleh posyandu.
                </p>
            </div>

            <a href="/transaksi/penyerahan"
                class="hidden sm:inline-flex rounded-xl bg-blue-50 px-3 py-2 text-xs font-bold text-blue-700 hover:bg-blue-100">
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
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $getBadgeStatus($row['status_penyerahan'] ?? '-'); ?>">
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