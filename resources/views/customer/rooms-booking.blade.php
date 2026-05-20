@php
  $initialCheckIn = request('checkIn') ? \Illuminate\Support\Carbon::parse(request('checkIn')) : now();
  $initialCheckOut = request('checkOut') ? \Illuminate\Support\Carbon::parse(request('checkOut')) : now()->addDay();

  if ($initialCheckOut->lessThanOrEqualTo($initialCheckIn)) {
    $initialCheckOut = $initialCheckIn->copy()->addDay();
  }

  $initialAdults = max((int) request('NguoiLon', request('adults', 2)), 1);
  $initialChildren = max((int) request('TreEm', request('children', 1)), 0);
  $initialRooms = max((int) request('SoPhong', request('rooms', 1)), 1);
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
  <body class="search-page">
    @include('customer.partials.nav')

    <section class="search-summary">
      <div class="container">
        <div class="search-summary-bar">
          <div class="search-summary-item">
            <span class="search-summary-label">Ngày nhận</span>
            <input
              type="text"
              class="search-summary-input"
              data-search-checkin
              value="{{ $initialCheckIn->format('d/m/Y') }}"
              inputmode="numeric"
              autocomplete="off"
              placeholder="dd/mm/yyyy"
            >
          </div>
          <div class="search-summary-item">
            <span class="search-summary-label">Ngày trả</span>
            <input
              type="text"
              class="search-summary-input"
              data-search-checkout
              value="{{ $initialCheckOut->format('d/m/Y') }}"
              inputmode="numeric"
              autocomplete="off"
              placeholder="dd/mm/yyyy"
            >
          </div>
          <div class="search-summary-item">
            <span class="search-summary-label">Số khách</span>
            <div class="search-summary-guest" data-search-guest>
              <button type="button" class="search-summary-guest-trigger" data-search-guest-trigger>
                <span data-search-guest-text>
                  {{ $initialAdults }} người lớn - {{ $initialChildren }} trẻ em - {{ $initialRooms }} phòng
                </span>
                <span class="icon ion-ios-arrow-down"></span>
              </button>
              <div class="search-summary-guest-panel" data-search-guest-panel>
                <div class="search-guest-row">
                  <span>Người lớn</span>
                  <div class="search-guest-stepper">
                    <button type="button" data-guest-action="dec" data-guest-type="adults">-</button>
                    <span data-guest-count="adults">{{ $initialAdults }}</span>
                    <button type="button" data-guest-action="inc" data-guest-type="adults">+</button>
                  </div>
                </div>
                <div class="search-guest-row">
                  <span>Trẻ em</span>
                  <div class="search-guest-stepper">
                    <button type="button" data-guest-action="dec" data-guest-type="children">-</button>
                    <span data-guest-count="children">{{ $initialChildren }}</span>
                    <button type="button" data-guest-action="inc" data-guest-type="children">+</button>
                  </div>
                </div>
                <div class="search-guest-row">
                  <span>Phòng</span>
                  <div class="search-guest-stepper">
                    <button type="button" data-guest-action="dec" data-guest-type="rooms">-</button>
                    <span data-guest-count="rooms">{{ $initialRooms }}</span>
                    <button type="button" data-guest-action="inc" data-guest-type="rooms">+</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <button type="button" class="search-summary-action" id="searchBtn" data-search-action>
            <span class="icon ion-ios-search"></span>
            Tìm kiếm
          </button>
          <div class="room-filter-toolbar search-summary-filters" data-room-filter-toolbar>
            <div class="room-filter-field room-filter-search">
              <label for="roomFilterSearch">Tìm phòng</label>
              <div class="room-filter-input-wrap">
                <span class="icon ion-ios-search"></span>
                <input
                  type="search"
                  id="roomFilterSearch"
                  data-room-filter-search
                  placeholder="Tên phòng, mô tả, tiện nghi"
                  autocomplete="off"
                >
              </div>
            </div>
            <div class="room-filter-field">
              <label for="roomFilterSort">Sắp xếp giá</label>
              <select id="roomFilterSort" data-room-filter-sort>
                <option value="">Mặc định</option>
                <option value="price-asc">Giá tăng dần</option>
                <option value="price-desc">Giá giảm dần</option>
              </select>
            </div>
            <button type="button" class="room-filter-reset" data-room-filter-reset>
              Xóa lọc
            </button>
          </div>
        </div>
      </div>
    </section>

    <section class="ftco-section search-results-section">
      <div class="container">
        <div class="row">
          <div class="col-lg-8">
            <div class="room-results-list" data-room-results>
              <div class="room-results-loading">Đang tải danh sách phòng...</div>
            </div>
          </div>
          <div class="col-lg-4">
            <div
              class="search-summary-card"
              data-booking-summary
              data-nights="1"
              data-adults="{{ $initialAdults }}"
              data-children="{{ $initialChildren }}"
              data-rooms="{{ $initialRooms }}"
              data-checkin="{{ $initialCheckIn->toDateString() }}"
            >
              <div class="search-summary-header">
                <h4>Thông tin phòng</h4>
              </div>
              <div class="search-summary-dates" data-booking-dates></div>
              <div class="search-summary-list" data-booking-list></div>
              <div class="search-summary-total">
                <span>Tổng cộng</span>
                <strong data-booking-total>0 VND</strong>
              </div>
              <a
                href="{{ route('customer.info-booking') }}"
                class="btn btn-primary w-100 search-summary-cta disabled"
                data-booking-continue
                aria-disabled="true"
                tabindex="-1"
              >Đặt ngay</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div class="room-amenities-modal" data-room-modal>
      <div class="room-amenities-modal-backdrop" data-room-modal-close></div>
      <div class="room-amenities-modal-dialog">
        <button type="button" class="room-amenities-modal-close" data-room-modal-close aria-label="Đóng">
          <span class="icon ion-ios-close"></span>
        </button>
        <div class="room-amenities-modal-body">
          <div class="room-amenities-modal-main">
            <div class="room-amenities-modal-slider" data-room-modal-slider>
              <button type="button" class="room-amenities-modal-nav prev" data-room-modal-prev aria-label="Ảnh trước">
                <span class="icon ion-ios-arrow-back"></span>
              </button>
              <div class="room-amenities-modal-slides" data-room-modal-slides></div>
              <button type="button" class="room-amenities-modal-nav next" data-room-modal-next aria-label="Ảnh sau">
                <span class="icon ion-ios-arrow-forward"></span>
              </button>
            </div>
            <div class="room-amenities-modal-info">
              <h3 data-room-modal-title>Executive Suite</h3>
              <div class="room-amenities-modal-area" data-room-modal-area>68 m²</div>
              <p data-room-modal-desc>
                Không gian nghỉ dưỡng rộng rãi, tiện nghi đầy đủ và ánh sáng tự nhiên hài hòa.
              </p>
            </div>
          </div>
          <div class="room-amenities-modal-list">
            <h4>Tiện ích trong phòng:</h4>
            <div class="room-amenities-grid">
              <div class="room-amenities-col">
                <div class="room-amenity-item"><span class="room-amenity-icon ion-ios-shirt"></span><span>Tủ quần áo</span></div>
                <div class="room-amenity-item"><span class="room-amenity-icon ion-logo-no-smoking"></span><span>Phòng không hút thuốc</span></div>
                <div class="room-amenity-item"><span class="room-amenity-icon ion-ios-snow"></span><span>Điều hòa</span></div>
                <div class="room-amenity-item"><span class="room-amenity-icon ion-ios-flashlight"></span><span>Máy sấy tóc</span></div>
                <div class="room-amenity-item"><span class="room-amenity-icon ion-ios-wifi"></span><span>Wifi</span></div>
                <div class="room-amenity-item"><span class="room-amenity-icon ion-ios-square-outline"></span><span>Khăn tắm</span></div>
                <div class="room-amenity-item"><span class="room-amenity-icon ion-ios-bulb"></span><span>Đèn bàn</span></div>
                <div class="room-amenity-item"><span class="room-amenity-icon ion-ios-desktop"></span><span>Bàn làm việc</span></div>
              </div>
              <div class="room-amenities-col">
                <div class="room-amenity-item"><span class="room-amenity-icon ion-ios-bed"></span><span>Ga trải giường, gối</span></div>
                <div class="room-amenity-item"><span class="room-amenity-icon ion-ios-water"></span><span>Vòi sen</span></div>
                <div class="room-amenity-item"><span class="room-amenity-icon ion-ios-shirt"></span><span>Dịch vụ giặt ủi</span></div>
                <div class="room-amenity-item"><span class="room-amenity-icon ion-ios-water"></span><span>Phòng có bồn tắm</span></div>
                <div class="room-amenity-item"><span class="room-amenity-icon ion-ios-medkit"></span><span>Đồ phòng tắm</span></div>
                <div class="room-amenity-item"><span class="room-amenity-icon ion-ios-call"></span><span>Điện thoại</span></div>
                <div class="room-amenity-item"><span class="room-amenity-icon ion-ios-tv"></span><span>Truyền hình cáp/Vệ tinh</span></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    @include('customer.partials.footer')

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const resultsContainer = document.querySelector('[data-room-results]');
        const searchBtn = document.getElementById('searchBtn');
        const checkInInput = document.querySelector('[data-search-checkin]');
        const checkOutInput = document.querySelector('[data-search-checkout]');
        const adultCount = document.querySelector('[data-guest-count="adults"]');
        const childrenCount = document.querySelector('[data-guest-count="children"]');
        const roomCount = document.querySelector('[data-guest-count="rooms"]');
        const guestWrapper = document.querySelector('[data-search-guest]');
        const guestText = document.querySelector('[data-search-guest-text]');
        const summary = document.querySelector('[data-booking-summary]');
        const bookingList = document.querySelector('[data-booking-list]');
        const bookingTotal = document.querySelector('[data-booking-total]');
        const bookingContinue = document.querySelector('[data-booking-continue]');
        const roomFilterSearch = document.querySelector('[data-room-filter-search]');
        const roomFilterSort = document.querySelector('[data-room-filter-sort]');
        const roomFilterReset = document.querySelector('[data-room-filter-reset]');
        const fallbackImage = '{{ asset("customers/images/room-6.jpg") }}';
        const assetBaseUrl = @json(rtrim(asset(''), '/') . '/');
        let allRoomTypes = [];
        let availableRoomTypes = [];
        let roomsData = [];
        const selections = new Map();

        const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#039;',
        }[char]));
        const escapeAttr = (value) => escapeHtml(value).replace(/`/g, '&#096;');
        const normalizeImageUrl = (value) => {
          const url = String(value || '').trim();
          if (!url) return '';
          if (/^(https?:)?\/\//i.test(url) || /^(data|blob):/i.test(url) || url.startsWith('/')) return url;
          return `${assetBaseUrl}${url.replace(/^public\//i, '').replace(/^\/+/, '')}`;
        };
        const getImageUrl = (image) => normalizeImageUrl(
          image?.Url || image?.url || image?.DuongDan || image?.duong_dan || image?.path || image?.src || ''
        );
        const getImages = (room) => {
          const rawImages = room.hinhs || room.hinhAnhs || room.hinh_anhs || room.images || [];
          const rows = Array.isArray(rawImages) ? rawImages : [rawImages];
          const images = rows.map((image) => (typeof image === 'string' ? normalizeImageUrl(image) : getImageUrl(image))).filter(Boolean);
          const uniqueImages = [...new Set(images)];
          return uniqueImages.length ? uniqueImages : [fallbackImage];
        };
        const getOriginalRoomPrice = (room) => {
          const originalPrice = Number(room?.GiaPhong ?? room?.gia_phong ?? room?.Gia ?? room?.gia);
          return Number.isFinite(originalPrice) && originalPrice > 0 ? originalPrice : 0;
        };
        const getNightlyPrice = (room) => {
          const salePrice = Number(room?.GiaGiam ?? room?.gia_giam);
          if (Number.isFinite(salePrice) && salePrice > 0) return salePrice;
          return getOriginalRoomPrice(room);
        };
        const getRoomDiscountPercent = (room) => {
          const originalPrice = getOriginalRoomPrice(room);
          const salePrice = getNightlyPrice(room);
          if (originalPrice <= 0 || salePrice <= 0 || salePrice >= originalPrice) return 0;
          return Math.round(((originalPrice - salePrice) / originalPrice) * 100);
        };
        const renderRoomPrice = (room) => {
          const originalPrice = Number(room.originalPrice ?? getOriginalRoomPrice(room));
          const salePrice = Number(room.price ?? getNightlyPrice(room));
          if (salePrice <= 0) return 'Liên hệ';
          const discountPercent = Number(room.discountPercent ?? getRoomDiscountPercent(room));
          if (discountPercent <= 0 || originalPrice <= salePrice) {
            return `
              <span class="customer-room-price">
                <span class="customer-room-price-current">
                  <span class="customer-room-price-sale">${salePrice.toLocaleString('vi-VN')} VND</span>
                  <span class="customer-room-price-per">/ đêm</span>
                </span>
              </span>
            `;
          }
          return `
            <span class="customer-room-price">
              <span class="customer-room-price-original">${originalPrice.toLocaleString('vi-VN')} VND</span>
              <span class="customer-room-price-current">
                <span class="customer-room-price-sale">${salePrice.toLocaleString('vi-VN')} VND</span>
                <span class="customer-room-price-per">/ đêm</span>
              </span>
              <span class="customer-room-discount-tag">-${discountPercent}%</span>
            </span>
          `;
        };
        const renderSummaryRoomPrice = (item) => {
          const salePrice = Number(item.price || 0);
          const originalPrice = Number(item.originalPrice || 0);
          const discountPercent = Number(item.discountPercent || 0);

          if (salePrice <= 0) return 'Liên hệ';

          if (discountPercent <= 0 || originalPrice <= salePrice) {
            return `
              <span class="customer-room-price">
                <span class="customer-room-price-current">
                  <span class="customer-room-price-sale">${salePrice.toLocaleString('vi-VN')} VND</span>
                  <span class="customer-room-price-per">/ đêm</span>
                </span>
              </span>
            `;
          }

          return `
            <span class="customer-room-price">
              <span class="customer-room-price-original">${originalPrice.toLocaleString('vi-VN')} VND</span>
              <span class="customer-room-price-current">
                <span class="customer-room-price-sale">${salePrice.toLocaleString('vi-VN')} VND</span>
                <span class="customer-room-price-per">/ đêm</span>
              </span>
              <span class="customer-room-discount-tag">-${discountPercent}%</span>
            </span>
          `;
        };
        const getRoomCount = (room) => {
          const phongs = room.phongs || [];
          const count = Number(room.soLuongPhong || room.so_luong_phong || (Array.isArray(phongs) ? phongs.length : 0));
          return Number.isFinite(count) && count > 0 ? count : 5;
        };
        const applyBackgroundImages = (root = document) => {
          root.querySelectorAll('[data-bg-image]').forEach((element) => {
            const backgroundImage = element.getAttribute('data-bg-image');
            if (backgroundImage && backgroundImage !== 'undefined' && backgroundImage !== 'null') {
              element.style.backgroundImage = `url("${backgroundImage}")`;
            }
          });
        };
        const jsonHeaders = {
          Accept: 'application/json',
        };

        const guestMinimums = {
          adults: 1,
          children: 0,
          rooms: 1,
        };

        function syncGuestControls() {
          const adults = Math.max(Number.parseInt(adultCount?.textContent || '1', 10) || 1, guestMinimums.adults);
          const children = Math.max(Number.parseInt(childrenCount?.textContent || '0', 10) || 0, guestMinimums.children);
          const rooms = Math.max(Number.parseInt(roomCount?.textContent || '1', 10) || 1, guestMinimums.rooms);

          if (adultCount) adultCount.textContent = String(adults);
          if (childrenCount) childrenCount.textContent = String(children);
          if (roomCount) roomCount.textContent = String(rooms);

          guestWrapper?.querySelectorAll('[data-guest-action]').forEach((button) => {
            const type = button.dataset.guestType;
            const counter = type ? guestWrapper.querySelector(`[data-guest-count="${type}"]`) : null;
            const current = Number.parseInt(counter?.textContent || '0', 10);
            const min = guestMinimums[type] ?? 0;

            button.disabled = button.dataset.guestAction === 'dec' && current <= min;
          });

          if (guestText) {
            guestText.textContent = `${adults} người lớn - ${children} trẻ em - ${rooms} phòng`;
          }

          if (summary) {
            summary.dataset.adults = String(adults);
            summary.dataset.children = String(children);
            summary.dataset.rooms = String(rooms);
          }
        }

        guestWrapper?.addEventListener('click', (event) => {
          const button = event.target instanceof Element ? event.target.closest('[data-guest-action]') : null;
          if (!button || !guestWrapper.contains(button)) return;

          event.preventDefault();
          event.stopImmediatePropagation();

          const type = button.dataset.guestType;
          const counter = type ? guestWrapper.querySelector(`[data-guest-count="${type}"]`) : null;
          if (!type || !counter) return;

          const min = guestMinimums[type] ?? 0;
          const current = Number.parseInt(counter.textContent || String(min), 10) || min;
          const next = button.dataset.guestAction === 'inc'
            ? current + 1
            : Math.max(current - 1, min);

          counter.textContent = String(next);
          syncGuestControls();
          summary?.dispatchEvent(new Event('booking-summary-change'));
        }, true);

        async function readApiJson(response, fallbackMessage) {
          const contentType = response.headers.get('content-type') || '';

          if (!contentType.includes('application/json')) {
            throw new Error(fallbackMessage);
          }

          return response.json();
        }

        const clearOldRoomCaches = () => {
          try {
            ['peachvalley:room-types:v3', 'peachvalley:room-types:v2'].forEach((key) => {
              window.localStorage?.removeItem(key);
              window.sessionStorage?.removeItem(key);
            });
          } catch (error) {
            // Storage can be unavailable; direct API fetch still works.
          }
        };
        const getRoomTypes = async () => {
          if (window.CustomerRoomApi?.getRoomTypes) {
            return window.CustomerRoomApi.getRoomTypes();
          }

          const response = await fetch(`/api/loai-phong?_=${Date.now()}`, {
            cache: 'no-store',
            headers: {
              ...jsonHeaders,
              'Cache-Control': 'no-cache',
            },
          });
          const result = await readApiJson(response, 'Không thể tải danh sách phòng');
          if (!result.success || !Array.isArray(result.data)) throw new Error(result.message || 'Không thể tải danh sách phòng');
          clearOldRoomCaches();
          return result.data;
        };

        function createSearchDate(year, month, day) {
          const date = new Date(year, month - 1, day);
          return date.getFullYear() === year && date.getMonth() === month - 1 && date.getDate() === day ? date : null;
        }

        function parseSearchDate(value) {
          const normalizedValue = String(value || '').trim();
          let match = normalizedValue.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
          if (match) {
            const [, day, month, year] = match;
            return createSearchDate(Number(year), Number(month), Number(day));
          }
          match = normalizedValue.match(/^(\d{4})-(\d{2})-(\d{2})$/);
          if (match) {
            const [, year, month, day] = match;
            return createSearchDate(Number(year), Number(month), Number(day));
          }
          const parsedDate = new Date(normalizedValue);
          return Number.isNaN(parsedDate.getTime()) ? null : parsedDate;
        }

        function formatApiDate(value) {
          const date = value instanceof Date ? value : parseSearchDate(value);
          if (!date) return '';
          const year = date.getFullYear();
          const month = String(date.getMonth() + 1).padStart(2, '0');
          const day = String(date.getDate()).padStart(2, '0');
          return `${year}-${month}-${day}`;
        }

        function formatDisplayDate(value) {
          const date = value instanceof Date ? value : parseSearchDate(value);
          if (!date) return '';
          const day = String(date.getDate()).padStart(2, '0');
          const month = String(date.getMonth() + 1).padStart(2, '0');
          const year = date.getFullYear();
          return `${day}/${month}/${year}`;
        }

        function applyInitialSearchFromUrl() {
          const params = new URLSearchParams(window.location.search);
          const checkIn = parseSearchDate(params.get('checkIn'));
          const checkOut = parseSearchDate(params.get('checkOut'));
          const adults = Number.parseInt(params.get('NguoiLon') || params.get('adults') || '', 10);
          const children = Number.parseInt(params.get('TreEm') || params.get('children') || '', 10);
          const rooms = Number.parseInt(params.get('SoPhong') || params.get('rooms') || '', 10);

          if (checkIn && checkInInput) {
            checkInInput.value = formatDisplayDate(checkIn);
          }

          if (checkOut && checkOutInput) {
            checkOutInput.value = formatDisplayDate(checkOut);
          }

          if (Number.isFinite(adults) && adultCount) {
            adultCount.textContent = String(Math.max(adults, 1));
          }

          if (Number.isFinite(children) && childrenCount) {
            childrenCount.textContent = String(Math.max(children, 0));
          }

          if (Number.isFinite(rooms) && roomCount) {
            roomCount.textContent = String(Math.max(rooms, 1));
          }
        }

        function getNights() {
          const checkIn = parseSearchDate(checkInInput?.value);
          const checkOut = parseSearchDate(checkOutInput?.value);
          if (!checkIn || !checkOut || checkIn >= checkOut) {
            return Number(summary?.dataset.nights || '1') || 1;
          }
          return Math.max(Math.round((checkOut - checkIn) / (24 * 60 * 60 * 1000)), 1);
        }

        function renderBookingSummary() {
          if (!bookingList || !bookingTotal) return;
          bookingList.innerHTML = '';
          let total = 0;
          const nights = getNights();
          let selectedRoomCount = 0;

          selections.forEach((item, key) => {
            selectedRoomCount += item.quantity;
            total += item.price * item.quantity * nights;
            const row = document.createElement('div');
            row.className = 'booking-item';
            row.innerHTML = `
              <div class="booking-item-title">Phòng: ${item.quantity} ${escapeHtml(item.name)}</div>
              <div class="booking-item-footer">
                <div class="booking-item-price">${renderSummaryRoomPrice(item)}</div>
                <button type="button" class="booking-item-cancel" data-room-cancel="${escapeAttr(key)}">
                  <span class="icon ion-ios-close"></span> Hủy
                </button>
              </div>
            `;
            bookingList.appendChild(row);
          });

          bookingTotal.textContent = `${total.toLocaleString('vi-VN')} VND`;

          if (bookingContinue) {
            const hasSelectedRooms = selectedRoomCount > 0;
            bookingContinue.classList.toggle('disabled', !hasSelectedRooms);
            bookingContinue.setAttribute('aria-disabled', hasSelectedRooms ? 'false' : 'true');

            if (hasSelectedRooms) {
              bookingContinue.removeAttribute('tabindex');
            } else {
              bookingContinue.setAttribute('tabindex', '-1');
            }
          }
        }

        function buildBookingPayload() {
          const checkIn = parseSearchDate(checkInInput?.value);
          const checkOut = parseSearchDate(checkOutInput?.value);
          const nights = getNights();
          const adults = Number.parseInt(adultCount?.textContent || '0', 10) || 0;
          const children = Number.parseInt(childrenCount?.textContent || '0', 10) || 0;
          const requestedRooms = Number.parseInt(roomCount?.textContent || '1', 10) || 1;
          const rooms = Array.from(selections.entries()).map(([key, item]) => {
            const room = roomsData.find((candidate) => String(candidate.id) === String(key)) || {};
            return {
              id: key,
              name: item.name,
              price: item.price,
              originalPrice: item.originalPrice,
              discountPercent: item.discountPercent,
              quantity: item.quantity,
              adults: Number(room.adults ?? item.adults ?? 0),
              children: Number(room.children ?? item.children ?? 0),
            };
          });
          const total = rooms.reduce((sum, room) => sum + (Number(room.price) || 0) * (Number(room.quantity) || 0) * nights, 0);

          return {
            checkIn: formatApiDate(checkIn),
            checkOut: formatApiDate(checkOut),
            nights,
            adults,
            children,
            requestedRooms,
            rooms,
            total,
            savedAt: new Date().toISOString(),
          };
        }

        function storeBookingPayload() {
          const payload = buildBookingPayload();

          if (!payload.rooms.length) {
            localStorage.removeItem('peachBookingSelection');
            return null;
          }

          localStorage.setItem('peachBookingSelection', JSON.stringify(payload));
          return payload;
        }

        function syncQuantity(input, nextValue = null, options = {}) {
          const { updateSelection = true } = options;
          const min = Number(input.min || '0');
          const max = Number(input.max || '1');
          const value = nextValue === null ? Number(input.value || '0') : nextValue;
          const normalizedValue = Math.min(Math.max(value, min), max);
          const stepper = input.closest('[data-room-qty-stepper]');
          const valueLabel = stepper?.querySelector('[data-room-qty-value]');
          const decrementButton = stepper?.querySelector('[data-room-qty-action="decrement"]');
          const incrementButton = stepper?.querySelector('[data-room-qty-action="increment"]');
          const key = input.dataset.roomId || input.dataset.roomName;

          input.value = String(normalizedValue);
          if (valueLabel) valueLabel.textContent = `${normalizedValue} phòng`;
          if (decrementButton) decrementButton.disabled = normalizedValue <= min;
          if (incrementButton) incrementButton.disabled = normalizedValue >= max;

          if (!updateSelection) {
            renderBookingSummary();
            return;
          }

          if (normalizedValue > 0) {
            selections.set(key, {
              name: input.dataset.roomName || 'Phòng',
              price: Number(input.dataset.roomPrice || '0'),
              originalPrice: Number(input.dataset.roomOriginalPrice || input.dataset.roomPrice || '0'),
              discountPercent: Number(input.dataset.roomDiscountPercent || '0'),
              quantity: normalizedValue,
              adults: Number(input.dataset.roomAdults || '0'),
              children: Number(input.dataset.roomChildren || '0'),
            });
          } else {
            selections.delete(key);
          }
          renderBookingSummary();
        }

        function openAmenitiesModal(trigger) {
          const modal = document.querySelector('[data-room-modal]');
          if (!modal) return;

          const title = modal.querySelector('[data-room-modal-title]');
          const area = modal.querySelector('[data-room-modal-area]');
          const desc = modal.querySelector('[data-room-modal-desc]');
          const slidesContainer = modal.querySelector('[data-room-modal-slides]');
          const amenitiesGrid = modal.querySelector('.room-amenities-modal-list .room-amenities-grid');
          const images = (trigger.dataset.roomImages || '').split('|').filter(Boolean);
          const amenities = (trigger.dataset.roomAmenities || '').split('|').filter(Boolean);

          if (title) title.textContent = trigger.dataset.roomTitle || 'Phòng';
          if (area) area.textContent = trigger.dataset.roomArea || '';
          if (desc) desc.textContent = trigger.dataset.roomDesc || '';
          if (slidesContainer) {
            slidesContainer.innerHTML = images.map((url, index) => `
              <div class="room-amenities-modal-slide${index === 0 ? ' is-active' : ''}" style="background-image: url('${escapeAttr(url)}')"></div>
            `).join('');
          }
          if (amenitiesGrid) {
            amenitiesGrid.innerHTML = amenities.length
              ? amenities.map((item) => `
                <div class="room-amenity-item">
                  <span class="room-amenity-icon ion-ios-checkmark-circle"></span>
                  <span>${escapeHtml(item)}</span>
                </div>
              `).join('')
              : '<div class="room-amenity-item"><span class="room-amenity-icon ion-ios-checkmark-circle"></span><span>Tiện nghi phòng</span></div>';
          }

          modal.classList.add('is-open');
          document.body.classList.add('modal-open');
        }

        function bindDynamicControls() {
          document.querySelectorAll('[data-room-slider]').forEach((slider) => {
            const slides = Array.from(slider.querySelectorAll('.room-result-slide'));
            if (!slides.length) return;
            let index = slides.findIndex((slide) => slide.classList.contains('is-active'));
            if (index < 0) {
              index = 0;
              slides[0].classList.add('is-active');
            }
            const show = (nextIndex) => {
              slides[index].classList.remove('is-active');
              index = (nextIndex + slides.length) % slides.length;
              slides[index].classList.add('is-active');
            };
            slider.querySelector('[data-room-slider-prev]')?.addEventListener('click', () => show(index - 1));
            slider.querySelector('[data-room-slider-next]')?.addEventListener('click', () => show(index + 1));
          });

          document.querySelectorAll('[data-room-qty-input]').forEach((input) => {
            const key = input.dataset.roomId || input.dataset.roomName;
            const selected = selections.get(key);

            if (selected) {
              input.value = String(Math.min(Number(selected.quantity || 0), Number(input.max || '1')));
            }

            syncQuantity(input, null, { updateSelection: false });
          });
          document.querySelectorAll('[data-room-qty-stepper]').forEach((stepper) => {
            const input = stepper.querySelector('[data-room-qty-input]');
            if (!(input instanceof HTMLInputElement)) return;
            stepper.querySelector('[data-room-qty-action="decrement"]')?.addEventListener('click', () => syncQuantity(input, Number(input.value || '0') - 1));
            stepper.querySelector('[data-room-qty-action="increment"]')?.addEventListener('click', () => syncQuantity(input, Number(input.value || '0') + 1));
          });

          document.querySelectorAll('[data-room-modal-trigger]').forEach((trigger) => {
            trigger.addEventListener('click', (event) => {
              event.stopPropagation();
              openAmenitiesModal(trigger);
            });
          });

          document.querySelectorAll('[data-room-card-trigger]').forEach((card) => {
            card.addEventListener('click', (event) => {
              const target = event.target instanceof Element ? event.target : null;
              if (target?.closest('a, button, input, select, textarea, .room-result-actions, [data-room-qty-stepper], [data-room-slider-prev], [data-room-slider-next], [data-room-modal-trigger]')) return;
              openAmenitiesModal(card);
            });
          });
        }

        function bindModalClose() {
          const modal = document.querySelector('[data-room-modal]');
          if (!modal) return;
          modal.querySelectorAll('[data-room-modal-close]').forEach((button) => {
            button.addEventListener('click', () => {
              modal.classList.remove('is-open');
              document.body.classList.remove('modal-open');
            });
          });

          const showModalSlide = (step) => {
            const slides = Array.from(modal.querySelectorAll('.room-amenities-modal-slide'));
            if (!slides.length) return;
            let index = slides.findIndex((slide) => slide.classList.contains('is-active'));
            if (index < 0) index = 0;
            slides[index].classList.remove('is-active');
            const nextIndex = (index + step + slides.length) % slides.length;
            slides[nextIndex].classList.add('is-active');
          };

          modal.querySelector('[data-room-modal-prev]')?.addEventListener('click', () => showModalSlide(-1));
          modal.querySelector('[data-room-modal-next]')?.addEventListener('click', () => showModalSlide(1));
        }

        function getRoomTypeId(room) {
          return room.MaLoaiPhong || room.ma_loai_phong || room.id;
        }

        function normalizeRoomType(room) {
          const id = getRoomTypeId(room);
          const name = room.TenLoaiPhong || room.ten_loai_phong || 'Phòng';
          const description = room.Mota || room.mo_ta || 'Phòng thoải mái, hiện đại và đầy đủ tiện nghi.';
          const adults = Number(room.NguoiLon ?? room.nguoi_lon ?? 0);
          const children = Number(room.TreEm ?? room.tre_em ?? 0);
          const amenities = room.tienNghis || room.tien_nghis || [];
          const amenitiesNames = Array.isArray(amenities) ? amenities.map((item) => item.TenTienNghi || item.ten_tien_nghi).filter(Boolean) : [];
          const capacity = Math.max((Number.isFinite(adults) ? adults : 0) + (Number.isFinite(children) ? children : 0), 1);

          return {
            id,
            name,
            description,
            adults,
            children,
            capacity,
            price: getNightlyPrice(room),
            originalPrice: getOriginalRoomPrice(room),
            discountPercent: getRoomDiscountPercent(room),
            images: getImages(room),
            roomCount: getRoomCount(room),
            amenitiesNames,
          };
        }

        function getRoomFilterValues() {
          return {
            keyword: String(roomFilterSearch?.value || '').trim().toLowerCase(),
            sort: roomFilterSort?.value || '',
          };
        }

        function getVisibleRoomTypes() {
          const filters = getRoomFilterValues();
          const normalizedRooms = availableRoomTypes.map((room) => ({
            source: room,
            view: normalizeRoomType(room),
          }));

          const filteredRooms = normalizedRooms.filter(({ view }) => {
            const searchableText = [
              view.name,
              view.description,
              ...view.amenitiesNames,
            ].join(' ').toLowerCase();
            const matchesKeyword = !filters.keyword || searchableText.includes(filters.keyword);

            return matchesKeyword;
          });

          if (filters.sort === 'price-asc') {
            filteredRooms.sort((left, right) => left.view.price - right.view.price);
          }

          if (filters.sort === 'price-desc') {
            filteredRooms.sort((left, right) => right.view.price - left.view.price);
          }

          return filteredRooms.map(({ source }) => source);
        }

        function applyRoomDisplayFilters() {
          renderRoomCards(getVisibleRoomTypes());
        }

        function renderRoomCards(rooms) {
          roomsData = rooms.map(normalizeRoomType);
          resultsContainer.innerHTML = '';

          if (roomsData.length === 0) {
            const emptyMessage = availableRoomTypes.length > 0
              ? 'Không có loại phòng phù hợp với bộ lọc bạn chọn.'
              : 'Không có loại phòng nào còn trống theo tiêu chí bạn chọn.';
            resultsContainer.innerHTML = `<div class="room-results-loading">${emptyMessage}</div>`;
            renderBookingSummary();
            return;
          }

          for (let i = 0; i < roomsData.length; i++) {
            const room = roomsData[i];
            const imageAttr = room.images.map(escapeAttr).join('|');
            const amenitiesAttr = room.amenitiesNames.map(escapeAttr).join('|');
            const slides = room.images.map((imageUrl, index) => `
              <div class="room-result-slide${index === 0 ? ' is-active' : ''}" data-bg-image="${escapeAttr(imageUrl)}"></div>
            `).join('');

            resultsContainer.insertAdjacentHTML('beforeend', `
              <div
                class="room-result-card"
                data-room-card-trigger
                data-room-id="${escapeAttr(room.id)}"
                data-room-title="${escapeAttr(room.name)}"
                data-room-area=""
                data-room-desc="${escapeAttr(room.description)}"
                data-room-images="${imageAttr}"
                data-room-amenities="${amenitiesAttr}"
              >
                <div class="room-result-slider" data-room-slider>
                  <button type="button" class="room-result-slider-btn prev" data-room-slider-prev aria-label="Ảnh trước">
                    <span class="icon ion-ios-arrow-back"></span>
                  </button>
                  <div class="room-result-slides">${slides}</div>
                  <button type="button" class="room-result-slider-btn next" data-room-slider-next aria-label="Ảnh sau">
                    <span class="icon ion-ios-arrow-forward"></span>
                  </button>
                </div>
                <div class="room-result-content">
                  <div class="room-result-header">
                    <h3>${escapeHtml(room.name)}</h3>
                    <span class="room-result-capacity">
                      <span class="icon ion-ios-people"></span>
                      ${room.capacity} khách
                    </span>
                  </div>
                  <p class="room-result-desc">${escapeHtml(room.description)}</p>
                  <div class="room-result-meta">
                    <span><strong>Người lớn:</strong> ${Number.isFinite(room.adults) ? room.adults : 0}</span>
                    <span><strong>Trẻ em:</strong> ${Number.isFinite(room.children) ? room.children : 0}</span>
                    <span><strong>Còn trống:</strong> ${room.roomCount} phòng</span>
                  </div>
                  <button
                    type="button"
                    class="room-result-amenities-link"
                    data-room-modal-trigger
                    data-room-title="${escapeAttr(room.name)}"
                    data-room-area=""
                    data-room-desc="${escapeAttr(room.description)}"
                    data-room-images="${imageAttr}"
                    data-room-amenities="${amenitiesAttr}"
                  >
                    Xem tất cả tiện nghi
                  </button>
                  <div class="room-result-footer">
                    <div class="room-result-price">
                      <div class="room-result-price-line">
                        <span class="value">${renderRoomPrice(room)}</span>
                      </div>
                    </div>
                    <div class="room-result-actions">
                      <div class="room-result-qty-stepper" data-room-qty-stepper>
                        <button type="button" class="room-result-qty-btn" data-room-qty-action="decrement" aria-label="Giảm số phòng">-</button>
                        <input
                          type="hidden"
                          value="0"
                          min="0"
                          max="${room.roomCount}"
                          data-room-qty
                          data-room-qty-input
                          data-room-id="${escapeAttr(room.id)}"
                          data-room-name="${escapeAttr(room.name)}"
                          data-room-price="${room.price}"
                          data-room-original-price="${room.originalPrice}"
                          data-room-discount-percent="${room.discountPercent}"
                          data-room-adults="${Number.isFinite(room.adults) ? room.adults : 0}"
                          data-room-children="${Number.isFinite(room.children) ? room.children : 0}"
                        >
                        <span class="room-result-qty-value" data-room-qty-value>0 phòng</span>
                        <button type="button" class="room-result-qty-btn" data-room-qty-action="increment" aria-label="Tăng số phòng">+</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            `);
          }

          applyBackgroundImages(resultsContainer);
          bindDynamicControls();
          renderBookingSummary();
        }

        async function loadRooms() {
          if (!resultsContainer) return;
          resultsContainer.innerHTML = '<div class="room-results-loading">Đang kiểm tra dữ liệu phòng mới nhất...</div>';

          try {
            const rooms = await getRoomTypes();
            if (!Array.isArray(rooms)) throw new Error('Không thể tải danh sách phòng');
            allRoomTypes = rooms;
            await searchAvailableRooms(false);
          } catch (error) {
            console.error('Error loading room types:', error);
            resultsContainer.innerHTML = '<div class="room-results-loading">Không thể tải danh sách phòng.</div>';
          }
        }

        function validateSearchInputs(shouldAlert = true) {
          const checkIn = parseSearchDate(checkInInput?.value);
          const checkOut = parseSearchDate(checkOutInput?.value);
          const adults = Number.parseInt(adultCount?.textContent || '0', 10);
          const children = Number.parseInt(childrenCount?.textContent || '0', 10);
          const rooms = Number.parseInt(roomCount?.textContent || '1', 10);

          if (!checkIn || !checkOut) {
            if (shouldAlert) alert('Vui lòng nhập ngày theo định dạng dd/mm/yyyy');
            return null;
          }
          if (checkIn >= checkOut) {
            if (shouldAlert) alert('Ngày nhận phòng phải trước ngày trả phòng');
            return null;
          }

          return {
            checkIn,
            checkOut,
            adults,
            children,
            rooms: Math.max(Number.isFinite(rooms) ? rooms : 1, 1),
          };
        }

        async function searchAvailableRooms(shouldAlert = true) {
          if (!resultsContainer) return;
          const search = validateSearchInputs(shouldAlert);
          if (!search) return;

          try {
            if (!allRoomTypes.length) {
              allRoomTypes = await getRoomTypes();
            }

            if (shouldAlert) {
              resultsContainer.innerHTML = '<div class="room-results-loading">Đang tìm phòng trống...</div>';
            }

            const params = new URLSearchParams({
              checkIn: formatApiDate(search.checkIn),
              checkOut: formatApiDate(search.checkOut),
              NguoiLon: String(Math.max(search.adults, 1)),
              TreEm: String(Math.max(search.children, 0)),
              SoPhong: String(Math.max(search.rooms, 1)),
            });

            if (shouldAlert) {
              const nextUrl = new URL(window.location.href);
              params.forEach((value, key) => nextUrl.searchParams.set(key, value));
              window.history.replaceState({}, '', nextUrl.toString());
            }

            const response = await fetch(`/api/phong/tim-kiem?${params.toString()}`, {
              cache: 'no-store',
              headers: jsonHeaders,
            });
            const result = await readApiJson(response, 'Không thể tìm phòng trống');

            if (!result.success || !Array.isArray(result.data)) {
              const validationMessage = Object.values(result.errors || {}).flat().filter(Boolean).join('\n');
              throw new Error(validationMessage || result.message || 'Không thể tìm phòng trống');
            }

            const availableCounts = new Map();
            result.data.forEach((room) => {
              const roomTypeId = room.MaLoaiPhong || room.ma_loai_phong || room.loaiPhong?.MaLoaiPhong || room.loai_phong?.ma_loai_phong;
              if (!roomTypeId) return;
              const key = String(roomTypeId);
              const roomRows = Array.isArray(room.phongs) ? room.phongs : [];
              const availableCount = Number(
                roomRows.length || room.soPhongTrong || room.so_luong_phong || room.tongPhong || room.tong_phong || 0
              );

              availableCounts.set(key, Number.isFinite(availableCount) ? availableCount : 0);
            });

            const searchedRoomTypes = allRoomTypes
              .map((roomType) => {
                const roomTypeId = getRoomTypeId(roomType);
                const availableCount = availableCounts.get(String(roomTypeId)) || 0;
                return availableCount >= Math.max(search.rooms, 1)
                  ? { ...roomType, soLuongPhong: availableCount, so_luong_phong: availableCount }
                  : null;
              })
              .filter(Boolean);

            availableRoomTypes = searchedRoomTypes;
            renderRoomCards(getVisibleRoomTypes());

            if (searchedRoomTypes.length === 0 && shouldAlert) {
              alert('Không có phòng trống phù hợp với ngày và số khách bạn chọn.');
            }
          } catch (error) {
            console.error('Error searching available rooms:', error);
            if (shouldAlert) {
              alert(error.message || 'Không thể tìm phòng trống. Vui lòng thử lại.');
            }
          }
        }

        function filterRooms(shouldAlert = true) {
          searchAvailableRooms(shouldAlert);
        }

        bookingList?.addEventListener('click', (event) => {
          const cancelButton = event.target instanceof Element ? event.target.closest('[data-room-cancel]') : null;
          if (!cancelButton) return;
          const key = cancelButton.getAttribute('data-room-cancel');
          const input = resultsContainer.querySelector(`[data-room-qty-input][data-room-id="${CSS.escape(String(key))}"]`);
          if (input instanceof HTMLInputElement) {
            syncQuantity(input, 0);
            return;
          }

          selections.delete(key);
          renderBookingSummary();
        });

        searchBtn?.addEventListener('click', () => filterRooms(true));
        roomFilterSearch?.addEventListener('input', applyRoomDisplayFilters);
        roomFilterSort?.addEventListener('change', applyRoomDisplayFilters);
        roomFilterReset?.addEventListener('click', () => {
          if (roomFilterSearch) roomFilterSearch.value = '';
          if (roomFilterSort) roomFilterSort.value = '';
          applyRoomDisplayFilters();
        });
        bookingContinue?.addEventListener('click', (event) => {
          if (bookingContinue.getAttribute('aria-disabled') === 'true') {
            event.preventDefault();
            alert('Vui lòng chọn ít nhất 1 phòng trước khi đặt.');
            return;
          }

          storeBookingPayload();
        });
        checkInInput?.addEventListener('keypress', (event) => {
          if (event.key === 'Enter') filterRooms(true);
        });
        checkOutInput?.addEventListener('keypress', (event) => {
          if (event.key === 'Enter') filterRooms(true);
        });
        summary?.addEventListener('booking-summary-change', renderBookingSummary);
        applyInitialSearchFromUrl();
        syncGuestControls();
        window.setTimeout(syncGuestControls, 0);
        bindModalClose();
        loadRooms();
      });
    </script>
  </body>
</html>
