@php
    $booking = $review?->datPhong;
    $customer = $booking?->khachHang;
    $roomTypeNames = $booking?->chiTietDatPhong
        ? $booking->chiTietDatPhong
            ->map(fn ($detail) => $detail?->phong?->loaiPhong?->TenLoaiPhong)
            ->filter()
            ->unique()
            ->values()
            ->implode(', ')
        : '';
    $formatDate = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y') : '--';
@endphp

<x-hotel-management.show-page
    title="Chi tiết đánh giá"
    subtitle="Thông tin chi tiết đánh giá"
    :index-route="route('hotel.reviews.index')"
>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã đánh giá</div><div class="fw-semibold">{{ $review->MaDG ?? '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã đặt phòng</div><div class="fw-semibold">{{ $review->MaDatPhong ?? '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã khách hàng</div><div class="fw-semibold">{{ $customer->MaKH ?? $booking->MaKH ?? '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên khách hàng</div><div class="fw-semibold">{{ $customer->TenKH ?? '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên loại phòng</div><div class="fw-semibold">{{ $roomTypeNames ?: '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số sao</div><div class="fw-semibold">{{ $review->Sao ? $review->Sao . ' sao' : '--' }}</div></div></div>
    <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Nội dung đánh giá</div><div class="fw-semibold">{{ $review->MoTa ?? '--' }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày đánh giá</div><div class="fw-semibold">{{ $formatDate($review->NgayDanhGia) }}</div></div></div>
</x-hotel-management.show-page>
