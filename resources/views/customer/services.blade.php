<!DOCTYPE html>
<html lang="vi">
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

    @php
      $servicesByType = $servicesByType ?? collect();
      $serviceGroups = $serviceGroups ?? [];
      $serviceOptions = collect($serviceOptions ?? []);

      $serviceImageUrl = function ($service, string $fallback) {
          $url = $service->hinhs->first()->Url ?? $fallback;

          if (\Illuminate\Support\Str::startsWith($url, ['http://', 'https://', '/'])) {
              return $url;
          }

          return asset($url);
      };
    @endphp

    <div class="hero-wrap" data-bg-image="{{ asset('customers/images/dv_fb.webp') }}">
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
                <div class="resto-img rounded" data-bg-image="{{ asset('customers/images/dv_fb.webp') }}"></div>
              </div>
              <div class="item">
                <div class="resto-img rounded" data-bg-image="{{ asset('customers/images/dv_spa.jpg') }}"></div>
              </div>
              <div class="item">
                <div class="resto-img rounded" data-bg-image="{{ asset('customers/images/dv_golf.jpg') }}"></div>
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

    @foreach($serviceGroups as $type => $group)
      @php
        $groupServices = $servicesByType->get($type, collect());
        $fallbackImages = $group['fallbackImages'];
      @endphp

      <section class="ftco-section ftco-menu bg-light service-section" data-service-section>
        <div class="container">
          <div class="row justify-content-center mb-5 pb-3">
            <div class="col-md-7 heading-section text-center ftco-animate">
              <span class="subheading">{{ $group['eyebrow'] }}</span>
              <h2>{{ $group['title'] }}</h2>
            </div>
          </div>

          <div class="row service-page-grid" data-service-page-grid>
            @forelse($groupServices as $service)
              @php
                $fallbackImage = $fallbackImages[$loop->index % count($fallbackImages)];
              @endphp

              <div class="col-lg-6 col-xl-6 d-flex service-page-item" data-service-page-item>
                <div class="pricing-entry service-pricing-entry rounded d-flex ftco-animate">
                  <div class="img" data-bg-image="{{ $serviceImageUrl($service, $fallbackImage) }}"></div>
                  <div class="desc p-4">
                    <div class="d-md-flex text align-items-start">
                      <h3><span>{{ $service->TenDV }}</span></h3>
                      <span class="price">{{ $service->GiaDVFormatted }}</span>
                    </div>
                    <div class="d-block">
                      <p class="service-type-label">{{ $service->LoaiDVText }}</p>
                      <button
                        class="service-booking-trigger"
                        type="button"
                        data-service-booking-trigger
                        data-service-id="{{ $service->MaDV }}"
                        data-service-type="{{ $service->LoaiDV }}"
                      >
                        Đặt dịch vụ
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            @empty
              <div class="col-12">
                <p class="text-center text-muted mb-0">Chưa có dịch vụ trong nhóm này.</p>
              </div>
            @endforelse
          </div>

          <div class="service-pagination" data-service-pagination hidden></div>
        </div>
      </section>
    @endforeach

    <div
      class="service-booking-modal"
      data-service-booking-modal
      data-service-options="{{ $serviceOptions->toJson() }}"
      hidden
    >
      <div class="service-booking-backdrop" data-service-booking-close></div>
      <div class="service-booking-dialog" role="dialog" aria-modal="true" aria-labelledby="service_booking_title">
        <aside class="service-booking-side">
          <span class="service-booking-side-icon"><i class="ion-ios-restaurant"></i></span>
          <h2>Yêu cầu dịch vụ</h2>
          <p>Hãy để chúng tôi chuẩn bị đúng dịch vụ quý khách cần trong thời gian lưu trú.</p>
        </aside>

        <form class="service-booking-form" data-service-booking-form>
          <button class="service-booking-close" type="button" aria-label="Đóng form đặt dịch vụ" data-service-booking-close>&times;</button>

          <div class="service-booking-heading">
            <h2 id="service_booking_title">Đăng ký dịch vụ</h2>
            <p>Vui lòng cung cấp thông tin chi tiết.</p>
          </div>

          <label for="service_booking_room">Số phòng</label>
          <select id="service_booking_room" name="MaDatPhong" required>
            <option value="PV9010">A101, A102</option>
            <option value="PV9011">D401</option>
          </select>

          <label for="service_booking_type">Loại dịch vụ</label>
          <select id="service_booking_type" name="LoaiDV" data-service-type-select required>
            @foreach($serviceGroups as $type => $group)
              <option value="{{ $type }}">{{ $group['eyebrow'] }}</option>
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
                @for($hour = 0; $hour <= 23; $hour++)
                  <option value="{{ str_pad((string) $hour, 2, '0', STR_PAD_LEFT) }}">{{ str_pad((string) $hour, 2, '0', STR_PAD_LEFT) }}</option>
                @endfor
              </select>
              <span class="service-booking-time-divider" aria-hidden="true">:</span>
              <select id="service_booking_minute" data-service-minute required>
                @for($minute = 0; $minute <= 59; $minute++)
                  <option value="{{ str_pad((string) $minute, 2, '0', STR_PAD_LEFT) }}">{{ str_pad((string) $minute, 2, '0', STR_PAD_LEFT) }}</option>
                @endfor
              </select>
            </div>
          </div>

          <button class="service-booking-submit" type="submit">
            Hoàn tất đăng ký <span aria-hidden="true">→</span>
          </button>
        </form>
      </div>
    </div>

    @include('customer.partials.footer')

    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>

    <script>
      (() => {
        const initServicePagination = () => {
          document.querySelectorAll('[data-service-section]').forEach((section) => {
            const items = Array.from(section.querySelectorAll('[data-service-page-item]'));
            const pagination = section.querySelector('[data-service-pagination]');
            const pageSize = 4;
            const pageCount = Math.ceil(items.length / pageSize);

            if (! pagination || pageCount <= 1) {
              return;
            }

            let currentPage = 1;
            let autoTimer = null;
            let isAnimating = false;

            const setVisiblePage = (page) => {
              items.forEach((item, index) => {
                const itemPage = Math.floor(index / pageSize) + 1;
                item.hidden = itemPage !== page;
              });
            };

            const syncDots = () => {
              pagination.querySelectorAll('button').forEach((button) => {
                const page = Number(button.dataset.servicePage);
                button.classList.toggle('active', page === currentPage);
                button.setAttribute('aria-current', page === currentPage ? 'page' : 'false');
              });
            };

            const render = (page, animate = true) => {
              const grid = section.querySelector('[data-service-page-grid]');

              if (! animate || ! grid) {
                currentPage = page;
                setVisiblePage(currentPage);
                syncDots();
                return;
              }

              if (isAnimating) {
                return;
              }

              isAnimating = true;
              grid.classList.add('is-sliding-out');

              window.setTimeout(() => {
                currentPage = page;
                setVisiblePage(currentPage);
                syncDots();
                grid.classList.remove('is-sliding-out');
                grid.classList.add('is-sliding-in');

                window.setTimeout(() => {
                  grid.classList.remove('is-sliding-in');
                  isAnimating = false;
                }, 260);
              }, 220);
            };

            const goToPage = (page) => {
              if (page === currentPage) {
                return;
              }

              render(page);
            };

            const startAutoPagination = () => {
              window.clearInterval(autoTimer);

              autoTimer = window.setInterval(() => {
                goToPage(currentPage === pageCount ? 1 : currentPage + 1);
              }, 4500);
            };

            const restartAutoPagination = () => {
              startAutoPagination();
            };

            pagination.hidden = false;
            pagination.innerHTML = '';
            pagination.classList.add('owl-dots');

            for (let page = 1; page <= pageCount; page += 1) {
              const button = document.createElement('button');
              button.type = 'button';
              button.className = 'owl-dot';
              button.dataset.servicePage = String(page);
              button.innerHTML = '<span></span>';
              button.setAttribute('aria-label', `Trang ${page}`);
              button.addEventListener('click', () => {
                goToPage(page);
                restartAutoPagination();
              });
              pagination.append(button);
            }

            section.addEventListener('mouseenter', () => window.clearInterval(autoTimer));
            section.addEventListener('mouseleave', startAutoPagination);
            section.addEventListener('focusin', () => window.clearInterval(autoTimer));
            section.addEventListener('focusout', startAutoPagination);

            render(1, false);
            startAutoPagination();
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
            typeSelect.value = trigger.dataset.serviceType || typeSelect.value;
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
            event.preventDefault();
            syncServiceTime();

            if (! timeInput?.value) {
              dateInput?.focus();
              return;
            }

            if (! form.checkValidity()) {
              form.reportValidity();
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
