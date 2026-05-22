<x-receptionist.index-page
    title="Quản lý thanh toán"
    subtitle="Danh sách quản lý thanh toán"
    table-title="Danh sách thanh toán"
>
    <x-slot:filters>
        <div class="col-lg-5">
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
            0 => 'Đặt cọc',
            1 => 'Thanh toán checkout',
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
                <tr data-payment-row data-status="{{ $invoiceStatus }}" data-search="{{ \Illuminate\Support\Str::lower($searchText) }}">
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
            const rows = Array.from(document.querySelectorAll('[data-payment-row]'));
            const filterEmpty = document.querySelector('[data-payment-filter-empty]');
            const applyButton = document.querySelector('.rd-filter-panel .btn.btn-primary');
            const resetButton = document.querySelector('.rd-filter-panel .btn.btn-light');
            const paginationWrap = document.querySelector('[data-payment-pagination-wrap]');
            const paginationInfo = document.querySelector('[data-payment-pagination-info]');
            const paginationPages = document.querySelector('[data-payment-pagination-pages]');
            const prevButton = document.querySelector('[data-payment-pagination-prev]');
            const nextButton = document.querySelector('[data-payment-pagination-next]');
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
