<?php
$penyerahan = $penyerahan ?? [];
ob_start();
?>

<div class="space-y-6">

    <!-- HEADER -->
    <div>
        <h1 class="text-2xl font-extrabold text-gray-900">
            Penyerahan Bantuan Gizi
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Data seluruh penyerahan bantuan kepada ibu penerima.
        </p>
    </div>

    <!-- SUCCESS -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="rounded-2xl border border-green-100 bg-green-50 px-4 py-3 text-sm text-green-700">
            <?= htmlspecialchars($_SESSION['success']); ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- ERROR -->
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
            <?= htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- BUTTON -->
    <div class="flex justify-end">
        <a href="/transaksi/penyerahan/create"
           class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-3 rounded-xl font-bold">
            + Tambah Penyerahan
        </a>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">

        <div class="overflow-x-auto">
            <table class="w-full text-sm">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">No</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Ibu</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Tanggal</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Status</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Catatan</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    <?php if (empty($penyerahan)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                Belum ada data penyerahan.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($penyerahan as $p): ?>
                        <tr class="hover:bg-gray-50">

                            <td class="px-6 py-4 font-bold text-gray-900">
                                #<?= htmlspecialchars($p['id_penyerahan']) ?>
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                <?= htmlspecialchars($p['nama_ibu'] ?? '-') ?>
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                <?= htmlspecialchars($p['tanggal_penyerahan']) ?>
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-bold rounded-full
                                    <?= ($p['status_penyerahan'] ?? 'Diproses') === 'Diserahkan'
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-yellow-100 text-yellow-700' ?>">
                                    <?= htmlspecialchars($p['status_penyerahan'] ?? 'Diproses') ?>
                                </span>
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                <?= htmlspecialchars($p['catatan'] ?? '-') ?>
                            </td>

                            <td class="px-6 py-4">

                                <?php if (($p['status_penyerahan'] ?? 'Diproses') === 'Diproses'): ?>

                                    <form action="/transaksi/penyerahan/serahkan" method="POST">

                                        <input type="hidden"
                                               name="id_penyerahan"
                                               value="<?= $p['id_penyerahan']; ?>">

                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                                bg-green-100 text-green-700 border border-green-200
                                                hover:bg-green-200 transition">
                                            Tandai Diserahkan
                                        </button>

                                    </form>

                                <?php else: ?>

                                    <span class="text-green-600 font-bold text-xs">
                                        Selesai
                                    </span>

                                <?php endif; ?>

                            </td>

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