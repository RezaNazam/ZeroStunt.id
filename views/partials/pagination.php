<?php
$paginationData = $paginationData ?? $data ?? [];

$halamanAktif = $paginationData['halaman_aktif'] ?? 1;
$totalHalaman = $paginationData['total_halaman'] ?? 1;
$pageParam = $paginationData['page_param'] ?? 'page';

$queryParams = $_GET;
unset($queryParams[$pageParam], $queryParams['ajax'], $queryParams['ajax_target']);

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$buildUrl = function ($page) use ($queryParams, $pageParam, $currentPath) {
    $queryParams[$pageParam] = $page;
    return $currentPath . '?' . http_build_query($queryParams);
};
?>

<?php if ($totalHalaman > 1): ?>
    <div class="border-t border-gray-100 px-6 py-4 flex items-center justify-between bg-white rounded-b-3xl">
        <div class="text-sm text-gray-500">
            Halaman <span class="font-bold text-gray-900"><?= $halamanAktif ?></span>
            dari <span class="font-bold text-gray-900"><?= $totalHalaman ?></span>
        </div>

        <div class="flex items-center gap-2">
            <?php if ($halamanAktif > 1): ?>
                <a href="<?= $buildUrl($halamanAktif - 1) ?>"
                    class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    <i class="fa-solid fa-chevron-left mr-1"></i> Sebelum
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalHalaman; $i++): ?>
                <a href="<?= $buildUrl($i) ?>"
                    class="rounded-xl px-4 py-2 text-sm font-bold transition <?= $halamanAktif === $i ? 'bg-teal-600 text-white shadow-sm' : 'border border-gray-200 text-gray-700 hover:bg-gray-50' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($halamanAktif < $totalHalaman): ?>
                <a href="<?= $buildUrl($halamanAktif + 1) ?>"
                    class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    Sesudah <i class="fa-solid fa-chevron-right ml-1"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>