<?php
require_once '../views/components/metric-card.php';

$pageTitle = 'Dashboard Ibu';
$pageSubtitle = 'Ringkasan data anak, status gizi, dan riwayat bantuan.';

$anaks = $anaks ?? [];
$ibu = $ibu ?? [];

$formatAngka = function ($value) {
    $value = (float) $value;

    if (floor($value) == $value) {
        return number_format($value, 0, ',', '.');
    }

    return number_format($value, 1, ',', '.');
};

$getBadgeStatusGizi = function ($status) {
    $status = strtolower(trim($status ?? ''));

    if (
        str_contains($status, 'prioritas 1') ||
        str_contains($status, 'berisiko stunting') ||
        str_contains($status, 'stunting') ||
        str_contains($status, 'buruk')
    ) {
        return 'bg-red-100 text-red-700';
    }

    if (
        str_contains($status, 'prioritas 2') ||
        str_contains($status, 'perlu pemantauan') ||
        str_contains($status, 'pemantauan') ||
        str_contains($status, 'kurang')
    ) {
        return 'bg-amber-100 text-amber-700';
    }

    return 'bg-green-100 text-green-700';
};

$getBadgePrioritas = function ($prioritas) {
    if ((int) $prioritas === 1) {
        return 'bg-red-600 text-white';
    }

    if ((int) $prioritas === 2) {
        return 'bg-amber-500 text-white';
    }

    return 'bg-gray-200 text-gray-700';
};

$totalAnak = count($anaks);

$totalSudahDiperiksa = count(array_filter($anaks, function ($anak) {
    return !empty($anak['tanggal_pemeriksaan_terakhir']);
}));

$prioritasTertinggi = null;

foreach ($anaks as $anak) {
    $prioritas = (int) ($anak['skala_prioritas'] ?? 3);

    if ($prioritasTertinggi === null || $prioritas < $prioritasTertinggi) {
        $prioritasTertinggi = $prioritas;
    }
}

if ((int)($ibu['is_pregnant'] ?? 0) === 1) {
    $prioritasTertinggi = 1;
}

$anakTerbaru = null;

foreach ($anaks as $anak) {
    if (!empty($anak['tanggal_pemeriksaan_terakhir'])) {
        if (
            $anakTerbaru === null ||
            strtotime($anak['tanggal_pemeriksaan_terakhir']) > strtotime($anakTerbaru['tanggal_pemeriksaan_terakhir'])
        ) {
            $anakTerbaru = $anak;
        }
    }
}

if (!empty($anakTerbaru['tanggal_pemeriksaan_terakhir'])) {
    $dayOfWeek = date('D', strtotime($anakTerbaru['tanggal_pemeriksaan_terakhir']));
    $dayNames = [
        'Sun' => 'Minggu',
        'Mon' => 'Senin',
        'Tue' => 'Selasa',
        'Wed' => 'Rabu',
        'Thu' => 'Kamis',
        'Fri' => "Jum'at",
        'Sat' => 'Sabtu'
    ];
    $dayName = $dayNames[$dayOfWeek] ?? '';
    $monthName = date('F', strtotime($anakTerbaru['tanggal_pemeriksaan_terakhir']));
    $monthNames = [
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember'
    ];
    $monthName = $monthNames[$monthName] ?? '';
    $formattedDate = date('j', strtotime($anakTerbaru['tanggal_pemeriksaan_terakhir'])) . ' ' . $monthName . ' ' . date('Y', strtotime($anakTerbaru['tanggal_pemeriksaan_terakhir']));
    $tanggalPemeriksaanTerakhir = $dayName . ', ' . $formattedDate;
} else {
    $tanggalPemeriksaanTerakhir = '-';
}

