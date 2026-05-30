<x-receptionist.index-page
    title="Quản lý đặt phòng"
    subtitle="Danh sách quản lý đặt phòng"
    :create-route="route('reception.bookings.create')"
    create-label="Thêm đặt phòng"
    table-title="Danh sách thông tin đặt phòng"
>
    <x-slot:filters>
        <div class="col-lg-5">
            <label class="form-label">Tìm kiếm</label>
            <input type="text" class="form-control" placeholder="Tìm theo mã đặt, tên khách, phòng" data-booking-search>
        </div>
        <div class="col-md-4 col-lg-3">
            <label class="form-label">Tình trạng phòng</label>
            <div class="rd-select-wrap">
                <select class="form-select" data-booking-status-filter>
                    <option value="">Tất cả trạng thái</option>
                    <option value="0">Chờ xác nhận</option>
                    <option value="1">Đã xác nhận</option>
                    <option value="2">Đang ở</option>
                    <option value="3">Đã trả phòng</option>
                    <option value="4">Đã hủy</option>
                </select>
            </div>
        </div>
    </x-slot:filters>

    @php
        $statusLabels = [
            \App\Models\DatPhong::HOLD => 'Chờ xác nhận',
            \App\Models\DatPhong::CONFIRMED => 'Đã xác nhận',
            \App\Models\DatPhong::CHECKED_IN => 'Đang ở',
            \App\Models\DatPhong::CHECKED_OUT => 'Đã trả phòng',
            \App\Models\DatPhong::CANCELLED => 'Đã hủy',
        ];

        $statusBadgeClasses = [
            \App\Models\DatPhong::HOLD => 'rd-badge--warning',
            \App\Models\DatPhong::CONFIRMED => 'rd-badge--warning',
            \App\Models\DatPhong::CHECKED_IN => 'rd-badge--success',
            \App\Models\DatPhong::CHECKED_OUT => 'rd-badge--muted',
            \App\Models\DatPhong::CANCELLED => 'rd-badge--danger',
        ];

        $formatDate = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y') : '--';
    @endphp

    <table class="table align-middle">
        <thead>
            <tr>
                <th>Mã</th>
                <th>Tên khách hàng</th>
                <th>Phòng</th>
                <th>Loại phòng</th>
                <th>Nhận phòng</th>
                <th>Trả phòng</th>
                <th>Tình trạng</th>
                <th style="min-width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody data-booking-table-body>
            @forelse(($bookings ?? collect()) as $booking)
                @php
                    $roomNumbers = $booking->chiTietDatPhong
                        ->map(fn ($detail) => $detail->phong?->SoPhong)
                        ->filter()
                        ->values()
                        ->implode(', ');
                    $roomTypes = $booking->chiTietDatPhong
                        ->map(fn ($detail) => $detail->phong?->loaiPhong?->TenLoaiPhong)
                        ->filter()
                        ->unique()
                        ->values()
                        ->implode(', ');
                    $status = (int) $booking->TinhTrang;
                    $searchText = collect([
                        $booking->MaDatPhong,
                        $booking->khachHang?->TenKH,
                        $booking->khachHang?->SoDienThoai,
                        $roomNumbers,
                        $roomTypes,
                    ])->filter()->implode(' ');
                @endphp
                <tr data-booking-row data-status="{{ $status }}" data-search="{{ \Illuminate\Support\Str::lower($searchText) }}">
                    <td>{{ $booking->MaDatPhong }}</td>
                    <td>{{ $booking->khachHang?->TenKH ?? '--' }}</td>
                    <td>{{ $roomNumbers ?: '--' }}</td>
                    <td>{{ $roomTypes ?: '--' }}</td>
                    <td>{{ $formatDate($booking->NgayNhanPhong) }}</td>
                    <td>{{ $formatDate($booking->NgayTraPhong) }}</td>
                    <td>
                        <span class="rd-badge {{ $statusBadgeClasses[$status] ?? 'rd-badge--muted' }}">
                            {{ $statusLabels[$status] ?? 'Không xác định' }}
                        </span>
                    </td>
                    <td>
                        @include('hotel-management.partials.action-icons', [
                            'showUrl' => route('reception.bookings.show', ['bookingId' => $booking->MaDatPhong]),
                            'editUrl' => route('reception.bookings.edit', ['bookingId' => $booking->MaDatPhong]),
                            'showDelete' => false,
                        ])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">Chưa có dữ liệu đặt phòng.</td>
                </tr>
            @endforelse
            <tr class="d-none" data-booking-filter-empty>
                <td colspan="8" class="text-center text-muted py-4">Không có đặt phòng phù hợp.</td>
            </tr>
        </tbody>
    </table>

    <div class="d-flex flex-column align-items-center justify-content-center gap-2 mt-3" data-booking-pagination-wrap>
        <div class="text-muted small" data-booking-pagination-info></div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-light btn-sm" data-booking-pagination-prev>Trước</button>
            <div class="d-flex align-items-center gap-1" data-booking-pagination-pages></div>
            <button type="button" class="btn btn-light btn-sm" data-booking-pagination-next>Sau</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.querySelector('[data-booking-search]');
            const statusFilter = document.querySelector('[data-booking-status-filter]');
            const rows = Array.from(document.querySelectorAll('[data-booking-row]'));
            const filterEmpty = document.querySelector('[data-booking-filter-empty]');
            const applyButton = document.querySelector('.rd-filter-panel .btn.btn-primary');
            const resetButton = document.querySelector('.rd-filter-panel .btn.btn-light');
            const paginationWrap = document.querySelector('[data-booking-pagination-wrap]');
            const paginationInfo = document.querySelector('[data-booking-pagination-info]');
            const paginationPages = document.querySelector('[data-booking-pagination-pages]');
            const prevButton = document.querySelector('[data-booking-pagination-prev]');
            const nextButton = document.querySelector('[data-booking-pagination-next]');
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
                    const rowIndex = filteredRows.indexOf(row);
                    const isVisible = rowIndex >= startIndex && rowIndex < endIndex;
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
                    paginationInfo.textContent = `Hiển thị ${from}-${to} / ${totalRows} đặt phòng`;
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
        });
    </script>
</x-receptionist.index-page>
