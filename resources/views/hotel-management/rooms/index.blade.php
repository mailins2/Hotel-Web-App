<x-hotel-management.index-page
    title="Quản lý phòng"
    subtitle="Danh sách quản lý phòng tại khách sạn"
    :create-route="route('hotel.rooms.create')"
>
    <x-slot:filters>
        <div class="col-md-3">
            <label class="form-label">Số phòng</label>
            <input type="text" class="form-control" placeholder="Ví dụ: A101">
        </div>
        <div class="col-md-3">
            <label class="form-label">Tình trạng</label>
            <div class="hm-select-wrap">
                <select class="form-select">
                    <option>Tất cả tình trạng</option>
                    <option>Trống</option>
                    <option>Đã đặt</option>
                    <option>Đang sử dụng</option>
                    <option>Đang dọn dẹp</option>
                </select>
            </div>
        </div>
    </x-slot:filters>

    <table class="table table-striped align-middle">
        <thead><tr><th>Mã phòng</th><th>Số phòng</th><th>Loại phòng</th><th>Sức chứa</th><th>Tình trạng</th><th style="min-width: 180px;">Thao tác</th></tr></thead>
        <tbody>
            <tr><td>1</td><td>A101</td><td>Deluxe</td><td>2</td><td><span class="hm-badge hm-badge--success">Trống</span></td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.rooms.show', ['recordId' => 1]), 'editUrl' => route('hotel.rooms.edit', ['recordId' => 1]), 'showDelete' => true])</td></tr>
            <tr><td>2</td><td>A102</td><td>Suite</td><td>4</td><td><span class="hm-badge hm-badge--info">Đang sử dụng</span></td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.rooms.show', ['recordId' => 2]), 'editUrl' => route('hotel.rooms.edit', ['recordId' => 2]), 'showDelete' => true])</td></tr>
        </tbody>
    </table>
</x-hotel-management.index-page>
