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
                  <input id="address" name="address" type="text" maxlength="255" value="{{ $address }}" data-profile-editable>
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

        document.querySelectorAll('[data-text-filter="digits"]').forEach((input) => {
          input.addEventListener('input', () => {
            input.value = input.value.replace(/[^0-9]/g, '');
          });
        });

        const setEditing = (isEditing) => {
          form.hidden = !isEditing;
          profileView.hidden = isEditing;
          form.dataset.profileLocked = isEditing ? 'false' : 'true';
          editableFields.forEach((field) => {
            field.disabled = !isEditing;
          });
          submitButton.textContent = isEditing ? 'Lưu cập nhật' : 'Chỉnh sửa';
        };

        editTrigger?.addEventListener('click', () => {
          setEditing(true);
          form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        form.addEventListener('submit', (event) => {
          event.preventDefault();

          if (!form.checkValidity()) {
            form.reportValidity();
          }
        });

        setEditing(false);
      });
    </script>
  </body>
</html>
