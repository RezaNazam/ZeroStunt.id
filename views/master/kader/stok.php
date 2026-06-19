<?php
$pageTitle = 'Dashboard Kader';
$pageSubtitle = 'Ringkasan pemeriksaan anak, stok posyandu, dan penyerahan paket gizi.';

ob_start();
?>

<!-- Page Content -->
            <div class="w-full bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Stok Posyandu</h1>
                        <p class="text-sm text-gray-500">Riwayat stok yang tersedia di posyandu.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 text-gray-600 text-sm font-semibold">
                                <th class="pb-3 pl-4">No</th>
                                <th class="pb-3">#</th>
                                <th class="pb-3">#</th>
                                <th class="pb-3">#</th>
                                <th class="pb-3">#</th>
                                <th class="pb-3">#</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            <?php if (!empty($stoks)): ?>
                                <?php $no = 1; foreach ($stoks as $stok): ?>
                                    <tr>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-gray-400">Belum ada data pemeriksaan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';