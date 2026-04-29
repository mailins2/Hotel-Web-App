<x-hotel-management.index-page
    title="Quản lý khách hàng"
    subtitle="Danh sách quản lý khách hàng tại khách sạn"
    :create-route="route('hotel.customers.create')"
>
    <x-slot:filters>
        <div class="col-md-3">
            <label class="form-label">Tìm kiếm</label>
            <input type="text" class="form-control" placeholder="Tìm kiếm...">
        </div>
        <div class="col-md-3">
            <label class="form-label">Trạng thái</label>
            <div class="hm-select-wrap">
                <select class="form-select">
                    <option>Tất cả trạng thái</option>
                    <option>Hoạt động</option>
                    <option>Không hoạt động</option>
                </select>
            </div>
        </div>
    </x-slot:filters>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Mã khách hàng</th>
                <th>Tên khách hàng</th>
                <th>Ngày sinh</th>
                <th>Giới tính</th>
                <th>Điểm</th>
                <th>Trạng thái</th>
                <th style="min-width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Nguyễn Minh An</td>
                <td>12/04/1998</td>
                <td>Nam</td>
                <td>120</td>
                <td><span class="hm-badge hm-badge--success">Hoạt động</span></td>
                <td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.customers.show', ['recordId' => 1]), 'editUrl' => route('hotel.customers.edit', ['recordId' => 1]), 'showDelete' => true])</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Trần Bảo Ngọc</td>
                <td>24/08/2000</td>
                <td>Nữ</td>
                <td>80</td>
                <td><span class="hm-badge hm-badge--muted">Không hoạt động</span></td>
                <td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.customers.show', ['recordId' => 2]), 'editUrl' => route('hotel.customers.edit', ['recordId' => 2]), 'showDelete' => true])</td>
            </tr>
        </tbody>
    </table>
</x-hotel-management.index-page>
