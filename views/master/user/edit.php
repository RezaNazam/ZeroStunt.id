<?php
$pageTitle = 'Edit Petugas';
$pageSubtitle = 'Perbarui data petugas pengelola sistem.';

ob_start();
?>

<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center">
    <div class="w-full max-w-2xl">
        <div class="mb-6">
            <a href="/master/users"
                class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-teal-700 transition">
                ← Kembali ke Data Petugas
            </a>
        </div>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                <?= htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="rounded-3xl border border-gray-100 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-6 py-5">
                <h2 class="text-xl font-extrabold text-gray-900">
                    Form Edit Petugas
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Ubah data Petugas sesuai kebutuhan.
                </p>
            </div>

            <form method="post" action="/master/users/update" class="p-6 space-y-5">
                <input type="hidden" name="id_user" value="<?= htmlspecialchars($user['id_user']); ?>">

                <div>
                    <label for="username" class="mb-2 block text-sm font-bold text-gray-700">
                        Username
                    </label>

                    <input type="text" id="username" name="username" readonly
                        value="<?= htmlspecialchars($user['username']); ?>"
                        class="w-full rounded-2xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm text-gray-500 outline-none cursor-not-allowed">
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-bold text-gray-700">
                        Password Baru (Opsional)
                    </label>

                    <input type="password" id="password" name="password"
                        placeholder="Kosongkan jika tidak ingin mengubah"
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100">
                    <p class="text-xs text-gray-500 mt-1">Isi kolom ini hanya jika Anda ingin mengganti password user.
                    </p>
                </div>

                <label class="mb-2 block text-sm font-bold text-gray-700">
                    Role <span class="text-red-500">*</span>
                </label>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <input type="radio" name="role" value="<?= ROLE_ADMIN ?>" id="role_admin" class="peer hidden"
                            required <?= $user['role'] === ROLE_ADMIN ? 'checked' : '' ?>>
                        <label for="role_admin"
                            class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-gray-200 p-4 transition hover:bg-gray-50 peer-checked:border-teal-500 peer-checked:bg-teal-50 peer-checked:ring-4 peer-checked:ring-teal-100">
                            <i class="fa-solid fa-user-shield text-2xl text-teal-600"></i>
                            <span class="font-bold text-gray-900">Admin</span>
                        </label>
                    </div>
                    <div>
                        <input type="radio" name="role" value="<?= ROLE_KADER ?>" id="role_kader" class="peer hidden"
                            required <?= $user['role'] === ROLE_KADER ? 'checked' : '' ?>>
                        <label for="role_kader"
                            class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-gray-200 p-4 transition hover:bg-gray-50 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:ring-4 peer-checked:ring-amber-100">
                            <i class="fa-solid fa-users text-2xl text-amber-600"></i>
                            <span class="font-bold text-gray-900">Kader</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center gap-4 rounded-2xl border border-gray-200 p-4">
                    <label for="is_active" class="block text-sm font-bold text-gray-700">Status</label>
                    <div class="flex items-center gap-3">
                        <input type="radio" name="is_active" value="1" id="status_aktif" required <?= $user['is_active'] ? 'checked' : '' ?>> <label for="status_aktif">Aktif</label>
                        <input type="radio" name="is_active" value="0" id="status_nonaktif" required
                            <?= !$user['is_active'] ? 'checked' : '' ?>> <label for="status_nonaktif">Tidak Aktif</label>
                    </div>
                </div>

                <!-- Posyandu (Only for Kader) -->
                <div id="posyandu_section" class="<?= $user['role'] === ROLE_KADER ? '' : 'hidden' ?>">
                    <label for="id_gudang" class="mb-2 block text-sm font-bold text-gray-700">
                        Posyandu Tugas <span class="text-red-500">*</span>
                    </label>
                    <select id="id_gudang" name="id_gudang"
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100 bg-white"
                        <?= $user['role'] === ROLE_KADER ? 'required' : '' ?>>
                        <option value="">-- Pilih Posyandu --</option>
                        <?php foreach ($posyandus as $p): ?>
                            <option value="<?= htmlspecialchars($p['id_gudang']); ?>" <?= $user['id_gudang'] == $p['id_gudang'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nama_gudang']); ?> - <?= htmlspecialchars($p['lokasi_gudang'] ?? '-'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex flex-col-reverse gap-3 pt-3 sm:flex-row sm:justify-end">
                    <a href="/master/users"
                        class="inline-flex items-center justify-center rounded-2xl border border-gray-200 px-5 py-3 text-sm font-bold text-gray-600 transition hover:bg-gray-50">
                        Batal
                    </a>

                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const roleKader = document.getElementById('role_kader');
                const roleAdmin = document.getElementById('role_admin');
                const posyanduSection = document.getElementById('posyandu_section');
                const posyanduSelect = document.getElementById('id_gudang');

                function togglePosyandu() {
                    if (roleKader.checked) {
                        posyanduSection.classList.remove('hidden');
                        posyanduSelect.setAttribute('required', 'required');
                    } else {
                        posyanduSection.classList.add('hidden');
                        posyanduSelect.removeAttribute('required');
                        posyanduSelect.value = '';
                    }
                }

                roleKader.addEventListener('change', togglePosyandu);
                roleAdmin.addEventListener('change', togglePosyandu);
            });
            </script>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';