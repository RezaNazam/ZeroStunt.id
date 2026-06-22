<?php
$role = $_SESSION['role'] ?? '';
$isAdmin = defined('ROLE_ADMIN') ? $role === ROLE_ADMIN : strtolower($role) === 'admin';
$isPetani = defined('ROLE_PETANI') ? $role === ROLE_PETANI : strtolower($role) === 'petani';

$pageTitle = $isAdmin ? 'Manajemen Pengadaan' : 'Pengadaan Komoditas';
$pageSubtitle = $isAdmin ? 'Kelola semua requirement pengadaan komoditas.' : 'Lihat dan ambil pasokan komoditas yang tersedia.';

$available = $data['available'] ?? [];
$taken = $data['taken'] ?? [];
$all_pengadaan = $data['all_pengadaan'] ?? [];

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
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">Belum ada transaksi pengadaan
                                    dibuat.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($all_pengadaan as $p): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-bold text-gray-900"><?= htmlspecialchars($p['no_kontrak']) ?></td>
                                <td class="px-6 py-4 text-gray-500"><?= htmlspecialchars($p['nama_gudang']) ?></td>
                                <td class="px-6 py-4">
                                    <ul class="list-disc list-inside text-xs text-gray-600">
                                        <?php foreach ($p['details'] as $det): ?>
                                            <li><?= htmlspecialchars($det['nama_komoditas']) ?>
                                                (<?= htmlspecialchars($det['jumlah']) ?>             <?= htmlspecialchars($det['satuan']) ?>)
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    Platini
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    <?= htmlspecialchars($p['nama_petani'] ?? 'Belum Ada (Lowongan)') ?></td>
                                <td class="px-6 py-4 font-medium text-gray-900">Rp
                                    <?= number_format($p['total_bayar'], 0, ',', '.') ?></td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 text-xs font-bold rounded-full <?= $p['status_kontrak'] === 'Disetujui' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' ?>">
                                        <?= htmlspecialchars($p['status_kontrak']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between border-t border-gray-100 px-6 py-4 bg-gray-50">
                <span class="text-xs text-gray-500">
                    Halaman <strong><?= $data['current_page'] ?? 1 ?></strong> dari
                    <strong><?= $data['total_pages'] ?? 1 ?></strong>
                </span>
                <div class="inline-flex gap-2">
                    <?php if (($data['current_page'] ?? 1) > 1): ?>
                        <a href="/transaksi/pengadaan?page=<?= $data['current_page'] - 1 ?>"
                            class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 transition hover:bg-gray-50">Previous</a>
                    <?php else: ?>
                        <button disabled
                            class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-1.5 text-xs font-bold text-gray-400 cursor-not-allowed">Previous</button>
                    <?php endif; ?>

                    <?php if (($data['current_page'] ?? 1) < ($data['total_pages'] ?? 1)): ?>
                        <a href="/transaksi/pengadaan?page=<?= $data['current_page'] + 1 ?>"
                            class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 transition hover:bg-gray-50">Next</a>
                    <?php else: ?>
                        <button disabled
                            class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-1.5 text-xs font-bold text-gray-400 cursor-not-allowed">Next</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($isPetani): ?>
        <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-5">
                <h3 class="text-lg font-extrabold text-gray-900"><i class="fa-solid fa-box-open text-teal-600 mr-2"></i>
                    Lowongan Pengadaan Tersedia</h3>
                <p class="text-sm text-gray-500 mt-1">Daftar pasokan pangan komoditas yang dibutuhkan Puskesmas dan siap
                    Anda penuhi.</p>
            </div>
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
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500">Saat ini belum ada requirement
                                    pengadaan yang dibuka oleh Admin.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($available as $item): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-bold text-gray-900">
                                    <?= htmlspecialchars($item['no_kontrak']) ?><br>
                                    <span
                                        class="text-xs text-gray-400 font-normal"><?= htmlspecialchars($item['nama_gudang']) ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <ul class="text-xs text-gray-600 list-disc list-inside">
                                        <?php foreach ($item['details'] as $det): ?>
                                            <li><?= htmlspecialchars($det['nama_komoditas']) ?>:
                                                <strong><?= htmlspecialchars($det['jumlah']) ?>
                                                    <?= htmlspecialchars($det['satuan']) ?></strong></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900">Rp
                                    <?= number_format($item['total_bayar'], 0, ',', '.') ?></td>
                                <td class="px-6 py-4 text-right">
                                    <form action="/transaksi/pengadaan/ambil" method="post">
                                        <input type="hidden" name="id_pengadaan" value="<?= $item['id_pengadaan'] ?>">
                                        <button type="submit"
                                            class="rounded-xl bg-teal-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-teal-700">Sanggupi
                                            / ACC</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between border-t border-gray-100 px-6 py-4 bg-gray-50">
                <span class="text-xs text-gray-500">
                    Lowongan: Halaman <strong><?= $data['current_page_avail'] ?? 1 ?></strong> dari
                    <strong><?= $data['total_pages_avail'] ?? 1 ?></strong>
                </span>
                <div class="inline-flex gap-2">
                    <?php if (($data['current_page_avail'] ?? 1) > 1): ?>
                        <a href="/transaksi/pengadaan?p_avail=<?= $data['current_page_avail'] - 1 ?>&p_taken=<?= $data['current_page_taken'] ?? 1 ?>"
                            class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 transition hover:bg-gray-50">Previous</a>
                    <?php else: ?>
                        <button disabled
                            class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-1.5 text-xs font-bold text-gray-400 cursor-not-allowed">Previous</button>
                    <?php endif; ?>

                    <?php if (($data['current_page_avail'] ?? 1) < ($data['total_pages_avail'] ?? 1)): ?>
                        <a href="/transaksi/pengadaan?p_avail=<?= $data['current_page_avail'] + 1 ?>&p_taken=<?= $data['current_page_taken'] ?? 1 ?>"
                            class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 transition hover:bg-gray-50">Next</a>
                    <?php else: ?>
                        <button disabled
                            class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-1.5 text-xs font-bold text-gray-400 cursor-not-allowed">Next</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-5">
                <h3 class="text-lg font-extrabold text-gray-900"><i
                        class="fa-solid fa-file-invoice-dollar text-amber-600 mr-2"></i> Kontrak Kerja Sama Saya</h3>
                <p class="text-sm text-gray-500 mt-1">Daftar pengadaan yang telah Anda ambil dan sedang Anda pasok jatahnya.
                </p>
            </div>
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
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500">Anda belum mengambil pengadaan apa
                                    pun.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($taken as $item): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-bold text-gray-900"><?= htmlspecialchars($item['no_kontrak']) ?></td>
                                <td class="px-6 py-4">
                                    <ul class="text-xs text-gray-600">
                                        <?php foreach ($item['details'] as $det): ?>
                                            <li>- <?= htmlspecialchars($det['nama_komoditas']) ?>
                                                (<?= htmlspecialchars($det['jumlah']) ?>)</li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>
                                <td class="px-6 py-4 text-gray-900 font-bold">Rp
                                    <?= number_format($item['total_bayar'], 0, ',', '.') ?></td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-bold <?= $item['status_bayar'] === 'Lunas' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                        <?= htmlspecialchars($item['status_bayar']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between border-t border-gray-100 px-6 py-4 bg-gray-50">
                <span class="text-xs text-gray-500">
                    Kontrak Saya: Halaman <strong><?= $data['current_page_taken'] ?? 1 ?></strong> dari
                    <strong><?= $data['total_pages_taken'] ?? 1 ?></strong>
                </span>
                <div class="inline-flex gap-2">
                    <?php if (($data['current_page_taken'] ?? 1) > 1): ?>
                        <a href="/transaksi/pengadaan?p_avail=<?= $data['current_page_avail'] ?? 1 ?>&p_taken=<?= $data['current_page_taken'] - 1 ?>"
                            class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 transition hover:bg-gray-50">Previous</a>
                    <?php else: ?>
                        <button disabled
                            class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-1.5 text-xs font-bold text-gray-400 cursor-not-allowed">Previous</button>
                    <?php endif; ?>

                    <?php if (($data['current_page_taken'] ?? 1) < ($data['total_pages_taken'] ?? 1)): ?>
                        <a href="/transaksi/pengadaan?p_avail=<?= $data['current_page_avail'] ?? 1 ?>&p_taken=<?= $data['current_page_taken'] + 1 ?>"
                            class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 transition hover:bg-gray-50">Next</a>
                    <?php else: ?>
                        <button disabled
                            class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-1.5 text-xs font-bold text-gray-400 cursor-not-allowed">Next</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>