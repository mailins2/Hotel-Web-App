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
                            <div class="rp-info-value">03/05/2026 - 05/05/2026</div>
                        </div>
                        <div class="rp-info-item">
                            <div class="rp-info-label">Ngày lập hóa đơn</div>
                            <div class="rp-info-value">05/05/2026</div>
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
                            <tbody>
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
                                    <tbody>
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
                                            <th>Loại dịch vụ</th>
                                            <th>Giá</th>
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
                            <div class="rp-total-value">300.000 VNĐ</div>
                        </div>
                        <div class="rp-total-line">
                            <div class="rp-total-label">Số tiền còn phải trả</div>
                            <div id="paymentAmountDue" class="rp-total-value">1.250.000 VNĐ</div>
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3">Chọn hình thức thanh toán</h5>
                    <div class="rp-method-grid">
                        <!-- <label class="rp-method-option is-selected">
                            <input type="radio" name="paymentMethod" value="cash" checked>
                            <div class="rp-method-title">Tiền mặt</div>
                            <div class="rp-method-note">Thanh toán trực tiếp tại quầy lễ tân</div>
                        </label> -->
                        <label class="rp-method-option">
                            <input type="radio" name="paymentMethod" value="bank_transfer">
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
                        <button type="button" class="btn btn-primary">Xác nhận thanh toán</button>
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
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Lưu</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const paymentMethodOptions = document.querySelectorAll('.rp-method-option');

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
