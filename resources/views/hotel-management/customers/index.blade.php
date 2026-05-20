<x-hotel-management.index-page
    title="Quản lý khách hàng"
    subtitle="Danh sách quản lý khách hàng tại khách sạn"
    :create-route="route('hotel.customers.create')"
>
    <x-slot:filters>
        <div class="col-md-3">
            <label class="form-label">Tìm kiếm</label>
            <input type="text" class="form-control" placeholder="Tìm theo tên, số điện thoại" data-customer-search>
        </div>
    </x-slot:filters>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Mã khách hàng</th>
                <th>Mã tài khoản</th>
                <th>Tên khách hàng</th>
                <th>SĐT</th>
                <th>Ngày sinh</th>
                <th>Giới tính</th>
                <th>Điểm</th>
                <th style="min-width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody id="customer-table-body">
            <tr>
                <td colspan="8" class="text-center text-muted py-4">Đang tải dữ liệu khách hàng...</td>
            </tr>
        </tbody>
    </table>

    <div
        id="customer-index-config"
        data-show-url-template="{{ route('hotel.customers.show', ['recordId' => '__CUSTOMER_ID__']) }}"
        data-edit-url-template="{{ route('hotel.customers.edit', ['recordId' => '__CUSTOMER_ID__']) }}"
        data-delete-url-template="{{ url('/api/khach-hang/__CUSTOMER_ID__') }}"
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
                const deleteUrlTemplate = config ? config.dataset.deleteUrlTemplate : '';

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
                        tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Không có khách hàng phù hợp.</td></tr>';
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
                                <td>${customer.SoDienThoai || '--'}</td>
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
                                        <button type="button" class="btn btn-sm btn-danger btn-icon" title="Xóa" data-delete-customer-id="${customer.MaKH || ''}">
                                            <span class="btn-inner">
                                                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M19 7L18.132 18.142C18.0578 19.0948 17.2636 19.8333 16.308 19.8333H7.692C6.73635 19.8333 5.9422 19.0948 5.868 18.142L5 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path d="M4 7H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                                                    <path d="M9 7V4.8C9 4.35817 9.35817 4 9.8 4H14.2C14.6418 4 15 4.35817 15 4.8V7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path d="M10 11V16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                                                    <path d="M14 11V16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                                                </svg>
                                            </span>
                                        </button>
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
                    applyButton.remove();
                }

                if (filterPanel) {
                    const filterForm = filterPanel.querySelector('form');
                    if (filterForm) {
                        filterForm.addEventListener('submit', function (event) {
                            event.preventDefault();
                            applyFilters();
                        });
                    }
                }

                if (searchInput) {
                    searchInput.addEventListener('input', applyFilters);
                }

                if (resetButton) {
                    resetButton.addEventListener('click', function () {
                        if (searchInput) {
                            searchInput.value = '';
                        }
                        applyFilters();
                    });
                }

                document.addEventListener('click', async function (event) {
                    const deleteButton = event.target && event.target.closest
                        ? event.target.closest('[data-delete-customer-id]')
                        : null;

                    if (!deleteButton) {
                        return;
                    }

                    event.preventDefault();
                    event.stopPropagation();

                    const customerId = deleteButton.getAttribute('data-delete-customer-id') || '';
                    if (!customerId) {
                        return;
                    }

                    const confirmed = await window.hmConfirmDeletion({
                        title: 'Xóa khách hàng?',
                        message: 'Bạn muốn xóa khách hàng này?',
                        recordLabel: 'Mã khách hàng: ' + customerId,
                        note: 'Khách đã có đơn đặt phòng sẽ không thể xóa.',
                    });

                    if (!confirmed) {
                        return;
                    }

                    deleteButton.disabled = true;

                    try {
                        const response = await fetch(deleteUrlTemplate.replace('__CUSTOMER_ID__', encodeURIComponent(customerId)), {
                            method: 'DELETE',
                            headers: { Accept: 'application/json' }
                        });
                        const payload = await response.json().catch(function () { return {}; });

                        if (!response.ok || payload.success === false) {
                            throw new Error(payload && payload.message ? payload.message : 'Không thể xóa khách hàng.');
                        }

                        customers = customers.filter(function (customer) {
                            return String(customer.MaKH || '') !== String(customerId);
                        });

                        loadCustomers();
                        window.hmShowToast({
                            type: 'success',
                            title: 'Đã xóa',
                            message: payload.message || 'Đã xóa khách hàng thành công.',
                        });
                    } catch (error) {
                        window.hmShowToast({
                            type: 'danger',
                            title: 'Không thể xóa',
                            message: error.message,
                        });
                    } finally {
                        deleteButton.disabled = false;
                    }
                });

                loadCustomers();
            });
        </script>
    @endpush
</x-hotel-management.index-page>
