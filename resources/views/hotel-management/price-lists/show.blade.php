<x-hotel-management.show-page
    title="Chi tiết bảng giá"
    subtitle="Thông tin chi tiết bảng giá"
    :index-route="route('hotel.price-lists.index')"
    :edit-route="route('hotel.price-lists.edit', ['recordId' => request()->route('recordId') ?? 1])"
>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã loại phòng</div><div class="fw-semibold">1</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mùa</div><div class="fw-semibold">Mùa 1</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên loại phòng</div><div class="fw-semibold">Phòng Standard</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Giá phòng</div><div class="fw-semibold">300.000 VNĐ</div></div></div>
</x-hotel-management.show-page>
