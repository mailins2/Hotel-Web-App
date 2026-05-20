@php
    $checkOutBookings = $checkOutBookings ?? collect();
    $checkOutStats = $checkOutStats ?? ['upcoming' => 0, 'today' => 0, 'roomsFreeing' => 0];
    $formatDate = fn ($value) => $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : '--';
    $formatMoneyValue = fn ($value) => (float) ($value ?? 0);
    $getNights = function ($booking) {
        if (!$booking?->NgayNhanPhong || !$booking?->NgayTraPhong) {
            return 0;
        }

        return max(1, \Carbon\Carbon::parse($booking->NgayNhanPhong)->diffInDays(\Carbon\Carbon::parse($booking->NgayTraPhong)));
    };
    $getServiceItems = function ($booking) use ($formatMoneyValue) {
        $invoiceDetails = $booking->hoaDon?->chiTietHoaDons ?? collect();

        return $invoiceDetails
            ->filter(fn ($detail) => $detail->MaSuDung)
            ->map(function ($detail) use ($formatMoneyValue) {
                $service = $detail->suDung?->dichVu;
                $roomNumber = $detail->suDung?->chiTietDatPhong?->phong?->SoPhong;
                $quantity = max(1, (int) ($detail->SoLuong ?? $detail->suDung?->SoLuong ?? 1));
                $unitPrice = $formatMoneyValue($detail->DonGia ?? $service?->GiaDV);

                return [
                    'name' => trim(($detail->MoTa ?: ($service?->TenDV ?? 'Dịch vụ')) . ($roomNumber ? " - Phòng {$roomNumber}" : '')),
                    'type' => $service?->LoaiDVText ?? 'Dịch vụ',
                    'price' => $unitPrice * $quantity,
                ];
            })
            ->values();
    };
    $getRoomItems = function ($booking) use ($getNights, $formatMoneyValue) {
        $nights = $getNights($booking);
        $invoiceDetails = $booking->hoaDon?->chiTietHoaDons ?? collect();
        $roomNumbersByType = $booking->chiTietDatPhong
            ->filter(fn ($detail) => $detail?->phong?->MaLoaiPhong)
            ->groupBy(fn ($detail) => (string) $detail->phong->MaLoaiPhong)
            ->map(fn ($items) => $items
                ->map(fn ($detail) => $detail?->phong?->SoPhong)
                ->filter()
                ->values()
                ->implode(', '));

        $invoiceRoomItems = $invoiceDetails
            ->filter(fn ($detail) => $detail->MaLoaiPhong)
            ->map(function ($detail) use ($nights, $formatMoneyValue, $roomNumbersByType) {
                $quantity = max(1, (int) ($detail->SoLuong ?? 1));
                $lineTotal = $formatMoneyValue($detail->DonGia) * $quantity;
                $unitPrice = $nights > 0 ? ($lineTotal / $quantity / $nights) : $formatMoneyValue($detail->DonGia);

                return [
                    'roomTypeId' => (string) $detail->MaLoaiPhong,
                    'type' => $detail->loaiPhong?->TenLoaiPhong ?? 'Loại phòng',
                    'roomNumbers' => $roomNumbersByType->get((string) $detail->MaLoaiPhong, '--') ?: '--',
                    'quantity' => $quantity,
                    'unitPrice' => $unitPrice,
                    'nights' => $nights,
                    'total' => $lineTotal,
                ];
            })
            ->values();

        if ($invoiceRoomItems->isNotEmpty()) {
            return $invoiceRoomItems;
        }

        return $booking->chiTietDatPhong
            ->groupBy(fn ($detail) => $detail->phong?->MaLoaiPhong ?? $detail->MaPhong)
            ->map(function ($items) use ($nights, $formatMoneyValue) {
                $roomType = $items->first()?->phong?->loaiPhong;
                $unitPrice = $formatMoneyValue($roomType?->GiaGiam ?? $roomType?->GiaPhong);
                $roomNumbers = $items
                    ->map(fn ($detail) => $detail?->phong?->SoPhong)
                    ->filter()
                    ->values()
                    ->implode(', ');

                return [
                    'roomTypeId' => (string) ($roomType?->MaLoaiPhong ?? $items->first()?->MaPhong),
                    'type' => $roomType?->TenLoaiPhong ?? 'Loại phòng',
                    'roomNumbers' => $roomNumbers ?: '--',
                    'quantity' => $items->count(),
                    'unitPrice' => $unitPrice,
                    'nights' => $nights,
                    'total' => $unitPrice * $items->count() * $nights,
                ];
            })
            ->values();
    };
    $getRoomSummaryItems = function ($booking) {
        return $booking->chiTietDatPhong
            ->groupBy(fn ($detail) => (string) ($detail->phong?->MaLoaiPhong ?? $detail->MaPhong))
            ->map(function ($items) {
                $firstDetail = $items->first();
                $roomType = $firstDetail?->phong?->loaiPhong;
                $roomNumbers = $items
                    ->map(fn ($detail) => $detail?->phong?->SoPhong)
                    ->filter()
                    ->values()
                    ->implode(', ');

                return [
                    'roomTypeId' => (string) ($roomType?->MaLoaiPhong ?? $firstDetail?->MaPhong),
                    'type' => $roomType?->TenLoaiPhong ?? 'Loại phòng',
                    'roomNumbers' => $roomNumbers ?: '--',
                ];
            })
            ->values();
    };
