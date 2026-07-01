<?php
$pageTitle = 'Dashboard Ibu';
$pageSubtitle = 'Ringkasan data anak, status gizi, dan riwayat bantuan.';

$anaks = $anaks ?? [];

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

$anakUtama = $anaks[0] ?? null;

$bbTerakhir = $anakUtama['berat_badan_terakhir'] ?? null;
$tbTerakhir = $anakUtama['tinggi_badan_terakhir'] ?? null;

if (!empty($anak['tanggal_pemeriksaan_terakhir'])) {
    $dayOfWeek = date('D', strtotime($anak['tanggal_pemeriksaan_terakhir']));
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
    $monthName = date('F', strtotime($anak['tanggal_pemeriksaan_terakhir']));
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
    $formattedDate = date('j', strtotime($anak['tanggal_pemeriksaan_terakhir'])) . ' ' . $monthName . ' ' . date('Y', strtotime($anak['tanggal_pemeriksaan_terakhir']));
    $tanggalPemeriksaanTerakhir = $dayName . ', ' . $formattedDate;
} else {
    $tanggalPemeriksaanTerakhir = '-';
}

ob_start();
?>

<!-- Page Content -->
<div class="w-full bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-8 py-6 border-b border-gray-100 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">
                Data Anak Saya
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Pantau pemeriksaan terakhir, status gizi, dan prioritas bantuan anak.
            </p>
        </div>

        <a href="/master/anak/create"
            class="inline-flex items-center justify-center rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-700">
            + Daftarkan Anak
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50">
                <tr class="text-gray-600 text-sm font-bold">
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4">Nama Anak</th>
                    <th class="px-6 py-4">Tgl Lahir / Jenis Kelamin</th>
                    <th class="px-6 py-4">Pemeriksaan Terakhir</th>
                    <th class="px-6 py-4">BB / TB Terakhir</th>
                    <th class="px-6 py-4 min-w-[150px]">Status Gizi</th>
                    <th class="px-6 py-4">Prioritas</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                <?php if (!empty($anaks)): ?>
                    <?php $no = 1; ?>
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

                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium">
                                <?= $no++; ?>
                            </td>

                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900">
                                    <?= htmlspecialchars($anak['nama_anak'] ?? '-'); ?>
                                </p>
                                <p class="text-xs text-gray-500">
                                    Ibu: <?= htmlspecialchars($anak['nama_ibu'] ?? '-'); ?>
                                </p>
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">
                                    <?= htmlspecialchars($anak['tgl_lahir'] ?? '-'); ?>
                                </div>
                                <div class="text-xs text-gray-400">
                                    <?= ($anak['jenis_kelamin'] ?? '') === 'L' ? 'Laki-laki' : 'Perempuan'; ?>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <?php if (!empty($anak['tanggal_pemeriksaan_terakhir'])): ?>
                                    <div class="font-bold text-gray-900">
                                        <?= htmlspecialchars($tanggalPemeriksaanTerakhir); ?>
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        Usia <?= htmlspecialchars($anak['usia_bulan_terakhir'] ?? '-'); ?> bulan
                                    </div>
                                <?php else: ?>
                                    <span class="text-gray-400">Belum diperiksa</span>
                                <?php endif; ?>
                            </td>

                            <td class="px-6 py-4">
                                <?php if (!empty($anak['berat_badan_terakhir']) && !empty($anak['tinggi_badan_terakhir'])): ?>
                                    <div class="font-bold text-gray-900">
                                        <?= $formatAngka($anak['berat_badan_terakhir']); ?> Kg
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <?= $formatAngka($anak['tinggi_badan_terakhir']); ?> Cm
                                    </div>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex w-max min-w-max whitespace-nowrap px-3 py-1 text-xs font-bold rounded-full <?= $getBadgeStatusGizi($statusGizi); ?>">
                                    <?= htmlspecialchars($statusGizi); ?>
                                </span>
                            </td>

                            <td class="px-6 py-4 min-w-[150px]">
                                <span class="px-3 py-1 text-xs font-bold rounded <?= $getBadgePrioritas($skalaPrioritas); ?>">
                                    <?= htmlspecialchars($skalaPrioritas); ?>
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex gap-2">
                                    <a href="/master/anak/edit?id=<?= (int) $anak['id_anak']; ?>"
                                        class="text-teal-600 hover:text-teal-800 font-bold text-xs bg-teal-50 px-3 py-1.5 rounded-xl">
                                        Edit
                                    </a>

                                    <a href="/master/anak/delete?id=<?= (int) $anak['id_anak']; ?>"
                                        onclick="return confirm('Hapus data anak ini?')"
                                        class="text-red-600 hover:text-red-800 font-bold text-xs bg-red-50 px-3 py-1.5 rounded-xl">
                                        Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="py-10 text-center text-gray-400">
                            Belum ada data anak.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
