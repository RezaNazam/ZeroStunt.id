<?php
$penyerahan = $penyerahan ?? [];

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

ob_start();
?>

<div class="space-y-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">
                Penyerahan Bantuan Gizi
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Data seluruh penyerahan bantuan kepada ibu penerima.
            </p>
        </div>

        <!-- BUTTON -->
        <div class="flex justify-end">
            <a href="/transaksi/penyerahan/create"
                class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-3 rounded-xl font-bold">
                + Tambah Penyerahan
            </a>
        </div>
    </div>

    <!-- SUCCESS -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="rounded-2xl border border-green-100 bg-green-50 px-4 py-3 text-sm text-green-700">
            <?= htmlspecialchars($_SESSION['success']); ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- ERROR -->
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
            <?= htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- TABLE -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">

        <div class="overflow-x-auto">
            <table class="w-full text-sm">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">No</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Ibu</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Tanggal</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Paket Gizi</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Status</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Catatan</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    <?php if (empty($penyerahan)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                Belum ada data penyerahan.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php $index = 0; ?>

                    <?php foreach ($penyerahan as $p): ?>
                        <tr class="hover:bg-gray-50">
                            <?php $index++;

                            $dayOfWeek = date('D', strtotime($p['tanggal_penyerahan']));
                            $dayNames = [
                                'Sun' => 'Minggu',
                                'Mon' => 'Senin',
                                'Tue' => 'Selasa',
                                'Wed' => 'Rabu',
                                'Thu' => 'Kamis',
                                'Fri' => "Jum'at",
                                'Sat' => 'Sabtu'
                            ];
                            $dayName = $dayNames[$dayOfWeek] ?? '';
                            $monthName = date('F', strtotime($p['tanggal_penyerahan']));
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
                            $monthName = $monthNames[$monthName] ?? '';
                            $formattedDate = date('j', strtotime($p['tanggal_penyerahan'])) . ' ' . $monthName . ' ' . date('Y', strtotime($p['tanggal_penyerahan']));
                            $tanggalFormat = $dayName . ', ' . $formattedDate;
                            ?>

                            <td class="px-6 py-4 font-bold text-gray-900">
                                <?= htmlspecialchars($index) ?>
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                <div class="font-semibold text-gray-900">
                                    <?= htmlspecialchars($p['nama_ibu'] ?? '-') ?>
                                </div>

                                <a href="/transaksi/penyerahan/detail?id=<?= (int) $p['id_penyerahan']; ?>"
                                    class="mt-1 inline-flex text-xs font-bold text-teal-700 hover:text-teal-800">
                                    Lihat detail
                                </a>
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                <?= htmlspecialchars($tanggalFormat) ?>
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                <?php
                                $namaPaket = $p['nama_paket'] ?? 'Paket #' . ($p['id_paket'] ?? '-');
                                $badgePaket = $getBadgePaket($p);
                                ?>

                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold <?= $badgePaket; ?>">
                                    <?= htmlspecialchars($namaPaket); ?>
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-bold rounded-full
                                    <?= ($p['status_penyerahan'] ?? 'Diproses') === 'Diserahkan'
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-yellow-100 text-yellow-700' ?>">
                                    <?= htmlspecialchars($p['status_penyerahan'] ?? 'Diproses') ?>
                                </span>
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                <?= htmlspecialchars($p['catatan'] ?? '-') ?>
                            </td>

                            <td class="px-6 py-4">

                                <?php if (($p['status_penyerahan'] ?? 'Diproses') === 'Diproses'): ?>

                                    <form action="/transaksi/penyerahan/serahkan" method="POST"
                                        data-confirm
                                        data-confirm-title="Tandai bantuan diserahkan?"
                                        data-confirm-message="Status penyerahan akan berubah menjadi Diserahkan dan stok posyandu akan diperbarui."
                                        data-confirm-text="Ya, serahkan"
                                        data-confirm-tone="success">

                                        <input type="hidden"
                                            name="id_penyerahan"
                                            value="<?= (int) $p['id_penyerahan']; ?>">

                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200 hover:bg-green-200 transition">
                                            Tandai Diserahkan
                                        </button>
                                    </form>

                                <?php else: ?>

                                    <span class="text-green-600 font-bold text-xs">
                                        Selesai
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>
                    <?php endforeach; ?>

                </tbody>

            </table>
        </div>

    </div>

</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>