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
      $profile = session('customer_profile', []);
      $account = collect(config('hotel-management.modules.accounts.records', []))->firstWhere('Email', $user['email'] ?? '');
      $customer = $account
        ? collect(config('hotel-management.modules.customers.records', []))->firstWhere('MaTK', $account['MaTK'] ?? null)
        : null;
      $customerPoints = (int) ($profile['points'] ?? $customer['Diem'] ?? 0);
      $today = now()->toDateString();
      $profileEditing = $errors->isNotEmpty();
      $profileLocked = false;
      $profileDisplayName = $profile['display_name'] ?? $customer['TenKH'] ?? $user['name'] ?? '';
      $profilePhone = $profile['phone'] ?? $customer['SoDienThoai'] ?? '';
      $profileCccd = $profile['cccd'] ?? $customer['CCCD'] ?? '';
      $profileBirthday = $profile['birthday'] ?? $customer['NgaySinh'] ?? '';
      $profileGender = (string) ($profile['gender'] ?? $customer['GioiTinh'] ?? '');
      $profileGenderLabels = ['0' => 'Nữ', '1' => 'Nam', '2' => 'Khác'];
      $profileProvince = $profile['province'] ?? '';
      $profileDistrict = $profile['district'] ?? '';
      $streetAddress = old(
        'street_address',
        $profile['street_address'] ?? trim(($profile['house_number'] ?? '') . ' ' . ($profile['street'] ?? '')) ?: ($customer['DiaChi'] ?? '')
      );
      $profileAddress = $profile['address'] ?? $customer['DiaChi'] ?? '';
    @endphp

    <section class="customer-account-section">
      <div class="container">
        <div class="customer-account-shell">
          @include('customer.partials.account-sidebar', ['active' => 'profile'])

          <main class="customer-account-main">
            <div class="customer-account-heading">
              <div class="eyebrow">Thông tin cá nhân</div>
              <p>Thông tin khách hàng đã đăng ký với Peach Valley.</p>
            </div>

        <div class="customer-profile-view" data-profile-view @if($profileEditing) hidden @endif>
          <div class="customer-profile-view-top">
            <div>
              <div class="customer-profile-view-name">{{ $profileDisplayName ?: 'Khách hàng Peach Valley' }}</div>
              <p>{{ $user['email'] ?? '--' }}</p>
            </div>
            <button type="button" class="customer-profile-submit" data-profile-edit-trigger>Chỉnh sửa</button>
          </div>

          <div class="customer-profile-points-panel">
            <span class="customer-profile-points-icon ion-ios-star"></span>
            <div class="customer-profile-points">
              <strong>{{ number_format($customerPoints, 0, ',', '.') }}</strong>
              <span>điểm</span>
            </div>
            <p>Điểm có thể dùng để đổi hoặc áp dụng các ưu đãi trong kho khuyến mãi.</p>
          </div>

          <div class="customer-profile-view-grid">
            <div class="customer-profile-view-item"><span>Họ và tên</span>{{ $profileDisplayName ?: '--' }}</div>
            <div class="customer-profile-view-item"><span>Số điện thoại</span>{{ $profilePhone ?: '--' }}</div>
            <div class="customer-profile-view-item"><span>CCCD</span>{{ $profileCccd ?: '--' }}</div>
            <div class="customer-profile-view-item"><span>Ngày sinh</span>{{ $profileBirthday ? \Carbon\Carbon::parse($profileBirthday)->format('d/m/Y') : '--' }}</div>
            <div class="customer-profile-view-item"><span>Giới tính</span>{{ $profileGenderLabels[$profileGender] ?? '--' }}</div>
            <div class="customer-profile-view-item customer-profile-view-full"><span>Địa chỉ</span>{{ $profileAddress ?: $streetAddress ?: '--' }}</div>
          </div>
        </div>

        <form
          class="customer-profile-form{{ $profileLocked ? ' is-locked' : '' }}"
          method="POST"
          action="{{ route('customer.profile.update') }}"
          data-customer-profile-form
          data-profile-locked="{{ $profileLocked ? 'true' : 'false' }}"
          data-selected-district="{{ old('district', $profileDistrict) }}"
          @if(! $profileEditing) hidden @endif
        >
          {{ csrf_field() }}
          <div class="customer-profile-row">
            <label class="customer-profile-label" for="profile_email">Địa chỉ email</label>
            <div class="customer-profile-control">
              <div class="customer-email-line">
                <span>{{ $user['email'] ?? '--' }}</span>
              </div>
              <p class="customer-profile-help">Email này dùng để đăng nhập và nhận xác nhận đặt phòng.</p>
              <input type="hidden" id="profile_email" name="email" value="{{ $user['email'] ?? '' }}">
            </div>
          </div>

          <div class="customer-profile-row">
            <div class="customer-profile-label">Điểm tích lũy</div>
            <div class="customer-profile-control">
              <div class="customer-profile-points">
                <strong>{{ number_format($customerPoints, 0, ',', '.') }}</strong>
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
                pattern="^[A-Za-zÀ-ỹĐđ\s]+$"
                title="Tên chỉ gồm chữ cái và khoảng trắng."
                value="{{ old('display_name', $profileDisplayName) }}"
                data-profile-editable
                @disabled($profileLocked)
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
                value="{{ old('phone', $profilePhone) }}"
                data-profile-editable
                @disabled($profileLocked)
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
                value="{{ old('cccd', $profileCccd) }}"
                data-profile-editable
                @disabled($profileLocked)
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
                max="{{ $today }}"
                value="{{ old('birthday', $profileBirthday) }}"
                data-profile-editable
                @disabled($profileLocked)
                required>
            </div>
          </div>

          <div class="customer-profile-row">
            <label class="customer-profile-label" for="gender">Giới tính</label>
            <div class="customer-profile-control">
              <select id="gender" name="gender" data-profile-editable @disabled($profileLocked) required>
                <option value="">Chọn giới tính</option>
                <option value="1" @selected(old('gender', $profileGender) === '1')>Nam</option>
                <option value="0" @selected(old('gender', $profileGender) === '0')>Nữ</option>
                <option value="2" @selected(old('gender', $profileGender) === '2')>Khác</option>
              </select>
            </div>
          </div>

          <div class="customer-profile-row">
            <label class="customer-profile-label" for="province">Địa chỉ</label>
            <div class="customer-profile-control">
              <div class="customer-address-grid">
                <select id="province" name="province" data-province-select data-profile-editable @disabled($profileLocked) required>
                  <option value="">Chọn tỉnh/thành phố</option>
                  <option value="TP. Hồ Chí Minh" @selected(old('province', $profileProvince) === 'TP. Hồ Chí Minh')>TP. Hồ Chí Minh</option>
                  <option value="Hà Nội" @selected(old('province', $profileProvince) === 'Hà Nội')>Hà Nội</option>
                  <option value="Lâm Đồng" @selected(old('province', $profileProvince) === 'Lâm Đồng')>Lâm Đồng</option>
                  <option value="Đà Nẵng" @selected(old('province', $profileProvince) === 'Đà Nẵng')>Đà Nẵng</option>
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
                  pattern="^[0-9A-Za-zÀ-ỹĐđ\s./-]+$"
                  title="Tên đường và số nhà chỉ gồm chữ, số, khoảng trắng và ký tự . / -"
                  value="{{ $streetAddress }}"
                  data-profile-editable
                  @disabled($profileLocked)
                  required>
                <div class="customer-address-full">
                  <input id="full_address" name="address" type="hidden" value="{{ old('address', $profileAddress) }}" data-full-address>
                  <input class="customer-address-preview" type="text" value="{{ old('address', $profileAddress) }}" data-address-preview placeholder="Địa chỉ đầy đủ sẽ tự động hiển thị" readonly>
                </div>
              </div>
            </div>
          </div>

          <div class="customer-profile-actions">
            <button type="submit" class="customer-profile-submit" data-profile-submit>
              {{ $profileLocked ? 'Chỉnh sửa' : 'Lưu cập nhật' }}
            </button>
          </div>
          <p class="customer-profile-save-note{{ session('profile_saved') ? ' is-visible' : '' }}" data-profile-save-note>{{ session('profile_saved') ?? 'Thông tin đã được kiểm tra hợp lệ.' }}</p>
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
        const saveNote = document.querySelector('[data-profile-save-note]');
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
          saveNote.classList.remove('is-visible');
          form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        province.addEventListener('change', updateDistricts);
        [district, streetAddress].forEach((element) => {
          element.addEventListener('input', updateFullAddress);
          element.addEventListener('change', updateFullAddress);
        });

        form.addEventListener('submit', (event) => {
          if (profileLocked) {
            event.preventDefault();
            setEditing(true);
            saveNote.classList.remove('is-visible');
            return;
          }

          updateFullAddress();
          if (!form.checkValidity()) {
            event.preventDefault();
            saveNote.classList.remove('is-visible');
            form.reportValidity();
          }
        });

        updateDistricts();
        setEditing(!form.hidden);
      });
    </script>
  </body>
</html>
