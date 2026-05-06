@if(request()->routeIs('reception.bookings.create'))
    <x-app-layout :assets="['animation']">
        <style>
            .rf-shell { padding-top: 4.5rem; }
            .rf-hero, .rf-card {
                background: #fff;
                border: 1px solid rgba(166, 98, 43, 0.15);
                border-radius: 28px;
                box-shadow: 0 18px 40px rgba(120, 74, 44, 0.08);
            }
            .rf-hero {
                padding: 1.8rem;
                margin-bottom: 1.5rem;
                background: linear-gradient(180deg, #fffaf3 0%, #fff 55%, #f6fbfb 100%);
            }
            .rf-card { padding: 1.4rem; height: 100%; }
            .rf-room-type-list { display: flex; flex-direction: column; gap: 1rem; }
            .rf-room-type-card {
                border: 1px solid rgba(166, 98, 43, 0.12);
                border-radius: 22px;
                background: #fff;
                overflow: hidden;
                transition: box-shadow 0.18s ease, border-color 0.18s ease;
            }
            .rf-room-type-card[open] {
                border-color: rgba(166, 98, 43, 0.22);
                box-shadow: 0 16px 30px rgba(120, 74, 44, 0.08);
            }
            .rf-room-type-summary {
                list-style: none;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 1rem 1.15rem;
            }
            .rf-room-type-summary::-webkit-details-marker { display: none; }
            .rf-room-type-meta { min-width: 0; }
            .rf-room-type-name {
                font-size: 1.05rem;
                font-weight: 700;
                color: #6f1d01;
                margin-bottom: 0.2rem;
            }
            .rf-room-type-note { color: #8b5e3c; font-size: 0.92rem; }
            .rf-room-count {
                flex: 0 0 auto;
                padding: 0.55rem 0.9rem;
                border-radius: 999px;
                background: #fff7ef;
                /* border: 1px solid rgba(166, 98, 43, 0.14); */
                color: #8a4b22;
                font-weight: 700;
                white-space: nowrap;
            }
            .rf-room-empty-list {
                padding: 0 1.15rem 1.1rem;
                display: grid;
                gap: 0.75rem;
            }
            .rf-room-empty-item {
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 0.95rem 1rem;
                border-radius: 18px;
                background: linear-gradient(180deg, #f9fffb 0%, #fff 100%);
                border: 1px solid rgba(22, 101, 52, 0.12);
                text-align: left;
                transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
            }
            .rf-room-empty-item:hover {
                transform: translateY(-1px);
                border-color: rgba(22, 101, 52, 0.2);
                box-shadow: 0 12px 24px rgba(22, 101, 52, 0.08);
            }
            .rf-room-empty-item.is-selected {
                border-color: rgba(166, 98, 43, 0.3);
                background: linear-gradient(180deg, #fff7ef 0%, #fff 100%);
                box-shadow: 0 14px 28px rgba(166, 98, 43, 0.12);
            }
            .rf-room-empty-code {
                font-weight: 700;
                color: #6f1d01;
            }
            .rf-room-empty-status {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0.38rem 0.8rem;
                border-radius: 999px;
                background: #dcfce7;
                color: #166534;
                font-size: 0.86rem;
                font-weight: 700;
                flex: 0 0 auto;
            }
            .rf-room-empty-item.is-selected .rf-room-empty-status {
                background: #fed7aa;
                color: #9a3412;
            }
            .rf-summary-block + .rf-summary-block { margin-top: 1rem; }
            .rf-summary-line {
                margin-top: 0.35rem;
                color: #7c5b45;
                font-size: 0.94rem;
            }
            .rf-summary-room-empty {
                margin-top: 0.6rem;
                color: #8b5e3c;
            }
            .rf-summary-room-entry + .rf-summary-room-entry { margin-top: 0.55rem; }
            .rf-dialog {
                width: min(460px, calc(100vw - 2rem));
                border: none;
                border-radius: 24px;
                padding: 0;
                overflow: hidden;
                box-shadow: 0 28px 70px rgba(73, 18, 15, 0.22);
            }
            .rf-dialog::backdrop {
                background: rgba(73, 18, 15, 0.28);
                backdrop-filter: blur(2px);
            }
            .rf-dialog-body {
                padding: 1.5rem;
                background: linear-gradient(180deg, #fffaf3 0%, #fff 100%);
            }
            .rf-dialog-title {
                margin: 0 0 0.6rem;
                color: #6f1d01;
                font-size: 1.35rem;
                font-weight: 700;
                margin-bottom: 20px;
            }
            .rf-dialog-text {
                margin: 0 0 1.25rem;
                color: #7c5b45;
            }
            .rf-dialog-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
            }
            .rf-dialog-actions .btn {
                flex: 1 1 160px;
            }
        </style>

        <div class="rf-shell">
            <div class="rf-hero">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h1 class="mb-2">Đặt phòng</h1>
                        <p class="text-muted mb-0">Tạo đơn đặt phòng mới</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('reception.bookings.index') }}" class="btn btn-light">Danh sách đặt phòng</a>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-8">
                    <form data-ui-only-form>
                        <div class="rf-card mb-4">
                            <h5 class="mb-3">Thông tin khách hàng</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Khách hàng</label>
                                    <select id="bookingCustomer" class="form-select">
                                        <option value="1" data-name="Nguyễn Minh An" data-phone="0901234567" data-cccd="079204000111">1 - Nguyễn Minh An</option>
                                        <option value="2" data-name="Trần Bảo Ngọc" data-phone="0912345678" data-cccd="079204000222">2 - Trần Bảo Ngọc</option>
                                        <option value="4" data-name="Phạm Khánh Vy" data-phone="0988001122" data-cccd="079204000444">4 - Phạm Khánh Vy</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Số điện thoại</label>
                                    <input id="bookingPhone" type="text" class="form-control" value="0901234567">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">CCCD</label>
                                    <input id="bookingCccd" type="text" class="form-control" value="079204000111">
                                </div>
                            </div>
                        </div>

                        <div class="rf-card mb-4">
                            <h5 class="mb-3">Lịch lưu trú</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Ngày đặt</label>
                                    <input id="bookingDate" type="date" class="form-control" value="2026-04-08">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ngày nhận phòng</label>
                                    <input id="checkinDate" type="date" class="form-control" value="2026-04-08">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ngày trả phòng</label>
                                    <input id="checkoutDate" type="date" class="form-control" value="2026-04-10">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Số lượng người ở</label>
                                    <input id="guestCount" type="number" class="form-control" value="2" min="1">
                                </div>
                            </div>
                        </div>

                        <div class="rf-card">
                            <h5 class="mb-3">Chọn phòng</h5>
                            <div class="rf-room-type-list">
                                <details class="rf-room-type-card" open>
                                    <summary class="rf-room-type-summary">
                                        <div class="rf-room-type-meta">
                                            <div class="rf-room-type-name">Deluxe</div>
                                            <div class="rf-room-type-note">Bấm để xem danh sách phòng trống</div>
                                        </div>
                                        <span class="rf-room-count">Còn trống 2 phòng</span>
                                    </summary>
                                    <div class="rf-room-empty-list">
                                        <button type="button" class="rf-room-empty-item" data-room-button data-room="A101" data-type="Deluxe" data-capacity="2" aria-pressed="false">
                                            <span class="rf-room-empty-code">Phòng A101</span>
                                            <span class="rf-room-empty-status">Trống</span>
                                        </button>
                                        <button type="button" class="rf-room-empty-item" data-room-button data-room="A104" data-type="Deluxe" data-capacity="2" aria-pressed="false">
                                            <span class="rf-room-empty-code">Phòng A104</span>
                                            <span class="rf-room-empty-status">Trống</span>
                                        </button>
                                    </div>
                                </details>

                                <details class="rf-room-type-card">
                                    <summary class="rf-room-type-summary">
                                        <div class="rf-room-type-meta">
                                            <div class="rf-room-type-name">Suite</div>
                                            <div class="rf-room-type-note">Bấm để xem danh sách phòng trống</div>
                                        </div>
                                        <span class="rf-room-count">Còn trống 3 phòng</span>
                                    </summary>
                                    <div class="rf-room-empty-list">
                                        <button type="button" class="rf-room-empty-item" data-room-button data-room="B202" data-type="Suite" data-capacity="4" aria-pressed="false">
                                            <span class="rf-room-empty-code">Phòng B202</span>
                                            <span class="rf-room-empty-status">Trống</span>
                                        </button>
                                        <button type="button" class="rf-room-empty-item" data-room-button data-room="B205" data-type="Suite" data-capacity="4" aria-pressed="false">
                                            <span class="rf-room-empty-code">Phòng B205</span>
                                            <span class="rf-room-empty-status">Trống</span>
                                        </button>
                                        <button type="button" class="rf-room-empty-item" data-room-button data-room="C201" data-type="Suite" data-capacity="4" aria-pressed="false">
                                            <span class="rf-room-empty-code">Phòng C201</span>
                                            <span class="rf-room-empty-status">Trống</span>
                                        </button>
                                    </div>
                                </details>

                                <details class="rf-room-type-card">
                                    <summary class="rf-room-type-summary">
                                        <div class="rf-room-type-meta">
                                            <div class="rf-room-type-name">Family</div>
                                            <div class="rf-room-type-note">Bấm để xem danh sách phòng trống</div>
                                        </div>
                                        <span class="rf-room-count">Còn trống 2 phòng</span>
                                    </summary>
                                    <div class="rf-room-empty-list">
                                        <button type="button" class="rf-room-empty-item" data-room-button data-room="C302" data-type="Family" data-capacity="5" aria-pressed="false">
                                            <span class="rf-room-empty-code">Phòng C302</span>
                                            <span class="rf-room-empty-status">Trống</span>
                                        </button>
                                        <button type="button" class="rf-room-empty-item" data-room-button data-room="C304" data-type="Family" data-capacity="5" aria-pressed="false">
                                            <span class="rf-room-empty-code">Phòng C304</span>
                                            <span class="rf-room-empty-status">Trống</span>
                                        </button>
                                    </div>
                                </details>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-xl-4">
                    <div class="rf-card">
                        <h5 class="mb-3">Tóm tắt đặt phòng</h5>

                        <div class="border rounded p-3 rf-summary-block">
                            <div class="small text-muted text-uppercase fw-bold">Khách hàng</div>
                            <div id="summaryCustomerName" class="fw-semibold mt-2">Nguyễn Minh An</div>
                            <div id="summaryCustomerPhone" class="rf-summary-line">0901234567</div>
                            <div id="summaryCustomerCccd" class="rf-summary-line">079204000111</div>
                        </div>

                        <div class="border rounded p-3 rf-summary-block">
                            <div class="small text-muted text-uppercase fw-bold">Khoảng thời gian</div>
                            <div id="summaryBookingPeriod" class="fw-semibold mt-2">08/04/2026 đến 10/04/2026</div>
                        </div>

                        <div class="border rounded p-3 rf-summary-block">
                            <div class="small text-muted text-uppercase fw-bold">Phòng đã chọn</div>
                            <div id="summarySelectedRooms" class="mt-2">
                                <div class="rf-summary-room-empty">Chưa chọn phòng</div>
                            </div>
                        </div>

                        <div class="border rounded p-3 rf-summary-block mb-4">
                            <div class="small text-muted text-uppercase fw-bold">Số người ở</div>
                            <div id="summaryCapacity" class="fw-semibold mt-2">0 khách</div>
                        </div>

                        <button id="saveBookingButton" type="button" class="btn btn-primary w-100">Lưu đặt phòng</button>
                    </div>
                </div>
            </div>
        </div>

        <dialog id="bookingSuccessDialog" class="rf-dialog">
            <div class="rf-dialog-body">
                <h3 class="rf-dialog-title">Đặt phòng thành công</h3>
                <p class="rf-dialog-text">Bạn muốn chuyển sang nhận phòng ngay hoặc tạo một đặt phòng khác?</p>
                <div class="rf-dialog-actions">
                    <button id="goToCheckinButton" type="button" class="btn btn-primary">Nhận phòng</button>
                    <button id="createAnotherBookingButton" type="button" class="btn btn-light">Đặt phòng mới</button>
                </div>
            </div>
        </dialog>

        <script>
            const bookingCustomer = document.getElementById('bookingCustomer');
            const bookingPhone = document.getElementById('bookingPhone');
            const bookingCccd = document.getElementById('bookingCccd');
            const checkinDate = document.getElementById('checkinDate');
            const checkoutDate = document.getElementById('checkoutDate');
            const guestCount = document.getElementById('guestCount');
            const roomButtons = document.querySelectorAll('[data-room-button]');
            const summaryCustomerName = document.getElementById('summaryCustomerName');
            const summaryCustomerPhone = document.getElementById('summaryCustomerPhone');
            const summaryCustomerCccd = document.getElementById('summaryCustomerCccd');
            const summaryBookingPeriod = document.getElementById('summaryBookingPeriod');
            const summarySelectedRooms = document.getElementById('summarySelectedRooms');
            const summaryCapacity = document.getElementById('summaryCapacity');
            const saveBookingButton = document.getElementById('saveBookingButton');
            const bookingSuccessDialog = document.getElementById('bookingSuccessDialog');
            const goToCheckinButton = document.getElementById('goToCheckinButton');
            const createAnotherBookingButton = document.getElementById('createAnotherBookingButton');

            function formatDate(dateValue) {
                if (!dateValue) {
                    return '--/--/----';
                }

                const dateParts = dateValue.split('-');
                return dateParts.length === 3
                    ? `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`
                    : dateValue;
            }

            function syncCustomerFields() {
                const selectedOption = bookingCustomer.options[bookingCustomer.selectedIndex];
                bookingPhone.value = selectedOption.dataset.phone || '';
                bookingCccd.value = selectedOption.dataset.cccd || '';
                syncSummary();
            }

            function syncSummary() {
                const selectedOption = bookingCustomer.options[bookingCustomer.selectedIndex];
                const selectedRooms = document.querySelectorAll('[data-room-button].is-selected');
                const guestTotal = Number(guestCount.value || 0);
                let capacityTotal = 0;

                summaryCustomerName.textContent = selectedOption.dataset.name || selectedOption.textContent.trim();
                summaryCustomerPhone.textContent = bookingPhone.value || '--';
                summaryCustomerCccd.textContent = bookingCccd.value || '--';
                summaryBookingPeriod.textContent = `${formatDate(checkinDate.value)} đến ${formatDate(checkoutDate.value)}`;

                if (selectedRooms.length === 0) {
                    summarySelectedRooms.innerHTML = '<div class="rf-summary-room-empty">Chưa chọn phòng</div>';
                    summaryCapacity.textContent = guestTotal > 0 ? `${guestTotal} khách` : '0 khách';
                    saveBookingButton.disabled = false;
                    return;
                }

                summarySelectedRooms.innerHTML = '';

                selectedRooms.forEach((roomButton) => {
                    capacityTotal += Number(roomButton.dataset.capacity || 0);
                    summarySelectedRooms.insertAdjacentHTML(
                        'beforeend',
                        `<div class="rf-summary-room-entry fw-semibold">${roomButton.dataset.room} - ${roomButton.dataset.type}</div>`
                    );
                });

                summaryCapacity.textContent = guestTotal > 0 ? `${guestTotal} khách` : '0 khách';
                saveBookingButton.disabled = guestTotal < 1 || guestTotal > capacityTotal;
            }

            roomButtons.forEach((roomButton) => {
                roomButton.addEventListener('click', () => {
                    roomButton.classList.toggle('is-selected');
                    roomButton.setAttribute(
                        'aria-pressed',
                        roomButton.classList.contains('is-selected') ? 'true' : 'false'
                    );
                    syncSummary();
                });
            });

            bookingCustomer.addEventListener('change', syncCustomerFields);
            bookingPhone.addEventListener('input', syncSummary);
            bookingCccd.addEventListener('input', syncSummary);
            checkinDate.addEventListener('input', syncSummary);
            checkoutDate.addEventListener('input', syncSummary);
            guestCount.addEventListener('input', syncSummary);

            saveBookingButton.addEventListener('click', () => {
                bookingSuccessDialog.showModal();
            });

            goToCheckinButton.addEventListener('click', () => {
                window.location.href = "{{ route('reception.check-ins.create') }}";
            });

            createAnotherBookingButton.addEventListener('click', () => {
                window.location.href = "{{ route('reception.bookings.create') }}";
            });

            syncSummary();
        </script>
    </x-app-layout>
@else
    <x-receptionist.form-page
        :is-edit="true"
        :index-route="route('reception.bookings.index')"
    >
        <div class="form-group col-md-6"><label class="form-label">Mã đặt phòng</label><input type="text" class="form-control hm-readonly-input" value="9001" readonly></div>
        <div class="form-group col-md-6"><label class="form-label">Mã khách hàng</label><input type="number" class="form-control hm-readonly-input" value="1" readonly></div>
        <div class="form-group col-md-6"><label class="form-label">Tên khách hàng</label><input type="text" class="form-control hm-readonly-input" value="Nguyễn Minh An" readonly></div>
        <div class="form-group col-md-6"><label class="form-label">Số điện thoại</label><input type="text" class="form-control hm-readonly-input" value="0901234567" readonly></div>
        <div class="form-group col-md-4"><label class="form-label">Ngày đặt</label><input type="date" class="form-control hm-readonly-input" value="2026-04-05" disabled></div>
        <div class="form-group col-md-4"><label class="form-label">Ngày nhận phòng</label><input type="date" class="form-control hm-readonly-input" value="2026-04-08" disabled></div>
        <div class="form-group col-md-4"><label class="form-label">Ngày trả phòng</label><input type="date" class="form-control hm-readonly-input" value="2026-04-10" disabled></div>
        <div class="form-group col-md-6"><label class="form-label">Số lượng người ở</label><input type="number" class="form-control hm-readonly-input" value="2" readonly></div>
        <div class="form-group col-md-6"><label class="form-label">Tình trạng</label><select class="form-select"><option>Chờ xác nhận</option><option>Đã xác nhận</option><option>Đã hủy</option><option>Đang ở</option><option>Đã trả phòng</option><option>Đã hủy</option></select></div>
    </x-receptionist.form-page>
@endif
