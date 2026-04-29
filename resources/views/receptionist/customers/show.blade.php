<x-receptionist.show-page
    title="Chi tiết khách hàng"
    subtitle="Trang xem nhanh thông tin khách hàng."
    :index-route="route('reception.customers.index')"
    :edit-route="route('reception.customers.edit', ['customerId' => request()->route('customerId') ?? 1])"
>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã khách hàng</div><div class="fw-semibold">1</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên khách hàng</div><div class="fw-semibold">Nguyễn Minh An</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày sinh</div><div class="fw-semibold">12/04/1998</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Giới tính</div><div class="fw-semibold">Nam</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số điện thoại</div><div class="fw-semibold">0901234567</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">CCCD</div><div class="fw-semibold">079204000111</div></div></div>
    <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Địa chỉ</div><div class="fw-semibold">12 Nguyễn Huệ, Quận 1, TP.HCM</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Điểm tích lũy</div><div class="fw-semibold">20</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Trạng thái</div><div class="fw-semibold">Hoạt động</div></div></div>
</x-receptionist.show-page>
