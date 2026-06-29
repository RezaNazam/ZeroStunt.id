<?php
$data = $data ?? [];

$pageTitle = 'Distribusi Stok';
$pageSubtitle = 'Kelola distribusi stok pangan dari gudang asal ke gudang tujuan.';

$distribusiList = $data['distribusi'] ?? [];

$role = $_SESSION['role'] ?? '';
$isAdmin = defined('ROLE_ADMIN') ? $role === ROLE_ADMIN : strtolower($role) === 'admin';
$isKader = defined('ROLE_KADER') ? $role === ROLE_KADER : strtolower($role) === 'kader';

$formatJumlah = function ($value) {
    $number = number_format((float) $value, 2, '.', '');
    return rtrim(rtrim($number, '0'), '.');
};

$renderDistribusiTable = function () use ($distribusiList, $data, $formatJumlah, $isAdmin, $isKader) {
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
                <?php if (empty($distribusiList)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                            Belum ada data distribusi.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($distribusiList as $d): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-bold text-gray-900">
                            <?= htmlspecialchars($d['no_distribusi']) ?>
                        </td>

                        <td class="px-6 py-4 text-gray-500">
                            <?= htmlspecialchars($d['gudang_asal']) ?>
                        </td>

                        <td class="px-6 py-4 text-gray-500">
                            <?= htmlspecialchars($d['gudang_tujuan']) ?>
                        </td>

                        <td class="px-6 py-4">
                            <ul class="list-disc list-inside text-xs text-gray-600">
                                <?php foreach (($d['details'] ?? []) as $det): ?>
                                    <li>
                                        <?= htmlspecialchars($det['nama_komoditas']) ?>
                                        (<?= htmlspecialchars($formatJumlah($det['jumlah'])) ?>
                                        <?= htmlspecialchars($det['satuan'] ?? '') ?>)
                                    </li>
                                <?php endforeach; ?>
                            </ul>
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
                                    onsubmit="return confirm('Tandai distribusi ini sebagai diterima?')">
                                    <input type="hidden" name="id_distribusi" value="<?= htmlspecialchars($d['id_distribusi']); ?>">

                                    <button type="submit"
                                        class="rounded-xl bg-green-50 px-3 py-2 text-xs font-bold text-green-700 transition hover:bg-green-100">
                                        Terima
                                    </button>
                                </form>

                            <?php elseif (($d['status_distribusi'] ?? '') === 'Dikirim' && $isAdmin): ?>
                                <form action="/transaksi/distribusi/batal" method="POST"
                                    onsubmit="return confirm('Batalkan distribusi ini?')">
                                    <input type="hidden" name="id_distribusi" value="<?= htmlspecialchars($d['id_distribusi']); ?>">

                                    <button type="submit"
                                        class="rounded-xl bg-red-50 px-3 py-2 text-xs font-bold text-red-700 transition hover:bg-red-100">
                                        Batal
                                    </button>
                                </form>

                            <?php else: ?>
                                <span class="text-xs font-semibold text-gray-400">
                                    -
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php
    $paginationData = [
        'halaman_aktif' => $data['current_page'] ?? 1,
        'total_halaman' => $data['total_pages'] ?? 1,
        'total_data' => $data['total_data'] ?? count($distribusiList),
        'per_halaman' => $data['per_halaman'] ?? 20,
        'page_param' => 'page'
    ];

    require '../views/partials/pagination.php';
    ?>

<?php
};

if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    $renderDistribusiTable();
    exit;
}

ob_start();
?>

<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900">
                Daftar Distribusi Stok
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Pemantauan distribusi stok pangan dari gudang asal ke gudang tujuan.
            </p>
        </div>

        <?php if ($isAdmin): ?>
            <a href="/transaksi/distribusi/create"
                class="inline-flex items-center justify-center rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-700">
                + Buat Distribusi Baru
            </a>
        <?php endif; ?>
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

    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-lg font-extrabold text-gray-900">
                    Tabel Distribusi
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Total data: <?= $data['total_data'] ?? count($distribusiList); ?> distribusi
                </p>
            </div>

            <div class="w-full lg:max-w-md">
                <?php
                $searchAction = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $searchPlaceholder = 'Cari no distribusi, gudang, komoditas, tanggal, atau status...';
                $searchTarget = 'tableResult';
                $searchParam = 'q';
                $pageParam = 'page';
                require '../views/partials/searchbar.php';
                ?>
            </div>
        </div>

        <div id="tableResult">
            <?php $renderDistribusiTable(); ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
