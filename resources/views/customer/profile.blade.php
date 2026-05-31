@php
  $account = $profileAccount ?? session('auth_account', []);
  $customer = $profileCustomer ?? null;
  $email = $customer?->taiKhoan?->Email ?? ($account['Email'] ?? '');
  $name = $customer?->TenKH ?? ($account['Ten'] ?? 'Khách hàng');
  $phone = $customer?->SoDienThoai ?? '';
  $cccd = $customer?->CCCD ?? '';
  $birthdayValue = $customer?->NgaySinh ? \Illuminate\Support\Carbon::parse($customer->NgaySinh)->toDateString() : '';
  $birthdayDisplay = $customer?->NgaySinh ? \Illuminate\Support\Carbon::parse($customer->NgaySinh)->format('d/m/Y') : '--';
  $genderValue = $customer?->GioiTinh;
  $genderLabel = match ((string) $genderValue) {
    '0' => 'Nữ',
    '1' => 'Nam',
    '2' => 'Khác',
    default => '--',
  };
  $address = $customer?->DiaChi ?? '';
  $provinces = $provinces ?? [];
  $communes = $communes ?? [];
  $addressParts = collect(explode(',', (string) $address))
    ->map(fn ($part) => trim($part))
    ->filter()
    ->values();
  $addressLineValue = $addressParts->get(0, '');
  $districtNameValue = $addressParts->get(1, '');
  $provinceNameValue = $addressParts->get(2, '');
  $selectedProvinceCode = collect($provinces)->firstWhere('name', $provinceNameValue)['code'] ?? '';
  $selectedDistrictCode = collect($communes[$selectedProvinceCode] ?? [])->firstWhere('name', $districtNameValue)['code'] ?? '';
  $points = (int) ($customer?->DIEM ?? 0);
