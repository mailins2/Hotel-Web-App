<x-app-layout :assets="['animation', 'chart']">
    <style>
        .report-brand-icon {
            color: #6f1d01;
        }

        .report-revenue-chart {
            min-height: 430px;
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

        .report-chart-shell {
            display: grid;
            grid-template-columns: 72px minmax(0, 1fr);
            gap: 1rem;
            align-items: stretch;
            min-height: 370px;
        }

        .report-chart-yaxis {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-end;
            padding: 0.5rem 0 2.75rem;
            color: #9a6a50;
            font-size: 0.78rem;
            font-weight: 600;
        }

        .report-chart-stage {
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .report-chart-board {
            position: relative;
            flex: 1;
            min-height: 320px;
            border-radius: 18px;
            background:
                linear-gradient(to bottom, rgba(194, 120, 55, 0.06), rgba(194, 120, 55, 0.01)),
                repeating-linear-gradient(
                    to bottom,
                    rgba(194, 120, 55, 0.14) 0,
                    rgba(194, 120, 55, 0.14) 1px,
                    transparent 1px,
                    transparent 52px
                );
            overflow: visible;
        }

        .report-chart-board svg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        .report-chart-board [data-main-chart-bar] {
            cursor: pointer;
            transition: opacity 0.18s ease, transform 0.18s ease;
            transform-box: fill-box;
            transform-origin: center;
        }

        .report-chart-board [data-main-chart-bar]:hover {
            opacity: 0.88;
        }

        .report-main-tooltip {
            position: absolute;
            z-index: 25;
            display: none;
            min-width: 220px;
            max-width: 260px;
            padding: 0;
            border: 1px solid rgba(111, 29, 1, 0.12);
            border-radius: 3px;
            background: #fff;
            box-shadow: 0 18px 40px -24px rgba(111, 29, 1, 0.45);
            color: #49120f;
            pointer-events: none;
            overflow: hidden;
        }

        .report-main-tooltip.is-visible {
            display: block;
        }

        .report-main-tooltip-title {
            padding: 1rem 1rem 0.9rem;
            font-size: 0.94rem;
            font-weight: 800;
            text-align: center;
            border-bottom: 1px solid rgba(111, 29, 1, 0.06);
        }

        .report-main-tooltip-line {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.25rem;
            padding: 0.78rem 1rem;
            color: #49120f;
            font-size: 0.78rem;
            font-weight: 600;
            white-space: nowrap;
            border-bottom: 1px solid rgba(111, 29, 1, 0.06);
        }

        .report-main-tooltip-line:last-child {
            border-bottom: 0;
        }

        .report-main-tooltip-line.is-total {
            font-weight: 800;
            text-transform: uppercase;
        }

        .report-main-tooltip-value {
            color: #8a4b2a;
            font-weight: 800;
            text-align: right;
        }

        .report-main-tooltip-value.is-discount {
            color: #f1657c;
        }

        .report-chart-empty {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            color: #8a4b2a;
            font-weight: 400;
            text-align: center;
            padding: 1rem;
        }

        .report-chart-xaxis {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 0.35rem;
            padding: 0.85rem 0.15rem 0;
            color: #8b5e45;
            font-size: 0.74rem;
            font-weight: 600;
            text-align: center;
        }

        .report-chart-xaxis span {
            min-width: 0;
            white-space: nowrap;
            line-height: 1.15;
            overflow: hidden;
            text-overflow: clip;
        }

        .report-date-input {
            width: 100%;
            min-width: 0;
            height: 40px;
            padding-left: 0.85rem;
            padding-right: 0.85rem;
            color: #6f1d01;
            border-color: rgba(111, 29, 1, 0.18);
            background-color: #fff;
        }

        .report-date-controls {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
            width: min(100%, 380px);
        }

        .report-main-filter-controls {
            display: grid;
            grid-template-columns: minmax(150px, 180px) minmax(190px, 240px) repeat(2, minmax(150px, 180px));
            gap: 1rem;
            align-items: end;
            max-width: 820px;
            width: 100%;
        }

        .report-date-controls--compact {
            gap: 1rem;
            width: min(100%, 340px);
        }

        .report-date-field {
            display: grid;
            gap: 0.35rem;
            min-width: 0;
        }

        .report-date-field label {
            color: #8a4b2a;
            font-size: 0.78rem;
            font-weight: 700;
            margin: 0;
        }

        .report-date-input:focus {
            border-color: #c97a3e;
            box-shadow: 0 0 0 0.2rem rgba(111, 29, 1, 0.12);
        }

        .report-service-controls {
            display: flex;
            align-items: end;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .report-service-type {
            width: 190px;
        }

        .report-room-type-control {
            width: min(100%, 240px);
        }

        .report-pie-card .card-body {
            display: grid;
            grid-template-columns: minmax(180px, 240px) minmax(0, 1fr);
            gap: 1.5rem;
            align-items: center;
            position: relative;
        }

        .report-pie-card {
            border-radius: 14px;
            overflow: visible;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .report-pie-card .card-body {
            flex: 1;
        }

        .report-card-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .report-pie-chart {
            width: min(100%, 220px);
            aspect-ratio: 1;
            margin: 0 auto;
            display: block;
            border-radius: 50%;
            filter: drop-shadow(0 14px 24px rgba(111, 29, 1, 0.12));
        }

        .report-pie-chart path {
            stroke: #fffaf7;
            stroke-width: 1.5;
            stroke-linejoin: round;
            stroke-linecap: round;
            cursor: pointer;
            transition: opacity 0.18s ease, transform 0.18s ease;
            transform-origin: center;
        }

        .report-pie-chart path:hover {
            opacity: 0.86;
        }

        .report-pie-legend {
            display: grid;
            gap: 0.75rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .report-pie-legend li {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
            color: #454040;
            font-weight: 500;
        }

        .report-pie-legend-label {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            min-width: 0;
        }

        .report-pie-legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            flex-shrink: 0;
        }

        .report-pie-legend-value {
            color: #454040;
            white-space: nowrap;
        }

        .report-service-tooltip {
            position: absolute;
            z-index: 30;
            display: none;
            min-width: 190px;
            padding: 0.75rem 0.85rem;
            border: 1px solid rgba(111, 29, 1, 0.12);
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 18px 40px -24px rgba(111, 29, 1, 0.45);
            color: #49120f;
            pointer-events: none;
        }

        .report-service-tooltip.is-visible {
            display: block;
        }

        .report-service-tooltip-title {
            font-weight: 800;
            margin-bottom: 0.35rem;
        }

        .report-service-tooltip-line {
            color: #8a4b2a;
            font-size: 0.88rem;
            font-weight: 600;
        }

        .report-service-empty {
            color: #8a4b2a;
            font-weight: 700;
            text-align: center;
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

        .report-stat-description {
            color: #8a4b2a;
            font-size: 1.0rem;
            font-weight: 500;
            line-height: 1.35;
            margin: 0.75rem 0 0;
            text-align: center;
        }

        .report-export-card {
            border: 1px solid rgba(111, 29, 1, 0.1);
            border-radius: 14px;
            background: #fff;
            height: 100%;
        }

        .report-export-card .card-body {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .report-export-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .report-export-chip {
            border-radius: 999px;
            background: rgba(111, 29, 1, 0.08);
            color: #6f1d01;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 0.35rem 0.65rem;
        }

        .report-export-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: auto;
        }

        .report-export-panel .form-label {
            color: #8a4b2a;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .report-export-panel .form-control,
        .report-export-panel .form-select {
            border-color: rgba(111, 29, 1, 0.18);
            color: #6f1d01;
        }

        .report-export-title {
            color: #49120f;
            font-weight: 800;
        }

        .report-export-description {
            color: #8a4b2a;
            line-height: 1.5;
            margin: 0;
        }

        @media (max-width: 767.98px) {
            .report-chart-shell {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }

            .report-chart-yaxis {
                display: none;
            }

            .report-revenue-toolbar {
                align-items: stretch;
            }

            .report-date-controls {
                width: 100%;
            }

            .report-main-filter-controls {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                max-width: 100%;
            }

            .report-date-controls--compact {
                grid-template-columns: 1fr;
            }

            .report-service-controls {
                align-items: stretch;
                justify-content: stretch;
                width: 100%;
            }

            .report-service-type {
                width: 100%;
            }

            .report-room-type-control {
                width: 100%;
            }

            .report-pie-card .card-body {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575.98px) {
            .report-main-filter-controls {
                grid-template-columns: 1fr;
            }
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
                        <div class="fw-bold text-uppercase mb-0 report-summary-title">Khách lưu trú</div>
                    </div>
                    <h3 class="mb-2 text-center">{{ $stayingGuestCount ?? $customerCount ?? 0 }}</h3>
                    <p class="report-stat-description">Số khách đang lưu trú</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="report-brand-icon d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 56px; height: 56px;">
                            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path opacity="0.4" d="M5 9C5 7.34315 6.34315 6 8 6H16C17.6569 6 19 7.34315 19 9V16H5V9Z" fill="currentColor"></path>
                                <path d="M7 10C7 9.44772 7.44772 9 8 9H10C10.5523 9 11 9.44772 11 10V12H7V10Z" fill="currentColor"></path>
                                <path d="M13 10C13 9.44772 13.4477 9 14 9H16C16.5523 9 17 9.44772 17 10V12H13V10Z" fill="currentColor"></path>
                                <path d="M4 13C4 12.4477 4.44772 12 5 12H19C19.5523 12 20 12.4477 20 13V15C20 15.5523 19.5523 16 19 16H5C4.44772 16 4 15.5523 4 15V13Z" fill="currentColor"></path>
                                <path d="M6 16H8V18C8 18.5523 7.55228 19 7 19C6.44772 19 6 18.5523 6 18V16Z" fill="currentColor"></path>
                                <path d="M16 16H18V18C18 18.5523 17.5523 19 17 19C16.4477 19 16 18.5523 16 18V16Z" fill="currentColor"></path>
                            </svg>
                        </div>
                        <div class="fw-bold text-uppercase mb-0 report-summary-title">Phòng đang ở</div>
                    </div>
                    <h3 class="mb-2 text-center">{{ $roomUsingCount ?? 0 }}</h3>
                    <p class="report-stat-description">Số phòng đang ở của khách sạn</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="report-brand-icon d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 56px; height: 56px;">
                            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path opacity="0.2" d="M4 8C4 6.89543 4.89543 6 6 6H18C19.1046 6 20 6.89543 20 8V16C20 17.1046 19.1046 18 18 18H6C4.89543 18 4 17.1046 4 16V8Z" fill="currentColor"></path>
                                <path d="M4 9C4 8.44772 4.44772 8 5 8H19C19.5523 8 20 8.44772 20 9V10.5H4V9Z" fill="currentColor"></path>
                                <path d="M7 12.5C7 11.9477 7.44772 11.5 8 11.5H12.5C13.0523 11.5 13.5 11.9477 13.5 12.5V14.5C13.5 15.0523 13.0523 15.5 12.5 15.5H8C7.44772 15.5 7 15.0523 7 14.5V12.5Z" fill="currentColor"></path>
                                <path d="M8.75 12.75C8.75 12.3358 9.08579 12 9.5 12H11C11.4142 12 11.75 12.3358 11.75 12.75V14.25C11.75 14.6642 11.4142 15 11 15H9.5C9.08579 15 8.75 14.6642 8.75 14.25V12.75Z" fill="white"></path>
                                <path d="M15 13.5C15 12.1193 16.1193 11 17.5 11C18.8807 11 20 12.1193 20 13.5C20 14.8807 18.8807 16 17.5 16C16.1193 16 15 14.8807 15 13.5Z" fill="currentColor"></path>
                                <path d="M16.75 13.5C16.75 13.0858 17.0858 12.75 17.5 12.75C17.9142 12.75 18.25 13.0858 18.25 13.5C18.25 13.9142 17.9142 14.25 17.5 14.25C17.0858 14.25 16.75 13.9142 16.75 13.5Z" fill="white"></path>
                            </svg>
                        </div>
                        <div class="fw-bold text-uppercase mb-0 report-summary-title">Đặt phòng hôm nay</div>
                    </div>
                    <h3 class="mb-2 text-center">{{ $todayBookingCount ?? 0 }}</h3>
                    <p class="report-stat-description">Số lượng đơn đặt phòng trong ngày</p>
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
                    <h3 class="mb-2 text-center">{{ number_format($averageRating ?? 0, 1) }} / 5</h3>
                    <p class="report-stat-description">Điểm đánh giá trong 1 tháng gần nhất</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="report-revenue-toolbar">
                        <div class="header-title">
                            <h4 class="card-title mb-0" data-main-report-title>Thống kê công suất phòng</h4>
                        </div>
                        <div class="report-main-filter-controls">
                            <div class="report-date-field">
                                <label>Nội dung xem</label>
                                <select class="form-select report-date-input" aria-label="Noi dung thong ke" data-main-report-type>
                                    <option value="occupancy" selected>Công suất phòng</option>
                                    <option value="revenue">Doanh thu</option>
                                </select>
                            </div>
                            <div class="report-date-field">
                                <label>Chi tiết</label>
                                <select class="form-select report-date-input" aria-label="Chi tiet thong ke" data-main-report-detail>
                                    <option value="all" selected>Tất cả</option>
                                    <option value="room">Tiền phòng</option>
                                    <option value="service">Dịch vụ</option>
                                    <option value="discount">Giảm giá</option>
                                    <option value="compensation">Đền bù</option>
                                </select>
                            </div>
                            <div class="report-date-field">
                                <label>Từ ngày</label>
                                <input class="form-control report-date-input" type="date" value="{{ $reportDefaultFromDate ?? now()->subMonth()->toDateString() }}" aria-label="Tu ngay" data-main-report-from>
                            </div>
                            <div class="report-date-field">
                                <label>Đến ngày</label>
                                <input class="form-control report-date-input" type="date" value="{{ $reportDefaultToDate ?? now()->toDateString() }}" aria-label="Den ngay" data-main-report-to>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="report-revenue-chart">
                        <div class="report-chart-shell">
                            <div class="report-chart-yaxis" data-main-report-yaxis>
                                <span>40 triệu</span>
                                <span>35 triệu</span>
                                <span>30 triệu</span>
                                <span>25 triệu</span>
                                <span>20 triệu</span>
                            </div>
                            <div class="report-chart-stage">
                                <div class="report-chart-board">
                                    <svg viewBox="0 0 640 360" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" data-main-report-chart>
                                        <defs>
                                            <linearGradient id="reportRevenueFill" x1="0" y1="0" x2="0" y2="1">
                                                <stop offset="0%" stop-color="#c97a3e" stop-opacity="0.94"/>
                                                <stop offset="100%" stop-color="#6f1d01" stop-opacity="0.88"/>
                                            </linearGradient>
                                        </defs>
                                    </svg>
                                    <div class="report-chart-empty d-none" data-main-report-empty>Chưa có dữ liệu trong khoảng thời gian này</div>
                                    <div class="report-main-tooltip" data-main-report-tooltip></div>
                                </div>

                                <div class="report-chart-xaxis" data-main-report-xaxis>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row align-items-stretch">
        <div class="col-md-6 d-flex">
            <div class="card report-pie-card w-100">
                <div class="card-header">
                    <div class="report-card-toolbar">
                        <div class="header-title">
                            <h4 class="card-title mb-0">Dịch vụ đã được đặt</h4>
                        </div>
                        <div class="report-service-controls">
                            <div class="report-date-field report-service-type">
                                <label>Loại dịch vụ</label>
                                <select class="form-select report-date-input" aria-label="Loai dich vu" data-service-revenue-type>
                                    <option value="all" selected>Tất cả</option>
                                    <option value="1">Dịch ăn uống</option>
                                    <option value="2">Dịch vụ phòng</option>
                                    <option value="3">Dịch vụ giải trí</option>
                                </select>
                            </div>
                            <div class="report-date-controls report-date-controls--compact">
                                <div class="report-date-field">
                                    <label>Từ ngày</label>
                                    <input class="form-control report-date-input" type="date" value="{{ $reportDefaultFromDate ?? now()->subMonth()->toDateString() }}" aria-label="Tu ngay doanh thu dich vu" data-service-revenue-from>
                                </div>
                                <div class="report-date-field">
                                    <label>Đến ngày</label>
                                    <input class="form-control report-date-input" type="date" value="{{ $reportDefaultToDate ?? now()->toDateString() }}" aria-label="Den ngay doanh thu dich vu" data-service-revenue-to>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <svg class="report-pie-chart" viewBox="0 0 100 100" aria-hidden="true" data-service-revenue-chart>
                    </svg>
                    <ul class="report-pie-legend" data-service-revenue-legend>
                    </ul>
                    <div class="report-service-tooltip" data-service-revenue-tooltip></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 d-flex">
            <div class="card report-pie-card w-100">
                <div class="card-header">
                    <div class="report-card-toolbar">
                        <div class="header-title">
                            <h4 class="card-title mb-0">Tình trạng phòng trong hôm nay</h4>
                        </div>
                        <div class="report-date-field report-room-type-control">
                            <label>Loại phòng</label>
                            <select class="form-select report-date-input" aria-label="Loai phong" data-room-status-type>
                                <option value="all" selected>Tất cả</option>
                                @foreach (($roomTypeOptions ?? collect()) as $roomType)
                                    <option value="{{ $roomType->MaLoaiPhong }}">{{ $roomType->TenLoaiPhong }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <svg class="report-pie-chart" viewBox="0 0 100 100" aria-hidden="true" data-room-status-chart>
                    </svg>
                    <ul class="report-pie-legend" data-room-status-legend>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card report-export-panel">
                <div class="card-header">
                    <div class="report-card-toolbar">
                        <div class="header-title">
                            <h4 class="card-title mb-0">Xuất báo cáo</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Từ ngày</label>
                            <input class="form-control" type="date" value="{{ $reportDefaultFromDate ?? now()->subMonth()->toDateString() }}" aria-label="Tu ngay bao cao Excel" data-export-report-from>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Đến ngày</label>
                            <input class="form-control" type="date" value="{{ $reportDefaultToDate ?? now()->toDateString() }}" aria-label="Den ngay bao cao Excel" data-export-report-to>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Chu kỳ</label>
                            <select class="form-select" aria-label="Chu ky bao cao" data-export-report-period>
                                <option value="month">Theo tháng</option>
                                <option value="day" selected>Theo ngày</option>
                                <option value="quarter">Theo quý</option>
                                <option value="year">Theo năm</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 col-xl">
                            <div class="card report-export-card mb-0">
                                <div class="card-body">
                                    <div>
                                        <h5 class="report-export-title mb-2">Báo cáo doanh thu</h5>
                                        <p class="report-export-description">Tổng doanh thu phòng, dịch vụ, giảm giá và doanh thu theo thời gian.</p>
                                    </div>
                                    <div class="report-export-meta">
                                        <span class="report-export-chip">Doanh thu</span>
                                        <span class="report-export-chip">Tổng hợp</span>
                                    </div>
                                    <div class="report-export-actions">
                                        <button type="button" class="btn btn-sm btn-primary" data-export-report-button data-export-report-url="{{ route('hotel.reports.export.revenue') }}">Xuất Excel</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl">
                            <div class="card report-export-card mb-0">
                                <div class="card-body">
                                    <div>
                                        <h5 class="report-export-title mb-2">Báo cáo đặt phòng</h5>
                                        <p class="report-export-description">Danh sách đặt phòng, ngày nhận trả, trạng thái đặt phòng.</p>
                                    </div>
                                    <div class="report-export-meta">
                                        <span class="report-export-chip">Đặt phòng</span>
                                        <span class="report-export-chip">Khách hàng</span>
                                    </div>
                                    <div class="report-export-actions">
                                        <button type="button" class="btn btn-sm btn-primary" data-export-report-button data-export-report-url="{{ route('hotel.reports.export.bookings') }}">Xuất Excel</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl">
                            <div class="card report-export-card mb-0">
                                <div class="card-body">
                                    <div>
                                        <h5 class="report-export-title mb-2">Báo cáo phòng</h5>
                                        <p class="report-export-description">Số lượng phòng theo trạng thái, loại phòng, công suất.</p>
                                    </div>
                                    <div class="report-export-meta">
                                        <span class="report-export-chip">Phòng</span>
                                        <span class="report-export-chip">Công suất</span>
                                    </div>
                                    <div class="report-export-actions">
                                        <button type="button" class="btn btn-sm btn-primary" data-export-report-button data-export-report-url="{{ route('hotel.reports.export.rooms') }}">Xuất Excel</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl">
                            <div class="card report-export-card mb-0">
                                <div class="card-body">
                                    <div>
                                        <h5 class="report-export-title mb-2">Báo cáo thanh toán</h5>
                                        <p class="report-export-description">Giao dịch thanh toán, phương thức, trạng thái thu tiền và khoản còn lại.</p>
                                    </div>
                                    <div class="report-export-meta">
                                        <span class="report-export-chip">Thanh toán</span>
                                        <span class="report-export-chip">Giao dịch</span>
                                    </div>
                                    <div class="report-export-actions">
                                        <button type="button" class="btn btn-sm btn-primary" data-export-report-button data-export-report-url="{{ route('hotel.reports.export.payments') }}">Xuất Excel</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl">
                            <div class="card report-export-card mb-0">
                                <div class="card-body">
                                    <div>
                                        <h5 class="report-export-title mb-2">Báo cáo dịch vụ</h5>
                                        <p class="report-export-description">Doanh thu từng dịch vụ, số lượt sử dụng.</p>
                                    </div>
                                    <div class="report-export-meta">
                                        <span class="report-export-chip">Dịch vụ</span>
                                    </div>
                                    <div class="report-export-actions">
                                        <button type="button" class="btn btn-sm btn-primary" data-export-report-button data-export-report-url="{{ route('hotel.reports.export.services') }}">Xuất Excel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="application/json" id="service-revenue-items-data">@json($serviceRevenueItems ?? [])</script>
    <script type="application/json" id="revenue-items-data">@json($revenueItems ?? [])</script>
    <script type="application/json" id="room-status-items-data">@json($roomStatusItems ?? [])</script>
    <script type="application/json" id="room-capacity-items-data">@json($roomCapacityItems ?? [])</script>
    <script type="application/json" id="room-occupancy-items-data">@json($roomOccupancyItems ?? [])</script>
    <script type="application/json" id="room-type-choices-data">@json(($roomTypeOptions ?? collect())->map(fn ($roomType) => [
        'value' => (string) $roomType->MaLoaiPhong,
        'label' => $roomType->TenLoaiPhong,
    ])->values())</script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const readJsonData = function (id, fallback) {
                const source = document.getElementById(id);

                if (!source) {
                    return fallback;
                }

                try {
                    return JSON.parse(source.textContent || '');
                } catch (error) {
                    return fallback;
                }
            };

            const serviceRevenueItems = readJsonData('service-revenue-items-data', []);
            const revenueItems = readJsonData('revenue-items-data', []);
            const roomStatusItems = readJsonData('room-status-items-data', []);
            const roomCapacityItems = readJsonData('room-capacity-items-data', []);
            const roomOccupancyItems = readJsonData('room-occupancy-items-data', []);
            const mainReportTypeSelect = document.querySelector('[data-main-report-type]');
            const mainReportDetailSelect = document.querySelector('[data-main-report-detail]');
            const mainReportTitle = document.querySelector('[data-main-report-title]');
            const mainReportFromInput = document.querySelector('[data-main-report-from]');
            const mainReportToInput = document.querySelector('[data-main-report-to]');
            const mainReportChart = document.querySelector('[data-main-report-chart]');
            const mainReportXAxis = document.querySelector('[data-main-report-xaxis]');
            const mainReportYAxis = document.querySelector('[data-main-report-yaxis]');
            const mainReportTooltip = document.querySelector('[data-main-report-tooltip]');
            const mainReportEmpty = document.querySelector('[data-main-report-empty]');
            const roomTypeChoices = readJsonData('room-type-choices-data', []);
            const typeSelect = document.querySelector('[data-service-revenue-type]');
            const fromInput = document.querySelector('[data-service-revenue-from]');
            const toInput = document.querySelector('[data-service-revenue-to]');
            const chart = document.querySelector('[data-service-revenue-chart]');
            const legend = document.querySelector('[data-service-revenue-legend]');
            const tooltip = document.querySelector('[data-service-revenue-tooltip]');
            const roomStatusTypeSelect = document.querySelector('[data-room-status-type]');
            const roomStatusChart = document.querySelector('[data-room-status-chart]');
            const roomStatusLegend = document.querySelector('[data-room-status-legend]');
            const exportReportFromInput = document.querySelector('[data-export-report-from]');
            const exportReportToInput = document.querySelector('[data-export-report-to]');
            const exportReportPeriodSelect = document.querySelector('[data-export-report-period]');
            const exportReportButtons = document.querySelectorAll('[data-export-report-button]');
            const colors = ['#F75270', '#FAE251', '#8CC0EB', '#5DD3B6', '#9B7EDE', '#F59E0B', '#38BDF8', '#84CC16'];
            const typeLabels = {
                '1': 'Dịch ăn uống',
                '2': 'Dịch vụ phòng',
                '3': 'Dịch vụ giải trí'
            };
            const roomStatusConfig = [
                { status: 0, label: 'Trống', color: '#F75270' },
                { status: 1, label: 'Đã đặt', color: '#FAE251' },
                { status: 2, label: 'Đang sử dụng', color: '#8CC0EB' },
                { status: 3, label: 'Đang dọn dẹp', color: '#5DD3B6' }
            ];
            const mainRevenueDetailOptions = [
                { value: 'all', label: 'Tất cả' },
                { value: 'room', label: 'Tiền phòng' },
                { value: 'service', label: 'Dịch vụ' },
                { value: 'discount', label: 'Giảm giá' },
                { value: 'compensation', label: 'Đền bù' }
            ];
            const mainRevenueValueLabels = {
                all: 'Tổng doanh thu',
                room: 'Tiền phòng',
                service: 'Dịch vụ',
                discount: 'Giảm giá',
                compensation: 'Đền bù'
            };
            const chartHeight = 300;
            const chartTop = 20;
            const chartBottom = 335;
            const dayMs = 24 * 60 * 60 * 1000;

            const renderMainReportDetailOptions = function () {
                if (!mainReportTypeSelect || !mainReportDetailSelect) {
                    return;
                }

                const options = mainReportTypeSelect.value === 'occupancy'
                    ? [{ value: 'all', label: 'Tất cả loại phòng' }].concat(roomTypeChoices)
                    : mainRevenueDetailOptions;

                mainReportDetailSelect.innerHTML = '';

                options.forEach(function (item) {
                    const option = document.createElement('option');
                    option.value = item.value;
                    option.textContent = item.label;
                    mainReportDetailSelect.appendChild(option);
                });
            };

            const parseDate = function (value) {
                const parts = String(value || '').split('-').map(Number);

                if (parts.length !== 3 || parts.some(Number.isNaN)) {
                    return null;
                }

                return new Date(parts[0], parts[1] - 1, parts[2]);
            };

            const syncDateRange = function (fromElement, toElement) {
                if (!fromElement || !toElement) {
                    return;
                }

                if (fromElement.value) {
                    toElement.min = fromElement.value;
                }

                if (toElement.value) {
                    fromElement.max = toElement.value;
                }

                if (fromElement.value && toElement.value && toElement.value < fromElement.value) {
                    toElement.value = fromElement.value;
                }
            };

            const toDateKey = function (date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');

                return [year, month, day].join('-');
            };

            const addDays = function (date, days) {
                const nextDate = new Date(date);
                nextDate.setDate(nextDate.getDate() + days);

                return nextDate;
            };

            const addMonths = function (date, months) {
                const nextDate = new Date(date);
                nextDate.setMonth(nextDate.getMonth() + months);

                return nextDate;
            };

            const startOfMonth = function (date) {
                return new Date(date.getFullYear(), date.getMonth(), 1);
            };

            const diffDays = function (start, end) {
                return Math.max(0, Math.round((end - start) / dayMs));
            };

            const maxDate = function (left, right) {
                return left > right ? left : right;
            };

            const minDate = function (left, right) {
                return left < right ? left : right;
            };

            const formatShortDate = function (date) {
                return String(date.getDate()).padStart(2, '0') + '/' + String(date.getMonth() + 1).padStart(2, '0');
            };

            const formatFullDate = function (date) {
                return formatShortDate(date) + '/' + date.getFullYear();
            };

            const formatMonth = function (date) {
                return 'Tháng ' + (date.getMonth() + 1) + '/' + date.getFullYear();
            };

            const formatCompactMoney = function (amount) {
                const value = Number(amount) || 0;

                if (value >= 1000000) {
                    return new Intl.NumberFormat('vi-VN', { maximumFractionDigits: 1 }).format(value / 1000000) + ' triệu';
                }

                if (value >= 1000) {
                    return new Intl.NumberFormat('vi-VN', { maximumFractionDigits: 0 }).format(value / 1000) + ' nghìn';
                }

                return new Intl.NumberFormat('vi-VN').format(value);
            };

            const buildMainReportBuckets = function (fromDate, toDate) {
                const endExclusive = addDays(toDate, 1);
                const totalDays = diffDays(fromDate, endExclusive);
                const buckets = [];

                if (totalDays <= 31) {
                    for (let cursor = new Date(fromDate); cursor < endExclusive; cursor = addDays(cursor, 1)) {
                        buckets.push({
                            key: toDateKey(cursor),
                            label: formatShortDate(cursor),
                            tooltipLabel: formatFullDate(cursor),
                            start: new Date(cursor),
                            end: addDays(cursor, 1)
                        });
                    }

                    return buckets;
                }

                for (let cursor = startOfMonth(fromDate); cursor < endExclusive; cursor = addMonths(cursor, 1)) {
                    const monthEnd = addMonths(cursor, 1);

                    buckets.push({
                        key: cursor.getFullYear() + '-' + String(cursor.getMonth() + 1).padStart(2, '0'),
                        label: 'Tháng ' + (cursor.getMonth() + 1),
                        tooltipLabel: formatMonth(cursor),
                        start: maxDate(cursor, fromDate),
                        end: minDate(monthEnd, endExclusive)
                    });
                }

                return buckets;
            };

            const getMainRoomCapacity = function (roomTypeId) {
                return roomCapacityItems.filter(function (room) {
                    return roomTypeId === 'all' || String(room.room_type_id) === roomTypeId;
                }).length;
            };

            const aggregateMainOccupancyBuckets = function (buckets, roomTypeId) {
                const roomCapacity = getMainRoomCapacity(roomTypeId);

                return buckets.map(function (bucket) {
                    const daysInBucket = diffDays(bucket.start, bucket.end);
                    const capacityRoomNights = roomCapacity * daysInBucket;
                    let soldRoomNights = 0;
                    let roomRevenue = 0;

                    roomOccupancyItems.forEach(function (item) {
                        if (roomTypeId !== 'all' && String(item.room_type_id) !== roomTypeId) {
                            return;
                        }

                        const checkIn = parseDate(item.check_in);
                        const checkOut = parseDate(item.check_out);

                        if (!checkIn || !checkOut) {
                            return;
                        }

                        const overlapStart = maxDate(checkIn, bucket.start);
                        const overlapEnd = minDate(checkOut, bucket.end);
                        const overlapDays = diffDays(overlapStart, overlapEnd);

                        if (overlapDays <= 0) {
                            return;
                        }

                        soldRoomNights += overlapDays;
                        roomRevenue += overlapDays * (Number(item.nightly_price) || 0);
                    });

                    return {
                        label: bucket.label,
                        tooltipLabel: bucket.tooltipLabel,
                        reportType: 'occupancy',
                        valueLabel: 'Tiá»n phĂ²ng',
                        value: roomRevenue,
                        soldRooms: soldRoomNights,
                        occupancyRate: capacityRoomNights > 0 ? soldRoomNights / capacityRoomNights * 100 : 0,
                        roomRevenue: roomRevenue
                    };
                });
            };

            const aggregateMainRevenueBuckets = function (buckets, detailType) {
                const selectedDetail = detailType || 'all';

                return buckets.map(function (bucket) {
                    const summary = {
                        label: bucket.label,
                        tooltipLabel: bucket.tooltipLabel,
                        reportType: 'revenue',
                        detailType: selectedDetail,
                        valueLabel: mainRevenueValueLabels[selectedDetail] || mainRevenueValueLabels.all,
                        value: 0,
                        soldRooms: 0,
                        occupancyRate: 0,
                        roomRevenue: 0,
                        total: 0,
                        room: 0,
                        service: 0,
                        discount: 0,
                        compensation: 0
                    };

                    revenueItems.forEach(function (item) {
                        const paymentDate = parseDate(item.date);

                        if (!paymentDate || paymentDate < bucket.start || paymentDate >= bucket.end) {
                            return;
                        }

                        summary.total += Number(item.total) || 0;
                        summary.room += Number(item.room) || 0;
                        summary.service += Number(item.service) || 0;
                        summary.discount += Number(item.discount) || 0;
                        summary.compensation += Number(item.compensation) || 0;
                    });

                    summary.value = selectedDetail === 'all'
                        ? summary.total
                        : Number(summary[selectedDetail]) || 0;
                    summary.roomRevenue = summary.value;

                    return summary;
                });
            };

            const formatTooltipMoney = function (amount) {
                return new Intl.NumberFormat('vi-VN').format(Math.round(Number(amount) || 0));
            };

            const buildMainTooltipLine = function (label, value, className) {
                return [
                    '<div class="report-main-tooltip-line' + (className ? ' ' + className : '') + '">',
                    '<span>' + escapeHtml(label) + '</span>',
                    '<span class="report-main-tooltip-value' + (className === 'is-discount' ? ' is-discount' : '') + '">' + formatTooltipMoney(value) + '</span>',
                    '</div>'
                ].join('');
            };

            const showMainTooltip = function (event, item) {
                if (!mainReportTooltip) {
                    return;
                }

                mainReportTooltip.innerHTML = [
                    '<div class="report-main-tooltip-title">' + escapeHtml(item.tooltipLabel) + '</div>',
                    '<div class="report-main-tooltip-line">Số phòng được bán: ' + item.soldRooms + '</div>',
                    '<div class="report-main-tooltip-line">Công suất: ' + item.occupancyRate.toFixed(1) + '%</div>',
                    '<div class="report-main-tooltip-line">Tiền phòng: ' + formatMoney(item.roomRevenue) + '</div>'
                ].join('');
                if (item.reportType === 'revenue') {
                    const isAllRevenue = item.detailType === 'all';
                    const revenueRows = isAllRevenue
                        ? [
                            buildMainTooltipLine('Tiền phòng', item.room),
                            buildMainTooltipLine('Dịch vụ', item.service),
                            buildMainTooltipLine('Đền bù', item.compensation),
                            buildMainTooltipLine('Giảm giá', item.discount, 'is-discount'),
                            buildMainTooltipLine('Tổng cộng', item.total, 'is-total')
                        ]
                        : [
                            buildMainTooltipLine(item.valueLabel, item.value, item.detailType === 'discount' ? 'is-discount' : '')
                        ];

                    mainReportTooltip.innerHTML = [
                        '<div class="report-main-tooltip-title">' + escapeHtml(item.tooltipLabel) + '</div>'
                    ].concat(revenueRows).join('');
                }

                mainReportTooltip.classList.add('is-visible');

                const board = mainReportTooltip.closest('.report-chart-board');
                const bounds = board ? board.getBoundingClientRect() : { left: 0, top: 0 };
                const tooltipWidth = mainReportTooltip.offsetWidth || 240;
                const tooltipHeight = mainReportTooltip.offsetHeight || 110;
                const maxLeft = Math.max(8, bounds.width - tooltipWidth - 8);
                const left = Math.min(maxLeft, Math.max(8, event.clientX - bounds.left - (tooltipWidth / 2)));
                const preferredTop = event.clientY - bounds.top - tooltipHeight - 16;
                const fallbackTop = event.clientY - bounds.top + 16;
                const maxTop = Math.max(8, bounds.height - tooltipHeight - 8);
                const top = preferredTop < 8 ? fallbackTop : preferredTop;

                mainReportTooltip.style.left = left + 'px';
                mainReportTooltip.style.top = Math.min(maxTop, Math.max(8, top)) + 'px';
            };

            const hideMainTooltip = function () {
                if (mainReportTooltip) {
                    mainReportTooltip.classList.remove('is-visible');
                }
            };

            const renderMainReportChart = function () {
                if (!mainReportChart || !mainReportXAxis || !mainReportYAxis || !mainReportTypeSelect || !mainReportDetailSelect) {
                    return;
                }

                const isOccupancy = mainReportTypeSelect.value === 'occupancy';
                const fromDate = parseDate(mainReportFromInput ? mainReportFromInput.value : '');
                const toDate = parseDate(mainReportToInput ? mainReportToInput.value : '');

                if (mainReportTitle) {
                    mainReportTitle.textContent = isOccupancy ? 'Thống kê công suất phòng' : 'Thống kê doanh thu';
                }

                if (!fromDate || !toDate || fromDate > toDate) {
                    mainReportChart.querySelectorAll('[data-main-chart-bar]').forEach(function (bar) {
                        bar.remove();
                    });
                    mainReportXAxis.innerHTML = '';
                    mainReportYAxis.innerHTML = ['40 triệu', '30 triệu', '20 triệu', '10 triệu', '0'].map(function (label) {
                        return '<span>' + label + '</span>';
                    }).join('');
                    if (mainReportEmpty) {
                        mainReportEmpty.textContent = 'Chưa có dữ liệu biểu đồ cho lựa chọn này';
                        mainReportEmpty.classList.remove('d-none');
                    }
                    hideMainTooltip();
                    return;
                }

                const buckets = buildMainReportBuckets(fromDate, toDate);
                const groups = isOccupancy
                    ? aggregateMainOccupancyBuckets(buckets, mainReportDetailSelect.value)
                    : aggregateMainRevenueBuckets(buckets, mainReportDetailSelect.value);
                const maxRevenue = Math.max.apply(null, groups.map(function (item) {
                    return item.value;
                }).concat([0]));
                const axisMax = maxRevenue > 0 ? Math.ceil(maxRevenue / 1000000) * 1000000 : 1000000;
                const barWidth = groups.length > 12 ? 18 : 26;
                const slotWidth = 640 / Math.max(groups.length, 1);

                mainReportChart.querySelectorAll('[data-main-chart-bar]').forEach(function (bar) {
                    bar.remove();
                });
                mainReportXAxis.innerHTML = '';
                mainReportXAxis.style.gridTemplateColumns = 'repeat(' + Math.max(groups.length, 1) + ', minmax(0, 1fr))';
                mainReportYAxis.innerHTML = [1, 0.75, 0.5, 0.25, 0].map(function (ratio) {
                    return '<span>' + formatCompactMoney(axisMax * ratio) + '</span>';
                }).join('');
                hideMainTooltip();

                if (mainReportEmpty) {
                    mainReportEmpty.classList.toggle('d-none', groups.some(function (item) {
                        return item.value > 0 || item.soldRooms > 0;
                    }));
                    mainReportEmpty.textContent = isOccupancy
                        ? 'Chưa có công suất phòng trong khoảng thời gian này'
                        : 'Chưa có doanh thu checkout trong khoảng thời gian này';
                }

                groups.forEach(function (item, index) {
                    const barHeight = item.value > 0 && axisMax > 0 ? Math.max(8, item.value / axisMax * chartHeight) : 0;
                    const x = (slotWidth * index) + ((slotWidth - barWidth) / 2);
                    const y = chartBottom - barHeight;
                    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                    const label = document.createElement('span');

                    if (barHeight > 0) {
                        path.setAttribute('d', describeTopRoundedBar(x, y, barWidth, barHeight, 12));
                        path.setAttribute('fill', 'url(#reportRevenueFill)');
                        path.setAttribute('data-main-chart-bar', 'true');
                        path.addEventListener('mouseenter', function (event) {
                            showMainTooltip(event, item);
                        });
                        path.addEventListener('mousemove', function (event) {
                            showMainTooltip(event, item);
                        });
                        path.addEventListener('mouseleave', hideMainTooltip);
                        mainReportChart.appendChild(path);
                    }

                    label.textContent = groups.length > 18 && index % 2 === 1 ? '' : item.label;
                    label.title = item.tooltipLabel;
                    mainReportXAxis.appendChild(label);
                });
            };

            const formatMoney = function (amount) {
                return new Intl.NumberFormat('vi-VN').format(Math.round(Number(amount) || 0)) + ' VNĐ';
            };

            const escapeHtml = function (value) {
                return String(value ?? '').replace(/[&<>"']/g, function (char) {
                    return {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;'
                    }[char];
                });
            };

            const polarToCartesian = function (centerX, centerY, radius, angleInDegrees) {
                const angleInRadians = (angleInDegrees - 90) * Math.PI / 180;

                return {
                    x: centerX + (radius * Math.cos(angleInRadians)),
                    y: centerY + (radius * Math.sin(angleInRadians))
                };
            };

            const describeSlice = function (startAngle, endAngle) {
                const start = polarToCartesian(50, 50, 46, endAngle);
                const end = polarToCartesian(50, 50, 46, startAngle);
                const largeArcFlag = endAngle - startAngle <= 180 ? 0 : 1;

                return [
                    'M', 50, 50,
                    'L', start.x.toFixed(2), start.y.toFixed(2),
                    'A', 46, 46, 0, largeArcFlag, 0, end.x.toFixed(2), end.y.toFixed(2),
                    'Z'
                ].join(' ');
            };

            const describeTopRoundedBar = function (x, y, width, height, radius) {
                const bottom = y + height;
                const corner = Math.min(radius, width / 2, height);
                const right = x + width;

                return [
                    'M', x.toFixed(2), bottom.toFixed(2),
                    'L', x.toFixed(2), (y + corner).toFixed(2),
                    'Q', x.toFixed(2), y.toFixed(2), (x + corner).toFixed(2), y.toFixed(2),
                    'L', (right - corner).toFixed(2), y.toFixed(2),
                    'Q', right.toFixed(2), y.toFixed(2), right.toFixed(2), (y + corner).toFixed(2),
                    'L', right.toFixed(2), bottom.toFixed(2),
                    'Z'
                ].join(' ');
            };

            const getFilteredItems = function () {
                const selectedType = typeSelect ? typeSelect.value : 'all';
                const fromDate = fromInput ? fromInput.value : '';
                const toDate = toInput ? toInput.value : '';

                return serviceRevenueItems.filter(function (item) {
                    if (selectedType !== 'all' && String(item.type) !== selectedType) {
                        return false;
                    }

                    if (fromDate && item.date < fromDate) {
                        return false;
                    }

                    if (toDate && item.date > toDate) {
                        return false;
                    }

                    return true;
                });
            };

            const aggregateItems = function (items) {
                const selectedType = typeSelect ? typeSelect.value : 'all';
                const groups = new Map();

                items.forEach(function (item) {
                    const key = selectedType === 'all'
                        ? String(item.type || 'other')
                        : String(item.service_id || item.service_name);
                    const name = selectedType === 'all'
                        ? (typeLabels[String(item.type)] || item.type_label || 'Khác')
                        : (item.service_name || 'Dịch vụ');

                    if (!groups.has(key)) {
                        groups.set(key, {
                            name: name,
                            quantity: 0,
                            revenue: 0
                        });
                    }

                    const group = groups.get(key);
                    group.quantity += Number(item.quantity) || 0;
                    group.revenue += Number(item.revenue) || 0;
                });

                return Array.from(groups.values())
                    .filter(function (item) {
                        return item.revenue > 0 || item.quantity > 0;
                    })
                    .sort(function (left, right) {
                        return right.revenue - left.revenue;
                    });
            };

            const showTooltip = function (event, item) {
                if (!tooltip) {
                    return;
                }

                tooltip.innerHTML = [
                    '<div class="report-service-tooltip-title">' + escapeHtml(item.name) + '</div>',
                    '<div class="report-service-tooltip-line">Số lượng bán: ' + item.quantity + '</div>',
                    '<div class="report-service-tooltip-line">Doanh thu: ' + formatMoney(item.revenue) + '</div>'
                ].join('');
                tooltip.classList.add('is-visible');

                const cardBody = tooltip.closest('.card-body');
                const bounds = cardBody ? cardBody.getBoundingClientRect() : { left: 0, top: 0 };
                const tooltipWidth = tooltip.offsetWidth || 190;
                const tooltipHeight = tooltip.offsetHeight || 90;
                const left = event.clientX - bounds.left - (tooltipWidth / 2);
                const top = event.clientY - bounds.top - tooltipHeight - 14;

                tooltip.style.left = Math.max(8, left) + 'px';
                tooltip.style.top = Math.max(8, top) + 'px';
            };

            const hideTooltip = function () {
                if (tooltip) {
                    tooltip.classList.remove('is-visible');
                }
            };

            const renderServiceRevenueChart = function () {
                if (!chart || !legend) {
                    return;
                }

                const groups = aggregateItems(getFilteredItems());
                const totalRevenue = groups.reduce(function (sum, item) {
                    return sum + item.revenue;
                }, 0);

                chart.innerHTML = '';
                legend.innerHTML = '';
                hideTooltip();

                if (!groups.length || totalRevenue <= 0) {
                    chart.innerHTML = '<circle cx="50" cy="50" r="44" fill="#f7eee9"></circle>';
                    legend.innerHTML = '<li class="report-service-empty">Chưa có doanh thu dịch vụ trong khoảng thời gian này</li>';
                    return;
                }

                let currentAngle = 0;

                groups.forEach(function (item, index) {
                    const sliceAngle = item.revenue / totalRevenue * 360;
                    const endAngle = groups.length === 1
                        ? 359.99
                        : (index === groups.length - 1 ? 360 : currentAngle + sliceAngle);
                    const color = colors[index % colors.length];
                    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');

                    path.setAttribute('d', describeSlice(currentAngle, endAngle));
                    path.setAttribute('fill', color);
                    path.addEventListener('mouseenter', function (event) {
                        showTooltip(event, item);
                    });
                    path.addEventListener('mousemove', function (event) {
                        showTooltip(event, item);
                    });
                    path.addEventListener('mouseleave', hideTooltip);
                    chart.appendChild(path);

                    const legendItem = document.createElement('li');
                    legendItem.innerHTML = [
                        '<span class="report-pie-legend-label">',
                        '<span class="report-pie-legend-dot" style="background: ' + color + ';"></span>',
                        escapeHtml(item.name),
                        '</span>',
                        '<span class="report-pie-legend-value">' + formatMoney(item.revenue) + '</span>'
                    ].join('');
                    legend.appendChild(legendItem);

                    currentAngle = endAngle;
                });
            };

            const renderPieChart = function (targetChart, targetLegend, groups, emptyMessage, valueFormatter) {
                if (!targetChart || !targetLegend) {
                    return;
                }

                const total = groups.reduce(function (sum, item) {
                    return sum + item.value;
                }, 0);

                targetChart.innerHTML = '';
                targetLegend.innerHTML = '';

                if (!groups.length || total <= 0) {
                    targetChart.innerHTML = '<circle cx="50" cy="50" r="44" fill="#f7eee9"></circle>';
                    targetLegend.innerHTML = '<li class="report-service-empty">' + emptyMessage + '</li>';
                    return;
                }

                let currentAngle = 0;

                groups.forEach(function (item, index) {
                    const sliceAngle = item.value / total * 360;
                    const endAngle = groups.length === 1
                        ? 359.99
                        : (index === groups.length - 1 ? 360 : currentAngle + sliceAngle);
                    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');

                    path.setAttribute('d', describeSlice(currentAngle, endAngle));
                    path.setAttribute('fill', item.color || colors[index % colors.length]);
                    targetChart.appendChild(path);

                    const legendItem = document.createElement('li');
                    legendItem.innerHTML = [
                        '<span class="report-pie-legend-label">',
                        '<span class="report-pie-legend-dot" style="background: ' + (item.color || colors[index % colors.length]) + ';"></span>',
                        escapeHtml(item.name),
                        '</span>',
                        '<span class="report-pie-legend-value">' + valueFormatter(item.value) + '</span>'
                    ].join('');
                    targetLegend.appendChild(legendItem);

                    currentAngle = endAngle;
                });
            };

            const renderRoomStatusChart = function () {
                const selectedRoomType = roomStatusTypeSelect ? roomStatusTypeSelect.value : 'all';
                const filteredRooms = roomStatusItems.filter(function (room) {
                    return selectedRoomType === 'all' || String(room.room_type_id) === selectedRoomType;
                });
                const groups = roomStatusConfig.map(function (config) {
                    return {
                        name: config.label,
                        color: config.color,
                        value: filteredRooms.filter(function (room) {
                            return Number(room.status) === config.status;
                        }).length
                    };
                }).filter(function (item) {
                    return item.value > 0;
                });

                renderPieChart(
                    roomStatusChart,
                    roomStatusLegend,
                    groups,
                    'Chưa có phòng thuộc loại phòng này',
                    function (value) {
                        return value;
                    }
                );
            };

            const bindDateRange = function (fromElement, toElement, onChange) {
                syncDateRange(fromElement, toElement);

                [fromElement, toElement].forEach(function (input) {
                    if (!input) {
                        return;
                    }

                    input.addEventListener('change', function () {
                        syncDateRange(fromElement, toElement);

                        if (typeof onChange === 'function') {
                            onChange();
                        }
                    });
                });
            };

            bindDateRange(mainReportFromInput, mainReportToInput, renderMainReportChart);
            bindDateRange(fromInput, toInput, renderServiceRevenueChart);
            bindDateRange(exportReportFromInput, exportReportToInput);

            exportReportButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    syncDateRange(exportReportFromInput, exportReportToInput);

                    const exportUrl = new URL(button.dataset.exportReportUrl, window.location.origin);
                    exportUrl.searchParams.set('from', exportReportFromInput ? exportReportFromInput.value : '');
                    exportUrl.searchParams.set('to', exportReportToInput ? exportReportToInput.value : '');
                    exportUrl.searchParams.set('period', exportReportPeriodSelect ? exportReportPeriodSelect.value : 'day');
                    exportUrl.searchParams.set('format', 'xlsx');

                    window.location.href = exportUrl.toString();
                });
            });

            [typeSelect].forEach(function (input) {
                if (input) {
                    input.addEventListener('change', renderServiceRevenueChart);
                }
            });

            if (roomStatusTypeSelect) {
                roomStatusTypeSelect.addEventListener('change', renderRoomStatusChart);
            }

            if (mainReportTypeSelect) {
                mainReportTypeSelect.addEventListener('change', function () {
                    renderMainReportDetailOptions();
                    renderMainReportChart();
                });
            }

            [mainReportDetailSelect].forEach(function (input) {
                if (input) {
                    input.addEventListener('change', renderMainReportChart);
                }
            });

            renderMainReportDetailOptions();
            renderMainReportChart();
            renderServiceRevenueChart();
            renderRoomStatusChart();
        });
    </script>
</x-app-layout>
