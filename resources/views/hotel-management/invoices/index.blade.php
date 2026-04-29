<x-hotel-management.index-page
    title="Quản lý hóa đơn"
    subtitle="Danh sách quản lý hóa đơn"
    :show-create-button="false"
>
    <x-slot:filters>
        <div class="col-md-3">
            <label class="form-label">Mã hóa đơn</label>
            <input type="text" class="form-control" placeholder="Tìm mã hóa đơn">
        </div>
        <div class="col-md-3">
            <label class="form-label">Trạng thái</label>
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
        <thead><tr><th>Mã hóa đơn</th><th>Ngày lập</th><th>Tên nhân viên</th><th>Tổng tiền</th><th>Đã thanh toán</th><th>Trạng thái</th><th style="min-width: 180px;">Thao tác</th></tr></thead>
        <tbody>
            <tr><td>5001</td><td>08/04/2026</td><td>Phạm Thùy Linh</td><td>4.500.000 VNĐ</td><td>1.500.000 VNĐ</td><td><span class="hm-badge hm-badge--warning">Chưa thanh toán</span></td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.invoices.show', ['recordId' => 5001]), 'editUrl' => null, 'showDelete' => false])</td></tr>
            <tr><td>5002</td><td>07/04/2026</td><td>Hoàng Gia Bảo</td><td>3.250.000 VNĐ</td><td>3.250.000 VNĐ</td><td><span class="hm-badge hm-badge--success">Đã thanh toán</span></td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.invoices.show', ['recordId' => 5002]), 'editUrl' => null, 'showDelete' => false])</td></tr>
        </tbody>
    </table>
</x-hotel-management.index-page>
