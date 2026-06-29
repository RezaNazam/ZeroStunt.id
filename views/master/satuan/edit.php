<?php

/** @var array<string,mixed> $satuan */

$pageTitle = 'Edit Satuan';
$pageSubtitle = 'Perbarui data satuan';

ob_start();
?>

<div class="w-full max-w-3xl mx-auto">

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm">

        <div class="px-6 py-5 border-b border-gray-100">

            <h2 class="text-xl font-extrabold text-gray-900">
                Edit Satuan
            </h2>

        </div>

        <form
            action="/master/satuan/update"
            method="POST"
            class="p-6 space-y-5">

            <input
                type="hidden"
                name="id_satuan"
                value="<?= $satuan['id_satuan'] ?>">

            <div>

                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Satuan
                </label>

                <input
                    type="text"
                    name="nama_satuan"
                    value="<?= htmlspecialchars($satuan['nama_satuan']) ?>"
                    required
                    class="w-full rounded-xl border border-gray-300 px-4 py-3">

            </div>

            <div>

                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Singkatan
                </label>

                <input
                    type="text"
                    name="singkat"
                    value="<?= htmlspecialchars($satuan['singkat']) ?>"
                    required
                    class="w-full rounded-xl border border-gray-300 px-4 py-3">

            </div>

            <div class="flex gap-3">

                <a href="/master/satuan"
                    class="px-4 py-2 rounded-xl bg-gray-200">

                    Kembali

                </a>

                <button
                    type="submit"
                    class="px-5 py-2 rounded-xl bg-teal-600 text-white">

                    Update

                </button>

            </div>

        </form>

    </div>

</div>

<?php

$content = ob_get_clean();
require '../views/layouts/dashboard.php';