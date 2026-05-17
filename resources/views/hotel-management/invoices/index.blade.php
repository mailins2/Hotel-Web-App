<x-hotel-management.index-page
    title="Quản lý hóa đơn"
    subtitle="Danh sách quản lý hóa đơn"
    :show-create-button="false"
>
    <x-slot:filters>
        <div class="col-md-3">
            <label class="form-label">Mã hóa đơn</label>
            <input type="text" class="form-control" placeholder="Tìm mã hóa đơn" data-invoice-search>
        </div>
        <div class="col-md-3">
            <label class="form-label">Trạng thái</label>
            <div class="hm-select-wrap">
                <select class="form-select" data-invoice-status>
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
                <th>Mã hóa đơn</th>
                <th>Ngày lập</th>
                <th>Tên nhân viên</th>
                <th>Tổng tiền</th>
                <th>Đã thanh toán</th>
                <th>Trạng thái</th>
                <!-- <th style="min-width: 180px;">Thao tác</th> -->
            </tr>
        </thead>
        <tbody id="invoice-table-body">
            <tr>
                <td colspan="7" class="text-center text-muted py-4">Đang tải dữ liệu hóa đơn...</td>
            </tr>
        </tbody>
    </table>

    <div
        id="invoice-index-config"
        data-show-url-template="{{ route('hotel.invoices.show', ['recordId' => '__INVOICE_ID__']) }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableBody = document.getElementById('invoice-table-body');
                const searchInput = document.querySelector('[data-invoice-search]');
                const statusSelect = document.querySelector('[data-invoice-status]');
                const filterPanel = document.querySelector('.hm-filter-panel');
                const config = document.getElementById('invoice-index-config');
                const applyButton = filterPanel ? filterPanel.querySelector('.btn.btn-primary') : null;
                const resetButton = filterPanel ? filterPanel.querySelector('.btn.btn-light') : null;
                const showUrlTemplate = config ? config.dataset.showUrlTemplate : '';

                let invoices = @json($invoices ?? []);

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
                            return { label: 'Chưa thanh toán', badgeClass: 'warning' };
                        case 1:
                            return { label: 'Đã thanh toán', badgeClass: 'success' };
                        case 3:
                            return { label: 'Đã hủy', badgeClass: 'danger' };
                        default:
                            return { label: 'Không xác định', badgeClass: 'muted' };
                    }
                };

                const renderRows = function (rows) {
                    if (!rows.length) {
                        tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Không có hóa đơn phù hợp.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = rows.map(function (invoice) {
                        const status = mapStatus(invoice.TrangThai);
                        const showUrl = showUrlTemplate.replace('__INVOICE_ID__', invoice.MaHD);
                        const employee = invoice && (invoice.nhanVien || invoice.nhan_vien) ? (invoice.nhanVien || invoice.nhan_vien) : null;
                        const employeeName = employee && employee.TenNV ? employee.TenNV : '--';

                        return `
                            <tr class="hm-clickable-row" data-hm-row-link="${showUrl}" tabindex="0">
                                <td>${invoice.MaHD || '--'}</td>
                                <td>${formatDate(invoice.NgayLapHD)}</td>
                                <td>${employeeName}</td>
                                <td>${formatCurrency(invoice.TongTien)}</td>
                                <td>${formatCurrency(invoice.DaThanhToan)}</td>
                                <td><span class="hm-badge hm-badge--${status.badgeClass}">${status.label}</span></td>
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
                    const keyword = ((searchInput ? searchInput.value : '') || '').trim().toLowerCase();
                    const statusValue = (statusSelect ? statusSelect.value : '') || '';

                    const filtered = invoices.filter(function (invoice) {
                        const matchesKeyword = !keyword
                            || String(invoice && invoice.MaHD ? invoice.MaHD : '').toLowerCase().includes(keyword);
                        const matchesStatus = statusValue === ''
                            || String(invoice && invoice.TrangThai !== undefined ? invoice.TrangThai : '') === statusValue;
                        return matchesKeyword && matchesStatus;
                    });

                    if (pagination) {
                        pagination.setItems(filtered);
                        return;
                    }

                    renderRows(filtered);
                };

                const loadInvoices = function () {
                    invoices = (Array.isArray(invoices) ? invoices : []).slice().sort(function (left, right) {
                        return Number(right.MaHD || 0) - Number(left.MaHD || 0);
                    });
                    applyFilters();
                };

                if (applyButton) {
                    applyButton.addEventListener('click', applyFilters);
                }

                if (resetButton) {
                    resetButton.addEventListener('click', function () {
                        if (searchInput) {
                            searchInput.value = '';
                        }
                        if (statusSelect) {
                            statusSelect.value = '';
                        }
                        applyFilters();
                    });
                }

                loadInvoices();
            });
        </script>
    @endpush
</x-hotel-management.index-page>
