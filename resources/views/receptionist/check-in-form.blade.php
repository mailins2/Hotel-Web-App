<x-app-layout :assets="['animation']">
    <style>
        .ci-shell { padding-top: 4.5rem; }
        .ci-hero, .ci-card {
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
        .ci-card { padding: 1.4rem; height: 100%; }
        .ci-booking-list {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }
        .ci-booking-card {
            width: 100%;
            border: 1px solid rgba(166, 98, 43, 0.12);
            border-radius: 22px;
            padding: 1rem;
            background: #fff;
            text-align: left;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
        }
        .ci-booking-card:hover {
            transform: translateY(-1px);
            border-color: rgba(166, 98, 43, 0.22);
            box-shadow: 0 14px 28px rgba(120, 74, 44, 0.08);
        }
        .ci-booking-card.is-active {
            border-color: rgba(166, 98, 43, 0.28);
            background: linear-gradient(180deg, #fffaf3 0%, #fff 100%);
            box-shadow: 0 18px 30px rgba(120, 74, 44, 0.1);
        }
        .ci-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.85rem;
        }
        .ci-room-card {
            border-radius: 20px;
            background: linear-gradient(180deg, #f6fefc 0%, #fff 100%);
            border: 1px solid rgba(15, 118, 110, 0.14);
            padding: 1rem;
        }
        .ci-dialog {
            width: min(520px, calc(100vw - 2rem));
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
        .ci-dialog-title {
            margin: 0 0 0.5rem;
            color: #6f1d01;
            font-size: 1.35rem;
            font-weight: 700;
        }
        .ci-dialog-text {
            margin: 0 0 1.25rem;
            color: #7c5b45;
        }
        .ci-dialog-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.85rem;
            margin-bottom: 1rem;
        }
        .ci-dialog-info {
            border: 1px solid rgba(166, 98, 43, 0.12);
            border-radius: 18px;
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
            font-size: 1.05rem;
            font-weight: 600;
        }
        .ci-dialog-fee {
            margin-bottom: 1.2rem;
            padding: 1rem 1.1rem;
            border-radius: 18px;
            background: linear-gradient(180deg, #fff4e5 0%, #fff 100%);
            border: 1px solid rgba(234, 88, 12, 0.16);
        }
        .ci-dialog-fee[hidden] {
            display: none;
        }
        .ci-dialog-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .ci-dialog-actions .btn {
            flex: 1 1 180px;
        }
        @media (max-width: 767.98px) {
            .ci-detail-grid {
                grid-template-columns: 1fr;
            }
            .ci-dialog-grid {
                grid-template-columns: 1fr;
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
            <div class="col-md-4"><div class="ci-card text-center"><div class="small text-uppercase text-muted fw-bold">Đặt phòng chờ nhận</div><div class="h4 mb-0 mt-2">5</div></div></div>
            <div class="col-md-4"><div class="ci-card text-center"><div class="small text-uppercase text-muted fw-bold">Khách đến hôm nay</div><div class="h4 mb-0 mt-2">2</div></div></div>
            <div class="col-md-4"><div class="ci-card text-center"><div class="small text-uppercase text-muted fw-bold">Đặt phòng nhận rồi</div><div class="h4 mb-0 mt-2">4</div></div></div>
        </div>

        <div class="row g-4">
            <div class="col-xl-5">
                <div class="ci-card">
                    <h5 class="mb-3">Danh sách nhận phòng hôm nay</h5>
                    <div class="ci-booking-list">
                        <button
                            type="button"
                            class="ci-booking-card is-active"
                            data-booking-card
                            data-booking-id="9001"
                            data-customer="Nguyễn Minh An"
                            data-phone="0901234567"
                            data-cccd="079204000111"
                            data-stay-period="08/04/2026 đến 10/04/2026"
                            data-room-summary="2 đêm - 1 phòng"
                            data-room-type="Deluxe"
                            data-room-capacity="Tối đa 2 khách"
                            data-early-fee="250000"
                            aria-pressed="true"
                        >
                            <div class="small text-uppercase text-muted fw-bold mb-1">Đặt phòng 9001</div>
                            <div class="fw-semibold">Nguyễn Minh An</div>
                            <div class="text-muted small mt-1">Deluxe</div>
                            <div class="text-muted small">08/04/2026 đến 10/04/2026</div>
                        </button>

                        <button
                            type="button"
                            class="ci-booking-card"
                            data-booking-card
                            data-booking-id="9004"
                            data-customer="Phạm Khánh Vy"
                            data-phone="0988001122"
                            data-cccd="079204000444"
                            data-stay-period="08/04/2026 đến 11/04/2026"
                            data-room-summary="3 đêm - 1 phòng"
                            data-room-type="Family"
                            data-room-capacity="Tối đa 5 khách"
                            data-early-fee="400000"
                            aria-pressed="false"
                        >
                            <div class="small text-uppercase text-muted fw-bold mb-1">Đặt phòng 9004</div>
                            <div class="fw-semibold">Phạm Khánh Vy</div>
                            <div class="text-muted small mt-1">Family</div>
                            <div class="text-muted small">08/04/2026 đến 11/04/2026</div>
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-xl-7">
                <div class="ci-card">
                    <h5 class="mb-3">Chi tiết xác nhận</h5>
                    <div class="ci-detail-grid mb-4">
                        <div class="border rounded p-3">
                            <div class="small text-uppercase text-muted fw-bold">Khách hàng</div>
                            <div id="checkinCustomerName" class="fw-semibold mt-2">Nguyễn Minh An</div>
                        </div>
                        <div class="border rounded p-3">
                            <div class="small text-uppercase text-muted fw-bold">Số điện thoại</div>
                            <div id="checkinCustomerPhone" class="fw-semibold mt-2">0901234567</div>
                        </div>
                        <div class="border rounded p-3">
                            <div class="small text-uppercase text-muted fw-bold">CCCD</div>
                            <div id="checkinCustomerCccd" class="fw-semibold mt-2">079204000111</div>
                        </div>
                        <div class="border rounded p-3">
                            <div class="small text-uppercase text-muted fw-bold">Thời gian lưu trú</div>
                            <div id="checkinStayPeriod" class="fw-semibold mt-2">08/04/2026 đến 10/04/2026</div>
                        </div>
                        <div class="border rounded p-3">
                            <div class="small text-uppercase text-muted fw-bold">Số đêm / phòng</div>
                            <div id="checkinRoomSummary" class="fw-semibold mt-2">2 đêm - 1 phòng</div>
                        </div>
                    </div>

                    <h6 class="mb-3">Loại phòng đã đặt</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="ci-room-card">
                                <div id="checkinRoomType" class="fw-bold">Deluxe</div>
                                <div id="checkinRoomCapacity" class="small mt-2">Tối đa 2 khách</div>
                            </div>
                        </div>
                    </div>

                    <button id="openCheckinDialogButton" type="button" class="btn btn-primary w-100">Xác nhận nhận phòng</button>
                </div>
            </div>
        </div>
    </div>

    <dialog id="checkinConfirmDialog" class="ci-dialog">
        <div class="ci-dialog-body">
            <h3 id="checkinDialogTitle" class="ci-dialog-title">Xác nhận nhận phòng</h3>
            <p id="checkinDialogText" class="ci-dialog-text">Vui lòng kiểm tra lại thời gian nhận phòng trước khi xác nhận.</p>

            <div class="ci-dialog-grid">
                <div class="ci-dialog-info">
                    <div class="ci-dialog-info-label">Khách hàng</div>
                    <div id="dialogCustomerName" class="ci-dialog-info-value">Nguyễn Minh An</div>
                </div>
                <div class="ci-dialog-info">
                    <div class="ci-dialog-info-label">Mã đặt phòng</div>
                    <div id="dialogBookingId" class="ci-dialog-info-value">9001</div>
                </div>
                <div class="ci-dialog-info">
                    <div class="ci-dialog-info-label">Ngày nhận</div>
                    <div id="dialogCheckinDate" class="ci-dialog-info-value">05/05/2026</div>
                </div>
                <div class="ci-dialog-info">
                    <div class="ci-dialog-info-label">Thời gian nhận</div>
                    <div id="dialogCheckinTime" class="ci-dialog-info-value">14:00</div>
                </div>
            </div>

            <div id="earlyCheckinFeeBlock" class="ci-dialog-fee" hidden>
                <div class="ci-dialog-info-label">Phí nhận phòng sớm sớm</div>
                <div id="dialogEarlyFee" class="ci-dialog-info-value">250.000 VNĐ</div>
            </div>

            <div class="ci-dialog-actions">
                <button id="confirmCheckinButton" type="button" class="btn btn-primary">Xác nhận</button>
                <button id="cancelCheckinDialogButton" type="button" class="btn btn-light">Đóng</button>
            </div>
        </div>
    </dialog>

    <script>
        const bookingCards = document.querySelectorAll('[data-booking-card]');
        const checkinCustomerName = document.getElementById('checkinCustomerName');
        const checkinCustomerPhone = document.getElementById('checkinCustomerPhone');
        const checkinCustomerCccd = document.getElementById('checkinCustomerCccd');
        const checkinStayPeriod = document.getElementById('checkinStayPeriod');
        const checkinRoomSummary = document.getElementById('checkinRoomSummary');
        const checkinRoomType = document.getElementById('checkinRoomType');
        const checkinRoomCapacity = document.getElementById('checkinRoomCapacity');
        const openCheckinDialogButton = document.getElementById('openCheckinDialogButton');
        const checkinConfirmDialog = document.getElementById('checkinConfirmDialog');
        const checkinDialogTitle = document.getElementById('checkinDialogTitle');
        const checkinDialogText = document.getElementById('checkinDialogText');
        const dialogCustomerName = document.getElementById('dialogCustomerName');
        const dialogBookingId = document.getElementById('dialogBookingId');
        const dialogCheckinDate = document.getElementById('dialogCheckinDate');
        const dialogCheckinTime = document.getElementById('dialogCheckinTime');
        const earlyCheckinFeeBlock = document.getElementById('earlyCheckinFeeBlock');
        const dialogEarlyFee = document.getElementById('dialogEarlyFee');
        const confirmCheckinButton = document.getElementById('confirmCheckinButton');
        const cancelCheckinDialogButton = document.getElementById('cancelCheckinDialogButton');

        function syncCheckinDetails(activeCard) {
            if (!activeCard) {
                return;
            }

            bookingCards.forEach((card) => {
                const isActive = card === activeCard;
                card.classList.toggle('is-active', isActive);
                card.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });

            checkinCustomerName.textContent = activeCard.dataset.customer || '--';
            checkinCustomerPhone.textContent = activeCard.dataset.phone || '--';
            checkinCustomerCccd.textContent = activeCard.dataset.cccd || '--';
            checkinStayPeriod.textContent = activeCard.dataset.stayPeriod || '--';
            checkinRoomSummary.textContent = activeCard.dataset.roomSummary || '--';
            checkinRoomType.textContent = activeCard.dataset.roomType || '--';
            checkinRoomCapacity.textContent = activeCard.dataset.roomCapacity || '--';
        }

        function getActiveBookingCard() {
            return document.querySelector('[data-booking-card].is-active') || bookingCards[0];
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

        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount) + ' VNĐ';
        }

        function openCheckinDialog() {
            const activeCard = getActiveBookingCard();
            const currentDateTime = new Date();
            const isEarlyCheckin = currentDateTime.getHours() < 14;
            const earlyFee = Number(activeCard?.dataset.earlyFee || 0);

            dialogCustomerName.textContent = activeCard?.dataset.customer || '--';
            dialogBookingId.textContent = activeCard?.dataset.bookingId || '--';
            dialogCheckinDate.textContent = formatDisplayDate(currentDateTime);
            dialogCheckinTime.textContent = formatDisplayTime(currentDateTime);

            if (isEarlyCheckin) {
                checkinDialogTitle.textContent = 'Xác nhận nhận phòng sớm';
                checkinDialogText.textContent = 'Khách đang nhận phòng trước 14:00. Vui lòng xác nhận thời gian nhận phòng và phụ phí nhận phòng sớm.';
                dialogEarlyFee.textContent = formatCurrency(earlyFee);
                earlyCheckinFeeBlock.hidden = false;
            } else {
                checkinDialogTitle.textContent = 'Xác nhận nhận phòng';
                checkinDialogText.textContent = 'Khách nhận phòng sau 14:00. Vui lòng xác nhận thời gian nhận phòng hiện tại.';
                dialogEarlyFee.textContent = formatCurrency(0);
                earlyCheckinFeeBlock.hidden = true;
            }

            checkinConfirmDialog.showModal();
        }

        bookingCards.forEach((card) => {
            card.addEventListener('click', () => {
                syncCheckinDetails(card);
            });
        });

        openCheckinDialogButton.addEventListener('click', openCheckinDialog);
        cancelCheckinDialogButton.addEventListener('click', () => {
            checkinConfirmDialog.close();
        });
        confirmCheckinButton.addEventListener('click', () => {
            checkinConfirmDialog.close();
        });

        syncCheckinDetails(document.querySelector('[data-booking-card].is-active') || bookingCards[0]);
    </script>
</x-app-layout>
