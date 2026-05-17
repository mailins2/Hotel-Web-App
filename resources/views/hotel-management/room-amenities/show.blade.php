<x-hotel-management.show-page
    title="Chi tiết tiện nghi phòng"
    subtitle="Thông tin tiện nghi trong hệ thống"
    :index-route="route('hotel.room-amenities.index')"
    :edit-route="route('hotel.room-amenities.edit', ['recordId' => $amenity->MaTienNghi])"
>
    <div class="col-md-6 mb-4">
        <div class="border rounded p-3 h-100">
            <div class="text-muted small mb-1">Mã tiện nghi</div>
            <div class="fw-semibold">{{ $amenity->MaTienNghi ?? '--' }}</div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="border rounded p-3 h-100">
            <div class="text-muted small mb-1">Tên tiện nghi</div>
            <div class="fw-semibold">{{ $amenity->TenTienNghi ?? '--' }}</div>
        </div>
    </div>
</x-hotel-management.show-page>
