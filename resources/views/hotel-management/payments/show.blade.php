<x-hotel-management.show-page
    title="Chi tiết thanh toán"
    subtitle="Thông tin chi tiết thanh toán"
    :index-route="route('hotel.payments.index')"
>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã thanh toán</div><div class="fw-semibold" id="payment-id">Đang tải...</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã đặt phòng</div><div class="fw-semibold" id="payment-booking-id">Đang tải...</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Người thanh toán</div><div class="fw-semibold" id="payment-customer-name">Đang tải...</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số tiền</div><div class="fw-semibold" id="payment-amount">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Loại thanh toán</div><div class="fw-semibold" id="payment-type">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày thanh toán</div><div class="fw-semibold" id="payment-date">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Trạng thái hóa đơn</div><div class="fw-semibold" id="payment-invoice-status">Đang tải...</div></div></div>
    <div id="payment-show-config" data-payment-id="{{ request()->route('recordId') }}" hidden></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', async function () {
                const config = document.getElementById('payment-show-config');
                const paymentId = config ? config.dataset.paymentId : '';

                const formatDateTime = function (value) {
                    if (!value) {
                        return '--';
                    }

                    const parts = String(value).split(' ');
                    const dateParts = parts[0] ? parts[0].split('-') : [];
                    const formattedDate = dateParts.length === 3 ? `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}` : parts[0];
                    return parts[1] ? `${formattedDate} ${parts[1]}` : formattedDate;
                };

                const formatCurrency = function (value) {
                    const number = Number(value);
                    if (Number.isNaN(number)) {
                        return '--';
                    }
                    return number.toLocaleString('vi-VN') + ' VNĐ';
                };

                const mapPaymentType = function (value) {
                    switch (Number(value)) {
                        case 0:
                            return 'Đặt cọc';
                        case 1:
                            return 'Thanh toán checkout';
                        default:
                            return '--';
                    }
                };

                const mapInvoiceStatus = function (value) {
                    switch (Number(value)) {
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
                    const paymentResponse = await fetch(`/api/thanh-toan/${paymentId}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!paymentResponse.ok) {
                        throw new Error('Không thể tải chi tiết thanh toán.');
                    }

                    const payment = await paymentResponse.json();
                    const invoiceId = payment && payment.MaHD ? payment.MaHD : null;
                    let invoiceDetail = null;
                    let customer = null;

                    if (invoiceId) {
                        const invoiceResponse = await fetch(`/api/hoa-don/${invoiceId}`, {
                            headers: { 'Accept': 'application/json' }
                        });

                        if (invoiceResponse.ok) {
                            invoiceDetail = await invoiceResponse.json();
                        }
                    }

                    if (invoiceDetail && invoiceDetail.hoaDon && invoiceDetail.hoaDon.dat_phong && invoiceDetail.hoaDon.dat_phong.MaKH) {
                        const customerResponse = await fetch(`/api/khach-hang/${invoiceDetail.hoaDon.dat_phong.MaKH}`, {
                            headers: { 'Accept': 'application/json' }
                        });

                        if (customerResponse.ok) {
                            customer = await customerResponse.json();
                        }
                    }

                    document.getElementById('payment-id').textContent = payment.MaTT || '--';
                    document.getElementById('payment-booking-id').textContent = invoiceDetail && invoiceDetail.hoaDon && invoiceDetail.hoaDon.dat_phong ? invoiceDetail.hoaDon.dat_phong.MaDatPhong : '--';
                    document.getElementById('payment-customer-name').textContent = customer && customer.TenKH ? customer.TenKH : '--';
                    document.getElementById('payment-amount').textContent = formatCurrency(payment.SoTien);
                    document.getElementById('payment-type').textContent = mapPaymentType(payment.LoaiThanhToan);
                    document.getElementById('payment-date').textContent = formatDateTime(payment.NgayThanhToan);
                    document.getElementById('payment-invoice-status').textContent = invoiceDetail && invoiceDetail.hoaDon ? mapInvoiceStatus(invoiceDetail.hoaDon.TrangThai) : '--';
                } catch (error) {
                    ['payment-id', 'payment-booking-id', 'payment-customer-name', 'payment-amount', 'payment-type', 'payment-date']
                        .forEach(function (id) {
                            document.getElementById(id).textContent = '--';
                        });
                    document.getElementById('payment-invoice-status').textContent = error.message;
                }
            });
        </script>
    @endpush
</x-hotel-management.show-page>
