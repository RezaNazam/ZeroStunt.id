<?php
$pageTitle = 'Tambah User';
$pageSubtitle = 'Buat akun baru untuk Admin atau Kader.';

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

$gudangPusat = $gudangPusat ?? [];
$posyandus = $posyandus ?? [];

ob_start();
?>

<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center">
    <div class="w-full max-w-2xl">
        <div class="mb-6">
            <a href="/master/users"
                class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-teal-700 transition">
                ← Kembali ke Data User
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
                <p class="text-sm font-bold text-amber-500 uppercase tracking-widest mb-2">
                    Buat Akun Internal
                </p>

                <h2 class="text-xl font-extrabold text-gray-900">
                    Form Tambah User
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Buat akun untuk Admin atau Kader sesuai hak akses yang dibutuhkan.
                </p>
            </div>

            <form method="post" action="/master/users/store" class="p-6 space-y-5">
                <!-- Role -->
                <div>
                    <p class="block text-sm font-bold text-gray-700 mb-3">
                        Role <span class="text-red-500">*</span>
                    </p>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <label class="cursor-pointer">
                            <input type="radio" id="role_admin" name="role" value="<?= ROLE_ADMIN ?>"
                                class="peer sr-only"
                                <?= (($old['role'] ?? '') === ROLE_ADMIN) ? 'checked' : ''; ?>
                                required>

                            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 transition peer-checked:border-teal-500 peer-checked:bg-teal-50 peer-checked:ring-4 peer-checked:ring-teal-100">
                                <div class="mb-2 text-2xl text-teal-600">
                                    <i class="fa-solid fa-user-shield"></i>
                                </div>
                                <p class="font-bold text-gray-900">Admin</p>
                                <p class="mt-1 text-xs text-gray-500">
                                    Mengelola master data dan transaksi sistem.
                                </p>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" id="role_kader" name="role" value="<?= ROLE_KADER ?>"
                                class="peer sr-only"
                                <?= (($old['role'] ?? '') === ROLE_KADER) ? 'checked' : ''; ?>
                                required>

                            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 transition peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:ring-4 peer-checked:ring-amber-100">
                                <div class="mb-2 text-2xl text-amber-600">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                                <p class="font-bold text-gray-900">Kader</p>
                                <p class="mt-1 text-xs text-gray-500">
                                    Mengelola pemeriksaan, distribusi, dan penyerahan.
                                </p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Username -->
                <div>
                    <label for="username" class="mb-2 block text-sm font-bold text-gray-700">
                        Username <span class="text-red-500">*</span>
                    </label>

                    <input type="text" id="username" name="username" required
                        value="<?= htmlspecialchars($old['username'] ?? ''); ?>"
                        autocomplete="username"
                        placeholder="Buat username"
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="mb-2 block text-sm font-bold text-gray-700">
                        Password <span class="text-red-500">*</span>
                    </label>

                    <input type="password" id="password" name="password" required
                        autocomplete="new-password"
                        placeholder="Masukkan password"
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="confirm_password" class="mb-2 block text-sm font-bold text-gray-700">
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>

                    <input type="password" id="confirm_password" name="confirm_password" required
                        autocomplete="new-password"
                        placeholder="Ulangi password"
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">
                </div>

                <!-- Gudang Pusat untuk Admin -->
                <div id="gudang_pusat_section" class="hidden">
                    <label for="id_gudang_admin" class="mb-2 block text-sm font-bold text-gray-700">
                        Gudang Pusat / Puskesmas <span class="text-red-500">*</span>
                    </label>

                    <select id="id_gudang_admin" name="id_gudang" disabled
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100 bg-white">
                        <option value="" disabled selected>-- Pilih Gudang Pusat --</option>

                        <?php foreach ($gudangPusat as $gudang): ?>
                            <option value="<?= htmlspecialchars($gudang['id_gudang']); ?>"
                                <?= (($old['id_gudang'] ?? '') == $gudang['id_gudang']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($gudang['nama_gudang']); ?>
                                - <?= htmlspecialchars($gudang['lokasi_gudang'] ?? '-'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Posyandu untuk Kader -->
                <div id="posyandu_section" class="hidden">
                    <label for="id_gudang_kader" class="mb-2 block text-sm font-bold text-gray-700">
                        Posyandu Tugas <span class="text-red-500">*</span>
                    </label>

                    <select id="id_gudang_kader" name="id_gudang" disabled
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100 bg-white">
                        <option value="" disabled selected>-- Pilih Posyandu --</option>

                        <?php foreach ($posyandus as $p): ?>
                            <option value="<?= htmlspecialchars($p['id_gudang']); ?>"
                                <?= (($old['id_gudang'] ?? '') == $p['id_gudang']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($p['nama_gudang']); ?>
                                - <?= htmlspecialchars($p['lokasi_gudang'] ?? '-'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Action -->
                <div class="flex flex-col-reverse gap-3 pt-3 sm:flex-row sm:justify-end">
                    <a href="/master/users"
                        class="inline-flex items-center justify-center rounded-2xl border border-gray-200 px-5 py-3 text-sm font-bold text-gray-600 transition hover:bg-gray-50">
                        Batal
                    </a>

                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-700">
                        Simpan User
                    </button>
                </div>
            </form>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const roleKader = document.getElementById('role_kader');
                    const roleAdmin = document.getElementById('role_admin');

                    const gudangPusatSection = document.getElementById('gudang_pusat_section');
                    const posyanduSection = document.getElementById('posyandu_section');

                    const gudangAdminSelect = document.getElementById('id_gudang_admin');
                    const gudangKaderSelect = document.getElementById('id_gudang_kader');

                    function disableSelect(select) {
                        select.disabled = true;
                        select.required = false;
                        select.value = '';
                    }

                    function enableSelect(select) {
                        select.disabled = false;
                        select.required = true;
                    }

                    function toggleGudang() {
                        if (roleAdmin.checked) {
                            gudangPusatSection.classList.remove('hidden');
                            posyanduSection.classList.add('hidden');

                            enableSelect(gudangAdminSelect);
                            disableSelect(gudangKaderSelect);
                            return;
                        }

                        if (roleKader.checked) {
                            posyanduSection.classList.remove('hidden');
                            gudangPusatSection.classList.add('hidden');

                            enableSelect(gudangKaderSelect);
                            disableSelect(gudangAdminSelect);
                            return;
                        }

                        gudangPusatSection.classList.add('hidden');
                        posyanduSection.classList.add('hidden');

                        disableSelect(gudangAdminSelect);
                        disableSelect(gudangKaderSelect);
                    }

                    roleAdmin.addEventListener('change', toggleGudang);
                    roleKader.addEventListener('change', toggleGudang);

                    toggleGudang();
                });
            </script>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
