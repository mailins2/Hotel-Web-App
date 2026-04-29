<x-hotel-management.index-page
    title="Quản lý tài khoản"
    subtitle="Danh sách quản lý tài khoản"
    :create-route="route('hotel.accounts.create')"
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
                    <option>Tất cả</option>
                    <option>Hoạt động</option>
                    <option>Không hoạt động</option>
                </select>
            </div>
        </div>
    </x-slot:filters>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Mã tài khoản</th>
                <th>Email</th>
                <th>Họ tên</th>
                <th>Loại tài khoản</th>
                <th>Trạng thái</th>
                <th style="min-width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>101</td>
                <td>minhan@gmail.com</td>
                <td>Nguyễn Minh An</td>
                <td>Khách hàng</td>
                <td><span class="hm-badge hm-badge--success">Hoạt động</span></td>
                <td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.accounts.show', ['recordId' => 101]), 'editUrl' => route('hotel.accounts.edit', ['recordId' => 101]), 'showDelete' => true])</td>
            </tr>
            <tr>
                <td>201</td>
                <td>letan01@peachvalley.vn</td>
                <td>Phạm Thùy Linh</td>
                <td>Lễ tân</td>
                <td><span class="hm-badge hm-badge--success">Hoạt động</span></td>
                <td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.accounts.show', ['recordId' => 201]), 'editUrl' => route('hotel.accounts.edit', ['recordId' => 201]), 'showDelete' => true])</td>
            </tr>
        </tbody>
    </table>
</x-hotel-management.index-page>
