<x-hotel-management.show-page
    title="Chi tiết phòng"
    subtitle="Thông tin chi tiết phòng"
    :index-route="route('hotel.rooms.index')"
    :edit-route="route('hotel.rooms.edit', ['recordId' => request()->route('recordId') ?? 1])"
>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã phòng</div><div class="fw-semibold" id="room-id">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số phòng</div><div class="fw-semibold" id="room-number">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Loại phòng</div><div class="fw-semibold" id="room-type">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tình trạng</div><div class="fw-semibold" id="room-status">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã đặt phòng</div><div class="fw-semibold" id="room-booking-id">Đang tải...</div></div></div>

    <div
        id="room-show-config"
        data-room-id="{{ request()->route('recordId') }}"
        data-booking-url-template="{{ route('hotel.bookings.show', ['recordId' => '__BOOKING_ID__']) }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const config = document.getElementById('room-show-config');
                const roomId = config ? config.dataset.roomId : '';
                const bookingUrlTemplate = config ? config.dataset.bookingUrlTemplate : '';

                const mapStatus = function (status) {
                    switch (Number(status)) {
                        case 0:
                            return 'Trống';
                        case 1:
                            return 'Đã đặt';
                        case 2:
                            return 'Đang sử dụng';
                        case 3:
                            return 'Đang dọn dẹp';
                        default:
                            return 'Không xác định';
                    }
                };

                const loadRoom = async function () {
                    try {
                        const response = await fetch(`/api/phong/${roomId}`, {
                            cache: 'no-store',
                            headers: { 'Accept': 'application/json' }
                        });

                        if (!response.ok) {
                            throw new Error('Không thể tải chi tiết phòng.');
                        }

                        const payload = await response.json();
                        const room = payload && payload.data ? payload.data : null;
                        const roomType = room && room.loai_phong ? room.loai_phong : null;
                        const status = room ? (room.TinhTrangHienTai ?? room.TinhTrang) : null;
                        const bookingId = room ? (room.MaDatPhongHienTai ?? null) : null;

                        document.getElementById('room-id').textContent = room && room.MaPhong ? room.MaPhong : '--';
                        document.getElementById('room-number').textContent = room && room.SoPhong ? room.SoPhong : '--';
                        document.getElementById('room-type').textContent = roomType && roomType.TenLoaiPhong ? roomType.TenLoaiPhong : '--';
                        document.getElementById('room-status').textContent = room ? mapStatus(status) : '--';
                        document.getElementById('room-booking-id').innerHTML = bookingId
                            ? `<a href="${bookingUrlTemplate.replace('__BOOKING_ID__', bookingId)}">#${bookingId}</a>`
                            : '--';
                    } catch (error) {
                        document.getElementById('room-id').textContent = '--';
                        document.getElementById('room-number').textContent = '--';
                        document.getElementById('room-type').textContent = '--';
                        document.getElementById('room-booking-id').textContent = '--';
                        document.getElementById('room-status').textContent = error.message;
                    }
                };

                loadRoom();
                setInterval(function () {
                    if (!document.hidden) {
                        loadRoom();
                    }
                }, 15000);
            });
        </script>
    @endpush
</x-hotel-management.show-page>
