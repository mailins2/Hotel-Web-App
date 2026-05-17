<x-hotel-management.show-page
    title="Chi tiết đánh giá"
    subtitle="Thông tin chi tiết đánh giá"
    :index-route="route('hotel.reviews.index')"
>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã đánh giá</div><div class="fw-semibold" id="review-id">Đang tải...</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã đặt phòng</div><div class="fw-semibold" id="review-booking-id">Đang tải...</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã khách hàng</div><div class="fw-semibold" id="review-customer-id">Đang tải...</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên khách hàng</div><div class="fw-semibold" id="review-customer-name">Đang tải...</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên loại phòng</div><div class="fw-semibold" id="review-room-type-name">Đang tải...</div></div></div>
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

                const getRelation = function (record, camelName, snakeName) {
                    if (!record) {
                        return null;
                    }

                    return record[camelName] || record[snakeName] || null;
                };

                const getRoomTypeNames = function (booking) {
                    const bookingDetails = getRelation(booking, 'chiTietDatPhong', 'chi_tiet_dat_phong');

                    if (!Array.isArray(bookingDetails)) {
                        return '--';
                    }

                    const names = bookingDetails
                        .map(function (detail) {
                            const room = getRelation(detail, 'phong', 'phong');
                            const roomType = getRelation(room, 'loaiPhong', 'loai_phong');
                            return roomType && roomType.TenLoaiPhong ? roomType.TenLoaiPhong : '';
                        })
                        .filter(Boolean);

                    return names.length ? Array.from(new Set(names)).join(', ') : '--';
                };

                try {
                    const response = await fetch(`/api/danh-gia/${reviewId}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải chi tiết đánh giá.');
                    }

                    const review = await response.json();
                    const booking = getRelation(review, 'datPhong', 'dat_phong');
                    const customer = getRelation(booking, 'khachHang', 'khach_hang');

                    document.getElementById('review-id').textContent = review.MaDG || '--';
                    document.getElementById('review-booking-id').textContent = review.MaDatPhong || '--';
                    document.getElementById('review-customer-id').textContent = customer && customer.MaKH ? customer.MaKH : (booking && booking.MaKH ? booking.MaKH : '--');
                    document.getElementById('review-customer-name').textContent = customer && customer.TenKH ? customer.TenKH : '--';
                    document.getElementById('review-room-type-name').textContent = getRoomTypeNames(booking);
                    document.getElementById('review-stars').textContent = review.Sao ? `${review.Sao} sao` : '--';
                    document.getElementById('review-content').textContent = review.MoTa || '--';
                    document.getElementById('review-date').textContent = formatDate(review.NgayDanhGia);
                } catch (error) {
                    ['review-id', 'review-booking-id', 'review-customer-id', 'review-customer-name', 'review-room-type-name', 'review-stars', 'review-date']
                        .forEach(function (id) {
                            document.getElementById(id).textContent = '--';
                        });
                    document.getElementById('review-content').textContent = error.message;
                }
            });
        </script>
    @endpush
</x-hotel-management.show-page>
