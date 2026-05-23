<x-app-layout :assets="['animation']">
    <style>
        .fd-shell { padding-top: 5rem; }

        .fd-card,
        .fd-map,
        .fd-panel {
            border: 1px solid rgba(217, 119, 6, 0.14);
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 16px 34px rgba(120, 74, 44, 0.06);
        }

        .fd-card {
            padding: 1.25rem 1.25rem 1rem;
            text-align: center;
            min-height: 142px;
        }

        .fd-card-link {
            display: block;
            text-decoration: none;
            color: inherit !important;
            height: 100%;
            border-radius: 24px;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        .fd-card-link:hover,
        .fd-card-link:focus {
            transform: translateY(-3px);
            box-shadow: 0 18px 32px rgba(120, 74, 44, 0.11);
            border-color: rgba(217, 119, 6, 0.22);
        }

        .fd-map,
        .fd-panel { padding: 1.35rem; }

        .fd-card-icon {
            width: 44px;
            height: 44px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fffdfa;
            border: 1px solid rgba(217, 119, 6, 0.24);
            color: #a55a2a;
            box-shadow: 0 10px 18px rgba(120, 74, 44, 0.08);
            margin-bottom: 1rem;
        }

        .fd-card-icon svg {
            width: 20px;
            height: 20px;
        }

        .fd-card-label {
            color: #8a4b22;
            font-size: 1.05rem;
            font-weight: 700;
            line-height: 1.15;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            margin-bottom: 0.9rem;
        }

        .fd-room-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 0.8rem;
        }

        .fd-room,
        .fd-room-link {
            border-radius: 18px;
            padding: 0.85rem;
            min-height: 110px;
            display: block;
            text-decoration: none;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .fd-room {
            position: relative;
        }

        .fd-room-link.fd-booked,
        .fd-room-link.fd-booked:hover,
        .fd-room-link.fd-booked:focus,
        .fd-room-link.fd-booked:visited {
            color: #92400e !important;
        }

        .fd-room-link.fd-using,
        .fd-room-link.fd-using:hover,
        .fd-room-link.fd-using:focus,
        .fd-room-link.fd-using:visited {
            color: #1d4ed8 !important;
        }

        .fd-room-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 26px rgba(120, 74, 44, 0.10);
        }

        .fd-empty { background: #dcfce7; color: #166534; }
        .fd-booked { background: #fef3c7; color: #92400e; }
        .fd-using { background: #dbeafe; color: #1d4ed8; }
        .fd-cleaning { background: #f3e8ff; color: #7e22ce; }

        .fd-cleaning-action {
            width: 100%;
            margin-top: 0.65rem;
            padding: 0.42rem 0.65rem;
            border: 1px solid rgba(126, 34, 206, 0.24);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.72);
            color: #6b21a8;
            font-size: 0.78rem;
            font-weight: 700;
            line-height: 1.15;
            transition: background 0.18s ease, border-color 0.18s ease, opacity 0.18s ease;
        }

        .fd-cleaning-action:hover,
        .fd-cleaning-action:focus {
            background: #fff;
            border-color: rgba(126, 34, 206, 0.42);
            outline: none;
        }

        .fd-room.is-updating {
            opacity: 0.72;
            pointer-events: none;
        }

        .fd-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 0.85rem 1rem;
        }

        .fd-legend-item {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.55rem 0.95rem;
            border-radius: 999px;
            font-weight: 600;
            border: 1px solid transparent;
            cursor: pointer;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        .fd-legend-button {
            appearance: none;
            background: transparent;
            font: inherit;
        }

        .fd-legend-button:hover,
        .fd-legend-button:focus {
            transform: translateY(-1px);
            box-shadow: 0 12px 22px rgba(120, 74, 44, 0.10);
            outline: none;
        }

        .fd-legend-button.is-active {
            border-color: currentColor;
            box-shadow: 0 12px 24px rgba(120, 74, 44, 0.14);
        }

        .fd-legend-item--empty { background: #dcfce7; color: #166534; }
        .fd-legend-item--booked { background: #fef3c7; color: #92400e; }
        .fd-legend-item--using { background: #dbeafe; color: #1d4ed8; }
        .fd-legend-item--cleaning { background: #f3e8ff; color: #7e22ce; }
        .fd-legend-item--all { background: #fff7ed; color: #9a4f35; }

        .fd-legend-dot {
            width: 11px;
            height: 11px;
            border-radius: 999px;
            flex-shrink: 0;
        }

        .fd-legend-dot--empty { background: #166534; }
        .fd-legend-dot--booked { background: #92400e; }
        .fd-legend-dot--using { background: #1d4ed8; }
        .fd-legend-dot--cleaning { background: #7e22ce; }
        .fd-legend-dot--all { background: #9a4f35; }

        .fd-list-item {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.85rem 0;
            border-bottom: 1px dashed rgba(194, 107, 45, 0.16);
        }

        .fd-list-item:last-child { border-bottom: none; }
    </style>

    <div class="fd-shell">
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-4">
                <a href="{{ route('reception.bookings.index') }}" class="fd-card fd-card-link">
                    <span class="fd-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 7V17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M7 12H17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <div class="fd-card-label">ĐẶT PHÒNG</div>
                </a>
            </div>
            <div class="col-md-6 col-xl-4">
                <a href="{{ route('reception.check-ins.create') }}" class="fd-card fd-card-link">
                    <span class="fd-card-icon" aria-hidden="true" style="color: #2f8f8f; border-color: rgba(47, 143, 143, 0.24);">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 8H6.75C5.7835 8 5 8.7835 5 9.75V17.25C5 18.2165 5.7835 19 6.75 19H14.25C15.2165 19 16 18.2165 16 17.25V14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 12L19 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M14 5H19V10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <div class="fd-card-label">NHẬN PHÒNG</div>
                </a>
            </div>
            <div class="col-md-6 col-xl-4">
                <a href="{{ route('reception.check-outs.create') }}" class="fd-card fd-card-link">
                    <span class="fd-card-icon" aria-hidden="true" style="color: #c56d35; border-color: rgba(197, 109, 53, 0.24);">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 8H17.25C18.2165 8 19 8.7835 19 9.75V17.25C19 18.2165 18.2165 19 17.25 19H9.75C8.7835 19 8 18.2165 8 17.25V14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 12L5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M10 5H5V10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <div class="fd-card-label">TRẢ PHÒNG</div>
                </a>
            </div>
        </div>

        <div class="fd-map mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h4 class="mb-1">Sơ đồ phòng</h4>
                    <div class="text-muted">Theo dõi tình trạng phòng tại khách sạn</div>
                </div>
                <div class="fd-legend">
                    <button type="button" class="fd-legend-item fd-legend-button fd-legend-item--all is-active" data-room-status-filter=""><span class="fd-legend-dot fd-legend-dot--all"></span>Tất cả</button>
                    <button type="button" class="fd-legend-item fd-legend-button fd-legend-item--empty" data-room-status-filter="empty"><span class="fd-legend-dot fd-legend-dot--empty"></span>Trống</button>
                    <button type="button" class="fd-legend-item fd-legend-button fd-legend-item--booked" data-room-status-filter="booked"><span class="fd-legend-dot fd-legend-dot--booked"></span>Đã đặt</button>
                    <button type="button" class="fd-legend-item fd-legend-button fd-legend-item--using" data-room-status-filter="using"><span class="fd-legend-dot fd-legend-dot--using"></span>Đang sử dụng</button>
                    <button type="button" class="fd-legend-item fd-legend-button fd-legend-item--cleaning" data-room-status-filter="cleaning"><span class="fd-legend-dot fd-legend-dot--cleaning"></span>Đang dọn dẹp</button>
                </div>
            </div>

            @forelse(($roomFloors ?? collect()) as $floor => $rooms)
                <div class="{{ $loop->last ? '' : 'mb-3' }}" data-room-floor>
                    <div class="fw-semibold mb-2" style="font-weight: 600">Tầng {{ $floor }}</div>
                    <div class="fd-room-grid">
                        @foreach($rooms as $room)
                            @php
                                $roomClass = 'fd-' . $room['status'];
                                $roomBody = '<div class="fw-bold">' . e($room['number']) . '</div>'
                                    . '<div data-room-status-label>' . e($room['statusLabel']) . '</div>'
                                    . ($room['type'] ? '<div class="text-muted small mt-1">' . e($room['type']) . '</div>' : '')
                                    . ($room['status'] === 'cleaning'
                                        ? '<button type="button" class="fd-cleaning-action" data-mark-room-cleaned data-room-id="' . e($room['id']) . '">Đã dọn xong</button>'
                                        : '');
                            @endphp

                            @if($room['bookingDetailId'] && in_array($room['status'], ['booked', 'using'], true))
                                <a href="{{ route('reception.booking-detail', ['bookingDetailId' => $room['bookingDetailId']]) }}" class="fd-room-link {{ $roomClass }}" data-room-status="{{ $room['status'] }}" data-room-id="{{ $room['id'] }}">{!! $roomBody !!}</a>
                            @else
                                <div class="fd-room {{ $roomClass }}" data-room-status="{{ $room['status'] }}" data-room-id="{{ $room['id'] }}">{!! $roomBody !!}</div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-muted">Chưa có dữ liệu phòng để hiển thị.</div>
            @endforelse
            <div class="text-muted d-none" data-room-filter-empty>Không có phòng nào thuộc trạng thái này.</div>

            @if(false)
            <div class="mb-3">
                <div class="fw-semibold mb-2" style="font-weight: 600">Tầng 1</div>
                <div class="fd-room-grid">
                    <div class="fd-room fd-empty"><div class="fw-bold">A101</div><div>Trống</div></div>
                    <a href="{{ route('reception.booking-detail', ['bookingDetailId' => 9002]) }}" class="fd-room-link fd-using"><div class="fw-bold">A102</div><div>Đang sử dụng</div></a>
                    <a href="{{ route('reception.booking-detail', ['bookingDetailId' => 9001]) }}" class="fd-room-link fd-booked"><div class="fw-bold">A103</div><div>Đã đặt</div></a>
                </div>
            </div>

            <div>
                <div class="fw-semibold mb-2" style="font-weight: 600">Tầng 2</div>
                <div class="fd-room-grid">
                    <div class="fd-room fd-cleaning"><div class="fw-bold">B201</div><div>Đang dọn dẹp</div></div>
                    <div class="fd-room fd-empty"><div class="fw-bold">B202</div><div>Trống</div></div>
                    <a href="{{ route('reception.booking-detail', ['bookingDetailId' => 9003]) }}" class="fd-room-link fd-booked"><div class="fw-bold">B203</div><div>Đã đặt</div></a>
                    <a href="{{ route('reception.booking-detail', ['bookingDetailId' => 9004]) }}" class="fd-room-link fd-booked"><div class="fw-bold">B204</div><div>Đã đặt</div></a>
                    <div class="fd-room fd-empty"><div class="fw-bold">B205</div><div>Trống</div></div>
                    <a href="{{ route('reception.booking-detail', ['bookingDetailId' => 9005]) }}" class="fd-room-link fd-using"><div class="fw-bold">B206</div><div>Đang sử dụng</div></a>
                </div>
            </div>
            @endif
        </div>

        <!-- <div class="row g-3">
            <div class="col-xl-6">
                <div class="fd-panel">
                    <h5 class="mb-1">Khách đến hôm nay</h5>
                    <div class="text-muted mb-3">Danh sách mẫu phục vụ bố cục check-in</div>
                    <div class="fd-list-item"><div><div class="fw-semibold">Nguyễn Minh An</div><div class="text-muted small">Phòng A101 - Deluxe</div></div><span class="badge text-bg-warning">Nhận phòng</span></div>
                    <div class="fd-list-item"><div><div class="fw-semibold">Phạm Khánh Vy</div><div class="text-muted small">Phòng C301 - Family</div></div><span class="badge text-bg-warning">Nhận phòng</span></div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="fd-panel">
                    <h5 class="mb-1">Khách trả phòng hôm nay</h5>
                    <div class="text-muted mb-3">Danh sách mẫu phục vụ bố cục checkout</div>
                    <div class="fd-list-item"><div><div class="fw-semibold">Trần Bảo Ngọc</div><div class="text-muted small">Phòng A102 - Suite</div></div><span class="badge text-bg-primary">Checkout</span></div>
                    <div class="fd-list-item"><div><div class="fw-semibold">Đỗ Thanh Tùng</div><div class="text-muted small">Phòng B202 - Suite</div></div><span class="badge text-bg-primary">Checkout</span></div>
                </div>
            </div>
            <div class="col-xl-7">
                <div class="fd-panel">
                    <h5 class="mb-1">Hóa đơn cần theo dõi</h5>
                    <div class="text-muted mb-3">Thông tin công nợ đang hiển thị tĩnh</div>
                    <div class="fd-list-item"><div><div class="fw-semibold">Hóa đơn #5001</div><div class="text-muted small">Đặt phòng #9001 - Phạm Thùy Linh phụ trách</div></div><div class="text-end"><div class="fw-bold">3.000.000 VNĐ</div><span class="badge text-bg-warning">Còn lại</span></div></div>
                    <div class="fd-list-item"><div><div class="fw-semibold">Hóa đơn #5003</div><div class="text-muted small">Đặt phòng #9004 - Hoàng Gia Bảo phụ trách</div></div><div class="text-end"><div class="fw-bold">3.900.000 VNĐ</div><span class="badge text-bg-warning">Còn lại</span></div></div>
                </div>
            </div>
        </div> -->
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterButtons = Array.from(document.querySelectorAll('[data-room-status-filter]'));
            const allButton = document.querySelector('[data-room-status-filter=""]');
            const floors = document.querySelectorAll('[data-room-floor]');
            const emptyState = document.querySelector('[data-room-filter-empty]');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            function refreshRoomFilter() {
                const activeFilterButton = filterButtons.find((button) => button.classList.contains('is-active'));
                const currentFilter = activeFilterButton?.dataset.roomStatusFilter || '';
                let visibleRoomCount = 0;

                floors.forEach((floor) => {
                    let floorHasVisibleRoom = false;

                    floor.querySelectorAll('[data-room-status]').forEach((room) => {
                        const isVisible = !currentFilter || room.dataset.roomStatus === currentFilter;
                        room.classList.toggle('d-none', !isVisible);
                        floorHasVisibleRoom = floorHasVisibleRoom || isVisible;
                        visibleRoomCount += isVisible ? 1 : 0;
                    });

                    floor.classList.toggle('d-none', !floorHasVisibleRoom);
                });

                if (emptyState) {
                    emptyState.classList.toggle('d-none', visibleRoomCount > 0);
                }
            }

            filterButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const selectedStatus = button.dataset.roomStatusFilter || '';
                    const currentFilter = button.classList.contains('is-active') && selectedStatus ? '' : selectedStatus;

                    filterButtons.forEach((item) => {
                        const itemStatus = item.dataset.roomStatusFilter || '';
                        item.classList.toggle('is-active', itemStatus === currentFilter);
                    });

                    if (!currentFilter && allButton) {
                        allButton.classList.add('is-active');
                    }

                    refreshRoomFilter();
                });
            });

            document.querySelectorAll('[data-mark-room-cleaned]').forEach((button) => {
                button.addEventListener('click', async () => {
                    const roomId = button.dataset.roomId;
                    const roomCard = button.closest('[data-room-status]');

                    if (!roomId || !roomCard) {
                        return;
                    }

                    button.disabled = true;
                    button.textContent = 'Đang chuyển...';
                    roomCard.classList.add('is-updating');

                    try {
                        const response = await fetch(`/api/phong/${encodeURIComponent(roomId)}/mark-cleaned`, {
                            method: 'POST',
                            headers: {
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                        });
                        const result = await response.json().catch(() => ({}));

                        if (!response.ok || result.success === false) {
                            throw new Error(result.message || 'Không thể chuyển trạng thái phòng.');
                        }

                        roomCard.dataset.roomStatus = 'empty';
                        roomCard.classList.remove('fd-cleaning', 'is-updating');
                        roomCard.classList.add('fd-empty');
                        roomCard.querySelector('[data-room-status-label]').textContent = 'Trống';
                        button.remove();
                        refreshRoomFilter();
                    } catch (error) {
                        alert(error.message || 'Không thể chuyển trạng thái phòng.');
                        button.disabled = false;
                        button.textContent = 'Đã dọn xong';
                        roomCard.classList.remove('is-updating');
                    }
                });
            });
        });
    </script>
</x-app-layout>
