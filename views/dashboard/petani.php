<?php
require_once '../views/components/metric-card.php';

$pageTitle = 'Dashboard Petani';
$pageSubtitle = 'Ringkasan kontrak pengadaan, pendapatan, dan profil lahan mitra.';

$data = $data ?? [];

$petani = $data['petani'] ?? [];
$komoditasLahan = $data['komoditas_lahan'] ?? [];
$kontrak = $data['kontrak'] ?? [];
$riwayatEkonomi = $data['riwayat_ekonomi'] ?? [];
$metricData = $data['metrics'] ?? [];

$formatRupiah = function ($value) {
    return 'Rp ' . number_format((float) $value, 0, ',', '.');
};

$formatAngka = function ($value) {
    $value = (float) $value;

    if (floor($value) == $value) {
        return number_format($value, 0, ',', '.');
    }

    return number_format($value, 2, ',', '.');
};

$komoditasNames = array_map(function ($item) {
    return $item['nama_komoditas'] ?? '-';
}, $komoditasLahan);

$ringkasanKomoditas = !empty($komoditasNames)
    ? implode(', ', array_slice($komoditasNames, 0, 3))
    : 'Belum ada komoditas';

if (count($komoditasNames) > 3) {
    $ringkasanKomoditas .= ' +' . (count($komoditasNames) - 3) . ' lainnya';
}

$totalEstimasiPanen = 0;
$totalLuasArea = 0;
$estimasiPanenBySatuan = [];

foreach ($komoditasLahan as $item) {
    $estimasiPanen = (float) ($item['estimasi_panen'] ?? 0);
    $luasArea = (float) ($item['luas_area'] ?? 0);
    $satuanPanen = $item['satuan'] ?? '-';

    $totalEstimasiPanen += $estimasiPanen;
    $totalLuasArea += $luasArea;

    if (!isset($estimasiPanenBySatuan[$satuanPanen])) {
        $estimasiPanenBySatuan[$satuanPanen] = 0;
    }

    $estimasiPanenBySatuan[$satuanPanen] += $estimasiPanen;
}

