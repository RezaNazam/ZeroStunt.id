<?php
$tableRows = $tableRows ?? [];
$tableColumns = $tableColumns ?? [];
$tableEmptyMessage = $tableEmptyMessage ?? 'Data tidak ditemukan.';
$tableColspan = count($tableColumns);
$paginationData = $tablePagination ?? $data ?? [];

$halamanAktif = $paginationData['halaman_aktif'] ?? 1;
$perHalaman = $paginationData['per_halaman'] ?? count($tableRows);

$nomorAwal = ($halamanAktif - 1) * $perHalaman;
?>

<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <?php foreach ($tableColumns as $column): ?>
                    <th class="px-6 py-4 text-left font-bold text-gray-600 <?= $column['th_class'] ?? ''; ?>">
                        <?= htmlspecialchars($column['label']); ?>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
            <?php if (empty($tableRows)): ?>
                <tr>
                    <td colspan="<?= $tableColspan; ?>" class="px-6 py-10 text-center text-gray-500">
                        <?= htmlspecialchars($tableEmptyMessage); ?>
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach ($tableRows as $index => $row): ?>
                <tr class="hover:bg-gray-50">
                    <?php foreach ($tableColumns as $column): ?>
                        <td class="px-6 py-4 <?= $column['td_class'] ?? ''; ?>">
                            <?php
                            if (($column['type'] ?? '') === 'number') {
                                echo htmlspecialchars((string) ($nomorAwal + $index + 1));
                            } elseif (isset($column['render']) && is_callable($column['render'])) {
                                echo $column['render']($row);
                            } else {
                                $key = $column['key'] ?? '';
                                echo htmlspecialchars((string) ($row[$key] ?? '-'));
                            }
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$paginationData = $tablePagination ?? $data ?? [];

if (!empty($paginationData) && (($paginationData['total_halaman'] ?? 1) > 1)) {
    require __DIR__ . '/pagination.php';
}
?>