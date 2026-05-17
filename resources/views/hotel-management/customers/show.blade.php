@php
    $gender = $customer->GioiTinh === null
        ? '--'
        : match ((int) $customer->GioiTinh) {
            0 => 'Nữ',
            1 => 'Nam',
            2 => 'Khác',
            default => '--',
        };
    $birthDate = $customer->NgaySinh ? \Carbon\Carbon::parse($customer->NgaySinh)->format('d/m/Y') : '--';
@endphp

<x-hotel-management.show-page
    title="Chi tiết khách hàng"
    subtitle="Trang xem nhanh thông tin khách hàng."
    :index-route="route('hotel.customers.index')"
    :edit-route="route('hotel.customers.edit', ['recordId' => $customer->MaKH])"
>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã khách hàng</div><div class="fw-semibold">{{ $customer->MaKH ?? '--' }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên khách hàng</div><div class="fw-semibold">{{ $customer->TenKH ?? '--' }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Ngày sinh</div><div class="fw-semibold">{{ $birthDate }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Giới tính</div><div class="fw-semibold">{{ $gender }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Số điện thoại</div><div class="fw-semibold">{{ $customer->SoDienThoai ?? '--' }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">CCCD</div><div class="fw-semibold">{{ $customer->CCCD ?? '--' }}</div></div></div>
    <div class="col-md-12 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Địa chỉ</div><div class="fw-semibold">{{ $customer->DiaChi ?? '--' }}</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Điểm tích lũy</div><div class="fw-semibold">{{ $customer->DIEM ?? '--' }}</div></div></div>
</x-hotel-management.show-page>
