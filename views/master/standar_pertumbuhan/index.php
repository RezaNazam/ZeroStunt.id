<?php
$pageTitle = 'Standar Pertumbuhan Anak (WHO)';
$pageSubtitle = 'Tabel referensi nilai ambang batas parameter antropometri Kemenkes RI.';

$standars = $data['standar'] ?? [];
$currentJk = $data['filter_jk'] ?? '';
$currentTipe = $data['filter_tipe'] ?? '';

ob_start();
?>

<div class="space-y-6">
    <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
        <form method="GET" action="/master/standar-pertumbuhan" class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <div class="flex-1">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Jenis Kelamin</label>
                <select name="jk"
                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-teal-500 focus:bg-white focus:outline-none">
                    <option value="">-- Semua Jenis Kelamin --</option>
                    <option value="L" <?= $currentJk === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="P" <?= $currentJk === 'P' ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Indikator
                    Gizi</label>
                <select name="tipe"
                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-teal-500 focus:bg-white focus:outline-none">
                    <option value="">-- Semua Indikator --</option>
                    <option value="TB/U" <?= $currentTipe === 'TB/U' ? 'selected' : '' ?>>TB/U (Tinggi Badan / Umur)
                    </option>
                    <option value="BB/U" <?= $currentTipe === 'BB/U' ? 'selected' : '' ?>>BB/U (Berat Badan / Umur)
                    </option>
                </select>
            </div>
            <div class="sm:mt-7">
                <button type="submit"
                    class="w-full sm:w-auto rounded-2xl bg-teal-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-teal-700">
                    <i class="fa-solid fa-filter mr-2"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Indikator</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">JK</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Usia</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Median (Normal)</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">+1 SD</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">-1 SD</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">-2 SD (Stunted)</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">-3 SD (Severe)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($standars)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500">Data acuan standar tidak ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($standars as $s): ?>
                        <?php $unit = ($s['tipe_standar'] === 'TB/U') ? ' cm' : ' kg'; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold">
                                <span
                                    class="inline-flex rounded-lg px-2 py-0.5 text-xs font-bold <?= $s['tipe_standar'] === 'TB/U' ? 'bg-purple-100 text-purple-700' : 'bg-amber-100 text-amber-700' ?>">
                                    <?= htmlspecialchars($s['tipe_standar']) ?>
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 font-bold <?= $s['jenis_kelamin'] === 'L' ? 'text-blue-600' : 'text-pink-600' ?>">
                                <?= $s['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                <?= htmlspecialchars($s['usia_bulan']) ?> Bulan
                            </td>
                            <td class="px-6 py-4 text-green-600 font-bold">
                                <?= htmlspecialchars($s['median']) . $unit ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <?= htmlspecialchars($s['sd_plus_1']) . $unit ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <?= htmlspecialchars($s['sd_minus_1']) . $unit ?>
                            </td>
                            <td class="px-6 py-4 text-amber-600 font-semibold">
                                <?= htmlspecialchars($s['sd_minus_2']) . $unit ?>
                            </td>
                            <td class="px-6 py-4 text-red-600 font-semibold">
                                <?= htmlspecialchars($s['sd_minus_3']) . $unit ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php require '../views/partials/pagination.php'; ?>

    </div>
</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>