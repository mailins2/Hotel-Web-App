<x-hotel-management.show-page
    title="Chi tiết khuyến mãi"
    subtitle="Thông tin chi tiết khuyến mãi"
    :index-route="route('hotel.promotions.index')"
    :edit-route="route('hotel.promotions.edit', ['recordId' => request()->route('recordId') ?? 1])"
>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã khuyến mãi</div><div class="fw-semibold">1</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên chương trình</div><div class="fw-semibold">Summer Escape</div></div></div>
    <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mô tả</div><div class="fw-semibold">Giảm giá cho khách đặt phòng trong mùa hè.</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Điểm yêu cầu</div><div class="fw-semibold">50</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày bắt đầu</div><div class="fw-semibold">01/05/2026</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">% giảm giá</div><div class="fw-semibold">15%</div></div></div>
</x-hotel-management.show-page>
