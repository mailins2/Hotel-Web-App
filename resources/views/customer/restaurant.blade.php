@php
  $serviceTypeLabels = [
    0 => 'Dịch vụ ăn uống',
    1 => 'Dịch vụ phòng',
    2 => 'Dịch vụ giải trí',
  ];

  $serviceTypeOptions = [
    1 => 'Dịch vụ phòng',
    2 => 'Dịch vụ giải trí',
    0 => 'Dịch vụ ăn uống',
  ];

  $serviceImageMap = [
    1 => 'resources/customer/images/menu-1.jpg',
    2 => 'resources/customer/images/menu-2.jpg',
    3 => 'resources/customer/images/menu-3.jpg',
    4 => 'resources/customer/images/menu-4.jpg',
    5 => 'resources/customer/images/menu-5.jpg',
    6 => 'resources/customer/images/room-1.jpg',
    7 => 'resources/customer/images/room-2.jpg',
    8 => 'resources/customer/images/room-3.jpg',
    9 => 'resources/customer/images/room-4.jpg',
    10 => 'resources/customer/images/room-5.jpg',
    11 => 'resources/customer/images/dv_spa.jpg',
    12 => 'resources/customer/images/dv_fb.webp',
    13 => 'resources/customer/images/dv_golf.jpg',
  ];

  $services = collect(config('hotel-management.modules.services.records', []))
    ->values()
    ->map(function (array $service) use ($serviceTypeLabels, $serviceImageMap) {
      $type = (int) ($service['LoaiDV'] ?? 0);
      $serviceId = (int) ($service['MaDV'] ?? 0);

      return array_merge($service, [
        'LoaiDV' => $type,
        'LoaiDVLabel' => $serviceTypeLabels[$type] ?? 'Dịch vụ',
        'ImagePath' => $serviceImageMap[$serviceId] ?? 'resources/customer/images/resto.jpg',
      ]);
    });

  $serviceOptions = $services
    ->map(fn (array $service) => [
      'id' => (string) $service['MaDV'],
      'name' => (string) $service['TenDV'],
      'type' => (string) $service['LoaiDV'],
      'price' => (float) $service['GiaDV'],
    ])
    ->values();

  $user = function_exists('mockUser') ? mockUser() : null;
  $account = $user
    ? collect(config('hotel-management.modules.accounts.records', []))->firstWhere('Email', $user['email'] ?? '')
    : null;
  $customer = $account
    ? collect(config('hotel-management.modules.customers.records', []))->firstWhere('MaTK', $account['MaTK'] ?? null)
    : null;
  $customerBookings = $customer
    ? collect(config('hotel-management.reception.bookings.records', []))
        ->where('MaKH', $customer['MaKH'] ?? null)
        ->values()
    : collect();
