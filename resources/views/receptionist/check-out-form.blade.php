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
        .co-booking-list {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }
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
        .co-room-number {
            color: #1d4ed8;
            font-size: 1rem;
            font-weight: 700;
        }
        .co-room-type {
            margin-top: 0.2rem;
            color: #6f1d01;
            font-weight: 600;
        }
        .co-dialog {
            width: min(560px, calc(100vw - 2rem));
            border: none;
            border-radius: 24px;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 28px 70px rgba(73, 18, 15, 0.22);
        }
        .co-dialog::backdrop {
            background: rgba(73, 18, 15, 0.28);
            backdrop-filter: blur(2px);
        }
        .co-dialog-body {
            padding: 1.5rem;
            background: linear-gradient(180deg, #fffaf3 0%, #fff 100%);
        }
        .co-guest-dialog .co-dialog-body {
            max-height: min(82vh, 760px);
            overflow-y: auto;
        }
        .co-dialog-title {
            margin: 0 0 0.5rem;
            color: #6f1d01;
            font-size: 1.35rem;
            font-weight: 700;
        }
        .co-dialog-text {
            margin: 0 0 1.25rem;
            color: #7c5b45;
        }
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
        .co-dialog-value {
            margin-top: 0.45rem;
            color: #6f1d01;
            font-size: 1.05rem;
            font-weight: 600;
        }
        .co-dialog-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .co-dialog-actions .btn {
            flex: 1 1 200px;
        }
        .co-guest-stack {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }
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
            .co-guest-grid {
                grid-template-columns: 1fr;
            }
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
            <div class="col-md-4"><div class="co-card text-center"><div class="small text-uppercase text-muted fw-bold">Khách sắp trả phòng</div><div class="h4 mb-0 mt-2">4</div></div></div>
            <div class="col-md-4"><div class="co-card text-center"><div class="small text-uppercase text-muted fw-bold">Trả phòng hôm nay</div><div class="h4 mb-0 mt-2">2</div></div></div>
            <div class="col-md-4"><div class="co-card text-center"><div class="small text-uppercase text-muted fw-bold">Phòng sẽ trống</div><div class="h4 mb-0 mt-2">2</div></div></div>
        </div>

        <div class="row g-4">
            <div class="col-xl-5">
                <div class="co-card">
                    <h5 class="mb-3">Danh sách trả phòng hôm nay</h5>
                    <div class="co-booking-list">
                        <button
                            type="button"
                            class="co-booking-card is-active"
                            data-checkout-card
                            data-booking-id="9002"
                            data-customer="Trần Bảo Ngọc"
                            data-phone="0912345678"
                            data-stay="2 đêm - 2 khách"
                            data-service-total="450000"
                            data-services='[{"name":"Mini bar","type":"Dịch vụ ăn uống","price":180000},{"name":"Giặt ủi","type":"Dịch vụ phòng","price":150000},{"name":"Nước suối Evian","type":"Dịch vụ ăn uống","price":120000}]'
                            data-amount-due="1250000"
                            aria-pressed="true"
                        >
                            <div class="small text-uppercase text-muted fw-bold mb-1">Trả phòng #9002</div>
                            <div class="fw-semibold">Trần Bảo Ngọc</div>
                            <div class="text-muted small">07/04/2026 đến 09/04/2026</div>
                        </button>

                        <button
                            type="button"
                            class="co-booking-card"
                            data-checkout-card
                            data-booking-id="9005"
                            data-customer="Đỗ Thanh Tùng"
                            data-phone="0908456123"
                            data-stay="4 đêm - 3 khách"
                            data-service-total="680000"
                            data-services='[{"name":"Giặt ủi","type":"Dịch vụ phòng","price":200000},{"name":"Coca Cola","type":"Dịch vụ ăn uống","price":80000},{"name":"Bún bò","type":"Dịch vụ ăn uống","price":220000},{"name":"Trái cây theo mùa","type":"Dịch vụ ăn uống","price":180000}]'
                            data-amount-due="1980000"
                            aria-pressed="false"
                        >
                            <div class="small text-uppercase text-muted fw-bold mb-1">Trả phòng #9005</div>
                            <div class="fw-semibold">Đỗ Thanh Tùng</div>
                            <div class="text-muted small">06/04/2026 đến 10/04/2026</div>
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-xl-7">
                <div class="co-card">
                    <h5 class="mb-3">Chi tiết xác nhận</h5>
                    <div class="co-detail-grid mb-4">
                        <div class="border rounded p-3">
                            <div class="small text-uppercase text-muted fw-bold">Khách hàng</div>
                            <div id="checkoutCustomerName" class="fw-semibold mt-2">Trần Bảo Ngọc</div>
                        </div>
                        <div class="border rounded p-3">
                            <div class="small text-uppercase text-muted fw-bold">Lưu trú</div>
                            <div id="checkoutStay" class="fw-semibold mt-2">2 đêm - 2 khách</div>
                        </div>
                    </div>

                    <h6 class="mb-3">Loại phòng sẽ trả</h6>
                    <div id="checkoutRoomList" class="co-room-list mb-4">
                        <button
                            type="button"
                            class="co-room-card"
                            data-room-guest-card
                            data-booking-id="9002"
                            data-room-type="Suite"
                            data-guests='[{"label":"Người lớn 1","name":"Trần Bảo Ngọc","birth":"12/03/1995","phone":"0912345678","cccd":"079204000111"},{"label":"Người lớn 2","name":"Lê Thanh Mai","birth":"21/08/1997","phone":"0909988776","cccd":"079204000222"}]'
                        >
                            <div class="co-room-number">Phòng 102</div>
                            <div class="co-room-type">Suite</div>
                        </button>
                        <button
                            type="button"
                            class="co-room-card"
                            data-room-guest-card
                            data-booking-id="9002"
                            data-room-type="Suite Twin"
                            data-guests='[{"label":"Người lớn 1","name":"Nguyễn Minh Châu","birth":"05/10/1993","phone":"0934567812","cccd":"079204000333"},{"label":"Trẻ em 1","name":"Nguyễn Gia Hân","birth":"18/07/2018","phone":"--","cccd":"--"}]'
                        >
                            <div class="co-room-number">Phòng 104</div>
                            <div class="co-room-type">Suite Twin</div>
                        </button>
                    </div>

                    <button id="processCheckoutButton" type="button" class="btn btn-primary w-100">Trả phòng</button>
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

        function parseServices(rawValue) {
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

        function parseGuests(rawValue) {
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

            roomGuestCards.forEach((roomCard, index) => {
                const isVisible = roomCard.dataset.bookingId === bookingId;
                roomCard.hidden = !isVisible;
                if (index === 0 && isVisible) {
                    roomCard.closest('.co-room-list')?.removeAttribute('data-empty');
                }
            });
        }

        function syncCheckoutDetails(activeCard) {
            if (!activeCard) {
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

        function buildGuestCards(guests) {
            return guests.map((guest) => `
                <div class="co-guest-card">
                    <span class="co-guest-chip">${guest.label || '--'}</span>
                    <div class="co-guest-grid">
                        <div class="co-dialog-info">
                            <div class="co-dialog-label">Tên khách</div>
                            <div class="co-dialog-value">${guest.name || '--'}</div>
                        </div>
                        <div class="co-dialog-info">
                            <div class="co-dialog-label">Ngày sinh</div>
                            <div class="co-dialog-value">${guest.birth || '--'}</div>
                        </div>
                        <div class="co-dialog-info">
                            <div class="co-dialog-label">Số điện thoại</div>
                            <div class="co-dialog-value">${guest.phone || '--'}</div>
                        </div>
                        <div class="co-dialog-info">
                            <div class="co-dialog-label">CCCD</div>
                            <div class="co-dialog-value">${guest.cccd || '--'}</div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function openRoomGuestDialog(roomCard) {
            const guests = parseGuests(roomCard.dataset.guests);
            roomGuestDialogStack.innerHTML = buildGuestCards(guests);
            roomGuestDialog.showModal();
        }

        function buildCheckoutPaymentPayload() {
            const activeCard = getActiveCheckoutCard();
            const currentDateTime = new Date();
            const amountDue = Number(activeCard?.dataset.amountDue || 0);
            const serviceItems = parseServices(activeCard?.dataset.services);
            const serviceAmount = serviceItems.reduce((total, item) => total + Number(item.price || 0), 0);

            return {
                invoiceId: `HD${activeCard?.dataset.bookingId || '0000'}`,
                bookingId: activeCard?.dataset.bookingId || '',
                customer: activeCard?.dataset.customer || '',
                phone: activeCard?.dataset.phone || '',
                stay: activeCard?.dataset.stay || '',
                checkoutDate: formatDisplayDate(currentDateTime),
                checkoutTime: formatDisplayTime(currentDateTime),
                serviceItems,
                serviceAmount: serviceAmount || Number(activeCard?.dataset.serviceTotal || 0),
                amountDue,
                compensationCode: '',
                compensationDescription: '',
                compensationAmount: 0,
                grandTotal: amountDue,
            };
        }

        function processCheckoutPayment() {
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

        closeRoomGuestDialogButton.addEventListener('click', () => {
            roomGuestDialog.close();
        });

        processCheckoutButton.addEventListener('click', processCheckoutPayment);

        syncCheckoutDetails(getActiveCheckoutCard());
    </script>
</x-app-layout>
