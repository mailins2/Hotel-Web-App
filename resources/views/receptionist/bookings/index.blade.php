<x-receptionist.index-page
    title="Quản lý đặt phòng"
    subtitle="Danh sách quản lý đặt phòng"
    :create-route="route('reception.bookings.create')"
    create-label="Thêm đặt phòng"
    table-title="Danh sách thông tin đặt phòng"
    :show-filter-actions="false"
>
    <style>
        .rf-date-display-field {
            position: relative;
        }

        .rf-date-display-field .form-control {
            color: transparent;
            caret-color: transparent;
        }

        .rf-date-display-field .form-control:focus {
            color: transparent;
        }

        .rf-date-display-field .form-control::-webkit-datetime-edit {
            color: transparent;
        }

        .rf-date-display-value {
            position: absolute;
            left: 0.95rem;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #8a97aa;
            font: inherit;
        }

        [data-booking-row] {
            cursor: pointer;
        }

        [data-booking-row]:hover {
            background: #fff7ed;
        }

        .rb-cancel-button {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            border: 1px solid #dc2626;
            background: #fff;
            color: #dc2626;
        }

        .rb-cancel-button:hover,
        .rb-cancel-button:focus {
            border-color: #b91c1c;
            background: #fee2e2;
            color: #b91c1c;
        }

        .rb-cancel-button svg {
            width: 18px;
            height: 18px;
            display: block;
            flex-shrink: 0;
            pointer-events: none;
            overflow: visible;
        }

        .rb-cancel-button svg path {
            stroke: #dc2626 !important;
            fill: none !important;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .rb-cancel-button:hover svg path,
        .rb-cancel-button:focus svg path {
            stroke: #b91c1c !important;
        }

        .rb-cancel-column {
            text-align: center;
        }

        .rb-cancel-modal[hidden] {
            display: none;
        }

        .rb-cancel-modal {
            position: fixed;
            inset: 0;
            z-index: 1060;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .rb-cancel-modal__backdrop {
            position: absolute;
            inset: 0;
            background: rgba(33, 24, 22, 0.46);
        }

        .rb-cancel-modal__dialog {
            position: relative;
            width: min(460px, 100%);
            border: 1px solid rgba(151, 64, 26, 0.18);
            border-radius: 12px;
            background: #fffefa;
            box-shadow: 0 24px 70px rgba(67, 20, 7, 0.24);
            padding: 1.35rem;
        }

        .rb-cancel-modal__title {
            margin: 0 0 0.65rem;
            color: #7a270c;
            font-size: 1.15rem;
            font-weight: 700;
        }

        .rb-cancel-modal__text {
            margin: 0 0 0.75rem;
            color: #3a211b;
            line-height: 1.5;
        }

        .rb-cancel-modal__warning {
            margin: 0 0 1.15rem;
            color: #b91c1c;
            font-weight: 700;
        }

        .rb-cancel-modal__actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.65rem;
        }
    </style>

    <x-slot:filters>
        <div class="col-md-6 col-lg-4">
            <label class="form-label">Tìm kiếm</label>
            <input type="text" class="form-control" placeholder="Tìm theo mã đặt, tên khách, phòng" data-booking-search>
        </div>
        <div class="col-md-6 col-lg-2">
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
        <div class="col-md-6 col-lg-3">
            <label class="form-label">Ngày nhận phòng</label>
            <div class="rf-date-display-field">
                <input type="date" class="form-control" lang="en-GB" data-booking-checkin>
                <span class="rf-date-display-value" data-date-display="booking-checkin">dd/mm/yyyy</span>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <label class="form-label">Ngày trả phòng</label>
            <div class="rf-date-display-field">
                <input type="date" class="form-control" lang="en-GB" data-booking-checkout>
                <span class="rf-date-display-value" data-date-display="booking-checkout">dd/mm/yyyy</span>
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
                <th>Nhận phòng</th>
                <th>Trả phòng</th>
                <th>Tình trạng</th>
                <th class="rb-cancel-column">Hủy đặt phòng</th>
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
                    $status = (int) $booking->TinhTrang;
                    $searchText = collect([
                        $booking->MaDatPhong,
                        $booking->khachHang?->TenKH,
                        $booking->khachHang?->SoDienThoai,
                        $roomNumbers,
                    ])->filter()->implode(' ');
                    $canCancel = in_array($status, [
                        \App\Models\DatPhong::HOLD,
                        \App\Models\DatPhong::CONFIRMED,
                    ], true);
                @endphp
                <tr
                    data-booking-row
                    data-status="{{ $status }}"
                    data-search="{{ \Illuminate\Support\Str::lower($searchText) }}"
                    data-checkin="{{ $booking->NgayNhanPhong ? \Carbon\Carbon::parse($booking->NgayNhanPhong)->toDateString() : '' }}"
                    data-checkout="{{ $booking->NgayTraPhong ? \Carbon\Carbon::parse($booking->NgayTraPhong)->toDateString() : '' }}"
                    data-detail-url="{{ route('reception.bookings.show', ['bookingId' => $booking->MaDatPhong]) }}"
                >
                    <td>{{ $booking->MaDatPhong }}</td>
                    <td>{{ $booking->khachHang?->TenKH ?? '--' }}</td>
                    <td>{{ $roomNumbers ?: '--' }}</td>
                    <td>{{ $formatDate($booking->NgayNhanPhong) }}</td>
                    <td>{{ $formatDate($booking->NgayTraPhong) }}</td>
                    <td class="rb-cancel-column">
                        <span class="rd-badge {{ $statusBadgeClasses[$status] ?? 'rd-badge--muted' }}">
                            {{ $statusLabels[$status] ?? 'Không xác định' }}
                        </span>
                    </td>
                    <td>
                        @if($canCancel)
                            <button
                                type="button"
                                class="btn btn-sm rb-cancel-button"
                                title="Hủy đặt phòng"
                                aria-label="Hủy đặt phòng #{{ $booking->MaDatPhong }}"
                                data-booking-cancel
                                data-booking-id="{{ $booking->MaDatPhong }}"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 7H20"/>
                                    <path d="M10 11V17"/>
                                    <path d="M14 11V17"/>
                                    <path d="M6 7L7 20H17L18 7"/>
                                    <path d="M9 7V4H15V7"/>
                                </svg>
                            </button>
                        @else
                            <span class="text-muted">--</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Chưa có dữ liệu đặt phòng.</td>
                </tr>
            @endforelse
            <tr class="d-none" data-booking-filter-empty>
                <td colspan="7" class="text-center text-muted py-4">Không có đặt phòng phù hợp.</td>
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

    <div class="rb-cancel-modal" data-booking-cancel-modal hidden>
        <div class="rb-cancel-modal__backdrop" data-booking-cancel-close></div>
        <div class="rb-cancel-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="bookingCancelTitle">
            <h5 id="bookingCancelTitle" class="rb-cancel-modal__title">Xác nhận hủy đặt phòng</h5>
            <p class="rb-cancel-modal__text">
                Bạn có chắc chắn muốn hủy đặt phòng <strong data-booking-cancel-label></strong> không?
            </p>
            <p class="rb-cancel-modal__warning">Hành động này không thể hoàn tác.</p>
            <div class="rb-cancel-modal__actions">
                <button type="button" class="btn btn-light btn-sm" data-booking-cancel-close>Không</button>
                <button type="button" class="btn btn-danger btn-sm" data-booking-cancel-confirm>Xác nhận hủy</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.querySelector('[data-booking-search]');
            const statusFilter = document.querySelector('[data-booking-status-filter]');
            const checkinInput = document.querySelector('[data-booking-checkin]');
            const checkoutInput = document.querySelector('[data-booking-checkout]');
            const checkinDisplay = document.querySelector('[data-date-display="booking-checkin"]');
            const checkoutDisplay = document.querySelector('[data-date-display="booking-checkout"]');
            const rows = Array.from(document.querySelectorAll('[data-booking-row]'));
            const filterEmpty = document.querySelector('[data-booking-filter-empty]');
            const paginationWrap = document.querySelector('[data-booking-pagination-wrap]');
            const paginationInfo = document.querySelector('[data-booking-pagination-info]');
            const paginationPages = document.querySelector('[data-booking-pagination-pages]');
            const prevButton = document.querySelector('[data-booking-pagination-prev]');
            const nextButton = document.querySelector('[data-booking-pagination-next]');
            const cancelModal = document.querySelector('[data-booking-cancel-modal]');
            const cancelLabel = document.querySelector('[data-booking-cancel-label]');
            const cancelConfirmButton = document.querySelector('[data-booking-cancel-confirm]');
            const cancelCloseButtons = document.querySelectorAll('[data-booking-cancel-close]');
            const pageSize = 10;
            let currentPage = 1;
            let filteredRows = rows;
            let selectedCancelBookingId = '';
            let selectedCancelButton = null;

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
                if (checkinDisplay) {
                    checkinDisplay.textContent = formatDate(checkinInput?.value || '');
                }

                if (checkoutDisplay) {
                    checkoutDisplay.textContent = formatDate(checkoutInput?.value || '');
                }
            };

            const closeCancelModal = () => {
                selectedCancelBookingId = '';
                selectedCancelButton = null;
                if (cancelModal) {
                    cancelModal.hidden = true;
                }
                if (cancelLabel) {
                    cancelLabel.textContent = '';
                }
            };

            const openCancelModal = (bookingId, button) => {
                selectedCancelBookingId = bookingId;
                selectedCancelButton = button;
                if (cancelLabel) {
                    cancelLabel.textContent = `#${bookingId}`;
                }
                if (cancelModal) {
                    cancelModal.hidden = false;
                }
            };

            const cancelBooking = async () => {
                if (!selectedCancelBookingId || !selectedCancelButton) {
                    return;
                }

                selectedCancelButton.disabled = true;
                if (cancelConfirmButton) {
                    cancelConfirmButton.disabled = true;
                    cancelConfirmButton.textContent = 'Đang hủy...';
                }

                try {
                    const response = await fetch(`/api/dat-phong/${encodeURIComponent(selectedCancelBookingId)}/cancel`, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                        },
                    });
                    const payload = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        throw new Error(payload.message || 'Không thể hủy đặt phòng.');
                    }

                    window.location.reload();
                } catch (error) {
                    selectedCancelButton.disabled = false;
                    if (cancelConfirmButton) {
                        cancelConfirmButton.disabled = false;
                        cancelConfirmButton.textContent = 'Xác nhận hủy';
                    }
                    alert(error.message || 'Không thể hủy đặt phòng.');
                }
            };

            const applyFilters = () => {
                const keyword = (searchInput?.value || '').trim().toLowerCase();
                const status = statusFilter?.value || '';
                const checkinDate = checkinInput?.value || '';
                const checkoutDate = checkoutInput?.value || '';

                filteredRows = rows.filter((row) => {
                    const matchesKeyword = !keyword || (row.dataset.search || '').includes(keyword);
                    const matchesStatus = !status || row.dataset.status === status;
                    const matchesCheckin = !checkinDate || row.dataset.checkin === checkinDate;
                    const matchesCheckout = !checkoutDate || row.dataset.checkout === checkoutDate;
                    return matchesKeyword && matchesStatus && matchesCheckin && matchesCheckout;
                });

                currentPage = 1;
                renderPagination();
            };

            searchInput?.addEventListener('input', applyFilters);
            statusFilter?.addEventListener('change', applyFilters);
            document.querySelectorAll('[data-booking-cancel]').forEach((button) => {
                button.addEventListener('click', () => {
                    const bookingId = button.dataset.bookingId;
                    if (!bookingId) {
                        return;
                    }

                    openCancelModal(bookingId, button);
                });
            });
            cancelConfirmButton?.addEventListener('click', cancelBooking);
            cancelCloseButtons.forEach((button) => button.addEventListener('click', closeCancelModal));
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && cancelModal && !cancelModal.hidden) {
                    closeCancelModal();
                }
            });
            rows.forEach((row) => {
                row.addEventListener('click', (event) => {
                    if (event.target.closest('a, button, input, select, textarea')) {
                        return;
                    }

                    const detailUrl = row.dataset.detailUrl;
                    if (detailUrl) {
                        window.location.href = detailUrl;
                    }
                });
            });
            checkinInput?.addEventListener('input', () => {
                updateDateDisplays();
                applyFilters();
            });
            checkinInput?.addEventListener('change', () => {
                updateDateDisplays();
                applyFilters();
            });
            checkoutInput?.addEventListener('input', () => {
                updateDateDisplays();
                applyFilters();
            });
            checkoutInput?.addEventListener('change', () => {
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
            updateDateDisplays();
            applyFilters();
        });
    </script>
</x-receptionist.index-page>
