<x-app-layout :assets="['animation']">
    <style>
        .rd-shell { padding-top: 4.5rem; }
        .rd-panel, .rd-table-card {
            border: 1px solid rgba(217, 119, 6, 0.14);
            border-radius: 28px;
            background: #fffdfa;
            box-shadow: 0 24px 60px rgba(148, 82, 24, 0.08);
        }
        .rd-panel { padding: 1.75rem; margin-bottom: 1.5rem; }
        .rd-card { border: 1px solid rgba(217, 119, 6, 0.14); border-radius: 22px; padding: 1.25rem; background: #fff; text-align: center; }
        .rd-badge { display: inline-flex; align-items: center; padding: 0.38rem 0.78rem; border-radius: 999px; font-size: 0.76rem; font-weight: 700; }
        .rd-badge--success { background: #dcfce7; color: #166534; }
        .rd-badge--warning { background: #fef3c7; color: #9a3412; }
        .rd-badge--danger { background: #fee2e2; color: #b91c1c; }
        .rd-badge--muted { background: #eceff3; color: #475569; }
    </style>

    <div class="rd-shell">
        <div class="rd-panel">
            @if(request()->routeIs('reception.customers.index'))
                <h2 class="mb-2">Quản lý khách hàng</h2>
                <p class="text-muted mb-0">Danh sách tĩnh để giữ bố cục trang lễ tân.</p>
            @elseif(request()->routeIs('reception.bookings.index'))
                <h2 class="mb-2">Quản lý đặt phòng</h2>
                <p class="text-muted mb-0">Bảng booking đang ở chế độ HTML tĩnh.</p>
            @else
                <h2 class="mb-2">Quản lý hóa đơn</h2>
                <p class="text-muted mb-0">Thông tin công nợ và thanh toán đang hiển thị tĩnh.</p>
            @endif
        </div>

        @if(request()->routeIs('reception.customers.index'))
            <div class="row g-3 mb-4">
                <div class="col-md-4"><div class="rd-card"><div class="small text-uppercase text-muted fw-bold">Khách đang hoạt động</div><div class="h4 mb-0 mt-2">24</div></div></div>
                <div class="col-md-4"><div class="rd-card"><div class="small text-uppercase text-muted fw-bold">Khách VIP</div><div class="h4 mb-0 mt-2">08</div></div></div>
                <div class="col-md-4"><div class="rd-card"><div class="small text-uppercase text-muted fw-bold">Cần liên hệ lại</div><div class="h4 mb-0 mt-2">03</div></div></div>
            </div>
        @elseif(request()->routeIs('reception.bookings.index'))
            <div class="row g-3 mb-4">
                <div class="col-md-4"><div class="rd-card"><div class="small text-uppercase text-muted fw-bold">Đã đặt</div><div class="h4 mb-0 mt-2">14</div></div></div>
                <div class="col-md-4"><div class="rd-card"><div class="small text-uppercase text-muted fw-bold">Đang sử dụng</div><div class="h4 mb-0 mt-2">09</div></div></div>
                <div class="col-md-4"><div class="rd-card"><div class="small text-uppercase text-muted fw-bold">Đã trả phòng</div><div class="h4 mb-0 mt-2">05</div></div></div>
            </div>
        @else
            <div class="row g-3 mb-4">
                <div class="col-md-4"><div class="rd-card"><div class="small text-uppercase text-muted fw-bold">Chưa thanh toán</div><div class="h4 mb-0 mt-2">07</div></div></div>
                <div class="col-md-4"><div class="rd-card"><div class="small text-uppercase text-muted fw-bold">Đã thanh toán</div><div class="h4 mb-0 mt-2">15</div></div></div>
                <div class="col-md-4"><div class="rd-card"><div class="small text-uppercase text-muted fw-bold">Công nợ còn lại</div><div class="h4 mb-0 mt-2">6.900.000 VNĐ</div></div></div>
            </div>
        @endif

        <div class="rd-table-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 p-4 pb-0">
                <div>
                    <h4 class="mb-1">Bảng dữ liệu</h4>
                    <p class="text-muted mb-0">Nội dung được viết trực tiếp trong Blade, không sử dụng biến data chung.</p>
                </div>
                @if(request()->routeIs('reception.customers.index'))
                    <a href="{{ route('reception.customers.create') }}" class="btn btn-primary" style="padding: 10px 18px;">Thêm khách hàng</a>
                @elseif(request()->routeIs('reception.bookings.index'))
                    <a href="{{ route('reception.bookings.create') }}" class="btn btn-primary" style="padding: 10px 18px;">Thêm đặt phòng</a>
                @endif
            </div>

            <div class="table-responsive p-4">
                @if(request()->routeIs('reception.customers.index'))
                    <table class="table align-middle">
                        <thead><tr><th>Mã KH</th><th>Tên KH</th><th>CCCD</th><th>Ngày sinh</th><th>Giới tính</th><th>Điểm</th><th>Trạng thái</th><th>Thao tác</th></tr></thead>
                        <tbody>
                            <tr><td>1</td><td>Nguyễn Minh An</td><td>079204000111</td><td>12/04/1998</td><td>Nam</td><td>120</td><td><span class="rd-badge rd-badge--success">Hoạt động</span></td><td><a href="{{ route('reception.customers.show', ['customerId' => 1]) }}" class="btn btn-sm btn-success">Xem</a> <a href="{{ route('reception.customers.edit', ['customerId' => 1]) }}" class="btn btn-sm btn-warning">Sửa</a></td></tr>
                            <tr><td>2</td><td>Trần Bảo Ngọc</td><td>048204000222</td><td>24/08/2000</td><td>Nữ</td><td>80</td><td><span class="rd-badge rd-badge--muted">Không hoạt động</span></td><td><a href="{{ route('reception.customers.show', ['customerId' => 2]) }}" class="btn btn-sm btn-success">Xem</a> <a href="{{ route('reception.customers.edit', ['customerId' => 2]) }}" class="btn btn-sm btn-warning">Sửa</a></td></tr>
                        </tbody>
                    </table>
                @elseif(request()->routeIs('reception.bookings.index'))
                    <table class="table align-middle">
                        <thead><tr><th>Mã đặt</th><th>Mã KH</th><th>Tên KH</th><th>Phòng</th><th>Loại phòng</th><th>Nhận phòng</th><th>Trả phòng</th><th>Tình trạng</th><th>Thao tác</th></tr></thead>
                        <tbody>
                            <tr><td>9001</td><td>1</td><td>Nguyễn Minh An</td><td>A101</td><td>Deluxe</td><td>08/04/2026</td><td>10/04/2026</td><td><span class="rd-badge rd-badge--warning">Đã đặt</span></td><td><a href="{{ route('reception.bookings.show', ['bookingId' => 9001]) }}" class="btn btn-sm btn-success">Xem</a> <a href="{{ route('reception.bookings.edit', ['bookingId' => 9001]) }}" class="btn btn-sm btn-warning">Sửa</a></td></tr>
                            <tr><td>9002</td><td>2</td><td>Trần Bảo Ngọc</td><td>A102</td><td>Suite</td><td>07/04/2026</td><td>09/04/2026</td><td><span class="rd-badge rd-badge--success">Đang sử dụng</span></td><td><a href="{{ route('reception.bookings.show', ['bookingId' => 9002]) }}" class="btn btn-sm btn-success">Xem</a> <a href="{{ route('reception.bookings.edit', ['bookingId' => 9002]) }}" class="btn btn-sm btn-warning">Sửa</a></td></tr>
                        </tbody>
                    </table>
                @else
                    <table class="table align-middle">
                        <thead><tr><th>Mã HĐ</th><th>Mã đặt phòng</th><th>Ngày lập</th><th>Nhân viên</th><th>Tổng tiền</th><th>Đã thanh toán</th><th>Còn lại</th><th>Trạng thái</th></tr></thead>
                        <tbody>
                            <tr><td>5001</td><td>9001</td><td>08/04/2026</td><td>Phạm Thùy Linh</td><td>4.500.000 VNĐ</td><td>1.500.000 VNĐ</td><td>3.000.000 VNĐ</td><td><span class="rd-badge rd-badge--warning">Chưa thanh toán</span></td></tr>
                            <tr><td>5002</td><td>9002</td><td>07/04/2026</td><td>Hoàng Gia Bảo</td><td>3.250.000 VNĐ</td><td>3.250.000 VNĐ</td><td>0 VNĐ</td><td><span class="rd-badge rd-badge--success">Đã thanh toán</span></td></tr>
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
