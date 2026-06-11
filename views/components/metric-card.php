<?php
if (!function_exists('renderMetricCard')) {
    function renderMetricCard($title, $value, $caption, $icon, $tone = 'teal')
    {
        $tones = [
            'teal' => [
                'bg' => 'bg-teal-50',
                'text' => 'text-teal-700',
                'iconBg' => 'bg-teal-100',
            ],
            'green' => [
                'bg' => 'bg-green-50',
                'text' => 'text-green-700',
                'iconBg' => 'bg-green-100',
            ],
            'amber' => [
                'bg' => 'bg-amber-50',
                'text' => 'text-amber-700',
                'iconBg' => 'bg-amber-100',
            ],
            'red' => [
                'bg' => 'bg-red-50',
                'text' => 'text-red-700',
                'iconBg' => 'bg-red-100',
            ],
            'blue' => [
                'bg' => 'bg-blue-50',
                'text' => 'text-blue-700',
                'iconBg' => 'bg-blue-100',
            ],
        ];

        $style = $tones[$tone] ?? $tones['teal'];
        ?>

        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-gray-500">
                        <?= htmlspecialchars($title); ?>
                    </p>

                    <p class="text-3xl font-extrabold text-gray-900 mt-3">
                        <?= htmlspecialchars($value); ?>
                    </p>

                    <p class="text-sm text-gray-500 mt-2">
                        <?= htmlspecialchars($caption); ?>
                    </p>
                </div>

                <div class="w-14 h-14 rounded-2xl <?= $style['iconBg']; ?> <?= $style['text']; ?> flex items-center justify-center text-2xl">
                    <?= $icon; ?>
                </div>
            </div>
        </div>

        <?php
    }
}