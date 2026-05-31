@php
    $invoice = $payment?->hoaDon;
    $booking = $invoice?->datPhong;
    $customer = $booking?->khachHang;

    $formatDateTime = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y H:i:s') : '--';
    $formatCurrency = fn ($amount) => is_numeric($amount) ? number_format((float) $amount, 0, ',', '.') . ' VNĐ' : '--';

    $paymentMethod = match ((int) ($payment->PhuongThuc ?? -1)) {
        1 => 'Thẻ',
        2 => 'QR Code',
        default => '--',
    };

    $transactionType = match ((int) ($payment->LoaiThanhToan ?? -1)) {
        0 => 'Thanh toán tiền phòng',
        1 => 'Thanh toán trả phòng',
        default => '--',
    };

    $transactionStatus = match ((int) ($payment->TrangThaiGiaoDich ?? -1)) {
        0 => 'Thất bại',
        1 => 'Thành công',
        2 => 'Đang xử lý',
        default => '--',
    };
@endphp

<x-hotel-management.show-page
    title="Chi tiết thanh toán"
    subtitle="Thông tin chi tiết thanh toán"
    :index-route="route('hotel.payments.index')"
>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã thanh toán</div><div class="fw-semibold">{{ $payment->MaTT ?? '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã hóa đơn</div><div class="fw-semibold">{{ $payment->MaHD ?? '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã khách hàng</div><div class="fw-semibold">{{ $customer->MaKH ?? '--' }}</div></div></div>

    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Người thanh toán</div><div class="fw-semibold">{{ $customer->TenKH ?? $payment->DinhDanhNguoiThanhToan ?? '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Định danh người thanh toán</div><div class="fw-semibold">{{ $payment->DinhDanhNguoiThanhToan ?? '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số tiền thanh toán</div><div class="fw-semibold">{{ $formatCurrency($payment->SoTien) }}</div></div></div>

    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Phương thức</div><div class="fw-semibold">{{ $paymentMethod }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Loại thanh toán</div><div class="fw-semibold">{{ $transactionType }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày thanh toán</div><div class="fw-semibold">{{ $formatDateTime($payment->NgayThanhToan) }}</div></div></div>

    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Nhà cung cấp</div><div class="fw-semibold">{{ $payment->NhaCungCap ?? '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Trạng thái giao dịch</div><div class="fw-semibold">{{ $transactionStatus }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã giao dịch</div><div class="fw-semibold">{{ $payment->MaGiaoDich ?? '--' }}</div></div></div>

    <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã giao dịch cổng thanh toán</div><div class="fw-semibold">{{ $payment->MaGiaoDichCongThanhToan ?? '--' }}</div></div></div>
</x-hotel-management.show-page>
