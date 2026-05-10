<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Peach Valley</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_hotel.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:200,300,400,600,700&display=swap" rel="stylesheet">
    <style>
      .home-slider .slider-item .slider-text h1 {
		color: #854023;
      }
    </style>

    @vite(['resources/customer/css/site.css', 'resources/customer/js/site.js'])
  </head>
  <body>

    @include('customer.partials.nav', ['active' => 'home'])
    <!-- END nav -->
		<div class="hero">
	    <section class="home-slider owl-carousel">
	      <div class="slider-item" data-bg-image="{{ asset('customers/images/siderbar1.jpg') }}">
	      	<div class="overlay"></div>
	        <div class="container">
	          <div class="row no-gutters slider-text align-items-center justify-content-end">
	          <div class="col-md-6 ftco-animate">
	          	<div class="text">
	          		<h2 style="color:#8c4a34;">Tận hưởng kì nghỉ cùng chúng tôi</h2>
		            <h1 class="mb-3">Peach Valley đồng hành cùng bạn</h1>
	            </div>
	          </div>
	        </div>
	        </div>
	      </div>

	      <div class="slider-item" data-bg-image="{{ asset('customers/images/siderbar2.jpg') }}">
	      	<div class="overlay"></div>
	        <div class="container">
	          <div class="row no-gutters slider-text align-items-center justify-content-end">
	          <div class="col-md-6 ftco-animate">
	          	<div class="text">
	          		<h2 style="color:#8c4a34;">Khách sạn peach valley</h2>
		            <h1 class="mb-3">Cảm giác như ở trong ngôi nhà của bạn</h1>
	            </div>
	          </div>
	        </div>
	        </div>
	      </div>
	    </section>
	  </div>

    <section class="ftco-booking ftco-section ftco-no-pt ftco-no-pb">
    	<div class="container">
    		<div class="row no-gutters">
    			<div class="col-lg-12">
    				<form action="{{ route('customer.rooms-booking') }}" method="GET" class="booking-form aside-stretch" data-home-booking-search>
	        		<div class="row">
	        			<div class="col-md d-flex py-md-4">
	        				<div class="form-group align-self-stretch d-flex align-items-end">
	        					<div class="wrap align-self-stretch py-3 px-4">
				    					<label for="#">Ngày nhận</label>
				    					<input type="text" name="checkIn" class="form-control checkin_date" placeholder="Chọn ngày">
			    					</div>
			    				</div>
	        			</div>
	        			<div class="col-md d-flex py-md-4">
	        				<div class="form-group align-self-stretch d-flex align-items-end">
	        					<div class="wrap align-self-stretch py-3 px-4">
				    					<label for="#">Ngày trả</label>
				    					<input type="text" name="checkOut" class="form-control checkout_date" placeholder="Chọn ngày">
			    					</div>
			    				</div>
	        			</div>
	        			<div class="col-md d-flex py-md-4">
	        				<div class="form-group align-self-stretch d-flex align-items-end">
	        					<div class="wrap align-self-stretch py-3 px-4">
			      					<label for="#">Phòng</label>
			      					<div class="form-field">
			        					<div class="select-wrap">
			                    <div class="icon"><span class="ion-ios-arrow-down"></span></div>
			                    <select name="" id="" class="form-control">
			                    	<option value="">Suite</option>
			                      <option value="">Family Room</option>
			                      <option value="">Deluxe Room</option>
			                      <option value="">Classic Room</option>
			                      <option value="">Superior Room</option>
			                      <option value="">Luxury Room</option>
			                    </select>
			                  </div>
				              </div>
				            </div>
		              </div>
	        			</div>
	        			<div class="col-md d-flex py-md-4">
	        				<div class="form-group align-self-stretch d-flex align-items-end">
	        					<div class="wrap align-self-stretch py-3 px-4">
			      					<label for="#">Số khách</label>
			      					<div class="form-field">
			        					<div class="select-wrap">
			                    <div class="icon"><span class="ion-ios-arrow-down"></span></div>
			                    <select name="" id="" class="form-control">
			                    	<option value="">1 Adult</option>
			                      <option value="">2 Adult</option>
			                      <option value="">3 Adult</option>
			                      <option value="">4 Adult</option>
			                      <option value="">5 Adult</option>
			                      <option value="">6 Adult</option>
			                    </select>
			                  </div>
				              </div>
				            </div>
		              </div>
	        			</div>
	        			<div class="col-md d-flex">
	        				<div class="form-group d-flex align-self-stretch">
			              <a href="{{ route('customer.rooms-booking') }}" class="btn btn-primary py-5 py-md-3 px-4 align-self-stretch d-block" data-home-booking-search-submit><span>Tìm kiếm</span></a>
			            </div>
	        			</div>
	        		</div>
	        	</form>
	    		</div>
    		</div>
    	</div>
    </section>


		<section class="ftco-section home-promotion-section">
      <div class="container">
      	<div class="row justify-content-center mb-5 pb-3">
          <div class="col-md-7 heading-section text-center ftco-animate">
          	<span class="subheading">Chào mừng đến với Peach Valley</span>
            <h2 class="mb-4">Bạn sẽ không bao giờ muốn rời đi</h2>
          </div>
        </div>  
        <div class="home-promotion-slider single-slider owl-carousel ftco-animate">
          <a class="home-promotion-slide" href="{{ route('customer.promotion') }}" data-bg-image="{{ asset('customers/images/screen1.png') }}" aria-label="Xem khuyến mãi Peach Valley"></a>
          <a class="home-promotion-slide" href="{{ route('customer.promotion') }}" data-bg-image="{{ asset('customers/images/screen2.png') }}" aria-label="Xem khuyến mãi Peach Valley"></a>
          <a class="home-promotion-slide" href="{{ route('customer.promotion') }}" data-bg-image="{{ asset('customers/images/screen3.png') }}" aria-label="Xem khuyến mãi Peach Valley"></a>
          <a class="home-promotion-slide" href="{{ route('customer.promotion') }}" data-bg-image="{{ asset('customers/images/screen.png') }}" aria-label="Xem khuyến mãi Peach Valley"></a>
        </div>
      </div>
    </section>

    <section class="ftco-section ftco-wrap-about ftco-no-pt ftco-no-pb">
			<div class="container">
				<div class="row no-gutters">
					<div class="col-md-7 order-md-last d-flex">
						<div class="img img-1 mr-md-2 ftco-animate" data-bg-image="{{ asset('customers/images/home2.jpg') }}"></div>
						<div class="img img-2 ml-md-2 ftco-animate" data-bg-image="{{ asset('customers/images/home3.jpg') }}"></div>
					</div>
					<div class="col-md-5 wrap-about pb-md-3 ftco-animate pr-md-5 pb-md-5 pt-md-4">
	          <div class="heading-section mb-4 my-5 my-md-0">
	          	<span class="subheading">Giới thiệu về Peach Valley</span>
	            <h2 class="mb-4">Khách Sạn Peach Valley là lựa chọn của nhiều du khách khi đến Đà Lạt</h2>
	          </div>
	          <p>Peach Valley mang đến không gian nghỉ dưỡng tinh tế và ấm áp, với phòng nghỉ rộng rãi, gam màu dịu mắt, chăn ga mềm mại tạo cảm giác thư giãn như được vỗ về. Ban công rộng mở đón nắng mai, kết hợp cùng ánh đèn vàng dịu nhẹ, tạo nên bầu không khí yên bình và dễ chịu mỗi khi chiều xuống.</p>
	          <p><a href="{{ route('customer.rooms-booking') }}" class="btn btn-secondary rounded">Đặt Phòng Ngay Bây Giờ</a></p>
					</div>
				</div>
			</div>
		</section>

    <section class="testimony-section">
      <div class="container">
        <div class="row no-gutters ftco-animate justify-content-center">
        	<div class="col-md-5 d-flex">
        		<div class="testimony-img aside-stretch-2" data-bg-image="{{ asset('customers/images/view1.jpg') }}"></div>
        	</div>
	          <div class="col-md-7 py-5 pl-md-5">
	          	<div class="py-md-5">
		          	<div class="heading-section ftco-animate mb-4">
		          		<span class="subheading">Đánh giá</span>
			        </div>
	            <div class="carousel-testimony owl-carousel ftco-animate">
              <div class="item">
                <a href="#customer-reviews" class="customer-review-slide-link">
                  <div class="testimony-wrap pb-4">
                    <div class="customer-review-stars mb-3" aria-label="5 sao">
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                    </div>
                    <div class="text">
                      <p class="mb-3 customer-review-clamp">Khach san co khong gian tinh te va sang trong. Nhan vien phuc vu chu dao, phong family rong rai va sach se.</p>
                      <span class="customer-review-room">Loai phong: Family Room</span>
                    </div>
                    <div class="d-flex align-items-center mt-4">
                      <span class="customer-review-user-icon"><i class="ion-ios-person"></i></span>
                      <div class="pos ml-3">
                        <p class="name">Le Bao Chau</p>
                        <span class="position">08/04/2026</span>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
              <div class="item">
                <a href="#customer-reviews" class="customer-review-slide-link">
                  <div class="testimony-wrap pb-4">
                    <div class="customer-review-stars mb-3" aria-label="5 sao">
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                    </div>
                    <div class="text">
                      <p class="mb-3 customer-review-clamp">Phong suite co view dep, giuong em va bua sang da dang. Dich vu phong phan hoi nhanh.</p>
                      <span class="customer-review-room">Loai phong: Premium Suite</span>
                    </div>
                    <div class="d-flex align-items-center mt-4">
                      <span class="customer-review-user-icon"><i class="ion-ios-person"></i></span>
                      <div class="pos ml-3">
                        <p class="name">Pham Minh Khoa</p>
                        <span class="position">07/04/2026</span>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
              <div class="item">
                <a href="#customer-reviews" class="customer-review-slide-link">
                  <div class="testimony-wrap pb-4">
                    <div class="customer-review-stars mb-3" aria-label="4 sao">
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star-outline"></span>
                    </div>
                    <div class="text">
                      <p class="mb-3 customer-review-clamp">Phong deluxe sach se, tien nghi day du va vi tri thuan tien cho ky nghi ngan ngay.</p>
                      <span class="customer-review-room">Loai phong: Deluxe Room</span>
                    </div>
                    <div class="d-flex align-items-center mt-4">
                      <span class="customer-review-user-icon"><i class="ion-ios-person"></i></span>
                      <div class="pos ml-3">
                        <p class="name">Tran Nhat Linh</p>
                        <span class="position">06/04/2026</span>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="ftco-section ftco-no-pb ftco-room">
    	<div class="container-fluid px-0">
    		<div class="row no-gutters justify-content-center mb-5 pb-3">
          <div class="col-md-7 heading-section text-center ftco-animate">
          	<span class="subheading">Phòng Peach Valley</span>
            <h2 class="mb-4">Các loại phòng của khách sạn</h2>
          </div>
        </div>  
    		<div class="row no-gutters">
    			<div class="col-lg-6">
    				<div class="room-wrap d-md-flex ftco-animate">
    					<a href="#" class="img" data-bg-image="{{ asset('customers/images/deluxe_family.jpg') }}"></a>
    					<div class="half left-arrow d-flex align-items-center">
    						<div class="text p-4 text-center">
								<h3 class="mb-3"><a href="{{ route('customer.rooms') }}">Phòng Deluxe Gia Đình</a></h3> 
								<p class="room-description mb-3">Khong gian gon gang, day du tien nghi va phu hop cho ky nghi ngan ngay hoac chuyen cong tac.</p>
    							<p class="mb-0"><span class="price mr-1">1.000.000</span> <span class="per">VNĐ/Đêm</span></p> 
	    						<p class="pt-1"><a href="room-single.html" class="btn-custom px-3 py-2 rounded">Chi Tiết <span class="icon-long-arrow-right"></span></a></p>
    						</div>
    					</div>
    				</div>
    			</div>
    			<div class="col-lg-6">
    				<div class="room-wrap d-md-flex ftco-animate">
    					<a href="#" class="img" data-bg-image="{{ asset('customers/images/suite_junior.jpg') }}"></a>
    					<div class="half left-arrow d-flex align-items-center">
    						<div class="text p-4 text-center">
    							<h3 class="mb-3"><a href="{{ route('customer.rooms') }}">Phòng Suite Junior</a></h3>
								<p class="room-description mb-3">Khong gian gon gang, day du tien nghi va phu hop cho ky nghi ngan ngay hoac chuyen cong tac.</p>
    							<p class="mb-0"><span class="price mr-1">800.000</span> <span class="per">VNĐ/Đêm</span></p>
	    						<p class="pt-1"><a href="room-single.html" class="btn-custom px-3 py-2 rounded">Chi Tiết <span class="icon-long-arrow-right"></span></a></p>
    						</div>
    					</div>
    				</div>
    			</div>

    			<div class="col-lg-6">
    				<div class="room-wrap d-md-flex ftco-animate">
    					<a href="#" class="img order-md-last" data-bg-image="{{ asset('customers/images/suite.jpg') }}"></a>
    					<div class="half right-arrow d-flex align-items-center">
    						<div class="text p-4 text-center">
    							<h3 class="mb-3"><a href="{{ route('customer.rooms') }}">Phòng Suite</a></h3>
								<p class="room-description mb-3">Khong gian gon gang, day du tien nghi va phu hop cho ky nghi ngan ngay hoac chuyen cong tac.</p> 
    							<p class="mb-0"><span class="price mr-1">2.000.000</span> <span class="per">VNĐ/Đêm</span></p>
	    						<p class="pt-1"><a href="room-single.html" class="btn-custom px-3 py-2 rounded">Chi Tiết <span class="icon-long-arrow-right"></span></a></p>
    						</div>
    					</div>
    				</div>
    			</div>
    			<div class="col-lg-6">
    				<div class="room-wrap d-md-flex ftco-animate">
    					<a href="#" class="img order-md-last" data-bg-image="{{ asset('customers/images/superior.jpg') }}"></a>
    					<div class="half right-arrow d-flex align-items-center">
    						<div class="text p-4 text-center">
    							<h3 class="mb-3"><a href="{{ route('customer.rooms') }}">Phòng Superior</a></h3>
								<p class="room-description mb-3">Khong gian gon gang, day du tien nghi va phu hop cho ky nghi ngan ngay hoac chuyen cong.</p>
    							<p class="mb-0"><span class="price mr-1">900.000</span> <span class="per">VNĐ/Đêm</span></p>
	    						<p class="pt-1"><a href="room-single.html" class="btn-custom px-3 py-2 rounded">Chi Tiết <span class="icon-long-arrow-right"></span></a></p>
    						</div>
    					</div>
    				</div>
    			</div>

    		</div>
    	</div>
    </section>

	  <section id="customer-reviews" class="ftco-section customer-reviews-section" data-customer-reviews>
      <div class="container">
        <div class="row justify-content-center mb-5 pb-3">
          <div class="col-md-7 heading-section text-center ftco-animate">
          	<span class="subheading">Đánh giá của Peach Valley</span>
            <h2 class="mb-4">Nhận xét từ khách hàng</h2>
          </div>
        </div>

        <div class="customer-reviews-shell">
          <aside class="customer-review-overview ftco-animate">
            <span>Tổng quan đánh giá</span>
            <div class="customer-review-score">
              <strong>4.1</strong>
              <em>/ 5</em>
            </div>
            <div class="customer-review-stars" aria-label="4.1 sao trung binh">
              <span class="ion-ios-star"></span>
              <span class="ion-ios-star"></span>
              <span class="ion-ios-star"></span>
              <span class="ion-ios-star"></span>
              <span class="ion-ios-star-outline"></span>
            </div>
            <p>Dua tren 10 danh gia tu khach hang thuc te</p>

            <div class="customer-review-bars">
              <div class="customer-review-bar-row">
                <span>5</span>
                <div style="--review-percent: 40;"><i></i></div>
                <strong>40%</strong>
              </div>
              <div class="customer-review-bar-row">
                <span>4</span>
                <div style="--review-percent: 40;"><i></i></div>
                <strong>40%</strong>
              </div>
              <div class="customer-review-bar-row">
                <span>3</span>
                <div style="--review-percent: 10;"><i></i></div>
                <strong>10%</strong>
              </div>
              <div class="customer-review-bar-row">
                <span>2</span>
                <div style="--review-percent: 10;"><i></i></div>
                <strong>10%</strong>
              </div>
              <div class="customer-review-bar-row">
                <span>1</span>
                <div style="--review-percent: 0;"><i></i></div>
                <strong>0%</strong>
              </div>
            </div>
          </aside>

          <div class="customer-reviews-content">
            <div class="customer-review-filters ftco-animate">
              <label>
                <span>Số sao</span>
                <select data-review-rating-filter>
                  <option value="">Tất cả số sao</option>
                  <option value="5">5 sao</option>
                  <option value="4">4 sao</option>
                  <option value="3">3 sao</option>
                  <option value="2">2 sao</option>
                  <option value="1">1 sao</option>
                </select>
              </label>
              <label>
                <span>Loại phòng</span>
                <select data-review-room-filter>
                  <option value="">Tất cả loại phòng</option>
                  <option value="Family Room">Family Room</option>
                  <option value="Premium Suite">Premium Suite</option>
                  <option value="Deluxe Room">Deluxe Room</option>
                  <option value="Suite Room">Suite Room</option>
                  <option value="Deluxe Garden">Deluxe Garden</option>
                </select>
              </label>
            </div>

            <div class="customer-review-list" data-review-list>
              <article class="customer-review-list-item ftco-animate" data-review-item data-review-rating="5" data-review-room="Family Room">
                <div class="customer-review-avatar"><i class="ion-ios-person"></i></div>
                <div class="customer-review-list-body">
                  <div class="customer-review-list-head">
                    <div>
                      <h3>Le Bao Chau</h3>
                      <time datetime="2026-04-08">08/04/2026</time>
                    </div>
                    <div class="customer-review-stars" aria-label="5 sao">
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                    </div>
                  </div>
                  <p class="customer-review-description" title="Khach san co khong gian tinh te va sang trong. Nhan vien phuc vu chu dao, phong family rong rai va sach se.">Khach san co khong gian tinh te va sang trong. Nhan vien phuc vu chu dao, phong family rong rai va sach se.</p>
                  <span class="customer-review-room">Loai phong: Family Room</span>
                </div>
              </article>
              <article class="customer-review-list-item ftco-animate" data-review-item data-review-rating="5" data-review-room="Premium Suite">
                <div class="customer-review-avatar"><i class="ion-ios-person"></i></div>
                <div class="customer-review-list-body">
                  <div class="customer-review-list-head">
                    <div>
                      <h3>Pham Minh Khoa</h3>
                      <time datetime="2026-04-07">07/04/2026</time>
                    </div>
                    <div class="customer-review-stars" aria-label="5 sao">
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                    </div>
                  </div>
                  <p class="customer-review-description" title="Phong suite co view dep, giuong em va bua sang da dang. Dich vu phong phan hoi nhanh.">Phong suite co view dep, giuong em va bua sang da dang. Dich vu phong phan hoi nhanh.</p>
                  <span class="customer-review-room">Loai phong: Premium Suite</span>
                </div>
              </article>
              <article class="customer-review-list-item ftco-animate" data-review-item data-review-rating="4" data-review-room="Deluxe Room">
                <div class="customer-review-avatar"><i class="ion-ios-person"></i></div>
                <div class="customer-review-list-body">
                  <div class="customer-review-list-head">
                    <div>
                      <h3>Tran Nhat Linh</h3>
                      <time datetime="2026-04-06">06/04/2026</time>
                    </div>
                    <div class="customer-review-stars" aria-label="4 sao">
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star-outline"></span>
                    </div>
                  </div>
                  <p class="customer-review-description" title="Phong deluxe sach se, tien nghi day du va vi tri thuan tien cho ky nghi ngan ngay.">Phong deluxe sach se, tien nghi day du va vi tri thuan tien cho ky nghi ngan ngay.</p>
                  <span class="customer-review-room">Loai phong: Deluxe Room</span>
                </div>
              </article>
              <article class="customer-review-list-item ftco-animate" data-review-item data-review-rating="5" data-review-room="Suite Room">
                <div class="customer-review-avatar"><i class="ion-ios-person"></i></div>
                <div class="customer-review-list-body">
                  <div class="customer-review-list-head">
                    <div>
                      <h3>Vo Gia Han</h3>
                      <time datetime="2026-04-05">05/04/2026</time>
                    </div>
                    <div class="customer-review-stars" aria-label="5 sao">
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                    </div>
                  </div>
                  <p class="customer-review-description" title="Phong suite rong, anh sang dep va khu tiep khach rieng tao cam giac rat thoai mai.">Phong suite rong, anh sang dep va khu tiep khach rieng tao cam giac rat thoai mai.</p>
                  <span class="customer-review-room">Loai phong: Suite Room</span>
                </div>
              </article>
              <article class="customer-review-list-item ftco-animate" data-review-item data-review-rating="4" data-review-room="Deluxe Garden">
                <div class="customer-review-avatar"><i class="ion-ios-person"></i></div>
                <div class="customer-review-list-body">
                  <div class="customer-review-list-head">
                    <div>
                      <h3>Nguyen Quoc Huy</h3>
                      <time datetime="2026-04-04">04/04/2026</time>
                    </div>
                    <div class="customer-review-stars" aria-label="4 sao">
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star-outline"></span>
                    </div>
                  </div>
                  <p class="customer-review-description" title="Noi that dep, phong tam sach va khu vuc chung yen tinh, de nghi ngoi.">Noi that dep, phong tam sach va khu vuc chung yen tinh, de nghi ngoi.</p>
                  <span class="customer-review-room">Loai phong: Deluxe Garden</span>
                </div>
              </article>
              <article class="customer-review-list-item ftco-animate" data-review-item data-review-rating="3" data-review-room="Family Room">
                <div class="customer-review-avatar"><i class="ion-ios-person"></i></div>
                <div class="customer-review-list-body">
                  <div class="customer-review-list-head">
                    <div>
                      <h3>Le Bao Chau</h3>
                      <time datetime="2026-04-03">03/04/2026</time>
                    </div>
                    <div class="customer-review-stars" aria-label="3 sao">
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star-outline"></span>
                      <span class="ion-ios-star-outline"></span>
                    </div>
                  </div>
                  <p class="customer-review-description" title="Khong gian phong rong va tien cho nhom, tuy nhien check-in vao gio cao diem con hoi lau.">Khong gian phong rong va tien cho nhom, tuy nhien check-in vao gio cao diem con hoi lau.</p>
                  <span class="customer-review-room">Loai phong: Family Room</span>
                </div>
              </article>
              <article class="customer-review-list-item ftco-animate" data-review-item data-review-rating="4" data-review-room="Premium Suite">
                <div class="customer-review-avatar"><i class="ion-ios-person"></i></div>
                <div class="customer-review-list-body">
                  <div class="customer-review-list-head">
                    <div>
                      <h3>Pham Minh Khoa</h3>
                      <time datetime="2026-04-02">02/04/2026</time>
                    </div>
                    <div class="customer-review-stars" aria-label="4 sao">
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star-outline"></span>
                    </div>
                  </div>
                  <p class="customer-review-description" title="Toi hai long voi phong suite, dac biet la giuong ngu va anh sang trong phong.">Toi hai long voi phong suite, dac biet la giuong ngu va anh sang trong phong.</p>
                  <span class="customer-review-room">Loai phong: Premium Suite</span>
                </div>
              </article>
              <article class="customer-review-list-item ftco-animate" data-review-item data-review-rating="5" data-review-room="Deluxe Room">
                <div class="customer-review-avatar"><i class="ion-ios-person"></i></div>
                <div class="customer-review-list-body">
                  <div class="customer-review-list-head">
                    <div>
                      <h3>Tran Nhat Linh</h3>
                      <time datetime="2026-04-01">01/04/2026</time>
                    </div>
                    <div class="customer-review-stars" aria-label="5 sao">
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                    </div>
                  </div>
                  <p class="customer-review-description" title="Khach san ho tro linh hoat, phong sach va vat dung chuan bi day du.">Khach san ho tro linh hoat, phong sach va vat dung chuan bi day du.</p>
                  <span class="customer-review-room">Loai phong: Deluxe Room</span>
                </div>
              </article>
              <article class="customer-review-list-item ftco-animate" data-review-item data-review-rating="2" data-review-room="Suite Room">
                <div class="customer-review-avatar"><i class="ion-ios-person"></i></div>
                <div class="customer-review-list-body">
                  <div class="customer-review-list-head">
                    <div>
                      <h3>Vo Gia Han</h3>
                      <time datetime="2026-03-30">30/03/2026</time>
                    </div>
                    <div class="customer-review-stars" aria-label="2 sao">
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star-outline"></span>
                      <span class="ion-ios-star-outline"></span>
                      <span class="ion-ios-star-outline"></span>
                    </div>
                  </div>
                  <p class="customer-review-description" title="Phong dep nhung buoi toi con nghe tieng tu hanh lang. Nhan vien xu ly phan hoi nhanh.">Phong dep nhung buoi toi con nghe tieng tu hanh lang. Nhan vien xu ly phan hoi nhanh.</p>
                  <span class="customer-review-room">Loai phong: Suite Room</span>
                </div>
              </article>
              <article class="customer-review-list-item ftco-animate" data-review-item data-review-rating="4" data-review-room="Deluxe Garden">
                <div class="customer-review-avatar"><i class="ion-ios-person"></i></div>
                <div class="customer-review-list-body">
                  <div class="customer-review-list-head">
                    <div>
                      <h3>Nguyen Quoc Huy</h3>
                      <time datetime="2026-03-28">28/03/2026</time>
                    </div>
                    <div class="customer-review-stars" aria-label="4 sao">
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star"></span>
                      <span class="ion-ios-star-outline"></span>
                    </div>
                  </div>
                  <p class="customer-review-description" title="Phong gon gang, sach se va dap ung tot nhu cau nghi ngoi.">Phong gon gang, sach se va dap ung tot nhu cau nghi ngoi.</p>
                  <span class="customer-review-room">Loai phong: Deluxe Garden</span>
                </div>
              </article>
            </div>

            <div class="customer-empty" data-review-empty hidden>Không có đánh giá phù hợp với bộ lọc.</div>
            <div class="customer-review-pagination" data-review-pagination hidden></div>
          </div>
        </div>
      </div>
    </section>

    <script>
      (() => {
        const parseHomeSearchDate = (value) => {
          const normalized = String(value || '').trim();
          let match = normalized.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);

          if (match) {
            const [, day, month, year] = match;
            return new Date(Number(year), Number(month) - 1, Number(day));
          }

          match = normalized.match(/^(\d{4})-(\d{2})-(\d{2})$/);

          if (match) {
            const [, year, month, day] = match;
            return new Date(Number(year), Number(month) - 1, Number(day));
          }

          const parsed = new Date(normalized);
          return Number.isNaN(parsed.getTime()) ? null : parsed;
        };

        const formatHomeSearchDate = (value) => {
          const date = parseHomeSearchDate(value);

          if (!date) {
            return '';
          }

          const year = date.getFullYear();
          const month = String(date.getMonth() + 1).padStart(2, '0');
          const day = String(date.getDate()).padStart(2, '0');
          return `${year}-${month}-${day}`;
        };

        const initHomeSearchSubmit = () => {
          const form = document.querySelector('[data-home-booking-search]');
          const submit = document.querySelector('[data-home-booking-search-submit]');

          if (!form || !submit) {
            return;
          }

          submit.addEventListener('click', (event) => {
            event.preventDefault();

            const checkInInput = form.querySelector('.checkin_date');
            const checkOutInput = form.querySelector('.checkout_date');
            const checkIn = parseHomeSearchDate(checkInInput?.value);
            const checkOut = parseHomeSearchDate(checkOutInput?.value);
            const adults = form.querySelector('[data-guest-input="adults"]')?.value || '2';
            const children = form.querySelector('[data-guest-input="children"]')?.value || '0';
            const rooms = form.querySelector('[data-guest-input="rooms"]')?.value || '1';

            if (!checkIn || !checkOut || checkIn >= checkOut) {
              alert('Vui lòng chọn ngày nhận và ngày trả hợp lệ.');
              return;
            }

            const url = new URL(form.action, window.location.origin);
            url.searchParams.set('checkIn', formatHomeSearchDate(checkInInput.value));
            url.searchParams.set('checkOut', formatHomeSearchDate(checkOutInput.value));
            url.searchParams.set('NguoiLon', String(Math.max(Number.parseInt(adults, 10) || 1, 1)));
            url.searchParams.set('TreEm', String(Math.max(Number.parseInt(children, 10) || 0, 0)));
            url.searchParams.set('SoPhong', String(Math.max(Number.parseInt(rooms, 10) || 1, 1)));
            window.location.href = url.toString();
          });
        };

        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', initHomeSearchSubmit, { once: true });
        } else {
          initHomeSearchSubmit();
        }
      })();

      (() => {
        const initCustomerReviews = () => {
          const section = document.querySelector('[data-customer-reviews]');

          if (! section) {
            return;
          }

          const items = Array.from(section.querySelectorAll('[data-review-item]'));
          const ratingFilter = section.querySelector('[data-review-rating-filter]');
          const roomFilter = section.querySelector('[data-review-room-filter]');
          const emptyState = section.querySelector('[data-review-empty]');
          const pagination = section.querySelector('[data-review-pagination]');
          const pageSize = 4;
          let currentPage = 1;

          const getFilteredItems = () => {
            const rating = ratingFilter?.value || '';
            const room = roomFilter?.value || '';

            return items.filter((item) => {
              const matchesRating = !rating || item.dataset.reviewRating === rating;
              const matchesRoom = !room || item.dataset.reviewRoom === room;

              return matchesRating && matchesRoom;
            });
          };

          const renderPagination = (pageCount) => {
            if (! pagination) {
              return;
            }

            pagination.innerHTML = '';
            pagination.hidden = pageCount <= 1;

            for (let page = 1; page <= pageCount; page += 1) {
              const button = document.createElement('button');
              button.type = 'button';
              button.textContent = String(page);
              button.classList.toggle('is-active', page === currentPage);
              button.setAttribute('aria-label', `Trang đánh giá ${page}`);
              button.addEventListener('click', () => {
                currentPage = page;
                renderReviews();
              });
              pagination.append(button);
            }
          };

          const renderReviews = () => {
            const filteredItems = getFilteredItems();
            const pageCount = Math.ceil(filteredItems.length / pageSize);

            if (currentPage > pageCount) {
              currentPage = pageCount || 1;
            }

            const start = (currentPage - 1) * pageSize;
            const end = start + pageSize;
            const visibleItems = filteredItems.slice(start, end);

            items.forEach((item) => {
              item.hidden = true;
            });

            visibleItems.forEach((item) => {
              item.hidden = false;
            });

            if (emptyState) {
              emptyState.hidden = filteredItems.length > 0;
            }

            renderPagination(pageCount);
          };

          [ratingFilter, roomFilter].forEach((filter) => {
            filter?.addEventListener('change', () => {
              currentPage = 1;
              renderReviews();
            });
          });

          renderReviews();
        };

        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', initCustomerReviews, { once: true });
        } else {
          initCustomerReviews();
        }
      })();
    </script>
   

    @include('customer.partials.footer')

    <div class="customer-booking-detail-modal" data-guest-payment-modal hidden>
      <div class="customer-booking-detail-backdrop" data-guest-payment-close></div>
      <div class="customer-booking-detail-dialog" role="dialog" aria-modal="true" aria-labelledby="guestPaymentTitle">
        <button type="button" class="customer-booking-detail-close" data-guest-payment-close aria-label="Đóng">×</button>
        <div class="customer-booking-detail-head">
          <div>
            <span data-guest-payment-code>Đặt phòng</span>
            <h3 id="guestPaymentTitle" data-guest-payment-title>Thanh toán thành công</h3>
          </div>
          <strong data-guest-payment-status>Đã xác nhận</strong>
        </div>

        <div class="customer-booking-detail-grid">
          <div class="customer-booking-detail-section">
            <h4>Thông tin phòng đã đặt</h4>
            <div data-guest-payment-rooms></div>
          </div>
          <div class="customer-booking-detail-section">
            <h4>Thông tin cá nhân</h4>
            <div class="customer-booking-detail-fields" data-guest-payment-person></div>
          </div>
        </div>

        <div class="customer-booking-detail-summary">
          <div><span>Ngày nhận phòng</span><strong data-guest-payment-checkin>--</strong></div>
          <div><span>Ngày trả phòng</span><strong data-guest-payment-checkout>--</strong></div>
          <div><span>Tổng số khách ở</span><strong data-guest-payment-guests>0 khách</strong></div>
          <div><span>Tổng tiền thanh toán</span><strong data-guest-payment-total>0 VND</strong></div>
          <div><span>Tiền đặt cọc</span><strong data-guest-payment-deposit>0 VND</strong></div>
        </div>
      </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const isCustomerLoggedIn = @json(filled(session('auth_account')));
        const modal = document.querySelector('[data-guest-payment-modal]');
        const roomsEl = document.querySelector('[data-guest-payment-rooms]');
        const personEl = document.querySelector('[data-guest-payment-person]');
        const statusLabels = {
          0: 'Chờ thanh toán',
          1: 'Đã xác nhận',
          2: 'Đang ở',
          3: 'Đã trả phòng',
          4: 'Đã hủy',
        };

        if (isCustomerLoggedIn || !modal || !roomsEl || !personEl) {
          return;
        }

        const formatCurrency = (value) => `${Number(value || 0).toLocaleString('vi-VN')} VND`;
        const formatDate = (value) => {
          const date = new Date(value);
          return Number.isNaN(date.getTime()) ? '--' : date.toLocaleDateString('vi-VN');
        };
        const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#039;',
        }[char]));
        const getRelation = (item, camelName, snakeName) => item?.[camelName] ?? item?.[snakeName] ?? null;
        const getArrayRelation = (item, camelName, snakeName) => {
          const value = getRelation(item, camelName, snakeName);
          return Array.isArray(value) ? value : [];
        };
        const diffDays = (start, end) => {
          const startDate = new Date(start);
          const endDate = new Date(end);
          if (Number.isNaN(startDate.getTime()) || Number.isNaN(endDate.getTime())) {
            return 1;
          }
          return Math.max(Math.round((endDate - startDate) / 86400000), 1);
        };

        const closeModal = () => {
          modal.classList.remove('is-open');
          modal.hidden = true;
          localStorage.removeItem('peachBookingPayment');
          localStorage.removeItem('peachBookingHolds');
          localStorage.removeItem('peachBookingSelection');
        };

        const buildBookingPayload = (booking) => {
          const customer = getRelation(booking, 'khachHang', 'khach_hang') || {};
          const account = getRelation(customer, 'taiKhoan', 'tai_khoan') || {};
          const invoice = getRelation(booking, 'hoaDon', 'hoa_don') || {};
          const details = getArrayRelation(booking, 'chiTietDatPhong', 'chi_tiet_dat_phong');
          const invoiceDetails = getArrayRelation(invoice, 'chiTietHoaDons', 'chi_tiet_hoa_dons');
          const nights = diffDays(booking.NgayNhanPhong, booking.NgayTraPhong);
          const groupedRooms = new Map();

          details.forEach((detail) => {
            const room = getRelation(detail, 'phong', 'phong') || {};
            const roomType = getRelation(room, 'loaiPhong', 'loai_phong') || {};
            const roomTypeId = room.MaLoaiPhong ?? roomType.MaLoaiPhong ?? 'unknown';
            const current = groupedRooms.get(roomTypeId) || {
              roomType,
              numbers: [],
              count: 0,
            };
            current.count += 1;
            if (room.SoPhong) {
              current.numbers.push(room.SoPhong);
            }
            groupedRooms.set(roomTypeId, current);
          });

          const rooms = Array.from(groupedRooms.entries()).map(([roomTypeId, group]) => {
            const invoiceDetail = invoiceDetails.find((item) => String(item.MaLoaiPhong) === String(roomTypeId)) || {};
            const quantity = Number(invoiceDetail.SoLuong || group.count || 1);
            const lineTotal = quantity * Number(invoiceDetail.DonGia || 0);
            const guestsPerRoom = Number(group.roomType?.NguoiLon || 0) + Number(group.roomType?.TreEm || 0);

            return {
              TenPhong: group.roomType?.TenLoaiPhong || 'Phòng',
              SoPhong: group.numbers.join(', '),
              SoLuongPhong: group.count || quantity,
              SoKhach: (group.count || quantity) * guestsPerRoom,
              GiaMoiDem: nights > 0 ? Number(invoiceDetail.DonGia || 0) / nights : 0,
              ThanhTien: lineTotal,
            };
          });

          return {
            MaDatPhong: booking.MaDatPhong,
            MaKH: booking.MaKH,
            TenKH: customer.TenKH,
            SoDienThoai: customer.SoDienThoai,
            Email: account.Email,
            CCCD: customer.CCCD,
            NgayDat: booking.NgayDat,
            NgayNhanPhong: booking.NgayNhanPhong,
            NgayTraPhong: booking.NgayTraPhong,
            SoDem: nights,
            TongSoKhach: rooms.reduce((total, room) => total + Number(room.SoKhach || 0), 0) || booking.SoLuong,
            StatusLabel: statusLabels[Number(booking.TinhTrang)] || 'Đã đặt',
            SummaryTitle: rooms.map((room) => room.TenPhong).filter(Boolean).join(', ') || 'Đặt phòng Peach Valley',
            Rooms: rooms,
            TongTien: Number(invoice.TongTien || 0),
            TienDatCoc: Number(invoice.DaThanhToan || 0),
            TinhTrang: Number(booking.TinhTrang),
            InvoiceStatus: Number(invoice.TrangThai),
          };
        };

        const vnpayMessages = {
          success: {
            title: 'Thanh toán thành công',
            status: 'Đã xác nhận',
            isError: false,
          },
          failed: {
            title: 'Thanh toán thất bại',
            status: 'Chờ thanh toán',
            isError: true,
          },
          invalid: {
            title: 'Không thể xác thực thanh toán',
            status: 'Chờ thanh toán',
            isError: true,
          },
          missing_order: {
            title: 'Không tìm thấy đơn thanh toán',
            status: 'Chờ thanh toán',
            isError: true,
          },
          amount_mismatch: {
            title: 'Số tiền thanh toán không khớp',
            status: 'Chờ thanh toán',
            isError: true,
          },
          confirm_failed: {
            title: 'Thanh toán chưa được ghi nhận',
            status: 'Chờ xác nhận',
            isError: true,
          },
        };

        const renderModal = (booking, options = {}) => {
          const title = options.title || 'Thanh toán thành công';
          const status = options.status || booking.StatusLabel || 'Đã xác nhận';
          document.querySelector('[data-guest-payment-code]').textContent = `Đặt phòng #${booking.MaDatPhong || '--'}`;
          document.querySelector('[data-guest-payment-title]').textContent = `${title} - ${booking.SoDem || 1} đêm`;
          document.querySelector('[data-guest-payment-status]').textContent = status;
          document.querySelector('[data-guest-payment-status]').style.color = options.isError ? '#dc2626' : '';
          document.querySelector('[data-guest-payment-checkin]').textContent = formatDate(booking.NgayNhanPhong);
          document.querySelector('[data-guest-payment-checkout]').textContent = formatDate(booking.NgayTraPhong);
          document.querySelector('[data-guest-payment-guests]').textContent = `${booking.TongSoKhach || 1} khách`;
          document.querySelector('[data-guest-payment-total]').textContent = formatCurrency(booking.TongTien);
          document.querySelector('[data-guest-payment-deposit]').textContent = formatCurrency(booking.TienDatCoc);

          roomsEl.innerHTML = '';
          (booking.Rooms || []).forEach((room) => {
            const row = document.createElement('div');
            row.className = 'customer-booking-detail-room';
            row.innerHTML = `
              <div>
                <strong>${escapeHtml(room.TenPhong || 'Phòng')}</strong>
                <span>${escapeHtml(room.SoLuongPhong || 1)} phòng${room.SoPhong ? ` - ${escapeHtml(room.SoPhong)}` : ''}</span>
              </div>
              <div>${formatCurrency(room.ThanhTien)}</div>
            `;
            roomsEl.appendChild(row);
          });

          personEl.innerHTML = `
            <div><span>Khách hàng</span><strong>${escapeHtml(booking.TenKH || '--')}</strong></div>
            <div><span>Số điện thoại</span><strong>${escapeHtml(booking.SoDienThoai || '--')}</strong></div>
            <div><span>Ngày đặt</span><strong>${formatDate(booking.NgayDat)}</strong></div>
          `;

          modal.hidden = false;
          modal.classList.add('is-open');
        };

        const fetchBooking = async (bookingId) => {
          const response = await fetch(`/api/dat-phong/${encodeURIComponent(bookingId)}`, {
            headers: { Accept: 'application/json' },
          });
          const result = await response.json().catch(() => null);
          if (!response.ok || !result?.success) {
            throw new Error(result?.message || 'Không thể tải thông tin đặt phòng.');
          }
          return buildBookingPayload(result.data);
        };

        const waitForPaidBooking = async (bookingId) => {
          for (let attempt = 0; attempt < 6; attempt += 1) {
            const booking = await fetchBooking(bookingId);
            const isPaid = booking.TinhTrang > 0 || booking.InvoiceStatus === 1 || booking.TienDatCoc > 0;
            if (isPaid) {
              return booking;
            }
            await new Promise((resolve) => setTimeout(resolve, 1500));
          }
          return null;
        };

        const initGuestPaymentModal = async () => {
          const query = new URLSearchParams(window.location.search);
          const vnpayStatus = query.get('vnpay');
          let payment = null;
          try {
            payment = JSON.parse(localStorage.getItem('peachBookingPayment') || 'null');
          } catch (error) {
            payment = null;
          }

          const bookingId = Array.isArray(payment?.datPhongIds) ? payment.datPhongIds[0] : null;
          const modalKey = `${payment?.appTransId || query.get('txn_ref') || bookingId}:${vnpayStatus || 'payment'}`;
          if (!bookingId || sessionStorage.getItem(`peachGuestPaymentShown:${modalKey}`)) {
            return;
          }

          try {
            const message = vnpayStatus ? (vnpayMessages[vnpayStatus] || vnpayMessages.failed) : null;
            const booking = message?.isError
              ? await fetchBooking(bookingId)
              : await waitForPaidBooking(bookingId);

            if (!booking) {
              return;
            }
            sessionStorage.setItem(`peachGuestPaymentShown:${modalKey}`, '1');
            renderModal(booking, message || undefined);

            if (vnpayStatus) {
              query.delete('vnpay');
              query.delete('txn_ref');
              const cleanUrl = `${window.location.pathname}${query.toString() ? `?${query.toString()}` : ''}${window.location.hash}`;
              window.history.replaceState({}, '', cleanUrl);
            }
          } catch (error) {
            console.error('Guest payment modal error:', error);
          }
        };

        document.querySelectorAll('[data-guest-payment-close]').forEach((button) => {
          button.addEventListener('click', closeModal);
        });
        document.addEventListener('keydown', (event) => {
          if (event.key === 'Escape' && !modal.hidden) {
            closeModal();
          }
        });

        initGuestPaymentModal();
      });
    </script>
    
  

  <!-- loader -->
  <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>

  </body>
</html>
