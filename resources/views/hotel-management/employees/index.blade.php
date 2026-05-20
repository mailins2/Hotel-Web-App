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
                <th>Chức vụ</th>
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
        data-delete-url-template="{{ url('/api/nhan-vien/__EMPLOYEE_ID__') }}"
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
                const deleteUrlTemplate = config ? config.dataset.deleteUrlTemplate : '';

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

                const mapPosition = function (position) {
                    if (position === undefined || position === null || position === '') {
                        return 'Chưa có chức vụ';
                    }

                    switch (Number(position)) {
                        case 0:
                            return 'Quản lý';
                        case 1:
                            return 'Nhân viên';
                        default:
                            return 'Chưa có chức vụ';
                    }
                };

                const getAccountId = function (employee) {
                    const account = employee && (employee.taiKhoan || employee.tai_khoan)
                        ? (employee.taiKhoan || employee.tai_khoan)
                        : null;

                    return account && account.MaTK ? account.MaTK : '';
                };

                const renderRows = function (rows) {
                    if (!rows.length) {
                        tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Không có nhân viên phù hợp.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = rows.map(function (employee) {
                        const showUrl = showUrlTemplate.replace('__EMPLOYEE_ID__', employee.MaNV);
                        const editUrl = editUrlTemplate.replace('__EMPLOYEE_ID__', employee.MaNV);

                        return `
                            <tr class="hm-clickable-row" data-hm-row-link="${showUrl}" tabindex="0">
                                <td>${employee.MaNV || '--'}</td>
                                <td>${employee.TenNV || '--'}</td>
                                <td>${getAccountId(employee) || '--'}</td>
                                <td>${mapPosition(employee.ChucVu)}</td>
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
                                        <button type="button" class="btn btn-sm btn-danger btn-icon" title="Xóa" data-delete-employee-id="${employee.MaNV || ''}">
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

                    const filtered = employees.filter(function (employee) {
                        return !keyword
                            || String(employee && employee.MaNV ? employee.MaNV : '').toLowerCase().includes(keyword)
                            || String(employee && employee.TenNV ? employee.TenNV : '').toLowerCase().includes(keyword)
                            || String(getAccountId(employee)).toLowerCase().includes(keyword)
                            || mapPosition(employee && employee.ChucVu !== undefined ? employee.ChucVu : null).toLowerCase().includes(keyword);
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

                document.addEventListener('click', async function (event) {
                    const deleteButton = event.target && event.target.closest
                        ? event.target.closest('[data-delete-employee-id]')
                        : null;

                    if (!deleteButton) {
                        return;
                    }

                    event.preventDefault();
                    event.stopPropagation();

                    const employeeId = deleteButton.getAttribute('data-delete-employee-id') || '';
                    if (!employeeId) {
                        return;
                    }

                    const confirmed = await window.hmConfirmDeletion({
                        title: 'Xóa nhân viên?',
                        message: 'Bạn muốn xóa nhân viên này?',
                        recordLabel: 'Mã nhân viên: ' + employeeId,
                        note: 'Đã xử lý hóa đơn thì hệ thống sẽ khóa tài khoản liên quan.',
                    });

                    if (!confirmed) {
                        return;
                    }

                    deleteButton.disabled = true;

                    try {
                        const response = await fetch(deleteUrlTemplate.replace('__EMPLOYEE_ID__', encodeURIComponent(employeeId)), {
                            method: 'DELETE',
                            headers: { Accept: 'application/json' }
                        });
                        const payload = await response.json().catch(function () { return {}; });

                        if (!response.ok || payload.success === false) {
                            throw new Error(payload && payload.message ? payload.message : 'Không thể xóa nhân viên.');
                        }

                        if (payload.action !== 'deactivated') {
                            employees = employees.filter(function (employee) {
                                return String(employee.MaNV || '') !== String(employeeId);
                            });
                        }

                        loadEmployees();
                        window.hmShowToast({
                            type: payload.action === 'deactivated' ? 'warning' : 'success',
                            title: payload.action === 'deactivated' ? 'Đã khóa tài khoản' : 'Đã xóa',
                            message: payload.message || 'Thao tác thành công.',
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

                loadEmployees();
            });
        </script>
    @endpush
</x-hotel-management.index-page>
