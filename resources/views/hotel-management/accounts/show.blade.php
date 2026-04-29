<x-hotel-management.show-page
    title="Chi tiết tài khoản"
    subtitle="Thông tin chi tiết tài khoản"
    :index-route="route('hotel.accounts.index')"
    :edit-route="route('hotel.accounts.edit', ['recordId' => request()->route('recordId') ?? 1])"
>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã tài khoản</div><div class="fw-semibold">101</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Email</div><div class="fw-semibold">minhan@gmail.com</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Họ tên</div><div class="fw-semibold">Nguyễn Minh An</div></div></div>
     <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mật khẩu</div><div class="fw-semibold">an1234@</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Loại tài khoản</div><div class="fw-semibold">Khách hàng</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Trạng thái</div><div class="fw-semibold">Hoạt động</div></div></div>
</x-hotel-management.show-page>
