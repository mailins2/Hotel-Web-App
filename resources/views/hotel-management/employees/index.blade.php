<x-hotel-management.index-page
    title="Quản lý nhân viên"
    subtitle="Danh sách quản lý nhân viên tại khách sạn"
    :create-route="route('hotel.employees.create')"
>
    <x-slot:filters>
        <div class="col-md-3">
            <label class="form-label">Tìm kiếm</label>
            <input type="text" class="form-control" placeholder="Tìm theo mã NV, tên, mã TK" data-employee-search>
        </div>
    </x-slot:filters>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Mã nhân viên</th>
                <th>Tên nhân viên</th>
                <th>Mã tài khoản</th>
                <th>Loại tài khoản</th>
                <th style="min-width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody id="employee-table-body">
            <tr>
                <td colspan="5" class="text-center text-muted py-4">Đang tải dữ liệu nhân viên...</td>
            </tr>
        </tbody>
    </table>

    <div
        id="employee-index-config"
        data-show-url-template="{{ route('hotel.employees.show', ['recordId' => '__EMPLOYEE_ID__']) }}"
        data-edit-url-template="{{ route('hotel.employees.edit', ['recordId' => '__EMPLOYEE_ID__']) }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableBody = document.getElementById('employee-table-body');
                const searchInput = document.querySelector('[data-employee-search]');
                const filterPanel = document.querySelector('.hm-filter-panel');
                const config = document.getElementById('employee-index-config');
                const applyButton = filterPanel ? filterPanel.querySelector('.btn.btn-primary') : null;
                const resetButton = filterPanel ? filterPanel.querySelector('.btn.btn-light') : null;
                const showUrlTemplate = config ? config.dataset.showUrlTemplate : '';
                const editUrlTemplate = config ? config.dataset.editUrlTemplate : '';

                let employees = @json($employees ?? []);

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

                const mapAccountType = function (type) {
                    switch (Number(type)) {
                        case 0:
                            return 'Khách hàng';
                        case 1:
                            return 'Nhân viên';
                        case 2:
                            return 'Quản lý';
                        case 3:
                            return 'Kế toán';
                        case 4:
                            return 'Nhân viên kinh doanh';
                        default:
                            return '--';
                    }
                };

                const renderRows = function (rows) {
                    if (!rows.length) {
                        tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Không có nhân viên phù hợp.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = rows.map(function (employee) {
                        const showUrl = showUrlTemplate.replace('__EMPLOYEE_ID__', employee.MaNV);
                        const editUrl = editUrlTemplate.replace('__EMPLOYEE_ID__', employee.MaNV);
                        const account = employee && (employee.taiKhoan || employee.tai_khoan) ? (employee.taiKhoan || employee.tai_khoan) : null;
                        const accountId = employee && employee.MaTK ? employee.MaTK : (account && account.MaTK ? account.MaTK : '');
                        const hasAccountId = String(accountId).trim() !== '';
                        const accountType = hasAccountId
                            ? mapAccountType(account && account.LoaiTaiKhoan !== undefined ? account.LoaiTaiKhoan : null)
                            : '--';

                        return `
                            <tr class="hm-clickable-row" data-hm-row-link="${showUrl}" tabindex="0">
                                <td>${employee.MaNV || '--'}</td>
                                <td>${employee.TenNV || '--'}</td>
                                <td>${accountId || '--'}</td>
                                <td>${accountType}</td>
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

                    const filtered = employees.filter(function (employee) {
                        const account = employee && (employee.taiKhoan || employee.tai_khoan) ? (employee.taiKhoan || employee.tai_khoan) : null;
                        const accountId = employee && employee.MaTK ? employee.MaTK : (account && account.MaTK ? account.MaTK : '');
                        const hasAccountId = String(accountId).trim() !== '';
                        const accountType = hasAccountId
                            ? mapAccountType(account && account.LoaiTaiKhoan !== undefined ? account.LoaiTaiKhoan : null).toLowerCase()
                            : '';

                        return !keyword
                            || String(employee && employee.MaNV ? employee.MaNV : '').toLowerCase().includes(keyword)
                            || String(employee && employee.TenNV ? employee.TenNV : '').toLowerCase().includes(keyword)
                            || String(accountId).toLowerCase().includes(keyword)
                            || accountType.includes(keyword);
                    });

                    if (pagination) {
                        pagination.setItems(filtered);
                        return;
                    }

                    renderRows(filtered);
                };

                const loadEmployees = function () {
                    employees = (Array.isArray(employees) ? employees : []).slice().sort(function (left, right) {
                        return compareRecordIdDesc(left, right, 'MaNV');
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

                loadEmployees();
            });
        </script>
    @endpush
</x-hotel-management.index-page>