$metrics = [
    [
        'title' => 'Anak Terdaftar',
        'value' => $totalAnak,
        'caption' => 'Data anak pada akun ini',
        'icon' => '<i class="fa-solid fa-children"></i>',
        'tone' => 'teal',
    ],
    [
        'title' => 'Sudah Diperiksa',
        'value' => $totalSudahDiperiksa,
        'caption' => 'Memiliki riwayat pemeriksaan',
        'icon' => '<i class="fa-solid fa-user-doctor"></i>',
        'tone' => 'green',
    ],
    [
        'title' => 'Prioritas Tertinggi',
        'value' => $prioritasTertinggi ? 'Prioritas ' . $prioritasTertinggi : '-',
        'caption' => 'Berdasarkan data anak',
        'icon' => '<i class="fa-solid fa-triangle-exclamation"></i>',
        'tone' => 'amber',
    ],
    [
        'title' => 'Pemeriksaan Terakhir',
        'value' => $tanggalPemeriksaanTerakhir,
        'caption' => !empty($anakTerbaru['nama_anak']) ? $anakTerbaru['nama_anak'] : 'Belum ada pemeriksaan',
        'icon' => '<i class="fa-solid fa-calendar-check"></i>',
        'tone' => 'blue',
    ],
];

ob_start();
?>

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

