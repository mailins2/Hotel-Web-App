<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Peach Valley</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_hotel.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:200,300,400,600,700&display=swap" rel="stylesheet">
    @vite(['resources/customer/css/site.css', 'resources/customer/js/site.js'])
  </head>
  <body class="booking-page customer-account-page">
    @include('customer.partials.nav')

    @php
      $user = mockUser() ?? [];
      $account = collect(config('hotel-management.modules.accounts.records', []))->firstWhere('Email', $user['email'] ?? '');
      $customer = $account
        ? collect(config('hotel-management.modules.customers.records', []))->firstWhere('MaTK', $account['MaTK'] ?? null)
        : null;
      $customerPoints = (int) (session('customer_profile.points') ?? $customer['Diem'] ?? 0);
      $today = now()->toDateString();
      $promotions = collect(config('hotel-management.modules.promotions.records', []))
        ->map(function ($promotion, $index) use ($customerPoints, $today) {
          $requiredPoints = (int) ($promotion['Diem'] ?? 0);
          $startsAt = $promotion['NgayBatDau'] ?? null;
          $endsAt = $promotion['NgayKetThuc'] ?? null;
          $isActive = (! $startsAt || $startsAt <= $today) && (! $endsAt || $endsAt >= $today);
          $canUse = $isActive && $customerPoints >= $requiredPoints;

          return array_merge($promotion, [
            'Code' => 'PEACH' . str_pad((string) ($promotion['MaKM'] ?? $index + 1), 2, '0', STR_PAD_LEFT),
            'IsActive' => $isActive,
            'CanUse' => $canUse,
            'StatusLabel' => $canUse ? 'Có thể dùng' : ($isActive ? 'Cần thêm điểm' : 'Chưa đến hạn'),
          ]);
        })
        ->filter(fn ($promotion) => $promotion['CanUse'])
        ->values();
    @endphp

    <section class="customer-account-section">
      <div class="container">
        <div class="customer-account-shell">
          @include('customer.partials.account-sidebar', ['active' => 'promotions'])

          <main class="customer-account-main">
            <div class="customer-account-heading">
              <div class="eyebrow">Kho khuyến mãi</div>
              <p>Dùng điểm tích lũy để lưu và áp dụng mã khuyến mãi khi đặt phòng tại Peach Valley.</p>
            </div>

            <!-- <div class="customer-points-panel">
              <div>
                <span>Điểm hiện có</span>
                <strong>{{ number_format($customerPoints, 0, ',', '.') }}</strong>
              </div>
              <p>Điểm sẽ được cộng sau mỗi đơn đặt phòng hoàn tất.</p>
            </div> -->

            <div class="customer-promo-grid">
              @forelse ($promotions as $promotion)
                <article class="customer-promo-card{{ $promotion['CanUse'] ? ' is-usable' : '' }}">
                  <div class="customer-promo-top">
                    <span class="customer-promo-code">{{ $promotion['Code'] }}</span>
                    <span class="customer-promo-status">{{ $promotion['StatusLabel'] }}</span>
                  </div>
                  <h3>{{ $promotion['TenKM'] ?? 'Khuyến mãi' }}</h3>
                  <p class="customer-promo-description">{{ $promotion['MoTa'] ?? 'Ưu đãi dành cho khách hàng Peach Valley.' }}</p>
                  <div class="customer-promo-value-row">
                    <span class="customer-promo-discount">{{ number_format((float) ($promotion['PhanTramGiamGia'] ?? 0), 0, ',', '.') }}%</span>
                    <span class="customer-promo-points">
                      {{ number_format((int) ($promotion['Diem'] ?? 0), 0, ',', '.') }} điểm
                    </span>
                  </div>
                  <div class="customer-promo-date">
                    {{ $promotion['NgayBatDau'] ? \Carbon\Carbon::parse($promotion['NgayBatDau'])->format('d/m/Y') : '--/--/----' }}
                    -
                    {{ $promotion['NgayKetThuc'] ? \Carbon\Carbon::parse($promotion['NgayKetThuc'])->format('d/m/Y') : '--/--/----' }}
                  </div>
                  <button type="button" class="customer-promo-action">Dùng mã này</button>
                </article>
              @empty
                <div class="customer-empty">Hiện chưa có mã khuyến mãi nào trong kho.</div>
              @endforelse
            </div>
          </main>
        </div>
      </div>
    </section>

    @include('customer.partials.footer')
    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>
  </body>
</html>
