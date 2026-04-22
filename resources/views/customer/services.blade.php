@php
  $serviceTypeOptions = [
    0 => 'Dich vu an uong',
    2 => 'Dich vu giai tri',
    1 => 'Dich vu phong',
  ];

  $serviceSections = [
    [
      'subheading' => 'Dịch vụ ăn uống',
      'title' => 'Thực đơn của khách sạn',
      'empty' => 'Chưa có dịch vụ ăn uống nào.',
      'services' => [
        ['MaDV' => 1, 'TenDV' => 'Buffet sang', 'LoaiDV' => 0, 'GiaDV' => 250000, 'LoaiDVLabel' => 'Dich vu an uong', 'ImagePath' => 'resources/customer/images/menu-1.jpg'],
        ['MaDV' => 2, 'TenDV' => 'Tra chieu tai san vuon', 'LoaiDV' => 0, 'GiaDV' => 180000, 'LoaiDVLabel' => 'Dich vu an uong', 'ImagePath' => 'resources/customer/images/menu-2.jpg'],
      ],
    ],
    [
      'subheading' => 'Dịch vụ giải trí',
      'title' => 'Loại hình giải trí của khách sạn',
      'empty' => 'Chưa có nội dung giải trí nào.',
      'services' => [
        ['MaDV' => 3, 'TenDV' => 'Spa thu gian 60 phut', 'LoaiDV' => 2, 'GiaDV' => 650000, 'LoaiDVLabel' => 'Dich vu giai tri', 'ImagePath' => 'resources/customer/images/dv_spa.jpg'],
        ['MaDV' => 4, 'TenDV' => 'San golf mini', 'LoaiDV' => 2, 'GiaDV' => 400000, 'LoaiDVLabel' => 'Dich vu giai tri', 'ImagePath' => 'resources/customer/images/dv_golf.jpg'],
      ],
    ],
    [
      'subheading' => 'Dịch vụ phòng',
      'title' => 'Dịch vụ phòng của khách sạn',
      'empty' => 'Chưa có dịch vụ phòng nào.',
      'services' => [
        ['MaDV' => 5, 'TenDV' => 'Don phong buoi toi', 'LoaiDV' => 1, 'GiaDV' => 150000, 'LoaiDVLabel' => 'Dich vu phong', 'ImagePath' => 'resources/customer/images/room-1.jpg'],
        ['MaDV' => 6, 'TenDV' => 'Trang tri phong ky niem', 'LoaiDV' => 1, 'GiaDV' => 500000, 'LoaiDVLabel' => 'Dich vu phong', 'ImagePath' => 'resources/customer/images/room-2.jpg'],
      ],
    ],
  ];

  $serviceOptions = [
    ['id' => '1', 'name' => 'Buffet sang', 'type' => '0', 'price' => 250000],
    ['id' => '2', 'name' => 'Tra chieu tai san vuon', 'type' => '0', 'price' => 180000],
    ['id' => '3', 'name' => 'Spa thu gian 60 phut', 'type' => '2', 'price' => 650000],
    ['id' => '4', 'name' => 'San golf mini', 'type' => '2', 'price' => 400000],
    ['id' => '5', 'name' => 'Don phong buoi toi', 'type' => '1', 'price' => 150000],
    ['id' => '6', 'name' => 'Trang tri phong ky niem', 'type' => '1', 'price' => 500000],
  ];

  $customerBookings = [
    ['MaDatPhong' => 'PV9010', 'SoPhong' => 'A101, A102'],
    ['MaDatPhong' => 'PV9011', 'SoPhong' => 'D401'],
  ];
@endphp

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
  <body>

    @include('customer.partials.nav', ['active' => 'services'])
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

    @foreach ($serviceSections as $section)
      <section class="ftco-section ftco-menu bg-light service-section" data-service-section>
        <div class="container">
          <div class="row justify-content-center mb-5 pb-3">
            <div class="col-md-7 heading-section text-center ftco-animate">
              <span class="subheading">{{ $section['subheading'] }}</span>
              <h2>{{ $section['title'] }}</h2>
            </div>
          </div>

          @if (!empty($section['services']))
            <div class="row service-page-grid" data-service-page-grid>
              @foreach ($section['services'] as $service)
                <div class="col-lg-6 col-xl-6 d-flex service-page-item" data-service-page-item>
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

            <div class="service-pagination" data-service-pagination hidden></div>
          @else
            <div class="customer-empty">{{ $section['empty'] }}</div>
          @endif
        </div>
      </section>
    @endforeach

    <div
      class="service-booking-modal"
      data-service-booking-modal
      data-service-options='@json($serviceOptions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)'
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

          <button class="service-booking-submit" type="submit" @disabled(empty($customerBookings))>
            Hoàn tất đăng ký <span aria-hidden="true">→</span>
          </button>
        </form>
      </div>
    </div>

    @include('customer.partials.footer')

    <!-- loader -->
    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>

    <script>
      (() => {
        const initServicePagination = () => {
          document.querySelectorAll('[data-service-section]').forEach((section) => {
            const items = Array.from(section.querySelectorAll('[data-service-page-item]'));
            const pagination = section.querySelector('[data-service-pagination]');
            const pageSize = 6;
            const pageCount = Math.ceil(items.length / pageSize);

            if (! pagination || pageCount <= 1) {
              return;
            }

            let currentPage = 1;

            const render = () => {
              items.forEach((item, index) => {
                const itemPage = Math.floor(index / pageSize) + 1;
                item.hidden = itemPage !== currentPage;
              });

              pagination.querySelectorAll('button').forEach((button) => {
                const page = Number(button.dataset.servicePage);
                button.classList.toggle('is-active', page === currentPage);
                button.setAttribute('aria-current', page === currentPage ? 'page' : 'false');
              });
            };

            pagination.hidden = false;
            pagination.innerHTML = '';

            for (let page = 1; page <= pageCount; page += 1) {
              const button = document.createElement('button');
              button.type = 'button';
              button.dataset.servicePage = String(page);
              button.textContent = String(page);
              button.setAttribute('aria-label', `Trang ${page}`);
              button.addEventListener('click', () => {
                currentPage = page;
                render();
              });
              pagination.append(button);
            }

            render();
          });
        };

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

        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', () => {
            initServicePagination();
            initServiceBooking();
          }, { once: true });
        } else {
          initServicePagination();
          initServiceBooking();
        }
      })();
    </script>
  </body>
</html>
