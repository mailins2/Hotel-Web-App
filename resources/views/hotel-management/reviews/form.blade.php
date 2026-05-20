<x-hotel-management.form-page
    :is-edit="request()->routeIs('hotel.reviews.edit')"
    :index-route="route('hotel.reviews.index')"
>
    <div class="form-group col-md-6"><label class="form-label">Mã đánh giá</label><input type="text" class="form-control hm-readonly-input" value="1" readonly disabled></div>
    <div class="form-group col-md-6"><label class="form-label">Mã đặt phòng</label><input type="number" class="form-control" value="9001"></div>
    <div class="form-group col-md-6"><label class="form-label">Số sao</label><select class="form-select"><option>5 sao</option><option>4 sao</option><option>3 sao</option></select></div>
    <div class="form-group col-md-12"><label class="form-label">Nội dung đánh giá</label><textarea class="form-control" rows="3">Phòng sạch sẽ, nhân viên hỗ trợ nhiệt tình.</textarea></div>
</x-hotel-management.form-page>