$metrics = [
    [
        'title' => 'Kontrak Aktif',
        'value' => $metricData['pengadaan_aktif'] ?? 0,
        'caption' => 'Belum berstatus lunas',
        'icon' => '<i class="fa-solid fa-file-signature"></i>',
        'tone' => 'teal',
    ],
    [
        'title' => 'Pendapatan Masuk',
        'value' => $formatRupiah($metricData['total_pendapatan'] ?? 0),
        'caption' => 'Dari kontrak lunas',
        'icon' => '<i class="fa-solid fa-wallet"></i>',
        'tone' => 'green',
    ],
    [
        'title' => 'Menunggu Dibayar',
        'value' => $formatRupiah($metricData['total_pending'] ?? 0),
        'caption' => 'Kontrak masih pending',
        'icon' => '<i class="fa-solid fa-clock"></i>',
        'tone' => 'amber',
    ],
    [
        'title' => 'Kapasitas Panen',
        'value' => $formatAngka($metricData['kapasitas_panen'] ?? 0) . ' kg',
        'caption' => 'Estimasi per bulan',
        'icon' => '<i class="fa-solid fa-seedling"></i>',
        'tone' => 'blue',
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
    <!-- Profil Mitra -->
    <div class="xl:col-span-1 bg-white rounded-3xl border border-gray-100 shadow-sm p-6 flex flex-col">
        <div class="flex items-start justify-between gap-4 mb-5">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">
                    Profil Mitra
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Data utama lahan petani.
                </p>
            </div>

            <span class="px-3 py-1 rounded-full text-xs font-bold <?= ($petani['status_lahan'] ?? '') === 'Aktif' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600'; ?>">
                <?= htmlspecialchars($petani['status_lahan'] ?? 'Belum Lengkap'); ?>
            </span>
        </div>

        <div class="flex flex-1 flex-col gap-4">
            <div class="rounded-2xl bg-teal-50 p-4">
                <p class="text-sm text-teal-700/80">Nama Lahan</p>
                <p class="text-xl font-extrabold text-teal-700 mt-1">
                    <?= htmlspecialchars($petani['nama_lahan'] ?? '-'); ?>
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-2xl bg-green-50 p-4">
                    <p class="text-sm text-green-700/80">Luas Lahan</p>
                    <p class="text-xl font-extrabold text-green-700 mt-1">
                        <?= $formatAngka($petani['luas_lahan'] ?? 0); ?>
                        <?= htmlspecialchars($petani['satuan_luas'] ?? 'ha'); ?>
                    </p>
                </div>

                <div class="rounded-2xl bg-amber-50 p-4">
                    <p class="text-sm text-amber-700/80">Jenis Usaha</p>
                    <p class="text-xl font-extrabold text-amber-700 mt-1">
                        <?= htmlspecialchars($petani['jenis_usaha'] ?? '-'); ?>
                    </p>
                </div>
            </div>

            <div class="flex-1 rounded-2xl bg-gray-50 p-4">
                <p class="text-sm text-gray-500">Alamat Lahan</p>
                <p class="font-bold text-gray-800 mt-1">
                    <?= htmlspecialchars($petani['alamat_lahan'] ?? '-'); ?>
                </p>
            </div>
        </div>

        <a href="/master/petani/profil-lahan"
            class="mt-5 inline-flex w-full items-center justify-center rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-700">
            Lihat Profil Lahan
        </a>
    </div>

    <!-- Komoditas Lahan -->
    <div class="xl:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">
                    Komoditas yang Dikelola
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Ringkasan komoditas, luas area, dan estimasi panen.
                </p>
            </div>

            <span class="px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-bold">
                <?= count($komoditasLahan); ?> Komoditas
            </span>
        </div>

        <div class="grid sm:grid-cols-3 gap-4 p-6 border-b border-gray-100">
            <div class="rounded-2xl bg-teal-50 p-4">
                <p class="text-sm text-teal-700/80">Jenis Komoditas</p>
                <p class="text-lg font-extrabold text-teal-700 mt-1">
                    <?= count($komoditasLahan); ?>
                </p>
            </div>

            <div class="rounded-2xl bg-green-50 p-4">
                <p class="text-sm text-green-700/80">Total Area Terpakai</p>
                <p class="text-lg font-extrabold text-green-700 mt-1">
                    <?= $formatAngka($totalLuasArea); ?>
                    <?= htmlspecialchars($petani['satuan_luas'] ?? 'ha'); ?>
                </p>
            </div>

            <div class="rounded-2xl bg-amber-50 p-4">
                <p class="text-sm text-amber-700/80">Estimasi Panen</p>

                <?php if (empty($estimasiPanenBySatuan)): ?>
                    <p class="text-lg font-extrabold text-amber-700 mt-1">
                        0 / bulan
                    </p>
                <?php else: ?>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <?php foreach ($estimasiPanenBySatuan as $satuan => $total): ?>
                            <span class="inline-flex rounded-full bg-white px-3 py-1 text-sm font-extrabold text-amber-700">
                                <?= $formatAngka($total); ?> <?= htmlspecialchars($satuan); ?>/bulan
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Komoditas</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Luas Area</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Estimasi Panen</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Catatan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($komoditasLahan)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                Belum ada komoditas lahan.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($komoditasLahan as $item): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-bold text-gray-900">
                                <?= htmlspecialchars($item['nama_komoditas'] ?? '-'); ?>
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                <?= $formatAngka($item['luas_area'] ?? 0); ?>
                                <?= htmlspecialchars($petani['satuan_luas'] ?? 'ha'); ?>
                            </td>

                            <td class="px-6 py-4 font-bold text-green-600">
                                <?= $formatAngka($item['estimasi_panen'] ?? 0); ?>
                                <?= htmlspecialchars($item['satuan'] ?? '-'); ?>/bulan
                            </td>

                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($item['catatan'] ?: '-'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="grid xl:grid-cols-2 gap-6 mt-8">
    <!-- Kontrak Pengadaan -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">
                    Kontrak Pengadaan Saya
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Daftar kontrak pasokan yang sedang atau sudah dikerjakan.
                </p>
            </div>

            <a href="/transaksi/pengadaan"
                class="hidden sm:inline-flex rounded-xl bg-teal-50 px-3 py-2 text-xs font-bold text-teal-700 hover:bg-teal-100">
                Lihat Semua
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Kontrak</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Komoditas</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Nilai</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($kontrak)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                Belum ada kontrak pengadaan.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($kontrak as $row): ?>
                        <?php
                        $details = [];

                        if (!empty($row['detail_komoditas'])) {
                            foreach (explode(';;', $row['detail_komoditas']) as $detailRow) {
                                $parts = explode('||', $detailRow);

                                $details[] = [
                                    'nama' => $parts[0] ?? '-',
                                    'jumlah' => (float) ($parts[1] ?? 0),
                                    'satuan' => $parts[2] ?? '-',
                                ];
                            }
                        }

                        $isLunas = ($row['status_bayar'] ?? '') === 'Lunas';
                        $badgeClass = $isLunas ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700';
                        ?>

                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <p class="font-mono font-bold text-gray-900">
                                    <?= htmlspecialchars($row['no_kontrak'] ?? '-'); ?>
                                </p>
                                <p class="text-xs text-gray-500">
                                    <?= !empty($row['tgl_pengadaan'])
                                        ? date('d M Y', strtotime($row['tgl_pengadaan']))
                                        : '-'; ?>
                                </p>
                                <p class="text-xs text-gray-400">
                                    <?= htmlspecialchars($row['nama_gudang'] ?? '-'); ?>
                                </p>
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                <div class="space-y-1">
                                    <?php foreach ($details as $detail): ?>
                                        <p>
                                            <?= htmlspecialchars($detail['nama']); ?>
                                            -
                                            <?= $formatAngka($detail['jumlah']); ?>
                                            <?= htmlspecialchars($detail['satuan']); ?>
                                        </p>
                                    <?php endforeach; ?>
                                </div>
                            </td>

                            <td class="px-6 py-4 font-bold text-gray-900">
                                <?= $formatRupiah($row['total_nilai'] ?? 0); ?>
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $badgeClass; ?>">
                                    <?= htmlspecialchars($row['status_bayar'] ?? '-'); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Riwayat Pembayaran -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">
                    Riwayat Pembayaran
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Status nilai ekonomi dari kontrak pengadaan.
                </p>
            </div>

            <a href="/master/petani/riwayat-ekonomi"
                class="hidden sm:inline-flex rounded-xl bg-green-50 px-3 py-2 text-xs font-bold text-green-700 hover:bg-green-100">
                Detail
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Kontrak</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Gudang</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Nominal</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($riwayatEkonomi)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                Belum ada riwayat pembayaran.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($riwayatEkonomi as $row): ?>
                        <?php
                        $isLunas = ($row['status_bayar'] ?? '') === 'Lunas';
                        $badgeClass = $isLunas ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700';
                        ?>

                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-mono font-bold text-gray-900">
                                <?= htmlspecialchars($row['no_kontrak'] ?? '-'); ?>
                            </td>

                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($row['nama_gudang'] ?? '-'); ?>
                            </td>

                            <td class="px-6 py-4 font-bold <?= $isLunas ? 'text-green-600' : 'text-amber-600'; ?>">
                                <?= $formatRupiah($row['total_nilai'] ?? 0); ?>
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $badgeClass; ?>">
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

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>