@php
    $invoice = $invoice ?? null;
    $booking = $invoice?->datPhong;
    $customer = $booking?->khachHang;
    $details = $invoice?->chiTietHoaDons ?? collect();
    $payments = $invoice?->thanhToans ?? collect();
    $paidAmount = (float) ($invoice?->DaThanhToan ?? $payments->sum('SoTien') ?? 0);
    $totalAmount = (float) ($invoice?->TongTien ?? 0);
    $remainingAmount = (int) ($invoice?->TrangThai ?? 0) === 0 ? max($totalAmount - $paidAmount, 0) : 0;
    $settledAt = $payments->max('NgayThanhToan');
    $formatMoney = fn ($amount) => number_format((float) ($amount ?? 0), 0, ',', '.') . ' VNĐ';
    $formatDate = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y') : '--';
    $statusLabels = [
        0 => 'Chưa thanh toán',
        1 => 'Đã thanh toán',
        3 => 'Đã hủy',
    ];
    $statusClasses = [
        0 => 'is-warning',
        1 => 'is-success',
        3 => 'is-danger',
    ];
    $paymentTypes = [
        0 => 'Thanh toán tiền phòng',
        1 => 'Thanh toán trả phòng',
    ];
    $paymentStatusLabels = [
        0 => 'Thất bại',
        1 => 'Thành công',
    ];
    $paymentStatusClasses = [
        0 => 'is-danger',
        1 => 'is-success',
    ];
    $status = (int) ($invoice?->TrangThai ?? -1);
    $rooms = $booking?->chiTietDatPhong?->pluck('phong.SoPhong')->filter()->unique()->implode(', ');
@endphp

<x-receptionist.show-page
    title="Chi tiết hóa đơn"
    subtitle="Thông tin hóa đơn, đặt phòng và các khoản thanh toán."
    :index-route="route('reception.invoices.index')"
