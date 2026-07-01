<?php
$currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$role = $_SESSION['role'] ?? 'User';

$isAdmin = defined('ROLE_ADMIN') ? $role === ROLE_ADMIN : strtolower($role) === 'admin';
$isIbu = defined('ROLE_IBU') ? $role === ROLE_IBU : strtolower($role) === 'ibu';
$isPetani = defined('ROLE_PETANI') ? $role === ROLE_PETANI : strtolower($role) === 'petani';
$isKader = defined('ROLE_KADER') ? $role === ROLE_KADER : strtolower($role) === 'kader';

$menus = [];

// SINKRONISASI IKON KE FONT AWESOME V6 SOLID
if ($isAdmin) {
    $menus = [
        ['label' => 'Dashboard', 'href' => '/dashboard', 'icon' => '<i class="fa-solid fa-chart-pie"></i>'],
        ['label' => 'Pengguna', 'href' => '/master/users', 'icon' => '<i class="fa-solid fa-users"></i>'],
        ['label' => 'Ibu & Anak', 'href' => '/master/ibu/ibuAnak', 'icon' => '<i class="fa-solid fa-person-breastfeeding"></i>'],
        ['label' => 'Standar Pertumbuhan', 'href' => '/master/standar-pertumbuhan', 'icon' => '<i class="fa-solid fa-chart-line"></i>'],
        ['label' => 'Gudang', 'href' => '/master/gudang', 'icon' => '<i class="fa-solid fa-warehouse"></i>'],
        ['label' => 'Komoditas Pangan', 'href' => '/master/komoditas', 'icon' => '<i class="fa-solid fa-carrot"></i>'],
        ['label' => 'Satuan', 'href' => '/master/satuan', 'icon' => '<i class="fa-solid fa-weight-scale"></i>'],
        ['label' => 'Stok Gudang', 'href' => '/master/kader/stok', 'icon' => '<i class="fa-solid fa-cubes"></i>'],
        ['label' => 'Paket Gizi', 'href' => '/master/paket-gizi', 'icon' => '<i class="fa-solid fa-boxes-stacked"></i>'],
        ['label' => 'Pengadaan', 'href' => '/transaksi/pengadaan', 'icon' => '<i class="fa-solid fa-box-open"></i>'],
        ['label' => 'Distribusi', 'href' => '/transaksi/distribusi', 'icon' => '<i class="fa-solid fa-truck-ramp-box"></i>'],
        ['label' => 'Laporan', 'href' => '/laporan', 'icon' => '<i class="fa-solid fa-chart-line"></i>'],
    ];
} elseif ($isKader) {
    $menus = [
        ['label' => 'Dashboard', 'href' => '/dashboard', 'icon' => '<i class="fa-solid fa-chart-pie"></i>'],
        ['label' => 'Standar Pertumbuhan', 'href' => '/master/standar-pertumbuhan', 'icon' => '<i class="fa-solid fa-chart-line"></i>'],
        ['label' => 'Pemeriksaan', 'href' => '/transaksi/pemeriksaan', 'icon' => '<i class="fa-solid fa-user-doctor"></i>'],
        ['label' => 'Penyerahan', 'href' => '/transaksi/penyerahan', 'icon' => '<i class="fa-solid fa-hand-holding-heart"></i>'],
        ['label' => 'Distribusi', 'href' => '/transaksi/distribusi', 'icon' => '<i class="fa-solid fa-truck-ramp-box"></i>'],
        ['label' => 'Stok Posyandu', 'href' => '/master/kader/stok', 'icon' => '<i class="fa-solid fa-cubes"></i>'],
        ['label' => 'Laporan', 'href' => '/laporan', 'icon' => '<i class="fa-solid fa-chart-line"></i>'],
    ];
} elseif ($isPetani) {
    $menus = [
        ['label' => 'Dashboard', 'href' => '/dashboard', 'icon' => '<i class="fa-solid fa-chart-pie"></i>'],
        ['label' => 'Pengadaan Saya', 'href' => '/transaksi/pengadaan/ambil', 'icon' => '<i class="fa-solid fa-file-invoice-dollar"></i>'],
        ['label' => 'Riwayat Ekonomi', 'href' => '/master/petani/riwayat-ekonomi', 'icon' => '<i class="fa-solid fa-wallet"></i>'],
        ['label' => 'Profil Lahan', 'href' => '/master/petani/profil-lahan', 'icon' => '<i class="fa-solid fa-seedling"></i>'],
        ['label' => 'Laporan', 'href' => '/laporan', 'icon' => '<i class="fa-solid fa-chart-line"></i>'],
    ];
} elseif ($isIbu) {
    $menus = [
        ['label' => 'Dashboard', 'href' => '/dashboard', 'icon' => '<i class="fa-solid fa-chart-pie"></i>'],
        ['label' => 'Data Anak', 'href' => '/master/anak', 'icon' => '<i class="fa-solid fa-baby"></i>'],
        ['label' => 'Histori Bantuan', 'href' => '/master/ibu/histori-bantuan', 'icon' => '<i class="fa-solid fa-gift"></i>'],
        ['label' => 'Riwayat Periksa', 'href' => '/master/ibu/riwayat-periksa', 'icon' => '<i class="fa-solid fa-clipboard-list"></i>'],
        ['label' => 'Laporan', 'href' => '/laporan', 'icon' => '<i class="fa-solid fa-chart-line"></i>'],
    ];
}
?>

