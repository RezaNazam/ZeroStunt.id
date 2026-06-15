<?php
$currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$role = $_SESSION['role'] ?? 'User';

$isAdmin = defined('ROLE_ADMIN') ? $role === ROLE_ADMIN : strtolower($role) === 'admin';
$isIbu = defined('ROLE_IBU') ? $role === ROLE_IBU : strtolower($role) === 'ibu';
$isPetani = defined('ROLE_PETANI') ? $role === ROLE_PETANI : strtolower($role) === 'petani';
$isKader = defined('ROLE_KADER') ? $role === ROLE_KADER : strtolower($role) === 'kader';

$menus = [];

if ($isAdmin) {
    $menus = [
        ['label' => 'Dashboard', 'href' => '/dashboard', 'icon' => '📊'],
        ['label' => 'Ibu & Anak', 'href' => '#', 'icon' => '👩‍👧'],
        ['label' => 'Pemeriksaan', 'href' => '#', 'icon' => '🩺'],
        ['label' => 'Pengadaan', 'href' => '#', 'icon' => '📦'],
        ['label' => 'Distribusi', 'href' => '#', 'icon' => '🚚'],
        ['label' => 'Penyerahan', 'href' => '#', 'icon' => '🤝'],
        ['label' => 'Laporan', 'href' => '#', 'icon' => '📈'],
        ['label' => 'Master Data', 'href' => '#', 'icon' => '🗂️'],
    ];
} elseif ($isKader) {
    $menus = [
        ['label' => 'Dashboard', 'href' => '/dashboard', 'icon' => '📊'],
        ['label' => 'Pemeriksaan', 'href' => '#', 'icon' => '🩺'],
        ['label' => 'Penyerahan', 'href' => '#', 'icon' => '🤝'],
        ['label' => 'Stok Posyandu', 'href' => '#', 'icon' => '📦'],
        ['label' => 'Laporan', 'href' => '#', 'icon' => '📈'],
    ];
} elseif ($isPetani) {
    $menus = [
        ['label' => 'Dashboard', 'href' => '/dashboard', 'icon' => '📊'],
        ['label' => 'Pengadaan Saya', 'href' => '#', 'icon' => '📦'],
        ['label' => 'Riwayat Ekonomi', 'href' => '#', 'icon' => '💰'],
        ['label' => 'Profil Lahan', 'href' => '#', 'icon' => '🌾'],
    ];
} elseif ($isIbu) {
    $menus = [
        ['label' => 'Dashboard', 'href' => '/dashboard', 'icon' => '📊'],
        ['label' => 'Data Anak', 'href' => '/master/anak', 'icon' => '👶'],
        ['label' => 'Histori Bantuan', 'href' => '#', 'icon' => '🎁'],
        ['label' => 'Riwayat Periksa', 'href' => '#', 'icon' => '🩺'],
    ];
}
?>

<aside id="dashboardSidebar"
    class="fixed top-0 left-0 z-50 h-screen w-72 bg-teal-800 text-white transform -translate-x-full lg:translate-x-0 transition-all duration-300 overflow-hidden">
    
    <div class="h-full flex flex-col">
        <!-- Logo -->
        <div class="h-20 px-6 flex items-center justify-between border-b border-teal-700">
            <a href="/dashboard" class="flex items-center gap-3 font-bold text-xl">
                <span class="w-10 h-10 rounded-2xl bg-white/15 flex items-center justify-center shrink-0">
                    🌱
                </span>

                <span class="sidebar-logo-text whitespace-nowrap">
                    ZeroStunt<span class="text-amber-400">.id</span>
                </span>
            </a>

            <button id="closeSidebar" class="lg:hidden text-white/80 hover:text-white">
                ✕
            </button>
        </div>

        <!-- User -->
        <div class="px-6 py-4 border-b border-teal-700">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400/20 text-amber-200 text-xs font-bold">
                <span>🛡️</span>
                <span class="sidebar-role-text"><?= htmlspecialchars($role); ?></span>
            </span>
        </div>

        <!-- Menu -->
        <nav class="flex-1 px-4 py-5 space-y-2 overflow-y-auto">
            <?php foreach ($menus as $menu): ?>
                <?php $isActive = $menu['href'] !== '#' && $currentUri === $menu['href']; ?>

                <a href="<?= $menu['href']; ?>"
                    class="sidebar-menu-link flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition
                    <?= $isActive
                        ? 'bg-amber-500 text-white shadow-lg'
                        : 'text-teal-100 hover:bg-white/10 hover:text-white'; ?>">

                    <span class="sidebar-menu-icon text-lg shrink-0">
                        <?= $menu['icon']; ?>
                    </span>

                    <span class="sidebar-label whitespace-nowrap">
                        <?= htmlspecialchars($menu['label']); ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- Logout -->
        <a href="/auth/logout"
            class="sidebar-logout flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-bold text-teal-100 hover:bg-red-500/20 hover:text-white transition">
            <span class="text-lg shrink-0">🚪</span>
            <span class="sidebar-label whitespace-nowrap">Logout</span>
        </a>
    </div>
</aside>