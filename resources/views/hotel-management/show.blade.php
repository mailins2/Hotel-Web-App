@php
    $routeName = request()->route()?->getName() ?? '';
    $moduleKey = request()->route('moduleKey');

    if ($routeName === 'reception.customers.show') {
        $moduleKey = 'customers';
    }

    if ($routeName === 'reception.bookings.show') {
        $moduleKey = 'reception-bookings';
    }
@endphp

<x-app-layout :assets="['animation']">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 pb-4">
                    <div class="header-title">
                        @switch($moduleKey)
                            @case('accounts')
                                <h4 class="card-title mb-1">Chi tiết tài khoản</h4>
                                <p class="mb-0 text-muted">Thông tin chi tiết tài khoản</p>
                                @break
                            @case('customers')
                                <h4 class="card-title mb-1">Chi tiết khách hàng</h4>
                                <p class="mb-0 text-muted">Trang xem nhanh thông tin khách hàng.</p>
                                @break
                            @case('employees')
                                <h4 class="card-title mb-1">Chi tiết nhân viên</h4>
                                <p class="mb-0 text-muted">Thông tin hồ sơ nhân viên</p>
                                @break
                            @case('room-types')
                                <h4 class="card-title mb-1">Chi tiết loại phòng</h4>
                                <p class="mb-0 text-muted">Thông tin loại phòng</p>
                                @break
                            @case('rooms')
                                <h4 class="card-title mb-1">Chi tiết phòng</h4>
                                <p class="mb-0 text-muted">Thông tin chi tiết phòng</p>
                                @break
                            @case('services')
                                <h4 class="card-title mb-1">Chi tiết dịch vụ</h4>
                                <p class="mb-0 text-muted">Thông tin chi tiết dịch vụ</p>
                                @break
                            @case('promotions')
                                <h4 class="card-title mb-1">Chi tiết khuyến mãi</h4>
                                <p class="mb-0 text-muted">Thông tin chi tiết khuyến mãi</p>
                                @break
                            @case('invoices')
                                <h4 class="card-title mb-1">Chi tiết hóa đơn</h4>
                                <p class="mb-0 text-muted">Thông tin chi tiết hóa đơn</p>
                                @break
                            @case('payments')
                                <h4 class="card-title mb-1">Chi tiết thanh toán</h4>
                                <p class="mb-0 text-muted">Thông tin chi tiết thanh toán</p>
                                @break
                            @case('reviews')
                                <h4 class="card-title mb-1">Chi tiết đánh giá</h4>
                                <p class="mb-0 text-muted">Thông tin chi tiết đánh giá</p>
                                @break
                            @case('reception-bookings')
                                <h4 class="card-title mb-1">Chi tiết đặt phòng</h4>
                                <p class="mb-0 text-muted">Thông tin chi tiết đặt phòng</p>
                                @break
                            @default
                                <h4 class="card-title mb-1">Chi tiết dữ liệu</h4>
                                <p class="mb-0 text-muted">Trang hiển thị tĩnh.</p>
                        @endswitch
                    </div>
                    <div class="d-flex gap-2">
                        @if($routeName === 'reception.customers.show')
                            <a href="{{ route('reception.customers.edit', ['customerId' => request()->route('customerId')]) }}" class="btn btn-sm btn-warning" style="padding: 10px;">Chỉnh sửa</a>
                            <a href="{{ route('reception.customers.index') }}" class="btn btn-sm btn-primary" style="padding: 10px;">Quay lại</a>
                        @elseif($routeName === 'reception.bookings.show')
                            <a href="{{ route('reception.bookings.edit', ['bookingId' => request()->route('bookingId')]) }}" class="btn btn-sm btn-warning" style="padding: 10px;">Chỉnh sửa</a>
                            <a href="{{ route('reception.bookings.index') }}" class="btn btn-sm btn-primary" style="padding: 10px;">Quay lại</a>
                        @else
                            @if(!in_array($moduleKey, ['invoices', 'payments', 'reviews'], true))
                                <a href="{{ route('hotel.modules.edit', ['moduleKey' => $moduleKey, 'recordId' => request()->route('recordId') ?? 1]) }}" class="btn btn-sm btn-warning" style="padding: 10px;">Chỉnh sửa</a>
                            @endif
                            <a href="{{ route('hotel.modules.index', ['moduleKey' => $moduleKey]) }}" class="btn btn-sm btn-primary" style="padding: 10px;">Quay lại</a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @switch($moduleKey)
                            @case('accounts')
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã tài khoản</div><div class="fw-semibold">101</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Email</div><div class="fw-semibold">minhan@gmail.com</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Họ tên</div><div class="fw-semibold">Nguyễn Minh An</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Loại tài khoản</div><div class="fw-semibold">Khách hàng</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Trạng thái</div><div class="fw-semibold">Hoạt động</div></div></div>
                                @break
                            @case('customers')
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã khách hàng</div><div class="fw-semibold">1</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên khách hàng</div><div class="fw-semibold">Nguyễn Minh An</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày sinh</div><div class="fw-semibold">12/04/1998</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Giới tính</div><div class="fw-semibold">Nam</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số điện thoại</div><div class="fw-semibold">0901234567</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">CCCD</div><div class="fw-semibold">079204000111</div></div></div>
                                <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Địa chỉ</div><div class="fw-semibold">12 Nguyễn Huệ, Quận 1, TP.HCM</div></div></div>
                                @break
                            @case('employees')
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã nhân viên</div><div class="fw-semibold">1</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên nhân viên</div><div class="fw-semibold">Phạm Thùy Linh</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã tài khoản</div><div class="fw-semibold">201</div></div></div>
                                @break
                            @case('room-types')
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã loại phòng</div><div class="fw-semibold">1</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên loại phòng</div><div class="fw-semibold">Deluxe</div></div></div>
                                <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mô tả</div><div class="fw-semibold">Phòng tiêu chuẩn cao cấp, phù hợp cho khách đi công tác hoặc cặp đôi.</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số người tối đa</div><div class="fw-semibold">2</div></div></div>
                                <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ảnh dịch vụ</div><div class="fw-semibold">Link ảnh</div></div></div>

                                @break
                            @case('rooms')
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã phòng</div><div class="fw-semibold">1</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số phòng</div><div class="fw-semibold">A101</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên loại phòng</div><div class="fw-semibold">Deluxe</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tình trạng</div><div class="fw-semibold">Trống</div></div></div>
                                <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mô tả</div><div class="fw-semibold">Hướng sân vườn yên tĩnh.</div></div></div>
                                @break
                            @case('services')
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã dịch vụ</div><div class="fw-semibold">6</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên dịch vụ</div><div class="fw-semibold">Giặt ủi</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Giá dịch vụ</div><div class="fw-semibold">120.000 VNĐ</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Loại dịch vụ</div><div class="fw-semibold">Dịch vụ phòng</div></div></div>
                                <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ảnh dịch vụ</div><div class="fw-semibold">Link ảnh</div></div></div>
                                @break
                            @case('promotions')
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã khuyến mãi</div><div class="fw-semibold">1</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên chương trình</div><div class="fw-semibold">Summer Escape</div></div></div>
                                <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mô tả</div><div class="fw-semibold">Giảm giá cho khách đặt phòng trong mùa hè.</div></div></div>
                                <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Điểm yêu cầu</div><div class="fw-semibold">50</div></div></div>
                                <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày bắt đầu</div><div class="fw-semibold">01/05/2026</div></div></div>
                                <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">% giảm giá</div><div class="fw-semibold">15%</div></div></div>
                                @break
                            @case('invoices')
                                <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã hóa đơn</div><div class="fw-semibold">5001</div></div></div>
                                <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã đặt phòng</div><div class="fw-semibold">9001</div></div></div>
                                <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày lập</div><div class="fw-semibold">08/04/2026</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Nhân viên phụ trách</div><div class="fw-semibold">Phạm Thùy Linh</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tổng tiền</div><div class="fw-semibold">4.500.000 VNĐ</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Đã thanh toán</div><div class="fw-semibold">1.500.000 VNĐ</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Trạng thái</div><div class="fw-semibold">Chưa thanh toán</div></div></div>
                                @break
                            @case('payments')
                                <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã thanh toán</div><div class="fw-semibold">1</div></div></div>
                                 <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã đặt phòng</div><div class="fw-semibold">1</div></div></div>
                                <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Người thanh toán</div><div class="fw-semibold">Nguyễn Minh An</div></div></div>
                                <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số tiền</div><div class="fw-semibold">1.500.000 VNĐ</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Loại thanh toán</div><div class="fw-semibold">Đặt cọc</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày thanh toán</div><div class="fw-semibold">08/04/2026 10:30</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Trạng thái hóa đơn</div><div class="fw-semibold">Chưa thanh toán</div></div></div>
                                @break
                            @case('reviews')
                                <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã đánh giá</div><div class="fw-semibold">1</div></div></div>
                                <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã đặt phòng</div><div class="fw-semibold">9001</div></div></div>
                                <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số sao</div><div class="fw-semibold">5 sao</div></div></div>
                                <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Nội dung đánh giá</div><div class="fw-semibold">Phòng sạch sẽ, nhân viên hỗ trợ nhiệt tình.</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày đánh giá</div><div class="fw-semibold">06/04/2026</div></div></div>
                                @break
                            @case('reception-bookings')
                                <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã đặt phòng</div><div class="fw-semibold">9001</div></div></div>
                                <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã khách hàng</div><div class="fw-semibold">1</div></div></div>
                                <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên khách hàng</div><div class="fw-semibold">Nguyễn Minh An</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày nhận phòng</div><div class="fw-semibold">08/04/2026</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày trả phòng</div><div class="fw-semibold">10/04/2026</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số phòng</div><div class="fw-semibold">A101</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Loại phòng</div><div class="fw-semibold">Deluxe</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số lượng người ở</div><div class="fw-semibold">2</div></div></div>
                                <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tình trạng</div><div class="fw-semibold">Đã đặt</div></div></div>
                                @break
                        @endswitch
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
