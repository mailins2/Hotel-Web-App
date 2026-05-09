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

                let customers = [];

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

                const renderRows = function (rows) {
                    if (!rows.length) {
                        tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Không có khách hàng phù hợp.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = rows.map(function (customer) {
                        const showUrl = showUrlTemplate.replace('__CUSTOMER_ID__', customer.MaKH);
                        const editUrl = editUrlTemplate.replace('__CUSTOMER_ID__', customer.MaKH);

                        return `
                            <tr>
                                <td>${customer.MaKH || '--'}</td>
                                <td>${customer.MaTK || '--'}</td>
                                <td>${customer.TenKH || '--'}</td>
                                <td>${formatDate(customer.NgaySinh)}</td>
                                <td>${mapGender(customer.GioiTinh)}</td>
                                <td>${customer.DIEM !== undefined && customer.DIEM !== null ? customer.DIEM : '--'}</td>
                                <td>
                                    <div class="hm-action-group">
                                        <a href="${showUrl}" class="btn btn-sm btn-icon text-white" style="background-color: #22c55e; border-color: #22c55e;" title="Xem chi tiết">
                                            <span class="btn-inner">
                                                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M2 12C3.73 8.11 7.52 5.5 12 5.5C16.48 5.5 20.27 8.11 22 12C20.27 15.89 16.48 18.5 12 18.5C7.52 18.5 3.73 15.89 2 12Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                </svg>
                                            </span>
                                        </a>
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

                const applyFilters = function () {
                    const keyword = ((searchInput ? searchInput.value : '') || '').trim().toLowerCase();

                    const filtered = customers.filter(function (customer) {
                        const matchesKeyword = !keyword
                            || String(customer && customer.MaKH ? customer.MaKH : '').toLowerCase().includes(keyword)
                            || String(customer && customer.MaTK ? customer.MaTK : '').toLowerCase().includes(keyword)
                            || String(customer && customer.TenKH ? customer.TenKH : '').toLowerCase().includes(keyword)
                            || String(customer && customer.SoDienThoai ? customer.SoDienThoai : '').toLowerCase().includes(keyword);
                        return matchesKeyword;
                    });

                    renderRows(filtered);
                };

                const loadCustomers = async function () {
                    try {
                        const response = await fetch('/api/khach-hang', {
                            headers: { 'Accept': 'application/json' }
                        });

                        if (!response.ok) {
                            throw new Error('Không thể tải danh sách khách hàng.');
                        }

                        customers = await response.json();
                        applyFilters();
                    } catch (error) {
                        tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">${error.message}</td></tr>`;
                    }
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
