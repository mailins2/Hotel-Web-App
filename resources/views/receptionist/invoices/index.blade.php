<x-receptionist.index-page
    title="Quản lý hóa đơn"
    subtitle="Thông tin danh sách hóa đơn"
    table-title="Danh sách hóa đơn"
>
    <x-slot:stats>
        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="rd-card"><div class="small text-uppercase text-muted fw-bold">Chưa thanh toán</div><div class="h4 mb-0 mt-2">07</div></div></div>
            <div class="col-md-4"><div class="rd-card"><div class="small text-uppercase text-muted fw-bold">Đã thanh toán</div><div class="h4 mb-0 mt-2">15</div></div></div>
            <div class="col-md-4"><div class="rd-card"><div class="small text-uppercase text-muted fw-bold">Công nợ còn lại</div><div class="h4 mb-0 mt-2">6.900.000 VNĐ</div></div></div>
        </div>
    </x-slot:stats>

    <x-slot:filters>
        <div class="col-lg-5">
            <label class="form-label">Tìm kiếm</label>
            <input type="text" class="form-control" placeholder="Tìm kiếm...">
        </div>
        <div class="col-md-4 col-lg-3">
            <label class="form-label">Trạng thái</label>
            <div class="rd-select-wrap">
                <select class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option>Chưa thanh toán</option>
                    <option>Đã thanh toán</option>
                </select>
            </div>
        </div>
    </x-slot:filters>

    <table class="table align-middle">
        <thead>
            <tr>
                <th>Mã HĐ</th>
                <th>Ngày lập</th>
                <th>Nhân viên</th>
                <th>Tổng tiền</th>
                <th>Đã thanh toán</th>
                <th>Còn lại</th>
                <th>Trạng thái</th>
                <th style="min-width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>5001</td>
                <td>08/04/2026</td>
                <td>Phạm Thùy Linh</td>
                <td>4.500.000 VNĐ</td>
                <td>1.500.000 VNĐ</td>
                <td>3.000.000 VNĐ</td>
                <td><span class="rd-badge rd-badge--warning">Chưa thanh toán</span></td>
                <td>
                    @include('hotel-management.partials.action-icons', [
                        'showUrl' => route('reception.invoices.show', ['invoiceId' => 5001]),
                        'editUrl' => route('reception.invoices.edit', ['invoiceId' => 5001]),
                        'showDelete' => false,
                    ])
                </td>
            </tr>
            <tr>
                <td>5002</td>
                <td>07/04/2026</td>
                <td>Hoàng Gia Bảo</td>
                <td>3.250.000 VNĐ</td>
                <td>3.250.000 VNĐ</td>
                <td>0 VNĐ</td>
                <td><span class="rd-badge rd-badge--success">Đã thanh toán</span></td>
                <td>
                    @include('hotel-management.partials.action-icons', [
                        'showUrl' => route('reception.invoices.show', ['invoiceId' => 5002]),
                        'editUrl' => route('reception.invoices.edit', ['invoiceId' => 5002]),
                        'showDelete' => false,
                    ])
                </td>
            </tr>
        </tbody>
    </table>
</x-receptionist.index-page>
