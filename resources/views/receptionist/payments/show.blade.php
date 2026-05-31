@php
    $invoice = $payment?->hoaDon;
    $booking = $invoice?->datPhong;
    $customer = $booking?->khachHang;

    $formatDateTime = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y H:i:s') : '--';
    $formatMoney = fn ($amount) => is_numeric($amount) ? number_format((float) $amount, 0, ',', '.') . ' VNĐ' : '--';

    $paymentMethod = match ((int) ($payment->PhuongThuc ?? -1)) {
        1 => 'Thẻ',
        2 => 'QR Code',
        default => '--',
    };

    $paymentType = match ((int) ($payment->LoaiThanhToan ?? -1)) {
        0 => 'Thanh toán tiền phòng',
        1 => 'Thanh toán trả phòng',
        default => '--',
    };

    $transactionStatus = match ((int) ($payment->TrangThaiGiaoDich ?? -1)) {
        0 => 'Chờ xử lý',
        1 => 'Thành công',
        2 => 'Thất bại',
        default => '--',
    };

    $invoiceStatus = match ((int) ($invoice?->TrangThai ?? -1)) {
        0 => 'Chưa thanh toán',
        1 => 'Đã thanh toán',
        3 => 'Đã hủy',
        default => '--',
    };
@endphp

<x-receptionist.show-page
    title="Chi tiết thanh toán"
    subtitle="Thông tin chi tiết thanh toán"
    :index-route="route('reception.payments.index')"
>
    <style>
        .rp-show {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .rp-section {
            border: 1px solid rgba(122, 57, 42, 0.16);
            border-radius: 18px;
            padding: 1.25rem;
            background: #fffdfa;
        }

        .rp-section-title {
            margin: 0 0 1rem;
            color: #6f1d01;
            font-size: 1.15rem;
            font-weight: 700;
        }

        .rp-info-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }

        .rp-info-grid--two {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .rp-info-item {
            min-width: 0;
            border-bottom: 1px solid rgba(122, 57, 42, 0.12);
            padding-bottom: 0.85rem;
        }

        .rp-label {
            color: #85584f;
            font-size: 0.9rem;
            margin-bottom: 0.35rem;
        }

        .rp-value {
            color: #1f0903;
            font-size: 1.05rem;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .rp-status {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 0.35rem 0.75rem;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .rp-status--success { background: #dcfce7; color: #166534; }
        .rp-status--warning { background: #fef3c7; color: #9a3412; }
        .rp-status--danger { background: #fee2e2; color: #b91c1c; }
        .rp-status--muted { background: #e5e7eb; color: #374151; }

        .rp-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .rp-section-header .rp-section-title {
            margin-bottom: 0;
        }

        .rp-link-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(122, 57, 42, 0.22);
            border-radius: 8px;
            padding: 0.5rem 0.9rem;
            background: #fff;
            color: #6f1d01;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
        }

        .rp-link-button:hover,
        .rp-link-button:focus {
            background: #6f1d01;
            color: #fff !important;
        }

        @media (max-width: 1199.98px) {
            .rp-info-grid,
            .rp-info-grid--two {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .rp-info-grid,
            .rp-info-grid--two {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="col-12">
        <div class="rp-show">
            <section class="rp-section">
                <h5 class="rp-section-title">Thông tin thanh toán</h5>
                <div class="rp-info-grid">
                    <div class="rp-info-item">
                        <div class="rp-label">Mã thanh toán</div>
                        <div class="rp-value">{{ $payment->MaTT ?? '--' }}</div>
                    </div>
                    <div class="rp-info-item">
                        <div class="rp-label">Số tiền</div>
                        <div class="rp-value">{{ $formatMoney($payment->SoTien) }}</div>
                    </div>
                    <div class="rp-info-item">
                        <div class="rp-label">Ngày thanh toán</div>
                        <div class="rp-value">{{ $formatDateTime($payment->NgayThanhToan) }}</div>
                    </div>
                    <div class="rp-info-item">
                        <div class="rp-label">Trạng thái giao dịch</div>
                        <div class="rp-value">
                            <span class="rp-status {{ (int) ($payment->TrangThaiGiaoDich ?? -1) === 1 ? 'rp-status--success' : ((int) ($payment->TrangThaiGiaoDich ?? -1) === 2 ? 'rp-status--danger' : 'rp-status--warning') }}">
                                {{ $transactionStatus }}
                            </span>
                        </div>
                    </div>
                    <div class="rp-info-item">
                        <div class="rp-label">Phương thức</div>
                        <div class="rp-value">{{ $paymentMethod }}</div>
                    </div>
                    <div class="rp-info-item">
                        <div class="rp-label">Loại thanh toán</div>
                        <div class="rp-value">{{ $paymentType }}</div>
                    </div>
                    <div class="rp-info-item">
                        <div class="rp-label">Nhà cung cấp</div>
                        <div class="rp-value">{{ $payment->NhaCungCap ?? '--' }}</div>
                    </div>
                    <div class="rp-info-item">
                        <div class="rp-label">Người thanh toán</div>
                        <div class="rp-value">{{ $customer?->TenKH ?? $payment->DinhDanhNguoiThanhToan ?? '--' }}</div>
                    </div>
                </div>
            </section>

            <section class="rp-section">
                <div class="rp-section-header">
                    <h5 class="rp-section-title">Liên kết hóa đơn và đặt phòng</h5>
                    @if($payment?->MaHD)
                        <a href="{{ route('reception.invoices.show', ['invoiceId' => $payment->MaHD]) }}" class="rp-link-button">
                            Xem hóa đơn
                        </a>
                    @endif
                </div>
                <div class="rp-info-grid">
                    <div class="rp-info-item">
                        <div class="rp-label">Mã hóa đơn</div>
                        <div class="rp-value">{{ $payment->MaHD ?? '--' }}</div>
                    </div>
                    <div class="rp-info-item">
                        <div class="rp-label">Trạng thái hóa đơn</div>
                        <div class="rp-value">{{ $invoiceStatus }}</div>
                    </div>
                    <div class="rp-info-item">
                        <div class="rp-label">Mã đặt phòng</div>
                        <div class="rp-value">{{ $booking?->MaDatPhong ?? '--' }}</div>
                    </div>
                    <div class="rp-info-item">
                        <div class="rp-label">Số điện thoại</div>
                        <div class="rp-value">{{ $customer?->SoDienThoai ?? '--' }}</div>
                    </div>
                </div>
            </section>

            <section class="rp-section">
                <h5 class="rp-section-title">Thông tin giao dịch</h5>
                <div class="rp-info-grid rp-info-grid--two">
                    <div class="rp-info-item">
                        <div class="rp-label">Mã giao dịch</div>
                        <div class="rp-value">{{ $payment->MaGiaoDich ?? '--' }}</div>
                    </div>
                    <div class="rp-info-item">
                        <div class="rp-label">Mã giao dịch cổng thanh toán</div>
                        <div class="rp-value">{{ $payment->MaGiaoDichCongThanhToan ?? '--' }}</div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-receptionist.show-page>
