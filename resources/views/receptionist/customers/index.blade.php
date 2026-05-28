<x-receptionist.index-page
    title="Quản lý khách hàng"
    subtitle="Danh sách khách hàng"
    :create-route="route('reception.customers.create')"
    create-label="Thêm khách hàng"
    table-title="Danh sách thông tin khách hàng"
>
    <style>
        tr[data-customer-row] {
            cursor: pointer;
        }
    </style>

    <x-slot:filters>
        <div class="col-md-3">
            <label class="form-label">Tìm kiếm</label>
            <input type="text" class="form-control" placeholder="Tìm theo mã, tên, CCCD, số điện thoại" data-customer-search>
        </div>
        <div class="col-md-3">
            <label class="form-label">Trạng thái</label>
            <div class="rd-select-wrap">
                <select class="form-select" data-customer-status-filter>
                    <option value="">Tất cả trạng thái</option>
                    <option value="active">Hoạt động</option>
                    <option value="inactive">Không hoạt động</option>
                </select>
            </div>
        </div>
    </x-slot:filters>

    <table class="table align-middle">
        <thead>
            <tr>
                <th>Mã</th>
                <th>Tên khách hàng</th>
                <th>Số điện thoại</th>
                <th>Ngày sinh</th>
                <th>Giới tính</th>
                <th>Trạng thái</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody data-customer-table-body>
            @forelse(($customers ?? collect()) as $customer)
                @php
                    $accountStatus = $customer->taiKhoan?->TrangThai;
                    $isActive = $accountStatus === null || (int) $accountStatus === 1;
                    $gender = $customer->GioiTinh === null
                        ? '--'
                        : match ((int) $customer->GioiTinh) {
                            0 => 'Nữ',
                            1 => 'Nam',
                            2 => 'Khác',
                            default => '--',
                        };
                    $birthDate = $customer->NgaySinh ? \Carbon\Carbon::parse($customer->NgaySinh)->format('d/m/Y') : '--';
                    $searchText = collect([
                        $customer->MaKH,
                        $customer->TenKH,
                        $customer->CCCD,
                        $customer->SoDienThoai,
                        $customer->taiKhoan?->Email,
                    ])->filter()->implode(' ');
                @endphp
                <tr
                    data-customer-row
                    data-detail-url="{{ route('reception.customers.show', ['customerId' => $customer->MaKH]) }}"
                    data-status="{{ $isActive ? 'active' : 'inactive' }}"
                    data-search="{{ \Illuminate\Support\Str::lower($searchText) }}"
                >
                    <td>{{ $customer->MaKH }}</td>
                    <td>{{ $customer->TenKH ?? '--' }}</td>
                    <td>{{ $customer->SoDienThoai ?? '--' }}</td>
                    <td>{{ $birthDate }}</td>
                    <td>{{ $gender }}</td>
                    <td>
                        @if($isActive)
                            <span class="rd-badge rd-badge--success">Hoạt động</span>
                        @else
                            <span class="rd-badge rd-badge--muted">Không hoạt động</span>
                        @endif
                    </td>
                    <td>
                        @include('hotel-management.partials.action-icons', [
                            'showUrl' => route('reception.customers.show', ['customerId' => $customer->MaKH]),
                            'editUrl' => route('reception.customers.edit', ['customerId' => $customer->MaKH]),
                            'showDelete' => false,
                        ])
                    </td>
                    <td>
                        <div class="dropdown">
                            <button
                                class="btn btn-light btn-sm border rounded-circle p-0"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                style="width: 30px; height: 30px;"
                            >
                                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-muted">
                                    <path d="M12 6.75C12.6904 6.75 13.25 6.19036 13.25 5.5C13.25 4.80964 12.6904 4.25 12 4.25C11.3096 4.25 10.75 4.80964 10.75 5.5C10.75 6.19036 11.3096 6.75 12 6.75Z" fill="currentColor"/>
                                    <path d="M12 13.25C12.6904 13.25 13.25 12.6904 13.25 12C13.25 11.3096 12.6904 10.75 12 10.75C11.3096 10.75 10.75 11.3096 10.75 12C10.75 12.6904 11.3096 13.25 12 13.25Z" fill="currentColor"/>
                                    <path d="M12 19.75C12.6904 19.75 13.25 19.1904 13.25 18.5C13.25 17.8096 12.6904 17.25 12 17.25C11.3096 17.25 10.75 17.8096 10.75 18.5C10.75 19.1904 11.3096 19.75 12 19.75Z" fill="currentColor"/>
                                </svg>
                            </button>
                            <ul class="dropdown-menu shadow-sm border-0">
                                <li>
                                    <a href="{{ route('reception.bookings.create') }}" class="dropdown-item">Đặt phòng</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">Chưa có dữ liệu khách hàng.</td>
                </tr>
            @endforelse
            <tr class="d-none" data-customer-filter-empty>
                <td colspan="8" class="text-center text-muted py-4">Không có khách hàng phù hợp.</td>
            </tr>
        </tbody>
    </table>

    <div class="d-flex flex-column align-items-center justify-content-center gap-2 mt-3" data-customer-pagination-wrap>
        <div class="text-muted small" data-customer-pagination-info></div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-light btn-sm" data-customer-pagination-prev>Trước</button>
            <div class="d-flex align-items-center gap-1" data-customer-pagination-pages></div>
            <button type="button" class="btn btn-light btn-sm" data-customer-pagination-next>Sau</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.querySelector('[data-customer-search]');
            const statusFilter = document.querySelector('[data-customer-status-filter]');
            const rows = Array.from(document.querySelectorAll('[data-customer-row]'));
            const filterEmpty = document.querySelector('[data-customer-filter-empty]');
            const applyButton = document.querySelector('.rd-filter-panel .btn.btn-primary');
            const resetButton = document.querySelector('.rd-filter-panel .btn.btn-light');
            const paginationWrap = document.querySelector('[data-customer-pagination-wrap]');
            const paginationInfo = document.querySelector('[data-customer-pagination-info]');
            const paginationPages = document.querySelector('[data-customer-pagination-pages]');
            const prevButton = document.querySelector('[data-customer-pagination-prev]');
            const nextButton = document.querySelector('[data-customer-pagination-next]');
            const pageSize = 10;
            let currentPage = 1;
            let filteredRows = rows;

            const renderPagination = () => {
                const totalRows = filteredRows.length;
                const totalPages = Math.max(Math.ceil(totalRows / pageSize), 1);
                currentPage = Math.min(Math.max(currentPage, 1), totalPages);
                const startIndex = (currentPage - 1) * pageSize;
                const endIndex = startIndex + pageSize;

                rows.forEach((row) => {
                    const isVisible = filteredRows.includes(row)
                        && filteredRows.indexOf(row) >= startIndex
                        && filteredRows.indexOf(row) < endIndex;
                    row.classList.toggle('d-none', !isVisible);
                });

                if (filterEmpty) {
                    filterEmpty.classList.toggle('d-none', totalRows > 0 || rows.length === 0);
                }

                if (paginationWrap) {
                    paginationWrap.classList.toggle('d-none', totalRows <= pageSize);
                }

                if (paginationInfo) {
                    const from = totalRows ? startIndex + 1 : 0;
                    const to = Math.min(endIndex, totalRows);
                    paginationInfo.textContent = `Hiển thị ${from}-${to} / ${totalRows} khách hàng`;
                }

                if (prevButton) {
                    prevButton.disabled = currentPage <= 1;
                }

                if (nextButton) {
                    nextButton.disabled = currentPage >= totalPages;
                }

                if (paginationPages) {
                    paginationPages.innerHTML = '';
                    for (let page = 1; page <= totalPages; page += 1) {
                        const pageButton = document.createElement('button');
                        pageButton.type = 'button';
                        pageButton.className = `btn btn-sm ${page === currentPage ? 'btn-primary' : 'btn-light'}`;
                        pageButton.textContent = page;
                        pageButton.addEventListener('click', () => {
                            currentPage = page;
                            renderPagination();
                        });
                        paginationPages.appendChild(pageButton);
                    }
                }
            };

            const applyFilters = () => {
                const keyword = (searchInput?.value || '').trim().toLowerCase();
                const status = statusFilter?.value || '';

                filteredRows = rows.filter((row) => {
                    const matchesKeyword = !keyword || (row.dataset.search || '').includes(keyword);
                    const matchesStatus = !status || row.dataset.status === status;
                    return matchesKeyword && matchesStatus;
                });

                currentPage = 1;
                renderPagination();
            };

            applyButton?.addEventListener('click', applyFilters);
            searchInput?.addEventListener('input', applyFilters);
            statusFilter?.addEventListener('change', applyFilters);
            prevButton?.addEventListener('click', () => {
                currentPage -= 1;
                renderPagination();
            });
            nextButton?.addEventListener('click', () => {
                currentPage += 1;
                renderPagination();
            });
            resetButton?.addEventListener('click', () => {
                if (searchInput) {
                    searchInput.value = '';
                }
                if (statusFilter) {
                    statusFilter.value = '';
                }
                applyFilters();
            });

            applyFilters();

            rows.forEach((row) => {
                row.addEventListener('click', (event) => {
                    if (event.target.closest('a, button, .dropdown, [data-bs-toggle="dropdown"]')) {
                        return;
                    }

                    const detailUrl = row.dataset.detailUrl;
                    if (detailUrl) {
                        window.location.href = detailUrl;
                    }
                });
            });
        });
    </script>
</x-receptionist.index-page>
