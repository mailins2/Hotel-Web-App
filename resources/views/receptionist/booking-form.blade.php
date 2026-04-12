<x-app-layout :assets="$assets ?? []">
    @php
        $stats = $page['stats'] ?? [];
        $defaults = $page['defaults'] ?? [];
        $customers = $page['customers'] ?? [];
        $rooms = $page['rooms'] ?? [];
        $roomTypes = $page['room_types'] ?? [];
        $createdBooking = $page['created_booking'] ?? null;
        $selectedRoomIds = collect(old('room_ids', []))->map(fn ($id) => (string) $id)->all();
    @endphp

    <style>
        .rf-shell {
            --rf-bg: linear-gradient(180deg, #fffaf3 0%, #fff 55%, #f6fbfb 100%);
            --rf-text: #40291d;
            --rf-muted: #856a57;
            --rf-line: rgba(166, 98, 43, 0.15);
            --rf-warm: #a65422;
            --rf-warm-soft: rgba(255, 241, 230, 0.88);
            --rf-teal: #0f766e;
            padding-top: 4.5rem;
            color: var(--rf-text);
        }

        .rf-hero,
        .rf-card {
            background: #fff;
            border: 1px solid var(--rf-line);
            border-radius: 28px;
            box-shadow: 0 18px 40px rgba(120, 74, 44, 0.08);
        }

        .rf-hero {
            padding: 1.8rem;
            background: var(--rf-bg);
            position: relative;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .rf-hero::after {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            right: -60px;
            top: -70px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(253, 186, 116, 0.32) 0%, rgba(253, 186, 116, 0) 72%);
        }

        .rf-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            font-weight: 800;
            color: var(--rf-warm);
            margin-bottom: 0.75rem;
        }

        .rf-title {
            font-size: 2.15rem;
            font-weight: 800;
            margin-bottom: 0.55rem;
        }

        .rf-description {
            color: var(--rf-muted);
            max-width: 760px;
            margin-bottom: 0;
        }

        .rf-hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: flex-end;
        }

        .rf-ghost-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.8rem 1rem;
            border-radius: 18px;
            text-decoration: none;
            color: var(--rf-text);
            background: rgba(255, 255, 255, 0.86);
            border: 1px solid rgba(166, 98, 43, 0.12);
        }

        .rf-stat-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .rf-stat-card {
            padding: 1.15rem 1.2rem;
            border-radius: 22px;
            border: 1px solid var(--rf-line);
            background: linear-gradient(180deg, #fff 0%, #fff8f0 100%);
        }

        .rf-stat-card--teal {
            background: linear-gradient(180deg, #f0fdfa 0%, #fff 100%);
        }

        .rf-stat-card--amber {
            background: linear-gradient(180deg, #fff7ed 0%, #fff 100%);
        }

        .rf-stat-label {
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--rf-muted);
            font-weight: 800;
        }

        .rf-stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            margin-top: 0.5rem;
        }

        .rf-card {
            padding: 1.4rem;
            height: 100%;
        }

        .rf-card + .rf-card {
            margin-top: 1.25rem;
        }

        .rf-section-title {
            font-size: 1.08rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
        }

        .rf-section-subtitle {
            color: var(--rf-muted);
            font-size: 0.92rem;
            margin-bottom: 1rem;
        }

        .rf-field-label {
            font-weight: 700;
            color: var(--rf-text);
            margin-bottom: 0.5rem;
        }

        .rf-form-control,
        .rf-form-select {
            width: 100%;
            border-radius: 16px;
            border: 1px solid rgba(166, 98, 43, 0.16);
            padding: 0.9rem 1rem;
            color: var(--rf-text);
            background: #fffdfa;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .rf-form-control:focus,
        .rf-form-select:focus {
            outline: none;
            border-color: rgba(166, 98, 43, 0.42);
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.12);
        }

        .rf-readonly {
            background: #f8f4ef;
            color: #6b5647;
        }

        .rf-room-type-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 0.85rem;
            margin-bottom: 1rem;
        }

        .rf-room-type-card {
            border: 1px solid rgba(13, 148, 136, 0.14);
            border-radius: 20px;
            padding: 1rem;
            background: linear-gradient(180deg, #f6fefc 0%, #fff 100%);
        }

        .rf-room-type-name {
            font-weight: 800;
            margin-bottom: 0.2rem;
        }

        .rf-room-type-meta {
            color: var(--rf-muted);
            font-size: 0.9rem;
        }

        .rf-room-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
            gap: 0.95rem;
        }

        .rf-room-option {
            position: relative;
        }

        .rf-room-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .rf-room-card {
            display: block;
            border: 1px solid rgba(166, 98, 43, 0.12);
            border-radius: 22px;
            padding: 1rem;
            background: #fff;
            cursor: pointer;
            min-height: 160px;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        .rf-room-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 32px rgba(140, 77, 33, 0.08);
        }

        .rf-room-option input:checked + .rf-room-card {
            border-color: rgba(15, 118, 110, 0.5);
            background: linear-gradient(180deg, #effcf9 0%, #fff 100%);
            box-shadow: 0 18px 34px rgba(15, 118, 110, 0.12);
        }

        .rf-room-number {
            font-size: 1.12rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
        }

        .rf-room-type {
            color: var(--rf-muted);
            font-size: 0.9rem;
        }

        .rf-room-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.3rem 0.7rem;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 800;
            margin-top: 0.85rem;
            background: var(--rf-warm-soft);
            color: var(--rf-warm);
        }

        .rf-summary-card {
            position: sticky;
            top: 96px;
        }

        .rf-summary-list {
            display: grid;
            gap: 0.9rem;
        }

        .rf-summary-item {
            border-radius: 18px;
            background: #fff9f2;
            padding: 0.95rem 1rem;
        }

        .rf-summary-key {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--rf-muted);
            font-weight: 800;
            margin-bottom: 0.35rem;
        }

        .rf-summary-value {
            font-weight: 700;
        }

        .rf-selected-rooms {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
        }

        .rf-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            border-radius: 999px;
            padding: 0.45rem 0.75rem;
            background: #f3fdfa;
            color: var(--rf-teal);
            font-weight: 700;
        }

        .rf-empty-inline {
            color: var(--rf-muted);
            font-size: 0.92rem;
        }

        .rf-submit-btn {
            width: 100%;
            border: none;
            border-radius: 18px;
            padding: 1rem 1.1rem;
            color: #fff;
            font-weight: 800;
            background: linear-gradient(135deg, #b45309 0%, #7c2d12 100%);
            box-shadow: 0 18px 30px rgba(146, 64, 14, 0.2);
        }

        .rf-alert {
            border-radius: 22px;
            border: 1px solid transparent;
            padding: 1rem 1.15rem;
            margin-bottom: 1.25rem;
        }

        .rf-alert--success {
            background: #ecfdf5;
            border-color: rgba(16, 185, 129, 0.18);
            color: #065f46;
        }

        .rf-alert--danger {
            background: #fff1f2;
            border-color: rgba(244, 63, 94, 0.16);
            color: #9f1239;
        }

        .rf-created-booking {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.85rem;
            margin-top: 0.95rem;
        }

        .rf-created-item {
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.8);
            padding: 0.85rem 0.95rem;
        }

        .rf-created-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        @media (max-width: 1199.98px) {
            .rf-summary-card {
                position: static;
            }
        }

        @media (max-width: 767.98px) {
            .rf-shell {
                padding-top: 4rem;
            }

            .rf-title {
                font-size: 1.8rem;
            }

            .rf-stat-grid,
            .rf-created-booking {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div
        class="rf-shell"
        id="booking-page"
        data-customers="{{ e(json_encode($customers, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)) }}"
    >
        <div class="rf-hero">
            <div class="row align-items-center g-4">
                <div class="col-xl-8">
                    <h1 class="rf-title">{{ $page['title'] ?? 'Đặt Phòng' }}</h1>
                    <p class="rf-description">{{ $page['description'] ?? '' }}</p>
                </div>
                <div class="col-xl-4">
                    <div class="rf-hero-actions">
                        <a href="{{ route('reception.bookings.index') }}" class="rf-ghost-btn">Xem danh sách đặt phòng</a>
                        <a href="{{ route('reception.check-ins.create') }}" class="rf-ghost-btn">Mở trang nhận phòng</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="rf-stat-grid">
            <div class="rf-stat-card">
                <div class="rf-stat-label">Khách hàng</div>
                <div class="rf-stat-value">{{ $stats['customer_count'] ?? 0 }}</div>
            </div>
            <div class="rf-stat-card rf-stat-card--teal">
                <div class="rf-stat-label">Phòng đang trống</div>
                <div class="rf-stat-value">{{ $stats['available_room_count'] ?? 0 }}</div>
            </div>
            <div class="rf-stat-card rf-stat-card--amber">
                <div class="rf-stat-label">Phòng chờ nhận</div>
                <div class="rf-stat-value">{{ $stats['pending_check_in_count'] ?? 0 }}</div>
            </div>
        </div>

        @if(session('success'))
            <div class="rf-alert rf-alert--success">
                <div class="fw-bold mb-1">{{ session('success') }}</div>
                @if($createdBooking)
                    <div class="rf-created-booking">
                        <div class="rf-created-item">
                            <div class="rf-summary-key">Đặt phòng mới</div>
                            <div class="rf-summary-value">#{{ $createdBooking['MaDatPhong'] }}</div>
                        </div>
                        <div class="rf-created-item">
                            <div class="rf-summary-key">Khách hàng</div>
                            <div class="rf-summary-value">{{ $createdBooking['TenKH'] ?: 'Chưa có tên khách' }}</div>
                        </div>
                        <div class="rf-created-item">
                            <div class="rf-summary-key">Lịch ở</div>
                            <div class="rf-summary-value">
                                {{ $createdBooking['NgayNhanPhong'] ?: '--' }} đến {{ $createdBooking['NgayTraPhong'] ?: '--' }}
                            </div>
                        </div>
                        <div class="rf-created-item">
                            <div class="rf-summary-key">Phòng đã giữ</div>
                            <div class="rf-summary-value">{{ $createdBooking['SoPhong'] ?: 'Chưa gán phòng' }}</div>
                        </div>
                    </div>
                    <div class="rf-created-actions">
                        <a href="{{ route('reception.check-ins.create', ['booking' => $createdBooking['MaDatPhong']]) }}" class="rf-ghost-btn">
                            Chuyển sang nhận phòng
                        </a>
                        <a href="{{ route('reception.bookings.create') }}" class="rf-ghost-btn">
                            Tạo booking khác
                        </a>
                    </div>
                @endif
            </div>
        @endif

        @if($errors->any())
            <div class="rf-alert rf-alert--danger">
                <div class="fw-bold mb-2">Có vài thông tin cần kiểm tra lại</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-xl-8">
                <form id="booking-form" method="POST" action="{{ route('reception.bookings.store') }}">
                    @csrf

                    <div class="rf-card">
                        <div class="rf-section-title">Thông tin khách hàng</div>

                        <div class="mb-3">
                            <label for="MaKH" class="rf-field-label">Khách hàng</label>
                            <select id="MaKH" name="MaKH" class="rf-form-select">
                                <option value="">Chọn khách đã có hồ sơ</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer['id'] }}" {{ (string) old('MaKH') === (string) $customer['id'] ? 'selected' : '' }}>
                                        {{ $customer['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="rf-field-label">Tên khách</label>
                                <input type="text" id="customer_name" class="rf-form-control rf-readonly" value="" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="rf-field-label">Số điện thoại</label>
                                <input type="text" id="customer_phone" class="rf-form-control rf-readonly" value="" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="rf-field-label">CCCD</label>
                                <input type="text" id="customer_cccd" class="rf-form-control rf-readonly" value="" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="rf-field-label">Điểm tích lũy</label>
                                <input type="text" id="customer_points" class="rf-form-control rf-readonly" value="" readonly>
                            </div>
                            <div class="col-12">
                                <label class="rf-field-label">Địa chỉ</label>
                                <input type="text" id="customer_address" class="rf-form-control rf-readonly" value="" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="rf-card">
                        <div class="rf-section-title">Lịch lưu trú</div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="NgayDat" class="rf-field-label">Ngày đặt</label>
                                <input
                                    id="NgayDat"
                                    name="NgayDat"
                                    type="date"
                                    class="rf-form-control"
                                    value="{{ old('NgayDat', $defaults['booking_date'] ?? now()->toDateString()) }}"
                                >
                            </div>
                            <div class="col-md-4">
                                <label for="NgayNhanPhong" class="rf-field-label">Ngày nhận phòng</label>
                                <input
                                    id="NgayNhanPhong"
                                    name="NgayNhanPhong"
                                    type="date"
                                    class="rf-form-control"
                                    value="{{ old('NgayNhanPhong', $defaults['check_in_date'] ?? now()->toDateString()) }}"
                                >
                            </div>
                            <div class="col-md-4">
                                <label for="NgayTraPhong" class="rf-field-label">Ngày trả phòng</label>
                                <input
                                    id="NgayTraPhong"
                                    name="NgayTraPhong"
                                    type="date"
                                    class="rf-form-control"
                                    value="{{ old('NgayTraPhong', $defaults['check_out_date'] ?? now()->addDay()->toDateString()) }}"
                                >
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label for="SoLuong" class="rf-field-label">Số lượng người ở</label>
                                <input
                                    id="SoLuong"
                                    name="SoLuong"
                                    type="number"
                                    min="1"
                                    class="rf-form-control"
                                    value="{{ old('SoLuong', 1) }}"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="rf-card">
                        <div class="rf-section-title">Chọn phòng</div>

                        <div class="rf-room-grid">
                            @forelse($rooms as $room)
                                @php
                                    $isChecked = in_array((string) $room['id'], $selectedRoomIds, true);
                                @endphp
                                <label class="rf-room-option">
                                    <input
                                        type="checkbox"
                                        name="room_ids[]"
                                        value="{{ $room['id'] }}"
                                        data-room-id="{{ $room['id'] }}"
                                        data-room-number="{{ $room['number'] }}"
                                        data-room-type="{{ $room['type_name'] }}"
                                        data-room-capacity="{{ $room['capacity'] }}"
                                        {{ $isChecked ? 'checked' : '' }}
                                    >
                                    <span class="rf-room-card">
                                        <span class="rf-room-number">Phòng {{ $room['number'] }}</span>
                                        <span class="rf-room-type">{{ $room['type_name'] }}</span>
                                        <span class="rf-room-pill">Tối đa {{ $room['capacity'] }} khách</span>
                                    </span>
                                </label>
                            @empty
                                <div class="rf-empty-inline">Hiện không còn phòng trống để tạo booking mới.</div>
                            @endforelse
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-xl-4">
                <div class="rf-card rf-summary-card">
                    <div class="rf-section-title">Tóm tắt đặt phòng</div>
                    <div class="rf-section-subtitle">Kiểm tra nhanh trước khi lưu.</div>

                    <div class="rf-summary-list">
                        <div class="rf-summary-item">
                            <div class="rf-summary-key">Khách hàng</div>
                            <div class="rf-summary-value" id="summary_customer">Chưa chọn khách hàng</div>
                        </div>
                        <div class="rf-summary-item">
                            <div class="rf-summary-key">Khoảng thời gian</div>
                            <div class="rf-summary-value" id="summary_dates">Chưa chọn ngày</div>
                        </div>
                        <div class="rf-summary-item">
                            <div class="rf-summary-key">Số đêm</div>
                            <div class="rf-summary-value" id="summary_nights">0 đêm</div>
                        </div>
                        <div class="rf-summary-item">
                            <div class="rf-summary-key">Phòng đã chọn</div>
                            <div class="rf-selected-rooms" id="summary_rooms">
                                <span class="rf-empty-inline">Chưa chọn phòng</span>
                            </div>
                        </div>
                        <div class="rf-summary-item">
                            <div class="rf-summary-key">Sức chứa tối đa</div>
                            <div class="rf-summary-value" id="summary_capacity">0 khách</div>
                        </div>
                    </div>

                    <div class="rf-summary-item">
                        <div class="rf-summary-key">Số lượng người ở</div>
                        <div class="rf-summary-value" id="summary_guest_count">1 khách</div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="rf-submit-btn" form="booking-form">Lưu booking vào hệ thống</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const customerSelect = document.getElementById('MaKH');
            const bookingDateInput = document.getElementById('NgayDat');
            const checkInInput = document.getElementById('NgayNhanPhong');
            const checkOutInput = document.getElementById('NgayTraPhong');
            const guestCountInput = document.getElementById('SoLuong');
            const customerName = document.getElementById('customer_name');
            const customerPhone = document.getElementById('customer_phone');
            const customerCccd = document.getElementById('customer_cccd');
            const customerAddress = document.getElementById('customer_address');
            const customerPoints = document.getElementById('customer_points');
            const summaryCustomer = document.getElementById('summary_customer');
            const summaryDates = document.getElementById('summary_dates');
            const summaryNights = document.getElementById('summary_nights');
            const summaryRooms = document.getElementById('summary_rooms');
            const summaryCapacity = document.getElementById('summary_capacity');
            const summaryGuestCount = document.getElementById('summary_guest_count');
            const bookingPage = document.getElementById('booking-page');
            const customerPayload = bookingPage?.dataset.customers ?? '[]';
            const customers = JSON.parse(customerPayload);
            const customerMap = Object.fromEntries(customers.map((customer) => [String(customer.id), customer]));
            const roomInputs = Array.from(document.querySelectorAll('input[name="room_ids[]"]'));
            const form = document.getElementById('booking-form');

            if (!form) {
                return;
            }

            const updateCustomer = () => {
                const selected = customerMap[String(customerSelect.value)] || null;
                customerName.value = selected?.name || '';
                customerPhone.value = selected?.phone || '';
                customerCccd.value = selected?.cccd || '';
                customerAddress.value = selected?.address || '';
                customerPoints.value = selected ? `${selected.points} điểm` : '';
                summaryCustomer.textContent = selected ? `${selected.name} • ${selected.phone || 'Chưa có SĐT'}` : 'Chưa chọn khách hàng';
            };

            const updateStay = () => {
                const checkIn = checkInInput.value;
                const checkOut = checkOutInput.value;

                if (!checkIn || !checkOut) {
                    summaryDates.textContent = 'Chưa chọn ngày';
                    summaryNights.textContent = '0 đêm';
                    return;
                }

                summaryDates.textContent = `${checkIn} đến ${checkOut}`;

                const start = new Date(checkIn);
                const end = new Date(checkOut);
                const diffMs = end - start;
                const diffDays = Number.isFinite(diffMs) ? Math.max(Math.round(diffMs / 86400000), 0) : 0;
                summaryNights.textContent = `${diffDays} đêm`;

                if (bookingDateInput.value && checkIn < bookingDateInput.value) {
                    checkInInput.setCustomValidity('Ngày nhận phòng không thể trước ngày đặt.');
                } else {
                    checkInInput.setCustomValidity('');
                }

                if (checkIn && checkOut <= checkIn) {
                    checkOutInput.setCustomValidity('Ngày trả phòng phải sau ngày nhận phòng.');
                } else {
                    checkOutInput.setCustomValidity('');
                }
            };

            const syncGuestCount = (totalCapacity = null) => {
                const guestCount = Number(guestCountInput?.value || 0);

                if (summaryGuestCount) {
                    summaryGuestCount.textContent = guestCount > 0 ? `${guestCount} khách` : 'Chưa nhập';
                }

                if (!guestCountInput) {
                    return;
                }

                if (guestCount < 1) {
                    guestCountInput.setCustomValidity('Số lượng người ở phải lớn hơn 0.');
                    return;
                }

                if (totalCapacity !== null && totalCapacity > 0 && guestCount > totalCapacity) {
                    guestCountInput.setCustomValidity('Số lượng người ở vượt quá sức chứa của phòng đã chọn.');
                    return;
                }

                guestCountInput.setCustomValidity('');
            };

            const updateRooms = () => {
                {
                const selectedRooms = roomInputs
                    .filter((input) => input.checked)
                    .map((input) => ({
                        number: input.dataset.roomNumber,
                        type: input.dataset.roomType,
                        capacity: Number(input.dataset.roomCapacity || 0),
                    }));

                summaryRooms.innerHTML = '';

                if (selectedRooms.length === 0) {
                    summaryRooms.innerHTML = '<span class="rf-empty-inline">Chưa chọn phòng</span>';
                    summaryCapacity.textContent = '0 khách';
                    syncGuestCount(0);
                    return;
                }

                selectedRooms.forEach((room) => {
                    const chip = document.createElement('span');
                    chip.className = 'rf-chip';
                    chip.textContent = `${room.number} • ${room.type}`;
                    summaryRooms.appendChild(chip);
                });

                const totalCapacity = selectedRooms.reduce((sum, room) => sum + room.capacity, 0);
                summaryCapacity.textContent = `${totalCapacity} khách`;
                syncGuestCount(totalCapacity);
                return;
                }

                const selectedRooms = roomInputs
                    .filter((input) => input.checked)
                    .map((input) => ({
                        number: input.dataset.roomNumber,
                        type: input.dataset.roomType,
                        capacity: Number(input.dataset.roomCapacity || 0),
                    }));

                summaryRooms.innerHTML = '';

                if (selectedRooms.length === 0) {
                    summaryRooms.innerHTML = '<span class="rf-empty-inline">Chưa chọn phòng</span>';
                    summaryCapacity.textContent = '0 khách';
                    return;
                }

                selectedRooms.forEach((room) => {
                    const chip = document.createElement('span');
                    chip.className = 'rf-chip';
                    chip.textContent = `${room.number} • ${room.type}`;
                    summaryRooms.appendChild(chip);
                });

                const totalCapacity = selectedRooms.reduce((sum, room) => sum + room.capacity, 0);
                summaryCapacity.textContent = `${totalCapacity} khách`;
            };

            customerSelect.addEventListener('change', updateCustomer);
            bookingDateInput.addEventListener('change', updateStay);
            checkInInput.addEventListener('change', updateStay);
            checkOutInput.addEventListener('change', updateStay);
            guestCountInput?.addEventListener('input', () => syncGuestCount());
            roomInputs.forEach((input) => input.addEventListener('change', updateRooms));

            updateCustomer();
            updateStay();
            syncGuestCount();
            updateRooms();
        })();
    </script>
</x-app-layout>
