<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lengkapi Profil Ibu - ZeroStunt.id</title>
    <link href="/css/tailwind.css" rel="stylesheet">
</head>

<body class="min-h-screen bg-gray-50 text-gray-900 lg:overflow-hidden">
    <?php
    /** @var array<int,array{id_gudang:int,nama_gudang:string,jenis_gudang:string}> $gudangs */
    $gudangs = $gudangs ?? [];
    ?>

    <main class="min-h-screen lg:h-screen grid lg:grid-cols-2">
        <!-- Left Side -->
        <section class="hidden lg:flex lg:sticky lg:top-0 lg:h-screen relative overflow-hidden bg-gradient-to-br from-teal-700 to-teal-900 px-10 py-8 text-white">
            <div class="absolute inset-0 opacity-10"
                style="background-image: radial-gradient(circle, #ffffff 1px, transparent 1px); background-size: 32px 32px;">
            </div>

            <div class="relative z-10 flex flex-col justify-between w-full max-w-xl gap-8">
                <a href="/" class="inline-flex items-center gap-3 font-bold text-2xl">
                    <span class="w-11 h-11 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center">
                        🌱
                    </span>
                    ZeroStunt<span class="text-amber-300">.id</span>
                </a>

                <div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-amber-400/15 border border-amber-300/30 text-amber-100 text-sm font-bold mb-6">
                        Profil Ibu / Penerima
                    </div>

                    <h1 class="text-4xl font-extrabold leading-tight mb-5">
                        Lengkapi data ibu sebelum masuk ke dashboard.
                    </h1>

                    <p class="text-teal-100 text-base leading-relaxed mb-6">
                        Data ini digunakan untuk menghubungkan ibu dengan posyandu/gudang terdekat
                        dan mencatat status prioritas penerima bantuan gizi.
                    </p>

                    <div class="space-y-3">
                        <div class="rounded-2xl bg-white/10 border border-white/15 p-4">
                            <p class="font-bold text-amber-300 mb-1">Data Identitas</p>
                            <p class="text-sm text-teal-100">
                                NIK dan nama lengkap membantu memastikan data penerima tidak tertukar.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-white/10 border border-white/15 p-4">
                            <p class="font-bold text-amber-300 mb-1">Posyandu / Gudang</p>
                            <p class="text-sm text-teal-100">
                                Pilihan posyandu digunakan untuk pencatatan pemeriksaan dan distribusi bantuan.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-white/10 border border-white/15 p-4">
                            <p class="font-bold text-amber-300 mb-1">Status Kehamilan</p>
                            <p class="text-sm text-teal-100">
                                Ibu hamil dapat masuk ke prioritas khusus sesuai kebutuhan program gizi.
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
                                Ibu
                            </span>
                        </div>

                        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">
                            Profil Ibu
                        </h1>

                        <p class="text-gray-500 leading-relaxed">
                            Isi data berikut agar akun Anda bisa digunakan untuk mengakses dashboard ibu.
                        </p>
                    </div>

                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                            <?= htmlspecialchars($_SESSION['error']); ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form method="post" action="/master/ibu/store" class="space-y-5">
                        <div>
                            <label for="nik_ibu" class="block text-sm font-bold text-gray-700 mb-2">
                                NIK <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="nik_ibu"
                                type="text"
                                name="nik_ibu"
                                inputmode="numeric"
                                pattern="[0-9]{16}" 
                                minlength="16"
                                maxlength="16"
                                required
                                placeholder="Masukkan 16 digit NIK"
                                class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">
                        </div>

                        <div>
                            <label for="nama_ibu" class="block text-sm font-bold text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="nama_ibu"
                                type="text"
                                name="nama_ibu"
                                required
                                placeholder="Masukkan nama lengkap"
                                class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">
                        </div>

                        <div>
                            <label for="no_telp" class="block text-sm font-bold text-gray-700 mb-2">
                                Nomor Telepon
                            </label>

                            <input
                                id="no_telp"
                                type="text"
                                name="no_telp"
                                placeholder="Contoh: 081234567890"
                                class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">
                        </div>

                        <div>
                            <label for="alamat" class="block text-sm font-bold text-gray-700 mb-2">
                                Alamat
                            </label>

                            <textarea
                                id="alamat"
                                name="alamat"
                                rows="3"
                                placeholder="Masukkan alamat tempat tinggal"
                                class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition resize-none focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100"></textarea>
                        </div>

                        <label class="flex items-start gap-3 rounded-2xl border border-amber-100 bg-amber-50 p-4 cursor-pointer">
                            <input
                                type="checkbox"
                                name="is_pregnant"
                                value="1"
                                class="mt-1 h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500">

                            <div>
                                <p class="font-bold text-gray-900">
                                    Sedang Hamil
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    Centang jika saat ini sedang dalam masa kehamilan.
                                </p>
                            </div>
                        </label>

                        <div>
                            <label for="id_gudang" class="block text-sm font-bold text-gray-700 mb-2">
                                Pilih Gudang / Posyandu <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="id_gudang"
                                name="id_gudang"
                                required
                                class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">
                                <option value="">-- Pilih Gudang / Posyandu --</option>

                                <?php foreach ($gudangs as $gudang): ?>
                                    <option value="<?= htmlspecialchars($gudang['id_gudang']); ?>">
                                        <?= htmlspecialchars($gudang['nama_gudang'] . ' (' . $gudang['jenis_gudang'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <?php if (empty($gudangs)): ?>
                                <p class="mt-2 text-sm text-amber-600">
                                    Belum ada data gudang/posyandu. Hubungi admin puskesmas.
                                </p>
                            <?php endif; ?>
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