<?php
$pageTitle = 'Master User';
$pageSubtitle = 'Kelola data user untuk pengelola sistem ZeroStunt.id.';

$users = $users ?? [];

ob_start();
?>

<div class="space-y-6">
    <!-- Header Action -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900">
                Data User
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Daftar User dalam sistem ZeroStunt.id yang digunakan untuk mengelola aplikasi.
            </p>
        </div>

        <a href="/master/users/create"
            class="inline-flex items-center justify-center rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-700">
            + Tambah User
        </a>
    </div>

    <!-- Flash Message -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="rounded-2xl border border-green-100 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
            <?= htmlspecialchars($_SESSION['success']); ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            <?= htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Table Card -->
    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-5">
            <h3 class="text-lg font-extrabold text-gray-900">
                Tabel User
            </h3>
            <p class="text-sm text-gray-500 mt-1">
                Total data:
                <?= count($users); ?> user
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">ID</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Username</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Role</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-600">Status</th>
                        <th class="px-6 py-4 text-right font-bold text-gray-600">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="mx-auto max-w-sm">
                                    <div
                                        class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-teal-100 text-2xl text-teal-600">
                                        <i class="fa-solid fa-users-slash"></i>
                                    </div>
                                    <p class="font-bold text-gray-900">
                                        Belum ada User
                                    </p>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Tambahkan data User pertama untuk mulai mengelola sistem.
                                    </p>
                                    <a href="/master/users/create"
                                        class="mt-5 inline-flex rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white hover:bg-teal-700">
                                        Tambah User
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($users as $row): ?>
                        <tr class="transition hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-500">
                                <?= htmlspecialchars($row['id_user']); ?>
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">
                                    <?= htmlspecialchars($row['username']); ?>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <?php
                                $roleClass = 'bg-gray-100 text-gray-700';
                                if ($row['role'] === ROLE_ADMIN)
                                    $roleClass = 'bg-teal-50 text-teal-700';
                                if ($row['role'] === ROLE_KADER)
                                    $roleClass = 'bg-green-50 text-green-700';
                                ?>
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold <?= $roleClass; ?>">
                                    <?= htmlspecialchars($row['role']); ?>
                                </span>
                            </td>

                            <td class="px-6 py-4 text-gray-500">
                                <?php if ($row['is_active']): ?>
                                    <span class="inline-flex items-center gap-1.5 text-green-700">
                                        <span class="h-2 w-2 rounded-full bg-green-500"></span> Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 text-red-700">
                                        <span class="h-2 w-2 rounded-full bg-red-500"></span> Tidak Aktif
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="/master/users/edit?id=<?= urlencode($row['id_user']); ?>"
                                        class="rounded-xl bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">
                                        Edit
                                    </a>

                                    <a href="/master/users/delete?id=<?= urlencode($row['id_user']); ?>"
                                        onclick="return confirm('Yakin ingin menghapus gudang ini?')"
                                        class="rounded-xl bg-red-50 px-3 py-2 text-xs font-bold text-red-700 transition hover:bg-red-100">
                                        Hapus
                                    </a>
                                </div>
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