<x-app-layout :assets="['animation']">
    <style>
        .rf-shell { padding-top: 4.5rem; }
        .rf-hero, .rf-card {
            background: #fff;
            border: 1px solid rgba(166, 98, 43, 0.15);
            border-radius: 28px;
            box-shadow: 0 18px 40px rgba(120, 74, 44, 0.08);
        }
        .rf-hero { padding: 1.8rem; margin-bottom: 1.5rem; background: linear-gradient(180deg, #fffaf3 0%, #fff 55%, #f6fbfb 100%); }
        .rf-card { padding: 1.4rem; height: 100%; }
        .rf-room-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 0.95rem; }
        .rf-room-card { border: 1px solid rgba(166, 98, 43, 0.12); border-radius: 22px; padding: 1rem; background: #fff; }
    </style>

    <div class="rf-shell">
        <div class="rf-hero">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="mb-2">Đặt phòng</h1>
                    <p class="text-muted mb-0">Form HTML tĩnh để mô tả bố cục tạo booking tại lễ tân.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('reception.bookings.index') }}" class="btn btn-light">Danh sách booking</a>
                    <a href="{{ route('reception.check-ins.create') }}" class="btn btn-light">Trang nhận phòng</a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                <form data-ui-only-form>
                    <div class="rf-card mb-4">
                        <h5 class="mb-3">Thông tin khách hàng</h5>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Khách hàng</label><select class="form-select"><option>#1 - Nguyễn Minh An</option><option>#2 - Trần Bảo Ngọc</option><option>#4 - Phạm Khánh Vy</option></select></div>
                            <div class="col-md-6"><label class="form-label">Số điện thoại</label><input type="text" class="form-control" value="0901234567"></div>
                            <div class="col-md-6"><label class="form-label">CCCD</label><input type="text" class="form-control" value="079204000111"></div>
                            <div class="col-md-6"><label class="form-label">Điểm tích lũy</label><input type="text" class="form-control" value="120 điểm"></div>
                            <div class="col-md-12"><label class="form-label">Địa chỉ</label><input type="text" class="form-control" value="12 Nguyễn Huệ, Quận 1, TP.HCM"></div>
                        </div>
                    </div>

                    <div class="rf-card mb-4">
                        <h5 class="mb-3">Lịch lưu trú</h5>
                        <div class="row g-3">
                            <div class="col-md-4"><label class="form-label">Ngày đặt</label><input type="date" class="form-control" value="2026-04-08"></div>
                            <div class="col-md-4"><label class="form-label">Ngày nhận phòng</label><input type="date" class="form-control" value="2026-04-08"></div>
                            <div class="col-md-4"><label class="form-label">Ngày trả phòng</label><input type="date" class="form-control" value="2026-04-10"></div>
                            <div class="col-md-4"><label class="form-label">Số lượng người ở</label><input type="number" class="form-control" value="2"></div>
                        </div>
                    </div>

                    <div class="rf-card">
                        <h5 class="mb-3">Chọn phòng</h5>
                        <div class="rf-room-grid">
                            <div class="rf-room-card"><div class="fw-bold">Phòng A101</div><div class="text-muted">Deluxe</div><div class="mt-2 small">Tối đa 2 khách</div></div>
                            <div class="rf-room-card"><div class="fw-bold">Phòng B202</div><div class="text-muted">Suite</div><div class="mt-2 small">Tối đa 4 khách</div></div>
                            <div class="rf-room-card"><div class="fw-bold">Phòng C302</div><div class="text-muted">Family</div><div class="mt-2 small">Tối đa 5 khách</div></div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-xl-4">
                <div class="rf-card">
                    <h5 class="mb-3">Tóm tắt đặt phòng</h5>
                    <div class="border rounded p-3 mb-3"><div class="small text-muted text-uppercase fw-bold">Khách hàng</div><div class="fw-semibold mt-2">Nguyễn Minh An</div></div>
                    <div class="border rounded p-3 mb-3"><div class="small text-muted text-uppercase fw-bold">Khoảng thời gian</div><div class="fw-semibold mt-2">08/04/2026 đến 10/04/2026</div></div>
                    <div class="border rounded p-3 mb-3"><div class="small text-muted text-uppercase fw-bold">Phòng đã chọn</div><div class="fw-semibold mt-2">A101 - Deluxe</div></div>
                    <div class="border rounded p-3 mb-4"><div class="small text-muted text-uppercase fw-bold">Sức chứa tối đa</div><div class="fw-semibold mt-2">2 khách</div></div>
                    <button type="button" class="btn btn-primary w-100" disabled>Lưu booking</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
