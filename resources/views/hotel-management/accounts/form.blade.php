<x-hotel-management.form-page
    :is-edit="request()->routeIs('hotel.accounts.edit')"
    :index-route="route('hotel.accounts.index')"
>
    <div class="form-group col-md-6"><label class="form-label">Mã tài khoản</label><input type="text" class="form-control hm-readonly-input" value="101" readonly></div>
    <div class="form-group col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" value="minhan@gmail.com"></div>
    <div class="form-group col-md-6"><label class="form-label">Mật khẩu</label><input type="password" class="form-control" value=""></div>
    <div class="form-group col-md-6"><label class="form-label">Loại tài khoản</label><select class="form-select"><option>Khách hàng</option><option>Lễ tân</option><option>Quản lý</option></select></div>
    <div class="form-group col-md-6"><label class="form-label">Trạng thái</label><select class="form-select"><option>Hoạt động</option><option>Không hoạt động</option></select></div>
</x-hotel-management.form-page>
