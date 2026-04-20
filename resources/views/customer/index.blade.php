@php
  $reviewBookings = collect(config('hotel-management.reception.bookings.records', []))->keyBy('MaDatPhong');
  $demoReviews = collect([
    ['MaDG' => 9004, 'MaDatPhong' => 9004, 'Sao' => 5, 'TieuDe' => 'Kỳ nghỉ tuyệt vời vượt mong đợi', 'MoTa' => 'Khách sạn có không gian vô cùng tinh tế và sang trọng. Đội ngũ nhân viên phục vụ rất chuyên nghiệp và chu đáo, từ lúc bước vào sảnh cho đến lúc check-out. Phòng family rộng rãi, sạch sẽ, phù hợp cho cả gia đình nghỉ dưỡng.', 'NgayDanhGia' => '2026-04-08'],
    ['MaDG' => 9005, 'MaDatPhong' => 9005, 'Sao' => 5, 'TieuDe' => 'Dịch vụ xứng đáng quay lại', 'MoTa' => 'Phòng suite có view đẹp, giường êm và không gian yên tĩnh. Bữa sáng đa dạng, nhân viên hỗ trợ nhanh khi tôi cần thêm dịch vụ phòng.', 'NgayDanhGia' => '2026-04-07'],
    ['MaDG' => 9006, 'MaDatPhong' => 9001, 'Sao' => 4, 'TieuDe' => 'Không gian sạch đẹp', 'MoTa' => 'Phòng deluxe sạch sẽ, tiện nghi đầy đủ và vị trí thuận tiện. Tôi thích cách khách sạn chuẩn bị phòng rất gọn gàng trước giờ nhận phòng.', 'NgayDanhGia' => '2026-04-06'],
    ['MaDG' => 9007, 'MaDatPhong' => 9002, 'Sao' => 5, 'TieuDe' => 'Trải nghiệm nghỉ dưỡng rất tốt', 'MoTa' => 'Phòng suite rộng, ánh sáng đẹp và khu vực tiếp khách riêng rất thoải mái. Nhân viên lễ tân tư vấn nhiệt tình về dịch vụ spa và nhà hàng.', 'NgayDanhGia' => '2026-04-05'],
    ['MaDG' => 9008, 'MaDatPhong' => 9003, 'Sao' => 4, 'TieuDe' => 'Phù hợp cho chuyến đi ngắn ngày', 'MoTa' => 'Phòng deluxe có nội thất đẹp, phòng tắm sạch và dịch vụ dọn phòng đúng giờ. Khu vực chung yên tĩnh, dễ nghỉ ngơi.', 'NgayDanhGia' => '2026-04-04'],
    ['MaDG' => 9009, 'MaDatPhong' => 9004, 'Sao' => 3, 'TieuDe' => 'Ổn nhưng cần nhanh hơn', 'MoTa' => 'Không gian phòng family rộng và tiện cho nhóm, tuy nhiên thời gian check-in hơi lâu vào giờ cao điểm. Nhân viên vẫn hỗ trợ lịch sự.', 'NgayDanhGia' => '2026-04-03'],
    ['MaDG' => 9010, 'MaDatPhong' => 9005, 'Sao' => 4, 'TieuDe' => 'Suite đẹp và yên tĩnh', 'MoTa' => 'Tôi hài lòng với phòng suite, đặc biệt là giường ngủ và ánh sáng trong phòng. Dịch vụ ăn uống phục vụ lên phòng khá nhanh.', 'NgayDanhGia' => '2026-04-02'],
    ['MaDG' => 9011, 'MaDatPhong' => 9001, 'Sao' => 5, 'TieuDe' => 'Nhân viên rất chu đáo', 'MoTa' => 'Tôi được hỗ trợ đổi giờ nhận phòng linh hoạt. Phòng deluxe sạch, thơm nhẹ và các vật dụng đều được chuẩn bị đầy đủ.', 'NgayDanhGia' => '2026-04-01'],
    ['MaDG' => 9012, 'MaDatPhong' => 9002, 'Sao' => 2, 'TieuDe' => 'Cần cải thiện cách âm', 'MoTa' => 'Phòng suite đẹp nhưng buổi tối còn nghe tiếng từ hành lang. Nhân viên xử lý phản hồi nhanh, hy vọng khách sạn cải thiện thêm.', 'NgayDanhGia' => '2026-03-30'],
    ['MaDG' => 9013, 'MaDatPhong' => 9003, 'Sao' => 4, 'TieuDe' => 'Đáng tiền trong tầm giá', 'MoTa' => 'Phòng deluxe gọn gàng, sạch sẽ và đáp ứng tốt nhu cầu nghỉ ngơi. Tôi thích khu vực sảnh và thái độ phục vụ thân thiện.', 'NgayDanhGia' => '2026-03-28'],
  ]);
  $roomReviewTitleMap = [
    5 => 'Kỳ nghỉ tuyệt vời vượt mong đợi',
    4 => 'Trải nghiệm rất hài lòng',
    3 => 'Trải nghiệm ổn nhưng cần cải thiện',
    2 => 'Cần cải thiện thêm',
    1 => 'Chưa hài lòng',
  ];
  $hotelReviews = collect(config('hotel-management.modules.reviews.records', []))
    ->concat($demoReviews)
    ->map(function ($review) use ($reviewBookings, $roomReviewTitleMap) {
      $booking = $reviewBookings->get($review['MaDatPhong'] ?? null, []);
      $customerName = $booking['TenKH'] ?? 'Khách hàng Peach Valley';

      return array_merge($review, [
        'TenKH' => $customerName,
        'LoaiPhong' => $booking['LoaiPhong'] ?? 'Phòng Peach Valley',
        'TieuDe' => $review['TieuDe'] ?? ($roomReviewTitleMap[(int) ($review['Sao'] ?? 5)] ?? 'Đánh giá từ khách hàng'),
      ]);
    })
    ->sortByDesc('NgayDanhGia')
    ->values();
  $reviewCount = $hotelReviews->count();
  $reviewAverage = $reviewCount ? round($hotelReviews->avg(fn ($review) => (int) ($review['Sao'] ?? 0)), 1) : 0;
  $reviewDistribution = collect(range(5, 1))->mapWithKeys(function ($star) use ($hotelReviews, $reviewCount) {
    $count = $hotelReviews->where('Sao', $star)->count();

    return [$star => [
      'count' => $count,
      'percent' => $reviewCount ? round(($count / $reviewCount) * 100) : 0,
    ]];
  });
  $reviewRoomTypes = $hotelReviews->pluck('LoaiPhong')->filter()->unique()->values();
