<x-app-layout :assets="['animation', 'chart']">
    <style>
        .report-brand-icon {
            color: #6f1d01;
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

        .report-chart-shell {
            display: grid;
            grid-template-columns: 72px minmax(0, 1fr);
            gap: 1rem;
            align-items: stretch;
            min-height: 270px;
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
            min-height: 220px;
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
            overflow: hidden;
        }

        .report-chart-board svg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        .report-chart-xaxis {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 0.5rem;
            padding: 0.85rem 0 0;
            color: #8b5e45;
            font-size: 0.8rem;
            font-weight: 600;
            text-align: center;
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

        .report-pie-card .card-body {
            display: grid;
            grid-template-columns: minmax(180px, 240px) minmax(0, 1fr);
            gap: 1.5rem;
            align-items: center;
        }

        .report-pie-card {
            border-radius: 14px;
            overflow: hidden;
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

            .report-pie-card .card-body {
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
                        <div class="fw-bold text-uppercase mb-0 report-summary-title">Khách hàng</div>
                    </div>
                    <h3 class="mb-2 text-center">{{ $customerCount ?? 0 }}</h3>
                    <p class="report-stat-description">Số lượng khách hàng đã có mã đặt phòng</p>
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
                        <div class="fw-bold text-uppercase mb-0 report-summary-title">Phòng đang sử dụng</div>
                    </div>
                    <h3 class="mb-2 text-center">{{ $roomUsingCount ?? 0 }}</h3>
                    <p class="report-stat-description">Số phòng hiện đang sử dụng</p>
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
                        <div class="fw-bold text-uppercase mb-0 report-summary-title">Phòng trống</div>
                    </div>
                    <h3 class="mb-2 text-center">{{ $roomEmptyCount ?? 0 }}</h3>
                    <p class="report-stat-description">Số phòng hiện đang trống</p>
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
                    <p class="report-stat-description">Điểm đánh giá trung bình từ khách lưu trú</p>
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
                            <h4 class="card-title mb-0">Thống kê doanh thu</h4>
                        </div>
                        <div class="report-date-controls">
                            <div class="report-date-field">
                                <label>Từ ngày</label>
                                <input class="form-control report-date-input" type="date" value="2026-01-01" aria-label="Tu ngay">
                            </div>
                            <div class="report-date-field">
                                <label>Đến ngày</label>
                                <input class="form-control report-date-input" type="date" value="2026-06-30" aria-label="Den ngay">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="report-revenue-chart">
                        <div class="report-chart-shell">
                            <div class="report-chart-yaxis">
                                <span>40 triệu</span>
                                <span>35 triệu</span>
                                <span>30 triệu</span>
                                <span>25 triệu</span>
                                <span>20 triệu</span>
                            </div>
                            <div class="report-chart-stage">
                                <div class="report-chart-board">
                                    <svg viewBox="0 0 640 260" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                                        <defs>
                                            <linearGradient id="reportRevenueFill" x1="0" y1="0" x2="0" y2="1">
                                                <stop offset="0%" stop-color="#c97a3e" stop-opacity="0.94"/>
                                                <stop offset="100%" stop-color="#6f1d01" stop-opacity="0.88"/>
                                            </linearGradient>
                                        </defs>
                                        <rect x="48" y="94" width="26" height="126" rx="12" fill="url(#reportRevenueFill)"/>
                                        <rect x="154" y="76" width="26" height="144" rx="12" fill="url(#reportRevenueFill)"/>
                                        <rect x="260" y="58" width="26" height="162" rx="12" fill="url(#reportRevenueFill)"/>
                                        <rect x="366" y="48" width="26" height="172" rx="12" fill="url(#reportRevenueFill)"/>
                                        <rect x="472" y="34" width="26" height="186" rx="12" fill="url(#reportRevenueFill)"/>
                                        <rect x="578" y="22" width="26" height="198" rx="12" fill="url(#reportRevenueFill)"/>
                                    </svg>
                                </div>

                                <div class="report-chart-xaxis">
                                    <span>Tháng 1</span>
                                    <span>Tháng 2</span>
                                    <span>Tháng 3</span>
                                    <span>Tháng 4</span>
                                    <span>Tháng 5</span>
                                    <span>Tháng 6</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card report-pie-card">
                <div class="card-header">
                    <div class="report-card-toolbar">
                        <div class="header-title">
                            <h4 class="card-title mb-0">Doanh thu dịch vụ</h4>
                        </div>
                        <div class="report-service-controls">
                            <div class="report-date-field report-service-type">
                                <label>Loại dịch vụ</label>
                                <select class="form-select report-date-input" aria-label="Loai dich vu">
                                    <option selected>Tất cả</option>
                                    <option>Dịch ăn uống</option>
                                    <option>Dịch vụ phòng</option>
                                    <option>Dịch vụ giải trí</option>
                                </select>
                            </div>
                            <div class="report-date-controls report-date-controls--compact">
                                <div class="report-date-field">
                                    <label>Từ ngày</label>
                                    <input class="form-control report-date-input" type="date" value="2026-01-01" aria-label="Tu ngay doanh thu dich vu">
                                </div>
                                <div class="report-date-field">
                                    <label>Đến ngày</label>
                                    <input class="form-control report-date-input" type="date" value="2026-06-30" aria-label="Den ngay doanh thu dich vu">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <svg class="report-pie-chart" viewBox="0 0 100 100" aria-hidden="true">
                        <path d="M50 50 L50 4 A46 46 0 0 1 72.11 90.35 Z" fill="#F75270"/>
                        <path d="M50 50 L72.11 90.35 A46 46 0 0 1 4.91 59.57 Z" fill="#FAE251"/>
                        <path d="M50 50 L4.91 59.57 A46 46 0 0 1 23.17 12.62 Z" fill="#8CC0EB"/>
                        <path d="M50 50 L23.17 12.62 A46 46 0 0 1 50 4 Z" fill="#5DD3B6"/>
                    </svg>
                    <ul class="report-pie-legend">
                        <li>
                            <span class="report-pie-legend-label">
                                <span class="report-pie-legend-dot" style="background: #F75270;"></span>
                                Nhà hàng
                            </span>
                            <span class="report-pie-legend-value">42%</span>
                        </li>
                        <li>
                            <span class="report-pie-legend-label">
                                <span class="report-pie-legend-dot" style="background: #FAE251;"></span>
                                Spa
                            </span>
                            <span class="report-pie-legend-value">28%</span>
                        </li>
                        <li>
                            <span class="report-pie-legend-label">
                                <span class="report-pie-legend-dot" style="background: #8CC0EB;"></span>
                                Giặt ủi
                            </span>
                            <span class="report-pie-legend-value">18%</span>
                        </li>
                        <li>
                            <span class="report-pie-legend-label">
                                <span class="report-pie-legend-dot" style="background: #5DD3B6;"></span>
                                Đưa đón
                            </span>
                            <span class="report-pie-legend-value">12%</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card report-pie-card">
                <div class="card-header">
                    <div class="report-card-toolbar">
                        <div class="header-title">
                            <h4 class="card-title mb-0">Tình trạng phòng</h4>
                        </div>
                        <div class="report-date-controls report-date-controls--compact">
                            <div class="report-date-field">
                                <label>Từ ngày</label>
                                <input class="form-control report-date-input" type="date" value="2026-01-01" aria-label="Tu ngay tinh trang phong">
                            </div>
                            <div class="report-date-field">
                                <label>Đến ngày</label>
                                <input class="form-control report-date-input" type="date" value="2026-06-30" aria-label="Den ngay tinh trang phong">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <svg class="report-pie-chart" viewBox="0 0 100 100" aria-hidden="true">
                        <path d="M50 50 L50 4 A46 46 0 0 1 95.91 52.89 Z" fill="#F75270"/>
                        <path d="M50 50 L95.91 52.89 A46 46 0 0 1 83.17 81.89 Z" fill="#FAE251"/>
                        <path d="M50 50 L83.17 81.89 A46 46 0 1 1 26.98 10.16 Z" fill="#8CC0EB"/>
                        <path d="M50 50 L26.98 10.16 A46 46 0 0 1 50 4 Z" fill="#5DD3B6"/>
                    </svg>
                    <ul class="report-pie-legend">
                        <li>
                            <span class="report-pie-legend-label">
                                <span class="report-pie-legend-dot" style="background: #F75270;"></span>
                                Trống
                            </span>
                            <span class="report-pie-legend-value">24</span>
                        </li>
                        <li>
                            <span class="report-pie-legend-label">
                                <span class="report-pie-legend-dot" style="background: #FAE251;"></span>
                                Đã đặt
                            </span>
                            <span class="report-pie-legend-value">12</span>
                        </li>
                        <li>
                            <span class="report-pie-legend-label">
                                <span class="report-pie-legend-dot" style="background: #8CC0EB;"></span>
                                Đang sử dụng
                            </span>
                            <span class="report-pie-legend-value">56</span>
                        </li>
                        <li>
                            <span class="report-pie-legend-label">
                                <span class="report-pie-legend-dot" style="background: #5DD3B6;"></span>
                                Đang dọn dẹp
                            </span>
                            <span class="report-pie-legend-value">8</span>
                        </li>
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
                            <h4 class="card-title mb-0">Xuất báo cáo Excel</h4>
                        </div>
                        <button type="button" class="btn btn-primary">
                            Xuất tất cả
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Từ ngày</label>
                            <input class="form-control" type="date" value="2026-01-01" aria-label="Tu ngay bao cao Excel">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Đến ngày</label>
                            <input class="form-control" type="date" value="2026-06-30" aria-label="Den ngay bao cao Excel">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Chu kỳ</label>
                            <select class="form-select" aria-label="Chu ky bao cao">
                                <option selected>Theo tháng</option>
                                <option>Theo ngày</option>
                                <option>Theo quý</option>
                                <option>Theo năm</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Định dạng</label>
                            <select class="form-select" aria-label="Dinh dang bao cao">
                                <option selected>Excel (.xlsx)</option>
                                <option>CSV (.csv)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 col-xl">
                            <div class="card report-export-card mb-0">
                                <div class="card-body">
                                    <div>
                                        <h5 class="report-export-title mb-2">Báo cáo doanh thu</h5>
                                        <p class="report-export-description">Tổng doanh thu phòng, dịch vụ, giảm giá và doanh thu ròng theo thời gian.</p>
                                    </div>
                                    <div class="report-export-meta">
                                        <span class="report-export-chip">Doanh thu</span>
                                        <span class="report-export-chip">Tổng hợp</span>
                                    </div>
                                    <div class="report-export-actions">
                                        <button type="button" class="btn btn-sm btn-primary">Xuất Excel</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary">Xem mẫu</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl">
                            <div class="card report-export-card mb-0">
                                <div class="card-body">
                                    <div>
                                        <h5 class="report-export-title mb-2">Báo cáo booking</h5>
                                        <p class="report-export-description">Danh sách đặt phòng, ngày nhận trả, trạng thái booking và thông tin khách hàng.</p>
                                    </div>
                                    <div class="report-export-meta">
                                        <span class="report-export-chip">Booking</span>
                                        <span class="report-export-chip">Khách hàng</span>
                                    </div>
                                    <div class="report-export-actions">
                                        <button type="button" class="btn btn-sm btn-primary">Xuất Excel</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary">Xem mẫu</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl">
                            <div class="card report-export-card mb-0">
                                <div class="card-body">
                                    <div>
                                        <h5 class="report-export-title mb-2">Báo cáo phòng</h5>
                                        <p class="report-export-description">Số lượng phòng theo trạng thái, loại phòng, công suất và lượt sử dụng.</p>
                                    </div>
                                    <div class="report-export-meta">
                                        <span class="report-export-chip">Phòng</span>
                                        <span class="report-export-chip">Công suất</span>
                                    </div>
                                    <div class="report-export-actions">
                                        <button type="button" class="btn btn-sm btn-primary">Xuất Excel</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary">Xem mẫu</button>
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
                                        <button type="button" class="btn btn-sm btn-primary">Xuất Excel</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary">Xem mẫu</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl">
                            <div class="card report-export-card mb-0">
                                <div class="card-body">
                                    <div>
                                        <h5 class="report-export-title mb-2">Báo cáo dịch vụ</h5>
                                        <p class="report-export-description">Doanh thu từng dịch vụ, số lượt sử dụng và tỷ trọng theo nhóm dịch vụ.</p>
                                    </div>
                                    <div class="report-export-meta">
                                        <span class="report-export-chip">Dịch vụ</span>
                                        <span class="report-export-chip">Tỷ trọng</span>
                                    </div>
                                    <div class="report-export-actions">
                                        <button type="button" class="btn btn-sm btn-primary">Xuất Excel</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary">Xem mẫu</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
