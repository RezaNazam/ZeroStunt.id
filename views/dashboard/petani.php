<?php
require_once '../views/components/metric-card.php';
require_once '../models/Pengadaan.php';

$idPetani = (int) $_SESSION['user_id'];

$pengadaanModel = new Pengadaan();
$kontrakSaya = $pengadaanModel->getByPetani($idPetani);
$totalPengadaanAktif = count($kontrakSaya); // Menghitung total data aktif langsung dari array

$pageTitle = 'Dashboard Petani';
$pageSubtitle = 'Ringkasan pengadaan, pendapatan, dan komoditas pangan lokal.';

$metrics = [
    [
        'title' => 'Pengadaan Aktif',
        'value' => $totalPengadaanAktif,
        'caption' => 'Kontrak sedang berjalan',
        'icon' => '<i class="fa-solid fa-file-signature"></i>',
        'tone' => 'teal',
    ],
    [
        'title' => 'Total Pendapatan',
        'value' => 'Rp 8.750.000',
        'caption' => 'Akumulasi bulan ini',
        'icon' => '<i class="fa-solid fa-wallet"></i>',
        'tone' => 'green',
    ],
    [
        'title' => 'Status Pembayaran',
        'value' => '2 Lunas',
        'caption' => '1 pengadaan diproses',
        'icon' => '<i class="fa-solid fa-circle-check"></i>',
        'tone' => 'amber',
    ],
    [
        'title' => 'Kapasitas Panen',
        'value' => '120 kg',
        'caption' => 'Estimasi siap kirim',
        'icon' => '<i class="fa-solid fa-seedling"></i>',
        'tone' => 'blue',
    ],
];

$orders = [
    ['komoditas' => 'Ikan Nila', 'jumlah' => '40 kg', 'tujuan' => 'Gudang Puskesmas', 'tanggal' => '05 Juni 2025', 'status' => 'Diterima', 'badge' => 'green'],
    ['komoditas' => 'Sayur Hijau', 'jumlah' => '25 kg', 'tujuan' => 'Gudang Puskesmas', 'tanggal' => '08 Juni 2025', 'status' => 'Diproses', 'badge' => 'amber'],
    ['komoditas' => 'Kacang-kacangan', 'jumlah' => '35 kg', 'tujuan' => 'Gudang Puskesmas', 'tanggal' => '12 Juni 2025', 'status' => 'Menunggu', 'badge' => 'blue'],
];

$payments = [
    ['kode' => 'PGD-001', 'komoditas' => 'Ikan Nila', 'nominal' => 'Rp 2.800.000', 'status' => 'Lunas', 'badge' => 'green'],
    ['kode' => 'PGD-002', 'komoditas' => 'Telur Ayam', 'nominal' => 'Rp 3.450.000', 'status' => 'Lunas', 'badge' => 'green'],
    ['kode' => 'PGD-003', 'komoditas' => 'Sayur Hijau', 'nominal' => 'Rp 1.250.000', 'status' => 'Diproses', 'badge' => 'amber'],
];

ob_start();
?>

<!-- Metric Cards -->
<section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <?php foreach ($metrics as $metric): ?>
        <?php renderMetricCard(
            $metric['title'],
            $metric['value'],
            $metric['caption'],
            $metric['icon'],
            $metric['tone']
        ); ?>
    <?php endforeach; ?>
</section>

<!-- Main Grid -->
<section class="grid xl:grid-cols-3 gap-6">
    <!-- Produksi / Panen -->
    <div class="xl:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">
                    Tren Pasokan Komoditas
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Contoh volume komoditas siap kirim per bulan.
                </p>
            </div>

            <span class="px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-bold">
                6 Bulan
            </span>
        </div>

        <div class="h-72 flex items-end gap-3">
            <?php foreach ([50, 65, 58, 80, 72, 95] as $index => $height): ?>
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full rounded-t-xl bg-green-500/80 hover:bg-green-600 transition"
                        style="height: <?= $height * 2; ?>px;">
                    </div>
                    <span class="text-xs text-gray-400">
                        <?= ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'][$index]; ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Profil Lahan -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-xl font-extrabold text-gray-900 mb-5">
            Profil Lahan
        </h2>

        <div class="space-y-4">
            <div class="rounded-2xl bg-green-50 p-4">
                <p class="text-sm text-green-700/80">Jenis Komoditas</p>
                <p class="text-2xl font-extrabold text-green-700 mt-1">Sayur & Ikan</p>
            </div>

            <div class="rounded-2xl bg-teal-50 p-4">
                <p class="text-sm text-teal-700/80">Luas / Kapasitas</p>
                <p class="text-2xl font-extrabold text-teal-700 mt-1">0.8 ha</p>
            </div>

            <div class="rounded-2xl bg-amber-50 p-4">
                <p class="text-sm text-amber-700/80">Status Mitra</p>
                <p class="text-2xl font-extrabold text-amber-700 mt-1">Aktif</p>
            </div>
        </div>
    </div>
</section>

<!-- Tables -->
<section class="grid xl:grid-cols-2 gap-6 mt-8">
    <!-- Pengadaan Saya -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-extrabold text-gray-900">
                Pengadaan Saya
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Daftar permintaan komoditas dari puskesmas.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Komoditas</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Jumlah</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Tanggal</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($orders as $row): ?>
                        <?php
                        $badgeClass = [
                            'green' => 'bg-green-50 text-green-700',
                            'amber' => 'bg-amber-50 text-amber-700',
                            'blue' => 'bg-blue-50 text-blue-700',
                        ][$row['badge']];
                        ?>

                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                <?= htmlspecialchars($row['komoditas']); ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($row['jumlah']); ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($row['tanggal']); ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $badgeClass; ?>">
                                    <?= htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Riwayat Ekonomi -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-xl font-extrabold text-gray-900">
                Riwayat Ekonomi
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Contoh riwayat pembayaran pengadaan.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Kode</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Komoditas</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Nominal</th>
                        <th class="text-left px-6 py-4 font-bold text-gray-600">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($payments as $row): ?>
                        <?php
                        $badgeClass = [
                            'green' => 'bg-green-50 text-green-700',
                            'amber' => 'bg-amber-50 text-amber-700',
                        ][$row['badge']];
                        ?>

                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                <?= htmlspecialchars($row['kode']); ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($row['komoditas']); ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($row['nominal']); ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $badgeClass; ?>">
                                    <?= htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
