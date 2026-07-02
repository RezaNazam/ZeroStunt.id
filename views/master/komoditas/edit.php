<?php
$pageTitle = 'Edit Komoditas Pangan';
$pageSubtitle = 'Perbarui data komoditas pangan yang digunakan dalam pengadaan dan distribusi.';

ob_start();
?>

<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center">
    <div class="w-full max-w-2xl">
        <div class="mb-6">
            <a href="/master/komoditas"
                class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-teal-700 transition">
                ← Kembali ke Data Komoditas
            </a>
        </div>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                <?= htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="rounded-3xl border border-gray-100 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-6 py-5">
                <h2 class="text-xl font-extrabold text-gray-900">
                    Form Edit Komoditas
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Ubah data komoditas sesuai kebutuhan.
                </p>
            </div>

            <form method="post" action="/master/komoditas/update" class="p-6 space-y-5">
                <input type="hidden"
                    name="id_komoditas"
                    value="<?= htmlspecialchars($komoditas['id_komoditas']); ?>">

                <div>
                    <label for="nama_komoditas" class="mb-2 block text-sm font-bold text-gray-700">
                        Nama Komoditas <span class="text-red-500">*</span>
                    </label>

                    <input type="text"
                        id="nama_komoditas"
                        name="nama_komoditas"
                        required
                        value="<?= htmlspecialchars($komoditas['nama_komoditas']); ?>"
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100">
                </div>

                <div>
                    <label for="kategori_gizi" class="mb-2 block text-sm font-bold text-gray-700">
                        Kategori Gizi <span class="text-red-500">*</span>
                    </label>

                    <select id="kategori_gizi"
                        name="kategori_gizi"
                        required
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100">

                        <?php
                        $kategoriOptions = [
                            'Protein Hewani',
                            'Protein Nabati',
                            'Karbohidrat',
                            'Vitamin',
                            'Mineral',
                            'Lainnya'
                        ];
                        ?>

                        <option value="">Pilih kategori gizi</option>

                        <?php foreach ($kategoriOptions as $kategori): ?>
                            <option value="<?= htmlspecialchars($kategori); ?>"
                                <?= $komoditas['kategori_gizi'] === $kategori ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($kategori); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="kategori_gizi_lain_wrapper"
                    class="<?= $komoditas['kategori_gizi'] !== 'Lainnya' ? 'hidden' : '' ?>">
                    <label for="kategori_gizi_lain" class="mb-2 block text-sm font-bold text-gray-700">
                        Kategori Gizi Lainnya <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="kategori_gizi_lain" name="kategori_gizi_lain"
                        placeholder="Masukkan kategori gizi jika tidak ada di daftar"
                        value="<?= $komoditas['kategori_gizi'] === 'Lainnya' ? htmlspecialchars($komoditas['kategori_gizi_lain'] ?? '') : '' ?>"
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100" />
                </div>

                <script>
                    document.getElementById('kategori_gizi').addEventListener('change', function() {
                        document.getElementById('kategori_gizi_lain_wrapper').classList.toggle('hidden', this.value !== 'Lainnya');
                    });
                </script>
                <!-- value="<?= !$isPengelolaKader ? htmlspecialchars($gudang['nama_pengelola'] ?? '') : '' ?>"
                class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100" />
        </div> -->

                <script>
                    document.getElementById('nama_pengelola_select').addEventListener('change', function() {
                        const otherWrapper = document.getElementById('pengelola_other_wrapper');
                        const otherInput = document.getElementById('nama_pengelola_text');
                        if (this.value !== 'other') {
                            otherWrapper.classList.add('hidden');
                            otherInput.value = '';
                        } else {
                            otherWrapper.classList.remove('hidden');
                        }
                    });
                </script>

                <div>
                    <label for="id_satuan" class="mb-2 block text-sm font-bold text-gray-700">
                        Satuan <span class="text-red-500">*</span>
                    </label>

                    <select id="id_satuan"
                        name="id_satuan"
                        required
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100">

                        <option value="">Pilih satuan</option>

                        <?php foreach (($satuans ?? []) as $satuan): ?>
                            <option value="<?= htmlspecialchars($satuan['id_satuan']); ?>"
                                <?= (int) $komoditas['id_satuan'] === (int) $satuan['id_satuan'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($satuan['nama_satuan']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="deskripsi" class="mb-2 block text-sm font-bold text-gray-700">
                        Deskripsi
                    </label>

                    <textarea id="deskripsi"
                        name="deskripsi"
                        rows="4"
                        class="w-full resize-none rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100"><?= htmlspecialchars($komoditas['deskripsi'] ?? ''); ?></textarea>
                </div>

                <div class="flex flex-col-reverse gap-3 pt-3 sm:flex-row sm:justify-end">
                    <a href="/master/komoditas"
                        class="inline-flex items-center justify-center rounded-2xl border border-gray-200 px-5 py-3 text-sm font-bold text-gray-600 transition hover:bg-gray-50">
                        Batal
                    </a>

                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
