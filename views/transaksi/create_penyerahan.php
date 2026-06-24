<?php
$ibus = $ibus ?? [];
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

        <!-- TANGGAL -->
        <div>
            <label class="block text-sm font-bold mb-2">Tanggal Penyerahan</label>
            <input type="date" name="tanggal_penyerahan" required
                class="w-full rounded-xl border border-gray-200 px-4 py-3">
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

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>