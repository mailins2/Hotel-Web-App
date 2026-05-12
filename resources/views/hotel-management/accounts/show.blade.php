<x-hotel-management.show-page
    title="Chi tiết tài khoản"
    subtitle="Thông tin chi tiết tài khoản"
    :index-route="route('hotel.accounts.index')"
    :edit-route="route('hotel.accounts.edit', ['recordId' => request()->route('recordId') ?? 1])"
>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã tài khoản</div><div class="fw-semibold" id="account-id">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Email</div><div class="fw-semibold" id="account-email">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Họ tên</div><div class="fw-semibold" id="account-name">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Loại tài khoản</div><div class="fw-semibold" id="account-type">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Trạng thái</div><div class="fw-semibold" id="account-status">Đang tải...</div></div></div>
    <div id="account-show-config" data-account-id="{{ request()->route('recordId') }}" hidden></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', async function () {
                const config = document.getElementById('account-show-config');
                const accountId = config ? config.dataset.accountId : '';
                const idEl = document.getElementById('account-id');
                const emailEl = document.getElementById('account-email');
                const nameEl = document.getElementById('account-name');
                const typeEl = document.getElementById('account-type');
                const statusEl = document.getElementById('account-status');

                const mapAccountType = function (type) {
                    switch (Number(type)) {
                        case 0:
                            return 'Khách hàng';
                        case 1:
                            return 'Nhân viên';
                        case 2:
                            return 'Quản lý';
                        case 3:
                            return 'Kế toán';
                        case 4:
                            return 'Nhân viên kinh doanh';
                        default:
                            return 'Không xác định';
                    }
                };

                const mapStatus = function (status) {
                    return Number(status) === 1 ? 'Hoạt động' : 'Không hoạt động';
                };

                const resolveDisplayName = function (account) {
                    return (account && account.khachHang && account.khachHang.TenKH)
                        || (account && account.khach_hang && account.khach_hang.TenKH)
                        || (account && account.nhanVien && account.nhanVien.TenNV)
                        || (account && account.nhan_vien && account.nhan_vien.TenNV)
                        || '--';
                };

                try {
                    const response = await fetch(`/api/tai-khoan/${accountId}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải chi tiết tài khoản.');
                    }

                    const account = await response.json();

                    idEl.textContent = account && account.MaTK ? account.MaTK : '--';
                    emailEl.textContent = account && account.Email ? account.Email : '--';
                    nameEl.textContent = resolveDisplayName(account);
                    typeEl.textContent = mapAccountType(account.LoaiTaiKhoan);
                    statusEl.textContent = mapStatus(account.TrangThai);
                } catch (error) {
                    idEl.textContent = '--';
                    emailEl.textContent = '--';
                    nameEl.textContent = error.message;
                    typeEl.textContent = '--';
                    statusEl.textContent = '--';
                }
            });
        </script>
    @endpush
</x-hotel-management.show-page>
