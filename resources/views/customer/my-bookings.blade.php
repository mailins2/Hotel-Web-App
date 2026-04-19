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
      $user = mockUser() ?? [];
      $account = collect(config('hotel-management.modules.accounts.records', []))->firstWhere('Email', $user['email'] ?? '');
      $customer = $account
        ? collect(config('hotel-management.modules.customers.records', []))->firstWhere('MaTK', $account['MaTK'] ?? null)
        : null;
      $customerId = $customer['MaKH'] ?? null;
      $bookings = collect(config('hotel-management.reception.bookings.records', []))
        ->filter(fn ($booking) => $customerId !== null && (string) ($booking['MaKH'] ?? '') === (string) $customerId)
        ->values();
    @endphp

    <section class="customer-account-section">
      <div class="container">
        <div class="customer-account-heading">
          <div class="eyebrow">Đặt phòng của bạn</div>
          <h2>Danh sách đặt phòng</h2>
          <p>Theo dõi các đặt phòng đã tạo với Peach Valley. Phòng đã đặt có thể gửi yêu cầu hủy trước ngày nhận phòng.</p>
        </div>

        @if ($bookings->isNotEmpty())
          <div class="customer-booking-list">
            @foreach ($bookings as $booking)
              <div class="customer-booking-item">
                <div class="customer-booking-top">
                  <div>
                    <div class="customer-booking-code">Đặt phòng #{{ $booking['MaDatPhong'] ?? '--' }}</div>
                    <h3 class="customer-booking-room">{{ $booking['LoaiPhong'] ?? 'Phòng' }}</h3>
                  </div>
                  <span class="customer-booking-status">Đã đặt</span>
                </div>

                <div class="customer-booking-table">
                  <div class="customer-booking-field"><span>Mã đặt phòng</span>{{ $booking['MaDatPhong'] ?? '--' }}</div>
                  <div class="customer-booking-field"><span>Khách hàng</span>{{ $booking['TenKH'] ?? '--' }}</div>
                  <div class="customer-booking-field"><span>Ngày đặt</span>{{ $booking['NgayDat'] ?? '--' }}</div>
                  <div class="customer-booking-field"><span>Ngày nhận phòng</span>{{ $booking['NgayNhanPhong'] ?? '--' }}</div>
                  <div class="customer-booking-field"><span>Ngày trả phòng</span>{{ $booking['NgayTraPhong'] ?? '--' }}</div>
                  <div class="customer-booking-field"><span>Số lượng người ở</span>{{ $booking['SoLuong'] ?? '--' }}</div>
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
            @endforeach
          </div>
        @else
          <div class="customer-empty">Bạn chưa có đặt phòng nào.</div>
        @endif
      </div>
    </section>

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
        const modal = document.querySelector('[data-cancel-modal]');
        const label = document.querySelector('[data-cancel-booking-label]');

        const openModal = (bookingId) => {
          label.textContent = bookingId ? `#${bookingId}` : '';
          modal.hidden = false;
          modal.classList.add('is-open');
        };

        const closeModal = () => {
          modal.classList.remove('is-open');
          modal.hidden = true;
        };

        document.querySelectorAll('[data-cancel-booking]').forEach((button) => {
          button.addEventListener('click', () => openModal(button.dataset.bookingId || ''));
        });

        document.querySelectorAll('[data-cancel-modal-close], [data-cancel-modal-confirm]').forEach((button) => {
          button.addEventListener('click', closeModal);
        });
      });
    </script>
  </body>
</html>
