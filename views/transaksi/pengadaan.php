<?php

$pageTitle = 'Pengadaan Saya';
$pageSubtitle = 'Pilih pengadaan komoditas yang tersedia dari posyandu.';

$pengadaan = [
    [
        'komoditas' => 'Beras Premium',
        'jumlah' => '100 Kg',
        'posyandu' => 'Posyandu Mawar',
        'status' => 'Tersedia',
        'badge' => 'green'
    ],
    [
        'komoditas' => 'Telur Ayam',
        'jumlah' => '250 Butir',
        'posyandu' => 'Posyandu Melati',
        'status' => 'Sudah Diambil',
        'badge' => 'red'
    ]
];

ob_start();
?>

<section class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">

    <div class="px-6 py-5 border-b border-gray-100">
        <h2 class="text-xl font-extrabold text-gray-900">
            Daftar Pengadaan
        </h2>

        <p class="text-sm text-gray-500 mt-1">
            Pilih pengadaan yang masih tersedia untuk diambil.
        </p>
    </div>

    <div class="overflow-x-auto">

        <table class="w-full text-sm">

            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-6 py-4 font-bold text-gray-600">Komoditas</th>
                    <th class="text-left px-6 py-4 font-bold text-gray-600">Jumlah</th>
                    <th class="text-left px-6 py-4 font-bold text-gray-600">Posyandu</th>
                    <th class="text-left px-6 py-4 font-bold text-gray-600">Status</th>
                    <th class="text-left px-6 py-4 font-bold text-gray-600">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">

                <?php foreach ($pengadaan as $row): ?>

                    <?php
                    $badgeClass = $row['badge'] === 'green'
                        ? 'bg-green-50 text-green-700'
                        : 'bg-red-50 text-red-700';
                    ?>

                    <tr class="hover:bg-gray-50 transition">

                        <td class="px-6 py-4 font-semibold text-gray-900">
                            <?= $row['komoditas']; ?>
                        </td>

                        <td class="px-6 py-4 text-gray-500">
                            <?= $row['jumlah']; ?>
                        </td>

                        <td class="px-6 py-4 text-gray-500">
                            <?= $row['posyandu']; ?>
                        </td>

                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold <?= $badgeClass; ?>">
                                <?= $row['status']; ?>
                            </span>
                        </td>

                        <td class="px-6 py-4">

                            <?php if ($row['status'] === 'Tersedia'): ?>

                                <button
                                    onclick="openModal()"
                                    class="px-4 py-2 rounded-xl border border-gray-200">
                                    ACC
                                </button>

                            <?php else: ?>

                                <button
                                    disabled
                                    class="px-4 py-2 rounded-xl bg-gray-200 text-gray-500 font-semibold cursor-not-allowed">
                                    Diambil
                                </button>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</section>

<!-- Modal -->
<div id="pengadaanModal"
    class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">

    <div class="bg-white rounded-3xl shadow-xl p-6 w-full max-w-md">

        <h2 class="text-xl font-extrabold text-gray-900 mb-4">
            Konfirmasi Pengadaan
        </h2>

        <div class="space-y-4 mb-6">

            <div class="bg-green-50 rounded-2xl p-4">
                <p class="text-sm text-green-700">Komoditas</p>
                <p class="font-bold text-green-900">Beras Premium</p>
            </div>

            <div class="bg-blue-50 rounded-2xl p-4">
                <p class="text-sm text-blue-700">Jumlah</p>
                <p class="font-bold text-blue-900">100 Kg</p>
            </div>

            <div class="bg-amber-50 rounded-2xl p-4">
                <p class="text-sm text-amber-700">Posyandu</p>
                <p class="font-bold text-amber-900">Posyandu Mawar</p>
            </div>

        </div>

        <div class="flex justify-end gap-3">

            <button
                onclick="closeModal()"
                class="px-4 py-2 rounded-xl border border-gray-200">
                Batal
            </button>

            <button
                class="px-4 py-2 rounded-xl border border-gray-200">
                Ya, Saya Ambil
            </button>

        </div>

    </div>

</div>

<script>
function openModal() {
    document.getElementById('pengadaanModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('pengadaanModal').classList.add('hidden');
}
</script>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
?>
```
