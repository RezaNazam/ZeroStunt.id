<?php
$data = $data ?? [];

$pageTitle = 'Pemeriksaan Anak';
$pageSubtitle = 'Kelola data hasil pemeriksaan antropometri (berat badan, tinggi badan, status gizi) anak.';

$pemeriksaanList = $data['pemeriksaan'] ?? [];

$role = $_SESSION['role'] ?? '';
$isAdmin = defined('ROLE_ADMIN') ? $role === ROLE_ADMIN : strtolower($role) === 'admin';
$isKader = defined('ROLE_KADER') ? $role === ROLE_KADER : strtolower($role) === 'kader';
$isIbu   = defined('ROLE_IBU')   ? $role === ROLE_IBU   : strtolower($role) === 'ibu';

$renderPemeriksaanTable = function () use ($pemeriksaanList, $isAdmin, $isKader, $isIbu) {
?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">No</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Nama Anak</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Ibu Kandung</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Tanggal Pemeriksaan</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Berat Badan</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Tinggi Badan</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Usia</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Status Gizi</th>
                    <?php if (!$isIbu): ?>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Posyandu</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-600">Kader</th>
                    <?php endif; ?>
                    <?php if ($isAdmin): ?>
                        <th class="px-6 py-4 text-right font-bold text-gray-600">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                <?php if (empty($pemeriksaanList)): ?>
                    <tr>
                        <td colspan="<?php
                            $cols = 8;
                            if (!$isIbu) $cols += 2;
                            if ($isAdmin) $cols++;
                            echo $cols;
                        ?>" class="px-6 py-10 text-center text-gray-500">
                            Belum ada data pemeriksaan.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php 
                $no = (($data['current_page'] ?? 1) - 1) * ($data['per_halaman'] ?? 20) + 1;
                foreach ($pemeriksaanList as $p): 
                ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-500">
                            <?= $no++ ?>
                        </td>

                        <td class="px-6 py-4 font-bold text-gray-900">
                            <?= htmlspecialchars($p['nama_anak']) ?>
                            <span class="block text-xs font-normal text-gray-500">
                                <?= $p['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?>
                            </span>
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            <?= htmlspecialchars($p['nama_ibu']) ?>
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            <?= htmlspecialchars($p['tanggal_pemeriksaan_label']) ?>
                        </td>

                        <td class="px-6 py-4 font-bold text-gray-900">
                            <?= htmlspecialchars($p['berat_badan_label']) ?>
                        </td>

                        <td class="px-6 py-4 font-bold text-gray-900">
                            <?= htmlspecialchars($p['tinggi_badan_label']) ?>
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            <?= htmlspecialchars($p['usia_bulan']) ?> Bulan
                        </td>

                        <td class="px-6 py-4">
                            <?php
                            $status = $p['status_gizi'] ?? '-';
                            $class = 'bg-gray-100 text-gray-700';

                            if (strpos(strtolower($status), 'prioritas 3') !== false || strtolower($status) === 'normal') {
                                $class = 'bg-green-100 text-green-700';
                            } elseif (strpos(strtolower($status), 'prioritas 1') !== false || strtolower($status) === 'stunting' || strtolower($status) === 'gizi buruk') {
                                $class = 'bg-red-100 text-red-700';
                            } elseif (strpos(strtolower($status), 'prioritas 2') !== false || strtolower($status) === 'gizi kurang' || strtolower($status) === 'kurang') {
                                $class = 'bg-amber-100 text-amber-700';
                            } elseif (strtolower($status) === 'gizi lebih' || strtolower($status) === 'obesitas') {
                                $class = 'bg-purple-100 text-purple-700';
                            }
                            ?>
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold <?= $class; ?>">
                                <?= htmlspecialchars($status); ?>
                            </span>
                        </td>

                        <?php if (!$isIbu): ?>
                        <td class="px-6 py-4 text-gray-500 text-xs">
                            <?= htmlspecialchars($p['nama_posyandu'] ?? '-'); ?>
                        </td>

                        <td class="px-6 py-4 text-gray-500 text-xs">
                            <?= htmlspecialchars($p['nama_kader'] ?? '-'); ?>
                        </td>
                        <?php endif; ?>

                        <?php if ($isAdmin): ?>
                            <td class="px-6 py-4 text-right">
                                <a href="/transaksi/pemeriksaan/delete?id=<?= htmlspecialchars($p['id_pemeriksaan']); ?>" 
                                   onclick="return confirm('Hapus data pemeriksaan ini?')"
                                   class="inline-flex items-center justify-center rounded-xl bg-red-50 px-3 py-2 text-xs font-bold text-red-700 transition hover:bg-red-100">
                                   Hapus
                                </a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php
    $paginationData = [
        'halaman_aktif' => $data['current_page'] ?? 1,
        'total_halaman' => $data['total_pages'] ?? 1,
        'total_data' => $data['total_data'] ?? count($pemeriksaanList),
        'per_halaman' => $data['per_halaman'] ?? 20,
        'page_param' => 'page'
    ];

    require '../views/partials/pagination.php';
    ?>
<?php
};

if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    $renderPemeriksaanTable();
    exit;
}

ob_start();
?>

<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900">
                Pemeriksaan Anak (Posyandu)
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Catat dan pantau tumbuh kembang balita secara berkala.
            </p>
        </div>

        <?php if ($isKader): ?>
        <a href="/transaksi/pemeriksaan/create"
            class="inline-flex items-center justify-center rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-700">
            + Input Pemeriksaan Baru
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
                    Data Hasil Pemeriksaan
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Total data: <?= $data['total_data'] ?? count($pemeriksaanList); ?> pemeriksaan
                </p>
            </div>

            <div class="w-full lg:max-w-md">
                <?php
                $searchAction = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $searchPlaceholder = 'Cari nama anak, ibu, status gizi...';
                $searchTarget = 'tableResult';
                $searchParam = 'q';
                $pageParam = 'page';
                require '../views/partials/searchbar.php';
                ?>
            </div>
        </div>

        <div id="tableResult">
            <?php $renderPemeriksaanTable(); ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>
