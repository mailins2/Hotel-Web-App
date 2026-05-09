<x-hotel-management.show-page
    title="Chi tiết nhân viên"
    subtitle="Thông tin hồ sơ nhân viên"
    :index-route="route('hotel.employees.index')"
    :edit-route="route('hotel.employees.edit', ['recordId' => request()->route('recordId') ?? 1])"
>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã nhân viên</div><div class="fw-semibold" id="employee-id">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Tên nhân viên</div><div class="fw-semibold" id="employee-name">Đang tải...</div></div></div>
    <div class="col-md-6 mb-4"><div class="border rounded p-3 h-100"><div class="text-muted small mb-1">Mã tài khoản</div><div class="fw-semibold" id="employee-account-id">Đang tải...</div></div></div>
    <div id="employee-show-config" data-employee-id="{{ request()->route('recordId') }}" hidden></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', async function () {
                const config = document.getElementById('employee-show-config');
                const employeeId = config ? config.dataset.employeeId : '';

                try {
                    const response = await fetch(`/api/nhan-vien/${employeeId}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải chi tiết nhân viên.');
                    }

                    const employee = await response.json();

                    document.getElementById('employee-id').textContent = employee.MaNV || '--';
                    document.getElementById('employee-name').textContent = employee.TenNV || '--';
                    document.getElementById('employee-account-id').textContent = employee.MaTK || '--';
                } catch (error) {
                    document.getElementById('employee-id').textContent = '--';
                    document.getElementById('employee-name').textContent = error.message;
                    document.getElementById('employee-account-id').textContent = '--';
                }
            });
        </script>
    @endpush
</x-hotel-management.show-page>
