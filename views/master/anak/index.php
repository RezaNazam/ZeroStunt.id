<?php
$pageTitle = 'Dashboard Ibu';
$pageSubtitle = 'Ringkasan data anak, status gizi, dan riwayat bantuan.';

ob_start();
?>

<!-- Page Content -->
            <div class="w-full bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Daftar Master Anak</h1>
                        <p class="text-sm text-gray-500">Pantau status gizi dan prioritas bantuan balita.</p>
                    </div>
                    <a href="/master/anak/create" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold px-4 py-2.5 rounded-xl transition duration-200 text-sm">
                        + Daftarkan Anak
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 text-gray-600 text-sm font-semibold">
                                <th class="pb-3 pl-4">No</th>
                                <th class="pb-3">Nama Anak</th>
                                <th class="pb-3">Ibu Kandung</th>
                                <th class="pb-3">Tgl Lahir / Jenis Kelamin</th>
                                <th class="pb-3">Status Gizi</th>
                                <th class="pb-3">Prioritas</th>
                                <th class="pb-3 pr-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            <?php if (!empty($anaks)): ?>
                                <?php $no = 1; foreach ($anaks as $anak): ?>
                                    <tr>
                                        <td class="py-4 pl-4 font-medium"><?= $no++; ?></td>
                                        <td class="py-4 font-semibold text-gray-900"><?= htmlspecialchars($anak['nama_anak']); ?></td>
                                        <td class="py-4"><?= htmlspecialchars($anak['nama_ibu']); ?></td>
                                        <td class="py-4">
                                            <div class="text-gray-900"><?= htmlspecialchars($anak['tgl_lahir']); ?></div>
                                            <div class="text-xs text-gray-400"><?= $anak['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></div>
                                        </td>
                                        <td class="py-4">
                                            <?php if (($anak['st_gizi_skrg'] ?? 'Normal') === 'Normal'): ?>
                                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Normal</span>
                                            <?php elseif (in_array(($anak['st_gizi_skrg'] ?? ''), ['Stunting', 'Gizi Buruk'])): ?>
                                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700"><?= htmlspecialchars($anak['st_gizi_skrg'] ?? ''); ?></span>
                                            <?php else: ?>
                                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700"><?= htmlspecialchars($anak['st_gizi_skrg'] ?? ''); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4">
                                            <?php if ($anak['skala_prioritas'] == '1'): ?>
                                                <span class="px-2.5 py-1 text-xs font-bold rounded bg-red-600 text-white">Prioritas 1</span>
                                            <?php elseif ($anak['skala_prioritas'] == '2'): ?>
                                                <span class="px-2.5 py-1 text-xs font-bold rounded bg-amber-500 text-white">Prioritas 2</span>
                                            <?php else: ?>
                                                <span class="px-2.5 py-1 text-xs font-semibold rounded bg-gray-200 text-gray-700">Prioritas 3</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4 pr-4 text-center">
                                            <div class="inline-flex gap-2">
                                                <a href="/master/anak/edit?id=<?= $anak['id_anak']; ?>" class="text-teal-600 hover:text-teal-800 font-semibold text-xs bg-teal-50 px-2 py-1 rounded">Edit</a>
                                                <a href="/master/anak/delete?id=<?= $anak['id_anak']; ?>" onclick="return confirm('Hapus data anak ini?')" class="text-red-600 hover:text-red-800 font-semibold text-xs bg-red-50 px-2 py-1 rounded">Hapus</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-gray-400">Belum ada data anak.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';