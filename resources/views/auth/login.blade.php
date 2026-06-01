<x-guest-layout>
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
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.12) 0%, rgba(15, 23, 42, 0.4) 100%);
            pointer-events: none;
         }

         .auth-side-image {
            width: 100%;
            height: 100%;
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
            font-weight: 500;
         }

         @media (max-width: 767.98px) {
            .auth-panel {
               min-height: auto;
            }
         }
      </style>
      <div class="row m-0 align-items-center bg-white auth-panel">
         <div class="col-md-6">
            <div class="row justify-content-center">
               <div class="col-md-10">
                  <div class="card card-transparent shadow-none d-flex justify-content-center mb-0 auth-card">
                     <div class="card-body">
                        <a href="{{ route('dashboard') }}" class="navbar-brand d-flex align-items-center mb-3">
                           <img src="{{ asset('images/logo_hotel.png') }}" alt="Peach Valley Hotel" class="auth-brand-logo">
                        </a>
                        <h2 class="mb-2 text-center">Đăng nhập</h2>
                        <form action="{{ route('login.store') }}" method="POST" accept-charset="UTF-8" data-login-form data-toggle="validator" novalidate>
                           @csrf
                           @if (request()->filled('redirect'))
                              <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                           @endif
                           <div class="row">
                              <div class="col-lg-12">
                                 <div class="form-group">
                                    <label for="email" class="form-label">Email*</label>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="Nhap email dang nhap" required autofocus>
                                    <span id="email-error" class="auth-field-error">@error('email') {{ $message }} @enderror</span>
                                 </div>
                              </div>
                              <div class="col-lg-12">
                                 <div class="form-group">
                                    <label for="password" class="form-label">Mật khẩu*</label>
                                    <div class="password-toggle-wrapper">
                                       <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" placeholder="Nhap mat khau" name="password" required autocomplete="current-password">
                                       <button type="button" class="password-toggle-button" data-target="password" aria-label="Hiện mật khẩu">
                                          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                       </button>
                                    </div>
                                    <span id="password-error" class="auth-field-error">@error('password') {{ $message }} @enderror</span>
                                 </div>
                              </div>
                              <div class="col-lg-12">
                                 <a href="{{ route('auth.recoverpw') }}" class="float-end">Quên mật khẩu?</a>
                              </div>
                           </div>
                           <div class="d-flex justify-content-center mt-4">
                              <button type="submit" class="btn btn-primary px-5">Đăng nhập</button>
                           </div>
                        </form>

                        <p class="mt-4 text-center">
                           Chưa có tài khoản? <a href="{{ route('auth.signup') }}" class="text-underline">Đăng kí ngay.</a>
                        </p>
                     </div>
                  </div>
               </div>
            </div>
            <div class="sign-bg">
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
         <div class="col-md-6 d-md-block d-none p-0 mt-n1 vh-100 overflow-hidden auth-side-visual">
            <img src="{{ asset('images/auth/khachsan.jpg') }}" class="auth-side-image animated-scaleX" alt="Khach san Peach Valley">
         </div>
      </div>

      @if (session('success') || $errors->any())
         <div class="modal fade" id="authMessageModal" tabindex="-1" aria-labelledby="authMessageModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
               <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="authMessageModalTitle">{{ $errors->any() ? 'Không thể đăng nhập' : 'Thông báo' }}</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                  </div>
                  <div class="modal-body">
                     {{ $errors->any() ? $errors->first() : session('success') }}
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
            const form = document.querySelector('[data-login-form]');

            if (!form) {
               return;
            }

            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const passwordToggles = form.querySelectorAll('.password-toggle-button');
            const eyeIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
            const eyeSlashIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94C16.25 19.2 14.21 20 12 20c-7 0-11-8-11-8a23.86 23.86 0 0 1 5.1-6.13"/><path d="M1 1l22 22"/><path d="M9.53 9.53a3 3 0 0 0 4.2 4.2"/></svg>';

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

            const setError = (input, id, message) => {
               const element = document.getElementById(id);
               if (element) element.textContent = message || '';
               if (!input) return;

               if (message) input.classList.add('is-invalid');
               else input.classList.remove('is-invalid');
            };

            form.addEventListener('submit', (event) => {
               let valid = true;

               if (!email.value.trim()) {
                  setError(email, 'email-error', 'Vui lòng nhập email.');
                  valid = false;
               } else if (!email.checkValidity()) {
                  setError(email, 'email-error', 'Email không đúng định dạng.');
                  valid = false;
               } else {
                  setError(email, 'email-error', '');
               }

               if (!password.value) {
                  setError(password, 'password-error', 'Vui lòng nhập mật khẩu.');
                  valid = false;
               } else {
                  setError(password, 'password-error', '');
               }

               if (!valid) {
                  event.preventDefault();
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
