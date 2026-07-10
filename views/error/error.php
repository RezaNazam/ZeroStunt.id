<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        <?= htmlspecialchars((string) $errorCode); ?> -
        <?= htmlspecialchars($errorTitle); ?>
    </title>

    <link rel="stylesheet" href="/css/tailwind.css">
</head>

<body class="min-h-screen bg-gray-50 text-gray-900">
    <main class="min-h-screen flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-xl text-center">
            <!-- Logo -->
            <a href="/landing"
                class="mb-10 inline-flex items-center gap-3 text-xl font-extrabold text-teal-800">
                <span class="w-11 h-11 rounded-2xl bg-white text-teal-700 flex items-center justify-center text-sm font-extrabold shadow-lg">
                    ZS
                </span>

                <span>
                    ZeroStunt<span class="text-amber-500">.id</span>
                </span>
            </a>

            <!-- Error Card -->
            <div class="relative overflow-hidden rounded-3xl border border-gray-100 bg-white px-7 py-10 shadow-xl sm:px-12">
                <div class="absolute -right-16 -top-16 h-40 w-40 rounded-full bg-teal-50"></div>
                <div class="absolute -bottom-20 -left-20 h-48 w-48 rounded-full bg-amber-50"></div>

                <div class="relative z-10">
                    <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-red-50 text-4xl">
                        <?php if ($errorCode === 404): ?>
                            <i class="fa-solid fa-file-circle-question text-blue-500"></i>
                        <?php elseif ($errorCode === 403): ?>
                            <i class="fa-solid fa-user-lock text-red-500"></i>
                        <?php else: ?>
                            <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                        <?php endif; ?>
                    </div>

                    <p class="text-sm font-extrabold uppercase tracking-[0.25em] text-teal-600">
                        Error
                    </p>

                    <h1 class="mt-2 text-7xl font-black tracking-tight text-gray-900">
                        <?= htmlspecialchars((string) $errorCode); ?>
                    </h1>

                    <h2 class="mt-4 text-2xl font-extrabold text-gray-900">
                        <?= htmlspecialchars($errorTitle); ?>
                    </h2>

                    <p class="mx-auto mt-3 max-w-md text-sm leading-7 text-gray-500">
                        <?= htmlspecialchars($errorMessage); ?>
                    </p>

                    <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                        <button type="button"
                            onclick="history.back()"
                            class="inline-flex items-center justify-center rounded-2xl border border-gray-200 px-5 py-3 text-sm font-bold text-gray-600 transition hover:bg-gray-50">
                            ← Kembali
                        </button>

                        <a href="<?= htmlspecialchars($actionUrl); ?>"
                            class="inline-flex items-center justify-center rounded-2xl bg-teal-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-700">
                            <?= htmlspecialchars($actionLabel); ?>
                        </a>
                    </div>
                </div>
            </div>

            <p class="mt-6 text-xs text-gray-400">
                Pastikan alamat halaman dan hak akses akunmu sudah benar.
            </p>
        </div>
    </main>
</body>

</html>