@php
  $bookingAccount = $bookingAccount ?? session('auth_account', []);
  $isCustomerAccount = (int) ($bookingAccount['LoaiTaiKhoan'] ?? -1) === 0;
  $bookingAccount = $isCustomerAccount ? $bookingAccount : [];
  $bookingCustomer = $bookingCustomer ?? null;
  $bookingCustomerName = old('fullName', $bookingCustomer?->TenKH ?? ($bookingAccount['Ten'] ?? ''));
  $bookingCustomerPhone = old('phone', $bookingCustomer?->SoDienThoai ?? '');
@endphp
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
  <body class="booking-page">
    @include('customer.partials.nav')

    <section class="booking-section">
      <div class="container">
        <div class="booking-header">
          <a href="{{ route('customer.rooms-booking') }}" class="booking-back">
            <span class="icon ion-ios-arrow-back"></span>
            Quay láº¡i
          </a>
          <h2>THÃ”NG TIN Äáº¶T PHÃ’NG</h2>
        </div>

        <div class="row">
          <div class="col-lg-8">
            <div class="booking-card">
              <h3>ThÃ´ng tin ngÆ°á»i Ä‘áº·t phÃ²ng</h3>
              <form id="bookingForm" class="booking-form-grid" accept-charset="UTF-8">
                <div class="booking-field">
                  <label for="fullName">Há» tÃªn *</label>
                  <input 
                    type="text" 
                    id="fullName"
                    name="fullName"
                    value="{{ $bookingCustomerName }}"
                    placeholder="Nháº­p há» tÃªn"
                    data-validation="name"
                    required>
                  <small class="error-message" id="fullName-error"></small>
                </div>
                <div class="booking-field">
                  <label for="phone">Sá»‘ Ä‘iá»‡n thoáº¡i *</label>
                  <input 
                    type="tel" 
                    id="phone"
                    name="phone"
                    value="{{ $bookingCustomerPhone }}"
                    inputmode="numeric" 
                    pattern="[0-9]*" 
                    placeholder="0982 123 123"
                    data-validation="phone"
                    required>
                  <small class="error-message" id="phone-error"></small>
                </div>
              </form>
            </div>

            <div class="booking-card">
              <h3>ChÃ­nh sÃ¡ch Ä‘áº·t phÃ²ng</h3>
              <div class="booking-policy">
                <ul>
                  <li>KhÃ´ng thá»ƒ chá»‰nh sá»­a sau khi Ä‘áº·t phÃ²ng.</li>
                  <li>Thanh toÃ¡n Ä‘áº·t cá»c trÆ°á»›c.</li>
                  <li>KhÃ¡ch hÃ ng cÃ³ thá»ƒ Ä‘Æ°á»£c yÃªu cáº§u thanh toÃ¡n trÆ°á»›c tá»« 30% Ä‘áº¿n 100% tá»•ng giÃ¡ trá»‹ Ä‘áº·t phÃ²ng, tÃ¹y theo háº¡ng phÃ²ng, thá»i gian lÆ°u trÃº, thá»i Ä‘iá»ƒm Ä‘áº·t phÃ²ng vÃ  cÃ¡c chÆ°Æ¡ng trÃ¬nh Æ°u Ä‘Ã£i/khuyáº¿n mÃ£i Ä‘ang Ã¡p dá»¥ng.</li>
                </ul>
              </div>
            </div>

            <div class="booking-card">
              <h3>ChÃ­nh sÃ¡ch há»§y phÃ²ng</h3>
              <div class="booking-policy">
                <ul>
                  <li>Há»§y trÆ°á»›c 10â€“15 ngÃ y: CÃ³ thá»ƒ Ä‘Æ°á»£c miá»…n phÃ­ há»§y phÃ²ng.</li>
                  <li>Há»§y trÆ°á»›c 5â€“10 ngÃ y: CÃ³ thá»ƒ chá»‹u 30%â€“70% phÃ­ Ä‘áº·t phÃ²ng.</li>
                  <li>Há»§y trÆ°á»›c 1â€“5 ngÃ y: CÃ³ thá»ƒ chá»‹u 100% phÃ­ Ä‘áº·t phÃ²ng.</li>
                </ul>
              </div>
            </div>

            <div class="booking-card">
              <h3>PhÆ°Æ¡ng thá»©c thanh toÃ¡n</h3>
              <div class="booking-payment-group">
                <label class="booking-payment-option">
                  <input type="radio" name="payment" data-payment-option="zalopay" disabled>
                  <span class="booking-payment-title">Thanh toÃ¡n vá»›i mÃ£ QR <span class="booking-payment-note">(Ä‘ang báº£o trÃ¬)</span></span>
                  <img src="{{ asset('customers/images/zalopay.png') }}" alt="ZaloPay" class="booking-payment-logo-image">
                </label>
                <label class="booking-payment-option is-selected">
                  <input type="radio" name="payment" data-payment-option="card-domestic" data-vnpay-bank-code="VNBANK" checked>
                  <span class="booking-payment-title">Thanh toÃ¡n báº±ng tháº» ná»™i Ä‘á»‹a</span>
                </label>
                <label class="booking-payment-option">
                  <input type="radio" name="payment" data-payment-option="card-international" data-vnpay-bank-code="INTCARD">
                  <span class="booking-payment-title">Thanh toÃ¡n báº±ng tháº» quá»‘c táº¿</span>
                </label>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="booking-summary">
              <h4>YÃªu cáº§u Ä‘áº·t phÃ²ng cá»§a báº¡n</h4>
              <div class="booking-summary-block">
                <p><strong>KhÃ¡ch sáº¡n Peach Valley</strong></p>
              </div>
              <div class="booking-summary-dates">
                <div class="booking-date-block">
                  <span>Nháº­n phÃ²ng</span>
                  <strong>Thá»© Báº£y, 18 thÃ¡ng 04 2026</strong>
                  <small>Tá»« 14:00</small>
                </div>
                <div class="booking-date-divider">
                  <span>3 Ä‘Ãªm</span>
                  <span class="icon ion-ios-arrow-forward"></span>
                </div>
                <div class="booking-date-block">
                  <span>Tráº£ phÃ²ng</span>
                  <strong>Thá»© Ba, 21 thÃ¡ng 04 2026</strong>
                  <small>TrÆ°á»›c 12:00</small>
                </div>
              </div>
              <div class="booking-summary-block booking-summary-rooms-block">
                <div class="booking-summary-title">
                  <span>ThÃ´ng tin phÃ²ng</span>
                </div>
                <div class="booking-summary-rooms-scroll" data-booking-rooms>
                  <div class="booking-summary-room">
                    <p><strong>PhÃ²ng 1:</strong> Deluxe Twin</p>
                    <p>Sá»‘ ngÆ°á»i: 1 ngÆ°á»i lá»›n</p>
                    <p class="booking-summary-room-unit-price">ÄÆ¡n giÃ¡: 577.500 vnd/ Ä‘Ãªm x 3 Ä‘Ãªm</p>
                  </div>
                  <div class="booking-summary-room">
                    <p><strong>PhÃ²ng 2:</strong> Superior King</p>
                    <p>Sá»‘ ngÆ°á»i: 2 ngÆ°á»i lá»›n</p>
                    <p class="booking-summary-room-unit-price">ÄÆ¡n giÃ¡: 483.333 vnd/ Ä‘Ãªm x 3 Ä‘Ãªm</p>
                  </div>
                  <div class="booking-summary-room">
                    <p><strong>PhÃ²ng 3:</strong> Suite Junior</p>
                    <p>Sá»‘ ngÆ°á»i: 2 ngÆ°á»i lá»›n, 1 tráº» em</p>
                    <p class="booking-summary-room-unit-price">ÄÆ¡n giÃ¡: 700.000 vnd/ Ä‘Ãªm x 3 Ä‘Ãªm</p>
                  </div>
                  <div class="booking-summary-room">
                    <p><strong>PhÃ²ng 4:</strong> Standard Garden</p>
                    <p>Sá»‘ ngÆ°á»i: 1 ngÆ°á»i lá»›n</p>
                    <p class="booking-summary-room-unit-price">ÄÆ¡n giÃ¡: 300.000 vnd/ Ä‘Ãªm x 3 Ä‘Ãªm</p>
                  </div>
                  <div class="booking-summary-room">
                    <p><strong>PhÃ²ng 5:</strong> Deluxe Family</p>
                    <p>Sá»‘ ngÆ°á»i: 3 ngÆ°á»i lá»›n</p>
                    <p class="booking-summary-room-unit-price">ÄÆ¡n giÃ¡: 416.667 vnd/ Ä‘Ãªm x 3 Ä‘Ãªm</p>
                  </div>
                </div>
              </div>
              <div class="booking-promo">
                <label for="promoCode">Nháº­p mÃ£ khuyáº¿n máº¡i/ mÃ£ voucher</label>
                <div class="booking-promo-control">
                  <input type="text" id="promoCode" name="promoCode" value="" autocomplete="off">
                  <button type="button" id="applyPromoBtn">ÃP Dá»¤NG</button>
                </div>
                <div class="booking-promo-status" data-promo-status hidden></div>
              </div>
              <div class="booking-discount-summary" data-booking-discount>
                <div class="booking-price-row">
                  <span>GiÃ¡ gá»‘c:</span>
                  <strong data-booking-original>7,432,500 VND</strong>
                </div>
                <div class="booking-price-row booking-price-discount">
                  <span>GiÃ¡ giáº£m:</span>
                  <strong data-booking-discount-amount>-743,250 VND</strong>
                </div>
              </div>
              <div class="booking-summary-total">
                <span>Tá»•ng giÃ¡:</span>
                <strong data-booking-total>6,689,250 VND</strong>
              </div>
              <div class="booking-summary-deposit">
                <span>Tiá»n Ä‘áº·t cá»c:</span>
                <strong data-booking-deposit>6,689,250 VND</strong>
              </div>
              <button type="button" id="paymentBtn" class="btn btn-primary booking-submit booking-submit-full" data-payment-submit>Thanh toÃ¡n vá»›i VNPAY</button>
              <div class="booking-promo-status" data-payment-status hidden></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    @include('customer.partials.footer')

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const fullNameInput = document.getElementById('fullName');
        const phoneInput = document.getElementById('phone');
        const paymentBtn = document.getElementById('paymentBtn');
        const bookingForm = document.getElementById('bookingForm');
        const promoInput = document.getElementById('promoCode');
        const applyPromoBtn = document.getElementById('applyPromoBtn');
        const promoStatus = document.querySelector('[data-promo-status]');
        const discountSummary = document.querySelector('[data-booking-discount]');
        const originalTotal = document.querySelector('[data-booking-original]');
        const discountAmount = document.querySelector('[data-booking-discount-amount]');
        const total = document.querySelector('[data-booking-total]');
        const deposit = document.querySelector('[data-booking-deposit]');
        const paymentOptions = document.querySelectorAll('[data-payment-option]');
        const bookingRooms = document.querySelector('[data-booking-rooms]');
        const bookingDateBlocks = document.querySelectorAll('.booking-date-block');
        const bookingNights = document.querySelector('.booking-date-divider span:first-child');
        const paymentStatus = document.querySelector('[data-payment-status]');
        const zaloPayPaymentUrl = @json(url('/api/zalopay-payment'));
        const vnPayPaymentUrl = @json(url('/api/vnpay-payment'));
        const datPhongStoreUrl = @json(url('/api/dat-phong'));
        const customerId = @json($bookingCustomer?->MaKH ?? ($bookingAccount['MaKH'] ?? null));
        const paymentRedirectUrl = customerId ? `${window.location.origin}/customer/my-bookings` : `${window.location.origin}/customer`;
        const customerCode = @json((string) ($bookingCustomer?->MaKH ?? ($bookingAccount['MaKH'] ?? $bookingAccount['MaTK'] ?? 'guest')));
        let bookingOriginalTotal = 0;
        let bookingFinalTotal = 0;
        const bookingDiscountRate = 0.1;
        let appliedPromoCode = promoInput.value.trim();

        // Validation functions
        function validateName(value) {
          return /^[\p{L}\s]+$/u.test(value.trim());
        }

        function validatePhone(value) {
          return /^[0-9\s\-\+()]+$/.test(value.trim()) && value.trim().length >= 10;
        }

        function formatCurrency(value) {
          return `${Number(value || 0).toLocaleString('vi-VN')} VND`;
        }

        function formatUnitPrice(value) {
          return `${Number(value || 0).toLocaleString('vi-VN')} vnd`;
        }

        function renderUnitPrice(room) {
          const salePrice = Number(room.price || 0);
          const originalPrice = Number(room.originalPrice || room.price || 0);
          const discountPercent = Number(room.discountPercent || 0);

          if (salePrice <= 0) {
            return formatUnitPrice(0);
          }

          if (discountPercent <= 0 || originalPrice <= salePrice) {
            return `<span class="customer-room-price"><span class="customer-room-price-sale">${formatUnitPrice(salePrice)}</span></span>`;
          }

          return `
            <span class="customer-room-price">
              <span class="customer-room-price-original">${formatUnitPrice(originalPrice)}</span>
              <span class="customer-room-price-sale">${formatUnitPrice(salePrice)}</span>
              <span class="customer-room-discount-tag">-${discountPercent}%</span>
            </span>
          `;
        }

        function escapeHtml(value) {
          return String(value ?? '').replace(/[&<>"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;',
          }[char]));
        }

        function formatBookingDate(value) {
          const date = new Date(`${value}T00:00:00`);

          if (Number.isNaN(date.getTime())) {
            return '--';
          }

          return date.toLocaleDateString('vi-VN', {
            weekday: 'long',
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
          });
        }

        function getStoredBooking() {
          try {
            const rawValue = localStorage.getItem('peachBookingSelection');
            const booking = rawValue ? JSON.parse(rawValue) : null;
            return booking && Array.isArray(booking.rooms) ? booking : null;
          } catch (error) {
            return null;
          }
        }

        function renderStoredBooking() {
          const booking = getStoredBooking();

          if (!booking || !booking.rooms.length) {
            bookingOriginalTotal = 0;
            if (bookingRooms) {
              bookingRooms.innerHTML = '<div class="booking-summary-room"><p>ChÆ°a cÃ³ thÃ´ng tin phÃ²ng. Vui lÃ²ng quay láº¡i chá»n phÃ²ng.</p></div>';
            }
            return;
          }

          bookingOriginalTotal = Number(booking.total || 0);

          if (bookingDateBlocks[0]) {
            bookingDateBlocks[0].querySelector('strong').textContent = formatBookingDate(booking.checkIn);
          }

          if (bookingDateBlocks[1]) {
            bookingDateBlocks[1].querySelector('strong').textContent = formatBookingDate(booking.checkOut);
          }

          if (bookingNights) {
            bookingNights.textContent = `${Number(booking.nights || 1)} Ä‘Ãªm`;
          }

          if (bookingRooms) {
            let roomDisplayIndex = 0;

            bookingRooms.innerHTML = booking.rooms.flatMap((room) => {
              const quantity = Number(room.quantity || 0);
              const adultsPerRoom = Number(room.adults || booking.adults || 0);
              const childrenPerRoom = Number(room.children || booking.children || 0);
              const adults = adultsPerRoom;
              const children = childrenPerRoom;
              const guestText = [
                adults > 0 ? `${adults} ngÆ°á»i lá»›n` : '',
                children > 0 ? `${children} tráº» em` : '',
              ].filter(Boolean).join(', ') || 'Theo tiÃªu chÃ­ Ä‘Ã£ chá»n';
              const nights = Number(booking.nights || 1);

              return Array.from({ length: quantity }, () => {
                roomDisplayIndex += 1;

                return `
                <div class="booking-summary-room">
                  <p><strong>PhÃ²ng ${roomDisplayIndex}:</strong> ${escapeHtml(room.name)}</p>
                  <p>Sá»‘ ngÆ°á»i: ${escapeHtml(guestText)}</p>
                  <p class="booking-summary-room-unit-price">ÄÆ¡n giÃ¡: ${renderUnitPrice(room)}/ Ä‘Ãªm x ${nights} Ä‘Ãªm</p>
                </div>
              `;
              });
            }).join('');
          }
        }

        function updatePaymentButton(value) {
          if (!value) {
            const fallbackOption = document.querySelector('[data-payment-option]:checked:not(:disabled)')
              || document.querySelector('[data-payment-option]:not(:disabled)');
            value = fallbackOption?.dataset.paymentOption || '';

            if (fallbackOption) {
              fallbackOption.checked = true;
            }
          }

          document.querySelectorAll('.booking-payment-option').forEach(option => {
            option.classList.toggle('is-selected', option.querySelector('[data-payment-option]')?.dataset.paymentOption === value);
          });

          if (value === 'zalopay') {
            paymentBtn.textContent = 'Thanh toÃ¡n vá»›i QR';
            paymentBtn.disabled = false;
          } else if (value.startsWith('card-')) {
            paymentBtn.textContent = 'Thanh toÃ¡n vá»›i VNPAY';
            paymentBtn.disabled = false;
          } else {
            paymentBtn.textContent = 'Chá»n phÆ°Æ¡ng thá»©c thanh toÃ¡n';
            paymentBtn.disabled = true;
          }
        }

        function syncSelectedPayment() {
          const selectedOption = document.querySelector('[data-payment-option]:checked:not(:disabled)')
            || document.querySelector('[data-payment-option]:not(:disabled)');

          if (selectedOption) {
            selectedOption.checked = true;
          }

          updatePaymentButton(selectedOption?.dataset.paymentOption || '');
        }

        function setPaymentStatus(message, isError = false) {
          if (!paymentStatus) return;

          paymentStatus.hidden = !message;
          paymentStatus.textContent = message || '';
          paymentStatus.style.color = isError ? '#dc2626' : '';
        }

        function focusFirstInvalidField() {
          const firstInvalidField = document.querySelector('.error-message:not(:empty)')?.closest('.booking-field')?.querySelector('input');

          if (!firstInvalidField) {
            return;
          }

          firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
          firstInvalidField.focus({ preventScroll: true });
        }

        async function postJson(url, payload) {
          const response = await fetch(url, {
            method: 'POST',
            headers: {
              Accept: 'application/json',
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload),
          });
          const result = await response.json().catch(() => null);

          if (!response.ok) {
            const validationMessage = Object.values(result?.errors || {}).flat().filter(Boolean).join('\n');
            throw new Error(validationMessage || result?.message || 'Khong the xu ly yeu cau.');
          }

          return result;
        }

        async function createBookingHolds(booking) {
          const bookingCustomerKey = customerId
            ? `customer:${customerId}`
            : `phone:${phoneInput.value.replace(/\D+/g, '')}`;
          const storedHolds = JSON.parse(localStorage.getItem('peachBookingHolds') || 'null');

          if (
            storedHolds?.selectionSavedAt === booking.savedAt
            && storedHolds?.customerKey === bookingCustomerKey
            && Array.isArray(storedHolds.datPhongIds)
            && storedHolds.datPhongIds.length === 1
            && Number(storedHolds.total || 0) > 0
          ) {
            return storedHolds;
          }

          const result = await postJson(datPhongStoreUrl, {
            MaKH: customerId,
            TenKH: fullNameInput.value.trim(),
            SoDienThoai: phoneInput.value.trim(),
            NgayNhanPhong: booking.checkIn,
            NgayTraPhong: booking.checkOut,
            LoaiPhongs: booking.rooms.map((room) => ({
              MaLoaiPhong: room.id,
              SoLuong: Number(room.quantity || 0),
            })),
          });
          const maDatPhong = result?.data?.datPhong?.MaDatPhong;
          const serverTotal = Number(result?.data?.hoaDon?.TongTien || 0);

          if (!result?.success || !maDatPhong || serverTotal <= 0) {
            throw new Error(result?.message || 'Khong the tao giu cho dat phong.');
          }

          const datPhongIds = [maDatPhong];
          const holdData = {
            selectionSavedAt: booking.savedAt,
            customerKey: bookingCustomerKey,
            datPhongIds,
            total: serverTotal,
            createdAt: new Date().toISOString(),
          };

          localStorage.setItem('peachBookingHolds', JSON.stringify(holdData));

          return holdData;
        }

        function updateDiscountUI(promoCode) {
          const hasPromo = false;
          const discountValue = hasPromo ? Math.round(bookingOriginalTotal * bookingDiscountRate) : 0;
          const finalTotal = bookingOriginalTotal - discountValue;
          bookingFinalTotal = finalTotal;

          if (discountSummary) {
            discountSummary.hidden = !hasPromo;
          }

          if (promoStatus) {
            promoStatus.hidden = !hasPromo;
            promoStatus.textContent = hasPromo ? `ÄÃ£ Ã¡p dá»¥ng mÃ£ ${promoCode}: giáº£m 10%` : '';
          }

          if (originalTotal) {
            originalTotal.textContent = formatCurrency(bookingOriginalTotal);
          }

          if (discountAmount) {
            discountAmount.textContent = `-${formatCurrency(discountValue)}`;
          }

          if (total) {
            total.textContent = formatCurrency(finalTotal);
          }

          if (deposit) {
            deposit.textContent = formatCurrency(finalTotal);
          }
        }

        async function createZaloPayPayment() {
          const booking = getStoredBooking();

          if (!booking || !booking.rooms.length) {
            throw new Error('Vui lÃ²ng quay láº¡i chá»n Ã­t nháº¥t 1 phÃ²ng trÆ°á»›c khi thanh toÃ¡n.');
          }

          let amount = Math.max(Math.round(Number(bookingFinalTotal || 0)), 0);

          if (amount <= 0) {
            throw new Error('Tá»•ng tiá»n thanh toÃ¡n khÃ´ng há»£p lá»‡.');
          }

          setPaymentStatus('Dang giu phong trong 15 phut...');
          const holdData = await createBookingHolds(booking);
          const datPhongIds = holdData.datPhongIds;
          amount = Math.max(Math.round(Number(holdData.total || bookingFinalTotal || 0)), 0);
          if (amount <= 0) {
            throw new Error('Tong tien thanh toan khong hop le.');
          }
          setPaymentStatus('Dang tao ma QR thanh toan ZaloPay...');

          const response = await fetch(zaloPayPaymentUrl, {
            method: 'POST',
            headers: {
              Accept: 'application/json',
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              amount,
              app_user: customerCode || phoneInput.value.replace(/\D+/g, '') || 'guest',
              description: `Peach Valley - thanh toÃ¡n Ä‘áº·t phÃ²ng ${booking.checkIn} Ä‘áº¿n ${booking.checkOut}`,
              redirect_url: paymentRedirectUrl,
              dat_phong_ids: datPhongIds,
            }),
          });

          const result = await response.json().catch(() => null);

          if (!response.ok || result?.status !== 'success' || !result?.order_url) {
            throw new Error(result?.sub_message || result?.message || 'KhÃ´ng thá»ƒ táº¡o thanh toÃ¡n ZaloPay.');
          }

          localStorage.setItem('peachBookingPayment', JSON.stringify({
            appTransId: result.app_trans_id,
            datPhongIds,
            amount,
            provider: 'ZALOPAY',
            createdAt: new Date().toISOString(),
          }));

          window.location.href = result.order_url;
        }

        async function createVnPayPayment(bankCode) {
          const booking = getStoredBooking();

          if (!['VNBANK', 'INTCARD'].includes(bankCode)) {
            throw new Error('Vui lÃ²ng chá»n tháº» ná»™i Ä‘á»‹a hoáº·c tháº» quá»‘c táº¿.');
          }

          if (!booking || !booking.rooms.length) {
            throw new Error('Vui lÃ²ng quay láº¡i chá»n Ã­t nháº¥t 1 phÃ²ng trÆ°á»›c khi thanh toÃ¡n.');
          }

          let amount = Math.max(Math.round(Number(bookingFinalTotal || 0)), 0);

          if (amount <= 0) {
            throw new Error('Tá»•ng tiá»n thanh toÃ¡n khÃ´ng há»£p lá»‡.');
          }

          setPaymentStatus('Äang giá»¯ phÃ²ng trong 15 phÃºt...');
          const holdData = await createBookingHolds(booking);
          const datPhongIds = holdData.datPhongIds;
          amount = Math.max(Math.round(Number(holdData.total || bookingFinalTotal || 0)), 0);

          if (amount <= 0) {
            throw new Error('Tá»•ng tiá»n thanh toÃ¡n khÃ´ng há»£p lá»‡.');
          }

          setPaymentStatus('Äang táº¡o liÃªn káº¿t thanh toÃ¡n VNPAY...');

          const response = await fetch(vnPayPaymentUrl, {
            method: 'POST',
            headers: {
              Accept: 'application/json',
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              amount,
              description: `Peach Valley thanh toan dat phong ${booking.checkIn} den ${booking.checkOut}`,
              redirect_url: paymentRedirectUrl,
              dat_phong_ids: datPhongIds,
              bank_code: bankCode,
            }),
          });

          const result = await response.json().catch(() => null);

          if (!response.ok || result?.status !== 'success' || !result?.payment_url) {
            throw new Error(result?.message || 'KhÃ´ng thá»ƒ táº¡o thanh toÃ¡n VNPAY.');
          }

          localStorage.setItem('peachBookingPayment', JSON.stringify({
            appTransId: result.txn_ref,
            datPhongIds,
            amount,
            provider: 'VNPAY',
            createdAt: new Date().toISOString(),
          }));

          window.location.href = result.payment_url;
        }

        // Real-time validation for name (letters only)
        let isComposingName = false;

        fullNameInput.addEventListener('input', function(e) {
          if (isComposingName) {
            return;
          }

          const value = e.target.value;
          // Remove non-letter characters
          const sanitized = value.replace(/[^\p{L}\s]/gu, '');
          if (sanitized !== value) {
            e.target.value = sanitized;
          }
          // Clear error message on valid input
          if (validateName(sanitized)) {
            document.getElementById('fullName-error').textContent = '';
          }
        });

        fullNameInput.addEventListener('compositionstart', function() {
          isComposingName = true;
        });

        fullNameInput.addEventListener('compositionend', function(e) {
          isComposingName = false;
          const sanitized = e.target.value.replace(/[^\p{L}\s]/gu, '');

          if (sanitized !== e.target.value) {
            e.target.value = sanitized;
          }

          if (validateName(sanitized)) {
            document.getElementById('fullName-error').textContent = '';
          }
        });

        // Real-time validation for phone (numbers only)
        phoneInput.addEventListener('input', function(e) {
          const value = e.target.value;
          // Remove non-number characters (but allow spaces, dashes, parentheses)
          const sanitized = value.replace(/[^0-9\s\-()]/g, '');
          if (sanitized !== value) {
            e.target.value = sanitized;
          }
          // Clear error message on valid input
          if (validatePhone(sanitized)) {
            document.getElementById('phone-error').textContent = '';
          }
        });

        applyPromoBtn.addEventListener('click', function() {
          const promoCode = promoInput.value.trim();

          if (!promoCode) {
            appliedPromoCode = '';
            localStorage.removeItem('peachBookingPromo');
            updateDiscountUI('');
            promoInput.focus();
            return;
          }

          appliedPromoCode = promoCode;
          const discountAmount = Math.round(bookingOriginalTotal * bookingDiscountRate);
          updateDiscountUI(promoCode);

          localStorage.setItem('peachBookingPromo', JSON.stringify({
            code: promoCode,
            originalTotal: bookingOriginalTotal,
            discountAmount: discountAmount,
            finalTotal: bookingOriginalTotal - discountAmount
          }));
        });

        paymentOptions.forEach(function(input) {
          input.addEventListener('change', function(event) {
            event.stopImmediatePropagation();
            updatePaymentButton(input.dataset.paymentOption);
          }, true);
        });

        function handlePaymentSubmit(e) {
          if (e.peachPaymentHandled) {
            return;
          }

          e.peachPaymentHandled = true;
          e.preventDefault();
          
          const fullName = document.getElementById('fullName').value.trim();
          const phone = document.getElementById('phone').value.trim();

          // Clear previous errors
          document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
          setPaymentStatus('');

          let hasError = false;

          if (!fullName) {
            document.getElementById('fullName-error').textContent = 'Vui lÃ²ng nháº­p há» tÃªn';
            hasError = true;
          } else if (!validateName(fullName)) {
            document.getElementById('fullName-error').textContent = 'Há» tÃªn chá»‰ chá»©a chá»¯ cÃ¡i';
            hasError = true;
          }

          if (!phone) {
            document.getElementById('phone-error').textContent = 'Vui lÃ²ng nháº­p sá»‘ Ä‘iá»‡n thoáº¡i';
            hasError = true;
          } else if (!validatePhone(phone)) {
            document.getElementById('phone-error').textContent = 'Sá»‘ Ä‘iá»‡n thoáº¡i khÃ´ng há»£p lá»‡ (tá»‘i thiá»ƒu 10 chá»¯ sá»‘)';
            hasError = true;
          }

          if (hasError) {
            setPaymentStatus('Vui lÃ²ng kiá»ƒm tra láº¡i thÃ´ng tin ngÆ°á»i Ä‘áº·t phÃ²ng.', true);
            focusFirstInvalidField();
            return;
          }

          if (!hasError) {
            if (!appliedPromoCode) {
              localStorage.removeItem('peachBookingPromo');
            }

            const selectedPayment = document.querySelector('[data-payment-option]:checked')?.dataset.paymentOption || '';
            const selectedPaymentInput = document.querySelector('[data-payment-option]:checked');
            updatePaymentButton(selectedPayment);

            if (selectedPayment.startsWith('card-')) {
              const bankCode = selectedPaymentInput?.dataset.vnpayBankCode || '';
              paymentBtn.disabled = true;
              setPaymentStatus('Äang táº¡o liÃªn káº¿t thanh toÃ¡n VNPAY...');

              createVnPayPayment(bankCode)
                .catch((error) => {
                  console.error('VNPAY payment error:', error);
                  setPaymentStatus(error.message || 'KhÃ´ng thá»ƒ táº¡o thanh toÃ¡n VNPAY.', true);
                  paymentBtn.disabled = false;
                });
              return;
            }

            if (selectedPayment !== 'zalopay') {
              setPaymentStatus('Vui lÃ²ng chá»n phÆ°Æ¡ng thá»©c thanh toÃ¡n há»£p lá»‡.', true);
              return;
            }

            paymentBtn.disabled = true;
            setPaymentStatus('Äang táº¡o mÃ£ QR thanh toÃ¡n ZaloPay...');

            createZaloPayPayment()
              .catch((error) => {
                console.error('ZaloPay payment error:', error);
                setPaymentStatus(error.message || 'KhÃ´ng thá»ƒ táº¡o thanh toÃ¡n ZaloPay.', true);
                paymentBtn.disabled = false;
              });
          }
        }

        // Payment button validation
        paymentBtn.addEventListener('click', handlePaymentSubmit);
        document.addEventListener('click', function(event) {
          const submitButton = event.target.closest('[data-payment-submit]');

          if (!submitButton || submitButton !== paymentBtn) {
            return;
          }

          handlePaymentSubmit(event);
        }, true);

        renderStoredBooking();
        updateDiscountUI(appliedPromoCode);
        syncSelectedPayment();
        requestAnimationFrame(syncSelectedPayment);
        window.setTimeout(syncSelectedPayment, 0);
      });
    </script>
  </body>
</html>

