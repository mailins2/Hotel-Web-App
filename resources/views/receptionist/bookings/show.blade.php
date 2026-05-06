<x-receptionist.show-page
    title="Chi tiết đặt phòng"
    subtitle="Thông tin chi tiết đặt phòng"
    :index-route="route('reception.bookings.index')"
    :edit-route="route('reception.bookings.edit', ['bookingId' => request()->route('bookingId') ?? 1])"
>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã đặt phòng</div><div class="fw-semibold">9001</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã khách hàng</div><div class="fw-semibold">1</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên khách hàng</div><div class="fw-semibold">Nguyễn Minh An</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày nhận phòng</div><div class="fw-semibold">08/04/2026</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày trả phòng</div><div class="fw-semibold">10/04/2026</div></div></div>
    <!-- <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số phòng</div><div class="fw-semibold">A101</div></div></div> -->
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Loại phòng</div><div class="fw-semibold">Deluxe</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số lượng người ở</div><div class="fw-semibold">2</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tình trạng</div><div class="fw-semibold">Đã đặt</div></div></div>
</x-receptionist.show-page>
