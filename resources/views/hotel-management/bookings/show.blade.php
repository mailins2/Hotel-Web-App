<x-app-layout :assets="['animation']">
    <style>
        .hb-detail-page {
            background:
                radial-gradient(circle at top left, rgba(247, 195, 144, 0.18), transparent 36%),
                linear-gradient(180deg, #fff7f0 0%, #ffffff 100%);
            border-radius: 28px;
            padding: 1.5rem;
        }

        .hb-section-card {
            height: 100%;
            padding: 1.45rem;
            border: 1px solid #f2dcc7;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 20px 45px -38px rgba(166, 91, 31, 0.5);
        }

        .hb-section-title {
            margin-bottom: 1.2rem;
            font-size: 1.5rem;
            font-weight: 600;
            color: #111827;
        }

        .hb-field-grid {
            display: grid;
            gap: 1.1rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .hb-field-card {
            padding: 1.15rem 1.2rem;
            border: 1px solid #f4dcc2;
            border-radius: 22px;
            background: #fff;
            min-height: 92px;
        }

        .hb-field-label {
            margin-bottom: 0.55rem;
            font-size: 0.88rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #b06f34;
        }

        .hb-field-value {
            font-size: 1rem;
            font-weight: 600;
            line-height: 1.45;
            color: #0f172a;
            word-break: break-word;
        }

        .hm-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.4rem 0.82rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .hm-badge--warning { background: #fef3c7; color: #9a3412; }
        .hm-badge--info { background: #dbeafe; color: #1d4ed8; }
        .hm-badge--success { background: #dcfce7; color: #166534; }
        .hm-badge--muted { background: #eceff3; color: #475569; }
        .hm-badge--danger { background: #fee2e2; color: #b91c1c; }

        @media (max-width: 991.98px) {
            .hb-section-title {
                font-size: 1.5rem;
            }

            .hb-field-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 pb-4">
                    <div class="header-title">
                        <h4 class="card-title mb-1">Chi tiết đặt phòng</h4>
                        <p class="mb-0 text-muted">Trang xem chi tiết thông tin đặt phòng</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('hotel.bookings.index') }}" class="btn btn-sm btn-primary" style="padding: 10px 14px;">Quay lại</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="hb-detail-page">
                        <div class="row g-4">
                            <div class="col-xl-6">
                                <section class="hb-section-card">
                                    <h2 class="hb-section-title">Thông tin khách hàng đặt</h2>
                                    <div class="hb-field-grid">
                                        <div class="hb-field-card">
                                            <div class="hb-field-label">Mã khách hàng</div>
                                            <div class="hb-field-value" id="booking-customer-id">Đang tải...</div>
                                        </div>
                                        <div class="hb-field-card">
                                            <div class="hb-field-label">Tên khách hàng</div>
                                            <div class="hb-field-value" id="booking-customer-name">Đang tải...</div>
                                        </div>
                                        <div class="hb-field-card">
                                            <div class="hb-field-label">Số điện thoại</div>
                                            <div class="hb-field-value" id="booking-customer-phone">Đang tải...</div>
                                        </div>
                                        <div class="hb-field-card">
                                            <div class="hb-field-label">Email</div>
                                            <div class="hb-field-value" id="booking-customer-email">Đang tải...</div>
                                        </div>
                                        <div class="hb-field-card">
                                            <div class="hb-field-label">CCCD</div>
                                            <div class="hb-field-value" id="booking-customer-cccd">Đang tải...</div>
                                        </div>
                                    </div>
                                </section>
                            </div>

                            <div class="col-xl-6">
                                <section class="hb-section-card">
                                    <h2 class="hb-section-title">Thông tin phòng đặt</h2>
                                    <div class="hb-field-grid">
                                        <div class="hb-field-card">
                                            <div class="hb-field-label">Số phòng</div>
                                            <div class="hb-field-value" id="booking-room-numbers">Đang tải...</div>
                                        </div>
                                        <div class="hb-field-card">
                                            <div class="hb-field-label">Loại phòng</div>
                                            <div class="hb-field-value" id="booking-room-types">Đang tải...</div>
                                        </div>
                                        <div class="hb-field-card">
                                            <div class="hb-field-label">Sức chứa tối đa</div>
                                            <div class="hb-field-value" id="booking-room-capacity">Đang tải...</div>
                                        </div>
                                        <div class="hb-field-card">
                                            <div class="hb-field-label">Giá phòng</div>
                                            <div class="hb-field-value" id="booking-room-price">Đang tải...</div>
                                        </div>
                                    </div>
                                </section>
                            </div>

                            <div class="col-12">
                                <section class="hb-section-card">
                                    <h2 class="hb-section-title">Thông tin đặt phòng</h2>
                                    <div class="hb-field-grid">
                                        <div class="hb-field-card">
                                            <div class="hb-field-label">Mã đặt phòng</div>
                                            <div class="hb-field-value" id="booking-id">Đang tải...</div>
                                        </div>
                                        <div class="hb-field-card">
                                            <div class="hb-field-label">Ngày đặt phòng</div>
                                            <div class="hb-field-value" id="booking-created-at">Đang tải...</div>
                                        </div>
                                        <div class="hb-field-card">
                                            <div class="hb-field-label">Ngày nhận phòng</div>
                                            <div class="hb-field-value" id="booking-check-in">Đang tải...</div>
                                        </div>
                                        <div class="hb-field-card">
                                            <div class="hb-field-label">Ngày trả phòng</div>
                                            <div class="hb-field-value" id="booking-check-out">Đang tải...</div>
                                        </div>
                                        <div class="hb-field-card">
                                            <div class="hb-field-label">Thời gian lưu trú</div>
                                            <div class="hb-field-value" id="booking-stay-duration">Đang tải...</div>
                                        </div>
                                        <div class="hb-field-card">
                                            <div class="hb-field-label">Số lượng</div>
                                            <div class="hb-field-value" id="booking-quantity">Đang tải...</div>
                                        </div>
                                        <div class="hb-field-card">
                                            <div class="hb-field-label">Tiền đặt cọc</div>
                                            <div class="hb-field-value" id="booking-deposit">Đang tải...</div>
                                        </div>
                                        <div class="hb-field-card">
                                            <div class="hb-field-label">Trạng thái đặt phòng</div>
                                            <div class="hb-field-value" id="booking-status">Đang tải...</div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="booking-show-config" data-booking-id="{{ request()->route('recordId') }}" hidden></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', async function () {
                const config = document.getElementById('booking-show-config');
                const bookingId = config ? config.dataset.bookingId : '';

                const formatDate = function (value) {
                    if (!value) {
                        return '--';
                    }

                    const raw = String(value).split(' ')[0];
                    const parts = raw.split('-');
                    return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : value;
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

                const formatCurrency = function (value) {
                    const number = Number(value);
                    if (Number.isNaN(number)) {
                        return '--';
                    }
                    return number.toLocaleString('vi-VN') + ' VNĐ';
                };

                const mapStatus = function (status) {
                    switch (Number(status)) {
                        case 0:
                            return '<span class="hm-badge hm-badge--warning">Chờ xác nhận</span>';
                        case 1:
                            return '<span class="hm-badge hm-badge--info">Đã xác nhận</span>';
                        case 2:
                            return '<span class="hm-badge hm-badge--success">Đang ở</span>';
                        case 3:
                            return '<span class="hm-badge hm-badge--muted">Đã trả phòng</span>';
                        case 4:
                            return '<span class="hm-badge hm-badge--danger">Đã hủy</span>';
                        default:
                            return '--';
                    }
                };

                const getNightCount = function (checkIn, checkOut) {
                    if (!checkIn || !checkOut) {
                        return '--';
                    }

                    const start = new Date(checkIn);
                    const end = new Date(checkOut);
                    const diff = Math.round((end - start) / 86400000);

                    if (Number.isNaN(diff) || diff < 0) {
                        return '--';
                    }

                    return `${diff} đêm`;
                };

                const setText = function (id, value) {
                    const element = document.getElementById(id);
                    if (element) {
                        element.textContent = value;
                    }
                };

                try {
                    const bookingResponse = await fetch(`/api/dat-phong/${bookingId}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!bookingResponse.ok) {
                        throw new Error('Không thể tải chi tiết đặt phòng.');
                    }

                    const bookingPayload = await bookingResponse.json();
                    const booking = bookingPayload && bookingPayload.data ? bookingPayload.data : null;

                    if (!booking) {
                        throw new Error('Không tìm thấy đặt phòng.');
                    }

                    const relatedRequests = [
                        booking.MaKH
                            ? fetch(`/api/khach-hang/${booking.MaKH}`, { headers: { 'Accept': 'application/json' } })
                            : Promise.resolve(null),
                        fetch('/api/hoa-don', { headers: { 'Accept': 'application/json' } }),
                        fetch('/api/loai-phong', { headers: { 'Accept': 'application/json' } })
                    ];

                    const [customerResponse, invoiceListResponse, roomTypeListResponse] = await Promise.all(relatedRequests);

                    const customer = customerResponse && customerResponse.ok ? await customerResponse.json() : null;
                    const invoices = invoiceListResponse && invoiceListResponse.ok ? await invoiceListResponse.json() : [];
                    const roomTypePayload = roomTypeListResponse && roomTypeListResponse.ok ? await roomTypeListResponse.json() : { data: [] };
                    const roomTypes = roomTypePayload && Array.isArray(roomTypePayload.data) ? roomTypePayload.data : [];
                    const roomTypeMap = {};

                    roomTypes.forEach(function (roomType) {
                        roomTypeMap[roomType.MaLoaiPhong] = roomType;
                    });

                    let invoiceDetail = null;
                    const invoice = (Array.isArray(invoices) ? invoices : []).find(function (item) {
                        return Number(item.MaDatPhong) === Number(booking.MaDatPhong);
                    });

                    if (invoice && invoice.MaHD) {
                        const invoiceDetailResponse = await fetch(`/api/hoa-don/${invoice.MaHD}`, {
                            headers: { 'Accept': 'application/json' }
                        });

                        if (invoiceDetailResponse.ok) {
                            invoiceDetail = await invoiceDetailResponse.json();
                        }
                    }

                    const roomDetails = Array.isArray(booking.chi_tiet_dat_phong) ? booking.chi_tiet_dat_phong : [];
                    const roomNumbers = roomDetails
                        .map(function (item) { return item && item.phong ? item.phong.SoPhong : null; })
                        .filter(Boolean);

                    const uniqueRoomTypeIds = Array.from(new Set(roomDetails
                        .map(function (item) { return item && item.phong ? item.phong.MaLoaiPhong : null; })
                        .filter(Boolean)));

                    const roomTypeNames = uniqueRoomTypeIds
                        .map(function (id) {
                            const roomType = roomTypeMap[id];
                            return roomType && roomType.TenLoaiPhong ? roomType.TenLoaiPhong : null;
                        })
                        .filter(Boolean);

                    const totalCapacity = uniqueRoomTypeIds.reduce(function (total, id) {
                        const roomType = roomTypeMap[id];
                        const matchingRooms = roomDetails.filter(function (item) {
                            return item && item.phong && Number(item.phong.MaLoaiPhong) === Number(id);
                        }).length;
                        const capacity = roomType ? Number(roomType.NguoiLon || 0) + Number(roomType.TreEm || 0) : 0;
                        return total + (capacity * matchingRooms);
                    }, 0);

                    let roomCharge = null;
                    let deposit = null;

                    if (invoiceDetail && invoiceDetail.hoaDon) {
                        const roomLines = Array.isArray(invoiceDetail.hoaDon.chi_tiet_hoa_dons)
                            ? invoiceDetail.hoaDon.chi_tiet_hoa_dons.filter(function (line) {
                                return line && line.MaLoaiPhong && !line.MaSuDung && !line.MaDenBu;
                            })
                            : [];

                        const paymentLines = Array.isArray(invoiceDetail.hoaDon.thanh_toans)
                            ? invoiceDetail.hoaDon.thanh_toans
                            : [];

                        roomCharge = roomLines.reduce(function (sum, line) {
                            return sum + (Number(line.SoLuong || 0) * Number(line.DonGia || 0));
                        }, 0);

                        deposit = paymentLines.reduce(function (sum, payment) {
                            return Number(payment.LoaiThanhToan) === 0 ? sum + Number(payment.SoTien || 0) : sum;
                        }, 0);
                    }

                    setText('booking-customer-id', booking.MaKH || '--');
                    setText('booking-customer-name', customer && customer.TenKH ? customer.TenKH : '--');
                    setText('booking-customer-phone', customer && customer.SoDienThoai ? customer.SoDienThoai : '--');
                    setText('booking-customer-email', customer && customer.tai_khoan && customer.tai_khoan.Email ? customer.tai_khoan.Email : '--');
                    setText('booking-customer-cccd', customer && customer.CCCD ? customer.CCCD : '--');
                    setText('booking-room-numbers', roomNumbers.length ? roomNumbers.join(', ') : '--');
                    setText('booking-room-types', roomTypeNames.length ? roomTypeNames.join(', ') : '--');
                    setText('booking-room-capacity', totalCapacity > 0 ? `${totalCapacity} khách` : '--');
                    setText('booking-room-price', roomCharge !== null ? formatCurrency(roomCharge) : '--');

                    setText('booking-id', booking.MaDatPhong || '--');
                    setText('booking-created-at', formatDateTime(booking.NgayDat));
                    setText('booking-check-in', formatDate(booking.NgayNhanPhong));
                    setText('booking-check-out', formatDate(booking.NgayTraPhong));
                    setText('booking-stay-duration', getNightCount(booking.NgayNhanPhong, booking.NgayTraPhong));
                    setText('booking-quantity', booking.SoLuong ? `${booking.SoLuong} phòng` : '--');
                    setText('booking-deposit', deposit !== null ? formatCurrency(deposit) : '--');
                    document.getElementById('booking-status').innerHTML = mapStatus(booking.TinhTrang);
                } catch (error) {
                    [
                        'booking-customer-id',
                        'booking-customer-phone',
                        'booking-customer-email',
                        'booking-customer-cccd',
                        'booking-room-numbers',
                        'booking-room-types',
                        'booking-room-capacity',
                        'booking-room-price',
                        'booking-id',
                        'booking-created-at',
                        'booking-check-in',
                        'booking-check-out',
                        'booking-stay-duration',
                        'booking-quantity',
                        'booking-deposit'
                    ].forEach(function (id) {
                        setText(id, '--');
                    });

                    setText('booking-customer-name', error.message);
                    setText('booking-status', '--');
                }
            });
        </script>
    @endpush
</x-app-layout>
