@php
    $checkInBookings = $checkInBookings ?? collect();
    $checkInStats = $checkInStats ?? ['waiting' => 0, 'arrivalsToday' => 0, 'checkedIn' => 0];
    $formatDate = fn ($value) => $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : '--';
    $getNights = function ($booking) {
        if (!$booking?->NgayNhanPhong || !$booking?->NgayTraPhong) {
            return 0;
        }

        return max(1, \Carbon\Carbon::parse($booking->NgayNhanPhong)->diffInDays(\Carbon\Carbon::parse($booking->NgayTraPhong)));
    };
@endphp

<x-app-layout :assets="['animation']">
    <style>
        .ci-shell {
            padding-top: 4.5rem;
        }

        .ci-hero,
        .ci-card {
            background: #fff;
            border: 1px solid rgba(166, 98, 43, 0.15);
            border-radius: 28px;
            box-shadow: 0 18px 40px rgba(120, 74, 44, 0.08);
        }

        .ci-hero {
            padding: 1.8rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(180deg, #fff7ef 0%, #fff 55%, #f3fbfa 100%);
        }

        .ci-card {
            padding: 1.4rem;
            height: 100%;
        }

        .ci-list-card {
            display: flex;
            flex-direction: column;
            height: 100%;
            max-height: none;
            min-height: 980px;
        }

        .ci-section-title {
            margin-bottom: 1rem;
        }

        .ci-search-box {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.9rem 1rem;
            margin-bottom: 1rem;
            border: 1px solid rgba(166, 98, 43, 0.14);
            border-radius: 18px;
            background: linear-gradient(180deg, #fffaf4 0%, #fff 100%);
        }

        .ci-search-box svg {
            color: #8b5e3c;
            flex-shrink: 0;
        }

        .ci-search-input {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            color: #6f1d01;
        }

        .ci-search-input::placeholder {
            color: #b08a71;
        }

        .ci-booking-list {
            display: flex;
            flex-direction: column;
            gap: 0.95rem;
            flex: 1 1 auto;
            min-height: 0;
            max-height: none;
            overflow-y: auto;
            overscroll-behavior: contain;
            padding: 0 0.35rem 0.2rem 0;
        }

        .ci-booking-list::-webkit-scrollbar {
            width: 8px;
        }

        .ci-booking-list::-webkit-scrollbar-track {
            background: rgba(166, 98, 43, 0.08);
            border-radius: 999px;
        }

        .ci-booking-list::-webkit-scrollbar-thumb {
            background: rgba(166, 98, 43, 0.35);
            border-radius: 999px;
        }

        .ci-booking-item {
            flex: 0 0 auto;
            border: 1px solid rgba(166, 98, 43, 0.12);
            border-radius: 24px;
            background: #fff;
            overflow: hidden;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
        }

        .ci-booking-item.is-active {
            border-color: rgba(166, 98, 43, 0.24);
            box-shadow: 0 18px 36px rgba(120, 74, 44, 0.08);
        }

        .ci-booking-summary {
            width: 100%;
            border: none;
            padding: 1.15rem 1.15rem 1rem;
            background: transparent;
            text-align: left;
            display: block;
        }

        .ci-booking-summary:hover {
            background: rgba(255, 247, 239, 0.55);
        }

        .ci-booking-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.65rem;
        }

        .ci-booking-code {
            color: #8b5e3c;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            overflow-wrap: anywhere;
        }

        .ci-stay-chip,
        .ci-meta-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2rem;
            padding: 0.3rem 0.8rem;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
            line-height: 1;
            white-space: nowrap;
        }

        .ci-stay-chip {
            color: #8a4b15;
            background: rgba(255, 239, 213, 0.95);
            border: 1px solid rgba(234, 88, 12, 0.18);
        }

        .ci-booking-name {
            color: #2f190f;
            font-size: 1.35rem;
            font-weight: 600;
            line-height: 1.3;
            overflow-wrap: anywhere;
        }

        .ci-booking-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
            margin-top: 0.8rem;
        }

        .ci-meta-chip {
            color: #7b5a46;
            background: #f8f4ef;
            border: 1px solid rgba(166, 98, 43, 0.12);
        }

        .ci-room-list {
            display: none;
            padding: 0 1.15rem 1.15rem;
            max-height: 320px;
            overflow-y: auto;
            overscroll-behavior: contain;
        }

        .ci-booking-item.is-active .ci-room-list {
            display: block;
        }

        .ci-room-list::-webkit-scrollbar {
            width: 8px;
        }

        .ci-room-list::-webkit-scrollbar-track {
            background: rgba(15, 118, 110, 0.08);
            border-radius: 999px;
        }

        .ci-room-list::-webkit-scrollbar-thumb {
            background: rgba(15, 118, 110, 0.28);
            border-radius: 999px;
        }

        .ci-room-list-label {
            margin: 0 0 0.75rem;
            color: #8b5e3c;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .ci-room-list-stack {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            min-height: 0;
            padding-bottom: 0.2rem;
        }

        .ci-room-option {
            flex: 0 0 auto;
            width: 100%;
            padding: 0.95rem 1rem;
            border: 1px solid rgba(15, 118, 110, 0.16);
            border-radius: 20px;
            background: linear-gradient(180deg, #f6fefc 0%, #fff 100%);
            text-align: left;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
        }

        .ci-room-option:hover,
        .ci-room-option.is-active {
            transform: translateY(-1px);
            border-color: rgba(15, 118, 110, 0.28);
            box-shadow: 0 16px 30px rgba(15, 118, 110, 0.08);
        }

        .ci-room-option-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            min-width: 0;
        }

        .ci-room-option-top > div {
            min-width: 0;
        }

        .ci-room-option-code {
            color: #0f766e;
            font-size: 1rem;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .ci-room-option-type {
            margin-top: 0.2rem;
            color: #6f1d01;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        .ci-detail-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .ci-detail-empty {
            padding: 1.5rem;
            border: 1px dashed rgba(166, 98, 43, 0.22);
            border-radius: 24px;
            background: linear-gradient(180deg, #fffaf4 0%, #fff 100%);
            color: #8d6e57;
            margin-bottom: 1rem;
        }

        .ci-room-form {
            display: none;
            flex-direction: column;
            gap: 1rem;
        }

        .ci-room-form.is-visible {
            display: flex;
        }

        .ci-guest-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .ci-guest-card {
            padding: 1rem;
            border: 1px solid rgba(166, 98, 43, 0.12);
            border-radius: 22px;
            background: #fff;
        }

        .ci-guest-label {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            background: #f8f4ef;
            color: #8b5e3c;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .ci-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.9rem;
            margin-top: 1rem;
        }

        .ci-form-field label {
            display: block;
            margin-bottom: 0.4rem;
            color: #7b5a46;
            font-size: 0.88rem;
            font-weight: 600;
        }

        .ci-form-field input {
            width: 100%;
            min-height: 2.95rem;
            padding: 0.7rem 0.95rem;
            border: 1px solid rgba(166, 98, 43, 0.14);
            border-radius: 16px;
            background: #fffdfb;
            color: #4d2c1d;
        }

        .ci-form-field input:focus {
            outline: none;
            border-color: rgba(15, 118, 110, 0.35);
            box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.08);
        }

        .ci-form-field input.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.08);
        }

        .ci-field-error {
            margin-top: 0.35rem;
            color: #dc3545;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .ci-detail-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 0.5rem;
        }

        .ci-detail-actions .btn {
            min-width: 220px;
            border-radius: 5px;
        }

        .ci-dialog {
            width: min(680px, calc(100vw - 2rem));
            border: none;
            border-radius: 24px;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 28px 70px rgba(73, 18, 15, 0.22);
        }

        .ci-dialog::backdrop {
            background: rgba(73, 18, 15, 0.28);
            backdrop-filter: blur(2px);
        }

        .ci-dialog-body {
            padding: 1.5rem;
            background: linear-gradient(180deg, #fffaf3 0%, #fff 100%);
        }

        .ci-dialog-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .ci-dialog-title {
            margin: 0;
            color: #6f1d01;
            font-size: 1.35rem;
            font-weight: 700;
        }

        .ci-time-button {
            border: 1px solid rgba(166, 98, 43, 0.16);
            border-radius: 10px;
            background: #fff;
            color: #6f1d01;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1;
            padding: 0.7rem 1rem;
        }

        .ci-dialog-text {
            margin: 0.45rem 0 1.25rem;
            color: #7c5b45;
        }

        .ci-dialog-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.85rem;
            margin-bottom: 1.25rem;
        }

        .ci-dialog-info {
            border: 1px solid rgba(166, 98, 43, 0.12);
            border-radius: 15px;
            padding: 0.95rem 1rem;
            background: #fff;
        }

        .ci-dialog-info-label {
            color: #8b5e3c;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .ci-dialog-info-value {
            margin-top: 0.45rem;
            color: #6f1d01;
            font-size: 1.0rem;
            font-weight: 500;
        }

        #dialogStayPeriod {
            white-space: nowrap;
        }

        .ci-dialog-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .ci-dialog-actions .btn {
            flex: 1 1 180px;
            border-radius: 5px;
        }

        @media (max-width: 991.98px) {
            .ci-list-card {
                min-height: 720px;
                max-height: 70vh;
            }

            .ci-form-grid,
            .ci-dialog-grid {
                grid-template-columns: 1fr;
            }

            #dialogStayPeriod {
                white-space: normal;
            }
        }

        @media (max-width: 767.98px) {
            .ci-booking-list {
                max-height: 58vh;
            }

            .ci-room-list {
                max-height: 260px;
            }

            .ci-booking-head,
            .ci-room-option-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .ci-detail-actions .btn {
                width: 100%;
            }
        }
    </style>

    <div class="ci-shell">
        <div class="ci-hero">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="mb-2">Nhận phòng</h1>
                    <p class="text-muted mb-0">Danh sách thông tin nhận phòng</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('reception.bookings.create') }}" class="btn btn-light">Tạo đặt phòng mới</a>
                    <a href="{{ route('reception.bookings.index') }}" class="btn btn-light">Danh sách đặt phòng</a>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="ci-card text-center">
                    <div class="small text-uppercase text-muted fw-bold">Đặt phòng chờ nhận</div>
                    <div class="h4 mb-0 mt-2">{{ $checkInStats['waiting'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="ci-card text-center">
                    <div class="small text-uppercase text-muted fw-bold">Khách đến hôm nay</div>
                    <div class="h4 mb-0 mt-2">{{ $checkInStats['arrivalsToday'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="ci-card text-center">
                    <div class="small text-uppercase text-muted fw-bold">Đặt phòng nhận rồi</div>
                    <div class="h4 mb-0 mt-2">{{ $checkInStats['checkedIn'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-5">
                <div class="ci-card ci-list-card">
                    <h5 class="ci-section-title">Danh sách nhận phòng hôm nay</h5>

                    <div class="ci-search-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M21 21L16.65 16.65M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <input
                            type="text"
                            class="ci-search-input"
                            data-checkin-search
                            placeholder="Tìm theo mã đặt phòng, tên khách hàng hoặc số phòng"
                        >
                    </div>

                    <div class="ci-booking-list">
                        @forelse($checkInBookings as $booking)
                            @php
                                $nights = $getNights($booking);
                                $stayPeriod = $formatDate($booking->NgayNhanPhong) . ' đến ' . $formatDate($booking->NgayTraPhong);
                                $customer = $booking->khachHang;
                                $customerName = $customer?->TenKH ?? 'Khách chưa có tên';
                                $roomNumbers = $booking->chiTietDatPhong
                                    ->map(fn ($detail) => $detail->phong?->SoPhong)
                                    ->filter()
                                    ->implode(' ');
                                $searchText = trim("#{$booking->MaDatPhong} {$customerName} {$roomNumbers}");
                            @endphp
                            <div class="ci-booking-item {{ $loop->first ? 'is-active' : '' }}" data-booking-item data-search-text="{{ \Illuminate\Support\Str::lower($searchText) }}">
                                <button type="button" class="ci-booking-summary" data-booking-toggle aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                                    <div class="ci-booking-head">
                                        <div class="ci-booking-code">Đặt phòng #{{ $booking->MaDatPhong }}</div>
                                        <span class="ci-stay-chip">{{ $nights }} đêm</span>
                                    </div>
                                    <div class="ci-booking-name">{{ $customerName }}</div>
                                    <div class="ci-booking-meta">
                                        <span class="ci-meta-chip">{{ $booking->chiTietDatPhong->count() }} phòng</span>
                                        <span class="ci-meta-chip">{{ $stayPeriod }}</span>
                                    </div>
                                </button>

                                <div class="ci-room-list">
                                    <p class="ci-room-list-label mb-0">Danh sách phòng đã đặt</p>
                                    <div class="ci-room-list-stack mt-3">
                                        @foreach($booking->chiTietDatPhong as $detail)
                                            @php
                                                $room = $detail->phong;
                                                $roomType = $room?->loaiPhong;
                                                $adults = max(0, (int) ($roomType?->NguoiLon ?? 1));
                                                $children = max(0, (int) ($roomType?->TreEm ?? 0));
                                                $guestParts = collect([
                                                    $adults > 0 ? "{$adults} người lớn" : null,
                                                    $children > 0 ? "{$children} trẻ em" : null,
                                                ])->filter();
                                                $guestSummary = $guestParts->isNotEmpty() ? $guestParts->implode(' • ') : 'Chưa có thông tin sức chứa';
                                                $roomTarget = 'room-' . $booking->MaDatPhong . '-' . ($room?->MaPhong ?? $detail->MaCTDP);
                                            @endphp
                                            <button
                                                type="button"
                                                class="ci-room-option"
                                                data-room-target="{{ $roomTarget }}"
                                                data-room-badge="{{ $nights }} đêm"
                                                data-booking-id="{{ $booking->MaDatPhong }}"
                                                data-room-number="{{ $room?->SoPhong ?? '--' }}"
                                                data-guest-summary="{{ $guestSummary }}"
                                                data-stay-period="{{ $stayPeriod }}"
                                            >
                                                <div class="ci-room-option-top">
                                                    <div>
                                                        <div class="ci-room-option-code">Phòng {{ $room?->SoPhong ?? '--' }}</div>
                                                        <div class="ci-room-option-type">{{ $roomType?->TenLoaiPhong ?? 'Chưa có loại phòng' }}</div>
                                                    </div>
                                                    <span class="ci-meta-chip">{{ $guestSummary }}</span>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="ci-detail-empty mb-0">Không có đặt phòng nào đang chờ nhận phòng.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-xl-7">
                <div class="ci-card">
                    <div class="ci-detail-header">
                        <h5 class="mb-0">Chi tiết xác nhận</h5>
                        <span id="selectedStayChip" class="ci-stay-chip">Chưa chọn phòng</span>
                    </div>

                    <div id="checkinDetailEmpty" class="ci-detail-empty">
                        Chọn phòng muốn cho nhận phòng
                    </div>

                    @foreach($checkInBookings as $booking)
                        @foreach($booking->chiTietDatPhong as $detail)
                            @php
                                $room = $detail->phong;
                                $roomType = $room?->loaiPhong;
                                $adults = max(1, (int) ($roomType?->NguoiLon ?? 1));
                                $children = max(0, (int) ($roomType?->TreEm ?? 0));
                                $roomTarget = 'room-' . $booking->MaDatPhong . '-' . ($room?->MaPhong ?? $detail->MaCTDP);
                                $customer = $booking->khachHang;
                            @endphp
                            <div id="{{ $roomTarget }}" class="ci-room-form" data-room-form data-booking-id="{{ $booking->MaDatPhong }}" data-room-id="{{ $room?->MaPhong }}">
                                <div class="ci-guest-grid">
                                    @for($i = 1; $i <= $adults; $i++)
                                        <div class="ci-guest-card" data-guest-role="adult">
                                            <span class="ci-guest-label">Người lớn {{ $i }}</span>
                                            <div class="ci-form-grid">
                                                <div class="ci-form-field">
                                                    <label>Họ và tên</label>
                                                    <input type="text" placeholder="Nhập họ và tên" data-guest-name>
                                                </div>
                                                <div class="ci-form-field">
                                                    <label>Ngày sinh</label>
                                                    <input type="date" data-birthdate data-guest-role="adult">
                                                </div>
                                                <div class="ci-form-field">
                                                    <label>CCCD</label>
                                                    <input type="text" inputmode="numeric" maxlength="12" pattern="[0-9]{12}" placeholder="Nhập số CCCD" data-guest-cccd>
                                                </div>
                                                <div class="ci-form-field">
                                                    <label>Số điện thoại</label>
                                                    <input type="tel" placeholder="Nhập số điện thoại" data-guest-phone>
                                                </div>
                                            </div>
                                        </div>
                                    @endfor

                                    @for($i = 1; $i <= $children; $i++)
                                        <div class="ci-guest-card" data-guest-role="child">
                                            <span class="ci-guest-label">Trẻ em {{ $i }}</span>
                                            <div class="ci-form-grid">
                                                <div class="ci-form-field">
                                                    <label>Họ và tên</label>
                                                    <input type="text" placeholder="Nhập họ và tên" data-guest-name>
                                                </div>
                                                <div class="ci-form-field">
                                                    <label>Ngày sinh</label>
                                                    <input type="date" data-birthdate data-guest-role="child">
                                                </div>
                                                <div class="ci-form-field">
                                                    <label>CCCD</label>
                                                    <input type="text" inputmode="numeric" maxlength="12" pattern="[0-9]{12}" placeholder="Nhập số CCCD" data-guest-cccd>
                                                </div>
                                                <div class="ci-form-field">
                                                    <label>Số điện thoại</label>
                                                    <input type="tel" placeholder="Nhập số điện thoại" data-guest-phone>
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        @endforeach
                    @endforeach

                    <div class="ci-detail-actions">
                        <button id="openCheckinConfirmDialogButton" type="button" class="btn btn-primary" @disabled($checkInBookings->isEmpty())>Nhận phòng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <dialog id="checkinConfirmDialog" class="ci-dialog">
        <div class="ci-dialog-body">
            <div class="ci-dialog-head">
                <h3 class="ci-dialog-title">Xác nhận nhận phòng</h3>
                <button type="button" class="ci-time-button">{{ now()->format('H:i') }}</button>
            </div>
            <p class="ci-dialog-text">Vui lòng kiểm tra lại thông tin phòng trước khi xác nhận nhận phòng.</p>

            <div class="ci-dialog-grid">
                <div class="ci-dialog-info">
                    <div class="ci-dialog-info-label">Mã đặt phòng</div>
                    <div id="dialogBookingId" class="ci-dialog-info-value">--</div>
                </div>
                <div class="ci-dialog-info">
                    <div class="ci-dialog-info-label">Số phòng</div>
                    <div id="dialogRoomNumber" class="ci-dialog-info-value">--</div>
                </div>
                <div class="ci-dialog-info">
                    <div class="ci-dialog-info-label">Số lượng khách</div>
                    <div id="dialogGuestSummary" class="ci-dialog-info-value">--</div>
                </div>
                <div class="ci-dialog-info">
                    <div class="ci-dialog-info-label">Thời gian lưu trú</div>
                    <div id="dialogStayPeriod" class="ci-dialog-info-value">--</div>
                </div>
            </div>

            <div class="ci-dialog-actions">
                <button id="closeCheckinConfirmDialogButton" type="button" class="btn btn-light">Hủy</button>
                <button id="confirmCheckinDialogButton" type="button" class="btn btn-primary">Xác nhận</button>
            </div>
        </div>
    </dialog>

    <script>
        const bookingItems = document.querySelectorAll('[data-booking-item]');
        const bookingToggles = document.querySelectorAll('[data-booking-toggle]');
        const roomOptions = document.querySelectorAll('.ci-room-option');
        const roomForms = document.querySelectorAll('[data-room-form]');
        const selectedStayChip = document.getElementById('selectedStayChip');
        const checkinDetailEmpty = document.getElementById('checkinDetailEmpty');
        const openCheckinConfirmDialogButton = document.getElementById('openCheckinConfirmDialogButton');
        const checkinConfirmDialog = document.getElementById('checkinConfirmDialog');
        const closeCheckinConfirmDialogButton = document.getElementById('closeCheckinConfirmDialogButton');
        const confirmCheckinDialogButton = document.getElementById('confirmCheckinDialogButton');
        const dialogBookingId = document.getElementById('dialogBookingId');
        const dialogRoomNumber = document.getElementById('dialogRoomNumber');
        const dialogGuestSummary = document.getElementById('dialogGuestSummary');
        const dialogStayPeriod = document.getElementById('dialogStayPeriod');
        const checkinSearchInput = document.querySelector('[data-checkin-search]');
        let selectedRoomButton = null;

        function parseInputDate(value) {
            const match = String(value || '').match(/^(\d{4})-(\d{2})-(\d{2})$/);

            if (!match) {
                return null;
            }

            const [, year, month, day] = match;
            const date = new Date(Number(year), Number(month) - 1, Number(day));

            if (
                date.getFullYear() !== Number(year)
                || date.getMonth() !== Number(month) - 1
                || date.getDate() !== Number(day)
            ) {
                return null;
            }

            date.setHours(0, 0, 0, 0);
            return date;
        }

        function calculateAge(birthDate) {
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();

            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age -= 1;
            }

            return age;
        }

        function setFieldError(input, message) {
            const field = input.closest('.ci-form-field');

            input.classList.add('is-invalid');

            if (!field) {
                return;
            }

            let errorElement = field.querySelector('.ci-field-error');

            if (!errorElement) {
                errorElement = document.createElement('div');
                errorElement.className = 'ci-field-error';
                field.appendChild(errorElement);
            }

            errorElement.textContent = message;
        }

        function clearFieldError(input) {
            input.classList.remove('is-invalid');
            input.closest('.ci-form-field')?.querySelector('.ci-field-error')?.remove();
        }

        function clearFormErrors(form) {
            if (!form) {
                return;
            }

            form.querySelectorAll('.is-invalid').forEach((input) => input.classList.remove('is-invalid'));
            form.querySelectorAll('.ci-field-error').forEach((errorElement) => errorElement.remove());
        }

        function validateCccdInput(input, { requireValue = false } = {}) {
            if (!input) {
                return true;
            }

            const rawValue = String(input.value || '');
            const digits = rawValue.replace(/\D/g, '');

            if (digits !== rawValue) {
                input.value = digits;
            }

            if (!digits) {
                if (requireValue) {
                    setFieldError(input, 'Vui lòng nhập số CCCD.');
                    return false;
                }

                clearFieldError(input);
                return true;
            }

            if (!/^\d{12}$/.test(digits)) {
                setFieldError(input, 'CCCD phải gồm đúng 12 chữ số.');
                return false;
            }

            clearFieldError(input);
            return true;
        }

        function validateBirthDateInput(input, { requireValue = false } = {}) {
            if (!input) {
                return true;
            }

            const role = input.dataset.guestRole || 'adult';
            const birthDate = parseInputDate(input.value);

            input.classList.remove('is-invalid');
            input.closest('.ci-form-field')?.querySelector('.ci-field-error')?.remove();

            if (!input.value) {
                if (requireValue) {
                    setFieldError(input, 'Vui lòng nhập ngày sinh.');
                    return false;
                }

                return true;
            }

            if (!birthDate) {
                setFieldError(input, 'Ngày sinh không hợp lệ.');
                return false;
            }

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (birthDate > today) {
                setFieldError(input, 'Ngày sinh không được lớn hơn hôm nay.');
                return false;
            }

            const age = calculateAge(birthDate);

            if (role === 'child' && age >= 12) {
                setFieldError(input, 'Khách từ 12 tuổi trở lên được tính là người lớn.');
                return false;
            }

            if (role === 'adult' && age < 12) {
                setFieldError(input, 'Khách nhỏ hơn 12 tuổi được tính là trẻ em.');
                return false;
            }

            validateAdultPresence(input.closest('[data-room-form]'), { showError: false });
            return true;
        }

        function validateAdultPresence(form, { showError = true } = {}) {
            if (!form) {
                return false;
            }

            const birthDateInputs = Array.from(form.querySelectorAll('[data-birthdate]'));
            const ages = birthDateInputs
                .map((input) => parseInputDate(input.value))
                .filter(Boolean)
                .map(calculateAge);
            const hasAdult = ages.some((age) => age >= 18);

            if (hasAdult || ages.length === 0) {
                return true;
            }

            if (showError) {
                const firstInput = birthDateInputs.find((input) => input.value) || birthDateInputs[0] || null;

                if (firstInput) {
                    setFieldError(firstInput, birthDateInputs.length === 1
                        ? 'Không cho phép chỉ một người dưới 18 tuổi check-in.'
                        : 'Cần có ít nhất một người đủ 18 tuổi trở lên để check-in.');
                }
            }

            return false;
        }

        function validateVisibleGuestForm({ scrollToError = true } = {}) {
            const visibleForm = document.querySelector('[data-room-form].is-visible');

            if (!visibleForm) {
                return false;
            }

            clearFormErrors(visibleForm);

            const birthDateInputs = Array.from(visibleForm.querySelectorAll('[data-birthdate]'));
            const nameInputs = Array.from(visibleForm.querySelectorAll('[data-guest-name]'));
            const cccdInputs = Array.from(visibleForm.querySelectorAll('[data-guest-cccd]'));
            let firstInvalidInput = null;

            nameInputs.forEach((input) => {
                if (!String(input.value || '').trim()) {
                    setFieldError(input, 'Vui lòng nhập họ và tên.');
                    firstInvalidInput ??= input;
                }
            });

            cccdInputs.forEach((input) => {
                if (!validateCccdInput(input, { requireValue: true })) {
                    firstInvalidInput ??= input;
                }
            });

            birthDateInputs.forEach((input) => {
                if (!validateBirthDateInput(input, { requireValue: true })) {
                    firstInvalidInput ??= input;
                }
            });

            if (!firstInvalidInput && !validateAdultPresence(visibleForm)) {
                firstInvalidInput = birthDateInputs[0] || null;
            }

            if (firstInvalidInput && scrollToError) {
                firstInvalidInput.focus({ preventScroll: true });
                firstInvalidInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return false;
            }

            return true;
        }

        function collectVisibleGuestPayload() {
            const visibleForm = document.querySelector('[data-room-form].is-visible');
            if (!visibleForm) {
                return null;
            }

            const guests = Array.from(visibleForm.querySelectorAll('.ci-guest-card')).map((card) => ({
                TenKhach: String(card.querySelector('[data-guest-name]')?.value || '').trim(),
                NgaySinh: String(card.querySelector('[data-birthdate]')?.value || '').trim(),
                CCCD: String(card.querySelector('[data-guest-cccd]')?.value || '').trim(),
                SoDienThoai: String(card.querySelector('[data-guest-phone]')?.value || '').trim() || null,
            }));

            return {
                MaPhong: Number(visibleForm.dataset.roomId || selectedRoomButton?.dataset.roomId || 0),
                KhachLuuTru: guests,
            };
        }

        function bindRealtimeBirthDateValidation(root = document) {
            root.querySelectorAll('[data-birthdate]').forEach((input) => {
                input.addEventListener('input', () => validateBirthDateInput(input));
                input.addEventListener('change', () => validateBirthDateInput(input));
                input.addEventListener('blur', () => {
                    validateBirthDateInput(input, { requireValue: true });
                    validateAdultPresence(input.closest('[data-room-form]'));
                });
            });
        }

        function bindRealtimeGuestFieldValidation(root = document) {
            root.querySelectorAll('[data-guest-name]').forEach((input) => {
                input.addEventListener('input', () => {
                    if (String(input.value || '').trim()) {
                        clearFieldError(input);
                    }
                });
                input.addEventListener('blur', () => {
                    if (!String(input.value || '').trim()) {
                        setFieldError(input, 'Vui lòng nhập họ và tên.');
                    }
                });
            });

            root.querySelectorAll('[data-guest-cccd]').forEach((input) => {
                input.addEventListener('input', () => validateCccdInput(input));
                input.addEventListener('blur', () => validateCccdInput(input, { requireValue: true }));
            });
        }

        function resetSelectedRoomState() {
            roomOptions.forEach((button) => {
                button.classList.remove('is-active');
            });

            roomForms.forEach((form) => {
                form.classList.remove('is-visible');
            });

            selectedRoomButton = null;

            if (selectedStayChip) {
                selectedStayChip.textContent = 'Chưa chọn phòng';
            }

            if (checkinDetailEmpty) {
                checkinDetailEmpty.hidden = false;
            }
        }

        function setActiveBooking(activeItem) {
            bookingItems.forEach((item) => {
                const isActive = item === activeItem;
                item.classList.toggle('is-active', isActive);

                const toggle = item.querySelector('[data-booking-toggle]');
                if (toggle) {
                    toggle.setAttribute('aria-expanded', isActive ? 'true' : 'false');
                }
            });
        }

        function setActiveRoom(activeRoomButton) {
            roomOptions.forEach((button) => {
                button.classList.toggle('is-active', button === activeRoomButton);
            });

            roomForms.forEach((form) => {
                form.classList.remove('is-visible');
            });

            const targetId = activeRoomButton.dataset.roomTarget;
            const targetForm = document.getElementById(targetId);

            if (targetForm) {
                targetForm.classList.add('is-visible');
                clearFormErrors(targetForm);
            }

            selectedRoomButton = activeRoomButton;

            if (selectedStayChip) {
                selectedStayChip.textContent = activeRoomButton.dataset.roomBadge || 'Đang chọn phòng';
            }

            if (checkinDetailEmpty) {
                checkinDetailEmpty.hidden = true;
            }
        }

        function openCheckinConfirmDialog() {
            const activeRoom = selectedRoomButton || document.querySelector('.ci-room-option.is-active');

            if (!activeRoom) {
                return;
            }

            if (!validateVisibleGuestForm()) {
                return;
            }

            if (dialogBookingId) {
                dialogBookingId.textContent = activeRoom?.dataset.bookingId || '--';
            }

            if (dialogRoomNumber) {
                dialogRoomNumber.textContent = activeRoom?.dataset.roomNumber || '--';
            }

            if (dialogGuestSummary) {
                dialogGuestSummary.textContent = activeRoom?.dataset.guestSummary || '--';
            }

            if (dialogStayPeriod) {
                dialogStayPeriod.textContent = activeRoom?.dataset.stayPeriod || '--';
            }

            if (checkinConfirmDialog) {
                checkinConfirmDialog.showModal();
            }
        }

        bookingToggles.forEach((toggle) => {
            toggle.addEventListener('click', () => {
                const bookingItem = toggle.closest('[data-booking-item]');
                if (!bookingItem) {
                    return;
                }

                setActiveBooking(bookingItem);
                resetSelectedRoomState();
            });
        });

        roomOptions.forEach((roomButton) => {
            roomButton.addEventListener('click', () => {
                const bookingItem = roomButton.closest('[data-booking-item]');
                if (bookingItem) {
                    setActiveBooking(bookingItem);
                }

                setActiveRoom(roomButton);
            });
        });

        if (openCheckinConfirmDialogButton) {
            openCheckinConfirmDialogButton.addEventListener('click', openCheckinConfirmDialog);
        }

        if (closeCheckinConfirmDialogButton) {
            closeCheckinConfirmDialogButton.addEventListener('click', () => {
                if (checkinConfirmDialog) {
                    checkinConfirmDialog.close();
                }
            });
        }

        if (confirmCheckinDialogButton) {
            confirmCheckinDialogButton.addEventListener('click', async () => {
                const activeRoom = selectedRoomButton || document.querySelector('.ci-room-option.is-active');
                const bookingId = activeRoom?.dataset.bookingId;

                if (!bookingId) {
                    return;
                }

                if (!validateVisibleGuestForm()) {
                    return;
                }

                confirmCheckinDialogButton.disabled = true;
                confirmCheckinDialogButton.textContent = 'Đang xác nhận...';

                try {
                    // Collect the payload with room and guest data
                    const payload = collectVisibleGuestPayload();
                    if (!payload) {
                        throw new Error('Không thể thu thập thông tin khách.');
                    }

                    // Get CSRF token from meta tag
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    const response = await fetch(`/api/dat-phong/${encodeURIComponent(bookingId)}/check-in`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken }),
                        },
                        body: JSON.stringify(payload),
                    });
                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Không thể nhận phòng.');
                    }

                    if (checkinConfirmDialog) {
                        checkinConfirmDialog.close();
                    }

                    window.location.reload();
                } catch (error) {
                    alert(error.message || 'Không thể nhận phòng.');
                    confirmCheckinDialogButton.disabled = false;
                    confirmCheckinDialogButton.textContent = 'Xác nhận';
                }
            });
        }

        if (checkinSearchInput) {
            checkinSearchInput.addEventListener('input', () => {
                const keyword = checkinSearchInput.value.trim().toLowerCase();

                bookingItems.forEach((item) => {
                    const searchText = item.dataset.searchText || '';
                    item.hidden = keyword !== '' && !searchText.includes(keyword);
                });
            });
        }

        bindRealtimeBirthDateValidation();
        bindRealtimeGuestFieldValidation();
    </script>
</x-app-layout>
