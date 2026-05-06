<x-receptionist.show-page
    title="Chi tiết thanh toán"
    subtitle="Thông tin chi tiết thanh toán"
    :index-route="route('reception.payments.index')"
>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã thanh toán</div><div class="fw-semibold">{{ request()->route('paymentId') ?? 1 }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã đặt phòng</div><div class="fw-semibold">9001</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Người thanh toán</div><div class="fw-semibold">Nguyễn Minh An</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số tiền</div><div class="fw-semibold">1.500.000 VNĐ</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Loại thanh toán</div><div class="fw-semibold">Đặt cọc</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày thanh toán</div><div class="fw-semibold">08/04/2026 10:30</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Trạng thái hóa đơn</div><div class="fw-semibold">Chưa thanh toán</div></div></div>
</x-receptionist.show-page>
