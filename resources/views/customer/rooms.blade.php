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
		<div class="hero-wrap" data-bg-image="{{ asset('customers/images/rooms.jpg') }}">
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
            <h2 class="mb-4">Các loại phòng của khách sạn</h2>
          </div>
        </div>  
    		<div class="row room-list-vertical" id="roomContainer">
    		</div>
    	</div>
    </section>

    <script>
    	// Debug: log all navigation attempts
    	window.addEventListener('beforeunload', function() {
    		console.log('Page unload, current URL:', window.location.href);
    	});

    	// Intercept all link clicks
    	document.addEventListener('click', function(e) {
    		if (e.target.tagName === 'A') {
    			console.log('Link clicked:', e.target.href);
    		}
    	}, true);

    	// Log all fetch requests
    	const originalFetch = window.fetch;
    	window.fetch = function(...args) {
    		console.log('Fetch request:', args[0]);
    		return originalFetch.apply(this, args);
    	};

    	// Log XMLHttpRequest
    	const originalXHR = window.XMLHttpRequest.prototype.open;
    	window.XMLHttpRequest.prototype.open = function(method, url) {
    		console.log('XHR request:', method, url);
    		return originalXHR.apply(this, arguments);
    	};

    	// Log resource load errors
    	window.addEventListener('error', function(e) {
    		if (e.filename || e.target?.src) {
    			console.error('Resource error:', e.filename || e.target.src);
    		}
    	}, true);

    	async function getRoomTypes() {
    		if (window.CustomerRoomApi?.getRoomTypes) {
    			return window.CustomerRoomApi.getRoomTypes();
    		}

    		const response = await fetch('/api/loai-phong');
    		const result = await response.json();
    		if (!result.success || !Array.isArray(result.data)) {
    			throw new Error(result.message || 'Failed to load rooms');
    		}
    		return result.data;
    	}

    	async function loadRooms() {
    		try {
    			const rooms = await getRoomTypes();
    			if (!Array.isArray(rooms)) {
    				console.error('Failed to load rooms: invalid room data');
    				return;
    			}

    			console.log('Loaded rooms count:', rooms.length);
    			const container = document.getElementById('roomContainer');
    			const formatRoomPrice = (room) => {
    				const priceRows = room.bangGias || room.bang_gias || [];
    				const firstPrice = Array.isArray(priceRows) ? priceRows[0] : priceRows;
    				const rawPrice = firstPrice?.GiaPhong || firstPrice?.gia_phong || firstPrice?.Gia || firstPrice?.gia;
    				const numericPrice = Number(rawPrice);

    				if (!Number.isFinite(numericPrice) || numericPrice <= 0) {
    					return 'Liên hệ';
    				}

    				return numericPrice.toLocaleString('vi-VN');
    			};
    			let html = '';
    			
    			for (let i = 0; i < rooms.length; i++) {
    				const room = rooms[i];
    				
    				// Get ID - try different property names
    				const roomId = room.MaLoaiPhong || room.id || i + 1;
    				console.log(`Room ${i}: ID=${roomId}, Name=${room.TenLoaiPhong}`);
    				
    				const fallbackImage = '{{ asset("customers/images/room-6.jpg") }}';
    				const firstImage = Array.isArray(room.hinhs) ? room.hinhs[0] : null;
    				const imageSrc = firstImage?.Url || firstImage?.url || firstImage?.DuongDan || firstImage?.duong_dan || fallbackImage;

    				const price = formatRoomPrice(room);

    				const arrowClass = i % 2 === 0 ? 'left-arrow' : 'right-arrow';
    				const orderClass = i % 2 === 0 ? '' : 'order-md-last';

    				html += `
    					<div class="col-12">
    						<div class="room-wrap d-md-flex">
    							<a href="#" class="img ${orderClass}" data-bg-image="${imageSrc}"></a>
    							<div class="half ${arrowClass} d-flex align-items-center">
    								<div class="text p-4 text-center">
    									<h3 class="mb-3"><a href="/customer/room/${roomId}">${room.TenLoaiPhong}</a></h3>
    									<p class="room-description mb-3">${room.Mota || 'Phòng thoải mái và hiện đại'}</p>
    									<p class="mb-0"><span class="price mr-1">${price}</span> <span class="per">VNĐ/Đêm</span></p>
    									<p class="pt-1"><a href="/customer/room/${roomId}" class="btn-custom px-3 py-2 rounded">Chi Tiết <span class="icon-long-arrow-right"></span></a></p>
    								</div>
    							</div>
    						</div>
    					</div>
    				`;
    			}
    			
    			container.innerHTML = html;

    			// Apply background images
    			document.querySelectorAll('[data-bg-image]').forEach((element) => {
    				const backgroundImage = element.getAttribute('data-bg-image');
    				if (backgroundImage && backgroundImage !== 'undefined' && backgroundImage !== 'null') {
    					element.style.backgroundImage = `url("${backgroundImage}")`;
    				}
    			});
    		} catch (error) {
    			console.error('Error loading rooms:', error);
    		}
    	}

    	if (document.readyState === 'loading') {
    		document.addEventListener('DOMContentLoaded', loadRooms);
    	} else {
    		loadRooms();
    	}

    	console.log('✅ Rooms page script loaded successfully');
    </script>


    @include('customer.partials.footer')
    
  

  <!-- loader -->
  <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>

  </body>
</html>
