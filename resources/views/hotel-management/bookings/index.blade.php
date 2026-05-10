<x-hotel-management.index-page
    title="Quản lý danh sách đặt phòng"
    subtitle="Danh sách thông tin đặt phòng"
    :show-create-button="false"
>
    <x-slot:filters>
        <div class="col-lg-4">
            <label class="form-label">Tìm kiếm</label>
            <input type="text" class="form-control" placeholder="Tìm theo mã đặt, tên khách hàng" data-booking-search>
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
                <th>Tên khách hàng</th>
                <th>Ngày đặt</th>
                <th>Ngày nhận</th>
                <th>Ngày trả</th>
                <th>Số lượng</th>
                <th>Tình trạng</th>
                <th style="min-width: 180px;">Thao tác</th>
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

                let bookings = [];

                const customerMap = {};

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

                const getCustomerName = function (maKH) {
                    const customer = customerMap[maKH];
                    return customer && customer.TenKH ? customer.TenKH : '--';
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
                            <tr>
                                <td>${booking.MaDatPhong || '--'}</td>
                                <td>${getCustomerName(booking.MaKH)}</td>
                                <td>${formatDateTime(booking.NgayDat)}</td>
                                <td>${formatDate(booking.NgayNhanPhong)}</td>
                                <td>${formatDate(booking.NgayTraPhong)}</td>
                                <td>${booking.SoLuong || 0}</td>
                                <td><span class="hm-badge hm-badge--${status.badgeClass}">${status.label}</span></td>
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
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('');
                };

                const applyFilters = function () {
                    const keyword = ((searchInput ? searchInput.value : '') || '').trim().toLowerCase();
                    const statusValue = (statusSelect ? statusSelect.value : '') || '';

                    const filtered = bookings.filter(function (booking) {
                        const customerName = getCustomerName(booking.MaKH);
                        const matchesKeyword = !keyword
                            || String(booking && booking.MaDatPhong ? booking.MaDatPhong : '').toLowerCase().includes(keyword)
                            || String(booking && booking.MaKH ? booking.MaKH : '').toLowerCase().includes(keyword)
                            || String(customerName).toLowerCase().includes(keyword);

                        const matchesStatus = statusValue === ''
                            || String(booking && booking.TinhTrang !== undefined ? booking.TinhTrang : '') === statusValue;

                        return matchesKeyword && matchesStatus;
                    });

                    renderRows(filtered);
                };

                const loadBookings = async function () {
                    try {
                        const [bookingResponse, customerResponse] = await Promise.all([
                            fetch('/api/dat-phong', { headers: { 'Accept': 'application/json' } }),
                            fetch('/api/khach-hang', { headers: { 'Accept': 'application/json' } })
                        ]);

                        if (!bookingResponse.ok) {
                            throw new Error('Không thể tải danh sách đặt phòng.');
                        }

                        if (!customerResponse.ok) {
                            throw new Error('Không thể tải thông tin khách hàng.');
                        }

                        const bookingPayload = await bookingResponse.json();
                        const customers = await customerResponse.json();

                        bookings = bookingPayload && Array.isArray(bookingPayload.data) ? bookingPayload.data : [];

                        (Array.isArray(customers) ? customers : []).forEach(function (customer) {
                            customerMap[customer.MaKH] = customer;
                        });

                        applyFilters();
                    } catch (error) {
                        tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4">${error.message}</td></tr>`;
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
