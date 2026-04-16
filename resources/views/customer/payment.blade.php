<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Peach Valley</title>
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
          <a href="{{ route('customer.booking') }}" class="booking-back">
            <span class="icon ion-ios-arrow-back"></span>
            Quay lại
          </a>
          <h2>THANH TOÁN</h2>
        </div>

        <div class="row">
          <div class="col-lg-8">
            <div class="booking-card">
              <h3>Phương thức thanh toán</h3>
              <div class="booking-payment-group">
                <label class="booking-payment-option">
                  <input type="radio" name="payment" data-payment-option="zalopay">
                  <span class="booking-payment-title">ZaloPay</span>
                  <img src="{{ Vite::asset('resources/customer/images/zalopay.png') }}" alt="ZaloPay" class="booking-payment-logo-image">
                </label>
                <div class="booking-payment-option">
                  <label class="booking-payment-head">
                    <input type="radio" name="payment" data-payment-option="card">
                    <span class="booking-payment-title">Thẻ thanh toán</span>
                    <span class="booking-payment-brands">VISA • Mastercard • JCB</span>
                  </label>
                  <div class="booking-payment-fields" data-payment-card-fields>
                    <div class="booking-field full">
                      <label>Số thẻ tín dụng</label>
                      <input type="text" placeholder="Số thẻ tín dụng">
                    </div>
                    <div class="booking-field">
                      <label>Hạn sử dụng</label>
                      <input type="text" placeholder="MM/YY">
                    </div>
                    <div class="booking-field">
                      <label>CVV/CVN</label>
                      <input type="text" placeholder="Mã 3-4 chữ số">
                    </div>
                    <div class="booking-field full">
                      <label>Tên trên thẻ</label>
                      <input type="text" placeholder="Tên ghi trên thẻ">
                    </div>
                  </div>
                </div>
              </div>
              <button type="button" class="btn btn-primary booking-submit" data-payment-submit>Chọn phương thức thanh toán</button>
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
              <div class="booking-summary-block">
                <div class="booking-summary-title">
                  <span>Thông tin phòng</span>
                </div>
                <div class="booking-summary-room">
                  <p><strong>Phòng 1:</strong> Deluxe Twin</p>
                  <p>Số phòng: 1 phòng</p>
                  <p>Số người: 1 người lớn</p>
                  <p>Giá phòng: 1,732,500 VND</p>
                </div>
              </div>
              <div class="booking-discount-summary" data-payment-discount hidden>
                <div class="booking-price-row">
                  <span>Giá gốc:</span>
                  <strong data-payment-original>1,732,500 VND</strong>
                </div>
                <div class="booking-price-row booking-price-discount">
                  <span>Giá giảm:</span>
                  <strong data-payment-discount-amount>-0 VND</strong>
                </div>
              </div>
              <div class="booking-summary-total">
                <span>Tổng giá:</span>
                <strong data-payment-total>1,732,500 VND</strong>
              </div>
              <div class="booking-summary-deposit">
                <span>Tiền đặt cọc:</span>
                <strong data-payment-deposit>1,732,500 VND</strong>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    @include('customer.partials.footer')
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const discountSummary = document.querySelector('[data-payment-discount]');
        const originalTotal = document.querySelector('[data-payment-original]');
        const discountAmount = document.querySelector('[data-payment-discount-amount]');
        const total = document.querySelector('[data-payment-total]');
        const deposit = document.querySelector('[data-payment-deposit]');

        function formatCurrency(value) {
          return `${Number(value || 0).toLocaleString('vi-VN')} VND`;
        }

        try {
          const promo = JSON.parse(localStorage.getItem('peachBookingPromo') || 'null');

          if (!promo || !promo.code || !promo.discountAmount) {
            return;
          }

          discountSummary.hidden = false;
          originalTotal.textContent = formatCurrency(promo.originalTotal);
          discountAmount.textContent = `-${formatCurrency(promo.discountAmount)}`;
          total.textContent = formatCurrency(promo.finalTotal);
          deposit.textContent = formatCurrency(promo.finalTotal);
        } catch (error) {
          localStorage.removeItem('peachBookingPromo');
        }
      });
    </script>
  </body>
</html>