@endphp

<x-app-layout :assets="['animation']">
    <style>
        .co-shell { padding-top: 4.5rem; }
        .co-hero, .co-card {
            background: #fff;
            border: 1px solid rgba(166, 98, 43, 0.15);
            border-radius: 28px;
            box-shadow: 0 18px 40px rgba(120, 74, 44, 0.08);
        }
        .co-hero {
            padding: 1.8rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(180deg, #fff7ef 0%, #fff 55%, #f8fbff 100%);
        }
        .co-card { padding: 1.4rem; height: 100%; }
        .co-booking-list { display: flex; flex-direction: column; gap: 0.85rem; }
        .co-booking-card {
            width: 100%;
            border: 1px solid rgba(166, 98, 43, 0.12);
            border-radius: 22px;
            padding: 1rem;
            background: #fff;
            text-align: left;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
        }
        .co-booking-card:hover {
            transform: translateY(-1px);
            border-color: rgba(166, 98, 43, 0.22);
            box-shadow: 0 14px 28px rgba(120, 74, 44, 0.08);
        }
        .co-booking-card.is-active {
            border-color: rgba(166, 98, 43, 0.28);
            background: linear-gradient(180deg, #fffaf3 0%, #fff 100%);
            box-shadow: 0 18px 30px rgba(120, 74, 44, 0.1);
        }
        .co-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.85rem;
        }
        .co-room-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.85rem;
            margin-bottom: 1rem;
        }
        .co-room-card {
            border-radius: 20px;
            background: linear-gradient(180deg, #f8fbff 0%, #fff 100%);
            border: 1px solid rgba(37, 99, 235, 0.14);
            padding: 1rem;
            text-align: left;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
        }
        .co-room-card:hover {
            transform: translateY(-1px);
            border-color: rgba(37, 99, 235, 0.24);
            box-shadow: 0 12px 26px rgba(37, 99, 235, 0.08);
        }
        .co-room-number { color: #1d4ed8; font-size: 1rem; font-weight: 700; }
        .co-room-type { margin-top: 0.2rem; color: #6f1d01; font-weight: 600; }
        .co-empty {
            border: 1px dashed rgba(166, 98, 43, 0.28);
            border-radius: 18px;
            padding: 1rem;
            color: #7c5b45;
            background: #fffaf3;
        }
        .co-dialog {
            width: min(560px, calc(100vw - 2rem));
            border: none;
            border-radius: 24px;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 28px 70px rgba(73, 18, 15, 0.22);
        }
        .co-dialog::backdrop { background: rgba(73, 18, 15, 0.28); backdrop-filter: blur(2px); }
        .co-dialog-body {
            padding: 1.5rem;
            background: linear-gradient(180deg, #fffaf3 0%, #fff 100%);
        }
        .co-guest-dialog .co-dialog-body { max-height: min(82vh, 760px); overflow-y: auto; }
        .co-dialog-title { margin: 0 0 0.5rem; color: #6f1d01; font-size: 1.35rem; font-weight: 700; }
        .co-dialog-text { margin: 0 0 1.25rem; color: #7c5b45; }
        .co-dialog-info {
            border: 1px solid rgba(166, 98, 43, 0.12);
            border-radius: 18px;
            padding: 0.95rem 1rem;
            background: #fff;
        }
        .co-dialog-label {
            color: #8b5e3c;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .co-dialog-value { margin-top: 0.45rem; color: #6f1d01; font-size: 1.05rem; font-weight: 600; }
        .co-dialog-actions { display: flex; flex-wrap: wrap; gap: 0.75rem; }
        .co-dialog-actions .btn { flex: 1 1 200px; }
        .co-guest-stack { display: flex; flex-direction: column; gap: 0.85rem; }
        .co-guest-card {
            border: 1px solid rgba(166, 98, 43, 0.12);
            border-radius: 18px;
            padding: 1rem;
            background: #fff;
        }
        .co-guest-chip {
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
        .co-guest-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.85rem;
            margin-top: 0.9rem;
        }
        @media (max-width: 767.98px) {
            .co-detail-grid,
            .co-room-list,
            .co-guest-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="co-shell">
        <div class="co-hero">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="mb-2">Trả phòng</h1>
                    <p class="text-muted mb-0">Danh sách thông tin trả phòng</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('reception.bookings.index') }}" class="btn btn-light">Danh sách đặt phòng</a>
                    <a href="{{ route('reception.check-ins.create') }}" class="btn btn-light">Trang nhận phòng</a>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="co-card text-center"><div class="small text-uppercase text-muted fw-bold">Khách sắp trả phòng</div><div class="h4 mb-0 mt-2">{{ $checkOutStats['upcoming'] ?? 0 }}</div></div></div>
            <div class="col-md-4"><div class="co-card text-center"><div class="small text-uppercase text-muted fw-bold">Trả phòng hôm nay</div><div class="h4 mb-0 mt-2">{{ $checkOutStats['today'] ?? 0 }}</div></div></div>
            <div class="col-md-4"><div class="co-card text-center"><div class="small text-uppercase text-muted fw-bold">Phòng sẽ trống</div><div class="h4 mb-0 mt-2">{{ $checkOutStats['roomsFreeing'] ?? 0 }}</div></div></div>
        </div>

        <div class="row g-4">
            <div class="col-xl-5">
                <div class="co-card">
                    <h5 class="mb-3">Danh sách đang lưu trú</h5>
                    <div class="co-booking-list">
                        @forelse($checkOutBookings as $booking)
                            @php
                                $customer = $booking->khachHang;
                                $customerName = $customer?->TenKH ?? 'Khách chưa có tên';
                                $nights = $getNights($booking);
                                $guestCount = max((int) ($booking->luuTrus?->count() ?? 0), (int) ($booking->SoLuong ?? 0));
                                $stayText = $nights . ' đêm - ' . $guestCount . ' khách';
                                $serviceItems = $getServiceItems($booking);
                                $serviceTotal = $serviceItems->sum('price');
                                $roomItems = $getRoomItems($booking);
                                $roomSummaryItems = $getRoomSummaryItems($booking);
                                $invoice = $booking->hoaDon;
                                $paidAmount = (float) ($invoice?->DaThanhToan ?? $invoice?->thanhToans?->sum('SoTien') ?? 0);
                                $totalAmount = (float) ($invoice?->TongTien ?? $roomItems->sum('total') + $serviceTotal);
                                $amountDue = max($totalAmount - $paidAmount, 0);
                                $activeClass = $loop->first ? 'is-active' : '';
                            @endphp
                            <button
                                type="button"
                                class="co-booking-card {{ $activeClass }}"
                                data-checkout-card
                                data-booking-id="{{ $booking->MaDatPhong }}"
                                data-invoice-id="{{ $invoice?->MaHD ? 'HD' . $invoice->MaHD : 'HD' . $booking->MaDatPhong }}"
                                data-customer="{{ $customerName }}"
                                data-phone="{{ $customer?->SoDienThoai ?? '' }}"
                                data-stay="{{ $stayText }}"
                                data-stay-period="{{ $formatDate($booking->NgayNhanPhong) }} - {{ $formatDate($booking->NgayTraPhong) }}"
                                data-service-total="{{ $serviceTotal }}"
                                data-services='@json($serviceItems)'
                                data-room-summary-items='@json($roomSummaryItems)'
                                data-room-items='@json($roomItems)'
                                data-paid-amount="{{ $paidAmount }}"
                                data-total-amount="{{ $totalAmount }}"
                                data-amount-due="{{ $amountDue }}"
                                aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                            >
                                <div class="small text-uppercase text-muted fw-bold mb-1">Trả phòng #{{ $booking->MaDatPhong }}</div>
                                <div class="fw-semibold">{{ $customerName }}</div>
                                <div class="text-muted small">{{ $formatDate($booking->NgayNhanPhong) }} đến {{ $formatDate($booking->NgayTraPhong) }}</div>
                            </button>
                        @empty
                            <div class="co-empty">Chưa có đặt phòng nào đang ở để trả phòng.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-xl-7">
                <div class="co-card">
                    <h5 class="mb-3">Chi tiết xác nhận</h5>
                    <div class="co-detail-grid mb-4">
                        <div class="border rounded p-3">
                            <div class="small text-uppercase text-muted fw-bold">Khách hàng</div>
                            <div id="checkoutCustomerName" class="fw-semibold mt-2">{{ $checkOutBookings->first()?->khachHang?->TenKH ?? '--' }}</div>
                        </div>
                        <div class="border rounded p-3">
                            <div class="small text-uppercase text-muted fw-bold">Lưu trú</div>
                            <div id="checkoutStay" class="fw-semibold mt-2">--</div>
                        </div>
                    </div>

                    <h6 class="mb-3">Phòng sẽ trả</h6>
                    <div id="checkoutRoomList" class="co-room-list mb-4">
                        @foreach($checkOutBookings as $booking)
                            @php
                                $stayGuestsByRoom = ($booking->luuTrus ?? collect())->groupBy(fn ($guest) => (int) $guest->MaPhong);
                            @endphp
                            @foreach($booking->chiTietDatPhong as $detail)
                                @php
                                    $room = $detail->phong;
                                    $roomType = $room?->loaiPhong;
                                    $guests = ($stayGuestsByRoom->get((int) $detail->MaPhong) ?? collect())
                                        ->values()
                                        ->map(function ($guest, $index) {
                                            return [
                                                'label' => 'Khách ' . ($index + 1),
                                                'name' => $guest->TenKhach,
                                                'birth' => $guest->NgaySinh ? \Carbon\Carbon::parse($guest->NgaySinh)->format('d/m/Y') : '--',
                                                'phone' => $guest->SoDienThoai ?: '--',
                                                'cccd' => $guest->CCCD ?: '--',
                                            ];
                                        });
                                @endphp
                                <button
                                    type="button"
                                    class="co-room-card"
                                    data-room-guest-card
                                    data-booking-id="{{ $booking->MaDatPhong }}"
                                    data-room-type="{{ $roomType?->TenLoaiPhong ?? 'Chưa có loại phòng' }}"
                                    data-guests='@json($guests)'
                                >
                                    <div class="co-room-number">Phòng {{ $room?->SoPhong ?? '--' }}</div>
                                    <div class="co-room-type">{{ $roomType?->TenLoaiPhong ?? 'Chưa có loại phòng' }}</div>
                                </button>
                            @endforeach
                        @endforeach
                    </div>

                    <button id="processCheckoutButton" type="button" class="btn btn-primary w-100" @disabled($checkOutBookings->isEmpty())>Trả phòng</button>
                </div>
            </div>
        </div>
    </div>

    <dialog id="roomGuestDialog" class="co-dialog co-guest-dialog">
        <div class="co-dialog-body">
            <h3 class="co-dialog-title">Thông tin khách ở</h3>
            <p class="co-dialog-text">Danh sách khách lưu trú theo phòng đã chọn.</p>
            <div id="roomGuestDialogStack" class="co-guest-stack"></div>
            <div class="co-dialog-actions mt-3">
                <button id="closeRoomGuestDialogButton" type="button" class="btn btn-light">Đóng</button>
            </div>
        </div>
    </dialog>

    <script>
        const checkoutCards = document.querySelectorAll('[data-checkout-card]');
        const roomGuestCards = document.querySelectorAll('[data-room-guest-card]');
        const checkoutCustomerName = document.getElementById('checkoutCustomerName');
        const checkoutStay = document.getElementById('checkoutStay');
        const roomGuestDialog = document.getElementById('roomGuestDialog');
        const roomGuestDialogStack = document.getElementById('roomGuestDialogStack');
        const closeRoomGuestDialogButton = document.getElementById('closeRoomGuestDialogButton');
        const processCheckoutButton = document.getElementById('processCheckoutButton');
        const paymentPageUrl = "{{ route('reception.payments.create') }}";

        function getActiveCheckoutCard() {
            return document.querySelector('[data-checkout-card].is-active') || checkoutCards[0];
        }

        function formatDisplayDate(dateValue) {
            return new Intl.DateTimeFormat('vi-VN').format(dateValue);
        }

        function formatDisplayTime(dateValue) {
            return new Intl.DateTimeFormat('vi-VN', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false,
            }).format(dateValue);
        }

        function parseJsonDataset(rawValue) {
            if (!rawValue) {
                return [];
            }

            try {
                const parsedValue = JSON.parse(rawValue);
                return Array.isArray(parsedValue) ? parsedValue : [];
            } catch (error) {
                return [];
            }
        }

        function syncRoomCards(activeCard) {
            const bookingId = activeCard?.dataset.bookingId || '';

            roomGuestCards.forEach((roomCard) => {
                roomCard.hidden = roomCard.dataset.bookingId !== bookingId;
            });
        }

        function syncCheckoutDetails(activeCard) {
            if (!activeCard) {
                checkoutCustomerName.textContent = '--';
                checkoutStay.textContent = '--';
                return;
            }

            checkoutCards.forEach((card) => {
                const isActive = card === activeCard;
                card.classList.toggle('is-active', isActive);
                card.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });

            checkoutCustomerName.textContent = activeCard.dataset.customer || '--';
            checkoutStay.textContent = activeCard.dataset.stay || '--';

            syncRoomCards(activeCard);
        }

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, (char) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            }[char]));
        }

        function buildGuestCards(guests) {
            if (!guests.length) {
                return '<div class="co-empty">Phòng này chưa có thông tin khách lưu trú.</div>';
            }

            return guests.map((guest) => `
                <div class="co-guest-card">
                    <span class="co-guest-chip">${escapeHtml(guest.label || '--')}</span>
                    <div class="co-guest-grid">
                        <div class="co-dialog-info">
                            <div class="co-dialog-label">Tên khách</div>
                            <div class="co-dialog-value">${escapeHtml(guest.name || '--')}</div>
                        </div>
                        <div class="co-dialog-info">
                            <div class="co-dialog-label">Ngày sinh</div>
                            <div class="co-dialog-value">${escapeHtml(guest.birth || '--')}</div>
                        </div>
                        <div class="co-dialog-info">
                            <div class="co-dialog-label">Số điện thoại</div>
                            <div class="co-dialog-value">${escapeHtml(guest.phone || '--')}</div>
                        </div>
                        <div class="co-dialog-info">
                            <div class="co-dialog-label">CCCD</div>
                            <div class="co-dialog-value">${escapeHtml(guest.cccd || '--')}</div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function openRoomGuestDialog(roomCard) {
            const guests = parseJsonDataset(roomCard.dataset.guests);
            roomGuestDialogStack.innerHTML = buildGuestCards(guests);
            roomGuestDialog.showModal();
        }

        function buildCheckoutPaymentPayload() {
            const activeCard = getActiveCheckoutCard();
            const currentDateTime = new Date();
            const amountDue = Number(activeCard?.dataset.amountDue || 0);
            const serviceItems = parseJsonDataset(activeCard?.dataset.services);
            const roomSummaryItems = parseJsonDataset(activeCard?.dataset.roomSummaryItems);
            const roomItems = parseJsonDataset(activeCard?.dataset.roomItems);
            const serviceAmount = serviceItems.reduce((total, item) => total + Number(item.price || 0), 0);
            const totalAmount = Number(activeCard?.dataset.totalAmount || amountDue);
            const paidAmount = Number(activeCard?.dataset.paidAmount || 0);

            return {
                invoiceId: activeCard?.dataset.invoiceId || `HD${activeCard?.dataset.bookingId || '0000'}`,
                bookingId: activeCard?.dataset.bookingId || '',
                customer: activeCard?.dataset.customer || '',
                phone: activeCard?.dataset.phone || '',
                stay: activeCard?.dataset.stay || '',
                stayPeriod: activeCard?.dataset.stayPeriod || '',
                checkoutDate: formatDisplayDate(currentDateTime),
                checkoutTime: formatDisplayTime(currentDateTime),
                roomSummaryItems,
                roomItems,
                serviceItems,
                serviceAmount,
                totalAmount,
                paidAmount,
                amountDue,
                compensationCode: '',
                compensationDescription: '',
                compensationAmount: 0,
                grandTotal: amountDue,
            };
        }

        function processCheckoutPayment() {
            const activeCard = getActiveCheckoutCard();

            if (!activeCard) {
                return;
            }

            const payload = buildCheckoutPaymentPayload();
            sessionStorage.setItem('receptionCheckoutPayment', JSON.stringify(payload));
            window.location.href = paymentPageUrl;
        }

        checkoutCards.forEach((card) => {
            card.addEventListener('click', () => {
                syncCheckoutDetails(card);
            });
        });

        roomGuestCards.forEach((roomCard) => {
            roomCard.addEventListener('click', () => {
                openRoomGuestDialog(roomCard);
            });
        });

        closeRoomGuestDialogButton?.addEventListener('click', () => {
            roomGuestDialog.close();
        });

        processCheckoutButton?.addEventListener('click', processCheckoutPayment);

        syncCheckoutDetails(getActiveCheckoutCard());
    </script>
</x-app-layout>
