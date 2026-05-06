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
        .rp-service-table-wrap {
            margin-top: 0.9rem;
            overflow-x: auto;
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
        .rp-compensation-table th,
        .rp-compensation-table td {
            padding: 0.85rem 0.75rem;
            border-bottom: 1px solid rgba(166, 98, 43, 0.12);
            text-align: left;
            color: #6f1d01;
        }
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
        .rp-compensation-table td:last-child,
        .rp-compensation-table th:last-child {
            text-align: right;
            white-space: nowrap;
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
            border-radius: 20px;
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
                            <div class="rp-info-label">Phòng</div>
                            <div id="paymentRoom" class="rp-info-value">A102 - Suite</div>
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
                </div>
            </div>

            <div class="col-xl-7">
                <div class="rp-card">
                    <h5 class="mb-3">Chi tiết hóa đơn</h5>

                    <div class="rp-detail-grid">
                        <div class="rp-detail-item rp-detail-item--full">
                            <div class="rp-detail-label">Thông tin sử dụng dịch vụ</div>
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
                            <div class="rp-total-label">Phí tổng dịch vụ</div>
                            <div id="paymentServiceTotalSummary" class="rp-total-value">450.000 VNĐ</div>
                        </div>
                        <div class="rp-total-line">
                            <div class="rp-total-label">Đền bù bổ sung</div>
                            <div id="paymentCompensationTotal" class="rp-total-value">300.000 VNĐ</div>
                        </div>
                           <div class="rp-total-line">
                            <div class="rp-total-label">Phần tiền phòng còn phải trả</div>
                            <div id="paymentAmountDue" class="rp-total-value">1.250.000 VNĐ</div>
                        </div>
                        <div class="rp-total-line">
                            <div class="rp-total-label">Tổng thanh toán</div>
                            <div id="paymentGrandTotal" class="rp-total-value">1.550.000 VNĐ</div>
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
                            <div class="rp-method-title">Quét mã QR</div>
                            <div class="rp-method-note">Xác nhận giao dịch qua ZaloPay</div>
                        </label>
                        <label class="rp-method-option">
                            <input type="radio" name="paymentMethod" value="card">
                            <div class="rp-method-title">Thẻ thanh toán</div>
                            <div class="rp-method-note">Xác nhận giao dịch qua ZaloPay</div>
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
