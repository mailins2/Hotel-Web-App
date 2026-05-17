@php
    $booking = $invoice?->datPhong;
    $customer = $booking?->khachHang;
    $employee = $invoice?->nhanVien;
    $promotion = $invoice?->khuyenMai;
    $formatDate = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y') : '--';
    $formatMoney = fn ($amount) => is_numeric($amount) ? number_format((float) $amount, 0, ',', '.') . ' VNĐ' : '--';
    $paid = $invoice->DaThanhToan ?? $invoice->thanhToans->sum('SoTien');
    $remaining = max((float) ($invoice->TongTien ?? 0) - (float) ($paid ?? 0), 0);
    $status = match ((int) $invoice->TrangThai) {
        0 => 'Chưa thanh toán',
        1 => 'Đã thanh toán',
        3 => 'Đã hủy',
        default => 'Không xác định',
    };
@endphp

<x-hotel-management.show-page
    title="Chi tiết hóa đơn"
    subtitle="Thông tin chi tiết hóa đơn"
    :index-route="route('hotel.invoices.index')"
>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã hóa đơn</div><div class="fw-semibold">{{ $invoice->MaHD ?? '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã đặt phòng</div><div class="fw-semibold">{{ $invoice->MaDatPhong ?? '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày lập</div><div class="fw-semibold">{{ $formatDate($invoice->NgayLapHD) }}</div></div></div>

    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Khách hàng</div><div class="fw-semibold">{{ $customer->TenKH ?? '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Nhân viên</div><div class="fw-semibold">{{ $employee->TenNV ?? '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Khuyến mãi</div><div class="fw-semibold">{{ $promotion->TenKM ?? $invoice->MaKM ?? '--' }}</div></div></div>

    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tổng tiền</div><div class="fw-semibold">{{ $formatMoney($invoice->TongTien) }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Đã thanh toán</div><div class="fw-semibold">{{ $formatMoney($paid) }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Còn lại</div><div class="fw-semibold">{{ $formatMoney($remaining) }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Trạng thái</div><div class="fw-semibold">{{ $status }}</div></div></div>

    <div class="col-md-12 mb-4">
        <div class="border rounded p-3 h-100">
            <div class="text-muted small mb-3">Chi tiết hóa đơn</div>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Nội dung</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoice->chiTietHoaDons as $detail)
                            @php
                                $label = $detail?->loaiPhong?->TenLoaiPhong
                                    ?? $detail?->suDung?->dichVu?->TenDV
                                    ?? $detail?->denBu?->MoTa
                                    ?? 'Chi tiết';
                                $quantity = $detail->SoLuong ?? 1;
                                $unitPrice = $detail->DonGia ?? $detail->ThanhTien ?? 0;
                                $lineTotal = $detail->ThanhTien ?? ((float) $quantity * (float) $unitPrice);
                            @endphp
                            <tr>
                                <td>{{ $label }}</td>
                                <td>{{ $quantity }}</td>
                                <td>{{ $formatMoney($unitPrice) }}</td>
                                <td>{{ $formatMoney($lineTotal) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Chưa có chi tiết hóa đơn.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-hotel-management.show-page>
