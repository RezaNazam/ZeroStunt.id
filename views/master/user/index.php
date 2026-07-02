<?php
$pageTitle = 'Master User';
$pageSubtitle = 'Kelola data user untuk pengelola sistem ZeroStunt.id.';

$users = $users ?? [];
$tablePagination = $tablePagination ?? [];

/*
|--------------------------------------------------------------------------
| Table Config
|--------------------------------------------------------------------------
*/
$tableRows = $users;
$tableEmptyMessage = 'Belum ada data user.';

$tableColumns = [
    [
        'label' => 'No',
        'type' => 'number',
        'td_class' => 'text-gray-500 font-semibold'
    ],
    [
        'label' => 'Username',
        'render' => function ($row) {
            return '<div class="font-bold text-gray-900">' .
                htmlspecialchars($row['username']) .
                '</div>';
        }
    ],
    [
        'label' => 'Role',
        'render' => function ($row) {
            $roleClass = 'bg-gray-100 text-gray-700';

            if ($row['role'] === ROLE_ADMIN) {
                $roleClass = 'bg-teal-50 text-teal-700';
            }

            if ($row['role'] === ROLE_KADER) {
                $roleClass = 'bg-green-50 text-green-700';
            }

            if (defined('ROLE_IBU') && $row['role'] === ROLE_IBU) {
                $roleClass = 'bg-pink-50 text-pink-700';
            }

            if (defined('ROLE_PETANI') && $row['role'] === ROLE_PETANI) {
                $roleClass = 'bg-amber-50 text-amber-700';
            }

            return '<span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ' . $roleClass . '">' .
                htmlspecialchars($row['role']) .
                '</span>';
        }
    ],
    [
        'label' => 'Status',
        'render' => function ($row) {
            if (!empty($row['is_active'])) {
                return '
                    <span class="inline-flex items-center gap-1.5 font-semibold text-green-700">
                        <span class="h-2 w-2 rounded-full bg-green-500"></span>
                        Aktif
                    </span>
                ';
            }

            return '
                <span class="inline-flex items-center gap-1.5 font-semibold text-red-700">
                    <span class="h-2 w-2 rounded-full bg-red-500"></span>
                    Tidak Aktif
                </span>
            ';
        }
    ],
    [
        'label' => 'Aksi',
        'th_class' => 'text-right',
        'td_class' => 'text-right',
        'render' => function ($row) {
            $id = urlencode($row['id_user']);

            return '
                <div class="flex justify-end gap-2">
                    <a href="/master/users/edit?id=' . $id . '"
                        class="rounded-xl bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">
                        Edit
                    </a>

                    <a href="/master/users/delete?id=' . $id . '"
                        data-confirm
                        data-confirm-title="Hapus pengguna?"
                        data-confirm-message="Pengguna ini akan dihapus dari sistem dan tidak dapat digunakan lagi untuk login."
                        data-confirm-text="Ya, hapus"
                        data-confirm-tone="danger"
                        class="rounded-xl bg-red-50 px-3 py-2 text-xs font-bold text-red-700 transition hover:bg-red-100">
                        Hapus
                    </a>
                </div>
            ';
        }
    ],
];

/*
|--------------------------------------------------------------------------
| AJAX Response
|--------------------------------------------------------------------------
*/
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    require '../views/partials/data_table.php';
    exit;
}

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
        <div class="border-b border-gray-100 px-6 py-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-lg font-extrabold text-gray-900">
                    Tabel User
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Total data: <?= $tablePagination['total_data'] ?? count($users); ?> user
                </p>
            </div>

            <div class="w-full lg:max-w-md">
                <?php
                $searchAction = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $searchPlaceholder = 'Cari username, role, atau status user...';
                $searchTarget = 'tableResult';
                $searchParam = 'q';
                $pageParam = 'page';
                require '../views/partials/searchbar.php';
                ?>
            </div>
        </div>

        <div id="tableResult">
            <?php require '../views/partials/data_table.php'; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
