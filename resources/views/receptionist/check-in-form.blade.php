<x-app-layout :assets="['animation']">
    <style>
        .ci-shell { padding-top: 4.5rem; }
        .ci-hero, .ci-card {
            background: #fff;
            border: 1px solid rgba(166, 98, 43, 0.15);
            border-radius: 28px;
            box-shadow: 0 18px 40px rgba(120, 74, 44, 0.08);
        }
        .ci-hero { padding: 1.8rem; margin-bottom: 1.5rem; background: linear-gradient(180deg, #fff7ef 0%, #fff 55%, #f3fbfa 100%); }
        .ci-card { padding: 1.4rem; height: 100%; }
        .ci-booking-card { border: 1px solid rgba(166, 98, 43, 0.12); border-radius: 22px; padding: 1rem; background: #fff; margin-bottom: 0.85rem; }
        .ci-detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.85rem; }
        .ci-room-card { border-radius: 20px; background: linear-gradient(180deg, #f6fefc 0%, #fff 100%); border: 1px solid rgba(15, 118, 110, 0.14); padding: 1rem; }
    </style>

    <div class="ci-shell">
        <div class="ci-hero">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="mb-2">Nhận phòng</h1>
                    <p class="text-muted mb-0">Trang HTML tĩnh để mô tả giao diện xác nhận check-in.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('reception.bookings.create') }}" class="btn btn-light">Tạo booking mới</a>
                    <a href="{{ route('reception.bookings.index') }}" class="btn btn-light">Danh sách booking</a>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="ci-card text-center"><div class="small text-uppercase text-muted fw-bold">Booking chờ nhận</div><div class="h4 mb-0 mt-2">5</div></div></div>
            <div class="col-md-4"><div class="ci-card text-center"><div class="small text-uppercase text-muted fw-bold">Khách đến hôm nay</div><div class="h4 mb-0 mt-2">2</div></div></div>
            <div class="col-md-4"><div class="ci-card text-center"><div class="small text-uppercase text-muted fw-bold">Phòng đang sử dụng</div><div class="h4 mb-0 mt-2">42</div></div></div>
        </div>

        <div class="row g-4">
            <div class="col-xl-5">
                <div class="ci-card">
                    <h5 class="mb-3">Booking chờ nhận phòng</h5>
                    <div class="ci-booking-card">
                        <div class="small text-uppercase text-muted fw-bold mb-1">Booking #9001</div>
                        <div class="fw-semibold">Nguyễn Minh An</div>
                        <div class="text-muted small mt-1">Phòng A101 - Deluxe</div>
                        <div class="text-muted small">08/04/2026 đến 10/04/2026</div>
                    </div>
                    <div class="ci-booking-card">
                        <div class="small text-uppercase text-muted fw-bold mb-1">Booking #9004</div>
                        <div class="fw-semibold">Phạm Khánh Vy</div>
                        <div class="text-muted small mt-1">Phòng C301 - Family</div>
                        <div class="text-muted small">08/04/2026 đến 11/04/2026</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-7">
                <div class="ci-card">
                    <h5 class="mb-3">Chi tiết xác nhận</h5>
                    <div class="ci-detail-grid mb-4">
                        <div class="border rounded p-3"><div class="small text-uppercase text-muted fw-bold">Khách hàng</div><div class="fw-semibold mt-2">Nguyễn Minh An</div></div>
                        <div class="border rounded p-3"><div class="small text-uppercase text-muted fw-bold">Số điện thoại</div><div class="fw-semibold mt-2">0901234567</div></div>
                        <div class="border rounded p-3"><div class="small text-uppercase text-muted fw-bold">CCCD</div><div class="fw-semibold mt-2">079204000111</div></div>
                        <div class="border rounded p-3"><div class="small text-uppercase text-muted fw-bold">Điểm tích lũy</div><div class="fw-semibold mt-2">120 điểm</div></div>
                        <div class="border rounded p-3"><div class="small text-uppercase text-muted fw-bold">Thời gian lưu trú</div><div class="fw-semibold mt-2">08/04/2026 đến 10/04/2026</div></div>
                        <div class="border rounded p-3"><div class="small text-uppercase text-muted fw-bold">Số đêm / phòng</div><div class="fw-semibold mt-2">2 đêm - 1 phòng</div></div>
                    </div>

                    <h6 class="mb-3">Phòng sẽ chuyển sang đang sử dụng</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6"><div class="ci-room-card"><div class="fw-bold">Phòng A101</div><div class="text-muted">Deluxe</div><div class="small mt-2">Tối đa 2 khách</div></div></div>
                    </div>

                    <button type="button" class="btn btn-primary w-100">Xác nhận nhận phòng</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
