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
                <th style="min-width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody id="employee-table-body">
            <tr>
                <td colspan="4" class="text-center text-muted py-4">Đang tải dữ liệu nhân viên...</td>
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

                let employees = [];

                const renderRows = function (rows) {
                    if (!rows.length) {
                        tableBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">Không có nhân viên phù hợp.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = rows.map(function (employee) {
                        const showUrl = showUrlTemplate.replace('__EMPLOYEE_ID__', employee.MaNV);
                        const editUrl = editUrlTemplate.replace('__EMPLOYEE_ID__', employee.MaNV);

                        return `
                            <tr>
                                <td>${employee.MaNV || '--'}</td>
                                <td>${employee.TenNV || '--'}</td>
                                <td>${employee.MaTK || '--'}</td>
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

                    const filtered = employees.filter(function (employee) {
                        return !keyword
                            || String(employee && employee.MaNV ? employee.MaNV : '').toLowerCase().includes(keyword)
                            || String(employee && employee.TenNV ? employee.TenNV : '').toLowerCase().includes(keyword)
                            || String(employee && employee.MaTK ? employee.MaTK : '').toLowerCase().includes(keyword);
                    });

                    renderRows(filtered);
                };

                const loadEmployees = async function () {
                    try {
                        const response = await fetch('/api/nhan-vien', {
                            headers: { 'Accept': 'application/json' }
                        });

                        if (!response.ok) {
                            throw new Error('Không thể tải danh sách nhân viên.');
                        }

                        employees = await response.json();
                        applyFilters();
                    } catch (error) {
                        tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger py-4">${error.message}</td></tr>`;
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

                loadEmployees();
            });
        </script>
    @endpush
</x-hotel-management.index-page>
