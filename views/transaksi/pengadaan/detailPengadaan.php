<?php
$role = $_SESSION['role'] ?? '';
$isAdmin = defined('ROLE_ADMIN') ? $role === ROLE_ADMIN : strtolower($role) === 'admin';
$isPetani = defined('ROLE_PETANI') ? $role === ROLE_PETANI : strtolower($role) === 'petani';

$pageTitle = 'Lembar Detail Kontrak Kerja Sama';
$pageSubtitle = 'Rincian data kebutuhan logistik pangan dan legalitas pemenuhan mitra.';
$nota = $nota ?? [];
ob_start();
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="/transaksi/pengadaan"
            class="inline-flex items-center gap-2 rounded-2xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
            <i class="fa-solid fa-arrow-left text-xs"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-gray-900 border-b border-gray-50 pb-3">Daftar Komoditas yang Diminta
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 font-bold">
                            <tr>
                                <th class="px-4 py-3 text-left">Nama Komoditas</th>
                                <th class="px-4 py-3 text-center">Volume</th>
                                <th class="px-4 py-3 text-right">Harga Satuan</th>
                                <th class="px-4 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php
                            $totalKeseluruhan = 0;
                            foreach ($nota['details'] as $item):
                                $subtotalItem = $item['jumlah'] * $item['harga_satuan'];
                                $totalKeseluruhan += $subtotalItem;
                            ?>
                                <tr>
                                    <td class="px-4 py-3.5 font-semibold text-gray-900">
                                        <?= htmlspecialchars($item['nama_komoditas']) ?>
                                    </td>
                                    <td class="px-4 py-3.5 text-center text-gray-600">
                                        <?= number_format($item['jumlah'], 0, ',', '.') ?>
                                        <?= htmlspecialchars($item['satuan']) ?>
                                    </td>
                                    <td class="px-4 py-3.5 text-right text-gray-600">Rp
                                        <?= number_format($item['harga_satuan'], 0, ',', '.') ?>
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-bold text-gray-900">Rp
                                        <?= number_format($subtotalItem, 0, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="bg-teal-50/50 font-extrabold text-teal-900">
                                <td colspan="3" class="px-4 py-4 text-left rounded-l-2xl">Total Anggaran Pengadaan</td>
                                <td class="px-4 py-4 text-right rounded-r-2xl">Rp
                                    <?= number_format($totalKeseluruhan, 0, ',', '.') ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
                <h4 class="text-sm font-bold text-gray-900 mb-2">Catatan / Keterangan Tambahan:</h4>
                <p class="text-sm text-gray-600 bg-gray-50 p-4 rounded-2xl italic">
                    <?= !empty($nota['keterangan']) ? nl2br(htmlspecialchars($nota['keterangan'])) : 'Tidak ada catatan tambahan untuk kontrak pengadaan ini.'; ?>
                </p>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-gray-900 border-b border-gray-50 pb-2">Status & Dokumen</h3>
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Nomor
                        Kontrak</span>
                    <span
                        class="font-mono text-sm font-bold text-gray-900 bg-gray-50 px-3 py-1.5 rounded-xl block border border-gray-100">
                        <?= htmlspecialchars($nota['no_kontrak']) ?>
                    </span>
                </div>
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Gudang
                        Logistik</span>
                    <span class="text-sm text-gray-700 font-semibold block">
                        <?= htmlspecialchars($nota['nama_gudang']) ?>
                    </span>
                </div>
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Tanggal
                        Rilis</span>
                    <span class="text-sm text-gray-700 block">
                        <?= date('d F Y', strtotime($nota['tgl_pengadaan'])) ?>
                    </span>
                </div>
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Mitra
                        Penyanggup</span>
                    <span
                        class="text-sm font-bold <?= !empty($nota['id_petani']) ? 'text-teal-700' : 'text-amber-600' ?>">
                        <i
                            class="fa-solid <?= !empty($nota['id_petani']) ? 'fa-user-check' : 'fa-user-clock' ?> mr-1.5"></i>
                        <?= htmlspecialchars($nota['nama_petani'] ?? 'Terbuka (Mencari Petani)') ?>
                    </span>
                </div>
                <div class="pt-2">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-2">Tahapan Kerja
                        Sama</span>
                    <span
                        class="px-3 py-1.5 text-xs font-bold rounded-full inline-block <?= $nota['status_kontrak'] === 'Disetujui' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' ?>">
                        <?= htmlspecialchars($nota['status_kontrak']) ?>
                    </span>
                </div>
                <div class="pt-2">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-2">Status
                        Pembayaran</span>
                    <span
                        class="px-3 py-1.5 text-xs font-bold rounded-full inline-block <?= $nota['status_bayar'] === 'Lunas' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' ?>">
                        <?= htmlspecialchars($nota['status_bayar']) ?>
                    </span>
                </div>
                <?php if ($isAdmin && $nota['status_kontrak'] === 'Disetujui' && $nota['status_bayar'] === 'Pending'): ?>
                    <div class="pt-4 border-t border-gray-100">
                        <form action="/transaksi/pengadaan/lunasi" method="POST"
                            data-confirm
                            data-confirm-title="Lunasi kontrak pengadaan?"
                            data-confirm-message="Pastikan barang fisik dari petani sudah sampai di gudang sebelum kontrak dilunasi."
                            data-confirm-text="Ya, lunasi"
                            data-confirm-tone="success">

                            <input type="hidden" name="id_pengadaan"
                                value="<?= !empty($nota['id_pengadaan']) ? (int) $nota['id_pengadaan'] : (int) $_GET['id'] ?>">

                            <button type="submit"
                                class="w-full text-center rounded-2xl bg-teal-600 py-3 text-sm font-bold text-white transition hover:bg-teal-700 shadow-md shadow-teal-100">
                                Verifikasi & Lunasi Kontrak
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>