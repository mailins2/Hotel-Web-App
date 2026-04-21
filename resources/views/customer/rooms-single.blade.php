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

    @include('customer.partials.nav', ['active' => 'rooms'])
    <!-- END nav -->
		<div class="hero-wrap" data-bg-image="{{ Vite::asset('resources/customer/images/rooms.jpg') }}">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text d-flex align-itemd-center justify-content-center">
          <div class="col-md-9 ftco-animate text-center d-flex align-items-end justify-content-center">
          	<div class="text">
	            <h1 class="mb-4 bread">Chi Tiết Phòng</h1>
            </div>
          </div>
        </div>
      </div>
    </div>

    <section class="ftco-section">
      <div class="container">
        <div class="row">
          <div class="col-lg-8">
          	<div class="row">
          		<div class="col-md-12 ftco-animate">
          			<div class="single-slider owl-carousel">
          				<div class="item">
          					<div class="room-img" data-bg-image="{{ Vite::asset('resources/customer/images/deluxe_family1.jpg') }}"></div>
          				</div>
          				<div class="item">
          					<div class="room-img" data-bg-image="{{ Vite::asset('resources/customer/images/810491790.jpg') }}"></div>
          				</div>
          				<div class="item">
          					<div class="room-img" data-bg-image="{{ Vite::asset('resources/customer/images/810491789.jpg') }}"></div>
          				</div>
          			</div>
          		</div>
          		<div class="col-md-12 room-single mt-4 mb-5 ftco-animate">
          			<h2 class="mb-4">Phòng Deluxe Gia Đình</h2>
    						<p>Mô tả phòng: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras vitae lacus orci. Donec eu finibus ex. Aliquam vel neque nec eros pretium vulputate sed eu leo. Vestibulum quis neque ornare risus euismod mollis id at diam. Sed vel risus leo. Vestibulum cursus ipsum vel euismod maximus. Integer convallis auctor libero, iaculis fringilla odio molestie eu.</p>
    						<div class="d-md-flex mt-5 mb-5">
    							<ul class="list">
	    							<li><span>Số người ở:</span> 4 người</li>
	    						</ul>
    						</div>
    						<div class="room-amenities mt-5">
    							<h3 class="room-amenities-title">Tiện ích trong phòng</h3>
    							<div class="room-amenities-grid">
    								<div class="room-amenities-col">
									<div class="room-amenity-item"><span class="room-amenity-icon ion-ios-shirt"></span><span>Tủ quần áo</span></div>
									<div class="room-amenity-item"><span class="room-amenity-icon ion-logo-no-smoking"></span><span>Phòng không hút thuốc</span></div>
									<div class="room-amenity-item"><span class="room-amenity-icon ion-ios-snow"></span><span>Điều hòa</span></div>
									<div class="room-amenity-item"><span class="room-amenity-icon ion-ios-flashlight"></span><span>Máy sấy tóc</span></div>
    									<div class="room-amenity-item"><span class="room-amenity-icon ion-ios-wifi"></span><span>Wifi</span></div>
									<div class="room-amenity-item"><span class="room-amenity-icon ion-ios-square-outline"></span><span>Khăn tắm</span></div>
									<div class="room-amenity-item"><span class="room-amenity-icon ion-ios-bulb"></span><span>Đèn bàn</span></div>
    								</div>
    								<div class="room-amenities-col">
									<div class="room-amenity-item"><span class="room-amenity-icon ion-ios-bed"></span><span>Ga trải giường, gối</span></div>
									<div class="room-amenity-item"><span class="room-amenity-icon ion-ios-water"></span><span>Vòi sen</span></div>
									<div class="room-amenity-item"><span class="room-amenity-icon ion-ios-shirt"></span><span>Dịch vụ giặt ủi</span></div>
									<div class="room-amenity-item"><span class="room-amenity-icon ion-ios-water"></span><span>Phòng có bồn tắm</span></div>
									<div class="room-amenity-item"><span class="room-amenity-icon ion-ios-medkit"></span><span>Đồ phòng tắm</span></div>
									<div class="room-amenity-item"><span class="room-amenity-icon ion-ios-call"></span><span>Điện thoại</span></div>
									<div class="room-amenity-item"><span class="room-amenity-icon ion-ios-desktop"></span><span>Bàn làm việc</span></div>
    								</div>
    							</div>
    						</div>
          		</div>
          	</div>
          </div> <!-- .col-md-8 -->
          <div class="col-lg-4 sidebar ftco-animate pl-md-5">
            <div class="sidebar-box room-booking-card ftco-animate">
              <div class="room-booking-card-head">
                <p class="room-booking-card-label mb-2">Thông tin chính sách</p>
              </div>

              <div class="room-booking-info">
                <div class="room-booking-section">
                  <h4>Chính sách hủy phòng</h4>
                  <p>Hủy trước 10–15 ngày: Có thể được miễn phí hủy phòng.</p>
                  <p>Hủy trước 5–10 ngày: Có thể chịu 30%–70% phí đặt phòng.</p>
                  <p>Hủy trước 1–5 ngày: Có thể chịu 100% phí đặt phòng.</p>
                </div>
                <div class="room-booking-section">
                  <h4>Khung giờ nhận/trả phòng</h4>
                  <ul class="room-booking-times">
                    <li><span>Nhận phòng</span><strong>sau 14:00</strong></li>
                    <li><span>Trả phòng</span><strong>07:00 - 12:00</strong></li>
                  </ul>
                </div>
              </div>

              <div class="room-booking-actions">
                <a href="{{ route('customer.rooms-booking') }}" class="btn btn-primary room-booking-submit">
                  Đặt phòng
                </a>
              </div>

            </div>
          </div>
        </div>
      </div>
    </section> <!-- .section -->


    @include('customer.partials.footer')
    
  

  <!-- loader -->
  <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>

  </body>
</html>
