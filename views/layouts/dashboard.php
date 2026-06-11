<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Dashboard'); ?> - ZeroStunt.id</title>
    <link href="/css/tailwind.css" rel="stylesheet">

    <style>
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
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <div id="dashboardContent" class="min-h-screen lg:ml-72 transition-all duration-300">
        <?php include __DIR__ . '/../components/navbar.php'; ?>

        <main class="p-6 lg:p-8">
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