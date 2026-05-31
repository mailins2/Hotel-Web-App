<x-receptionist.index-page
    title="Quản lý hóa đơn"
    subtitle="Thông tin danh sách hóa đơn"
    table-title="Danh sách hóa đơn"
    :show-filter-actions="false"
>
    <style>
        .ri-date-display-field {
            position: relative;
        }

        .ri-date-display-field .form-control {
            color: transparent;
            caret-color: transparent;
        }

        .ri-date-display-field .form-control:focus {
            color: transparent;
        }

        .ri-date-display-field .form-control::-webkit-datetime-edit {
            color: transparent;
        }

        .ri-date-display-value {
            position: absolute;
            left: 0.95rem;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #8a97aa;
            font: inherit;
        }

        tr[data-invoice-row] {
            cursor: pointer;
        }

        tr[data-invoice-row]:hover {
            background: #fff7ed;
        }

        .ri-invoice-filter-grid {
            display: grid;
            grid-template-columns: minmax(280px, 1.35fr) minmax(180px, 0.65fr) repeat(3, minmax(250px, 1fr));
            gap: 1rem;
            align-items: end;
        }

        .ri-filter-group {
            min-width: 0;
        }

        .ri-filter-group--pair {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .ri-filter-group-title {
            margin-bottom: 0.45rem;
            color: #7b4a43;
            font-weight: 600;
            line-height: 1.2;
        }

        .ri-filter-group .form-label {
            margin-bottom: 0;
            font-size: 0.78rem;
            color: #9a6a62;
        }

        @media (max-width: 1399.98px) {
            .ri-invoice-filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .ri-invoice-filter-grid,
            .ri-filter-group--pair {
                grid-template-columns: 1fr;
            }
        }

        .rd-filter-panel form > .row {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            column-gap: 1rem;
            row-gap: 1.1rem;
            margin: 0;
            align-items: end;
        }

        .rd-filter-panel form > .row > [class*="col-"] {
            width: 100%;
            max-width: none;
            min-width: 0;
            padding: 0;
            grid-column: auto;
        }

        .rd-filter-panel form > .row > [class*="col-"]:nth-child(1) {
            grid-column: auto;
            width: 100%;
        }

        .rd-filter-panel form > .row > [class*="col-"]:nth-child(2) {
            grid-column: auto;
            width: 100%;
        }

        .rd-filter-panel form > .row > [class*="col-"]:nth-child(1) .form-control {
            width: 100%;
        }

        .rd-filter-panel form > .row > [class*="col-"]:nth-child(2) .form-label,
        .rd-filter-panel form > .row > [class*="col-"]:nth-child(2) .rd-select-wrap {
            position: relative;
            left: 0;
        }

        .rd-filter-panel form > .row > [class*="col-"]:nth-child(2) .rd-select-wrap {
            width: 100%;
        }

        .rd-filter-panel form > .row > [class*="col-"]:nth-child(n+3) {
            grid-column: auto;
        }

        .rd-filter-panel .form-control,
        .rd-filter-panel .form-select,
        .rd-filter-panel .rd-select-wrap {
            width: 100%;
            min-width: 0;
        }

        .rd-filter-panel .form-label {
            margin-bottom: 0.35rem;
            line-height: 1.2;
        }

        @media (max-width: 1199.98px) {
            .rd-filter-panel form > .row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .rd-filter-panel form > .row > [class*="col-"]:nth-child(1),
            .rd-filter-panel form > .row > [class*="col-"]:nth-child(n+3) {
                grid-column: auto;
            }

            .rd-filter-panel form > .row > [class*="col-"]:nth-child(2) {
                grid-column: auto;
                width: 100%;
                margin-left: 0;
            }

            .rd-filter-panel form > .row > [class*="col-"]:nth-child(1) .form-control {
                width: 100%;
            }

            .rd-filter-panel form > .row > [class*="col-"]:nth-child(2) .form-label,
            .rd-filter-panel form > .row > [class*="col-"]:nth-child(2) .rd-select-wrap {
                left: 0;
            }

            .rd-filter-panel form > .row > [class*="col-"]:nth-child(2) .rd-select-wrap {
                width: 100%;
            }
        }

        @media (max-width: 767.98px) {
            .rd-filter-panel form > .row {
                grid-template-columns: 1fr;
            }

            .rd-filter-panel form > .row > [class*="col-"],
            .rd-filter-panel form > .row > [class*="col-"]:nth-child(1),
            .rd-filter-panel form > .row > [class*="col-"]:nth-child(2),
            .rd-filter-panel form > .row > [class*="col-"]:nth-child(n+3) {
                grid-column: 1;
                width: auto;
                margin-left: 0;
            }
        }
    </style>

    @php
        $invoiceList = $invoices ?? collect();
        $formatMoney = fn ($amount) => number_format((float) ($amount ?? 0), 0, ',', '.') . ' VNĐ';
        $today = now()->toDateString();
        $remainingAmountOf = function ($invoice) {
            $paid = (float) ($invoice->DaThanhToan ?? $invoice->thanhToans?->sum('SoTien') ?? 0);
            return max((float) ($invoice->TongTien ?? 0) - $paid, 0);
        };
        $todayUnpaidInvoices = $invoiceList->filter(function ($invoice) use ($today) {
            return (int) $invoice->TrangThai === 0
                && $invoice->datPhong?->NgayTraPhong
                && \Carbon\Carbon::parse($invoice->datPhong->NgayTraPhong)->toDateString() === $today;
        });
        $todayUnpaidCount = $todayUnpaidInvoices->count();
        $todayReceivableAmount = $todayUnpaidInvoices->sum($remainingAmountOf);
        $totalReceivableAmount = $invoiceList
            ->filter(fn ($invoice) => (int) $invoice->TrangThai === 0)
            ->sum($remainingAmountOf);

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
            <div class="col-md-4"><div class="rd-card"><div class="small text-uppercase text-muted fw-bold">Hóa đơn chưa thanh toán hôm nay</div><div class="h4 mb-0 mt-2">{{ $todayUnpaidCount }}</div></div></div>
            <div class="col-md-4"><div class="rd-card"><div class="small text-uppercase text-muted fw-bold">Công nợ phải thu hôm nay</div><div class="h4 mb-0 mt-2">{{ $formatMoney($todayReceivableAmount) }}</div></div></div>
            <div class="col-md-4"><div class="rd-card"><div class="small text-uppercase text-muted fw-bold">Tổng công nợ phải thu</div><div class="h4 mb-0 mt-2">{{ $formatMoney($totalReceivableAmount) }}</div></div></div>
        </div>
    </x-slot:stats>

    <x-slot:filters>
        <div class="col-md-6 col-lg-4">
            <label class="form-label">Tìm kiếm</label>
            <input type="text" class="form-control" placeholder="Tìm theo mã hóa đơn, khách hàng" data-invoice-search>
        </div>
        <div class="col-md-6 col-lg-2">
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
        <div class="col-md-6 col-lg-3">
            <label class="form-label">Ngày lập từ</label>
            <div class="ri-date-display-field">
                <input type="date" class="form-control" lang="en-GB" data-invoice-date-from>
                <span class="ri-date-display-value" data-date-display="invoice-date-from">dd/mm/yyyy</span>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <label class="form-label">Ngày lập đến</label>
            <div class="ri-date-display-field">
                <input type="date" class="form-control" lang="en-GB" data-invoice-date-to>
                <span class="ri-date-display-value" data-date-display="invoice-date-to">dd/mm/yyyy</span>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <label class="form-label">Hạn tất toán từ</label>
            <div class="ri-date-display-field">
                <input type="date" class="form-control" lang="en-GB" data-invoice-due-from>
                <span class="ri-date-display-value" data-date-display="invoice-due-from">dd/mm/yyyy</span>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <label class="form-label">Hạn tất toán đến</label>
            <div class="ri-date-display-field">
                <input type="date" class="form-control" lang="en-GB" data-invoice-due-to>
                <span class="ri-date-display-value" data-date-display="invoice-due-to">dd/mm/yyyy</span>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <label class="form-label">Ngày tất toán từ</label>
            <div class="ri-date-display-field">
                <input type="date" class="form-control" lang="en-GB" data-invoice-settled-from>
                <span class="ri-date-display-value" data-date-display="invoice-settled-from">dd/mm/yyyy</span>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <label class="form-label">Ngày tất toán đến</label>
            <div class="ri-date-display-field">
                <input type="date" class="form-control" lang="en-GB" data-invoice-settled-to>
                <span class="ri-date-display-value" data-date-display="invoice-settled-to">dd/mm/yyyy</span>
            </div>
        </div>
    </x-slot:filters>

    <table class="table align-middle">
        <thead>
            <tr>
                <th>Mã HĐ</th>
                <th>Ngày lập</th>
                <th>Hạn tất toán</th>
                <th>Ngày tất toán</th>
                <th>Khách hàng</th>
                <th>Tổng tiền</th>
                <th>Đã thanh toán</th>
                <th>Còn lại</th>
                <th>Trạng thái</th>
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
                    $settlementDueDate = $invoice->datPhong?->NgayTraPhong;
                    $settledAt = $invoice->thanhToans?->max('NgayThanhToan');
                    $searchText = collect([
                        $invoice->MaHD,
                        $invoice->MaDatPhong,
                        $customerName,
                    ])->filter()->implode(' ');
                @endphp
                <tr
                    data-invoice-row
                    data-detail-url="{{ route('reception.invoices.show', ['invoiceId' => $invoice->MaHD]) }}"
                    data-status="{{ $status }}"
                    data-invoice-id="{{ $invoice->MaHD }}"
                    data-search="{{ \Illuminate\Support\Str::lower($searchText) }}"
                    data-invoice-date="{{ $invoice->NgayLapHD ? \Carbon\Carbon::parse($invoice->NgayLapHD)->toDateString() : '' }}"
                    data-invoice-due-date="{{ $settlementDueDate ? \Carbon\Carbon::parse($settlementDueDate)->toDateString() : '' }}"
                    data-invoice-settled-date="{{ $status === 1 && $settledAt ? \Carbon\Carbon::parse($settledAt)->toDateString() : '' }}"
                    tabindex="0"
                    role="link"
                >
                    <td>{{ $invoice->MaHD }}</td>
                    <td>{{ $formatDate($invoice->NgayLapHD) }}</td>
                    <td>{{ $formatDate($settlementDueDate) }}</td>
                    <td>{{ $status === 1 ? $formatDate($settledAt) : '--' }}</td>
                    <td>{{ $customerName ?? '--' }}</td>
                    <td>{{ $formatMoney($totalAmount) }}</td>
                    <td>{{ $formatMoney($paidAmount) }}</td>
                    <td>{{ $formatMoney($remainingAmount) }}</td>
                    <td>
                        <span class="rd-badge {{ $statusBadgeClasses[$status] ?? 'rd-badge--muted' }}">
                            {{ $statusLabels[$status] ?? 'Không xác định' }}
                        </span>
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

    <div class="d-flex flex-column align-items-center justify-content-center gap-2 mt-3" data-invoice-pagination-wrap>
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
            const dateFromInput = document.querySelector('[data-invoice-date-from]');
            const dateToInput = document.querySelector('[data-invoice-date-to]');
            const dueFromInput = document.querySelector('[data-invoice-due-from]');
            const dueToInput = document.querySelector('[data-invoice-due-to]');
            const settledFromInput = document.querySelector('[data-invoice-settled-from]');
            const settledToInput = document.querySelector('[data-invoice-settled-to]');
            const dateFromDisplay = document.querySelector('[data-date-display="invoice-date-from"]');
            const dateToDisplay = document.querySelector('[data-date-display="invoice-date-to"]');
            const dueFromDisplay = document.querySelector('[data-date-display="invoice-due-from"]');
            const dueToDisplay = document.querySelector('[data-date-display="invoice-due-to"]');
            const settledFromDisplay = document.querySelector('[data-date-display="invoice-settled-from"]');
            const settledToDisplay = document.querySelector('[data-date-display="invoice-settled-to"]');
            const tableBody = document.querySelector('[data-invoice-table-body]');
            const rows = Array.from(document.querySelectorAll('[data-invoice-row]'));
            const filterEmpty = document.querySelector('[data-invoice-filter-empty]');
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

                if (tableBody) {
                    const filteredRowSet = new Set(filteredRows);
                    filteredRows.forEach((row) => tableBody.appendChild(row));
                    rows
                        .filter((row) => !filteredRowSet.has(row))
                        .forEach((row) => tableBody.appendChild(row));
                    if (filterEmpty) {
                        tableBody.appendChild(filterEmpty);
                    }
                }

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

            const formatDate = (dateValue) => {
                if (!dateValue) {
                    return 'dd/mm/yyyy';
                }

                const dateParts = dateValue.split('-');
                return dateParts.length === 3
                    ? `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`
                    : dateValue;
            };

            const updateDateDisplays = () => {
                [
                    [dateFromInput, dateFromDisplay],
                    [dateToInput, dateToDisplay],
                    [dueFromInput, dueFromDisplay],
                    [dueToInput, dueToDisplay],
                    [settledFromInput, settledFromDisplay],
                    [settledToInput, settledToDisplay],
                ].forEach(([input, display]) => {
                    if (display) {
                        display.textContent = formatDate(input?.value || '');
                    }
                });
            };

            const matchesDateRange = (dateValue, fromValue, toValue) => {
                if ((fromValue || toValue) && !dateValue) {
                    return false;
                }

                return (!fromValue || dateValue >= fromValue) && (!toValue || dateValue <= toValue);
            };

            const applyFilters = () => {
                const keyword = (searchInput?.value || '').trim().toLowerCase();
                const status = statusFilter?.value || '';
                const dateFrom = dateFromInput?.value || '';
                const dateTo = dateToInput?.value || '';
                const dueFrom = dueFromInput?.value || '';
                const dueTo = dueToInput?.value || '';
                const settledFrom = settledFromInput?.value || '';
                const settledTo = settledToInput?.value || '';

                filteredRows = rows.filter((row) => {
                    const matchesKeyword = !keyword
                        || (/^\d+$/.test(keyword)
                            ? row.dataset.invoiceId === keyword
                            : (row.dataset.search || '').includes(keyword));
                    const matchesStatus = !status || row.dataset.status === status;
                    const invoiceDate = row.dataset.invoiceDate || '';
                    const dueDate = row.dataset.invoiceDueDate || '';
                    const settledDate = row.dataset.invoiceSettledDate || '';
                    const matchesInvoiceDate = matchesDateRange(invoiceDate, dateFrom, dateTo);
                    const matchesDueDate = matchesDateRange(dueDate, dueFrom, dueTo);
                    const matchesSettledDate = matchesDateRange(settledDate, settledFrom, settledTo);
                    return matchesKeyword && matchesStatus && matchesInvoiceDate && matchesDueDate && matchesSettledDate;
                });

                const activeSortDate = dateFrom || dateTo
                    ? 'invoiceDate'
                    : dueFrom || dueTo
                        ? 'invoiceDueDate'
                        : settledFrom || settledTo
                            ? 'invoiceSettledDate'
                            : '';

                if (activeSortDate) {
                    filteredRows.sort((firstRow, secondRow) => {
                        const firstDate = firstRow.dataset[activeSortDate] || '';
                        const secondDate = secondRow.dataset[activeSortDate] || '';
                        return firstDate.localeCompare(secondDate);
                    });
                }

                currentPage = 1;
                renderPagination();
            };

            searchInput?.addEventListener('input', applyFilters);
            statusFilter?.addEventListener('change', applyFilters);
            [
                dateFromInput,
                dateToInput,
                dueFromInput,
                dueToInput,
                settledFromInput,
                settledToInput,
            ].forEach((input) => {
                input?.addEventListener('input', () => {
                    updateDateDisplays();
                    applyFilters();
                });
                input?.addEventListener('change', () => {
                    updateDateDisplays();
                    applyFilters();
                });
            });
            prevButton?.addEventListener('click', () => {
                currentPage -= 1;
                renderPagination();
            });
            nextButton?.addEventListener('click', () => {
                currentPage += 1;
                renderPagination();
            });
            rows.forEach((row) => {
                const openDetail = () => {
                    const detailUrl = row.dataset.detailUrl;
                    if (detailUrl) {
                        window.location.href = detailUrl;
                    }
                };

                row.addEventListener('click', openDetail);
                row.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter') {
                        openDetail();
                    }
                });
            });
            updateDateDisplays();
            applyFilters();
        });
    </script>
</x-receptionist.index-page>
