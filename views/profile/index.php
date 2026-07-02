<?php
$pageTitle = 'Profil Akun';
$pageSubtitle = 'Informasi akun yang sedang digunakan.';

$username = $user['username'] ?? ($_SESSION['username'] ?? 'User');
$role = $user['role'] ?? ($_SESSION['role'] ?? 'Role');

$created = $user['created_at'] ?? $user['tgl_created'] ?? null;

$bulanIndonesia = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember',
];

if ($created) {
    $timestamp = strtotime($created);

    $tanggal = date('j', $timestamp);
    $bulan = $bulanIndonesia[(int) date('m', $timestamp)];
    $tahun = date('Y', $timestamp);

    $createdText = $tanggal . ' ' . $bulan . ' ' . $tahun;
} else {
    $createdText = '-';
}

$initial = strtoupper(substr($username, 0, 1));

ob_start();
?>

<div class="min-h-[calc(100vh-10rem)] flex items-center justify-center py-4">
    <div class="w-full max-w-5xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start justify-center">

            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Profile Summary -->
                <div class="rounded-3xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-br from-teal-600 to-teal-800 px-8 py-9 text-white text-center">
                        <div class="mx-auto mb-5 flex items-center justify-center rounded-3xl bg-white/15 text-4xl font-extrabold ring-4 ring-white/10"
                            style="width: 5.5rem; height: 5.5rem;">
                            <?= htmlspecialchars($initial); ?>
                        </div>

                        <h2 class="text-3xl font-extrabold">
                            <?= htmlspecialchars($username); ?>
                        </h2>

                        <p class="mt-3 inline-flex items-center justify-center rounded-full bg-white/15 px-4 py-1.5 text-sm font-bold text-teal-50">
                            <?= htmlspecialchars($role); ?>
                        </p>
                    </div>

                    <div class="p-6">
                        <form method="post"
                            action="/profile/delete"
                            data-confirm
                            data-confirm-title="Hapus akun?"
                            data-confirm-message="Akun ini tidak akan bisa digunakan untuk login lagi. Pastikan Anda benar-benar ingin menghapus akun."
                            data-confirm-text="Ya, hapus akun"
                            data-confirm-tone="danger">

                            <button type="submit"
                                class="flex w-full items-center justify-center gap-2 rounded-2xl bg-red-100 px-5 py-4 text-sm font-extrabold text-red-600 transition hover:bg-red-200">
                                <i class="fa-solid fa-trash"></i>
                                Hapus Akun
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Role Info -->
                <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="flex shrink-0 items-center justify-center rounded-2xl bg-teal-50 text-teal-700"
                            style="width: 3rem; height: 3rem;">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>

                        <div>
                            <h3 class="text-xl font-extrabold text-gray-900">
                                Akses Role
                            </h3>

                            <p class="mt-2 text-sm leading-6 text-gray-500">
                                Akun dengan role
                                <span class="font-bold text-teal-700"><?= htmlspecialchars($role); ?></span>
                                hanya dapat mengakses menu dan fitur sesuai hak aksesnya.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="lg:col-span-3">
                <div class="rounded-3xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                    <div class="border-b border-gray-100 px-10 py-5">
                        <h3 class="text-2xl font-extrabold text-gray-900">
                            Detail Akun
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Data dasar akun yang tersimpan pada sesi login.
                        </p>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="min-h-28 rounded-2xl bg-gray-50 px-6 py-3 flex flex-col justify-center">
                                <p class="text-xs font-bold uppercase tracking-wide text-gray-400">
                                    Username
                                </p>
                                <p class="mt-3 text-lg font-extrabold text-gray-900">
                                    <?= htmlspecialchars($username); ?>
                                </p>
                            </div>

                            <div class="min-h-28 rounded-2xl bg-gray-50 px-6 py-3 flex flex-col justify-center">
                                <p class="text-xs font-bold uppercase tracking-wide text-teal-600/70">
                                    Role
                                </p>
                                <p class="mt-3 text-lg font-extrabold text-teal-700">
                                    <?= htmlspecialchars($role); ?>
                                </p>
                            </div>

                            <div class="min-h-28 rounded-2xl bg-teal-50 px-6 py-3 flex flex-col justify-center">
                                <p class="text-xs font-bold uppercase tracking-wide text-green-600/70">
                                    Status
                                </p>
                                <p class="mt-3 text-lg font-extrabold text-green-700">
                                    <i class="fa-solid fa-circle-check mr-1"></i>
                                    Aktif
                                </p>
                            </div>

                            <div class="min-h-28 rounded-2xl bg-green-50 px-6 py-3 flex flex-col justify-center">
                                <p class="text-xs font-bold uppercase tracking-wide text-gray-400">
                                    Aktif Sejak
                                </p>
                                <p class="mt-3 text-lg font-extrabold text-gray-900">
                                    <?= htmlspecialchars($createdText); ?>
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 rounded-3xl bg-amber-50 border border-amber-100 px-4 py-3">
                            <div class="flex items-start gap-4">
                                <div class="flex shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-700"
                                    style="width: 3rem; height: 3rem;">
                                    <i class="fa-solid fa-circle-info"></i>
                                </div>

                                <div>
                                    <h4 class="text-lg font-extrabold text-amber-800">
                                        Catatan Akun
                                    </h4>
                                    <p class="mt-2 text-sm leading-6 text-amber-700">
                                        Jika akun dihapus, data login akan dinonaktifkan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:justify-end">
                            <a href="/profile/edit"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-700">
                                <i class="fa-solid fa-pen-to-square"></i>
                                Edit Profil
                            </a>

                            <a href="/dashboard"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl border border-gray-200 px-5 py-3 text-sm font-bold text-gray-600 transition hover:bg-gray-50">
                                <i class="fa-solid fa-arrow-left"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require '../views/layouts/dashboard.php';
