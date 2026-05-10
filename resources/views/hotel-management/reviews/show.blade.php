<x-hotel-management.show-page
    title="Chi tiết đánh giá"
    subtitle="Thông tin chi tiết đánh giá"
    :index-route="route('hotel.reviews.index')"
>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã đánh giá</div><div class="fw-semibold" id="review-id">Đang tải...</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã đặt phòng</div><div class="fw-semibold" id="review-booking-id">Đang tải...</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số sao</div><div class="fw-semibold" id="review-stars">Đang tải...</div></div></div>
    <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Nội dung đánh giá</div><div class="fw-semibold" id="review-content">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày đánh giá</div><div class="fw-semibold" id="review-date">Đang tải...</div></div></div>
    <div id="review-show-config" data-review-id="{{ request()->route('recordId') }}" hidden></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', async function () {
                const config = document.getElementById('review-show-config');
                const reviewId = config ? config.dataset.reviewId : '';

                const formatDate = function (value) {
                    if (!value) {
                        return '--';
                    }
                    const parts = String(value).split('-');
                    return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : value;
                };

                try {
                    const response = await fetch(`/api/danh-gia/${reviewId}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải chi tiết đánh giá.');
                    }

                    const review = await response.json();

                    document.getElementById('review-id').textContent = review.MaDG || '--';
                    document.getElementById('review-booking-id').textContent = review.MaDatPhong || '--';
                    document.getElementById('review-stars').textContent = review.Sao ? `${review.Sao} sao` : '--';
                    document.getElementById('review-content').textContent = review.MoTa || '--';
                    document.getElementById('review-date').textContent = formatDate(review.NgayDanhGia);
                } catch (error) {
                    ['review-id', 'review-booking-id', 'review-stars', 'review-date']
                        .forEach(function (id) {
                            document.getElementById(id).textContent = '--';
                        });
                    document.getElementById('review-content').textContent = error.message;
                }
            });
        </script>
    @endpush
</x-hotel-management.show-page>
