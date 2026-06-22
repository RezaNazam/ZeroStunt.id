<?php
$pageTitle = 'Data Ibu & Anak';
$pageSubtitle = 'Manajemen dan pemantauan data Ibu hamil/menyusui serta Anak dampingan.';

$daftarIbu = $data['ibu'] ?? [];
$daftarAnak = $data['anak'] ?? [];

ob_start();
?>

<div class="space-y-8">
    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-5 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-extrabold text-gray-900">
                    <i class="fa-solid fa-person-breastfeeding text-pink-600 mr-2"></i> Rekap Data Ibu
                </h3>
                <p class="text-sm text-gray-500 mt-1">Daftar seluruh Ibu yang terintegrasi dengan Posyandu binaan.</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">NIK</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Nama Lengkap</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">No. Telepon</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Status Kehamilan</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Alamat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($daftarIbu)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">Belum ada data Ibu terdaftar.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($daftarIbu as $ibu): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-mono text-gray-700">
                                <?= htmlspecialchars($ibu['NIK_ibu']) ?>
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900">
                                <?= htmlspecialchars($ibu['nama_ibu']) ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($ibu['no_telp'] ?: '-') ?>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2.5 py-1 text-xs font-bold rounded-full <?= $ibu['is_pregnant'] ? 'bg-pink-100 text-pink-700' : 'bg-blue-100 text-blue-700' ?>">
                                    <?= $ibu['is_pregnant'] ? 'Hamil' : 'Tidak Hamil / Menyusui' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 truncate max-w-xs">
                                <?= htmlspecialchars($ibu['alamat']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm mt-7">
        <div class="border-b border-gray-100 px-6 py-5">
            <h3 class="text-lg font-extrabold text-gray-900">
                <i class="fa-solid fa-child text-indigo-600 mr-2"></i> Rekap Data Anak
            </h3>
            <p class="text-sm text-gray-500 mt-1">Daftar anak dampingan penanganan stunting beserta relasi orang tua.
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">NIK Anak</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Nama Anak</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Jenis Kelamin</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Tanggal Lahir</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Ibu Kandung</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Posyandu/Gudang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($daftarAnak)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">Belum ada data Anak terdaftar.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($daftarAnak as $anak): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-mono text-gray-700">
                                <?= htmlspecialchars($anak['NIK_anak']) ?>
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900">
                                <?= htmlspecialchars($anak['nama_anak']) ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <?= $anak['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                <?= date('d M Y', strtotime($anak['tgl_lahir'])) ?>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-700">
                                <?= htmlspecialchars($anak['nama_ibu']) ?>
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-teal-700"><span
                                    class="bg-teal-50 px-2 py-1 rounded-md">
                                    <?= htmlspecialchars($anak['nama_gudang']) ?>
                                </span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>