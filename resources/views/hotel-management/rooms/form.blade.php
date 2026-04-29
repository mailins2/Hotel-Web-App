<x-hotel-management.form-page
    :is-edit="request()->routeIs('hotel.rooms.edit')"
    :index-route="route('hotel.rooms.index')"
>
    <div class="form-group col-md-6"><label class="form-label">Mã phòng</label><input type="text" class="form-control hm-readonly-input" value="1" readonly></div>
    <div class="form-group col-md-6"><label class="form-label">Số phòng</label><input type="text" class="form-control" value="A101"></div>
    <div class="form-group col-md-6"><label class="form-label">Mã loại phòng</label><input type="number" class="form-control" value="1"></div>
     <div class="form-group col-md-6"><label class="form-label">Loại phòng</label><select class="form-select"><option>Deluxe</option><option>Family</option><option>Junior</option><option>Deluxe Family</option></select></div>
    <div class="form-group col-md-6"><label class="form-label">Tình trạng</label><select class="form-select"><option>Trống</option><option>Đã đặt</option><option>Đang sử dụng</option><option>Đang dọn dẹp</option></select></div>
</x-hotel-management.form-page>
