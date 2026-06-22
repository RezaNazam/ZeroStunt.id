<?php
$pageTitle = 'Standar Pertumbuhan Anak (WHO)';
$pageSubtitle = 'Tabel referensi nilai ambang batas parameter antropometri Kemenkes RI.';

$standars = $data['standar'] ?? [];
$currentJk = $data['filter_jk'] ?? '';
$currentTipe = $data['filter_tipe'] ?? '';

$tableRows = $standars;
$tableEmptyMessage = 'Data acuan standar tidak ditemukan.';

$tableColumns = [
    [
        'label' => 'Indikator',
        'render' => function ($s) {
            $class = $s['tipe_standar'] === 'TB/U'
                ? 'bg-purple-100 text-purple-700'
                : 'bg-amber-100 text-amber-700';

            return '<span class="inline-flex rounded-lg px-2 py-0.5 text-xs font-bold ' . $class . '">' .
                htmlspecialchars($s['tipe_standar']) .
                '</span>';
        }
    ],
    [
        'label' => 'JK',
        'render' => function ($s) {
            $class = $s['jenis_kelamin'] === 'L' ? 'text-blue-600' : 'text-pink-600';
            $label = $s['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan';

            return '<span class="font-bold ' . $class . '">' . htmlspecialchars($label) . '</span>';
        }
    ],
    [
        'label' => 'Usia',
        'render' => function ($s) {
            return htmlspecialchars($s['usia_bulan']) . ' Bulan';
        }
    ],
    [
        'label' => 'Median (Normal)',
        'render' => function ($s) {
            $unit = $s['tipe_standar'] === 'TB/U' ? ' cm' : ' kg';
            return '<span class="font-bold text-green-600">' . htmlspecialchars(NumberHelper::decimal($s['median'])) . $unit . '</span>';
        }
    ],
    [
        'label' => '+1 SD',
        'render' => function ($s) {
            $unit = $s['tipe_standar'] === 'TB/U' ? ' cm' : ' kg';
            return htmlspecialchars(NumberHelper::decimal($s['sd_plus_1'])) . $unit;
        }
    ],
    [
        'label' => '-1 SD',
        'render' => function ($s) {
            $unit = $s['tipe_standar'] === 'TB/U' ? ' cm' : ' kg';
            return htmlspecialchars(NumberHelper::decimal($s['sd_minus_1'])) . $unit;
        }
    ],
    [
        'label' => '-2 SD (Stunted)',
        'render' => function ($s) {
            $unit = $s['tipe_standar'] === 'TB/U' ? ' cm' : ' kg';
            return '<span class="font-semibold text-amber-600">' . htmlspecialchars(NumberHelper::decimal($s['sd_minus_2'])) . $unit . '</span>';
        }
    ],
    [
        'label' => '-3 SD (Severe)',
        'render' => function ($s) {
            $unit = $s['tipe_standar'] === 'TB/U' ? ' cm' : ' kg';
            return '<span class="font-semibold text-red-600">' . htmlspecialchars(NumberHelper::decimal($s['sd_minus_3'])) . $unit . '</span>';
        }
    ],
];

if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    require '../views/partials/data_table.php';
    exit;
}

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
        <div class="border-b border-gray-100 px-6 py-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-lg font-extrabold text-gray-900">
                    <i class="fa-solid fa-chart-line text-purple-600 mr-2"></i>
                    Tabel Standar Pertumbuhan
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Total data: <?= htmlspecialchars((string) ($data['total_data'] ?? count($standars))); ?> standar pertumbuhan
                </p>
            </div>

            <div class="w-full lg:max-w-md">
                <?php
                $searchAction = '/master/standar-pertumbuhan';
                $searchPlaceholder = 'Cari indikator, jenis kelamin, usia, atau nilai standar...';
                $searchTarget = 'tableResult';
                $searchParam = 'q';
                $pageParam = 'page';
                require '../views/partials/searchbar.php';
                ?>
            </div>
        </div>

        <div id="tableResult">
            <?php require '../views/partials/data_table.php'; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>