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
    </style>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 pb-4">
                    <div class="header-title">
                        <h4 class="card-title mb-1">{{ $title }}</h4>
                        <p class="mb-0 text-muted">{{ $subtitle }}</p>
                    </div>

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
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.js-confirm-delete').forEach(function (formElement) {
                    formElement.addEventListener('submit', function (event) {
                        event.preventDefault();
                        window.confirm('Đây là giao diện tĩnh, chưa có thao tác xóa thật.');
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
