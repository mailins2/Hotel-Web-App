<x-hotel-management.index-page
    title="Quản lý khuyến mãi"
    subtitle="Danh sách chương trình khuyến mãi tại khách sạn"
    :create-route="route('hotel.promotions.create')"
>
    <x-slot:filters>
        <div class="col-md-3">
            <label class="form-label">Ngày bắt đầu từ</label>
            <input type="date" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Ngày kết thúc đến</label>
            <input type="date" class="form-control">
        </div>
    </x-slot:filters>

    <table class="table table-striped align-middle">
        <thead><tr><th>Mã khuyế mãi</th><th>Tên khuyến mãi</th><th>Điểm</th><th>Ngày bắt đầu</th><th>Ngày kết thúc</th><th>Giảm giá</th><th style="min-width: 180px;">Thao tác</th></tr></thead>
        <tbody>
            <tr><td>1</td><td>Summer Escape</td><td>50</td><td>01/05/2026</td><td>30/06/2026</td><td>15%</td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.promotions.show', ['recordId' => 1]), 'editUrl' => route('hotel.promotions.edit', ['recordId' => 1]), 'showDelete' => true])</td></tr>
            <tr><td>2</td><td>Stay Longer</td><td>80</td><td>15/04/2026</td><td>31/07/2026</td><td>20%</td><td>@include('hotel-management.partials.action-icons', ['showUrl' => route('hotel.promotions.show', ['recordId' => 2]), 'editUrl' => route('hotel.promotions.edit', ['recordId' => 2]), 'showDelete' => true])</td></tr>
        </tbody>
    </table>
</x-hotel-management.index-page>
