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
            .rf-stay-search {
                min-height: 46px;
            }
            .rf-date-display-field {
                position: relative;
            }
            .rf-date-display-field .form-control {
                color: transparent;
                caret-color: transparent;
            }
            .rf-date-display-field .form-control:focus {
                color: transparent;
            }
            .rf-date-display-field .form-control::-webkit-datetime-edit {
                color: transparent;
            }
            .rf-date-display-value {
                position: absolute;
                left: 0.95rem;
                top: 50%;
                transform: translateY(-50%);
                pointer-events: none;
                color: #8a97aa;
                font: inherit;
            }
            .rf-customer-search {
                position: relative;
            }
            .rf-customer-results {
                display: none;
                margin-top: 0.45rem;
                border: 1px solid #e2cfc6;
                border-radius: 14px;
                background: #fff;
                box-shadow: 0 16px 34px rgba(73, 18, 15, 0.1);
                overflow: hidden;
            }
            .rf-customer-results.is-open {
                display: block;
            }
            .rf-customer-result {
                width: 100%;
                display: grid;
                gap: 0.15rem;
                padding: 0.75rem 0.95rem;
                border: 0;
                border-bottom: 1px solid rgba(166, 98, 43, 0.12);
                background: #fff;
                color: #49120f;
                text-align: left;
            }
            .rf-customer-result:last-child {
                border-bottom: 0;
            }
            .rf-customer-result:hover,
            .rf-customer-result.is-selected {
                background: #fff7ef;
            }
            .rf-customer-result-name {
                font-weight: 700;
            }
            .rf-customer-result-meta {
                color: #8b5e3c;
                font-size: 0.9rem;
            }
            .rf-guest-picker {
                position: relative;
            }
            .rf-guest-toggle {
                width: 100%;
                min-height: 46px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.75rem;
                padding: 0.65rem 0.85rem;
                color: #49120f;
                background: #fff;
                border: 1px solid #e2cfc6;
                border-radius: 0.45rem;
                text-align: left;
            }
            .rf-guest-toggle > span {
                min-width: 0;
            }
            .rf-guest-toggle svg {
                flex: 0 0 auto;
            }
            .rf-guest-label {
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .rf-guest-panel {
                position: absolute;
                z-index: 20;
                top: calc(100% + 0.5rem);
                left: 0;
                width: min(100%, 430px);
                min-width: min(100vw - 2rem, 420px);
                display: none;
                padding: 1rem;
                background: #fff;
                border: 1px solid rgba(166, 98, 43, 0.18);
                border-radius: 18px;
                box-shadow: 0 20px 45px rgba(73, 18, 15, 0.12);
            }
            .rf-guest-picker.is-open .rf-guest-panel {
                display: grid;
                gap: 0.8rem;
            }
            .rf-guest-row {
                display: grid;
                grid-template-columns: minmax(90px, 1fr) minmax(160px, 220px);
                gap: 1rem;
                align-items: center;
            }
            .rf-guest-row-label {
                color: #1f2937;
                font-weight: 600;
            }
            .rf-stepper {
                display: grid;
                grid-template-columns: 48px 1fr 48px;
                align-items: center;
                min-height: 46px;
                border: 1px solid #dedede;
                border-radius: 16px;
                overflow: hidden;
                background: #fff;
            }
            .rf-stepper button {
                width: 38px;
                height: 38px;
                margin: 4px;
                border: 0;
                border-radius: 12px;
                color: #9a4c34;
                background: #f6efec;
                font-size: 1.4rem;
                line-height: 1;
            }
            .rf-stepper button:disabled {
                color: #d4bdb4;
                background: #faf7f6;
            }
            .rf-stepper-value {
                color: #111827;
                font-weight: 700;
                text-align: center;
                font-size: 1.15rem;
            }
            @media (max-width: 575.98px) {
                .rf-guest-panel {
                    min-width: 100%;
                }
                .rf-guest-row {
                    grid-template-columns: 1fr;
                    gap: 0.45rem;
                }
            }
            .rf-room-results-frame {
                height: 520px;
                overflow-y: auto;
                padding-right: 0.35rem;
            }
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
            .rf-room-booking-row {
                display: grid;
                grid-template-columns: minmax(0, 1fr) minmax(150px, 190px);
                gap: 1rem;
                align-items: center;
                padding: 1rem;
                border: 1px solid rgba(22, 101, 52, 0.12);
                border-radius: 18px;
                background: linear-gradient(180deg, #f9fffb 0%, #fff 100%);
            }
            .rf-room-price-stack {
                display: flex;
                flex-wrap: wrap;
                gap: 0.55rem 1rem;
                align-items: baseline;
                min-width: 0;
            }
            .rf-room-price-original {
                color: #9a6a50;
                text-decoration: line-through;
                font-weight: 600;
            }
            .rf-room-price-sale {
                color: #6f1d01;
                font-size: 1.05rem;
                font-weight: 800;
            }
            .rf-room-qty {
                display: grid;
                grid-template-columns: 40px 1fr 40px;
                align-items: center;
                min-height: 44px;
                border: 1px solid #dedede;
                border-radius: 14px;
                background: #fff;
                overflow: hidden;
            }
            .rf-room-qty button {
                width: 34px;
                height: 34px;
                margin: 4px;
                border: 0;
                border-radius: 10px;
                color: #9a4c34;
                background: #f6efec;
                font-size: 1.25rem;
                line-height: 1;
            }
            .rf-room-qty button:disabled {
                color: #d4bdb4;
                background: #faf7f6;
            }
            .rf-room-qty-value {
                color: #111827;
                font-weight: 700;
                text-align: center;
                white-space: nowrap;
            }
            @media (max-width: 575.98px) {
                .rf-room-booking-row {
                    grid-template-columns: 1fr;
                }
            }
            .rf-room-search-empty {
                padding: 1rem 1.15rem;
                color: #8b5e3c;
                border: 1px dashed rgba(166, 98, 43, 0.18);
                border-radius: 18px;
                background: #fffaf7;
            }
            .rf-room-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                flex-wrap: wrap;
                margin-bottom: 1rem;
            }
            .rf-room-sort {
                width: min(100%, 260px);
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
            #summarySelectedRooms {
                max-height: 360px;
                overflow-y: auto;
                padding-right: 0.35rem;
            }
            .rf-summary-room-entry {
                padding: 0.75rem 0;
                border-top: 1px solid rgba(166, 98, 43, 0.12);
            }
            .rf-summary-room-entry:first-child {
                border-top: 0;
                padding-top: 0;
            }
            .rf-summary-room-entry p {
                margin: 0.25rem 0 0;
                color: #7c5b45;
                font-size: 0.94rem;
            }
            .rf-summary-room-entry strong {
                color: #49120f;
            }
            .rf-summary-room-price {
                color: #6f1d01;
                font-weight: 700;
            }
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
                                <div class="col-12">
                                    <label class="form-label">Tự động điền khách hàng cũ</label>
                                    <div class="rf-customer-search">
                                        <div class="input-group">
                                            <input id="customerSearchInput" type="search" class="form-control" placeholder="Nhập mã, tên, số điện thoại hoặc CCCD của khách hàng">
                                            <button id="searchCustomerButton" type="button" class="btn btn-primary">Áp dụng</button>
                                        </div>
                                        <div id="customerSearchResults" class="rf-customer-results"></div>
                                    </div>
                                    <div id="customerSearchMessage" class="form-text text-muted"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Khách hàng</label>
                                    <input id="bookingCustomer" type="text" class="form-control" required>
                                    <small id="bookingCustomerError" class="text-danger"></small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Số điện thoại</label>
                                    <input id="bookingPhone" type="text" class="form-control" inputmode="numeric" maxlength="10" required>
                                    <small id="bookingPhoneError" class="text-danger"></small>
                                </div>
                            </div>
                        </div>

                        <div class="rf-card mb-4">
                            <h5 class="mb-3">Lịch lưu trú</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Ngày nhận phòng</label>
                                    <div class="rf-date-display-field">
                                        <input id="checkinDate" type="date" class="form-control" value="{{ now()->toDateString() }}" min="{{ now()->toDateString() }}" lang="en-GB">
                                        <span class="rf-date-display-value" data-date-display="checkinDate">{{ now()->format('d/m/Y') }}</span>
                                    </div>
                                    <small id="checkinDateError" class="text-danger"></small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ngày trả phòng</label>
                                    <div class="rf-date-display-field">
                                        <input id="checkoutDate" type="date" class="form-control" value="{{ now()->addDay()->toDateString() }}" min="{{ now()->addDay()->toDateString() }}" lang="en-GB">
                                        <span class="rf-date-display-value" data-date-display="checkoutDate">{{ now()->addDay()->format('d/m/Y') }}</span>
                                    </div>
                                    <small id="checkoutDateError" class="text-danger"></small>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Số khách</label>
                                    <div class="rf-guest-picker" id="guestPicker">
                                        <button id="guestPickerToggle" type="button" class="rf-guest-toggle" aria-expanded="false" aria-controls="guestPickerPanel">
                                            <span class="d-inline-flex align-items-center gap-2 min-w-0">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M7 3V6M17 3V6M4 9H20M6 5H18C19.1046 5 20 5.89543 20 7V19C20 20.1046 19.1046 21 18 21H6C4.89543 21 4 20.1046 4 19V7C4 5.89543 4.89543 5 6 5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                                                </svg>
                                                <span id="guestPickerLabel" class="rf-guest-label">2 người lớn - 0 trẻ em - 1 phòng</span>
                                            </span>
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </button>
                                        <div id="guestPickerPanel" class="rf-guest-panel">
                                            <div class="rf-guest-row">
                                                <div class="rf-guest-row-label">Người lớn</div>
                                                <div class="rf-stepper">
                                                    <button type="button" data-stepper-button data-target="adultCount" data-delta="-1">-</button>
                                                    <span id="adultCountValue" class="rf-stepper-value">2</span>
                                                    <button type="button" data-stepper-button data-target="adultCount" data-delta="1">+</button>
                                                </div>
                                            </div>
                                            <div class="rf-guest-row">
                                                <div class="rf-guest-row-label">Trẻ em</div>
                                                <div class="rf-stepper">
                                                    <button type="button" data-stepper-button data-target="childCount" data-delta="-1">-</button>
                                                    <span id="childCountValue" class="rf-stepper-value">0</span>
                                                    <button type="button" data-stepper-button data-target="childCount" data-delta="1">+</button>
                                                </div>
                                            </div>
                                            <div class="rf-guest-row">
                                                <div class="rf-guest-row-label">Phòng</div>
                                                <div class="rf-stepper">
                                                    <button type="button" data-stepper-button data-target="requestedRoomCount" data-delta="-1">-</button>
                                                    <span id="requestedRoomCountValue" class="rf-stepper-value">1</span>
                                                    <button type="button" data-stepper-button data-target="requestedRoomCount" data-delta="1">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button id="searchRoomsButton" type="button" class="btn btn-primary w-100 rf-stay-search">
                                        <span class="d-inline-flex align-items-center justify-content-center gap-2">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"></circle>
                                                <path d="M20 20L16.5 16.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                            </svg>
                                            Tìm phòng
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="rf-card" id="roomSearchResults">
                            <div class="rf-room-toolbar">
                                <h5 class="mb-0">Chọn phòng</h5>
                                <select id="roomPriceSort" class="form-select rf-room-sort" aria-label="Sắp xếp giá phòng">
                                    <option value="">Sắp xếp giá</option>
                                    <option value="price-asc">Giá tăng dần</option>
                                    <option value="price-desc">Giá giảm dần</option>
                                </select>
                            </div>
                            <div class="rf-room-results-frame">
                                <div class="rf-room-type-list" id="availableRoomTypeList">
                                    <div class="rf-room-search-empty">Chọn ngày lưu trú và bấm Tìm phòng để xem loại phòng còn trống.</div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-xl-4">
                    <div class="rf-card">
                        <h5 class="mb-3">Tóm tắt đặt phòng</h5>

                        <div class="border rounded p-3 rf-summary-block">
                            <div class="small text-muted text-uppercase fw-bold">Khách hàng</div>
                            <div id="summaryCustomerName" class="fw-semibold mt-2">--</div>
                            <div id="summaryCustomerPhone" class="rf-summary-line">SĐT: --</div>
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
                            <div class="small text-muted text-uppercase fw-bold">Tổng cộng</div>
                            <div id="summaryCapacity" class="fw-semibold mt-2">0 khách</div>
                        </div>

                        <button id="saveBookingButton" type="button" class="btn btn-primary w-100">Lưu đặt phòng</button>
                    </div>
                </div>
            </div>
        </div>

        <dialog id="bookingConfirmDialog" class="rf-dialog">
            <div class="rf-dialog-body">
                <h3 class="rf-dialog-title">Xác nhận đặt phòng</h3>
                <p class="rf-dialog-text">Bạn có chắc muốn lưu đặt phòng này không?</p>
                <div id="bookingConfirmMessage" class="rf-dialog-text text-danger" hidden></div>
                <div class="rf-dialog-actions">
                    <button id="confirmBookingButton" type="button" class="btn btn-primary">Xác nhận</button>
                    <button id="cancelBookingButton" type="button" class="btn btn-light">Hủy</button>
                </div>
            </div>
        </dialog>

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
            const customerSearchInput = document.getElementById('customerSearchInput');
            const searchCustomerButton = document.getElementById('searchCustomerButton');
            const customerSearchResults = document.getElementById('customerSearchResults');
            const customerSearchMessage = document.getElementById('customerSearchMessage');
            const bookingPhone = document.getElementById('bookingPhone');
            const bookingCustomerError = document.getElementById('bookingCustomerError');
            const bookingPhoneError = document.getElementById('bookingPhoneError');
            const checkinDate = document.getElementById('checkinDate');
            const checkoutDate = document.getElementById('checkoutDate');
            const checkinDateDisplay = document.querySelector('[data-date-display="checkinDate"]');
            const checkoutDateDisplay = document.querySelector('[data-date-display="checkoutDate"]');
            const checkinDateError = document.getElementById('checkinDateError');
            const checkoutDateError = document.getElementById('checkoutDateError');
            const guestPicker = document.getElementById('guestPicker');
            const guestPickerToggle = document.getElementById('guestPickerToggle');
            const guestPickerLabel = document.getElementById('guestPickerLabel');
            const stepperButtons = document.querySelectorAll('[data-stepper-button]');
            const stepperValues = {
                adultCount: document.getElementById('adultCountValue'),
                childCount: document.getElementById('childCountValue'),
                requestedRoomCount: document.getElementById('requestedRoomCountValue')
            };
            const searchRoomsButton = document.getElementById('searchRoomsButton');
            const roomSearchResults = document.getElementById('roomSearchResults');
            const availableRoomTypeList = document.getElementById('availableRoomTypeList');
            const summaryCustomerName = document.getElementById('summaryCustomerName');
            const summaryCustomerPhone = document.getElementById('summaryCustomerPhone');
            const summaryBookingPeriod = document.getElementById('summaryBookingPeriod');
            const summarySelectedRooms = document.getElementById('summarySelectedRooms');
            const summaryCapacity = document.getElementById('summaryCapacity');
            const saveBookingButton = document.getElementById('saveBookingButton');
            const bookingConfirmDialog = document.getElementById('bookingConfirmDialog');
            const confirmBookingButton = document.getElementById('confirmBookingButton');
            const cancelBookingButton = document.getElementById('cancelBookingButton');
            const bookingConfirmMessage = document.getElementById('bookingConfirmMessage');
            const bookingSuccessDialog = document.getElementById('bookingSuccessDialog');
            const goToCheckinButton = document.getElementById('goToCheckinButton');
            const createAnotherBookingButton = document.getElementById('createAnotherBookingButton');
            const guestState = {
                adultCount: 2,
                childCount: 0,
                requestedRoomCount: 1
            };
            const guestLimits = {
                adultCount: { min: 1, max: 20 },
                childCount: { min: 0, max: 20 },
                requestedRoomCount: { min: 1, max: 10 }
            };
            const roomSearchUrl = @json(url('/api/phong/tim-kiem'));
            const customerSearchUrl = @json(url('/api/khach-hang'));
            const bookingStoreUrl = @json(url('/api/dat-phong'));
            const roomPriceSort = document.getElementById('roomPriceSort');
            let currentAvailableRoomTypes = [];
            let customerCache = [];
            let selectedCustomerForApply = null;
            let appliedCustomerId = null;
            let isComposingCustomerName = false;

            function formatDate(dateValue) {
                if (!dateValue) {
                    return '--/--/----';
                }

                const dateParts = dateValue.split('-');
                return dateParts.length === 3
                    ? `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`
                    : dateValue;
            }

            function toDateInputValue(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');

                return `${year}-${month}-${day}`;
            }

            function addDays(dateValue, days) {
                const dateParts = dateValue.split('-').map(Number);
                const date = dateParts.length === 3
                    ? new Date(dateParts[0], dateParts[1] - 1, dateParts[2])
                    : new Date();

                date.setDate(date.getDate() + days);

                return toDateInputValue(date);
            }

            function syncCustomerFields() {
                syncSummary();
            }

            function validateName(value) {
                return /^[\p{L}\s]+$/u.test(value.trim());
            }

            function validatePhone(value) {
                return /^0\d{9}$/.test(value.trim());
            }

            function setFieldError(field, errorElement, message) {
                field.classList.toggle('is-invalid', Boolean(message));
                errorElement.textContent = message;
            }

            function validateCustomerNameField() {
                const value = bookingCustomer.value.trim();
                let message = '';

                if (!value) {
                    message = 'Vui lòng nhập tên khách hàng';
                } else if (!validateName(value)) {
                    message = 'Tên khách hàng chỉ chứa chữ cái';
                }

                setFieldError(bookingCustomer, bookingCustomerError, message);
                return !message;
            }

            function validatePhoneField() {
                const value = bookingPhone.value.trim();
                let message = '';

                if (!value) {
                    message = 'Vui lòng nhập số điện thoại';
                } else if (!validatePhone(value)) {
                    message = 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0';
                }

                setFieldError(bookingPhone, bookingPhoneError, message);
                return !message;
            }

            function validateCustomerFields(shouldFocus = false) {
                const validations = [
                    { field: bookingCustomer, isValid: validateCustomerNameField() },
                    { field: bookingPhone, isValid: validatePhoneField() }
                ];
                const firstInvalid = validations.find((validation) => !validation.isValid);

                if (firstInvalid && shouldFocus) {
                    firstInvalid.field.focus();
                }

                return !firstInvalid;
            }

            function fillCustomerFields(customer) {
                bookingCustomer.value = customer.TenKH || '';
                bookingPhone.value = customer.SoDienThoai || '';
                appliedCustomerId = customer.MaKH ? Number(customer.MaKH) : null;
                validateCustomerFields();
                syncSummary();
            }

            function getCustomerSearchLabel(customer) {
                const id = customer.MaKH ? `${customer.MaKH} - ` : '';
                return `${id}${customer.TenKH || 'Khách hàng'}`;
            }

            function findCustomersByKeyword(customers, keyword) {
                const normalizedKeyword = keyword.trim().toLowerCase();

                if (!normalizedKeyword) {
                    return [];
                }

                return customers.filter((customer) => {
                    const searchableValues = [
                        customer.MaKH,
                        customer.TenKH,
                        customer.SoDienThoai,
                        customer.CCCD
                    ];

                    return searchableValues.some((value) => String(value ?? '').toLowerCase().includes(normalizedKeyword));
                }).sort((left, right) => {
                    const leftId = String(left.MaKH ?? '').toLowerCase();
                    const rightId = String(right.MaKH ?? '').toLowerCase();
                    const leftName = String(left.TenKH ?? '').toLowerCase();
                    const rightName = String(right.TenKH ?? '').toLowerCase();

                    const getPriority = (id, name) => {
                        if (id === normalizedKeyword) {
                            return 0;
                        }

                        if (id.startsWith(normalizedKeyword)) {
                            return 1;
                        }

                        if (name.includes(normalizedKeyword)) {
                            return 2;
                        }

                        return 3;
                    };

                    return getPriority(leftId, leftName) - getPriority(rightId, rightName);
                }).slice(0, 8);
            }

            function renderCustomerSearchResults(customers) {
                if (!customerSearchResults) {
                    return;
                }

                if (!Array.isArray(customers) || customers.length === 0) {
                    customerSearchResults.innerHTML = '';
                    customerSearchResults.classList.remove('is-open');
                    return;
                }

                customerSearchResults.innerHTML = customers.map((customer) => {
                    const id = customer.MaKH || '';
                    const isSelected = selectedCustomerForApply
                        && String(selectedCustomerForApply.MaKH) === String(id);

                    return `
                        <button type="button" class="rf-customer-result${isSelected ? ' is-selected' : ''}" data-customer-id="${escapeHtml(id)}">
                            <span class="rf-customer-result-name">${escapeHtml(getCustomerSearchLabel(customer))}</span>
                            <span class="rf-customer-result-meta">SĐT: ${escapeHtml(customer.SoDienThoai || '--')} · CCCD: ${escapeHtml(customer.CCCD || '--')}</span>
                        </button>
                    `;
                }).join('');
                customerSearchResults.classList.add('is-open');
            }

            async function loadCustomerCache() {
                if (customerCache.length > 0) {
                    return customerCache;
                }

                const response = await fetch(customerSearchUrl, {
                    cache: 'no-store',
                    headers: { Accept: 'application/json' }
                });
                const payload = await response.json();

                if (!response.ok || !Array.isArray(payload)) {
                    throw new Error('Không thể tải danh sách khách hàng.');
                }

                customerCache = payload;
                return customerCache;
            }

            async function showCustomerSearchResults() {
                const keyword = customerSearchInput.value.trim();
                selectedCustomerForApply = null;

                if (!keyword) {
                    renderCustomerSearchResults([]);
                    customerSearchMessage.textContent = '';
                    return;
                }

                customerSearchMessage.textContent = 'Đang tìm khách hàng...';
                customerSearchMessage.classList.remove('text-danger', 'text-success');
                customerSearchMessage.classList.add('text-muted');

                try {
                    const customers = findCustomersByKeyword(await loadCustomerCache(), keyword);
                    renderCustomerSearchResults(customers);

                    if (customers.length === 0) {
                        customerSearchMessage.textContent = 'Không tìm thấy khách hàng phù hợp.';
                        customerSearchMessage.classList.remove('text-muted', 'text-success');
                        customerSearchMessage.classList.add('text-danger');
                        return;
                    }

                    customerSearchMessage.textContent = 'Chọn một khách hàng trong danh sách rồi nhấn Áp dụng.';
                    customerSearchMessage.classList.remove('text-danger', 'text-success');
                    customerSearchMessage.classList.add('text-muted');
                } catch (error) {
                    renderCustomerSearchResults([]);
                    customerSearchMessage.textContent = error.message || 'Không thể tìm khách hàng.';
                    customerSearchMessage.classList.remove('text-muted', 'text-success');
                    customerSearchMessage.classList.add('text-danger');
                }
            }

            function selectCustomerSearchResult(customer) {
                selectedCustomerForApply = customer;
                customerSearchInput.value = getCustomerSearchLabel(customer);
                renderCustomerSearchResults([]);
                customerSearchMessage.textContent = 'Đã chọn khách hàng. Nhấn Áp dụng để điền thông tin.';
                customerSearchMessage.classList.remove('text-muted', 'text-danger');
                customerSearchMessage.classList.add('text-success');
            }

            function applySelectedCustomer() {
                if (!selectedCustomerForApply) {
                    customerSearchInput.focus();
                    customerSearchMessage.textContent = 'Vui lòng chọn một khách hàng trong danh sách kết quả.';
                    customerSearchMessage.classList.remove('text-muted', 'text-success');
                    customerSearchMessage.classList.add('text-danger');
                    return;
                }

                fillCustomerFields(selectedCustomerForApply);
                customerSearchMessage.textContent = `Đã áp dụng khách hàng mã ${selectedCustomerForApply.MaKH || '--'}.`;
                customerSearchMessage.classList.remove('text-muted', 'text-danger');
                customerSearchMessage.classList.add('text-success');
            }

            function syncDateDisplays() {
                if (checkinDateDisplay) {
                    checkinDateDisplay.textContent = formatDate(checkinDate.value);
                }

                if (checkoutDateDisplay) {
                    checkoutDateDisplay.textContent = formatDate(checkoutDate.value);
                }
            }

            function syncStayDateLimits() {
                const today = toDateInputValue(new Date());

                checkinDate.min = today;

                if (!checkinDate.value || checkinDate.value < today) {
                    checkinDate.value = today;
                }

                const minCheckoutDate = addDays(checkinDate.value, 1);
                checkoutDate.min = minCheckoutDate;

                if (!checkoutDate.value || checkoutDate.value < minCheckoutDate) {
                    checkoutDate.value = minCheckoutDate;
                }

                syncDateDisplays();
            }

            function validateStayDates(shouldFocus = false) {
                const today = toDateInputValue(new Date());
                const minCheckoutDate = checkinDate.value ? addDays(checkinDate.value, 1) : addDays(today, 1);
                const isCheckinInvalid = !checkinDate.value || checkinDate.value < today;
                const isCheckoutInvalid = !checkoutDate.value || checkoutDate.value < minCheckoutDate;
                const checkinMessage = isCheckinInvalid ? 'Ngày nhận phòng không được ở quá khứ' : '';
                const checkoutMessage = isCheckoutInvalid ? 'Ngày trả phòng phải sau ngày nhận phòng ít nhất 1 ngày' : '';

                checkinDate.classList.toggle('is-invalid', isCheckinInvalid);
                checkoutDate.classList.toggle('is-invalid', isCheckoutInvalid);
                checkinDateError.textContent = checkinMessage;
                checkoutDateError.textContent = checkoutMessage;

                if (shouldFocus) {
                    if (isCheckinInvalid) {
                        checkinDate.focus();
                    } else if (isCheckoutInvalid) {
                        checkoutDate.focus();
                    }
                }

                return !isCheckinInvalid && !isCheckoutInvalid;
            }

            function syncSummary() {
                const selectedRoomInputs = document.querySelectorAll('[data-room-quantity]');
                const guestTotal = guestState.adultCount + guestState.childCount;
                const nights = getStayNightCount();
                let capacityTotal = 0;
                let selectedRoomTotal = 0;
                let roomDisplayIndex = 0;
                let roomTotalAmount = 0;
                let maxAdultTotal = 0;
                let maxChildTotal = 0;

                syncStayDateLimits();
                validateStayDates();
                summaryCustomerName.textContent = bookingCustomer.value.trim() || '--';
                summaryCustomerPhone.textContent = `SĐT: ${bookingPhone.value || '--'}`;
                summaryBookingPeriod.textContent = `${formatDate(checkinDate.value)} đến ${formatDate(checkoutDate.value)}`;
                guestPickerLabel.textContent = `${guestState.adultCount} người lớn - ${guestState.childCount} trẻ em - ${guestState.requestedRoomCount} phòng`;

                const selectedRooms = Array.from(selectedRoomInputs).filter((input) => Number(input.value || 0) > 0);

                if (selectedRooms.length === 0) {
                    summarySelectedRooms.innerHTML = '<div class="rf-summary-room-empty">Chưa chọn phòng</div>';
                    summaryCapacity.innerHTML = `
                        <div>Tổng số phòng: <strong>0 phòng</strong></div>
                        <div>Số khách tối đa: <strong>0 khách</strong></div>
                        <div>Tổng tiền phòng: <strong>${formatMoney(0)}</strong></div>
                    `;
                    saveBookingButton.disabled = false;
                    return;
                }

                summarySelectedRooms.innerHTML = '';

                selectedRooms.forEach((roomInput) => {
                    const quantity = Number(roomInput.value || 0);
                    const adults = Number(roomInput.dataset.adultsPerRoom || 0);
                    const children = Number(roomInput.dataset.childrenPerRoom || 0);
                    const guestText = [
                        adults > 0 ? `${adults} người lớn` : '',
                        children > 0 ? `${children} trẻ em` : ''
                    ].filter(Boolean).join(', ') || 'Theo tiêu chí đã chọn';
                    const price = Number(roomInput.dataset.pricePerNight || 0);

                    selectedRoomTotal += quantity;
                    roomTotalAmount += price * nights * quantity;
                    capacityTotal += Number(roomInput.dataset.capacityPerRoom || 0) * quantity;
                    maxAdultTotal += adults * quantity;
                    maxChildTotal += children * quantity;

                    Array.from({ length: quantity }).forEach(() => {
                        roomDisplayIndex += 1;
                        summarySelectedRooms.insertAdjacentHTML(
                            'beforeend',
                            `<div class="rf-summary-room-entry">
                                <p><strong>Phòng ${roomDisplayIndex}:</strong> ${escapeHtml(roomInput.dataset.type)}</p>
                                <p>Số người: ${escapeHtml(guestText)}</p>
                                <p>Đơn giá: <span class="rf-summary-room-price">${price > 0 ? formatMoney(price) : 'Liên hệ'}</span> / đêm x ${nights} đêm</p>
                            </div>`
                        );
                    });
                });

                summaryCapacity.innerHTML = `
                    <div>Tổng số phòng: <strong>${selectedRoomTotal} phòng</strong></div>
                    <div>Số khách tối đa: <strong>${maxAdultTotal} người lớn, ${maxChildTotal} trẻ em</strong></div>
                    <div>Tổng tiền phòng: <strong>${formatMoney(roomTotalAmount)}</strong></div>
                `;
                saveBookingButton.disabled = guestTotal < 1
                    || guestTotal > capacityTotal
                    || selectedRoomTotal !== guestState.requestedRoomCount;
            }

            function clampGuestValue(name, value) {
                const limit = guestLimits[name];
                return Math.min(limit.max, Math.max(limit.min, value));
            }

            function syncGuestPicker() {
                Object.keys(stepperValues).forEach((name) => {
                    if (stepperValues[name]) {
                        stepperValues[name].textContent = guestState[name];
                    }
                });

                stepperButtons.forEach((button) => {
                    const target = button.dataset.target;
                    const delta = Number(button.dataset.delta || 0);
                    const limit = guestLimits[target];

                    if (!limit) {
                        return;
                    }

                    button.disabled = delta < 0
                        ? guestState[target] <= limit.min
                        : guestState[target] >= limit.max;
                });

                syncSummary();
            }

            function escapeHtml(value) {
                return String(value ?? '').replace(/[&<>"']/g, (char) => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                }[char]));
            }

            function formatMoney(value) {
                return `${Math.round(Number(value || 0)).toLocaleString('vi-VN')} VNĐ`;
            }

            function getStayNightCount() {
                const checkin = new Date(`${checkinDate.value}T00:00:00`);
                const checkout = new Date(`${checkoutDate.value}T00:00:00`);
                const diffMs = checkout.getTime() - checkin.getTime();
                const diffDays = Math.round(diffMs / 86400000);

                return Math.max(diffDays, 1);
            }

            function renderAvailableRoomTypes(roomTypes) {
                if (!availableRoomTypeList) {
                    return;
                }

                if (!Array.isArray(roomTypes) || roomTypes.length === 0) {
                    availableRoomTypeList.innerHTML = '<div class="rf-room-search-empty">Không có loại phòng nào còn trống theo tiêu chí đã chọn.</div>';
                    syncSummary();
                    return;
                }

                const sortedRoomTypes = roomTypes.slice().sort((left, right) => {
                    const sortValue = roomPriceSort ? roomPriceSort.value : '';
                    const leftPrice = Number(left.GiaGiam || left.GiaPhong || 0);
                    const rightPrice = Number(right.GiaGiam || right.GiaPhong || 0);

                    if (sortValue === 'price-asc') {
                        return leftPrice - rightPrice;
                    }

                    if (sortValue === 'price-desc') {
                        return rightPrice - leftPrice;
                    }

                    return 0;
                });

                availableRoomTypeList.innerHTML = sortedRoomTypes.map((roomType) => {
                    const id = roomType.MaLoaiPhong || '';
                    const name = roomType.TenLoaiPhong || 'Loại phòng';
                    const adults = Number(roomType.NguoiLon || 0);
                    const children = Number(roomType.TreEm || 0);
                    const capacityPerRoom = Math.max(adults + children, 1);
                    const requestedRooms = guestState.requestedRoomCount;
                    const availableCount = Number(roomType.soPhongTrong || roomType.tongPhong || 0);
                    const originalPrice = Number(roomType.GiaPhong || 0);
                    const salePrice = Number(roomType.GiaGiam || originalPrice || 0);
                    const hasDiscount = originalPrice > 0 && salePrice > 0 && salePrice < originalPrice;

                    return `
                        <details class="rf-room-type-card" open>
                            <summary class="rf-room-type-summary">
                                <div class="rf-room-type-meta">
                                    <div class="rf-room-type-name">${escapeHtml(name)}</div>
                                    <div class="rf-room-type-note">Sức chứa ${adults} người lớn, ${children} trẻ em / phòng</div>
                                </div>
                                <span class="rf-room-count">Còn trống ${availableCount} phòng</span>
                            </summary>
                            <div class="rf-room-empty-list">
                                <div class="rf-room-booking-row">
                                    <div class="rf-room-price-stack">
                                        ${hasDiscount ? `<span class="rf-room-price-original">${formatMoney(originalPrice)}</span>` : ''}
                                        <span class="rf-room-price-sale">${salePrice > 0 ? formatMoney(salePrice) : 'Liên hệ'}</span>
                                    </div>
                                    <div class="rf-room-qty" data-room-qty-control>
                                        <button type="button" data-room-qty-action="decrement" aria-label="Giảm số phòng">-</button>
                                        <input
                                            type="hidden"
                                            value="0"
                                            min="0"
                                            max="${availableCount}"
                                            data-room-quantity
                                            data-room-type-id="${escapeHtml(id)}"
                                            data-type="${escapeHtml(name)}"
                                            data-capacity-per-room="${capacityPerRoom}"
                                            data-adults-per-room="${adults}"
                                            data-children-per-room="${children}"
                                            data-price-per-night="${salePrice}"
                                        >
                                        <span class="rf-room-qty-value" data-room-qty-value>0 phòng</span>
                                        <button type="button" data-room-qty-action="increment" aria-label="Tăng số phòng">+</button>
                                    </div>
                                </div>
                            </div>
                        </details>
                    `;
                }).join('');

                availableRoomTypeList.querySelectorAll('[data-room-quantity]').forEach((input) => {
                    syncRoomQuantity(input);
                });
                syncSummary();
            }

            async function searchAvailableRooms() {
                syncStayDateLimits();

                if (!validateStayDates(true) || checkoutDate.value <= checkinDate.value) {
                    checkoutDate.focus();
                    checkoutDate.classList.add('is-invalid');
                    return;
                }

                checkoutDate.classList.remove('is-invalid');

                if (availableRoomTypeList) {
                    availableRoomTypeList.innerHTML = '<div class="rf-room-search-empty">Đang tìm loại phòng còn trống...</div>';
                }

                const params = new URLSearchParams({
                    checkIn: checkinDate.value,
                    checkOut: checkoutDate.value,
                    NguoiLon: String(guestState.adultCount),
                    TreEm: String(guestState.childCount),
                    SoPhong: String(guestState.requestedRoomCount)
                });

                try {
                    const response = await fetch(`${roomSearchUrl}?${params.toString()}`, {
                        cache: 'no-store',
                        headers: { Accept: 'application/json' }
                    });
                    const payload = await response.json().catch(() => ({}));

                    if (!response.ok || payload.success === false || !Array.isArray(payload.data)) {
                        const validationMessage = Object.values(payload.errors || {}).flat().filter(Boolean).join('\n');
                        throw new Error(validationMessage || payload.message || 'Không thể tìm phòng trống.');
                    }

                    currentAvailableRoomTypes = payload.data;
                    renderAvailableRoomTypes(currentAvailableRoomTypes);
                } catch (error) {
                    if (availableRoomTypeList) {
                        availableRoomTypeList.innerHTML = `<div class="rf-room-search-empty">${escapeHtml(error.message || 'Không thể tìm phòng trống.')}</div>`;
                    }
                }

                if (roomSearchResults) {
                    roomSearchResults.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

            function syncRoomQuantity(input, nextValue = null) {
                const max = Number(input.max || 0);
                const value = Math.min(max, Math.max(0, Number(nextValue ?? input.value ?? 0)));
                const control = input.closest('[data-room-qty-control]');
                const valueLabel = control?.querySelector('[data-room-qty-value]');
                const decButton = control?.querySelector('[data-room-qty-action="decrement"]');
                const incButton = control?.querySelector('[data-room-qty-action="increment"]');

                input.value = String(value);

                if (valueLabel) {
                    valueLabel.textContent = `${value} phòng`;
                }

                if (decButton) {
                    decButton.disabled = value <= 0;
                }

                if (incButton) {
                    incButton.disabled = value >= max;
                }

                syncSummary();
            }

            function getSelectedRoomInputs() {
                return Array.from(document.querySelectorAll('[data-room-quantity]'))
                    .filter((input) => Number(input.value || 0) > 0);
            }

            function validateSelectedRooms(shouldFocus = false) {
                const selectedRooms = getSelectedRoomInputs();
                const selectedRoomTotal = selectedRooms.reduce((total, input) => total + Number(input.value || 0), 0);

                if (selectedRoomTotal === 0) {
                    summarySelectedRooms.innerHTML = '<div class="rf-room-search-empty">Vui lòng chọn ít nhất 1 phòng trước khi lưu đặt phòng.</div>';

                    if (shouldFocus && roomSearchResults) {
                        roomSearchResults.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }

                    return false;
                }

                if (selectedRoomTotal !== guestState.requestedRoomCount) {
                    summarySelectedRooms.insertAdjacentHTML(
                        'beforeend',
                        `<div class="rf-room-search-empty">Số phòng đã chọn phải bằng ${guestState.requestedRoomCount} phòng.</div>`
                    );

                    if (shouldFocus && roomSearchResults) {
                        roomSearchResults.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }

                    return false;
                }

                return true;
            }

            function buildBookingPayload() {
                const selectedRooms = getSelectedRoomInputs();

                return {
                    MaKH: appliedCustomerId,
                    TenKH: bookingCustomer.value.trim(),
                    SoDienThoai: bookingPhone.value.trim(),
                    NgayNhanPhong: checkinDate.value,
                    NgayTraPhong: checkoutDate.value,
                    TinhTrang: 1,
                    LoaiPhongs: selectedRooms.map((input) => ({
                        MaLoaiPhong: input.dataset.roomTypeId,
                        SoLuong: Number(input.value || 0)
                    }))
                };
            }

            async function submitBooking() {
                bookingConfirmMessage.hidden = true;
                bookingConfirmMessage.textContent = '';
                confirmBookingButton.disabled = true;
                cancelBookingButton.disabled = true;
                confirmBookingButton.textContent = 'Đang lưu...';

                try {
                    const response = await fetch(bookingStoreUrl, {
                        method: 'POST',
                        cache: 'no-store',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(buildBookingPayload())
                    });
                    const payload = await response.json().catch(() => ({}));

                    if (!response.ok || payload.success === false) {
                        const validationMessage = Object.values(payload.errors || {}).flat().filter(Boolean).join('\n');
                        throw new Error(validationMessage || payload.message || 'Không thể lưu đặt phòng.');
                    }

                    appliedCustomerId = null;
                    selectedCustomerForApply = null;
                    bookingConfirmDialog.close();
                    bookingSuccessDialog.showModal();
                } catch (error) {
                    bookingConfirmMessage.textContent = error.message || 'Không thể lưu đặt phòng.';
                    bookingConfirmMessage.hidden = false;
                } finally {
                    confirmBookingButton.disabled = false;
                    cancelBookingButton.disabled = false;
                    confirmBookingButton.textContent = 'Xác nhận';
                }
            }

            availableRoomTypeList.addEventListener('click', (event) => {
                const actionButton = event.target.closest('[data-room-qty-action]');

                if (!actionButton) {
                    return;
                }

                const control = actionButton.closest('[data-room-qty-control]');
                const input = control?.querySelector('[data-room-quantity]');

                if (!input) {
                    return;
                }

                const delta = actionButton.dataset.roomQtyAction === 'increment' ? 1 : -1;
                syncRoomQuantity(input, Number(input.value || 0) + delta);
            });

            bookingCustomer.addEventListener('input', () => {
                appliedCustomerId = null;

                if (!isComposingCustomerName) {
                    const sanitized = bookingCustomer.value.replace(/[^\p{L}\s]/gu, '');

                    if (sanitized !== bookingCustomer.value) {
                        bookingCustomer.value = sanitized;
                    }

                    validateCustomerFields();
                }

                syncCustomerFields();
            });
            bookingCustomer.addEventListener('compositionstart', () => {
                isComposingCustomerName = true;
            });
            bookingCustomer.addEventListener('compositionend', () => {
                appliedCustomerId = null;
                isComposingCustomerName = false;
                const sanitized = bookingCustomer.value.replace(/[^\p{L}\s]/gu, '');

                if (sanitized !== bookingCustomer.value) {
                    bookingCustomer.value = sanitized;
                }

                validateCustomerFields();
                syncCustomerFields();
            });
            searchCustomerButton.addEventListener('click', applySelectedCustomer);
            customerSearchResults.addEventListener('click', (event) => {
                const resultButton = event.target.closest('[data-customer-id]');

                if (!resultButton) {
                    return;
                }

                const customer = customerCache.find((item) => String(item.MaKH) === String(resultButton.dataset.customerId));

                if (customer) {
                    selectCustomerSearchResult(customer);
                }
            });
            customerSearchInput.addEventListener('input', showCustomerSearchResults);
            customerSearchInput.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();

                    if (selectedCustomerForApply) {
                        applySelectedCustomer();
                    } else {
                        showCustomerSearchResults();
                    }
                }
            });
            bookingPhone.addEventListener('input', () => {
                appliedCustomerId = null;
                const sanitized = bookingPhone.value.replace(/\D+/g, '').slice(0, 10);

                if (sanitized !== bookingPhone.value) {
                    bookingPhone.value = sanitized;
                }

                validateCustomerFields();
                syncSummary();
            });
            bookingCustomer.addEventListener('blur', () => validateCustomerFields());
            bookingPhone.addEventListener('blur', () => validateCustomerFields());
            checkinDate.addEventListener('input', () => {
                syncStayDateLimits();
                validateStayDates();
                syncSummary();
            });
            checkoutDate.addEventListener('input', () => {
                syncStayDateLimits();
                validateStayDates();
                syncSummary();
            });
            searchRoomsButton.addEventListener('click', searchAvailableRooms);
            roomPriceSort.addEventListener('change', () => {
                renderAvailableRoomTypes(currentAvailableRoomTypes);
            });
            guestPickerToggle.addEventListener('click', () => {
                const isOpen = guestPicker.classList.toggle('is-open');
                guestPickerToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
            stepperButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const target = button.dataset.target;
                    const delta = Number(button.dataset.delta || 0);

                    if (!guestLimits[target]) {
                        return;
                    }

                    guestState[target] = clampGuestValue(target, guestState[target] + delta);
                    syncGuestPicker();
                });
            });
            document.addEventListener('click', (event) => {
                if (!guestPicker || guestPicker.contains(event.target)) {
                    return;
                }

                guestPicker.classList.remove('is-open');
                guestPickerToggle.setAttribute('aria-expanded', 'false');
            });

            saveBookingButton.addEventListener('click', () => {
                if (!validateCustomerFields(true)) {
                    return;
                }

                syncStayDateLimits();

                if (!validateStayDates(true)) {
                    return;
                }

                syncSummary();

                if (!validateSelectedRooms(true)) {
                    return;
                }

                bookingConfirmMessage.hidden = true;
                bookingConfirmMessage.textContent = '';
                bookingConfirmDialog.showModal();
            });

            confirmBookingButton.addEventListener('click', submitBooking);
            cancelBookingButton.addEventListener('click', () => {
                bookingConfirmDialog.close();
            });

            goToCheckinButton.addEventListener('click', () => {
                window.location.href = "{{ route('reception.check-ins.create') }}";
            });

            createAnotherBookingButton.addEventListener('click', () => {
                window.location.href = "{{ route('reception.bookings.create') }}";
            });

            syncGuestPicker();
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
