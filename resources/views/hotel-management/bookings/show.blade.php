@php
    $customer = $booking?->khachHang;
    $invoice = $booking?->hoaDon;
    $details = collect($booking?->chiTietDatPhong ?? []);
    $paidAmount = (float) ($invoice?->DaThanhToan ?? $invoice?->thanhToans?->sum('SoTien') ?? 0);
    $formatDate = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y') : '--';
    $formatDateTime = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y H:i:s') : '--';
    $formatMoney = fn ($amount) => is_numeric($amount) ? number_format((float) $amount, 0, ',', '.') . ' VNĐ' : '--';
    $stayDays = $booking->NgayNhanPhong && $booking->NgayTraPhong
        ? max(\Carbon\Carbon::parse($booking->NgayNhanPhong)->diffInDays(\Carbon\Carbon::parse($booking->NgayTraPhong)), 1)
        : null;
    $status = match ((int) $booking->TinhTrang) {
        \App\Models\DatPhong::HOLD => ['label' => 'Chờ xác nhận', 'class' => 'warning'],
        \App\Models\DatPhong::CONFIRMED => ['label' => 'Đã xác nhận', 'class' => 'info'],
        \App\Models\DatPhong::CHECKED_IN => ['label' => 'Đang ở', 'class' => 'success'],
        \App\Models\DatPhong::CHECKED_OUT => ['label' => 'Đã trả phòng', 'class' => 'muted'],
        \App\Models\DatPhong::CANCELLED => ['label' => 'Đã hủy', 'class' => 'danger'],
        default => ['label' => 'Không xác định', 'class' => 'muted'],
    };
@endphp

