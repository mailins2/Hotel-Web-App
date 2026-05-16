<x-hotel-management.show-page
    title="Chi tiết tiện nghi phòng"
    subtitle="Thông tin tiện nghi trong hệ thống"
    :index-route="route('hotel.room-amenities.index')"
    :edit-route="route('hotel.room-amenities.edit', ['recordId' => request()->route('recordId') ?? 1])"
>
    <div class="col-md-6 mb-4">
        <div class="border rounded p-3 h-100">
            <div class="text-muted small mb-1">Mã tiện nghi</div>
            <div class="fw-semibold" id="room-amenity-id">Đang tải...</div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="border rounded p-3 h-100">
            <div class="text-muted small mb-1">Tên tiện nghi</div>
            <div class="fw-semibold" id="room-amenity-name">Đang tải...</div>
        </div>
    </div>

    <div
        id="room-amenity-show-config"
        data-room-amenity-id="{{ request()->route('recordId') }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', async function () {
                const config = document.getElementById('room-amenity-show-config');
                const amenityId = config ? config.dataset.roomAmenityId : '';

                try {
                    const response = await fetch(`/api/tien-nghi/${encodeURIComponent(amenityId)}`, {
                        headers: { Accept: 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải chi tiết tiện nghi.');
                    }

                    const payload = await response.json();
                    const amenity = payload && payload.data ? payload.data : null;

                    document.getElementById('room-amenity-id').textContent = amenity && amenity.MaTienNghi ? amenity.MaTienNghi : '--';
                    document.getElementById('room-amenity-name').textContent = amenity && amenity.TenTienNghi ? amenity.TenTienNghi : '--';
                } catch (error) {
                    document.getElementById('room-amenity-id').textContent = '--';
                    document.getElementById('room-amenity-name').textContent = error.message;
                }
            });
        </script>
    @endpush
</x-hotel-management.show-page>
