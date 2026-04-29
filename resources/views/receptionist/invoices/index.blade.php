<x-receptionist.index-page
    title="Quản lý hóa đơn"
    subtitle="Thông tin danh sách hóa đơn"
>
    <x-slot:stats>
        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="rd-card"><div class="small text-uppercase text-muted fw-bold">Chưa thanh toán</div><div class="h4 mb-0 mt-2">07</div></div></div>
            <div class="col-md-4"><div class="rd-card"><div class="small text-uppercase text-muted fw-bold">Đã thanh toán</div><div class="h4 mb-0 mt-2">15</div></div></div>
            <div class="col-md-4"><div class="rd-card"><div class="small text-uppercase text-muted fw-bold">Công nợ còn lại</div><div class="h4 mb-0 mt-2">6.900.000 VNĐ</div></div></div>
        </div>
    </x-slot:stats>

    <table class="table align-middle">
        <thead><tr><th>Mã HĐ</th><th>Mã đặt phòng</th><th>Ngày lập</th><th>Nhân viên</th><th>Tổng tiền</th><th>Đã thanh toán</th><th>Còn lại</th><th>Trạng thái</th></tr></thead>
        <tbody>
            <tr><td>5001</td><td>9001</td><td>08/04/2026</td><td>Phạm Thùy Linh</td><td>4.500.000 VNĐ</td><td>1.500.000 VNĐ</td><td>3.000.000 VNĐ</td><td><span class="rd-badge rd-badge--warning">Chưa thanh toán</span></td></tr>
            <tr><td>5002</td><td>9002</td><td>07/04/2026</td><td>Hoàng Gia Bảo</td><td>3.250.000 VNĐ</td><td>3.250.000 VNĐ</td><td>0 VNĐ</td><td><span class="rd-badge rd-badge--success">Đã thanh toán</span></td></tr>
        </tbody>
    </table>
</x-receptionist.index-page>
