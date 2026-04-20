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
              <form id="bookingForm" class="booking-form-grid">
                <div class="booking-field">
                  <label for="fullName">Họ tên *</label>
                  <input 
                    type="text" 
                    id="fullName"
                    name="fullName"
                    placeholder="Nhập họ tên"
                    data-validation="name"
                    required>
                  <small class="error-message" id="fullName-error"></small>
                </div>
                <div class="booking-field">
                  <label for="email">Email *</label>
                  <input 
                    type="email" 
                    id="email"
                    name="email"
                    placeholder="email@gmail.com"
                    required>
                  <small class="error-message" id="email-error"></small>
                </div>
                <div class="booking-field">
                  <label for="phone">Số điện thoại *</label>
                  <input 
                    type="tel" 
                    id="phone"
                    name="phone"
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
              <h3>Chính sách đặt phòng</h3>
              <div class="booking-policy">
                <ul>
                  <li>Không thể chỉnh sửa sau khi đặt phòng.</li>
                  <li>Thanh toán đặt cọc trước.</li>
                  <li>Khách hàng có thể được yêu cầu thanh toán trước từ 30% đến 100% tổng giá trị đặt phòng, tùy theo hạng phòng, thời gian lưu trú, thời điểm đặt phòng và các chương trình ưu đãi/khuyến mãi đang áp dụng.</li>
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
                <label class="booking-payment-option is-selected">
                  <input type="radio" name="payment" data-payment-option="zalopay" checked>
                  <span class="booking-payment-title">ZaloPay</span>
                  <img src="{{ Vite::asset('resources/customer/images/zalopay.png') }}" alt="ZaloPay" class="booking-payment-logo-image">
                </label>
                <label class="booking-payment-option">
                  <input type="radio" name="payment" data-payment-option="card">
                  <span class="booking-payment-title">Thẻ thanh toán</span>
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
                <div class="booking-summary-rooms-scroll">
                  <div class="booking-summary-room">
                    <p><strong>Phòng 1:</strong> Deluxe Twin</p>
                    <p>Số phòng: 1 phòng</p>
                    <p>Số người: 1 người lớn</p>
                    <p class="booking-summary-room-price">Giá phòng: 1,732,500 VND</p>
                  </div>
                  <div class="booking-summary-room">
                    <p><strong>Phòng 2:</strong> Superior King</p>
                    <p>Số phòng: 1 phòng</p>
                    <p>Số người: 2 người lớn</p>
                    <p class="booking-summary-room-price">Giá phòng: 1,450,000 VND</p>
                  </div>
                  <div class="booking-summary-room">
                    <p><strong>Phòng 3:</strong> Suite Junior</p>
                    <p>Số phòng: 1 phòng</p>
                    <p>Số người: 2 người lớn, 1 trẻ em</p>
                    <p class="booking-summary-room-price">Giá phòng: 2,100,000 VND</p>
                  </div>
                  <div class="booking-summary-room">
                    <p><strong>Phòng 4:</strong> Standard Garden</p>
                    <p>Số phòng: 1 phòng</p>
                    <p>Số người: 1 người lớn</p>
                    <p class="booking-summary-room-price">Giá phòng: 900,000 VND</p>
                  </div>
                  <div class="booking-summary-room">
                    <p><strong>Phòng 5:</strong> Deluxe Family</p>
                    <p>Số phòng: 1 phòng</p>
                    <p>Số người: 3 người lớn</p>
                    <p class="booking-summary-room-price">Giá phòng: 1,250,000 VND</p>
                  </div>
                </div>
              </div>
              <div class="booking-promo">
                <label for="promoCode">Nhập mã khuyến mại/ mã voucher</label>
                <div class="booking-promo-control">
                  <input type="text" id="promoCode" name="promoCode" value="PEACH10" autocomplete="off">
                  <button type="button" id="applyPromoBtn">ÁP DỤNG</button>
                </div>
                <div class="booking-promo-status" data-promo-status>Đã áp dụng mã PEACH10: giảm 10%</div>
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
                <span>Tiền đặt cọc:</span>
                <strong data-booking-deposit>6,689,250 VND</strong>
              </div>
              <button type="button" id="paymentBtn" class="btn btn-primary booking-submit booking-submit-full" data-payment-submit>Thanh toán với QR</button>
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
        const emailInput = document.getElementById('email');
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
        const bookingOriginalTotal = 7432500;
        const bookingDiscountRate = 0.1;
        let appliedPromoCode = promoInput.value.trim();

        // Validation functions
        function validateName(value) {
          return /^[a-zA-ZÀ-ỿ\s]+$/.test(value.trim());
        }

        function validatePhone(value) {
          return /^[0-9\s\-\+()]+$/.test(value.trim()) && value.trim().length >= 10;
        }

        function formatCurrency(value) {
          return `${Number(value || 0).toLocaleString('vi-VN')} VND`;
        }

        function updatePaymentButton(value) {
          document.querySelectorAll('.booking-payment-option').forEach(option => {
            option.classList.toggle('is-selected', option.querySelector('[data-payment-option]')?.dataset.paymentOption === value);
          });

          if (value === 'zalopay') {
            paymentBtn.textContent = 'Thanh toán với QR';
          } else if (value === 'card') {
            paymentBtn.textContent = 'Thanh toán thẻ';
          } else {
            paymentBtn.textContent = 'Chọn phương thức thanh toán';
          }
        }

        function updateDiscountUI(promoCode) {
          const hasPromo = Boolean(promoCode);
          const discountValue = hasPromo ? Math.round(bookingOriginalTotal * bookingDiscountRate) : 0;
          const finalTotal = bookingOriginalTotal - discountValue;

          if (discountSummary) {
            discountSummary.hidden = !hasPromo;
          }

          if (promoStatus) {
            promoStatus.hidden = !hasPromo;
            promoStatus.textContent = hasPromo ? `Đã áp dụng mã ${promoCode}: giảm 10%` : '';
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

        // Real-time validation for name (letters only)
        fullNameInput.addEventListener('input', function(e) {
          const value = e.target.value;
          // Remove non-letter characters
          const sanitized = value.replace(/[^a-zA-ZÀ-ỿ\s]/g, '');
          if (sanitized !== value) {
            e.target.value = sanitized;
          }
          // Clear error message on valid input
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
          input.addEventListener('change', function() {
            updatePaymentButton(input.dataset.paymentOption);
          });
        });

        // Payment button validation
        paymentBtn.addEventListener('click', function(e) {
          e.preventDefault();
          
          const fullName = document.getElementById('fullName').value.trim();
          const phone = document.getElementById('phone').value.trim();
          const email = document.getElementById('email').value.trim();

          // Clear previous errors
          document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

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
            document.getElementById('phone-error').textContent = 'Số điện thoại không hợp lệ (tối thiểu 10 chữ số)';
            hasError = true;
          }

          if (!email) {
            document.getElementById('email-error').textContent = 'Vui lòng nhập email';
            hasError = true;
          } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            document.getElementById('email-error').textContent = 'Email không hợp lệ';
            hasError = true;
          }

          if (!hasError) {
            if (!appliedPromoCode) {
              localStorage.removeItem('peachBookingPromo');
            }

            const selectedPayment = document.querySelector('[data-payment-option]:checked')?.dataset.paymentOption || '';
            updatePaymentButton(selectedPayment);
          }
        });

        updateDiscountUI(appliedPromoCode);
        updatePaymentButton(document.querySelector('[data-payment-option]:checked')?.dataset.paymentOption || '');
      });
    </script>
  </body>
</html>
