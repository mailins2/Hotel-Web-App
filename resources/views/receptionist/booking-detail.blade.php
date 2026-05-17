<x-app-layout :assets="['animation']">
    @php
        $statusLabels = [
            \App\Models\DatPhong::HOLD => 'Đang giữ chỗ',
            \App\Models\DatPhong::CONFIRMED => 'Đã đặt',
            \App\Models\DatPhong::CHECKED_IN => 'Đang sử dụng',
            \App\Models\DatPhong::CHECKED_OUT => 'Đã trả phòng',
            \App\Models\DatPhong::CANCELLED => 'Đã hủy',
        ];

        $statusClasses = [
            \App\Models\DatPhong::HOLD => 'bd-badge--hold',
            \App\Models\DatPhong::CONFIRMED => 'bd-badge--booked',
            \App\Models\DatPhong::CHECKED_IN => 'bd-badge--using',
            \App\Models\DatPhong::CHECKED_OUT => 'bd-badge--done',
            \App\Models\DatPhong::CANCELLED => 'bd-badge--cancelled',
        ];

        $formatDate = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y') : '--';
        $formatDateTime = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y H:i') : '--';
        $formatMoney = fn ($amount) => number_format((float) ($amount ?? 0), 0, ',', '.') . ' VND';

        $checkIn = $booking->NgayNhanPhong ? \Carbon\Carbon::parse($booking->NgayNhanPhong) : null;
        $checkOut = $booking->NgayTraPhong ? \Carbon\Carbon::parse($booking->NgayTraPhong) : null;
        $nights = ($checkIn && $checkOut) ? max($checkIn->diffInDays($checkOut), 1) : 0;
        $customer = $booking->khachHang;
        $account = $customer?->taiKhoan;
        $invoice = $booking->hoaDon;
        $paidAmount = (float) ($invoice?->DaThanhToan ?? $invoice?->thanhToans?->sum('SoTien') ?? 0);
        $totalAmount = (float) ($invoice?->TongTien ?? 0);
        $remainingAmount = max($totalAmount - $paidAmount, 0);
        $roomDetails = $booking->chiTietDatPhong ?? collect();
        $serviceUsages = $booking->suDungDichVu ?? collect();
    @endphp

    <style>
        .bd-shell { padding-top: 4.75rem; }

        .bd-hero,
        .bd-section {
            border: 1px solid rgba(217, 119, 6, 0.14);
            border-radius: 28px;
            background: #fffdfa;
            box-shadow: 0 24px 60px rgba(148, 82, 24, 0.08);
        }

        .bd-hero {
            padding: 1.75rem;
            margin-bottom: 1.5rem;
        }

        .bd-section {
            padding: 1.35rem;
            height: 100%;
        }

        .bd-label {
            color: #8b5e3c;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .bd-value {
            color: #2f1d12;
            font-size: 1.05rem;
            font-weight: 600;
            margin-top: 0.35rem;
            overflow-wrap: anywhere;
        }

        .bd-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.45rem 0.9rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.82rem;
        }

        .bd-badge--hold { background: #ffedd5; color: #9a3412; }
        .bd-badge--booked { background: #fef3c7; color: #92400e; }
        .bd-badge--using { background: #dbeafe; color: #1d4ed8; }
        .bd-badge--done { background: #dcfce7; color: #166534; }
        .bd-badge--cancelled { background: #fee2e2; color: #991b1b; }

        .bd-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }

        .bd-info-card {
            border: 1px solid rgba(217, 119, 6, 0.14);
            border-radius: 18px;
            background: #fff;
            padding: 1rem;
        }

        .bd-list {
            display: grid;
            gap: 0.85rem;
        }

        .bd-list-item {
            border: 1px solid rgba(217, 119, 6, 0.14);
            border-radius: 18px;
            background: #fff;
            padding: 1rem;
        }

        .bd-list-line {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: flex-start;
        }

        .bd-note {
            border-radius: 18px;
            padding: 1rem 1.1rem;
            background: linear-gradient(135deg, #fff5ea, #fffdf9);
            border: 1px dashed rgba(191, 102, 38, 0.28);
            color: #7a4b27;
        }
    </style>

    <div class="bd-shell">
        <div class="bd-hero">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                        <h2 class="mb-0">Chi tiết đặt phòng #{{ $booking->MaDatPhong }}</h2>
                        <span class="bd-badge {{ $statusClasses[(int) $booking->TinhTrang] ?? 'bd-badge--booked' }}">
                            {{ $statusLabels[(int) $booking->TinhTrang] ?? 'Không xác định' }}
                        </span>
                    </div>
                    <p class="text-muted mb-0">Dữ liệu được lấy trực tiếp từ đặt phòng, phòng, khách hàng và hóa đơn.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('reception.bookings.index') }}" class="btn btn-light">Danh sách đặt phòng</a>
                    <a href="{{ route('reception.dashboard') }}" class="btn btn-primary">Quay lại sơ đồ phòng</a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-6">
                <div class="bd-section">
                    <h4 class="mb-3">Thông tin khách hàng đặt</h4>
                    <div class="bd-info-grid">
                        <div class="bd-info-card"><div class="bd-label">Mã khách hàng</div><div class="bd-value">{{ $customer?->MaKH ?? '--' }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">Tên khách hàng</div><div class="bd-value">{{ $customer?->TenKH ?? '--' }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">Số điện thoại</div><div class="bd-value">{{ $customer?->SoDienThoai ?? '--' }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">Email</div><div class="bd-value">{{ $account?->Email ?? '--' }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">CCCD</div><div class="bd-value">{{ $customer?->CCCD ?? '--' }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">Địa chỉ</div><div class="bd-value">{{ $customer?->DiaChi ?? '--' }}</div></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="bd-section">
                    <h4 class="mb-3">Thông tin đặt phòng</h4>
                    <div class="bd-info-grid">
                        <div class="bd-info-card"><div class="bd-label">Mã đặt phòng</div><div class="bd-value">#{{ $booking->MaDatPhong }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">Ngày đặt phòng</div><div class="bd-value">{{ $formatDate($booking->NgayDat) }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">Ngày nhận phòng</div><div class="bd-value">{{ $formatDate($booking->NgayNhanPhong) }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">Ngày trả phòng</div><div class="bd-value">{{ $formatDate($booking->NgayTraPhong) }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">Thời gian lưu trú</div><div class="bd-value">{{ $nights }} đêm</div></div>
                        <div class="bd-info-card"><div class="bd-label">Số lượng khách</div><div class="bd-value">{{ $booking->SoLuong ?? 0 }} khách</div></div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="bd-section">
                    <h4 class="mb-3">Phòng đã đặt</h4>
                    @if($roomDetails->isNotEmpty())
                        <div class="bd-list">
                            @foreach($roomDetails as $detail)
                                @php
                                    $room = $detail->phong;
                                    $roomType = $room?->loaiPhong;
                                    $price = $roomType?->GiaGiam ?? $roomType?->GiaPhong;
                                @endphp
                                <div class="bd-list-item">
                                    <div class="bd-list-line">
                                        <div>
                                            <div class="bd-label">Phòng {{ $room?->SoPhong ?? '--' }}</div>
                                            <div class="bd-value">{{ $roomType?->TenLoaiPhong ?? 'Chưa có loại phòng' }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="bd-label">Giá phòng</div>
                                            <div class="bd-value">{{ $price !== null ? $formatMoney($price) . ' / đêm' : '--' }}</div>
                                        </div>
                                    </div>
                                    <div class="bd-info-grid mt-3">
                                        <div><div class="bd-label">Mã phòng</div><div class="bd-value">{{ $room?->MaPhong ?? '--' }}</div></div>
                                        <div><div class="bd-label">Mã loại phòng</div><div class="bd-value">{{ $roomType?->MaLoaiPhong ?? '--' }}</div></div>
                                        <div><div class="bd-label">Người lớn tối đa</div><div class="bd-value">{{ $roomType?->NguoiLon ?? 0 }}</div></div>
                                        <div><div class="bd-label">Trẻ em tối đa</div><div class="bd-value">{{ $roomType?->TreEm ?? 0 }}</div></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bd-note">Chưa có phòng nào trong đặt phòng này.</div>
                    @endif
                </div>
            </div>

            <div class="col-xl-6">
                <div class="bd-section">
                    <h4 class="mb-3">Hóa đơn</h4>
                    <div class="bd-info-grid">
                        <div class="bd-info-card"><div class="bd-label">Mã hóa đơn</div><div class="bd-value">{{ $invoice?->MaHD ? '#' . $invoice->MaHD : '--' }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">Ngày lập</div><div class="bd-value">{{ $formatDate($invoice?->NgayLapHD) }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">Tổng tiền</div><div class="bd-value">{{ $formatMoney($totalAmount) }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">Đã thanh toán</div><div class="bd-value">{{ $formatMoney($paidAmount) }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">Còn lại</div><div class="bd-value">{{ $formatMoney($remainingAmount) }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">Khuyến mãi</div><div class="bd-value">{{ $invoice?->khuyenMai?->TenKM ?? ($invoice?->MaKM ?? '--') }}</div></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="bd-section">
                    <h4 class="mb-3">Dịch vụ đã sử dụng</h4>
                    @if($serviceUsages->isNotEmpty())
                        <div class="bd-list">
                            @foreach($serviceUsages as $usage)
                                <div class="bd-list-item">
                                    <div class="bd-list-line">
                                        <div>
                                            <div class="bd-label">{{ $usage->dichVu?->TenDV ?? 'Dịch vụ' }}</div>
                                            <div class="bd-value">Số lượng: {{ $usage->SoLuong ?? 0 }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="bd-label">Thời gian</div>
                                            <div class="bd-value">{{ $formatDateTime($usage->ThoiGian) }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bd-note">Chưa có dịch vụ nào được đăng ký cho đặt phòng này.</div>
                    @endif
                </div>
            </div>

            <div class="col-12">
                <div class="bd-section">
                    <h4 class="mb-3">Chi tiết hóa đơn</h4>
                    @if($invoice && $invoice->chiTietHoaDons->isNotEmpty())
                        <div class="bd-list">
                            @foreach($invoice->chiTietHoaDons as $item)
                                @php
                                    $itemName = $item->loaiPhong?->TenLoaiPhong
                                        ?? $item->suDung?->dichVu?->TenDV
                                        ?? $item->denBu?->MoTa
                                        ?? $item->MoTa
                                        ?? 'Khoản thu';
                                    $lineTotal = (float) $item->SoLuong * (float) $item->DonGia;
                                @endphp
                                <div class="bd-list-item">
                                    <div class="bd-list-line">
                                        <div>
                                            <div class="bd-label">{{ $itemName }}</div>
                                            <div class="bd-value">{{ $item->MoTa ?? 'Chi tiết #' . $item->MaCTHD }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="bd-label">{{ $item->SoLuong }} x {{ $formatMoney($item->DonGia) }}</div>
                                            <div class="bd-value">{{ $formatMoney($lineTotal) }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bd-note">Chưa có chi tiết hóa đơn để hiển thị.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
