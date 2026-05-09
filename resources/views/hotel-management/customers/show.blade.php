<x-hotel-management.show-page
    title="Chi tiết khách hàng"
    subtitle="Trang xem nhanh thông tin khách hàng."
    :index-route="route('hotel.customers.index')"
    :edit-route="route('hotel.customers.edit', ['recordId' => request()->route('recordId') ?? 1])"
>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã khách hàng</div><div class="fw-semibold" id="customer-id">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên khách hàng</div><div class="fw-semibold" id="customer-name">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày sinh</div><div class="fw-semibold" id="customer-birth">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Giới tính</div><div class="fw-semibold" id="customer-gender">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số điện thoại</div><div class="fw-semibold" id="customer-phone">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">CCCD</div><div class="fw-semibold" id="customer-cccd">Đang tải...</div></div></div>
    <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Địa chỉ</div><div class="fw-semibold" id="customer-address">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Điểm tích lũy</div><div class="fw-semibold" id="customer-points">Đang tải...</div></div></div>
    <div id="customer-show-config" data-customer-id="{{ request()->route('recordId') }}" hidden></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', async function () {
                const config = document.getElementById('customer-show-config');
                const customerId = config ? config.dataset.customerId : '';

                const mapGender = function (value) {
                    switch (Number(value)) {
                        case 0:
                            return 'Nữ';
                        case 1:
                            return 'Nam';
                        case 2:
                            return 'Khác';
                        default:
                            return '--';
                    }
                };

                const formatDate = function (value) {
                    if (!value) {
                        return '--';
                    }
                    const parts = String(value).split('-');
                    return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : value;
                };

                try {
                    const response = await fetch(`/api/khach-hang/${customerId}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải chi tiết khách hàng.');
                    }

                    const customer = await response.json();

                    document.getElementById('customer-id').textContent = customer.MaKH || '--';
                    document.getElementById('customer-name').textContent = customer.TenKH || '--';
                    document.getElementById('customer-birth').textContent = formatDate(customer.NgaySinh);
                    document.getElementById('customer-gender').textContent = mapGender(customer.GioiTinh);
                    document.getElementById('customer-phone').textContent = customer.SoDienThoai || '--';
                    document.getElementById('customer-cccd').textContent = customer.CCCD || '--';
                    document.getElementById('customer-address').textContent = customer.DiaChi || '--';
                    document.getElementById('customer-points').textContent = customer.DIEM !== undefined && customer.DIEM !== null ? customer.DIEM : '--';
                } catch (error) {
                    document.getElementById('customer-name').textContent = error.message;
                    ['customer-id', 'customer-birth', 'customer-gender', 'customer-phone', 'customer-cccd', 'customer-address', 'customer-points']
                        .forEach(function (id) {
                            document.getElementById(id).textContent = '--';
                        });
                    document.getElementById('customer-name').textContent = error.message;
                }
            });
        </script>
    @endpush
</x-hotel-management.show-page>
