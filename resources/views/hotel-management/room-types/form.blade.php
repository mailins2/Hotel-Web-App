<x-hotel-management.form-page
    :is-edit="request()->routeIs('hotel.room-types.edit')"
    :index-route="route('hotel.room-types.index')"
>
    <div class="form-group col-md-6"><label class="form-label">Mã loại phòng</label><input type="text" class="form-control hm-readonly-input" value="1" readonly></div>
    <div class="form-group col-md-6"><label class="form-label">Tên loại phòng</label><input type="text" class="form-control" value="Deluxe"></div>
    <div class="form-group col-md-12"><label class="form-label">Mô tả</label><textarea class="form-control" rows="3">Phòng tiêu chuẩn cao cấp, phù hợp cho khách đi công tác.</textarea></div>
    <div class="form-group col-md-6"><label class="form-label">Người lớn</label><input type="number" class="form-control" value="2" min="1"></div>
    <div class="form-group col-md-6"><label class="form-label">Trẻ em</label><input type="number" class="form-control" value="0" min="0"></div>
    <div class="form-group col-md-12"><label class="form-label">Ảnh phòng</label><input type="text" class="form-control" placeholder="Nhập đường dẫn ảnh minh họa"></div>
</x-hotel-management.form-page>
