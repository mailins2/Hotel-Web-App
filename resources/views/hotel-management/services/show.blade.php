@php
    $typeMap = [
        1 => 'Dịch vụ ăn uống',
        2 => 'Dịch vụ phòng',
        3 => 'Dịch vụ giải trí',
    ];
    $image = $service->hinhs->first();
    $imageUrl = $image?->Url ?: 'https://placehold.co/800x800/f3f4f6/9ca3af?text=Service';
    $formatCurrency = fn ($amount) => is_numeric($amount) ? number_format((float) $amount, 0, ',', '.') . ' VNĐ' : '--';
@endphp

<x-hotel-management.show-page
    title="Chi tiết dịch vụ"
    subtitle="Thông tin chi tiết dịch vụ"
    :index-route="route('hotel.services.index')"
    :edit-route="route('hotel.services.edit', ['recordId' => $service->MaDV])"
>
    <div class="col-md-6 mb-4">
        <div class="border rounded p-3 h-100">
            <div class="text-muted small mb-1">Mã dịch vụ</div>
            <div class="fw-semibold">{{ $service->MaDV ?? '--' }}</div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="border rounded p-3 h-100">
            <div class="text-muted small mb-1">Tên dịch vụ</div>
            <div class="fw-semibold">{{ $service->TenDV ?? '--' }}</div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="border rounded p-3 h-100">
            <div class="text-muted small mb-1">Giá dịch vụ</div>
            <div class="fw-semibold">{{ $formatCurrency($service->GiaDV) }}</div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="border rounded p-3 h-100">
            <div class="text-muted small mb-1">Loại dịch vụ</div>
            <div class="fw-semibold">{{ $typeMap[(int) $service->LoaiDV] ?? 'Khác' }}</div>
        </div>
    </div>
    <div class="col-md-12 mb-4">
        <div class="border rounded p-3 h-100">
            <div class="text-muted small mb-3">Ảnh dịch vụ</div>
            <img
                src="{{ $imageUrl }}"
                alt="{{ $service->TenDV ? 'Ảnh dịch vụ ' . $service->TenDV : 'Ảnh dịch vụ' }}"
                class="rounded border bg-light d-block mx-auto"
                style="width: min(100%, 320px); aspect-ratio: 1 / 1; object-fit: cover; object-position: center;"
                onerror="this.onerror=null;this.src='https://placehold.co/800x800/f3f4f6/9ca3af?text=Service';"
            >
            <div class="small text-muted mt-3 text-break">{{ $image?->Url ?: 'Chưa có link ảnh dịch vụ.' }}</div>
        </div>
    </div>
</x-hotel-management.show-page>
