<x-hotel-management.show-page
    title="Chi tiết nhân viên"
    subtitle="Thông tin hồ sơ nhân viên"
    :index-route="route('hotel.employees.index')"
    :edit-route="route('hotel.employees.edit', ['recordId' => request()->route('recordId') ?? 1])"
>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã nhân viên</div><div class="fw-semibold">1</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên nhân viên</div><div class="fw-semibold">Phạm Thùy Linh</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã tài khoản</div><div class="fw-semibold">201</div></div></div>
</x-hotel-management.show-page>
