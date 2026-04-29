@props([
    'title',
    'subtitle',
    'createRoute' => null,
    'createLabel' => null,
    'tableTitle' => 'Bảng dữ liệu',
])

<x-app-layout :assets="['animation']">
    <style>
        .rd-shell { padding-top: 4.5rem; }

        .rd-panel,
        .rd-table-card {
            border: 1px solid rgba(217, 119, 6, 0.14);
            border-radius: 28px;
            background: #fffdfa;
            box-shadow: 0 24px 60px rgba(148, 82, 24, 0.08);
        }

        .rd-panel { padding: 1.75rem; margin-bottom: 1.5rem; }

        .rd-card {
            border: 1px solid rgba(217, 119, 6, 0.14);
            border-radius: 22px;
            padding: 1.25rem;
            background: #fff;
            text-align: center;
        }

        .rd-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.38rem 0.78rem;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 700;
        }

        .rd-badge--success { background: #dcfce7; color: #166534; }
        .rd-badge--warning { background: #fef3c7; color: #9a3412; }
        .rd-badge--danger { background: #fee2e2; color: #b91c1c; }
        .rd-badge--muted { background: #eceff3; color: #475569; }

        .rd-create-button {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
        }

        .rd-create-button svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .rd-select-wrap {
            position: relative;
        }

        .rd-select-wrap::after {
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

        .rd-select-wrap .form-select,
        .rd-select-wrap .form-control {
            padding-right: 2.5rem;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: none !important;
        }

        .rd-filter-panel {
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
    </style>

    <div class="rd-shell">
        <div class="rd-panel">
            <h2 class="mb-2">{{ $title }}</h2>
            <p class="text-muted mb-0">{{ $subtitle }}</p>
        </div>

        @isset($stats)
            {{ $stats }}
        @endisset

        <div class="rd-table-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 p-4 pb-0">
                <div>
                    <h4 class="mb-1">{{ $tableTitle }}</h4>
                </div>

                @if($createRoute && $createLabel)
                    <a href="{{ $createRoute }}" class="btn btn-primary btn-sm rd-create-button" style="padding: 10px 14px;">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11 13.5C13.4853 13.5 15.5 11.4853 15.5 9C15.5 6.51472 13.4853 4.5 11 4.5C8.51472 4.5 6.5 6.51472 6.5 9C6.5 11.4853 8.51472 13.5 11 13.5Z" fill="currentColor" opacity="0.92"/>
                            <path d="M3.5 19.5C3.5 16.7386 6.18629 14.5 9.5 14.5H12.5C14.163 14.5 15.6681 15.063 16.7518 15.9721C15.6497 16.5803 14.9048 17.7537 14.9048 19.0952V19.5H3.5Z" fill="currentColor" opacity="0.92"/>
                            <path d="M18.5 14.5V22.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M14.5 18.5H22.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        {{ $createLabel }}
                    </a>
                @endif
            </div>

            <div class="p-4">
                @isset($filters)
                    <div class="mb-4">
                        <div class="rd-filter-panel">
                            <form>
                                <div class="row g-2 align-items-end">
                                    {{ $filters }}
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
                @endisset

                <div class="table-responsive">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
