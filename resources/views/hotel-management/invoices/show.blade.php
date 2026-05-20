@php
    $booking = $invoice?->datPhong;
    $customer = $booking?->khachHang;
    $employee = $invoice?->nhanVien;
    $promotion = $invoice?->khuyenMai;
    $details = collect($invoice?->chiTietHoaDons ?? []);
    $roomDetails = $details->filter(fn ($detail) => !empty($detail->MaLoaiPhong));
    $serviceDetails = $details->filter(fn ($detail) => !empty($detail->MaSuDung));
    $damageDetails = $details->filter(fn ($detail) => !empty($detail->MaDenBu));
    $roomNumbersByType = collect($booking?->chiTietDatPhong ?? [])
        ->filter(fn ($detail) => $detail?->phong?->MaLoaiPhong)
        ->groupBy(fn ($detail) => (string) $detail->phong->MaLoaiPhong)
        ->map(fn ($items) => $items
            ->map(fn ($detail) => $detail?->phong?->SoPhong)
            ->filter()
            ->values()
            ->implode(', ')
        );
    $formatDate = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y') : '--';
    $formatDateTime = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y H:i:s') : '--';
    $formatMoney = fn ($amount) => is_numeric($amount) ? number_format((float) $amount, 0, ',', '.') . ' VNĐ' : '--';
    $paid = (float) ($invoice->DaThanhToan ?? $invoice->thanhToans->sum('SoTien') ?? 0);
    $total = (float) ($invoice->TongTien ?? 0);
    $remaining = max($total - $paid, 0);
    $roomTotal = $roomDetails->sum(fn ($detail) => (float) ($detail->SoLuong ?? 1) * (float) ($detail->DonGia ?? 0));
    $serviceTotal = $serviceDetails->sum(fn ($detail) => (float) ($detail->SoLuong ?? 1) * (float) ($detail->DonGia ?? 0));
    $damageTotal = $damageDetails->sum(fn ($detail) => (float) ($detail->SoLuong ?? 1) * (float) ($detail->DonGia ?? 0));
    $status = match ((int) $invoice->TrangThai) {
        0 => ['label' => 'Chưa thanh toán', 'class' => 'warning'],
        1 => ['label' => 'Đã thanh toán', 'class' => 'success'],
        2 => ['label' => 'Đã xuất hóa đơn', 'class' => 'info'],
        3 => ['label' => 'Đã hủy', 'class' => 'danger'],
        default => ['label' => 'Không xác định', 'class' => 'muted'],
    };
@endphp

