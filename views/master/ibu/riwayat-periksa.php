<?php
$pageTitle = 'Dashboard Ibu';
$pageSubtitle = 'Ringkasan data anak, status gizi, dan riwayat bantuan.';

$pemeriksaans = $pemeriksaans ?? ($data['pemeriksaans'] ?? []);
$tablePagination = $tablePagination ?? ($data['pagination_pemeriksaan'] ?? []);

/*
|--------------------------------------------------------------------------
| Table Config
|--------------------------------------------------------------------------
*/
$tableRows = $pemeriksaans;
$tableEmptyMessage = 'Belum ada data pemeriksaan.';

$tableColumns = [
    [
        'label' => 'No',
        'type' => 'number',
        'td_class' => 'text-gray-500 font-semibold'
    ],
    [
        'label' => 'Nama Anak',
        'render' => function ($row) {
            return '<div class="font-bold text-gray-900">' .
                htmlspecialchars($row['nama_anak'] ?? '-') .
                '</div>';
        }
    ],
    [
        'label' => 'Tanggal Pemeriksaan',
        'render' => function ($row) {
            if (empty($row['tanggal_pemeriksaan'])) {
                return '-';
            }

            return htmlspecialchars(date('d/m/Y', strtotime($row['tanggal_pemeriksaan'])));
        },
        'td_class' => 'text-gray-500'
    ],
    [
        'label' => 'Berat Badan',
        'render' => function ($row) {
            return htmlspecialchars($row['berat_badan'] ?? '-') . ' kg';
        },
        'td_class' => 'text-gray-500'
    ],
    [
        'label' => 'Tinggi Badan',
        'render' => function ($row) {
            return htmlspecialchars($row['tinggi_badan'] ?? '-') . ' cm';
        },
        'td_class' => 'text-gray-500'
    ],
    [
        'label' => 'Status Gizi',
        'render' => function ($row) {
            $status = $row['status_gizi'] ?? '-';

            $class = 'bg-gray-100 text-gray-700';

            if ($status === 'Normal') {
                $class = 'bg-green-50 text-green-700';
            } elseif ($status === 'Gizi Kurang') {
                $class = 'bg-amber-50 text-amber-700';
            } elseif ($status === 'Berisiko Stunting' || $status === 'Stunting') {
                $class = 'bg-red-50 text-red-700';
            }

            return '<span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ' . $class . '">' .
                htmlspecialchars($status) .
                '</span>';
        }
    ],
];

/*
|--------------------------------------------------------------------------
| AJAX Response
|--------------------------------------------------------------------------
*/
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    require '../views/partials/data_table.php';
    exit;
}

ob_start();
?>

<div class="space-y-6">
    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-lg font-extrabold text-gray-900">
                    Riwayat Pemeriksaan
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Total data: <?= $tablePagination['total_data'] ?? count($pemeriksaans); ?> pemeriksaan
                </p>
            </div>

            <div class="w-full lg:max-w-md">
                <?php
                $searchAction = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $searchPlaceholder = 'Cari nama anak, tanggal, atau status gizi...';
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