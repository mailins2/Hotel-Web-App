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
        .co-room-card {
            border-radius: 20px;
            background: linear-gradient(180deg, #f8fbff 0%, #fff 100%);
            border: 1px solid rgba(37, 99, 235, 0.14);
            padding: 1rem;
        }
        .co-action-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        .co-action-row .btn {
            flex: 1 1 220px;
        }
        .co-button-icon {
            width: 1rem;
            height: 1rem;
            margin-right: 0.45rem;
            vertical-align: -0.125rem;
        }
        .co-compensation-panel {
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 22px;
            border: 1px solid rgba(37, 99, 235, 0.14);
            background: linear-gradient(180deg, #f8fbff 0%, #fff 100%);
        }
        .co-compensation-panel[hidden] {
            display: none;
        }
        .co-compensation-title {
            font-size: 1rem;
            font-weight: 700;
            color: #6f1d01;
            margin-bottom: 0.85rem;
        }
        .co-compensation-note {
            margin-top: 0.75rem;
            color: #7c5b45;
            font-size: 0.92rem;
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
        .co-dialog-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.85rem;
            margin-bottom: 1rem;
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
        @media (max-width: 767.98px) {
            .co-detail-grid,
            .co-dialog-grid {
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
                            data-room="A102 - Suite"
                            data-stay="2 đêm - 2 khách"
                            data-service-total="450000"
                            data-services='[{"name":"Mini bar","type":"Dịch vụ ăn uống","price":180000},{"name":"Giặt ủi","type":"Dịch vụ ăn uống","price":150000},{"name":"Nước suối Evian","type":"Dịch vụ ăn uống","price":120000}]'
                            data-amount-due="1250000"
                            data-room-type="Suite"
                            data-room-note="Sẵn sàng dọn phòng sau trả phòng"
                            data-compensation-code="DB9002"
                            data-compensation-description="Hư hỏng khăn tắm hoặc vật dụng trong phòng"
                            data-compensation-amount="300000"
                            aria-pressed="true"
                        >
                            <div class="small text-uppercase text-muted fw-bold mb-1">Trả phòng 9002</div>
                            <div class="fw-semibold">Trần Bảo Ngọc</div>
                            <div class="text-muted small mt-1">Suite</div>
                            <div class="text-muted small">07/04/2026 đến 09/04/2026</div>
                        </button>

                        <button
                            type="button"
                            class="co-booking-card"
                            data-checkout-card
                            data-booking-id="9005"
                            data-customer="Đỗ Thanh Tùng"
                            data-phone="0908456123"
                            data-room="B202 - Suite"
                            data-stay="4 đêm - 3 khách"
                            data-service-total="680000"
                            data-services='[{"name":"Giặt ủi","type":"Dịch vụ phòng","price":200000},{"name":"Coca Cola","type":"Dịch vụ ăn uống","price":80000},{"name":"Bún bò","type":"Dịch vụ ăn uống","price":220000},{"name":"Trái cây theo mùa","type":"Dịch vụ ăn uống","price":180000}]'
                            data-amount-due="1980000"
                            data-room-type="Suite"
                            data-room-note="Sẵn sàng dọn phòng sau trả phòng"
                            data-compensation-code="DB9005"
                            data-compensation-description="Mất móc áo và bể ly thủy tinh"
                            data-compensation-amount="450000"
                            aria-pressed="false"
                        >
                            <div class="small text-uppercase text-muted fw-bold mb-1">Trả phòng 9005</div>
                            <div class="fw-semibold">Đỗ Thanh Tùng</div>
                            <div class="text-muted small mt-1">Suite</div>
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
                            <div class="small text-uppercase text-muted fw-bold">Số điện thoại</div>
                            <div id="checkoutCustomerPhone" class="fw-semibold mt-2">0912345678</div>
                        </div>
                        <div class="border rounded p-3">
                            <div class="small text-uppercase text-muted fw-bold">Phòng</div>
                            <div id="checkoutRoom" class="fw-semibold mt-2">A102 - Suite</div>
                        </div>
                        <div class="border rounded p-3">
                            <div class="small text-uppercase text-muted fw-bold">Lưu trú</div>
                            <div id="checkoutStay" class="fw-semibold mt-2">2 đêm - 2 khách</div>
                        </div>
                        <div class="border rounded p-3">
                            <div class="small text-uppercase text-muted fw-bold">Phát sinh dịch vụ</div>
                            <div id="checkoutServiceTotal" class="fw-semibold mt-2">450.000 VNĐ</div>
                        </div>
                        <div class="border rounded p-3">
                            <div class="small text-uppercase text-muted fw-bold">Còn phải thu</div>
                            <div id="checkoutAmountDue" class="fw-semibold mt-2">1.250.000 VNĐ</div>
                        </div>
                    </div>

                    <h6 class="mb-3">Loại phòng sẽ trả</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="co-room-card">
                                <div id="checkoutRoomType" class="fw-bold">Suite</div>
                                <div id="checkoutRoomNote" class="small mt-2">Sẵn sàng dọn phòng sau trả phòng</div>
                            </div>
                        </div>
                    </div>

                    <div id="compensationPanel" class="co-compensation-panel" hidden>
                        <div class="co-compensation-title">Thông tin đền bù</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="compensationCode" class="form-label">Mã đền bù</label>
                                <input id="compensationCode" type="text" class="form-control" value="DB9002">
                            </div>
                            <div class="col-md-6">
                                <label for="compensationBookingId" class="form-label">Mã đặt phòng</label>
                                <input id="compensationBookingId" type="text" class="form-control" value="9002" readonly>
                            </div>
                            <div class="col-12">
                                <label for="compensationDescription" class="form-label">Mô tả</label>
                                <textarea id="compensationDescription" class="form-control" rows="3">Hư hỏng khăn tắm hoặc vật dụng trong phòng</textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="compensationAmount" class="form-label">Tiền đền bù</label>
                                <input id="compensationAmount" type="number" class="form-control" value="300000" min="0" step="1000">
                            </div>
                        </div>
                    </div>

                    <div class="co-action-row">
                        <button id="toggleCompensationButton" type="button" class="btn btn-light border">
                            <svg class="co-button-icon" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                <path d="M8 1.5a.75.75 0 0 1 .75.75v5h5a.75.75 0 0 1 0 1.5h-5v5a.75.75 0 0 1-1.5 0v-5h-5a.75.75 0 0 1 0-1.5h5v-5A.75.75 0 0 1 8 1.5Z"/>
                            </svg>
                            Thêm đền bù
                        </button>
                    </div>

                    <button id="openCheckoutDialogButton" type="button" class="btn btn-primary w-100">Xác nhận trả phòng</button>
                </div>
            </div>
        </div>
    </div>

    <dialog id="checkoutConfirmDialog" class="co-dialog">
        <div class="co-dialog-body">
            <h3 class="co-dialog-title">Xác nhận trả phòng</h3>
            <p class="co-dialog-text">Vui lòng kiểm tra lại thời gian trả phòng hiện tại trước khi chuyển sang bước thanh toán.</p>

            <div class="co-dialog-grid">
                <div class="co-dialog-info">
                    <div class="co-dialog-label">Khách hàng</div>
                    <div id="dialogCheckoutCustomer" class="co-dialog-value">Trần Bảo Ngọc</div>
                </div>
                <div class="co-dialog-info">
                    <div class="co-dialog-label">Mã đặt phòng</div>
                    <div id="dialogCheckoutBookingId" class="co-dialog-value">9002</div>
                </div>
                <div class="co-dialog-info">
                    <div class="co-dialog-label">Ngày trả</div>
                    <div id="dialogCheckoutDate" class="co-dialog-value">05/05/2026</div>
                </div>
                <div class="co-dialog-info">
                    <div class="co-dialog-label">Thời gian trả</div>
                    <div id="dialogCheckoutTime" class="co-dialog-value">15:10</div>
                </div>
                <div class="co-dialog-info">
                    <div class="co-dialog-label">Phí đền bù</div>
                    <div id="dialogCompensationAmount" class="co-dialog-value">300.000 VNĐ</div>
                </div>
                <div class="co-dialog-info">
                    <div class="co-dialog-label">Tổng cần thanh toán</div>
                    <div id="dialogGrandTotal" class="co-dialog-value">1.550.000 VNĐ</div>
                </div>
            </div>

            <div class="co-dialog-actions">
                <button id="confirmCheckoutDialogButton" type="button" class="btn btn-primary">Xác nhận và thanh toán</button>
                <button id="cancelCheckoutDialogButton" type="button" class="btn btn-light">Đóng</button>
            </div>
        </div>
    </dialog>

    <script>
        const checkoutCards = document.querySelectorAll('[data-checkout-card]');
        const checkoutCustomerName = document.getElementById('checkoutCustomerName');
        const checkoutCustomerPhone = document.getElementById('checkoutCustomerPhone');
        const checkoutRoom = document.getElementById('checkoutRoom');
        const checkoutStay = document.getElementById('checkoutStay');
        const checkoutServiceTotal = document.getElementById('checkoutServiceTotal');
        const checkoutAmountDue = document.getElementById('checkoutAmountDue');
        const checkoutRoomType = document.getElementById('checkoutRoomType');
        const checkoutRoomNote = document.getElementById('checkoutRoomNote');
        const compensationPanel = document.getElementById('compensationPanel');
        const toggleCompensationButton = document.getElementById('toggleCompensationButton');
        const compensationCode = document.getElementById('compensationCode');
        const compensationBookingId = document.getElementById('compensationBookingId');
        const compensationDescription = document.getElementById('compensationDescription');
        const compensationAmount = document.getElementById('compensationAmount');
        const openCheckoutDialogButton = document.getElementById('openCheckoutDialogButton');
        const checkoutConfirmDialog = document.getElementById('checkoutConfirmDialog');
        const dialogCheckoutCustomer = document.getElementById('dialogCheckoutCustomer');
        const dialogCheckoutBookingId = document.getElementById('dialogCheckoutBookingId');
        const dialogCheckoutDate = document.getElementById('dialogCheckoutDate');
        const dialogCheckoutTime = document.getElementById('dialogCheckoutTime');
        const dialogCompensationAmount = document.getElementById('dialogCompensationAmount');
        const dialogGrandTotal = document.getElementById('dialogGrandTotal');
        const confirmCheckoutDialogButton = document.getElementById('confirmCheckoutDialogButton');
        const cancelCheckoutDialogButton = document.getElementById('cancelCheckoutDialogButton');
        const paymentPageUrl = "{{ route('reception.payments.create') }}";
        let pendingCheckoutPaymentPayload = null;

        function getActiveCheckoutCard() {
            return document.querySelector('[data-checkout-card].is-active') || checkoutCards[0];
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN').format(Number(amount || 0)) + ' VNĐ';
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

        function parseAmount(value) {
            return Number(String(value || '').replace(/[^\d]/g, '')) || 0;
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

        function syncCompensationForm(activeCard) {
            compensationCode.value = activeCard?.dataset.compensationCode || '';
            compensationBookingId.value = activeCard?.dataset.bookingId || '';
            compensationDescription.value = activeCard?.dataset.compensationDescription || '';
            compensationAmount.value = activeCard?.dataset.compensationAmount || '';
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
            checkoutCustomerPhone.textContent = activeCard.dataset.phone || '--';
            checkoutRoom.textContent = activeCard.dataset.room || '--';
            checkoutStay.textContent = activeCard.dataset.stay || '--';
            checkoutServiceTotal.textContent = formatCurrency(activeCard.dataset.serviceTotal);
            checkoutAmountDue.textContent = formatCurrency(activeCard.dataset.amountDue);
            checkoutRoomType.textContent = activeCard.dataset.roomType || '--';
            checkoutRoomNote.textContent = activeCard.dataset.roomNote || '--';

            syncCompensationForm(activeCard);
        }

        function buildCheckoutPaymentPayload() {
            const activeCard = getActiveCheckoutCard();
            const currentDateTime = new Date();
            const amountDue = Number(activeCard?.dataset.amountDue || 0);
            const compensationTotal = parseAmount(compensationAmount.value);
            const serviceItems = parseServices(activeCard?.dataset.services);
            const serviceAmount = serviceItems.reduce((total, item) => total + Number(item.price || 0), 0);

            return {
                invoiceId: `HD${activeCard?.dataset.bookingId || '0000'}`,
                bookingId: activeCard?.dataset.bookingId || '',
                customer: activeCard?.dataset.customer || '',
                room: activeCard?.dataset.room || '',
                phone: activeCard?.dataset.phone || '',
                stay: activeCard?.dataset.stay || '',
                checkoutDate: formatDisplayDate(currentDateTime),
                checkoutTime: formatDisplayTime(currentDateTime),
                serviceItems,
                serviceAmount: serviceAmount || Number(activeCard?.dataset.serviceTotal || 0),
                amountDue,
                compensationCode: compensationCode.value || '',
                compensationDescription: compensationDescription.value || '',
                compensationAmount: compensationTotal,
                grandTotal: amountDue + compensationTotal,
            };
        }

        function openCheckoutDialog() {
            const payload = buildCheckoutPaymentPayload();
            pendingCheckoutPaymentPayload = payload;

            dialogCheckoutCustomer.textContent = payload.customer || '--';
            dialogCheckoutBookingId.textContent = payload.bookingId || '--';
            dialogCheckoutDate.textContent = payload.checkoutDate || '--';
            dialogCheckoutTime.textContent = payload.checkoutTime || '--';
            dialogCompensationAmount.textContent = formatCurrency(payload.compensationAmount);
            dialogGrandTotal.textContent = formatCurrency(payload.grandTotal);

            checkoutConfirmDialog.showModal();
        }

        checkoutCards.forEach((card) => {
            card.addEventListener('click', () => {
                syncCheckoutDetails(card);
            });
        });

        toggleCompensationButton.addEventListener('click', () => {
            const isHidden = compensationPanel.hasAttribute('hidden');
            syncCompensationForm(getActiveCheckoutCard());
            compensationPanel.toggleAttribute('hidden', !isHidden);
            toggleCompensationButton.innerHTML = isHidden
                ? `<svg class="co-button-icon" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                        <path d="M3.22 3.22a.75.75 0 0 1 1.06 0L8 6.94l3.72-3.72a.75.75 0 1 1 1.06 1.06L9.06 8l3.72 3.72a.75.75 0 1 1-1.06 1.06L8 9.06l-3.72 3.72a.75.75 0 1 1-1.06-1.06L6.94 8 3.22 4.28a.75.75 0 0 1 0-1.06Z"/>
                   </svg>
                   Đóng form đền bù`
                : `<svg class="co-button-icon" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                        <path d="M8 1.5a.75.75 0 0 1 .75.75v5h5a.75.75 0 0 1 0 1.5h-5v5a.75.75 0 0 1-1.5 0v-5h-5a.75.75 0 0 1 0-1.5h5v-5A.75.75 0 0 1 8 1.5Z"/>
                   </svg>
                   Thêm đền bù`;
        });

        openCheckoutDialogButton.addEventListener('click', openCheckoutDialog);
        cancelCheckoutDialogButton.addEventListener('click', () => {
            pendingCheckoutPaymentPayload = null;
            checkoutConfirmDialog.close();
        });
        confirmCheckoutDialogButton.addEventListener('click', () => {
            const payload = pendingCheckoutPaymentPayload || buildCheckoutPaymentPayload();
            sessionStorage.setItem('receptionCheckoutPayment', JSON.stringify(payload));
            window.location.href = paymentPageUrl;
        });

        syncCheckoutDetails(getActiveCheckoutCard());
    </script>
</x-app-layout>
