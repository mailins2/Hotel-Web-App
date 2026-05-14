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

    <style>
      .service-booking-modal {
        align-items: flex-start !important;
        padding: 10px !important;
        overflow-y: auto !important;
      }

      .service-booking-dialog {
        grid-template-columns: 200px minmax(0, 1fr) !important;
        width: min(1120px, calc(100vw - 20px)) !important;
        max-height: calc(100vh - 20px) !important;
        margin: auto !important;
        border-radius: 18px !important;
      }

      .service-booking-side {
        min-height: 100% !important;
        padding: 32px 26px !important;
      }

      .service-booking-form {
        max-height: calc(100vh - 20px) !important;
        overflow-y: scroll !important;
        overscroll-behavior: contain;
        padding: 34px 40px 38px !important;
        scrollbar-width: thin;
        scrollbar-color: rgba(140, 74, 52, 0.55) rgba(140, 74, 52, 0.12);
      }

      .service-booking-form::-webkit-scrollbar {
        width: 12px;
      }

      .service-booking-form::-webkit-scrollbar-track {
        background: rgba(140, 74, 52, 0.1);
        border-radius: 999px;
      }

      .service-booking-form::-webkit-scrollbar-thumb {
        background: rgba(140, 74, 52, 0.5);
        border-radius: 999px;
        border: 2px solid rgba(255, 255, 255, 0.88);
      }

      .service-booking-form::-webkit-scrollbar-thumb:hover {
        background: rgba(140, 74, 52, 0.72);
      }

      .service-booking-food-list {
        display: grid;
        gap: 0.85rem;
      }

      .service-booking-food-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 120px auto;
        gap: 0.75rem;
        align-items: end;
      }

      .service-booking-food-row label {
        margin-bottom: 0.4rem;
      }

      .service-booking-food-row .service-booking-food-remove {
        height: 36px;
        align-self: end;
        margin-bottom: 22px;
      }

      .service-booking-food-add,
      .service-booking-food-remove {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: 36px !important;
        padding: 0 16px !important;
        border-radius: 6px !important;
        border: 1px solid transparent !important;
        font-size: 14px !important;
        font-weight: 800 !important;
        transition: all 0.2s ease !important;
      }

      .service-booking-food-add {
        background: #8c4a34 !important;
        border-color: #8c4a34 !important;
        color: #fff !important;
        box-shadow: 0 10px 22px rgba(140, 74, 52, 0.18) !important;
      }

      .service-booking-food-add:hover,
      .service-booking-food-add:focus {
        background: #6f3928 !important;
        border-color: #6f3928 !important;
        color: #fff !important;
        outline: none !important;
      }

      .service-booking-food-remove {
        background: rgba(140, 74, 52, 0.08) !important;
        border-color: rgba(140, 74, 52, 0.24) !important;
        color: #8c4a34 !important;
      }

      .service-booking-food-remove:hover,
      .service-booking-food-remove:focus {
        background: rgba(140, 74, 52, 0.15) !important;
        border-color: rgba(140, 74, 52, 0.35) !important;
        color: #6f3928 !important;
        outline: none !important;
      }

      @media (max-width: 767.98px) {
        .service-booking-dialog {
          grid-template-columns: 1fr !important;
          width: min(100%, calc(100vw - 12px)) !important;
        }

        .service-booking-side {
          padding: 24px 22px !important;
        }

        .service-booking-form {
          padding: 26px 20px 30px !important;
        }

        .service-booking-food-row {
          grid-template-columns: 1fr;
        }

        .service-booking-food-row .service-booking-food-remove {
          width: 100%;
        }
      }
    </style>

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
            <option value="PV9010">A101</option>
            <option value="PV9011">D401</option>
          </select>

          <label for="service_booking_type">Loại dịch vụ</label>
          <select id="service_booking_type" name="LoaiDV" data-service-type-select required>
            @foreach($serviceGroups as $type => $group)
              <option value="{{ $type }}">{{ $group['eyebrow'] }}</option>
            @endforeach
          </select>

          <div data-service-single-block>
            <label for="service_booking_service">Tên dịch vụ</label>
            <select id="service_booking_service" name="MaDV" data-service-name-select required></select>
          </div>

          <div data-service-food-block hidden>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
              <label class="mb-0">Món ăn đã chọn</label>
              <button type="button" class="service-booking-food-add" data-service-food-add-row>
                Thêm món
              </button>
            </div>
            <div class="service-booking-food-list" data-service-food-list></div>
          </div>

          <div class="service-booking-grid">
            <div data-service-quantity-block>
              <label for="service_booking_quantity" data-service-quantity-label>Số lượng người</label>
              <input id="service_booking_quantity" name="SoLuong" type="number" min="1" max="20" value="1" data-service-quantity-input required>
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
          const singleServiceBlock = modal.querySelector('[data-service-single-block]');
          const foodServiceBlock = modal.querySelector('[data-service-food-block]');
          const foodList = modal.querySelector('[data-service-food-list]');
          const addFoodRowButton = modal.querySelector('[data-service-food-add-row]');
          const quantityBlock = modal.querySelector('[data-service-quantity-block]');
          const quantityLabel = modal.querySelector('[data-service-quantity-label]');
          const quantityInput = modal.querySelector('[data-service-quantity-input]');
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

          const getServicesByType = (type) => services.filter((service) => String(service.type) === String(type));

          const buildFoodOptionHtml = (selectedId = '') => {
            return getServicesByType('1').map((service) => {
              const selected = String(service.id) === String(selectedId) ? ' selected' : '';
              return `<option value="${service.id}"${selected}>${service.name}</option>`;
            }).join('');
          };

          const renderServiceOptions = (selectedId = '') => {
            const selectedType = typeSelect.value;
            const filteredServices = getServicesByType(selectedType);

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

          const refreshFoodRemoveButtons = () => {
            const rows = foodList ? Array.from(foodList.querySelectorAll('[data-service-food-row]')) : [];

            rows.forEach((row) => {
              const removeButton = row.querySelector('[data-service-food-remove]');
              if (removeButton) {
                removeButton.hidden = rows.length <= 1;
                removeButton.disabled = rows.length <= 1;
              }
            });
          };

          const createFoodRow = (selectedId = '', quantityValue = '1') => {
            if (! foodList) {
              return;
            }

            const row = document.createElement('div');
            row.className = 'service-booking-food-row';
            row.setAttribute('data-service-food-row', '');
            row.innerHTML = `
              <div>
                <label>Tên món ăn</label>
                <select class="service-booking-food-select" data-service-food-select required>
                  ${buildFoodOptionHtml(selectedId)}
                </select>
              </div>
              <div>
                <label>Số lượng món</label>
                <input class="service-booking-food-qty" data-service-food-qty type="number" min="1" max="20" value="${quantityValue}" required>
              </div>
              <button type="button" class="service-booking-food-remove" data-service-food-remove>
                Bỏ món
              </button>
            `;

            foodList.append(row);
            refreshFoodRemoveButtons();
          };

          const resetFoodRows = (selectedId = '') => {
            if (! foodList) {
              return;
            }

            foodList.innerHTML = '';
            createFoodRow(selectedId || '');
          };

          const updateBookingMode = (selectedType, selectedId = '') => {
            const normalizedType = String(selectedType || '');
            const isFood = normalizedType === '1';
            const isRoomService = normalizedType === '2';

            if (singleServiceBlock) {
              singleServiceBlock.hidden = isFood;
            }

            if (foodServiceBlock) {
              foodServiceBlock.hidden = !isFood;
            }

            if (nameSelect) {
              nameSelect.disabled = isFood;
              nameSelect.required = !isFood;
            }

            if (quantityBlock) {
              quantityBlock.hidden = isFood;
            }

            if (quantityInput) {
              quantityInput.disabled = isFood;
              quantityInput.required = !isFood;
              quantityInput.value = '1';
            }

            if (quantityLabel) {
              quantityLabel.textContent = isRoomService ? 'Số lượng dịch vụ' : 'Số lượng người';
            }

            if (isFood) {
              resetFoodRows(selectedId);
            } else {
              renderServiceOptions(selectedId);
            }
          };

          const openModal = (trigger) => {
            const selectedType = trigger.dataset.serviceType || typeSelect.value;
            const selectedServiceId = trigger.dataset.serviceId || '';

            typeSelect.value = selectedType;
            updateBookingMode(selectedType, selectedServiceId);
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

          typeSelect.addEventListener('change', () => updateBookingMode(typeSelect.value));

          addFoodRowButton?.addEventListener('click', () => {
            createFoodRow('', '1');
          });

          foodList?.addEventListener('click', (event) => {
            const removeButton = event.target && event.target.closest
              ? event.target.closest('[data-service-food-remove]')
              : null;

            if (! removeButton) {
              return;
            }

            const row = removeButton.closest('[data-service-food-row]');
            if (row) {
              row.remove();
              refreshFoodRemoveButtons();
            }
          });

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

          updateBookingMode(typeSelect.value);
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
