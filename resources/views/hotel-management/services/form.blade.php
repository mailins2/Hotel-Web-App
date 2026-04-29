<x-hotel-management.form-page
    :is-edit="request()->routeIs('hotel.services.edit')"
    :index-route="route('hotel.services.index')"
>
    <div class="form-group col-md-6"><label class="form-label">Mã dịch vụ</label><input type="text" class="form-control hm-readonly-input" value="6" readonly></div>
    <div class="form-group col-md-6"><label class="form-label">Tên dịch vụ</label><input type="text" class="form-control" value="Giặt ủi"></div>
    <div class="form-group col-md-6"><label class="form-label">Giá dịch vụ</label><input type="number" class="form-control" value="120000"></div>
    <div class="form-group col-md-6"><label class="form-label">Loại dịch vụ</label><select class="form-select"><option>Ăn uống</option><option>Dịch vụ phòng</option><option>Giải trí</option></select></div>
    <div class="form-group col-md-12"><label class="form-label">Ảnh dịch vụ</label><input type="text" class="form-control" placeholder="Nhập đường dẫn ảnh minh họa"></div>
</x-hotel-management.form-page>
