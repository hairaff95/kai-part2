{{-- Global Toast Notification & Confirmation Component --}}
<div id="toast-container" class="fixed top-5 right-5 z-[99999] flex flex-col gap-2.5 pointer-events-none max-w-[92vw] sm:max-w-[420px] w-full px-2 sm:px-0"></div>

{{-- Template Icon SVGs for Javascript cloning --}}
<div id="toast-icon-templates" class="hidden">
    <div data-type="success">
        <x-icon name="toast-sukses" class="w-8 h-8 shrink-0" />
    </div>
    <div data-type="error">
        <x-icon name="toast-gagal" class="w-8 h-8 shrink-0" />
    </div>
    <div data-type="warning">
        <x-icon name="toast-peringatan" class="w-8 h-8 shrink-0" />
    </div>
    <div data-type="info">
        <x-icon name="toast-peringatan" class="w-8 h-8 shrink-0" />
    </div>
</div>

<script>
    (function () {
        let lastToastMsg = '';
        let lastToastTime = 0;

        // Core showToast function
        window.showToast = function (message, type = 'success', duration = 5000) {
            const container = document.getElementById('toast-container');
            if (!container || !message) return;

            // Deduplicate same message within 1.5 seconds
            const now = Date.now();
            if (message === lastToastMsg && (now - lastToastTime) < 1500) {
                return;
            }
            lastToastMsg = message;
            lastToastTime = now;

            // Normalize type
            let normType = (type || 'success').toLowerCase();
            if (normType === 'danger' || normType === 'gagal') normType = 'error';
            if (normType === 'sukses') normType = 'success';
            if (normType === 'peringatan') normType = 'warning';

            // Get icon SVG
            const tpl = document.querySelector(`#toast-icon-templates [data-type="${normType}"]`) ||
                        document.querySelector(`#toast-icon-templates [data-type="success"]`);
            const iconHtml = tpl ? tpl.innerHTML.trim() : '';

            // Create toast element matching mockup
            const toast = document.createElement('div');
            toast.className = `
                pointer-events-auto flex items-center justify-between gap-3.5
                p-3 sm:px-4 sm:py-3.5 rounded-[16px] sm:rounded-[20px]
                bg-white dark:bg-[#25282B]
                border border-gray-100/90 dark:border-white/10
                shadow-[0_10px_35px_rgba(0,0,0,0.12)] dark:shadow-[0_10px_35px_rgba(0,0,0,0.6)]
                text-gray-900 dark:text-white
                opacity-0 translate-y-[-10px] sm:translate-y-0 sm:translate-x-8 scale-95
                transition-all duration-200 ease-out select-none
            `.replace(/\s+/g, ' ').trim();

            toast.innerHTML = `
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="shrink-0 flex items-center justify-center">
                        ${iconHtml}
                    </div>
                    <p class="text-[13px] sm:text-[14px] font-medium leading-snug text-gray-900 dark:text-white truncate-2-lines flex-1">
                        ${message}
                    </p>
                </div>
                <button type="button" class="btn-toast-close p-1.5 -mr-1 rounded-lg text-gray-700 hover:text-black dark:text-white/80 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition cursor-pointer shrink-0" title="Tutup">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            `;

            container.appendChild(toast);

            // Animate in
            requestAnimationFrame(() => {
                toast.classList.remove('opacity-0', 'translate-y-[-10px]', 'sm:translate-x-8', 'scale-95');
                toast.classList.add('opacity-100', 'translate-y-0', 'sm:translate-x-0', 'scale-100');
            });

            // Dismiss logic
            let timer = null;
            let remaining = duration;
            let startTime = Date.now();

            function startTimer() {
                if (duration <= 0) return;
                startTime = Date.now();
                timer = setTimeout(dismiss, remaining);
            }

            function pauseTimer() {
                if (!timer) return;
                clearTimeout(timer);
                remaining -= Date.now() - startTime;
            }

            function dismiss() {
                if (timer) clearTimeout(timer);
                toast.style.transition = 'all 0.15s ease-out';
                toast.classList.remove('opacity-100', 'translate-y-0', 'sm:translate-x-0', 'scale-100');
                toast.classList.add('opacity-0', 'translate-y-[-8px]', 'sm:translate-x-6', 'scale-95');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 150);
            }

            // Close button: langsung hilang seketika saat diklik
            const closeBtn = toast.querySelector('.btn-toast-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dismiss();
                });
            }

            toast.addEventListener('mouseenter', pauseTimer);
            toast.addEventListener('mouseleave', startTimer);

            startTimer();
        };

        // Interactive Toast Confirmation for Delete / Actions (Yakin / Batal)
        window.showToastConfirm = function (options) {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const opts = typeof options === 'string' ? { message: options } : (options || {});
            const message = opts.message || 'Apakah Anda yakin ingin menghapus data ini?';
            const onConfirm = typeof opts.onConfirm === 'function' ? opts.onConfirm : () => {};
            const onCancel = typeof opts.onCancel === 'function' ? opts.onCancel : () => {};

            // Get warning icon
            const tpl = document.querySelector('#toast-icon-templates [data-type="warning"]') ||
                        document.querySelector('#toast-icon-templates [data-type="success"]');
            const iconHtml = tpl ? tpl.innerHTML.trim() : '';

            // Create confirm toast element
            const toast = document.createElement('div');
            toast.className = `
                pointer-events-auto flex flex-col gap-3
                p-3.5 sm:p-4 rounded-[18px] sm:rounded-[22px]
                bg-white dark:bg-[#25282B]
                border border-red-100/80 dark:border-white/15
                shadow-[0_12px_40px_rgba(0,0,0,0.18)] dark:shadow-[0_12px_40px_rgba(0,0,0,0.8)]
                text-gray-900 dark:text-white
                opacity-0 translate-y-[-10px] sm:translate-y-0 sm:translate-x-8 scale-95
                transition-all duration-200 ease-out select-none
            `.replace(/\s+/g, ' ').trim();

            toast.innerHTML = `
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3 min-w-0 flex-1">
                        <div class="shrink-0 flex items-center justify-center mt-0.5">
                            ${iconHtml}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[13px] sm:text-[14px] font-semibold leading-snug text-gray-900 dark:text-white">
                                Konfirmasi Hapus Data
                            </p>
                            <p class="text-xs text-gray-500 dark:text-[#9AA0A6] mt-0.5 leading-relaxed">
                                ${message}
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn-confirm-close p-1.5 -mr-1 rounded-lg text-gray-700 hover:text-black dark:text-white/80 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition cursor-pointer shrink-0" title="Batal">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
                <div class="flex items-center justify-end gap-2 pt-1 border-t border-gray-100 dark:border-white/10">
                    <button type="button" class="btn-confirm-cancel px-3.5 py-1.5 rounded-lg border border-gray-200 dark:border-white/10 bg-transparent hover:bg-gray-100 dark:hover:bg-white/10 text-xs font-semibold text-gray-700 dark:text-gray-200 transition cursor-pointer active:scale-95">
                        Batal
                    </button>
                    <button type="button" class="btn-confirm-yes px-4 py-1.5 rounded-lg bg-[#E00000] hover:bg-red-700 text-xs font-semibold text-white transition shadow-xs cursor-pointer active:scale-95">
                        Yakin
                    </button>
                </div>
            `;

            container.appendChild(toast);

            // Animate in
            requestAnimationFrame(() => {
                toast.classList.remove('opacity-0', 'translate-y-[-10px]', 'sm:translate-x-8', 'scale-95');
                toast.classList.add('opacity-100', 'translate-y-0', 'sm:translate-x-0', 'scale-100');
            });

            function dismissConfirm() {
                toast.style.transition = 'all 0.15s ease-out';
                toast.classList.remove('opacity-100', 'translate-y-0', 'sm:translate-x-0', 'scale-100');
                toast.classList.add('opacity-0', 'translate-y-[-8px]', 'sm:translate-x-6', 'scale-95');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 150);
            }

            toast.querySelector('.btn-confirm-close').addEventListener('click', () => {
                dismissConfirm();
                onCancel();
            });

            toast.querySelector('.btn-confirm-cancel').addEventListener('click', () => {
                dismissConfirm();
                onCancel();
            });

            toast.querySelector('.btn-confirm-yes').addEventListener('click', () => {
                dismissConfirm();
                onConfirm();
            });
        };

        // Helper to intercept delete actions with the custom toast confirm
        window.confirmDelete = function (actionOrForm, message = 'Data yang dihapus tidak dapat dikembalikan. Lanjutkan?') {
            window.showToastConfirm({
                message: message,
                onConfirm: () => {
                    if (typeof actionOrForm === 'function') {
                        actionOrForm();
                    } else if (actionOrForm && typeof actionOrForm.submit === 'function') {
                        actionOrForm.submit();
                    } else if (typeof actionOrForm === 'string') {
                        window.location.href = actionOrForm;
                    }
                }
            });
            return false;
        };

        // Helper to queue toast before page reload/redirect
        window.setPendingToast = function (message, type = 'success', viewUrl = null) {
            try {
                if (typeof message === 'object') {
                    sessionStorage.setItem('kai_pending_toast', JSON.stringify({ ...message, time: Date.now() }));
                } else {
                    sessionStorage.setItem('kai_pending_toast', JSON.stringify({ message, type, viewUrl, time: Date.now() }));
                }
            } catch (e) {}
        };

        // Interactive Toast with Action Buttons (Lihat Aset & Batal/Tutup)
        window.showToastAction = function (options) {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const opts = typeof options === 'string' ? { message: options } : (options || {});
            const title = opts.title || 'Aset Berhasil Ditambahkan';
            const message = opts.message || 'Data aset dan kontrak baru telah berhasil disimpan ke sistem.';
            const viewUrl = opts.viewUrl || null;
            const viewText = opts.viewText || 'Lihat Aset';
            const cancelText = opts.cancelText || 'Batal';
            const duration = opts.duration || 8000;
            const onCancel = typeof opts.onCancel === 'function' ? opts.onCancel : () => {};

            // Get success icon
            const tpl = document.querySelector('#toast-icon-templates [data-type="success"]');
            const iconHtml = tpl ? tpl.innerHTML.trim() : '';

            // Create action toast element
            const toast = document.createElement('div');
            toast.className = `
                pointer-events-auto flex flex-col gap-3
                p-3.5 sm:p-4 rounded-[18px] sm:rounded-[22px]
                bg-white dark:bg-[#25282B]
                border border-blue-100/90 dark:border-white/15
                shadow-[0_12px_40px_rgba(0,0,0,0.18)] dark:shadow-[0_12px_40px_rgba(0,0,0,0.8)]
                text-gray-900 dark:text-white
                opacity-0 translate-y-[-10px] sm:translate-y-0 sm:translate-x-8 scale-95
                transition-all duration-200 ease-out select-none
            `.replace(/\s+/g, ' ').trim();

            toast.innerHTML = `
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3 min-w-0 flex-1">
                        <div class="shrink-0 flex items-center justify-center mt-0.5">
                            ${iconHtml}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[13px] sm:text-[14px] font-semibold leading-snug text-gray-900 dark:text-white">
                                ${title}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-[#9AA0A6] mt-0.5 leading-relaxed">
                                ${message}
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn-action-close p-1.5 -mr-1 rounded-lg text-gray-700 hover:text-black dark:text-white/80 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition cursor-pointer shrink-0" title="Tutup">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100 dark:border-white/10">
                    <button type="button" class="btn-action-cancel px-3.5 py-1.5 rounded-lg border border-gray-200 dark:border-white/10 bg-transparent hover:bg-gray-100 dark:hover:bg-white/10 text-xs font-semibold text-gray-700 dark:text-gray-200 transition cursor-pointer active:scale-95">
                        ${cancelText}
                    </button>
                    ${viewUrl ? `
                        <a href="${viewUrl}" class="btn-action-view inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-[#0066FF] hover:bg-blue-700 text-xs font-semibold text-white transition shadow-xs cursor-pointer active:scale-95">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <span>${viewText}</span>
                        </a>
                    ` : ''}
                </div>
            `;

            container.appendChild(toast);

            // Animate in
            requestAnimationFrame(() => {
                toast.classList.remove('opacity-0', 'translate-y-[-10px]', 'sm:translate-x-8', 'scale-95');
                toast.classList.add('opacity-100', 'translate-y-0', 'sm:translate-x-0', 'scale-100');
            });

            // Dismiss logic
            let timer = null;
            if (duration > 0) {
                timer = setTimeout(dismissAction, duration);
            }

            function dismissAction() {
                if (timer) clearTimeout(timer);
                toast.style.transition = 'all 0.15s ease-out';
                toast.classList.remove('opacity-100', 'translate-y-0', 'sm:translate-x-0', 'scale-100');
                toast.classList.add('opacity-0', 'translate-y-[-8px]', 'sm:translate-x-6', 'scale-95');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 150);
            }

            toast.querySelector('.btn-action-close').addEventListener('click', () => {
                dismissAction();
                onCancel();
            });

            toast.querySelector('.btn-action-cancel').addEventListener('click', () => {
                dismissAction();
                onCancel();
            });
        };

        // Auto trigger toast on page load (sessionStorage fallback + server session)
        function triggerSessionToasts() {
            // Check sessionStorage
            try {
                const pending = sessionStorage.getItem('kai_pending_toast');
                if (pending) {
                    sessionStorage.removeItem('kai_pending_toast');
                    const parsed = JSON.parse(pending);
                    if (parsed && parsed.message && (Date.now() - (parsed.time || 0)) < 10000) {
                        if (parsed.viewUrl) {
                            window.showToastAction({
                                title: parsed.title || 'Aset Berhasil Ditambahkan',
                                message: parsed.message,
                                viewUrl: parsed.viewUrl,
                                viewText: parsed.viewText || 'Lihat Aset',
                                cancelText: parsed.cancelText || 'Batal'
                            });
                        } else {
                            window.showToast(parsed.message, parsed.type || 'success', 5000);
                        }
                    }
                }
            } catch (e) {}

            // Check server session flash
            setTimeout(() => {
                @if(session('created_asset_url'))
                    window.showToastAction({
                        title: 'Aset Berhasil Ditambahkan',
                        message: @json(session('success') ?? 'Aset dan kontrak baru berhasil ditambahkan!'),
                        viewUrl: @json(session('created_asset_url')),
                        viewText: 'Lihat Aset',
                        cancelText: 'Batal'
                    });
                @elseif(session('success'))
                    window.showToast(@json(session('success')), 'success', 5000);
                @endif

                @if(session('error'))
                    window.showToast(@json(session('error')), 'error', 5000);
                @endif

                @if(session('warning'))
                    window.showToast(@json(session('warning')), 'warning', 5000);
                @endif

                @if(session('info'))
                    window.showToast(@json(session('info')), 'info', 5000);
                @endif

                @if(session('status') && !session('success'))
                    window.showToast(@json(session('status')), 'info', 5000);
                @endif

                @if(isset($errors) && $errors->any())
                    @foreach($errors->all() as $error)
                        window.showToast(@json($error), 'error', 5000);
                    @endforeach
                @endif
            }, 60);
        }

        // Global Form Validation Listener for contract_number & asset_number across all CRUD forms
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!form || !(form instanceof HTMLFormElement)) return;

            // Skip delete or logout forms
            if (form.id && (form.id.includes('delete') || form.id.includes('hapus') || form.id.includes('logout'))) return;
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput && methodInput.value.toUpperCase() === 'DELETE') return;

            const contractInput = form.querySelector('[name="contract_number"], [name="no_kontrak"], #input-contract-number, #contract_number');
            const assetInput    = form.querySelector('[name="asset_number"], [name="no_aset"], #input-asset-number, #asset_number');

            if (contractInput && contractInput.type !== 'hidden' && !contractInput.readOnly && !contractInput.disabled) {
                if (!contractInput.value || contractInput.value.trim() === '') {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    contractInput.focus();
                    contractInput.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                    setTimeout(() => contractInput.classList.remove('border-red-500', 'ring-1', 'ring-red-500'), 3000);
                    window.showToast('Field Nomor Kontrak wajib diisi dan tidak boleh kosong!', 'warning', 4500);
                    return false;
                }
            }

            if (assetInput && assetInput.type !== 'hidden' && !assetInput.readOnly && !assetInput.disabled) {
                if (!assetInput.value || assetInput.value.trim() === '') {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    assetInput.focus();
                    assetInput.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                    setTimeout(() => assetInput.classList.remove('border-red-500', 'ring-1', 'ring-red-500'), 3000);
                    window.showToast('Field Nomor Aset wajib diisi dan tidak boleh kosong!', 'warning', 4500);
                    return false;
                }
            }
        }, true);

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', triggerSessionToasts);
        } else {
            triggerSessionToasts();
        }
    })();
</script>
