<x-app-layout :assets="['animation']">
    @php
        $decode = fn ($value) => html_entity_decode($value, ENT_QUOTES, 'UTF-8');
        $statusLabels = [
            \App\Models\DatPhong::HOLD => $decode('&#272;ang gi&#7919; ch&#7895;'),
            \App\Models\DatPhong::CONFIRMED => $decode('&#272;&#227; &#273;&#7863;t'),
            \App\Models\DatPhong::CHECKED_IN => $decode('&#272;ang s&#7917; d&#7909;ng'),
            \App\Models\DatPhong::CHECKED_OUT => $decode('&#272;&#227; tr&#7843; ph&#242;ng'),
            \App\Models\DatPhong::CANCELLED => $decode('&#272;&#227; h&#7911;y'),
        ];

        $statusClasses = [
            \App\Models\DatPhong::HOLD => 'bd-badge--hold',
            \App\Models\DatPhong::CONFIRMED => 'bd-badge--booked',
            \App\Models\DatPhong::CHECKED_IN => 'bd-badge--using',
            \App\Models\DatPhong::CHECKED_OUT => 'bd-badge--done',
            \App\Models\DatPhong::CANCELLED => 'bd-badge--cancelled',
        ];

        $formatDate = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y') : '--';
        $formatDateTime = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y H:i') : '--';
        $formatMoney = fn ($amount) => number_format((float) ($amount ?? 0), 0, ',', '.') . ' VND';

        $checkIn = $booking->NgayNhanPhong ? \Carbon\Carbon::parse($booking->NgayNhanPhong) : null;
        $checkOut = $booking->NgayTraPhong ? \Carbon\Carbon::parse($booking->NgayTraPhong) : null;
        $nights = ($checkIn && $checkOut) ? max($checkIn->diffInDays($checkOut), 1) : 0;
        $customer = $booking->khachHang;
        $account = $customer?->taiKhoan;
        $invoice = $booking->hoaDon;
        $paidAmount = (float) ($invoice?->DaThanhToan ?? $invoice?->thanhToans?->sum('SoTien') ?? 0);
        $totalAmount = (float) ($invoice?->TongTien ?? 0);
        $remainingAmount = max($totalAmount - $paidAmount, 0);
        $roomDetails = $booking->chiTietDatPhong ?? collect();
        $selectedRoomDetail = $selectedRoomDetail ?? null;
        $selectedRoom = $selectedRoomDetail?->phong ?? $roomDetails->first()?->phong;
        $stayGuestsByRoom = ($booking->luuTrus ?? collect())->groupBy(fn ($guest) => (int) $guest->MaPhong);
        $serviceUsages = $booking->suDungDichVu ?? collect();
    @endphp

    <style>
        .bd-shell { padding-top: 4.75rem; }
        .bd-hero,
        .bd-section {
            border: 1px solid rgba(217, 119, 6, 0.14);
            border-radius: 28px;
            background: #fffdfa;
            box-shadow: 0 24px 60px rgba(148, 82, 24, 0.08);
        }
        .bd-hero { padding: 1.75rem; margin-bottom: 1.5rem; }
        .bd-section { padding: 1.35rem; height: 100%; }
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
            min-width: 0;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .bd-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.45rem 0.9rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.82rem;
        }
        .bd-badge--hold { background: #ffedd5; color: #9a3412; }
        .bd-badge--booked { background: #fef3c7; color: #92400e; }
        .bd-badge--using { background: #dbeafe; color: #1d4ed8; }
        .bd-badge--done { background: #dcfce7; color: #166534; }
        .bd-badge--cancelled { background: #fee2e2; color: #991b1b; }
        .bd-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }
        .bd-info-card,
        .bd-list-item,
        .bd-guest-card {
            border-radius: 18px;
            min-width: 0;
            background: #fff;
            padding: 1rem;
        }
        .bd-info-card,
        .bd-list-item { border: 1px solid rgba(217, 119, 6, 0.14); }
        .bd-list { display: grid; gap: 0.85rem; }
        .bd-list-line {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: flex-start;
        }
        .bd-note {
            border-radius: 18px;
            padding: 1rem 1.1rem;
            background: linear-gradient(135deg, #fff5ea, #fffdf9);
            border: 1px dashed rgba(191, 102, 38, 0.28);
            color: #7a4b27;
        }
        .bd-guest-panel {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed rgba(217, 119, 6, 0.22);
        }
        .bd-guest-list {
            display: grid;
            gap: 0.75rem;
            margin-top: 0.75rem;
        }
        .bd-guest-card {
            border: 1px solid rgba(15, 118, 110, 0.14);
            background: #f8fffd;
        }
        .bd-guest-head {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 0.65rem;
        }
        .bd-guest-name {
            color: #164e45;
            font-size: 1rem;
            font-weight: 700;
        }
        .bd-guest-meta {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.65rem;
        }

        .bd-guest-meta-placeholder {
            visibility: hidden;
        }

        @media (max-width: 991.98px) {
            .bd-guest-meta {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 575.98px) {
            .bd-guest-meta {
                grid-template-columns: 1fr;
            }

            .bd-guest-meta-placeholder {
                display: none;
            }
        }
    </style>

    <div class="bd-shell">
        <div class="bd-hero">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                        <h2 class="mb-0">Chi ti&#7871;t ph&#242;ng {{ $selectedRoom?->SoPhong ?? '--' }}</h2>
                        <span class="bd-badge {{ $statusClasses[(int) $booking->TinhTrang] ?? 'bd-badge--booked' }}">
                            {{ $statusLabels[(int) $booking->TinhTrang] ?? $decode('Kh&#244;ng x&#225;c &#273;&#7883;nh') }}
                        </span>
                    </div>
                    <p class="text-muted mb-0">Thu&#7897;c &#273;&#7863;t ph&#242;ng #{{ $booking->MaDatPhong }}. D&#7919; li&#7879;u kh&#225;ch l&#432;u tr&#250; ch&#7881; hi&#7875;n th&#7883; cho ph&#242;ng n&#224;y.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('reception.bookings.index') }}" class="btn btn-light">Danh s&#225;ch &#273;&#7863;t ph&#242;ng</a>
                    <a href="{{ route('reception.dashboard') }}" class="btn btn-primary">Quay l&#7841;i s&#417; &#273;&#7891; ph&#242;ng</a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-6">
                <div class="bd-section">
                    <h4 class="mb-3">Th&#244;ng tin kh&#225;ch h&#224;ng &#273;&#7863;t</h4>
                    <div class="bd-info-grid">
                        <div class="bd-info-card"><div class="bd-label">M&#227; kh&#225;ch h&#224;ng</div><div class="bd-value">{{ $customer?->MaKH ?? '--' }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">T&#234;n kh&#225;ch h&#224;ng</div><div class="bd-value">{{ $customer?->TenKH ?? '--' }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">S&#7889; &#273;i&#7879;n tho&#7841;i</div><div class="bd-value">{{ $customer?->SoDienThoai ?? '--' }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">Email</div><div class="bd-value">{{ $account?->Email ?? '--' }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">CCCD</div><div class="bd-value">{{ $customer?->CCCD ?? '--' }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">&#272;&#7883;a ch&#7881;</div><div class="bd-value">{{ $customer?->DiaChi ?? '--' }}</div></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="bd-section">
                    <h4 class="mb-3">Th&#244;ng tin &#273;&#7863;t ph&#242;ng</h4>
                    <div class="bd-info-grid">
                        <div class="bd-info-card"><div class="bd-label">M&#227; &#273;&#7863;t ph&#242;ng</div><div class="bd-value">#{{ $booking->MaDatPhong }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">Ng&#224;y &#273;&#7863;t ph&#242;ng</div><div class="bd-value">{{ $formatDate($booking->NgayDat) }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">Ng&#224;y nh&#7853;n ph&#242;ng</div><div class="bd-value">{{ $formatDate($booking->NgayNhanPhong) }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">Ng&#224;y tr&#7843; ph&#242;ng</div><div class="bd-value">{{ $formatDate($booking->NgayTraPhong) }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">Th&#7901;i gian l&#432;u tr&#250;</div><div class="bd-value">{{ $nights }} &#273;&#234;m</div></div>
                        <div class="bd-info-card"><div class="bd-label">S&#7889; l&#432;&#7907;ng kh&#225;ch</div><div class="bd-value">{{ $booking->SoLuong ?? 0 }} kh&#225;ch</div></div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="bd-section">
                    <h4 class="mb-3">Ph&#242;ng &#273;ang xem</h4>
                    @if($roomDetails->isNotEmpty())
                        <div class="bd-list">
                            @foreach($roomDetails as $detail)
                                @php
                                    $room = $detail->phong;
                                    $roomType = $room?->loaiPhong;
                                    $price = $roomType?->GiaGiam ?? $roomType?->GiaPhong;
                                    $roomGuests = $stayGuestsByRoom->get((int) ($room?->MaPhong ?? 0), collect());
                                @endphp
                                <div class="bd-list-item">
                                    <div class="bd-list-line">
                                        <div>
                                            <div class="bd-label">Ph&#242;ng {{ $room?->SoPhong ?? '--' }}</div>
                                            <div class="bd-value">{{ $roomType?->TenLoaiPhong ?? $decode('Ch&#432;a c&#243; lo&#7841;i ph&#242;ng') }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="bd-label">Gi&#225; ph&#242;ng</div>
                                            <div class="bd-value">{{ $price !== null ? $formatMoney($price) . $decode(' / &#273;&#234;m') : '--' }}</div>
                                        </div>
                                    </div>
                                    <div class="bd-info-grid mt-3">
                                        <div><div class="bd-label">M&#227; ph&#242;ng</div><div class="bd-value">{{ $room?->MaPhong ?? '--' }}</div></div>
                                        <div><div class="bd-label">M&#227; lo&#7841;i ph&#242;ng</div><div class="bd-value">{{ $roomType?->MaLoaiPhong ?? '--' }}</div></div>
                                        <div><div class="bd-label">Ng&#432;&#7901;i l&#7899;n t&#7889;i &#273;a</div><div class="bd-value">{{ $roomType?->NguoiLon ?? 0 }}</div></div>
                                        <div><div class="bd-label">Tr&#7867; em t&#7889;i &#273;a</div><div class="bd-value">{{ $roomType?->TreEm ?? 0 }}</div></div>
                                    </div>
                                    <div class="bd-guest-panel">
                                        <div class="bd-list-line">
                                            <div>
                                                <div class="bd-label">Kh&#225;ch l&#432;u tr&#250;</div>
                                                <div class="bd-value">{{ $roomGuests->count() }} kh&#225;ch</div>
                                            </div>
                                        </div>

                                        @if($roomGuests->isNotEmpty())
                                            <div class="bd-guest-list">
                                                @foreach($roomGuests as $guest)
                                                    @php
                                                        $guestAge = $guest->NgaySinh ? \Carbon\Carbon::parse($guest->NgaySinh)->age : null;
                                                    @endphp
                                                    <div class="bd-guest-card">
                                                        <div class="bd-guest-head">
                                                            <div class="bd-guest-name">{{ $guest->TenKhach ?? $decode('Kh&#225;ch l&#432;u tr&#250;') }}</div>
                                                            <span class="bd-badge {{ $guestAge !== null && $guestAge < 12 ? 'bd-badge--hold' : 'bd-badge--using' }}">
                                                                {{ $guestAge !== null && $guestAge < 12 ? $decode('Tr&#7867; em') : $decode('Ng&#432;&#7901;i l&#7899;n') }}
                                                            </span>
                                                        </div>
                                                        <div class="bd-guest-meta">
                                                            <div><div class="bd-label">Ng&#224;y sinh</div><div class="bd-value">{{ $formatDate($guest->NgaySinh) }}</div></div>
                                                            <div><div class="bd-label">Tu&#7893;i</div><div class="bd-value">{{ $guestAge !== null ? $guestAge . $decode(' tu&#7893;i') : '--' }}</div></div>
                                                            <div><div class="bd-label">CCCD</div><div class="bd-value">{{ $guest->CCCD ?: '--' }}</div></div>
                                                            @if($guestAge === null || $guestAge >= 12)
                                                                <div><div class="bd-label">S&#7889; &#273;i&#7879;n tho&#7841;i</div><div class="bd-value">{{ $guest->SoDienThoai ?: '--' }}</div></div>
                                                            @else
                                                                <div class="bd-guest-meta-placeholder" aria-hidden="true"><div class="bd-label">S&#7889; &#273;i&#7879;n tho&#7841;i</div><div class="bd-value">--</div></div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="bd-note mt-3">Ph&#242;ng n&#224;y ch&#432;a c&#243; kh&#225;ch l&#432;u tr&#250;.</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bd-note">Ch&#432;a c&#243; ph&#242;ng n&#224;o trong &#273;&#7863;t ph&#242;ng n&#224;y.</div>
                    @endif
                </div>
            </div>

            <div class="col-xl-6">
                <div class="bd-section">
                    <h4 class="mb-3">H&#243;a &#273;&#417;n</h4>
                    <div class="bd-info-grid">
                        <div class="bd-info-card"><div class="bd-label">M&#227; h&#243;a &#273;&#417;n</div><div class="bd-value">{{ $invoice?->MaHD ? '#' . $invoice->MaHD : '--' }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">Ng&#224;y l&#7853;p</div><div class="bd-value">{{ $formatDate($invoice?->NgayLapHD) }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">T&#7893;ng ti&#7873;n</div><div class="bd-value">{{ $formatMoney($totalAmount) }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">&#272;&#227; thanh to&#225;n</div><div class="bd-value">{{ $formatMoney($paidAmount) }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">C&#242;n l&#7841;i</div><div class="bd-value">{{ $formatMoney($remainingAmount) }}</div></div>
                        <div class="bd-info-card"><div class="bd-label">Khuy&#7871;n m&#227;i</div><div class="bd-value">{{ $invoice?->khuyenMai?->TenKM ?? ($invoice?->MaKM ?? '--') }}</div></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="bd-section">
                    <h4 class="mb-3">D&#7883;ch v&#7909; &#273;&#227; s&#7917; d&#7909;ng</h4>
                    @if($serviceUsages->isNotEmpty())
                        <div class="bd-list">
                            @foreach($serviceUsages as $usage)
                                <div class="bd-list-item">
                                    <div class="bd-list-line">
                                        <div>
                                            <div class="bd-label">{{ $usage->dichVu?->TenDV ?? $decode('D&#7883;ch v&#7909;') }}</div>
                                            <div class="bd-value">S&#7889; l&#432;&#7907;ng: {{ $usage->SoLuong ?? 0 }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="bd-label">Th&#7901;i gian</div>
                                            <div class="bd-value">{{ $formatDateTime($usage->ThoiGian) }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bd-note">Ch&#432;a c&#243; d&#7883;ch v&#7909; n&#224;o &#273;&#432;&#7907;c &#273;&#259;ng k&#253; cho &#273;&#7863;t ph&#242;ng n&#224;y.</div>
                    @endif
                </div>
            </div>

            <div class="col-12">
                <div class="bd-section">
                    <h4 class="mb-3">Chi ti&#7871;t h&#243;a &#273;&#417;n</h4>
                    @if($invoice && $invoice->chiTietHoaDons->isNotEmpty())
                        <div class="bd-list">
                            @foreach($invoice->chiTietHoaDons as $item)
                                @php
                                    $itemName = $item->loaiPhong?->TenLoaiPhong
                                        ?? $item->suDung?->dichVu?->TenDV
                                        ?? $item->denBu?->MoTa
                                        ?? $item->MoTa
                                        ?? $decode('Kho&#7843;n thu');
                                    $lineTotal = (float) $item->SoLuong * (float) $item->DonGia;
                                @endphp
                                <div class="bd-list-item">
                                    <div class="bd-list-line">
                                        <div>
                                            <div class="bd-label">{{ $itemName }}</div>
                                            <div class="bd-value">{{ $item->MoTa ?? $decode('Chi ti&#7871;t #') . $item->MaCTHD }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="bd-label">{{ $item->SoLuong }} x {{ $formatMoney($item->DonGia) }}</div>
                                            <div class="bd-value">{{ $formatMoney($lineTotal) }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bd-note">Ch&#432;a c&#243; chi ti&#7871;t h&#243;a &#273;&#417;n &#273;&#7875; hi&#7875;n th&#7883;.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
