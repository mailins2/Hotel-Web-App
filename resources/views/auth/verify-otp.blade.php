<x-guest-layout>
   <section class="login-content">
      <style>
         .auth-reset-panel {
            min-height: 100vh;
         }

         .auth-reset-side {
            position: relative;
            background: #0f172a;
         }

         .auth-reset-side::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.08) 0%, rgba(15, 23, 42, 0.36) 100%);
            pointer-events: none;
         }

         .auth-reset-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
         }

         .auth-field-error {
            display: block;
            margin-top: 0.5rem;
            color: #dc2626;
            font-size: 0.875rem;
            font-weight: 600;
         }

         .auth-reset-help {
            color: #6b7280;
            line-height: 1.6;
         }

         .auth-reset-card .form-control.is-invalid {
            border-color: #dc2626;
         }

         .auth-reset-card .form-control:focus {
            border-color: #8c4a34;
            box-shadow: 0 0 0 0.2rem rgba(140, 74, 52, 0.14);
         }

         @media (max-width: 767.98px) {
            .auth-reset-panel {
               min-height: auto;
            }
         }
      </style>

      <div class="row m-0 align-items-center bg-white auth-reset-panel">
         <div class="col-md-6 d-md-block d-none bg-primary p-0 mt-n1 vh-100 overflow-hidden auth-reset-side">
            <img src="{{ asset('images/auth/khachsan.jpg') }}" class="auth-reset-image animated-scaleX" alt="Khach san Peach Valley">
         </div>

         <div class="col-md-6 p-0">
            <div class="card card-transparent auth-card auth-reset-card shadow-none d-flex justify-content-center mb-0">
               <div class="card-body">
                  <a href="{{ route('dashboard') }}" class="navbar-brand d-flex align-items-center mb-3">
                     <img src="{{ asset('images/logo_hotel.png') }}" alt="Peach Valley Hotel" class="auth-brand-logo">
                  </a>

                  <h2 class="mb-2">Nhập mã OTP</h2>
                  <p class="auth-reset-help mb-4">Nhập mã OTP để xác minh yêu cầu đặt lại mật khẩu.</p>

                  <form data-verify-otp-form novalidate>
                     <input type="hidden" name="phone" value="{{ request('phone') }}" data-verify-phone>
                     <div class="form-group">
                        <label for="otp" class="form-label">Mã OTP</label>
                        <input
                           type="text"
                           inputmode="numeric"
                           maxlength="6"
                           class="form-control"
                           id="otp"
                           name="otp"
                           placeholder="Nhập mã OTP"
                           data-verify-otp
                           required>
                        <span class="auth-field-error" data-otp-error hidden></span>
                     </div>

                     <button type="submit" class="btn btn-primary btn-block" data-verify-submit>Xác nhận OTP</button>
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
         document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('[data-verify-otp-form]');

            if (!form) {
               return;
            }

            const phone = form.querySelector('[data-verify-phone]');
            const otp = form.querySelector('[data-verify-otp]');
            const otpError = form.querySelector('[data-otp-error]');
            const submitButton = form.querySelector('[data-verify-submit]');
            const phoneFromStorage = sessionStorage.getItem('forgotPasswordPhone') || '';
            const touched = {
               otp: false,
            };

            if (!phone.value && phoneFromStorage) {
               phone.value = phoneFromStorage;
            }

            if (!phone.value) {
               window.location.href = @json(route('auth.recoverpw'));
               return;
            }

            const setOtpError = function (message) {
               otp.classList.toggle('is-invalid', Boolean(message));
               otpError.hidden = !message;
               otpError.textContent = message || '';
            };

            const validateOtp = function () {
               otp.value = otp.value.replace(/\D+/g, '').slice(0, 6);

               if (!otp.value) {
                  setOtpError('Vui lòng nhập mã OTP.');
                  return false;
               }

               if (!/^\d{6}$/.test(otp.value)) {
                  setOtpError('Mã OTP phải gồm đúng 6 chữ số.');
                  return false;
               }

               setOtpError('');
               return true;
            };

            otp.addEventListener('input', function () {
               otp.value = otp.value.replace(/\D+/g, '').slice(0, 6);

               if (touched.otp) {
                  validateOtp();
               }
            });

            otp.addEventListener('blur', function () {
               touched.otp = true;
               validateOtp();
            });

            form.addEventListener('submit', async function (event) {
               event.preventDefault();
               touched.otp = true;

               if (!validateOtp()) {
                  otp.focus();
                  return;
               }

               const originalText = submitButton.textContent;
               submitButton.disabled = true;
               submitButton.textContent = 'Đang xác nhận...';

               try {
                  const response = await fetch('/api/quen-mat-khau/xac-nhan-otp', {
                     method: 'POST',
                     headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                     },
                     body: JSON.stringify({
                        phone: phone.value,
                        otp: otp.value,
                     }),
                  });
                  const payload = await response.json().catch(() => ({}));

                  if (!response.ok) {
                     throw new Error(payload.message || 'Mã OTP không đúng.');
                  }

                  sessionStorage.setItem('forgotPasswordVerifiedOtp', otp.value);
                  sessionStorage.setItem('forgotPasswordVerifiedPhone', phone.value);
                  window.location.href = `{{ route('auth.new-password') }}?phone=${encodeURIComponent(phone.value)}`;
               } catch (error) {
                  setOtpError(error.message || 'Mã OTP không đúng.');
                  otp.focus();
               } finally {
                  submitButton.disabled = false;
                  submitButton.textContent = originalText;
               }
            });
         });
      </script>
   </section>
</x-guest-layout>
