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

        <div class="promotion-card-grid">
          <article class="promotion-card ftco-animate">
            <div class="promotion-card-media">
              <div class="promotion-card-slider single-slider owl-carousel">
                <div class="promotion-card-slide" data-bg-image="{{ asset('customers/images/screen1.png') }}"></div>
                <div class="promotion-card-slide" data-bg-image="{{ asset('customers/images/screen2.png') }}"></div>
                <div class="promotion-card-slide" data-bg-image="{{ asset('customers/images/screen3.png') }}"></div>
              </div>
              <span class="promotion-card-discount">-15%</span>
            </div>

            <div class="promotion-card-body">
              <div class="promotion-card-code">PEACH01</div>
              <h3>Giảm 15% cho kỳ nghỉ cuối tuần</h3>
              <p class="promotion-card-description">
                Ưu đãi cho khách đặt phòng Deluxe và Suite trong thời gian cuối tuần.
              </p>

              <div class="promotion-card-actions">
                <span class="promotion-card-points">
                  <i aria-hidden="true"></i>
                  80 điểm
                </span>
                <button type="button" class="promotion-card-save">Lưu mã</button>
              </div>

              <div class="promotion-card-date">
                01/04/2026
                -
                31/05/2026
              </div>
            </div>
          </article>

          <article class="promotion-card ftco-animate">
            <div class="promotion-card-media">
              <div class="promotion-card-slider single-slider owl-carousel">
                <div class="promotion-card-slide" data-bg-image="{{ asset('customers/images/screen2.png') }}"></div>
                <div class="promotion-card-slide" data-bg-image="{{ asset('customers/images/screen3.png') }}"></div>
                <div class="promotion-card-slide" data-bg-image="{{ asset('customers/images/screen.png') }}"></div>
              </div>
              <span class="promotion-card-discount">-20%</span>
            </div>

            <div class="promotion-card-body">
              <div class="promotion-card-code">PEACH02</div>
              <h3>Combo Spa và Breakfast</h3>
              <p class="promotion-card-description">
                Tăng trải nghiệm thư giãn và bữa sáng tại nhà hàng cho khách lưu trú.
              </p>

              <div class="promotion-card-actions">
                <span class="promotion-card-points">
                  <i aria-hidden="true"></i>
                  100 điểm
                </span>
                <button type="button" class="promotion-card-save">Lưu mã</button>
              </div>

              <div class="promotion-card-date">
                10/04/2026
                -
                15/06/2026
              </div>
            </div>
          </article>

          <article class="promotion-card ftco-animate">
            <div class="promotion-card-media">
              <div class="promotion-card-slider single-slider owl-carousel">
                <div class="promotion-card-slide" data-bg-image="{{ asset('customers/images/screen3.png') }}"></div>
                <div class="promotion-card-slide" data-bg-image="{{ asset('customers/images/screen.png') }}"></div>
                <div class="promotion-card-slide" data-bg-image="{{ asset('customers/images/screen1.png') }}"></div>
              </div>
              <span class="promotion-card-discount">-10%</span>
            </div>

            <div class="promotion-card-body">
              <div class="promotion-card-code">PEACH03</div>
              <h3>Đặt sớm tiết kiệm hơn</h3>
              <p class="promotion-card-description">
                Áp dụng cho đặt phòng sớm trước 14 ngày với các hạng phòng tiêu chuẩn.
              </p>

              <div class="promotion-card-actions">
                <span class="promotion-card-points">
                  <i aria-hidden="true"></i>
                  60 điểm
                </span>
                <button type="button" class="promotion-card-save">Lưu mã</button>
              </div>

              <div class="promotion-card-date">
                15/04/2026
                -
                01/07/2026
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>

    @include('customer.partials.footer')

    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>
  </body>
</html>
