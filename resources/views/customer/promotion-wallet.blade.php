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

    <section class="customer-account-section">
      <div class="container">
        <div class="customer-account-shell">
          @include('customer.partials.account-sidebar', ['active' => 'promotions'])

          <main class="customer-account-main">
            <div class="customer-account-heading">
              <div class="eyebrow">Kho khuyến mãi</div>
              <p>Dùng điểm tích lũy để lưu và áp dụng mã khuyến mãi khi đặt phòng tại Peach Valley.</p>
            </div>

            <div class="customer-points-panel">
              <div>
                <span>Điểm hiện có</span>
                <strong>{{ (int) ($customer->DIEM ?? 0) }}</strong>
              </div>
              <p>Điểm sẽ được cộng sau mỗi đơn đặt phòng hoàn tất.</p>
            </div>

            <div class="customer-promo-grid">
              @forelse ($promotionWalletItems as $walletItem)
                @php
                  $promotion = $walletItem->khuyenMai;
                  $status = (int) ($walletItem->TrangThai ?? 0);
                  $isExpired = $promotion?->NgayKetThuc && \Illuminate\Support\Carbon::parse($promotion->NgayKetThuc)->endOfDay()->isPast();
                  $statusLabel = $isExpired || $status === 2
                    ? 'Hết hạn'
                    : ($status === 1 ? 'Đã sử dụng' : 'Có thể dùng');
                  $statusClass = $isExpired || $status === 2
                    ? 'is-expired'
                    : ($status === 1 ? 'is-used' : 'is-usable');
                @endphp

                <article class="customer-promo-card {{ $statusClass }}">
                  <div class="customer-promo-top">
                    <span class="customer-promo-code">{{ $walletItem->MaKM }}</span>
                    <span class="customer-promo-status">{{ $statusLabel }}</span>
                  </div>
                  <h3>{{ $promotion->TenKM ?? 'Mã khuyến mãi' }}</h3>
                  <p class="customer-promo-description">{{ $promotion->MoTa ?: 'Không có mô tả' }}</p>
                  <div class="customer-promo-value-row">
                    <span class="customer-promo-discount">{{ (float) ($promotion->PhanTramGiamGia ?? 0) }}%</span>
                    <span class="customer-promo-points">{{ (int) ($promotion->Diem ?? 0) }} điểm</span>
                  </div>
                  @if ($promotion?->NgayBatDau || $promotion?->NgayKetThuc)
                    <div class="customer-promo-date">
                      {{ $promotion?->NgayBatDau ? \Illuminate\Support\Carbon::parse($promotion->NgayBatDau)->format('d/m/Y') : '' }}
                      @if ($promotion?->NgayBatDau && $promotion?->NgayKetThuc)
                        -
                      @endif
                      {{ $promotion?->NgayKetThuc ? \Illuminate\Support\Carbon::parse($promotion->NgayKetThuc)->format('d/m/Y') : '' }}
                    </div>
                  @endif
                  <button
                    type="button"
                    class="customer-promo-action"
                    @disabled($status !== 0 || $isExpired)
                    onclick="navigator.clipboard.writeText('{{ $walletItem->MaKM }}')"
                  >Sao chép mã</button>
                </article>
              @empty
                <div class="customer-empty">Bạn chưa lưu mã khuyến mãi nào.</div>
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
