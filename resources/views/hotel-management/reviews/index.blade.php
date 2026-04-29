<x-hotel-management.index-page
    title="Quản lý đánh giá"
    subtitle="Danh sách quản lý đánh giá"
    :show-create-button="false"
>
    <x-slot:filters>
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
    </x-slot:filters>

    <table class="table table-striped align-middle">
        <thead><tr><th>Mã ĐG</th><th>Mã đặt phòng</th><th>Số sao</th><th>Mô tả</th><th>Ngày đánh giá</th><th style="min-width: 180px;">Thao tác</th></tr></thead>
        <tbody>
            <tr><td>1</td><td>9001</td><td>5 sao</td><td>Phòng sạch sẽ, nhân viên nhiệt tình.</td><td>06/04/2026</td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.reviews.show', ['recordId' => 1]), 'editUrl' => null, 'showDelete' => false])</td></tr>
            <tr><td>2</td><td>9002</td><td>4 sao</td><td>Dịch vụ tốt, bữa sáng đa dạng.</td><td>07/04/2026</td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.reviews.show', ['recordId' => 2]), 'editUrl' => null, 'showDelete' => false])</td></tr>
        </tbody>
    </table>
</x-hotel-management.index-page>
