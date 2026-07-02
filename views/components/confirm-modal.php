<div id="globalConfirmModal"
    class="fixed inset-0 z-[9999] hidden items-center justify-center px-4"
    style="background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(6px);">

    <div
        class="w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl"
        style="border: 1px solid #e5e7eb;">

        <div id="confirmModalTopLine" style="height: 6px; background: #0f766e;"></div>

        <div class="p-6">
            <div class="flex items-start gap-4">
                <div id="confirmModalIcon"
                    class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl"
                    style="background:#ecfdf5;color:#059669;">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                </div>

                <div class="flex-1">
                    <h3 id="confirmModalTitle" class="text-xl font-extrabold text-gray-900">
                        Konfirmasi Aksi
                    </h3>

                    <p id="confirmModalMessage" class="mt-2 text-sm leading-relaxed text-gray-500">
                        Apakah Anda yakin ingin melanjutkan aksi ini?
                    </p>
                </div>
            </div>

            <div class="mt-7 flex justify-end gap-3">
                <button type="button" id="confirmModalCancel"
                    style="background:#ffffff;color:#374151;border:1px solid #d1d5db;border-radius:16px;padding:12px 22px;font-weight:800;cursor:pointer;">
                    Batal
                </button>

                <button type="button" id="confirmModalConfirm"
                    style="background:#0f766e;color:#ffffff;border:1px solid #0f766e;border-radius:16px;padding:12px 22px;font-weight:800;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;">
                    Ya, lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const modal = document.getElementById('globalConfirmModal');
        const title = document.getElementById('confirmModalTitle');
        const message = document.getElementById('confirmModalMessage');
        const icon = document.getElementById('confirmModalIcon');
        const topLine = document.getElementById('confirmModalTopLine');
        const cancelBtn = document.getElementById('confirmModalCancel');
        const confirmBtn = document.getElementById('confirmModalConfirm');

        let pendingAction = null;

        const tones = {
            danger: {
                color: '#dc2626',
                bg: '#fef2f2',
                icon: '<i class="fa-solid fa-trash text-xl"></i>'
            },
            success: {
                color: '#059669',
                bg: '#ecfdf5',
                icon: '<i class="fa-solid fa-circle-check text-xl"></i>'
            },
            warning: {
                color: '#d97706',
                bg: '#fffbeb',
                icon: '<i class="fa-solid fa-triangle-exclamation text-xl"></i>'
            },
            default: {
                color: '#0f766e',
                bg: '#ecfdf5',
                icon: '<i class="fa-solid fa-circle-question text-xl"></i>'
            }
        };

        function applyTone(toneName) {
            const tone = tones[toneName] || tones.default;

            topLine.style.background = tone.color;

            icon.style.background = tone.bg;
            icon.style.color = tone.color;
            icon.innerHTML = tone.icon;

            confirmBtn.style.background = tone.color;
            confirmBtn.style.borderColor = tone.color;
            confirmBtn.style.color = '#ffffff';
            confirmBtn.style.display = 'inline-flex';
        }

        function openModal(options, callback) {
            title.textContent = options.title || 'Konfirmasi Aksi';
            message.textContent = options.message || 'Apakah Anda yakin ingin melanjutkan aksi ini?';
            confirmBtn.textContent = options.confirmText || 'Ya, lanjutkan';

            applyTone(options.tone || 'default');

            pendingAction = callback;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            pendingAction = null;
        }

        cancelBtn.addEventListener('click', closeModal);

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });

        confirmBtn.addEventListener('click', function () {
            if (typeof pendingAction === 'function') {
                const action = pendingAction;
                closeModal();
                action();
            }
        });

        document.addEventListener('click', function (event) {
            const target = event.target.closest('a[data-confirm]');

            if (!target) {
                return;
            }

            event.preventDefault();

            openModal({
                title: target.dataset.confirmTitle,
                message: target.dataset.confirmMessage,
                confirmText: target.dataset.confirmText,
                tone: target.dataset.confirmTone
            }, function () {
                window.location.href = target.href;
            });
        });

        document.addEventListener('submit', function (event) {
            const form = event.target;

            if (!form.matches('form[data-confirm]')) {
                return;
            }

            if (form.dataset.confirmed === '1') {
                return;
            }

            event.preventDefault();

            openModal({
                title: form.dataset.confirmTitle,
                message: form.dataset.confirmMessage,
                confirmText: form.dataset.confirmText,
                tone: form.dataset.confirmTone
            }, function () {
                form.dataset.confirmed = '1';
                form.submit();
            });
        }, true);
    })();
</script>