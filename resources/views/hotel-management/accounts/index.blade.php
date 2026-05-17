<x-hotel-management.index-page
    title="Quản lý tài khoản"
    subtitle="Danh sách quản lý tài khoản"
    :create-route="route('hotel.accounts.create')"
>
    <x-slot:filters>
        <div class="col-md-3">
            <label class="form-label">Tìm kiếm</label>
            <input type="text" class="form-control" placeholder="Tìm theo mã, email, họ tên" data-account-search>
        </div>
        <div class="col-md-3">
            <label class="form-label">Trạng thái</label>
            <div class="hm-select-wrap">
                <select class="form-select" data-account-status>
                    <option value="">Tất cả</option>
                    <option value="1">Hoạt động</option>
                    <option value="0">Không hoạt động</option>
                </select>
            </div>
        </div>
    </x-slot:filters>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Mã tài khoản</th>
                <th>Email</th>
                <th>Họ tên</th>
                <th>Loại tài khoản</th>
                <th>Trạng thái</th>
                <th style="min-width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody id="account-table-body">
            <tr>
                <td colspan="6" class="text-center text-muted py-4">Đang tải dữ liệu tài khoản...</td>
            </tr>
        </tbody>
    </table>

    <div
        id="account-index-config"
        data-show-url-template="{{ route('hotel.accounts.show', ['recordId' => '__ACCOUNT_ID__']) }}"
        data-edit-url-template="{{ route('hotel.accounts.edit', ['recordId' => '__ACCOUNT_ID__']) }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableBody = document.getElementById('account-table-body');
                const searchInput = document.querySelector('[data-account-search]');
                const statusSelect = document.querySelector('[data-account-status]');
                const filterPanel = document.querySelector('.hm-filter-panel');
                const config = document.getElementById('account-index-config');
                const applyButton = filterPanel ? filterPanel.querySelector('.btn.btn-primary') : null;
                const resetButton = filterPanel ? filterPanel.querySelector('.btn.btn-light') : null;
                const showUrlTemplate = config ? config.dataset.showUrlTemplate : '';
                const editUrlTemplate = config ? config.dataset.editUrlTemplate : '';

                let accounts = @json($accounts ?? []);

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
                            return 'Không xác định';
                    }
                };

                const mapStatus = function (status) {
                    return Number(status) === 1
                        ? { label: 'Hoạt động', badgeClass: 'success' }
                        : { label: 'Không hoạt động', badgeClass: 'muted' };
                };

                const resolveDisplayName = function (account) {
                    return (account && account.khachHang && account.khachHang.TenKH)
                        || (account && account.khach_hang && account.khach_hang.TenKH)
                        || (account && account.nhanVien && account.nhanVien.TenNV)
                        || (account && account.nhan_vien && account.nhan_vien.TenNV)
                        || '--';
                };

                const renderRows = function (rows) {
                    if (!rows.length) {
                        tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Không có tài khoản phù hợp.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = rows.map(function (account) {
                        const status = mapStatus(account.TrangThai);
                        const showUrl = showUrlTemplate.replace('__ACCOUNT_ID__', account.MaTK);
                        const editUrl = editUrlTemplate.replace('__ACCOUNT_ID__', account.MaTK);
                        const accountId = account && account.MaTK ? account.MaTK : '--';
                        const accountEmail = account && account.Email ? account.Email : '--';

                        return `
                            <tr class="hm-clickable-row" data-hm-row-link="${showUrl}" tabindex="0">
                                <td>${accountId}</td>
                                <td>${accountEmail}</td>
                                <td>${resolveDisplayName(account)}</td>
                                <td>${mapAccountType(account.LoaiTaiKhoan)}</td>
                                <td><span class="hm-badge hm-badge--${status.badgeClass}">${status.label}</span></td>
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
                    const statusValue = (statusSelect ? statusSelect.value : '') || '';

                    const filtered = accounts.filter(function (account) {
                        const matchesKeyword = !keyword
                            || String(account && account.MaTK ? account.MaTK : '').toLowerCase().includes(keyword)
                            || String(account && account.Email ? account.Email : '').toLowerCase().includes(keyword)
                            || resolveDisplayName(account).toLowerCase().includes(keyword);

                        const matchesStatus = statusValue === ''
                            || String(account && account.TrangThai ? account.TrangThai : '') === statusValue;

                        return matchesKeyword && matchesStatus;
                    });

                    if (pagination) {
                        pagination.setItems(filtered);
                        return;
                    }

                    renderRows(filtered);
                };

                const loadAccounts = function () {
                    accounts = (Array.isArray(accounts) ? accounts : []).slice().sort(function (left, right) {
                        return compareRecordIdDesc(left, right, 'MaTK');
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

                loadAccounts();
            });
        </script>
    @endpush
</x-hotel-management.index-page>
