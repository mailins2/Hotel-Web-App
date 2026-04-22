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
      $customerPoints = 120;
      $promotions = [
        [
          'Code' => 'PEACH01',
          'CanUse' => true,
          'StatusLabel' => 'Co the dung',
          'TenKM' => 'Giam 15% cho ky nghi cuoi tuan',
          'MoTa' => 'Ap dung cho dat phong Deluxe va Suite trong khung thu Sau den Chu Nhat.',
          'PhanTramGiamGia' => 15,
          'Diem' => 80,
          'NgayBatDau' => '2026-04-01',
          'NgayBatDauDisplay' => '01/04/2026',
          'NgayKetThuc' => '2026-05-31',
          'NgayKetThucDisplay' => '31/05/2026',
        ],
        [
          'Code' => 'PEACH02',
          'CanUse' => true,
          'StatusLabel' => 'Co the dung',
          'TenKM' => 'Giam 20% dich vu spa',
          'MoTa' => 'Danh cho khach hang thanh vien khi su dung spa va massage tai khuon vien khach san.',
          'PhanTramGiamGia' => 20,
          'Diem' => 100,
          'NgayBatDau' => '2026-04-10',
          'NgayBatDauDisplay' => '10/04/2026',
          'NgayKetThuc' => '2026-06-15',
          'NgayKetThucDisplay' => '15/06/2026',
        ],
      ];
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
                    {{ $promotion['NgayBatDauDisplay'] ?? '--/--/----' }}
                    -
                    {{ $promotion['NgayKetThucDisplay'] ?? '--/--/----' }}
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
