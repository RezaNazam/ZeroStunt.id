<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - ZeroStunt.id</title>
    <link rel="icon" type="image/png" href="/img/icon-logo.png?v=2">
    <link rel="shortcut icon" type="image/png" href="/img/icon-logo.png?v=2">
    <link rel="apple-touch-icon" href="/img/icon-logo.png?v=2">
    <link href="/css/tailwind.css" rel="stylesheet">
</head>

<body class="min-h-screen bg-gray-50 text-gray-900">
    <main class="min-h-screen grid lg:grid-cols-2">
        <!-- Left Side -->
        <section
            class="hidden lg:flex lg:sticky lg:top-0 lg:h-screen relative overflow-hidden bg-gradient-to-br from-teal-700 to-teal-900 text-white"
            style="padding: 32px 56px;">
            <div class="absolute inset-0 opacity-10"
                style="background-image: radial-gradient(circle, #ffffff 1px, transparent 1px); background-size: 32px 32px;">
            </div>

            <div class="relative z-10 flex h-full w-full max-w-2xl flex-col">
                <!-- Logo -->
                <a href="/" class="inline-flex items-center gap-4 font-bold text-2xl">
                    <span class="w-12 h-12 rounded-2xl bg-white text-teal-700 flex items-center justify-center text-sm font-extrabold shadow-lg">
                        ZS
                    </span>

                    <span class="text-white">
                        ZeroStunt<span class="text-amber-300">.id</span>
                    </span>
                </a>

                <!-- Main Text -->
                <div class="flex flex-1 flex-col justify-center py-8">
                    <div class="inline-flex w-max items-center gap-2 px-5 py-2.5 rounded-full bg-amber-400/15 border border-amber-300/30 text-amber-100 text-sm font-bold mb-8">
                        Akun Penerima & Mitra
                    </div>

                    <h1 class="max-w-xl text-4xl xl:text-[52px] font-extrabold leading-[1.12] mb-6">
                        Daftar sebagai bagian dari ekosistem pencegahan stunting.
                    </h1>

                    <p class="max-w-xl text-teal-100 text-lg leading-relaxed mb-8">
                        Buat akun untuk mengakses sistem sesuai peran Anda sebagai ibu penerima manfaat
                        atau petani lokal penyedia komoditas pangan.
                    </p>

                    <div class="max-w-xl space-y-4">
                        <div class="rounded-2xl bg-white/10 border border-white/15 p-5">
                            <p class="font-bold text-amber-300 mb-1">
                                Ibu / Penerima
                            </p>
                            <p class="text-sm text-teal-100 leading-relaxed">
                                Melihat data pertumbuhan anak dan riwayat bantuan gizi.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-white/10 border border-white/15 p-5">
                            <p class="font-bold text-amber-300 mb-1">
                                Petani Lokal
                            </p>
                            <p class="text-sm text-teal-100 leading-relaxed">
                                Memantau kontrak pengadaan dan histori penyaluran komoditas.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <p class="text-sm text-teal-100">
                    © 2025 ZeroStunt.id — Versi 1.0
                </p>
            </div>
        </section>

        <!-- Right Side -->
        <section class="flex items-center justify-center px-6 py-10">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <a href="/" class="lg:hidden inline-flex items-center gap-3 font-bold text-2xl text-teal-700 mb-10">
                    <span class="w-11 h-11 rounded-2xl bg-teal-600 text-white flex items-center justify-center">
                        🌱
                    </span>
                    ZeroStunt<span class="text-amber-500">.id</span>
                </a>

                <div class="bg-white rounded-3xl border border-gray-100 shadow-xl p-8">
                    <div class="mb-8">
                        <p class="text-sm font-bold text-amber-500 uppercase tracking-widest mb-3">
                            Buat Akun
                        </p>

                        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">
                            Daftar ke Sistem
                        </h1>

                        <p class="text-gray-500 leading-relaxed">
                            Pilih peran akun, lalu isi username dan password untuk mendaftar.
                        </p>
                    </div>

                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                            <?= htmlspecialchars($_SESSION['error']); ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form method="post" action="/auth/register" class="space-y-5">
                        <!-- Role Selection -->
                        <div>
                            <p class="block text-sm font-bold text-gray-700 mb-3">
                                Daftar Sebagai
                            </p>

                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="role" value="Ibu" checked class="peer sr-only">

                                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 transition peer-checked:border-teal-500 peer-checked:bg-teal-50 peer-checked:ring-4 peer-checked:ring-teal-100">
                                        <div class="text-2xl mb-2">👩‍👧</div>
                                        <p class="font-bold text-gray-900">Saya Ibu</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Penerima manfaat
                                        </p>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="role" value="Petani" class="peer sr-only">

                                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 transition peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:ring-4 peer-checked:ring-amber-100">
                                        <div class="text-2xl mb-2">🌾</div>
                                        <p class="font-bold text-gray-900">Saya Petani</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Mitra komoditas
                                        </p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="username" class="block text-sm font-bold text-gray-700 mb-2">
                                Username
                            </label>

                            <input
                                id="username"
                                type="text"
                                name="username"
                                required
                                autocomplete="username"
                                placeholder="Buat username"
                                class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-bold text-gray-700 mb-2">
                                Password
                            </label>

                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="Buat password"
                                class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">
                        </div>

                        <div>
                            <label for="confirm_password" class="block text-sm font-bold text-gray-700 mb-2">
                                Konfirmasi Password
                            </label>

                            <input
                                id="confirm_password"
                                type="password"
                                name="confirm_password"
                                required
                                autocomplete="new-password"
                                placeholder="Ulangi password"
                                class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-2xl bg-teal-600 px-5 py-3 font-bold text-white transition hover:bg-teal-700 focus:outline-none focus:ring-4 focus:ring-teal-100">
                            Daftar
                        </button>
                    </form>

                    <div class="mt-7 text-center">
                        <p class="text-sm text-gray-500">
                            Sudah punya akun?
                            <a href="/auth/login" class="font-bold text-teal-700 hover:text-teal-800">
                                Login sekarang
                            </a>
                        </p>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <a href="/" class="text-sm font-semibold text-gray-500 hover:text-teal-700">
                        ← Kembali ke halaman utama
                    </a>
                </div>
            </div>
        </section>
    </main>
</body>

</html>