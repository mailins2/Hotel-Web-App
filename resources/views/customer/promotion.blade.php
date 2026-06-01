<!DOCTYPE html>
@php
  $authAccount = session('auth_account');
  $isLoggedIn = filled($authAccount) && (int) ($authAccount['LoaiTaiKhoan'] ?? -1) === 0;
  $customerId = $authAccount['MaKH'] ?? null;
  $customerPoints = null;

  if ($isLoggedIn && blank($customerId) && !empty($authAccount['MaTK'])) {
    $customerId = \App\Models\TaiKhoan::where('MaTK', $authAccount['MaTK'])->value('MaKH');
  }

  if ($isLoggedIn && filled($customerId)) {
    $customerPoints = (int) \App\Models\KhachHang::where('MaKH', $customerId)->value('DIEM');
  }

  $promotionAuthPayload = [
    'isLoggedIn' => $isLoggedIn,
    'customerId' => $customerId,
    'customerPoints' => $customerPoints,
  ];
@endphp
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

    @include('customer.partials.nav', ['active' => 'promotion'])

    <div class="hero-wrap" data-bg-image="{{ asset('customers/images/screen.png') }}">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text d-flex align-itemd-center justify-content-center">
          <div class="col-md-9 ftco-animate text-center d-flex align-items-end justify-content-center">
            <div class="text">
              <h1 class="mb-4 bread">Khuyến mãi</h1>
            </div>
          </div>
        </div>
      </div>
    </div>

    <section class="ftco-section customer-promotion-page">
      <div class="container">
        <div class="row justify-content-center mb-5 pb-3">
          <div class="col-md-8 heading-section text-center ftco-animate">
            <span class="subheading">Ưu đãi Peach Valley</span>
            <h2 class="mb-4">Chọn ưu đãi cho kỳ nghỉ của bạn</h2>
          </div>
        </div>

        @if ($promotions->count() > 0)
        <div class="promotion-card-grid">
          @foreach ($promotions as $promotion)
          <article class="promotion-card ftco-animate">
            <div class="promotion-card-media">
              @if ($promotion->hinhs && $promotion->hinhs->count() > 0)
              <div class="promotion-card-slider single-slider owl-carousel">
                @foreach ($promotion->hinhs as $image)
                <div class="promotion-card-slide" data-bg-image="{{ $image->Url }}"></div>
                @endforeach
              </div>
              @else
              <div class="promotion-card-slider single-slider owl-carousel">
                <div class="promotion-card-slide" data-bg-image="{{ asset('customers/images/screen.png') }}"></div>
              </div>
              @endif
              @if ($promotion->PhanTramGiamGia)
              <span class="promotion-card-discount">-{{ $promotion->PhanTramGiamGia }}%</span>
              @endif
            </div>

            <div class="promotion-card-body">
              <div class="promotion-card-code">{{ $promotion->MaKM }}</div>
              <h3>{{ $promotion->TenKM }}</h3>
              <p class="promotion-card-description">
                {{ $promotion->MoTa ?: 'Không có mô tả' }}
              </p>

              <div class="promotion-card-actions">
                @if ($promotion->Diem)
                <span class="promotion-card-points">
                  <i aria-hidden="true"></i>
                  {{ $promotion->Diem }} điểm
                </span>
                @endif
                <button
                  type="button"
                  class="promotion-card-save"
                  onclick="copyToClipboard('{{ $promotion->MaKM }}', {{ (int) ($promotion->Diem ?? 0) }}, this)"
                >Lưu mã</button>
              </div>

              @if ($promotion->NgayBatDau || $promotion->NgayKetThuc)
              <div class="promotion-card-date">
                {{ $promotion->NgayBatDau ? \Carbon\Carbon::parse($promotion->NgayBatDau)->format('d/m/Y') : '' }}
                @if ($promotion->NgayBatDau && $promotion->NgayKetThuc)
                -
                @endif
                {{ $promotion->NgayKetThuc ? \Carbon\Carbon::parse($promotion->NgayKetThuc)->format('d/m/Y') : '' }}
              </div>
              @endif
            </div>
          </article>
          @endforeach
        </div>
        @else
        <div class="row justify-content-center">
          <div class="col-md-8 text-center">
            <p class="text-muted">Hiện tại không có khuyến mãi nào.</p>
          </div>
        </div>
        @endif
      </div>
    </section>

    <div class="promotion-login-modal" data-promotion-login-modal hidden>
      <div class="promotion-login-modal-backdrop" data-promotion-login-close></div>
      <div class="promotion-login-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="promotion-login-title">
        <h3 id="promotion-login-title">Bạn cần đăng nhập</h3>
        <p>Vui lòng đăng nhập để lưu mã khuyến mãi vào tài khoản của bạn.</p>
        <div class="promotion-login-modal-actions">
          <button type="button" class="promotion-login-modal-secondary" data-promotion-login-close>Đã hiểu</button>
          <a href="{{ route('login') }}" class="promotion-login-modal-primary">Đăng nhập</a>
        </div>
      </div>
    </div>

    <div class="promotion-feedback-modal" data-promotion-feedback-modal hidden>
      <div class="promotion-feedback-modal-backdrop" data-promotion-feedback-close></div>
      <div class="promotion-feedback-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="promotion-feedback-title">
        <h3 id="promotion-feedback-title" data-promotion-feedback-title></h3>
        <p data-promotion-feedback-message></p>
        <div class="promotion-feedback-modal-actions">
          <button type="button" class="promotion-feedback-modal-primary" data-promotion-feedback-close>Đã hiểu</button>
        </div>
      </div>
    </div>

    <script>
      window.PeachPromotionAuth = {{ \Illuminate\Support\Js::from($promotionAuthPayload) }};

      function openPromotionLoginModal() {
        const modal = document.querySelector('[data-promotion-login-modal]');
        if (!modal) return;
        modal.hidden = false;
        modal.classList.add('is-open');
        document.body.classList.add('modal-open');
      }

      function closePromotionLoginModal() {
        const modal = document.querySelector('[data-promotion-login-modal]');
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.hidden = true;
        document.body.classList.remove('modal-open');
      }

      function openPromotionFeedbackModal(title, message) {
        const modal = document.querySelector('[data-promotion-feedback-modal]');
        if (!modal) return;

        const titleElement = modal.querySelector('[data-promotion-feedback-title]');
        const messageElement = modal.querySelector('[data-promotion-feedback-message]');

        if (titleElement) titleElement.textContent = title;
        if (messageElement) messageElement.textContent = message;

        modal.hidden = false;
        modal.classList.add('is-open');
        document.body.classList.add('modal-open');
      }

      function closePromotionFeedbackModal() {
        const modal = document.querySelector('[data-promotion-feedback-modal]');
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.hidden = true;
        document.body.classList.remove('modal-open');
      }

      function setPromotionSaveLoading(button, isLoading) {
        if (!button) return;

        if (isLoading) {
          button.dataset.originalText = button.textContent.trim();
          button.classList.add('is-loading');
          button.disabled = true;
          button.setAttribute('aria-busy', 'true');
          button.innerHTML = '<span class="promotion-card-save-spinner" aria-hidden="true"></span><span>Đang lưu</span>';
          return;
        }

        button.classList.remove('is-loading');
        button.disabled = false;
        button.removeAttribute('aria-busy');
        button.textContent = button.dataset.originalText || 'Lưu mã';
      }

      async function copyToClipboard(text, pointsRequired, button) {
        if (!window.PeachPromotionAuth || !window.PeachPromotionAuth.isLoggedIn) {
          openPromotionLoginModal();
          return;
        }

        if (!window.PeachPromotionAuth.customerId) {
          openPromotionFeedbackModal('Không thể lưu mã', 'Không tìm thấy thông tin khách hàng của tài khoản hiện tại.');
          return;
        }

        const currentPoints = Number(window.PeachPromotionAuth.customerPoints);
        const neededPoints = Math.max(0, Number(pointsRequired) || 0);

        if (Number.isFinite(currentPoints) && currentPoints < neededPoints) {
          openPromotionFeedbackModal(
            'Điểm không đủ',
            `Bạn cần ${neededPoints} điểm để đổi mã này. Hiện bạn có ${currentPoints} điểm, còn thiếu ${neededPoints - currentPoints} điểm.`,
          );
          return;
        }

        setPromotionSaveLoading(button, true);

        try {
          const response = await fetch('/api/kho-khuyen-mai/doi-bang-diem', {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              MaKM: text,
              MaKH: window.PeachPromotionAuth.customerId,
            }),
          });
          const result = await response.json();

          if (response.ok && result.success) {
            const data = result.data || {};
            if (Number.isInteger(data.diemConLai)) {
              window.PeachPromotionAuth.customerPoints = data.diemConLai;
            }
            const diemConLai = Number.isInteger(data.diemConLai) ? ` Bạn còn ${data.diemConLai} điểm.` : '';
            openPromotionFeedbackModal('Lưu mã thành công', `Mã ${text} đã được lưu vào kho khuyến mãi của bạn.${diemConLai}`);
            return;
          }

          const data = result.data || {};
          if (data.diemThieu !== undefined) {
            openPromotionFeedbackModal(
              'Điểm không đủ',
              `Bạn cần ${data.diemCan} điểm để đổi mã này. Hiện bạn có ${data.diemHienTai} điểm, còn thiếu ${data.diemThieu} điểm.`,
            );
            return;
          }

          openPromotionFeedbackModal('Không thể lưu mã', result.message || 'Vui lòng thử lại sau.');
        } catch (error) {
          console.error('Lỗi khi lưu mã khuyến mãi: ', error);
          openPromotionFeedbackModal('Không thể lưu mã', 'Có lỗi xảy ra khi lưu mã. Vui lòng thử lại sau.');
        } finally {
          setPromotionSaveLoading(button, false);
        }
      }
      document.addEventListener('DOMContentLoaded', function () {
        const loginModal = document.querySelector('[data-promotion-login-modal]');
        const feedbackModal = document.querySelector('[data-promotion-feedback-modal]');

        loginModal?.querySelectorAll('[data-promotion-login-close]').forEach(function (button) {
          button.addEventListener('click', closePromotionLoginModal);
        });

        feedbackModal?.querySelectorAll('[data-promotion-feedback-close]').forEach(function (button) {
          button.addEventListener('click', closePromotionFeedbackModal);
        });

        document.addEventListener('keydown', function (event) {
          if (event.key === 'Escape' && loginModal?.classList.contains('is-open')) {
            closePromotionLoginModal();
          }

          if (event.key === 'Escape' && feedbackModal?.classList.contains('is-open')) {
            closePromotionFeedbackModal();
          }
        });
      });
    </script>

    @include('customer.partials.footer')

    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>
  </body>
</html>
