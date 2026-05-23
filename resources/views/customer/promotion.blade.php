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

    <div class="hero-wrap" data-bg-image="{{ asset('customers/images/screen.png') }}">
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

        @if ($promotions->count() > 0)
        <div class="promotion-card-grid">
          @foreach ($promotions as $promotion)
          <article class="promotion-card ftco-animate">
            <div class="promotion-card-media">
              @if ($promotion->hinhs && $promotion->hinhs->count() > 0)
              <div class="promotion-card-slider single-slider owl-carousel">
                @foreach ($promotion->hinhs as $image)
                <div class="promotion-card-slide" data-bg-image="{{ $image->Url }}"></div>
                @endforeach
              </div>
              @else
              <div class="promotion-card-slider single-slider owl-carousel">
                <div class="promotion-card-slide" data-bg-image="{{ asset('customers/images/screen.png') }}"></div>
              </div>
              @endif
              @if ($promotion->PhanTramGiamGia)
              <span class="promotion-card-discount">-{{ $promotion->PhanTramGiamGia }}%</span>
              @endif
            </div>

            <div class="promotion-card-body">
              <div class="promotion-card-code">{{ $promotion->MaKM }}</div>
              <h3>{{ $promotion->TenKM }}</h3>
              <p class="promotion-card-description">
                {{ $promotion->MoTa ?: 'Không có mô tả' }}
              </p>

              <div class="promotion-card-actions">
                @if ($promotion->Diem)
                <span class="promotion-card-points">
                  <i aria-hidden="true"></i>
                  {{ $promotion->Diem }} điểm
                </span>
                @endif
                <button type="button" class="promotion-card-save" onclick="copyToClipboard('{{ $promotion->MaKM }}')">Lưu mã</button>
              </div>

              @if ($promotion->NgayBatDau || $promotion->NgayKetThuc)
              <div class="promotion-card-date">
                {{ $promotion->NgayBatDau ? \Carbon\Carbon::parse($promotion->NgayBatDau)->format('d/m/Y') : '' }}
                @if ($promotion->NgayBatDau && $promotion->NgayKetThuc)
                -
                @endif
                {{ $promotion->NgayKetThuc ? \Carbon\Carbon::parse($promotion->NgayKetThuc)->format('d/m/Y') : '' }}
              </div>
              @endif
            </div>
          </article>
          @endforeach
        </div>
        @else
        <div class="row justify-content-center">
          <div class="col-md-8 text-center">
            <p class="text-muted">Hiện tại không có khuyến mãi nào.</p>
          </div>
        </div>
        @endif
      </div>
    </section>

    @push('scripts')
    <script>
      function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function () {
          alert('Mã khuyến mãi đã được sao chép: ' + text);
        }).catch(function (err) {
          console.error('Lỗi khi sao chép: ', err);
        });
      }
    </script>
    @endpush

    @include('customer.partials.footer')

    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>
  </body>
</html>
