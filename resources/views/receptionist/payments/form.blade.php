@php
    $paymentData = $paymentData ?? [];
    $authAccount = session('auth_account', []);
    $currentEmployeeId = $authAccount['MaNV'] ?? null;

    if (!$currentEmployeeId && !empty($authAccount['MaTK'])) {
        $currentEmployeeId = \App\Models\TaiKhoan::where('MaTK', $authAccount['MaTK'])->value('MaNV');
    }
@endphp

<x-app-layout :assets="['animation']">
    <style>
        .rp-shell { padding-top: 4.5rem; }
        .rp-hero, .rp-card {
            background: #fff;
            border: 1px solid rgba(166, 98, 43, 0.15);
            border-radius: 28px;
            box-shadow: 0 18px 40px rgba(120, 74, 44, 0.08);
        }
        .rp-hero {
            padding: 1.8rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(180deg, #fff7ef 0%, #fff 55%, #f6fbfb 100%);
        }
        .rp-card { padding: 1.4rem; height: 100%; }
        .rp-info-grid,
        .rp-detail-grid,
        .rp-method-grid {
            display: grid;
            gap: 0.9rem;
        }
        .rp-info-grid,
        .rp-detail-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .rp-info-item,
        .rp-detail-item {
            border: 1px solid rgba(166, 98, 43, 0.12);
            border-radius: 20px;
            padding: 1rem;
            background: #fff;
        }
        .rp-detail-item--full {
            grid-column: 1 / -1;
        }
        .rp-info-label,
        .rp-detail-label,
        .rp-total-label {
            color: #8b5e3c;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .rp-info-value,
        .rp-detail-value,
        .rp-total-value {
            margin-top: 0.45rem;
            color: #6f1d01;
            font-size: 1.05rem;
            font-weight: 600;
        }
        .rp-detail-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .rp-detail-header h5 {
            margin: 0;
        }
        .rp-service-table-wrap {
            margin-top: 0.9rem;
            overflow-x: auto;
        }
        .rp-room-table-wrap {
            margin-top: 1rem;
            overflow-x: auto;
        }
        .rp-room-table {
            width: 100%;
            min-width: 360px;
            border-collapse: collapse;
        }
        .rp-service-table {
            width: 100%;
            min-width: 560px;
            border-collapse: collapse;
        }
        .rp-compensation-table {
            width: 100%;
            min-width: 560px;
            border-collapse: collapse;
        }
        .rp-service-table th,
        .rp-service-table td,
        .rp-room-table th,
        .rp-room-table td,
        .rp-compensation-table th,
        .rp-compensation-table td {
            padding: 0.85rem 0.75rem;
            border-bottom: 1px solid rgba(166, 98, 43, 0.12);
            text-align: left;
            color: #6f1d01;
        }
        .rp-room-table th,
        .rp-service-table th {
            color: #8b5e3c;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .rp-compensation-table th {
            color: #8b5e3c;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .rp-service-table td:last-child,
        .rp-service-table th:last-child,
        .rp-room-table td:last-child,
        .rp-room-table th:last-child,
        .rp-compensation-table td:last-child,
        .rp-compensation-table th:last-child {
            text-align: right;
            white-space: nowrap;
        }
        .rp-table-total td {
            font-weight: 700;
            background: #fffaf3;
        }
        .rp-service-table .rp-service-room-row td {
            padding-top: 1rem;
            padding-bottom: 0.75rem;
            color: #8b3b12;
            font-size: 0.95rem;
            font-weight: 700;
            text-align: left;
            text-transform: uppercase;
            background: #fff7ef;
            white-space: normal;
        }
        .rp-service-room-row + tr td {
            border-top: 1px solid rgba(166, 98, 43, 0.12);
        }
        .rp-modal {
            border: 1px solid rgba(166, 98, 43, 0.15);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 28px 80px rgba(73, 18, 15, 0.22);
        }
        .rp-modal .modal-header {
            align-items: center;
            padding: 1.25rem 1.5rem;
            background: linear-gradient(180deg, #fff7ef 0%, #fff 100%);
            border-color: rgba(166, 98, 43, 0.12);
        }
        .rp-modal .modal-title {
            color: #6f1d01;
            font-weight: 700;
            font-size: 1.2rem;
        }
        .rp-modal .modal-body {
            padding: 1.35rem 1.5rem;
            background: #fff;
        }
        .rp-modal .modal-footer {
            padding: 1rem 1.5rem;
            background: #fffaf3;
            border-color: rgba(166, 98, 43, 0.12);
        }
        .rp-modal .form-label {
            color: #7c5b45;
            font-weight: 600;
            margin-bottom: 0.45rem;
        }
        .rp-modal .form-control {
            min-height: 46px;
            border-color: rgba(166, 98, 43, 0.22);
            border-radius: 8px;
            color: #6f1d01;
        }
        .rp-modal .form-control:focus {
            border-color: rgba(166, 98, 43, 0.55);
            box-shadow: 0 0 0 0.2rem rgba(166, 98, 43, 0.12);
        }
        .rp-modal textarea.form-control {
            min-height: 118px;
            resize: vertical;
        }
        .rp-modal .btn {
            min-width: 104px;
            border-radius: 8px;
            font-weight: 600;
        }
        .rp-compensation-note {
            margin: 0 0 1.1rem;
            color: #7c5b45;
        }
        .rp-compensation-form {
            border: 1px solid rgba(166, 98, 43, 0.12);
            border-radius: 16px;
            padding: 1rem;
            background: linear-gradient(180deg, #fff 0%, #fffaf6 100%);
        }
        .rp-service-empty {
            margin-top: 0.9rem;
            color: #7c5b45;
        }
        .rp-total-card {
            margin-top: 1rem;
            border-radius: 22px;
            padding: 1.1rem 1.2rem;
            background: linear-gradient(180deg, #fff7ef 0%, #fff 100%);
            border: 1px solid rgba(166, 98, 43, 0.16);
        }
        .rp-total-line {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
        }
        .rp-total-line + .rp-total-line {
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid rgba(166, 98, 43, 0.12);
        }
        .rp-method-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin-top: 1rem;
        }
        .rp-method-option {
            border: 1px solid rgba(166, 98, 43, 0.12);
            border-radius: 6px;
            padding: 1rem;
            background: #fff;
            cursor: pointer;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
        }
        .rp-method-option:hover {
            transform: translateY(-1px);
            border-color: rgba(166, 98, 43, 0.22);
            box-shadow: 0 14px 28px rgba(120, 74, 44, 0.08);
        }
        .rp-method-option.is-selected {
            border-color: rgba(166, 98, 43, 0.28);
            background: linear-gradient(180deg, #fffaf3 0%, #fff 100%);
            box-shadow: 0 18px 30px rgba(120, 74, 44, 0.1);
        }
        .rp-method-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        .rp-method-title {
            color: #6f1d01;
            font-weight: 700;
        }
        .rp-method-note {
            margin-top: 0.4rem;
            color: #7c5b45;
            font-size: 0.92rem;
        }
        .rp-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1.25rem;
        }
        .rp-actions .btn {
            flex: 1 1 220px;
        }
        @media (max-width: 991.98px) {
            .rp-method-grid {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 767.98px) {
            .rp-info-grid,
            .rp-detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="rp-shell">
        <div class="rp-hero">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="mb-2">Thanh toán trả phòng</h1>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('reception.check-outs.create') }}" class="btn btn-light">Quay lại trả phòng</a>
                    <a href="{{ route('reception.invoices.index') }}" class="btn btn-light">Danh sách hóa đơn</a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-5">
                <div class="rp-card">
                    <h5 class="mb-3">Thông tin hóa đơn</h5>
                    <div class="rp-info-grid">
                        <div class="rp-info-item">
                            <div class="rp-info-label">Mã hóa đơn</div>
                            <div id="paymentInvoiceId" class="rp-info-value">HD9002</div>
                        </div>
                        <div class="rp-info-item">
                            <div class="rp-info-label">Mã đặt phòng</div>
                            <div id="paymentBookingId" class="rp-info-value">9002</div>
                        </div>
                        <div class="rp-info-item">
                            <div class="rp-info-label">Khách hàng</div>
                            <div id="paymentCustomerName" class="rp-info-value">Trần Bảo Ngọc</div>
                        </div>
                        <div class="rp-info-item">
                            <div class="rp-info-label">Thời gian ở</div>
                            <div id="paymentStayPeriod" class="rp-info-value">03/05/2026 - 05/05/2026</div>
                        </div>
                        <div class="rp-info-item">
                            <div class="rp-info-label">Ngày lập hóa đơn</div>
                            <div id="paymentInvoiceDate" class="rp-info-value">05/05/2026</div>
                        </div>
                        <div class="rp-info-item">
                            <div class="rp-info-label">Ngày trả</div>
                            <div id="paymentCheckoutDate" class="rp-info-value">05/05/2026</div>
                        </div>
                        <div class="rp-info-item">
                            <div class="rp-info-label">Thời gian trả</div>
                            <div id="paymentCheckoutTime" class="rp-info-value">15:10</div>
                        </div>
                    </div>

                    <div class="rp-room-table-wrap">
                        <table class="rp-room-table">
                            <thead>
                                <tr>
                                    <th>Loại phòng</th>
                                    <th>Số phòng</th>
                                </tr>
                            </thead>
                            <tbody id="paymentRoomSummaryTableBody">
                                <tr>
                                    <td>Suite</td>
                                    <td>102, 104</td>
                                </tr>
                                <tr>
                                    <td>Suite Twin</td>
                                    <td>304</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-7">
                <div class="rp-card">
                    <div class="rp-detail-header">
                        <h5>Chi tiết hóa đơn</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#compensationModal">
                            Thêm đền bù
                        </button>
                    </div>

                    <div class="rp-detail-grid">
                        <div class="rp-detail-item rp-detail-item--full">
                            <div class="rp-detail-label">Phòng đã đặt</div>
                            <div class="rp-service-table-wrap">
                                <table class="rp-room-table">
                                    <thead>
                                        <tr>
                                            <th>Loại phòng</th>
                                            <th>Số lượng</th>
                                            <th>Đơn giá</th>
                                            <th>Số đêm</th>
                                            <th>Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody id="paymentRoomTableBody">
                                        <tr>
                                            <td>Suite</td>
                                            <td>1</td>
                                            <td>750.000 VNĐ</td>
                                            <td>2</td>
                                            <td>1.500.000 VNĐ</td>
                                        </tr>
                                        <tr>
                                            <td>Suite Twin</td>
                                            <td>1</td>
                                            <td>850.000 VNĐ</td>
                                            <td>2</td>
                                            <td>1.700.000 VNĐ</td>
                                        </tr>
                                        <tr class="rp-table-total">
                                            <td colspan="4">Tổng tiền</td>
                                            <td>3.200.000 VNĐ</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="rp-detail-item rp-detail-item--full">
                            <div class="rp-detail-label">Dịch vụ sử dụng</div>
                            <div id="paymentServiceEmpty" class="rp-service-empty" hidden>Không có phát sinh dịch vụ</div>
                            <div class="rp-service-table-wrap">
                                <table class="rp-service-table">
                                    <thead>
                                        <tr>
                                            <th>Tên dịch vụ</th>
                                            <th>Số lượng</th>
                                            <th>Đơn giá</th>
                                            <th>Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody id="paymentServiceTableBody">
                                        <tr>
                                            <td>Mini bar</td>
                                            <td>Dịch vụ ăn uống</td>
                                            <td>180.000 VNĐ</td>
                                        </tr>
                                        <tr>
                                            <td>Giặt ủi</td>
                                            <td>Dịch vụ phòng</td>
                                            <td>150.000 VNĐ</td>
                                        </tr>
                                        <tr>
                                            <td>Nước suối Evian</td>
                                            <td>Dịch vụ ăn uống</td>
                                            <td>120.000 VNĐ</td>
                                        </tr>
                                        <tr class="rp-table-total">
                                            <td colspan="2">Phí tổng dịch vụ</td>
                                            <td>450.000 VNĐ</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="rp-detail-item rp-detail-item--full">
                            <div class="rp-detail-label">Thông tin đền bù</div>
                            <div id="paymentCompensationEmpty" class="rp-service-empty" hidden>Không có đền bù</div>
                            <div class="rp-service-table-wrap">
                                <table class="rp-compensation-table">
                                    <thead>
                                        <tr>
                                            <th>Mô tả</th>
                                            <th>Phí đền bù</th>
                                        </tr>
                                    </thead>
                                    <tbody id="paymentCompensationTableBody">
                                        <tr>
                                            <td>Hư hỏng khăn tắm hoặc vật dụng trong phòng</td>
                                            <td>300.000 VNĐ</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="rp-total-card">
                        <div class="rp-total-line">
                            <div class="rp-total-label">Tổng thanh toán</div>
                            <div id="paymentGrandTotal" class="rp-total-value">1.550.000 VNĐ</div>
                        </div>
                        <div class="rp-total-line">
                            <div class="rp-total-label">Số tiền đã thanh toán</div>
                            <div id="paymentPaidAmount" class="rp-total-value">300.000 VNĐ</div>
                        </div>
                        <div class="rp-total-line">
                            <div class="rp-total-label">Số tiền còn phải trả</div>
                            <div id="paymentAmountDue" class="rp-total-value">1.250.000 VNĐ</div>
                        </div>
                    </div>

                    <h5 id="paymentMethodTitle" class="mt-4 mb-3">Chọn hình thức thanh toán</h5>
                    <div id="paymentMethodGrid" class="rp-method-grid">
                        <!-- <label class="rp-method-option is-selected">
                            <input type="radio" name="paymentMethod" value="cash" checked>
                            <div class="rp-method-title">Tiền mặt</div>
                            <div class="rp-method-note">Thanh toán trực tiếp tại quầy lễ tân</div>
                        </label> -->
                        <label class="rp-method-option is-selected">
                            <input type="radio" name="paymentMethod" value="bank_transfer" checked>
                            <div class="rp-method-title">Thanh toán với mã QR</div>
                        </label>
                        <label class="rp-method-option">
                            <input type="radio" name="paymentMethod" value="card">
                            <div class="rp-method-title">Thẻ thanh toán nội địa</div>
                        </label>
                        <label class="rp-method-option">
                            <input type="radio" name="paymentMethod" value="international_card">
                            <div class="rp-method-title">Thẻ thanh toán quốc tế</div>
                        </label>
                    </div>

                    <div class="rp-actions">
                        <button id="confirmPaymentButton" type="button" class="btn btn-primary">Xác nhận thanh toán</button>
                        <a href="{{ route('reception.check-outs.create') }}" class="btn btn-light">Quay lại xác nhận trả phòng</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="compensationModal" tabindex="-1" aria-labelledby="compensationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rp-modal">
                <div class="modal-header">
                    <h5 id="compensationModalLabel" class="modal-title">Thông tin đền bù</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <p class="rp-compensation-note">Nhập khoản phát sinh cần thu thêm trong quá trình khách trả phòng.</p>
                    <div class="rp-compensation-form">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="compensationBookingId" class="form-label">Mã đặt phòng</label>
                                <input id="compensationBookingId" type="text" class="form-control" value="9002">
                            </div>
                            <div class="col-md-6">
                                <label for="compensationAmount" class="form-label">Tiền đền bù</label>
                                <input id="compensationAmount" type="number" class="form-control" value="300000" min="0" step="1000">
                            </div>
                            <div class="col-12">
                                <label for="compensationDescription" class="form-label">Mô tả</label>
                                <textarea id="compensationDescription" class="form-control" rows="4">Hư hỏng khăn tắm hoặc vật dụng trong phòng</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="saveCompensationButton">Lưu</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const paymentMethodOptions = document.querySelectorAll('.rp-method-option');
        const zaloPayPaymentUrl = @json(url('/api/zalopay-payment'));
        const vnPayPaymentUrl = @json(url('/api/vnpay-payment'));
        const paymentRedirectUrl = @json(route('reception.check-outs.create'));
        const currentEmployeeId = @json($currentEmployeeId ? (int) $currentEmployeeId : null);
        const paymentMethodTitle = document.getElementById('paymentMethodTitle');
        const paymentMethodGrid = document.getElementById('paymentMethodGrid');
        const confirmPaymentButton = document.getElementById('confirmPaymentButton');

        function parseJsonValue(value, fallback = null) {
            try {
                return value ? JSON.parse(value) : fallback;
            } catch (error) {
                return fallback;
            }
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

        function formatMoney(value) {
            return `${Number(value || 0).toLocaleString('vi-VN')} VNĐ`;
        }

        function setText(id, value) {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value || '--';
            }
        }

        function renderRoomSummary(payload) {
            const body = document.getElementById('paymentRoomSummaryTableBody');
            if (!body) return;

            const roomItems = Array.isArray(payload.roomSummaryItems) && payload.roomSummaryItems.length
                ? payload.roomSummaryItems
                : (Array.isArray(payload.roomItems) ? payload.roomItems : []);

            if (!roomItems.length) {
                body.innerHTML = '<tr><td colspan="2" class="text-muted">Không có thông tin phòng.</td></tr>';
                return;
            }

            body.innerHTML = roomItems.map((item, index) => `
                <tr>
                    <td>${escapeHtml(item.type || 'Loại phòng')}</td>
                    <td>${escapeHtml(item.roomNumbers || '--')}</td>
                </tr>
            `).join('');
        }

        function renderRoomItems(payload) {
            const body = document.getElementById('paymentRoomTableBody');
            if (!body) return;

            const roomItems = Array.isArray(payload.roomItems) ? payload.roomItems : [];
            const roomTotal = Number(payload.roomTotal ?? roomItems.reduce((total, item) => total + Number(item.total || 0), 0));

            if (!roomItems.length) {
                body.innerHTML = '<tr><td colspan="5" class="text-muted">Không có thông tin phòng.</td></tr>';
                return;
            }

            body.innerHTML = `${roomItems.map((item) => `
                <tr>
                    <td>${escapeHtml(item.type || 'Loại phòng')}</td>
                    <td>${Number(item.quantity || 0)}</td>
                    <td>${formatMoney(item.unitPrice)}</td>
                    <td>${Number(item.nights || 0)}</td>
                    <td>${formatMoney(item.total)}</td>
                </tr>
            `).join('')}
                <tr class="rp-table-total">
                    <td colspan="4">Tổng tiền</td>
                    <td>${formatMoney(roomTotal)}</td>
                </tr>`;
        }

        function renderServiceItems(payload) {
            const body = document.getElementById('paymentServiceTableBody');
            const empty = document.getElementById('paymentServiceEmpty');
            if (!body) return;

            const serviceItems = Array.isArray(payload.serviceItems) ? payload.serviceItems : [];
            const serviceAmount = Number(payload.serviceAmount ?? serviceItems.reduce((total, item) => total + Number(item.price || 0), 0));

            if (empty) empty.hidden = serviceItems.length > 0;

            if (!serviceItems.length) {
                body.innerHTML = '';
                return;
            }

            const groupedServices = groupServiceItemsByRoom(serviceItems);
            const serviceRows = groupedServices.map((group) => `
                <tr class="rp-service-room-row">
                    <td colspan="4">${escapeHtml(group.label)}</td>
                </tr>
                ${group.items.map((item) => `
                    <tr>
                        <td>${escapeHtml(item.displayName || 'Dịch vụ')}</td>
                        <td>${Number(item.quantity || 1)}</td>
                        <td>${formatMoney(item.unitPrice)}</td>
                        <td>${formatMoney(item.price ?? (Number(item.unitPrice || 0) * Number(item.quantity || 1)))}</td>
                    </tr>
                `).join('')}
            `).join('');

            body.innerHTML = `${serviceRows}
                <tr class="rp-table-total">
                    <td colspan="3">Phí tổng dịch vụ</td>
                    <td>${formatMoney(serviceAmount)}</td>
                </tr>`;
        }

        function normalizeServiceItem(item) {
            const rawName = String(item.name || 'Dịch vụ').trim();
            const roomMatch = rawName.match(/\s+-\s+Phòng\s+(.+)$/i);
            const roomNumber = String(item.roomNumber || item.room || item.roomName || '').trim() || (roomMatch ? roomMatch[1].trim() : '');
            const displayName = roomMatch ? rawName.slice(0, roomMatch.index).trim() : rawName;

            return {
                ...item,
                displayName: displayName || 'Dịch vụ',
                roomNumber,
            };
        }

        function groupServiceItemsByRoom(serviceItems) {
            const groups = new Map();

            serviceItems.map(normalizeServiceItem).forEach((item) => {
                const key = item.roomNumber || 'unknown';
                const label = item.roomNumber ? `Phòng ${item.roomNumber}` : 'Dịch vụ chưa gắn phòng';

                if (!groups.has(key)) {
                    groups.set(key, {
                        label,
                        items: [],
                    });
                }

                groups.get(key).items.push(item);
            });

            return Array.from(groups.values());
        }

        function renderCompensationItems(payload) {
            const body = document.getElementById('paymentCompensationTableBody');
            const empty = document.getElementById('paymentCompensationEmpty');
            if (!body) return;

            const items = Array.isArray(payload.compensationItems) ? payload.compensationItems : [];

            if (empty) empty.hidden = items.length > 0;

            body.innerHTML = items.map((item) => `
                <tr>
                    <td>${escapeHtml(item.description || 'Đền bù')}</td>
                    <td>${formatMoney(item.amount)}</td>
                </tr>
            `).join('');
        }

        function renderPayment(payload) {
            setText('paymentInvoiceId', payload.invoiceId);
            setText('paymentBookingId', payload.bookingId);
            setText('paymentCustomerName', payload.customer);
            setText('paymentStayPeriod', payload.stayPeriod || payload.stay);
            setText('paymentInvoiceDate', payload.invoiceDate);
            setText('paymentCheckoutDate', payload.checkoutDate);
            setText('paymentCheckoutTime', payload.checkoutTime);
            setText('paymentGrandTotal', formatMoney(payload.totalAmount));
            setText('paymentPaidAmount', formatMoney(payload.paidAmount));
            setText('paymentAmountDue', formatMoney(payload.amountDue));

            const compensationBookingId = document.getElementById('compensationBookingId');
            const compensationAmount = document.getElementById('compensationAmount');
            const compensationDescription = document.getElementById('compensationDescription');
            const compensationItems = Array.isArray(payload.compensationItems) ? payload.compensationItems : [];
            const currentCompensationAmount = payload.compensationAmount ?? compensationItems[0]?.amount ?? 0;
            if (compensationBookingId) compensationBookingId.value = payload.bookingId || '';
            if (compensationAmount) compensationAmount.value = Number(currentCompensationAmount || 0);
            if (compensationDescription) compensationDescription.value = compensationItems[0]?.description || '';

            renderRoomSummary(payload);
            renderRoomItems(payload);
            renderServiceItems(payload);
            renderCompensationItems(payload);
            updateCheckoutActionMode(payload);
        }

        function updateCheckoutActionMode(payload) {
            const amountDue = Math.max(Number(payload?.amountDue || 0), 0);
            const isAlreadyPaid = amountDue <= 0;

            if (paymentMethodTitle) paymentMethodTitle.hidden = isAlreadyPaid;
            if (paymentMethodGrid) paymentMethodGrid.hidden = isAlreadyPaid;
            if (confirmPaymentButton) {
                confirmPaymentButton.textContent = isAlreadyPaid ? 'Xác nhận trả phòng' : 'Xác nhận thanh toán';
            }
        }

        function readErrorMessage(data, fallback = 'Không thể xử lý yêu cầu. Vui lòng thử lại.') {
            if (data?.message) {
                return data.message;
            }

            if (data?.sub_message) {
                return data.sub_message;
            }

            if (data?.errors) {
                return Object.values(data.errors).flat().join('\n');
            }

            return fallback;
        }

        function getSelectedPaymentMethod() {
            return document.querySelector('input[name="paymentMethod"]:checked')?.value || 'bank_transfer';
        }

        function getCheckoutPaymentAmount() {
            return Math.max(Math.round(Number(currentPaymentPayload?.amountDue || 0)), 0);
        }

        function getCheckoutBookingId() {
            const bookingId = String(currentPaymentPayload?.bookingId || '').replace(/\D+/g, '');
            return bookingId ? Number(bookingId) : 0;
        }

        function buildCheckoutPaymentDescription() {
            const bookingId = currentPaymentPayload?.bookingId || '';
            const customer = currentPaymentPayload?.customer || 'khach hang';

            return `Peach Valley thanh toan checkout dat phong ${bookingId} - ${customer}`;
        }

        function getPaymentAppUser() {
            return String(currentPaymentPayload?.phone || currentPaymentPayload?.customer || currentPaymentPayload?.bookingId || 'reception')
                .replace(/[^\p{L}\p{N}_-]+/gu, '_')
                .slice(0, 50) || 'reception';
        }

        function buildCheckoutReturnUrl(params = {}) {
            const url = new URL(paymentRedirectUrl, window.location.origin);
            const bookingId = getCheckoutBookingId();

            if (bookingId) {
                url.searchParams.set('booking', bookingId);
            }

            Object.entries(params).forEach(([key, value]) => {
                if (value !== undefined && value !== null && value !== '') {
                    url.searchParams.set(key, value);
                }
            });

            return url.toString();
        }

        async function createZaloPayCheckoutPayment(amount, bookingId) {
            const response = await fetch(zaloPayPaymentUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    amount,
                    app_user: getPaymentAppUser(),
                    description: buildCheckoutPaymentDescription(),
                    redirect_url: buildCheckoutReturnUrl({ checkout: 'success' }),
                    dat_phong_ids: [bookingId],
                    payment_type: 'checkout',
                    ma_nv: currentEmployeeId,
                }),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok || data.status !== 'success' || !data.order_url) {
                throw new Error(readErrorMessage(data, 'Không thể tạo thanh toán ZaloPay.'));
            }

            window.location.href = data.order_url;
        }

        async function createVnPayCheckoutPayment(amount, bookingId, bankCode) {
            const response = await fetch(vnPayPaymentUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    amount,
                    description: buildCheckoutPaymentDescription(),
                    redirect_url: buildCheckoutReturnUrl(),
                    dat_phong_ids: [bookingId],
                    bank_code: bankCode,
                    payment_type: 'checkout',
                    ma_nv: currentEmployeeId,
                }),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok || data.status !== 'success' || !data.payment_url) {
                throw new Error(readErrorMessage(data, 'Không thể tạo thanh toán VNPAY.'));
            }

            window.location.href = data.payment_url;
        }

        async function confirmDirectCheckout(bookingId) {
            const response = await fetch(`/api/dat-phong/${bookingId}/check-out`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    MaNV: currentEmployeeId,
                }),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok || data.success === false) {
                throw new Error(readErrorMessage(data, 'Không thể xác nhận trả phòng.'));
            }

            window.location.href = buildCheckoutReturnUrl({ checkout: 'success' });
        }

        async function confirmCheckoutPayment(event) {
            const button = event.currentTarget;
            const method = getSelectedPaymentMethod();
            const amount = getCheckoutPaymentAmount();
            const bookingId = getCheckoutBookingId();

            if (!bookingId) {
                alert('Không xác định được mã đặt phòng để thanh toán.');
                return;
            }

            if (!currentEmployeeId) {
                alert('Tài khoản đang đăng nhập chưa được gắn mã nhân viên. Vui lòng kiểm tra lại tài khoản lễ tân.');
                return;
            }

            button.disabled = true;
            const originalText = button.textContent;
            button.textContent = amount <= 0 ? 'Đang xác nhận trả phòng...' : 'Đang tạo thanh toán...';

            try {
                if (amount <= 0) {
                    await confirmDirectCheckout(bookingId);
                    return;
                }

                if (method === 'bank_transfer') {
                    await createZaloPayCheckoutPayment(amount, bookingId);
                    return;
                }

                if (method === 'card') {
                    await createVnPayCheckoutPayment(amount, bookingId, 'VNBANK');
                    return;
                }

                if (method === 'international_card') {
                    await createVnPayCheckoutPayment(amount, bookingId, 'INTCARD');
                    return;
                }

                throw new Error('Vui lòng chọn hình thức thanh toán.');
            } catch (error) {
                alert(error.message || 'Không thể tạo thanh toán. Vui lòng thử lại.');
                button.disabled = false;
                button.textContent = originalText;
            }
        }

        function hideCompensationModal() {
            const modalElement = document.getElementById('compensationModal');
            if (!modalElement || !window.bootstrap?.Modal) return;

            const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
            modal.hide();
        }

        function updatePaymentAfterCompensation(payload, responseData, description, amount) {
            const invoice = responseData?.data?.hoaDon || {};
            const totalAmount = Number(invoice.TongTien ?? payload.totalAmount ?? 0);
            const paidAmount = Number(invoice.DaThanhToan ?? payload.paidAmount ?? 0);

            return {
                ...payload,
                compensationAmount: amount,
                compensationItems: amount > 0
                    ? [{
                        description: description || 'Đền bù hư hỏng',
                        amount,
                    }]
                    : [],
                totalAmount,
                paidAmount,
                amountDue: Math.max(totalAmount - paidAmount, 0),
            };
        }

        function fillMissingRoomNumbers(payload, fallbackPayload) {
            const roomItems = Array.isArray(payload.roomItems) ? payload.roomItems : [];
            const fallbackItems = Array.isArray(fallbackPayload.roomItems) ? fallbackPayload.roomItems : [];
            const roomSummaryItems = Array.isArray(payload.roomSummaryItems) ? payload.roomSummaryItems : [];
            const fallbackSummaryItems = Array.isArray(fallbackPayload.roomSummaryItems) ? fallbackPayload.roomSummaryItems : [];
            const sameBooking = String(payload.bookingId || '') === String(fallbackPayload.bookingId || '');
            const normalizeType = (value) => String(value || '').trim().toLowerCase();

            const nextPayload = {
                ...payload,
            };

            nextPayload.roomItems = roomItems.map((item, index) => {
                    if (item.roomNumbers) {
                        return item;
                    }

                    const fallback = fallbackItems.find((fallbackItem) => (
                        item.roomTypeId && String(fallbackItem.roomTypeId || '') === String(item.roomTypeId)
                    )) || fallbackItems.find((fallbackItem) => (
                        String(fallbackItem.type || '') === String(item.type || '')
                    )) || fallbackItems[index];

                    return {
                        ...item,
                        roomNumbers: fallback?.roomNumbers || '--',
                    };
                });

            const sourceSummaryItems = sameBooking && fallbackSummaryItems.length
                ? fallbackSummaryItems
                : roomSummaryItems;

            const sourceRoomItems = sameBooking && fallbackItems.length
                ? fallbackItems
                : (nextPayload.roomItems || []);

            const summaryByType = new Map();

            sourceRoomItems.forEach((item) => {
                const type = item.type || 'Loại phòng';
                const key = normalizeType(type);
                if (!key || summaryByType.has(key)) return;

                summaryByType.set(key, {
                    roomTypeId: item.roomTypeId || '',
                    type,
                    roomNumbers: item.roomNumbers || '--',
                });
            });

            sourceSummaryItems.forEach((item) => {
                const type = item.type || 'Loại phòng';
                const key = normalizeType(type);
                if (!key) return;

                const existing = summaryByType.get(key);
                summaryByType.set(key, {
                    roomTypeId: item.roomTypeId || existing?.roomTypeId || '',
                    type,
                    roomNumbers: item.roomNumbers && item.roomNumbers !== '--'
                        ? item.roomNumbers
                        : (existing?.roomNumbers || '--'),
                });
            });

            nextPayload.roomSummaryItems = Array.from(summaryByType.values());

            return nextPayload;
        }

        const serverPaymentPayload = @json($paymentData);
        const sessionPaymentPayload = parseJsonValue(sessionStorage.getItem('receptionCheckoutPayment'), null);
        const isValidSessionPaymentPayload = (payload) => {
            if (!payload || Number(payload.payloadVersion || 0) < 2 || !payload.bookingId) {
                return false;
            }

            const generatedAt = Number(payload.generatedAt || 0);
            const maxAge = 10 * 60 * 1000;

            return generatedAt > 0 && Date.now() - generatedAt <= maxAge;
        };
        const validSessionPaymentPayload = isValidSessionPaymentPayload(sessionPaymentPayload)
            ? sessionPaymentPayload
            : null;

        if (sessionPaymentPayload && !validSessionPaymentPayload) {
            sessionStorage.removeItem('receptionCheckoutPayment');
        }

        const mergedPaymentPayload = {
            ...serverPaymentPayload,
            ...(validSessionPaymentPayload || {}),
            serviceItems: Array.isArray(validSessionPaymentPayload?.serviceItems)
                ? validSessionPaymentPayload.serviceItems
                : (serverPaymentPayload.serviceItems || []),
            serviceAmount: validSessionPaymentPayload
                ? validSessionPaymentPayload.serviceAmount
                : serverPaymentPayload.serviceAmount,
        };

        let currentPaymentPayload = fillMissingRoomNumbers(mergedPaymentPayload, serverPaymentPayload);
        renderPayment(currentPaymentPayload);

        document.getElementById('saveCompensationButton')?.addEventListener('click', async (event) => {
            const button = event.currentTarget;
            const bookingId = document.getElementById('compensationBookingId')?.value?.trim();
            const amount = Number(document.getElementById('compensationAmount')?.value || 0);
            const description = document.getElementById('compensationDescription')?.value?.trim();

            if (!bookingId) {
                alert('Vui lòng chọn hóa đơn/đặt phòng trước khi thêm đền bù.');
                return;
            }

            if (!Number.isFinite(amount) || amount < 0) {
                alert('Tiền đền bù không hợp lệ.');
                return;
            }

            button.disabled = true;

            try {
                const response = await fetch('/api/den-bu', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        MaDatPhong: bookingId,
                        MoTa: description,
                        TienDenBu: amount,
                    }),
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok || data.success === false) {
                    throw new Error(readErrorMessage(data, 'Không thể lưu tiền đền bù. Vui lòng thử lại.'));
                }

                currentPaymentPayload = updatePaymentAfterCompensation(
                    currentPaymentPayload,
                    data,
                    description,
                    amount
                );
                sessionStorage.setItem('receptionCheckoutPayment', JSON.stringify(currentPaymentPayload));

                renderPayment(currentPaymentPayload);
                hideCompensationModal();
            } catch (error) {
                alert(error.message || 'Không thể lưu tiền đền bù. Vui lòng thử lại.');
            } finally {
                button.disabled = false;
            }
        });

        confirmPaymentButton?.addEventListener('click', confirmCheckoutPayment);

        paymentMethodOptions.forEach((option) => {
            option.addEventListener('click', () => {
                paymentMethodOptions.forEach((item) => {
                    const input = item.querySelector('input');
                    const isSelected = item === option;
                    item.classList.toggle('is-selected', isSelected);
                    input.checked = isSelected;
                });
            });
        });
    </script>
</x-app-layout>
