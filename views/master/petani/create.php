<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lengkapi Profil Petani - ZeroStunt.id</title>
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
                        Profil Mitra Petani
                    </div>

                    <h1 class="text-4xl xl:text-5xl font-extrabold leading-tight mb-6">
                        Lengkapi data lahan sebelum masuk ke dashboard.
                    </h1>

                    <p class="text-teal-100 text-lg leading-relaxed mb-8">
                        Data ini digunakan untuk mencatat kapasitas panen, lokasi lahan, dan kebutuhan pengadaan pangan lokal.
                    </p>

                    <div class="space-y-4">
                        <div class="rounded-2xl bg-white/10 border border-white/15 p-5">
                            <p class="font-bold text-amber-300 mb-1">Data Lahan</p>
                            <p class="text-sm text-teal-100">
                                Nama dan alamat lahan membantu admin puskesmas mengenali sumber komoditas.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-white/10 border border-white/15 p-5">
                            <p class="font-bold text-amber-300 mb-1">Kapasitas Panen</p>
                            <p class="text-sm text-teal-100">
                                Kapasitas bulanan membantu sistem memperkirakan suplai pangan yang tersedia.
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
            <div class="w-full max-w-lg my-auto py-8">
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
                                Lengkapi Profil
                            </p>

                            <span class="px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-xs font-bold">
                                Petani
                            </span>
                        </div>

                        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">
                            Profil Petani
                        </h1>

                        <p class="text-gray-500 leading-relaxed">
                            Isi data berikut agar akun Anda bisa digunakan untuk mengakses dashboard petani.
                        </p>
                    </div>

                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                            <?= htmlspecialchars($_SESSION['error']); ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form method="post" action="/master/petani/store" class="space-y-5">
                        <div>
                            <label for="nama_lahan" class="block text-sm font-bold text-gray-700 mb-2">
                                Nama Lahan <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="nama_lahan"
                                type="text"
                                name="nama_lahan"
                                required
                                placeholder="Contoh: Lahan Tani Sejahtera"
                                class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">
                        </div>

                        <div>
                              <label for="alamat_lahan" class="block text-sm font-bold text-gray-700 mb-2">
                                Alamat Lahan
                            </label>

                            <textarea
                                id="alamat_lahan"
                                name="alamat_lahan"
                                rows="3"
                                placeholder="Masukkan alamat atau lokasi lahan"
                                class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition resize-none focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100"></textarea>
                        </div>

                        <div>
                            <label for="no_rekening" class="block text-sm font-bold text-gray-700 mb-2">
                                Nomor Rekening
                            </label>

                            <input
                                id="no_rekening"
                                type="text"
                                name="no_rekening"
                                placeholder="Contoh: 1234567890"
                                class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">
                        </div>

                        <div>
                            <label for="kapasitas_panen_bulan" class="block text-sm font-bold text-gray-700 mb-2">
                                Kapasitas Panen per Bulan
                            </label>

                            <div class="relative">
                                <input
                                    id="kapasitas_panen_bulan"
                                    type="number"
                                    name="kapasitas_panen_bulan"
                                    step="0.01"
                                    min="0"
                                    placeholder="Contoh: 120"
                                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 pr-14 text-gray-900 outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">

                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400">
                                    Kg
                                </span>
                            </div>
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-2xl bg-teal-600 px-5 py-3 font-bold text-white transition hover:bg-teal-700 focus:outline-none focus:ring-4 focus:ring-teal-100">
                            Simpan Profil
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
</body>

</html>