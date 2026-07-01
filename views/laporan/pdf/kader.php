<?php
$data = $data ?? [];

$kader = $data['kader_info'] ?? [];
$summary = $data['summary'] ?? [];
$pemeriksaan = $data['pemeriksaan'] ?? [];
$stok = $data['stok'] ?? [];
$distribusi = $data['distribusi'] ?? [];
$penyerahan = $data['penyerahan'] ?? [];

$startDate = $data['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $data['end_date'] ?? date('Y-m-d');

$formatAngka = function ($value) {
    $value = (float) $value;

    if (floor($value) == $value) {
        return number_format($value, 0, ',', '.');
    }

    return number_format($value, 1, ',', '.');
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
                <div class="report-title">LAPORAN KADER</div>
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
            <td class="info-label">Username Kader</td>
            <td><?= htmlspecialchars($kader['username'] ?? '-'); ?></td>

            <td class="info-label">Posyandu</td>
            <td><?= htmlspecialchars($kader['nama_gudang'] ?? '-'); ?></td>
        </tr>

        <tr>
            <td class="info-label">Jenis Gudang</td>
            <td><?= htmlspecialchars($kader['jenis_gudang'] ?? '-'); ?></td>

            <td class="info-label">Alamat</td>
            <td><?= htmlspecialchars($kader['alamat_lengkap'] ?? '-'); ?></td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td>
                <div>Total Anak</div>
                <div class="value"><?= $summary['total_anak'] ?? 0; ?></div>
            </td>
            <td>
                <div>Pemeriksaan</div>
                <div class="value"><?= $summary['total_pemeriksaan'] ?? 0; ?></div>
            </td>
            <td>
                <div>Penyerahan</div>
                <div class="value"><?= $summary['total_penyerahan'] ?? 0; ?></div>
            </td>
            <td>
                <div>Jenis Stok</div>
                <div class="value"><?= $summary['jenis_stok'] ?? 0; ?></div>
            </td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td>
                <div>Prioritas 1</div>
                <div class="value"><?= $summary['prioritas_1'] ?? 0; ?></div>
            </td>
            <td>
                <div>Prioritas 2</div>
                <div class="value"><?= $summary['prioritas_2'] ?? 0; ?></div>
            </td>
            <td>
                <div>Prioritas 3</div>
                <div class="value"><?= $summary['prioritas_3'] ?? 0; ?></div>
            </td>
            <td>
                <div>Total Prioritas</div>
                <div class="value">
                    <?= ($summary['prioritas_1'] ?? 0) + ($summary['prioritas_2'] ?? 0) + ($summary['prioritas_3'] ?? 0); ?>
                </div>
            </td>
        </tr>
    </table>

    <h2>Riwayat Pemeriksaan</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Anak</th>
                <th>Ibu</th>
                <th>BB / TB</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($pemeriksaan)): ?>
                <tr>
                    <td colspan="5">Belum ada pemeriksaan pada periode ini.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($pemeriksaan as $row): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($row['tanggal_pemeriksaan'])); ?></td>
                    <td><?= htmlspecialchars($row['nama_anak'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['nama_ibu'] ?? '-'); ?></td>
                    <td>
                        <?= $formatAngka($row['berat_badan'] ?? 0); ?> Kg /
                        <?= $formatAngka($row['tinggi_badan'] ?? 0); ?> Cm
                    </td>
                    <td><?= htmlspecialchars($row['status_gizi'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Stok Posyandu</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Komoditas</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Stok Saat Ini</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($stok)): ?>
                <tr>
                    <td colspan="4">Belum ada stok posyandu.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($stok as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama_komoditas'] ?? '-'); ?></td>
                    <td><?= $formatAngka($row['qty_in'] ?? 0); ?> <?= htmlspecialchars($row['satuan'] ?? '-'); ?></td>
                    <td><?= $formatAngka($row['qty_out'] ?? 0); ?> <?= htmlspecialchars($row['satuan'] ?? '-'); ?></td>
                    <td><?= $formatAngka($row['qty_current'] ?? 0); ?> <?= htmlspecialchars($row['satuan'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Distribusi Masuk</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>No Distribusi</th>
                <th>Asal</th>
                <th>Isi</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($distribusi)): ?>
                <tr>
                    <td colspan="5">Belum ada distribusi pada periode ini.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($distribusi as $row): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($row['tanggal_distribusi'])); ?></td>
                    <td><?= htmlspecialchars($row['no_distribusi'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['gudang_asal'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['detail_distribusi'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['status_distribusi'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Penyerahan Paket</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Penerima</th>
                <th>Paket</th>
                <th>Isi</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($penyerahan)): ?>
                <tr>
                    <td colspan="5">Belum ada penyerahan pada periode ini.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($penyerahan as $row): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($row['tanggal_penyerahan'])); ?></td>
                    <td>
                        <?= htmlspecialchars($row['nama_anak'] ?? '-'); ?><br>
                        <span style="color:#6b7280;">Ibu: <?= htmlspecialchars($row['nama_ibu'] ?? '-'); ?></span>
                    </td>
                    <td><?= htmlspecialchars($row['nama_paket'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['isi_paket'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['status_penyerahan'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis melalui ZeroStunt.id pada <?= date('d/m/Y H:i'); ?>
    </div>
</body>

</html>