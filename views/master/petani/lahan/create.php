<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Lahan Petani - ZeroStunt.id</title>
    <link href="/css/tailwind.css" rel="stylesheet">
</head>

<body class="min-h-screen bg-gray-50 text-gray-900 lg:overflow-hidden">
    <main class="min-h-screen lg:h-screen grid lg:grid-cols-2">
        <!-- Left Side -->
        <section class="hidden lg:flex lg:sticky lg:top-0 lg:h-screen relative overflow-hidden bg-gradient-to-br from-teal-700 to-teal-900 px-12 py-10 text-white">
            <div class="absolute inset-0 opacity-10"
                style="background-image: radial-gradient(circle, #ffffff 1px, transparent 1px); background-size: 32px 32px;">
            </div>

            <div class="relative z-10 flex flex-col justify-between w-full max-w-xl">
                <a href="/" class="inline-flex items-center gap-3 font-bold text-2xl">
                    <span class="w-11 h-11 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center">
                        🌱
                    </span>
                    ZeroStunt<span class="text-amber-300">.id</span>
                </a>

                <div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-amber-400/15 border border-amber-300/30 text-amber-100 text-sm font-bold mb-6">
                        Detail Lahan Petani
                    </div>

                    <h1 class="text-4xl xl:text-5xl font-extrabold leading-tight mb-6">
                        Lengkapi detail lahan dan komoditas budidaya.
                    </h1>

                    <p class="text-teal-100 text-lg leading-relaxed mb-8">
                        Data ini membantu admin mengetahui luas lahan, jenis usaha, serta estimasi hasil panen yang dapat mendukung pengadaan pangan lokal.
                    </p>

                    <div class="space-y-4">
                        <div class="rounded-2xl bg-white/10 border border-white/15 p-5">
                            <p class="font-bold text-amber-300 mb-1">Informasi Lahan</p>
                            <p class="text-sm text-teal-100">
                                Luas lahan, jenis usaha, dan status lahan digunakan untuk memvalidasi profil petani.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-white/10 border border-white/15 p-5">
                            <p class="font-bold text-amber-300 mb-1">Komoditas Budidaya</p>
                            <p class="text-sm text-teal-100">
                                Komoditas yang ditanam membantu sistem memperkirakan potensi suplai pangan.
                            </p>
                        </div>
                    </div>
                </div>

                <p class="text-sm text-teal-100">
                    © 2025 ZeroStunt.id — Versi 1.0
                </p>
            </div>
        </section>

        <!-- Right Side -->
        <section class="flex justify-center px-6 py-10 lg:h-screen lg:overflow-y-auto">
            <div class="w-full max-w-2xl my-auto py-8">
                <!-- Mobile Logo -->
                <a href="/" class="lg:hidden inline-flex items-center gap-3 font-bold text-2xl text-teal-700 mb-10">
                    <span class="w-11 h-11 rounded-2xl bg-teal-600 text-white flex items-center justify-center">
                        🌱
                    </span>
                    ZeroStunt<span class="text-amber-500">.id</span>
                </a>

                <div class="bg-white rounded-3xl border border-gray-100 shadow-xl p-8">
                    <div class="mb-8">
                        <div class="flex items-center justify-between gap-4 mb-5">
                            <p class="text-sm font-bold text-amber-500 uppercase tracking-widest">
                                Lengkapi Detail
                            </p>

                            <span class="px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-xs font-bold">
                                Lahan Petani
                            </span>
                        </div>

                        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">
                            Detail Lahan
                        </h1>

                        <p class="text-gray-500 leading-relaxed">
                            Isi data luas lahan, jenis usaha, dan komoditas yang dibudidayakan sebelum masuk ke dashboard.
                        </p>
                    </div>

                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                            <?= htmlspecialchars($_SESSION['error']); ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form method="POST" action="/master/petani/lahan/store" class="space-y-5">
                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-bold text-gray-700">
                                    Luas Lahan <span class="text-red-500">*</span>
                                </label>

                                <input type="number" name="luas_lahan" step="0.01" min="0.01" required
                                    placeholder="Contoh: 2"
                                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-bold text-gray-700">
                                    Satuan Luas <span class="text-red-500">*</span>
                                </label>

                                <select name="satuan_luas" required
                                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">
                                    <option value="ha">Hektar</option>
                                    <option value="m2">Meter Persegi</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-bold text-gray-700">
                                Jenis Usaha <span class="text-red-500">*</span>
                            </label>

                            <input type="text" name="jenis_usaha" required
                                placeholder="Contoh: Pertanian, Perikanan, Peternakan"
                                class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-bold text-gray-700">
                                Status Lahan <span class="text-red-500">*</span>
                            </label>

                            <select name="status_lahan" required
                                class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-bold text-gray-700">
                                Deskripsi Lahan
                            </label>

                            <textarea name="deskripsi_lahan" rows="3"
                                placeholder="Contoh: Lahan pertanian dengan akses irigasi baik..."
                                class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition resize-none focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100"></textarea>
                        </div>

                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                            <div class="mb-4 flex items-center justify-between gap-3">
                                <div>
                                    <h2 class="font-extrabold text-gray-900">
                                        Komoditas Dibudidayakan
                                    </h2>
                                    <p class="text-sm text-gray-500">
                                        Minimal tambahkan satu komoditas.
                                    </p>
                                    <p class="mt-2 text-xs text-gray-500">
                                        Luas area mengikuti satuan luas lahan di atas.
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Estimasi panen mengikuti satuan komoditas yang dipilih.
                                    </p>
                                </div>

                                <button type="button" id="addKomoditas"
                                    class="rounded-xl bg-teal-50 px-3 py-2 text-xs font-bold text-teal-700 hover:bg-teal-100">
                                    + Tambah
                                </button>
                            </div>

                            <div id="komoditasWrapper" class="space-y-3">
                                <div class="komoditas-row rounded-2xl border border-gray-100 bg-white p-4 space-y-3">
                                    <select name="id_komoditas[]" required
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-teal-500">
                                        <option value="" disabled selected>Pilih Komoditas</option>
                                        <?php foreach (($komoditas ?? []) as $item): ?>
                                            <option value="<?= htmlspecialchars($item['id_komoditas']); ?>">
                                                <?= htmlspecialchars($item['nama_komoditas']); ?> (<?= htmlspecialchars($item['satuan']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <div class="grid gap-3 md:grid-cols-3">
                                        <input type="number" name="luas_area[]" step="0.01" min="0"
                                            placeholder="Luas area lahan"
                                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-teal-500">

                                        <input type="number" name="estimasi_panen[]" step="0.01" min="0"
                                            placeholder="Estimasi panen / bulan"
                                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-teal-500">

                                        <button type="button"
                                            class="removeKomoditas rounded-xl bg-red-50 px-3 py-2 text-xs font-bold text-red-700 hover:bg-red-100">
                                            Hapus
                                        </button>
                                    </div>

                                    <input type="text" name="catatan_komoditas[]"
                                        placeholder="Catatan opsional"
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-teal-500">
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full rounded-2xl bg-teal-600 px-5 py-3 font-bold text-white transition hover:bg-teal-700 focus:outline-none focus:ring-4 focus:ring-teal-100">
                            Simpan Detail Lahan
                        </button>
                    </form>

                    <div class="mt-7 text-center">
                        <p class="text-sm text-gray-500">
                            Salah akun?
                            <a href="/auth/logout" class="font-bold text-teal-700 hover:text-teal-800">
                                Keluar dari akun ini
                            </a>
                        </p>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="/" class="text-sm font-semibold text-gray-500 hover:text-teal-700">
                            ← Kembali ke halaman utama
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const wrapper = document.getElementById('komoditasWrapper');
            const addButton = document.getElementById('addKomoditas');

            addButton.addEventListener('click', function() {
                const firstRow = wrapper.querySelector('.komoditas-row');
                const newRow = firstRow.cloneNode(true);

                newRow.querySelectorAll('input, select').forEach(function(input) {
                    input.value = '';
                });

                wrapper.appendChild(newRow);
            });

            wrapper.addEventListener('click', function(event) {
                if (!event.target.classList.contains('removeKomoditas')) {
                    return;
                }

                const rows = wrapper.querySelectorAll('.komoditas-row');

                if (rows.length <= 1) {
                    rows[0].querySelectorAll('input, select').forEach(function(input) {
                        input.value = '';
                    });
                    return;
                }

                event.target.closest('.komoditas-row').remove();
            });
        });
    </script>
</body>

</html>