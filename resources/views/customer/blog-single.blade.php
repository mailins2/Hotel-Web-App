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

    @include('customer.partials.nav', ['active' => 'blog'])
    <!-- END nav -->
		<div class="hero-wrap" data-bg-image="{{ asset('customers/images/rooms.jpg') }}">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text d-flex align-itemd-center justify-content-center">
          <div class="col-md-9 ftco-animate text-center d-flex align-items-end justify-content-center">
          	<div class="text">
	            <h1 class="mb-4 bread">Về Peach Valley</h1>
            </div>
          </div>
        </div>
      </div>
    </div>

    <section class="ftco-section ftco-degree-bg">
      <div class="container">
        <div class="row">
          <div class="col-lg-8 ftco-animate">
            <h2 class="mb-3">Khách sạn Peach Valley</h2>
            <p>
              Khách sạn Peach Valley là một khách sạn 3.0 sao tọa lạc tại Đà Lạt, Việt Nam. Với vị trí thuận lợi và tiện nghi hiện đại, khách sạn này là lựa chọn lý tưởng cho những du khách muốn khám phá vẻ đẹp của thành phố hoa Đà Lạt. Với khoảng cách chỉ 0.2km từ trung tâm thành phố, Khách sạn Peach Valley mang đến sự thuận tiện cho việc khám phá các điểm tham quan nổi tiếng trong thành phố. Ngoài ra, khách sạn cũng chỉ cách sân bay 35 phút đi xe, giúp du khách dễ dàng di chuyển đến và đi về. Khách sạn Peach Valley đảm bảo sự riêng tư và thoải mái cho du khách. Với thiết kế hiện đại và trang bị đầy đủ tiện nghi, mỗi phòng đều mang lại không gian ấm cúng và thoải mái cho du khách sau một ngày dạo chơi và khám phá Đà Lạt.
            </p>
            <p>
              <img src="{{ asset('customers/images/blog.jpg') }}" alt="" class="img-fluid">
            </p>
            <p>Khách sạn Peach Valley tại Đà Lạt cung cấp nhiều tiện nghi giải trí và tiện lợi cho du khách. Về giải trí, khách sạn có spa hiện đại, dịch vụ mát-xa chuyên nghiệp và khu vực sinh hoạt chung có TV, sofa để thư giãn, xem phim, gặp gỡ bạn bè. Về tiện ích, khách sạn có giặt ủi, dịch vụ phòng, lễ tân 24/7, Wi-Fi miễn phí toàn khách sạn, giữ hành lý, dọn phòng hằng ngày, cửa hàng tiện lợi, khu hút thuốc, lò sưởi và khu giặt đồ, giúp khách lưu trú thoải mái và thuận tiện hơn.</p>
          </div>

        </div>
      </div>
    </section> <!-- .section -->

    @include('customer.partials.footer')
    
  

  <!-- loader -->
  <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>

  </body>
</html>
