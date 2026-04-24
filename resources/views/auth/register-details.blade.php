<x-guest-layout>
   @php
      $addressOptions = [
         'TP.HCM' => ['Quan 1', 'Quan 3', 'Quan 7', 'Quan 10', 'Binh Thanh', 'Go Vap', 'Thu Duc'],
         'Ha Noi' => ['Ba Dinh', 'Hoan Kiem', 'Dong Da', 'Cau Giay', 'Thanh Xuan', 'Tay Ho'],
         'Da Nang' => ['Hai Chau', 'Thanh Khe', 'Son Tra', 'Ngu Hanh Son', 'Lien Chieu', 'Cam Le'],
         'Lam Dong' => ['Da Lat', 'Bao Loc', 'Duc Trong', 'Lac Duong', 'Di Linh'],
         'Can Tho' => ['Ninh Kieu', 'Binh Thuy', 'Cai Rang', 'O Mon', 'Thot Not'],
      ];
      $today = now()->toDateString();
      $selectedProvince = old('province', '');
      $selectedDistrict = old('district', '');
   @endphp

   <section class="login-content">
      <style>
         .auth-panel {
            min-height: 100vh;
         }

         .auth-side-visual {
            position: relative;
            background: #0f172a;
         }

         .auth-side-visual::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.08) 0%, rgba(15, 23, 42, 0.42) 100%);
            pointer-events: none;
         }

         .auth-side-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
         }

         .register-detail-panel {
            max-height: 100vh;
            overflow-y: auto;
            padding: 42px 36px;
         }

         .register-detail-card {
            width: min(100%, 680px);
            margin: 0 auto;
         }

         .register-detail-card .card-body {
            width: 100%;
            padding-left: 28px;
            padding-right: 28px;
         }

         .register-detail-title {
            color: #111827;
            font-size: 24px;
            font-weight: 500;
            line-height: 1.25;
         }

         .register-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px 20px;
         }

         .register-detail-field.full {
            grid-column: 1 / -1;
         }

         .register-detail-field label {
            display: block;
            margin-bottom: 7px;
            color: rgba(73, 18, 15, 0.78);
            font-size: 14px;
            font-weight: 400;
         }

         .register-detail-field input,
         .register-detail-field select {
            width: 100%;
            min-height: 6px;
            border: 0.1px solid rgba(111, 29, 1, 0.18);
            border-radius: 4px;
            background: #fff;
            color: #64748b;
            padding: 10px 14px;
            font-size: 15px;
            transition:
               border-color 0.18s ease,
               box-shadow 0.18s ease;
         }

         .register-detail-field input:focus,
         .register-detail-field select:focus {
            border-color: #cbd5e1;
            outline: none;
            box-shadow: 0 0 0 0.18rem rgba(140, 74, 52, 0.12);
         }

         .register-detail-error {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            line-height: 1.45;
            color: #dc2626;
            font-weight: 700;
         }

         .register-detail-submit {
            width: min(100%, 240px);
            min-height: 46px;
            border: 0;
            border-color: #49120f;
            border-radius: 8px;
            background: linear-gradient(90deg, #49120f, #854023);
            color: #fff;
            font-weight: 400;
            cursor: pointer;
         }

         .register-detail-submit:hover,
         .register-detail-submit:focus {
            background: #6f3a28;
            color: #fff;
         }

         @media (max-width: 767.98px) {
            .auth-panel {
               min-height: auto;
            }

            .register-detail-panel {
               max-height: none;
               padding: 26px 16px;
            }

            .register-detail-grid {
               grid-template-columns: 1fr;
            }
         }
      </style>

      <div class="row m-0 align-items-stretch bg-white auth-panel">
         <div class="col-md-7 register-detail-panel">
            <div class="register-detail-card">
               <div class="card card-transparent auth-card shadow-none d-flex justify-content-center mb-0">
                  <div class="card-body">
                     <a href="{{ route('dashboard') }}" class="navbar-brand d-flex align-items-center mb-3">
                        <img src="{{ asset('images/logo_hotel.png') }}" alt="Peach Valley Hotel" class="auth-brand-logo">
                     </a>

                     <h2 class="register-detail-title mb-4 text-center">Hoàn tất thông tin đăng kí</h2>

                     <form
                        data-ui-only-form
                        data-register-detail-form
                        data-address-options='@json($addressOptions)'
                        data-selected-district="{{ $selectedDistrict }}"
                     >
                        <div class="register-detail-grid">
                           <div class="register-detail-field">
                              <label for="full_name">Họ và tên <span class="text-danger">*</span></label>
                              <input
                                 id="full_name"
                                 name="full_name"
                                 type="text"
                                 value="{{ old('full_name') }}"
                                 placeholder="Nguyen Van An"
                                 minlength="2"
                                 maxlength="60"
                                 pattern="^[A-Za-z\s]+$"
                                 title="Ho va ten chi gom chu cai va khoang trang."
                                 data-text-filter="letters"
                                 required>
                              @error('full_name')
                                 <span class="register-detail-error">{{ $message }}</span>
                              @enderror
                           </div>

                           <div class="register-detail-field">
                              <label for="gender">Giới tính <span class="text-danger">*</span></label>
                              <select id="gender" name="gender" required>
                                 <option value="">Chọn giới tính</option>
                                 <option value="1" @selected(old('gender') === '1')>Nam</option>
                                 <option value="0" @selected(old('gender') === '0')>Nữ</option>
                                 <option value="2" @selected(old('gender') === '2')>Khác</option>
                              </select>
                              @error('gender')
                                 <span class="register-detail-error">{{ $message }}</span>
                              @enderror
                           </div>

                           <div class="register-detail-field">
                              <label for="phone">Số điện thoại <span class="text-danger">*</span></label>
                              <input
                                 id="phone"
                                 name="phone"
                                 type="tel"
                                 inputmode="numeric"
                                 value="{{ old('phone') }}"
                                 placeholder="0901234567"
                                 maxlength="10"
                                 pattern="^0[0-9]{9}$"
                                 title="So dien thoai gom 10 chu so va bat dau bang 0."
                                 data-text-filter="digits"
                                 required>
                              @error('phone')
                                 <span class="register-detail-error">{{ $message }}</span>
                              @enderror
                           </div>

                           <div class="register-detail-field">
                              <label for="cccd">CCCD <span class="text-danger">*</span></label>
                              <input
                                 id="cccd"
                                 name="cccd"
                                 type="text"
                                 inputmode="numeric"
                                 value="{{ old('cccd') }}"
                                 placeholder="012345678901"
                                 maxlength="12"
                                 pattern="^[0-9]{12}$"
                                 title="CCCD gom dung 12 chu so."
                                 data-text-filter="digits"
                                 required>
                              @error('cccd')
                                 <span class="register-detail-error">{{ $message }}</span>
                              @enderror
                           </div>

                           <div class="register-detail-field">
                              <label for="birthday">Ngày sinh <span class="text-danger">*</span></label>
                              <input
                                 id="birthday"
                                 name="birthday"
                                 type="date"
                                 value="{{ old('birthday') }}"
                                 max="{{ $today }}"
                                 required>
                              @error('birthday')
                                 <span class="register-detail-error">{{ $message }}</span>
                              @enderror
                           </div>

                           <div class="register-detail-field">
                              <label for="province">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                              <select id="province" name="province" data-province-select required>
                                 <option value="">Chọn tỉnh/thành phố</option>
                                 @foreach ($addressOptions as $provinceName => $districts)
                                    <option value="{{ $provinceName }}" @selected($selectedProvince === $provinceName)>{{ $provinceName }}</option>
                                 @endforeach
                              </select>
                              @error('province')
                                 <span class="register-detail-error">{{ $message }}</span>
                              @enderror
                           </div>

                           <div class="register-detail-field">
                              <label for="district">Quận / Huyện <span class="text-danger">*</span></label>
                              <select id="district" name="district" data-district-select required disabled>
                                 <option value="">Chọn quận/huyện</option>
                              </select>
                              @error('district')
                                 <span class="register-detail-error">{{ $message }}</span>
                              @enderror
                           </div>

                           <div class="register-detail-field full">
                              <label for="address_line">Số nhà và tên đường <span class="text-danger">*</span></label>
                              <input
                                 id="address_line"
                                 name="address_line"
                                 type="text"
                                 value="{{ old('address_line') }}"
                                 placeholder="26K duong Yersin"
                                 minlength="4"
                                 maxlength="120"
                                 pattern="^[0-9A-Za-z\s./,-]+$"
                                 title="So nha va ten duong chi gom chu, so, khoang trang va ky tu . / , -"
                                 data-text-filter="address"
                                 required>
                              <input id="address" name="address" type="hidden" value="{{ old('address') }}" data-full-address>
                              @error('address_line')
                                 <span class="register-detail-error">{{ $message }}</span>
                              @enderror
                           </div>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                           <button type="submit" class="register-detail-submit">Hoàn tất đăng kí</button>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>

         <div class="col-md-5 d-md-block d-none p-0 mt-n1 vh-100 overflow-hidden auth-side-visual">
            <img src="{{ asset('images/auth/khachsan.jpg') }}" class="auth-side-image animated-scaleX" alt="Khach san Peach Valley">
         </div>
      </div>

      <script>
         document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('[data-register-detail-form]');

            if (!form) {
               return;
            }

            const addressOptions = JSON.parse(form.dataset.addressOptions || '{}');
            let selectedDistrict = form.dataset.selectedDistrict || '';
            const province = form.querySelector('[data-province-select]');
            const district = form.querySelector('[data-district-select]');
            const addressLine = document.getElementById('address_line');
            const fullAddress = form.querySelector('[data-full-address]');

            const filters = {
               digits: /[^0-9]/g,
               letters: /[^A-Za-z\s]/g,
               address: /[^0-9A-Za-z\s./,-]/g,
            };

            form.querySelectorAll('[data-text-filter]').forEach((input) => {
               input.addEventListener('input', () => {
                  const filter = filters[input.dataset.textFilter];

                  if (filter) {
                     input.value = input.value.replace(filter, '');
                  }
               });
            });

            const syncFullAddress = () => {
               const parts = [
                  addressLine.value.trim(),
                  district.value.trim(),
                  province.value.trim(),
               ].filter(Boolean);

               fullAddress.value = parts.join(', ');
            };

            const renderDistricts = () => {
               const items = addressOptions[province.value] || [];
               district.innerHTML = '<option value="">Chon quan/huyen</option>';

               items.forEach((item) => {
                  const option = document.createElement('option');
                  option.value = item;
                  option.textContent = item;
                  district.appendChild(option);
               });

               district.disabled = items.length === 0;

               if (selectedDistrict && items.includes(selectedDistrict)) {
                  district.value = selectedDistrict;
               }

               syncFullAddress();
            };

            province.addEventListener('change', () => {
               selectedDistrict = '';
               renderDistricts();
            });

            [district, addressLine].forEach((field) => {
               field.addEventListener('input', syncFullAddress);
               field.addEventListener('change', syncFullAddress);
            });

            district.addEventListener('change', () => {
               selectedDistrict = district.value;
            });

            form.addEventListener('submit', (event) => {
               event.preventDefault();
               syncFullAddress();

               if (!form.checkValidity()) {
                  form.reportValidity();
               }
            });

            renderDistricts();
         });
      </script>
   </section>
</x-guest-layout>
