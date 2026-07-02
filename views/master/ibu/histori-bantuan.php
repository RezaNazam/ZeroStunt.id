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

$getBadgePaket = function ($row) {
    $namaPaket = strtolower($row['nama_paket'] ?? '');
    $idPaket = (int) ($row['id_paket'] ?? 0);

    if (str_contains($namaPaket, 'prioritas 1') || $idPaket === 1) {
        return 'bg-red-50 text-red-700';
    }

    if (str_contains($namaPaket, 'prioritas 2') || $idPaket === 2) {
        return 'bg-amber-50 text-amber-700';
    }

    if (str_contains($namaPaket, 'prioritas 3') || $idPaket === 3) {
        return 'bg-green-50 text-green-700';
    }

    return 'bg-teal-50 text-teal-700';
};

$tableColumns = [
    [
        'label' => 'No',
        'type' => 'number',
        'td_class' => 'text-gray-500 font-semibold'
    ],
    [
        'label' => 'Tanggal Bantuan',
        'render' => function ($row) {
            $tanggal = $row['tanggal_penyerahan'] ?? null;

            if (empty($tanggal)) {
                $tanggalText = '-';
            } else {
                $dayNames = [
                    'Sun' => 'Minggu',
                    'Mon' => 'Senin',
                    'Tue' => 'Selasa',
                    'Wed' => 'Rabu',
                    'Thu' => 'Kamis',
                    'Fri' => "Jum'at",
                    'Sat' => 'Sabtu'
                ];

                $monthNames = [
                    'January' => 'Januari',
                    'February' => 'Februari',
                    'March' => 'Maret',
                    'April' => 'April',
                    'May' => 'Mei',
                    'June' => 'Juni',
                    'July' => 'Juli',
                    'August' => 'Agustus',
                    'September' => 'September',
                    'October' => 'Oktober',
                    'November' => 'November',
                    'December' => 'Desember'
                ];

                $dayName = $dayNames[date('D', strtotime($tanggal))] ?? '';
                $monthName = $monthNames[date('F', strtotime($tanggal))] ?? date('F', strtotime($tanggal));

                $tanggalText = $dayName . ', ' . date('j', strtotime($tanggal)) . ' ' . $monthName . ' ' . date('Y', strtotime($tanggal));
            }

            $idPenyerahan = (int) ($row['id_penyerahan'] ?? 0);

            $detailLink = '';

            if ($idPenyerahan > 0) {
                $detailLink = '
                    <a href="/transaksi/penyerahan/detail?id=' . $idPenyerahan . '"
                        class="mt-1 inline-flex text-xs font-bold text-teal-700 hover:text-teal-800">
                        Lihat detail
                    </a>
                ';
            }

            return '
                <div>
                    <div class="font-semibold text-gray-700">' . htmlspecialchars($tanggalText) . '</div>
                    ' . $detailLink . '
                </div>
            ';
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
        'label' => 'Paket Gizi',
        'render' => function ($row) use ($getBadgePaket) {
            $namaPaket = $row['nama_paket'] ?? 'Paket Bantuan';
            $badgeClass = $getBadgePaket($row);

            return '
            <div>
                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ' . $badgeClass . '">' .
                htmlspecialchars($namaPaket) .
                '</span>
                <div class="mt-0.5 text-xs px-3 text-gray-500">' .
                (int) ($row['total_item'] ?? 0) . ' jenis komoditas
                </div>
            </div>
        ';
        }
    ],
    [
        'label' => 'Ringkasan Isi',
        'render' => function ($row) {
            $detail = $row['detail_bantuan'] ?? '';

            if ($detail === '') {
                return '<span class="text-gray-400">-</span>';
            }

            $items = explode('||', $detail);

            $html = '<ul class="space-y-1 text-gray-600">';

            foreach ($items as $item) {
                $html .= '<li class="text-sm">• ' . htmlspecialchars($item) . '</li>';
            }

            $html .= '</ul>';

            return $html;
        }
    ],
    [
        'label' => 'Status',
        'render' => function ($row) {
            $status = $row['status_penyerahan'] ?? 'Diterima';

            $class = 'bg-gray-100 text-gray-700';

            if ($status === 'Diterima' || $status === 'Selesai' || $status === 'Diserahkan') {
                $class = 'bg-green-50 text-green-700';
            } elseif ($status === 'Pending' || $status === 'Menunggu' || $status === 'Diproses') {
                $class = 'bg-amber-50 text-amber-700';
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
