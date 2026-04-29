<x-hotel-management.show-page
    title="Chi tiết phòng"
    subtitle="Thông tin chi tiết phòng"
    :index-route="route('hotel.rooms.index')"
    :edit-route="route('hotel.rooms.edit', ['recordId' => request()->route('recordId') ?? 1])"
>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã phòng</div><div class="fw-semibold">1</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số phòng</div><div class="fw-semibold">A101</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Loại phòng</div><div class="fw-semibold">Deluxe</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tình trạng</div><div class="fw-semibold">Trống</div></div></div>
    <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mô tả</div><div class="fw-semibold">Hướng sân vườn yên tĩnh.</div></div></div>
</x-hotel-management.show-page>
