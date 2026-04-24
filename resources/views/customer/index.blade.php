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
    				<form action="#" class="booking-form aside-stretch">
	        		<div class="row">
	        			<div class="col-md d-flex py-md-4">
	        				<div class="form-group align-self-stretch d-flex align-items-end">
	        					<div class="wrap align-self-stretch py-3 px-4">
				    					<label for="#">Ngày nhận</label>
				    					<input type="text" class="form-control checkin_date" placeholder="Chọn ngày">
			    					</div>
			    				</div>
	        			</div>
	        			<div class="col-md d-flex py-md-4">
	        				<div class="form-group align-self-stretch d-flex align-items-end">
	        					<div class="wrap align-self-stretch py-3 px-4">
				    					<label for="#">Ngày trả</label>
				    					<input type="text" class="form-control checkout_date" placeholder="Chọn ngày">
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
			              <a href="{{ route('customer.rooms-booking') }}" class="btn btn-primary py-5 py-md-3 px-4 align-self-stretch d-block"><span>Tìm kiếm</span></a>
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
    
  

  <!-- loader -->
  <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>

  </body>
</html>

