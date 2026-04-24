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

                  <h2 class="mb-2">Mật khẩu mới</h2>
                  <p class="auth-reset-help mb-4">Nhập mật khẩu mới cho tài khoản của bạn.</p>

                  <form data-new-password-form>
                     <div class="row">
                        <div class="col-lg-12">
                           <div class="form-group">
                              <label for="password" class="form-label">Mật khẩu mới</label>
                              <input
                                 type="password"
                                 class="form-control @error('password') is-invalid @enderror"
                                 id="password"
                                 name="password"
                                 placeholder="Nhap mat khau moi"
                                 minlength="8"
                                 autocomplete="new-password"
                                 data-new-password
                                 required>
                              @error('password')
                                 <span class="auth-field-error">{{ $message }}</span>
                              @enderror
                           </div>
                        </div>

                        <div class="col-lg-12">
                           <div class="form-group">
                              <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                              <input
                                 type="password"
                                 class="form-control @error('password_confirmation') is-invalid @enderror"
                                 id="password_confirmation"
                                 name="password_confirmation"
                                 placeholder="Nhap lai mat khau moi"
                                 autocomplete="new-password"
                                 data-new-password-confirm
                                 required>
                              <span class="auth-field-error" data-password-match-error @if(! $errors->has('password_confirmation')) hidden @endif>
                                 {{ $errors->first('password_confirmation') ?: 'Xac nhan mat khau khong khop.' }}
                              </span>
                           </div>
                        </div>
                     </div>

                     <button type="submit" class="btn btn-primary btn-block">Cập nhật mật khẩu</button>
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
            const form = document.querySelector('[data-new-password-form]');

            if (!form) {
               return;
            }

            const password = form.querySelector('[data-new-password]');
            const confirmation = form.querySelector('[data-new-password-confirm]');
            const matchError = form.querySelector('[data-password-match-error]');

            const validatePasswordMatch = function () {
               const isMismatch = confirmation.value.length > 0 && password.value !== confirmation.value;

               confirmation.classList.toggle('is-invalid', isMismatch);
               confirmation.setCustomValidity(isMismatch ? 'Xac nhan mat khau khong khop.' : '');

               if (matchError) {
                  matchError.hidden = !isMismatch;
                  matchError.textContent = 'Xac nhan mat khau khong khop.';
               }

               return !isMismatch;
            };

            password.addEventListener('input', validatePasswordMatch);
            confirmation.addEventListener('input', validatePasswordMatch);

            form.addEventListener('submit', function (event) {
               event.preventDefault();

               if (!validatePasswordMatch() || !form.checkValidity()) {
                  form.reportValidity();
               }
            });
         });
      </script>
   </section>
</x-guest-layout>
