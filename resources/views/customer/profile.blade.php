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

    <section class="customer-account-section">
      <div class="container">
        <div class="customer-account-shell">
          @include('customer.partials.account-sidebar', ['active' => 'profile'])

          <main class="customer-account-main">
            <div class="customer-account-heading">
              <div class="eyebrow">Thông tin cá nhân</div>
              <p>Thông tin khách hàng đã đăng ký với Peach Valley.</p>
            </div>

            <div class="customer-profile-view" data-profile-view>
              <div class="customer-profile-view-top">
                <div>
                  <div class="customer-profile-view-name">Nguyễn Minh An</div>
                  <p>minhan@gmail.com</p>
                </div>
                <button type="button" class="customer-profile-submit" data-profile-edit-trigger>Chỉnh sửa</button>
              </div>

              <div class="customer-profile-points-panel">
                <span class="customer-profile-points-icon ion-ios-star"></span>
                <div class="customer-profile-points">
                  <strong>120</strong>
                  <span>điểm</span>
                </div>
                <p>Điểm có thể dùng để đổi hoặc áp dụng các ưu đãi trong kho khuyến mãi.</p>
              </div>

              <div class="customer-profile-view-grid">
                <div class="customer-profile-view-item"><span>Họ và tên</span>Nguyễn Minh An</div>
                <div class="customer-profile-view-item"><span>Số điện thoại</span>0901234567</div>
                <div class="customer-profile-view-item"><span>CCCD</span>079204000111</div>
                <div class="customer-profile-view-item"><span>Ngày sinh</span>12/04/1998</div>
                <div class="customer-profile-view-item"><span>Giới tính</span>Nam</div>
                <div class="customer-profile-view-item customer-profile-view-full"><span>Địa chỉ</span>25 Nguyễn Đình Chiểu, Quận 3, TP. Hồ Chí Minh</div>
              </div>
            </div>

            <form
              class="customer-profile-form"
              data-customer-profile-form
              data-profile-locked="false"
              data-selected-district="Quận 3"
              hidden
            >
              <div class="customer-profile-row">
                <label class="customer-profile-label" for="profile_email">Địa chỉ email</label>
                <div class="customer-profile-control">
                  <div class="customer-email-line">
                    <span>minhan@gmail.com</span>
                  </div>
                  <p class="customer-profile-help">Email này dùng để đăng nhập và nhận xác nhận đặt phòng.</p>
                  <input type="hidden" id="profile_email" name="email" value="minhan@gmail.com">
                </div>
              </div>

              <div class="customer-profile-row">
                <div class="customer-profile-label">Điểm tích lũy</div>
                <div class="customer-profile-control">
                  <div class="customer-profile-points">
                    <strong>120</strong>
                    <span>điểm tích lũy</span>
                  </div>
                  <p class="customer-profile-help">Điểm có thể dùng để đổi hoặc áp dụng các ưu đãi trong kho khuyến mãi.</p>
                </div>
              </div>

              <div class="customer-profile-row">
                <label class="customer-profile-label" for="display_name">Họ và tên</label>
                <div class="customer-profile-control">
                  <input
                    id="display_name"
                    name="display_name"
                    type="text"
                    placeholder="Nhập tên hiển thị"
                    minlength="2"
                    maxlength="60"
                    pattern="^[A-Za-zÀ-ỹĐđ\\s]+$"
                    title="Tên chỉ gồm chữ cái và khoảng trắng."
                    value="Nguyễn Minh An"
                    data-profile-editable
                    required>
                </div>
              </div>

              <div class="customer-profile-row">
                <label class="customer-profile-label" for="phone">Số điện thoại</label>
                <div class="customer-profile-control">
                  <input
                    id="phone"
                    name="phone"
                    type="tel"
                    inputmode="numeric"
                    placeholder="Nhập số điện thoại"
                    maxlength="10"
                    pattern="^0[0-9]{9}$"
                    title="Số điện thoại gồm 10 chữ số và bắt đầu bằng 0."
                    data-text-filter="digits"
                    value="0901234567"
                    data-profile-editable
                    required>
                  <p class="customer-profile-help">Khách sạn sẽ liên hệ qua số này nếu cần xác nhận đặt phòng.</p>
                </div>
              </div>

              <div class="customer-profile-row">
                <label class="customer-profile-label" for="cccd">CCCD</label>
                <div class="customer-profile-control">
                  <input
                    id="cccd"
                    name="cccd"
                    type="text"
                    inputmode="numeric"
                    placeholder="Nhập số CCCD"
                    maxlength="12"
                    pattern="^[0-9]{12}$"
                    title="CCCD gồm đúng 12 chữ số."
                    data-text-filter="digits"
                    value="079204000111"
                    data-profile-editable
                    required>
                </div>
              </div>

              <div class="customer-profile-row">
                <label class="customer-profile-label" for="birthday">Ngày sinh</label>
                <div class="customer-profile-control">
                  <input
                    id="birthday"
                    name="birthday"
                    type="date"
                    max="2026-04-22"
                    value="1998-04-12"
                    data-profile-editable
                    required>
                </div>
              </div>

              <div class="customer-profile-row">
                <label class="customer-profile-label" for="gender">Giới tính</label>
                <div class="customer-profile-control">
                  <select id="gender" name="gender" data-profile-editable required>
                    <option value="">Chọn giới tính</option>
                    <option value="1" selected>Nam</option>
                    <option value="0">Nữ</option>
                    <option value="2">Khác</option>
                  </select>
                </div>
              </div>

              <div class="customer-profile-row">
                <label class="customer-profile-label" for="province">Địa chỉ</label>
                <div class="customer-profile-control">
                  <div class="customer-address-grid">
                    <select id="province" name="province" data-province-select data-profile-editable required>
                      <option value="">Chọn tỉnh/thành phố</option>
                      <option value="TP. Hồ Chí Minh" selected>TP. Hồ Chí Minh</option>
                      <option value="Hà Nội">Hà Nội</option>
                      <option value="Lâm Đồng">Lâm Đồng</option>
                      <option value="Đà Nẵng">Đà Nẵng</option>
                    </select>
                    <select id="district" name="district" data-district-select data-profile-editable required disabled>
                      <option value="">Chọn quận/huyện</option>
                    </select>
                    <input
                      id="street_address"
                      name="street_address"
                      type="text"
                      placeholder="Tên đường và số nhà"
                      maxlength="120"
                      pattern="^[0-9A-Za-zÀ-ỹĐđ\\s./-]+$"
                      title="Tên đường và số nhà chỉ gồm chữ, số, khoảng trắng và ký tự . / -"
                      value="25 Nguyễn Đình Chiểu"
                      data-profile-editable
                      required>
                    <div class="customer-address-full">
                      <input id="full_address" name="address" type="hidden" value="25 Nguyễn Đình Chiểu, Quận 3, TP. Hồ Chí Minh" data-full-address>
                      <input class="customer-address-preview" type="text" value="25 Nguyễn Đình Chiểu, Quận 3, TP. Hồ Chí Minh" data-address-preview placeholder="Địa chỉ đầy đủ sẽ tự động hiển thị" readonly>
                    </div>
                  </div>
                </div>
              </div>

              <div class="customer-profile-actions">
                <button type="submit" class="customer-profile-submit" data-profile-submit>
                  Lưu cập nhật
                </button>
              </div>
            </form>
          </main>
        </div>
      </div>
    </section>

    @include('customer.partials.footer')
    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const addressData = {
          'TP. Hồ Chí Minh': ['Quận 1', 'Quận 3', 'Quận 7', 'Bình Thạnh', 'Thủ Đức'],
          'Hà Nội': ['Ba Đình', 'Hoàn Kiếm', 'Đống Đa', 'Cầu Giấy', 'Thanh Xuân'],
          'Lâm Đồng': ['Đà Lạt', 'Bảo Lộc', 'Đức Trọng', 'Lạc Dương', 'Di Linh'],
          'Đà Nẵng': ['Hải Châu', 'Thanh Khê', 'Sơn Trà', 'Ngũ Hành Sơn', 'Liên Chiểu'],
        };

        const form = document.querySelector('[data-customer-profile-form]');
        const province = document.querySelector('[data-province-select]');
        const district = document.querySelector('[data-district-select]');
        const fullAddress = document.querySelector('[data-full-address]');
        const preview = document.querySelector('[data-address-preview]');
        const streetAddress = document.getElementById('street_address');
        const submitButton = document.querySelector('[data-profile-submit]');
        const profileView = document.querySelector('[data-profile-view]');
        const editTrigger = document.querySelector('[data-profile-edit-trigger]');
        const editableFields = Array.from(document.querySelectorAll('[data-profile-editable]'));
        const selectedDistrict = form?.dataset.selectedDistrict || '';
        let profileLocked = form?.dataset.profileLocked === 'true';

        document.querySelectorAll('[data-text-filter="digits"]').forEach((input) => {
          input.addEventListener('input', () => {
            input.value = input.value.replace(/[^0-9]/g, '');
          });
        });

        const updateFullAddress = () => {
          const parts = [streetAddress.value, district.value, province.value]
            .map((part) => part.trim())
            .filter(Boolean);
          const value = parts.join(', ');
          fullAddress.value = value;
          preview.value = value;
        };

        const updateDistricts = () => {
          const items = addressData[province.value] || [];
          district.innerHTML = '<option value="">Chọn quận/huyện</option>';
          items.forEach((item) => {
            const option = document.createElement('option');
            option.value = item;
            option.textContent = item;
            district.appendChild(option);
          });
          if (selectedDistrict && items.includes(selectedDistrict)) {
            district.value = selectedDistrict;
          }
          district.disabled = profileLocked || items.length === 0;
          updateFullAddress();
        };

        const setEditing = (isEditing) => {
          profileLocked = !isEditing;
          form.dataset.profileLocked = profileLocked ? 'true' : 'false';
          form.hidden = !isEditing;
          if (profileView) {
            profileView.hidden = isEditing;
          }
          form.classList.toggle('is-locked', profileLocked);
          editableFields.forEach((field) => {
            field.disabled = profileLocked;
          });
          district.disabled = profileLocked || !(addressData[province.value] || []).length;
          submitButton.textContent = profileLocked ? 'Chỉnh sửa' : 'Lưu cập nhật';
        };

        editTrigger?.addEventListener('click', () => {
          setEditing(true);
          form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        province.addEventListener('change', updateDistricts);
        [district, streetAddress].forEach((element) => {
          element.addEventListener('input', updateFullAddress);
          element.addEventListener('change', updateFullAddress);
        });

        form.addEventListener('submit', (event) => {
          event.preventDefault();

          if (profileLocked) {
            setEditing(true);
            return;
          }

          updateFullAddress();
          if (!form.checkValidity()) {
            form.reportValidity();
          }
        });

        updateDistricts();
        setEditing(!form.hidden);
      });
    </script>
  </body>
</html>
