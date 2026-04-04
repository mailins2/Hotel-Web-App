<x-app-layout :assets="$assets ?? []">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 pb-4">
                    <div class="header-title">
                        <h4 class="card-title mb-1">{{ $module['title'] }}</h4>
                        <p class="mb-0 text-muted">{{ $module['description'] }}</p>
                    </div>
                    <a href="{{ route('hotel.' . $moduleKey . '.create') }}" class="btn btn-primary btn-sm" style="padding: 10px;">
                        Thêm {{ $module['singular'] }}
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
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
                                                if (in_array($column, ['GiaDV', 'SoTien'], true)) {
                                                    $value = number_format((float) $value, 0, ',', '.') . ' VNĐ';
                                                }
                                            @endphp
                                            <td>
                                                @if($column === 'TrangThai')
                                                    @php
                                                        $isActive = (string) $rawValue === '1';
                                                    @endphp
                                                    <span
                                                        class="d-inline-flex align-items-center fw-semibold px-2 py-1"
                                                        style="font-size: 0.76rem; min-width: 112px; border-radius: 8px; line-height: 1.2; background-color: {{ $isActive ? '#8f3f12' : '#e5e7eb' }}; color: {{ $isActive ? '#ffffff' : '#6b7280' }};"
                                                    >
                                                        <span
                                                            class="rounded-circle me-2"
                                                            style="width: 7px; height: 7px; background-color: {{ $isActive ? '#f8d7c3' : '#8b7b74' }};"
                                                        ></span>
                                                        {{ \Illuminate\Support\Str::upper($value) }}
                                                    </span>
                                                @elseif($column === 'TinhTrang')
                                                    @php
                                                        $roomStatuses = [
                                                            '0' => ['bg' => '#dcfce7', 'text' => '#166534', 'dot' => '#166534'],
                                                            '1' => ['bg' => '#fef3c7', 'text' => '#92400e', 'dot' => '#d97706'],
                                                            '2' => ['bg' => '#dbeafe', 'text' => '#1d4ed8', 'dot' => '#2563eb'],
                                                            '3' => ['bg' => '#f3e8ff', 'text' => '#7e22ce', 'dot' => '#9333ea'],
                                                        ];
                                                        $roomStatusStyle = $roomStatuses[(string) $rawValue] ?? ['bg' => '#e5e7eb', 'text' => '#6b7280', 'dot' => '#8b7b74'];
                                                    @endphp
                                                    <span
                                                        class="d-inline-flex align-items-center fw-semibold px-2 py-1"
                                                        style="font-size: 0.76rem; min-width: 118px; border-radius: 8px; line-height: 1.2; background-color: {{ $roomStatusStyle['bg'] }}; color: {{ $roomStatusStyle['text'] }};"
                                                    >
                                                        <span
                                                            class="rounded-circle me-2"
                                                            style="width: 7px; height: 7px; background-color: {{ $roomStatusStyle['dot'] }};"
                                                        ></span>
                                                        {{ \Illuminate\Support\Str::upper($value) }}
                                                    </span>
                                                @elseif($column === 'LoaiDV')
                                                    @php
                                                        $serviceTypes = [
                                                            '0' => ['bg' => '#ffedd5', 'text' => '#9a3412', 'dot' => '#ea580c'],
                                                            '1' => ['bg' => '#dbeafe', 'text' => '#1d4ed8', 'dot' => '#2563eb'],
                                                            '2' => ['bg' => '#dcfce7', 'text' => '#166534', 'dot' => '#16a34a'],
                                                            '3' => ['bg' => '#f3e8ff', 'text' => '#7e22ce', 'dot' => '#9333ea'],
                                                            '4' => ['bg' => '#fae8ff', 'text' => '#a21caf', 'dot' => '#c026d3'],
                                                            '5' => ['bg' => '#fef3c7', 'text' => '#92400e', 'dot' => '#d97706'],
                                                            '6' => ['bg' => '#cffafe', 'text' => '#155e75', 'dot' => '#0891b2'],
                                                            '7' => ['bg' => '#e0e7ff', 'text' => '#3730a3', 'dot' => '#4f46e5'],
                                                            '8' => ['bg' => '#fee2e2', 'text' => '#b91c1c', 'dot' => '#dc2626'],
                                                            '9' => ['bg' => '#ecfccb', 'text' => '#3f6212', 'dot' => '#65a30d'],
                                                        ];
                                                        $serviceTypeStyle = $serviceTypes[(string) $rawValue] ?? ['bg' => '#e5e7eb', 'text' => '#6b7280', 'dot' => '#8b7b74'];
                                                    @endphp
                                                    <span
                                                        class="d-inline-flex align-items-center fw-semibold px-2 py-1"
                                                        style="font-size: 0.76rem; min-width: 118px; border-radius: 8px; line-height: 1.2; background-color: {{ $serviceTypeStyle['bg'] }}; color: {{ $serviceTypeStyle['text'] }};"
                                                    >
                                                        <span
                                                            class="rounded-circle me-2"
                                                            style="width: 7px; height: 7px; background-color: {{ $serviceTypeStyle['dot'] }};"
                                                        ></span>
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
                                                    href="{{ route('hotel.' . $moduleKey . '.show', $record[$module['primary_key']]) }}"
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
                                                <a
                                                    href="{{ route('hotel.' . $moduleKey . '.edit', $record[$module['primary_key']]) }}"
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
                                                <form action="{{ route('hotel.' . $moduleKey . '.destroy', $record[$module['primary_key']]) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa {{ $module['singular'] }} này?')">
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
</x-app-layout>
