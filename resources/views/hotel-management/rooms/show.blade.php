@php
    $activeBooking = $room->chiTietDatPhong
        ->pluck('datPhong')
        ->filter()
        ->sortByDesc(fn ($booking) => match ((int) $booking->TinhTrang) {
            \App\Models\DatPhong::CHECKED_IN => 3,
            \App\Models\DatPhong::CONFIRMED => 2,
            \App\Models\DatPhong::HOLD => 1,
            default => 0,
        })
        ->first();

    $statusValue = match (true) {
        (int) $room->TinhTrang === 3 => 3,
        $activeBooking && (int) $activeBooking->TinhTrang === \App\Models\DatPhong::CHECKED_IN => 2,
        $activeBooking && in_array((int) $activeBooking->TinhTrang, [\App\Models\DatPhong::HOLD, \App\Models\DatPhong::CONFIRMED], true) => 1,
        default => (int) $room->TinhTrang,
    };

    $statusLabel = match ($statusValue) {
        0 => 'Trống',
        1 => 'Đã đặt',
        2 => 'Đang sử dụng',
        3 => 'Đang dọn dẹp',
        default => 'Không xác định',
    };
@endphp

<x-hotel-management.show-page
    title="Chi tiết phòng"
    subtitle="Thông tin chi tiết phòng"
    :index-route="route('hotel.rooms.index')"
    :edit-route="route('hotel.rooms.edit', ['recordId' => $room->MaPhong])"
>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã phòng</div><div class="fw-semibold">{{ $room->MaPhong ?? '--' }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số phòng</div><div class="fw-semibold">{{ $room->SoPhong ?? '--' }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Loại phòng</div><div class="fw-semibold">{{ $room?->loaiPhong?->TenLoaiPhong ?? '--' }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tình trạng</div><div class="fw-semibold">{{ $statusLabel }}</div></div></div>
    <div class="col-md-6 mb-4">
        <div class="border rounded p-3 h-100">
            <div class="text-muted small mb-1">Mã đặt phòng</div>
            <div class="fw-semibold">
                @if($activeBooking)
                    <a href="{{ route('hotel.bookings.show', ['recordId' => $activeBooking->MaDatPhong]) }}">#{{ $activeBooking->MaDatPhong }}</a>
                @else
                    --
                @endif
            </div>
        </div>
    </div>
</x-hotel-management.show-page>
