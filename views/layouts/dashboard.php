<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Dashboard'); ?> - ZeroStunt.id</title>
    <link rel="icon" type="image/png" href="/img/icon-logo.png?v=2">
    <link rel="shortcut icon" type="image/png" href="/img/icon-logo.png?v=2">
    <link rel="apple-touch-icon" href="/img/icon-logo.png?v=2">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="/css/tailwind.css" rel="stylesheet">

    <style>
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        @media (min-width: 1024px) {
            body.sidebar-collapsed #dashboardSidebar {
                width: 5rem;
            }

            body.sidebar-collapsed #dashboardContent {
                margin-left: 5rem;
            }

            body.sidebar-collapsed .sidebar-logo-text,
            body.sidebar-collapsed .sidebar-label,
            body.sidebar-collapsed .sidebar-role-text,
            body.sidebar-collapsed .sidebar-user-text {
                display: none;
            }

            body.sidebar-collapsed .sidebar-menu-link {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
            }

            body.sidebar-collapsed .sidebar-menu-icon {
                margin: 0;
            }

            body.sidebar-collapsed .sidebar-logout {
                justify-content: center;
            }
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 overflow-x-hidden">
    <?php $hideSidebar = $hideSidebar ?? false; ?>

    <?php if (!$hideSidebar): ?>
        <?php include __DIR__ . '/../components/sidebar.php'; ?>
    <?php endif; ?>

    <div id="dashboardContent" class="min-h-screen transition-all duration-300 <?= !$hideSidebar ? 'lg:ml-72' : ''; ?>">

        <?php include __DIR__ . '/../components/navbar.php'; ?>

        <main class="<?= $hideSidebar ? 'p-6 lg:p-8' : 'p-6 lg:p-8'; ?>">
            <?= $content ?? ''; ?>
        </main>
    </div>

    <script>
        const sidebar = document.getElementById('dashboardSidebar');
        const openSidebar = document.getElementById('openSidebar');
        const closeSidebar = document.getElementById('closeSidebar');

        const savedSidebarState = localStorage.getItem('sidebar-collapsed');

        if (savedSidebarState === 'true') {
            document.body.classList.add('sidebar-collapsed');
        }

        openSidebar?.addEventListener('click', () => {
            if (window.innerWidth >= 1024) {
                document.body.classList.toggle('sidebar-collapsed');

                localStorage.setItem(
                    'sidebar-collapsed',
                    document.body.classList.contains('sidebar-collapsed')
                );
            } else {
                sidebar?.classList.remove('-translate-x-full');
            }
        });

        closeSidebar?.addEventListener('click', () => {
            sidebar?.classList.add('-translate-x-full');
        });
    </script>
</body>

</html>