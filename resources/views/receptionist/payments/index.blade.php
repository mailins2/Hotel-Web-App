<x-receptionist.index-page
    title="Quản lý thanh toán"
    subtitle="Danh sách quản lý thanh toán"
    table-title="Danh sách thanh toán"
>
    <x-slot:filters>
        <div class="col-lg-5">
            <label class="form-label">Tìm kiếm</label>
            <input type="text" class="form-control" placeholder="Tìm kiếm...">
        </div>
        <div class="col-md-4 col-lg-3">
            <label class="form-label">Trạng thái hóa đơn</label>
            <div class="rd-select-wrap">
                <select class="form-select">
                    <option>Tất cả trạng thái</option>
                    <option>Chưa thanh toán</option>
                    <option>Đã thanh toán</option>
                </select>
            </div>
        </div>
    </x-slot:filters>

    <table class="table align-middle">
        <thead>
            <tr>
                <th>Mã thanh toán</th>
                <th>Người thanh toán</th>
                <th>Số tiền</th>
                <th>Loại thanh toán</th>
                <th>Ngày thanh toán</th>
                <th>Trạng thái hóa đơn</th>
                <th style="min-width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Nguyễn Minh An</td>
                <td>1.500.000 VNĐ</td>
                <td>Đặt cọc</td>
                <td>08/04/2026 10:30</td>
                <td><span class="rd-badge rd-badge--warning">Chưa thanh toán</span></td>
                <td>
                    @include('hotel-management.partials.action-icons', [
                        'showUrl' => route('reception.payments.show', ['paymentId' => 1]),
                        'editUrl' => null,
                        'showDelete' => false,
                    ])
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td>Trần Bảo Ngọc</td>
                <td>3.250.000 VNĐ</td>
                <td>Thanh toán checkout</td>
                <td>07/04/2026 14:15</td>
                <td><span class="rd-badge rd-badge--success">Đã thanh toán</span></td>
                <td>
                    @include('hotel-management.partials.action-icons', [
                        'showUrl' => route('reception.payments.show', ['paymentId' => 2]),
                        'editUrl' => null,
                        'showDelete' => false,
                    ])
                </td>
            </tr>
        </tbody>
    </table>
</x-receptionist.index-page>
