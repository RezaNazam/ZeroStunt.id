<?php
$data = $data ?? [];

$summary = $data['summary'] ?? [];
$pemeriksaan = $data['pemeriksaan'] ?? [];
$pengadaan = $data['pengadaan'] ?? [];
$distribusi = $data['distribusi'] ?? [];
$penyerahan = $data['penyerahan'] ?? [];
$stok = $data['stok'] ?? [];

$startDate = $data['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
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
            font-size: 10px;
            color: #111827;
            margin: 22px;
        }

        .kop {
            width: 100%;
            border-bottom: 3px solid #0f766e;
            padding-bottom: 12px;
            margin-bottom: 16px;
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
            font-size: 10px;
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
            margin-bottom: 14px;
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
            margin-top: 12px;
            border-collapse: collapse;
        }

        .summary td {
            border: 1px solid #e5e7eb;
            padding: 9px;
            width: 12.5%;
        }

        .summary .label {
            color: #6b7280;
            font-size: 9px;
        }

        .summary .value {
            font-size: 15px;
            font-weight: bold;
            margin-top: 3px;
        }

        h2 {
            font-size: 13px;
            margin: 16px 0 7px 0;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            page-break-inside: auto;
        }

        table.data th {
            background: #f3f4f6;
            text-align: left;
            padding: 7px;
            border: 1px solid #e5e7eb;
            font-size: 9px;
        }

        table.data td {
            padding: 7px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
            font-size: 9px;
        }

        tr {
            page-break-inside: avoid;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #6b7280;
        }

        .text-muted {
            color: #6b7280;
        }

        .text-green {
            color: #047857;
            font-weight: bold;
        }

        .text-red {
            color: #b91c1c;
            font-weight: bold;
        }

        .text-amber {
            color: #b45309;
            font-weight: bold;
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
                <div class="brand-subtitle">
                    Sistem Monitoring Gizi Anak dan Distribusi Bantuan Pangan
                </div>
                <div class="brand-subtitle">
                    Laporan ini dibuat otomatis berdasarkan data pada sistem ZeroStunt.id
                </div>
            </td>

            <td style="width: 260px;">
                <div class="report-title">LAPORAN ADMIN</div>
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
            <td class="info-label">Nama Sistem</td>
            <td>ZeroStunt.id</td>

            <td class="info-label">Jenis Laporan</td>
            <td>Laporan Global Admin</td>
        </tr>

        <tr>
            <td class="info-label">Cakupan Data</td>
            <td>Seluruh data anak, stok, pengadaan, distribusi, dan penyerahan</td>

            <td class="info-label">Periode</td>
            <td><?= date('d/m/Y', strtotime($startDate)); ?> - <?= date('d/m/Y', strtotime($endDate)); ?></td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Total Anak</div>
                <div class="value"><?= $summary['total_anak'] ?? 0; ?></div>
            </td>
            <td>
                <div class="label">Prioritas 1</div>
                <div class="value text-red"><?= $summary['prioritas_1'] ?? 0; ?></div>
            </td>
            <td>
                <div class="label">Prioritas 2</div>
                <div class="value text-amber"><?= $summary['prioritas_2'] ?? 0; ?></div>
            </td>
            <td>
                <div class="label">Prioritas 3</div>
                <div class="value text-green"><?= $summary['prioritas_3'] ?? 0; ?></div>
            </td>
            <td>
                <div class="label">Pemeriksaan</div>
                <div class="value"><?= $summary['total_pemeriksaan'] ?? 0; ?></div>
            </td>
            <td>
                <div class="label">Pengadaan</div>
                <div class="value"><?= $summary['total_pengadaan'] ?? 0; ?></div>
            </td>
            <td>
                <div class="label">Distribusi</div>
                <div class="value"><?= $summary['total_distribusi'] ?? 0; ?></div>
            </td>
            <td>
                <div class="label">Penyerahan</div>
                <div class="value"><?= $summary['total_penyerahan'] ?? 0; ?></div>
            </td>
        </tr>
    </table>

    <h2>Data Pemeriksaan Anak</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Anak</th>
                <th>Ibu</th>
                <th>Posyandu</th>
                <th>Kader</th>
                <th>BB / TB</th>
                <th>Status</th>
                <th>Prioritas</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($pemeriksaan)): ?>
                <tr>
                    <td colspan="8">Belum ada pemeriksaan pada periode ini.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($pemeriksaan as $row): ?>
                <tr>
                    <td><?= !empty($row['tanggal_pemeriksaan']) ? date('d/m/Y', strtotime($row['tanggal_pemeriksaan'])) : '-'; ?></td>
                    <td><?= htmlspecialchars($row['nama_anak'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['nama_ibu'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['nama_posyandu'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['nama_kader'] ?? '-'); ?></td>
                    <td>
                        <?= $formatAngka($row['berat_badan'] ?? 0); ?> Kg /
                        <?= $formatAngka($row['tinggi_badan'] ?? 0); ?> Cm
                    </td>
                    <td><?= htmlspecialchars($row['status_gizi'] ?? '-'); ?></td>
                    <td>Prioritas <?= htmlspecialchars($row['skala_prioritas'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Data Pengadaan</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>No Kontrak</th>
                <th>Petani</th>
                <th>Gudang</th>
                <th>Komoditas</th>
                <th>Nilai</th>
                <th>Status Kontrak</th>
                <th>Status Bayar</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($pengadaan)): ?>
                <tr>
                    <td colspan="8">Belum ada pengadaan pada periode ini.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($pengadaan as $row): ?>
                <tr>
                    <td><?= !empty($row['tgl_pengadaan']) ? date('d/m/Y', strtotime($row['tgl_pengadaan'])) : '-'; ?></td>
                    <td><?= htmlspecialchars($row['no_kontrak'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['nama_petani'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['nama_gudang'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['detail_komoditas'] ?? '-'); ?></td>
                    <td><?= $formatRupiah($row['total_nilai'] ?? 0); ?></td>
                    <td><?= htmlspecialchars($row['status_kontrak'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['status_bayar'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Stok Gudang Pusat</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Gudang</th>
                <th>Komoditas</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Stok Saat Ini</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($stok)): ?>
                <tr>
                    <td colspan="5">Belum ada stok gudang pusat.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($stok as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama_gudang'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['nama_komoditas'] ?? '-'); ?></td>
                    <td><?= $formatAngka($row['qty_in'] ?? 0); ?> <?= htmlspecialchars($row['satuan'] ?? '-'); ?></td>
                    <td><?= $formatAngka($row['qty_out'] ?? 0); ?> <?= htmlspecialchars($row['satuan'] ?? '-'); ?></td>
                    <td><?= $formatAngka($row['qty_current'] ?? 0); ?> <?= htmlspecialchars($row['satuan'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Data Distribusi</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>No Distribusi</th>
                <th>Gudang Asal</th>
                <th>Gudang Tujuan</th>
                <th>Isi Distribusi</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($distribusi)): ?>
                <tr>
                    <td colspan="6">Belum ada distribusi pada periode ini.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($distribusi as $row): ?>
                <tr>
                    <td><?= !empty($row['tanggal_distribusi']) ? date('d/m/Y', strtotime($row['tanggal_distribusi'])) : '-'; ?></td>
                    <td><?= htmlspecialchars($row['no_distribusi'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['gudang_asal'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['gudang_tujuan'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['detail_distribusi'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['status_distribusi'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Data Penyerahan Paket</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>No Penyerahan</th>
                <th>Penerima</th>
                <th>Posyandu</th>
                <th>Paket</th>
                <th>Isi Paket</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($penyerahan)): ?>
                <tr>
                    <td colspan="7">Belum ada penyerahan pada periode ini.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($penyerahan as $row): ?>
                <tr>
                    <td><?= !empty($row['tanggal_penyerahan']) ? date('d/m/Y', strtotime($row['tanggal_penyerahan'])) : '-'; ?></td>
                    <td><?= htmlspecialchars($row['no_penyerahan'] ?? '-'); ?></td>
                    <td>
                        <?= htmlspecialchars($row['nama_anak'] ?? '-'); ?><br>
                        <span class="text-muted">Ibu: <?= htmlspecialchars($row['nama_ibu'] ?? '-'); ?></span>
                    </td>
                    <td><?= htmlspecialchars($row['nama_posyandu'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['nama_paket'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['isi_paket'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($row['status_penyerahan'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis melalui ZeroStunt.id pada <?= date('d/m/Y H:i'); ?>.
        Laporan ini merupakan rekap data sistem berdasarkan periode yang dipilih.
    </div>
</body>

</html>