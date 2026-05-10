<x-hotel-management.show-page
    title="Chi tiết loại phòng"
    subtitle="Thông tin loại phòng"
    :index-route="route('hotel.room-types.index')"
    :edit-route="route('hotel.room-types.edit', ['recordId' => request()->route('recordId') ?? 1])"
>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã loại phòng</div><div class="fw-semibold" id="room-type-id">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên loại phòng</div><div class="fw-semibold" id="room-type-name">Đang tải...</div></div></div>
    <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mô tả</div><div class="fw-semibold" id="room-type-desc">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Người lớn</div><div class="fw-semibold" id="room-type-adults">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Trẻ em</div><div class="fw-semibold" id="room-type-children">Đang tải...</div></div></div>
    <div class="col-md-12 mb-4">
        <div class="border rounded p-3 h-100">
            <div class="text-muted small mb-3">Ảnh phòng</div>
            <div id="room-type-images" class="d-flex flex-wrap gap-3">
                <div class="text-muted">Đang tải...</div>
            </div>
        </div>
    </div>
    <div id="room-type-show-config" data-room-type-id="{{ request()->route('recordId') }}" data-placeholder-image="https://placehold.co/800x800/f3f4f6/9ca3af?text=Room+Type" hidden></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', async function () {
                const config = document.getElementById('room-type-show-config');
                const roomTypeId = config ? config.dataset.roomTypeId : '';
                const placeholderImage = config ? config.dataset.placeholderImage : '';
                const imageContainer = document.getElementById('room-type-images');

                const getImageUrl = function (image) {
                    if (!image) {
                        return '';
                    }

                    return image.Url || image.url || image.DuongDan || image.duong_dan || '';
                };

                const renderImages = function (roomType) {
                    if (!roomType || !Array.isArray(roomType.hinhs) || !roomType.hinhs.length) {
                        imageContainer.innerHTML = '<div class="text-muted">Chưa có ảnh phòng.</div>';
                        return;
                    }

                    imageContainer.innerHTML = roomType.hinhs.map(function (image, index) {
                        const imageUrl = getImageUrl(image) || placeholderImage;
                        return `
                            <div>
                                <img
                                    src="${imageUrl}"
                                    alt="Ảnh phòng ${index + 1}"
                                    class="rounded border bg-light d-block"
                                    style="width: min(100%, 320px); aspect-ratio: 1 / 1; object-fit: cover; object-position: center;"
                                    onerror="this.onerror=null;this.src='${placeholderImage}';"
                                >
                            </div>
                        `;
                    }).join('');
                };

                try {
                    const response = await fetch(`/api/loai-phong/${roomTypeId}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải chi tiết loại phòng.');
                    }

                    const payload = await response.json();
                    const roomType = payload && payload.data ? payload.data : null;

                    document.getElementById('room-type-id').textContent = roomType && roomType.MaLoaiPhong ? roomType.MaLoaiPhong : '--';
                    document.getElementById('room-type-name').textContent = roomType && roomType.TenLoaiPhong ? roomType.TenLoaiPhong : '--';
                    document.getElementById('room-type-desc').textContent = roomType && roomType.Mota ? roomType.Mota : '--';
                    document.getElementById('room-type-adults').textContent = roomType && roomType.NguoiLon ? roomType.NguoiLon : '--';
                    document.getElementById('room-type-children').textContent = roomType && roomType.TreEm !== undefined ? roomType.TreEm : '--';
                    renderImages(roomType);
                } catch (error) {
                    ['room-type-id', 'room-type-desc', 'room-type-adults', 'room-type-children']
                        .forEach(function (id) {
                            document.getElementById(id).textContent = '--';
                        });
                    document.getElementById('room-type-name').textContent = error.message;
                    imageContainer.innerHTML = '<div class="text-muted">--</div>';
                }
            });
        </script>
    @endpush
</x-hotel-management.show-page>
