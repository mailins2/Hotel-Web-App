<x-guest-layout>
   <section class="login-content">
      <style>
         .auth-panel {
            min-height: 100vh;
            overflow: hidden;
         }

         .auth-main-column {
            display: flex;
            align-items: center;
            min-height: 100vh;
            padding: 40px 0;
         }

         .auth-side-visual {
            position: relative;
            background: #0f172a;
            display: flex;
            min-height: 100vh;
            overflow: hidden;
         }

         .auth-side-visual::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.12) 0%, rgba(15, 23, 42, 0.4) 100%);
            pointer-events: none;
         }

         .auth-side-image {
            width: 100%;
            height: 100%;
            min-height: 100vh;
            display: block;
            object-fit: cover;
            object-position: center;
         }

         .password-toggle-wrapper {
            position: relative;
            width: 100%;
         }

         .password-toggle-wrapper .form-control {
            padding-left: 3rem;
         }

         .password-toggle-button {
            position: absolute;
            top: 50%;
            left: 0.75rem;
            transform: translateY(-50%);
            width: 2rem;
            height: 2rem;
            line-height: 0;
            background: transparent;
            border: none;
            color: #667085;
            cursor: pointer;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
         }

         .password-toggle-button:hover {
            color: #0f172a;
         }

         .password-toggle-button svg {
            width: 1.1rem;
            height: 1.1rem;
         }

         .auth-field-error {
            display: block;
            margin-top: 0.5rem;
            color: #dc2626;
            font-size: 0.875rem;
            font-weight: 600;
         }

         @media (max-width: 1199.98px) {
            .auth-main-column {
               padding: 32px 0;
            }

            .auth-side-image {
               object-position: 58% center;
            }
         }

         @media (max-width: 991.98px) {
            .auth-side-visual,
            .auth-side-image {
               min-height: 100%;
            }
         }

         @media (max-width: 767.98px) {
            .auth-panel {
               min-height: auto;
               overflow: visible;
            }

            .auth-main-column {
               min-height: auto;
               padding: 24px 0;
            }
         }
      </style>
      <div class="row m-0 align-items-stretch bg-white auth-panel">
         <div class="col-md-6 auth-main-column">
            <div class="row justify-content-center">
               <div class="col-md-10">
                  <div class="card card-transparent auth-card shadow-none d-flex justify-content-center mb-0">
                     <div class="card-body">
                        <a href="{{ route('dashboard') }}" class="navbar-brand d-flex align-items-center mb-3">
                           <img src="{{ asset('images/logo_hotel.png') }}" alt="Peach Valley Hotel" class="auth-brand-logo">
                        </a>
                        <h2 class="mb-2 text-center">Đăng ký</h2>
                        <form action="{{ route('register.step1') }}" method="POST" data-ui-only-form data-toggle="validator" novalidate>
                           @csrf
                           <div class="row">
                              <div class="col-lg-12">
                                 <div class="form-group">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input class="form-control @error('email') is-invalid @enderror" type="email" placeholder="Nhap email cua ban" id="email" name="email" value="{{ old('email') }}" required>
                                    <span id="email-error" class="auth-field-error">
                                       @error('email')
                                          {{ $message }}
                                       @enderror
                                    </span>
                                 </div>
                              </div>
                              <div class="col-lg-12">
                                 <div class="form-group">
                                    <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                    <div class="password-toggle-wrapper">
                                       <input class="form-control @error('password') is-invalid @enderror" type="password" placeholder="Tao mat khau" id="password" name="password" required autocomplete="new-password">
                                       <button type="button" class="password-toggle-button" data-target="password" aria-label="Hiện mật khẩu">
                                          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                       </button>
                                    </div>
                                    <span id="password-error" class="auth-field-error">
                                       @error('password')
                                          {{ $message }}
                                       @enderror
                                    </span>
                                 </div>
                              </div>
                              <div class="col-lg-12">
                                 <div class="form-group">
                                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                    <div class="password-toggle-wrapper">
                                       <input id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" type="password" placeholder="Nhap lai mat khau" name="password_confirmation" required>
                                       <button type="button" class="password-toggle-button" data-target="password_confirmation" aria-label="Hiện mật khẩu">
                                          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                       </button>
                                    </div>
                                    <span id="password_confirmation-error" class="auth-field-error">
                                       @error('password_confirmation')
                                          {{ $message }}
                                       @enderror
                                    </span>
                                 </div>
                              </div>
                           </div>
                           <div class="d-flex justify-content-center mt-4">
                              <button type="submit" class="btn btn-primary px-5">Đăng ký</button>
                           </div>
                        </form>


                        <p class="mt-4 text-center">
                           Đã có tài khoản? <a href="{{ route('auth.signin') }}" class="text-underline"><b>Đăng nhập</b></a>
                        </p>
                     </div>
                  </div>
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
         <div class="col-md-6 d-md-block d-none p-0 auth-side-visual">
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
            const form = document.querySelector('[data-ui-only-form]');

            if (!form) {
               return;
            }

            const email = form.querySelector('#email');
            const password = form.querySelector('#password');
            const passwordConfirmation = form.querySelector('#password_confirmation');
            const emailError = document.querySelector('#email-error');
            const passwordError = document.querySelector('#password-error');
            const passwordConfirmationError = document.querySelector('#password_confirmation-error');
            const passwordToggles = form.querySelectorAll('.password-toggle-button');
            const eyeIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
            const eyeSlashIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94C16.25 19.2 14.21 20 12 20c-7 0-11-8-11-8a23.86 23.86 0 0 1 5.1-6.13"/><path d="M1 1l22 22"/><path d="M9.53 9.53a3 3 0 0 0 4.2 4.2"/></svg>';
            
            const touched = {
               email: false,
               password: false,
               password_confirmation: false
            };

            passwordToggles.forEach((toggle) => {
               toggle.addEventListener('click', () => {
                  const targetId = toggle.getAttribute('data-target');
                  const input = document.getElementById(targetId);
                  if (!input) {
                     return;
                  }

                  const isPassword = input.type === 'password';
                  input.type = isPassword ? 'text' : 'password';
                  toggle.innerHTML = isPassword ? eyeSlashIcon : eyeIcon;
                  toggle.setAttribute('aria-label', isPassword ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
               });
            });

            const setError = (input, errorId, msg) => {
               const el = document.getElementById(errorId);
               if (el) el.textContent = msg || '';
               if (input) {
                  if (msg) input.classList.add('is-invalid');
                  else input.classList.remove('is-invalid');
               }
            };

            const validateEmail = () => {
               const value = email.value.trim();
               email.value = value;

               if (!value) {
                  setError(email, 'email-error', 'Email không được để trống.');
                  return false;
               }

               if (!email.checkValidity()) {
                  setError(email, 'email-error', 'Email không hợp lệ.');
                  return false;
               }

               setError(email, 'email-error', '');
               return true;
            };

            const validatePassword = () => {
               const value = password.value;

               if (!value) {
                  setError(password, 'password-error', 'Mật khẩu không được để trống.');
                  return false;
               }

               if (value.length < 8) {
                  setError(password, 'password-error', 'Mật khẩu phải có ít nhất 8 ký tự.');
                  return false;
               }

               setError(password, 'password-error', '');
               return true;
            };

            const validatePasswordConfirmation = () => {
               const value = passwordConfirmation.value;

               if (!value) {
                  setError(passwordConfirmation, 'password_confirmation-error', 'Xác nhận mật khẩu không được để trống.');
                  return false;
               }

               if (password.value !== value) {
                  setError(passwordConfirmation, 'password_confirmation-error', 'Mật khẩu xác nhận không khớp.');
                  return false;
               }

               setError(passwordConfirmation, 'password_confirmation-error', '');
               return true;
            };

            const handleFieldValidation = (touched, validator) => {
               if (!touched) {
                  return true;
               }
               return validator();
            };

            email.addEventListener('blur', () => {
               touched.email = true;
               validateEmail();
            });
            password.addEventListener('blur', () => {
               touched.password = true;
               validatePassword();
            });
            passwordConfirmation.addEventListener('blur', () => {
               touched.password_confirmation = true;
               validatePasswordConfirmation();
            });

            email.addEventListener('input', () => {
               if (touched.email) {
                  validateEmail();
               }
            });
            password.addEventListener('input', () => {
               if (touched.password) {
                  validatePassword();
               }
               if (touched.password_confirmation && passwordConfirmation.value) {
                  validatePasswordConfirmation();
               }
            });
            passwordConfirmation.addEventListener('input', () => {
               if (touched.password_confirmation) {
                  validatePasswordConfirmation();
               }
            });

            form.addEventListener('submit', (event) => {
               Object.keys(touched).forEach(k => touched[k] = true);

               const validEmail = validateEmail();
               const validPassword = validatePassword();
               const validPasswordConfirmation = validatePasswordConfirmation();

               if (!validEmail || !validPassword || !validPasswordConfirmation) {
                  event.preventDefault();
                  const firstErr = form.querySelector('.is-invalid');
                  if (firstErr) firstErr.focus();
               }
            });

            const messageModal = document.getElementById('authMessageModal');

            if (messageModal && window.bootstrap) {
               bootstrap.Modal.getOrCreateInstance(messageModal).show();
            }
         });
      </script>
   </section>
</x-guest-layout>
