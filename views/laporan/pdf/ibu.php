<?php
$data = $data ?? [];
$summary = $data['summary'] ?? [];
$ibuInfo = $data['ibu_info'] ?? [];
$anak = $data['anak'] ?? [];
$pemeriksaan = $data['pemeriksaan'] ?? [];
$bantuan = $data['bantuan'] ?? [];

$startDate = $data['start_date'] ?? date('Y-m-01');
$endDate = $data['end_date'] ?? date('Y-m-d');

$formatAngka = function ($value) {
    $value = (float) $value;

    if (floor($value) == $value) {
        return number_format($value, 0, ',', '.');
    }

    return number_format($value, 1, ',', '.');
};
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

        .brand-row {
            width: 100%;
        }

        .logo-box {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: #0f766e;
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            line-height: 52px;
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
            width: 120px;
            background: #f9fafb;
            color: #6b7280;
            font-weight: bold;
        }

        h2 {
            font-size: 14px;
            margin: 18px 0 8px 0;
        }

        .muted {
            color: #6b7280;
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
            font-size: 18px;
            font-weight: bold;
            margin-top: 4px;
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
                <?php
                $logoPath = dirname(__DIR__, 3) . '/public/img/icon-logo.png';
                $logoSrc = null;

                if (is_readable($logoPath)) {
                    $logoData = base64_encode(file_get_contents($logoPath));
                    $logoSrc = 'data:image/png;base64,' . $logoData;
                }
                ?>

                <?php if ($logoSrc): ?>
                    <img src="<?= $logoSrc; ?>" style="width:52px;height:52px;">
                <?php else: ?>
                    <div class="logo-box">ZS</div>
                <?php endif; ?>
            </td>

            <td>
                <p class="brand-title">
                    ZeroStunt<span>.id</span>
                </p>
                <div class="brand-subtitle">
                    Sistem Monitoring Gizi Anak dan Distribusi Bantuan Pangan
                </div>
                <div class="brand-subtitle">
                    Laporan ini dibuat otomatis berdasarkan data pada sistem ZeroStunt.id
                </div>
            </td>

            <td style="width: 230px;">
                <div class="report-title">
                    LAPORAN IBU & ANAK
                </div>
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
            <td class="info-label">Nama Ibu</td>
            <td><?= htmlspecialchars($ibuInfo['nama_ibu'] ?? '-'); ?></td>

            <td class="info-label">Posyandu</td>
            <td><?= htmlspecialchars($ibuInfo['nama_posyandu'] ?? '-'); ?></td>
        </tr>

        <tr>
            <td class="info-label">NIK Ibu</td>
            <td><?= htmlspecialchars($ibuInfo['NIK_ibu'] ?? '-'); ?></td>

            <td class="info-label">No. HP</td>
            <td><?= htmlspecialchars($ibuInfo['no_telp'] ?? '-'); ?></td>
        </tr>

        <tr>
            <td class="info-label">Alamat</td>
            <td colspan="3"><?= htmlspecialchars($ibuInfo['alamat'] ?? '-'); ?></td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td>
                <div class="muted">Total Anak</div>
                <div class="value"><?= $summary['total_anak'] ?? 0; ?></div>
            </td>
            <td>
                <div class="muted">Pemeriksaan</div>
                <div class="value"><?= $summary['total_pemeriksaan'] ?? 0; ?></div>
            </td>
            <td>
                <div class="muted">Bantuan</div>
                <div class="value"><?= $summary['total_bantuan'] ?? 0; ?></div>
            </td>
            <td>
                <div class="muted">Prioritas 1</div>
                <div class="value"><?= $summary['prioritas_1'] ?? 0; ?></div>
            </td>
        </tr>
    </table>

    <h2>Data Anak</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Nama Anak</th>
                <th>Status Gizi</th>
                <th>Prioritas</th>
                <th>Pemeriksaan Terakhir</th>
                <th>BB / TB</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($anak)): ?>
                <tr>
                    <td colspan="5">Belum ada data anak.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($anak as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama_anak'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['st_gizi_skrg'] ?? '-'); ?></td>
                    <td>Prioritas <?= htmlspecialchars($row['skala_prioritas'] ?? '-'); ?></td>
                    <td>
                        <?= !empty($row['tanggal_pemeriksaan_terakhir'])
                            ? date('d/m/Y', strtotime($row['tanggal_pemeriksaan_terakhir']))
                            : '-'; ?>
                    </td>
                    <td>
                        <?= !empty($row['berat_badan_terakhir'])
                            ? $formatAngka($row['berat_badan_terakhir']) . ' Kg / ' . $formatAngka($row['tinggi_badan_terakhir']) . ' Cm'
                            : '-'; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Riwayat Pemeriksaan</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Anak</th>
                <th>Berat</th>
                <th>Tinggi</th>
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
                    <td><?= $formatAngka($row['berat_badan'] ?? 0); ?> Kg</td>
                    <td><?= $formatAngka($row['tinggi_badan'] ?? 0); ?> Cm</td>
                    <td><?= htmlspecialchars($row['status_gizi'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Histori Bantuan</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Paket</th>
                <th>Isi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($bantuan)): ?>
                <tr>
                    <td colspan="4">Belum ada bantuan pada periode ini.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($bantuan as $row): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($row['tanggal_penyerahan'])); ?></td>
                    <td><?= htmlspecialchars($row['nama_paket'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['isi_paket'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['status_penyerahan'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        <table style="width: 100%;">
            <tr>
                <td>
                    Dicetak otomatis melalui ZeroStunt.id pada <?= date('d/m/Y H:i'); ?>
                </td>
                <td style="text-align: right;">
                    Halaman laporan ibu & anak
                </td>
            </tr>
        </table>
    </div>
</body>

</html>