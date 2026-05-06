<x-receptionist.show-page
    title="Chi tiết sử dụng dịch vụ"
    subtitle="Thông tin chi tiết dịch vụ khách đang sử dụng."
    :index-route="route('reception.services.index')"
>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã sử dụng</div><div class="fw-semibold">{{ request()->route('serviceUsageId') ?? 'SD001' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã đặt phòng</div><div class="fw-semibold">9002</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên khách hàng</div><div class="fw-semibold">Trần Bảo Ngọc</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã dịch vụ</div><div class="fw-semibold">DV010</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên dịch vụ</div><div class="fw-semibold">Mini bar</div></div></div>
     <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Giá dịch vụ</div><div class="fw-semibold">120.000 VNĐ</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Loại dịch vụ</div><div class="fw-semibold">Dịch vụ ăn uống</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số lượng</div><div class="fw-semibold">2</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Thời gian</div><div class="fw-semibold">07/04/2026 18:20</div></div></div>
</x-receptionist.show-page>
