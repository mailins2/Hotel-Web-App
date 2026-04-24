<x-app-layout :assets="['animation', 'chart']">
    <style>
        .revenue-icon-blue {
            color: #2f80ed;
        }

        .report-revenue-chart {
            min-height: 320px;
            margin-bottom: 1.5rem;
            border: 1px dashed rgba(111, 29, 1, 0.18);
            border-radius: 16px;
            background: linear-gradient(180deg, rgba(255, 250, 247, 0.98), rgba(255, 244, 236, 0.96));
            padding: 1.5rem;
        }

        .report-revenue-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .report-chart-placeholder {
            height: 100%;
            min-height: 270px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #8a4b2a;
            gap: 0.75rem;
        }

        .report-chart-placeholder svg {
            width: 72px;
            height: 72px;
            color: #b45309;
            opacity: 0.8;
        }

        .report-select-wrap {
            position: relative;
        }

        .report-select-wrap::after {
            content: '';
            position: absolute;
            top: 50%;
            right: 14px;
            width: 10px;
            height: 6px;
            pointer-events: none;
            transform: translateY(-50%);
            background-repeat: no-repeat;
            background-size: 10px 6px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6' fill='none'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%2364748B' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        }

        .report-select-wrap .form-select {
            padding-right: 2.5rem;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: none !important;
        }

        .report-summary-title {
            font-size: 14px;
            line-height: 1.3;
        }

        .report-summary-note {
            color: #8a4b2a;
            font-size: 0.98rem;
            line-height: 1.5;
        }
    </style>

    <div class="row">
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="text-primary d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 56px; height: 56px;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M11.9488 14.54C8.49884 14.54 5.58789 15.1038 5.58789 17.2795C5.58789 19.4562 8.51765 20.0001 11.9488 20.0001C15.3988 20.0001 18.3098 19.4364 18.3098 17.2606C18.3098 15.084 15.38 14.54 11.9488 14.54Z" fill="currentColor"></path>
                                <path opacity="0.4" d="M11.949 12.467C14.2851 12.467 16.1583 10.5831 16.1583 8.23351C16.1583 5.88306 14.2851 4 11.949 4C9.61293 4 7.73975 5.88306 7.73975 8.23351C7.73975 10.5831 9.61293 12.467 11.949 12.467Z" fill="currentColor"></path>
                                <path opacity="0.4" d="M21.0881 9.21923C21.6925 6.84176 19.9205 4.70654 17.664 4.70654C17.4187 4.70654 17.1841 4.73356 16.9549 4.77949C16.9244 4.78669 16.8904 4.802 16.8725 4.82902C16.8519 4.86324 16.8671 4.90917 16.8895 4.93889C17.5673 5.89528 17.9568 7.0597 17.9568 8.30967C17.9568 9.50741 17.5996 10.6241 16.9728 11.5508C16.9083 11.6462 16.9656 11.775 17.0793 11.7948C17.2369 11.8227 17.3981 11.8371 17.5629 11.8416C19.2059 11.8849 20.6807 10.8213 21.0881 9.21923Z" fill="currentColor"></path>
                            </svg>
                        </div>
                        <div class="fw-bold text-uppercase mb-0 report-summary-title">Khách hàng</div>
                    </div>
                    <h3 class="mb-2 text-center">128</h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="text-success d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 56px; height: 56px;">
                            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path opacity="0.4" d="M5 9C5 7.34315 6.34315 6 8 6H16C17.6569 6 19 7.34315 19 9V16H5V9Z" fill="currentColor"></path>
                                <path d="M7 10C7 9.44772 7.44772 9 8 9H10C10.5523 9 11 9.44772 11 10V12H7V10Z" fill="currentColor"></path>
                                <path d="M13 10C13 9.44772 13.4477 9 14 9H16C16.5523 9 17 9.44772 17 10V12H13V10Z" fill="currentColor"></path>
                                <path d="M4 13C4 12.4477 4.44772 12 5 12H19C19.5523 12 20 12.4477 20 13V15C20 15.5523 19.5523 16 19 16H5C4.44772 16 4 15.5523 4 15V13Z" fill="currentColor"></path>
                                <path d="M6 16H8V18C8 18.5523 7.55228 19 7 19C6.44772 19 6 18.5523 6 18V16Z" fill="currentColor"></path>
                                <path d="M16 16H18V18C18 18.5523 17.5523 19 17 19C16.4477 19 16 18.5523 16 18V16Z" fill="currentColor"></path>
                            </svg>
                        </div>
                        <div class="fw-bold text-uppercase mb-0 report-summary-title">Phòng đang sử dụng</div>
                    </div>
                    <h3 class="mb-2 text-center">42</h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="revenue-icon-blue d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 56px; height: 56px;">
                            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path opacity="0.2" d="M4 8C4 6.89543 4.89543 6 6 6H18C19.1046 6 20 6.89543 20 8V16C20 17.1046 19.1046 18 18 18H6C4.89543 18 4 17.1046 4 16V8Z" fill="currentColor"></path>
                                <path d="M4 9C4 8.44772 4.44772 8 5 8H19C19.5523 8 20 8.44772 20 9V10.5H4V9Z" fill="currentColor"></path>
                                <path d="M7 12.5C7 11.9477 7.44772 11.5 8 11.5H12.5C13.0523 11.5 13.5 11.9477 13.5 12.5V14.5C13.5 15.0523 13.0523 15.5 12.5 15.5H8C7.44772 15.5 7 15.0523 7 14.5V12.5Z" fill="currentColor"></path>
                                <path d="M8.75 12.75C8.75 12.3358 9.08579 12 9.5 12H11C11.4142 12 11.75 12.3358 11.75 12.75V14.25C11.75 14.6642 11.4142 15 11 15H9.5C9.08579 15 8.75 14.6642 8.75 14.25V12.75Z" fill="white"></path>
                                <path d="M15 13.5C15 12.1193 16.1193 11 17.5 11C18.8807 11 20 12.1193 20 13.5C20 14.8807 18.8807 16 17.5 16C16.1193 16 15 14.8807 15 13.5Z" fill="currentColor"></path>
                                <path d="M16.75 13.5C16.75 13.0858 17.0858 12.75 17.5 12.75C17.9142 12.75 18.25 13.0858 18.25 13.5C18.25 13.9142 17.9142 14.25 17.5 14.25C17.0858 14.25 16.75 13.9142 16.75 13.5Z" fill="white"></path>
                            </svg>
                        </div>
                        <div class="fw-bold text-uppercase mb-0 report-summary-title">Doanh thu hôm nay</div>
                    </div>
                    <h3 class="mb-2 text-center">32.500.000 VNĐ</h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="text-warning d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 56px; height: 56px;">
                            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path opacity="0.4" d="M12 4L14.4721 9.00872L20 9.8123L16 13.7106L16.9443 19.2154L12 16.6154L7.05573 19.2154L8 13.7106L4 9.8123L9.52786 9.00872L12 4Z" fill="currentColor"></path>
                                <path d="M12 7.5L13.2361 10.0044C13.3816 10.2994 13.6631 10.5039 13.9889 10.5513L16.7526 10.9529L14.7526 12.9024C14.5168 13.1323 14.4091 13.4635 14.4648 13.788L14.9365 16.5401L12.4648 15.2401C12.1738 15.087 11.8262 15.087 11.5352 15.2401L9.06353 16.5401L9.53519 13.788C9.59085 13.4635 9.4832 13.1323 9.24736 12.9024L7.24736 10.9529L10.0111 10.5513C10.3369 10.5039 10.6184 10.2994 10.7639 10.0044L12 7.5Z" fill="currentColor"></path>
                            </svg>
                        </div>
                        <div class="fw-bold text-uppercase mb-0 report-summary-title">Đánh giá trung bình</div>
                    </div>
                    <h3 class="mb-2 text-center">4.6 / 5</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <div class="report-revenue-toolbar">
                        <div class="header-title">
                            <h4 class="card-title mb-0">Doanh thu theo tháng</h4>
                        </div>
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="report-select-wrap" style="min-width: 180px;">
                                <select class="form-select" disabled>
                                    <option>Theo ngày</option>
                                    <option selected>Theo tháng</option>
                                    <option>Theo năm</option>
                                </select>
                            </div>
                            <div class="report-select-wrap" style="min-width: 220px;">
                                <select class="form-select" disabled>
                                    <option>Tháng 1</option>
                                    <option>Tháng 2</option>
                                    <option>Tháng 3</option>
                                    <option selected>Tháng 4</option>
                                    <option>Tháng 5</option>
                                    <option>Tháng 6</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="report-revenue-chart">
                        <div class="report-chart-placeholder">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 18.5H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                <path d="M6 15L10 11L13 13.5L18 8.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="6" cy="15" r="1.2" fill="currentColor"/>
                                <circle cx="10" cy="11" r="1.2" fill="currentColor"/>
                                <circle cx="13" cy="13.5" r="1.2" fill="currentColor"/>
                                <circle cx="18" cy="8.5" r="1.2" fill="currentColor"/>
                            </svg>
                            <p class="fw-semibold mb-0">Biểu đồ đang ở chế độ giao diện tĩnh.</p>
                            <p class="mb-0">Bộ lọc vẫn được giữ lại để không vỡ bố cục trang thống kê.</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Tháng</th>
                                    <th>Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>Tháng 1</td><td>210.000.000 VNĐ</td></tr>
                                <tr><td>Tháng 2</td><td>240.000.000 VNĐ</td></tr>
                                <tr><td>Tháng 3</td><td>268.000.000 VNĐ</td></tr>
                                <tr><td>Tháng 4</td><td>285.000.000 VNĐ</td></tr>
                                <tr><td>Tháng 5</td><td>301.000.000 VNĐ</td></tr>
                                <tr><td>Tháng 6</td><td>318.000.000 VNĐ</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title mb-0">Tình trạng phòng</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                        <span>Trống</span>
                        <span class="fw-semibold">18</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                        <span>Đã đặt</span>
                        <span class="fw-semibold">9</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                        <span>Đang sử dụng</span>
                        <span class="fw-semibold">42</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-3">
                        <span>Đang dọn dẹp</span>
                        <span class="fw-semibold">6</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
