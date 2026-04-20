<nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
  <div class="container">
    <a class="navbar-brand brand-logo" href="{{ route('customer.home') }}">
      <span class="brand-peach">Peach</span><span class="brand-valley">Valley</span>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="oi oi-menu"></span>
    </button>

    <div class="collapse navbar-collapse" id="ftco-nav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item{{ ($active ?? '') === 'home' ? ' active' : '' }}"><a href="{{ route('customer.home') }}" class="nav-link">Trang Chủ</a></li>
        <li class="nav-item{{ ($active ?? '') === 'rooms' ? ' active' : '' }}"><a href="{{ route('customer.rooms') }}" class="nav-link">Phòng</a></li>
        <li class="nav-item{{ ($active ?? '') === 'services' ? ' active' : '' }}"><a href="{{ route('customer.services') }}" class="nav-link">Dịch Vụ</a></li>
        <li class="nav-item{{ ($active ?? '') === 'promotion' ? ' active' : '' }}"><a href="{{ route('customer.promotion') }}" class="nav-link">Khuyến Mãi</a></li>
        <li class="nav-item{{ ($active ?? '') === 'blog' ? ' active' : '' }}"><a href="{{ route('customer.blog-single') }}" class="nav-link">Về Chúng Tôi</a></li>
      </ul>

      @php
        $navUser = mockUser();
      @endphp
      <div class="nav-cta-group">
        @if ($navUser)
          <div class="dropdown customer-user-menu">
            <button
              type="button"
              class="nav-cta-secondary customer-user-toggle dropdown-toggle"
              id="customerUserMenu"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              Xin chào, {{ $navUser['name'] ?? 'Khách hàng' }}
            </button>
            <div class="dropdown-menu dropdown-menu-right customer-user-dropdown" aria-labelledby="customerUserMenu">
              <a class="dropdown-item customer-user-dropdown-item" href="{{ route('customer.profile') }}">
                <span class="customer-user-dropdown-icon ion-ios-person"></span>
                <span>Thông tin cá nhân</span>
              </a>
              <a class="dropdown-item customer-user-dropdown-item" href="{{ route('customer.my-bookings') }}">
                <span class="customer-user-dropdown-icon ion-ios-briefcase"></span>
                <span>Đặt phòng của bạn</span>
              </a>
              <a class="dropdown-item customer-user-dropdown-item" href="{{ route('customer.promotion-wallet') }}">
                <span class="customer-user-dropdown-icon ion-ios-pricetag"></span>
                <span>Kho khuyến mãi</span>
              </a>
              <form method="POST" action="{{ route('logout') }}" class="customer-user-logout-form">
                {{ csrf_field() }}
                <button type="submit" class="dropdown-item customer-user-dropdown-item customer-user-logout">
                  <span class="customer-user-dropdown-icon ion-ios-log-out"></span>
                  <span>Đăng xuất</span>
                </button>
              </form>
            </div>
          </div>
        @else
          <a href="{{ route('login') }}" class="nav-cta-secondary">Đăng nhập / Đăng ký</a>
        @endif
      </div>
    </div>
  </div>
</nav>
