<x-app-layout :assets="['animation']">
    <style>
        .hm-service-page .hm-select-wrap {
            position: relative;
        }

        .hm-service-page .hm-select-wrap::after {
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

        .hm-service-page .hm-select-wrap .form-select,
        .hm-service-page .hm-select-wrap .form-control {
            padding-right: 2.5rem;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: none !important;
        }

        .hm-service-page .hm-filter-panel {
            padding: 1rem 1.1rem;
            border-radius: 16px;
            box-shadow: 0 12px 32px -24px rgba(111, 29, 1, 0.24);
            background: #fffaf6;
        }

        .hm-service-page .hm-service-card {
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 1rem;
            height: 100%;
            background: #fff;
        }

        .hm-service-page .hm-create-button {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
        }

        .hm-service-page .hm-create-button svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .hm-service-page .hm-action-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
    </style>

    <div class="row hm-service-page">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 pb-4">
                    <div class="header-title">
                        <h4 class="card-title mb-1">Dịch vụ giải trí</h4>
                        <p class="mb-0 text-muted">Danh sách dịch vụ thư giãn và trải nghiệm tại khách sạn.</p>
                    </div>

                    <a href="{{ route('hotel.services.create') }}" class="btn btn-primary btn-sm hm-create-button" style="padding: 10px;">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11 13.5C13.4853 13.5 15.5 11.4853 15.5 9C15.5 6.51472 13.4853 4.5 11 4.5C8.51472 4.5 6.5 6.51472 6.5 9C6.5 11.4853 8.51472 13.5 11 13.5Z" fill="currentColor" opacity="0.92"/>
                            <path d="M3.5 19.5C3.5 16.7386 6.18629 14.5 9.5 14.5H12.5C14.163 14.5 15.6681 15.063 16.7518 15.9721C15.6497 16.5803 14.9048 17.7537 14.9048 19.0952V19.5H3.5Z" fill="currentColor" opacity="0.92"/>
                            <path d="M18.5 14.5V22.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M14.5 18.5H22.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        Thêm mới
                    </a>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <div class="hm-filter-panel">
                            <form>
                                <div class="row g-2 align-items-end">
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
                                                <option selected>Dịch vụ giải trí</option>
                                            </select>
                                        </div>
                                    </div>
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

                    <div class="mb-4">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <a href="{{ route('hotel.services.index') }}" class="btn btn-sm btn-light" style="padding: 8px 14px;">Tất cả dịch vụ</a>
                            <a href="{{ route('hotel.services.food-and-beverage') }}" class="btn btn-sm btn-light" style="padding: 8px 14px;">Dịch vụ ăn uống</a>
                            <a href="{{ route('hotel.services.room-service') }}" class="btn btn-sm btn-light" style="padding: 8px 14px;">Dịch vụ phòng</a>
                            <a href="{{ route('hotel.services.entertainment') }}" class="btn btn-sm btn-primary" style="padding: 8px 14px;">Dịch vụ giải trí</a>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <h6 class="fw-semibold mb-1">Dịch vụ giải trí</h6>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <div class="hm-service-card">
                                <div class="fw-semibold">Golf</div>
                                <div class="small text-muted mt-1">1.000.000 VNĐ</div>
                                <div class="mt-3">
                                    <div class="d-flex align-items-center justify-content-center rounded border bg-light text-muted" style="height: 160px; border-style: dashed !important;">
                                        Khu vực ảnh dịch vụ
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <div class="hm-service-card">
                                <div class="fw-semibold">Spa</div>
                                <div class="small text-muted mt-1">300.000 VNĐ</div>
                                <div class="mt-3">
                                    <div class="d-flex align-items-center justify-content-center rounded border bg-light text-muted" style="height: 160px; border-style: dashed !important;">
                                        Khu vực ảnh dịch vụ
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <div class="hm-service-card">
                                <div class="fw-semibold">Bắn cung</div>
                                <div class="small text-muted mt-1">100.000 VNĐ</div>
                                <div class="mt-3">
                                    <div class="d-flex align-items-center justify-content-center rounded border bg-light text-muted" style="height: 160px; border-style: dashed !important;">
                                        Khu vực ảnh dịch vụ
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Mã dịch vụ</th>
                                    <th>Tên dịch vụ</th>
                                    <th>Giá dịch vụ</th>
                                    <th>Loại dịch vụ</th>
                                    <th style="min-width: 180px;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>8</td>
                                    <td>Spa</td>
                                    <td>850.000 VNĐ</td>
                                    <td>Dịch vụ giải trí</td>
                                    <td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.services.show', ['recordId' => 8]), 'editUrl' => route('hotel.services.edit', ['recordId' => 8]), 'showDelete' => true])</td>
                                </tr>
                                <tr>
                                    <td>9</td>
                                    <td>Golf</td>
                                    <td>1.250.000 VNĐ</td>
                                    <td>Dịch vụ giải trí</td>
                                    <td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.services.show', ['recordId' => 9]), 'editUrl' => route('hotel.services.edit', ['recordId' => 9]), 'showDelete' => true])</td>
                                </tr>
                                <tr>
                                    <td>11</td>
                                    <td>Phòng game</td>
                                    <td>150.000 VNĐ</td>
                                    <td>Dịch vụ giải trí</td>
                                    <td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.services.show', ['recordId' => 11]), 'editUrl' => route('hotel.services.edit', ['recordId' => 11]), 'showDelete' => true])</td>
                                </tr>
                            </tbody>
                        </table>
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
