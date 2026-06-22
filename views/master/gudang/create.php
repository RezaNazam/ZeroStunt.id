<?php
$pageTitle = 'Tambah Komoditas Pangan';
$pageSubtitle = 'Tambahkan data bahan pangan yang akan digunakan untuk pengadaan dan distribusi.';

ob_start();
?>

<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center">
    <div class="w-full max-w-2xl">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="/master/gudang"
                class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-teal-700 transition">
                ← Kembali ke Data gudang
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
                    Form Tambah gudang
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Data ini akan menjadi acuan komoditas pangan pada proses pengadaan.
                </p>
            </div>

            <form method="post" action="/master/gudang/store" class="p-6 space-y-5">
                <!-- Nama gudang -->
                <div>
                    <label for="nama_gudang" class="mb-2 block text-sm font-bold text-gray-700">
                        Nama Gudang <span class="text-red-500">*</span>
                    </label>

                    <input type="text" id="nama_gudang" name="nama_gudang" required
                        placeholder="Contoh: Gudang Suka Maju, Gudang Purwasari, Gudang Posyandu Jaya  "
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100">
                </div>

                <!-- Lokasi Gudang -->
                <div>
                    <label for="lokasi_gudang" class="mb-2 block text-sm font-bold text-gray-700">
                        Lokasi Gudang <span class="text-red-500">*</span>
                    </label>

                    <input type="text" id="lokasi_gudang" name="lokasi_gudang" required
                        placeholder="Contoh: Cikarang, Karawang, Bekasi"
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100">
                </div>

                <!-- Jenis Gudang -->
                <label class="mb-2 block text-sm font-bold text-gray-700">
                    Jenis Gudang <span class="text-red-500">*</span>
                </label>

                <div class="grid grid-cols-2 gap-3">
                    <label
                        class="inline-flex cursor-pointer items-center gap-3 rounded-2xl border border-gray-200 px-4 py-3 text-sm font-medium transition hover:border-teal-500">
                        <input type="radio" name="jenis_gudang" value="Pusat"
                            class="h-4 w-4 text-teal-600 focus:ring-teal-500" required>
                        <span>Pusat</span>
                    </label>

                    <label
                        class="inline-flex cursor-pointer items-center gap-3 rounded-2xl border border-gray-200 px-4 py-3 text-sm font-medium transition hover:border-teal-500">
                        <input type="radio" name="jenis_gudang" value="Posyandu"
                            class="h-4 w-4 text-teal-600 focus:ring-teal-500" required>
                        <span>Posyandu</span>
                    </label>
                </div>

                <!-- Alaamat Lengkap -->
                <div>
                    <label for="alamat_lengkap" class="mb-2 block text-sm font-bold text-gray-700">
                        Alamat Lengkap
                    </label>

                    <textarea id="alamat_lengkap" name="alamat_lengkap" rows="4"
                        placeholder="Contoh: Jl. Raya Suka Maju No. 123, Desa Sukamaju, Kecamatan Sukamaju, Kabupaten Sukamaju"
                        class="w-full resize-none rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100"></textarea>
                </div>

                <!-- Nama Pengelola -->
                <div class="space-y-2">
                    <label for="nama_pengelola_select" class="block text-sm font-bold text-gray-700">Nama
                        Pengelola</label>
                    <select id="nama_pengelola_select" name="nama_pengelola_select"
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100">
                        <option value="">Pilih Pengelola (Kader)</option>
                        <?php foreach ($kaders as $kader): ?>
                            <option value="<?= htmlspecialchars($kader['username']) ?>">
                                <?= htmlspecialchars($kader['username']) ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="other">Lainnya...</option>
                    </select>
                </div>

                <div id="pengelola_other_wrapper" class="hidden">
                    <label for="nama_pengelola_text" class="mb-2 block text-sm font-bold text-gray-700">Nama Pengelola
                        Lainnya</label>
                    <input type="text" id="nama_pengelola_text" name="nama_pengelola_text"
                        placeholder="Masukkan nama pengelola jika tidak ada di daftar"
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100" />
                </div>

                <script>
                    document.getElementById('nama_pengelola_select').addEventListener('change', function () {
                        document.getElementById('pengelola_other_wrapper').classList.toggle('hidden', this.value !== 'other');
                    });
                </script>

                <!-- Action -->
                <div class="flex flex-col-reverse gap-3 pt-3 sm:flex-row sm:justify-end">
                    <a href="/master/gudang"
                        class="inline-flex items-center justify-center rounded-2xl border border-gray-200 px-5 py-3 text-sm font-bold text-gray-600 transition hover:bg-gray-50">
                        Batal
                    </a>

                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-700">
                        Simpan Gudang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
