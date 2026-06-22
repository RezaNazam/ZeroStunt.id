<?php
$pageTitle = 'Dashboard Kader';
$pageSubtitle = 'Ringkasan pemeriksaan anak, stok posyandu, dan penyerahan paket gizi.';

$stoks = $stoks ?? ($data['stoks'] ?? []);
$tablePagination = $tablePagination ?? ($data['pagination_stok'] ?? []);

/*
|--------------------------------------------------------------------------
| Table Config
|--------------------------------------------------------------------------
*/
$tableRows = $stoks;
$tableEmptyMessage = 'Belum ada data stok posyandu.';

$tableColumns = [
    [
        'label' => 'No',
        'type' => 'number',
        'td_class' => 'text-gray-500 font-semibold'
    ],
    [
        'label' => 'Komoditas',
        'render' => function ($row) {
            $nama = $row['nama_komoditas']
                ?? $row['nama_bahan']
                ?? $row['komoditas']
                ?? '-';

            return '<div class="font-bold text-gray-900">' .
                htmlspecialchars($nama) .
                '</div>';
        }
    ],
    [
        'label' => 'Jumlah Stok',
        'render' => function ($row) {
            $jumlah = $row['jumlah_stok']
                ?? $row['stok']
                ?? $row['jumlah']
                ?? 0;

            $satuan = $row['singkat']
                ?? $row['nama_satuan']
                ?? $row['satuan']
                ?? '';

            return '<span class="font-bold text-teal-700">' .
                htmlspecialchars($jumlah . ' ' . $satuan) .
                '</span>';
        }
    ],
    [
        'label' => 'Lokasi',
        'render' => function ($row) {
            $lokasi = $row['nama_posyandu']
                ?? $row['nama_gudang']
                ?? $row['lokasi']
                ?? '-';

            return htmlspecialchars($lokasi);
        },
        'td_class' => 'text-gray-500'
    ],
    [
        'label' => 'Status',
        'render' => function ($row) {
            $jumlah = (int) (
                $row['jumlah_stok']
                ?? $row['stok']
                ?? $row['jumlah']
                ?? 0
            );

            if ($jumlah <= 0) {
                $status = 'Habis';
                $class = 'bg-red-50 text-red-700';
            } elseif ($jumlah <= 10) {
                $status = 'Menipis';
                $class = 'bg-amber-50 text-amber-700';
            } else {
                $status = 'Tersedia';
                $class = 'bg-green-50 text-green-700';
            }

            return '<span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ' . $class . '">' .
                htmlspecialchars($status) .
                '</span>';
        }
    ],
    [
        'label' => 'Terakhir Update',
        'render' => function ($row) {
            $tanggal = $row['updated_at']
                ?? $row['tgl_update']
                ?? $row['tgl_created']
                ?? $row['created_at']
                ?? null;

            if (empty($tanggal)) {
                return '-';
            }

            return htmlspecialchars(date('d/m/Y H:i', strtotime($tanggal)));
        },
        'td_class' => 'text-gray-500'
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
                    <i class="fa-solid fa-boxes-stacked text-teal-600 mr-2"></i>
                    Stok Posyandu
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Total data: <?= $tablePagination['total_data'] ?? count($stoks); ?> stok
                </p>
            </div>

            <div class="w-full lg:max-w-md">
                <?php
                $searchAction = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $searchPlaceholder = 'Cari komoditas, jumlah, lokasi, atau status stok...';
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