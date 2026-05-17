<x-hotel-management.index-page
    title="Quản lý khách hàng"
    subtitle="Danh sách quản lý khách hàng tại khách sạn"
    :create-route="route('hotel.customers.create')"
>
    <x-slot:filters>
        <div class="col-md-3">
            <label class="form-label">Tìm kiếm</label>
            <input type="text" class="form-control" placeholder="Tìm theo mã, tên, số điện thoại" data-customer-search>
        </div>
    </x-slot:filters>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Mã khách hàng</th>
                <th>Mã tài khoản</th>
                <th>Tên khách hàng</th>
                <th>Ngày sinh</th>
                <th>Giới tính</th>
                <th>Điểm</th>
                <th style="min-width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody id="customer-table-body">
            <tr>
                <td colspan="7" class="text-center text-muted py-4">Đang tải dữ liệu khách hàng...</td>
            </tr>
        </tbody>
    </table>

    <div
        id="customer-index-config"
        data-show-url-template="{{ route('hotel.customers.show', ['recordId' => '__CUSTOMER_ID__']) }}"
        data-edit-url-template="{{ route('hotel.customers.edit', ['recordId' => '__CUSTOMER_ID__']) }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableBody = document.getElementById('customer-table-body');
                const searchInput = document.querySelector('[data-customer-search]');
                const filterPanel = document.querySelector('.hm-filter-panel');
                const config = document.getElementById('customer-index-config');
                const applyButton = filterPanel ? filterPanel.querySelector('.btn.btn-primary') : null;
                const resetButton = filterPanel ? filterPanel.querySelector('.btn.btn-light') : null;
                const showUrlTemplate = config ? config.dataset.showUrlTemplate : '';
                const editUrlTemplate = config ? config.dataset.editUrlTemplate : '';

                let customers = @json($customers ?? []);

                const compareRecordIdDesc = function (left, right, fieldName) {
                    const leftValue = left && left[fieldName] !== undefined && left[fieldName] !== null ? String(left[fieldName]) : '';
                    const rightValue = right && right[fieldName] !== undefined && right[fieldName] !== null ? String(right[fieldName]) : '';
                    const leftNumber = Number(leftValue);
                    const rightNumber = Number(rightValue);

                    if (!Number.isNaN(leftNumber) && !Number.isNaN(rightNumber)) {
                        return rightNumber - leftNumber;
                    }

                    return rightValue.localeCompare(leftValue, undefined, { numeric: true, sensitivity: 'base' });
                };

                const mapGender = function (value) {
                    switch (Number(value)) {
                        case 0:
                            return 'Nữ';
                        case 1:
                            return 'Nam';
                        case 2:
                            return 'Khác';
                        default:
                            return '--';
                    }
                };

                const formatDate = function (value) {
                    if (!value) {
                        return '--';
                    }

                    const parts = String(value).split('-');
                    return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : value;
                };

                const getAccountId = function (customer) {
                    const account = customer && (customer.taiKhoan || customer.tai_khoan) ? (customer.taiKhoan || customer.tai_khoan) : null;
                    return customer && customer.MaTK ? customer.MaTK : (account && account.MaTK ? account.MaTK : '');
                };

                const renderRows = function (rows) {
                    if (!rows.length) {
                        tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Không có khách hàng phù hợp.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = rows.map(function (customer) {
                        const showUrl = showUrlTemplate.replace('__CUSTOMER_ID__', customer.MaKH);
                        const editUrl = editUrlTemplate.replace('__CUSTOMER_ID__', customer.MaKH);

                        return `
                            <tr class="hm-clickable-row" data-hm-row-link="${showUrl}" tabindex="0">
                                <td>${customer.MaKH || '--'}</td>
                                <td>${getAccountId(customer) || '--'}</td>
                                <td>${customer.TenKH || '--'}</td>
                                <td>${formatDate(customer.NgaySinh)}</td>
                                <td>${mapGender(customer.GioiTinh)}</td>
                                <td>${customer.DIEM !== undefined && customer.DIEM !== null ? customer.DIEM : '--'}</td>
                                <td>
                                    <div class="hm-action-group">
                                        <a href="${editUrl}" class="btn btn-sm btn-warning btn-icon" title="Chỉnh sửa">
                                            <span class="btn-inner">
                                                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M13.7476 20H21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path d="M16.8392 3.41187C17.6212 2.62988 18.8891 2.62988 19.6711 3.41187L20.5881 4.32887C21.3701 5.11087 21.3701 6.37875 20.5881 7.16075L8.14912 19.5998C7.65512 20.0938 7.04312 20.4538 6.37112 20.6478L3 21L3.352 17.6289C3.546 16.9569 3.906 16.3448 4.4 15.8508L16.8392 3.41187Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                </svg>
                                            </span>
                                        </a>
                                    </div>
                                </td>
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

                    const filtered = customers.filter(function (customer) {
                        const matchesKeyword = !keyword
                            || String(customer && customer.MaKH ? customer.MaKH : '').toLowerCase().includes(keyword)
                            || String(getAccountId(customer)).toLowerCase().includes(keyword)
                            || String(customer && customer.TenKH ? customer.TenKH : '').toLowerCase().includes(keyword)
                            || String(customer && customer.SoDienThoai ? customer.SoDienThoai : '').toLowerCase().includes(keyword);
                        return matchesKeyword;
                    });

                    if (pagination) {
                        pagination.setItems(filtered);
                        return;
                    }

                    renderRows(filtered);
                };

                const loadCustomers = function () {
                    customers = (Array.isArray(customers) ? customers : []).slice().sort(function (left, right) {
                        return compareRecordIdDesc(left, right, 'MaKH');
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
                        applyFilters();
                    });
                }

                loadCustomers();
            });
        </script>
    @endpush
</x-hotel-management.index-page>
