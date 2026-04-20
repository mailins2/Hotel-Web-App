<x-guest-layout>
   <section class="login-content">
      <style>
         .auth-panel {
            min-height: 100vh;
         }

         .auth-copy {
            color: #64748b;
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

         .auth-divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.5rem 0 1rem;
            color: #94a3b8;
            font-size: 0.95rem;
         }

         .auth-divider::before,
         .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
         }

         .auth-image-caption {
            position: absolute;
            left: 3rem;
            right: 3rem;
            bottom: 3rem;
            z-index: 1;
            color: #fff;
         }

         .auth-image-caption p {
            max-width: 32rem;
            color: rgba(255, 255, 255, 0.82);
         }

         .auth-field-error {
            display: block;
            margin-top: 0.5rem;
            color: #dc2626;
            font-size: 0.875rem;
            font-weight: 600;
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
                  <div class="card card-transparent auth-card shadow-none d-flex justify-content-center mb-0">
                     <div class="card-body">
                        <a href="{{ route('dashboard') }}" class="navbar-brand d-flex align-items-center mb-3">
                           <img src="{{ asset('images/logo_hotel.png') }}" alt="Peach Valley Hotel" class="auth-brand-logo">
                        </a>
                        <h2 class="mb-2 text-center">Đăng ký</h2>
                        <x-auth-session-status class="mb-4" :status="session('status')" />
                        <form method="POST" action="{{ route('register') }}" data-toggle="validator">
                           {{ csrf_field() }}
                           <div class="row">
                              <div class="col-lg-12">
                                 <div class="form-group">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input class="form-control @error('email') is-invalid @enderror" type="email" placeholder="Nhập email của bạn" id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                       <span class="auth-field-error">{{ $message }}</span>
                                    @enderror
                                 </div>
                              </div>
                              <div class="col-lg-12">
                                 <div class="form-group">
                                    <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                    <input class="form-control @error('password') is-invalid @enderror" type="password" placeholder="Tạo mật khẩu" id="password" name="password" required autocomplete="new-password">
                                    @error('password')
                                       <span class="auth-field-error">{{ $message }}</span>
                                    @enderror
                                 </div>
                              </div>
                              <div class="col-lg-12">
                                 <div class="form-group">
                                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                    <input id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" type="password" placeholder="Nhập lại mật khẩu" name="password_confirmation" required>
                                    @error('password_confirmation')
                                       <span class="auth-field-error">{{ $message }}</span>
                                    @enderror
                                 </div>
                              </div>
                           </div>
                           <div class="d-flex justify-content-center mt-4">
                              <button type="submit" class="btn btn-primary px-5">Đăng ký</button>
                           </div>
                        </form>

                        <div class="auth-divider">Hoặc đăng ký với</div>
                        <a href="{{ route('auth.google', ['source' => 'register']) }}" class="auth-google-button text-decoration-none">
                           <img src="{{ asset('images/brands/gm.svg') }}" class="auth-google-icon" alt="Google">
                           <span class="auth-google-label">Tiếp tục với Google</span>
                        </a>

                        <p class="mt-4 text-center">
                           Đã có tài khoản? <a href="{{ route('auth.signin') }}" class="text-underline">Đăng nhập</a>
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
         <div class="col-md-6 d-md-block d-none p-0 mt-n1 vh-100 overflow-hidden auth-side-visual">
            <img src="{{ asset('images/auth/khachsan.jpg') }}" class="auth-side-image animated-scaleX" alt="Khách sạn Peach Valley">
         </div>
      </div>
   </section>
</x-guest-layout>