@endphp

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
	      <div class="slider-item" data-bg-image="{{ Vite::asset('resources/customer/images/siderbar1.jpg') }}">
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

	      <div class="slider-item" data-bg-image="{{ Vite::asset('resources/customer/images/siderbar2.jpg') }}">
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
          <a class="home-promotion-slide" href="{{ route('customer.promotion') }}" data-bg-image="{{ Vite::asset('resources/customer/images/screen1.png') }}" aria-label="Xem khuyến mãi Peach Valley"></a>
          <a class="home-promotion-slide" href="{{ route('customer.promotion') }}" data-bg-image="{{ Vite::asset('resources/customer/images/screen2.png') }}" aria-label="Xem khuyến mãi Peach Valley"></a>
          <a class="home-promotion-slide" href="{{ route('customer.promotion') }}" data-bg-image="{{ Vite::asset('resources/customer/images/screen3.png') }}" aria-label="Xem khuyến mãi Peach Valley"></a>
          <a class="home-promotion-slide" href="{{ route('customer.promotion') }}" data-bg-image="{{ Vite::asset('resources/customer/images/screen.png') }}" aria-label="Xem khuyến mãi Peach Valley"></a>
        </div>
      </div>
    </section>

    <section class="ftco-section ftco-wrap-about ftco-no-pt ftco-no-pb">
			<div class="container">
				<div class="row no-gutters">
					<div class="col-md-7 order-md-last d-flex">
						<div class="img img-1 mr-md-2 ftco-animate" data-bg-image="{{ Vite::asset('resources/customer/images/home2.jpg') }}"></div>
						<div class="img img-2 ml-md-2 ftco-animate" data-bg-image="{{ Vite::asset('resources/customer/images/home3.jpg') }}"></div>
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
        		<div class="testimony-img aside-stretch-2" data-bg-image="{{ Vite::asset('resources/customer/images/view1.jpg') }}"></div>
        	</div>
	          <div class="col-md-7 py-5 pl-md-5">
	          	<div class="py-md-5">
		          	<div class="heading-section ftco-animate mb-4">
		          		<span class="subheading">Đánh giá</span>
			        </div>
	            <div class="carousel-testimony owl-carousel ftco-animate">
	              @forelse ($hotelReviews->take(3) as $review)
	                <div class="item">
	                  <a href="#customer-reviews" class="customer-review-slide-link">
	                    <div class="testimony-wrap pb-4">
	                      <div class="customer-review-stars mb-3" aria-label="{{ (int) ($review['Sao'] ?? 0) }} sao">
	                        @for ($star = 1; $star <= 5; $star++)
	                          <span class="{{ $star <= (int) ($review['Sao'] ?? 0) ? 'ion-ios-star' : 'ion-ios-star-outline' }}"></span>
	                        @endfor
	                      </div>
	                      <div class="text">
	                        <p class="mb-3 customer-review-clamp">{{ $review['MoTa'] ?? 'Trải nghiệm lưu trú dễ chịu tại Peach Valley.' }}</p>
	                        <span class="customer-review-room">Loại phòng: {{ $review['LoaiPhong'] }}</span>
	                      </div>
	                      <div class="d-flex align-items-center mt-4">
			                  <span class="customer-review-user-icon"><i class="ion-ios-person"></i></span>
			                  <div class="pos ml-3">
			                  	<p class="name">{{ $review['TenKH'] }}</p>
			                    <span class="position">{{ \Carbon\Carbon::parse($review['NgayDanhGia'])->format('d/m/Y') }}</span>
			                  </div>
			                </div>
	                    </div>
	                  </a>
	                </div>
	              @empty
	                <div class="item">
	                  <div class="testimony-wrap pb-4">
	                    <div class="text">
	                      <p class="mb-4">Chưa có đánh giá nào từ khách hàng.</p>
	                    </div>
	                  </div>
	                </div>
	              @endforelse
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
    					<a href="#" class="img" data-bg-image="{{ Vite::asset('resources/customer/images/deluxe_family.jpg') }}"></a>
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
    					<a href="#" class="img" data-bg-image="{{ Vite::asset('resources/customer/images/suite_junior.jpg') }}"></a>
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
    					<a href="#" class="img order-md-last" data-bg-image="{{ Vite::asset('resources/customer/images/suite.jpg') }}"></a>
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
    					<a href="#" class="img order-md-last" data-bg-image="{{ Vite::asset('resources/customer/images/superior.jpg') }}"></a>
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
              <strong>{{ number_format($reviewAverage, 1) }}</strong>
              <em>/ 5</em>
            </div>
            <div class="customer-review-stars" aria-label="{{ $reviewAverage }} sao trung bình">
              @for ($star = 1; $star <= 5; $star++)
                <span class="{{ $star <= round($reviewAverage) ? 'ion-ios-star' : 'ion-ios-star-outline' }}"></span>
              @endfor
            </div>
            <p>Dựa trên {{ number_format($reviewCount, 0, ',', '.') }} đánh giá từ khách hàng thực tế</p>

            <div class="customer-review-bars">
              @foreach ($reviewDistribution as $star => $data)
                <div class="customer-review-bar-row">
                  <span>{{ $star }}</span>
                  <div style="--review-percent: {{ $data['percent'] }};"><i></i></div>
                  <strong>{{ $data['percent'] }}%</strong>
                </div>
              @endforeach
            </div>
          </aside>

          <div class="customer-reviews-content">
            <div class="customer-review-filters ftco-animate">
              <label>
                <span>Số sao</span>
                <select data-review-rating-filter>
                  <option value="">Tất cả số sao</option>
                  @for ($star = 5; $star >= 1; $star--)
                    <option value="{{ $star }}">{{ $star }} sao</option>
                  @endfor
                </select>
              </label>
              <label>
                <span>Loại phòng</span>
                <select data-review-room-filter>
                  <option value="">Tất cả loại phòng</option>
                  @foreach ($reviewRoomTypes as $roomType)
                    <option value="{{ $roomType }}">{{ $roomType }}</option>
                  @endforeach
                </select>
              </label>
            </div>

            <div class="customer-review-list" data-review-list>
              @forelse ($hotelReviews as $review)
                <article
                  class="customer-review-list-item ftco-animate"
                  data-review-item
                  data-review-rating="{{ (int) ($review['Sao'] ?? 0) }}"
                  data-review-room="{{ $review['LoaiPhong'] }}"
                >
                  <div class="customer-review-avatar"><i class="ion-ios-person"></i></div>
                  <div class="customer-review-list-body">
                    <div class="customer-review-list-head">
                      <div>
                        <h3>{{ $review['TenKH'] }}</h3>
                        <time datetime="{{ $review['NgayDanhGia'] }}">{{ \Carbon\Carbon::parse($review['NgayDanhGia'])->format('d/m/Y') }}</time>
                      </div>
                      <div class="customer-review-stars" aria-label="{{ (int) ($review['Sao'] ?? 0) }} sao">
                        @for ($star = 1; $star <= 5; $star++)
                          <span class="{{ $star <= (int) ($review['Sao'] ?? 0) ? 'ion-ios-star' : 'ion-ios-star-outline' }}"></span>
                        @endfor
                      </div>
                    </div>
                    <p class="customer-review-description" title="{{ $review['MoTa'] ?? 'Trải nghiệm lưu trú dễ chịu tại Peach Valley.' }}">{{ $review['MoTa'] ?? 'Trải nghiệm lưu trú dễ chịu tại Peach Valley.' }}</p>
                    <span class="customer-review-room">Loại phòng: {{ $review['LoaiPhong'] }}</span>
                  </div>
                </article>
              @empty
                <div class="customer-empty">Chưa có đánh giá nào từ khách hàng.</div>
              @endforelse
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
