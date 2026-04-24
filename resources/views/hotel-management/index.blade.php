@php
    $moduleKey = request()->route('moduleKey');
    $isReadOnlyModule = in_array($moduleKey, ['invoices', 'payments', 'reviews'], true);
@endphp

<x-app-layout :assets="['animation']">
    <style>
        .hm-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.38rem 0.78rem;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 700;
        }

        .hm-badge--success { background: #dcfce7; color: #166534; }
        .hm-badge--warning { background: #fef3c7; color: #9a3412; }
        .hm-badge--muted { background: #eceff3; color: #475569; }
        .hm-badge--danger { background: #fee2e2; color: #b91c1c; }
        .hm-badge--info { background: #dbeafe; color: #1d4ed8; }

        .hm-create-button {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
        }

        .hm-create-button svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .hm-select-wrap {
            position: relative;
        }

        .hm-select-wrap::after {
            content: '';
            position: absolute;
            top: 50%;
            right: 14px;
            width: 10px;
            height: 6px;
            pointer-events: none;
            transform: translateY(-50%);
            background-repeat: no-repeat;
            background-size: 10px 6px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6' fill='none'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%2364748B' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        }

        .hm-select-wrap .form-select,
        .hm-select-wrap .form-control {
            padding-right: 2.5rem;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: none !important;
        }

        .hm-filter-panel {
            padding: 1rem 1.1rem;
            border-radius: 16px;
            box-shadow: 0 12px 32px -24px rgba(111, 29, 1, 0.24);
            background: #fffaf6;
            /* border: 1px solid rgba(166, 98, 43, 0.12); */
        }

        .hm-action-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .hm-service-card {
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 1rem;
            height: 100%;
            background: #fff;
        }
    </style>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 pb-4">
                    <div class="header-title">
                        @switch($moduleKey)
                            @case('accounts')
                                <h4 class="card-title mb-1">Quản lý tài khoản</h4>
                                <p class="mb-0 text-muted">Danh sách quản lý tài khoản</p>
                                @break
                            @case('customers')
                                <h4 class="card-title mb-1">Quản lý khách hàng</h4>
                                <p class="mb-0 text-muted">Danh sách quản lý khách hàng tại khách sạn</p>
                                @break
                            @case('employees')
                                <h4 class="card-title mb-1">Quản lý nhân viên</h4>
                                <p class="mb-0 text-muted">Danh sách quản lý nhân viên tại khách sạn</p>
                                @break
                            @case('room-types')
                                <h4 class="card-title mb-1">Quản lý loại phòng</h4>
                                <p class="mb-0 text-muted">Danh sách quản lý các loại phòng tại khách sạn</p>
                                @break
                            @case('rooms')
                                <h4 class="card-title mb-1">Quản lý phòng</h4>
                                <p class="mb-0 text-muted">Danh sách quản lý phòng tại khách sạn</p>
                                @break
                            @case('services')
                                <h4 class="card-title mb-1">Quản lý dịch vụ</h4>
                                <p class="mb-0 text-muted">Danh sách quản lý dịch vụ tại khách sạn</p>
                                @break
                            @case('promotions')
                                <h4 class="card-title mb-1">Quản lý khuyến mãi</h4>
                                <p class="mb-0 text-muted">Danh sách chương trình khuyến mãi tại khách sạn</p>
                                @break
                            @case('invoices')
                                <h4 class="card-title mb-1">Quản lý hóa đơn</h4>
                                <p class="mb-0 text-muted">Danh sách quản lý hóa đơn</p>
                                @break
                            @case('payments')
                                <h4 class="card-title mb-1">Quản lý thanh toán</h4>
                                <p class="mb-0 text-muted">Danh sách quản lý thanh toán</p>
                                @break
                            @case('reviews')
                                <h4 class="card-title mb-1">Quản lý đánh giá</h4>
                                <p class="mb-0 text-muted">Danh sách quản lý đánh giá</p>
                                @break
                            @default
                                <h4 class="card-title mb-1">Quản lý dữ liệu</h4>
                                <p class="mb-0 text-muted">Trang danh sách đang hiển thị tĩnh theo module.</p>
                        @endswitch
                    </div>

                    @unless($isReadOnlyModule)
                        <a href="{{ route('hotel.modules.create', ['moduleKey' => $moduleKey]) }}" class="btn btn-primary btn-sm hm-create-button" style="padding: 10px;">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11 13.5C13.4853 13.5 15.5 11.4853 15.5 9C15.5 6.51472 13.4853 4.5 11 4.5C8.51472 4.5 6.5 6.51472 6.5 9C6.5 11.4853 8.51472 13.5 11 13.5Z" fill="currentColor" opacity="0.92"/>
                                <path d="M3.5 19.5C3.5 16.7386 6.18629 14.5 9.5 14.5H12.5C14.163 14.5 15.6681 15.063 16.7518 15.9721C15.6497 16.5803 14.9048 17.7537 14.9048 19.0952V19.5H3.5Z" fill="currentColor" opacity="0.92"/>
                                <path d="M18.5 14.5V22.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M14.5 18.5H22.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            Thêm mới
                        </a>
                    @endunless
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <div class="hm-filter-panel">
                            <form>
                                <div class="row g-2 align-items-end">
                                    @if($moduleKey === 'customers')
                                        <div class="col-md-3">
                                            <label class="form-label">Tìm nhanh</label>
                                            <input type="text" class="form-control" placeholder="Tên khách, CCCD, số điện thoại...">
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
                                    @elseif($moduleKey === 'rooms')
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
                                    @elseif($moduleKey === 'services')
                                        <div class="col-md-3">
                                            <label class="form-label">Tên dịch vụ</label>
                                            <input type="text" class="form-control" placeholder="Tìm theo tên dịch vụ">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Nhóm dịch vụ</label>
                                            <div class="hm-select-wrap">
                                                <select class="form-select">
                                                    <option>Tất cả nhóm dịch vụ</option>
                                                    <option>Dịch vụ ăn uống</option>
                                                    <option>Dịch vụ phòng</option>
                                                    <option>Dịch vụ giải trí</option>
                                                </select>
                                            </div>
                                        </div>
                                    @elseif($moduleKey === 'promotions')
                                        <div class="col-md-3">
                                            <label class="form-label">Ngày bắt đầu từ</label>
                                            <input type="date" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Ngày kết thúc đến</label>
                                            <input type="date" class="form-control">
                                        </div>
                                    @elseif($moduleKey === 'invoices')
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
                                    @elseif($moduleKey === 'payments')
                                        <div class="col-md-3">
                                            <label class="form-label">Loại thanh toán</label>
                                            <div class="hm-select-wrap">
                                                <select class="form-select">
                                                    <option>Tất cả loại thanh toán</option>
                                                    <option>Đặt cọc</option>
                                                    <option>Thanh toán checkout</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Trạng thái hóa đơn</label>
                                            <div class="hm-select-wrap">
                                                <select class="form-select">
                                                    <option>Tất cả trạng thái</option>
                                                    <option>Chưa thanh toán</option>
                                                    <option>Đã thanh toán</option>
                                                </select>
                                            </div>
                                        </div>
                                    @elseif($moduleKey === 'reviews')
                                        <div class="col-md-3">
                                            <label class="form-label">Từ ngày</label>
                                            <input type="date" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Đến ngày</label>
                                            <input type="date" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Số sao</label>
                                            <div class="hm-select-wrap">
                                                <select class="form-select">
                                                    <option>Tất cả số sao</option>
                                                    <option>5 sao</option>
                                                    <option>4 sao</option>
                                                    <option>3 sao</option>
                                                </select>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-md-3">
                                            <label class="form-label">Từ khóa</label>
                                            <input type="text" class="form-control" placeholder="Tìm kiếm theo nội dung hiển thị">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Bộ lọc</label>
                                            <div class="hm-select-wrap">
                                                <select class="form-select">
                                                    <option>Tất cả</option>
                                                    <option>Mục đang hoạt động</option>
                                                    <option>Mục cần theo dõi</option>
                                                </select>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-md-auto">
                                        <div class="d-flex justify-content-start gap-2 w-100">
                                            <button type="button" class="btn btn-primary" style="padding: 10px 18px; white-space: nowrap;">Áp dụng</button>
                                            <button type="button" class="btn btn-light btn-sm" style="padding: 10px 18px; white-space: nowrap;">Đặt lại</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if($moduleKey === 'services')
                        <div class="mb-4">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <a href="#" class="btn btn-sm btn-primary" style="padding: 8px 14px;">Tất cả dịch vụ</a>
                                <a href="#" class="btn btn-sm btn-light" style="padding: 8px 14px;">Dịch vụ ăn uống</a>
                                <a href="#" class="btn btn-sm btn-light" style="padding: 8px 14px;">Dịch vụ phòng</a>
                                <a href="#" class="btn btn-sm btn-light" style="padding: 8px 14px;">Dịch vụ giải trí</a>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <h6 class="fw-semibold mb-1">Dịch vụ ăn uống</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="hm-service-card">
                                    <div class="fw-semibold">Bánh mì</div>
                                    <div class="small text-muted mt-1">35.000 VNĐ</div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="hm-service-card">
                                    <div class="fw-semibold">Cơm chiên</div>
                                    <div class="small text-muted mt-1">60.000 VNĐ</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <h6 class="fw-semibold mb-1">Dịch vụ phòng</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="hm-service-card">
                                    <div class="fw-semibold">Giặt ủi</div>
                                    <div class="small text-muted mt-1">120.000 VNĐ</div>
                                    <div class="mt-3">
                                        <div class="d-flex align-items-center justify-content-center rounded border bg-light text-muted" style="height: 160px; border-style: dashed !important;">
                                            Khu vực ảnh dịch vụ
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="hm-service-card">
                                    <div class="fw-semibold">Vệ sinh</div>
                                    <div class="small text-muted mt-1">90.000 VNĐ</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <h6 class="fw-semibold mb-1">Dịch vụ giải trí</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="hm-service-card">
                                    <div class="fw-semibold">Spa</div>
                                    <div class="small text-muted mt-1">850.000 VNĐ</div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="hm-service-card">
                                    <div class="fw-semibold">Golf</div>
                                    <div class="small text-muted mt-1">1.250.000 VNĐ</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="table-responsive">
                        @switch($moduleKey)
                            @case('accounts')
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>Mã TK</th>
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
                                            <td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.modules.show', ['moduleKey' => 'accounts', 'recordId' => 101]), 'editUrl' => route('hotel.modules.edit', ['moduleKey' => 'accounts', 'recordId' => 101]), 'showDelete' => true])</td>
                                        </tr>
                                        <tr>
                                            <td>201</td>
                                            <td>letan01@peachvalley.vn</td>
                                            <td>Phạm Thùy Linh</td>
                                            <td>Lễ tân</td>
                                            <td><span class="hm-badge hm-badge--success">Hoạt động</span></td>
                                            <td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.modules.show', ['moduleKey' => 'accounts', 'recordId' => 201]), 'editUrl' => route('hotel.modules.edit', ['moduleKey' => 'accounts', 'recordId' => 201]), 'showDelete' => true])</td>
                                        </tr>
                                    </tbody>
                                </table>
                                @break

                            @case('customers')
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>Mã KH</th>
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
                                            <td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.modules.show', ['moduleKey' => 'customers', 'recordId' => 1]), 'editUrl' => route('hotel.modules.edit', ['moduleKey' => 'customers', 'recordId' => 1]), 'showDelete' => true])</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Trần Bảo Ngọc</td>
                                            <td>24/08/2000</td>
                                            <td>Nữ</td>
                                            <td>80</td>
                                            <td><span class="hm-badge hm-badge--muted">Không hoạt động</span></td>
                                            <td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.modules.show', ['moduleKey' => 'customers', 'recordId' => 2]), 'editUrl' => route('hotel.modules.edit', ['moduleKey' => 'customers', 'recordId' => 2]), 'showDelete' => true])</td>
                                        </tr>
                                    </tbody>
                                </table>
                                @break

                            @case('employees')
                                <table class="table table-striped align-middle">
                                    <thead><tr><th>Mã NV</th><th>Tên nhân viên</th><th>Mã TK</th><th style="min-width: 180px;">Thao tác</th></tr></thead>
                                    <tbody>
                                        <tr><td>1</td><td>Phạm Thùy Linh</td><td>201</td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.modules.show', ['moduleKey' => 'employees', 'recordId' => 1]), 'editUrl' => route('hotel.modules.edit', ['moduleKey' => 'employees', 'recordId' => 1]), 'showDelete' => true])</td></tr>
                                        <tr><td>2</td><td>Hoàng Gia Bảo</td><td>202</td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.modules.show', ['moduleKey' => 'employees', 'recordId' => 2]), 'editUrl' => route('hotel.modules.edit', ['moduleKey' => 'employees', 'recordId' => 2]), 'showDelete' => true])</td></tr>
                                    </tbody>
                                </table>
                                @break

                            @case('room-types')
                                <table class="table table-striped align-middle">
                                    <thead><tr><th>Mã loại</th><th>Tên loại phòng</th><th>Mô tả</th><th>Số người tối đa</th><th>Ảnh phòng</th><th style="min-width: 180px;">Thao tác</th></tr></thead>
                                    <tbody>
                                        <tr><td>1</td><td>Deluxe</td><td>Phòng tiêu chuẩn cao cấp</td><td>2</td><td>dấdada</td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.modules.show', ['moduleKey' => 'room-types', 'recordId' => 1]), 'editUrl' => route('hotel.modules.edit', ['moduleKey' => 'room-types', 'recordId' => 1]), 'showDelete' => true])</td></tr>
                                        <tr><td>2</td><td>Suite</td><td>Không gian rộng rãi</td><td>4</td><td>dấdada</td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.modules.show', ['moduleKey' => 'room-types', 'recordId' => 2]), 'editUrl' => route('hotel.modules.edit', ['moduleKey' => 'room-types', 'recordId' => 2]), 'showDelete' => true])</td></tr>
                                    </tbody>
                                </table>
                                @break

                            @case('rooms')
                                <table class="table table-striped align-middle">
                                    <thead><tr><th>Mã phòng</th><th>Số phòng</th><th>Tên loại phòng</th><th>Sức chứa</th><th>Tình trạng</th><th style="min-width: 180px;">Thao tác</th></tr></thead>
                                    <tbody>
                                        <tr><td>1</td><td>A101</td><td>Deluxe</td><td>2</td><td><span class="hm-badge hm-badge--success">Trống</span></td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.modules.show', ['moduleKey' => 'rooms', 'recordId' => 1]), 'editUrl' => route('hotel.modules.edit', ['moduleKey' => 'rooms', 'recordId' => 1]), 'showDelete' => true])</td></tr>
                                        <tr><td>2</td><td>A102</td><td>Suite</td><td>4</td><td><span class="hm-badge hm-badge--info">Đang sử dụng</span></td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.modules.show', ['moduleKey' => 'rooms', 'recordId' => 2]), 'editUrl' => route('hotel.modules.edit', ['moduleKey' => 'rooms', 'recordId' => 2]), 'showDelete' => true])</td></tr>
                                    </tbody>
                                </table>
                                @break

                            @case('services')
                                <table class="table table-striped align-middle">
                                    <thead><tr><th>Mã DV</th><th>Tên dịch vụ</th><th>Giá dịch vụ</th><th>Loại dịch vụ</th><th style="min-width: 180px;">Thao tác</th></tr></thead>
                                    <tbody>
                                        <tr><td>1</td><td>Bánh mì</td><td>35.000 VNĐ</td><td>Dịch vụ ăn uống</td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.modules.show', ['moduleKey' => 'services', 'recordId' => 1]), 'editUrl' => route('hotel.modules.edit', ['moduleKey' => 'services', 'recordId' => 1]), 'showDelete' => true])</td></tr>
                                        <tr><td>6</td><td>Giặt ủi</td><td>120.000 VNĐ</td><td>Dịch vụ phòng</td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.modules.show', ['moduleKey' => 'services', 'recordId' => 6]), 'editUrl' => route('hotel.modules.edit', ['moduleKey' => 'services', 'recordId' => 6]), 'showDelete' => true])</td></tr>
                                    </tbody>
                                </table>
                                @break

                            @case('promotions')
                                <table class="table table-striped align-middle">
                                    <thead><tr><th>Mã KM</th><th>Tên KM</th><th>Điểm</th><th>Ngày bắt đầu</th><th>Ngày kết thúc</th><th>% giảm</th><th style="min-width: 180px;">Thao tác</th></tr></thead>
                                    <tbody>
                                        <tr><td>1</td><td>Summer Escape</td><td>50</td><td>01/05/2026</td><td>30/06/2026</td><td>15%</td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.modules.show', ['moduleKey' => 'promotions', 'recordId' => 1]), 'editUrl' => route('hotel.modules.edit', ['moduleKey' => 'promotions', 'recordId' => 1]), 'showDelete' => true])</td></tr>
                                        <tr><td>2</td><td>Stay Longer</td><td>80</td><td>15/04/2026</td><td>31/07/2026</td><td>20%</td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.modules.show', ['moduleKey' => 'promotions', 'recordId' => 2]), 'editUrl' => route('hotel.modules.edit', ['moduleKey' => 'promotions', 'recordId' => 2]), 'showDelete' => true])</td></tr>
                                    </tbody>
                                </table>
                                @break

                            @case('invoices')
                                <table class="table table-striped align-middle">
                                    <thead><tr><th>Mã HĐ</th><th>Ngày lập</th><th>Tên NV</th><th>Tổng tiền</th><th>Đã thanh toán</th><th>Trạng thái</th><th style="min-width: 180px;">Thao tác</th></tr></thead>
                                    <tbody>
                                        <tr><td>5001</td><td>08/04/2026</td><td>Phạm Thùy Linh</td><td>4.500.000 VNĐ</td><td>1.500.000 VNĐ</td><td><span class="hm-badge hm-badge--warning">Chưa thanh toán</span></td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.modules.show', ['moduleKey' => 'invoices', 'recordId' => 5001]), 'editUrl' => null, 'showDelete' => false])</td></tr>
                                        <tr><td>5002</td><td>07/04/2026</td><td>Hoàng Gia Bảo</td><td>3.250.000 VNĐ</td><td>3.250.000 VNĐ</td><td><span class="hm-badge hm-badge--success">Đã thanh toán</span></td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.modules.show', ['moduleKey' => 'invoices', 'recordId' => 5002]), 'editUrl' => null, 'showDelete' => false])</td></tr>
                                    </tbody>
                                </table>
                                @break

                            @case('payments')
                                <table class="table table-striped align-middle">
                                    <thead><tr><th>Mã TT</th><th>Mã đặt phòng</th><th>Người thanh toán</th><th>Số tiền</th><th>Loại thanh toán</th><th>Ngày thanh toán</th><th>Trạng thái hóa đơn</th><th style="min-width: 180px;">Thao tác</th></tr></thead>
                                    <tbody>
                                        <tr><td>1</td><td>4</td><td>Nguyễn Minh An</td><td>1.500.000 VNĐ</td><td>Đặt cọc</td><td>08/04/2026 10:30</td><td><span class="hm-badge hm-badge--warning">Chưa thanh toán</span></td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.modules.show', ['moduleKey' => 'payments', 'recordId' => 1]), 'editUrl' => null, 'showDelete' => false])</td></tr>
                                        <tr><td>2</td><td>5</td><td>Trần Bảo Ngọc</td><td>3.250.000 VNĐ</td><td>Thanh toán checkout</td><td>07/04/2026 14:15</td><td><span class="hm-badge hm-badge--success">Đã thanh toán</span></td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.modules.show', ['moduleKey' => 'payments', 'recordId' => 2]), 'editUrl' => null, 'showDelete' => false])</td></tr>
                                    </tbody>
                                </table>
                                @break

                            @case('reviews')
                                <table class="table table-striped align-middle">
                                    <thead><tr><th>Mã ĐG</th><th>Mã đặt phòng</th><th>Số sao</th><th>Mô tả</th><th>Ngày đánh giá</th><th style="min-width: 180px;">Thao tác</th></tr></thead>
                                    <tbody>
                                        <tr><td>1</td><td>9001</td><td>5 sao</td><td>Phòng sạch sẽ, nhân viên nhiệt tình.</td><td>06/04/2026</td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.modules.show', ['moduleKey' => 'reviews', 'recordId' => 1]), 'editUrl' => null, 'showDelete' => false])</td></tr>
                                        <tr><td>2</td><td>9002</td><td>4 sao</td><td>Dịch vụ tốt, bữa sáng đa dạng.</td><td>07/04/2026</td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.modules.show', ['moduleKey' => 'reviews', 'recordId' => 2]), 'editUrl' => null, 'showDelete' => false])</td></tr>
                                    </tbody>
                                </table>
                                @break

                            @default
                                <div class="text-muted py-4">Không có cấu hình giao diện cho module này.</div>
                        @endswitch
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.js-confirm-delete').forEach(function (formElement) {
                    formElement.addEventListener('submit', function (event) {
                        event.preventDefault();
                        window.confirm('Đây là giao diện tĩnh, chưa có thao tác xóa thật.');
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
