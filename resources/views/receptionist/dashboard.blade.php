<x-app-layout :assets="$assets ?? []">
    <style>
        .fd-shell {
            --fd-border: rgba(217, 119, 6, 0.14);
            --fd-text: #3f2b1d;
            --fd-muted: #8a6d58;
            padding-top: 5rem;
        }

        .fd-summary-card,
        .fd-content-card,
        .fd-action-card {
            border: 1px solid var(--fd-border);
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 16px 34px rgba(120, 74, 44, 0.06);
        }

        .fd-summary-card {
            --fd-icon-color: #a16207;
            --fd-icon-border: rgba(217, 119, 6, 0.24);
            --fd-icon-bg: rgba(255, 251, 235, 0.9);
            padding: 1.0rem 0.80rem;
            min-height: 100px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 0.20rem;
            text-align: center;
        }

        .fd-summary-label {
            color: var(--fd-muted);
            font-size: 0.88rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0;
        }

        .fd-summary-row {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .fd-summary-col {
            min-width: 0;
        }

        .fd-summary-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--fd-icon-border);
            border-radius: 999px;
            background: var(--fd-icon-bg);
            color: var(--fd-icon-color);
        }

        .fd-summary-icon svg {
            width: 18px;
            height: 18px;
            display: block;
        }

        .fd-tone-sunrise {
            --fd-icon-color: #9a5b2c;
            --fd-icon-border: rgba(194, 107, 45, 0.22);
            --fd-icon-bg: rgba(255, 248, 240, 0.92);
            background: linear-gradient(180deg, #fff3e5 0%, #fff 100%);
        }

        .fd-tone-teal {
            --fd-icon-color: #0f766e;
            --fd-icon-border: rgba(15, 118, 110, 0.2);
            --fd-icon-bg: rgba(240, 253, 250, 0.92);
            background: linear-gradient(180deg, #eefbfb 0%, #fff 100%);
        }

        .fd-tone-amber {
            --fd-icon-color: #c2410c;
            --fd-icon-border: rgba(194, 65, 12, 0.18);
            --fd-icon-bg: rgba(255, 247, 237, 0.94);
            background: linear-gradient(180deg, #fff7d6 0%, #fff 100%);
        }

        .fd-tone-slate {
            --fd-icon-color: #374151;
            --fd-icon-border: rgba(55, 65, 81, 0.18);
            --fd-icon-bg: rgba(248, 250, 252, 0.95);
            background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
        }

        .fd-content-card {
            padding: 1.35rem;
            height: 100%;
        }

        .fd-room-map-card {
            padding: 1.35rem;
            margin-bottom: 1.5rem;
        }

        .fd-room-map-head {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.15rem;
        }

        .fd-room-map-title {
            font-size: 10rem;
            line-height: 1.1;
        }

        .fd-room-map-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 0.65rem;
        }

        .fd-room-legend-item {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .fd-room-legend-dot {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            flex-shrink: 0;
        }

        .fd-room-floor + .fd-room-floor {
            margin-top: 1rem;
        }

        .fd-room-floor-label {
            color: var(--fd-text);
            font-size: 0.92rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
        }

        .fd-room-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 0.8rem;
        }

        .fd-room-tile {
            border-radius: 20px;
            padding: 0.9rem;
            min-height: 112px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid transparent;
        }

        .fd-room-number {
            font-size: 1rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .fd-room-state {
            font-size: 0.9rem;
            font-weight: 700;
            opacity: 0.95;
        }

        .fd-room-tone-empty {
            background: #dcfce7;
            color: #166534;
            border-color: rgba(22, 101, 52, 0.12);
        }

        .fd-room-tone-booked {
            background: #fef3c7;
            color: #92400e;
            border-color: rgba(217, 119, 6, 0.14);
        }

        .fd-room-tone-using {
            background: #dbeafe;
            color: #1d4ed8;
            border-color: rgba(37, 99, 235, 0.14);
        }

        .fd-room-tone-cleaning {
            background: #f3e8ff;
            color: #7e22ce;
            border-color: rgba(147, 51, 234, 0.14);
        }

        .fd-section-title {
            color: var(--fd-text);
            font-size: 1.05rem;
            font-weight: 800;
            margin-bottom: 0.3rem;
        }

        .fd-section-subtitle {
            color: var(--fd-muted);
            font-size: 0.92rem;
            margin-bottom: 1rem;
        }

        .fd-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 0.9rem 0;
            border-bottom: 1px dashed rgba(194, 107, 45, 0.16);
        }

        .fd-list-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .fd-list-title {
            color: var(--fd-text);
            font-weight: 700;
        }

        .fd-list-meta {
            color: var(--fd-muted);
            font-size: 0.88rem;
        }

        .fd-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.38rem 0.8rem;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 700;
        }

        .fd-pill--warn {
            background: #fef3c7;
            color: #9a3412;
        }

        .fd-pill--ok {
            background: #dcfce7;
            color: #166534;
        }

        .fd-pill--info {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .fd-action-card {
            padding: 1.25rem;
            height: 100%;
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .fd-action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 36px rgba(148, 82, 24, 0.12);
            color: inherit;
        }

        .fd-kpi-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.85rem;
        }

        .fd-kpi-item {
            border-radius: 18px;
            background: #fff8f1;
            padding: 1rem;
        }

        .fd-kpi-value {
            color: var(--fd-text);
            font-size: 1.35rem;
            font-weight: 800;
        }

        .fd-kpi-label {
            color: var(--fd-muted);
            font-size: 0.82rem;
            margin-top: 0.3rem;
        }

        @media (max-width: 1199.98px) {
            .fd-summary-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .fd-shell {
                padding-top: 4rem;
            }

            .fd-summary-row {
                grid-template-columns: 1fr;
            }

            .fd-kpi-grid {
                grid-template-columns: 1fr;
            }

            .fd-room-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>

    <div class="fd-shell">
        <div class="fd-summary-row">
            @foreach($summaryCards as $summaryCard)
                <div class="fd-summary-col">
                    <div class="fd-summary-card fd-tone-{{ $summaryCard['tone'] ?? 'sunrise' }}">
                        <div class="fd-summary-icon" aria-hidden="true">
                            @switch($summaryCard['icon'] ?? '')
                                @case('booking')
                                    <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M32 14V50" stroke="currentColor" stroke-width="4.5" stroke-linecap="round"/>
                                        <path d="M14 32H50" stroke="currentColor" stroke-width="4.5" stroke-linecap="round"/>
                                    </svg>
                                    @break
                                @case('check-in')
                                    <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M53 12H24C22.8954 12 22 12.8954 22 14V27" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M53 12H58C59.1046 12 60 12.8954 60 14V66C60 67.1046 59.1046 68 58 68H24C22.8954 68 22 67.1046 22 66V53" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 40H38" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>
                                        <path d="M26 28L38 40L26 52" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    @break
                                @case('check-out')
                                    <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M27 12H56C57.1046 12 58 12.8954 58 14V27" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M27 12H22C20.8954 12 20 12.8954 20 14V66C20 67.1046 20.8954 68 22 68H56C57.1046 68 58 67.1046 58 66V53" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M42 40H68" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>
                                        <path d="M56 28L68 40L56 52" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    @break
                                @case('switch-room')
                                    <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M22 16H40C47.732 16 54 22.268 54 30V31" stroke="currentColor" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M47 24L54 31L61 24" stroke="currentColor" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M42 48H24C16.268 48 10 41.732 10 34V33" stroke="currentColor" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M17 40L10 33L3 40" stroke="currentColor" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    @break
                            @endswitch
                        </div>
                        <div class="fd-summary-label">{{ $summaryCard['label'] ?? '' }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="fd-content-card fd-room-map-card">
            <div class="fd-room-map-head">
                <div>
                    <div class="fd-section-title fd-room-map-title">Sơ đồ phòng</div>
                    <div class="fd-section-subtitle mb-0">Theo dõi trạng thái phòng</div>
                </div>
                <div class="fd-room-map-legend">
                    @foreach($roomStatus as $status)
                        <div class="fd-room-legend-item fd-room-tone-{{ $status['tone'] }}">
                            <span class="fd-room-legend-dot" style="background: currentColor;"></span>
                            {{ $status['label'] }}
                        </div>
                    @endforeach
                </div>
            </div>

            @forelse($roomMapFloors as $floor)
                <div class="fd-room-floor">
                    <div class="fd-room-floor-label">{{ $floor['label'] }}</div>
                    <div class="fd-room-grid">
                        @foreach($floor['rooms'] as $room)
                            <div class="fd-room-tile fd-room-tone-{{ $room['tone'] }}">
                                <div class="fd-room-number">{{ $room['room_number'] }}</div>
                                <div class="fd-room-state">{{ $room['status_label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="fd-list-meta">Chưa có dữ liệu phòng để hiển thị sơ đồ.</div>
            @endforelse
        </div>

        <div class="row g-3">
            <div class="col-xl-6">
                <div class="fd-content-card">
                    <div class="fd-section-title">Khách đến hôm nay</div>
                    <div class="fd-section-subtitle">Những booking cần sẵn sàng check-in trong ca làm việc.</div>
                    @forelse($arrivalsToday as $arrival)
                        <div class="fd-list-item">
                            <div>
                                <div class="fd-list-title">{{ $arrival['TenKH'] ?? '' }}</div>
                                <div class="fd-list-meta">Phòng {{ $arrival['SoPhong'] ?? '' }} • {{ $arrival['LoaiPhong'] ?? '' }}</div>
                            </div>
                            <span class="fd-pill fd-pill--warn">Nhận phòng</span>
                        </div>
                    @empty
                        <div class="fd-list-meta">Không có lượt nhận phòng nào trong hôm nay.</div>
                    @endforelse
                </div>
            </div>

            <div class="col-xl-6">
                <div class="fd-content-card">
                    <div class="fd-section-title">Khách trả phòng hôm nay</div>
                    <div class="fd-section-subtitle">Những phòng cần chuẩn bị checkout và hoàn tất đối soát.</div>
                    @forelse($departuresToday as $departure)
                        <div class="fd-list-item">
                            <div>
                                <div class="fd-list-title">{{ $departure['TenKH'] ?? '' }}</div>
                                <div class="fd-list-meta">Phòng {{ $departure['SoPhong'] ?? '' }} • Trả ngày {{ \Carbon\Carbon::parse($departure['NgayTraPhong'])->format('d/m/Y') }}</div>
                            </div>
                            <span class="fd-pill fd-pill--info">Checkout</span>
                        </div>
                    @empty
                        <div class="fd-list-meta">Hiện chưa có ca checkout nào trong hôm nay.</div>
                    @endforelse
                </div>
            </div>

            <div class="col-xl-7">
                <div class="fd-content-card">
                    <div class="fd-section-title">Hóa đơn cần theo dõi</div>
                    <div class="fd-section-subtitle">Ưu tiên các hóa đơn còn công nợ để hỗ trợ khách trước khi checkout.</div>
                    @forelse($pendingInvoices as $invoice)
                        <div class="fd-list-item">
                            <div>
                                <div class="fd-list-title">Hóa đơn #{{ $invoice['MaHD'] ?? '' }} • Đặt phòng #{{ $invoice['MaDatPhong'] ?? '' }}</div>
                                <div class="fd-list-meta">Nhân viên phụ trách: {{ $invoice['TenNV'] ?? 'Chưa cập nhật' }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">{{ number_format((float) ($invoice['ConLai'] ?? 0), 0, ',', '.') }} VNĐ</div>
                                <span class="fd-pill fd-pill--warn">Còn lại</span>
                            </div>
                        </div>
                    @empty
                        <div class="fd-list-meta">Không còn hóa đơn chờ xử lý.</div>
                    @endforelse
                </div>
            </div>

            <div class="col-xl-5">
                <div class="fd-content-card">
                    <div class="fd-section-title">Tệp khách tại quầy</div>
                    <div class="fd-section-subtitle">Ba chỉ số hữu ích khi xử lý chăm sóc và upsell.</div>
                    <div class="fd-kpi-grid">
                        <div class="fd-kpi-item">
                            <div class="fd-kpi-value">{{ $customerHighlights['active'] ?? 0 }}</div>
                            <div class="fd-kpi-label">Khách đang hoạt động</div>
                        </div>
                        <div class="fd-kpi-item">
                            <div class="fd-kpi-value">{{ $customerHighlights['vip'] ?? 0 }}</div>
                            <div class="fd-kpi-label">Khách VIP</div>
                        </div>
                        <div class="fd-kpi-item">
                            <div class="fd-kpi-value">{{ $customerHighlights['new_today'] ?? 0 }}</div>
                            <div class="fd-kpi-label">Lượt đến hôm nay</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
