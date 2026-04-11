<x-app-layout :assets="$assets ?? []">
    <style>
        .hm-badge {
            display: inline-flex;
            align-items: center;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            font-size: 0.76rem;
            min-width: 112px;
            border-radius: 8px;
            line-height: 1.2;
        }

        .hm-badge--wide {
            min-width: 118px;
        }

        .hm-dot {
            width: 7px;
            height: 7px;
            display: inline-block;
            border-radius: 9999px;
            margin-right: 0.5rem;
            flex-shrink: 0;
        }

        .hm-badge--active {
            background-color: #8f3f12;
            color: #ffffff;
        }

        .hm-dot--active {
            background-color: #f8d7c3;
        }

        .hm-badge--inactive {
            background-color: #e5e7eb;
            color: #6b7280;
        }

        .hm-dot--inactive {
            background-color: #8b7b74;
        }

        .hm-badge--room-empty {
            background-color: #dcfce7;
            color: #166534;
        }

        .hm-dot--room-empty {
            background-color: #166534;
        }

        .hm-badge--room-booked {
            background-color: #fef3c7;
            color: #92400e;
        }

        .hm-dot--room-booked {
            background-color: #d97706;
        }

        .hm-badge--room-using {
            background-color: #dbeafe;
            color: #1d4ed8;
        }

        .hm-dot--room-using {
            background-color: #2563eb;
        }

        .hm-badge--room-cleaning {
            background-color: #f3e8ff;
            color: #7e22ce;
        }

        .hm-dot--room-cleaning {
            background-color: #9333ea;
        }

        .hm-badge--service-food {
            background-color: #ffedd5;
            color: #9a3412;
        }

        .hm-dot--service-food {
            background-color: #ea580c;
        }

        .hm-badge--service-room {
            background-color: #dbeafe;
            color: #1d4ed8;
        }

        .hm-dot--service-room {
            background-color: #2563eb;
        }

        .hm-badge--service-entertainment {
            background-color: #ecfccb;
            color: #3f6212;
        }

        .hm-dot--service-entertainment {
            background-color: #65a30d;
        }

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
    </style>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 pb-4">
                    <div class="header-title">
                        <h4 class="card-title mb-1">{{ $module['title'] }}</h4>
                        <p class="mb-0 text-muted">{{ $module['description'] }}</p>
                    </div>
                    @if($module['allow_create'] ?? true)
                        <a href="{{ route('hotel.modules.create', ['moduleKey' => $moduleKey]) }}" class="btn btn-primary btn-sm hm-create-button" style="padding: 10px;">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11 13.5C13.4853 13.5 15.5 11.4853 15.5 9C15.5 6.51472 13.4853 4.5 11 4.5C8.51472 4.5 6.5 6.51472 6.5 9C6.5 11.4853 8.51472 13.5 11 13.5Z" fill="currentColor" opacity="0.92"/>
                                <path d="M3.5 19.5C3.5 16.7386 6.18629 14.5 9.5 14.5H12.5C14.163 14.5 15.6681 15.063 16.7518 15.9721C15.6497 16.5803 14.9048 17.7537 14.9048 19.0952V19.5H3.5Z" fill="currentColor" opacity="0.92"/>
                                <path d="M18.5 14.5V22.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M14.5 18.5H22.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            Thêm {{ $module['singular'] }}
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(!empty($module['filters']))
                        <div class="mb-4">
                            <form method="GET" action="{{ route('hotel.modules.index', ['moduleKey' => $moduleKey]) }}">
                                <div class="row g-2 align-items-end">
                                    @foreach($module['filters'] as $filterKey => $filter)
                                        @php
                                            $filterValue = request()->query($filterKey, '');
                                        @endphp
                                        <div class="col-md-3">
                                            <label class="form-label" for="{{ $filterKey }}">{{ $filter['label'] }}</label>
                                            @if(($filter['type'] ?? 'text') === 'select')
                                                <select class="form-control" id="{{ $filterKey }}" name="{{ $filterKey }}">
                                                    @foreach($filter['options'] ?? [] as $optionValue => $optionLabel)
                                                        <option value="{{ $optionValue }}" {{ (string) $filterValue === (string) $optionValue ? 'selected' : '' }}>
                                                            {{ $optionLabel }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input
                                                    type="{{ $filter['type'] ?? 'text' }}"
                                                    class="form-control"
                                                    id="{{ $filterKey }}"
                                                    name="{{ $filterKey }}"
                                                    value="{{ $filterValue }}"
                                                >
                                            @endif
                                        </div>
                                    @endforeach
                                    <div class="col-md-auto">
                                        <div class="d-flex justify-content-start gap-2 w-100">
                                            <button type="submit" class="btn btn-primary" style="padding: 10px 18px; white-space: nowrap;">Áp dụng</button>
                                            <a href="{{ route('hotel.modules.index', ['moduleKey' => $moduleKey]) }}" class="btn btn-light btn-sm" style="padding: 10px 18px; white-space: nowrap;">Đặt lại</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif

                    @if($moduleKey === 'services')
                        @php
                            $serviceCategories = $module['service_categories'] ?? [];
                            $activeServiceCategory = request()->query('service_category', 'all');
                            $serviceRecordsByItemKey = [];
                            foreach ($records as $serviceRecord) {
                                $serviceItemKey = $serviceRecord['ServiceItemKey'] ?? null;
                                if (is_string($serviceItemKey) && $serviceItemKey !== '') {
                                    $serviceRecordsByItemKey[$serviceItemKey] = $serviceRecord;
                                }
                            }
                            $isAllServiceCategories = $activeServiceCategory === 'all';
                        @endphp

                        <div class="mb-4">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <a
                                    href="{{ route('hotel.modules.index', array_merge(['moduleKey' => $moduleKey], request()->except('service_category'))) }}"
                                    class="btn btn-sm {{ $isAllServiceCategories ? 'btn-primary' : 'btn-light' }}"
                                    style="padding: 8px 14px;"
                                >
                                    Tất cả dịch vụ
                                </a>
                                @foreach($serviceCategories as $serviceCategory)
                                    @php
                                        $serviceCategoryKey = $serviceCategory['key'] ?? '';
                                        $isActiveServiceCategory = $activeServiceCategory === $serviceCategoryKey;
                                    @endphp
                                    <a
                                        href="{{ route('hotel.modules.index', array_merge(['moduleKey' => $moduleKey, 'service_category' => $serviceCategoryKey], request()->except('service_category'))) }}"
                                        class="btn btn-sm {{ $isActiveServiceCategory ? 'btn-primary' : 'btn-light' }}"
                                        style="padding: 8px 14px;"
                                    >
                                        {{ $serviceCategory['label'] ?? $serviceCategoryKey }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            @foreach($serviceCategories as $serviceCategory)
                                @php
                                    $serviceCategoryKey = $serviceCategory['key'] ?? '';
                                @endphp
                                @if(!$isAllServiceCategories && $serviceCategoryKey !== $activeServiceCategory)
                                    @continue
                                @endif
                                <div class="col-12">
                                    <h6 class="fw-semibold mb-1">{{ $serviceCategory['label'] ?? 'Nhóm dịch vụ' }}</h6>
                                </div>
                                @foreach(($serviceCategory['items'] ?? []) as $serviceItemKey => $serviceItem)
                                    @php
                                        $serviceRecord = $serviceRecordsByItemKey[$serviceItemKey] ?? null;
                                        $serviceName = $serviceItem['label'] ?? $serviceItemKey;
                                        $servicePrice = is_array($serviceRecord) ? ($serviceRecord['GiaDV'] ?? null) : null;
                                        $serviceImageRaw = is_array($serviceRecord) ? ($serviceRecord['ServiceImage'] ?? null) : null;
                                        $serviceImageUrl = null;

                                        if (is_string($serviceImageRaw) && trim($serviceImageRaw) !== '') {
                                            $serviceImageRaw = trim($serviceImageRaw);
                                            $serviceImageUrl = \Illuminate\Support\Str::startsWith($serviceImageRaw, ['http://', 'https://'])
                                                ? $serviceImageRaw
                                                : asset(ltrim($serviceImageRaw, '/'));
                                        }
                                    @endphp
                                    <div class="col-md-6 col-xl-4">
                                        <div class="border rounded-3 p-3 h-100" style="border-color: #e5e7eb !important;">
                                            <div class="fw-semibold">{{ $serviceName }}</div>
                                            <div class="small text-muted mt-1">
                                                @if($servicePrice !== null && $servicePrice !== '')
                                                    {{ number_format((float) $servicePrice, 0, ',', '.') }} VNĐ
                                                @else
                                                    Chưa cập nhật giá dịch vụ
                                                @endif
                                            </div>
                                            @if($serviceItemKey === 'giat-ui')
                                                <div class="mt-3">
                                                    @if($serviceImageUrl)
                                                        <img
                                                            src="{{ $serviceImageUrl }}"
                                                            alt="Hình ảnh dịch vụ giặt ủi"
                                                            class="img-fluid rounded"
                                                            style="width: 100%; height: 160px; object-fit: cover;"
                                                        >
                                                    @else
                                                        <div class="d-flex align-items-center justify-content-center rounded border bg-light text-muted" style="height: 160px; border-style: dashed !important;">
                                                            Chưa có hình ảnh từ bảng Hinh
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    @foreach($module['list_columns'] as $column)
                                        <th>{{ $module['fields'][$column]['label'] ?? $column }}</th>
                                    @endforeach
                                    <th style="min-width: 180px;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $record)
                                    <tr>
                                        @foreach($module['list_columns'] as $column)
                                            @php
                                                $field = $module['fields'][$column] ?? null;
                                                $rawValue = $record[$column] ?? '';
                                                $value = $rawValue;
                                                if (($field['type'] ?? null) === 'select') {
                                                    $value = $field['options'][$value] ?? $value;
                                                }
                                                if (in_array($column, ['GiaDV', 'SoTien', 'TongTien', 'DaThanhToan', 'DonGia', 'GiaPhong', 'TienDenBu'], true)) {
                                                    $value = number_format((float) $value, 0, ',', '.') . ' VNĐ';
                                                }
                                            @endphp
                                            <td>
                                                @if($column === 'TrangThai')
                                                    @php
                                                        $isActive = (string) $rawValue === '1';
                                                        $statusVariant = $isActive
                                                            ? ['badge' => 'hm-badge hm-badge--active', 'dot' => 'hm-dot hm-dot--active']
                                                            : ['badge' => 'hm-badge hm-badge--inactive', 'dot' => 'hm-dot hm-dot--inactive'];
                                                    @endphp
                                                    <span class="{{ $statusVariant['badge'] }}">
                                                        <span class="{{ $statusVariant['dot'] }}"></span>
                                                        {{ \Illuminate\Support\Str::upper($value) }}
                                                    </span>
                                                @elseif($column === 'TinhTrang')
                                                    @php
                                                        $roomStatuses = [
                                                            '0' => ['badge' => 'hm-badge hm-badge--wide hm-badge--room-empty', 'dot' => 'hm-dot hm-dot--room-empty'],
                                                            '1' => ['badge' => 'hm-badge hm-badge--wide hm-badge--room-booked', 'dot' => 'hm-dot hm-dot--room-booked'],
                                                            '2' => ['badge' => 'hm-badge hm-badge--wide hm-badge--room-using', 'dot' => 'hm-dot hm-dot--room-using'],
                                                            '3' => ['badge' => 'hm-badge hm-badge--wide hm-badge--room-cleaning', 'dot' => 'hm-dot hm-dot--room-cleaning'],
                                                        ];
                                                        $roomStatusVariant = $roomStatuses[(string) $rawValue] ?? ['badge' => 'hm-badge hm-badge--wide hm-badge--inactive', 'dot' => 'hm-dot hm-dot--inactive'];
                                                    @endphp
                                                    <span class="{{ $roomStatusVariant['badge'] }}">
                                                        <span class="{{ $roomStatusVariant['dot'] }}"></span>
                                                        {{ \Illuminate\Support\Str::upper($value) }}
                                                    </span>
                                                @elseif($column === 'LoaiDV')
                                                    @php
                                                        $serviceTypes = [
                                                            '0' => ['badge' => 'hm-badge hm-badge--wide hm-badge--service-food', 'dot' => 'hm-dot hm-dot--service-food'],
                                                            '1' => ['badge' => 'hm-badge hm-badge--wide hm-badge--service-room', 'dot' => 'hm-dot hm-dot--service-room'],
                                                            '2' => ['badge' => 'hm-badge hm-badge--wide hm-badge--service-entertainment', 'dot' => 'hm-dot hm-dot--service-entertainment'],
                                                        ];
                                                        $serviceTypeVariant = $serviceTypes[(string) $rawValue] ?? ['badge' => 'hm-badge hm-badge--wide hm-badge--inactive', 'dot' => 'hm-dot hm-dot--inactive'];
                                                    @endphp
                                                    <span class="{{ $serviceTypeVariant['badge'] }}">
                                                        <span class="{{ $serviceTypeVariant['dot'] }}"></span>
                                                        {{ \Illuminate\Support\Str::upper($value) }}
                                                    </span>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                        @endforeach
                                        <td>
                                            <div class="d-flex flex-wrap gap-2">
                                                <a
                                                    href="{{ route('hotel.modules.show', ['moduleKey' => $moduleKey, 'recordId' => $record[$module['primary_key']]]) }}"
                                                    class="btn btn-sm btn-icon text-white"
                                                    style="background-color: #22c55e; border-color: #22c55e;"
                                                    title="Xem chi tiết"
                                                >
                                                    <span class="btn-inner">
                                                        <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M2 12C3.73 8.11 7.52 5.5 12 5.5C16.48 5.5 20.27 8.11 22 12C20.27 15.89 16.48 18.5 12 18.5C7.52 18.5 3.73 15.89 2 12Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                            <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                                @if($module['allow_edit'] ?? true)
                                                    <a
                                                        href="{{ route('hotel.modules.edit', ['moduleKey' => $moduleKey, 'recordId' => $record[$module['primary_key']]]) }}"
                                                        class="btn btn-sm btn-warning btn-icon"
                                                        title="Chỉnh sửa"
                                                    >
                                                        <span class="btn-inner">
                                                            <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M13.7476 20H21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                <path d="M16.8392 3.41187C17.6212 2.62988 18.8891 2.62988 19.6711 3.41187L20.5881 4.32887C21.3701 5.11087 21.3701 6.37875 20.5881 7.16075L8.14912 19.5998C7.65512 20.0938 7.04312 20.4538 6.37112 20.6478L3 21L3.352 17.6289C3.546 16.9569 3.906 16.3448 4.4 15.8508L16.8392 3.41187Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                            </svg>
                                                        </span>
                                                    </a>
                                                @endif
                                                @if($module['allow_delete'] ?? true)
                                                    <form action="{{ route('hotel.modules.destroy', ['moduleKey' => $moduleKey, 'recordId' => $record[$module['primary_key']]]) }}" method="POST" class="js-confirm-delete" data-confirm-message="{{ 'Bạn có chắc muốn xóa ' . $module['singular'] . ' này?' }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger btn-icon" title="Xóa">
                                                            <span class="btn-inner">
                                                                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M19 7L18.132 18.142C18.0578 19.0948 17.2636 19.8333 16.308 19.8333H7.692C6.73635 19.8333 5.9422 19.0948 5.868 18.142L5 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                    <path d="M4 7H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                                                                    <path d="M9 7V4.8C9 4.35817 9.35817 4 9.8 4H14.2C14.6418 4 15 4.35817 15 4.8V7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                    <path d="M10 11V16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                                                                    <path d="M14 11V16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                                                                </svg>
                                                            </span>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($module['list_columns']) + 1 }}" class="text-center text-muted py-4">
                                            Chưa có dữ liệu để hiển thị.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
                        const confirmMessage = formElement.dataset.confirmMessage || 'Bạn có chắc muốn xóa mục này?';

                        if (!window.confirm(confirmMessage)) {
                            event.preventDefault();
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
