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
        <div class="room-filter-toolbar" data-room-list-filters>
          <div class="room-filter-field room-filter-search">
            <label for="roomListFilterSearch">Tìm phòng</label>
            <div class="room-filter-input-wrap">
              <span class="icon ion-ios-search"></span>
              <input
                type="search"
                id="roomListFilterSearch"
                data-room-list-filter-search
                placeholder="Tên phòng, mô tả, tiện nghi"
                autocomplete="off"
              >
            </div>
          </div>
          <div class="room-filter-field">
            <label for="roomListFilterSort">Sắp xếp giá</label>
            <select id="roomListFilterSort" data-room-list-filter-sort>
              <option value="">Mặc định</option>
              <option value="price-asc">Giá tăng dần</option>
              <option value="price-desc">Giá giảm dần</option>
            </select>
          </div>
          <div class="room-filter-field room-filter-number">
            <label for="roomListFilterAdults">Người lớn tối đa</label>
            <input
              type="number"
              id="roomListFilterAdults"
              data-room-list-filter-adults
              min="0"
              step="1"
              placeholder="Bất kỳ"
              inputmode="numeric"
            >
          </div>
          <div class="room-filter-field room-filter-number">
            <label for="roomListFilterChildren">Trẻ em tối đa</label>
            <input
              type="number"
              id="roomListFilterChildren"
              data-room-list-filter-children
              min="0"
              step="1"
              placeholder="Bất kỳ"
              inputmode="numeric"
            >
          </div>
          <button type="button" class="room-filter-reset" data-room-list-filter-reset>
            Xóa lọc
          </button>
        </div>
    		<div class="row room-list-vertical" id="roomContainer" aria-live="polite">
    			<div class="col-12">
    				<div class="room-list-loading" role="status">
    					<span class="room-list-spinner" aria-hidden="true"></span>
    					<span>Đang tải danh sách phòng...</span>
    				</div>
    			</div>
    		</div>
        <nav class="room-list-pagination" id="roomPagination" aria-label="Phân trang phòng" hidden></nav>
    	</div>
    </section>

    <script>
      const roomContainer = document.getElementById('roomContainer');
      const roomPagination = document.getElementById('roomPagination');
      const roomListFilterSearch = document.querySelector('[data-room-list-filter-search]');
      const roomListFilterSort = document.querySelector('[data-room-list-filter-sort]');
      const roomListFilterAdults = document.querySelector('[data-room-list-filter-adults]');
      const roomListFilterChildren = document.querySelector('[data-room-list-filter-children]');
      const roomListFilterReset = document.querySelector('[data-room-list-filter-reset]');
      const roomsPerPage = 5;
      let allRooms = [];
      let visibleRooms = [];
      let currentRoomPage = 1;

      const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
      }[char]));
      const escapeAttr = (value) => escapeHtml(value).replace(/`/g, '&#096;');
      const getOriginalRoomPrice = (room) => {
        const originalPrice = Number(room?.GiaPhong ?? room?.gia_phong ?? room?.Gia ?? room?.gia);
        return Number.isFinite(originalPrice) && originalPrice > 0 ? originalPrice : 0;
      };
      const getRoomPrice = (room) => {
        const salePrice = Number(room?.GiaGiam ?? room?.gia_giam);
        if (Number.isFinite(salePrice) && salePrice > 0) return salePrice;
        return getOriginalRoomPrice(room);
      };
      const getRoomDiscountPercent = (room) => {
        const originalPrice = getOriginalRoomPrice(room);
        const salePrice = getRoomPrice(room);
        if (originalPrice <= 0 || salePrice <= 0 || salePrice >= originalPrice) return 0;
        return Math.round(((originalPrice - salePrice) / originalPrice) * 100);
      };
      const renderRoomPrice = (room) => {
        const originalPrice = getOriginalRoomPrice(room);
        const salePrice = getRoomPrice(room);

        if (salePrice <= 0) return 'Liên hệ';

        const discountPercent = getRoomDiscountPercent(room);
        if (discountPercent <= 0) {
          return `
            <span class="customer-room-price">
              <span class="customer-room-price-current">
                <span class="customer-room-price-sale">${salePrice.toLocaleString('vi-VN')} VNĐ</span>
                <span class="customer-room-price-per">/ đêm</span>
              </span>
            </span>
          `;
        }

        return `
          <span class="customer-room-price">
            <span class="customer-room-price-original">${originalPrice.toLocaleString('vi-VN')} VNĐ</span>
            <span class="customer-room-price-current">
              <span class="customer-room-price-sale">${salePrice.toLocaleString('vi-VN')} VNĐ</span>
              <span class="customer-room-price-per">/ đêm</span>
            </span>
            <span class="customer-room-discount-tag">-${discountPercent}%</span>
          </span>
        `;
      };

      function getRoomAmenities(room) {
        const amenities = room.tienNghis || room.tien_nghis || [];
        return Array.isArray(amenities) ? amenities.map((item) => item.TenTienNghi || item.ten_tien_nghi).filter(Boolean) : [];
      }

      function getRoomListFilterValues() {
        const adults = Number.parseInt(roomListFilterAdults?.value || '', 10);
        const children = Number.parseInt(roomListFilterChildren?.value || '', 10);

        return {
          keyword: String(roomListFilterSearch?.value || '').trim().toLowerCase(),
          sort: roomListFilterSort?.value || '',
          adults: Number.isFinite(adults) && adults > 0 ? adults : 0,
          children: Number.isFinite(children) && children > 0 ? children : 0,
        };
      }

      function getFilteredRooms() {
        const filters = getRoomListFilterValues();
        const rooms = allRooms.filter((room) => {
          const adults = Number(room.NguoiLon ?? room.nguoi_lon ?? 0);
          const children = Number(room.TreEm ?? room.tre_em ?? 0);
          const searchableText = [
            room.TenLoaiPhong || room.ten_loai_phong || '',
            room.Mota || room.mo_ta || '',
            ...getRoomAmenities(room),
          ].join(' ').toLowerCase();
          const matchesKeyword = !filters.keyword || searchableText.includes(filters.keyword);
          const matchesAdults = !filters.adults || (Number.isFinite(adults) && adults >= filters.adults);
          const matchesChildren = !filters.children || (Number.isFinite(children) && children >= filters.children);

          return matchesKeyword && matchesAdults && matchesChildren;
        });

        if (filters.sort === 'price-asc') {
          rooms.sort((left, right) => getRoomPrice(left) - getRoomPrice(right));
        }

        if (filters.sort === 'price-desc') {
          rooms.sort((left, right) => getRoomPrice(right) - getRoomPrice(left));
        }

        return rooms;
      }

      function setRoomsLoading() {
        if (!roomContainer) return;

        if (roomPagination) {
          roomPagination.hidden = true;
          roomPagination.innerHTML = '';
        }

        roomContainer.innerHTML = `
          <div class="col-12">
            <div class="room-list-loading" role="status">
              <span class="room-list-spinner" aria-hidden="true"></span>
              <span>Đang tải danh sách phòng...</span>
            </div>
          </div>
        `;
      }

      function setRoomsMessage(message, canRetry = false) {
        if (!roomContainer) return;

        if (roomPagination) {
          roomPagination.hidden = true;
          roomPagination.innerHTML = '';
        }

        roomContainer.innerHTML = `
          <div class="col-12">
            <div class="room-list-loading room-list-message">
              <span>${message}</span>
              ${canRetry ? '<button type="button" class="room-list-retry" data-room-retry>Tải lại</button>' : ''}
            </div>
          </div>
        `;

        roomContainer.querySelector('[data-room-retry]')?.addEventListener('click', loadRooms);
      }

      async function getRoomTypes() {
        if (window.CustomerRoomApi?.getRoomTypes) {
          return window.CustomerRoomApi.getRoomTypes();
        }

        const response = await fetch(`/api/loai-phong?_=${Date.now()}`, {
          cache: 'no-store',
          headers: {
            Accept: 'application/json',
            'Cache-Control': 'no-cache',
          },
        });
        const result = await response.json();

        if (!result.success || !Array.isArray(result.data)) {
          throw new Error(result.message || 'Failed to load rooms');
        }

        return result.data;
      }

      function applyRoomImages() {
        roomContainer.querySelectorAll('[data-bg-image]').forEach((element) => {
          const backgroundImage = element.getAttribute('data-bg-image');
          if (backgroundImage && backgroundImage !== 'undefined' && backgroundImage !== 'null') {
            element.style.backgroundImage = `url("${backgroundImage}")`;
          }
        });
      }

      function renderPagination() {
        if (!roomPagination) return;

        const pageCount = Math.ceil(visibleRooms.length / roomsPerPage);
        roomPagination.hidden = pageCount <= 1;

        if (pageCount <= 1) {
          roomPagination.innerHTML = '';
          return;
        }

        const pageButtons = Array.from({ length: pageCount }, (_, index) => {
          const page = index + 1;
          return `
            <button
              type="button"
              class="room-list-page${page === currentRoomPage ? ' is-active' : ''}"
              data-room-page="${page}"
              aria-current="${page === currentRoomPage ? 'page' : 'false'}"
            >${page}</button>
          `;
        }).join('');

        roomPagination.innerHTML = `
          <button type="button" class="room-list-page" data-room-page-prev ${currentRoomPage === 1 ? 'disabled' : ''}><<</button>
          ${pageButtons}
          <button type="button" class="room-list-page" data-room-page-next ${currentRoomPage === pageCount ? 'disabled' : ''}>>></button>
        `;

        roomPagination.querySelector('[data-room-page-prev]')?.addEventListener('click', () => renderRoomsPage(currentRoomPage - 1, true));
        roomPagination.querySelector('[data-room-page-next]')?.addEventListener('click', () => renderRoomsPage(currentRoomPage + 1, true));
        roomPagination.querySelectorAll('[data-room-page]').forEach((button) => {
          button.addEventListener('click', () => renderRoomsPage(Number(button.dataset.roomPage), true));
        });
      }

      function renderRoomsPage(page, shouldScroll = false) {
        const pageCount = Math.max(Math.ceil(visibleRooms.length / roomsPerPage), 1);
        currentRoomPage = Math.min(Math.max(Number(page) || 1, 1), pageCount);

        const startIndex = (currentRoomPage - 1) * roomsPerPage;
        const rooms = visibleRooms.slice(startIndex, startIndex + roomsPerPage);
        const fallbackImage = '{{ asset("customers/images/room-6.jpg") }}';
        const html = rooms.map((room, index) => {
          const absoluteIndex = startIndex + index;
          const roomId = room.MaLoaiPhong || room.id || absoluteIndex + 1;
          const detailUrl = `/customer/room/${encodeURIComponent(roomId)}`;
          const firstImage = Array.isArray(room.hinhs) ? room.hinhs[0] : null;
          const imageSrc = firstImage?.Url || firstImage?.url || firstImage?.DuongDan || firstImage?.duong_dan || fallbackImage;
          const arrowClass = absoluteIndex % 2 === 0 ? 'left-arrow' : 'right-arrow';
          const orderClass = absoluteIndex % 2 === 0 ? '' : 'order-md-last';

          return `
            <div class="col-12">
              <div class="room-wrap d-md-flex">
                <a href="${detailUrl}" class="img ${orderClass}" data-bg-image="${escapeAttr(imageSrc)}" aria-label="${escapeAttr(room.TenLoaiPhong || 'Phòng Peach Valley')}"></a>
                <div class="half ${arrowClass} d-flex align-items-center">
                  <div class="text p-4 text-center">
                    <h3 class="mb-3"><a href="${detailUrl}">${escapeHtml(room.TenLoaiPhong || 'Phòng Peach Valley')}</a></h3>
                    <p class="room-description mb-3">${escapeHtml(room.Mota || 'Phòng thoải mái và hiện đại')}</p>
                    <p class="mb-0 room-list-price"><span class="price mr-1">${renderRoomPrice(room)}</span></p>
                    <p class="pt-1"><a href="${detailUrl}" class="btn-custom px-3 py-2 rounded">Chi Tiết <span class="icon-long-arrow-right"></span></a></p>
                  </div>
                </div>
              </div>
            </div>
          `;
        }).join('');

        roomContainer.innerHTML = html;
        applyRoomImages();
        renderPagination();

        if (shouldScroll) {
          roomContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }

      function applyRoomListFilters() {
        visibleRooms = getFilteredRooms();
        currentRoomPage = 1;

        if (!visibleRooms.length) {
          setRoomsMessage('Không có loại phòng phù hợp với bộ lọc bạn chọn.');
          return;
        }

        renderRoomsPage(1);
      }

      async function loadRooms() {
        setRoomsLoading();

        try {
          const rooms = await getRoomTypes();

          if (!Array.isArray(rooms)) {
            setRoomsMessage('Dữ liệu phòng không hợp lệ.', true);
            return;
          }

          if (!rooms.length) {
            setRoomsMessage('Hiện chưa có loại phòng nào để hiển thị.');
            return;
          }

          allRooms = rooms;
          applyRoomListFilters();
        } catch (error) {
          console.error('Error loading rooms:', error);
          setRoomsMessage('Không thể tải danh sách phòng. Vui lòng thử lại.', true);
        }
      }

      roomListFilterSearch?.addEventListener('input', applyRoomListFilters);
      roomListFilterSort?.addEventListener('change', applyRoomListFilters);
      roomListFilterAdults?.addEventListener('input', applyRoomListFilters);
      roomListFilterChildren?.addEventListener('input', applyRoomListFilters);
      roomListFilterReset?.addEventListener('click', () => {
        if (roomListFilterSearch) roomListFilterSearch.value = '';
        if (roomListFilterSort) roomListFilterSort.value = '';
        if (roomListFilterAdults) roomListFilterAdults.value = '';
        if (roomListFilterChildren) roomListFilterChildren.value = '';
        applyRoomListFilters();
      });

      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadRooms);
      } else {
        loadRooms();
      }
    </script>


    @include('customer.partials.footer')
    
  

  <!-- loader -->
  <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>

  </body>
</html>
