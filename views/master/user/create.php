<?php
$pageTitle = 'Tambah User';
$pageSubtitle = 'Buat akun baru untuk Admin atau Kader.';

ob_start();
?>

<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center">
    <div class="w-full max-w-2xl">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="/master/users"
                class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-teal-700 transition">
                ← Kembali ke Data User
            </a>
        </div>

        <!-- Flash Message -->
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                <?= htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="rounded-3xl border border-gray-100 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-6 py-5">
                <h2 class="text-xl font-extrabold text-gray-900">
                    Form Tambah User
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    User yang dibuat di sini akan memiliki akses ke sistem sesuai dengan rolenya.
                </p>
            </div>

            <form method="post" action="/master/users/store" class="p-6 space-y-5">
                <!-- Username -->
                <div>
                    <label for="username" class="mb-2 block text-sm font-bold text-gray-700">
                        Username <span class="text-red-500">*</span>
                    </label>

                    <input type="text" id="username" name="username" required placeholder="Masukkan username unik"
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="mb-2 block text-sm font-bold text-gray-700">
                        Password <span class="text-red-500">*</span>
                    </label>

                    <input type="password" id="password" name="password" required placeholder="Masukkan password"
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100">
                </div>

                <!-- Role -->
                <label class="mb-2 block text-sm font-bold text-gray-700">
                    Role <span class="text-red-500">*</span>
                </label>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <input type="radio" name="role" value="<?= ROLE_ADMIN ?>" id="role_admin" class="peer hidden"
                            required>
                        <label for="role_admin"
                            class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-gray-200 p-4 transition hover:bg-gray-50 peer-checked:border-teal-500 peer-checked:bg-teal-50 peer-checked:ring-4 peer-checked:ring-teal-100">
                            <i class="fa-solid fa-user-shield text-2xl text-teal-600"></i>
                            <span class="font-bold text-gray-900">Admin</span>
                        </label>
                    </div>

                    <div>
                        <input type="radio" name="role" value="<?= ROLE_KADER ?>" id="role_kader" class="peer hidden"
                            required>
                        <label for="role_kader"
                            class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-gray-200 p-4 transition hover:bg-gray-50 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:ring-4 peer-checked:ring-amber-100">
                            <i class="fa-solid fa-users text-2xl text-amber-600"></i>
                            <span class="font-bold text-gray-900">Kader</span>
                        </label>
                    </div>
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
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
