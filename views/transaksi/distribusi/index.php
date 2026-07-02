<?php
$data = $data ?? [];

$pageTitle = 'Distribusi Stok';
$pageSubtitle = 'Kelola distribusi stok pangan dari gudang asal ke gudang tujuan.';

$distribusiList = $data['distribusi'] ?? [];

$role = $_SESSION['role'] ?? '';

$isAdmin = defined('ROLE_ADMIN')
    ? $role === ROLE_ADMIN
    : strtolower($role) === 'admin';

$isKader = defined('ROLE_KADER')
    ? $role === ROLE_KADER
    : strtolower($role) === 'kader';

$ongoingDistribusiList = [];
$historyDistribusiList = [];

if ($isKader) {
    $ongoingDistribusiList = array_values(array_filter($distribusiList, function ($d) {
        return ($d['status_distribusi'] ?? '') === 'Dikirim';
    }));

    $historyDistribusiList = array_values(array_filter($distribusiList, function ($d) {
        return ($d['status_distribusi'] ?? '') === 'Diterima';
    }));
}

$formatJumlah = function ($value) {
    $number = number_format((float) $value, 2, '.', '');
    return rtrim(rtrim($number, '0'), '.');
};

$renderDistribusiRows = function (array $list, string $emptyMessage) use ($formatJumlah, $isAdmin, $isKader) {
?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">No Distribusi</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Gudang Asal</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Gudang Tujuan</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Detail Komoditas</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Tanggal</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Status</th>
                    <th class="px-6 py-4 text-right font-bold text-gray-600">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                <?php if (empty($list)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <?= htmlspecialchars($emptyMessage); ?>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($list as $d): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-bold text-gray-900">
                            <?= htmlspecialchars($d['no_distribusi'] ?? '-'); ?>
                        </td>

                        <td class="px-6 py-4 text-gray-500">
                            <?= htmlspecialchars($d['gudang_asal'] ?? '-'); ?>
                        </td>

                        <td class="px-6 py-4 text-gray-500">
                            <?= htmlspecialchars($d['gudang_tujuan'] ?? '-'); ?>
                        </td>

                        <td class="px-6 py-4">
                            <?php if (!empty($d['details'])): ?>
                                <ul class="list-disc list-inside text-xs text-gray-600 space-y-1">
                                    <?php foreach ($d['details'] as $det): ?>
                                        <li>
                                            <?= htmlspecialchars($det['nama_komoditas'] ?? '-'); ?>
                                            (<?= htmlspecialchars($formatJumlah($det['jumlah'] ?? 0)); ?>
                                            <?= htmlspecialchars($det['satuan'] ?? ''); ?>)
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <span class="text-xs text-gray-400">-</span>
                            <?php endif; ?>
                        </td>

                        <td class="px-6 py-4 text-gray-500">
                            <?= !empty($d['tanggal_distribusi'])
                                ? htmlspecialchars(date('d/m/Y', strtotime($d['tanggal_distribusi'])))
                                : '-'; ?>
                        </td>

                        <td class="px-6 py-4">
                            <?php
                            $status = $d['status_distribusi'] ?? '-';
                            $class = 'bg-gray-100 text-gray-700';

                            if ($status === 'Dikirim') {
                                $class = 'bg-amber-100 text-amber-700';
                            } elseif ($status === 'Diterima') {
                                $class = 'bg-green-100 text-green-700';
                            } elseif ($status === 'Dibatalkan') {
                                $class = 'bg-red-100 text-red-700';
                            }
                            ?>

                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold <?= $class; ?>">
                                <?= htmlspecialchars($status); ?>
                            </span>
                        </td>

                        <td class="px-6 py-4 text-right">
                            <?php if (($d['status_distribusi'] ?? '') === 'Dikirim' && $isKader): ?>
                                <form action="/transaksi/distribusi/terima" method="POST"
                                    data-confirm
                                    data-confirm-title="Terima distribusi?"
                                    data-confirm-message="Pastikan stok fisik sudah diterima di posyandu sebelum menandai distribusi sebagai diterima."
                                    data-confirm-text="Ya, terima"
                                    data-confirm-tone="success">

                                    <input type="hidden"
                                        name="id_distribusi"
                                        value="<?= htmlspecialchars($d['id_distribusi'] ?? ''); ?>">

                                    <button type="submit"
                                        class="rounded-xl bg-green-50 px-3 py-2 text-xs font-bold text-green-700 transition hover:bg-green-100">
                                        Terima
                                    </button>
                                </form>

                            <?php elseif (($d['status_distribusi'] ?? '') === 'Dikirim' && $isAdmin): ?>
                                <form action="/transaksi/distribusi/batal" method="POST"
                                    data-confirm
                                    data-confirm-title="Batalkan distribusi?"
                                    data-confirm-message="Distribusi ini akan dibatalkan dan tidak dapat diterima oleh kader."
                                    data-confirm-text="Ya, batalkan"
                                    data-confirm-tone="danger">

                                    <input type="hidden"
                                        name="id_distribusi"
                                        value="<?= htmlspecialchars($d['id_distribusi'] ?? ''); ?>">

                                    <button type="submit"
                                        class="rounded-xl bg-red-50 px-3 py-2 text-xs font-bold text-red-700 transition hover:bg-red-100">
                                        Batal
                                    </button>
                                </form>

                            <?php else: ?>
                                <span class="text-xs font-semibold text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php
};

$renderDistribusiCard = function (
    string $title,
    string $subtitle,
    array $list,
    string $emptyMessage,
    string $dotClass = 'bg-teal-500',
    bool $showPagination = false
) use ($renderDistribusiRows, $data) {
?>
    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-5">
            <div class="flex items-center gap-3">
                <span class="h-2.5 w-2.5 rounded-full <?= $dotClass; ?>"></span>
                <h3 class="text-lg font-extrabold text-gray-900">
                    <?= htmlspecialchars($title); ?>
                </h3>
            </div>

            <p class="mt-1 text-sm text-gray-500">
                <?= htmlspecialchars($subtitle); ?>
            </p>
        </div>

        <?php $renderDistribusiRows($list, $emptyMessage); ?>

        <?php if ($showPagination): ?>
            <?php
            $paginationData = [
                'halaman_aktif' => $data['current_page'] ?? 1,
                'total_halaman' => $data['total_pages'] ?? 1,
                'total_data' => $data['total_data'] ?? count($list),
                'per_halaman' => $data['per_halaman'] ?? 20,
                'page_param' => 'page'
            ];

            require '../views/partials/pagination.php';
            ?>
        <?php endif; ?>
    </div>
    <?php
};

$renderDistribusiContent = function () use (
    $isKader,
    $distribusiList,
    $ongoingDistribusiList,
    $historyDistribusiList,
    $renderDistribusiCard
) {
    if ($isKader) {
    ?>
        <div class="space-y-8">
            <?php
            $renderDistribusiCard(
                'Distribusi Sedang Dikirim',
                'Distribusi yang masih menunggu diterima oleh kader.',
                $ongoingDistribusiList,
                'Tidak ada distribusi yang sedang dikirim.',
                'bg-amber-500',
                false
            );

            $renderDistribusiCard(
                'Riwayat Penerimaan Distribusi',
                'Distribusi yang sudah berhasil diterima.',
                $historyDistribusiList,
                'Belum ada riwayat penerimaan distribusi.',
                'bg-green-500',
                false
            );
            ?>
        </div>
<?php
        return;
    }

    $renderDistribusiCard(
        'Daftar Seluruh Distribusi',
        'Semua transaksi distribusi stok, termasuk dikirim, diterima, dan dibatalkan.',
        $distribusiList,
        'Belum ada data distribusi.',
        'bg-teal-500',
        true
    );
};

if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    $renderDistribusiContent();
    exit;
}

