<?php
$data = $data ?? [];

$role = $_SESSION['role'] ?? '';
$isAdmin = defined('ROLE_ADMIN') ? $role === ROLE_ADMIN : strtolower($role) === 'admin';
$isPetani = defined('ROLE_PETANI') ? $role === ROLE_PETANI : strtolower($role) === 'petani';

$pageTitle = $isAdmin ? 'Manajemen Pengadaan' : 'Pengadaan Komoditas';
$pageSubtitle = $isAdmin ? 'Kelola semua requirement pengadaan komoditas.' : 'Lihat dan ambil pasokan komoditas yang tersedia.';

$available = $data['available'] ?? [];
$taken = $data['taken'] ?? [];
$all_pengadaan = $data['all_pengadaan'] ?? [];

$renderAdminPengadaanTable = function () use ($all_pengadaan, $data) {
?>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">No Kontrak</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Gudang</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Detail Komoditas</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Petani</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Total Nilai</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Status Kontrak</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                <?php if (empty($all_pengadaan)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            Data pengadaan tidak ditemukan.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($all_pengadaan as $p): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-bold text-gray-900">
                            <?= htmlspecialchars($p['no_kontrak']) ?>
                        </td>

                        <td class="px-6 py-4 text-gray-500">
                            <?= htmlspecialchars($p['nama_gudang']) ?>
                        </td>

                        <td class="px-6 py-4">
                            <ul class="list-disc list-inside text-xs text-gray-600">
                                <?php foreach (($p['details'] ?? []) as $det): ?>
                                    <li>
                                        <?= htmlspecialchars($det['nama_komoditas']) ?>
                                        (<?= htmlspecialchars($det['jumlah']) ?>
                                        <?= htmlspecialchars($det['satuan']) ?>)
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>

                        <td class="px-6 py-4 text-gray-700">
                            <?= htmlspecialchars($p['nama_petani'] ?? 'Belum Ada (Lowongan)') ?>
                        </td>

                        <td class="px-6 py-4 font-medium text-gray-900">
                            Rp <?= number_format($p['total_bayar'], 0, ',', '.') ?>
                        </td>

                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-bold rounded-full <?= $p['status_kontrak'] === 'Disetujui' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' ?>">
                                <?= htmlspecialchars($p['status_kontrak']) ?>
                            </span>
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
        'total_data' => $data['total_data'] ?? count($all_pengadaan),
        'per_halaman' => $data['per_halaman'] ?? 20,
        'page_param' => 'page'
    ];

    require '../views/partials/pagination.php';
    ?>

<?php
};