>
    <style>
        .ri-show {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .ri-section {
            border: 1px solid rgba(122, 57, 42, 0.16);
            border-radius: 18px;
            padding: 1.25rem;
            background: #fffdfa;
        }

        .ri-section-title {
            margin: 0 0 1rem;
            color: #6f1d01;
            font-size: 1.15rem;
            font-weight: 700;
        }

        .ri-info-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }

        .ri-info-item {
            min-width: 0;
            border-bottom: 1px solid rgba(122, 57, 42, 0.12);
            padding-bottom: 0.85rem;
        }

        .ri-label {
            color: #85584f;
            font-size: 0.9rem;
            margin-bottom: 0.35rem;
        }

        .ri-value {
            color: #1f0903;
            font-size: 1.05rem;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .ri-status {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 0.35rem 0.75rem;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .ri-status.is-warning { background: #fef3c7; color: #9a3412; }
        .ri-status.is-success { background: #dcfce7; color: #166534; }
        .ri-status.is-danger { background: #fee2e2; color: #b91c1c; }
        .ri-status.is-muted { background: #e5e7eb; color: #374151; }

        .ri-table {
            width: 100%;
            margin: 0;
        }

        .ri-table th {
            color: #6f1d01;
            font-weight: 700;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .ri-table th,
        .ri-table td {
            border-bottom: 1px solid rgba(122, 57, 42, 0.12);
            padding: 0.85rem 0.75rem;
            vertical-align: middle;
        }

        .ri-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .ri-money {
            text-align: right;
            white-space: nowrap;
        }

        .ri-detail-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(122, 57, 42, 0.22);
            border-radius: 8px;
            padding: 0.45rem 0.8rem;
            color: #6f1d01;
            background: #fff;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
        }

        .ri-detail-button:hover,
        .ri-detail-button:focus {
            color: #fff !important;
            background: #6f1d01;
        }

        @media (max-width: 1199.98px) {
            .ri-info-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .ri-info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="col-12">
        <div class="ri-show">
            <section class="ri-section">
                <h5 class="ri-section-title">Thông tin hóa đơn</h5>
                <div class="ri-info-grid">
                    <div class="ri-info-item">
                        <div class="ri-label">Mã hóa đơn</div>
                        <div class="ri-value">{{ $invoice?->MaHD ?? '--' }}</div>
                    </div>
                    <div class="ri-info-item">
                        <div class="ri-label">Ngày lập</div>
                        <div class="ri-value">{{ $formatDate($invoice?->NgayLapHD) }}</div>
                    </div>
                    <div class="ri-info-item">
                        <div class="ri-label">Hạn tất toán</div>
                        <div class="ri-value">{{ $formatDate($booking?->NgayTraPhong) }}</div>
                    </div>
                    <div class="ri-info-item">
                        <div class="ri-label">Ngày tất toán</div>
                        <div class="ri-value">{{ $status === 1 ? $formatDate($settledAt) : '--' }}</div>
                    </div>
                    <div class="ri-info-item">
                        <div class="ri-label">Tổng tiền</div>
                        <div class="ri-value">{{ $formatMoney($totalAmount) }}</div>
                    </div>
                    <div class="ri-info-item">
                        <div class="ri-label">Đã thanh toán</div>
                        <div class="ri-value">{{ $formatMoney($paidAmount) }}</div>
                    </div>
                    <div class="ri-info-item">
                        <div class="ri-label">Còn lại</div>
                        <div class="ri-value">{{ $formatMoney($remainingAmount) }}</div>
                    </div>
                    <div class="ri-info-item">
                        <div class="ri-label">Trạng thái</div>
                        <div class="ri-value">
                            <span class="ri-status {{ $statusClasses[$status] ?? 'is-muted' }}">
                                {{ $statusLabels[$status] ?? 'Không xác định' }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="ri-section">
                <h5 class="ri-section-title">Thông tin đặt phòng và khách hàng</h5>
                <div class="ri-info-grid">
                    <div class="ri-info-item">
                        <div class="ri-label">Mã đặt phòng</div>
                        <div class="ri-value">{{ $booking?->MaDatPhong ?? '--' }}</div>
                    </div>
                    <div class="ri-info-item">
                        <div class="ri-label">Khách hàng</div>
                        <div class="ri-value">{{ $customer?->TenKH ?? '--' }}</div>
                    </div>
                    <div class="ri-info-item">
                        <div class="ri-label">Số điện thoại</div>
                        <div class="ri-value">{{ $customer?->SoDienThoai ?? '--' }}</div>
                    </div>
                    <div class="ri-info-item">
                        <div class="ri-label">CCCD</div>
                        <div class="ri-value">{{ $customer?->CCCD ?? '--' }}</div>
                    </div>
                    <div class="ri-info-item">
                        <div class="ri-label">Ngày nhận phòng</div>
                        <div class="ri-value">{{ $formatDate($booking?->NgayNhanPhong) }}</div>
                    </div>
                    <div class="ri-info-item">
                        <div class="ri-label">Ngày trả phòng</div>
                        <div class="ri-value">{{ $formatDate($booking?->NgayTraPhong) }}</div>
                    </div>
                    <div class="ri-info-item">
                        <div class="ri-label">Phòng</div>
                        <div class="ri-value">{{ $rooms ?: '--' }}</div>
                    </div>
                    <div class="ri-info-item">
                        <div class="ri-label">Nhân viên lập</div>
                        <div class="ri-value">{{ $invoice?->nhanVien?->TenNV ?? '--' }}</div>
                    </div>
                </div>
            </section>

            <section class="ri-section">
                <h5 class="ri-section-title">Chi tiết chi phí</h5>
                <div class="table-responsive">
                    <table class="ri-table">
                        <thead>
                            <tr>
                                <th>Mã CTHĐ</th>
                                <th>Khoản mục</th>
                                <th>Loại</th>
                                <th class="text-center">Số lượng</th>
                                <th class="ri-money">Đơn giá</th>
                                <th class="ri-money">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details as $detail)
                                @php
                                    $itemType = 'Khác';
                                    $itemName = $detail->MoTa ?: 'Khoản phí';

                                    if ($detail->MaLoaiPhong) {
                                        $itemType = 'Phòng';
                                        $itemName = $detail->loaiPhong?->TenLoaiPhong ?? $itemName;
                                    } elseif ($detail->MaSuDung) {
                                        $itemType = 'Dịch vụ';
                                        $itemName = $detail->suDung?->dichVu?->TenDV ?? $itemName;
                                    } elseif ($detail->MaDenBu) {
                                        $itemType = 'Đền bù';
                                        $itemName = $detail->denBu?->MoTa ?? $detail->denBu?->LyDo ?? $itemName;
                                    }

                                    $quantity = (int) ($detail->SoLuong ?? 0);
                                    $unitPrice = (float) ($detail->DonGia ?? 0);
                                    $lineTotal = $quantity * $unitPrice;
                                @endphp
                                <tr>
                                    <td>{{ $detail->MaCTHD }}</td>
                                    <td>{{ $itemName }}</td>
                                    <td>{{ $itemType }}</td>
                                    <td class="text-center">{{ $quantity }}</td>
                                    <td class="ri-money">{{ $formatMoney($unitPrice) }}</td>
                                    <td class="ri-money">{{ $formatMoney($lineTotal) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Chưa có chi tiết chi phí.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="ri-section">
                <h5 class="ri-section-title">Lịch sử thanh toán</h5>
                <div class="table-responsive">
                    <table class="ri-table">
                        <thead>
                            <tr>
                                <th>Mã thanh toán</th>
                                <th>Ngày thanh toán</th>
                                <th>Loại thanh toán</th>
                                <th>Trạng thái</th>
                                <th class="ri-money">Số tiền</th>
                                <th class="text-center">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                @php
                                    $paymentStatus = (int) ($payment->TrangThaiGiaoDich ?? 1);
                                @endphp
                                <tr>
                                    <td>{{ $payment->MaTT }}</td>
                                    <td>{{ $formatDate($payment->NgayThanhToan) }}</td>
                                    <td>{{ $paymentTypes[(int) ($payment->LoaiThanhToan ?? -1)] ?? '--' }}</td>
                                    <td>
                                        <span class="ri-status {{ $paymentStatusClasses[$paymentStatus] ?? 'is-muted' }}">
                                            {{ $paymentStatusLabels[$paymentStatus] ?? 'Không xác định' }}
                                        </span>
                                    </td>
                                    <td class="ri-money">{{ $formatMoney($payment->SoTien) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('reception.payments.show', ['paymentId' => $payment->MaTT]) }}" class="ri-detail-button">
                                            Xem
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Chưa có thanh toán.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-receptionist.show-page>
