<?php
$pageTitle = 'Laporan Kader';
$pageSubtitle = 'Laporan posyandu, pemeriksaan anak, stok, distribusi, dan penyerahan bantuan.';

$data = $data ?? [];
$kader = $data['kader_info'] ?? [];
$summary = $data['summary'] ?? [];
$pemeriksaan = $data['pemeriksaan'] ?? [];
$stok = $data['stok'] ?? [];
$distribusi = $data['distribusi'] ?? [];
$penyerahan = $data['penyerahan'] ?? [];

$startDate = $data['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $data['end_date'] ?? date('Y-m-d');

$formatAngka = function ($value) {
    $value = (float) $value;

    if (floor($value) == $value) {
        return number_format($value, 0, ',', '.');
    }

    return number_format($value, 1, ',', '.');
};

$getBadgeStatus = function ($status) {
    $status = strtolower(trim($status ?? ''));

    if (str_contains($status, 'diterima') || str_contains($status, 'diserahkan') || str_contains($status, 'selesai')) {
        return 'bg-green-100 text-green-700';
    }

    if (str_contains($status, 'dikirim') || str_contains($status, 'diproses') || str_contains($status, 'pending')) {
        return 'bg-amber-100 text-amber-700';
    }

    if (str_contains($status, 'batal') || str_contains($status, 'tolak')) {
        return 'bg-red-100 text-red-700';
    }

    return 'bg-gray-100 text-gray-700';
};

ob_start();
?>

<div class="space-y-6">
    <div class="rounded-3xl bg-white border border-gray-100 shadow-sm p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">Laporan Kader</h1>
                <p class="text-sm text-gray-500 mt-1">
                    <?= htmlspecialchars($kader['nama_gudang'] ?? 'Posyandu'); ?> ·
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

    <section class="grid md:grid-cols-4 gap-4">
        <div class="rounded-3xl bg-white border border-gray-100 shadow-sm p-6">
            <p class="text-sm text-gray-500">Total Anak</p>
            <p class="text-3xl font-extrabold text-teal-700 mt-2"><?= $summary['total_anak'] ?? 0; ?></p>
        </div>

        <div class="rounded-3xl bg-white border border-gray-100 shadow-sm p-6">
            <p class="text-sm text-gray-500">Pemeriksaan</p>
            <p class="text-3xl font-extrabold text-green-700 mt-2"><?= $summary['total_pemeriksaan'] ?? 0; ?></p>
        </div>

        <div class="rounded-3xl bg-white border border-gray-100 shadow-sm p-6">
            <p class="text-sm text-gray-500">Penyerahan</p>
            <p class="text-3xl font-extrabold text-amber-600 mt-2"><?= $summary['total_penyerahan'] ?? 0; ?></p>
        </div>

        <div class="rounded-3xl bg-white border border-gray-100 shadow-sm p-6">
            <p class="text-sm text-gray-500">Jenis Stok</p>
            <p class="text-3xl font-extrabold text-blue-700 mt-2"><?= $summary['jenis_stok'] ?? 0; ?></p>
        </div>
    </section>

    <section class="grid md:grid-cols-3 gap-4">
        <div class="rounded-3xl bg-red-50 border border-red-100 p-6">
            <p class="text-sm text-red-700">Prioritas 1</p>
            <p class="text-3xl font-extrabold text-red-700 mt-2"><?= $summary['prioritas_1'] ?? 0; ?></p>
        </div>

        <div class="rounded-3xl bg-amber-50 border border-amber-100 p-6">
            <p class="text-sm text-amber-700">Prioritas 2</p>
            <p class="text-3xl font-extrabold text-amber-700 mt-2"><?= $summary['prioritas_2'] ?? 0; ?></p>
        </div>

        <div class="rounded-3xl bg-green-50 border border-green-100 p-6">
            <p class="text-sm text-green-700">Prioritas 3</p>
            <p class="text-3xl font-extrabold text-green-700 mt-2"><?= $summary['prioritas_3'] ?? 0; ?></p>
        </div>
    </section>

    <div class="rounded-3xl bg-white border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-extrabold text-gray-900">Riwayat Pemeriksaan</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4">Tanggal</th>
                        <th class="text-left px-6 py-4">Anak</th>
                        <th class="text-left px-6 py-4">Ibu</th>
                        <th class="text-left px-6 py-4">BB / TB</th>
                        <th class="text-left px-6 py-4">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($pemeriksaan)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                Belum ada pemeriksaan pada periode ini.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($pemeriksaan as $row): ?>
                        <tr>
                            <td class="px-6 py-4"><?= date('d/m/Y', strtotime($row['tanggal_pemeriksaan'])); ?></td>
                            <td class="px-6 py-4 font-bold"><?= htmlspecialchars($row['nama_anak'] ?? '-'); ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['nama_ibu'] ?? '-'); ?></td>
                            <td class="px-6 py-4">
                                <?= $formatAngka($row['berat_badan'] ?? 0); ?> Kg /
                                <?= $formatAngka($row['tinggi_badan'] ?? 0); ?> Cm
                            </td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['status_gizi'] ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-3xl bg-white border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-extrabold text-gray-900">Stok Posyandu</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4">Komoditas</th>
                        <th class="text-left px-6 py-4">Masuk</th>
                        <th class="text-left px-6 py-4">Keluar</th>
                        <th class="text-left px-6 py-4">Stok Saat Ini</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($stok)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada stok.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($stok as $row): ?>
                        <tr>
                            <td class="px-6 py-4 font-bold"><?= htmlspecialchars($row['nama_komoditas'] ?? '-'); ?></td>
                            <td class="px-6 py-4"><?= $formatAngka($row['qty_in'] ?? 0); ?> <?= htmlspecialchars($row['satuan'] ?? '-'); ?></td>
                            <td class="px-6 py-4"><?= $formatAngka($row['qty_out'] ?? 0); ?> <?= htmlspecialchars($row['satuan'] ?? '-'); ?></td>
                            <td class="px-6 py-4 font-bold text-teal-700"><?= $formatAngka($row['qty_current'] ?? 0); ?> <?= htmlspecialchars($row['satuan'] ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-3xl bg-white border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-extrabold text-gray-900">Distribusi Masuk</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4">Tanggal</th>
                        <th class="text-left px-6 py-4">No Distribusi</th>
                        <th class="text-left px-6 py-4">Asal</th>
                        <th class="text-left px-6 py-4">Isi</th>
                        <th class="text-left px-6 py-4">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($distribusi)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada distribusi pada periode ini.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($distribusi as $row): ?>
                        <tr>
                            <td class="px-6 py-4"><?= date('d/m/Y', strtotime($row['tanggal_distribusi'])); ?></td>
                            <td class="px-6 py-4 font-mono font-bold"><?= htmlspecialchars($row['no_distribusi'] ?? '-'); ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['gudang_asal'] ?? '-'); ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['detail_distribusi'] ?? '-'); ?></td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-bold rounded-full <?= $getBadgeStatus($row['status_distribusi'] ?? '-'); ?>">
                                    <?= htmlspecialchars($row['status_distribusi'] ?? '-'); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-3xl bg-white border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-extrabold text-gray-900">Penyerahan Paket</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4">Tanggal</th>
                        <th class="text-left px-6 py-4">Penerima</th>
                        <th class="text-left px-6 py-4">Paket</th>
                        <th class="text-left px-6 py-4">Isi</th>
                        <th class="text-left px-6 py-4">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($penyerahan)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada penyerahan pada periode ini.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($penyerahan as $row): ?>
                        <tr>
                            <td class="px-6 py-4"><?= date('d/m/Y', strtotime($row['tanggal_penyerahan'])); ?></td>
                            <td class="px-6 py-4">
                                <p class="font-bold"><?= htmlspecialchars($row['nama_anak'] ?? '-'); ?></p>
                                <p class="text-xs text-gray-500">Ibu: <?= htmlspecialchars($row['nama_ibu'] ?? '-'); ?></p>
                            </td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['nama_paket'] ?? '-'); ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['isi_paket'] ?? '-'); ?></td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-bold rounded-full <?= $getBadgeStatus($row['status_penyerahan'] ?? '-'); ?>">
                                    <?= htmlspecialchars($row['status_penyerahan'] ?? '-'); ?>
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