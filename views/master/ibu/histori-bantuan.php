<?php
$pageTitle = 'Dashboard Ibu';
$pageSubtitle = 'Ringkasan data anak, status gizi, dan riwayat bantuan.';

$bantuans = $bantuans ?? ($data['bantuans'] ?? []);
$tablePagination = $tablePagination ?? ($data['pagination_bantuan'] ?? []);

/*
|--------------------------------------------------------------------------
| Table Config
|--------------------------------------------------------------------------
*/
$tableRows = $bantuans;
$tableEmptyMessage = 'Belum ada data bantuan.';

$tableColumns = [
    [
        'label' => 'No',
        'type' => 'number',
        'td_class' => 'text-gray-500 font-semibold'
    ],
    [
        'label' => 'Tanggal Bantuan',
        'render' => function ($row) {
            $tanggal = $row['tanggal_bantuan']
                ?? $row['tanggal_penyerahan']
                ?? $row['tgl_penyerahan']
                ?? $row['created_at']
                ?? null;

            if (empty($tanggal)) {
                return '-';
            }

            return htmlspecialchars(date('d/m/Y', strtotime($tanggal)));
        },
        'td_class' => 'text-gray-500'
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
        'label' => 'Bantuan',
        'render' => function ($row) {
            $namaBantuan = $row['nama_komoditas']
                ?? $row['nama_bantuan']
                ?? $row['jenis_bantuan']
                ?? '-';

            return '<span class="inline-flex rounded-full bg-teal-50 px-3 py-1 text-xs font-bold text-teal-700">' .
                htmlspecialchars($namaBantuan) .
                '</span>';
        }
    ],
    [
        'label' => 'Jumlah',
        'render' => function ($row) {
            $jumlah = $row['jumlah'] ?? '-';
            $satuan = $row['satuan'] ?? $row['nama_satuan'] ?? '';

            return htmlspecialchars($jumlah . ' ' . $satuan);
        },
        'td_class' => 'text-gray-500'
    ],
    [
        'label' => 'Status',
        'render' => function ($row) {
            $status = $row['status_bantuan']
                ?? $row['status_penyerahan']
                ?? $row['status']
                ?? 'Diterima';

            $class = 'bg-gray-100 text-gray-700';

            if ($status === 'Diterima' || $status === 'Selesai' || $status === 'Diserahkan') {
                $class = 'bg-green-50 text-green-700';
            } elseif ($status === 'Pending' || $status === 'Menunggu' || $status === 'Diproses') {
            } elseif ($status === 'Ditolak' || $status === 'Gagal') {
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
                    Riwayat Bantuan
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Total data: <?= $tablePagination['total_data'] ?? count($bantuans); ?> bantuan
                </p>
            </div>

            <div class="w-full lg:max-w-md">
                <?php
                $searchAction = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $searchPlaceholder = 'Cari nama anak, bantuan, jumlah, atau status...';
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
