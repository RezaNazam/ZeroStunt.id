<?php
$pageTitle = 'Laporan Petani';
$pageSubtitle = 'Laporan profil lahan, komoditas, kontrak pengadaan, dan pendapatan.';

$data = $data ?? [];
$petani = $data['petani_info'] ?? [];
$summary = $data['summary'] ?? [];
$komoditasLahan = $data['komoditas_lahan'] ?? [];
$pengadaan = $data['pengadaan'] ?? [];

$startDate = $data['start_date'] ?? date('Y-m-01');
$endDate = $data['end_date'] ?? date('Y-m-d');

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

$getBadgeStatus = function ($status) {
    $status = strtolower(trim($status ?? ''));

    if (str_contains($status, 'lunas') || str_contains($status, 'disetujui')) {
        return 'bg-green-100 text-green-700';
    }

    if (str_contains($status, 'pending') || str_contains($status, 'diproses')) {
        return 'bg-amber-100 text-amber-700';
    }

    return 'bg-gray-100 text-gray-700';
};

ob_start();
?>

<div class="space-y-6">
    <div class="rounded-3xl bg-white border border-gray-100 shadow-sm p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">
                    Laporan Petani
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Periode <?= date('d/m/Y', strtotime($startDate)); ?> - <?= date('d/m/Y', strtotime($endDate)); ?>
                </p>
            </div>

            <a href="/laporan/pdf?start_date=<?= htmlspecialchars($startDate); ?>&end_date=<?= htmlspecialchars($endDate); ?>"
                class="inline-flex items-center justify-center rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white hover:bg-teal-700">
                Download PDF
            </a>
        </div>

        <form method="GET" action="/laporan" class="mt-5 grid gap-4 md:grid-cols-3">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Awal</label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($startDate); ?>"
                    class="w-full rounded-xl border border-gray-200 px-4 py-3">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date" value="<?= htmlspecialchars($endDate); ?>"
                    class="w-full rounded-xl border border-gray-200 px-4 py-3">
            </div>

            <div class="flex items-end">
                <button type="submit"
                    style="background-color:#0f766e;color:#ffffff;height:50px;border-radius:14px;width:100%;font-weight:800;border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;"
                    class="text-sm transition hover:opacity-90">
                    <i class="fa-solid fa-filter mr-2"></i>Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <section class="grid md:grid-cols-5 gap-4">
        <div class="rounded-3xl bg-white border border-gray-100 shadow-sm p-6">
            <p class="text-sm text-gray-500">Total Kontrak</p>
            <p class="text-3xl font-extrabold text-teal-700 mt-2"><?= $summary['total_kontrak'] ?? 0; ?></p>
        </div>

        <div class="rounded-3xl bg-white border border-gray-100 shadow-sm p-6">
            <p class="text-sm text-gray-500">Kontrak Lunas</p>
            <p class="text-3xl font-extrabold text-green-700 mt-2"><?= $summary['total_lunas'] ?? 0; ?></p>
        </div>

        <div class="rounded-3xl bg-white border border-gray-100 shadow-sm p-6">
            <p class="text-sm text-gray-500">Kontrak Pending</p>
            <p class="text-3xl font-extrabold text-amber-600 mt-2"><?= $summary['total_pending'] ?? 0; ?></p>
        </div>

        <div class="rounded-3xl bg-white border border-gray-100 shadow-sm p-6 md:col-span-2">
            <p class="text-sm text-gray-500">Pendapatan Lunas</p>
            <p class="text-3xl font-extrabold text-green-700 mt-2">
                <?= $formatRupiah($summary['pendapatan_lunas'] ?? 0); ?>
            </p>
        </div>
    </section>

    <div class="rounded-3xl bg-white border border-gray-100 shadow-sm p-6">
        <h2 class="text-xl font-extrabold text-gray-900 mb-4">
            Profil Lahan
        </h2>

        <?php if (empty($petani)): ?>
            <p class="text-gray-500">Profil petani belum tersedia.</p>
        <?php else: ?>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-2xl bg-teal-50 p-4">
                    <p class="text-sm text-teal-700/80">Nama Lahan</p>
                    <p class="text-xl font-extrabold text-teal-700 mt-1">
                        <?= htmlspecialchars($petani['nama_lahan'] ?? '-'); ?>
                    </p>
                </div>

                <div class="rounded-2xl bg-green-50 p-4">
                    <p class="text-sm text-green-700/80">Kapasitas Panen / Bulan</p>
                    <p class="text-xl font-extrabold text-green-700 mt-1">
                        <?= $formatAngka($petani['kapasitas_panen_bulan'] ?? 0); ?> Kg
                    </p>
                </div>

                <div class="rounded-2xl bg-amber-50 p-4">
                    <p class="text-sm text-amber-700/80">Jenis Usaha</p>
                    <p class="text-xl font-extrabold text-amber-700 mt-1">
                        <?= htmlspecialchars($petani['jenis_usaha'] ?? '-'); ?>
                    </p>
                </div>

                <div class="rounded-2xl bg-gray-50 p-4">
                    <p class="text-sm text-gray-500">Alamat Lahan</p>
                    <p class="font-bold text-gray-800 mt-1">
                        <?= htmlspecialchars($petani['alamat_lahan'] ?? '-'); ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="rounded-3xl bg-white border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-extrabold text-gray-900">Komoditas Lahan</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4">Komoditas</th>
                        <th class="text-left px-6 py-4">Luas Area</th>
                        <th class="text-left px-6 py-4">Estimasi Panen</th>
                        <th class="text-left px-6 py-4">Catatan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($komoditasLahan)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada komoditas lahan.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($komoditasLahan as $row): ?>
                        <tr>
                            <td class="px-6 py-4 font-bold text-gray-900"><?= htmlspecialchars($row['nama_komoditas'] ?? '-'); ?></td>
                            <td class="px-6 py-4"><?= $formatAngka($row['luas_area'] ?? 0); ?> <?= htmlspecialchars($petani['satuan_luas'] ?? 'ha'); ?></td>
                            <td class="px-6 py-4"><?= $formatAngka($row['estimasi_panen'] ?? 0); ?> <?= htmlspecialchars($row['satuan'] ?? '-'); ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['catatan'] ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-3xl bg-white border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-extrabold text-gray-900">Kontrak Pengadaan</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4">Tanggal</th>
                        <th class="text-left px-6 py-4">No Kontrak</th>
                        <th class="text-left px-6 py-4">Gudang</th>
                        <th class="text-left px-6 py-4">Komoditas</th>
                        <th class="text-left px-6 py-4">Nilai</th>
                        <th class="text-left px-6 py-4">Status Bayar</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($pengadaan)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada pengadaan pada periode ini.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($pengadaan as $row): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <?= !empty($row['tgl_pengadaan']) ? date('d/m/Y', strtotime($row['tgl_pengadaan'])) : '-'; ?>
                            </td>
                            <td class="px-6 py-4 font-mono font-bold"><?= htmlspecialchars($row['no_kontrak'] ?? '-'); ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['nama_gudang'] ?? '-'); ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['detail_komoditas'] ?? '-'); ?></td>
                            <td class="px-6 py-4 font-bold text-green-700"><?= $formatRupiah($row['total_nilai'] ?? 0); ?></td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-bold rounded-full <?= $getBadgeStatus($row['status_bayar'] ?? '-'); ?>">
                                    <?= htmlspecialchars($row['status_bayar'] ?? '-'); ?>
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