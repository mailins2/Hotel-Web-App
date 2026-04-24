@php
    $routeName = request()->route()?->getName() ?? '';
    $moduleKey = request()->route('moduleKey');
    $isEdit = $routeName === 'hotel.modules.edit' || $routeName === 'reception.customers.edit' || $routeName === 'reception.bookings.edit';

    if (str_starts_with($routeName, 'reception.customers')) {
        $moduleKey = 'customers';
    }

    if ($routeName === 'reception.bookings.edit') {
        $moduleKey = 'reception-bookings';
    }
@endphp

<x-app-layout :assets="['animation']">
    <style>
        .hm-readonly-input {
            background-color: #f3f4f6;
            color: #6b7280;
            border-color: #cbd5e1;
        }
    </style>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 pb-4">
                    <div class="header-title">
                        <h4 class="card-title mb-1">{{ $isEdit ? 'Chỉnh sửa' : 'Thêm mới' }}</h4>
                    </div>
                    @if(str_starts_with($routeName, 'reception.customers'))
                        <a href="{{ route('reception.customers.index') }}" class="btn btn-sm btn-primary" style="padding: 10px;">Quay lại</a>
                    @elseif($routeName === 'reception.bookings.edit')
                        <a href="{{ route('reception.bookings.index') }}" class="btn btn-sm btn-primary" style="padding: 10px;">Quay lại</a>
                    @else
                        <a href="{{ route('hotel.modules.index', ['moduleKey' => $moduleKey]) }}" class="btn btn-sm btn-primary" style="padding: 10px;">Quay lại</a>
                    @endif
                </div>
                <div class="card-body">
                    <form data-ui-only-form>
                        <div class="row">
                            @switch($moduleKey)
                                @case('accounts')
                                    <div class="form-group col-md-6"><label class="form-label">Mã tài khoản</label><input type="text" class="form-control hm-readonly-input" value="101" readonly></div>
                                    <div class="form-group col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" value="minhan@gmail.com"></div>
                                    <div class="form-group col-md-6"><label class="form-label">Mật khẩu</label><input type="password" class="form-control" value=""></div>
                                    <div class="form-group col-md-6"><label class="form-label">Loại tài khoản</label><select class="form-select"><option>Khách hàng</option><option>Lễ tân</option><option>Quản lý</option></select></div>
                                    <div class="form-group col-md-6"><label class="form-label">Trạng thái</label><select class="form-select"><option>Hoạt động</option><option>Không hoạt động</option></select></div>
                                    @break
                                @case('customers')
                                    <div class="form-group col-md-6"><label class="form-label">Mã khách hàng</label><input type="text" class="form-control hm-readonly-input" value="1" readonly></div>
                                    <div class="form-group col-md-6"><label class="form-label">Tên khách hàng</label><input type="text" class="form-control" value="Nguyễn Minh An"></div>
                                    <div class="form-group col-md-6"><label class="form-label">Ngày sinh</label><input type="date" class="form-control" value="1998-04-12"></div>
                                    <div class="form-group col-md-6"><label class="form-label">Giới tính</label><select class="form-select"><option>Nam</option><option>Nữ</option><option>Khác</option></select></div>
                                    <div class="form-group col-md-6"><label class="form-label">Số điện thoại</label><input type="text" class="form-control" value="0901234567"></div>
                                    <div class="form-group col-md-6"><label class="form-label">CCCD</label><input type="text" class="form-control" value="079204000111"></div>
                                    <div class="form-group col-md-12"><label class="form-label">Địa chỉ</label><textarea class="form-control" rows="3">12 Nguyễn Huệ, Quận 1, TP.HCM</textarea></div>
                                    <div class="form-group col-md-6"><label class="form-label">Điểm tích lũy</label><input type="number" class="form-control" value="120"></div>
                                    <div class="form-group col-md-6"><label class="form-label">Trạng thái</label><select class="form-select"><option>Hoạt động</option><option>Không hoạt động</option></select></div>
                                    @break
                                @case('employees')
                                    <div class="form-group col-md-6"><label class="form-label">Mã nhân viên</label><input type="text" class="form-control hm-readonly-input" value="1" readonly></div>
                                    <div class="form-group col-md-6"><label class="form-label">Tên nhân viên</label><input type="text" class="form-control" value="Phạm Thùy Linh"></div>
                                    <div class="form-group col-md-6"><label class="form-label">Mã tài khoản</label><input type="number" class="form-control" value="201"></div>
                                    @break
                                @case('room-types')
                                    <div class="form-group col-md-6"><label class="form-label">Mã loại phòng</label><input type="text" class="form-control hm-readonly-input" value="1" readonly></div>
                                    <div class="form-group col-md-6"><label class="form-label">Tên loại phòng</label><input type="text" class="form-control" value="Deluxe"></div>
                                    <div class="form-group col-md-12"><label class="form-label">Mô tả</label><textarea class="form-control" rows="3">Phòng tiêu chuẩn cao cấp, phù hợp cho khách đi công tác.</textarea></div>
                                    <div class="form-group col-md-6"><label class="form-label">Số người tối đa</label><input type="number" class="form-control" value="2"></div>
                                    <div class="form-group col-md-12"><label class="form-label">Ảnh phòng</label><input type="text" class="form-control" placeholder="Nhập đường dẫn ảnh minh họa"></div>
                                    @break
                                @case('rooms')
                                    <div class="form-group col-md-6"><label class="form-label">Mã phòng</label><input type="text" class="form-control hm-readonly-input" value="1" readonly></div>
                                    <div class="form-group col-md-6"><label class="form-label">Số phòng</label><input type="text" class="form-control" value="A101"></div>
                                    <div class="form-group col-md-6"><label class="form-label">Mã loại phòng</label><input type="number" class="form-control" value="1"></div>
                                    <div class="form-group col-md-6"><label class="form-label">Tên loại phòng</label><input type="text" class="form-control" value="Deluxe"></div>
                                    <div class="form-group col-md-6"><label class="form-label">Tình trạng</label><select class="form-select"><option>Trống</option><option>Đã đặt</option><option>Đang sử dụng</option><option>Đang dọn dẹp</option></select></div>
                                    @break
                                @case('services')
                                    <div class="form-group col-md-6"><label class="form-label">Mã dịch vụ</label><input type="text" class="form-control hm-readonly-input" value="6" readonly></div>
                                    <div class="form-group col-md-6"><label class="form-label">Tên dịch vụ</label><input type="text" class="form-control" value="Giặt ủi"></div>
                                    <div class="form-group col-md-6"><label class="form-label">Giá dịch vụ</label><input type="number" class="form-control" value="120000"></div>
                                    <div class="form-group col-md-6"><label class="form-label">Loại dịch vụ</label><select class="form-select"><option>Ăn uống</option><option>Dịch vụ phòng</option><option>Giải trí</option></select></div>
                                    <div class="form-group col-md-12"><label class="form-label">Ảnh dịch vụ</label><input type="text" class="form-control" placeholder="Nhập đường dẫn ảnh minh họa"></div>
                                    @break
                                @case('promotions')
                                    <div class="form-group col-md-6"><label class="form-label">Mã khuyến mãi</label><input type="text" class="form-control hm-readonly-input" value="1" readonly></div>
                                    <div class="form-group col-md-6"><label class="form-label">Tên chương trình</label><input type="text" class="form-control" value="Summer Escape"></div>
                                    <div class="form-group col-md-12"><label class="form-label">Mô tả</label><textarea class="form-control" rows="3">Giảm giá cho khách đặt phòng trong mùa hè.</textarea></div>
                                    <div class="form-group col-md-4"><label class="form-label">Điểm yêu cầu</label><input type="number" class="form-control" value="50"></div>
                                    <div class="form-group col-md-4"><label class="form-label">Ngày bắt đầu</label><input type="date" class="form-control" value="2026-05-01"></div>
                                    <div class="form-group col-md-4"><label class="form-label">Ngày kết thúc</label><input type="date" class="form-control" value="2026-06-30"></div>
                                    <div class="form-group col-md-6"><label class="form-label">Phần trăm giảm giá</label><input type="number" class="form-control" value="15"></div>
                                    @break
                                @case('invoices')
                                    <div class="form-group col-md-6"><label class="form-label">Mã hóa đơn</label><input type="text" class="form-control hm-readonly-input" value="5001" readonly></div>
                                    <div class="form-group col-md-6"><label class="form-label">Mã đặt phòng</label><input type="text" class="form-control hm-readonly-input" value="9001" readonly></div>
                                    <div class="form-group col-md-6"><label class="form-label">Ngày lập</label><input type="date" class="form-control hm-readonly-input" value="2026-04-08" readonly></div>
                                    <div class="form-group col-md-6"><label class="form-label">Tổng tiền</label><input type="text" class="form-control hm-readonly-input" value="4.500.000 VNĐ" readonly></div>
                                    @break
                                @case('payments')
                                    <div class="form-group col-md-6"><label class="form-label">Mã thanh toán</label><input type="text" class="form-control hm-readonly-input" value="1" readonly></div>
                                    <div class="form-group col-md-6"><label class="form-label">Mã hóa đơn</label><input type="number" class="form-control" value="5001"></div>
                                    <div class="form-group col-md-6"><label class="form-label">Số tiền</label><input type="number" class="form-control" value="1500000"></div>
                                    <div class="form-group col-md-6"><label class="form-label">Loại thanh toán</label><select class="form-select"><option>Đặt cọc</option><option>Thanh toán checkout</option></select></div>
                                    @break
                                @case('reviews')
                                    <div class="form-group col-md-6"><label class="form-label">Mã đánh giá</label><input type="text" class="form-control hm-readonly-input" value="1" readonly></div>
                                    <div class="form-group col-md-6"><label class="form-label">Mã đặt phòng</label><input type="number" class="form-control" value="9001"></div>
                                    <div class="form-group col-md-6"><label class="form-label">Số sao</label><select class="form-select"><option>5 sao</option><option>4 sao</option><option>3 sao</option></select></div>
                                    <div class="form-group col-md-12"><label class="form-label">Nội dung đánh giá</label><textarea class="form-control" rows="3">Phòng sạch sẽ, nhân viên hỗ trợ nhiệt tình.</textarea></div>
                                    @break
                                @case('reception-bookings')
                                    <div class="form-group col-md-6"><label class="form-label">Mã đặt phòng</label><input type="text" class="form-control hm-readonly-input" value="9001" readonly></div>
                                    <div class="form-group col-md-6"><label class="form-label">Mã khách hàng</label><input type="number" class="form-control" value="1"></div>
                                    <div class="form-group col-md-6"><label class="form-label">Tên khách hàng</label><input type="text" class="form-control hm-readonly-input" value="Nguyễn Minh An" readonly></div>
                                    <div class="form-group col-md-6"><label class="form-label">Số điện thoại</label><input type="text" class="form-control hm-readonly-input" value="0901234567" readonly></div>
                                    <div class="form-group col-md-4"><label class="form-label">Ngày đặt</label><input type="date" class="form-control" value="2026-04-05"></div>
                                    <div class="form-group col-md-4"><label class="form-label">Ngày nhận phòng</label><input type="date" class="form-control" value="2026-04-08"></div>
                                    <div class="form-group col-md-4"><label class="form-label">Ngày trả phòng</label><input type="date" class="form-control" value="2026-04-10"></div>
                                    <div class="form-group col-md-6"><label class="form-label">Số lượng người ở</label><input type="number" class="form-control" value="2"></div>
                                    <div class="form-group col-md-6"><label class="form-label">Tình trạng</label><select class="form-select"><option>Đã đặt</option><option>Đang sử dụng</option><option>Đã hủy</option><option>Đã trả phòng</option></select></div>
                                    @break
                            @endswitch
                        </div>

                        <button type="submit" class="btn btn-primary mt-3" style="padding: 10px;">{{ $isEdit ? 'Lưu thay đổi' : 'Tạo mới' }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelector('[data-ui-only-form]')?.addEventListener('submit', function (event) {
                    event.preventDefault();
                });
            });
        </script>
    @endpush
</x-app-layout>
