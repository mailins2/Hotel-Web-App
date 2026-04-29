<x-hotel-management.index-page
    title="Quản lý dịch vụ"
    subtitle="Danh sách quản lý dịch vụ tại khách sạn"
    :create-route="route('hotel.services.create')"
>
    <x-slot:filters>
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
    </x-slot:filters>

    <x-slot:beforeTable>
        <div class="mb-4">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="#" class="btn btn-sm btn-primary" style="padding: 8px 14px;">Tất cả dịch vụ</a>
                <a href="{{ route('hotel.services.food-and-beverage') }}" class="btn btn-sm btn-light" style="padding: 8px 14px;">Dịch vụ ăn uống</a>
                <a href="{{ route('hotel.services.room-service') }}" class="btn btn-sm btn-light" style="padding: 8px 14px;">Dịch vụ phòng</a>
                <a href="{{ route('hotel.services.entertainment') }}" class="btn btn-sm btn-light" style="padding: 8px 14px;">Dịch vụ giải trí</a>
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
                     <div class="mt-3">
                        <div class="d-flex align-items-center justify-content-center rounded border bg-light text-muted" style="height: 160px; border-style: dashed !important;">
                            Khu vực ảnh dịch vụ
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="hm-service-card">
                    <div class="fw-semibold">Cơm chiên</div>
                    <div class="small text-muted mt-1">60.000 VNĐ</div>
                     <div class="mt-3">
                        <div class="d-flex align-items-center justify-content-center rounded border bg-light text-muted" style="height: 160px; border-style: dashed !important;">
                            Khu vực ảnh dịch vụ
                        </div>
                    </div>
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
                     <div class="mt-3">
                        <div class="d-flex align-items-center justify-content-center rounded border bg-light text-muted" style="height: 160px; border-style: dashed !important;">
                            Khu vực ảnh dịch vụ
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <h6 class="fw-semibold mb-1">Dịch vụ giải trí</h6>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="hm-service-card">
                    <div class="fw-semibold">Spa</div>
                    <div class="small text-muted mt-1">850.000 VNĐ</div>
                     <div class="mt-3">
                        <div class="d-flex align-items-center justify-content-center rounded border bg-light text-muted" style="height: 160px; border-style: dashed !important;">
                            Khu vực ảnh dịch vụ
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="hm-service-card">
                    <div class="fw-semibold">Golf</div>
                    <div class="small text-muted mt-1">1.250.000 VNĐ</div>
                    <div class="mt-3">
                        <div class="d-flex align-items-center justify-content-center rounded border bg-light text-muted" style="height: 160px; border-style: dashed !important;">
                            Khu vực ảnh dịch vụ
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:beforeTable>

    <table class="table table-striped align-middle">
        <thead><tr><th>Mã dịch vụ</th><th>Tên dịch vụ</th><th>Giá dịch vụ</th><th>Loại dịch vụ</th><th style="min-width: 180px;">Thao tác</th></tr></thead>
        <tbody>
            <tr><td>1</td><td>Bánh mì</td><td>35.000 VNĐ</td><td>Dịch vụ ăn uống</td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.services.show', ['recordId' => 1]), 'editUrl' => route('hotel.services.edit', ['recordId' => 1]), 'showDelete' => true])</td></tr>
            <tr><td>6</td><td>Giặt ủi</td><td>120.000 VNĐ</td><td>Dịch vụ phòng</td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.services.show', ['recordId' => 6]), 'editUrl' => route('hotel.services.edit', ['recordId' => 6]), 'showDelete' => true])</td></tr>
        </tbody>
    </table>
</x-hotel-management.index-page>
