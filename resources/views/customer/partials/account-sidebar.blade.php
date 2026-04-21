@php
  $accountActive = $active ?? '';
  $accountLinks = [
    [
      'key' => 'profile',
      'label' => 'Thông tin cá nhân',
      'route' => route('customer.profile'),
      'icon' => 'ion-ios-person',
    ],
    [
      'key' => 'bookings',
      'label' => 'Đặt phòng của bạn',
      'route' => route('customer.my-bookings'),
      'icon' => 'ion-ios-briefcase',
    ],
    [
      'key' => 'promotions',
      'label' => 'Kho khuyến mãi',
      'route' => route('customer.promotion-wallet'),
      'icon' => 'ion-ios-pricetag',
    ],
  ];
@endphp

<aside class="customer-account-sidebar" aria-label="Tài khoản khách hàng">
  <nav class="customer-account-menu">
    @foreach ($accountLinks as $accountLink)
      <a
        href="{{ $accountLink['route'] }}"
        class="customer-account-menu-item{{ $accountActive === $accountLink['key'] ? ' is-active' : '' }}"
      >
        <span class="customer-account-menu-icon {{ $accountLink['icon'] }}"></span>
        <span>{{ $accountLink['label'] }}</span>
      </a>
    @endforeach
  </nav>
</aside>