<x-app-layout :assets="['animation']">
    <style>
        .booking-detail-page {
            color: #211816;
            padding-top: 56px;
        }

        .booking-detail-hero,
        .booking-detail-section {
            background: #fffefa;
            border: 1px solid rgba(151, 64, 26, 0.15);
            box-shadow: 0 20px 50px rgba(122, 39, 12, 0.14);
        }

        .booking-detail-hero {
            border-radius: 28px;
            padding: 26px 28px;
        }

        .booking-detail-title {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
        }

        .booking-detail-title h1 {
            margin: 0;
            color: #090706;
            font-size: 30px;
            font-weight: 700;
            letter-spacing: 0;
        }

        .booking-detail-subtitle {
            margin: 8px 0 0;
            color: #6b5a54;
            font-size: 16px;
        }

        .booking-detail-badge {
            display: inline-flex;
            align-items: center;
            min-height: 28px;
            padding: 5px 13px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .booking-detail-badge--success { background: #dcfce7; color: #166534; }
        .booking-detail-badge--info { background: #e0f2fe; color: #0369a1; }
        .booking-detail-badge--warning { background: #fef3c7; color: #92400e; }
        .booking-detail-badge--muted { background: #eceff3; color: #475569; }
        .booking-detail-badge--danger { background: #fee2e2; color: #991b1b; }

        .booking-detail-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 10px;
        }

        .booking-detail-actions .btn {
            border-radius: 5px;
            min-height: 42px;
            padding: 10px 18px;
            font-weight: 600;
        }

        .booking-detail-actions .btn-primary {
            background: #7a270c;
            border-color: #6f1d01;
            color: #fff;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.18);
        }

        .booking-detail-actions .btn-primary:hover {
            background: #5f1b08;
            border-color: #5f1b08;
        }

        .booking-detail-actions .btn-light {
            background: #f1f0f4;
            border-color: #e4e1e8;
            color: #5a2a1b;
        }

        .booking-detail-label {
            margin-bottom: 9px;
            color: #8b3a1d;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .booking-detail-value {
            color: #1e1715;
            font-size: 16px;
            font-weight: 500;
            line-height: 1.45;
        }

        .booking-detail-section {
            border-radius: 24px;
            height: 100%;
            padding: 22px;
            margin-bottom: 20px;
        }

        .booking-detail-section h2 {
            margin: 0 0 18px;
            color: #100d0c;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0;
        }

        .booking-detail-info-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .booking-detail-info-card {
            border: 1px solid rgba(151, 64, 26, 0.13);
            border-radius: 13px;
            min-height: 78px;
            padding: 15px 16px;
            background: #fffefa;
            box-shadow: 0 12px 28px rgba(122, 39, 12, 0.09);
        }

        .booking-detail-table-wrap {
            overflow-x: auto;
            border: 1px solid rgba(151, 64, 26, 0.13);
            border-radius: 13px;
            box-shadow: 0 12px 28px rgba(122, 39, 12, 0.08);
        }

        .booking-detail-table {
            min-width: 820px;
            margin: 0;
            color: #271b18;
        }

        .booking-detail-table thead th {
            background: #fffaf6;
            border-bottom: 1px solid rgba(151, 64, 26, 0.13);
            color: #8b3a1d;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0;
            padding: 15px 16px;
            text-transform: uppercase;
        }

        .booking-detail-table tbody td {
            border-bottom: 1px solid rgba(151, 64, 26, 0.1);
            color: #241917;
            font-size: 15px;
            font-weight: 500;
            padding: 15px 16px;
            vertical-align: middle;
        }

        .booking-detail-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .booking-detail-table .text-muted {
            color: #7a675f !important;
            font-weight: 500;
        }

        @media (max-width: 991.98px) {
            .booking-detail-actions {
                justify-content: flex-start;
            }

            .booking-detail-info-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 575.98px) {
            .booking-detail-hero,
            .booking-detail-section {
                border-radius: 18px;
                padding: 18px;
            }

            .booking-detail-title h1 {
                font-size: 24px;
            }

            .booking-detail-info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="booking-detail-page">
        <div class="booking-detail-hero mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-lg-7">
                    <div class="booking-detail-title">
                        <h1>Chi tiết đặt phòng</h1>
                        <span class="booking-detail-badge booking-detail-badge--{{ $status['class'] }}">{{ $status['label'] }}</span>
                    </div>
                    <p class="booking-detail-subtitle">Thông tin chi tiết đặt phòng tại khách sạn</p>
                </div>
                <div class="col-lg-5">
                    <div class="booking-detail-actions">
                        <a href="{{ route('hotel.bookings.index') }}" class="btn btn-light">Danh Sách Đặt Phòng</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-xl-5">
                <section class="booking-detail-section">
                    <h2>Thông tin khách hàng</h2>
                    <div class="booking-detail-info-grid">
                        <div class="booking-detail-info-card">
                            <div class="booking-detail-label">Mã khách hàng</div>
                            <div class="booking-detail-value">{{ $customer?->MaKH ? '#' . $customer->MaKH : '--' }}</div>
                        </div>
                        <div class="booking-detail-info-card">
                            <div class="booking-detail-label">Tên khách hàng</div>
                            <div class="booking-detail-value">{{ $customer->TenKH ?? '--' }}</div>
                        </div>
                        <div class="booking-detail-info-card">
                            <div class="booking-detail-label">Số điện thoại</div>
                            <div class="booking-detail-value">{{ $customer->SoDienThoai ?? '--' }}</div>
                        </div>
                        <div class="booking-detail-info-card">
                            <div class="booking-detail-label">Email</div>
                            <div class="booking-detail-value">{{ $customer?->taiKhoan?->Email ?? '--' }}</div>
                        </div>
                        <div class="booking-detail-info-card">
                            <div class="booking-detail-label">CCCD</div>
                            <div class="booking-detail-value">{{ $customer->CCCD ?? '--' }}</div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-xl-7">
                <section class="booking-detail-section">
                    <h2>Thông tin phòng đã đặt</h2>
                    <div class="booking-detail-table-wrap">
                        <table class="table booking-detail-table">
                            <thead>
                                <tr>
                                    <th>Số phòng</th>
                                    <th>Loại phòng</th>
                                    <th>Sức chứa</th>
                                    <th>Giá phòng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($details as $detail)
                                    @php
                                        $room = $detail?->phong;
                                        $roomType = $room?->loaiPhong;
                                        $capacity = $roomType
                                            ? trim(($roomType->NguoiLon ?? 0) . ' người lớn' . (($roomType->TreEm ?? 0) > 0 ? ', ' . $roomType->TreEm . ' trẻ em' : ''))
                                            : '--';
                                        $roomPrice = $roomType ? $roomType->giaSauKhuyenMai($booking->NgayNhanPhong) : null;
                                    @endphp
                                    <tr>
                                        <td>{{ $room->SoPhong ?? '--' }}</td>
                                        <td>{{ $roomType->TenLoaiPhong ?? '--' }}</td>
                                        <td>{{ $capacity }}</td>
                                        <td>{{ $roomPrice !== null ? $formatMoney($roomPrice) . ' / đêm' : '--' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Chưa có phòng được gán cho đặt phòng này.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>

        <section class="booking-detail-section">
            <h2>Thông tin đặt phòng</h2>
            <div class="row g-3">
                <div class="col-md-6 col-xl-3">
                    <div class="booking-detail-info-card">
                        <div class="booking-detail-label">Mã đặt phòng</div>
                        <div class="booking-detail-value">#{{ $booking->MaDatPhong ?? '--' }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="booking-detail-info-card">
                        <div class="booking-detail-label">Ngày đặt phòng</div>
                        <div class="booking-detail-value">{{ $formatDateTime($booking->NgayDat) }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="booking-detail-info-card">
                        <div class="booking-detail-label">Ngày nhận phòng</div>
                        <div class="booking-detail-value">{{ $formatDate($booking->NgayNhanPhong) }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="booking-detail-info-card">
                        <div class="booking-detail-label">Ngày trả phòng</div>
                        <div class="booking-detail-value">{{ $formatDate($booking->NgayTraPhong) }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="booking-detail-info-card">
                        <div class="booking-detail-label">Thời gian lưu trú</div>
                        <div class="booking-detail-value">{{ $stayDays ? $stayDays . ' đêm' : '--' }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="booking-detail-info-card">
                        <div class="booking-detail-label">Tiền đặt cọc</div>
                        <div class="booking-detail-value">{{ $formatMoney($paidAmount) }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="booking-detail-info-card">
                        <div class="booking-detail-label">Trạng thái booking</div>
                        <div class="booking-detail-value">{{ $status['label'] }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="booking-detail-info-card">
                        <div class="booking-detail-label">Số lượng</div>
                        <div class="booking-detail-value">{{ $booking->SoLuong ?? $details->count() }}</div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
