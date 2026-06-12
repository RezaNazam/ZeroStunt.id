<?php
$pageTitle = 'Tambah Komoditas Pangan';
$pageSubtitle = 'Tambahkan data bahan pangan yang akan digunakan untuk pengadaan dan distribusi.';

ob_start();
?>

<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center">
    <div class="w-full max-w-2xl">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="/master/komoditas"
                class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-teal-700 transition">
                ← Kembali ke Data Komoditas
            </a>
        </div>

        <!-- Flash Message -->
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                <?= htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="rounded-3xl border border-gray-100 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-6 py-5">
                <h2 class="text-xl font-extrabold text-gray-900">
                    Form Tambah Komoditas
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Data ini akan menjadi acuan komoditas pangan pada proses pengadaan.
                </p>
            </div>

            <form method="post" action="/master/komoditas/store" class="p-6 space-y-5">
                <!-- Nama Komoditas -->
                <div>
                    <label for="nama_komoditas" class="mb-2 block text-sm font-bold text-gray-700">
                        Nama Komoditas <span class="text-red-500">*</span>
                    </label>

                    <input type="text"
                        id="nama_komoditas"
                        name="nama_komoditas"
                        required
                        placeholder="Contoh: Ikan Nila, Telur Ayam, Sayur Hijau"
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100">
                </div>

                <!-- Kategori Gizi -->
                <div>
                    <label for="kategori_gizi" class="mb-2 block text-sm font-bold text-gray-700">
                        Kategori Gizi <span class="text-red-500">*</span>
                    </label>

                    <select id="kategori_gizi"
                        name="kategori_gizi"
                        required
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100">
                        <option value="">Pilih kategori gizi</option>
                        <option value="Protein Hewani">Protein Hewani</option>
                        <option value="Protein Nabati">Protein Nabati</option>
                        <option value="Karbohidrat">Karbohidrat</option>
                        <option value="Vitamin">Vitamin</option>
                        <option value="Mineral">Mineral</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <!-- Satuan -->
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
                            <option value="<?= htmlspecialchars($satuan['id_satuan']); ?>">
                                <?= htmlspecialchars($satuan['nama_satuan']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <p class="mt-2 text-xs text-gray-500">
                        Yang ditampilkan adalah nama satuan, tetapi yang disimpan tetap ID satuannya.
                    </p>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="deskripsi" class="mb-2 block text-sm font-bold text-gray-700">
                        Deskripsi
                    </label>

                    <textarea id="deskripsi"
                        name="deskripsi"
                        rows="4"
                        placeholder="Contoh: Sumber protein hewani untuk paket gizi prioritas."
                        class="w-full resize-none rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100"></textarea>
                </div>

                <!-- Action -->
                <div class="flex flex-col-reverse gap-3 pt-3 sm:flex-row sm:justify-end">
                    <a href="/master/komoditas"
                        class="inline-flex items-center justify-center rounded-2xl border border-gray-200 px-5 py-3 text-sm font-bold text-gray-600 transition hover:bg-gray-50">
                        Batal
                    </a>

                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-700">
                        Simpan Komoditas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
