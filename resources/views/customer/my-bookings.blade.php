<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Peach Valley</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_hotel.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:200,300,400,600,700&display=swap" rel="stylesheet">
    @vite(['resources/customer/css/site.css', 'resources/customer/js/site.js'])
  </head>
  <body class="booking-page customer-account-page">
    @include('customer.partials.nav')

    @php
      $customerName = 'Nguyen Minh An';
      $customerPhone = '0901234567';
      $customerCccd = '079204000111';
      $customerEmail = 'minhan@gmail.com';
      $bookings = [
        [
          'MaDatPhong' => 'PV9010',
          'MaKH' => 1,
          'TenKH' => $customerName,
          'SoDienThoai' => $customerPhone,
          'Email' => $customerEmail,
          'CCCD' => $customerCccd,
          'NgayDat' => '2026-04-12',
          'NgayDatDisplay' => '12/04/2026',
          'NgayNhanPhong' => '2026-04-18',
          'NgayNhanPhongDisplay' => '18/04/2026',
          'NgayTraPhong' => '2026-04-21',
          'NgayTraPhongDisplay' => '21/04/2026',
          'SoDem' => 3,
          'TongSoKhach' => 8,
          'StatusLabel' => 'Da dat',
          'SummaryTitle' => 'Deluxe Twin, Suite Junior',
          'Rooms' => [
            ['TenPhong' => 'Deluxe Twin', 'SoPhong' => 'A101, A102', 'SoLuongPhong' => 2, 'SoKhach' => 4, 'GiaMoiDem' => 1732500, 'ThanhTien' => 10395000],
            ['TenPhong' => 'Suite Junior', 'SoPhong' => 'B201', 'SoLuongPhong' => 1, 'SoKhach' => 2, 'GiaMoiDem' => 2100000, 'ThanhTien' => 6300000],
            ['TenPhong' => 'Superior King', 'SoPhong' => 'C302', 'SoLuongPhong' => 1, 'SoKhach' => 2, 'GiaMoiDem' => 1450000, 'ThanhTien' => 4350000],
          ],
          'TongTien' => 21045000,
          'TienDatCoc' => 10522500,
        ],
        [
          'MaDatPhong' => 'PV9011',
          'MaKH' => 1,
          'TenKH' => $customerName,
          'SoDienThoai' => $customerPhone,
          'Email' => $customerEmail,
          'CCCD' => $customerCccd,
          'NgayDat' => '2026-04-14',
          'NgayDatDisplay' => '14/04/2026',
          'NgayNhanPhong' => '2026-05-02',
          'NgayNhanPhongDisplay' => '02/05/2026',
          'NgayTraPhong' => '2026-05-04',
          'NgayTraPhongDisplay' => '04/05/2026',
          'SoDem' => 2,
          'TongSoKhach' => 4,
          'StatusLabel' => 'Da dat',
          'SummaryTitle' => 'Deluxe Family, Standard Garden',
          'Rooms' => [
            ['TenPhong' => 'Deluxe Family', 'SoPhong' => 'D401', 'SoLuongPhong' => 1, 'SoKhach' => 3, 'GiaMoiDem' => 1840000, 'ThanhTien' => 3680000],
            ['TenPhong' => 'Standard Garden', 'SoPhong' => 'D402', 'SoLuongPhong' => 1, 'SoKhach' => 1, 'GiaMoiDem' => 900000, 'ThanhTien' => 1800000],
          ],
          'TongTien' => 5480000,
          'TienDatCoc' => 2740000,
        ],
      ];
    @endphp

    <section class="customer-account-section">
      <div class="container">
        <div class="customer-account-shell">
          @include('customer.partials.account-sidebar', ['active' => 'bookings'])

          <main class="customer-account-main">
            <div class="customer-account-heading">
              <div class="eyebrow">Đặt phòng của bạn</div>
            </div>

            @if (!empty($bookings))
              <div class="customer-booking-list">
                @foreach ($bookings as $booking)
                  <div
                    class="customer-booking-item customer-booking-card"
                    role="button"
                    tabindex="0"
                    data-booking-card
                    data-booking-payload='@json($booking, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)'
                  >
                    <div class="customer-booking-card-left">
                      <div class="customer-booking-top">
                        <div>
                          <div class="customer-booking-code">Đặt phòng #{{ $booking['MaDatPhong'] ?? '--' }}</div>
                          <h3 class="customer-booking-room">
                            {{ $booking['SummaryTitle'] ?? ($booking['LoaiPhong'] ?? 'Phòng') }}
                          </h3>
                        </div>
                        <span class="customer-booking-status">{{ $booking['StatusLabel'] ?? 'Đã đặt' }}</span>
                      </div>

                      <div class="customer-booking-room-list">
                        @foreach (($booking['Rooms'] ?? []) as $room)
                          <div class="customer-booking-room-line">
                            <strong>{{ $room['TenPhong'] ?? 'Phòng' }}</strong>
                            <span>{{ $room['SoLuongPhong'] ?? 1 }} phòng</span>
                          </div>
                        @endforeach
                      </div>

                      <div class="customer-booking-date-line">
                        {{ $booking['NgayNhanPhongDisplay'] ?? '--/--/----' }}
                        -
                        {{ $booking['NgayTraPhongDisplay'] ?? '--/--/----' }}
                        • {{ $booking['SoDem'] ?? 1 }} đêm • {{ $booking['TongSoKhach'] ?? 1 }} khách
                      </div>
                    </div>

                    <div class="customer-booking-card-right">
                      <div class="customer-booking-person-title">Thông tin cá nhân</div>
                      <div class="customer-booking-person-grid">
                        <div class="customer-booking-field"><span>Khách hàng</span>{{ $booking['TenKH'] ?? '--' }}</div>
                        <div class="customer-booking-field"><span>Số điện thoại</span>{{ $booking['SoDienThoai'] ?? '--' }}</div>
                        <div class="customer-booking-field"><span>Email</span>{{ $booking['Email'] ?? '--' }}</div>
                        <div class="customer-booking-field"><span>CCCD</span>{{ $booking['CCCD'] ?? '--' }}</div>
                      </div>

                      <div class="customer-booking-money">
                        <span>Tổng tiền</span>
                        <strong>{{ number_format((int) ($booking['TongTien'] ?? 0), 0, ',', '.') }} VND</strong>
                      </div>

                      <div class="customer-booking-actions">
                        <button
                          type="button"
                          class="customer-booking-cancel"
                          data-cancel-booking
                          data-booking-id="{{ $booking['MaDatPhong'] ?? '' }}"
                        >
                          Hủy phòng
                        </button>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <div class="customer-empty">Bạn chưa có đặt phòng nào.</div>
            @endif
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

    @include('customer.partials.footer')
    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const cancelModal = document.querySelector('[data-cancel-modal]');
        const cancelLabel = document.querySelector('[data-cancel-booking-label]');
        const detailModal = document.querySelector('[data-booking-detail-modal]');
        const detailRooms = document.querySelector('[data-booking-detail-rooms]');
        const detailPerson = document.querySelector('[data-booking-detail-person]');

        const formatCurrency = (value) => `${Number(value || 0).toLocaleString('vi-VN')} VND`;
        const formatDate = (value) => {
          const date = new Date(value);
          return Number.isNaN(date.getTime()) ? '--' : date.toLocaleDateString('vi-VN');
        };

        const openCancelModal = (bookingId) => {
          cancelLabel.textContent = bookingId ? `#${bookingId}` : '';
          cancelModal.hidden = false;
          cancelModal.classList.add('is-open');
        };

        const closeCancelModal = () => {
          cancelModal.classList.remove('is-open');
          cancelModal.hidden = true;
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
            <div><span>Email</span><strong>${booking.Email || '--'}</strong></div>
            <div><span>CCCD</span><strong>${booking.CCCD || '--'}</strong></div>
            <div><span>Ngày đặt</span><strong>${formatDate(booking.NgayDat)}</strong></div>
          `;
        };

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

        document.querySelectorAll('[data-cancel-modal-close], [data-cancel-modal-confirm]').forEach((button) => {
          button.addEventListener('click', closeCancelModal);
        });

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
