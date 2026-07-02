<?php
$pageTitle = 'Detail Penyerahan Bantuan';
$pageSubtitle = 'Rincian paket bantuan gizi yang diserahkan kepada penerima.';

$penyerahan = $penyerahan ?? [];
$details = $penyerahan['details'] ?? [];

$role = $_SESSION['role'] ?? '';
$isIbu = defined('ROLE_IBU') ? $role === ROLE_IBU : strtolower($role) === 'ibu';

$backUrl = $isIbu ? '/master/ibu/histori-bantuan' : '/transaksi/penyerahan';

$getBadgeStatus = function ($status) {
    $status = strtolower(trim($status ?? ''));

    if (str_contains($status, 'diserahkan')) {
        return 'bg-green-100 text-green-700';
    }

    if (str_contains($status, 'diproses')) {
        return 'bg-amber-100 text-amber-700';
    }

    if (str_contains($status, 'batal')) {
        return 'bg-red-100 text-red-700';
    }

    return 'bg-gray-100 text-gray-700';
};

$getBadgePrioritas = function ($prioritas) {
    if ((int) $prioritas === 1) {
        return 'bg-red-100 text-red-700';
    }

    if ((int) $prioritas === 2) {
        return 'bg-amber-100 text-amber-700';
    }

    return 'bg-green-100 text-green-700';
};

ob_start();
?>

<div class="space-y-6">
    <div>
        <a href="<?= htmlspecialchars($backUrl); ?>"
            class="inline-flex items-center gap-2 rounded-2xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-start justify-between gap-4 border-b border-gray-100 pb-4">
                    <div>
                        <h2 class="text-xl font-extrabold text-gray-900">
                            Isi Paket Bantuan
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Daftar komoditas yang diberikan kepada penerima.
                        </p>
                    </div>

                    <span class="px-3 py-1 rounded-full text-xs font-bold <?= $getBadgeStatus($penyerahan['status_penyerahan'] ?? '-'); ?>">
                        <?= htmlspecialchars($penyerahan['status_penyerahan'] ?? '-'); ?>
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 font-bold">
                            <tr>
                                <th class="px-4 py-3 text-left">Komoditas</th>
                                <th class="px-4 py-3 text-right">Jumlah</th>
                                <th class="px-4 py-3 text-left">Satuan</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($details)): ?>
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada detail komoditas.
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($details as $item): ?>
                                <tr>
                                    <td class="px-4 py-3.5 font-semibold text-gray-900">
                                        <?= htmlspecialchars($item['nama_komoditas'] ?? '-'); ?>
                                    </td>

                                    <td class="px-4 py-3.5 text-right font-bold text-teal-700">
                                        <?= number_format((float) ($item['jumlah'] ?? 0), 1, ',', '.'); ?>
                                    </td>

                                    <td class="px-4 py-3.5 text-gray-600">
                                        <?= htmlspecialchars($item['satuan'] ?? '-'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="text-base font-bold text-gray-900 mb-3">
                    Catatan Penyerahan
                </h3>

                <p class="rounded-2xl bg-gray-50 p-4 text-sm text-gray-600 italic">
                    <?= !empty($penyerahan['catatan'])
                        ? nl2br(htmlspecialchars($penyerahan['catatan']))
                        : 'Tidak ada catatan tambahan.'; ?>
                </p>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-gray-900 border-b border-gray-50 pb-2">
                    Informasi Penyerahan
                </h3>

                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">
                        Nomor Penyerahan
                    </span>
                    <span class="font-mono text-sm font-bold text-gray-900 bg-gray-50 px-3 py-1.5 rounded-xl block border border-gray-100">
                        <?= htmlspecialchars($penyerahan['no_penyerahan'] ?? '-'); ?>
                    </span>
                </div>

                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">
                        Tanggal Penyerahan
                    </span>
                    <span class="text-sm text-gray-700 font-semibold">
                        <?= !empty($penyerahan['tanggal_penyerahan'])
                            ? date('d F Y', strtotime($penyerahan['tanggal_penyerahan']))
                            : '-'; ?>
                    </span>
                </div>

                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">
                        Posyandu
                    </span>
                    <span class="text-sm text-gray-700 font-semibold">
                        <?= htmlspecialchars($penyerahan['nama_posyandu'] ?? '-'); ?>
                    </span>
                </div>

                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">
                        Paket Gizi
                    </span>
                    <span class="text-sm font-bold text-teal-700">
                        <?= htmlspecialchars($penyerahan['nama_paket'] ?? '-'); ?>
                    </span>
                </div>
            </div>

            <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-gray-900 border-b border-gray-50 pb-2">
                    Penerima Bantuan
                </h3>

                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">
                        Ibu Penerima
                    </span>
                    <span class="text-sm font-bold text-gray-900">
                        <?= htmlspecialchars($penyerahan['nama_ibu'] ?? '-'); ?>
                    </span>
                </div>

                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">
                        Anak
                    </span>
                    <span class="text-sm font-bold text-gray-900">
                        <?= htmlspecialchars($penyerahan['nama_anak'] ?? '-'); ?>
                    </span>
                </div>

                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">
                        Status Gizi
                    </span>
                    <span class="text-sm text-gray-700">
                        <?= htmlspecialchars($penyerahan['st_gizi_skrg'] ?? '-'); ?>
                    </span>
                </div>

                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-2">
                        Skala Prioritas
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold <?= $getBadgePrioritas($penyerahan['skala_prioritas'] ?? 3); ?>">
                        Prioritas <?= htmlspecialchars($penyerahan['skala_prioritas'] ?? '-'); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>