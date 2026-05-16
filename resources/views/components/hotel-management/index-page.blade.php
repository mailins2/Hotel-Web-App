@props([
    'title',
    'subtitle',
    'createRoute' => null,
    'showCreateButton' => true,
    'trashRoute' => null,
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
            font-weight: 700;
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

                        @if($trashRoute)
                            <a href="{{ $trashRoute }}" class="btn btn-light btn-sm hm-icon-button" title="Lịch sử xóa" aria-label="Lịch sử xóa">
                                <svg width="18" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 8V12L14.5 14.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C15.0481 3 17.7421 4.51684 19.3696 6.83739" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M21 4V8H17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                                Lịch sử xóa
                            </a>
                        @endif

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

                document.addEventListener('submit', function (event) {
                    if (!event.target || !event.target.matches('.js-confirm-delete')) {
                        return;
                    }

                    event.preventDefault();
                    window.confirm('Đây là giao diện tĩnh, chưa có thao tác xóa thật.');
                });
            });
        </script>
    @endpush
</x-app-layout>
