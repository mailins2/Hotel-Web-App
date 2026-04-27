<x-guest-layout>
   @php
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
                        action="{{ route('register.step2') }}"
                        method="POST"
                        novalidate
                        data-ui-only-form
                        data-register-detail-form
                        data-communes='@json($communes)'
                        data-selected-district="{{ $selectedDistrict }}"
                     >
                        @csrf
                        <div class="register-detail-grid">
                           <div class="register-detail-field">
                              <label for="full_name">Họ và tên <span class="text-danger">*</span></label>
                              <input
                                 id="full_name"
                                 name="full_name"
                                 type="text"
                                 value="{{ old('full_name') }}"
                                 class="form-control @error('full_name') is-invalid @enderror"
                                 placeholder="Nguyễn Văn An"
                                 minlength="2"
                                 maxlength="60"
                                 pattern="^[A-Za-zÀ-ỿ\s]+$"
                                 title="Họ tên chỉ gồm chữ cái và khoảng trắng."
                                 data-text-filter="letters"
                                 required>
                              <span id="full_name-error" class="register-detail-error">@error('full_name') {{ $message }} @enderror</span>
                           </div>

                           <div class="register-detail-field">
                              <label for="gender">Giới tính <span class="text-danger">*</span></label>
                              <select id="gender" name="gender" class="form-control @error('gender') is-invalid @enderror" required>
                                 <option value="">Chọn giới tính</option>
                                 <option value="1" @selected(old('gender') === '1')>Nam</option>
                                 <option value="0" @selected(old('gender') === '0')>Nữ</option>
                                 <option value="2" @selected(old('gender') === '2')>Khác</option>
                              </select>
                              <span id="gender-error" class="register-detail-error">@error('gender') {{ $message }} @enderror</span>
                           </div>

                           <div class="register-detail-field">
                              <label for="phone">Số điện thoại <span class="text-danger">*</span></label>
                              <input
                                 id="phone"
                                 name="phone"
                                 type="tel"
                                 inputmode="numeric"
                                 value="{{ old('phone') }}"
                                 class="form-control @error('phone') is-invalid @enderror"
                                 placeholder="0901234567"
                                 maxlength="10"
                                 pattern="^0[0-9]{9}$"
                                 title="So dien thoai gom 10 chu so va bat dau bang 0."
                                 data-text-filter="digits"
                                 required>
                              <span id="phone-error" class="register-detail-error">@error('phone') {{ $message }} @enderror</span>
                           </div>

                           <div class="register-detail-field">
                              <label for="cccd">CCCD <span class="text-danger">*</span></label>
                              <input
                                 id="cccd"
                                 name="cccd"
                                 type="text"
                                 inputmode="numeric"
                                 value="{{ old('cccd') }}"
                                 class="form-control @error('cccd') is-invalid @enderror"
                                 placeholder="012345678901"
                                 maxlength="12"
                                 pattern="^[0-9]{12}$"
                                 title="CCCD gom dung 12 chu so."
                                 data-text-filter="digits"
                                 required>
                              <span id="cccd-error" class="register-detail-error">@error('cccd') {{ $message }} @enderror</span>
                           </div>

                           <div class="register-detail-field">
                              <label for="birthday">Ngày sinh <span class="text-danger">*</span></label>
                              <div style="position: relative; width: 100%;">
                                 <input
                                    id="birthday"
                                    name="birthday"
                                    type="date"
                                    value="{{ old('birthday') }}"
                                    max="{{ $today }}"
                                    style="position: absolute; opacity: 0; width: 1px; height: 1px; right: 14px; top: 50%; pointer-events: none;"
                                    required>
                                 <input
                                    id="birthday-display"
                                    name="birthday_display"
                                    type="text"
                                    inputmode="numeric"
                                    placeholder="dd/mm/yyyy"
                                    value="{{ old('birthday_display') }}"
                                    class="form-control @error('birthday') is-invalid @enderror"
                                    style="width: 100%; min-height: 38px; border: 0.1px solid rgba(111, 29, 1, 0.18); border-radius: 4px; background: #fff; color: #64748b; padding: 10px 14px; padding-right: 44px; font-size: 15px; box-sizing: border-box;"
                                 >
                                 <button
                                    type="button"
                                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 4px; color: #64748b; display: flex; align-items: center; justify-content: center;"
                                    onclick="document.getElementById('birthday').click();"
                                 >
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                       <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                       <line x1="16" y1="2" x2="16" y2="6"></line>
                                       <line x1="8" y1="2" x2="8" y2="6"></line>
                                       <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                 </button>
                              </div>
                              <span id="birthday-error" class="register-detail-error">@error('birthday') {{ $message }} @enderror</span>
                           </div>

                           <div class="register-detail-field">
                              <label for="province">Tỉnh / Thành phố</label>
                              <select id="province" name="province" class="form-control @error('province') is-invalid @enderror" data-province-select>
                                 <option value="">Chọn tỉnh/thành phố</option>
                                 @forelse ($provinces as $province)
                                    <option value="{{ $province['code'] }}" @selected($selectedProvince === $province['code'])>{{ $province['name'] }}</option>
                                 @empty
                                    <option disabled>Không thể tải danh sách tỉnh/thành phố</option>
                                 @endforelse
                              </select>
                              <span id="province-error" class="register-detail-error">@error('province') {{ $message }} @enderror</span>
                           </div>

                           <div class="register-detail-field">
                              <label for="district">Phường / Xã</label>
                              <select id="district" name="district" class="form-control @error('district') is-invalid @enderror" data-district-select>
                                 <option value="">Chọn phường/xã</option>
                              </select>
                              <span id="district-error" class="register-detail-error"></span>
                           </div>

                           <div class="register-detail-field full">
                              <label for="address_line">Số nhà và tên đường</label>
                              <input
                                 id="address_line"
                                 name="address_line"
                                 type="text"
                                 value="{{ old('address_line') }}"
                                 class="form-control @error('address_line') is-invalid @enderror"
                                 placeholder="26 Đường Yersin"
                                 minlength="4"
                                 maxlength="120"
                                 pattern="^[0-9A-Za-zÀ-ỿ\s./,-]+$"
                                 title="Số nhà và tên đường gồm chữ, số, khoảng trắng và ký tự . / , -"
                                 data-text-filter="address"
                                 >
                              <input id="address" name="address" type="hidden" value="{{ old('address') }}" data-full-address>
                              <span id="address_line-error" class="register-detail-error"></span>
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

      @if ($errors->any())
         <div class="modal fade" id="authMessageModal" tabindex="-1" aria-labelledby="authMessageModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
               <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="authMessageModalTitle">Không thể đăng ký</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                  </div>
                  <div class="modal-body">
                     {{ $errors->first() }}
                  </div>
                  <div class="modal-footer">
                     <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đóng</button>
                  </div>
               </div>
            </div>
         </div>
      @endif

      <script>
         document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('[data-register-detail-form]');

            if (!form) {
               return;
            }

            let selectedDistrict = form.dataset.selectedDistrict || '';
            const province = form.querySelector('[data-province-select]');
            const district = form.querySelector('[data-district-select]');
            const addressLine = document.getElementById('address_line');
            const fullAddress = form.querySelector('[data-full-address]');
            const communesData = JSON.parse(form.dataset.communes || '{}');
            const fullNameInput = document.getElementById('full_name');
            const genderInput = document.getElementById('gender');
            const phoneInput = document.getElementById('phone');
            const cccdInput = document.getElementById('cccd');
            const provinceInput = province;
            const districtInput = district;
            const addressLineInput = addressLine;

            const filters = {
               digits: /[^0-9]/g,
               letters: /[^A-Za-zÀ-ỿ\s]/g,
               address: /[^0-9A-Za-zÀ-ỿ\s./,-]/g,
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
               const selectedProvince = province.value
                  ? province.options[province.selectedIndex]?.textContent.trim()
                  : '';
               const selectedDistrict = district.value
                  ? district.options[district.selectedIndex]?.textContent.trim()
                  : '';
               const parts = [
                  addressLine.value.trim(),
                  selectedDistrict,
                  selectedProvince,
               ].filter(Boolean);

               fullAddress.value = parts.join(', ');
            };

            const renderDistricts = () => {
               const provinceCode = province.value;
               
               if (!provinceCode) {
                  district.innerHTML = '<option value="">Chọn phường/xã</option>';
                  district.disabled = true;
                  syncFullAddress();
                  return;
               }

               // Get communes from loaded data (instant, no API call needed)
               const items = communesData[provinceCode] || [];

               district.innerHTML = '<option value="">Chọn phường/xã</option>';

               items.forEach((item) => {
                  const option = document.createElement('option');
                  option.value = item.code;
                  option.textContent = item.name;
                  district.appendChild(option);
               });

               district.disabled = items.length === 0;

               if (selectedDistrict && items.some(item => item.code === selectedDistrict)) {
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

            // Handle birthday date display format (dd/mm/yyyy)
            const birthdayInput = document.getElementById('birthday');
            const birthdayDisplay = document.getElementById('birthday-display');
            
            const formatBirthdayDisplay = () => {
               const value = birthdayInput.value;
               if (!value) {
                  return;
               }
               
               // value is in yyyy-mm-dd format from date picker
               const parts = value.split('-');
               if (parts.length === 3) {
                  const [year, month, day] = parts;
                  birthdayDisplay.value = `${day}/${month}/${year}`; // Display as dd/mm/yyyy
               }
            };

            const syncBirthdayValue = () => {
               const value = birthdayDisplay.value.trim();
               const match = value.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);

               if (!match) {
                  birthdayInput.value = '';
                  return false;
               }

               const [, day, month, year] = match;
               const parsedDate = new Date(Number(year), Number(month) - 1, Number(day));
               const isValidDate = parsedDate.getFullYear() === Number(year)
                  && parsedDate.getMonth() === Number(month) - 1
                  && parsedDate.getDate() === Number(day);

               if (!isValidDate) {
                  birthdayInput.value = '';
                  return false;
               }

               birthdayInput.value = `${year}-${month}-${day}`;
               return true;
            };

            const formatTypedBirthday = () => {
               const digits = birthdayDisplay.value.replace(/[^0-9]/g, '').slice(0, 8);
               const parts = [];

               if (digits.length > 0) parts.push(digits.slice(0, 2));
               if (digits.length > 2) parts.push(digits.slice(2, 4));
               if (digits.length > 4) parts.push(digits.slice(4, 8));

               birthdayDisplay.value = parts.join('/');
               syncBirthdayValue();
            };

            birthdayInput.addEventListener('change', formatBirthdayDisplay);
            birthdayInput.addEventListener('input', formatBirthdayDisplay);
            birthdayDisplay.addEventListener('input', formatTypedBirthday);
            birthdayDisplay.addEventListener('input', () => {
               if (touched.birthday) validateBirthday();
            });
            
            // Format initial value if exists
            setTimeout(formatBirthdayDisplay, 100);

            // ===== CLIENT-SIDE VALIDATION =====
            const validationStates = {
               full_name: false,
               gender: false,
               phone: false,
               cccd: false,
               birthday: false,
               province: true, // Tỉnh thành mặc định cho qua nếu không bắt buộc
               district: true,
               address_line: true,
            };

            const touched = {
               full_name: false,
               gender: false,
               phone: false,
               cccd: false,
               birthday: false,
               province: false,
               district: false,
               address_line: false,
            };

            const setError = (input, id, msg) => {
               const el = document.getElementById(id);
               if (el) el.textContent = msg || '';
               if (input) {
                  if (msg) input.classList.add('is-invalid');
                  else input.classList.remove('is-invalid');
               }
            };

            const calculateAge = (dateString) => {
               const today = new Date();
               const birthDate = new Date(dateString);
               let age = today.getFullYear() - birthDate.getFullYear();
               const monthDiff = today.getMonth() - birthDate.getMonth();
               
               if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                  age--;
               }
               
               return age;
            };

            const validateFullName = () => {
               const v = fullNameInput.value.trim();
               
               if (!v) {
                  setError(fullNameInput, 'full_name-error', 'Họ và tên không được để trống.');
                  return validationStates.full_name = false;
               }
               
               if (v.length < 2 || v.length > 60) {
                  setError(fullNameInput, 'full_name-error', 'Họ và tên phải từ 2-60 ký tự.');
                  return validationStates.full_name = false;
               }
               
               if (!/^[A-Za-zÀ-ỿ\s]+$/.test(v)) {
                  setError(fullNameInput, 'full_name-error', 'Họ và tên chỉ gồm chữ cái và khoảng trắng.');
                  return validationStates.full_name = false;
               }
               
               setError(fullNameInput, 'full_name-error', '');
               return validationStates.full_name = true;
            };

            const validateGender = () => {
               const v = genderInput.value;
               
               if (!v) {
                  setError(genderInput, 'gender-error', 'Vui lòng chọn giới tính.');
                  return validationStates.gender = false;
               }
               
               setError(genderInput, 'gender-error', '');
               return validationStates.gender = true;
            };

            const validatePhone = () => {
               const v = phoneInput.value.trim();
               
               if (!v) {
                  setError(phoneInput, 'phone-error', 'Số điện thoại không được để trống.');
                  return validationStates.phone = false;
               }
               
               if (!/^0[0-9]{9}$/.test(v)) {
                  setError(phoneInput, 'phone-error', 'Số điện thoại phải gồm 10 chữ số, bắt đầu bằng 0.');
                  return validationStates.phone = false;
               }
               
               setError(phoneInput, 'phone-error', '');
               return validationStates.phone = true;
            };

            const validateCCCD = () => {
               const v = cccdInput.value.trim();
               
               if (!v) {
                  setError(cccdInput, 'cccd-error', 'CCCD không được để trống.');
                  return validationStates.cccd = false;
               }
               
               if (!/^[0-9]{12}$/.test(v)) {
                  setError(cccdInput, 'cccd-error', 'CCCD phải gồm đúng 12 chữ số.');
                  return validationStates.cccd = false;
               }
               
               setError(cccdInput, 'cccd-error', '');
               return validationStates.cccd = true;
            };

            const validateBirthday = () => {
               const hasValidTypedDate = syncBirthdayValue();
               const v = birthdayInput.value;
               
               if (!v && birthdayDisplay.value.trim() && !hasValidTypedDate) {
                  setError(birthdayDisplay, 'birthday-error', 'Ngày sinh phải đúng định dạng dd/mm/yyyy.');
                  return validationStates.birthday = false;
               }

               if (!v) {
                  setError(birthdayDisplay, 'birthday-error', 'Ngày sinh không được để trống.');
                  return validationStates.birthday = false;
               }
               
               const age = calculateAge(v);
               if (age < 18) {
                  setError(birthdayDisplay, 'birthday-error', 'Bạn phải đủ 18 tuổi để đăng ký.');
                  return validationStates.birthday = false;
               }
               
               setError(birthdayDisplay, 'birthday-error', '');
               return validationStates.birthday = true;
            };

            const validateProvince = () => {
               const v = provinceInput.value.trim();
               if (!v) {
                  setError(provinceInput, 'province-error', '');
                  return validationStates.province = true;
               }
               setError(provinceInput, 'province-error', '');
               return validationStates.province = true;
            };

            const validateDistrict = () => {
               const v = districtInput.value.trim();
               if (!v) {
                  setError(districtInput, 'district-error', '');
                  return validationStates.district = true;
               }
               setError(districtInput, 'district-error', '');
               return validationStates.district = true;
            };

            const validateAddressLine = () => {
               const v = addressLineInput.value.trim(); // Still trim to handle whitespace only
               
               if (!v) { // If empty, it's valid as it's not required
                  setError(addressLineInput, 'address_line-error', '');
                  return validationStates.address_line = true;
               }
               
               if (v.length < 4 || v.length > 120) {
                  setError(addressLineInput, 'address_line-error', 'Số nhà và tên đường phải từ 4-120 ký tự.');
                  return validationStates.address_line = false;
               }
               
               if (!/^[0-9A-Za-zÀ-ỿ\s./,-]+$/.test(v)) {
                  setError(addressLineInput, 'address_line-error', 'Số nhà và tên đường chỉ gồm chữ, số và ký tự . / , -');
                  return validationStates.address_line = false;
               }
               
               return validationStates.address_line = true;
            };

            // Blur events - mark as touched and validate
            fullNameInput.addEventListener('blur', () => {
               touched.full_name = true;
               validateFullName();
            });

            genderInput.addEventListener('blur', () => {
               touched.gender = true;
               validateGender();
            });

            phoneInput.addEventListener('blur', () => {
               touched.phone = true;
               validatePhone();
            });

            cccdInput.addEventListener('blur', () => {
               touched.cccd = true;
               validateCCCD();
            });

            birthdayDisplay.addEventListener('blur', () => {
               touched.birthday = true;
               validateBirthday();
            });

            provinceInput.addEventListener('blur', () => {
               touched.province = true;
               validateProvince();
            });

            districtInput.addEventListener('blur', () => {
               touched.district = true;
               validateDistrict();
            });

            addressLineInput.addEventListener('blur', () => {
               touched.address_line = true;
               validateAddressLine();
            });

            // Input/change events - validate if touched
            fullNameInput.addEventListener('input', () => {
               if (touched.full_name) validateFullName();
            });

            genderInput.addEventListener('change', () => {
               if (touched.gender) validateGender();
            });

            phoneInput.addEventListener('input', () => {
               if (touched.phone) validatePhone();
            });

            cccdInput.addEventListener('input', () => {
               if (touched.cccd) validateCCCD();
            });

            birthdayDisplay.addEventListener('change', () => {
               syncBirthdayValue();
               if (touched.birthday) validateBirthday();
            });

            birthdayInput.addEventListener('change', () => {
               formatBirthdayDisplay();
               if (touched.birthday) validateBirthday();
            });

            provinceInput.addEventListener('change', () => {
               if (touched.province) validateProvince();
            });

            districtInput.addEventListener('change', () => {
               if (touched.district) validateDistrict();
            });

            addressLineInput.addEventListener('input', () => {
               if (touched.address_line) validateAddressLine();
            });

            // Form submit
            form.addEventListener('submit', (event) => {
               // Mark all as touched
               Object.keys(touched).forEach(key => {
                  touched[key] = true;
               });

               // Validate all fields
               const v1 = validateFullName();
               const v2 = validateGender();
               const v3 = validatePhone();
               const v4 = validateCCCD();
               const v5 = validateBirthday();
               const v6 = validateProvince();
               const v7 = validateDistrict();
               const v8 = validateAddressLine();

               // Check if all valid
               const allValid = v1 && v2 && v3 && v4 && v5 && v6 && v7 && v8;
               
               if (!allValid) {
                  event.preventDefault();
                  // Cuộn đến lỗi đầu tiên để người dùng thấy ngay
                  const firstInvalid = form.querySelector('.is-invalid');
                  if (firstInvalid) firstInvalid.focus();
                  return;
               }

               syncFullAddress();
            });

            renderDistricts();

            const messageModal = document.getElementById('authMessageModal');

            if (messageModal && window.bootstrap) {
               bootstrap.Modal.getOrCreateInstance(messageModal).show();
            }
         });
      </script>
   </section>
</x-guest-layout>
