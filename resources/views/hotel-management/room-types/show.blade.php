<x-hotel-management.show-page
    title="Chi tiết loại phòng"
    subtitle="Thông tin loại phòng"
    :index-route="route('hotel.room-types.index')"
    :edit-route="route('hotel.room-types.edit', ['recordId' => $roomType->MaLoaiPhong])"
>
    <style>
        .hm-room-type-images {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .hm-room-type-images__item {
            min-width: 0;
        }

        .hm-room-type-images__img {
            width: 100%;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            object-position: center;
            display: block;
        }

        .hm-room-type-description {
            white-space: pre-line;
        }

        .hm-room-type-amenities {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .hm-room-type-amenity {
            display: inline-flex;
            align-items: center;
            padding: 0.55rem 0.9rem;
            border-radius: 999px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            font-weight: 600;
        }

        @media (min-width: 768px) {
            .hm-room-type-images {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
    </style>

    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã loại phòng</div><div class="fw-semibold">{{ $roomType->MaLoaiPhong ?? '--' }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên loại phòng</div><div class="fw-semibold">{{ $roomType->TenLoaiPhong ?? '--' }}</div></div></div>
    <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mô tả</div><div class="fw-semibold hm-room-type-description">{{ $roomType->Mota ?? '--' }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Người lớn</div><div class="fw-semibold">{{ $roomType->NguoiLon ?? '--' }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Trẻ em</div><div class="fw-semibold">{{ $roomType->TreEm ?? 0 }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã khuyến mãi</div><div class="fw-semibold">{{ $roomType->MaKM ?: 'chưa có khuyến mãi' }}</div></div></div>
    <div class="col-md-12 mb-4">
        <div class="border rounded p-3 h-100">
            <div class="text-muted small mb-3">Tiện nghi phòng</div>
            <div class="hm-room-type-amenities">
                @forelse($roomType->tienNghis as $amenity)
                    <span class="hm-room-type-amenity">{{ $amenity->TenTienNghi ?? 'Tiện nghi' }}</span>
                @empty
                    <div class="text-muted">Chưa có tiện nghi phòng.</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-12 mb-4">
        <div class="border rounded p-3 h-100">
            <div class="text-muted small mb-3">Ảnh phòng</div>
            <div class="hm-room-type-images">
                @forelse($roomType->hinhs as $image)
                    <div class="hm-room-type-images__item">
                        <img
                            src="{{ $image->Url ?: 'https://placehold.co/800x800/f3f4f6/9ca3af?text=Room+Type' }}"
                            alt="Ảnh phòng {{ $loop->iteration }}"
                            class="hm-room-type-images__img rounded border bg-light"
                            onerror="this.onerror=null;this.src='https://placehold.co/800x800/f3f4f6/9ca3af?text=Room+Type';"
                        >
                    </div>
                @empty
                    <div class="text-muted">Chưa có ảnh phòng.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-hotel-management.show-page>
