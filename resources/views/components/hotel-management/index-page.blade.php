@props([
    'title',
    'subtitle',
    'createRoute' => null,
    'showCreateButton' => true,
])

<x-app-layout :assets="['animation']">
    <style>
        .hm-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.38rem 0.78rem;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 700;
        }

        .hm-badge--success { background: #dcfce7; color: #166534; }
        .hm-badge--warning { background: #fef3c7; color: #9a3412; }
        .hm-badge--muted { background: #eceff3; color: #475569; }
        .hm-badge--danger { background: #fee2e2; color: #b91c1c; }
        .hm-badge--info { background: #dbeafe; color: #1d4ed8; }

        .hm-create-button {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
        }

        .hm-create-button svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .hm-icon-button {
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-width: 42px;
            padding: 0 0.9rem;
            border-radius: 5px;
            white-space: nowrap;
            font-weight: 500;
        }

        .hm-icon-button svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .hm-select-wrap {
            position: relative;
        }

        .hm-select-wrap::after {
            content: '';
            position: absolute;
            top: 50%;
            right: 14px;
            width: 10px;
            height: 6px;
            pointer-events: none;
            transform: translateY(-50%);
            background-repeat: no-repeat;
            background-size: 10px 6px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6' fill='none'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%2364748B' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        }

        .hm-select-wrap .form-select,
        .hm-select-wrap .form-control {
            padding-right: 2.5rem;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: none !important;
        }

        .hm-filter-panel {
            padding: 1rem 1.1rem;
            border-radius: 16px;
            box-shadow: 0 12px 32px -24px rgba(111, 29, 1, 0.24);
            background: #fffaf6;
        }

        .hm-action-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .hm-service-card {
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 1rem;
            height: 100%;
            background: #fff;
        }

        .hm-pagination {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-top: 1.25rem;
        }

        .hm-pagination__controls,
        .hm-pagination__pages {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.45rem;
        }

        .hm-pagination__button {
            min-width: 42px;
            padding: 8px 12px;
            border-radius: 10px;
        }

        .hm-clickable-row {
            cursor: pointer;
        }

        .hm-clickable-row:hover > td {
            background: #fff7ed;
        }

        .hm-dialog-backdrop {
            position: fixed;
            inset: 0;
            z-index: 1080;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(2px);
        }

        .hm-dialog-backdrop.is-visible {
            display: flex;
        }

        .hm-dialog {
            width: min(460px, 100%);
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 28px 90px -32px rgba(15, 23, 42, 0.72);
            overflow: hidden;
        }

        .hm-dialog__body {
            display: grid;
            grid-template-columns: 46px 1fr;
            gap: 0.95rem;
            padding: 1.35rem 1.35rem 0.8rem;
        }

        .hm-dialog__icon {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: #fee2e2;
            color: #b91c1c;
        }

        .hm-dialog__icon svg {
            width: 22px;
            height: 22px;
            flex-shrink: 0;
        }

        .hm-dialog__title {
            margin: 0 0 0.35rem;
            font-size: 1.08rem;
            font-weight: 800;
            color: #0f172a;
        }

        .hm-dialog__message {
            margin: 0;
            color: #334155;
            font-size: 0.95rem;
            line-height: 1.45;
        }

        .hm-dialog__record {
            display: none;
            width: fit-content;
            max-width: 100%;
            margin-top: 0.75rem;
            padding: 0.35rem 0.65rem;
            border-radius: 999px;
            background: #f1f5f9;
            color: #334155;
            font-size: 0.85rem;
            font-weight: 700;
            word-break: break-word;
        }

        .hm-dialog__record:not(:empty) {
            display: inline-flex;
        }

        .hm-dialog__note {
            display: none;
            margin: 0.75rem 0 0;
            color: #64748b;
            font-size: 0.88rem;
            line-height: 1.4;
        }

        .hm-dialog__note:not(:empty) {
            display: block;
        }

        .hm-dialog__actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.6rem;
            padding: 0.9rem 1.35rem 1.25rem;
            background: #f8fafc;
        }

        .hm-dialog__actions .btn {
            min-width: 76px;
            border-radius: 7px;
            padding: 0.48rem 0.9rem;
            font-weight: 700;
        }

        @media (max-width: 420px) {
            .hm-dialog__body {
                grid-template-columns: 1fr;
            }

            .hm-dialog__actions {
                flex-direction: column-reverse;
            }

            .hm-dialog__actions .btn {
                width: 100%;
            }
        }

        .hm-notice-backdrop {
            position: fixed;
            inset: 0;
            z-index: 1090;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
            background: rgba(15, 23, 42, 0.34);
            backdrop-filter: blur(3px);
        }

        .hm-notice-backdrop.is-visible {
            display: flex;
        }

        .hm-notice {
            position: relative;
            width: min(360px, 100%);
            min-height: 195px;
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 16px;
            padding: 2.15rem 2rem 1.95rem;
            background: #fff;
            box-shadow: 0 30px 90px -28px rgba(15, 23, 42, 0.72);
            transform: translateY(8px) scale(0.98);
            animation: hm-notice-in 0.16s ease-out forwards;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .hm-notice::before {
            content: '';
            position: absolute;
            inset: 0 0 auto;
            height: 7px;
            background: #2563eb;
        }

        .hm-notice--success::before { background: #16a34a; }
        .hm-notice--danger::before { background: #dc2626; }
        .hm-notice--warning::before { background: #d97706; }

        .hm-notice__title {
            margin: 0 0 0.7rem;
            text-align: center;
            font-size: 1.3rem;
            font-weight: 700;
            color: #111827;
        }

        .hm-notice__message {
            margin: 0 auto;
            max-width: 460px;
            text-align: center;
            color: #4b5563;
            font-size: 1rem;
            line-height: 1.55;
        }

        @keyframes hm-notice-in {
            to {
                transform: translateY(0) scale(1);
            }
        }
    </style>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 pb-4">
                    <div class="header-title">
                        <h4 class="card-title mb-1">{{ $title }}</h4>
                        <p class="mb-0 text-muted">{{ $subtitle }}</p>
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        @isset($headerActions)
                            {{ $headerActions }}
                        @endisset

                        @if($showCreateButton && $createRoute)
                            <a href="{{ $createRoute }}" class="btn btn-primary btn-sm hm-create-button" style="padding: 10px;">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11 13.5C13.4853 13.5 15.5 11.4853 15.5 9C15.5 6.51472 13.4853 4.5 11 4.5C8.51472 4.5 6.5 6.51472 6.5 9C6.5 11.4853 8.51472 13.5 11 13.5Z" fill="currentColor" opacity="0.92"/>
                                    <path d="M3.5 19.5C3.5 16.7386 6.18629 14.5 9.5 14.5H12.5C14.163 14.5 15.6681 15.063 16.7518 15.9721C15.6497 16.5803 14.9048 17.7537 14.9048 19.0952V19.5H3.5Z" fill="currentColor" opacity="0.92"/>
                                    <path d="M18.5 14.5V22.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M14.5 18.5H22.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                Thêm mới
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <div class="hm-filter-panel">
                            <form>
                                <div class="row g-2 align-items-end">
                                    @isset($filters)
                                        {{ $filters }}
                                    @endisset

                                    <div class="col-md-auto">
                                        <div class="d-flex justify-content-start gap-2 w-100">
                                            <button type="button" class="btn btn-primary" style="padding: 10px 18px; white-space: nowrap;">Áp dụng</button>
                                            <button type="button" class="btn btn-light btn-sm" style="padding: 10px 18px; white-space: nowrap;">Đặt lại</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    @isset($beforeTable)
                        {{ $beforeTable }}
                    @endisset

                    <div class="table-responsive">
                        {{ $slot }}
                    </div>

                    <div class="hm-pagination" data-hm-pagination hidden>
                        <div class="hm-pagination__controls">
                            <button type="button" class="btn btn-light btn-sm hm-pagination__button" data-hm-pagination-prev>
                                Trước
                            </button>
                            <div class="hm-pagination__pages" data-hm-pagination-pages></div>
                            <button type="button" class="btn btn-light btn-sm hm-pagination__button" data-hm-pagination-next>
                                Sau
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="hm-dialog-backdrop" data-hm-confirm-dialog aria-hidden="true">
        <div class="hm-dialog" role="dialog" aria-modal="true" aria-labelledby="hm-confirm-title">
            <div class="hm-dialog__body">
                <div class="hm-dialog__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 9V13" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                        <path d="M12 17H12.01" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"></path>
                        <path d="M10.29 4.86L2.82 18C2.06 19.33 3.02 21 4.55 21H19.45C20.98 21 21.94 19.33 21.18 18L13.71 4.86C12.95 3.52 11.05 3.52 10.29 4.86Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"></path>
                    </svg>
                </div>
                <div>
                    <h5 class="hm-dialog__title" id="hm-confirm-title" data-hm-confirm-title>Xác nhận xóa</h5>
                    <p class="hm-dialog__message" data-hm-confirm-message></p>
                    <div class="hm-dialog__record" data-hm-confirm-record></div>
                    <p class="hm-dialog__note" data-hm-confirm-note></p>
                </div>
            </div>
            <div class="hm-dialog__actions">
                <button type="button" class="btn btn-light btn-sm" data-hm-confirm-cancel>Hủy</button>
                <button type="button" class="btn btn-danger btn-sm" data-hm-confirm-ok>Xóa</button>
            </div>
        </div>
    </div>

    <div class="hm-notice-backdrop" data-hm-notice-dialog aria-hidden="true">
        <div class="hm-notice" data-hm-notice-box role="status" aria-live="polite">
            <div class="hm-notice__title" data-hm-notice-title>Thông báo</div>
            <p class="hm-notice__message" data-hm-notice-message></p>
        </div>
    </div>

    @push('scripts')
        <script>
            if (typeof window.createHmPagination !== 'function') {
                window.createHmPagination = function (options) {
                    const settings = options || {};
                    const container = settings.container || null;
                    const pageSize = Number(settings.pageSize) > 0 ? Number(settings.pageSize) : 10;
                    const onPageChange = typeof settings.onPageChange === 'function'
                        ? settings.onPageChange
                        : function () {};

                    if (!container) {
                        return {
                            setItems: function (items) {
                                onPageChange(Array.isArray(items) ? items : [], {
                                    currentPage: 1,
                                    pageSize: pageSize,
                                    totalItems: Array.isArray(items) ? items.length : 0,
                                    totalPages: 1,
                                });
                            }
                        };
                    }

                    const pagesElement = container.querySelector('[data-hm-pagination-pages]');
                    const prevButton = container.querySelector('[data-hm-pagination-prev]');
                    const nextButton = container.querySelector('[data-hm-pagination-next]');

                    let items = [];
                    let currentPage = 1;

                    const getPageWindow = function (totalPages) {
                        const windowSize = 5;
                        const start = Math.max(1, currentPage - Math.floor(windowSize / 2));
                        const end = Math.min(totalPages, start + windowSize - 1);
                        const adjustedStart = Math.max(1, end - windowSize + 1);
                        const pages = [];

                        for (let page = adjustedStart; page <= end; page += 1) {
                            pages.push(page);
                        }

                        return pages;
                    };

                    const render = function () {
                        const totalItems = items.length;
                        const totalPages = Math.max(1, Math.ceil(totalItems / pageSize));

                        if (totalItems === 0) {
                            container.hidden = true;
                            if (pagesElement) {
                                pagesElement.innerHTML = '';
                            }
                            if (prevButton) {
                                prevButton.disabled = true;
                            }
                            if (nextButton) {
                                nextButton.disabled = true;
                            }
                            onPageChange([], {
                                currentPage: 1,
                                pageSize: pageSize,
                                totalItems: 0,
                                totalPages: 0,
                            });
                            return;
                        }

                        currentPage = Math.min(Math.max(currentPage, 1), totalPages);

                        const startIndex = (currentPage - 1) * pageSize;
                        const endIndex = Math.min(startIndex + pageSize, totalItems);
                        const pageItems = items.slice(startIndex, endIndex);

                        container.hidden = false;

                        if (prevButton) {
                            prevButton.disabled = currentPage === 1;
                        }

                        if (nextButton) {
                            nextButton.disabled = currentPage === totalPages;
                        }

                        if (pagesElement) {
                            pagesElement.innerHTML = getPageWindow(totalPages).map(function (page) {
                                const isActive = page === currentPage;
                                const buttonClass = isActive ? 'btn-primary' : 'btn-light';

                                return `
                                    <button
                                        type="button"
                                        class="btn btn-sm ${buttonClass} hm-pagination__button"
                                        data-hm-pagination-page="${page}"
                                    >
                                        ${page}
                                    </button>
                                `;
                            }).join('');

                            pagesElement.querySelectorAll('[data-hm-pagination-page]').forEach(function (button) {
                                button.addEventListener('click', function () {
                                    currentPage = Number(button.dataset.hmPaginationPage || 1);
                                    render();
                                });
                            });
                        }

                        onPageChange(pageItems, {
                            currentPage: currentPage,
                            pageSize: pageSize,
                            totalItems: totalItems,
                            totalPages: totalPages,
                        });
                    };

                    if (prevButton) {
                        prevButton.addEventListener('click', function () {
                            if (currentPage > 1) {
                                currentPage -= 1;
                                render();
                            }
                        });
                    }

                    if (nextButton) {
                        nextButton.addEventListener('click', function () {
                            const totalPages = Math.max(1, Math.ceil(items.length / pageSize));
                            if (currentPage < totalPages) {
                                currentPage += 1;
                                render();
                            }
                        });
                    }

                    return {
                        setItems: function (nextItems, paginationOptions) {
                            const resolvedOptions = paginationOptions || {};
                            items = Array.isArray(nextItems) ? nextItems : [];

                            if (resolvedOptions.resetPage !== false) {
                                currentPage = 1;
                            }

                            render();
                        }
                    };
                };
            }

            if (typeof window.hmShowToast !== 'function') {
                window.hmShowToast = function (options) {
                    const settings = options || {};
                    const dialog = document.querySelector('[data-hm-notice-dialog]');
                    const box = dialog ? dialog.querySelector('[data-hm-notice-box]') : null;
                    const title = dialog ? dialog.querySelector('[data-hm-notice-title]') : null;
                    const message = dialog ? dialog.querySelector('[data-hm-notice-message]') : null;

                    if (!dialog || !box || !title || !message) {
                        return;
                    }

                    const type = settings.type || 'success';
                    box.className = `hm-notice hm-notice--${type}`;
                    title.textContent = settings.title || 'Thông báo';
                    message.textContent = settings.message || '';
                    dialog.classList.add('is-visible');
                    dialog.setAttribute('aria-hidden', 'false');

                    if (window.hmNoticeTimer) {
                        window.clearTimeout(window.hmNoticeTimer);
                    }

                    window.hmNoticeTimer = window.setTimeout(function () {
                        dialog.classList.remove('is-visible');
                        dialog.setAttribute('aria-hidden', 'true');
                    }, Number(settings.duration || 1800));
                };
            }

            if (typeof window.hmConfirmDeletion !== 'function') {
                window.hmConfirmDeletion = function (options) {
                    const settings = options || {};
                    const dialog = document.querySelector('[data-hm-confirm-dialog]');
                    const title = dialog ? dialog.querySelector('[data-hm-confirm-title]') : null;
                    const message = dialog ? dialog.querySelector('[data-hm-confirm-message]') : null;
                    const record = dialog ? dialog.querySelector('[data-hm-confirm-record]') : null;
                    const note = dialog ? dialog.querySelector('[data-hm-confirm-note]') : null;
                    const okButton = dialog ? dialog.querySelector('[data-hm-confirm-ok]') : null;
                    const cancelButton = dialog ? dialog.querySelector('[data-hm-confirm-cancel]') : null;

                    if (!dialog || !okButton || !cancelButton) {
                        return Promise.resolve(false);
                    }

                    title.textContent = settings.title || 'Xác nhận xóa';
                    message.textContent = settings.message || 'Bạn có chắc chắn muốn xóa mục này?';
                    if (record) {
                        record.textContent = settings.recordLabel || '';
                    }
                    if (note) {
                        note.textContent = settings.note || '';
                    }
                    okButton.textContent = settings.confirmText || 'Xóa';
                    dialog.classList.add('is-visible');
                    dialog.setAttribute('aria-hidden', 'false');
                    okButton.focus();

                    return new Promise(function (resolve) {
                        const close = function (result) {
                            dialog.classList.remove('is-visible');
                            dialog.setAttribute('aria-hidden', 'true');
                            okButton.removeEventListener('click', onOk);
                            cancelButton.removeEventListener('click', onCancel);
                            dialog.removeEventListener('click', onBackdrop);
                            document.removeEventListener('keydown', onKeydown);
                            resolve(result);
                        };

                        const onOk = function () { close(true); };
                        const onCancel = function () { close(false); };
                        const onBackdrop = function (event) {
                            if (event.target === dialog) {
                                close(false);
                            }
                        };
                        const onKeydown = function (event) {
                            if (event.key === 'Escape') {
                                close(false);
                            }
                        };

                        okButton.addEventListener('click', onOk);
                        cancelButton.addEventListener('click', onCancel);
                        dialog.addEventListener('click', onBackdrop);
                        document.addEventListener('keydown', onKeydown);
                    });
                };
            }

            document.addEventListener('DOMContentLoaded', function () {
                document.addEventListener('click', function (event) {
                    const row = event.target && event.target.closest ? event.target.closest('[data-hm-row-link]') : null;

                    if (!row) {
                        return;
                    }

                    if (event.target.closest('a, button, input, select, textarea, form, label')) {
                        return;
                    }

                    const url = row.getAttribute('data-hm-row-link');
                    if (url) {
                        window.location.href = url;
                    }
                });

                document.addEventListener('keydown', function (event) {
                    const row = event.target && event.target.closest ? event.target.closest('[data-hm-row-link]') : null;

                    if (!row) {
                        return;
                    }

                    if (event.key !== 'Enter' && event.key !== ' ') {
                        return;
                    }

                    event.preventDefault();

                    const url = row.getAttribute('data-hm-row-link');
                    if (url) {
                        window.location.href = url;
                    }
                });

            });
        </script>
    @endpush
</x-app-layout>
