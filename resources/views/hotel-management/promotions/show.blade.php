<x-hotel-management.show-page
    title="Chi tiết khuyến mãi"
    subtitle="Thông tin chi tiết khuyến mãi"
    :index-route="route('hotel.promotions.index')"
    :edit-route="route('hotel.promotions.edit', ['recordId' => request()->route('recordId') ?? 1])"
>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã khuyến mãi</div><div class="fw-semibold" id="promotion-id">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên chương trình</div><div class="fw-semibold" id="promotion-name">Đang tải...</div></div></div>
    <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mô tả</div><div class="fw-semibold" id="promotion-description">Đang tải...</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Điểm yêu cầu</div><div class="fw-semibold" id="promotion-points">Đang tải...</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày bắt đầu</div><div class="fw-semibold" id="promotion-start">Đang tải...</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày kết thúc</div><div class="fw-semibold" id="promotion-end">Đang tải...</div></div></div>
    <div class="col-md-4 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Phần trăm giảm giá</div><div class="fw-semibold" id="promotion-discount">Đang tải...</div></div></div>
    <div id="promotion-show-config" data-promotion-id="{{ request()->route('recordId') }}" hidden></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', async function () {
                const config = document.getElementById('promotion-show-config');
                const promotionId = config ? config.dataset.promotionId : '';

                const formatDate = function (value) {
                    if (!value) {
                        return '--';
                    }
                    const parts = String(value).split('-');
                    return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : value;
                };

                try {
                    const response = await fetch(`/api/khuyen-mai/${promotionId}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải chi tiết khuyến mãi.');
                    }

                    const promotion = await response.json();

                    document.getElementById('promotion-id').textContent = promotion.MaKM || '--';
                    document.getElementById('promotion-name').textContent = promotion.TenKM || '--';
                    document.getElementById('promotion-description').textContent = promotion.MoTa || '--';
                    document.getElementById('promotion-points').textContent = promotion.Diem || 0;
                    document.getElementById('promotion-start').textContent = formatDate(promotion.NgayBatDau);
                    document.getElementById('promotion-end').textContent = formatDate(promotion.NgayKetThuc);
                    document.getElementById('promotion-discount').textContent = promotion.PhanTramGiamGia ? `${Number(promotion.PhanTramGiamGia)}%` : '--';
                } catch (error) {
                    ['promotion-id', 'promotion-points', 'promotion-start', 'promotion-end', 'promotion-discount']
                        .forEach(function (id) {
                            document.getElementById(id).textContent = '--';
                        });
                    document.getElementById('promotion-name').textContent = error.message;
                    document.getElementById('promotion-description').textContent = '--';
                }
            });
        </script>
    @endpush
</x-hotel-management.show-page>