@endphp

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
                  <div class="customer-profile-view-name">{{ $name }}</div>
                  <p>{{ $email ?: '--' }}</p>
                </div>
                <button type="button" class="customer-profile-submit" data-profile-edit-trigger>Chỉnh sửa</button>
              </div>

              <div class="customer-profile-points-panel">
                <span class="customer-profile-points-icon ion-ios-star"></span>
                <div class="customer-profile-points">
                  <strong>{{ number_format($points, 0, ',', '.') }}</strong>
                  <span>điểm</span>
                </div>
                <p>Điểm có thể dùng để đổi hoặc áp dụng các ưu đãi trong kho khuyến mãi.</p>
              </div>

              <div class="customer-profile-view-grid">
                <div class="customer-profile-view-item"><span>Họ và tên</span>{{ $name }}</div>
                <div class="customer-profile-view-item"><span>Email</span>{{ $email ?: '--' }}</div>
                <div class="customer-profile-view-item"><span>Số điện thoại</span>{{ $phone ?: '--' }}</div>
                <div class="customer-profile-view-item"><span>CCCD</span>{{ $cccd ?: '--' }}</div>
                <div class="customer-profile-view-item"><span>Ngày sinh</span>{{ $birthdayDisplay }}</div>
                <div class="customer-profile-view-item"><span>Giới tính</span>{{ $genderLabel }}</div>
                <div class="customer-profile-view-item customer-profile-view-full"><span>Địa chỉ</span>{{ $address ?: '--' }}</div>
              </div>
            </div>

            <form
              class="customer-profile-form"
              data-customer-profile-form
              data-profile-locked="true"
              data-customer-id="{{ $customer?->MaKH ?? ($account['MaKH'] ?? '') }}"
              data-update-url-template="{{ url('/api/khach-hang/__CUSTOMER_ID__') }}"
              data-communes='@json($communes)'
              hidden
            >
              <div class="customer-profile-row">
                <label class="customer-profile-label" for="profile_email">Địa chỉ email</label>
                <div class="customer-profile-control">
                  <div class="customer-email-line">
                    <span>{{ $email ?: '--' }}</span>
                  </div>
                  <p class="customer-profile-help">Email này dùng để đăng nhập và nhận xác nhận đặt phòng.</p>
                  <input type="hidden" id="profile_email" name="email" value="{{ $email }}">
                </div>
              </div>

              <div class="customer-profile-row">
                <div class="customer-profile-label">Điểm tích lũy</div>
                <div class="customer-profile-control">
                  <div class="customer-profile-points">
                    <strong>{{ number_format($points, 0, ',', '.') }}</strong>
                    <span>điểm tích lũy</span>
                  </div>
                  <p class="customer-profile-help">Điểm có thể dùng để đổi hoặc áp dụng các ưu đãi trong kho khuyến mãi.</p>
                </div>
              </div>

              <div class="customer-profile-row">
                <label class="customer-profile-label" for="display_name">Họ và tên</label>
                <div class="customer-profile-control">
                  <input id="display_name" name="display_name" type="text" value="{{ $name }}" data-profile-editable required>
                </div>
              </div>

              <div class="customer-profile-row">
                <label class="customer-profile-label" for="phone">Số điện thoại</label>
                <div class="customer-profile-control">
                  <input id="phone" name="phone" type="tel" inputmode="numeric" maxlength="10" pattern="^0[0-9]{9}$" value="{{ $phone }}" data-text-filter="digits" data-profile-editable required>
                  <p class="customer-profile-help">Khách sạn sẽ liên hệ qua số này nếu cần xác nhận đặt phòng.</p>
                </div>
              </div>

              <div class="customer-profile-row">
                <label class="customer-profile-label" for="cccd">CCCD</label>
                <div class="customer-profile-control">
                  <input id="cccd" name="cccd" type="text" inputmode="numeric" maxlength="12" pattern="^[0-9]{12}$" value="{{ $cccd }}" data-text-filter="digits" data-profile-editable>
                </div>
              </div>

              <div class="customer-profile-row">
                <label class="customer-profile-label" for="birthday">Ngày sinh</label>
                <div class="customer-profile-control">
                  <input id="birthday" name="birthday" type="date" max="{{ now()->toDateString() }}" value="{{ $birthdayValue }}" data-profile-editable>
                </div>
              </div>

              <div class="customer-profile-row">
                <label class="customer-profile-label" for="gender">Giới tính</label>
                <div class="customer-profile-control">
                  <select id="gender" name="gender" data-profile-editable>
                    <option value="">Chọn giới tính</option>
                    <option value="1" @selected((string) $genderValue === '1')>Nam</option>
                    <option value="0" @selected((string) $genderValue === '0')>Nữ</option>
                    <option value="2" @selected((string) $genderValue === '2')>Khác</option>
                  </select>
                </div>
              </div>

              <div class="customer-profile-row">
                <label class="customer-profile-label" for="address">Địa chỉ</label>
                <div class="customer-profile-control">
                  <select id="profile_province" name="province" data-profile-province data-profile-editable>
                    <option value="">Chá»n tá»‰nh/thÃ nh phá»‘</option>
                    @forelse ($provinces as $province)
                      <option value="{{ $province['code'] }}" @selected($selectedProvinceCode === $province['code'])>{{ $province['name'] }}</option>
                    @empty
                      <option value="" disabled>KhÃ´ng thá»ƒ táº£i danh sÃ¡ch tá»‰nh/thÃ nh phá»‘</option>
                    @endforelse
                  </select>
                </div>
              </div>

              <div class="customer-profile-row">
                <label class="customer-profile-label" for="profile_district">PhÆ°á»ng/XÃ£</label>
                <div class="customer-profile-control">
                  <select id="profile_district" name="district" data-profile-district data-selected-district="{{ $selectedDistrictCode }}" data-profile-editable @disabled(!$selectedProvinceCode)>
                    <option value="">Chá»n phÆ°á»ng/xÃ£</option>
                  </select>
                </div>
              </div>

              <div class="customer-profile-row">
                <label class="customer-profile-label" for="address_line">Sá»‘ nhÃ , Ä‘Æ°á»ng</label>
                <div class="customer-profile-control">
                  <input id="address_line" name="address_line" type="text" maxlength="120" value="{{ $addressLineValue }}" data-text-filter="address" data-profile-address-line data-profile-editable>
                  <input id="address" name="address" type="hidden" maxlength="255" value="{{ $address }}" data-profile-address>
                </div>
              </div>

              <div class="customer-profile-actions">
                <button type="submit" class="customer-profile-submit" data-profile-submit>Lưu cập nhật</button>
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
        const form = document.querySelector('[data-customer-profile-form]');
        const profileView = document.querySelector('[data-profile-view]');
        const editTrigger = document.querySelector('[data-profile-edit-trigger]');
        const editableFields = Array.from(document.querySelectorAll('[data-profile-editable]'));
        const submitButton = document.querySelector('[data-profile-submit]');
        const customerId = form?.dataset.customerId || '';
        const updateUrlTemplate = form?.dataset.updateUrlTemplate || '';
        const communesData = JSON.parse(form?.dataset.communes || '{}');
        const provinceSelect = form?.querySelector('[data-profile-province]');
        const districtSelect = form?.querySelector('[data-profile-district]');
        const addressLineInput = form?.querySelector('[data-profile-address-line]');
        const addressInput = form?.querySelector('[data-profile-address]');
        let selectedDistrict = districtSelect?.dataset.selectedDistrict || '';

        const addressLabel = document.querySelector('label[for="address"]');
        if (addressLabel) {
          addressLabel.htmlFor = 'profile_province';
          addressLabel.textContent = 'Tỉnh/Thành phố';
        }
        const districtLabel = document.querySelector('label[for="profile_district"]');
        if (districtLabel) {
          districtLabel.textContent = 'Phường/Xã';
        }
        const addressLineLabel = document.querySelector('label[for="address_line"]');
        if (addressLineLabel) {
          addressLineLabel.textContent = 'Số nhà, đường';
        }
        if (provinceSelect?.options[0]) {
          provinceSelect.options[0].textContent = 'Chọn tỉnh/thành phố';
        }

        document.querySelectorAll('[data-text-filter="digits"]').forEach((input) => {
          input.addEventListener('input', () => {
            input.value = input.value.replace(/[^0-9]/g, '');
          });
        });

        document.querySelectorAll('[data-text-filter="address"]').forEach((input) => {
          input.addEventListener('input', () => {
            input.value = input.value.replace(/[^0-9A-Za-zÀ-ỿ\s./,-]/g, '');
          });
        });

        const syncAddress = () => {
          if (!addressInput || !provinceSelect || !districtSelect || !addressLineInput) {
            return;
          }

          const provinceName = provinceSelect.value
            ? provinceSelect.options[provinceSelect.selectedIndex]?.textContent.trim()
            : '';
          const districtName = districtSelect.value
            ? districtSelect.options[districtSelect.selectedIndex]?.textContent.trim()
            : '';
          const parts = [
            addressLineInput.value.trim(),
            districtName,
            provinceName,
          ].filter(Boolean);

          addressInput.value = parts.join(', ');
        };

        const renderDistricts = () => {
          if (!provinceSelect || !districtSelect) {
            return;
          }

          const provinceCode = provinceSelect.value;
          const items = provinceCode ? (communesData[provinceCode] || []) : [];

          districtSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
          items.forEach((item) => {
            const option = document.createElement('option');
            option.value = item.code;
            option.textContent = item.name;
            districtSelect.append(option);
          });

          districtSelect.disabled = items.length === 0 || form.dataset.profileLocked === 'true';

          if (selectedDistrict && items.some((item) => String(item.code) === String(selectedDistrict))) {
            districtSelect.value = selectedDistrict;
          }

          syncAddress();
        };

        const setEditing = (isEditing) => {
          form.hidden = !isEditing;
          profileView.hidden = isEditing;
          form.dataset.profileLocked = isEditing ? 'false' : 'true';
          editableFields.forEach((field) => {
            field.disabled = !isEditing;
          });
          renderDistricts();
          submitButton.textContent = isEditing ? 'Lưu cập nhật' : 'Chỉnh sửa';
        };

        editTrigger?.addEventListener('click', () => {
          setEditing(true);
          form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        provinceSelect?.addEventListener('change', () => {
          selectedDistrict = '';
          renderDistricts();
        });

        districtSelect?.addEventListener('change', () => {
          selectedDistrict = districtSelect.value;
          syncAddress();
        });

        addressLineInput?.addEventListener('input', syncAddress);
        addressLineInput?.addEventListener('change', syncAddress);

        form.addEventListener('submit', async (event) => {
          event.preventDefault();

          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }

          syncAddress();

          if (!customerId || !updateUrlTemplate) {
            alert('Không tìm thấy thông tin khách hàng để cập nhật.');
            return;
          }

          const payload = {
            TenKH: document.getElementById('display_name')?.value.trim() || '',
            SoDienThoai: document.getElementById('phone')?.value.trim() || '',
            CCCD: document.getElementById('cccd')?.value.trim() || null,
            NgaySinh: document.getElementById('birthday')?.value || null,
            GioiTinh: document.getElementById('gender')?.value || null,
            DiaChi: addressInput?.value.trim() || null,
          };

          submitButton.disabled = true;
          submitButton.textContent = 'Đang lưu...';

          try {
            const response = await fetch(updateUrlTemplate.replace('__CUSTOMER_ID__', encodeURIComponent(customerId)), {
              method: 'PUT',
              headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
              },
              body: JSON.stringify(payload),
            });
            const result = await response.json().catch(() => ({}));

            if (!response.ok) {
              const errors = result.errors || {};
              const firstError = Object.values(errors)[0]?.[0] || result.message || 'Không thể cập nhật thông tin.';
              throw new Error(firstError);
            }

            window.location.reload();
          } catch (error) {
            alert(error.message || 'Không thể cập nhật thông tin.');
            submitButton.disabled = false;
            submitButton.textContent = 'Lưu cập nhật';
          }
        });

        renderDistricts();
        setEditing(false);
      });
    </script>
  </body>
</html>
