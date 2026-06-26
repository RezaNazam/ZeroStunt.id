<?php
$searchAction = $searchAction ?? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$searchPlaceholder = $searchPlaceholder ?? 'Cari data...';
$searchTarget = $searchTarget ?? 'tableResult';
$searchParam = $searchParam ?? 'q';
$pageParam = $pageParam ?? 'page';

$searchValue = $_GET[$searchParam] ?? '';
?>

<form method="GET"
    action="<?= htmlspecialchars($searchAction); ?>"
    class="w-full js-ajax-search"
    data-target="<?= htmlspecialchars($searchTarget); ?>"
    data-search-param="<?= htmlspecialchars($searchParam); ?>"
    data-page-param="<?= htmlspecialchars($pageParam); ?>">

    <div class="relative">
        <input type="text"
            name="<?= htmlspecialchars($searchParam); ?>"
            value="<?= htmlspecialchars($searchValue); ?>"
            placeholder="<?= htmlspecialchars($searchPlaceholder); ?>"
            autocomplete="off"
            class="js-ajax-search-input w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 pl-11 text-sm outline-none transition focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-100">

        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
    </div>
</form>

<script>
    (function() {
        if (window.zeroStuntAjaxSearchReady) {
            return;
        }

        window.zeroStuntAjaxSearchReady = true;

        async function loadTable(url, targetId) {
            const target = document.getElementById(targetId);

            if (!target) {
                console.error('Target table tidak ditemukan:', targetId);
                return;
            }

            target.classList.add('opacity-50');

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const html = await response.text();

                if (html.trim() === '') {
                    console.error('Response AJAX kosong. Cek ajax_target:', targetId);
                    return;
                }

                target.innerHTML = html;

                const cleanUrl = new URL(url, window.location.origin);
                cleanUrl.searchParams.delete('ajax');
                cleanUrl.searchParams.delete('ajax_target');

                window.history.replaceState({}, '', cleanUrl.pathname + cleanUrl.search);
            } catch (error) {
                console.error(error);
            } finally {
                target.classList.remove('opacity-50');
            }
        }

        function buildSearchUrl(form) {
            const targetId = form.dataset.target;
            const searchParam = form.dataset.searchParam || 'q';
            const pageParam = form.dataset.pageParam || 'page';
            const input = form.querySelector('.js-ajax-search-input');

            const params = new URLSearchParams(window.location.search);

            if (input.value.trim() === '') {
                params.delete(searchParam);
            } else {
                params.set(searchParam, input.value.trim());
            }

            params.delete(pageParam);
            params.set('ajax', '1');
            params.set('ajax_target', targetId);

            const action = form.getAttribute('action') || window.location.pathname;

            return action + '?' + params.toString();
        }

        document.addEventListener('input', function(event) {
            const input = event.target.closest('.js-ajax-search-input');

            if (!input) {
                return;
            }

            const form = input.closest('.js-ajax-search');

            if (!form) {
                return;
            }

            clearTimeout(input.searchTimer);

            input.searchTimer = setTimeout(function() {
                const targetId = form.dataset.target;
                const url = buildSearchUrl(form);

                loadTable(url, targetId);
            }, 400);
        });

        document.addEventListener('submit', function(event) {
            const form = event.target.closest('.js-ajax-search');

            if (!form) {
                return;
            }

            event.preventDefault();

            const targetId = form.dataset.target;
            const url = buildSearchUrl(form);

            loadTable(url, targetId);
        });

        document.addEventListener('click', function(event) {
            if (event.defaultPrevented) {
                return;
            }

            const link = event.target.closest('a');

            if (!link) {
                return;
            }

            const target = document.getElementById(searchTarget);

            if (!target || !target.contains(link)) {
                return;
            }

            const url = new URL(link.href, window.location.origin);

            // AJAX cuma boleh nangkep pagination/search di halaman yang sama.
            // Link edit/delete/detail beda path, jadi jangan di-AJAX-kan.
            if (url.pathname !== window.location.pathname) {
                return;
            }

            // AJAX cuma untuk link pagination yang punya parameter page.
            // Contoh: /master/users?page=2
            if (!url.searchParams.has(pageParam)) {
                return;
            }

            event.preventDefault();
            loadTable(url.toString());
        });
    })();
</script>