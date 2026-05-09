<x-hotel-management.index-page
    title="Quản lý bảng giá"
    subtitle="Danh sách thông tin bảng giá theo loại phòng"
    :create-route="route('hotel.price-lists.create')"
>
    <x-slot:filters>
        <div class="col-md-3">
            <label class="form-label">Loại phòng</label>
            <div class="hm-select-wrap">
                <select class="form-select">
                    <option>Tất cả loại phòng</option>
                    <option>Phòng Standard</option>
                    <option>Phòng Superior</option>
                    <option>Phòng Suite</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label">Mùa</label>
            <div class="hm-select-wrap">
                <select class="form-select">
                    <option>Tất cả mùa</option>
                    <option>Mùa 1</option>
                    <option>Mùa 2</option>
                    <option>Mùa 3</option>
                </select>
            </div>
        </div>
    </x-slot:filters>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Mã loại phòng</th>
                <th>Mùa</th>
                <th>Tên loại phòng</th>
                <th>Giá phòng</th>
                <th style="min-width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Mùa 1</td>
                <td>Phòng Standard</td>
                <td>300.000 VNĐ</td>
                <td>
                    @include('hotel-management.partials.action-icons', [
                        'showUrl' => route('hotel.price-lists.show', ['recordId' => 1]),
                        'editUrl' => route('hotel.price-lists.edit', ['recordId' => 1]),
                        'showDelete' => true,
                    ])
                </td>
            </tr>
            <tr>
                <td>1</td>
                <td>Mùa 2</td>
                <td>Phòng Standard</td>
                <td>400.000 VNĐ</td>
                <td>
                    @include('hotel-management.partials.action-icons', [
                        'showUrl' => route('hotel.price-lists.show', ['recordId' => 2]),
                        'editUrl' => route('hotel.price-lists.edit', ['recordId' => 2]),
                        'showDelete' => true,
                    ])
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td>Mùa 1</td>
                <td>Phòng Superior</td>
                <td>500.000 VNĐ</td>
                <td>
                    @include('hotel-management.partials.action-icons', [
                        'showUrl' => route('hotel.price-lists.show', ['recordId' => 3]),
                        'editUrl' => route('hotel.price-lists.edit', ['recordId' => 3]),
                        'showDelete' => true,
                    ])
                </td>
            </tr>
            <tr>
                <td>9</td>
                <td>Mùa 3</td>
                <td>Phòng Suite</td>
                <td>3.500.000 VNĐ</td>
                <td>
                    @include('hotel-management.partials.action-icons', [
                        'showUrl' => route('hotel.price-lists.show', ['recordId' => 4]),
                        'editUrl' => route('hotel.price-lists.edit', ['recordId' => 4]),
                        'showDelete' => true,
                    ])
                </td>
            </tr>
        </tbody>
    </table>
</x-hotel-management.index-page>
