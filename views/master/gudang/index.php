<?php
$pageTitle = 'Master Gudang';
$pageSubtitle = 'Kelola data gudang untuk pengelolaan stok.';

$gudangs = $gudangs ?? [];
$tablePagination = $tablePagination ?? [];


// | Table Config

$tableRows = $gudangs;
$tableEmptyMessage = 'Belum ada Gudang';

$tableColumns = [
    [
        'label' => 'No',
        'type' => 'number',
        'td_class' => 'text-gray-500 font-medium'
    ],
    [
        'label' => 'Nama Gudang',
        'render' => function ($row) {
            return '<div class="font-bold text-gray-900">' .
                htmlspecialchars($row['nama_gudang']) .
                '</div>';
        }
    ],
    [
        'label' => 'Lokasi Gudang',
        'render' => function ($row) {
            return '<span class="inline-flex rounded-full bg-teal-50 px-3 py-1 text-xs font-bold text-teal-700">' .
                htmlspecialchars($row['lokasi_gudang']) .
                '</span>';
        }
    ],
    [
        'label' => 'Jenis Gudang',
        'render' => function ($row) {
            return htmlspecialchars($row['jenis_gudang'] ?? '-');
        },
        'td_class' => 'text-gray-500'
    ],
    [
        'label' => 'Nama Pengelola',
        'render' => function ($row) {
            return '<p class="truncate">' .
                htmlspecialchars($row['nama_pengelola'] ?: '-') .
                '</p>';
        },
        'td_class' => 'max-w-xs text-gray-500'
    ],
    [
        'label' => 'Aksi',
        'th_class' => 'text-right',
        'td_class' => 'text-right',
        'render' => function ($row) {
            $id = urlencode($row['id_gudang']);

            return '
                <div class="flex justify-end gap-2">
                    <a href="/master/gudang/edit?id=' . $id . '"
                        class="rounded-xl bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">
                        Edit
                    </a>

                    <a href="/master/gudang/delete?id=' . $id . '"
                        data-confirm
                        data-confirm-title="Hapus gudang?"
                        data-confirm-message="Data gudang ini akan dihapus dari sistem. Pastikan gudang tidak sedang dipakai oleh data lain."
                        data-confirm-text="Ya, hapus"
                        data-confirm-tone="danger"
                        class="rounded-xl bg-red-50 px-3 py-2 text-xs font-bold text-red-700 transition hover:bg-red-100">
                        Hapus
                    </a>
                </div>
            ';
        }
    ],
];

/*  | AJAX Response
    | Kalau request dari searchbar/pagination AJAX, balikin table doang.*/

if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    require '../views/partials/data_table.php';
    exit;
}

ob_start();
?>

<div class="space-y-6">
    <!-- Header Action -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900">
                Data Gudang
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Daftar Gudang dalam sistem ZeroStunt.id yang digunakan untuk menyimpan stok bahan pangan.
            </p>
        </div>

        <a href="/master/gudang/create"
            class="inline-flex items-center justify-center rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-700">
            + Tambah Gudang
        </a>
    </div>

    <!-- Flash Message -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="rounded-2xl border border-green-100 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
            <?= htmlspecialchars($_SESSION['success']); ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            <?= htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Table Card -->
    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-lg font-extrabold text-gray-900">
                    Tabel Gudang
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Total data: <?= $tablePagination['total_data'] ?? count($gudangs); ?> gudang
                </p>
            </div>

            <div class="w-full lg:max-w-md">
                <?php
                $searchAction = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $searchPlaceholder = 'Cari nama, lokasi, jenis, atau pengelola gudang...';
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
