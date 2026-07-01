<?php
$pageTitle = 'Laporan Ibu';
$pageSubtitle = 'Laporan data anak, pemeriksaan, dan bantuan yang diterima.';

$data = $data ?? [];
$summary = $data['summary'] ?? [];
$anak = $data['anak'] ?? [];
$pemeriksaan = $data['pemeriksaan'] ?? [];
$bantuan = $data['bantuan'] ?? [];

$startDate = $data['start_date'] ?? date('Y-m-01');
$endDate = $data['end_date'] ?? date('Y-m-d');

$formatAngka = function ($value) {
    $value = (float) $value;

    if (floor($value) == $value) {
        return number_format($value, 0, ',', '.');
    }

    return number_format($value, 1, ',', '.');
};

ob_start();
?>

<div class="space-y-6">
    <div class="rounded-3xl bg-white border border-gray-100 shadow-sm p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">
                    Laporan Ibu
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
            <p class="text-sm text-gray-500">Bantuan Diterima</p>
            <p class="text-3xl font-extrabold text-amber-600 mt-2"><?= $summary['total_bantuan'] ?? 0; ?></p>
        </div>

        <div class="rounded-3xl bg-white border border-gray-100 shadow-sm p-6">
            <p class="text-sm text-gray-500">Prioritas 1</p>
            <p class="text-3xl font-extrabold text-red-700 mt-2"><?= $summary['prioritas_1'] ?? 0; ?></p>
        </div>
    </section>

    <div class="rounded-3xl bg-white border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-extrabold text-gray-900">Data Anak</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4">Nama Anak</th>
                        <th class="text-left px-6 py-4">Status Gizi</th>
                        <th class="text-left px-6 py-4">Prioritas</th>
                        <th class="text-left px-6 py-4">Pemeriksaan Terakhir</th>
                        <th class="text-left px-6 py-4">BB / TB</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($anak)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada data anak.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($anak as $row): ?>
                        <tr>
                            <td class="px-6 py-4 font-bold text-gray-900"><?= htmlspecialchars($row['nama_anak'] ?? '-'); ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['st_gizi_skrg'] ?? '-'); ?></td>
                            <td class="px-6 py-4">Prioritas <?= htmlspecialchars($row['skala_prioritas'] ?? '-'); ?></td>
                            <td class="px-6 py-4">
                                <?= !empty($row['tanggal_pemeriksaan_terakhir'])
                                    ? date('d/m/Y', strtotime($row['tanggal_pemeriksaan_terakhir']))
                                    : '-'; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= !empty($row['berat_badan_terakhir'])
                                    ? $formatAngka($row['berat_badan_terakhir']) . ' Kg / ' . $formatAngka($row['tinggi_badan_terakhir']) . ' Cm'
                                    : '-'; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

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
                        <th class="text-left px-6 py-4">Berat</th>
                        <th class="text-left px-6 py-4">Tinggi</th>
                        <th class="text-left px-6 py-4">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($pemeriksaan)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada pemeriksaan pada periode ini.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($pemeriksaan as $row): ?>
                        <tr>
                            <td class="px-6 py-4"><?= date('d/m/Y', strtotime($row['tanggal_pemeriksaan'])); ?></td>
                            <td class="px-6 py-4 font-bold"><?= htmlspecialchars($row['nama_anak'] ?? '-'); ?></td>
                            <td class="px-6 py-4"><?= $formatAngka($row['berat_badan'] ?? 0); ?> Kg</td>
                            <td class="px-6 py-4"><?= $formatAngka($row['tinggi_badan'] ?? 0); ?> Cm</td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['status_gizi'] ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-3xl bg-white border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-extrabold text-gray-900">Histori Bantuan</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4">Tanggal</th>
                        <th class="text-left px-6 py-4">Paket</th>
                        <th class="text-left px-6 py-4">Isi</th>
                        <th class="text-left px-6 py-4">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($bantuan)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada bantuan pada periode ini.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($bantuan as $row): ?>
                        <tr>
                            <td class="px-6 py-4"><?= date('d/m/Y', strtotime($row['tanggal_penyerahan'])); ?></td>
                            <td class="px-6 py-4 font-bold"><?= htmlspecialchars($row['nama_paket'] ?? '-'); ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['isi_paket'] ?? '-'); ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['status_penyerahan'] ?? '-'); ?></td>
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