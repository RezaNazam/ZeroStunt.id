<header class="sticky top-0 z-30 bg-white border-b border-gray-100">
    <div class="h-20 px-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <button id="openSidebar"
                class="w-11 h-11 rounded-2xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition">
                ☰
            </button>

            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">
                    <?= htmlspecialchars($pageTitle ?? 'Dashboard'); ?>
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    <?= htmlspecialchars($pageSubtitle ?? 'Ringkasan aktivitas sistem ZeroStunt.id'); ?>
                </p>
            </div>
        </div>

        <div class="hidden md:flex items-center gap-4">
            <div class="relative">
                <input
                    type="text"
                    placeholder="Cari data..."
                    class="w-64 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">
            </div>

            <button class="w-11 h-11 rounded-2xl bg-gray-50 border border-gray-100 hover:bg-gray-100">
                🔔
            </button>

            <div class="flex items-center gap-3 pl-4 border-l border-gray-100">
                <div class="w-11 h-11 rounded-2xl bg-teal-600 text-white flex items-center justify-center font-bold">
                    <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
                </div>

                <div>
                    <p class="text-sm font-bold text-gray-900">
                        <?= htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
                    </p>
                    <p class="text-xs text-gray-500">
                        <?= htmlspecialchars($_SESSION['role'] ?? 'Role'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</header>