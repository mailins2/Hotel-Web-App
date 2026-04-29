<x-hotel-management.index-page
    title="Quản lý nhân viên"
    subtitle="Danh sách quản lý nhân viên tại khách sạn"
    :create-route="route('hotel.employees.create')"
>
    <x-slot:filters>
        <div class="col-md-3">
            <label class="form-label">Tìm kiếm</label>
            <input type="text" class="form-control" placeholder="Tìm kiếm...">
        </div>
    </x-slot:filters>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Mã NV</th>
                <th>Tên nhân viên</th>
                <th>Mã TK</th>
                <th style="min-width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>1</td><td>Phạm Thùy Linh</td><td>201</td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.employees.show', ['recordId' => 1]), 'editUrl' => route('hotel.employees.edit', ['recordId' => 1]), 'showDelete' => true])</td></tr>
            <tr><td>2</td><td>Hoàng Gia Bảo</td><td>202</td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.employees.show', ['recordId' => 2]), 'editUrl' => route('hotel.employees.edit', ['recordId' => 2]), 'showDelete' => true])</td></tr>
        </tbody>
    </table>
</x-hotel-management.index-page>
