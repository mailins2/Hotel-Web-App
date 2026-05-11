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
                <td colspan="8" class="text-center text-muted py-4">Đang tải dữ liệu thanh toán...</td>
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

                let payments = [];

                const customerMap = {};

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

                const renderRows = function (rows) {
                    if (!rows.length) {
                        tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Không có thanh toán phù hợp.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = rows.map(function (payment) {
                        const invoiceStatus = mapInvoiceStatus(payment.invoiceStatus);
                        const showUrl = showUrlTemplate.replace('__PAYMENT_ID__', payment.MaTT);

                        return `
                            <tr class="hm-clickable-row" data-hm-row-link="${showUrl}" tabindex="0">
                                <td>${payment.MaTT || '--'}</td>
                                <td>${payment.MaDatPhong || '--'}</td>
                                <td>${payment.customerName || '--'}</td>
                                <td>${formatCurrency(payment.SoTien)}</td>
                                <td>${mapPaymentType(payment.LoaiThanhToan)}</td>
                                <td>${formatDateTime(payment.NgayThanhToan)}</td>
                                <td><span class="hm-badge hm-badge--${invoiceStatus.badgeClass}">${invoiceStatus.label}</span></td>
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
                        return statusValue === '' || String(payment.invoiceStatus) === statusValue;
                    });

                    if (pagination) {
                        pagination.setItems(filtered);
                        return;
                    }

                    renderRows(filtered);
                };

                const loadPayments = async function () {
                    try {
                        const [invoiceResponse, customerResponse] = await Promise.all([
                            fetch('/api/hoa-don', { headers: { 'Accept': 'application/json' } }),
                            fetch('/api/khach-hang', { headers: { 'Accept': 'application/json' } })
                        ]);

                        if (!invoiceResponse.ok) {
                            throw new Error('Không thể tải danh sách hóa đơn để tổng hợp thanh toán.');
                        }

                        if (!customerResponse.ok) {
                            throw new Error('Không thể tải thông tin khách hàng.');
                        }

                        const invoices = await invoiceResponse.json();
                        const customers = await customerResponse.json();

                        (Array.isArray(customers) ? customers : []).forEach(function (customer) {
                            customerMap[customer.MaKH] = customer;
                        });

                        const detailResults = await Promise.allSettled((Array.isArray(invoices) ? invoices : []).map(function (invoice) {
                            return fetch(`/api/hoa-don/${invoice.MaHD}`, {
                                headers: { 'Accept': 'application/json' }
                            }).then(function (response) {
                                if (!response.ok) {
                                    throw new Error('Invoice detail failed');
                                }
                                return response.json();
                            });
                        }));

                        payments = detailResults.reduce(function (rows, result) {
                            if (result.status !== 'fulfilled') {
                                return rows;
                            }

                            const detail = result.value;
                            const invoice = detail && detail.hoaDon ? detail.hoaDon : null;
                            const booking = invoice && invoice.dat_phong ? invoice.dat_phong : null;
                            const customer = booking ? customerMap[booking.MaKH] : null;
                            const paymentRows = invoice && Array.isArray(invoice.thanh_toans) ? invoice.thanh_toans : [];

                            paymentRows.forEach(function (payment) {
                                rows.push({
                                    MaTT: payment.MaTT,
                                    MaHD: payment.MaHD,
                                    MaDatPhong: booking && booking.MaDatPhong ? booking.MaDatPhong : '--',
                                    customerName: customer && customer.TenKH ? customer.TenKH : '--',
                                    SoTien: payment.SoTien,
                                    LoaiThanhToan: payment.LoaiThanhToan,
                                    NgayThanhToan: payment.NgayThanhToan,
                                    invoiceStatus: invoice ? invoice.TrangThai : null
                                });
                            });

                            return rows;
                        }, []);

                        payments.sort(function (a, b) {
                            return Number(b.MaTT || 0) - Number(a.MaTT || 0);
                        });

                        applyFilters();
                    } catch (error) {
                        tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4">${error.message}</td></tr>`;
                    }
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
