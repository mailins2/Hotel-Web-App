<x-hotel-management.form-page
    :is-edit="request()->routeIs('hotel.invoices.edit')"
    :index-route="route('hotel.invoices.index')"
>
    <div class="form-group col-md-6"><label class="form-label">Mã hóa đơn</label><input type="text" class="form-control hm-readonly-input" value="5001" readonly></div>
    <div class="form-group col-md-6"><label class="form-label">Mã đặt phòng</label><input type="text" class="form-control hm-readonly-input" value="9001" readonly></div>
    <div class="form-group col-md-6"><label class="form-label">Ngày lập</label><input type="date" class="form-control hm-readonly-input" value="2026-04-08" readonly></div>
    <div class="form-group col-md-6"><label class="form-label">Tổng tiền</label><input type="text" class="form-control hm-readonly-input" value="4.500.000 VNĐ" readonly></div>
</x-hotel-management.form-page>