@endphp

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Peach Valley Hotel</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:200,300,400,600,700&display=swap" rel="stylesheet">

    @vite(['resources/customer/css/site.css', 'resources/customer/js/site.js'])
  </head>
  <body>

    @include('customer.partials.nav', ['active' => 'restaurant'])
    <!-- END nav -->
    <div class="hero-wrap" data-bg-image="{{ Vite::asset('resources/customer/images/dv_fb.webp') }}">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text d-flex align-itemd-center justify-content-center">
          <div class="col-md-9 ftco-animate text-center d-flex align-items-end justify-content-center">
            <div class="text">
              <h1 class="mb-4 bread">Dịch vụ</h1>
            </div>
          </div>
        </div>
      </div>
    </div>

    <section class="ftco-section">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <div class="single-slider-resto mb-4 mb-md-0 owl-carousel">
              <div class="item">
                <div class="resto-img rounded" data-bg-image="{{ Vite::asset('resources/customer/images/dv_fb.webp') }}"></div>
              </div>
              <div class="item">
                <div class="resto-img rounded" data-bg-image="{{ Vite::asset('resources/customer/images/dv_spa.jpg') }}"></div>
              </div>
              <div class="item">
                <div class="resto-img rounded" data-bg-image="{{ Vite::asset('resources/customer/images/dv_golf.jpg') }}"></div>
              </div>
            </div>
          </div>
          <div class="col-md-6 pl-md-5">
            <div class="heading-section mb-4 my-5 my-md-0">
              <span class="subheading">Khách sạn Peach Valley</span>
              <h2 class="mb-4">Tận hưởng dịch vụ cùng Peach Valley</h2>
            </div>
            <p>Peach Valley mang đến cho khách hàng trải nghiệm nghỉ dưỡng trọn vẹn với hệ thống dịch vụ đa dạng và chu đáo. Từ dịch vụ ăn uống với thực đơn hấp dẫn, không gian ấm cúng, đến các hoạt động giải trí thư giãn giúp quý khách tận hưởng từng khoảnh khắc nghỉ ngơi, mọi chi tiết đều được chăm chút kỹ lưỡng. Bên cạnh đó, dịch vụ phòng luôn được phục vụ tận tâm, nhanh chóng và tiện nghi, góp phần đem đến sự thoải mái, hài lòng và những trải nghiệm tốt nhất trong suốt thời gian lưu trú.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="ftco-section ftco-menu bg-light">
      <div class="container">
        <div class="row justify-content-center mb-5 pb-3">
          <div class="col-md-7 heading-section text-center ftco-animate">
            <span class="subheading">Dịch vụ khách sạn</span>
            <h2>Danh sách dịch vụ</h2>
          </div>
        </div>

        @if ($errors->any())
          <div class="service-booking-alert service-booking-alert-error" role="alert">{{ $errors->first() }}</div>
        @endif

        <div class="row">
          @foreach ($services as $service)
            <div class="col-lg-6 col-xl-6 d-flex">
              <div class="pricing-entry service-pricing-entry rounded d-flex ftco-animate">
                <div class="img" data-bg-image="{{ Vite::asset($service['ImagePath']) }}"></div>
                <div class="desc p-4">
                  <div class="d-md-flex text align-items-start">
                    <h3><span>{{ $service['TenDV'] }}</span></h3>
                    <span class="price">{{ number_format($service['GiaDV'], 0, ',', '.') }} VNĐ</span>
                  </div>
                  <div class="d-block">
                    <p class="service-type-label">{{ $service['LoaiDVLabel'] }}</p>
                    <button
                      class="service-booking-trigger"
                      type="button"
                      data-service-booking-trigger
                      data-service-id="{{ $service['MaDV'] }}"
                      data-service-type="{{ $service['LoaiDV'] }}"
                    >
                      Đặt dịch vụ
                    </button>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>

    <div
      class="service-booking-modal"
      data-service-booking-modal
      data-service-options='{{ $serviceOptions->toJson() }}'
      hidden
    >
      <div class="service-booking-backdrop" data-service-booking-close></div>
      <div class="service-booking-dialog" role="dialog" aria-modal="true" aria-labelledby="service_booking_title">
        <aside class="service-booking-side">
          <span class="service-booking-side-icon"><i class="ion-ios-restaurant"></i></span>
          <h2>Yêu cầu Dịch vụ</h2>
          <p>Hãy để chúng tôi chuẩn bị đúng dịch vụ quý khách cần trong thời gian lưu trú.</p>
        </aside>

        <form class="service-booking-form" method="POST" action="{{ route('customer.service-booking.store') }}" data-service-booking-form>
          {{ csrf_field() }}
          <button class="service-booking-close" type="button" aria-label="Đóng form đặt dịch vụ" data-service-booking-close>&times;</button>

          <div class="service-booking-heading">
            <h2 id="service_booking_title">Đăng ký Dịch vụ</h2>
            <p>Vui lòng cung cấp thông tin chi tiết.</p>
          </div>

          <label for="service_booking_room">Số phòng</label>
          <select id="service_booking_room" name="MaDatPhong" required>
            @forelse ($customerBookings as $booking)
              <option value="{{ $booking['MaDatPhong'] }}">{{ $booking['SoPhong'] }}</option>
            @empty
              <option value="">Đăng nhập và đặt phòng trước khi dùng dịch vụ</option>
            @endforelse
          </select>

          <label for="service_booking_type">Loại dịch vụ</label>
          <select id="service_booking_type" name="LoaiDV" data-service-type-select required>
            @foreach ($serviceTypeOptions as $type => $label)
              <option value="{{ $type }}">{{ $label }}</option>
            @endforeach
          </select>

          <label for="service_booking_service">Tên dịch vụ</label>
          <select id="service_booking_service" name="MaDV" data-service-name-select required></select>

          <div class="service-booking-grid">
            <div>
              <label for="service_booking_quantity">Số lượng người</label>
              <input id="service_booking_quantity" name="SoLuong" type="number" min="1" max="20" value="1" required>
            </div>
            <div>
              <label for="service_booking_date">Ngày sử dụng</label>
              <input id="service_booking_date" type="date" data-service-date required>
            </div>
          </div>

          <input id="service_booking_time" name="ThoiGian" type="hidden" data-service-time>

          <div class="service-booking-time-group">
            <label for="service_booking_hour">Giờ sử dụng</label>
            <div class="service-booking-time-row">
              <select id="service_booking_hour" data-service-hour required>
                @for ($hour = 0; $hour < 24; $hour++)
                  <option value="{{ str_pad((string) $hour, 2, '0', STR_PAD_LEFT) }}">{{ str_pad((string) $hour, 2, '0', STR_PAD_LEFT) }}</option>
                @endfor
              </select>
              <span class="service-booking-time-divider" aria-hidden="true">:</span>
              <select id="service_booking_minute" data-service-minute required>
                @for ($minute = 0; $minute < 60; $minute++)
                  <option value="{{ str_pad((string) $minute, 2, '0', STR_PAD_LEFT) }}">{{ str_pad((string) $minute, 2, '0', STR_PAD_LEFT) }}</option>
                @endfor
              </select>
            </div>
          </div>

          <button class="service-booking-submit" type="submit" @disabled($customerBookings->isEmpty())>
            Hoàn tất đăng ký <span aria-hidden="true">→</span>
          </button>
        </form>
      </div>
    </div>

    @if (session('service_booking_saved'))
      <div class="service-booking-success-modal" data-service-booking-success-modal hidden>
        <div class="service-booking-success-backdrop" data-service-booking-success-close></div>
        <div class="service-booking-success-dialog" role="dialog" aria-modal="true" aria-labelledby="service_booking_success_title">
          <button class="service-booking-success-close" type="button" aria-label="Đóng thông báo" data-service-booking-success-close>&times;</button>
          <div class="service-booking-success-icon" aria-hidden="true">✓</div>
          <h2 id="service_booking_success_title">Đăng kí dịch vụ thành công</h2>
          <p>{{ session('service_booking_saved') }}</p>
          <button class="service-booking-success-action" type="button" data-service-booking-success-close>Đóng</button>
        </div>
      </div>
    @endif

    @include('customer.partials.footer')

    <!-- loader -->
    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>

    <script>
      (() => {
        const initServiceBooking = () => {
        const modal = document.querySelector('[data-service-booking-modal]');

        if (! modal) {
          return;
        }

        const triggers = document.querySelectorAll('[data-service-booking-trigger]');
        const closeButtons = modal.querySelectorAll('[data-service-booking-close]');
        const typeSelect = modal.querySelector('[data-service-type-select]');
        const nameSelect = modal.querySelector('[data-service-name-select]');
        const form = modal.querySelector('[data-service-booking-form]');
        const dateInput = modal.querySelector('[data-service-date]');
        const hourSelect = modal.querySelector('[data-service-hour]');
        const minuteSelect = modal.querySelector('[data-service-minute]');
        const timeInput = modal.querySelector('[data-service-time]');
        let services = [];

        try {
          services = JSON.parse(modal.dataset.serviceOptions || '[]');
        } catch (error) {
          services = [];
        }

        const pad = (value) => String(value).padStart(2, '0');
        const now = new Date();
        const todayValue = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`;

        const syncServiceTime = () => {
          if (! dateInput || ! hourSelect || ! minuteSelect || ! timeInput) {
            return;
          }

          timeInput.value = `${dateInput.value}T${hourSelect.value}:${minuteSelect.value}`;
        };

        if (dateInput) {
          dateInput.min = todayValue;
          dateInput.value = dateInput.value || todayValue;
        }

        if (hourSelect) {
          hourSelect.value = pad(now.getHours());
        }

        if (minuteSelect) {
          minuteSelect.value = pad(now.getMinutes());
        }

        [dateInput, hourSelect, minuteSelect].forEach((input) => {
          input?.addEventListener('change', syncServiceTime);
          input?.addEventListener('input', syncServiceTime);
        });

        const renderServiceOptions = (selectedId = '') => {
          const selectedType = typeSelect.value;
          const filteredServices = services.filter((service) => service.type === selectedType);

          nameSelect.innerHTML = '';

          filteredServices.forEach((service) => {
            const option = document.createElement('option');
            option.value = service.id;
            option.textContent = service.name;

            if (service.id === selectedId) {
              option.selected = true;
            }

            nameSelect.append(option);
          });
        };

        const openModal = (trigger) => {
          typeSelect.value = trigger.dataset.serviceType || '1';
          renderServiceOptions(trigger.dataset.serviceId || '');
          syncServiceTime();
          modal.hidden = false;
          document.body.classList.add('service-booking-open');
          window.requestAnimationFrame(() => modal.classList.add('is-open'));
        };

        const closeModal = () => {
          modal.classList.remove('is-open');
          document.body.classList.remove('service-booking-open');
          window.setTimeout(() => {
            modal.hidden = true;
          }, 160);
        };

        triggers.forEach((trigger) => {
          trigger.addEventListener('click', () => openModal(trigger));
        });

        closeButtons.forEach((button) => {
          button.addEventListener('click', closeModal);
        });

        typeSelect.addEventListener('change', () => renderServiceOptions());

        form?.addEventListener('submit', (event) => {
          syncServiceTime();

          if (! timeInput?.value) {
            event.preventDefault();
            dateInput?.focus();
          }
        });

        document.addEventListener('keydown', (event) => {
          if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
          }
        });

        renderServiceOptions();
        syncServiceTime();
        };

        const initServiceBookingSuccessModal = () => {
          const successModal = document.querySelector('[data-service-booking-success-modal]');

          if (! successModal) {
            return;
          }

          const closeButtons = successModal.querySelectorAll('[data-service-booking-success-close]');

          const openSuccessModal = () => {
            successModal.hidden = false;
            document.body.classList.add('service-booking-open');
            window.requestAnimationFrame(() => successModal.classList.add('is-open'));
          };

          const closeSuccessModal = () => {
            successModal.classList.remove('is-open');
            document.body.classList.remove('service-booking-open');
            window.setTimeout(() => {
              successModal.hidden = true;
            }, 160);
          };

          closeButtons.forEach((button) => {
            button.addEventListener('click', closeSuccessModal);
          });

          document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && successModal.classList.contains('is-open')) {
              closeSuccessModal();
            }
          });

          openSuccessModal();
        };

        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', () => {
            initServiceBooking();
            initServiceBookingSuccessModal();
          }, { once: true });
        } else {
          initServiceBooking();
          initServiceBookingSuccessModal();
        }
      })();
    </script>
  </body>
</html>
