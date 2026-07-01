<?php

$pageTitle = 'Edit Paket Gizi';
$pageSubtitle = 'Atur isi bundling komoditas untuk penyerahan bantuan.';

$paket = $paket ?? [];
$komoditas = $komoditas ?? [];
$details = $paket['details'] ?? [];

ob_start();
?>

<div class="space-y-6">

    <div>
        <a href="/master/paket-gizi"
            class="inline-flex items-center rounded-2xl border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-600 hover:bg-gray-50">
            ← Kembali
        </a>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
            <?= htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="/master/paket-gizi/update" method="POST"
        class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm space-y-6">

        <input type="hidden" name="id_paket" value="<?= (int) ($paket['id_paket'] ?? 0); ?>">

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-bold text-gray-700">
                    Kode Prioritas
                </label>

                <input type="text" readonly
                    value="<?= htmlspecialchars($paket['kode_prioritas'] ?? ''); ?>"
                    class="w-full rounded-2xl border border-gray-200 bg-gray-100 px-4 py-3 text-gray-500">
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-gray-700">
                    Nama Paket
                </label>

                <input type="text" name="nama_paket" required
                    value="<?= htmlspecialchars($paket['nama_paket'] ?? ''); ?>"
                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 outline-none focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">
            </div>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-gray-700">
                Deskripsi
            </label>

            <textarea name="deskripsi" rows="3"
                class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 outline-none resize-none focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100"><?= htmlspecialchars($paket['deskripsi'] ?? ''); ?></textarea>
        </div>

        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1"
                <?= !empty($paket['is_active']) ? 'checked' : ''; ?>
                class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500">

            <span class="text-sm font-bold text-gray-700">
                Paket aktif dan bisa dipilih saat penyerahan
            </span>
        </label>

        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="font-extrabold text-gray-900">
                        Detail Komoditas Paket
                    </h2>
                    <p class="text-sm text-gray-500">
                        Atur komoditas dan jumlah untuk paket ini.
                    </p>
                </div>

                <button type="button" id="addDetail"
                    class="rounded-xl bg-teal-50 px-3 py-2 text-xs font-bold text-teal-700 hover:bg-teal-100">
                    + Tambah
                </button>
            </div>

            <div id="detailWrapper" class="space-y-3">
                <?php if (empty($details)): ?>
                    <div class="detail-row grid gap-3 md:grid-cols-[1fr_160px_90px]">
                        <select name="id_komoditas[]" required
                            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-teal-500">
                            <option value="" disabled selected>Pilih Komoditas</option>

                            <?php foreach ($komoditas as $item): ?>
                                <option value="<?= (int) $item['id_komoditas']; ?>">
                                    <?= htmlspecialchars($item['nama_komoditas']); ?>
                                    (<?= htmlspecialchars($item['satuan']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <input type="number" name="jumlah[]" step="0.01" min="0.01" required
                            placeholder="Jumlah"
                            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-teal-500">

                        <button type="button"
                            class="removeDetail rounded-xl bg-red-50 px-3 py-2 text-xs font-bold text-red-700 hover:bg-red-100">
                            Hapus
                        </button>
                    </div>
                <?php else: ?>
                    <?php foreach ($details as $detail): ?>
                        <div class="detail-row grid gap-3 md:grid-cols-[1fr_160px_90px]">
                            <select name="id_komoditas[]" required
                                class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-teal-500">
                                <option value="" disabled>Pilih Komoditas</option>

                                <?php foreach ($komoditas as $item): ?>
                                    <option value="<?= (int) $item['id_komoditas']; ?>"
                                        <?= (int) $item['id_komoditas'] === (int) $detail['id_komoditas'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($item['nama_komoditas']); ?>
                                        (<?= htmlspecialchars($item['satuan']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <input type="number" name="jumlah[]" step="0.01" min="0.01" required
                                value="<?= htmlspecialchars($detail['jumlah']); ?>"
                                placeholder="Jumlah"
                                class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-teal-500">

                            <button type="button"
                                class="removeDetail rounded-xl bg-red-50 px-3 py-2 text-xs font-bold text-red-700 hover:bg-red-100">
                                Hapus
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <button type="submit"
            class="w-full rounded-2xl bg-teal-600 px-5 py-3 font-bold text-white transition hover:bg-teal-700">
            Simpan Paket
        </button>
    </form>
</div>

<script>
    const detailWrapper = document.getElementById('detailWrapper');
    const addDetail = document.getElementById('addDetail');

    addDetail.addEventListener('click', function () {
        const firstRow = detailWrapper.querySelector('.detail-row');
        const newRow = firstRow.cloneNode(true);

        newRow.querySelectorAll('select, input').forEach(function (input) {
            input.value = '';
        });

        detailWrapper.appendChild(newRow);
    });

    detailWrapper.addEventListener('click', function (event) {
        if (!event.target.classList.contains('removeDetail')) {
            return;
        }

        const rows = detailWrapper.querySelectorAll('.detail-row');

        if (rows.length <= 1) {
            rows[0].querySelectorAll('select, input').forEach(function (input) {
                input.value = '';
            });
            return;
        }

        event.target.closest('.detail-row').remove();
    });
</script>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>