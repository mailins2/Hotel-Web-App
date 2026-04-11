<x-app-layout :assets="$assets ?? []">
    @php
        $stats = $page['stats'] ?? [];
        $bookings = $page['bookings'] ?? [];
        $checkedInBooking = $page['checked_in_booking'] ?? null;
        $selectedBookingId = (string) ($page['selected_booking_id'] ?? '');
    @endphp

    <style>
        .ci-shell {
            --ci-text: #3f2b1d;
            --ci-muted: #866b59;
            --ci-line: rgba(166, 98, 43, 0.15);
            --ci-hero: linear-gradient(180deg, #fff7ef 0%, #fff 55%, #f3fbfa 100%);
            --ci-teal: #0f766e;
            padding-top: 4.5rem;
            color: var(--ci-text);
        }

        .ci-hero,
        .ci-card {
            background: #fff;
            border: 1px solid var(--ci-line);
            border-radius: 28px;
            box-shadow: 0 18px 40px rgba(120, 74, 44, 0.08);
        }

        .ci-hero {
            padding: 1.8rem;
            background: var(--ci-hero);
            margin-bottom: 1.5rem;
        }

        .ci-eyebrow {
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            color: #a65422;
            margin-bottom: 0.7rem;
        }

        .ci-title {
            font-size: 2.1rem;
            font-weight: 800;
            margin-bottom: 0.55rem;
        }

        .ci-description {
            color: var(--ci-muted);
            margin-bottom: 0;
            max-width: 760px;
        }

        .ci-hero-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .ci-ghost-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            border-radius: 18px;
            padding: 0.8rem 1rem;
            text-decoration: none;
            color: var(--ci-text);
            background: rgba(255, 255, 255, 0.86);
            border: 1px solid rgba(166, 98, 43, 0.12);
        }

        .ci-stat-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .ci-stat-card {
            padding: 1.15rem 1.2rem;
            border-radius: 22px;
            border: 1px solid var(--ci-line);
            background: linear-gradient(180deg, #fff 0%, #fff8ef 100%);
        }

        .ci-stat-card--teal {
            background: linear-gradient(180deg, #effcf9 0%, #fff 100%);
        }

        .ci-stat-card--amber {
            background: linear-gradient(180deg, #fff7ed 0%, #fff 100%);
        }

        .ci-stat-label {
            color: var(--ci-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.82rem;
            font-weight: 800;
        }

        .ci-stat-value {
            margin-top: 0.5rem;
            font-size: 1.8rem;
            font-weight: 800;
        }

        .ci-card {
            padding: 1.4rem;
            height: 100%;
        }

        .ci-section-title {
            font-size: 1.08rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
        }

        .ci-section-subtitle {
            color: var(--ci-muted);
            font-size: 0.92rem;
            margin-bottom: 1rem;
        }

        .ci-search {
            width: 100%;
            border-radius: 16px;
            border: 1px solid rgba(166, 98, 43, 0.16);
            padding: 0.9rem 1rem;
            background: #fffdfa;
            margin-bottom: 1rem;
        }

        .ci-booking-list {
            display: grid;
            gap: 0.85rem;
            max-height: 700px;
            overflow-y: auto;
            padding-right: 0.2rem;
        }

        .ci-booking-option {
            position: relative;
        }

        .ci-booking-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .ci-booking-card {
            display: block;
            border: 1px solid rgba(166, 98, 43, 0.12);
            border-radius: 22px;
            padding: 1rem;
            cursor: pointer;
            transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
            background: #fff;
        }

        .ci-booking-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 30px rgba(140, 77, 33, 0.08);
        }

        .ci-booking-option input:checked + .ci-booking-card {
            border-color: rgba(15, 118, 110, 0.46);
            background: linear-gradient(180deg, #effcf9 0%, #fff 100%);
            box-shadow: 0 18px 34px rgba(15, 118, 110, 0.12);
        }

        .ci-booking-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 0.75rem;
        }

        .ci-booking-id {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--ci-muted);
            font-weight: 800;
            margin-bottom: 0.2rem;
        }

        .ci-booking-name {
            font-weight: 800;
            font-size: 1rem;
        }

        .ci-booking-meta {
            color: var(--ci-muted);
            font-size: 0.9rem;
        }

        .ci-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.36rem 0.78rem;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 800;
        }

        .ci-badge--pending {
            background: #fff7ed;
            color: #c2410c;
        }

        .ci-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.85rem;
            margin-bottom: 1rem;
        }

        .ci-detail-item {
            border-radius: 18px;
            background: #fff8f1;
            padding: 0.95rem 1rem;
        }

        .ci-detail-key {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--ci-muted);
            font-weight: 800;
            margin-bottom: 0.35rem;
        }

        .ci-detail-value {
            font-weight: 700;
        }

        .ci-room-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 0.85rem;
        }

        .ci-room-card {
            border-radius: 20px;
            background: linear-gradient(180deg, #f6fefc 0%, #fff 100%);
            border: 1px solid rgba(15, 118, 110, 0.14);
            padding: 1rem;
        }

        .ci-room-number {
            font-size: 1.02rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
        }

        .ci-room-meta {
            color: var(--ci-muted);
            font-size: 0.9rem;
        }

        .ci-submit-btn {
            width: 100%;
            border: none;
            border-radius: 18px;
            padding: 1rem 1.1rem;
            color: #fff;
            font-weight: 800;
            background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
            box-shadow: 0 18px 30px rgba(15, 118, 110, 0.2);
        }

        .ci-alert {
            border-radius: 22px;
            border: 1px solid transparent;
            padding: 1rem 1.15rem;
            margin-bottom: 1.25rem;
        }

        .ci-alert--success {
            background: #ecfdf5;
            border-color: rgba(16, 185, 129, 0.18);
            color: #065f46;
        }

        .ci-alert--danger {
            background: #fff1f2;
            border-color: rgba(244, 63, 94, 0.16);
            color: #9f1239;
        }

        .ci-empty {
            color: var(--ci-muted);
            font-size: 0.95rem;
        }

        @media (max-width: 991.98px) {
            .ci-stat-grid,
            .ci-detail-grid {
                grid-template-columns: 1fr;
            }

            .ci-hero-actions {
                justify-content: flex-start;
            }
        }
    </style>

    <div class="ci-shell" id="check-in-page" data-bookings="{{ e(json_encode($bookings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)) }}">
        <div class="ci-hero">
            <div class="row align-items-center g-4">
                <div class="col-xl-8">
                    
                    <h1 class="ci-title">{{ $page['title'] ?? 'Nhận Phòng' }}</h1>
                    <p class="ci-description">{{ $page['description'] ?? '' }}</p>
                </div>
                <div class="col-xl-4">
                    <div class="ci-hero-actions">
                        <a href="{{ route('reception.bookings.create') }}" class="ci-ghost-btn">Tạo booking mới</a>
                        <a href="{{ route('reception.bookings.index') }}" class="ci-ghost-btn">Danh sách booking</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="ci-stat-grid">
            <div class="ci-stat-card">
                <div class="ci-stat-label">Booking chờ nhận</div>
                <div class="ci-stat-value">{{ $stats['pending_count'] ?? 0 }}</div>
            </div>
            <div class="ci-stat-card ci-stat-card--teal">
                <div class="ci-stat-label">Khách đến hôm nay</div>
                <div class="ci-stat-value">{{ $stats['arrival_today_count'] ?? 0 }}</div>
            </div>
            <div class="ci-stat-card ci-stat-card--amber">
                <div class="ci-stat-label">Phòng đang sử dụng</div>
                <div class="ci-stat-value">{{ $stats['occupied_room_count'] ?? 0 }}</div>
            </div>
        </div>

        @if(session('success'))
            <div class="ci-alert ci-alert--success">
                <div class="fw-bold mb-1">{{ session('success') }}</div>
                @if($checkedInBooking)
                    <div>
                        Booking #{{ $checkedInBooking['MaDatPhong'] }} của {{ $checkedInBooking['TenKH'] }} đã được chuyển sang trạng thái
                        <strong>{{ $checkedInBooking['TinhTrangLabel'] }}</strong>.
                    </div>
                @endif
            </div>
        @endif

        @if($errors->any())
            <div class="ci-alert ci-alert--danger">
                <div class="fw-bold mb-2">Chưa thể xác nhận nhận phòng</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="check-in-form" method="POST" action="{{ route('reception.check-ins.store') }}">
            @csrf

            <div class="row g-4">
                <div class="col-xl-5">
                    <div class="ci-card">
                        <div class="ci-section-title">Booking chờ nhận phòng</div>
                        <div class="ci-section-subtitle">Chọn một booking để xem chi tiết và xác nhận.</div>

                        <input type="text" id="booking_search" class="ci-search" placeholder="Tìm theo mã booking, tên khách hoặc số phòng...">

                        <div class="ci-booking-list" id="booking_list">
                            @forelse($bookings as $booking)
                                <label class="ci-booking-option" data-search="{{ mb_strtolower(($booking['MaDatPhong'] ?? '') . ' ' . ($booking['TenKH'] ?? '') . ' ' . ($booking['SoPhong'] ?? '')) }}">
                                    <input
                                        type="radio"
                                        name="booking_id"
                                        value="{{ $booking['MaDatPhong'] }}"
                                        {{ $selectedBookingId === (string) $booking['MaDatPhong'] ? 'checked' : '' }}
                                    >
                                    <span class="ci-booking-card">
                                        <span class="ci-booking-top">
                                            <span>
                                                <span class="ci-booking-id">Booking #{{ $booking['MaDatPhong'] }}</span>
                                                <span class="ci-booking-name">{{ $booking['TenKH'] ?: 'Chưa có tên khách' }}</span>
                                            </span>
                                            <span class="ci-badge ci-badge--pending">Chờ nhận</span>
                                        </span>
                                        <span class="ci-booking-meta">{{ $booking['SoPhong'] ?: 'Chưa có phòng' }}</span>
                                        <span class="ci-booking-meta d-block mt-1">
                                            {{ $booking['NgayNhanPhong'] ?: '--' }} đến {{ $booking['NgayTraPhong'] ?: '--' }}
                                        </span>
                                    </span>
                                </label>
                            @empty
                                <div class="ci-empty">Hiện không có booking nào ở trạng thái chờ nhận phòng.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-xl-7">
                    <div class="ci-card">
                        <div class="ci-section-title">Chi tiết xác nhận</div>
                        <div class="ci-section-subtitle">Thông tin được lấy từ <code>DatPhong</code>, <code>ChiTietDatPhong</code>, <code>KhachHang</code> và <code>Phong</code>.</div>

                        <div class="ci-detail-grid">
                            <div class="ci-detail-item">
                                <div class="ci-detail-key">Khách hàng</div>
                                <div class="ci-detail-value" id="detail_customer">Chưa chọn booking</div>
                            </div>
                            <div class="ci-detail-item">
                                <div class="ci-detail-key">Số điện thoại</div>
                                <div class="ci-detail-value" id="detail_phone">--</div>
                            </div>
                            <div class="ci-detail-item">
                                <div class="ci-detail-key">CCCD</div>
                                <div class="ci-detail-value" id="detail_cccd">--</div>
                            </div>
                            <div class="ci-detail-item">
                                <div class="ci-detail-key">Điểm tích lũy</div>
                                <div class="ci-detail-value" id="detail_points">0 điểm</div>
                            </div>
                            <div class="ci-detail-item">
                                <div class="ci-detail-key">Thời gian lưu trú</div>
                                <div class="ci-detail-value" id="detail_dates">--</div>
                            </div>
                            <div class="ci-detail-item">
                                <div class="ci-detail-key">Số đêm / phòng</div>
                                <div class="ci-detail-value" id="detail_nights">--</div>
                            </div>
                            <div class="ci-detail-item" style="grid-column: 1 / -1;">
                                <div class="ci-detail-key">Địa chỉ</div>
                                <div class="ci-detail-value" id="detail_address">--</div>
                            </div>
                        </div>

                        <div class="ci-section-title mt-4">Phòng sẽ chuyển sang trạng thái đang sử dụng</div>
                        <div class="ci-section-subtitle">Sau khi xác nhận, tất cả phòng thuộc booking sẽ cập nhật sang <strong>Đang sử dụng</strong>.</div>

                        <div class="ci-room-grid" id="detail_rooms">
                            <div class="ci-empty">Chưa chọn booking để xem danh sách phòng.</div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="ci-submit-btn" form="check-in-form">Xác nhận nhận phòng</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        (() => {
            const checkInPage = document.getElementById('check-in-page');
            const bookingPayload = checkInPage?.dataset.bookings ?? '[]';
            const bookings = JSON.parse(bookingPayload);
            const bookingMap = Object.fromEntries(bookings.map((booking) => [String(booking.MaDatPhong), booking]));
            const radios = Array.from(document.querySelectorAll('input[name="booking_id"]'));
            const searchInput = document.getElementById('booking_search');
            const bookingItems = Array.from(document.querySelectorAll('.ci-booking-option'));
            const detailCustomer = document.getElementById('detail_customer');
            const detailPhone = document.getElementById('detail_phone');
            const detailCccd = document.getElementById('detail_cccd');
            const detailPoints = document.getElementById('detail_points');
            const detailDates = document.getElementById('detail_dates');
            const detailNights = document.getElementById('detail_nights');
            const detailAddress = document.getElementById('detail_address');
            const detailRooms = document.getElementById('detail_rooms');

            const renderBooking = () => {
                const selectedRadio = radios.find((radio) => radio.checked);
                const selectedBooking = selectedRadio ? bookingMap[String(selectedRadio.value)] : null;

                if (!selectedBooking) {
                    detailCustomer.textContent = 'Chưa chọn booking';
                    detailPhone.textContent = '--';
                    detailCccd.textContent = '--';
                    detailPoints.textContent = '0 điểm';
                    detailDates.textContent = '--';
                    detailNights.textContent = '--';
                    detailAddress.textContent = '--';
                    detailRooms.innerHTML = '<div class="ci-empty">Chưa chọn booking để xem danh sách phòng.</div>';
                    return;
                }

                detailCustomer.textContent = `${selectedBooking.TenKH || 'Chưa có tên khách'} • #${selectedBooking.MaDatPhong}`;
                detailPhone.textContent = selectedBooking.SoDienThoai || '--';
                detailCccd.textContent = selectedBooking.CCCD || '--';
                detailPoints.textContent = `${selectedBooking.Diem || 0} điểm`;
                detailDates.textContent = `${selectedBooking.NgayNhanPhong || '--'} đến ${selectedBooking.NgayTraPhong || '--'}`;
                detailNights.textContent = `${selectedBooking.SoDem || 0} đêm • ${selectedBooking.SoLuongPhong || 0} phòng`;
                detailAddress.textContent = selectedBooking.DiaChi || '--';

                const rooms = Array.isArray(selectedBooking.Rooms) ? selectedBooking.Rooms : [];
                detailRooms.innerHTML = '';

                if (rooms.length === 0) {
                    detailRooms.innerHTML = '<div class="ci-empty">Booking chưa có phòng được gán.</div>';
                    return;
                }

                rooms.forEach((room) => {
                    const roomCard = document.createElement('div');
                    roomCard.className = 'ci-room-card';
                    roomCard.innerHTML = `
                        <div class="ci-room-number">Phòng ${room.number}</div>
                        <div class="ci-room-meta">${room.type_name}</div>
                        <div class="ci-room-meta mt-1">Tối đa ${room.capacity || 0} khách</div>
                    `;
                    detailRooms.appendChild(roomCard);
                });
            };

            const filterBookings = () => {
                const keyword = (searchInput.value || '').trim().toLowerCase();

                bookingItems.forEach((item) => {
                    const haystack = item.dataset.search || '';
                    item.style.display = keyword === '' || haystack.includes(keyword) ? '' : 'none';
                });
            };

            radios.forEach((radio) => radio.addEventListener('change', renderBooking));
            if (searchInput) {
                searchInput.addEventListener('input', filterBookings);
            }

            renderBooking();
        })();
    </script>
</x-app-layout>
