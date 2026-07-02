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

// SINKRONISASI ASLI: Proses pemisahan data antara kontrak pending dengan yang sudah diverifikasi (lunas)
$kontrak_pending = [];
$kontrak_lunas = [];

foreach ($all_pengadaan as $p) {
    $statusBayar = $p['status_bayar'] ?? '';
    $statusKontrak = $p['status_kontrak'] ?? '';

    if ($statusKontrak === 'Dibatalkan' || $statusBayar === 'Lunas') {
        $kontrak_lunas[] = $p;
    } else {
        $kontrak_pending[] = $p;
    }
}

usort($kontrak_lunas, function ($a, $b) {
    $aDibatalkan = ($a['status_kontrak'] ?? '') === 'Dibatalkan';
    $bDibatalkan = ($b['status_kontrak'] ?? '') === 'Dibatalkan';

    // Yang dibatalkan turun ke bawah
    if ($aDibatalkan !== $bDibatalkan) {
        return $aDibatalkan <=> $bDibatalkan;
    }

    // Di dalam grup masing-masing, tetap urut terbaru dulu
    return ((int) ($b['id_pengadaan'] ?? 0)) <=> ((int) ($a['id_pengadaan'] ?? 0));
});

$renderAdminPengadaanTable = function ($daftar_kontrak) use ($data, $kontrak_lunas) {
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
                    <th class="px-6 py-4 text-center font-bold text-gray-600">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                <?php if (empty($daftar_kontrak)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                            Data kontrak pengadaan tidak ditemukan.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($daftar_kontrak as $p): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono font-bold text-gray-900">
                            <?= htmlspecialchars($p['no_kontrak'] ?? '') ?>
                        </td>

                        <td class="px-6 py-4 text-gray-500">
                            <?= htmlspecialchars($p['nama_gudang'] ?? '') ?>
                        </td>

                        <td class="px-6 py-4">
                            <ul class="list-disc list-inside text-xs text-gray-600 space-y-1">
                                <?php
                                $hitungTotalNilaiAdmin = 0;
                                foreach (($p['details'] ?? []) as $det):
                                    // SINKRONISASI ASLI: Menggunakan indeks 'jumlah' dan akumulasi kalkulasi total nilai
                                    $qty = (float) ($det['jumlah'] ?? 0);
                                    $harga = (float) ($det['harga_satuan'] ?? 0);
                                    $hitungTotalNilaiAdmin += ($qty * $harga);
                                ?>
                                    <li>
                                        <?= htmlspecialchars($det['nama_komoditas'] ?? '') ?>
                                        (<?= number_format($qty, 0, ',', '.') ?> <?= htmlspecialchars($det['satuan'] ?? 'Kg') ?>)
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>

                        <td class="px-6 py-4 text-gray-700">
                            <?= htmlspecialchars($p['nama_petani_label'] ?? $p['nama_petani'] ?? 'Belum Ada (Lowongan)') ?>
                        </td>

                        <td class="px-6 py-4 font-bold text-gray-900">
                            Rp <?= number_format($hitungTotalNilaiAdmin, 0, ',', '.') ?>
                        </td>

                        <td class="px-6 py-4">
                            <?php
                            $statusBayar = $p['status_bayar'] ?? '';
                            $statusKontrak = $p['status_kontrak'] ?? '';
                            ?>

                            <?php if ($statusKontrak === 'Dibatalkan'): ?>

                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700">
                                    Dibatalkan
                                </span>

                            <?php elseif ($statusBayar === 'Lunas'): ?>

                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700">
                                    Lunas
                                </span>

                            <?php else: ?>

                                <span class="px-2.5 py-1 text-xs font-bold rounded-full <?= $statusKontrak === 'Disetujui' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' ?>">
                                    <?= htmlspecialchars($statusKontrak ?: '-'); ?>
                                </span>

                            <?php endif; ?>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <a href="/transaksi/pengadaan/detail?id=<?= $p['id_pengadaan'] ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-600 transition hover:bg-teal-50 hover:text-teal-600 hover:border-teal-200 shadow-sm" title="Lihat Detail">
                                <i class="fa-solid fa-eye text-sm"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
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
                            <?= htmlspecialchars($item['no_kontrak'] ?? '') ?><br>
                            <span class="text-xs text-gray-400 font-normal">
                                <?= htmlspecialchars($item['nama_gudang'] ?? '') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <ul class="text-xs text-gray-600 list-disc list-inside space-y-0.5">
                                <?php
                                $hitungTotalNilaiAvail = 0;
                                foreach (($item['details'] ?? []) as $det):
                                    $qty = (float) ($det['jumlah'] ?? 0);
                                    $harga = (float) ($det['harga_satuan'] ?? 0);
                                    $hitungTotalNilaiAvail += ($qty * $harga);
                                ?>
                                    <li>
                                        <?= htmlspecialchars($det['nama_komoditas'] ?? '') ?>:
                                        <strong><?= number_format($qty, 0, ',', '.') ?> <?= htmlspecialchars($det['satuan'] ?? 'Kg') ?></strong>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-900">
                            Rp <?= number_format($hitungTotalNilaiAvail, 0, ',', '.') ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="/transaksi/pengadaan/ambil" method="post">
                                <input type="hidden" name="id_pengadaan" value="<?= $item['id_pengadaan'] ?>">
                                <button type="submit" class="rounded-xl bg-teal-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-teal-700">
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
                    <th class="px-6 py-4 text-center font-bold text-gray-600">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                <?php if (empty($taken)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                            Anda belum mengambil pengadaan apa pun.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($taken as $item): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono font-bold text-gray-900">
                            <?= htmlspecialchars($item['no_kontrak'] ?? '') ?>
                        </td>
                        <td class="px-6 py-4">
                            <ul class="text-xs text-gray-600 space-y-0.5">
                                <?php
                                $hitungTotalNilaiTaken = 0;
                                foreach (($item['details'] ?? []) as $det):
                                    $qty = (float) ($det['jumlah'] ?? 0);
                                    $harga = (float) ($det['harga_satuan'] ?? 0);
                                    $hitungTotalNilaiTaken += ($qty * $harga);
                                ?>
                                    <li>- <?= htmlspecialchars($det['nama_komoditas'] ?? '') ?> (<?= number_format($qty, 0, ',', '.') ?>)</li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td class="px-6 py-4 text-gray-900 font-bold">
                            Rp <?= number_format($hitungTotalNilaiTaken, 0, ',', '.') ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold <?= ($item['status_bayar'] ?? '') === 'Lunas' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                <?= htmlspecialchars($item['status_bayar'] ?? '') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="/transaksi/pengadaan/detail?id=<?= $item['id_pengadaan'] ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-700 transition hover:bg-teal-100 shadow-sm">
                                <i class="fa-solid fa-eye text-sm"></i>
                            </a>
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

// Handle Ajax request
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    $target = $_GET['ajax_target'] ?? '';
    if ($isAdmin && $target === 'tableResult') {
        $renderAdminPengadaanTable($all_pengadaan);
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
                <p class="text-sm text-gray-500 mt-1">Daftar pemantauan seluruh transaksi pasokan pangan dari petani lokal.</p>
            </div>
            <a href="/transaksi/pengadaan/buat" class="inline-flex items-center justify-center gap-x-2 rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-700">
                <span>+</span>
                <span>&nbsp;Buat Pengadaan Baru</span>
            </a>
        </div>

        <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4 bg-gray-50/50">
                <h3 class="text-sm font-bold text-amber-800 flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Kontrak Menunggu Verifikasi Gudang
                </h3>
            </div>
            <?php $renderAdminPengadaanTable($kontrak_pending); ?>
        </div>

        <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm mt-8">
            <div class="border-b border-gray-100 px-6 py-4 bg-gray-50/50">
                <h3 class="text-sm font-bold text-green-800 flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-green-500"></span>
                    Riwayat Pasokan Kontrak Selesai / Dibatalkan
                </h3>
            </div>
            <?php $renderAdminPengadaanTable($kontrak_lunas); ?>

            <div class="flex items-center justify-between border-t border-gray-100 px-6 py-4 bg-gray-50">
                <span class="text-xs text-gray-500">Halaman <strong><?= $data['current_page'] ?? 1 ?></strong> dari <strong><?= $data['total_pages'] ?? 1 ?></strong></span>
                <div class="inline-flex gap-2">
                    <?php if (($data['current_page'] ?? 1) > 1): ?>
                        <a href="/transaksi/pengadaan?page=<?= $data['current_page'] - 1 ?>" class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 transition hover:bg-gray-50">Previous</a>
                    <?php endif; ?>
                    <?php if (($data['current_page'] ?? 1) < ($data['total_pages'] ?? 1)): ?>
                        <a href="/transaksi/pengadaan?page=<?= $data['current_page'] + 1 ?>" class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 transition hover:bg-gray-50">Next</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($isPetani): ?>
        <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-5">
                <h3 class="text-lg font-extrabold text-gray-900">Lowongan Pengadaan Tersedia</h3>
                <p class="text-sm text-gray-500 mt-1">Daftar pasokan pangan komoditas yang dibutuhkan Puskesmas.</p>
            </div>
            <div id="availableTable">
                <?php $renderAvailableTable(); ?>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm mt-8">
            <div class="border-b border-gray-100 px-6 py-5">
                <h3 class="text-lg font-extrabold text-gray-900">Kontrak Kerja Sama Saya</h3>
                <p class="text-sm text-gray-500 mt-1">Daftar pengadaan yang telah Anda ambil dan sedang dipasok.</p>
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