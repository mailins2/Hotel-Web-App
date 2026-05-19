@php
    $accountTypeLabels = [
        0 => 'Khách hàng',
        1 => 'Nhân viên',
        2 => 'Quản lý',
        3 => 'Kế toán',
        4 => 'Nhân viên kinh doanh',
    ];

    $isCustomerAccount = (int) ($account->LoaiTaiKhoan ?? -1) === 0;
    $displayName = $account?->khachHang?->TenKH
        ?? $account?->nhanVien?->TenNV
        ?? '--';
@endphp

<x-hotel-management.show-page
    title="Chi tiết tài khoản"
    subtitle="Thông tin chi tiết tài khoản"
    :index-route="route('hotel.accounts.index')"
    :edit-route="route('hotel.accounts.edit', ['recordId' => $account->MaTK])"
>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã tài khoản</div><div class="fw-semibold">{{ $account->MaTK ?? '--' }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Email</div><div class="fw-semibold">{{ $account->Email ?? '--' }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Họ tên</div><div class="fw-semibold">{{ $displayName }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Loại tài khoản</div><div class="fw-semibold">{{ $accountTypeLabels[(int) $account->LoaiTaiKhoan] ?? 'Không xác định' }}</div></div></div>

    @if ($isCustomerAccount)
        <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã khách hàng</div><div class="fw-semibold">{{ $account->MaKH ?? '--' }}</div></div></div>
    @else
        <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã nhân viên</div><div class="fw-semibold">{{ $account->MaNV ?? '--' }}</div></div></div>
    @endif

    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Trạng thái</div><div class="fw-semibold">{{ (int) $account->TrangThai === 1 ? 'Hoạt động' : 'Không hoạt động' }}</div></div></div>
</x-hotel-management.show-page>
