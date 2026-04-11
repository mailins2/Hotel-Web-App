<x-app-layout :assets="$assets ?? []">
    @php
        $editUrl = $editUrl ?? route('hotel.modules.edit', ['moduleKey' => $moduleKey, 'recordId' => $record[$module['primary_key']]]);
        $backUrl = $backUrl ?? route('hotel.modules.index', ['moduleKey' => $moduleKey]);
    @endphp
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 pb-4">
                    <div class="header-title">
                        <h4 class="card-title mb-1">Chi tiết {{ $module['singular'] }}</h4>
                        <p class="mb-0 text-muted">{{ $module['title'] }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        @if($module['allow_edit'] ?? true)
                            <a href="{{ $editUrl }}" class="btn btn-sm btn-warning" style="padding: 10px;">Chỉnh sửa</a>
                        @endif
                        <a href="{{ $backUrl }}" class="btn btn-sm btn-primary" style="padding: 10px;">Quay lại</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($module['fields'] as $fieldKey => $field)
                            @php
                                $value = $record[$fieldKey] ?? '';
                                if (($field['type'] ?? null) === 'select') {
                                    $value = $field['options'][$value] ?? $value;
                                }
                                if (in_array($fieldKey, ['GiaDV', 'SoTien', 'TongTien', 'DaThanhToan', 'DonGia', 'GiaPhong', 'TienDenBu'], true)) {
                                    $value = number_format((float) $value, 0, ',', '.') . ' VNĐ';
                                }
                            @endphp
                            <div class="col-md-6 mb-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small mb-1">{{ $field['label'] }}</div>
                                    @if($moduleKey === 'services' && $fieldKey === 'ServiceImage' && $value !== '')
                                        @php
                                            $imagePreviewUrl = \Illuminate\Support\Str::startsWith($value, ['http://', 'https://'])
                                                ? $value
                                                : asset(ltrim($value, '/'));
                                        @endphp
                                        <img
                                            src="{{ $imagePreviewUrl }}"
                                            alt="Ảnh dịch vụ"
                                            class="img-fluid rounded border mb-3"
                                            style="max-height: 220px; object-fit: cover;"
                                        >
                                        <div class="fw-semibold">{{ $value }}</div>
                                    @else
                                        <div class="fw-semibold">{{ $value !== '' ? $value : 'Không có dữ liệu' }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
