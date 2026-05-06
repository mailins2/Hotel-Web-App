<x-receptionist.form-page
    :is-edit="true"
    :index-route="route('reception.invoices.index')"
>
    <div class="form-group col-md-6"><label class="form-label">Mã hóa đơn</label><input type="text" class="form-control hm-readonly-input" value="{{ request()->route('invoiceId') ?? 5001 }}" readonly></div>
    <div class="form-group col-md-6"><label class="form-label">Mã đặt phòng</label><input type="text" class="form-control hm-readonly-input" value="9001" readonly></div>
    <div class="form-group col-md-6"><label class="form-label">Khách hàng</label><input type="text" class="form-control hm-readonly-input" value="Nguyễn Minh An" readonly></div>
    <div class="form-group col-md-6"><label class="form-label">Nhân viên</label><input type="text" class="form-control hm-readonly-input" value="Phạm Thùy Linh" readonly></div>
    <div class="form-group col-md-4"><label class="form-label">Ngày lập</label><input type="text" class="form-control hm-readonly-input" value="08/04/2026" readonly></div>
    <div class="form-group col-md-4"><label class="form-label">Tổng tiền</label><input type="text" class="form-control hm-readonly-input" value="4.500.000 VNĐ" readonly></div>
    <div class="form-group col-md-4"><label class="form-label">Đã thanh toán</label><input type="text" class="form-control hm-readonly-input" value="1.500.000 VNĐ" readonly></div>
    <div class="form-group col-md-6"><label class="form-label">Còn lại</label><input type="text" class="form-control hm-readonly-input" value="3.000.000 VNĐ" readonly></div>
    <div class="form-group col-md-6">
        <label class="form-label">Trạng thái</label>
        <select class="form-select">
            <option selected>Chưa thanh toán</option>
            <option>Đã thanh toán</option>
        </select>
    </div>
</x-receptionist.form-page>
