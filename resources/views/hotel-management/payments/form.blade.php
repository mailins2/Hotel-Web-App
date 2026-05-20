<x-hotel-management.form-page
    :is-edit="request()->routeIs('hotel.payments.edit')"
    :index-route="route('hotel.payments.index')"
>
    <div class="form-group col-md-6"><label class="form-label">Mã thanh toán</label><input type="text" class="form-control hm-readonly-input" value="1" readonly disabled></div>
    <div class="form-group col-md-6"><label class="form-label">Mã hóa đơn</label><input type="number" class="form-control" value="5001"></div>
    <div class="form-group col-md-6"><label class="form-label">Số tiền</label><input type="number" class="form-control" value="1500000"></div>
    <div class="form-group col-md-6"><label class="form-label">Loại thanh toán</label><select class="form-select"><option>Đặt cọc</option><option>Thanh toán checkout</option></select></div>
</x-hotel-management.form-page>
