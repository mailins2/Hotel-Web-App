<x-app-layout :assets="['animation']">
    <style>
        .co-shell { padding-top: 4.5rem; }
        .co-hero, .co-card {
            background: #fff;
            border: 1px solid rgba(166, 98, 43, 0.15);
            border-radius: 28px;
            box-shadow: 0 18px 40px rgba(120, 74, 44, 0.08);
        }
        .co-hero {
            padding: 1.8rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(180deg, #fff7ef 0%, #fff 55%, #f8fbff 100%);
        }
        .co-card { padding: 1.4rem; height: 100%; }
        .co-booking-card {
            border: 1px solid rgba(166, 98, 43, 0.12);
            border-radius: 22px;
            padding: 1rem;
            background: #fff;
            margin-bottom: 0.85rem;
        }
        .co-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.85rem;
        }
        .co-room-card {
            border-radius: 20px;
            background: linear-gradient(180deg, #f8fbff 0%, #fff 100%);
            border: 1px solid rgba(37, 99, 235, 0.14);
            padding: 1rem;
        }
    </style>

    <div class="co-shell">
        <div class="co-hero">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="mb-2">Trả phòng</h1>
                    <p class="text-muted mb-0">Danh sách thông tin trả phòng</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('reception.bookings.index') }}" class="btn btn-light">Danh sách đặt phòng</a>
                    <a href="{{ route('reception.check-ins.create') }}" class="btn btn-light">Trang nhận phòng</a>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="co-card text-center"><div class="small text-uppercase text-muted fw-bold">Khách sắp trả phòng</div><div class="h4 mb-0 mt-2">4</div></div></div>
            <div class="col-md-4"><div class="co-card text-center"><div class="small text-uppercase text-muted fw-bold">Checkout hôm nay</div><div class="h4 mb-0 mt-2">2</div></div></div>
            <div class="col-md-4"><div class="co-card text-center"><div class="small text-uppercase text-muted fw-bold">Phòng sẽ trống</div><div class="h4 mb-0 mt-2">2</div></div></div>
        </div>

        <div class="row g-4">
            <div class="col-xl-5">
                <div class="co-card">
                    <h5 class="mb-3">Khách chờ trả phòng</h5>
                    <div class="co-booking-card">
                        <div class="small text-uppercase text-muted fw-bold mb-1">Booking #9002</div>
                        <div class="fw-semibold">Trần Bảo Ngọc</div>
                        <div class="text-muted small mt-1">Phòng A102 - Suite</div>
                        <div class="text-muted small">07/04/2026 đến 09/04/2026</div>
                    </div>
                    <div class="co-booking-card">
                        <div class="small text-uppercase text-muted fw-bold mb-1">Booking #9005</div>
                        <div class="fw-semibold">Đỗ Thanh Tùng</div>
                        <div class="text-muted small mt-1">Phòng B206 - Suite</div>
                        <div class="text-muted small">06/04/2026 đến 10/04/2026</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-7">
                <div class="co-card">
                    <h5 class="mb-3">Chi tiết xác nhận</h5>
                    <div class="co-detail-grid mb-4">
                        <div class="border rounded p-3"><div class="small text-uppercase text-muted fw-bold">Khách hàng</div><div class="fw-semibold mt-2">Trần Bảo Ngọc</div></div>
                        <div class="border rounded p-3"><div class="small text-uppercase text-muted fw-bold">Số điện thoại</div><div class="fw-semibold mt-2">0912345678</div></div>
                        <div class="border rounded p-3"><div class="small text-uppercase text-muted fw-bold">Phòng</div><div class="fw-semibold mt-2">A102 - Suite</div></div>
                        <div class="border rounded p-3"><div class="small text-uppercase text-muted fw-bold">Lưu trú</div><div class="fw-semibold mt-2">2 đêm - 2 khách</div></div>
                        <div class="border rounded p-3"><div class="small text-uppercase text-muted fw-bold">Phát sinh dịch vụ</div><div class="fw-semibold mt-2">450.000 VNĐ</div></div>
                        <div class="border rounded p-3"><div class="small text-uppercase text-muted fw-bold">Còn phải thu</div><div class="fw-semibold mt-2">1.250.000 VNĐ</div></div>
                    </div>

                    <h6 class="mb-3">Phòng sẽ chuyển sang trạng thái trống</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6"><div class="co-room-card"><div class="fw-bold">Phòng A102</div><div class="text-muted">Suite</div><div class="small mt-2">Sẵn sàng dọn phòng sau checkout</div></div></div>
                    </div>

                    <button type="button" class="btn btn-primary w-100">Xác nhận trả phòng</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
