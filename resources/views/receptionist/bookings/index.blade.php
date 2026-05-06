<x-receptionist.index-page
    title="Quản lý đặt phòng"
    subtitle="Danh sách quản lý đặt phòng"
    :create-route="route('reception.bookings.create')"
    create-label="Thêm đặt phòng"
    table-title="Danh sách thông tin đặt phòng"
>
    <x-slot:filters>
        <div class="col-lg-5">
            <label class="form-label">Tìm kiếm</label>
            <input type="text" class="form-control" placeholder="Tìm kiếm...">
        </div>
        <div class="col-md-4 col-lg-3">
            <label class="form-label">Tình trạng phòng</label>
            <div class="rd-select-wrap">
                <select class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option>Chờ xác nhận</option>
                    <option>Đã xác nhận</option>
                    <option>Đang ở</option>
                    <option>Đã hủy</option> 
                </select>
            </div>
        </div>
    </x-slot:filters>

    <table class="table align-middle">
        <thead>
            <tr>
                <th>Mã đặt phòng</th>
                <th>Tên khách hàng</th>
                <!-- <th>Phòng</th> -->
                <th>Loại phòng</th>
                <th>Nhận phòng</th>
                <th>Trả phòng</th>
                <th>Tình trạng</th>
                <th style="min-width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>9001</td>
                <td>Nguyễn Minh An</td>
                <!-- <td>A101</td> -->
                <td>Deluxe</td>
                <td>08/04/2026</td>
                <td>10/04/2026</td>
                <td><span class="rd-badge rd-badge--warning">Đã đặt</span></td>
                <td>
                    @include('hotel-management.partials.action-icons', [
                        'showUrl' => route('reception.bookings.show', ['bookingId' => 9001]),
                        'editUrl' => route('reception.bookings.edit', ['bookingId' => 9001]),
                        'showDelete' => false,
                    ])
                </td>
            </tr>
            <tr>
                <td>9002</td>
                <td>Trần Bảo Ngọc</td>
                <!-- <td>A102</td> -->
                <td>Suite</td>
                <td>07/04/2026</td>
                <td>09/04/2026</td>
                <td><span class="rd-badge rd-badge--success">Đang sử dụng</span></td>
                <td>
                    @include('hotel-management.partials.action-icons', [
                        'showUrl' => route('reception.bookings.show', ['bookingId' => 9002]),
                        'editUrl' => route('reception.bookings.edit', ['bookingId' => 9002]),
                        'showDelete' => false,
                    ])
                </td>
            </tr>
        </tbody>
    </table>
</x-receptionist.index-page>
