<x-hotel-management.show-page
    title="Chi tiết tiện nghi phòng"
    subtitle="Thông tin tiện nghi của các loại phòng"
    :index-route="route('hotel.room-amenities.index')"
    :edit-route="route('hotel.room-amenities.edit', ['recordId' => request()->route('recordId') ?? 1])"
>
    <style>
        .hm-amenity-room-types {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .hm-amenity-room-types__item {
            display: inline-flex;
            flex-direction: column;
            gap: 0.2rem;
            min-width: 160px;
            padding: 0.9rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #fffaf6;
        }

        .hm-amenity-room-types__code {
            font-size: 0.78rem;
            font-weight: 700;
            color: #9a3412;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .hm-amenity-room-types__name {
            color: #0f172a;
            font-weight: 600;
        }
    </style>

    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã tiện nghi</div><div class="fw-semibold" id="room-amenity-id">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên tiện nghi</div><div class="fw-semibold" id="room-amenity-name">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số loại phòng áp dụng</div><div class="fw-semibold" id="room-amenity-room-type-count">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Trạng thái liên kết</div><div class="fw-semibold" id="room-amenity-binding-state">Đang tải...</div></div></div>
    <div class="col-md-12 mb-4">
        <div class="border rounded p-3 h-100">
            <div class="text-muted small mb-3">Các loại phòng đang gắn tiện nghi này</div>
            <div id="room-amenity-room-types" class="hm-amenity-room-types">
                <div class="text-muted">Đang tải...</div>
            </div>
        </div>
    </div>

    <div id="room-amenity-show-config" data-room-amenity-id="{{ request()->route('recordId') }}" hidden></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', async function () {
                const config = document.getElementById('room-amenity-show-config');
                const amenityId = config ? config.dataset.roomAmenityId : '';
                const roomTypeContainer = document.getElementById('room-amenity-room-types');

                const escapeHtml = function (value) {
                    return String(value || '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;');
                };

                const renderRoomTypes = function (roomTypes) {
                    if (!Array.isArray(roomTypes) || !roomTypes.length) {
                        roomTypeContainer.innerHTML = '<div class="text-muted">Tiện nghi này chưa được gán cho loại phòng nào.</div>';
                        return;
                    }

                    roomTypeContainer.innerHTML = roomTypes.map(function (roomType) {
                        return `
                            <div class="hm-amenity-room-types__item">
                                <span class="hm-amenity-room-types__code">${escapeHtml(roomType.MaLoaiPhong || '--')}</span>
                                <span class="hm-amenity-room-types__name">${escapeHtml(roomType.TenLoaiPhong || 'Loại phòng')}</span>
                            </div>
                        `;
                    }).join('');
                };

                try {
                    const response = await fetch(`/api/tien-nghi/${encodeURIComponent(amenityId)}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải chi tiết tiện nghi phòng.');
                    }

                    const payload = await response.json();
                    const amenity = payload && payload.data ? payload.data : null;
                    const roomTypes = Array.isArray(amenity && amenity.loai_phongs)
                        ? amenity.loai_phongs
                        : (Array.isArray(amenity && amenity.loaiPhongs) ? amenity.loaiPhongs : []);

                    document.getElementById('room-amenity-id').textContent = amenity && amenity.MaTienNghi ? amenity.MaTienNghi : '--';
                    document.getElementById('room-amenity-name').textContent = amenity && amenity.TenTienNghi ? amenity.TenTienNghi : '--';
                    document.getElementById('room-amenity-room-type-count').textContent = String(roomTypes.length);
                    document.getElementById('room-amenity-binding-state').textContent = roomTypes.length ? 'Đang được sử dụng' : 'Chưa gán loại phòng';
                    renderRoomTypes(roomTypes);
                } catch (error) {
                    document.getElementById('room-amenity-id').textContent = '--';
                    document.getElementById('room-amenity-name').textContent = error.message;
                    document.getElementById('room-amenity-room-type-count').textContent = '--';
                    document.getElementById('room-amenity-binding-state').textContent = '--';
                    roomTypeContainer.innerHTML = '<div class="text-muted">--</div>';
                }
            });
        </script>
    @endpush
</x-hotel-management.show-page>
