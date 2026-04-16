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

         .auth-google-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 70%;
            margin: 0 auto;
            padding: 0.7rem 1rem;
            border: 1px solid #dbe4f0;
            border-radius: 500px;
            background: #fff;
            color: #0f172a;
            font-weight: 400;
            transition: all 0.2s ease;
         }

         .auth-google-button:hover {
            border-color: #cbd5e1;
            box-shadow: 0 12px 30px rgba(30, 34, 44, 0.08);
            color: #0f172a;
         }

         .auth-google-icon {
            width: 22px;
            height: 22px;
            flex-shrink: 0;
            object-fit: contain;
         }

         .auth-field-error {
            display: block;
            margin-top: 0.5rem;
            color: #dc2626;
            font-size: 0.875rem;
            font-weight: 500;
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
                           <svg width="30" class="text-primary" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <rect x="-0.757324" y="19.2427" width="28" height="4" rx="2" transform="rotate(-45 -0.757324 19.2427)" fill="currentColor"/>
                              <rect x="7.72803" y="27.728" width="28" height="4" rx="2" transform="rotate(-45 7.72803 27.728)" fill="currentColor"/>
                              <rect x="10.5366" y="16.3945" width="16" height="4" rx="2" transform="rotate(45 10.5366 16.3945)" fill="currentColor"/>
                              <rect x="10.5562" y="-0.556152" width="28" height="4" rx="2" transform="rotate(45 10.5562 -0.556152)" fill="currentColor"/>
                           </svg>
                           <h4 class="logo-title ms-3">Khách Sạn Peach Valley</h4>
                        </a>
                        <h2 class="mb-2 text-center">Đăng nhập</h2>
                        <x-auth-session-status class="mb-4" :status="session('status')" />
                        <form method="POST" action="{{ route('login') }}" data-toggle="validator">
                           {{ csrf_field() }}
                           <div class="row">
                              <div class="col-lg-12">
                                 <div class="form-group">
                                    <label for="email" class="form-label">Email</label>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="Nhập email đăng nhập" required autofocus>
                                    @error('email')
                                       <span class="auth-field-error">{{ $message }}</span>
                                    @enderror
                                 </div>
                              </div>
                              <div class="col-lg-12">
                                 <div class="form-group">
                                    <label for="password" class="form-label">Mật khẩu</label>
                                    <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" placeholder="Nhập mật khẩu" name="password" required autocomplete="current-password">
                                    @error('password')
                                       <span class="auth-field-error">{{ $message }}</span>
                                    @enderror
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

                        <div class="auth-divider">Hoặc tiếp tục với</div>
                        <a href="{{ route('auth.google', ['source' => 'login']) }}" class="auth-google-button text-decoration-none">
                           <img src="{{ asset('images/brands/gm.svg') }}" class="auth-google-icon" alt="Google">
                           Đăng nhập với Google
                        </a>

                        <p class="mt-4 text-center">
                           Chưa có tài khoản? <a href="{{ route('auth.signup') }}" class="text-underline">Đăng ký ngay.</a>
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
            <img src="{{ asset('images/auth/khachsan.jpg') }}" class="auth-side-image animated-scaleX" alt="Khách sạn Peach Valley">
         </div>
      </div>
   </section>
</x-guest-layout>
