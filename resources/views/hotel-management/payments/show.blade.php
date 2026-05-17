@php
    $invoice = $payment?->hoaDon;
    $booking = $invoice?->datPhong;
    $customer = $booking?->khachHang;
    $formatDateTime = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y H:i:s') : '--';
    $formatCurrency = fn ($amount) => is_numeric($amount) ? number_format((float) $amount, 0, ',', '.') . ' VNĐ' : '--';
    $paymentType = match ((int) $payment->LoaiThanhToan) {
        0 => 'Đặt cọc',
        1 => 'Thanh toán checkout',
        default => '--',
    };
    $invoiceStatus = match ((int) ($invoice?->TrangThai ?? -1)) {
        0 => 'Chưa thanh toán',
        1 => 'Đã thanh toán',
        3 => 'Đã hủy',
        default => 'Không xác định',
    };
@endphp

<x-hotel-management.show-page
    title="Chi tiết thanh toán"
    subtitle="Thông tin chi tiết thanh toán"
    :index-route="route('hotel.payments.index')"
>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã thanh toán</div><div class="fw-semibold">{{ $payment->MaTT ?? '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã đặt phòng</div><div class="fw-semibold">{{ $booking->MaDatPhong ?? '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Người thanh toán</div><div class="fw-semibold">{{ $customer->TenKH ?? $payment->DinhDanhNguoiThanhToan ?? '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số tiền</div><div class="fw-semibold">{{ $formatCurrency($payment->SoTien) }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Loại thanh toán</div><div class="fw-semibold">{{ $paymentType }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày thanh toán</div><div class="fw-semibold">{{ $formatDateTime($payment->NgayThanhToan) }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Trạng thái hóa đơn</div><div class="fw-semibold">{{ $invoiceStatus }}</div></div></div>
</x-hotel-management.show-page>
