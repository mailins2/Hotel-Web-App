<x-receptionist.form-page
    :is-edit="request()->routeIs('reception.customers.edit')"
    :index-route="route('reception.customers.index')"
>
    <div class="form-group col-md-6"><label class="form-label">Mã khách hàng</label><input type="text" class="form-control hm-readonly-input" value="1" readonly></div>
    <div class="form-group col-md-6"><label class="form-label">Tên khách hàng</label><input type="text" class="form-control" value="Nguyễn Minh An"></div>
    <div class="form-group col-md-6"><label class="form-label">Ngày sinh</label><input type="date" class="form-control" value="1998-04-12"></div>
    <div class="form-group col-md-6"><label class="form-label">Giới tính</label><select class="form-select"><option>Nam</option><option>Nữ</option><option>Khác</option></select></div>
    <div class="form-group col-md-6"><label class="form-label">Số điện thoại</label><input type="text" class="form-control" value="0901234567"></div>
    <div class="form-group col-md-6"><label class="form-label">CCCD</label><input type="text" class="form-control" value="079204000111"></div>
    <div class="form-group col-md-6"><label class="form-label">Tỉnh/Thành phố</label><select class="form-select"><option>TPHCM</option><option>Bình Dương</option></select></div>
    <div class="form-group col-md-6"><label class="form-label">Quận/Huyện</label><select class="form-select"><option>Quận 1</option><option>Bến Cát</option></select></div>
    <div class="form-group col-md-12"><label class="form-label">Số nhà, đường</label><input type="text" class="form-control" value="12 Nguyễn Huệ"></div>
    <div class="form-group col-md-6"><label class="form-label">Điểm tích lũy</label><input type="number" class="form-control" value="120"></div>
    <div class="form-group col-md-6"><label class="form-label">Trạng thái</label><select class="form-select"><option>Hoạt động</option><option>Không hoạt động</option></select></div>
</x-receptionist.form-page>
