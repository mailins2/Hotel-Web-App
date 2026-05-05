<x-hotel-management.show-page
    title="Chi tiết loại phòng"
    subtitle="Thông tin loại phòng"
    :index-route="route('hotel.room-types.index')"
    :edit-route="route('hotel.room-types.edit', ['recordId' => request()->route('recordId') ?? 1])"
>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã loại phòng</div><div class="fw-semibold">1</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên loại phòng</div><div class="fw-semibold">Deluxe</div></div></div>
    <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mô tả</div><div class="fw-semibold">Phòng tiêu chuẩn cao cấp, phù hợp cho khách đi công tác hoặc cặp đôi.</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Người lớn</div><div class="fw-semibold">2</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Trẻ em</div><div class="fw-semibold">0</div></div></div>
    <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ảnh dịch vụ</div><div class="fw-semibold">Link ảnh</div></div></div>
</x-hotel-management.show-page>