<x-app-layout :assets="['animation']">
    <style>
        .invoice-detail-page {
            color: #211816;
            padding-top: 56px;
            margin-bottom: 20px;
        }

        .invoice-detail-hero,
        .invoice-detail-section {
            background: #fffefa;
            border: 1px solid rgba(151, 64, 26, 0.15);
            box-shadow: 0 20px 50px rgba(122, 39, 12, 0.14);
            /* margin-bottom: 20px; */
        }

        .invoice-detail-hero {
            border-radius: 28px;
            padding: 26px 28px;
        }

        .invoice-detail-title {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
        }

        .invoice-detail-title h1 {
            margin: 0;
            color: #090706;
            font-size: 30px;
            font-weight: 700;
            letter-spacing: 0;
        }

        .invoice-detail-subtitle {
            margin: 8px 0 0;
            color: #6b5a54;
            font-size: 16px;
        }

        .invoice-detail-badge {
            display: inline-flex;
            align-items: center;
            min-height: 28px;
            padding: 5px 13px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .invoice-detail-badge--success { background: #dcfce7; color: #166534; }
        .invoice-detail-badge--info { background: #e0f2fe; color: #0369a1; }
        .invoice-detail-badge--warning { background: #fef3c7; color: #92400e; }
        .invoice-detail-badge--danger { background: #fee2e2; color: #991b1b; }
        .invoice-detail-badge--muted { background: #eceff3; color: #475569; }

        .invoice-detail-actions {
            display: flex;
            justify-content: flex-end;
        }

        .invoice-detail-actions .btn {
            border-radius: 5px;
            min-height: 42px;
            padding: 10px 18px;
            font-weight: 600;
        }

        .invoice-detail-actions .btn-primary {
            background: #7a270c;
            border-color: #6f1d01;
            color: #fff;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.18);
        }

        .invoice-detail-actions .btn-primary:hover {
            background: #5f1b08;
            border-color: #5f1b08;
        }

        .invoice-detail-section {
            border-radius: 24px;
            height: 100%;
            padding: 22px;
            /* margin-bottom: 20px; */
        }

        .invoice-detail-section h2 {
            margin: 0 0 18px;
            color: #100d0c;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0;
        }

        .invoice-detail-info-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .invoice-detail-info-grid--compact {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .invoice-detail-card {
            min-height: 82px;
            padding: 15px 16px;
            border: 1px solid rgba(151, 64, 26, 0.13);
            border-radius: 13px;
            background: #fffefa;
            box-shadow: 0 12px 28px rgba(122, 39, 12, 0.09);
        }

        .invoice-detail-card--accent {
            background: linear-gradient(180deg, #fff7ef 0%, #fffefa 100%);
        }

        .invoice-detail-label {
            margin-bottom: 9px;
            color: #8b3a1d;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .invoice-detail-value {
            color: #1e1715;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.45;
        }

        .invoice-detail-table-wrap {
            overflow-x: auto;
            border: 1px solid rgba(151, 64, 26, 0.13);
            border-radius: 13px;
            box-shadow: 0 12px 28px rgba(122, 39, 12, 0.08);
        }

        .invoice-detail-table {
            min-width: 760px;
            margin: 0;
            color: #271b18;
        }

        .invoice-detail-table thead th {
            background: #fffaf6;
            border-bottom: 1px solid rgba(151, 64, 26, 0.13);
            color: #8b3a1d;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0;
            padding: 15px 16px;
            text-transform: uppercase;
        }

        .invoice-detail-table tbody td {
            border-bottom: 1px solid rgba(151, 64, 26, 0.1);
            color: #241917;
            font-size: 15px;
            font-weight: 500;
            padding: 15px 16px;
            vertical-align: middle;
        }

        .invoice-detail-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .invoice-detail-table tfoot td {
            background: #fff7ef;
            color: #7a270c;
            font-size: 15px;
            font-weight: 800;
            padding: 15px 16px;
        }

        .invoice-detail-empty {
            color: #7a675f !important;
            font-weight: 500 !important;
        }

        .invoice-bill {
            width: 100%;
            height: 100%;
            border: 1px solid rgba(151, 64, 26, 0.18);
            border-radius: 18px;
            background: linear-gradient(180deg, #fff7ef 0%, #fffefa 100%);
            box-shadow: 0 18px 42px rgba(122, 39, 12, 0.14);
            overflow: hidden;
        }

        .invoice-bill__header {
            padding: 18px 20px;
            border-bottom: 1px dashed rgba(151, 64, 26, 0.25);
        }

        .invoice-bill__header h3 {
            margin: 0;
            color: #100d0c;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 0;
        }

        .invoice-bill__body {
            padding: 8px 20px 20px;
        }

        .invoice-bill__row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 12px 0;
            border-bottom: 1px solid rgba(151, 64, 26, 0.1);
            color: #5a2a1b;
            font-weight: 700;
        }

        .invoice-bill__row:last-child {
            border-bottom: 0;
        }

        .invoice-bill__row strong {
            color: #1e1715;
            font-size: 16px;
            white-space: nowrap;
        }

        .invoice-bill__row--total {
            margin-top: 4px;
            padding-top: 16px;
            color: #7a270c;
            font-size: 17px;
        }

        .invoice-bill__row--total strong {
            color: #7a270c;
            font-size: 20px;
        }

        @media (max-width: 991.98px) {
            .invoice-detail-actions {
                justify-content: flex-start;
            }

            .invoice-detail-info-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .invoice-detail-info-grid--compact {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 575.98px) {
            .invoice-detail-page {
                padding-top: 42px;
            }

            .invoice-detail-hero,
            .invoice-detail-section {
                border-radius: 18px;
                padding: 18px;
            }

            .invoice-detail-title h1 {
                font-size: 24px;
            }

            .invoice-detail-info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="invoice-detail-page">
        <div class="invoice-detail-hero mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-lg-8">
                    <div class="invoice-detail-title">
                        <h1>Chi tiết hóa đơn</h1>
                        <span class="invoice-detail-badge invoice-detail-badge--{{ $status['class'] }}">{{ $status['label'] }}</span>
                    </div>
                    <p class="invoice-detail-subtitle">Thông tin hóa đơn, phòng đã đặt, dịch vụ sử dụng và đền bù</p>
                </div>
                <div class="col-lg-4">
                    <div class="invoice-detail-actions">
                        <a href="{{ route('hotel.invoices.index') }}" class="btn btn-primary">Quay Lại</a>
                    </div>
                </div>
            </div>
        </div>

        <section class="invoice-detail-section mb-4">
            <div class="row g-4 align-items-stretch">
                <div class="col-xl-8">
                    <h2>Thông tin hóa đơn</h2>
                    <div class="invoice-detail-info-grid invoice-detail-info-grid--compact">
                        <div class="invoice-detail-card">
                            <div class="invoice-detail-label">Mã hóa đơn</div>
                            <div class="invoice-detail-value">#{{ $invoice->MaHD ?? '--' }}</div>
                        </div>
                        <div class="invoice-detail-card">
                            <div class="invoice-detail-label">Mã đặt phòng</div>
                            <div class="invoice-detail-value">{{ $invoice->MaDatPhong ? '#' . $invoice->MaDatPhong : '--' }}</div>
                        </div>
                        <div class="invoice-detail-card">
                            <div class="invoice-detail-label">Ngày lập</div>
                            <div class="invoice-detail-value">{{ $formatDate($invoice->NgayLapHD) }}</div>
                        </div>
                        <div class="invoice-detail-card">
                            <div class="invoice-detail-label">Khách hàng</div>
                            <div class="invoice-detail-value">{{ $customer->TenKH ?? '--' }}</div>
                        </div>
                        <div class="invoice-detail-card">
                            <div class="invoice-detail-label">Số điện thoại</div>
                            <div class="invoice-detail-value">{{ $customer->SoDienThoai ?? '--' }}</div>
                        </div>
                        <div class="invoice-detail-card">
                            <div class="invoice-detail-label">Nhân viên lập</div>
                            <div class="invoice-detail-value">{{ $employee->TenNV ?? '--' }}</div>
                        </div>
                        <div class="invoice-detail-card">
                            <div class="invoice-detail-label">Ngày nhận phòng</div>
                            <div class="invoice-detail-value">{{ $formatDate($booking?->NgayNhanPhong) }}</div>
                        </div>
                        <div class="invoice-detail-card">
                            <div class="invoice-detail-label">Ngày trả phòng</div>
                            <div class="invoice-detail-value">{{ $formatDate($booking?->NgayTraPhong) }}</div>
                        </div>
                        <div class="invoice-detail-card">
                            <div class="invoice-detail-label">Khuyến mãi</div>
                            <div class="invoice-detail-value">{{ $promotion->TenKM ?? $invoice->MaKM ?? '--' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="invoice-bill">
                        <div class="invoice-bill__header">
                            <h3>Tổng kết thanh toán</h3>
                        </div>
                        <div class="invoice-bill__body">
                            <div class="invoice-bill__row">
                                <span>Tổng tiền phòng</span>
                                <strong>{{ $formatMoney($roomTotal) }}</strong>
                            </div>
                            <div class="invoice-bill__row">
                                <span>Tổng tiền dịch vụ</span>
                                <strong>{{ $formatMoney($serviceTotal) }}</strong>
                            </div>
                            <div class="invoice-bill__row">
                                <span>Tổng đền bù</span>
                                <strong>{{ $formatMoney($damageTotal) }}</strong>
                            </div>
                            <div class="invoice-bill__row invoice-bill__row--total">
                                <span>Tổng tiền</span>
                                <strong>{{ $formatMoney($total) }}</strong>
                            </div>
                            <div class="invoice-bill__row">
                                <span>Đã thanh toán</span>
                                <strong>{{ $formatMoney($paid) }}</strong>
                            </div>
                            <div class="invoice-bill__row">
                                <span>Còn lại</span>
                                <strong>{{ $formatMoney($remaining) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="invoice-detail-section mb-4">
            <h2>Thông tin loại phòng đã đặt</h2>
            <div class="invoice-detail-table-wrap">
                <table class="table invoice-detail-table">
                    <thead>
                        <tr>
                            <th>Loại phòng</th>
                            <th>Số phòng</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roomDetails as $detail)
                            @php
                                $quantity = (int) ($detail->SoLuong ?? 1);
                                $unitPrice = (float) ($detail->DonGia ?? 0);
                                $roomNumbers = $roomNumbersByType->get((string) $detail->MaLoaiPhong, '--');
                            @endphp
                            <tr>
                                <td>{{ $detail?->loaiPhong?->TenLoaiPhong ?? $detail->MoTa ?? 'Tiền phòng' }}</td>
                                <td>{{ $roomNumbers ?: '--' }}</td>
                                <td>{{ $quantity }}</td>
                                <td>{{ $formatMoney($unitPrice) }}</td>
                                <td>{{ $formatMoney($quantity * $unitPrice) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center invoice-detail-empty py-4">Chưa có thông tin loại phòng.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">Tổng tiền phòng</td>
                            <td>{{ $formatMoney($roomTotal) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>

        <div class="row g-4">
            <div class="col-xl-6">
                <section class="invoice-detail-section">
                    <h2>Thông tin dịch vụ sử dụng</h2>
                    <div class="invoice-detail-table-wrap">
                        <table class="table invoice-detail-table">
                            <thead>
                                <tr>
                                    <th>Dịch vụ</th>
                                    <th>Loại dịch vụ</th>
                                    <th>Số lượng</th>
                                    <th>Thời gian</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($serviceDetails as $detail)
                                    @php
                                        $quantity = (int) ($detail->SoLuong ?? $detail?->suDung?->SoLuong ?? 1);
                                        $unitPrice = (float) ($detail->DonGia ?? $detail?->suDung?->dichVu?->GiaDV ?? 0);
                                        $serviceType = $detail?->suDung?->dichVu?->LoaiDVText ?? '--';
                                    @endphp
                                    <tr>
                                        <td>{{ $detail?->suDung?->dichVu?->TenDV ?? $detail->MoTa ?? 'Dịch vụ' }}</td>
                                        <td>{{ $serviceType }}</td>
                                        <td>{{ $quantity }}</td>
                                        <td>{{ $formatDateTime($detail?->suDung?->ThoiGian) }}</td>
                                        <td>{{ $formatMoney($unitPrice) }}</td>
                                        <td>{{ $formatMoney($quantity * $unitPrice) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center invoice-detail-empty py-4">Chưa có dịch vụ sử dụng.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5">Tổng tiền dịch vụ</td>
                                    <td>{{ $formatMoney($serviceTotal) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>
            </div>

            <div class="col-xl-6">
                <section class="invoice-detail-section">
                    <h2>Thông tin đền bù</h2>
                    <div class="invoice-detail-table-wrap">
                        <table class="table invoice-detail-table">
                            <thead>
                                <tr>
                                    <th>Mô tả</th>
                                    <th>Số lượng</th>
                                    <th>Đơn giá</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($damageDetails as $detail)
                                    @php
                                        $quantity = (int) ($detail->SoLuong ?? 1);
                                        $unitPrice = (float) ($detail->DonGia ?? $detail?->denBu?->TienDenBu ?? 0);
                                    @endphp
                                    <tr>
                                        <td>{{ $detail?->denBu?->MoTa ?? $detail->MoTa ?? 'Đền bù hư hỏng' }}</td>
                                        <td>{{ $quantity }}</td>
                                        <td>{{ $formatMoney($unitPrice) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center invoice-detail-empty py-4">Chưa có khoản đền bù.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">Tổng tiền đền bù</td>
                                    <td>{{ $formatMoney($damageTotal) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
