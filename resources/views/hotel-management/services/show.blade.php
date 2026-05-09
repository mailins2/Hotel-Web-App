<x-hotel-management.show-page
    title="Chi tiết dịch vụ"
    subtitle="Thông tin chi tiết dịch vụ"
    :index-route="route('hotel.services.index')"
    :edit-route="route('hotel.services.edit', ['recordId' => request()->route('recordId')])"
>
    <div class="col-md-6 mb-4">
        <div class="border rounded p-3 h-100">
            <div class="text-muted small mb-1">Mã dịch vụ</div>
            <div class="fw-semibold" id="service-id">Đang tải...</div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="border rounded p-3 h-100">
            <div class="text-muted small mb-1">Tên dịch vụ</div>
            <div class="fw-semibold" id="service-name">Đang tải...</div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="border rounded p-3 h-100">
            <div class="text-muted small mb-1">Giá dịch vụ</div>
            <div class="fw-semibold" id="service-price">Đang tải...</div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="border rounded p-3 h-100">
            <div class="text-muted small mb-1">Loại dịch vụ</div>
            <div class="fw-semibold" id="service-type">Đang tải...</div>
        </div>
    </div>
    <div class="col-md-12 mb-4">
        <div class="border rounded p-3 h-100">
            <div class="text-muted small mb-3">Ảnh dịch vụ</div>
            <img
                id="service-image"
                src="https://placehold.co/800x800/f3f4f6/9ca3af?text=Service"
                alt="Ảnh dịch vụ"
                class="rounded border bg-light d-block mx-auto"
                style="width: min(100%, 320px); aspect-ratio: 1 / 1; object-fit: cover; object-position: center;"
            >
            <div class="small text-muted mt-3 text-break" id="service-image-url">Đang tải...</div>
        </div>
    </div>

    <div
        id="service-show-config"
        data-service-id="{{ request()->route('recordId') }}"
        data-placeholder-image="https://placehold.co/800x800/f3f4f6/9ca3af?text=Service"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', async function () {
                const config = document.getElementById('service-show-config');
                const serviceId = config ? config.dataset.serviceId : '';
                const placeholderImage = config ? config.dataset.placeholderImage : '';

                const typeMap = {
                    '1': 'Dịch vụ ăn uống',
                    '2': 'Dịch vụ phòng',
                    '3': 'Dịch vụ giải trí'
                };

                const formatCurrency = function (value) {
                    const number = Number(value);
                    if (!Number.isFinite(number)) {
                        return '--';
                    }
                    return number.toLocaleString('vi-VN') + ' VNĐ';
                };

                const fillFallback = function (message) {
                    document.getElementById('service-id').textContent = '--';
                    document.getElementById('service-name').textContent = '--';
                    document.getElementById('service-price').textContent = '--';
                    document.getElementById('service-type').textContent = '--';
                    document.getElementById('service-image').src = placeholderImage;
                    document.getElementById('service-image-url').textContent = message;
                };

                try {
                    const responses = await Promise.all([
                        fetch(`/api/dich-vu/${serviceId}`, { headers: { 'Accept': 'application/json' } }),
                        fetch('/api/hinh-anh', { headers: { 'Accept': 'application/json' } })
                    ]);

                    if (!responses[0].ok) {
                        throw new Error('Không thể tải chi tiết dịch vụ.');
                    }

                    if (!responses[1].ok) {
                        throw new Error('Không thể tải ảnh dịch vụ.');
                    }

                    const servicePayload = await responses[0].json();
                    const imagePayload = await responses[1].json();
                    const service = servicePayload && servicePayload.data ? servicePayload.data : null;
                    const images = Array.isArray(imagePayload) ? imagePayload : [];

                    if (!service) {
                        throw new Error('Không tìm thấy dữ liệu dịch vụ.');
                    }

                    const matchedImage = images.find(function (image) {
                        return String(image.MaDV || '') === String(service.MaDV || '');
                    });

                    const imageUrl = matchedImage && matchedImage.Url ? matchedImage.Url : placeholderImage;

                    document.getElementById('service-id').textContent = service.MaDV || '--';
                    document.getElementById('service-name').textContent = service.TenDV || '--';
                    document.getElementById('service-price').textContent = formatCurrency(service.GiaDV);
                    document.getElementById('service-type').textContent = typeMap[String(service.LoaiDV || '')] || 'Khác';
                    document.getElementById('service-image').src = imageUrl;
                    document.getElementById('service-image').alt = service.TenDV ? `Ảnh dịch vụ ${service.TenDV}` : 'Ảnh dịch vụ';
                    document.getElementById('service-image').onerror = function () {
                        this.onerror = null;
                        this.src = placeholderImage;
                    };
                    document.getElementById('service-image-url').textContent = matchedImage && matchedImage.Url ? matchedImage.Url : 'Chưa có link ảnh dịch vụ.';
                } catch (error) {
                    fillFallback(error.message);
                }
            });
        </script>
    @endpush
</x-hotel-management.show-page>
