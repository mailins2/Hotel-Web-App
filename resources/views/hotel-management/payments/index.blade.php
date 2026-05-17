<x-hotel-management.index-page
    title="Quản lý thanh toán"
    subtitle="Danh sách quản lý thanh toán"
    :show-create-button="false"
>
    <x-slot:filters>
        <div class="col-md-3">
            <label class="form-label">Trạng thái hóa đơn</label>
            <div class="hm-select-wrap">
                <select class="form-select" data-payment-status>
                    <option value="">Tất cả trạng thái</option>
                    <option value="0">Chưa thanh toán</option>
                    <option value="1">Đã thanh toán</option>
                    <option value="3">Đã hủy</option>
                </select>
            </div>
        </div>
    </x-slot:filters>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Mã TT</th>
                <th>Mã đặt phòng</th>
                <th>Người thanh toán</th>
                <th>Số tiền</th>
                <th>Phương thức</th>
                <th>Loại thanh toán</th>
                <th>Ngày thanh toán</th>
                <th>Nhà cung cấp</th>
                <th>Trạng thái giao dịch</th>
                <!-- <th style="min-width: 180px;">Thao tác</th> -->
            </tr>
        </thead>
        <tbody id="payment-table-body">
            <tr>
                <td colspan="9" class="text-center text-muted py-4">Đang tải dữ liệu thanh toán...</td>
            </tr>
        </tbody>
    </table>

    <div
        id="payment-index-config"
        data-show-url-template="{{ route('hotel.payments.show', ['recordId' => '__PAYMENT_ID__']) }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableBody = document.getElementById('payment-table-body');
                const statusSelect = document.querySelector('[data-payment-status]');
                const filterPanel = document.querySelector('.hm-filter-panel');
                const config = document.getElementById('payment-index-config');
                const applyButton = filterPanel ? filterPanel.querySelector('.btn.btn-primary') : null;
                const resetButton = filterPanel ? filterPanel.querySelector('.btn.btn-light') : null;
                const showUrlTemplate = config ? config.dataset.showUrlTemplate : '';

                let payments = @json($payments ?? []);

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

                const mapInvoiceStatus = function (status) {
                    switch (Number(status)) {
                        case 0:
                            return { label: 'Chưa thanh toán', badgeClass: 'warning' };
                        case 1:
                            return { label: 'Đã thanh toán', badgeClass: 'success' };
                        case 3:
                            return { label: 'Đã hủy', badgeClass: 'danger' };
                        default:
                            return { label: 'Không xác định', badgeClass: 'muted' };
                    }
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

                const mapPaymentMethod = function (value) {
                    switch (Number(value)) {
                        case 1:
                            return 'Thẻ';
                        case 2:
                            return 'QR Code';
                        default:
                            return '--';
                    }
                };

                const mapTransactionStatus = function (value) {
                    switch (Number(value)) {
                        case 0:
                            return { label: 'Thất bại', badgeClass: 'danger' };
                        case 1:
                            return { label: 'Thành công', badgeClass: 'success' };
                        case 2:
                            return { label: 'Đang xử lý', badgeClass: 'warning' };
                        default:
                            return { label: '--', badgeClass: 'muted' };
                    }
                };

                const renderRows = function (rows) {
                    if (!rows.length) {
                        tableBody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">Không có thanh toán phù hợp.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = rows.map(function (payment) {
                        const invoice = payment && (payment.hoaDon || payment.hoa_don) ? (payment.hoaDon || payment.hoa_don) : null;
                        const booking = invoice && (invoice.datPhong || invoice.dat_phong) ? (invoice.datPhong || invoice.dat_phong) : null;
                        const customer = booking && (booking.khachHang || booking.khach_hang) ? (booking.khachHang || booking.khach_hang) : null;
                        const transactionStatus = mapTransactionStatus(payment.TrangThaiGiaoDich);
                        const showUrl = showUrlTemplate.replace('__PAYMENT_ID__', payment.MaTT);

                        return `
                            <tr class="hm-clickable-row" data-hm-row-link="${showUrl}" tabindex="0">
                                <td>${payment.MaTT || '--'}</td>
                                <td>${booking && booking.MaDatPhong ? booking.MaDatPhong : '--'}</td>
                                <td>${customer && customer.TenKH ? customer.TenKH : (payment.DinhDanhNguoiThanhToan || '--')}</td>
                                <td>${formatCurrency(payment.SoTien)}</td>
                                <td>${mapPaymentMethod(payment.PhuongThuc)}</td>
                                <td>${mapPaymentType(payment.LoaiThanhToan)}</td>
                                <td>${formatDateTime(payment.NgayThanhToan)}</td>
                                <td>${payment.NhaCungCap || '--'}</td>
                                <td><span class="hm-badge hm-badge--${transactionStatus.badgeClass}">${transactionStatus.label}</span></td>
                            </tr>
                        `;
                    }).join('');
                };

                const pagination = typeof window.createHmPagination === 'function'
                    ? window.createHmPagination({
                        container: document.querySelector('[data-hm-pagination]'),
                        pageSize: 10,
                        onPageChange: renderRows
                    })
                    : null;

                const applyFilters = function () {
                    const statusValue = (statusSelect ? statusSelect.value : '') || '';

                    const filtered = payments.filter(function (payment) {
                        const invoice = payment && (payment.hoaDon || payment.hoa_don) ? (payment.hoaDon || payment.hoa_don) : null;
                        return statusValue === '' || String(invoice && invoice.TrangThai !== undefined ? invoice.TrangThai : '') === statusValue;
                    });

                    if (pagination) {
                        pagination.setItems(filtered);
                        return;
                    }

                    renderRows(filtered);
                };

                const loadPayments = function () {
                    payments = (Array.isArray(payments) ? payments : []).slice().sort(function (a, b) {
                        return Number(b.MaTT || 0) - Number(a.MaTT || 0);
                    });
                    applyFilters();
                };

                if (applyButton) {
                    applyButton.addEventListener('click', applyFilters);
                }

                if (resetButton) {
                    resetButton.addEventListener('click', function () {
                        if (statusSelect) {
                            statusSelect.value = '';
                        }
                        applyFilters();
                    });
                }

                loadPayments();
            });
        </script>
    @endpush
</x-hotel-management.index-page>
