<x-receptionist.show-page
    title="Chi tiết hóa đơn"
    subtitle="Trang xem nhanh thông tin hóa đơn."
    :index-route="route('reception.invoices.index')"
    :edit-route="route('reception.invoices.edit', ['invoiceId' => request()->route('invoiceId') ?? 5001])"
>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã hóa đơn</div><div class="fw-semibold">{{ request()->route('invoiceId') ?? 5001 }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã đặt phòng</div><div class="fw-semibold">9001</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Khách hàng</div><div class="fw-semibold">Nguyễn Minh An</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày lập</div><div class="fw-semibold">08/04/2026</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Nhân viên</div><div class="fw-semibold">Phạm Thùy Linh</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tổng tiền</div><div class="fw-semibold">4.500.000 VNĐ</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Đã thanh toán</div><div class="fw-semibold">1.500.000 VNĐ</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Còn lại</div><div class="fw-semibold">3.000.000 VNĐ</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Trạng thái</div><div class="fw-semibold">Chưa thanh toán</div></div></div>
</x-receptionist.show-page>
