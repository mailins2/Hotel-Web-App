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
    <style>
        .hm-promotion-images {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .hm-promotion-images__item {
            min-width: 0;
        }

        .hm-promotion-images__img {
            width: 100%;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            object-position: center;
            display: block;
        }

        @media (min-width: 768px) {
            .hm-promotion-images {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
    </style>

    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã khuyến mãi</div><div class="fw-semibold">{{ $promotion->MaKM ?? '--' }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên chương trình</div><div class="fw-semibold">{{ $promotion->TenKM ?? '--' }}</div></div></div>
    <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mô tả</div><div class="fw-semibold">{{ $promotion->MoTa ?? '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Điểm yêu cầu</div><div class="fw-semibold">{{ $promotion->Diem ?? 0 }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày bắt đầu</div><div class="fw-semibold">{{ $formatDate($promotion->NgayBatDau) }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày kết thúc</div><div class="fw-semibold">{{ $formatDate($promotion->NgayKetThuc) }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Phần trăm giảm giá</div><div class="fw-semibold">{{ $promotion->PhanTramGiamGia !== null ? (float) $promotion->PhanTramGiamGia . '%' : '--' }}</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Loại khuyến mãi</div><div class="fw-semibold">{{ $formatPromotionType($promotion->LoaiKM ?? 0) }}</div></div></div>
    <div class="col-md-12 mb-4">
        <div class="border rounded p-3 h-100">
            <div class="text-muted small mb-3">Ảnh khuyến mãi</div>
            <div class="hm-promotion-images">
                @forelse($promotion->hinhs as $image)
                    <div class="hm-promotion-images__item">
                        <img
                            src="{{ $image->Url ?: 'https://placehold.co/800x800/f3f4f6/9ca3af?text=Promotion' }}"
                            alt="Ảnh khuyến mãi {{ $loop->iteration }}"
                            class="hm-promotion-images__img rounded border bg-light"
                            onerror="this.onerror=null;this.src='https://placehold.co/800x800/f3f4f6/9ca3af?text=Promotion';"
                        >
                    </div>
                @empty
                    <div class="text-muted">Chưa có ảnh khuyến mãi.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-hotel-management.show-page>
