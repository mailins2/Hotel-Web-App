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

            <!-- <div class="customer-points-panel">
              <div>
                <span>Điểm hiện có</span>
                <strong>120</strong>
              </div>
              <p>Điểm sẽ được cộng sau mỗi đơn đặt phòng hoàn tất.</p>
            </div> -->

            <div class="customer-promo-grid">
              <article class="customer-promo-card is-usable">
                <div class="customer-promo-top">
                  <span class="customer-promo-code">PEACH01</span>
                  <span class="customer-promo-status">Có thể dùng</span>
                </div>
                <h3>Giảm 15% cho kỳ nghỉ cuối tuần</h3>
                <p class="customer-promo-description">Áp dụng cho đặt phòng Deluxe và Suite trong khung thứ Sáu đến Chủ Nhật.</p>
                <div class="customer-promo-value-row">
                  <span class="customer-promo-discount">15%</span>
                  <span class="customer-promo-points">80 điểm</span>
                </div>
                <div class="customer-promo-date">
                  01/04/2026
                  -
                  31/05/2026
                </div>
                <button type="button" class="customer-promo-action">Dùng mã này</button>
              </article>

              <article class="customer-promo-card is-usable">
                <div class="customer-promo-top">
                  <span class="customer-promo-code">PEACH02</span>
                  <span class="customer-promo-status">Có thể dùng</span>
                </div>
                <h3>Giảm 20% dịch vụ spa</h3>
                <p class="customer-promo-description">Dành cho khách hàng thành viên khi sử dụng spa và massage tại khuôn viên khách sạn.</p>
                <div class="customer-promo-value-row">
                  <span class="customer-promo-discount">20%</span>
                  <span class="customer-promo-points">100 điểm</span>
                </div>
                <div class="customer-promo-date">
                  10/04/2026
                  -
                  15/06/2026
                </div>
                <button type="button" class="customer-promo-action">Dùng mã này</button>
              </article>
            </div>
          </main>
        </div>
      </div>
    </section>

    @include('customer.partials.footer')
    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>
  </body>
</html>