$renderAvailableTable = function () use ($available, $data) {
?>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">No Kontrak / Hub Gudang</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Kebutuhan Komoditas</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Pagu Anggaran</th>
                    <th class="px-6 py-4 text-right font-bold text-gray-600">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                <?php if (empty($available)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                            Saat ini belum ada requirement pengadaan yang dibuka oleh Admin.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($available as $item): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-bold text-gray-900">
                            <?= htmlspecialchars($item['no_kontrak']) ?><br>
                            <span class="text-xs text-gray-400 font-normal">
                                <?= htmlspecialchars($item['nama_gudang']) ?>
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <ul class="text-xs text-gray-600 list-disc list-inside">
                                <?php foreach (($item['details'] ?? []) as $det): ?>
                                    <li>
                                        <?= htmlspecialchars($det['nama_komoditas']) ?>:
                                        <strong>
                                            <?= htmlspecialchars($det['jumlah']) ?>
                                            <?= htmlspecialchars($det['satuan']) ?>
                                        </strong>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>

                        <td class="px-6 py-4 font-bold text-gray-900">
                            Rp <?= number_format($item['total_bayar'], 0, ',', '.') ?>
                        </td>

                        <td class="px-6 py-4 text-right">
                            <form action="/transaksi/pengadaan/ambil" method="post">
                                <input type="hidden" name="id_pengadaan" value="<?= $item['id_pengadaan'] ?>">
                                <button type="submit"
                                    class="rounded-xl bg-teal-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-teal-700">
                                    Sanggupi / ACC
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php
    $paginationData = [
        'halaman_aktif' => $data['current_page_avail'] ?? 1,
        'total_halaman' => $data['total_pages_avail'] ?? 1,
        'total_data' => $data['total_data_avail'] ?? count($available),
        'per_halaman' => $data['per_halaman_avail'] ?? 5,
        'page_param' => 'p_avail'
    ];

    require '../views/partials/pagination.php';
    ?>

<?php
};

$renderTakenTable = function () use ($taken, $data) {
?>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">No Kontrak</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Komoditas Anda</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Nilai Kontrak</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Status Bayar</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                <?php if (empty($taken)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                            Anda belum mengambil pengadaan apa pun.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($taken as $item): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-bold text-gray-900">
                            <?= htmlspecialchars($item['no_kontrak']) ?>
                        </td>

                        <td class="px-6 py-4">
                            <ul class="text-xs text-gray-600">
                                <?php foreach (($item['details'] ?? []) as $det): ?>
                                    <li>
                                        - <?= htmlspecialchars($det['nama_komoditas']) ?>
                                        (<?= htmlspecialchars($det['jumlah']) ?>)
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>

                        <td class="px-6 py-4 text-gray-900 font-bold">
                            Rp <?= number_format($item['total_bayar'], 0, ',', '.') ?>
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold <?= $item['status_bayar'] === 'Lunas' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                <?= htmlspecialchars($item['status_bayar']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php
    $paginationData = [
        'halaman_aktif' => $data['current_page_taken'] ?? 1,
        'total_halaman' => $data['total_pages_taken'] ?? 1,
        'total_data' => $data['total_data_taken'] ?? count($taken),
        'per_halaman' => $data['per_halaman_taken'] ?? 5,
        'page_param' => 'p_taken'
    ];

    require '../views/partials/pagination.php';
    ?>

<?php
};

if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    $target = $_GET['ajax_target'] ?? '';

    if ($isAdmin && $target === 'tableResult') {
        $renderAdminPengadaanTable();
        exit;
    }

    if ($isPetani && $target === 'availableTable') {
        $renderAvailableTable();
        exit;
    }

    if ($isPetani && $target === 'takenTable') {
        $renderTakenTable();
        exit;
    }

    exit;
}

ob_start();
?>

<div class="space-y-8">
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

    <?php if ($isAdmin): ?>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">Daftar Kontrak Pengadaan</h2>
                <p class="text-sm text-gray-500 mt-1">Daftar pemantauan seluruh transaksi pasokan pangan dari petani lokal.
                </p>
            </div>
            <a href="/transaksi/pengadaan/create"
                class="inline-flex items-center justify-center rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-700">+
                Buat Pengadaan Baru</a>
        </div>

        <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-extrabold text-gray-900">
                        Tabel Pengadaan
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Total data: <?= $data['total_data'] ?? count($all_pengadaan); ?> pengadaan
                    </p>
                </div>

                <div class="w-full lg:max-w-md">
                    <?php
                    $searchAction = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                    $searchPlaceholder = 'Cari no kontrak, gudang, komoditas, petani, atau status...';
                    $searchTarget = 'tableResult';
                    $searchParam = 'q';
                    $pageParam = 'page';
                    require '../views/partials/searchbar.php';
                    ?>
                </div>
            </div>

            <div id="tableResult">
                <?php $renderAdminPengadaanTable(); ?>
            </div>
        </div>
</div>
<?php endif; ?>

<?php if ($isPetani): ?>
    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-lg font-extrabold text-gray-900">
                    <i class="fa-solid fa-box-open text-teal-600 mr-2"></i>
                    Lowongan Pengadaan Tersedia
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Total data: <?= $data['total_data_avail'] ?? count($available); ?> lowongan
                </p>
            </div>

            <div class="w-full lg:max-w-md">
                <?php
                $searchAction = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $searchPlaceholder = 'Cari no kontrak, gudang, komoditas, atau nilai...';
                $searchTarget = 'availableTable';
                $searchParam = 'q_avail';
                $pageParam = 'p_avail';
                require '../views/partials/searchbar.php';
                ?>
            </div>
        </div>

        <div id="availableTable">
            <?php $renderAvailableTable(); ?>
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-lg font-extrabold text-gray-900">
                    <i class="fa-solid fa-file-invoice-dollar text-amber-600 mr-2"></i>
                    Kontrak Kerja Sama Saya
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Total data: <?= $data['total_data_taken'] ?? count($taken); ?> kontrak
                </p>
            </div>

            <div class="w-full lg:max-w-md">
                <?php
                $searchAction = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $searchPlaceholder = 'Cari no kontrak, komoditas, nilai, atau status bayar...';
                $searchTarget = 'takenTable';
                $searchParam = 'q_taken';
                $pageParam = 'p_taken';
                require '../views/partials/searchbar.php';
                ?>
            </div>
        </div>

        <div id="takenTable">
            <?php $renderTakenTable(); ?>
        </div>
    </div>
<?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>