<x-receptionist.index-page
    title="Quản lý hóa đơn"
    subtitle="Thông tin danh sách hóa đơn"
    table-title="Danh sách hóa đơn"
>
    @php
        $invoiceList = $invoices ?? collect();
        $formatMoney = fn ($amount) => number_format((float) ($amount ?? 0), 0, ',', '.') . ' VNĐ';
        $unpaidCount = $invoiceList->filter(fn ($invoice) => (int) $invoice->TrangThai === 0)->count();
        $paidCount = $invoiceList->filter(fn ($invoice) => (int) $invoice->TrangThai === 1)->count();
        $remainingDebt = $invoiceList->filter(fn ($invoice) => (int) $invoice->TrangThai === 0)->sum(function ($invoice) {
            $paid = (float) ($invoice->DaThanhToan ?? $invoice->thanhToans?->sum('SoTien') ?? 0);
            return max((float) ($invoice->TongTien ?? 0) - $paid, 0);
        });

        $statusLabels = [
            0 => 'Chưa thanh toán',
            1 => 'Đã thanh toán',
            3 => 'Đã hủy',
        ];

        $statusBadgeClasses = [
            0 => 'rd-badge--warning',
            1 => 'rd-badge--success',
            3 => 'rd-badge--danger',
        ];

        $formatDate = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y') : '--';
    @endphp

    <x-slot:stats>
        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="rd-card"><div class="small text-uppercase text-muted fw-bold">Chưa thanh toán</div><div class="h4 mb-0 mt-2">{{ $unpaidCount }}</div></div></div>
            <div class="col-md-4"><div class="rd-card"><div class="small text-uppercase text-muted fw-bold">Đã thanh toán</div><div class="h4 mb-0 mt-2">{{ $paidCount }}</div></div></div>
            <div class="col-md-4"><div class="rd-card"><div class="small text-uppercase text-muted fw-bold">Công nợ còn lại</div><div class="h4 mb-0 mt-2">{{ $formatMoney($remainingDebt) }}</div></div></div>
        </div>
    </x-slot:stats>

    <x-slot:filters>
        <div class="col-lg-5">
            <label class="form-label">Tìm kiếm</label>
            <input type="text" class="form-control" placeholder="Tìm theo mã hóa đơn, khách hàng, nhân viên" data-invoice-search>
        </div>
        <div class="col-md-4 col-lg-3">
            <label class="form-label">Trạng thái</label>
            <div class="rd-select-wrap">
                <select class="form-select" data-invoice-status-filter>
                    <option value="">Tất cả trạng thái</option>
                    <option value="0">Chưa thanh toán</option>
                    <option value="1">Đã thanh toán</option>
                    <option value="3">Đã hủy</option>
                </select>
            </div>
        </div>
    </x-slot:filters>

    <table class="table align-middle">
        <thead>
            <tr>
                <th>Mã HĐ</th>
                <th>Ngày lập</th>
                <th>Khách hàng</th>
                <th>Nhân viên</th>
                <th>Tổng tiền</th>
                <th>Đã thanh toán</th>
                <th>Còn lại</th>
                <th>Trạng thái</th>
                <th style="min-width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody data-invoice-table-body>
            @forelse($invoiceList as $invoice)
                @php
                    $paidAmount = (float) ($invoice->DaThanhToan ?? $invoice->thanhToans?->sum('SoTien') ?? 0);
                    $totalAmount = (float) ($invoice->TongTien ?? 0);
                    $status = (int) $invoice->TrangThai;
                    $remainingAmount = $status === 0 ? max($totalAmount - $paidAmount, 0) : 0;
                    $customerName = $invoice->datPhong?->khachHang?->TenKH;
                    $employeeName = $invoice->nhanVien?->TenNV;
                    $searchText = collect([
                        $invoice->MaHD,
                        $invoice->MaDatPhong,
                        $customerName,
                        $employeeName,
                    ])->filter()->implode(' ');
                @endphp
                <tr data-invoice-row data-status="{{ $status }}" data-search="{{ \Illuminate\Support\Str::lower($searchText) }}">
                    <td>{{ $invoice->MaHD }}</td>
                    <td>{{ $formatDate($invoice->NgayLapHD) }}</td>
                    <td>{{ $customerName ?? '--' }}</td>
                    <td>{{ $employeeName ?? '--' }}</td>
                    <td>{{ $formatMoney($totalAmount) }}</td>
                    <td>{{ $formatMoney($paidAmount) }}</td>
                    <td>{{ $formatMoney($remainingAmount) }}</td>
                    <td>
                        <span class="rd-badge {{ $statusBadgeClasses[$status] ?? 'rd-badge--muted' }}">
                            {{ $statusLabels[$status] ?? 'Không xác định' }}
                        </span>
                    </td>
                    <td>
                        @include('hotel-management.partials.action-icons', [
                            'showUrl' => route('reception.invoices.show', ['invoiceId' => $invoice->MaHD]),
                            'editUrl' => route('reception.invoices.edit', ['invoiceId' => $invoice->MaHD]),
                            'showDelete' => false,
                        ])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">Chưa có dữ liệu hóa đơn.</td>
                </tr>
            @endforelse
            <tr class="d-none" data-invoice-filter-empty>
                <td colspan="9" class="text-center text-muted py-4">Không có hóa đơn phù hợp.</td>
            </tr>
        </tbody>
    </table>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3" data-invoice-pagination-wrap>
        <div class="text-muted small" data-invoice-pagination-info></div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-light btn-sm" data-invoice-pagination-prev>Trước</button>
            <div class="d-flex align-items-center gap-1" data-invoice-pagination-pages></div>
            <button type="button" class="btn btn-light btn-sm" data-invoice-pagination-next>Sau</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.querySelector('[data-invoice-search]');
            const statusFilter = document.querySelector('[data-invoice-status-filter]');
            const rows = Array.from(document.querySelectorAll('[data-invoice-row]'));
            const filterEmpty = document.querySelector('[data-invoice-filter-empty]');
            const applyButton = document.querySelector('.rd-filter-panel .btn.btn-primary');
            const resetButton = document.querySelector('.rd-filter-panel .btn.btn-light');
            const paginationWrap = document.querySelector('[data-invoice-pagination-wrap]');
            const paginationInfo = document.querySelector('[data-invoice-pagination-info]');
            const paginationPages = document.querySelector('[data-invoice-pagination-pages]');
            const prevButton = document.querySelector('[data-invoice-pagination-prev]');
            const nextButton = document.querySelector('[data-invoice-pagination-next]');
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
                    row.classList.toggle('d-none', !(rowIndex >= startIndex && rowIndex < endIndex));
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
                    paginationInfo.textContent = `Hiển thị ${from}-${to} / ${totalRows} hóa đơn`;
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
