@php
    $formatDate = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y') : '--';
    $formatPromotionType = fn ($type) => (int) $type === 1 ? 'Hội viên' : 'Chung';
@endphp

<x-hotel-management.show-page
    title="Chi tiết khuyến mãi"
    subtitle="Thông tin chi tiết khuyến mãi"
    :index-route="route('hotel.promotions.index')"
    :edit-route="route('hotel.promotions.edit', ['recordId' => $promotion->MaKM])"
>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã khuyến mãi</div><div class="fw-semibold">{{ $promotion->MaKM ?? '--' }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên chương trình</div><div class="fw-semibold">{{ $promotion->TenKM ?? '--' }}</div></div></div>
    <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mô tả</div><div class="fw-semibold">{{ $promotion->MoTa ?? '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Điểm yêu cầu</div><div class="fw-semibold">{{ $promotion->Diem ?? 0 }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày bắt đầu</div><div class="fw-semibold">{{ $formatDate($promotion->NgayBatDau) }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày kết thúc</div><div class="fw-semibold">{{ $formatDate($promotion->NgayKetThuc) }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Phần trăm giảm giá</div><div class="fw-semibold">{{ $promotion->PhanTramGiamGia !== null ? (float) $promotion->PhanTramGiamGia . '%' : '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Loại khuyến mãi</div><div class="fw-semibold">{{ $formatPromotionType($promotion->LoaiKM ?? 0) }}</div></div></div>
</x-hotel-management.show-page>
