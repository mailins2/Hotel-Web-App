<x-app-layout :assets="['animation']">
    <style>
        .bd-shell { padding-top: 4.75rem; }

        .bd-hero,
        .bd-section {
            border: 1px solid rgba(217, 119, 6, 0.14);
            border-radius: 28px;
            background: #fffdfa;
            box-shadow: 0 24px 60px rgba(148, 82, 24, 0.08);
        }

        .bd-hero {
            padding: 1.75rem;
            margin-bottom: 1.5rem;
        }

        .bd-section {
            padding: 1.35rem;
            height: 100%;
        }

        .bd-summary-card {
            border: 1px solid rgba(217, 119, 6, 0.14);
            border-radius: 20px;
            background: #fff;
            padding: 1rem;
            height: 100%;
        }

        .bd-label {
            color: #8b5e3c;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .bd-value {
            color: #2f1d12;
            font-size: 1.05rem;
            font-weight: 600;
            margin-top: 0.35rem;
        }

        .bd-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.45rem 0.9rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.82rem;
        }

        .bd-badge--booked {
            background: #fef3c7;
            color: #92400e;
        }

        .bd-badge--using {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .bd-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }

        .bd-info-card {
            border: 1px solid rgba(217, 119, 6, 0.14);
            border-radius: 18px;
            background: #fff;
            padding: 1rem;
        }

        .bd-note {
            border-radius: 18px;
            padding: 1rem 1.1rem;
            background: linear-gradient(135deg, #fff5ea, #fffdf9);
            border: 1px dashed rgba(191, 102, 38, 0.28);
            color: #7a4b27;
        }
    </style>

    <div class="bd-shell">
        <div class="bd-hero">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                        <h2 class="mb-0">Chi tiết đặt phòng</h2>
                        @if(request()->route('bookingId') == 9002 || request()->route('bookingId') == 9005)
                            <span class="bd-badge bd-badge--using">Đang sử dụng</span>
                        @else
                            <span class="bd-badge bd-badge--booked">Đã đặt</span>
                        @endif
                    </div>
                    <p class="text-muted mb-0">Thông tin chi tiết đặt phòng tại khách sạn</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('reception.bookings.index') }}" class="btn btn-light">Danh sách đặt phòng</a>
                    <a href="{{ route('reception.dashboard') }}" class="btn btn-primary">Quay lại sơ đồ phòng</a>
                </div>
            </div>
        </div>

        @if(request()->route('bookingId') == 9002)
            <div class="row g-4">
                <div class="col-xl-6">
                    <div class="bd-section">
                        <h4 class="mb-3">Thông tin khách hàng đặt</h4>
                        <div class="bd-info-grid">
                            <div class="bd-info-card"><div class="bd-label">Mã khách hàng</div><div class="bd-value">KH002</div></div>
                            <div class="bd-info-card"><div class="bd-label">Tên khách hàng</div><div class="bd-value">Trần Bảo Ngọc</div></div>
                            <div class="bd-info-card"><div class="bd-label">Số điện thoại</div><div class="bd-value">0938 222 456</div></div>
                            <div class="bd-info-card"><div class="bd-label">Email</div><div class="bd-value">baongoc@gmail.com</div></div>
                            <div class="bd-info-card"><div class="bd-label">CCCD</div><div class="bd-value">048204000222</div></div>
                            <div class="bd-info-card"><div class="bd-label">Địa chỉ</div><div class="bd-value">92 Nguyễn Thị Minh Khai, Quận 3, TP.HCM</div></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="bd-section">
                        <h4 class="mb-3">Thông tin phòng đặt</h4>
                        <div class="bd-info-grid">
                            <div class="bd-info-card"><div class="bd-label">Số phòng</div><div class="bd-value">A102</div></div>
                            <div class="bd-info-card"><div class="bd-label">Loại phòng</div><div class="bd-value">Suite</div></div>
                            <div class="bd-info-card"><div class="bd-label">Sức chứa tối đa</div><div class="bd-value">4 khách</div></div>
                            <div class="bd-info-card"><div class="bd-label">Số người ở</div><div class="bd-value">2 người ở</div></div>
                            <div class="bd-info-card"><div class="bd-label">Giá phòng</div><div class="bd-value">2.150.000 VNĐ / đêm</div></div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="bd-section">
                        <h4 class="mb-3">Thông tin đặt phòng</h4>
                        <div class="bd-info-grid mb-3">
                            <div class="bd-info-card"><div class="bd-label">Mã đặt phòng</div><div class="bd-value">#9002</div></div>
                            <div class="bd-info-card"><div class="bd-label">Ngày đặt phòng</div><div class="bd-value">04/04/2026</div></div>
                            <div class="bd-info-card"><div class="bd-label">Ngày nhận phòng</div><div class="bd-value">07/04/2026</div></div>
                            <div class="bd-info-card"><div class="bd-label">Ngày trả phòng</div><div class="bd-value">09/04/2026</div></div>
                            <div class="bd-info-card"><div class="bd-label">Thời gian lưu trú</div><div class="bd-value">2 đêm</div></div>
                            <div class="bd-info-card"><div class="bd-label">Tiền đặt cọc</div><div class="bd-value">2.000.000 VNĐ</div></div>
                            <div class="bd-info-card"><div class="bd-label">Trạng thái đặt phòng</div><div class="bd-value">Đang sử dụng</div></div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(request()->route('bookingId') == 9003)
            <div class="row g-4">
                <div class="col-xl-6">
                    <div class="bd-section">
                        <h4 class="mb-3">Thông tin khách hàng đặt</h4>
                        <div class="bd-info-grid">
                            <div class="bd-info-card"><div class="bd-label">Mã khách hàng</div><div class="bd-value">KH004</div></div>
                            <div class="bd-info-card"><div class="bd-label">Tên khách hàng</div><div class="bd-value">Phạm Khánh Vy</div></div>
                            <div class="bd-info-card"><div class="bd-label">Số điện thoại</div><div class="bd-value">0912 888 321</div></div>
                            <div class="bd-info-card"><div class="bd-label">Email</div><div class="bd-value">khanhvy@gmail.com</div></div>
                            <div class="bd-info-card"><div class="bd-label">CCCD</div><div class="bd-value">079304000567</div></div>
                            <div class="bd-info-card"><div class="bd-label">Địa chỉ</div><div class="bd-value">45 Võ Văn Tần, Quận 3, TP.HCM</div></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="bd-section">
                        <h4 class="mb-3">Thông tin phòng đặt</h4>
                        <div class="bd-info-grid">
                            <div class="bd-info-card"><div class="bd-label">Số phòng</div><div class="bd-value">B203</div></div>
                            <div class="bd-info-card"><div class="bd-label">Loại phòng</div><div class="bd-value">Family</div></div>
                            <div class="bd-info-card"><div class="bd-label">Sức chứa tối đa</div><div class="bd-value">5 khách</div></div>
                            <div class="bd-info-card"><div class="bd-label">Số người ở</div><div class="bd-value">4 người ở</div></div>
                            <div class="bd-info-card"><div class="bd-label">Giá phòng</div><div class="bd-value">2.750.000 VNĐ / đêm</div></div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="bd-section">
                        <h4 class="mb-3">Thông tin đặt phòng</h4>
                        <div class="bd-info-grid mb-3">
                            <div class="bd-info-card"><div class="bd-label">Mã đặt phòng</div><div class="bd-value">#9002</div></div>
                            <div class="bd-info-card"><div class="bd-label">Ngày đặt phòng</div><div class="bd-value">06/04/2026</div></div>
                            <div class="bd-info-card"><div class="bd-label">Ngày nhận phòng</div><div class="bd-value">09/04/2026</div></div>
                            <div class="bd-info-card"><div class="bd-label">Ngày trả phòng</div><div class="bd-value">12/04/2026</div></div>
                            <div class="bd-info-card"><div class="bd-label">Thời gian lưu trú</div><div class="bd-value">3 đêm</div></div>
                            <div class="bd-info-card"><div class="bd-label">Tiền đặt cọc</div><div class="bd-value">2.500.000 VNĐ</div></div>
                            <div class="bd-info-card"><div class="bd-label">Trạng thái đặt phòng</div><div class="bd-value">Đã đặt</div></div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(request()->route('bookingId') == 9004)
            <div class="row g-4">
                <div class="col-xl-6">
                    <div class="bd-section">
                        <h4 class="mb-3">Thông tin khách hàng đặt</h4>
                        <div class="bd-info-grid">
                            <div class="bd-info-card"><div class="bd-label">Mã khách hàng</div><div class="bd-value">KH005</div></div>
                            <div class="bd-info-card"><div class="bd-label">Tên khách hàng</div><div class="bd-value">Hoàng Gia Bảo</div></div>
                            <div class="bd-info-card"><div class="bd-label">Số điện thoại</div><div class="bd-value">0987 654 210</div></div>
                            <div class="bd-info-card"><div class="bd-label">Email</div><div class="bd-value">giabao@gmail.com</div></div>
                            <div class="bd-info-card"><div class="bd-label">CCCD</div><div class="bd-value">031204000889</div></div>
                            <div class="bd-info-card"><div class="bd-label">Địa chỉ</div><div class="bd-value">18 Lê Lợi, TP. Thủ Dầu Một, Bình Dương</div></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="bd-section">
                        <h4 class="mb-3">Thông tin phòng đặt</h4>
                        <div class="bd-info-grid">
                            <div class="bd-info-card"><div class="bd-label">Số phòng</div><div class="bd-value">B204</div></div>
                            <div class="bd-info-card"><div class="bd-label">Loại phòng</div><div class="bd-value">Executive</div></div>
                            <div class="bd-info-card"><div class="bd-label">Sức chứa tối đa</div><div class="bd-value">3 khách</div></div>
                            <div class="bd-info-card"><div class="bd-label">Số người ở</div><div class="bd-value">2 người ở</div></div>
                            <div class="bd-info-card"><div class="bd-label">Giá phòng</div><div class="bd-value">2.350.000 VNĐ / đêm</div></div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="bd-section">
                        <h4 class="mb-3">Thông tin đặt phòng</h4>
                        <div class="bd-info-grid mb-3">
                             <div class="bd-info-card"><div class="bd-label">Mã đặt phòng</div><div class="bd-value">#9002</div></div>
                            <div class="bd-info-card"><div class="bd-label">Ngày đặt phòng</div><div class="bd-value">06/04/2026</div></div>
                            <div class="bd-info-card"><div class="bd-label">Ngày nhận phòng</div><div class="bd-value">10/04/2026</div></div>
                            <div class="bd-info-card"><div class="bd-label">Ngày trả phòng</div><div class="bd-value">12/04/2026</div></div>
                            <div class="bd-info-card"><div class="bd-label">Thời gian lưu trú</div><div class="bd-value">2 đêm</div></div>
                            <div class="bd-info-card"><div class="bd-label">Tiền đặt cọc</div><div class="bd-value">1.800.000 VNĐ</div></div>
                            <div class="bd-info-card"><div class="bd-label">Trạng thái đặt phòng</div><div class="bd-value">Đã đặt</div></div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(request()->route('bookingId') == 9005)

            <div class="row g-4">
                <div class="col-xl-6">
                    <div class="bd-section">
                        <h4 class="mb-3">Thông tin khách hàng đặt</h4>
                        <div class="bd-info-grid">
                            <div class="bd-info-card"><div class="bd-label">Mã khách hàng</div><div class="bd-value">KH006</div></div>
                            <div class="bd-info-card"><div class="bd-label">Tên khách hàng</div><div class="bd-value">Đỗ Thanh Tùng</div></div>
                            <div class="bd-info-card"><div class="bd-label">Số điện thoại</div><div class="bd-value">0909 765 321</div></div>
                            <div class="bd-info-card"><div class="bd-label">Email</div><div class="bd-value">thanhtung@gmail.com</div></div>
                            <div class="bd-info-card"><div class="bd-label">CCCD</div><div class="bd-value">077104000654</div></div>
                            <div class="bd-info-card"><div class="bd-label">Địa chỉ</div><div class="bd-value">5 Phan Đình Phùng, Phú Nhuận, TP.HCM</div></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="bd-section">
                        <h4 class="mb-3">Thông tin phòng đặt</h4>
                        <div class="bd-info-grid">
                            <div class="bd-info-card"><div class="bd-label">Số phòng</div><div class="bd-value">B206</div></div>
                            <div class="bd-info-card"><div class="bd-label">Loại phòng</div><div class="bd-value">Suite</div></div>
                            <div class="bd-info-card"><div class="bd-label">Sức chứa tối đa</div><div class="bd-value">4 khách</div></div>
                            <div class="bd-info-card"><div class="bd-label">Số người ở</div><div class="bd-value">3 người ở</div></div>
                            <div class="bd-info-card"><div class="bd-label">Giá phòng</div><div class="bd-value">2.100.000 VNĐ / đêm</div></div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="bd-section">
                        <h4 class="mb-3">Thông tin đặt phòng</h4>
                        <div class="bd-info-grid mb-3">
                            <div class="bd-info-card"><div class="bd-label">Mã đặt phòng</div><div class="bd-value">#9002</div></div>
                            <div class="bd-info-card"><div class="bd-label">Ngày đặt phòng</div><div class="bd-value">05/04/2026</div></div>
                            <div class="bd-info-card"><div class="bd-label">Ngày nhận phòng</div><div class="bd-value">08/04/2026</div></div>
                            <div class="bd-info-card"><div class="bd-label">Ngày trả phòng</div><div class="bd-value">11/04/2026</div></div>
                            <div class="bd-info-card"><div class="bd-label">Thời gian lưu trú</div><div class="bd-value">3 đêm</div></div>
                            <div class="bd-info-card"><div class="bd-label">Tiền đặt cọc</div><div class="bd-value">2.100.000 VNĐ</div></div>
                            <div class="bd-info-card"><div class="bd-label">Trạng thái đặt phòng</div><div class="bd-value">Đang sử dụng</div></div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row g-4">
                <div class="col-xl-6">
                    <div class="bd-section">
                        <h4 class="mb-3">Thông tin khách hàng đặt</h4>
                        <div class="bd-info-grid">
                            <div class="bd-info-card"><div class="bd-label">Mã khách hàng</div><div class="bd-value">KH001</div></div>
                            <div class="bd-info-card"><div class="bd-label">Tên khách hàng</div><div class="bd-value">Nguyễn Minh An</div></div>
                            <div class="bd-info-card"><div class="bd-label">Số điện thoại</div><div class="bd-value">0901 234 567</div></div>
                            <div class="bd-info-card"><div class="bd-label">Email</div><div class="bd-value">minhan@gmail.com</div></div>
                            <div class="bd-info-card"><div class="bd-label">CCCD</div><div class="bd-value">079204000111</div></div>
                            <div class="bd-info-card"><div class="bd-label">Địa chỉ</div><div class="bd-value">12 Nguyễn Huệ, Quận 1, TP.HCM</div></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="bd-section">
                        <h4 class="mb-3">Thông tin phòng đặt</h4>
                        <div class="bd-info-grid">
                            <div class="bd-info-card"><div class="bd-label">Số phòng</div><div class="bd-value">A103</div></div>
                            <div class="bd-info-card"><div class="bd-label">Loại phòng</div><div class="bd-value">Deluxe</div></div>
                            <div class="bd-info-card"><div class="bd-label">Sức chứa tối đa</div><div class="bd-value">2 khách</div></div>
                            <div class="bd-info-card"><div class="bd-label">Số người ở</div><div class="bd-value">2 người ở</div></div>
                            <div class="bd-info-card"><div class="bd-label">Giá phòng</div><div class="bd-value">1.450.000 VNĐ / đêm</div></div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="bd-section">
                        <h4 class="mb-3">Thông tin đặt phòng</h4>
                        <div class="bd-info-grid mb-3">
                            <div class="bd-info-card"><div class="bd-label">Mã đặt phòng</div><div class="bd-value">#9002</div></div>
                            <div class="bd-info-card"><div class="bd-label">Ngày đặt phòng</div><div class="bd-value">05/04/2026</div></div>
                            <div class="bd-info-card"><div class="bd-label">Ngày nhận phòng</div><div class="bd-value">08/04/2026</div></div>
                            <div class="bd-info-card"><div class="bd-label">Ngày trả phòng</div><div class="bd-value">10/04/2026</div></div>
                            <div class="bd-info-card"><div class="bd-label">Thời gian lưu trú</div><div class="bd-value">2 đêm</div></div>
                            <div class="bd-info-card"><div class="bd-label">Tiền đặt cọc</div><div class="bd-value">1.500.000 VNĐ</div></div>
                            <div class="bd-info-card"><div class="bd-label">Trạng thái đặt phòng</div><div class="bd-value">Đã đặt</div></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