<aside id="dashboardSidebar"
    class="fixed top-0 left-0 z-50 h-screen w-72 bg-teal-800 text-white transform -translate-x-full lg:translate-x-0 transition-all duration-300 overflow-hidden">

    <div class="h-full flex flex-col">
        <div class="h-20 px-6 flex items-center justify-between border-b border-teal-700">
            <a href="/dashboard" class="flex items-center gap-3 font-bold text-xl">
                <span class="w-10 h-10 rounded-2xl bg-white/15 flex items-center justify-center shrink-0 text-base">
                    🌱
                </span>
                <span class="sidebar-logo-text whitespace-nowrap">
                    ZeroStunt<span class="text-amber-400">.id</span>
                </span>
            </a>
            <button id="closeSidebar" class="lg:hidden text-white/80 hover:text-white">✕</button>
        </div>

        <div class="px-6 py-4 border-b border-teal-700">
            <span
                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400/20 text-amber-200 text-xs font-bold">
                <i class="fa-solid fa-shield-cat text-sm"></i>
                <span class="sidebar-role-text"><?= htmlspecialchars($role); ?></span>
            </span>
        </div>

        <nav class="flex-1 px-4 py-5 space-y-2 overflow-y-auto no-scrollbar">
            <?php foreach ($menus as $menu): ?>
                <?php $isActive = $menu['href'] !== '#' && ($currentUri === $menu['href'] || strpos($currentUri, $menu['href'] . '/') === 0); ?>

                <a href="<?= $menu['href']; ?>" class="sidebar-menu-link flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition
                    <?= $isActive
                        ? 'bg-amber-500 text-white shadow-lg'
                        : 'text-teal-100 hover:bg-white/10 hover:text-white'; ?>">

                    <span class="sidebar-menu-icon flex items-center justify-center w-5 h-5 text-lg shrink-0">
                        <?= $menu['icon']; ?>
                    </span>

                    <span class="sidebar-label whitespace-nowrap">
                        <?= htmlspecialchars($menu['label']); ?>
                    </span>
                </a>
            <?php endforeach; ?>
            <?php if ($isAdmin): ?>
    <?php endif; ?>
</nav>

        <a href="/auth/logout"
            class="sidebar-logout flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-bold text-teal-100 hover:bg-red-500/20 hover:text-white transition">
            <span class="flex items-center justify-center w-5 h-5 text-lg shrink-0">
                <i class="fa-solid fa-right-from-bracket"></i>
            </span>
            <span class="sidebar-label whitespace-nowrap">Logout</span>
        </a>
    </div>
</aside>