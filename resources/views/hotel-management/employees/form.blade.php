<x-hotel-management.form-page
    :is-edit="request()->routeIs('hotel.employees.edit')"
    :index-route="route('hotel.employees.index')"
>
    <div class="form-group col-md-6"><label class="form-label">Mã nhân viên</label><input type="text" class="form-control hm-readonly-input" value="1" readonly></div>
    <div class="form-group col-md-6"><label class="form-label">Tên nhân viên</label><input type="text" class="form-control" value="Phạm Thùy Linh"></div>
    <div class="form-group col-md-6"><label class="form-label">Mã tài khoản</label><input type="number" class="form-control" value="201"></div>
</x-hotel-management.form-page>
