<x-hotel-management.index-page
    title="Quản lý danh sách đặt phòng"
    subtitle="Danh sách thông tin đặt phòng"
    :show-create-button="false"
>
    <x-slot:filters>
        <div class="col-lg-4">
            <label class="form-label">Tìm kiếm</label>
            <input type="text" class="form-control" placeholder="Tìm theo mã đặt, mã CTĐP, tên khách hàng, SĐT" data-booking-search>
        </div>
        <div class="col-md-4 col-lg-3">
            <label class="form-label">Tình trạng</label>
            <div class="hm-select-wrap">
                <select class="form-select" data-booking-status>
                    <option value="">Tất cả tình trạng</option>
                    <option value="0">Chờ xác nhận</option>
                    <option value="1">Đã xác nhận</option>
                    <option value="2">Đang ở</option>
                    <option value="3">Đã trả phòng</option>
                    <option value="4">Đã hủy</option>
                </select>
            </div>
        </div>
    </x-slot:filters>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Mã đặt phòng</th>
                <th>Mã CTĐP</th>
                <th>Tên khách hàng</th>
                <th>Ngày đặt</th>
                <th>Ngày nhận</th>
                <th>Ngày trả</th>
                <th>Số lượng</th>
                <th>Tình trạng</th>
                <!-- <th style="min-width: 180px;">Thao tác</th> -->
            </tr>
        </thead>
        <tbody id="booking-table-body">
            <tr>
                <td colspan="8" class="text-center text-muted py-4">Đang tải dữ liệu đặt phòng...</td>
            </tr>
        </tbody>
    </table>

    <div
        id="booking-index-config"
        data-show-url-template="{{ route('hotel.bookings.show', ['recordId' => '__BOOKING_ID__']) }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableBody = document.getElementById('booking-table-body');
                const searchInput = document.querySelector('[data-booking-search]');
                const statusSelect = document.querySelector('[data-booking-status]');
                const filterPanel = document.querySelector('.hm-filter-panel');
                const config = document.getElementById('booking-index-config');
                const applyButton = filterPanel ? filterPanel.querySelector('.btn.btn-primary') : null;
                const resetButton = filterPanel ? filterPanel.querySelector('.btn.btn-light') : null;
                const showUrlTemplate = config ? config.dataset.showUrlTemplate : '';

                let bookings = @json($bookings ?? []);

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

                const mapStatus = function (status) {
                    switch (Number(status)) {
                        case 0:
                            return { label: 'Chờ xác nhận', badgeClass: 'warning' };
                        case 1:
                            return { label: 'Đã xác nhận', badgeClass: 'info' };
                        case 2:
                            return { label: 'Đang ở', badgeClass: 'success' };
                        case 3:
                            return { label: 'Đã trả phòng', badgeClass: 'muted' };
                        case 4:
                            return { label: 'Đã hủy', badgeClass: 'danger' };
                        default:
                            return { label: 'Không xác định', badgeClass: 'muted' };
                    }
                };

                const formatDateTime = function (value) {
                    if (!value) {
                        return '--';
                    }

                    const parts = String(value).split(' ');
                    const dateParts = parts[0] ? parts[0].split('-') : [];
                    const formattedDate = dateParts.length === 3 ? `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}` : parts[0];

                    return parts[1] ? `${formattedDate} ${parts[1]}` : formattedDate;
                };

                const formatDate = function (value) {
                    if (!value) {
                        return '--';
                    }

                    const raw = String(value).split(' ')[0];
                    const parts = raw.split('-');
                    return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : value;
                };

                const getRelation = function (record, camelName, snakeName) {
                    if (!record) {
                        return null;
                    }

                    return record[camelName] || record[snakeName] || null;
                };

                const getCustomerName = function (booking) {
                    const customer = getRelation(booking, 'khachHang', 'khach_hang');
                    return customer && customer.TenKH ? customer.TenKH : '--';
                };

                const getCustomerPhone = function (booking) {
                    const customer = getRelation(booking, 'khachHang', 'khach_hang');
                    return customer && customer.SoDienThoai ? customer.SoDienThoai : '';
                };

                const getBookingDetailIds = function (booking) {
                    const details = booking && (booking.chiTietDatPhong || booking.chi_tiet_dat_phong)
                        ? (booking.chiTietDatPhong || booking.chi_tiet_dat_phong)
                        : [];

                    return Array.isArray(details)
                        ? details
                            .map(function (detail) {
                                return detail && detail.MaCTDP ? String(detail.MaCTDP) : '';
                            })
                            .filter(Boolean)
                            .join(', ')
                        : '';
                };

                const renderRows = function (rows) {
                    if (!rows.length) {
                        tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Không có đặt phòng phù hợp.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = rows.map(function (booking) {
                        const status = mapStatus(booking.TinhTrang);
                        const showUrl = showUrlTemplate.replace('__BOOKING_ID__', booking.MaDatPhong);

                        return `
                            <tr class="hm-clickable-row" data-hm-row-link="${showUrl}" tabindex="0">
                                <td>${booking.MaDatPhong || '--'}</td>
                                <td>${getBookingDetailIds(booking) || '--'}</td>
                                <td>${getCustomerName(booking)}</td>
                                <td>${formatDateTime(booking.NgayDat)}</td>
                                <td>${formatDate(booking.NgayNhanPhong)}</td>
                                <td>${formatDate(booking.NgayTraPhong)}</td>
                                <td>${booking.SoLuong || 0}</td>
                                <td><span class="hm-badge hm-badge--${status.badgeClass}">${status.label}</span></td>
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

                    const filtered = bookings.filter(function (booking) {
                        const customerName = getCustomerName(booking);
                        const matchesKeyword = !keyword
                            || String(booking && booking.MaDatPhong ? booking.MaDatPhong : '').toLowerCase().includes(keyword)
                            || String(getBookingDetailIds(booking)).toLowerCase().includes(keyword)
                            || String(customerName).toLowerCase().includes(keyword)
                            || String(getCustomerPhone(booking)).toLowerCase().includes(keyword);

                        const matchesStatus = statusValue === ''
                            || String(booking && booking.TinhTrang !== undefined ? booking.TinhTrang : '') === statusValue;

                        return matchesKeyword && matchesStatus;
                    });

                    if (pagination) {
                        pagination.setItems(filtered);
                        return;
                    }

                    renderRows(filtered);
                };

                const loadBookings = function () {
                    bookings = (Array.isArray(bookings) ? bookings : []).slice().sort(function (left, right) {
                        return compareRecordIdDesc(left, right, 'MaDatPhong');
                    });
                    applyFilters();
                };

                if (applyButton) {
                    applyButton.remove();
                }

                if (filterPanel) {
                    const filterForm = filterPanel.querySelector('form');
                    if (filterForm) {
                        filterForm.addEventListener('submit', function (event) {
                            event.preventDefault();
                            applyFilters();
                        });
                    }
                }

                if (searchInput) {
                    searchInput.addEventListener('input', applyFilters);
                }

                if (statusSelect) {
                    statusSelect.addEventListener('change', applyFilters);
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

                loadBookings();
            });
        </script>
    @endpush
</x-hotel-management.index-page>
