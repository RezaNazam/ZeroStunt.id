<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZeroStunt.id</title>
    <link href="/css/tailwind.css" rel="stylesheet">
</head>

<body class="bg-gray-50 text-gray-900">

    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-gray-100">
        <nav class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 font-bold text-xl text-teal-700">
                <span class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center">
                    🌱
                </span>
                ZeroStunt<span class="text-amber-500">.id</span>
            </a>

            <div class="hidden md:flex items-center gap-8 text-sm text-gray-600">
                <a href="#tentang" class="hover:text-teal-700">Tentang</a>
                <a href="#cara-kerja" class="hover:text-teal-700">Cara Kerja</a>
                <a href="#fitur" class="hover:text-teal-700">Fitur</a>
                <a href="#peran" class="hover:text-teal-700">Peran</a>
            </div>

            <a href="/auth/login"
                class="px-5 py-2.5 rounded-xl bg-teal-600 text-white text-sm font-semibold hover:bg-teal-700 transition">
                Masuk
            </a>
        </nav>
    </header>

    <main>
        <section class="max-w-7xl mx-auto px-6 py-20 grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-teal-50 text-teal-700 text-sm font-semibold mb-6">
                    Sistem Pencegahan Stunting Terintegrasi
                </div>

                <h1 class="text-4xl md:text-6xl font-extrabold leading-tight text-gray-900 mb-6">
                    Pantau Gizi Anak dan Distribusi Pangan Lokal dalam Satu Sistem
                </h1>

                <p class="text-lg text-gray-500 leading-relaxed mb-8">
                    ZeroStunt.id membantu puskesmas, kader posyandu, petani lokal, dan ibu penerima manfaat dalam pemantauan stunting, stok pangan, pengadaan, serta distribusi paket gizi.
                </p>

                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="/auth/login"
                        class="px-6 py-3 rounded-xl bg-teal-600 text-white font-semibold hover:bg-teal-700 transition text-center">
                        Mulai Sekarang
                    </a>

                    <a href="#cara-kerja"
                        class="px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-semibold hover:border-teal-600 hover:text-teal-700 transition text-center">
                        Lihat Cara Kerja
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-5 rounded-2xl bg-teal-50">
                        <p class="text-3xl font-bold text-teal-700">1.247</p>
                        <p class="text-sm text-gray-500">Anak Dipantau</p>
                    </div>
                    <div class="p-5 rounded-2xl bg-green-50">
                        <p class="text-3xl font-bold text-green-700">23</p>
                        <p class="text-sm text-gray-500">Posyandu Aktif</p>
                    </div>
                    <div class="p-5 rounded-2xl bg-amber-50">
                        <p class="text-3xl font-bold text-amber-600">47</p>
                        <p class="text-sm text-gray-500">Petani Mitra</p>
                    </div>
                    <div class="p-5 rounded-2xl bg-blue-50">
                        <p class="text-3xl font-bold text-blue-600">94%</p>
                        <p class="text-sm text-gray-500">Distribusi Tepat Sasaran</p>
                    </div>
                </div>
            </div>
        </section>
        <section id="tentang" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <p class="text-sm font-bold text-amber-500 uppercase tracking-widest mb-3">
                        Tentang ZeroStunt.id
                    </p>

                    <h2 class="text-3xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-6">
                        Satu Platform untuk Pencegahan Stunting Terintegrasi
                    </h2>

                    <p class="text-lg text-gray-500 leading-relaxed mb-8">
                        ZeroStunt.id menghubungkan puskesmas, posyandu, petani lokal, dan ibu penerima manfaat dalam satu sistem untuk memantau status gizi anak, mengelola stok pangan, serta memastikan distribusi paket gizi tepat sasaran.
                    </p>

                    <div class="space-y-4">
                        <div class="flex gap-3">
                            <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center text-sm font-bold">✓</span>
                            <p class="text-gray-700 font-medium">Pemantauan pertumbuhan anak berbasis Z-Score WHO 2006.</p>
                        </div>

                        <div class="flex gap-3">
                            <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center text-sm font-bold">✓</span>
                            <p class="text-gray-700 font-medium">Supply chain pangan lokal dari petani ke posyandu.</p>
                        </div>

                        <div class="flex gap-3">
                            <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center text-sm font-bold">✓</span>
                            <p class="text-gray-700 font-medium">Distribusi paket gizi berdasarkan prioritas kondisi anak.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-teal-50 p-6 rounded-3xl border border-teal-100">
                        <div class="text-3xl mb-4">🐟</div>
                        <h3 class="font-bold text-gray-900 mb-1">Ikan Nila Segar</h3>
                        <p class="text-sm text-gray-500 mb-3">Protein Hewani</p>
                        <span class="text-xs bg-white text-teal-700 px-3 py-1 rounded-full font-semibold">Satuan: Kg</span>
                    </div>

                    <div class="bg-amber-50 p-6 rounded-3xl border border-amber-100">
                        <div class="text-3xl mb-4">🥚</div>
                        <h3 class="font-bold text-gray-900 mb-1">Telur Ayam Kampung</h3>
                        <p class="text-sm text-gray-500 mb-3">Protein + Mikronutrien</p>
                        <span class="text-xs bg-white text-amber-700 px-3 py-1 rounded-full font-semibold">Satuan: Butir</span>
                    </div>

                    <div class="bg-green-50 p-6 rounded-3xl border border-green-100">
                        <div class="text-3xl mb-4">🥬</div>
                        <h3 class="font-bold text-gray-900 mb-1">Sayur Hijau</h3>
                        <p class="text-sm text-gray-500 mb-3">Vitamin & Mineral</p>
                        <span class="text-xs bg-white text-green-700 px-3 py-1 rounded-full font-semibold">Satuan: Kg</span>
                    </div>

                    <div class="bg-blue-50 p-6 rounded-3xl border border-blue-100">
                        <div class="text-3xl mb-4">🌱</div>
                        <h3 class="font-bold text-gray-900 mb-1">Kacang-kacangan</h3>
                        <p class="text-sm text-gray-500 mb-3">Protein Nabati</p>
                        <span class="text-xs bg-white text-blue-700 px-3 py-1 rounded-full font-semibold">Satuan: Kg</span>
                    </div>
                </div>
            </div>
        </section>
        <!-- Cara Kerja Section -->
        <section id="cara-kerja" class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-14">
                    <p class="text-sm font-bold text-amber-500 uppercase tracking-widest mb-3">
                        Rantai Pasokan
                    </p>

                    <h2 class="text-3xl md:text-5xl font-extrabold text-gray-900 mb-5">
                        Bagaimana ZeroStunt.id Bekerja?
                    </h2>

                    <p class="text-lg text-gray-500 max-w-2xl mx-auto leading-relaxed">
                        Empat pihak utama saling terhubung dalam satu alur, mulai dari petani lokal,
                        puskesmas, posyandu, hingga ibu dan anak sebagai penerima manfaat.
                    </p>
                </div>

                <!-- Supply Chain Steps -->
                <div class="grid md:grid-cols-4 gap-6">
                    <div class="relative bg-white rounded-3xl p-6 border border-amber-100 shadow-sm hover:shadow-md transition">
                        <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center text-3xl mb-5">
                            🌾
                        </div>

                        <span class="inline-block px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-bold mb-4">
                            Sumber
                        </span>

                        <h3 class="text-xl font-bold text-gray-900 mb-3">
                            Kelompok Tani
                        </h3>

                        <p class="text-sm text-gray-500 leading-relaxed">
                            Petani lokal menyuplai komoditas pangan bergizi tinggi langsung ke sistem pengadaan.
                        </p>

                        <div class="hidden md:flex absolute top-1/2 -right-5 -translate-y-1/2 w-10 h-10 rounded-full bg-teal-600 text-white items-center justify-center font-bold z-10">
                            →
                        </div>
                    </div>

                    <div class="relative bg-white rounded-3xl p-6 border border-teal-100 shadow-sm hover:shadow-md transition">
                        <div class="w-14 h-14 rounded-2xl bg-teal-50 flex items-center justify-center text-3xl mb-5">
                            📦
                        </div>

                        <span class="inline-block px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-xs font-bold mb-4">
                            Pusat
                        </span>

                        <h3 class="text-xl font-bold text-gray-900 mb-3">
                            Gudang Puskesmas
                        </h3>

                        <p class="text-sm text-gray-500 leading-relaxed">
                            Admin mencatat pengadaan, mengelola stok pusat, dan menyalurkan bahan pangan ke posyandu.
                        </p>

                        <div class="hidden md:flex absolute top-1/2 -right-5 -translate-y-1/2 w-10 h-10 rounded-full bg-teal-600 text-white items-center justify-center font-bold z-10">
                            →
                        </div>
                    </div>

                    <div class="relative bg-white rounded-3xl p-6 border border-green-100 shadow-sm hover:shadow-md transition">
                        <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center text-3xl mb-5">
                            🚚
                        </div>

                        <span class="inline-block px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-bold mb-4">
                            Distribusi
                        </span>

                        <h3 class="text-xl font-bold text-gray-900 mb-3">
                            Posyandu
                        </h3>

                        <p class="text-sm text-gray-500 leading-relaxed">
                            Kader menerima stok, melakukan pemeriksaan anak, dan menyerahkan paket gizi.
                        </p>

                        <div class="hidden md:flex absolute top-1/2 -right-5 -translate-y-1/2 w-10 h-10 rounded-full bg-teal-600 text-white items-center justify-center font-bold z-10">
                            →
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-6 border border-blue-100 shadow-sm hover:shadow-md transition">
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center text-3xl mb-5">
                            👩‍👧
                        </div>

                        <span class="inline-block px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold mb-4">
                            Penerima
                        </span>

                        <h3 class="text-xl font-bold text-gray-900 mb-3">
                            Ibu & Anak
                        </h3>

                        <p class="text-sm text-gray-500 leading-relaxed">
                            Penerima manfaat mendapatkan paket gizi berdasarkan hasil screening dan status prioritas.
                        </p>
                    </div>
                </div>

                <!-- Paket Gizi Table -->
                <div class="mt-14 bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900">
                            Paket Gizi Berdasarkan Prioritas
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Paket ditentukan berdasarkan kondisi anak dan hasil pemeriksaan gizi.
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left px-6 py-4 font-bold text-gray-600">Paket</th>
                                    <th class="text-left px-6 py-4 font-bold text-gray-600">Target Penerima</th>
                                    <th class="text-left px-6 py-4 font-bold text-gray-600">Isi Paket</th>
                                    <th class="text-left px-6 py-4 font-bold text-gray-600">Pemicu</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-5">
                                        <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">
                                            Prioritas 1
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-gray-700 font-medium">
                                        Balita berisiko stunting + ibu hamil
                                    </td>
                                    <td class="px-6 py-5 text-gray-500">
                                        1.5 kg ikan + 10 butir telur
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="px-3 py-1 rounded-lg bg-red-50 text-red-700 text-xs font-bold">
                                            Z &lt; -3 SD / Hamil
                                        </span>
                                    </td>
                                </tr>

                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-5">
                                        <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">
                                            Prioritas 2
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-gray-700 font-medium">
                                        Balita dengan status gizi kurang
                                    </td>
                                    <td class="px-6 py-5 text-gray-500">
                                        1.0 kg ikan + 8 butir telur
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="px-3 py-1 rounded-lg bg-amber-50 text-amber-700 text-xs font-bold">
                                            -3 SD ≤ Z &lt; -2 SD
                                        </span>
                                    </td>
                                </tr>

                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-5">
                                        <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">
                                            Prioritas 3
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-gray-700 font-medium">
                                        Anak dengan status normal / monitoring rutin
                                    </td>
                                    <td class="px-6 py-5 text-gray-500">
                                        0.5 kg sayur + 3 butir telur
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="px-3 py-1 rounded-lg bg-green-50 text-green-700 text-xs font-bold">
                                            Z ≥ -2 SD
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        <!-- Fitur Section -->
        <section id="fitur" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-14">
                    <p class="text-sm font-bold text-amber-500 uppercase tracking-widest mb-3">
                        Fitur Lengkap
                    </p>

                    <h2 class="text-3xl md:text-5xl font-extrabold text-gray-900 mb-5">
                        Semua yang Dibutuhkan dalam Satu Sistem
                    </h2>

                    <p class="text-lg text-gray-500 max-w-2xl mx-auto leading-relaxed">
                        ZeroStunt.id membantu proses pemantauan, pengadaan, distribusi, hingga pelaporan
                        agar pencegahan stunting lebih terukur dan terintegrasi.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Feature 1 -->
                    <div class="bg-white rounded-3xl p-7 border border-gray-100 shadow-sm hover:shadow-lg transition">
                        <div class="flex items-start justify-between mb-5">
                            <div class="w-14 h-14 rounded-2xl bg-teal-50 flex items-center justify-center text-3xl">
                                📈
                            </div>

                            <span class="px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-xs font-bold">
                                Pemeriksaan
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mb-3">
                            Pemantauan Z-Score WHO
                        </h3>

                        <p class="text-sm text-gray-500 leading-relaxed">
                            Hitung dan pantau status gizi anak menggunakan standar pertumbuhan WHO 2006
                            untuk membantu deteksi dini risiko stunting.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-white rounded-3xl p-7 border border-gray-100 shadow-sm hover:shadow-lg transition">
                        <div class="flex items-start justify-between mb-5">
                            <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center text-3xl">
                                🌱
                            </div>

                            <span class="px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-bold">
                                Pengadaan
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mb-3">
                            Supply Chain Pangan Lokal
                        </h3>

                        <p class="text-sm text-gray-500 leading-relaxed">
                            Menghubungkan petani lokal dengan puskesmas dan posyandu untuk memastikan
                            pasokan pangan bergizi lebih dekat dan terarah.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-white rounded-3xl p-7 border border-gray-100 shadow-sm hover:shadow-lg transition">
                        <div class="flex items-start justify-between mb-5">
                            <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center text-3xl">
                                📦
                            </div>

                            <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-bold">
                                Inventori
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mb-3">
                            Manajemen Stok Real-time
                        </h3>

                        <p class="text-sm text-gray-500 leading-relaxed">
                            Pantau stok komoditas di gudang puskesmas dan posyandu agar distribusi
                            paket gizi tetap aman dan tercatat.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="bg-white rounded-3xl p-7 border border-gray-100 shadow-sm hover:shadow-lg transition">
                        <div class="flex items-start justify-between mb-5">
                            <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center text-3xl">
                                📊
                            </div>

                            <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold">
                                Laporan
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mb-3">
                            Laporan & Analitik
                        </h3>

                        <p class="text-sm text-gray-500 leading-relaxed">
                            Sajikan laporan pertumbuhan anak, distribusi paket, stok pangan, dan
                            aktivitas mitra secara lebih mudah dipahami.
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="bg-white rounded-3xl p-7 border border-gray-100 shadow-sm hover:shadow-lg transition">
                        <div class="flex items-start justify-between mb-5">
                            <div class="w-14 h-14 rounded-2xl bg-purple-50 flex items-center justify-center text-3xl">
                                🛡️
                            </div>

                            <span class="px-3 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-bold">
                                Keamanan
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mb-3">
                            RBAC Multi-Peran
                        </h3>

                        <p class="text-sm text-gray-500 leading-relaxed">
                            Hak akses pengguna dibedakan berdasarkan peran seperti admin, kader,
                            petani, dan ibu penerima manfaat.
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="bg-white rounded-3xl p-7 border border-gray-100 shadow-sm hover:shadow-lg transition">
                        <div class="flex items-start justify-between mb-5">
                            <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center text-3xl">
                                ❤️
                            </div>

                            <span class="px-3 py-1 rounded-full bg-red-50 text-red-700 text-xs font-bold">
                                Distribusi
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mb-3">
                            Distribusi Tepat Sasaran
                        </h3>

                        <p class="text-sm text-gray-500 leading-relaxed">
                            Paket gizi dapat diprioritaskan berdasarkan kondisi anak sehingga bantuan
                            lebih tepat sasaran dan tidak sekadar dibagikan merata.
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <!-- Peran Section -->
        <section id="peran" class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-14">
                    <p class="text-sm font-bold text-amber-500 uppercase tracking-widest mb-3">
                        Multi-Peran
                    </p>

                    <h2 class="text-3xl md:text-5xl font-extrabold text-gray-900 mb-5">
                        Satu Platform untuk Semua Stakeholder
                    </h2>

                    <p class="text-lg text-gray-500 max-w-2xl mx-auto leading-relaxed">
                        Setiap pengguna memiliki akses dan tampilan sesuai perannya,
                        sehingga sistem tetap aman, terarah, dan mudah digunakan.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Role 1 -->
                    <div class="bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition">
                        <div class="bg-gradient-to-br from-teal-600 to-teal-800 p-6">
                            <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center text-3xl mb-5">
                                🛡️
                            </div>

                            <h3 class="text-xl font-bold text-white">
                                Admin Puskesmas
                            </h3>
                        </div>

                        <div class="p-6">
                            <ul class="space-y-3">
                                <li class="flex gap-3 text-sm text-gray-600">
                                    <span class="text-teal-600 font-bold">✓</span>
                                    Kelola semua data master
                                </li>
                                <li class="flex gap-3 text-sm text-gray-600">
                                    <span class="text-teal-600 font-bold">✓</span>
                                    Buat dan kelola pengadaan
                                </li>
                                <li class="flex gap-3 text-sm text-gray-600">
                                    <span class="text-teal-600 font-bold">✓</span>
                                    Monitor distribusi lintas posyandu
                                </li>
                                <li class="flex gap-3 text-sm text-gray-600">
                                    <span class="text-teal-600 font-bold">✓</span>
                                    Akses laporan analitik
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Role 2 -->
                    <div class="bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition">
                        <div class="bg-gradient-to-br from-green-500 to-green-700 p-6">
                            <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center text-3xl mb-5">
                                👥
                            </div>

                            <h3 class="text-xl font-bold text-white">
                                Kader Posyandu
                            </h3>
                        </div>

                        <div class="p-6">
                            <ul class="space-y-3">
                                <li class="flex gap-3 text-sm text-gray-600">
                                    <span class="text-green-600 font-bold">✓</span>
                                    Input hasil pemeriksaan anak
                                </li>
                                <li class="flex gap-3 text-sm text-gray-600">
                                    <span class="text-green-600 font-bold">✓</span>
                                    Kelola stok posyandu
                                </li>
                                <li class="flex gap-3 text-sm text-gray-600">
                                    <span class="text-green-600 font-bold">✓</span>
                                    Rekam penyerahan paket gizi
                                </li>
                                <li class="flex gap-3 text-sm text-gray-600">
                                    <span class="text-green-600 font-bold">✓</span>
                                    Lihat laporan posyandu sendiri
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Role 3 -->
                    <div class="bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition">
                        <div class="bg-gradient-to-br from-amber-500 to-amber-700 p-6">
                            <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center text-3xl mb-5">
                                🌾
                            </div>

                            <h3 class="text-xl font-bold text-white">
                                Petani Lokal
                            </h3>
                        </div>

                        <div class="p-6">
                            <ul class="space-y-3">
                                <li class="flex gap-3 text-sm text-gray-600">
                                    <span class="text-amber-600 font-bold">✓</span>
                                    Lihat kontrak pengadaan
                                </li>
                                <li class="flex gap-3 text-sm text-gray-600">
                                    <span class="text-amber-600 font-bold">✓</span>
                                    Pantau status pembayaran
                                </li>
                                <li class="flex gap-3 text-sm text-gray-600">
                                    <span class="text-amber-600 font-bold">✓</span>
                                    Lihat histori penyaluran
                                </li>
                                <li class="flex gap-3 text-sm text-gray-600">
                                    <span class="text-amber-600 font-bold">✓</span>
                                    Kelola profil lahan sendiri
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Role 4 -->
                    <div class="bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-700 p-6">
                            <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center text-3xl mb-5">
                                👩‍👧
                            </div>

                            <h3 class="text-xl font-bold text-white">
                                Ibu / Penerima
                            </h3>
                        </div>

                        <div class="p-6">
                            <ul class="space-y-3">
                                <li class="flex gap-3 text-sm text-gray-600">
                                    <span class="text-blue-600 font-bold">✓</span>
                                    Lihat data pertumbuhan anak
                                </li>
                                <li class="flex gap-3 text-sm text-gray-600">
                                    <span class="text-blue-600 font-bold">✓</span>
                                    Histori penerimaan bantuan
                                </li>
                                <li class="flex gap-3 text-sm text-gray-600">
                                    <span class="text-blue-600 font-bold">✓</span>
                                    Status prioritas gizi anak
                                </li>
                                <li class="flex gap-3 text-sm text-gray-600">
                                    <span class="text-blue-600 font-bold">✓</span>
                                    Riwayat pemeriksaan posyandu
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- CTA Section -->
        <section class="py-20 bg-gradient-to-br from-teal-700 to-teal-900">
            <div class="max-w-4xl mx-auto px-6 text-center">
                <p class="text-sm font-bold text-amber-300 uppercase tracking-widest mb-3">
                    Mulai Implementasi
                </p>

                <h2 class="text-3xl md:text-5xl font-extrabold text-white leading-tight mb-6">
                    Mulai Wujudkan Zero Stunting di Wilayah Anda
                </h2>

                <p class="text-lg text-teal-100 max-w-2xl mx-auto leading-relaxed mb-8">
                    ZeroStunt.id membantu puskesmas dan posyandu mengelola pemantauan gizi,
                    pengadaan pangan lokal, serta distribusi paket bantuan secara lebih terarah.
                </p>

                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="/auth/login"
                        class="px-7 py-3 rounded-xl bg-amber-500 text-white font-bold hover:bg-amber-600 transition text-center">
                        Coba Demo Sekarang
                    </a>

                    <a href="mailto:info@zerostunt.id"
                        class="px-7 py-3 rounded-xl border border-teal-300 text-white font-bold hover:bg-white hover:text-teal-700 transition text-center">
                        Hubungi Kami
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-6 py-14">
                <div class="grid md:grid-cols-4 gap-10 mb-10">
                    <!-- Brand -->
                    <div class="md:col-span-2">
                        <a href="/" class="inline-flex items-center gap-2 font-bold text-xl text-teal-700 mb-4">
                            <span class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center">
                                🌱
                            </span>
                            ZeroStunt<span class="text-amber-500">.id</span>
                        </a>

                        <p class="text-gray-500 leading-relaxed max-w-md mb-5">
                            Sistem pencegahan stunting terintegrasi berbasis supply chain pangan lokal
                            dan pemantauan pertumbuhan anak menggunakan standar WHO 2006.
                        </p>

                        <div class="space-y-2 text-sm text-gray-500">
                            <p>📍 Teknologi Rekayasa Perangkat Lunak, ASTRATECH</p>
                            <p>✉️ info@zerostunt.id</p>
                        </div>
                    </div>

                    <!-- Modul -->
                    <div>
                        <h4 class="text-gray-900 font-bold mb-4">
                            Modul
                        </h4>

                        <ul class="space-y-3 text-sm text-gray-500">
                            <li><a href="#" class="hover:text-teal-700 transition">Pemeriksaan Anak</a></li>
                            <li><a href="#" class="hover:text-teal-700 transition">Pengadaan Pangan</a></li>
                            <li><a href="#" class="hover:text-teal-700 transition">Distribusi Internal</a></li>
                            <li><a href="#" class="hover:text-teal-700 transition">Penyerahan Bantuan</a></li>
                            <li><a href="#" class="hover:text-teal-700 transition">Laporan Analitik</a></li>
                        </ul>
                    </div>

                    <!-- Referensi -->
                    <div>
                        <h4 class="text-gray-900 font-bold mb-4">
                            Referensi
                        </h4>

                        <ul class="space-y-3 text-sm text-gray-500">
                            <li><a href="#" class="hover:text-teal-700 transition">WHO Growth Standards 2006</a></li>
                            <li><a href="#" class="hover:text-teal-700 transition">RIRN 2017–2045</a></li>
                            <li><a href="#" class="hover:text-teal-700 transition">SDGs Goal 2 & 3</a></li>
                            <li><a href="#" class="hover:text-teal-700 transition">Dokumentasi Sistem</a></li>
                            <li><a href="#" class="hover:text-teal-700 transition">Panduan Pengguna</a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-6 flex flex-col md:flex-row justify-between items-center gap-3">
                    <p class="text-xs text-gray-500 text-center md:text-left">
                        © 2025 ZeroStunt.id — Teknologi Rekayasa Perangkat Lunak, ASTRATECH. Versi 1.0.
                    </p>

                    <p class="text-xs text-gray-500 text-center md:text-right">
                        Mendukung
                        <span class="text-teal-700 font-bold">SDGs Goal 2</span>
                        &
                        <span class="text-teal-700 font-bold">Goal 3</span>
                    </p>
                </div>
            </div>
        </footer>
    </main>

</body>

</html>