ob_start();
?>

<div class="space-y-8">
    <div class="space-y-4">
        <!-- Judul + tombol admin -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">
                    Daftar Distribusi Stok
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Pemantauan distribusi stok pangan dari gudang asal ke gudang tujuan.
                </p>

                <div class="mt-3 flex flex-wrap gap-2">
                    <?php if ($isKader): ?>
                        <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">
                            Dikirim: <?= count($ongoingDistribusiList); ?>
                        </span>

                        <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-700">
                            Diterima: <?= count($historyDistribusiList); ?>
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center rounded-full bg-teal-50 px-3 py-1 text-xs font-bold text-teal-700">
                            Total: <?= $data['total_data'] ?? count($distribusiList); ?> distribusi
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($isAdmin): ?>
                <a href="/transaksi/distribusi/create"
                    class="inline-flex h-12 w-auto self-start shrink-0 items-center justify-center whitespace-nowrap rounded-2xl bg-teal-600 px-5 text-sm font-bold text-white transition hover:bg-teal-700">
                    + Buat Distribusi Baru
                </a>
            <?php endif; ?>
        </div>

        <!-- Search -->
        <div class="w-full sm:max-w-md">
            <?php
            $searchAction = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $searchPlaceholder = 'Cari distribusi...';
            $searchTarget = 'tableResult';
            $searchParam = 'q';
            $pageParam = 'page';
            require '../views/partials/searchbar.php';
            ?>
        </div>
    </div>

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

    <div id="tableResult">
        <?php $renderDistribusiContent(); ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
