<x-hotel-management.form-page
    :is-edit="request()->routeIs('hotel.promotions.edit')"
    :index-route="route('hotel.promotions.index')"
>
    <div class="form-group col-md-6"><label class="form-label">Mã khuyến mãi</label><input type="text" class="form-control hm-readonly-input" value="1" readonly></div>
    <div class="form-group col-md-6"><label class="form-label">Tên chương trình</label><input type="text" class="form-control" value="Summer Escape"></div>
    <div class="form-group col-md-12"><label class="form-label">Mô tả</label><textarea class="form-control" rows="3">Giảm giá cho khách đặt phòng trong mùa hè.</textarea></div>
    <div class="form-group col-md-4"><label class="form-label">Điểm yêu cầu</label><input type="number" class="form-control" value="50"></div>
    <div class="form-group col-md-4"><label class="form-label">Ngày bắt đầu</label><input type="date" class="form-control" value="2026-05-01"></div>
    <div class="form-group col-md-4"><label class="form-label">Ngày kết thúc</label><input type="date" class="form-control" value="2026-06-30"></div>
    <div class="form-group col-md-6"><label class="form-label">Phần trăm giảm giá</label><input type="number" class="form-control" value="15"></div>
</x-hotel-management.form-page>
