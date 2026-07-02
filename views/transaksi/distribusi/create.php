<?php
$data = $data ?? [];

$pageTitle = 'Buat Distribusi Stok';
$pageSubtitle = 'Buat transaksi distribusi stok pangan dari gudang asal ke gudang tujuan.';

$gudangPusat = $data['gudang_pusat'] ?? [];
$gudangPosyandu = $data['gudang_posyandu'] ?? [];
$komoditas = $data['komoditas'] ?? [];

ob_start();
?>

<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900">
                Buat Distribusi Baru
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Pilih gudang asal, gudang tujuan, dan komoditas yang akan didistribusikan.
            </p>
        </div>

        <a href="/transaksi/distribusi"
            class="inline-flex items-center justify-center rounded-2xl border border-gray-200 bg-white px-5 py-3 text-sm font-bold text-gray-700 transition hover:bg-gray-50">
            Kembali
        </a>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            <?= htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="/transaksi/distribusi/store" method="POST" class="space-y-6">
        <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
            <h3 class="mb-5 text-lg font-extrabold text-gray-900">
                Informasi Distribusi
            </h3>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-bold text-gray-700">
                        Gudang Asal
                    </label>
                    <select name="id_gudang_asal"
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:bg-white">
                        <option value="">-- Pilih Gudang Pusat / Puskesmas --</option>

                        <?php foreach ($gudangPusat as $gudang): ?>
                            <option value="<?= htmlspecialchars($gudang['id_gudang']); ?>">
                                <?= htmlspecialchars($gudang['nama_gudang']); ?>
                                - <?= htmlspecialchars($gudang['lokasi_gudang'] ?? '-'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-gray-700">
                        Gudang Tujuan
                    </label>
                    <select name="id_gudang_tujuan"
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:bg-white">
                        <option value="">-- Pilih Posyandu Tujuan --</option>

                        <?php foreach ($gudangPosyandu as $gudang): ?>
                            <option value="<?= htmlspecialchars($gudang['id_gudang']); ?>">
                                <?= htmlspecialchars($gudang['nama_gudang']); ?>
                                - <?= htmlspecialchars($gudang['lokasi_gudang'] ?? '-'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-gray-700">
                        Tanggal Distribusi
                    </label>
                    <input type="date" name="tanggal_distribusi" value="<?= date('Y-m-d'); ?>"
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:bg-white">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-gray-700">
                        Catatan
                    </label>
                    <input type="text" name="catatan"
                        placeholder="Opsional..."
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:bg-white">
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-extrabold text-gray-900">
                        Detail Komoditas
                    </h3>
                    <p class="text-sm text-gray-500">
                        Tambahkan komoditas yang akan dikirim.
                    </p>
                </div>

                <button type="button" id="addDetail"
                    class="rounded-2xl bg-teal-50 px-4 py-2 text-sm font-bold text-teal-700 hover:bg-teal-100">
                    + Tambah Baris
                </button>
            </div>

            <div id="detailWrapper" class="space-y-3">
                <div class="grid gap-3 md:grid-cols-[1fr_180px_80px]">
                    <select name="id_komoditas[]"
                        class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:bg-white">
                        <option value="">-- Pilih Komoditas --</option>
                        <?php foreach ($komoditas as $item): ?>
                            <option value="<?= htmlspecialchars($item['id_komoditas']); ?>">
                                <?= htmlspecialchars($item['nama_komoditas']); ?>
                                <?= !empty($item['satuan']) ? '(' . htmlspecialchars($item['satuan']) . ')' : ''; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <input type="number" name="jumlah[]" min="0" step="0.01" placeholder="Jumlah"
                        class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:bg-white">

                    <button type="button"
                        class="removeDetail rounded-2xl bg-red-50 px-4 py-3 text-sm font-bold text-red-700 hover:bg-red-100">
                        Hapus
                    </button>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="/transaksi/distribusi"
                class="rounded-2xl border border-gray-200 bg-white px-6 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50">
                Batal
            </a>

            <button type="submit"
                class="rounded-2xl bg-teal-600 px-6 py-3 text-sm font-bold text-white hover:bg-teal-700">
                Simpan Distribusi
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addButton = document.getElementById('addDetail');
        const wrapper = document.getElementById('detailWrapper');

        addButton.addEventListener('click', function() {
            const firstRow = wrapper.querySelector('.grid');
            const newRow = firstRow.cloneNode(true);

            newRow.querySelectorAll('select, input').forEach(function(input) {
                input.value = '';
            });

            wrapper.appendChild(newRow);
        });

        wrapper.addEventListener('click', function(event) {
            const button = event.target.closest('.removeDetail');
            if (!button) return;

            const rows = wrapper.querySelectorAll('.grid');

            if (rows.length <= 1) {
                rows[0].querySelectorAll('select, input').forEach(function(input) {
                    input.value = '';
                });
                return;
            }

            button.closest('.grid').remove();
        });
    });
</script>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
