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

    @php
      $searchSummary = [
        'checkin' => '2026-04-14',
        'checkout' => '2026-04-17',
        'nguoiLon' => 2,
        'treEm' => 1,
        'soDem' => 3,
        'soPhong' => 1,
      ];

      $roomResults = [
        [
          'tenLoaiPhong' => 'Deluxe Family Triple',
          'moTa' => 'Phòng dành cho gia đình, bố trí 1 giường đôi và 1 giường đơn, không gian thoáng và đầy đủ tiện nghi.',
          'moTaDayDu' => 'Phòng dành cho gia đình với thiết kế ấm cúng, tầm nhìn đẹp và không gian rộng rãi. Bố trí nội thất hiện đại, phù hợp cho nhóm bạn hoặc gia đình nhỏ muốn nghỉ dưỡng thoải mái.',
          'soNguoiToiDa' => 4,
          'dienTich' => 38,
          'giuong' => '1 giường đôi, 1 giường đơn',
          'giaPhong' => 1840000,
          'anh' => 'resources/customer/images/deluxe_family.jpg',
          'images' => [
            'resources/customer/images/deluxe_family.jpg',
            'resources/customer/images/deluxe_family1.jpg',
            'resources/customer/images/810491790.jpg',
          ],
        ],
        [
          'tenLoaiPhong' => 'Executive Suite',
          'moTa' => 'Không gian rộng với khu tiếp khách riêng, phù hợp cho kỳ nghỉ cao cấp hoặc chuyến công tác dài ngày.',
          'moTaDayDu' => 'Phòng hạng cao cấp với khu tiếp khách riêng, tầm nhìn đẹp và nội thất tinh tế. Không gian rộng rãi giúp bạn thư giãn trọn vẹn trong suốt kỳ nghỉ.',
          'soNguoiToiDa' => 2,
          'dienTich' => 68,
          'giuong' => '1 giường king size',
          'giaPhong' => 2530000,
          'anh' => 'resources/customer/images/suite.jpg',
          'images' => [
            'resources/customer/images/suite.jpg',
            'resources/customer/images/810491789.jpg',
            'resources/customer/images/810491790.jpg',
          ],
        ],
        [
          'tenLoaiPhong' => 'Superior Room',
          'moTa' => 'Lựa chọn cân bằng giữa chi phí và tiện nghi, thiết kế hiện đại, phù hợp cho cặp đôi hoặc khách cá nhân.',
          'moTaDayDu' => 'Phòng thiết kế hiện đại, tiện nghi đầy đủ và ánh sáng tự nhiên. Lựa chọn phù hợp cho khách cá nhân hoặc cặp đôi muốn tiết kiệm chi phí.',
          'soNguoiToiDa' => 2,
          'dienTich' => 32,
          'giuong' => '1 giường queen',
          'giaPhong' => 1490000,
          'anh' => 'resources/customer/images/superior.jpg',
          'images' => [
            'resources/customer/images/superior.jpg',
            'resources/customer/images/room-2.jpg',
            'resources/customer/images/room-3.jpg',
          ],
        ],
      ];
    @endphp

    <section class="search-summary">
      <div class="container">
        <div class="search-summary-bar">
          <div class="search-summary-item">
            <span class="search-summary-label">Ngày nhận</span>
            <input
              type="text"
              class="search-summary-input"
              data-search-checkin
              value="{{ \Carbon\Carbon::parse($searchSummary['checkin'])->format('d/m/Y') }}"
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
              value="{{ \Carbon\Carbon::parse($searchSummary['checkout'])->format('d/m/Y') }}"
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
                  {{ $searchSummary['nguoiLon'] }} người lớn - {{ $searchSummary['treEm'] }} trẻ em
                </span>
                <span class="icon ion-ios-arrow-down"></span>
              </button>
              <div class="search-summary-guest-panel" data-search-guest-panel>
                <div class="search-guest-row">
                  <span>Người lớn</span>
                  <div class="search-guest-stepper">
                    <button type="button" data-guest-action="dec" data-guest-type="adults">-</button>
                    <span data-guest-count="adults">{{ $searchSummary['nguoiLon'] }}</span>
                    <button type="button" data-guest-action="inc" data-guest-type="adults">+</button>
                  </div>
                </div>
                <div class="search-guest-row">
                  <span>Trẻ em</span>
                  <div class="search-guest-stepper">
                    <button type="button" data-guest-action="dec" data-guest-type="children">-</button>
                    <span data-guest-count="children">{{ $searchSummary['treEm'] }}</span>
                    <button type="button" data-guest-action="inc" data-guest-type="children">+</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <button type="button" class="search-summary-action" id="searchBtn" data-search-action>
            <span class="icon ion-ios-search"></span>
            Tìm kiếm
          </button>
        </div>
      </div>
    </section>

    <section class="ftco-section search-results-section">
      <div class="container">
        <div class="row">
          <div class="col-lg-8">
            @foreach ($roomResults as $room)
              <div
                class="room-result-card"
                data-room-card-trigger
                data-room-title="{{ $room['tenLoaiPhong'] }}"
                data-room-area="{{ $room['dienTich'] }} m²"
                data-room-desc="{{ $room['moTaDayDu'] }}"
                data-room-images="{{ collect($room['images'])->map(fn ($image) => Vite::asset($image))->implode('|') }}"
              >
                <div class="room-result-slider" data-room-slider>
                  <button type="button" class="room-result-slider-btn prev" data-room-slider-prev aria-label="Ảnh trước">
                    <span class="icon ion-ios-arrow-back"></span>
                  </button>
                  <div class="room-result-slides">
                    @foreach ($room['images'] as $index => $image)
                      <div class="room-result-slide{{ $index === 0 ? ' is-active' : '' }}" data-bg-image="{{ Vite::asset($image) }}"></div>
                    @endforeach
                  </div>
                  <button type="button" class="room-result-slider-btn next" data-room-slider-next aria-label="Ảnh sau">
                    <span class="icon ion-ios-arrow-forward"></span>
                  </button>
                </div>
                <div class="room-result-content">
                  <div class="room-result-header">
                    <h3>{{ $room['tenLoaiPhong'] }}</h3>
                    <span class="room-result-capacity">
                      <span class="icon ion-ios-people"></span>
                      {{ $room['soNguoiToiDa'] }} khách
                    </span>
                  </div>
                  <p class="room-result-desc">{{ $room['moTa'] }}</p>
                  <div class="room-result-meta">
                    <span><strong>Giường:</strong> {{ $room['giuong'] }}</span>
                    <span><strong>Diện tích:</strong> {{ $room['dienTich'] }} m²</span>
                  </div>
                  <button
                    type="button"
                    class="room-result-amenities-link"
                    data-room-modal-trigger
                    data-room-title="{{ $room['tenLoaiPhong'] }}"
                    data-room-area="{{ $room['dienTich'] }} m²"
                    data-room-desc="{{ $room['moTaDayDu'] }}"
                    data-room-images="{{ collect($room['images'])->map(fn ($image) => Vite::asset($image))->implode('|') }}"
                  >
                    Xem tất cả tiện nghi
                  </button>
                  <div class="room-result-footer">
                    <div class="room-result-price">
                      <span class="label">Giá chỉ từ</span>
                      <span class="room-result-price-line">
                        <span class="value">{{ number_format($room['giaPhong'], 0, ',', '.') }} VND</span>
                        <span class="per">/ đêm</span>
                      </span>
                    </div>
                    <div class="room-result-actions">
                      <div class="room-result-qty-stepper" data-room-qty-stepper>
                        <button type="button" class="room-result-qty-btn" data-room-qty-action="decrement" aria-label="Giảm số phòng">-</button>
                        <input
                          type="hidden"
                          value="0"
                          min="0"
                          max="5"
                          data-room-qty
                          data-room-qty-input
                          data-room-name="{{ $room['tenLoaiPhong'] }}"
                          data-room-price="{{ $room['giaPhong'] }}"
                        >
                        <span class="room-result-qty-value" data-room-qty-value>0 phòng</span>
                        <button type="button" class="room-result-qty-btn" data-room-qty-action="increment" aria-label="Tăng số phòng">+</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
          <div class="col-lg-4">
            <div
              class="search-summary-card"
              data-booking-summary
              data-nights="{{ $searchSummary['soDem'] - 1 }}"
              data-adults="{{ $searchSummary['nguoiLon'] }}"
              data-children="{{ $searchSummary['treEm'] }}"
              data-checkin="{{ $searchSummary['checkin'] }}"
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
              <a href="{{ route('customer.info-booking') }}" class="btn btn-primary w-100 search-summary-cta">Đặt ngay</a>
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
        const searchBtn = document.getElementById('searchBtn');
        const checkInInput = document.querySelector('[data-search-checkin]');
        const checkOutInput = document.querySelector('[data-search-checkout]');
        const adultCount = document.querySelector('[data-guest-count="adults"]');
        const childrenCount = document.querySelector('[data-guest-count="children"]');
        const roomCards = document.querySelectorAll('.room-result-card');

        // Room data
        const roomsData = [
          {
            name: 'Deluxe Family Triple',
            capacity: 4,
            element: roomCards[0]
          },
          {
            name: 'Executive Suite',
            capacity: 2,
            element: roomCards[1]
          },
          {
            name: 'Superior Room',
            capacity: 2,
            element: roomCards[2]
          }
        ];

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

        function createSearchDate(year, month, day) {
          const date = new Date(year, month - 1, day);

          if (
            date.getFullYear() !== year ||
            date.getMonth() !== month - 1 ||
            date.getDate() !== day
          ) {
            return null;
          }

          return date;
        }

        function filterRooms() {
          const checkIn = parseSearchDate(checkInInput.value);
          const checkOut = parseSearchDate(checkOutInput.value);
          const adults = parseInt(adultCount.textContent);
          const children = parseInt(childrenCount.textContent);
          const totalGuests = adults + children;

          if (!checkIn || !checkOut) {
            alert('Vui lòng nhập ngày theo định dạng dd/mm/yyyy');
            return;
          }

          console.log('[v0] Filtering with:', {
            checkIn: checkIn.toLocaleDateString('vi-VN'),
            checkOut: checkOut.toLocaleDateString('vi-VN'),
            totalGuests: totalGuests,
            adults: adults,
            children: children
          });

          // Validate dates
          if (checkIn >= checkOut) {
            alert('Ngày nhận phòng phải trước ngày trả phòng');
            return;
          }

          // Filter rooms based on capacity - keep layout intact
          let visibleCount = 0;
          roomsData.forEach(room => {
            if (totalGuests > 0 && totalGuests <= room.capacity) {
              room.element.classList.remove('room-hidden');
              visibleCount++;
            } else if (totalGuests > room.capacity) {
              room.element.classList.add('room-hidden');
            } else {
              room.element.classList.remove('room-hidden');
              visibleCount++;
            }
          });

          if (visibleCount === 0) {
            alert('Không có phòng nào phù hợp với số khách bạn chọn. Vui lòng thay đổi tiêu chí tìm kiếm.');
            roomsData.forEach(room => room.element.classList.remove('room-hidden'));
          }
        }

        // Search button click
        searchBtn.addEventListener('click', filterRooms);

        // Allow Enter key in date inputs
        checkInInput.addEventListener('keypress', function(e) {
          if (e.key === 'Enter') filterRooms();
        });

        checkOutInput.addEventListener('keypress', function(e) {
          if (e.key === 'Enter') filterRooms();
        });
      });
    </script>
  </body>
</html>
