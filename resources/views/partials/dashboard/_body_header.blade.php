@php
  $dashboardRoute = route('admin.dashboard');
  $portalLabel = 'Admin';
@endphp

<nav class="nav navbar navbar-expand-lg navbar-light iq-navbar navs-color">
  <div class="container-fluid navbar-inner">
    <a href="{{ $dashboardRoute }}" class="navbar-brand">
      <img src="{{ asset('images/logo_hotel.png') }}" alt="Peach Valley Hotel" class="hotel-brand-logo hotel-brand-logo-navbar">
      <h4 class="logo-title d-none">{{ config('app.name', 'Peach Valley') }}</h4>
    </a>
    <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
      <i class="icon">
        <svg width="20px" height="20px" viewBox="0 0 24 24">
          <path fill="currentColor" d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z" />
      </svg>
      </i>
    </div>
    <div class="input-group search-input">
      <span class="input-group-text d-flex align-items-center justify-content-center" id="search-input" style="width: 44px; min-width: 44px; padding: 0;">
        <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="11.7669" cy="11.7666" r="8.98856" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></circle>
          <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
      </span>
      <input type="search" class="form-control" placeholder="{{ request()->routeIs('reception.*') ? 'Tim booking, khach hang, hoa don...' : 'Tim kiem...' }}">
    </div>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
      <span class="navbar-toggler-icon">
        <span class="navbar-toggler-bar bar1 mt-2"></span>
        <span class="navbar-toggler-bar bar2"></span>
        <span class="navbar-toggler-bar bar3"></span>
      </span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto navbar-list mb-2 mb-lg-0">
        <li class="nav-item dropdown">
          <a class="nav-link py-0 d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="bg-soft-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="currentColor" stroke-width="1.5"/>
                <path d="M4 20C4 16.6863 7.58172 14 12 14C16.4183 14 20 16.6863 20 20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
            </span>
            <div class="caption d-none d-md-block">
              <h6 class="mb-0 caption-title">{{ $portalLabel }}</h6>
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
            <li>
              <a href="{{ route('login') }}" class="dropdown-item">Đăng xuất</a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
