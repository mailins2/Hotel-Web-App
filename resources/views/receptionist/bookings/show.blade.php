@php
    $customer = $booking?->khachHang;
    $details = collect($booking?->chiTietDatPhong ?? []);
    $stays = collect($booking?->luuTrus ?? []);
    $roomNumbers = $details
        ->map(fn ($detail) => $detail->phong?->SoPhong)
        ->filter()
        ->values()
        ->implode(', ');
    $roomTypes = $details
        ->map(fn ($detail) => $detail->phong?->loaiPhong?->TenLoaiPhong)
        ->filter()
        ->unique()
        ->values()
        ->implode(', ');
    $capacityAdults = $details->sum(fn ($detail) => (int) ($detail->phong?->loaiPhong?->NguoiLon ?? 0));
    $capacityChildren = $details->sum(fn ($detail) => (int) ($detail->phong?->loaiPhong?->TreEm ?? 0));
    $capacityLabel = $details->isNotEmpty()
        ? "{$capacityAdults} người lớn, {$capacityChildren} trẻ em"
        : '--';
    $formatCapacity = fn ($roomType) => $roomType
        ? ((int) ($roomType->NguoiLon ?? 0)) . ' người lớn, ' . ((int) ($roomType->TreEm ?? 0)) . ' trẻ em'
        : '--';
    $formatDate = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y') : '--';
    $statusLabels = [
        \App\Models\DatPhong::HOLD => 'Chờ xác nhận',
        \App\Models\DatPhong::CONFIRMED => 'Đã xác nhận',
        \App\Models\DatPhong::CHECKED_IN => 'Đang ở',
        \App\Models\DatPhong::CHECKED_OUT => 'Đã trả phòng',
        \App\Models\DatPhong::CANCELLED => 'Đã hủy',
    ];
    $detailStatusLabels = [
        \App\Models\ChiTietDatPhong::BOOKED => 'Đã đặt',
        \App\Models\ChiTietDatPhong::CHECKED_IN => 'Đang ở',
        \App\Models\ChiTietDatPhong::CHECKED_OUT => 'Đã trả phòng',
        \App\Models\ChiTietDatPhong::CANCELLED => 'Đã hủy',
    ];
    $guestAge = fn ($guest) => $guest?->NgaySinh ? \Carbon\Carbon::parse($guest->NgaySinh)->age : null;
    $adultCount = $stays->filter(fn ($guest) => ($guestAge($guest) ?? 12) >= 12)->count();
    $childCount = $stays->filter(fn ($guest) => ($guestAge($guest) ?? 12) < 12)->count();
    $guestCountLabel = $stays->isNotEmpty()
        ? "{$adultCount} người lớn, {$childCount} trẻ em"
        : '--';
@endphp

<x-receptionist.show-page
    title="Chi tiết đặt phòng"
    subtitle="Thông tin chi tiết đặt phòng #{{ $booking->MaDatPhong }}"
    :index-route="route('reception.bookings.index')"
