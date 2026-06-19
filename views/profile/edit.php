<?php
$pageTitle = 'Edit Profil';
$pageSubtitle = 'Perbarui informasi akun yang sedang digunakan.';

$username = $user['username'] ?? ($_SESSION['username'] ?? 'User');
$role = $_SESSION['role'] ?? 'Role';

$isAdmin = defined('ROLE_ADMIN') ? $role === ROLE_ADMIN : strtolower($role) === 'admin';
$isIbu = defined('ROLE_IBU') ? $role === ROLE_IBU : strtolower($role) === 'ibu';
$isPetani = defined('ROLE_PETANI') ? $role === ROLE_PETANI : strtolower($role) === 'petani';
$isKader = defined('ROLE_KADER') ? $role === ROLE_KADER : strtolower($role) === 'kader';

$ibu = $ibu ?? [];
$petani = $petani ?? [];
$kader = $kader ?? [];

ob_start();
?>

<div class="min-h-[calc(100vh-10rem)] flex items-center justify-center py-6">
    <div class="w-full max-w-3xl">

        <div class="rounded-3xl border border-gray-100 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-8 py-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-extrabold text-gray-900">
                            Form Edit Profil
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Field yang tampil disesuaikan dengan role akun.
                        </p>
                    </div>

                    <span class="inline-flex rounded-full bg-teal-50 px-4 py-2 text-xs font-extrabold text-teal-700">
                        <?= htmlspecialchars($role); ?>
                    </span>
                </div>
            </div>

            <?php if (!empty($_SESSION['error'])): ?>
                <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                    <?= htmlspecialchars($_SESSION['error']); ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['success'])): ?>
                <div class="mb-5 rounded-2xl border border-green-100 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                    <?= htmlspecialchars($_SESSION['success']); ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <form method="post" action="/profile/update" class="p-8 space-y-8">
                <!-- Semua Role: Data Akun -->
                <section>
                    <h3 class="mb-4 text-lg font-extrabold text-gray-900">
                        Data Akun
                    </h3>

                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label for="username" class="mb-2 block text-sm font-bold text-gray-700">
                                Username <span class="text-red-500">*</span>
                            </label>

                            <input type="text" id="username" name="username" required minlength="4" maxlength="30"
                                value="<?= htmlspecialchars($username); ?>"
                                class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100">
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-bold text-gray-700">
                                Role
                            </label>

                            <input type="text" value="<?= htmlspecialchars($role); ?>" disabled
                                class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-bold text-gray-500 outline-none">
                        </div>
                    </div>
                </section>

                <!-- Admin -->
                <?php if ($isAdmin): ?>
                    <section class="rounded-3xl border border-teal-100 bg-teal-50/60 p-6">
                        <div class="flex gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-teal-100 text-teal-700">
                                <i class="fa-solid fa-user-shield"></i>
                            </div>

                            <div>
                                <h3 class="text-lg font-extrabold text-gray-900">
                                    Sesi Admin
                                </h3>
                                <p class="mt-1 text-sm leading-6 text-gray-600">
                                    Admin hanya mengubah data akun utama. Data akses menu dan role dikelola dari sistem.
                                </p>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Ibu -->
                <?php if ($isIbu): ?>
                    <section class="rounded-3xl border border-teal-100 bg-teal-50/60 p-6">
                        <h3 class="mb-4 text-lg font-extrabold text-gray-900">
                            Sesi Profil Ibu
                        </h3>

                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label class="mb-2 block text-sm font-bold text-gray-700">
                                    NIK Ibu
                                </label>
                                <input type="text" name="nik_ibu" maxlength="16" minlength="16" inputmode="numeric" pattern="[0-9]{16}" disabled
                                    value="<?= htmlspecialchars($ibu['NIK_ibu'] ?? ''); ?>"
                                    class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-bold text-gray-700">
                                    Nama Ibu
                                </label>
                                <input type="text" name="nama_ibu"
                                    value="<?= htmlspecialchars($ibu['nama_ibu'] ?? ''); ?>"
                                    class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-bold text-gray-700">
                                    No. Telepon
                                </label>
                                <input type="text" name="no_telp"
                                    value="<?= htmlspecialchars($ibu['no_telp'] ?? ''); ?>"
                                    class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100">
                            </div>

                            <div class="flex items-end">
                                <label class="flex w-full items-center gap-3 rounded-2xl border border-gray-200 px-4 py-3 text-sm font-bold text-gray-700">
                                    <input type="checkbox" name="is_pregnant" value="1"
                                        <?= !empty($ibu['is_pregnant']) ? 'checked' : ''; ?>
                                        class="h-4 w-4 rounded border-gray-300 text-teal-600">
                                    Sedang Hamil
                                </label>
                            </div>

                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-bold text-gray-700">
                                    Alamat
                                </label>
                                <textarea name="alamat" rows="3"
                                    class="w-full resize-none rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100"><?= htmlspecialchars($ibu['alamat'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Petani -->
                <?php if ($isPetani): ?>
                    <section class="rounded-3xl border border-green-100 bg-green-50/60 p-6">
                        <h3 class="mb-4 text-lg font-extrabold text-gray-900">
                            Sesi Profil Petani
                        </h3>

                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label class="mb-2 block text-sm font-bold text-gray-700">
                                    Nama Lahan
                                </label>
                                <input type="text" name="nama_lahan"
                                    value="<?= htmlspecialchars($petani['nama_lahan'] ?? ''); ?>"
                                    class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-bold text-gray-700">
                                    No. Rekening
                                </label>
                                <input type="text" name="no_rekening"
                                    value="<?= htmlspecialchars($petani['no_rekening'] ?? ''); ?>"
                                    class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-bold text-gray-700">
                                    Kapasitas Panen / Bulan
                                </label>
                                <input type="number" name="kapasitas_panen_bulan" step="0.01" min="0"
                                    value="<?= htmlspecialchars($petani['kapasitas_panen_bulan'] ?? ''); ?>"
                                    class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100">
                            </div>

                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-bold text-gray-700">
                                    Alamat Lahan
                                </label>
                                <textarea name="alamat_lahan" rows="3"
                                    class="w-full resize-none rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100"><?= htmlspecialchars($petani['alamat_lahan'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Kader -->
                <?php if ($isKader): ?>
                    <section class="rounded-3xl border border-blue-100 bg-blue-50/60 p-6">
                        <div class="flex gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-teal-100 text-teal-700">
                                <i class="fa-solid fa-user-shield"></i>
                            </div>

                            <div>
                                <h3 class="text-lg font-extrabold text-gray-900">
                                    Sesi Profil Kader
                                </h3>
                                <p class="mt-1 text-sm leading-6 text-gray-600">
                                    Kader hanya mengubah data username. Data akses menu dan role dikelola dari sistem.
                                </p>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <div class="flex flex-col-reverse gap-3 pt-3 sm:flex-row sm:justify-end">
                    <a href="/profile"
                        class="inline-flex items-center justify-center rounded-2xl border border-gray-200 px-5 py-3 text-sm font-bold text-gray-600 transition hover:bg-gray-50">
                        Batal
                    </a>

                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
