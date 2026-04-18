<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Peach Valley Hotel</title>
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
	            <h1 class="mb-4 bread">Phòng</h1>
            </div>
          </div>
        </div>
      </div>
    </div>

    <section class="ftco-section ftco-no-pb ftco-room room-list-section">
    	<div class="container">
    		<div class="row no-gutters justify-content-center mb-5 pb-3">
          <div class="col-md-7 heading-section text-center ftco-animate">
          	<span class="subheading">Phòng Peach Valley</span>
            <h2 class="mb-4">Phòng chính của khách sạn</h2>
          </div>
        </div>  
    		<div class="row room-list-vertical">
    			<div class="col-12">
    				<div class="room-wrap d-md-flex ftco-animate">
    					<a href="#" class="img" data-bg-image="{{ Vite::asset('resources/customer/images/room-6.jpg') }}"></a>
    					<div class="half left-arrow d-flex align-items-center">
    						<div class="text p-4 text-center">
    							<h3 class="mb-3"><a href="{{ route('customer.rooms') }}">Phòng Suite</a></h3>
								<p class="room-description mb-3">Khong gian gon gang, day du tien nghi va phu hop cho ky nghi ngan ngay hoac chuyen cong tac.</p> 
    							<p class="mb-0"><span class="price mr-1">2.000.000</span> <span class="per">VNĐ/Đêm</span></p>
	    						<p class="pt-1"><a href="room-single.html" class="btn-custom px-3 py-2 rounded">Chi Tiết <span class="icon-long-arrow-right"></span></a></p>
    						</div>
    					</div>
    				</div>
    			</div>
    			<div class="col-12">
    				<div class="room-wrap d-md-flex ftco-animate">
    					<a href="#" class="img" data-bg-image="{{ Vite::asset('resources/customer/images/room-1.jpg') }}"></a>
    					<div class="half left-arrow d-flex align-items-center">
    						<div class="text p-4 text-center">
    							<h3 class="mb-3"><a href="{{ route('customer.rooms') }}">Phòng Suite</a></h3>
								<p class="room-description mb-3">Khong gian gon gang, day du tien nghi va phu hop cho ky nghi ngan ngay hoac chuyen cong tac.</p> 
    							<p class="mb-0"><span class="price mr-1">2.000.000</span> <span class="per">VNĐ/Đêm</span></p>
	    						<p class="pt-1"><a href="room-single.html" class="btn-custom px-3 py-2 rounded">Chi Tiết <span class="icon-long-arrow-right"></span></a></p>
    						</div>
    					</div>
    				</div>
    			</div>

    			<div class="col-12">
    				<div class="room-wrap d-md-flex ftco-animate">
    					<a href="#" class="img order-md-last" data-bg-image="{{ Vite::asset('resources/customer/images/room-2.jpg') }}"></a>
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
    			<div class="col-12">
    				<div class="room-wrap d-md-flex ftco-animate">
    					<a href="#" class="img order-md-last" data-bg-image="{{ Vite::asset('resources/customer/images/room-3.jpg') }}"></a>
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

    			<div class="col-12">
    				<div class="room-wrap d-md-flex ftco-animate">
    					<a href="#" class="img" data-bg-image="{{ Vite::asset('resources/customer/images/room-4.jpg') }}"></a>
    					<div class="half left-arrow d-flex align-items-center">
    						<div class="text p-4 text-center">
    							<h3 class="mb-3"><a href="{{ route('customer.rooms') }}">Phòng Suite</a></h3>
								<p class="room-description mb-3">Khong gian gon gang, day du tien nghi va phu hop cho ky nghi ngan ngay hoac chuyen cong tac.</p> 
    							<p class="mb-0"><span class="price mr-1">2.000.000</span> <span class="per">VNĐ/Đêm</span></p>
	    						<p class="pt-1"><a href="room-single.html" class="btn-custom px-3 py-2 rounded">Chi Tiết <span class="icon-long-arrow-right"></span></a></p>
    						</div>
    					</div>
    				</div>
    			</div>
    			<div class="col-12">
    				<div class="room-wrap d-md-flex ftco-animate">
    					<a href="#" class="img" data-bg-image="{{ Vite::asset('resources/customer/images/room-5.jpg') }}"></a>
    					<div class="half left-arrow d-flex align-items-center">
    						<div class="text p-4 text-center">
    							<h3 class="mb-3"><a href="{{ route('customer.rooms') }}">Phòng Suite</a></h3>
								<p class="room-description mb-3">Khong gian gon gang, day du tien nghi va phu hop cho ky nghi ngan ngay hoac chuyen cong tac.</p> 
    							<p class="mb-0"><span class="price mr-1">2.000.000</span> <span class="per">VNĐ/Đêm</span></p>
	    						<p class="pt-1"><a href="room-single.html" class="btn-custom px-3 py-2 rounded">Chi Tiết <span class="icon-long-arrow-right"></span></a></p>
    						</div>
    					</div>
    				</div>
    			</div>
    		</div>
    	</div>
    </section>


    @include('customer.partials.footer')
    
  

  <!-- loader -->
  <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>

  </body>
</html>
