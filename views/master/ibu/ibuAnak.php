<?php
$pageTitle = 'Data Ibu & Anak';
$pageSubtitle = 'Manajemen dan pemantauan data Ibu hamil/menyusui serta Anak dampingan.';

$daftarIbu = $data['ibu'] ?? [];
$daftarAnak = $data['anak'] ?? [];

$paginationIbu = $data['pagination_ibu'] ?? [];
$paginationAnak = $data['pagination_anak'] ?? [];

$getBadgeStatusGizi = function ($status) {
    $status = strtolower(trim($status ?? ''));

    if (
        str_contains($status, 'prioritas 1') ||
        str_contains($status, 'berisiko stunting') ||
        str_contains($status, 'stunting') ||
        str_contains($status, 'buruk')
    ) {
        return 'bg-red-100 text-red-700';
    }

    if (
        str_contains($status, 'prioritas 2') ||
        str_contains($status, 'perlu pemantauan') ||
        str_contains($status, 'pemantauan') ||
        str_contains($status, 'kurang')
    ) {
        return 'bg-amber-100 text-amber-700';
    }

    return 'bg-green-100 text-green-700';
};

$getBadgePrioritas = function ($prioritas) {
    if ((int) $prioritas === 1) {
        return 'bg-red-600 text-white';
    }

    if ((int) $prioritas === 2) {
        return 'bg-amber-500 text-white';
    }

    return 'bg-gray-200 text-gray-700';
};

$ibuColumns = [
    [
        'label' => 'NIK',
        'key' => 'NIK_ibu',
        'td_class' => 'font-mono text-gray-700'
    ],
    [
        'label' => 'Nama Lengkap',
        'key' => 'nama_ibu',
        'td_class' => 'font-bold text-gray-900'
    ],
    [
        'label' => 'No. Telepon',
        'render' => function ($ibu) {
            return htmlspecialchars($ibu['no_telp'] ?: '-');
        }
    ],
    [
        'label' => 'Status Kehamilan',
        'render' => function ($ibu) {
            $isPregnant = !empty($ibu['is_pregnant']);

            $class = $isPregnant
                ? 'bg-pink-100 text-pink-700'
                : 'bg-blue-100 text-blue-700';

            $label = $isPregnant
                ? 'Hamil'
                : 'Tidak Hamil / Menyusui';

            return '<span class="px-2.5 py-1 text-xs font-bold rounded-full ' . $class . '">' .
                htmlspecialchars($label) .
                '</span>';
        }
    ],
    [
        'label' => 'Alamat',
        'render' => function ($ibu) {
            return htmlspecialchars($ibu['alamat'] ?: '-');
        },
        'td_class' => 'text-gray-500 truncate max-w-xs'
    ],
];

$anakColumns = [
    [
        'label' => 'NIK Anak',
        'key' => 'NIK_anak',
        'td_class' => 'font-mono text-gray-700'
    ],
    [
        'label' => 'Nama Anak',
        'key' => 'nama_anak',
        'td_class' => 'font-bold text-gray-900'
    ],
    [
        'label' => 'Jenis Kelamin',
        'render' => function ($anak) {
            return $anak['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan';
        }
    ],
    [
        'label' => 'Tanggal Lahir',
        'render' => function ($anak) {
            return !empty($anak['tgl_lahir'])
                ? date('d M Y', strtotime($anak['tgl_lahir']))
                : '-';
        }
    ],
    [
        'label' => 'Ibu Kandung',
        'key' => 'nama_ibu',
        'td_class' => 'font-medium text-gray-700'
    ],
    [
        'label' => 'Status Gizi',
        'render' => function ($anak) use ($getBadgeStatusGizi) {

            if (($anak['st_gizi_skrg'] ?? '') === 'Prioritas 1') {
                $status = 'Berisiko Stunting';
            } elseif (($anak['st_gizi_skrg'] ?? '') === 'Prioritas 2') {
                $status = 'Perlu Pemantauan';
            } else {
                $status = 'Normal';
            }

            return '<span class="px-2.5 py-1 text-xs font-bold rounded-full ' .
                $getBadgeStatusGizi($status) .
                '">' .
                htmlspecialchars($status) .
                '</span>';
        }
    ],
    [
        'label' => 'Prioritas',
        'render' => function ($anak) use ($getBadgePrioritas) {
            return '<span class="px-2.5 py-1 text-xs font-bold rounded-full ' .
                $getBadgePrioritas($anak['skala_prioritas']) .
                '">' .
                htmlspecialchars($anak['skala_prioritas'] ?? '-') .
                '</span>';
        }
    ],
    [
        'label' => 'Posyandu',
        'render' => function ($anak) {
            return '<span class="bg-teal-50 px-2 py-1 rounded-md text-xs font-semibold text-teal-700">' .
                htmlspecialchars($anak['nama_gudang'] ?? '-') .
                '</span>';
        }
    ],
];

$renderIbuTable = function () use ($daftarIbu, $paginationIbu, $ibuColumns) {
    $tableRows = $daftarIbu;
    $tablePagination = $paginationIbu;
    $tableColumns = $ibuColumns;
    $tableEmptyMessage = 'Belum ada data Ibu terdaftar.';

    require '../views/partials/data_table.php';
};

$renderAnakTable = function () use ($daftarAnak, $paginationAnak, $anakColumns) {
    $tableRows = $daftarAnak;
    $tablePagination = $paginationAnak;
    $tableColumns = $anakColumns;
    $tableEmptyMessage = 'Belum ada data Anak terdaftar.';

    require '../views/partials/data_table.php';
};

if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    $target = $_GET['ajax_target'] ?? '';

    if ($target === 'ibuTable') {
        $renderIbuTable();
        exit;
    }

    if ($target === 'anakTable') {
        $renderAnakTable();
        exit;
    }

    exit;
}

ob_start();
?>

<div class="space-y-8">

    <!-- Table Ibu -->
    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-lg font-extrabold text-gray-900">
                    <i class="fa-solid fa-person-breastfeeding text-pink-600 mr-2"></i>
                    Rekap Data Ibu
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Daftar seluruh Ibu yang terintegrasi dengan Posyandu binaan.
                </p>
            </div>

            <div class="w-full lg:max-w-md">
                <?php
                $searchAction = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $searchPlaceholder = 'Cari data ibu...';
                $searchTarget = 'ibuTable';
                $searchParam = 'q_ibu';
                $pageParam = 'page_ibu';
                require '../views/partials/searchbar.php';
                ?>
            </div>
        </div>

        <div id="ibuTable">
            <?php $renderIbuTable(); ?>
        </div>
    </div>

    <!-- Table Anak -->
    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-lg font-extrabold text-gray-900">
                    <i class="fa-solid fa-child text-indigo-600 mr-2"></i>
                    Rekap Data Anak
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Daftar anak dampingan penanganan stunting beserta relasi orang tua.
                </p>
            </div>

            <div class="w-full lg:max-w-md">
                <?php
                $searchAction = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $searchPlaceholder = 'Cari data anak...';
                $searchTarget = 'anakTable';
                $searchParam = 'q_anak';
                $pageParam = 'page_anak';
                require '../views/partials/searchbar.php';
                ?>
            </div>
        </div>

        <div id="anakTable">
            <?php $renderAnakTable(); ?>
        </div>
    </div>

</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>