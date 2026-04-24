<x-app-layout :assets="['animation']">
    <style>
        .fd-shell { padding-top: 5rem; }
        .fd-card, .fd-map, .fd-panel {
            border: 1px solid rgba(217, 119, 6, 0.14);
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 16px 34px rgba(120, 74, 44, 0.06);
        }
        .fd-card { padding: 1rem; text-align: center; }
        .fd-map, .fd-panel { padding: 1.35rem; }
        .fd-room-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 0.8rem; }
        .fd-room { border-radius: 18px; padding: 0.85rem; min-height: 96px; }
        .fd-empty { background: #dcfce7; color: #166534; }
        .fd-booked { background: #fef3c7; color: #92400e; }
        .fd-using { background: #dbeafe; color: #1d4ed8; }
        .fd-cleaning { background: #f3e8ff; color: #7e22ce; }
        .fd-list-item { display: flex; justify-content: space-between; gap: 1rem; padding: 0.85rem 0; border-bottom: 1px dashed rgba(194, 107, 45, 0.16); }
        .fd-list-item:last-child { border-bottom: none; }
    </style>

    <div class="fd-shell">
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-3"><div class="fd-card"><div class="text-uppercase small text-muted fw-bold">Đặt phòng</div><div class="h4 mb-0 mt-2">24</div></div></div>
            <div class="col-md-6 col-xl-3"><div class="fd-card"><div class="text-uppercase small text-muted fw-bold">Nhận phòng</div><div class="h4 mb-0 mt-2">08</div></div></div>
            <div class="col-md-6 col-xl-3"><div class="fd-card"><div class="text-uppercase small text-muted fw-bold">Trả phòng</div><div class="h4 mb-0 mt-2">06</div></div></div>
            <div class="col-md-6 col-xl-3"><div class="fd-card"><div class="text-uppercase small text-muted fw-bold">Đổi phòng</div><div class="h4 mb-0 mt-2">03</div></div></div>
        </div>

        <div class="fd-map mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h4 class="mb-1">Sơ đồ phòng</h4>
                    <div class="text-muted">Theo dõi tình trạng phòng ở dạng HTML tĩnh</div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge text-bg-light">Trống</span>
                    <span class="badge text-bg-warning">Đã đặt</span>
                    <span class="badge text-bg-primary">Đang sử dụng</span>
                    <span class="badge text-bg-secondary">Đang dọn dẹp</span>
                </div>
            </div>

            <div class="mb-3">
                <div class="fw-semibold mb-2">Tầng 1</div>
                <div class="fd-room-grid">
                    <div class="fd-room fd-empty"><div class="fw-bold">A101</div><div>Trống</div></div>
                    <div class="fd-room fd-using"><div class="fw-bold">A102</div><div>Đang sử dụng</div></div>
                    <div class="fd-room fd-booked"><div class="fw-bold">A103</div><div>Đã đặt</div></div>
                </div>
            </div>

            <div>
                <div class="fw-semibold mb-2">Tầng 2</div>
                <div class="fd-room-grid">
                    <div class="fd-room fd-cleaning"><div class="fw-bold">B201</div><div>Đang dọn dẹp</div></div>
                    <div class="fd-room fd-empty"><div class="fw-bold">B202</div><div>Trống</div></div>
                    <div class="fd-room fd-booked"><div class="fw-bold">B203</div><div>Đã đặt</div></div>
                </div>
            </div>
        </div>

        <div class="row g-3">
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
            <div class="col-xl-5">
                <div class="fd-panel">
                    <h5 class="mb-1">Tệp khách tại quầy</h5>
                    <div class="text-muted mb-3">Chỉ số giao diện tĩnh</div>
                    <div class="row g-3">
                        <div class="col-12"><div class="border rounded p-3"><div class="small text-muted text-uppercase fw-bold">Khách đang hoạt động</div><div class="h4 mb-0 mt-2">46</div></div></div>
                        <div class="col-12"><div class="border rounded p-3"><div class="small text-muted text-uppercase fw-bold">Khách VIP</div><div class="h4 mb-0 mt-2">12</div></div></div>
                        <div class="col-12"><div class="border rounded p-3"><div class="small text-muted text-uppercase fw-bold">Lượt đến hôm nay</div><div class="h4 mb-0 mt-2">08</div></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
