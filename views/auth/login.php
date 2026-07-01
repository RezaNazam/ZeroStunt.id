<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - ZeroStunt.id</title>
    <link rel="icon" type="image/png" href="/img/icon-logo.png?v=2">
    <link rel="shortcut icon" type="image/png" href="/img/icon-logo.png?v=2">
    <link rel="apple-touch-icon" href="/img/icon-logo.png?v=2">
    <link href="/css/tailwind.css" rel="stylesheet">
</head>

<body class="min-h-screen bg-gray-50 text-gray-900">
    <main class="min-h-screen grid lg:grid-cols-2">
        <!-- Left Side -->
        <section class="hidden lg:flex relative overflow-hidden bg-gradient-to-br from-teal-700 to-teal-900 px-12 py-10 text-white">
            <div class="absolute inset-0 opacity-10"
                style="background-image: radial-gradient(circle, #ffffff 1px, transparent 1px); background-size: 32px 32px;">
            </div>

            <div class="relative z-10 flex flex-col justify-between w-full max-w-xl">
                <a href="/" class="inline-flex items-center gap-3 font-bold text-2xl">
                    <span class="w-11 h-11 rounded-2xl bg-white text-teal-700 flex items-center justify-center text-sm font-extrabold shadow-lg">
                        ZS
                    </span>
                    ZeroStunt<span class="text-amber-300">.id</span>
                </a>

                <div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-amber-400/15 border border-amber-300/30 text-amber-100 text-sm font-bold mb-6">
                        Sistem Pencegahan Stunting Terintegrasi
                    </div>

                    <h1 class="text-4xl xl:text-5xl font-extrabold leading-tight mb-6">
                        Kelola pemantauan gizi dan distribusi pangan dalam satu sistem.
                    </h1>

                    <p class="text-teal-100 text-lg leading-relaxed mb-8">
                        Masuk untuk mengakses dashboard sesuai peran Anda: admin puskesmas,
                        kader posyandu, petani lokal, atau ibu penerima manfaat.
                    </p>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-2xl bg-white/10 border border-white/15 p-5">
                            <p class="text-3xl font-extrabold text-amber-300">1.247</p>
                            <p class="text-sm text-teal-100 mt-1">Anak Dipantau</p>
                        </div>

                        <div class="rounded-2xl bg-white/10 border border-white/15 p-5">
                            <p class="text-3xl font-extrabold text-amber-300">94%</p>
                            <p class="text-sm text-teal-100 mt-1">Distribusi Tepat Sasaran</p>
                        </div>
                    </div>
                </div>

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
                            Selamat Datang
                        </p>

                        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">
                            Masuk ke Sistem
                        </h1>

                        <p class="text-gray-500 leading-relaxed">
                            Gunakan username dan password yang sudah terdaftar untuk melanjutkan.
                        </p>
                    </div>

                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                            <?= htmlspecialchars($_SESSION['error']); ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <?php if (!empty($_SESSION['success'])): ?>
                        <div class="mb-5 rounded-2xl border border-green-100 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                            <?= htmlspecialchars($_SESSION['success']); ?>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <form method="post" action="/auth/login" class="space-y-5">
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
                                placeholder="Masukkan username"
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
                                autocomplete="current-password"
                                placeholder="Masukkan password"
                                class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-2xl bg-teal-600 px-5 py-3 font-bold text-white transition hover:bg-teal-700 focus:outline-none focus:ring-4 focus:ring-teal-100">
                            Login
                        </button>
                    </form>

                    <div class="mt-7 text-center">
                        <p class="text-sm text-gray-500">
                            Belum punya akun?
                            <a href="/auth/register" class="font-bold text-teal-700 hover:text-teal-800">
                                Daftar sekarang
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