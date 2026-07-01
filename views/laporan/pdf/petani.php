<?php
$data = $data ?? [];
$petani = $data['petani_info'] ?? [];
$summary = $data['summary'] ?? [];
$komoditasLahan = $data['komoditas_lahan'] ?? [];
$pengadaan = $data['pengadaan'] ?? [];

$startDate = $data['start_date'] ?? date('Y-m-01');
$endDate = $data['end_date'] ?? date('Y-m-d');

$formatAngka = function ($value) {
    $value = (float) $value;

    if (floor($value) == $value) {
        return number_format($value, 0, ',', '.');
    }

    return number_format($value, 1, ',', '.');
};

$formatRupiah = function ($value) {
    return 'Rp ' . number_format((float) $value, 0, ',', '.');
};

$logoPath = dirname(__DIR__, 3) . '/public/img/icon-logo.png';
$logoSrc = null;

if (is_readable($logoPath)) {
    $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
            margin: 28px;
        }

        .kop {
            width: 100%;
            border-bottom: 3px solid #0f766e;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .brand-title {
            font-size: 24px;
            font-weight: bold;
            color: #111827;
            margin: 0;
        }

        .brand-title span {
            color: #f59e0b;
        }

        .brand-subtitle {
            margin-top: 4px;
            color: #6b7280;
            font-size: 11px;
        }

        .report-title {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            color: #0f766e;
        }

        .report-period {
            text-align: right;
            margin-top: 4px;
            color: #6b7280;
            font-size: 10px;
        }

        .logo-box {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: #0f766e;
            color: white;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            line-height: 52px;
        }

        .info-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .info-box td {
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
        }

        .info-label {
            width: 130px;
            background: #f9fafb;
            color: #6b7280;
            font-weight: bold;
        }

        .summary {
            width: 100%;
            margin-top: 16px;
            border-collapse: collapse;
        }

        .summary td {
            border: 1px solid #e5e7eb;
            padding: 10px;
            width: 25%;
        }

        .summary .value {
            font-size: 17px;
            font-weight: bold;
            margin-top: 4px;
        }

        h2 {
            font-size: 14px;
            margin: 18px 0 8px 0;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        table.data th {
            background: #f3f4f6;
            text-align: left;
            padding: 8px;
            border: 1px solid #e5e7eb;
        }

        table.data td {
            padding: 8px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .footer {
            margin-top: 24px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>

<body>
    <table class="kop">
        <tr>
            <td style="width: 60px;">
                <?php if ($logoSrc): ?>
                    <img src="<?= $logoSrc; ?>" style="width:52px;height:52px;">
                <?php else: ?>
                    <div class="logo-box">ZS</div>
                <?php endif; ?>
            </td>

            <td>
                <p class="brand-title">ZeroStunt<span>.id</span></p>
                <div class="brand-subtitle">Sistem Monitoring Gizi Anak dan Distribusi Bantuan Pangan</div>
                <div class="brand-subtitle">Laporan ini dibuat otomatis berdasarkan data pada sistem ZeroStunt.id</div>
            </td>

            <td style="width: 230px;">
                <div class="report-title">LAPORAN PETANI</div>
                <div class="report-period">
                    Periode <?= date('d/m/Y', strtotime($startDate)); ?> - <?= date('d/m/Y', strtotime($endDate)); ?>
                </div>
                <div class="report-period">
                    Dicetak: <?= date('d/m/Y H:i'); ?>
                </div>
            </td>
        </tr>
    </table>

    <table class="info-box">
        <tr>
            <td class="info-label">Nama Lahan</td>
            <td><?= htmlspecialchars($petani['nama_lahan'] ?? '-'); ?></td>

            <td class="info-label">Jenis Usaha</td>
            <td><?= htmlspecialchars($petani['jenis_usaha'] ?? '-'); ?></td>
        </tr>

        <tr>
            <td class="info-label">Alamat Lahan</td>
            <td colspan="3"><?= htmlspecialchars($petani['alamat_lahan'] ?? '-'); ?></td>
        </tr>

        <tr>
            <td class="info-label">No. Rekening</td>
            <td><?= htmlspecialchars($petani['no_rekening'] ?? '-'); ?></td>

            <td class="info-label">Kapasitas Panen</td>
            <td><?= $formatAngka($petani['kapasitas_panen_bulan'] ?? 0); ?> Kg / bulan</td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td>
                <div>Total Kontrak</div>
                <div class="value"><?= $summary['total_kontrak'] ?? 0; ?></div>
            </td>
            <td>
                <div>Kontrak Lunas</div>
                <div class="value"><?= $summary['total_lunas'] ?? 0; ?></div>
            </td>
            <td>
                <div>Kontrak Pending</div>
                <div class="value"><?= $summary['total_pending'] ?? 0; ?></div>
            </td>
            <td>
                <div>Pendapatan Lunas</div>
                <div class="value"><?= $formatRupiah($summary['pendapatan_lunas'] ?? 0); ?></div>
            </td>
        </tr>
    </table>

    <h2>Komoditas Lahan</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Komoditas</th>
                <th>Luas Area</th>
                <th>Estimasi Panen</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($komoditasLahan)): ?>
                <tr><td colspan="4">Belum ada komoditas lahan.</td></tr>
            <?php endif; ?>

            <?php foreach ($komoditasLahan as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama_komoditas'] ?? '-'); ?></td>
                    <td><?= $formatAngka($row['luas_area'] ?? 0); ?> <?= htmlspecialchars($petani['satuan_luas'] ?? 'ha'); ?></td>
                    <td><?= $formatAngka($row['estimasi_panen'] ?? 0); ?> <?= htmlspecialchars($row['satuan'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['catatan'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Kontrak Pengadaan</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>No Kontrak</th>
                <th>Gudang</th>
                <th>Komoditas</th>
                <th>Nilai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pengadaan)): ?>
                <tr><td colspan="6">Belum ada pengadaan pada periode ini.</td></tr>
            <?php endif; ?>

            <?php foreach ($pengadaan as $row): ?>
                <tr>
                    <td><?= !empty($row['tgl_pengadaan']) ? date('d/m/Y', strtotime($row['tgl_pengadaan'])) : '-'; ?></td>
                    <td><?= htmlspecialchars($row['no_kontrak'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['nama_gudang'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['detail_komoditas'] ?? '-'); ?></td>
                    <td><?= $formatRupiah($row['total_nilai'] ?? 0); ?></td>
                    <td><?= htmlspecialchars($row['status_bayar'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis melalui ZeroStunt.id pada <?= date('d/m/Y H:i'); ?>
    </div>
</body>
</html>