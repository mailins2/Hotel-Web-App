<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Peach Valley</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_hotel.png') }}">
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:200,300,400,600,700&display=swap" rel="stylesheet">
    @vite(['resources/customer/css/site.css', 'resources/customer/js/site.js'])
  </head>
  <body class="booking-page customer-account-page">
    @include('customer.partials.nav')

    <section class="customer-account-section">
      <div class="container">
        <div class="customer-account-shell">
          @include('customer.partials.account-sidebar', ['active' => 'bookings'])

          <main class="customer-account-main">
            <div class="customer-account-heading">
              <div class="eyebrow">Đặt phòng của bạn</div>
            </div>

            @php
              $statusLabels = [
                \App\Models\DatPhong::HOLD => 'Chờ thanh toán',
                \App\Models\DatPhong::CONFIRMED => 'Đã xác nhận',
                \App\Models\DatPhong::CHECKED_IN => 'Đang ở',
                \App\Models\DatPhong::CHECKED_OUT => 'Đã trả phòng',
                \App\Models\DatPhong::CANCELLED => 'Đã hủy',
              ];
            @endphp

            @if(($customerBookings ?? collect())->isNotEmpty())
              <div class="customer-booking-filter" data-booking-status-filter>
                <span>Lọc trạng thái</span>
                <div class="customer-booking-filter-options">
                  <button type="button" class="is-active" data-booking-filter-value="all">Tất cả</button>
                  @foreach($statusLabels as $statusValue => $label)
                    <button type="button" data-booking-filter-value="{{ $statusValue }}">{{ $label }}</button>
                  @endforeach
                </div>
              </div>
            @endif

            <div class="customer-booking-list">
              @forelse(($customerBookings ?? collect()) as $booking)
                @php
                  $customer = $booking->khachHang;
                  $invoice = $booking->hoaDon;
                  $roomGroups = $booking->chiTietDatPhong->groupBy(fn ($detail) => $detail->phong?->MaLoaiPhong ?? 'unknown');
                  $invoiceDetails = collect($invoice?->chiTietHoaDons ?? []);
                  $checkIn = \Illuminate\Support\Carbon::parse($booking->NgayNhanPhong);
                  $checkOut = \Illuminate\Support\Carbon::parse($booking->NgayTraPhong);
                  $nights = max($checkIn->diffInDays($checkOut), 1);
                  $statusLabel = $statusLabels[(int) $booking->TinhTrang] ?? 'Đã đặt';
                  $canCancel = in_array((int) $booking->TinhTrang, [\App\Models\DatPhong::HOLD, \App\Models\DatPhong::CONFIRMED], true)
                    && $checkIn->copy()->startOfDay()->isFuture();
                  $rooms = $roomGroups->map(function ($items, $roomTypeId) use ($invoiceDetails, $nights) {
                    $first = $items->first();
                    $roomType = $first?->phong?->loaiPhong;
                    $invoiceDetail = $invoiceDetails->firstWhere('MaLoaiPhong', is_numeric($roomTypeId) ? (int) $roomTypeId : $roomTypeId);
                    $roomCount = $items->count();
                    $roomNumbers = $items
                      ->map(fn ($item) => $item->phong?->SoPhong)
                      ->filter()
                      ->values()
                      ->implode(', ');
                    $lineTotal = (float) (($invoiceDetail?->SoLuong ?? $roomCount) * ($invoiceDetail?->DonGia ?? 0));

                    return [
                      'TenPhong' => $roomType?->TenLoaiPhong ?? 'Phòng',
                      'SoPhong' => $roomNumbers,
                      'SoLuongPhong' => $roomCount,
                      'SoKhach' => $roomCount * ((int) ($roomType?->NguoiLon ?? 0) + (int) ($roomType?->TreEm ?? 0)),
                      'GiaMoiDem' => $nights > 0 ? (float) (($invoiceDetail?->DonGia ?? 0) / $nights) : 0,
                      'ThanhTien' => $lineTotal,
                    ];
                  })->values();
                  $summaryTitle = $rooms->pluck('TenPhong')->unique()->implode(', ') ?: 'Đặt phòng Peach Valley';
                  $invoiceOriginalTotal = (float) $rooms->sum('ThanhTien');
                  $invoiceFinalTotal = (float) ($invoice?->TongTien ?? $invoiceOriginalTotal);
                  $voucherPercent = (float) ($invoice?->khuyenMai?->PhanTramGiamGia ?? 0);
                  $voucherCode = $invoice?->MaKM;
                  $voucherDiscount = max($invoiceOriginalTotal - $invoiceFinalTotal, 0);
                  $payload = [
                    'MaDatPhong' => $booking->MaDatPhong,
                    'MaKH' => $booking->MaKH,
                    'TenKH' => $customer?->TenKH,
                    'SoDienThoai' => $customer?->SoDienThoai,
                    'Email' => $customer?->taiKhoan?->Email,
                    'CCCD' => $customer?->CCCD,
                    'NgayDat' => $booking->NgayDat,
                    'NgayNhanPhong' => $booking->NgayNhanPhong,
                    'NgayTraPhong' => $booking->NgayTraPhong,
                    'SoDem' => $nights,
                    'TongSoKhach' => $rooms->sum('SoKhach') ?: $booking->SoLuong,
                    'StatusLabel' => $statusLabel,
                    'SummaryTitle' => $summaryTitle,
                    'Rooms' => $rooms,
                    'TongTienGoc' => $invoiceOriginalTotal,
                    'TongTien' => $invoiceFinalTotal,
                    'MaKM' => $voucherCode,
                    'PhanTramGiam' => $voucherPercent,
                    'TienGiam' => $voucherDiscount,
                    'TienDatCoc' => (float) ($invoice?->DaThanhToan ?? 0),
                  ];
                @endphp

                <div
                  class="customer-booking-item customer-booking-card"
                  role="button"
                  tabindex="0"
                  data-booking-card
                  data-booking-status="{{ (int) $booking->TinhTrang }}"
                  data-booking-payload="{!! e(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) !!}"
                >
                  <div class="customer-booking-card-left">
                    <div class="customer-booking-top">
                      <div>
                        <div class="customer-booking-code">Đặt phòng #{{ $booking->MaDatPhong }}</div>
                        <h3 class="customer-booking-room">{{ $summaryTitle }}</h3>
                      </div>
                      <span class="customer-booking-status">{{ $statusLabel }}</span>
                    </div>

                    <div class="customer-booking-room-list">
                      @foreach($rooms as $room)
                        <div class="customer-booking-room-line">
                          <strong>{{ $room['TenPhong'] }}</strong>
                          <span>{{ $room['SoLuongPhong'] }} phòng{{ $room['SoPhong'] ? ' - ' . $room['SoPhong'] : '' }}</span>
                        </div>
                      @endforeach
                    </div>

                    <div class="customer-booking-date-line">
                      {{ $checkIn->format('d/m/Y') }}
                      -
                      {{ $checkOut->format('d/m/Y') }}
                      • {{ $nights }} đêm • {{ $payload['TongSoKhach'] }} khách
                    </div>
                  </div>

                  <div class="customer-booking-card-right">
                    <div class="customer-booking-person-title">Thông tin cá nhân</div>
                    <div class="customer-booking-person-grid">
                      <div class="customer-booking-field"><span>Khách hàng</span>{{ $customer?->TenKH ?? '--' }}</div>
                      <div class="customer-booking-field"><span>Số điện thoại</span>{{ $customer?->SoDienThoai ?? '--' }}</div>
                      <div class="customer-booking-field"><span>Email</span>{{ $customer?->taiKhoan?->Email ?? '--' }}</div>
                      <div class="customer-booking-field"><span>CCCD</span>{{ $customer?->CCCD ?? '--' }}</div>
                    </div>

                    <div class="customer-booking-money">
                      <span>Tổng tiền</span>
                      <strong>{{ number_format($invoiceFinalTotal, 0, ',', '.') }} VND</strong>
                    </div>

                    @if($voucherCode && $voucherDiscount > 0)
                      <div class="customer-booking-discount">
                        <span>Mã {{ $voucherCode }}{{ $voucherPercent > 0 ? ' -' . rtrim(rtrim(number_format($voucherPercent, 2, ',', '.'), '0'), ',') . '%' : '' }}</span>
                        <strong>-{{ number_format($voucherDiscount, 0, ',', '.') }} VND</strong>
                        <small>Giá gốc: {{ number_format($invoiceOriginalTotal, 0, ',', '.') }} VND</small>
                      </div>
                    @endif

                    <div class="customer-booking-money">
                      <span>Đã thanh toán</span>
                      <strong>{{ number_format((float) ($invoice?->DaThanhToan ?? 0), 0, ',', '.') }} VND</strong>
                    </div>

                    @if($canCancel)
                      <div class="customer-booking-actions">
                        <button
                          type="button"
                          class="customer-booking-cancel"
                          data-cancel-booking
                          data-booking-id="{{ $booking->MaDatPhong }}"
                        >
                          Hủy phòng
                        </button>
                      </div>
                    @endif
                  </div>
                </div>
              @empty
                <div class="customer-booking-item customer-booking-card">
                  <div class="customer-booking-card-left">
                    <div class="customer-booking-top">
                      <div>
                        <div class="customer-booking-code">Chưa có đặt phòng</div>
                        <h3 class="customer-booking-room">Các đặt phòng đã xác nhận sẽ hiển thị tại đây.</h3>
                      </div>
                    </div>
                  </div>
                </div>
              @endforelse

              <div class="customer-booking-filter-empty" data-booking-filter-empty hidden>
                Không có đặt phòng nào ở trạng thái này.
              </div>

              @if(false)
              <div
                class="customer-booking-item customer-booking-card"
                role="button"
                tabindex="0"
                data-booking-card
                data-booking-payload='{"MaDatPhong":"PV9010","MaKH":1,"TenKH":"Nguyễn Minh An","SoDienThoai":"0901234567","Email":"minhan@gmail.com","CCCD":"079204000111","NgayDat":"2026-04-12","NgayDatDisplay":"12/04/2026","NgayNhanPhong":"2026-04-18","NgayNhanPhongDisplay":"18/04/2026","NgayTraPhong":"2026-04-21","NgayTraPhongDisplay":"21/04/2026","SoDem":3,"TongSoKhach":8,"StatusLabel":"Đã đặt","SummaryTitle":"Deluxe Twin, Suite Junior","Rooms":[{"TenPhong":"Deluxe Twin","SoPhong":"A101, A102","SoLuongPhong":2,"SoKhach":4,"GiaMoiDem":1732500,"ThanhTien":10395000},{"TenPhong":"Suite Junior","SoPhong":"B201","SoLuongPhong":1,"SoKhach":2,"GiaMoiDem":2100000,"ThanhTien":6300000},{"TenPhong":"Superior King","SoPhong":"C302","SoLuongPhong":1,"SoKhach":2,"GiaMoiDem":1450000,"ThanhTien":4350000}],"TongTien":21045000,"TienDatCoc":10522500}'
              >
                <div class="customer-booking-card-left">
                  <div class="customer-booking-top">
                    <div>
                      <div class="customer-booking-code">Đặt phòng #PV9010</div>
                      <h3 class="customer-booking-room">Deluxe Twin, Suite Junior</h3>
                    </div>
                    <span class="customer-booking-status">Đã đặt</span>
                  </div>

                  <div class="customer-booking-room-list">
                    <div class="customer-booking-room-line">
                      <strong>Deluxe Twin</strong>
                      <span>2 phòng</span>
                    </div>
                    <div class="customer-booking-room-line">
                      <strong>Suite Junior</strong>
                      <span>1 phòng</span>
                    </div>
                    <div class="customer-booking-room-line">
                      <strong>Superior King</strong>
                      <span>1 phòng</span>
                    </div>
                  </div>

                  <div class="customer-booking-date-line">
                    18/04/2026
                    -
                    21/04/2026
                    • 3 đêm • 8 khách
                  </div>
                </div>

                <div class="customer-booking-card-right">
                  <div class="customer-booking-person-title">Thông tin cá nhân</div>
                  <div class="customer-booking-person-grid">
                    <div class="customer-booking-field"><span>Khách hàng</span>Nguyễn Minh An</div>
                    <div class="customer-booking-field"><span>Số điện thoại</span>0901234567</div>
                    <div class="customer-booking-field"><span>Email</span>minhan@gmail.com</div>
                    <div class="customer-booking-field"><span>CCCD</span>079204000111</div>
                  </div>

                  <div class="customer-booking-money">
                    <span>Tổng tiền</span>
                    <strong>21.045.000 VND</strong>
                  </div>

                  <div class="customer-booking-actions">
                    <button
                      type="button"
                      class="customer-booking-cancel"
                      data-cancel-booking
                      data-booking-id="PV9010"
                    >
                      Hủy phòng
                    </button>
                  </div>
                </div>
              </div>

              <div
                class="customer-booking-item customer-booking-card"
                role="button"
                tabindex="0"
                data-booking-card
                data-booking-payload='{"MaDatPhong":"PV9011","MaKH":1,"TenKH":"Nguyễn Minh An","SoDienThoai":"0901234567","Email":"minhan@gmail.com","CCCD":"079204000111","NgayDat":"2026-04-14","NgayDatDisplay":"14/04/2026","NgayNhanPhong":"2026-05-02","NgayNhanPhongDisplay":"02/05/2026","NgayTraPhong":"2026-05-04","NgayTraPhongDisplay":"04/05/2026","SoDem":2,"TongSoKhach":4,"StatusLabel":"Đã đặt","SummaryTitle":"Deluxe Family, Standard Garden","Rooms":[{"TenPhong":"Deluxe Family","SoPhong":"D401","SoLuongPhong":1,"SoKhach":3,"GiaMoiDem":1840000,"ThanhTien":3680000},{"TenPhong":"Standard Garden","SoPhong":"D402","SoLuongPhong":1,"SoKhach":1,"GiaMoiDem":900000,"ThanhTien":1800000}],"TongTien":5480000,"TienDatCoc":2740000}'
              >
                <div class="customer-booking-card-left">
                  <div class="customer-booking-top">
                    <div>
                      <div class="customer-booking-code">Đặt phòng #PV9011</div>
                      <h3 class="customer-booking-room">Deluxe Family, Standard Garden</h3>
                    </div>
                    <span class="customer-booking-status">Đã đặt</span>
                  </div>

                  <div class="customer-booking-room-list">
                    <div class="customer-booking-room-line">
                      <strong>Deluxe Family</strong>
                      <span>1 phòng</span>
                    </div>
                    <div class="customer-booking-room-line">
                      <strong>Standard Garden</strong>
                      <span>1 phòng</span>
                    </div>
                  </div>

                  <div class="customer-booking-date-line">
                    02/05/2026
                    -
                    04/05/2026
                    • 2 đêm • 4 khách
                  </div>
                </div>

                <div class="customer-booking-card-right">
                  <div class="customer-booking-person-title">Thông tin cá nhân</div>
                  <div class="customer-booking-person-grid">
                    <div class="customer-booking-field"><span>Khách hàng</span>Nguyễn Minh An</div>
                    <div class="customer-booking-field"><span>Số điện thoại</span>0901234567</div>
                    <div class="customer-booking-field"><span>Email</span>minhan@gmail.com</div>
                    <div class="customer-booking-field"><span>CCCD</span>079204000111</div>
                  </div>

                  <div class="customer-booking-money">
                    <span>Tổng tiền</span>
                    <strong>5.480.000 VND</strong>
                  </div>

                  <div class="customer-booking-actions">
                    <button
                      type="button"
                      class="customer-booking-cancel"
                      data-cancel-booking
                      data-booking-id="PV9011"
                    >
                      Hủy phòng
                    </button>
                  </div>
                </div>
              </div>
              @endif
            </div>
          </main>
        </div>
      </div>
    </section>

    <div class="customer-booking-detail-modal" data-booking-detail-modal hidden>
      <div class="customer-booking-detail-backdrop" data-booking-detail-close></div>
      <div class="customer-booking-detail-dialog" role="dialog" aria-modal="true" aria-labelledby="bookingDetailTitle">
        <button type="button" class="customer-booking-detail-close" data-booking-detail-close aria-label="Đóng">×</button>
        <div class="customer-booking-detail-head">
          <div>
            <span data-booking-detail-code>Đặt phòng</span>
            <h3 id="bookingDetailTitle" data-booking-detail-title>Chi tiết đặt phòng</h3>
          </div>
          <strong data-booking-detail-status>Đã đặt</strong>
        </div>

        <div class="customer-booking-detail-grid">
          <div class="customer-booking-detail-section">
            <h4>Thông tin phòng đã đặt</h4>
            <div data-booking-detail-rooms></div>
          </div>
          <div class="customer-booking-detail-section">
            <h4>Thông tin cá nhân</h4>
            <div class="customer-booking-detail-fields" data-booking-detail-person></div>
          </div>
        </div>

        <div class="customer-booking-detail-summary">
          <div><span>Ngày nhận phòng</span><strong data-booking-detail-checkin>--</strong></div>
          <div><span>Ngày trả phòng</span><strong data-booking-detail-checkout>--</strong></div>
          <div><span>Tổng số khách ở</span><strong data-booking-detail-guests>0 khách</strong></div>
          <div data-booking-detail-original-row hidden><span>Giá gốc</span><strong data-booking-detail-original>0 VND</strong></div>
          <div data-booking-detail-discount-row hidden><span data-booking-detail-discount-label>Khuyến mãi</span><strong data-booking-detail-discount>0 VND</strong></div>
          <div><span>Tổng tiền thanh toán</span><strong data-booking-detail-total>0 VND</strong></div>
          <div><span>Tiền đặt cọc</span><strong data-booking-detail-deposit>0 VND</strong></div>
        </div>
      </div>
    </div>

    <div class="customer-cancel-modal" data-cancel-modal hidden>
      <div class="customer-cancel-modal-backdrop" data-cancel-modal-close></div>
      <div class="customer-cancel-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="cancelBookingTitle">
        <h3 id="cancelBookingTitle">Hủy đặt phòng</h3>
        <p>Bạn có chắc chắn muốn hủy đặt phòng <strong data-cancel-booking-label></strong> không?</p>
        <div class="customer-cancel-modal-actions">
          <button type="button" class="customer-cancel-modal-secondary" data-cancel-modal-close>Không</button>
          <button type="button" class="customer-cancel-modal-primary" data-cancel-modal-confirm>Xác nhận hủy</button>
        </div>
      </div>
    </div>

    <div
      id="customer-bookings-config"
      data-cancel-url-template="{{ route('customer.my-bookings.cancel', ['booking' => '__BOOKING_ID__']) }}"
      hidden
    ></div>

    @include('customer.partials.footer')
    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const cancelModal = document.querySelector('[data-cancel-modal]');
        const cancelLabel = document.querySelector('[data-cancel-booking-label]');
        const cancelConfirmButton = document.querySelector('[data-cancel-modal-confirm]');
        const config = document.getElementById('customer-bookings-config');
        const cancelUrlTemplate = config ? config.dataset.cancelUrlTemplate : '';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const detailModal = document.querySelector('[data-booking-detail-modal]');
        const detailRooms = document.querySelector('[data-booking-detail-rooms]');
        const detailPerson = document.querySelector('[data-booking-detail-person]');
        const bookingFilter = document.querySelector('[data-booking-status-filter]');
        const bookingFilterEmpty = document.querySelector('[data-booking-filter-empty]');
        let selectedCancelBookingId = null;

        const formatCurrency = (value) => `${Number(value || 0).toLocaleString('vi-VN')} VND`;
        const formatDate = (value) => {
          const date = new Date(value);
          return Number.isNaN(date.getTime()) ? '--' : date.toLocaleDateString('vi-VN');
        };

        const openCancelModal = (bookingId) => {
          selectedCancelBookingId = bookingId;
          cancelLabel.textContent = bookingId ? `#${bookingId}` : '';
          cancelModal.hidden = false;
          cancelModal.classList.add('is-open');
        };

        const closeCancelModal = () => {
          selectedCancelBookingId = null;
          cancelModal.classList.remove('is-open');
          cancelModal.hidden = true;
        };

        const cancelBooking = async () => {
          if (!selectedCancelBookingId || !cancelUrlTemplate || !cancelConfirmButton) {
            return;
          }

          const originalText = cancelConfirmButton.textContent;
          cancelConfirmButton.disabled = true;
          cancelConfirmButton.textContent = 'Đang hủy...';

          try {
            const response = await fetch(cancelUrlTemplate.replace('__BOOKING_ID__', selectedCancelBookingId), {
              method: 'POST',
              headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
              },
            });
            const payload = await response.json().catch(() => ({}));

            if (!response.ok || payload.success === false) {
              throw new Error(payload.message || 'Không thể hủy đặt phòng.');
            }

            window.location.reload();
          } catch (error) {
            alert(error.message);
          } finally {
            cancelConfirmButton.disabled = false;
            cancelConfirmButton.textContent = originalText;
          }
        };

        const closeDetailModal = () => {
          detailModal.classList.remove('is-open');
          detailModal.hidden = true;
        };

        const renderDetail = (booking) => {
          document.querySelector('[data-booking-detail-code]').textContent = `Đặt phòng #${booking.MaDatPhong || '--'}`;
          document.querySelector('[data-booking-detail-title]').textContent = `Chi tiết đặt phòng ${booking.SoDem || 1} đêm`;
          document.querySelector('[data-booking-detail-status]').textContent = booking.StatusLabel || 'Đã đặt';
          document.querySelector('[data-booking-detail-checkin]').textContent = formatDate(booking.NgayNhanPhong);
          document.querySelector('[data-booking-detail-checkout]').textContent = formatDate(booking.NgayTraPhong);
          document.querySelector('[data-booking-detail-guests]').textContent = `${booking.TongSoKhach || 1} khách`;
          const originalRow = document.querySelector('[data-booking-detail-original-row]');
          const discountRow = document.querySelector('[data-booking-detail-discount-row]');
          const discountAmount = Number(booking.TienGiam || 0);

          if (originalRow && discountRow) {
            originalRow.hidden = discountAmount <= 0;
            discountRow.hidden = discountAmount <= 0;
          }

          if (discountAmount > 0) {
            document.querySelector('[data-booking-detail-original]').textContent = formatCurrency(booking.TongTienGoc);
            document.querySelector('[data-booking-detail-discount]').textContent = `-${formatCurrency(discountAmount)}`;
            document.querySelector('[data-booking-detail-discount-label]').textContent = booking.MaKM
              ? `Khuyến mãi ${booking.MaKM}${Number(booking.PhanTramGiam || 0) > 0 ? ` -${Number(booking.PhanTramGiam).toLocaleString('vi-VN', { maximumFractionDigits: 2 })}%` : ''}`
              : 'Khuyến mãi';
          }

          document.querySelector('[data-booking-detail-total]').textContent = formatCurrency(booking.TongTien);
          document.querySelector('[data-booking-detail-deposit]').textContent = formatCurrency(booking.TienDatCoc);

          detailRooms.innerHTML = '';
          (booking.Rooms || []).forEach((room) => {
            const row = document.createElement('div');
            row.className = 'customer-booking-detail-room';
            row.innerHTML = `
              <div>
                <strong>${room.TenPhong || 'Phòng'}</strong>
                <span>${room.SoLuongPhong || 1} phòng</span>
              </div>
              <div>${formatCurrency(room.ThanhTien)}</div>
            `;
            detailRooms.appendChild(row);
          });

          detailPerson.innerHTML = `
            <div><span>Khách hàng</span><strong>${booking.TenKH || '--'}</strong></div>
            <div><span>Số điện thoại</span><strong>${booking.SoDienThoai || '--'}</strong></div>
            <div><span>Ngày đặt</span><strong>${formatDate(booking.NgayDat)}</strong></div>
          `;
        };

        const applyBookingStatusFilter = (status) => {
          const cards = Array.from(document.querySelectorAll('[data-booking-card]'));
          let visibleCount = 0;

          cards.forEach((card) => {
            const isVisible = status === 'all' || card.dataset.bookingStatus === status;
            card.hidden = !isVisible;
            if (isVisible) {
              visibleCount += 1;
            }
          });

          if (bookingFilterEmpty) {
            bookingFilterEmpty.hidden = visibleCount !== 0;
          }
        };

        bookingFilter?.addEventListener('click', (event) => {
          const button = event.target instanceof Element ? event.target.closest('[data-booking-filter-value]') : null;
          if (!button || !bookingFilter.contains(button)) {
            return;
          }

          bookingFilter.querySelectorAll('[data-booking-filter-value]').forEach((item) => {
            item.classList.toggle('is-active', item === button);
          });
          applyBookingStatusFilter(button.dataset.bookingFilterValue || 'all');
        });

        document.querySelectorAll('[data-booking-card]').forEach((card) => {
          const openDetail = () => {
            try {
              renderDetail(JSON.parse(card.dataset.bookingPayload || '{}'));
              detailModal.hidden = false;
              detailModal.classList.add('is-open');
            } catch (error) {
              console.error('Booking detail payload invalid', error);
            }
          };

          card.addEventListener('click', (event) => {
            if (event.target.closest('[data-cancel-booking]')) {
              return;
            }
            openDetail();
          });
          card.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
              event.preventDefault();
              openDetail();
            }
          });
        });

        document.querySelectorAll('[data-cancel-booking]').forEach((button) => {
          button.addEventListener('click', () => openCancelModal(button.dataset.bookingId || ''));
        });

        document.querySelectorAll('[data-cancel-modal-close]').forEach((button) => {
          button.addEventListener('click', closeCancelModal);
        });

        cancelConfirmButton?.addEventListener('click', cancelBooking);

        document.querySelectorAll('[data-booking-detail-close]').forEach((button) => {
          button.addEventListener('click', closeDetailModal);
        });

        document.addEventListener('keydown', (event) => {
          if (event.key === 'Escape') {
            if (!detailModal.hidden) {
              closeDetailModal();
            }
            if (!cancelModal.hidden) {
              closeCancelModal();
            }
          }
        });
      });
    </script>
  </body>
</html>
