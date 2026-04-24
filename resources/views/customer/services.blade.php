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

    <section class="ftco-section ftco-menu bg-light service-section" data-service-section>
      <div class="container">
        <div class="row justify-content-center mb-5 pb-3">
          <div class="col-md-7 heading-section text-center ftco-animate">
            <span class="subheading">Dịch vụ ăn uống</span>
            <h2>Thực đơn của khách sạn</h2>
          </div>
        </div>

        <div class="row service-page-grid" data-service-page-grid>
          <div class="col-lg-6 col-xl-6 d-flex service-page-item" data-service-page-item>
            <div class="pricing-entry service-pricing-entry rounded d-flex ftco-animate">
              <div class="img" data-bg-image="{{ asset('customers/images/menu-1.jpg') }}"></div>
              <div class="desc p-4">
                <div class="d-md-flex text align-items-start">
                  <h3><span>Buffet sáng</span></h3>
                  <span class="price">250.000 VND</span>
                </div>
                <div class="d-block">
                  <p class="service-type-label">Dịch vụ ăn uống</p>
                  <button
                    class="service-booking-trigger"
                    type="button"
                    data-service-booking-trigger
                    data-service-id="1"
                    data-service-type="0"
                  >
                    Đặt dịch vụ
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-xl-6 d-flex service-page-item" data-service-page-item>
            <div class="pricing-entry service-pricing-entry rounded d-flex ftco-animate">
              <div class="img" data-bg-image="{{ asset('customers/images/menu-2.jpg') }}"></div>
              <div class="desc p-4">
                <div class="d-md-flex text align-items-start">
                  <h3><span>Trà chiều tại sân vườn</span></h3>
                  <span class="price">180.000 VND</span>
                </div>
                <div class="d-block">
                  <p class="service-type-label">Dịch vụ ăn uống</p>
                  <button
                    class="service-booking-trigger"
                    type="button"
                    data-service-booking-trigger
                    data-service-id="2"
                    data-service-type="0"
                  >
                    Đặt dịch vụ
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="service-pagination" data-service-pagination hidden></div>
      </div>
    </section>

    <section class="ftco-section ftco-menu bg-light service-section" data-service-section>
      <div class="container">
        <div class="row justify-content-center mb-5 pb-3">
          <div class="col-md-7 heading-section text-center ftco-animate">
            <span class="subheading">Dịch vụ giải trí</span>
            <h2>Loại hình giải trí của khách sạn</h2>
          </div>
        </div>

        <div class="row service-page-grid" data-service-page-grid>
          <div class="col-lg-6 col-xl-6 d-flex service-page-item" data-service-page-item>
            <div class="pricing-entry service-pricing-entry rounded d-flex ftco-animate">
              <div class="img" data-bg-image="{{ asset('customers/images/dv_spa.jpg') }}"></div>
              <div class="desc p-4">
                <div class="d-md-flex text align-items-start">
                  <h3><span>Spa thư giãn 60 phút</span></h3>
                  <span class="price">650.000 VND</span>
                </div>
                <div class="d-block">
                  <p class="service-type-label">Dịch vụ giải trí</p>
                  <button
                    class="service-booking-trigger"
                    type="button"
                    data-service-booking-trigger
                    data-service-id="3"
                    data-service-type="2"
                  >
                    Đặt dịch vụ
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-xl-6 d-flex service-page-item" data-service-page-item>
            <div class="pricing-entry service-pricing-entry rounded d-flex ftco-animate">
              <div class="img" data-bg-image="{{ asset('customers/images/dv_golf.jpg') }}"></div>
              <div class="desc p-4">
                <div class="d-md-flex text align-items-start">
                  <h3><span>Sân golf mini</span></h3>
                  <span class="price">400.000 VND</span>
                </div>
                <div class="d-block">
                  <p class="service-type-label">Dịch vụ giải trí</p>
                  <button
                    class="service-booking-trigger"
                    type="button"
                    data-service-booking-trigger
                    data-service-id="4"
                    data-service-type="2"
                  >
                    Đặt dịch vụ
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="service-pagination" data-service-pagination hidden></div>
      </div>
    </section>

    <section class="ftco-section ftco-menu bg-light service-section" data-service-section>
      <div class="container">
        <div class="row justify-content-center mb-5 pb-3">
          <div class="col-md-7 heading-section text-center ftco-animate">
            <span class="subheading">Dịch vụ phòng</span>
            <h2>Dịch vụ phòng của khách sạn</h2>
          </div>
        </div>

        <div class="row service-page-grid" data-service-page-grid>
          <div class="col-lg-6 col-xl-6 d-flex service-page-item" data-service-page-item>
            <div class="pricing-entry service-pricing-entry rounded d-flex ftco-animate">
              <div class="img" data-bg-image="{{ asset('customers/images/room-1.jpg') }}"></div>
              <div class="desc p-4">
                <div class="d-md-flex text align-items-start">
                  <h3><span>Dọn phòng buổi tối</span></h3>
                  <span class="price">150.000 VND</span>
                </div>
                <div class="d-block">
                  <p class="service-type-label">Dịch vụ phòng</p>
                  <button
                    class="service-booking-trigger"
                    type="button"
                    data-service-booking-trigger
                    data-service-id="5"
                    data-service-type="1"
                  >
                    Đặt dịch vụ
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-xl-6 d-flex service-page-item" data-service-page-item>
            <div class="pricing-entry service-pricing-entry rounded d-flex ftco-animate">
              <div class="img" data-bg-image="{{ asset('customers/images/room-2.jpg') }}"></div>
              <div class="desc p-4">
                <div class="d-md-flex text align-items-start">
                  <h3><span>Trang trí phòng kỷ niệm</span></h3>
                  <span class="price">500.000 VND</span>
                </div>
                <div class="d-block">
                  <p class="service-type-label">Dịch vụ phòng</p>
                  <button
                    class="service-booking-trigger"
                    type="button"
                    data-service-booking-trigger
                    data-service-id="6"
                    data-service-type="1"
                  >
                    Đặt dịch vụ
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="service-pagination" data-service-pagination hidden></div>
      </div>
    </section>

    <div
      class="service-booking-modal"
      data-service-booking-modal
      data-service-options='[{"id":"1","name":"Buffet sáng","type":"0","price":250000},{"id":"2","name":"Trà chiều tại sân vườn","type":"0","price":180000},{"id":"3","name":"Spa thư giãn 60 phút","type":"2","price":650000},{"id":"4","name":"Sân golf mini","type":"2","price":400000},{"id":"5","name":"Dọn phòng buổi tối","type":"1","price":150000},{"id":"6","name":"Trang trí phòng kỷ niệm","type":"1","price":500000}]'
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
            <option value="0">Dịch vụ ăn uống</option>
            <option value="2">Dịch vụ giải trí</option>
            <option value="1">Dịch vụ phòng</option>
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
                <option value="00">00</option>
                <option value="01">01</option>
                <option value="02">02</option>
                <option value="03">03</option>
                <option value="04">04</option>
                <option value="05">05</option>
                <option value="06">06</option>
                <option value="07">07</option>
                <option value="08">08</option>
                <option value="09">09</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
                <option value="13">13</option>
                <option value="14">14</option>
                <option value="15">15</option>
                <option value="16">16</option>
                <option value="17">17</option>
                <option value="18">18</option>
                <option value="19">19</option>
                <option value="20">20</option>
                <option value="21">21</option>
                <option value="22">22</option>
                <option value="23">23</option>
              </select>
              <span class="service-booking-time-divider" aria-hidden="true">:</span>
              <select id="service_booking_minute" data-service-minute required>
                <option value="00">00</option>
                <option value="01">01</option>
                <option value="02">02</option>
                <option value="03">03</option>
                <option value="04">04</option>
                <option value="05">05</option>
                <option value="06">06</option>
                <option value="07">07</option>
                <option value="08">08</option>
                <option value="09">09</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
                <option value="13">13</option>
                <option value="14">14</option>
                <option value="15">15</option>
                <option value="16">16</option>
                <option value="17">17</option>
                <option value="18">18</option>
                <option value="19">19</option>
                <option value="20">20</option>
                <option value="21">21</option>
                <option value="22">22</option>
                <option value="23">23</option>
                <option value="24">24</option>
                <option value="25">25</option>
                <option value="26">26</option>
                <option value="27">27</option>
                <option value="28">28</option>
                <option value="29">29</option>
                <option value="30">30</option>
                <option value="31">31</option>
                <option value="32">32</option>
                <option value="33">33</option>
                <option value="34">34</option>
                <option value="35">35</option>
                <option value="36">36</option>
                <option value="37">37</option>
                <option value="38">38</option>
                <option value="39">39</option>
                <option value="40">40</option>
                <option value="41">41</option>
                <option value="42">42</option>
                <option value="43">43</option>
                <option value="44">44</option>
                <option value="45">45</option>
                <option value="46">46</option>
                <option value="47">47</option>
                <option value="48">48</option>
                <option value="49">49</option>
                <option value="50">50</option>
                <option value="51">51</option>
                <option value="52">52</option>
                <option value="53">53</option>
                <option value="54">54</option>
                <option value="55">55</option>
                <option value="56">56</option>
                <option value="57">57</option>
                <option value="58">58</option>
                <option value="59">59</option>
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