<section class="grid xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">
                    Ringkasan Anak Saya
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Kondisi terbaru anak berdasarkan data pemeriksaan.
                </p>
            </div>

            <a href="/master/anak"
                class="hidden sm:inline-flex rounded-xl bg-teal-50 px-3 py-2 text-xs font-bold text-teal-700 hover:bg-teal-100">
                Kelola Anak
            </a>
        </div>

        <div class="p-6 grid gap-4 md:grid-cols-2">
            <?php if (empty($anaks)): ?>
                <div class="md:col-span-2 rounded-2xl bg-gray-50 px-5 py-8 text-center text-gray-500">
                    Belum ada data anak.
                </div>
            <?php endif; ?>

            <?php foreach ($anaks as $anak): ?>
                <?php
                if ($anak['st_gizi_skrg'] === 'Prioritas 1') {
                    $statusGizi = 'Berisiko Stunting';
                } elseif ($anak['st_gizi_skrg'] === 'Prioritas 2') {
                    $statusGizi = 'Perlu Pemantauan';
                } else {
                    $statusGizi = 'Normal';
                }
                $skalaPrioritas = $anak['skala_prioritas'] ?? 3;
                ?>

                <div class="rounded-3xl border border-gray-100 bg-gray-50 p-5">
                    <div class="mb-4 flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-extrabold text-gray-900">
                                <?= htmlspecialchars($anak['nama_anak'] ?? '-'); ?>
                            </h3>
                            <p class="text-sm text-gray-500">
                                <?= ($anak['jenis_kelamin'] ?? '') === 'L' ? 'Laki-laki' : 'Perempuan'; ?> ·
                                <?= htmlspecialchars($anak['tgl_lahir'] ?? '-'); ?>
                            </p>
                        </div>

                        <span class="px-3 py-1 text-xs font-bold rounded <?= $getBadgePrioritas($skalaPrioritas); ?>">
                            Prioritas <?= htmlspecialchars($skalaPrioritas); ?>
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="rounded-2xl bg-white p-4">
                            <p class="text-xs text-gray-500">Berat Terakhir</p>
                            <p class="mt-1 text-xl font-extrabold text-teal-700">
                                <?= !empty($anak['berat_badan_terakhir'])
                                    ? $formatAngka($anak['berat_badan_terakhir']) . ' Kg'
                                    : '-'; ?>
                            </p>
                        </div>

                        <div class="rounded-2xl bg-white p-4">
                            <p class="text-xs text-gray-500">Tinggi Terakhir</p>
                            <p class="mt-1 text-xl font-extrabold text-green-700">
                                <?= !empty($anak['tinggi_badan_terakhir'])
                                    ? $formatAngka($anak['tinggi_badan_terakhir']) . ' Cm'
                                    : '-'; ?>
                            </p>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white p-4">
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <p class="text-sm font-bold text-gray-700">
                                Status Gizi
                            </p>

                            <span class="px-3 py-1 text-xs font-bold rounded-full <?= $getBadgeStatusGizi($statusGizi); ?>">
                                <?= htmlspecialchars($statusGizi); ?>
                            </span>
                        </div>

                        <div class="rounded-2xl bg-white p-4 mt-4">
                            <p class="text-sm font-bold text-gray-700 mb-3">Tren Pertumbuhan</p>
                            <?php if (count($anak['riwayat_grafik']) >= 2): ?>
                                <canvas id="chart-anak-<?= $anak['id_anak']; ?>" height="120"></canvas>
                            <?php else: ?>
                                <p class="text-xs text-gray-400">Data belum cukup untuk menampilkan tren (minimal 2
                                    pemeriksaan).</p>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($anak['tanggal_pemeriksaan_terakhir'])): ?>
                            <p class="text-sm text-gray-500">
                                Pemeriksaan terakhir:
                                <span class="font-bold text-gray-800">
                                    <?= date('d/m/Y', strtotime($anak['tanggal_pemeriksaan_terakhir'])); ?>
                                </span>
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                Usia saat pemeriksaan: <?= htmlspecialchars($anak['usia_bulan_terakhir'] ?? '-'); ?> bulan
                            </p>
                        <?php else: ?>
                            <p class="text-sm text-gray-500">
                                Anak belum memiliki riwayat pemeriksaan.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-xl font-extrabold text-gray-900 mb-5">
            Informasi Bantuan
        </h2>

        <div class="space-y-4">
            <?php if (($ibu['is_pregnant'] ?? 0) === 1): ?>
                <div class="rounded-2xl bg-red-50 p-4 border border-red-100">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-sm font-bold text-red-700">Status Ibu: Hamil</p>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-red-600 text-white">Prioritas 1</span>
                    </div>
                    <p class="text-xs text-red-600/90">
                        Karena status sedang hamil, Anda otomatis mendapatkan Prioritas 1 untuk pemantauan dan paket bantuan.
                    </p>
                </div>
            <?php endif; ?>
            <div class="rounded-2xl bg-teal-50 p-4">
                <p class="text-sm text-teal-700/80">
                    Paket bantuan akan mengikuti prioritas anak.
                </p>
                <p class="mt-2 text-2xl font-extrabold text-teal-700">
                    Otomatis
                </p>
            </div>

            <div class="rounded-2xl bg-amber-50 p-4">
                <p class="text-sm text-amber-700/80">
                    Prioritas 1 mendapat paket dengan jumlah bantuan paling besar.
                </p>
                <p class="mt-2 text-lg font-extrabold text-amber-700">
                    Berdasarkan pemeriksaan
                </p>
            </div>

            <a href="/master/ibu/histori-bantuan"
                class="inline-flex w-full items-center justify-center rounded-2xl bg-green-50 px-5 py-3 text-sm font-bold text-green-700 hover:bg-green-100">
                Lihat Riwayat Bantuan
            </a>

            <a href="/master/ibu/riwayat-periksa"
                class="inline-flex w-full items-center justify-center rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white hover:bg-teal-700">
                Lihat Riwayat Pemeriksaan
            </a>
        </div>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
<?php foreach ($anaks as $anak): ?>
    <?php if (count($anak['riwayat_grafik']) >= 2): ?>
                new Chart(document.getElementById('chart-anak-<?= $anak['id_anak']; ?>'), {
                    type: 'line',
                    data: {
                        labels: <?= json_encode(array_map(fn($r) => date('M Y', strtotime($r['tanggal_pemeriksaan'])), $anak['riwayat_grafik'])); ?>,
                    datasets: [{
                        label: 'Berat Badan (Kg)',
                        data: <?= json_encode(array_map(fn($r) => (float) $r['berat_badan'], $anak['riwayat_grafik'])); ?>,
                        borderColor: '#0f766e',
                        backgroundColor: 'rgba(15, 118, 110, 0.1)',
                        tension: 0.3,
                        fill: true
                }]
            },
                    options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: false } }
                }
        });
    <?php endif; ?>
<?php endforeach; ?>
</script>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>