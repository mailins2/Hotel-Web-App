@php
    $customer = $booking?->khachHang;
    $invoice = $booking?->hoaDon;
    $roomNumbers = $booking->chiTietDatPhong
        ->map(fn ($detail) => $detail?->phong?->SoPhong)
        ->filter()
        ->values()
        ->implode(', ');
    $roomTypes = $booking->chiTietDatPhong
        ->map(fn ($detail) => $detail?->phong?->loaiPhong?->TenLoaiPhong)
        ->filter()
        ->unique()
        ->values()
        ->implode(', ');
    $formatDate = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y') : '--';
    $formatDateTime = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y H:i:s') : '--';
    $formatMoney = fn ($amount) => is_numeric($amount) ? number_format((float) $amount, 0, ',', '.') . ' VNĐ' : '--';
    $stayDays = $booking->NgayNhanPhong && $booking->NgayTraPhong
        ? max(\Carbon\Carbon::parse($booking->NgayNhanPhong)->diffInDays(\Carbon\Carbon::parse($booking->NgayTraPhong)), 1)
        : null;
    $status = match ((int) $booking->TinhTrang) {
        \App\Models\DatPhong::HOLD => 'Chờ xác nhận',
        \App\Models\DatPhong::CONFIRMED => 'Đã xác nhận',
        \App\Models\DatPhong::CHECKED_IN => 'Đang ở',
        \App\Models\DatPhong::CHECKED_OUT => 'Đã trả phòng',
        \App\Models\DatPhong::CANCELLED => 'Đã hủy',
        default => 'Không xác định',
    };
@endphp

<x-hotel-management.show-page
    title="Chi tiết đặt phòng"
    subtitle="Thông tin chi tiết đặt phòng"
    :index-route="route('hotel.bookings.index')"
>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã đặt phòng</div><div class="fw-semibold">{{ $booking->MaDatPhong ?? '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày đặt</div><div class="fw-semibold">{{ $formatDateTime($booking->NgayDat) }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tình trạng</div><div class="fw-semibold">{{ $status }}</div></div></div>

    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Khách hàng</div><div class="fw-semibold">{{ $customer->TenKH ?? '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số điện thoại</div><div class="fw-semibold">{{ $customer->SoDienThoai ?? '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Email</div><div class="fw-semibold">{{ $customer?->taiKhoan?->Email ?? '--' }}</div></div></div>

    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày nhận phòng</div><div class="fw-semibold">{{ $formatDate($booking->NgayNhanPhong) }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày trả phòng</div><div class="fw-semibold">{{ $formatDate($booking->NgayTraPhong) }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số đêm</div><div class="fw-semibold">{{ $stayDays ? $stayDays . ' đêm' : '--' }}</div></div></div>

    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Phòng</div><div class="fw-semibold">{{ $roomNumbers ?: '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Loại phòng</div><div class="fw-semibold">{{ $roomTypes ?: '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số lượng</div><div class="fw-semibold">{{ $booking->SoLuong ?? 0 }}</div></div></div>

    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã hóa đơn</div><div class="fw-semibold">{{ $invoice?->MaHD ? '#' . $invoice->MaHD : '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tổng tiền</div><div class="fw-semibold">{{ $formatMoney($invoice?->TongTien) }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Đã thanh toán</div><div class="fw-semibold">{{ $formatMoney($invoice?->DaThanhToan ?? $invoice?->thanhToans?->sum('SoTien')) }}</div></div></div>
</x-hotel-management.show-page>
