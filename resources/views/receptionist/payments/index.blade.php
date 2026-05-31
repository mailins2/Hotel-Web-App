<x-receptionist.index-page
    title="Quản lý thanh toán"
    subtitle="Danh sách quản lý thanh toán"
    table-title="Danh sách thanh toán"
>
    <style>
        .rp-date-display-field {
            position: relative;
        }

        .rp-date-display-field .form-control {
            color: transparent;
            caret-color: transparent;
        }

        .rp-date-display-field .form-control:focus,
        .rp-date-display-field .form-control::-webkit-datetime-edit {
            color: transparent;
        }

        .rp-date-display-value {
            position: absolute;
            left: 0.95rem;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #8a97aa;
            font: inherit;
        }

        .rd-filter-panel .btn.btn-primary,
        .rd-filter-panel .btn.btn-light {
            display: none !important;
        }

        [data-payment-table-body] tr > :nth-child(10),
        [data-payment-table-body] tr > :nth-child(11),
        .table thead tr > :nth-child(10),
        .table thead tr > :nth-child(11) {
            display: none;
        }

        tr[data-payment-row] {
            cursor: pointer;
        }

        tr[data-payment-row]:hover {
            background: #fff7ed;
        }
    </style>

    <x-slot:filters>
        <div class="col-md-6 col-lg-3">
            <label class="form-label">Tìm kiếm</label>
            <input type="text" class="form-control" placeholder="Tìm theo mã thanh toán, hóa đơn, khách hàng" data-payment-search>
        </div>
        <div class="col-md-4 col-lg-3">
            <label class="form-label">Trạng thái hóa đơn</label>
            <div class="rd-select-wrap">
                <select class="form-select" data-payment-status-filter>
                    <option value="">Tất cả trạng thái</option>
                    <option value="0">Chưa thanh toán</option>
                    <option value="1">Đã thanh toán</option>
                    <option value="3">Đã hủy</option>
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-2">
            <label class="form-label">Loại thanh toán</label>
            <div class="rd-select-wrap">
                <select class="form-select" data-payment-type-filter>
                    <option value="">Tất cả loại</option>
                    <option value="0">Thanh toán tiền phòng</option>
                    <option value="1">Thanh toán trả phòng</option>
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-2">
            <label class="form-label">Ngày thanh toán từ</label>
            <div class="rp-date-display-field">
                <input type="date" class="form-control" lang="en-GB" data-payment-date-from>
                <span class="rp-date-display-value" data-date-display="payment-date-from">dd/mm/yyyy</span>
            </div>
        </div>
        <div class="col-md-6 col-lg-2">
            <label class="form-label">Ngày thanh toán đến</label>
            <div class="rp-date-display-field">
                <input type="date" class="form-control" lang="en-GB" data-payment-date-to>
                <span class="rp-date-display-value" data-date-display="payment-date-to">dd/mm/yyyy</span>
            </div>
        </div>
    </x-slot:filters>

    @php
        $paymentList = $payments ?? collect();
        $formatMoney = fn ($amount) => number_format((float) ($amount ?? 0), 0, ',', '.') . ' VNĐ';
        $formatDateTime = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y H:i') : '--';

        $invoiceStatusLabels = [
            0 => 'Chưa thanh toán',
            1 => 'Đã thanh toán',
            3 => 'Đã hủy',
        ];

        $invoiceStatusBadgeClasses = [
            0 => 'rd-badge--warning',
            1 => 'rd-badge--success',
            3 => 'rd-badge--danger',
        ];

        $paymentTypes = [
            0 => 'Thanh toán tiền phòng',
            1 => 'Thanh toán trả phòng',
        ];

        $paymentMethods = [
            1 => 'Thẻ',
            2 => 'QR Code',
        ];

        $transactionStatusLabels = [
            0 => 'Chờ xử lý',
            1 => 'Thành công',
            2 => 'Thất bại',
        ];

        $transactionStatusBadgeClasses = [
            0 => 'rd-badge--warning',
            1 => 'rd-badge--success',
            2 => 'rd-badge--danger',
        ];
    @endphp

    <table class="table align-middle">
        <thead>
            <tr>
                <th>Mã thanh toán</th>
                <th>Mã hóa đơn</th>
                <th>Người thanh toán</th>
                <th>Số tiền</th>
                <th>Phương thức</th>
                <th>Loại thanh toán</th>
                <th>Ngày thanh toán</th>
                <th>Nhà cung cấp</th>
                <th>Trạng thái giao dịch</th>
                <th>Trạng thái hóa đơn</th>
                <th style="min-width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody data-payment-table-body>
            @forelse($paymentList as $payment)
                @php
                    $invoice = $payment->hoaDon;
                    $booking = $invoice?->datPhong;
                    $customer = $booking?->khachHang;
                    $invoiceStatus = (int) ($invoice?->TrangThai ?? -1);
                    $transactionStatus = (int) ($payment->TrangThaiGiaoDich ?? 1);
                    $payerName = $customer?->TenKH ?? $payment->DinhDanhNguoiThanhToan;
                    $searchText = collect([
                        $payment->MaTT,
                        $payment->MaHD,
                        $booking?->MaDatPhong,
                        $payerName,
                        $payment->MaGiaoDich,
                        $payment->MaGiaoDichCongThanhToan,
                    ])->filter()->implode(' ');
                @endphp
                <tr
                    data-payment-row
                    data-detail-url="{{ route('reception.payments.show', ['paymentId' => $payment->MaTT]) }}"
                    data-status="{{ $transactionStatus }}"
                    data-payment-type="{{ (int) ($payment->LoaiThanhToan ?? -1) }}"
                    data-payment-date="{{ $payment->NgayThanhToan ? \Carbon\Carbon::parse($payment->NgayThanhToan)->toDateString() : '' }}"
                    data-search="{{ \Illuminate\Support\Str::lower($searchText) }}"
                    tabindex="0"
                    role="link"
                >
                    <td>{{ $payment->MaTT }}</td>
                    <td>{{ $payment->MaHD ?? '--' }}</td>
                    <td>{{ $payerName ?? '--' }}</td>
                    <td>{{ $formatMoney($payment->SoTien) }}</td>
                    <td>{{ $paymentMethods[(int) $payment->PhuongThuc] ?? '--' }}</td>
                    <td>{{ $paymentTypes[(int) $payment->LoaiThanhToan] ?? '--' }}</td>
                    <td>{{ $formatDateTime($payment->NgayThanhToan) }}</td>
                    <td>{{ $payment->NhaCungCap ?? 'manual' }}</td>
                    <td>
                        <span class="rd-badge {{ $transactionStatusBadgeClasses[$transactionStatus] ?? 'rd-badge--muted' }}">
                            {{ $transactionStatusLabels[$transactionStatus] ?? 'Không xác định' }}
                        </span>
                    </td>
                    <td>
                        <span class="rd-badge {{ $invoiceStatusBadgeClasses[$invoiceStatus] ?? 'rd-badge--muted' }}">
                            {{ $invoiceStatusLabels[$invoiceStatus] ?? 'Không xác định' }}
                        </span>
                    </td>
                    <td>
                        @include('hotel-management.partials.action-icons', [
                            'showUrl' => route('reception.payments.show', ['paymentId' => $payment->MaTT]),
                            'editUrl' => null,
                            'showDelete' => false,
                        ])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center text-muted py-4">Chưa có dữ liệu thanh toán.</td>
                </tr>
            @endforelse
            <tr class="d-none" data-payment-filter-empty>
                <td colspan="11" class="text-center text-muted py-4">Không có thanh toán phù hợp.</td>
            </tr>
        </tbody>
    </table>

    <div class="d-flex flex-column align-items-center justify-content-center gap-2 mt-3" data-payment-pagination-wrap>
        <div class="text-muted small" data-payment-pagination-info></div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-light btn-sm" data-payment-pagination-prev>Trước</button>
            <div class="d-flex align-items-center gap-1" data-payment-pagination-pages></div>
            <button type="button" class="btn btn-light btn-sm" data-payment-pagination-next>Sau</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.querySelector('[data-payment-search]');
            const statusFilter = document.querySelector('[data-payment-status-filter]');
            const typeFilter = document.querySelector('[data-payment-type-filter]');
            const dateFromInput = document.querySelector('[data-payment-date-from]');
            const dateToInput = document.querySelector('[data-payment-date-to]');
            const dateFromDisplay = document.querySelector('[data-date-display="payment-date-from"]');
            const dateToDisplay = document.querySelector('[data-date-display="payment-date-to"]');
            const rows = Array.from(document.querySelectorAll('[data-payment-row]'));
            const filterEmpty = document.querySelector('[data-payment-filter-empty]');
            const paginationWrap = document.querySelector('[data-payment-pagination-wrap]');
            const paginationInfo = document.querySelector('[data-payment-pagination-info]');
            const paginationPages = document.querySelector('[data-payment-pagination-pages]');
            const prevButton = document.querySelector('[data-payment-pagination-prev]');
            const nextButton = document.querySelector('[data-payment-pagination-next]');
            const pageSize = 10;
            let currentPage = 1;
            let filteredRows = rows;

            if (statusFilter) {
                const statusLabel = statusFilter.closest('[class*="col-"]')?.querySelector('.form-label');
                if (statusLabel) {
                    statusLabel.textContent = 'Trạng thái giao dịch';
                }

                statusFilter.innerHTML = `
                    <option value="">Tất cả trạng thái</option>
                    <option value="0">Chờ xử lý</option>
                    <option value="1">Thành công</option>
                    <option value="2">Thất bại</option>
                `;
            }

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
                    paginationInfo.textContent = `Hiển thị ${from}-${to} / ${totalRows} thanh toán`;
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
                if (dateFromDisplay) {
                    dateFromDisplay.textContent = formatDate(dateFromInput?.value || '');
                }

                if (dateToDisplay) {
                    dateToDisplay.textContent = formatDate(dateToInput?.value || '');
                }
            };

            const applyFilters = () => {
                const keyword = (searchInput?.value || '').trim().toLowerCase();
                const status = statusFilter?.value || '';
                const type = typeFilter?.value || '';
                const dateFrom = dateFromInput?.value || '';
                const dateTo = dateToInput?.value || '';

                filteredRows = rows.filter((row) => {
                    const matchesKeyword = !keyword || (row.dataset.search || '').includes(keyword);
                    const matchesStatus = !status || row.dataset.status === status;
                    const matchesType = !type || row.dataset.paymentType === type;
                    const paymentDate = row.dataset.paymentDate || '';
                    const matchesDateFrom = !dateFrom || (paymentDate && paymentDate >= dateFrom);
                    const matchesDateTo = !dateTo || (paymentDate && paymentDate <= dateTo);
                    return matchesKeyword && matchesStatus && matchesType && matchesDateFrom && matchesDateTo;
                });

                if (dateFrom || dateTo) {
                    filteredRows.sort((firstRow, secondRow) => {
                        const firstDate = firstRow.dataset.paymentDate || '';
                        const secondDate = secondRow.dataset.paymentDate || '';
                        return firstDate.localeCompare(secondDate);
                    });
                }

                currentPage = 1;
                renderPagination();
            };

            searchInput?.addEventListener('input', applyFilters);
            statusFilter?.addEventListener('change', applyFilters);
            typeFilter?.addEventListener('change', applyFilters);
            dateFromInput?.addEventListener('input', () => {
                updateDateDisplays();
                applyFilters();
            });
            dateFromInput?.addEventListener('change', () => {
                updateDateDisplays();
                applyFilters();
            });
            dateToInput?.addEventListener('input', () => {
                updateDateDisplays();
                applyFilters();
            });
            dateToInput?.addEventListener('change', () => {
                updateDateDisplays();
                applyFilters();
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
