@php
  $promotions = [
    [
      'Code' => 'PEACH01',
      'TenKM' => 'Giam 15% cho ky nghi cuoi tuan',
      'MoTa' => 'Uu dai cho khach dat phong Deluxe va Suite trong thoi gian cuoi tuan.',
      'PhanTramGiamGia' => 15,
      'Diem' => 80,
      'NgayBatDau' => '2026-04-01',
      'NgayBatDauDisplay' => '01/04/2026',
      'NgayKetThuc' => '2026-05-31',
      'NgayKetThucDisplay' => '31/05/2026',
      'Images' => [
        'resources/customer/images/screen1.png',
        'resources/customer/images/screen2.png',
        'resources/customer/images/screen3.png',
      ],
    ],
    [
      'Code' => 'PEACH02',
      'TenKM' => 'Combo Spa va Breakfast',
      'MoTa' => 'Tang trai nghiem thu gian va bua sang tai nha hang cho khach luu tru.',
      'PhanTramGiamGia' => 20,
      'Diem' => 100,
      'NgayBatDau' => '2026-04-10',
      'NgayBatDauDisplay' => '10/04/2026',
      'NgayKetThuc' => '2026-06-15',
      'NgayKetThucDisplay' => '15/06/2026',
      'Images' => [
        'resources/customer/images/screen2.png',
        'resources/customer/images/screen3.png',
        'resources/customer/images/screen.png',
      ],
    ],
    [
      'Code' => 'PEACH03',
      'TenKM' => 'Dat som tiet kiem hon',
      'MoTa' => 'Ap dung cho dat phong som truoc 14 ngay voi cac hang phong tieu chuan.',
      'PhanTramGiamGia' => 10,
      'Diem' => 60,
      'NgayBatDau' => '2026-04-15',
      'NgayBatDauDisplay' => '15/04/2026',
      'NgayKetThuc' => '2026-07-01',
      'NgayKetThucDisplay' => '01/07/2026',
      'Images' => [
        'resources/customer/images/screen3.png',
        'resources/customer/images/screen.png',
        'resources/customer/images/screen1.png',
      ],
    ],
  ];
@endphp

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
  <body>

    @include('customer.partials.nav', ['active' => 'promotion'])

    <div class="hero-wrap" data-bg-image="{{ Vite::asset('resources/customer/images/screen.png') }}">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text d-flex align-itemd-center justify-content-center">
          <div class="col-md-9 ftco-animate text-center d-flex align-items-end justify-content-center">
            <div class="text">
              <h1 class="mb-4 bread">Khuyến mãi</h1>
            </div>
          </div>
        </div>
      </div>
    </div>

    <section class="ftco-section customer-promotion-page">
      <div class="container">
        <div class="row justify-content-center mb-5 pb-3">
          <div class="col-md-8 heading-section text-center ftco-animate">
            <span class="subheading">Ưu đãi Peach Valley</span>
            <h2 class="mb-4">Chọn ưu đãi cho kỳ nghỉ của bạn</h2>
          </div>
        </div>

        <div class="promotion-card-grid">
          @forelse ($promotions as $promotion)
            <article class="promotion-card ftco-animate">
              <div class="promotion-card-media">
                <div class="promotion-card-slider single-slider owl-carousel">
                  @foreach ($promotion['Images'] as $image)
                    <div class="promotion-card-slide" data-bg-image="{{ Vite::asset($image) }}"></div>
                  @endforeach
                </div>
                <span class="promotion-card-discount">
                  -{{ number_format((float) ($promotion['PhanTramGiamGia'] ?? 0), 0, ',', '.') }}%
                </span>
              </div>

              <div class="promotion-card-body">
                <div class="promotion-card-code">{{ $promotion['Code'] }}</div>
                <h3>{{ $promotion['TenKM'] ?? 'Khuyến mãi Peach Valley' }}</h3>
                <p class="promotion-card-description">
                  {{ $promotion['MoTa'] ?? 'Ưu đãi dành cho khách hàng Peach Valley.' }}
                </p>

                <div class="promotion-card-actions">
                  <span class="promotion-card-points">
                    <i aria-hidden="true"></i>
                    {{ number_format((int) ($promotion['Diem'] ?? 0), 0, ',', '.') }} điểm
                  </span>
                  <button type="button" class="promotion-card-save">Lưu mã</button>
                </div>

                <div class="promotion-card-date">
                  {{ $promotion['NgayBatDauDisplay'] ?? '--/--/----' }}
                  -
                  {{ $promotion['NgayKetThucDisplay'] ?? '--/--/----' }}
                </div>
              </div>
            </article>
          @empty
            <div class="customer-empty">Hiện chưa có chương trình khuyến mãi nào.</div>
          @endforelse
        </div>
      </div>
    </section>

    @include('customer.partials.footer')

    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>
  </body>
</html>
