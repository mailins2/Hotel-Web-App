<x-guest-layout>
   <section class="login-content">
      <style>
         .auth-field-error {
            display: block;
            margin-top: 0.5rem;
            color: #dc2626;
            font-size: 0.875rem;
            font-weight: 600;
         }
      </style>
      <div class="row m-0 align-items-center bg-white vh-100">
         <div class="col-md-6 d-md-block d-none bg-primary p-0 mt-n1 vh-100 overflow-hidden">
            <img src="{{ asset('images/auth/khachsan.jpg') }}" class="img-fluid gradient-main animated-scaleX" alt="Khach san">
         </div>
         <div class="col-md-6 p-0">
            <div class="card card-transparent auth-card shadow-none d-flex justify-content-center mb-0">
               <div class="card-body">
                  <a href="{{ route('dashboard') }}" class="navbar-brand d-flex align-items-center mb-3">
                     <img src="{{ asset('images/logo_hotel.png') }}" alt="Peach Valley Hotel" class="auth-brand-logo">
                  </a>
                  <h2 class="mb-2">Quên mật khẩu</h2>
                  <p>Nhập số điện thoại để xác thực tài khoản </p>
                  <form data-forgot-password-form novalidate>
                     <div class="row">
                        <div class="col-lg-12">
                           <div class="floating-label form-group">
                              <label for="phone" class="form-label">Số điện thoại</label>
                              <input
                                 type="tel"
                                 inputmode="numeric"
                                 maxlength="10"
                                 class="form-control @error('phone') is-invalid @enderror"
                                 id="phone"
                                 name="phone"
                                 value="{{ old('phone') }}"
                                 aria-describedby="phone-error"
                                 placeholder=" "
                                 required
                              >
                              <span id="phone-error" class="auth-field-error">
                                 @error('phone')
                                    {{ $message }}
                                 @enderror
                              </span>
                           </div>
                        </div>
                     </div>
                     <button type="submit" class="btn btn-primary btn-block" data-forgot-password-submit>Nhận OTP</button>
                  </form>
               </div>
            </div>
            <div class="sign-bg sign-bg-right">
               <svg width="280" height="230" viewBox="0 0 431 398" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <g opacity="0.05">
                  <rect x="-157.085" y="193.773" width="543" height="77.5714" rx="38.7857" transform="rotate(-45 -157.085 193.773)" fill="#3B8AFF"/>
                  <rect x="7.46875" y="358.327" width="543" height="77.5714" rx="38.7857" transform="rotate(-45 7.46875 358.327)" fill="#3B8AFF"/>
                  <rect x="61.9355" y="138.545" width="310.286" height="77.5714" rx="38.7857" transform="rotate(45 61.9355 138.545)" fill="#3B8AFF"/>
                  <rect x="62.3154" y="-190.173" width="543" height="77.5714" rx="38.7857" transform="rotate(45 62.3154 -190.173)" fill="#3B8AFF"/>
                  </g>
               </svg>
            </div>
         </div>
      </div>

      <script>
         document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('[data-forgot-password-form]');

            if (!form) {
               return;
            }

            const phoneInput = form.querySelector('#phone');
            const phoneError = form.querySelector('#phone-error');
            const submitButton = form.querySelector('[data-forgot-password-submit]');
            const touched = {
               phone: false,
            };

            const setPhoneError = (message) => {
               if (phoneError) {
                  phoneError.textContent = message || '';
               }

               phoneInput.classList.toggle('is-invalid', Boolean(message));
            };

            const validatePhone = () => {
               const phone = phoneInput.value.trim();
               phoneInput.value = phone;

               if (!phone) {
                  setPhoneError('Vui lòng nhập số điện thoại.');
                  return false;
               }

               if (!/^0\d{9}$/.test(phone)) {
                  setPhoneError('Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.');
                  return false;
               }

               setPhoneError('');
               return true;
            };

            phoneInput.addEventListener('input', () => {
               phoneInput.value = phoneInput.value.replace(/\D+/g, '').slice(0, 10);

               if (touched.phone) {
                  validatePhone();
               }
            });

            phoneInput.addEventListener('blur', () => {
               touched.phone = true;
               validatePhone();
            });

            form.addEventListener('submit', async (event) => {
               event.preventDefault();
               touched.phone = true;

               if (!validatePhone()) {
                  phoneInput.focus();
                  return;
               }

               const originalText = submitButton.textContent;
               submitButton.disabled = true;
               submitButton.textContent = 'Đang kiểm tra...';

               try {
                  const response = await fetch('/api/quen-mat-khau/gui-otp', {
                     method: 'POST',
                     headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                     },
                     body: JSON.stringify({
                        phone: phoneInput.value.trim(),
                     }),
                  });
                  const payload = await response.json().catch(() => ({}));

                  if (!response.ok) {
                     throw new Error(payload.message || 'Số điện thoại không tồn tại trong hệ thống.');
                  }

                  sessionStorage.removeItem('forgotPasswordVerifiedOtp');
                  sessionStorage.removeItem('forgotPasswordVerifiedPhone');
                  sessionStorage.setItem('forgotPasswordPhone', phoneInput.value.trim());
                  if (payload.otp) {
                     sessionStorage.setItem('forgotPasswordOtpHint', payload.otp);
                  }

                  window.location.href = `{{ route('auth.verify-otp') }}?phone=${encodeURIComponent(phoneInput.value.trim())}`;
               } catch (error) {
                  setPhoneError(error.message || 'Không thể gửi OTP. Vui lòng thử lại.');
                  phoneInput.focus();
               } finally {
                  submitButton.disabled = false;
                  submitButton.textContent = originalText;
               }
            });
         });
      </script>
   </section>
</x-guest-layout>
