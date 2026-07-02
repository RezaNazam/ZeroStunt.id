<?php

$pageTitle = 'Paket Gizi';
$pageSubtitle = 'Kelola bundling komoditas berdasarkan prioritas penyerahan.';

$pakets = $pakets ?? [];

ob_start();
?>

<div class="space-y-6">

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="rounded-2xl border border-green-100 bg-green-50 px-4 py-3 text-sm font-bold text-green-700">
            <?= htmlspecialchars($_SESSION['success']); ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
            <?= htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="grid gap-5 lg:grid-cols-3">
        <?php foreach ($pakets as $paket): ?>
            <?php
            $badgeClass = 'bg-teal-50 text-teal-700';

            if (($paket['kode_prioritas'] ?? '') === 'PRIORITAS_1') {
                $badgeClass = 'bg-red-50 text-red-700';
            } elseif (($paket['kode_prioritas'] ?? '') === 'PRIORITAS_2') {
                $badgeClass = 'bg-amber-50 text-amber-700';
            } elseif (($paket['kode_prioritas'] ?? '') === 'PRIORITAS_3') {
                $badgeClass = 'bg-green-50 text-green-700';
            }
            ?>

            <div class="rounded-3xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 p-6">
                    <div class="mb-4 flex items-start justify-between gap-3">
                        <span class="rounded-full px-3 py-1 text-xs font-extrabold <?= $badgeClass; ?>">
                            <?= htmlspecialchars($paket['kode_prioritas']); ?>
                        </span>

                        <span class="rounded-full px-3 py-1 text-xs font-bold <?= !empty($paket['is_active']) ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'; ?>">
                            <?= !empty($paket['is_active']) ? 'Aktif' : 'Nonaktif'; ?>
                        </span>
                    </div>

                    <h2 class="text-xl font-extrabold text-gray-900">
                        <?= htmlspecialchars($paket['nama_paket']); ?>
                    </h2>

                    <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                        <?= htmlspecialchars($paket['deskripsi'] ?: 'Belum ada deskripsi paket.'); ?>
                    </p>
                </div>

                <div class="p-6">
                    <h3 class="mb-3 text-sm font-bold text-gray-900">
                        Isi Paket
                    </h3>

                    <?php if (empty($paket['details'])): ?>
                        <div class="rounded-2xl bg-gray-50 px-4 py-4 text-sm text-gray-500">
                            Paket belum memiliki komoditas.
                        </div>
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php foreach ($paket['details'] as $detail): ?>
                                <div class="flex items-center justify-between gap-3 rounded-2xl bg-gray-50 px-4 py-3">
                                    <span class="text-sm font-bold text-gray-800">
                                        <?= htmlspecialchars($detail['nama_komoditas']); ?>
                                    </span>

                                    <span class="text-sm font-extrabold text-teal-700">
                                        <?= number_format((float) $detail['jumlah'], 2, ',', '.'); ?>
                                        <?= htmlspecialchars($detail['satuan']); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <a href="/master/paket-gizi/edit?id=<?= (int) $paket['id_paket']; ?>"
                        class="mt-5 inline-flex w-full items-center justify-center rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-700">
                        Edit Isi Paket
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>