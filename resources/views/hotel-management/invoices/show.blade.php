<x-hotel-management.show-page
    title="Chi tiết hóa đơn"
    subtitle="Thông tin chi tiết hóa đơn"
    :index-route="route('hotel.invoices.index')"
>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã hóa đơn</div><div class="fw-semibold" id="invoice-id">Đang tải...</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã đặt phòng</div><div class="fw-semibold" id="invoice-booking-id">Đang tải...</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày lập</div><div class="fw-semibold" id="invoice-date">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Nhân viên phụ trách</div><div class="fw-semibold" id="invoice-employee">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tổng tiền</div><div class="fw-semibold" id="invoice-total">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Đã thanh toán</div><div class="fw-semibold" id="invoice-paid">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Trạng thái</div><div class="fw-semibold" id="invoice-status">Đang tải...</div></div></div>
    <div id="invoice-show-config" data-invoice-id="{{ request()->route('recordId') }}" hidden></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', async function () {
                const config = document.getElementById('invoice-show-config');
                const invoiceId = config ? config.dataset.invoiceId : '';

                const formatDate = function (value) {
                    if (!value) {
                        return '--';
                    }
                    const parts = String(value).split('-');
                    return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : value;
                };

                const formatCurrency = function (value) {
                    const number = Number(value);
                    if (Number.isNaN(number)) {
                        return '--';
                    }
                    return number.toLocaleString('vi-VN') + ' VNĐ';
                };

                const mapStatus = function (status) {
                    switch (Number(status)) {
                        case 0:
                            return 'Chưa thanh toán';
                        case 1:
                            return 'Đã thanh toán';
                        case 3:
                            return 'Đã hủy';
                        default:
                            return 'Không xác định';
                    }
                };

                try {
                    const response = await fetch(`/api/hoa-don/${invoiceId}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải chi tiết hóa đơn.');
                    }

                    const payload = await response.json();
                    const invoice = payload && payload.hoaDon ? payload.hoaDon : null;

                    if (!invoice) {
                        throw new Error('Không tìm thấy hóa đơn.');
                    }

                    document.getElementById('invoice-id').textContent = invoice.MaHD || '--';
                    document.getElementById('invoice-booking-id').textContent = invoice.MaDatPhong || '--';
                    document.getElementById('invoice-date').textContent = formatDate(invoice.NgayLapHD);
                    document.getElementById('invoice-employee').textContent = invoice.nhan_vien && invoice.nhan_vien.TenNV ? invoice.nhan_vien.TenNV : '--';
                    document.getElementById('invoice-total').textContent = formatCurrency(payload.TongTien);
                    document.getElementById('invoice-paid').textContent = formatCurrency(payload.DaThanhToan);
                    document.getElementById('invoice-status').textContent = mapStatus(invoice.TrangThai);
                } catch (error) {
                    ['invoice-id', 'invoice-booking-id', 'invoice-date', 'invoice-employee', 'invoice-total', 'invoice-paid', 'invoice-status']
                        .forEach(function (id) {
                            document.getElementById(id).textContent = '--';
                        });
                    document.getElementById('invoice-status').textContent = error.message;
                }
            });
        </script>
    @endpush
</x-hotel-management.show-page>
