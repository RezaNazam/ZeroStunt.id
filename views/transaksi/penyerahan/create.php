<?php
$ibus = $ibus ?? [];
$gudangs = $gudangs ?? [];
$komoditas = $komoditas ?? [];

ob_start();
?>

<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-extrabold text-gray-900">
            Tambah Penyerahan Bantuan
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Input data penyerahan bantuan gizi ke ibu penerima.
        </p>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
            <?= htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="/transaksi/penyerahan/store" method="POST"
        class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-5">

        <!-- IBU -->
        <div>
            <label class="block text-sm font-bold mb-2">Ibu Penerima</label>
            <select name="id_ibu" required
                class="w-full rounded-xl border border-gray-200 px-4 py-3">
                <option value="">Pilih Ibu</option>
                <?php foreach ($ibus as $ibu): ?>
                    <option value="<?= $ibu['id_ibu']; ?>">
                        <?= htmlspecialchars($ibu['nama_ibu']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- GUDANG / POSYANDU -->
        <div>
            <label class="block text-sm font-bold mb-2">Gudang / Posyandu</label>
            <select name="id_gudang" required
                class="w-full rounded-xl border border-gray-200 px-4 py-3">
                <option value="" disabled selected>Pilih Gudang Posyandu</option>

                <?php foreach ($gudangs as $gudang): ?>
                    <option value="<?= htmlspecialchars($gudang['id_gudang']); ?>">
                        <?= htmlspecialchars($gudang['nama_gudang']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- TANGGAL -->
        <div>
            <label class="block text-sm font-bold mb-2">Tanggal Penyerahan</label>
            <input type="date" name="tanggal_penyerahan" required
                class="w-full rounded-xl border border-gray-200 px-4 py-3">
        </div>

        <!-- DETAIL BANTUAN -->
        <div>
            <div class="mb-3 flex items-center justify-between">
                <label class="block text-sm font-bold">Detail Bantuan</label>

                <button type="button" id="addDetail"
                    class="rounded-xl bg-teal-50 px-3 py-2 text-xs font-bold text-teal-700 hover:bg-teal-100">
                    + Tambah Item
                </button>
            </div>

            <div id="detailWrapper" class="space-y-3">
                <div class="detail-row grid grid-cols-1 gap-3 md:grid-cols-[1fr_160px_90px]">
                    <select name="id_komoditas[]" required
                        class="w-full rounded-xl border border-gray-200 px-4 py-3">
                        <option value="" disabled selected>Pilih Komoditas</option>

                        <?php foreach ($komoditas as $item): ?>
                            <option value="<?= htmlspecialchars($item['id_komoditas']); ?>">
                                <?= htmlspecialchars($item['nama_komoditas']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <input type="number" name="jumlah[]" step="0.01" min="0.01" required
                        placeholder="Jumlah"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3">

                    <button type="button"
                        class="removeDetail rounded-xl bg-red-50 px-3 py-2 text-xs font-bold text-red-700 hover:bg-red-100">
                        Hapus
                    </button>
                </div>
            </div>
        </div>

        <!-- CATATAN -->
        <div>
            <label class="block text-sm font-bold mb-2">Catatan</label>
            <textarea name="catatan" rows="3"
                class="w-full rounded-xl border border-gray-200 px-4 py-3"></textarea>
        </div>

        <button type="submit"
            class="bg-teal-600 text-white px-5 py-3 rounded-xl font-bold hover:bg-teal-700">
            Simpan Penyerahan
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