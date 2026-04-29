<x-hotel-management.show-page
    title="Chi tiết dịch vụ"
    subtitle="Thông tin chi tiết dịch vụ"
    :index-route="route('hotel.services.index')"
    :edit-route="route('hotel.services.edit', ['recordId' => request()->route('recordId') ?? 1])"
>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã dịch vụ</div><div class="fw-semibold">6</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên dịch vụ</div><div class="fw-semibold">Giặt ủi</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Giá dịch vụ</div><div class="fw-semibold">120.000 VNĐ</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Loại dịch vụ</div><div class="fw-semibold">Dịch vụ phòng</div></div></div>
    <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ảnh dịch vụ</div><div class="fw-semibold">Link ảnh</div></div></div>
</x-hotel-management.show-page>
