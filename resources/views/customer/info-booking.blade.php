@php
  $bookingAccount = $bookingAccount ?? session('auth_account', []);
  $isCustomerAccount = (int) ($bookingAccount['LoaiTaiKhoan'] ?? -1) === 0;
  $bookingAccount = $isCustomerAccount ? $bookingAccount : [];
  $bookingCustomer = $bookingCustomer ?? null;
  $bookingCustomerName = old('fullName', $bookingCustomer?->TenKH ?? ($bookingAccount['Ten'] ?? ''));
  $bookingCustomerPhone = old('phone', $bookingCustomer?->SoDienThoai ?? '');
  $bookingPromotions = collect($bookingPromotions ?? []);
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
            Quay lại
          </a>
          <h2>THÔNG TIN ĐẶT PHÒNG</h2>
        </div>

        <div class="row">
          <div class="col-lg-8">
            <div class="booking-card">
              <h3>Thông tin người đặt phòng</h3>
              <form id="bookingForm" class="booking-form-grid" accept-charset="UTF-8">
                <div class="booking-field">
                  <label for="fullName">Họ tên *</label>
                  <input 
                    type="text" 
                    id="fullName"
                    name="fullName"
                    value="{{ $bookingCustomerName }}"
                    placeholder="Nhập họ tên"
                    data-validation="name"
                    required>
                  <small class="error-message" id="fullName-error"></small>
                </div>
                <div class="booking-field">
                  <label for="phone">Số điện thoại *</label>
                  <input 
                    type="tel" 
                    id="phone"
                    name="phone"
                    value="{{ $bookingCustomerPhone }}"
                    inputmode="numeric" 
                    pattern="[0-9]*" 
                    maxlength="10"
                    placeholder="0982 123 123"
                    data-validation="phone"
                    required>
                  <small class="error-message" id="phone-error"></small>
                </div>
              </form>
            </div>

            <div class="booking-card">
              <h3>Chính sách đặt phòng</h3>
              <div class="booking-policy">
                <ul>
                  <li>Không thể chỉnh sửa sau khi đặt phòng.</li>
                  <li>Khách hàng cần thanh toán trước 100% tiền phòng để được xác nhận đăt phòng</li>
                </ul>
              </div>
            </div>

            <div class="booking-card">
              <h3>Chính sách hủy phòng</h3>
              <div class="booking-policy">
                <ul>
                  <li>Hủy trước 10–15 ngày: Có thể được miễn phí hủy phòng.</li>
                  <li>Hủy trước 5–10 ngày: Có thể chịu 30%–70% phí đặt phòng.</li>
                  <li>Hủy trước 1–5 ngày: Có thể chịu 100% phí đặt phòng.</li>
                </ul>
              </div>
            </div>

            <div class="booking-card">
              <h3>Phương thức thanh toán</h3>
              <div class="booking-payment-group">
                <label class="booking-payment-option">
                  <input type="radio" name="payment" data-payment-option="zalopay">
                  <span class="booking-payment-title">Thanh toán với mã QR</span>
                  <img src="{{ asset('customers/images/zalopay.png') }}" alt="ZaloPay" class="booking-payment-logo-image">
                </label>
                <label class="booking-payment-option is-selected">
                  <input type="radio" name="payment" data-payment-option="card-domestic" data-vnpay-bank-code="VNBANK" checked>
                  <span class="booking-payment-title">Thanh toán bằng thẻ nội địa</span>
                </label>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="booking-summary">
              <h4>Yêu cầu đặt phòng của bạn</h4>
              <div class="booking-summary-block">
                <p><strong>Khách sạn Peach Valley</strong></p>
              </div>
              <div class="booking-summary-dates">
                <div class="booking-date-block">
                  <span>Nhận phòng</span>
                  <strong>Thứ Bảy, 18 tháng 04 2026</strong>
                  <small>Từ 14:00</small>
                </div>
                <div class="booking-date-divider">
                  <span>3 đêm</span>
                  <span class="icon ion-ios-arrow-forward"></span>
                </div>
                <div class="booking-date-block">
                  <span>Trả phòng</span>
                  <strong>Thứ Ba, 21 tháng 04 2026</strong>
                  <small>Trước 12:00</small>
                </div>
              </div>
              <div class="booking-summary-block booking-summary-rooms-block">
                <div class="booking-summary-title">
                  <span>Thông tin phòng</span>
                </div>
                <div class="booking-summary-rooms-scroll" data-booking-rooms>
                  <div class="booking-summary-room">
                    <p><strong>Phòng 1:</strong> Deluxe Twin</p>
                    <p>Số người: 1 người lớn</p>
                    <p class="booking-summary-room-unit-price">Đơn giá: 577.500 vnd/ đêm x 3 đêm</p>
                  </div>
                  <div class="booking-summary-room">
                    <p><strong>Phòng 2:</strong> Superior King</p>
                    <p>Số người: 2 người lớn</p>
                    <p class="booking-summary-room-unit-price">Đơn giá: 483.333 vnd/ đêm x 3 đêm</p>
                  </div>
                  <div class="booking-summary-room">
                    <p><strong>Phòng 3:</strong> Suite Junior</p>
                    <p>Số người: 2 người lớn, 1 trẻ em</p>
                    <p class="booking-summary-room-unit-price">Đơn giá: 700.000 vnd/ đêm x 3 đêm</p>
                  </div>
                  <div class="booking-summary-room">
                    <p><strong>Phòng 4:</strong> Standard Garden</p>
                    <p>Số người: 1 người lớn</p>
                    <p class="booking-summary-room-unit-price">Đơn giá: 300.000 vnd/ đêm x 3 đêm</p>
                  </div>
                  <div class="booking-summary-room">
                    <p><strong>Phòng 5:</strong> Deluxe Family</p>
                    <p>Số người: 3 người lớn</p>
                    <p class="booking-summary-room-unit-price">Đơn giá: 416.667 vnd/ đêm x 3 đêm</p>
                  </div>
                </div>
              </div>
              <div class="booking-promo">
                <label for="promoCode">Nhập mã khuyến mại/ mã voucher</label>
                <div class="booking-promo-control">
                  <select id="promoCode" name="promoCode" {{ $bookingPromotions->isEmpty() ? 'disabled' : '' }}>
                    <option value="">Chọn mã khuyến mãi</option>
                    @foreach($bookingPromotions as $promotion)
                      <option
                        value="{{ $promotion['code'] }}"
                        data-discount-percent="{{ $promotion['discountPercent'] }}"
                        data-promotion-name="{{ $promotion['name'] }}"
                        data-expires-at="{{ $promotion['expiresAt'] }}"
                      >
                        {{ $promotion['code'] }} - giảm {{ rtrim(rtrim(number_format($promotion['discountPercent'], 2, ',', '.'), '0'), ',') }}%
                      </option>
                    @endforeach
                  </select>
                  <button type="button" id="applyPromoBtn">ÁP DỤNG</button>
                </div>
                <div class="booking-promo-status" data-promo-status hidden></div>
              </div>
              <div class="booking-discount-summary" data-booking-discount>
                <div class="booking-price-row">
                  <span>Giá gốc:</span>
                  <strong data-booking-original>7,432,500 VND</strong>
                </div>
                <div class="booking-price-row booking-price-discount">
                  <span>Giá giảm:</span>
                  <strong data-booking-discount-amount>-743,250 VND</strong>
                </div>
              </div>
              <div class="booking-summary-total">
                <span>Tổng giá:</span>
                <strong data-booking-total>6,689,250 VND</strong>
              </div>
              <div class="booking-summary-deposit">
                <span>Tổng thanh toán:</span>
                <strong data-booking-deposit>6,689,250 VND</strong>
              </div>
              <button type="button" id="paymentBtn" class="btn btn-primary booking-submit booking-submit-full" data-payment-submit>Thanh toán với VNPAY</button>
              <div class="booking-promo-status" data-payment-status hidden></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div class="service-booking-warning-modal" data-payment-error-modal hidden>
      <div class="service-booking-warning-backdrop" data-payment-error-close></div>
      <div class="service-booking-warning-dialog" role="dialog" aria-modal="true" aria-labelledby="payment_error_title">
        <button type="button" class="service-booking-warning-close" data-payment-error-close aria-label="Đóng">&times;</button>
        <div class="service-booking-warning-icon">!</div>
        <h2 id="payment_error_title">Không thể đặt phòng</h2>
        <p data-payment-error-message>Đã có lỗi xảy ra. Vui lòng thử lại.</p>
        <button type="button" class="service-booking-warning-action" data-payment-error-close>Đóng</button>
      </div>
    </div>

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
        const paymentErrorModal = document.querySelector('[data-payment-error-modal]');
        const paymentErrorMessage = document.querySelector('[data-payment-error-message]');
        const zaloPayPaymentUrl = @json(url('/api/zalopay-payment'));
        const vnPayPaymentUrl = @json(url('/api/vnpay-payment'));
        const datPhongStoreUrl = @json(url('/api/dat-phong'));
        const customerId = @json($bookingCustomer?->MaKH ?? ($bookingAccount['MaKH'] ?? null));
        const promotionOptions = @json($bookingPromotions);
        const paymentRedirectUrl = customerId ? `${window.location.origin}/customer/my-bookings` : `${window.location.origin}/customer`;
        const customerCode = @json((string) ($bookingCustomer?->MaKH ?? ($bookingAccount['MaKH'] ?? $bookingAccount['MaTK'] ?? 'guest')));
        let bookingOriginalTotal = 0;
        let bookingFinalTotal = 0;
        let appliedPromotion = null;
        let appliedPromoCode = promoInput.value;

        // Validation functions
        function validateName(value) {
          return /^[\p{L}\s]+$/u.test(value.trim());
        }

        function validatePhone(value) {
          return /^0\d{9}$/.test(value.trim());
        }

        function formatCurrency(value) {
          return `${Number(value || 0).toLocaleString('vi-VN')} VND`;
        }

        function formatPercent(value) {
          return `${Number(value || 0).toLocaleString('vi-VN', { maximumFractionDigits: 2 })}%`;
        }

        function formatUnitPrice(value) {
          return `${Number(value || 0).toLocaleString('vi-VN')} vnd`;
        }

        function renderUnitPrice(room) {
          const salePrice = Number(room.price || 0);

          if (salePrice <= 0) {
            return formatUnitPrice(0);
          }

          return `
            <span class="customer-room-price">
              <span class="customer-room-price-current">
                <span class="customer-room-price-sale">${formatUnitPrice(salePrice)}</span>
              </span>
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
              bookingRooms.innerHTML = '<div class="booking-summary-room"><p>Chưa có thông tin phòng. Vui lòng quay lại chọn phòng.</p></div>';
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
            bookingNights.textContent = `${Number(booking.nights || 1)} đêm`;
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
                adults > 0 ? `${adults} người lớn` : '',
                children > 0 ? `${children} trẻ em` : '',
              ].filter(Boolean).join(', ') || 'Theo tiêu chí đã chọn';
              const nights = Number(booking.nights || 1);

              return Array.from({ length: quantity }, () => {
                roomDisplayIndex += 1;

                return `
                <div class="booking-summary-room">
                  <p><strong>Phòng ${roomDisplayIndex}:</strong> ${escapeHtml(room.name)}</p>
                  <p>Số người: ${escapeHtml(guestText)}</p>
                  <p class="booking-summary-room-unit-price">
                    <span class="booking-summary-room-unit-label">Đơn giá:</span>
                    <span class="booking-summary-room-unit-value">${renderUnitPrice(room)}</span>
                    <span class="booking-summary-room-unit-meta">/ đêm x ${nights} đêm</span>
                  </p>
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
            paymentBtn.textContent = 'Thanh toán với QR';
            paymentBtn.disabled = false;
          } else if (value.startsWith('card-')) {
            paymentBtn.textContent = 'Thanh toán với VNPAY';
            paymentBtn.disabled = false;
          } else {
            paymentBtn.textContent = 'Chọn phương thức thanh toán';
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

        function openPaymentErrorModal(message) {
          const fallbackMessage = 'Không thể tiếp tục đến thanh toán. Vui lòng kiểm tra lại thông tin hoặc chọn phòng khác.';
          const displayMessage = String(message || fallbackMessage).trim() || fallbackMessage;

          setPaymentStatus(displayMessage, true);

          if (!paymentErrorModal || !paymentErrorMessage) {
            return;
          }

          paymentErrorMessage.textContent = displayMessage;
          paymentErrorModal.hidden = false;
          document.body.classList.add('modal-open');
          window.requestAnimationFrame(() => paymentErrorModal.classList.add('is-open'));
        }

        function closePaymentErrorModal() {
          if (!paymentErrorModal) {
            return;
          }

          paymentErrorModal.classList.remove('is-open');
          document.body.classList.remove('modal-open');

          window.setTimeout(() => {
            paymentErrorModal.hidden = true;
          }, 150);
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
          const activePromoCode = appliedPromotion?.code || '';

          if (
            storedHolds?.selectionSavedAt === booking.savedAt
            && storedHolds?.customerKey === bookingCustomerKey
            && String(storedHolds?.promoCode || '') === String(activePromoCode)
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
            MaKM: activePromoCode || null,
          });
          const maDatPhong = result?.data?.datPhong?.MaDatPhong;
          const serverTotal = Number(result?.data?.hoaDon?.TongTien || 0);
          const serverDiscount = Math.max(bookingOriginalTotal - serverTotal, 0);

          if (!result?.success || !maDatPhong || serverTotal <= 0) {
            throw new Error(result?.message || 'Khong the tao giu cho dat phong.');
          }

          bookingFinalTotal = serverTotal;

          if (activePromoCode && serverDiscount > 0) {
            if (discountSummary) {
              discountSummary.hidden = false;
            }

            if (discountAmount) {
              discountAmount.textContent = `-${formatCurrency(serverDiscount)}`;
            }
          }

          if (total) {
            total.textContent = formatCurrency(serverTotal);
          }

          if (deposit) {
            deposit.textContent = formatCurrency(serverTotal);
          }

          const datPhongIds = [maDatPhong];
          const holdData = {
            selectionSavedAt: booking.savedAt,
            customerKey: bookingCustomerKey,
            datPhongIds,
            total: serverTotal,
            promoCode: activePromoCode,
            createdAt: new Date().toISOString(),
          };

          localStorage.setItem('peachBookingHolds', JSON.stringify(holdData));

          return holdData;
        }

        function updateDiscountUI() {
          const discountPercent = Math.min(Math.max(Number(appliedPromotion?.discountPercent || 0), 0), 100);
          const hasPromo = Boolean(appliedPromotion && discountPercent > 0);
          const promoCode = appliedPromotion?.code || '';
          const discountValue = hasPromo ? Math.round(bookingOriginalTotal * discountPercent / 100) : 0;
          const finalTotal = bookingOriginalTotal - discountValue;
          bookingFinalTotal = finalTotal;

          if (discountSummary) {
            discountSummary.hidden = !hasPromo;
          }

          if (promoStatus) {
            promoStatus.hidden = !hasPromo;
            promoStatus.textContent = hasPromo ? `Đã áp dụng mã ${promoCode}: giảm ${formatPercent(discountPercent)}` : '';
          }

          if (promoStatus && hasPromo) {
            promoStatus.textContent = `Đã áp dụng mã ${appliedPromotion.code}: giảm ${formatPercent(discountPercent)}`;
            promoStatus.style.color = '';
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
            throw new Error('Vui lòng quay lại chọn ít nhất 1 phòng trước khi thanh toán.');
          }

          let amount = Math.max(Math.round(Number(bookingFinalTotal || 0)), 0);

          if (amount <= 0) {
            throw new Error('Tổng tiền thanh toán không hợp lệ.');
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
              description: `Peach Valley - thanh toán đặt phòng ${booking.checkIn} đến ${booking.checkOut}`,
              redirect_url: paymentRedirectUrl,
              dat_phong_ids: datPhongIds,
            }),
          });

          const result = await response.json().catch(() => null);

          if (!response.ok || result?.status !== 'success' || !result?.order_url) {
            throw new Error(result?.sub_message || result?.message || 'Không thể tạo thanh toán ZaloPay.');
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

          if (bankCode !== 'VNBANK') {
            throw new Error('Vui lòng chọn thẻ nội địa.');
          }

          if (!booking || !booking.rooms.length) {
            throw new Error('Vui lòng quay lại chọn ít nhất 1 phòng trước khi thanh toán.');
          }

          let amount = Math.max(Math.round(Number(bookingFinalTotal || 0)), 0);

          if (amount <= 0) {
            throw new Error('Tổng tiền thanh toán không hợp lệ.');
          }

          setPaymentStatus('Đang giữ phòng trong 15 phút...');
          const holdData = await createBookingHolds(booking);
          const datPhongIds = holdData.datPhongIds;
          amount = Math.max(Math.round(Number(holdData.total || bookingFinalTotal || 0)), 0);

          if (amount <= 0) {
            throw new Error('Tổng tiền thanh toán không hợp lệ.');
          }

          setPaymentStatus('Đang tạo liên kết thanh toán VNPAY...');

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
            throw new Error(result?.message || 'Không thể tạo thanh toán VNPAY.');
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
          // Remove non-number characters and keep the same 10-digit phone rule as the receptionist form.
          const sanitized = value.replace(/\D+/g, '').slice(0, 10);
          if (sanitized !== value) {
            e.target.value = sanitized;
          }
          // Clear error message on valid input
          if (validatePhone(sanitized)) {
            document.getElementById('phone-error').textContent = '';
          }
        });

        applyPromoBtn.addEventListener('click', function() {
          const promoCode = promoInput.value;

          if (!promoCode) {
            appliedPromoCode = '';
            appliedPromotion = null;
            localStorage.removeItem('peachBookingPromo');
            updateDiscountUI();

            if (promoStatus) {
              promoStatus.hidden = false;
              promoStatus.textContent = promotionOptions.length
                ? 'Vui lòng chọn mã khuyến mãi.'
                : 'Bạn chưa có mã khuyến mãi còn hạn.';
              promoStatus.style.color = '#dc2626';
            }

            return;
          }

          const selectedPromotion = promotionOptions.find((promotion) => String(promotion.code) === String(promoCode));

          if (!selectedPromotion) {
            appliedPromoCode = '';
            appliedPromotion = null;
            updateDiscountUI();

            if (promoStatus) {
              promoStatus.hidden = false;
              promoStatus.textContent = 'Mã khuyến mãi không hợp lệ.';
              promoStatus.style.color = '#dc2626';
            }

            return;
          }

          appliedPromoCode = promoCode;
          appliedPromotion = selectedPromotion;
          const discountAmount = Math.round(bookingOriginalTotal * Number(selectedPromotion.discountPercent || 0) / 100);
          updateDiscountUI();

          localStorage.setItem('peachBookingPromo', JSON.stringify({
            code: promoCode,
            discountPercent: Number(selectedPromotion.discountPercent || 0),
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
            document.getElementById('fullName-error').textContent = 'Vui lòng nhập họ tên';
            hasError = true;
          } else if (!validateName(fullName)) {
            document.getElementById('fullName-error').textContent = 'Họ tên chỉ chứa chữ cái';
            hasError = true;
          }

          if (!phone) {
            document.getElementById('phone-error').textContent = 'Vui lòng nhập số điện thoại';
            hasError = true;
          } else if (!validatePhone(phone)) {
            document.getElementById('phone-error').textContent = 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0';
            hasError = true;
          }

          if (hasError) {
            openPaymentErrorModal('Vui lòng kiểm tra lại thông tin người đặt phòng.');
            setPaymentStatus('Vui lòng kiểm tra lại thông tin người đặt phòng.', true);
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
              setPaymentStatus('Đang tạo liên kết thanh toán VNPAY...');

              createVnPayPayment(bankCode)
                .catch((error) => {
                  console.error('VNPAY payment error:', error);
                  openPaymentErrorModal(error.message || 'Không thể tạo thanh toán VNPAY.');
                  setPaymentStatus(error.message || 'Không thể tạo thanh toán VNPAY.', true);
                  paymentBtn.disabled = false;
                });
              return;
            }

            if (selectedPayment !== 'zalopay') {
              openPaymentErrorModal('Vui lòng chọn phương thức thanh toán hợp lệ.');
              setPaymentStatus('Vui lòng chọn phương thức thanh toán hợp lệ.', true);
              return;
            }

            paymentBtn.disabled = true;
            setPaymentStatus('Đang tạo mã QR thanh toán ZaloPay...');

            createZaloPayPayment()
              .catch((error) => {
                console.error('ZaloPay payment error:', error);
                openPaymentErrorModal(error.message || 'Không thể tạo thanh toán ZaloPay.');
                setPaymentStatus(error.message || 'Không thể tạo thanh toán ZaloPay.', true);
                paymentBtn.disabled = false;
              });
          }
        }

        document.querySelectorAll('[data-payment-error-close]').forEach((button) => {
          button.addEventListener('click', closePaymentErrorModal);
        });

        document.addEventListener('keydown', function(event) {
          if (event.key === 'Escape' && paymentErrorModal && !paymentErrorModal.hidden) {
            closePaymentErrorModal();
          }
        });

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
        updateDiscountUI();
        syncSelectedPayment();
        requestAnimationFrame(syncSelectedPayment);
        window.setTimeout(syncSelectedPayment, 0);
      });
    </script>
  </body>
</html>
