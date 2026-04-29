<x-hotel-management.index-page
    title="Quản lý thanh toán"
    subtitle="Danh sách quản lý thanh toán"
    :show-create-button="false"
>
    <x-slot:filters>
        <div class="col-md-3">
            <label class="form-label">Trạng thái hóa đơn</label>
            <div class="hm-select-wrap">
                <select class="form-select">
                    <option>Tất cả trạng thái</option>
                    <option>Chưa thanh toán</option>
                    <option>Đã thanh toán</option>
                </select>
            </div>
        </div>
    </x-slot:filters>

    <table class="table table-striped align-middle">
        <thead><tr><th>Mã TT</th><th>Mã đặt phòng</th><th>Người thanh toán</th><th>Số tiền</th><th>Loại thanh toán</th><th>Ngày thanh toán</th><th>Trạng thái hóa đơn</th><th style="min-width: 180px;">Thao tác</th></tr></thead>
        <tbody>
            <tr><td>1</td><td>4</td><td>Nguyễn Minh An</td><td>1.500.000 VNĐ</td><td>Đặt cọc</td><td>08/04/2026 10:30</td><td><span class="hm-badge hm-badge--warning">Chưa thanh toán</span></td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.payments.show', ['recordId' => 1]), 'editUrl' => null, 'showDelete' => false])</td></tr>
            <tr><td>2</td><td>5</td><td>Trần Bảo Ngọc</td><td>3.250.000 VNĐ</td><td>Thanh toán checkout</td><td>07/04/2026 14:15</td><td><span class="hm-badge hm-badge--success">Đã thanh toán</span></td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.payments.show', ['recordId' => 2]), 'editUrl' => null, 'showDelete' => false])</td></tr>
        </tbody>
    </table>
</x-hotel-management.index-page>
