<x-receptionist.index-page
    title="Quản lý khách hàng"
    subtitle="Danh sách khách hàng"
    :create-route="route('reception.customers.create')"
    create-label="Thêm khách hàng"
    table-title="Danh sách thông tin khách hàng"
>
    <x-slot:filters>
        <div class="col-md-3">
            <label class="form-label">Tìm kiếm</label>
            <input type="text" class="form-control" placeholder="Tìm kiếm...">
        </div>
        <div class="col-md-3">
            <label class="form-label">Trạng thái</label>
            <div class="rd-select-wrap">
                <select class="form-select">
                    <option>Tất cả trạng thái</option>
                    <option>Hoạt động</option>
                    <option>Không hoạt động</option>
                </select>
            </div>
        </div>
    </x-slot:filters>

    <table class="table align-middle">
        <thead>
            <tr>
                <th>Mã khách hàng</th>
                <th>Tên khách hàng</th>
                <th>CCCD</th>
                <th>Ngày sinh</th>
                <th>Giới tính</th>
                <!-- <th>Điểm</th> -->
                <th>Trạng thái</th>
                <th style="min-width: 180px;">Thao tác</th>
                <th ></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Nguyễn Minh An</td>
                <td>079204000111</td>
                <td>12/04/1998</td>
                <td>Nam</td>
                <!-- <td>120</td> -->
                <td><span class="rd-badge rd-badge--success">Hoạt động</span></td>
                <td>
                    @include('hotel-management.partials.action-icons', [
                        'showUrl' => route('reception.customers.show', ['customerId' => 1]),
                        'editUrl' => route('reception.customers.edit', ['customerId' => 1]),
                        'showDelete' => false,
                    ])
                </td>
                <td>
                    <div class="dropdown">
                        <button
                            class="btn btn-light btn-sm border rounded-circle p-0"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            style="width: 30px; height: 30px;"
                        >
                            <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-muted">
                                <path d="M12 6.75C12.6904 6.75 13.25 6.19036 13.25 5.5C13.25 4.80964 12.6904 4.25 12 4.25C11.3096 4.25 10.75 4.80964 10.75 5.5C10.75 6.19036 11.3096 6.75 12 6.75Z" fill="currentColor"/>
                                <path d="M12 13.25C12.6904 13.25 13.25 12.6904 13.25 12C13.25 11.3096 12.6904 10.75 12 10.75C11.3096 10.75 10.75 11.3096 10.75 12C10.75 12.6904 11.3096 13.25 12 13.25Z" fill="currentColor"/>
                                <path d="M12 19.75C12.6904 19.75 13.25 19.1904 13.25 18.5C13.25 17.8096 12.6904 17.25 12 17.25C11.3096 17.25 10.75 17.8096 10.75 18.5C10.75 19.1904 11.3096 19.75 12 19.75Z" fill="currentColor"/>
                            </svg>
                        </button>
                        <ul class="dropdown-menu shadow-sm border-0">
                            <li>
                                <a href="{{ route('reception.bookings.create') }}" class="dropdown-item">Đặt phòng</a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td>Trần Bảo Ngọc</td>
                <td>048204000222</td>
                <td>24/08/2000</td>
                <td>Nữ</td>
                <!-- <td>80</td> -->
                <td><span class="rd-badge rd-badge--muted">Không hoạt động</span></td>
                <td>
                    @include('hotel-management.partials.action-icons', [
                        'showUrl' => route('reception.customers.show', ['customerId' => 2]),
                        'editUrl' => route('reception.customers.edit', ['customerId' => 2]),
                        'showDelete' => false,
                    ])
                </td>
                <td>
                    <div class="dropdown">
                        <button
                            class="btn btn-light btn-sm border rounded-circle p-0"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            style="width: 30px; height: 30px;"
                        >
                            <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-muted">
                                <path d="M12 6.75C12.6904 6.75 13.25 6.19036 13.25 5.5C13.25 4.80964 12.6904 4.25 12 4.25C11.3096 4.25 10.75 4.80964 10.75 5.5C10.75 6.19036 11.3096 6.75 12 6.75Z" fill="currentColor"/>
                                <path d="M12 13.25C12.6904 13.25 13.25 12.6904 13.25 12C13.25 11.3096 12.6904 10.75 12 10.75C11.3096 10.75 10.75 11.3096 10.75 12C10.75 12.6904 11.3096 13.25 12 13.25Z" fill="currentColor"/>
                                <path d="M12 19.75C12.6904 19.75 13.25 19.1904 13.25 18.5C13.25 17.8096 12.6904 17.25 12 17.25C11.3096 17.25 10.75 17.8096 10.75 18.5C10.75 19.1904 11.3096 19.75 12 19.75Z" fill="currentColor"/>
                            </svg>
                        </button>
                        <ul class="dropdown-menu shadow-sm border-0">
                            <li>
                                <a href="{{ route('reception.bookings.create') }}" class="dropdown-item">Đặt phòng</a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</x-receptionist.index-page>