>
    <x-slot:actions>
        @if($booking->hoaDon?->MaHD)
            <a
                href="{{ route('reception.invoices.show', ['invoiceId' => $booking->hoaDon->MaHD]) }}"
                class="btn btn-sm btn-light"
                style="padding: 10px;"
            >
                Xem Hóa Đơn
            </a>
        @endif
    </x-slot:actions>

    <style>
        .booking-show-section {
            border: 1px solid rgba(151, 64, 26, 0.12);
            border-radius: 14px;
            padding: 18px;
            height: 100%;
            background: #fffefa;
        }

        .booking-show-section-title {
            margin: 0 0 14px;
            color: #5f1b08;
            font-size: 18px;
            font-weight: 700;
        }

        .booking-show-info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .booking-show-info-item {
            min-width: 0;
            padding: 12px 0;
            border-bottom: 1px solid rgba(151, 64, 26, 0.1);
        }

        .booking-show-info-item:nth-last-child(-n+2) {
            border-bottom: 0;
        }

        .booking-show-info-item--wide {
            grid-column: 1 / -1;
        }

        .booking-show-label {
            margin-bottom: 4px;
            color: #8a5a4d;
            font-size: 13px;
        }

        .booking-show-value {
            color: #2d0904;
            font-size: 16px;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        @media (max-width: 575.98px) {
            .booking-show-info-grid {
                grid-template-columns: 1fr;
            }

            .booking-show-info-item:nth-last-child(-n+2) {
                border-bottom: 1px solid rgba(151, 64, 26, 0.1);
            }

            .booking-show-info-item:last-child {
                border-bottom: 0;
            }
        }
    </style>

    <div class="col-lg-4 mb-4">
        <section class="booking-show-section">
            <h5 class="booking-show-section-title">Thông tin đặt phòng</h5>
            <div class="booking-show-info-grid">
                <div class="booking-show-info-item">
                    <div class="booking-show-label">Mã đặt phòng</div>
                    <div class="booking-show-value">{{ $booking->MaDatPhong }}</div>
                </div>
                <div class="booking-show-info-item">
                    <div class="booking-show-label">Tình trạng</div>
                    <div class="booking-show-value">{{ $statusLabels[(int) $booking->TinhTrang] ?? 'Không xác định' }}</div>
                </div>
                <div class="booking-show-info-item booking-show-info-item--wide">
                    <div class="booking-show-label">Ngày đặt</div>
                    <div class="booking-show-value">{{ $formatDate($booking->NgayDat) }}</div>
                </div>
                <div class="booking-show-info-item booking-show-info-item--wide">
                    <div class="booking-show-label">Sức chứa</div>
                    <div class="booking-show-value">{{ $capacityLabel }}</div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-lg-8 mb-4">
        <section class="booking-show-section">
            <h5 class="booking-show-section-title">Thông tin khách hàng</h5>
            <div class="booking-show-info-grid">
                <div class="booking-show-info-item">
                    <div class="booking-show-label">Mã khách hàng</div>
                    <div class="booking-show-value">{{ $customer?->MaKH ?? '--' }}</div>
                </div>
                <div class="booking-show-info-item">
                    <div class="booking-show-label">Tên khách hàng</div>
                    <div class="booking-show-value">{{ $customer?->TenKH ?? '--' }}</div>
                </div>
                <div class="booking-show-info-item">
                    <div class="booking-show-label">Số điện thoại</div>
                    <div class="booking-show-value">{{ $customer?->SoDienThoai ?? '--' }}</div>
                </div>
                <div class="booking-show-info-item">
                    <div class="booking-show-label">CCCD</div>
                    <div class="booking-show-value">{{ $customer?->CCCD ?? '--' }}</div>
                </div>
                <div class="booking-show-info-item booking-show-info-item--wide">
                    <div class="booking-show-label">Địa chỉ</div>
                    <div class="booking-show-value">{{ $customer?->DiaChi ?? '--' }}</div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-12 mb-4">
        <section class="booking-show-section">
            <h5 class="booking-show-section-title">Danh sách phòng đặt</h5>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Mã CTĐP</th>
                            <th>Phòng</th>
                            <th>Loại phòng</th>
                            <th>Sức chứa</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($details as $detail)
                            <tr>
                                <td>{{ $detail->MaCTDP }}</td>
                                <td>{{ $detail->phong?->SoPhong ?? '--' }}</td>
                                <td>{{ $detail->phong?->loaiPhong?->TenLoaiPhong ?? '--' }}</td>
                                <td>{{ $formatCapacity($detail->phong?->loaiPhong) }}</td>
                                <td>{{ $detailStatusLabels[(int) $detail->TrangThai] ?? 'Không xác định' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Chưa có thông tin phòng đặt.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="col-12 mb-4">
        <section class="booking-show-section">
            <h5 class="booking-show-section-title">Thông tin lưu trú</h5>
            <div class="booking-show-info-grid">
                <div class="booking-show-info-item">
                    <div class="booking-show-label">Ngày nhận phòng</div>
                    <div class="booking-show-value">{{ $formatDate($booking->NgayNhanPhong) }}</div>
                </div>
                <div class="booking-show-info-item">
                    <div class="booking-show-label">Ngày trả phòng</div>
                    <div class="booking-show-value">{{ $formatDate($booking->NgayTraPhong) }}</div>
                </div>
                <div class="booking-show-info-item">
                    <div class="booking-show-label">Phòng</div>
                    <div class="booking-show-value">{{ $roomNumbers ?: '--' }}</div>
                </div>
                <div class="booking-show-info-item">
                    <div class="booking-show-label">Số lượng người ở</div>
                    <div class="booking-show-value">{{ $guestCountLabel }}</div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-12 mt-4">
        <section class="booking-show-section">
            <h5 class="booking-show-section-title">Danh sách khách lưu trú</h5>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Mã lưu trú</th>
                            <th>Khách lưu trú</th>
                            <th>Phòng</th>
                            <th>Ngày sinh</th>
                            <th>Phân loại</th>
                            <th>Số điện thoại</th>
                            <th>CCCD</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stays as $stay)
                            @php
                                $age = $guestAge($stay);
                                $guestType = $age !== null && $age < 12 ? 'Trẻ em' : 'Người lớn';
                            @endphp
                            <tr>
                                <td>{{ $stay->MaLuuTru }}</td>
                                <td>{{ $stay->TenKhach ?? '--' }}</td>
                                <td>{{ $stay->phong?->SoPhong ?? '--' }}</td>
                                <td>{{ $formatDate($stay->NgaySinh) }}</td>
                                <td>{{ $guestType }}</td>
                                <td>{{ $stay->SoDienThoai ?? '--' }}</td>
                                <td>{{ $stay->CCCD ?? '--' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Chưa có danh sách khách lưu trú.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-receptionist.show-page>
