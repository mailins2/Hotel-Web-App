<x-hotel-management.index-page
    title="Quản lý loại phòng"
    subtitle="Danh sách quản lý các loại phòng tại khách sạn"
    :create-route="route('hotel.room-types.create')"
>
    <x-slot:filters>
        <div class="col-md-3">
            <label class="form-label">Loại phòng</label>
            <div class="hm-select-wrap">
                <select class="form-select">
                    <option>Tất cả</option>
                    <option>Deluxe</option>
                    <option>Junior</option>
                </select>
            </div>
        </div>
    </x-slot:filters>

    <table class="table table-striped align-middle">
        <thead><tr><th>Mã loại</th><th>Tên loại phòng</th><th>Mô tả</th><th>Số người tối đa</th><th>Ảnh phòng</th><th style="min-width: 180px;">Thao tác</th></tr></thead>
        <tbody>
            <tr><td>1</td><td>Deluxe</td><td>Phòng tiêu chuẩn cao cấp</td><td>2</td><td>dấdada</td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.room-types.show', ['recordId' => 1]), 'editUrl' => route('hotel.room-types.edit', ['recordId' => 1]), 'showDelete' => true])</td></tr>
            <tr><td>2</td><td>Suite</td><td>Không gian rộng rãi</td><td>4</td><td>dấdada</td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.room-types.show', ['recordId' => 2]), 'editUrl' => route('hotel.room-types.edit', ['recordId' => 2]), 'showDelete' => true])</td></tr>
        </tbody>
    </table>
</x-hotel-management.index-page>
