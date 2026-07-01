<?php

$pageTitle = 'Riwayat Pendapatan';
$pageSubtitle = 'Riwayat pemasukan dari hasil pengadaan komoditas.';

$riwayat = $riwayat ?? [];

$formatRupiah = function ($value) {
    return 'Rp ' . number_format((float) $value, 0, ',', '.');
};

$totalPendapatan = 0;
$totalPending = 0;
$totalTransaksi = count($riwayat);
$totalLunas = 0;

foreach ($riwayat as $item) {
    $nilai = (float) ($item['total_nilai'] ?? 0);

    if (($item['status_bayar'] ?? '') === 'Lunas') {
        $totalPendapatan += $nilai;
        $totalLunas++;
    } else {
        $totalPending += $nilai;
    }
}

ob_start();
?>

<div class="space-y-6">

    <div class="grid md:grid-cols-4 gap-4">
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
            <p class="text-sm text-gray-500">Pendapatan Masuk</p>
            <h2 class="text-3xl font-extrabold text-green-600 mt-2">
                <?= $formatRupiah($totalPendapatan); ?>
            </h2>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
            <p class="text-sm text-gray-500">Menunggu Pembayaran</p>
            <h2 class="text-3xl font-extrabold text-amber-500 mt-2">
                <?= $formatRupiah($totalPending); ?>
            </h2>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
            <p class="text-sm text-gray-500">Total Kontrak</p>
            <h2 class="text-3xl font-extrabold text-teal-600 mt-2">
                <?= $totalTransaksi; ?>
            </h2>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
            <p class="text-sm text-gray-500">Kontrak Lunas</p>
            <h2 class="text-3xl font-extrabold text-blue-600 mt-2">
                <?= $totalLunas; ?>
            </h2>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-extrabold text-gray-900">
                Riwayat Pendapatan
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Data pemasukan dihitung dari detail kontrak pengadaan yang diambil petani.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Tanggal</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">No Kontrak</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Gudang</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Komoditas</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Nilai Kontrak</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Status Bayar</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($riwayat)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                Belum ada riwayat pendapatan.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($riwayat as $item): ?>
                        <?php
                        $details = [];

                        if (!empty($item['detail_komoditas'])) {
                            $rows = explode(';;', $item['detail_komoditas']);

                            foreach ($rows as $row) {
                                $parts = explode('||', $row);

                                $details[] = [
                                    'nama' => $parts[0] ?? '-',
                                    'jumlah' => (float) ($parts[1] ?? 0),
                                    'satuan' => $parts[2] ?? '-',
                                    'harga' => (float) ($parts[3] ?? 0),
                                    'subtotal' => (float) ($parts[4] ?? 0),
                                ];
                            }
                        }

                        $isLunas = ($item['status_bayar'] ?? '') === 'Lunas';
                        ?>

                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-700">
                                <?= date('d M Y', strtotime($item['tgl_pengadaan'])); ?>
                            </td>

                            <td class="px-6 py-4">
                                <p class="font-mono font-bold text-gray-900">
                                    <?= htmlspecialchars($item['no_kontrak'] ?? '-'); ?>
                                </p>
                                <a href="/transaksi/pengadaan/detail?id=<?= (int) $item['id_pengadaan']; ?>"
                                    class="text-xs font-bold text-teal-700 hover:text-teal-800">
                                    Lihat detail
                                </a>
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                <?= htmlspecialchars($item['nama_gudang'] ?? '-'); ?>
                            </td>

                            <td class="px-6 py-4">
                                <div class="space-y-2">
                                    <?php foreach ($details as $detail): ?>
                                        <div class="rounded-xl bg-gray-50 px-3 py-2">
                                            <p class="font-bold text-gray-900">
                                                <?= htmlspecialchars($detail['nama']); ?>
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                <?= number_format($detail['jumlah'], 0, ',', '.'); ?>
                                                <?= htmlspecialchars($detail['satuan']); ?>
                                                ×
                                                <?= $formatRupiah($detail['harga']); ?>
                                            </p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </td>

                            <td class="px-6 py-4 font-extrabold <?= $isLunas ? 'text-green-600' : 'text-amber-600'; ?>">
                                <?= $formatRupiah($item['total_nilai'] ?? 0); ?>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold <?= $isLunas ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'; ?>">
                                    <?= htmlspecialchars($item['status_bayar'] ?? '-'); ?>
                                </span>
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