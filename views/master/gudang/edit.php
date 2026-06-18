<?php
$pageTitle = 'Edit Gudang';
$pageSubtitle = 'Perbarui data gudang yang digunakan untuk pengelolaan stok.';

ob_start();

$kaderUsernames = array_map(fn ($k) => $k['username'], $kaders);
$isPengelolaKader = in_array($gudang['nama_pengelola'], $kaderUsernames);

?>

<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center">
    <div class="w-full max-w-2xl">
        <div class="mb-6">
            <a href="/master/gudang"
                class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-teal-700 transition">
                ← Kembali ke Data Gudang
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
                    Form Edit Gudang
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Ubah data Gudang sesuai kebutuhan.
                </p>
            </div>

            <form method="post" action="/master/gudang/update" class="p-6 space-y-5">
                <input type="hidden" name="id_gudang" value="<?= htmlspecialchars($gudang['id_gudang']); ?>">

                <div>
                    <label for="nama_gudang" class="mb-2 block text-sm font-bold text-gray-700">
                        Nama Gudang <span class="text-red-500">*</span>
                    </label>

                    <input type="text" id="nama_gudang" name="nama_gudang" required
                        value="<?= htmlspecialchars($gudang['nama_gudang']); ?>"
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100">
                </div>

                <div>
                    <label for="lokasi_gudang" class="mb-2 block text-sm font-bold text-gray-700">
                        Lokasi Gudang <span class="text-red-500">*</span>
                    </label>

                    <input type="text" id="lokasi_gudang" name="lokasi_gudang" required
                        value="<?= htmlspecialchars($gudang['lokasi_gudang']); ?>"
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100">
                </div>

                <div>
                    <label for="jenis_gudang" class="mb-2 block text-sm font-bold text-gray-700">
                        Jenis Gudang <span class="text-red-500">*</span>
                    </label>

                    <select id="jenis_gudang" name="jenis_gudang" required
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100">

                        <option value="">Pilih Jenis Gudang</option>
                        <option value="Pusat" <?= $gudang['jenis_gudang'] === 'Pusat' ? 'selected' : ''; ?>>Pusat</option>
                        <option value="Posyandu" <?= $gudang['jenis_gudang'] === 'Posyandu' ? 'selected' : ''; ?>>Posyandu</option>
                    </select>
                </div>

                <div>
                    <label for="alamat_lengkap" class="mb-2 block text-sm font-bold text-gray-700">
                        Alamat Lengkap <span class="text-red-500">*</span>
                    </label>

                    <textarea id="alamt_lengkap" name="alamat_lengkap" rows="4"
                        class="w-full resize-none rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100"><?= htmlspecialchars($gudang['alamat_lengkap'] ?? ''); ?></textarea>
                </div>

                <!-- Nama Pengelola -->
                <div class="space-y-2">
                    <label for="nama_pengelola_select" class="block text-sm font-bold text-gray-700">Nama Pengelola</label>
                    <select id="nama_pengelola_select" name="nama_pengelola_select"
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100">
                        <option value="">Pilih Pengelola (Kader)</option>
                        <?php foreach ($kaders as $kader): ?>
                            <option value="<?= htmlspecialchars($kader['username']) ?>"
                                <?= ($isPengelolaKader && $gudang['nama_pengelola'] === $kader['username']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kader['username']) ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="other" <?= !$isPengelolaKader && !empty($gudang['nama_pengelola']) ? 'selected' : '' ?>>
                            Lainnya...</option>
                    </select>
                </div>

                <div id="pengelola_other_wrapper"
                    class="<?= !$isPengelolaKader && !empty($gudang['nama_pengelola']) ? '' : 'hidden' ?>">
                    <label for="nama_pengelola_text" class="mb-2 block text-sm font-bold text-gray-700">Nama Pengelola
                        Lainnya</label>
                    <input type="text" id="nama_pengelola_text" name="nama_pengelola_text"
                        placeholder="Masukkan nama pengelola jika tidak ada di daftar"
                        value="<?= !$isPengelolaKader ? htmlspecialchars($gudang['nama_pengelola'] ?? '') : '' ?>"
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100" />
                </div>

                <script>
                    document.getElementById('nama_pengelola_select').addEventListener('change', function () {
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

                <div class="flex flex-col-reverse gap-3 pt-3 sm:flex-row sm:justify-end">
                    <a href="/master/gudang"
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