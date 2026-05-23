@php
    $isEditCustomer = request()->routeIs('reception.customers.edit');
    $customerId = request()->route('customerId');
@endphp

<x-receptionist.form-page
    :is-edit="$isEditCustomer"
    :index-route="route('reception.customers.index')"
>
    <div class="col-12">
        <div id="customerFormAlert" class="alert d-none mb-4" role="alert"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Mã khách hàng</label>
        <input id="customerId" type="text" class="form-control hm-readonly-input" value="--" readonly disabled>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Tên khách hàng</label>
        <input id="customerName" type="text" class="form-control" placeholder="Nguyễn Minh An" required>
        <div id="customerNameError" class="invalid-feedback"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Ngày sinh</label>
        <input id="customerBirthday" type="date" class="form-control" max="{{ now()->toDateString() }}" required>
        <div id="customerBirthdayError" class="invalid-feedback"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Giới tính</label>
        <select id="customerGender" class="form-select" required>
            <option value="">Chọn giới tính</option>
            <option value="1">Nam</option>
            <option value="0">Nữ</option>
            <option value="2">Khác</option>
        </select>
        <div id="customerGenderError" class="invalid-feedback"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Số điện thoại</label>
        <input id="customerPhone" type="text" class="form-control" placeholder="0901234567" inputmode="numeric" maxlength="10" required>
        <div id="customerPhoneError" class="invalid-feedback"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">CCCD</label>
        <input id="customerCccd" type="text" class="form-control" placeholder="079204000111" inputmode="numeric" maxlength="12">
        <div id="customerCccdError" class="invalid-feedback"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Tỉnh/Thành phố</label>
        <select id="customerProvince" class="form-select">
            <option value="">Chọn tỉnh/thành phố</option>
            <option value="TPHCM">TPHCM</option>
            <option value="Bình Dương">Bình Dương</option>
            <option value="Đà Lạt">Đà Lạt</option>
            <option value="Hà Nội">Hà Nội</option>
        </select>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Quận/Huyện</label>
        <select id="customerDistrict" class="form-select">
            <option value="">Chọn quận/huyện</option>
            <option value="Quận 1">Quận 1</option>
            <option value="Bến Cát">Bến Cát</option>
            <option value="Đà Lạt">Đà Lạt</option>
            <option value="Hoàn Kiếm">Hoàn Kiếm</option>
        </select>
    </div>

    <div class="form-group col-md-12">
        <label class="form-label">Số nhà, đường</label>
        <input id="customerAddressLine" type="text" class="form-control" placeholder="12 Nguyễn Huệ">
        <div id="customerAddressLineError" class="invalid-feedback"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Điểm tích lũy</label>
        <input id="customerPoints" type="number" class="form-control hm-readonly-input" value="0" disabled>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Trạng thái</label>
        <select id="customerStatus" class="form-select hm-readonly-input" disabled>
            <option value="1">Hoạt động</option>
            <option value="0">Không hoạt động</option>
        </select>
    </div>

    <div
        id="customerFormConfig"
        data-is-edit="{{ $isEditCustomer ? '1' : '0' }}"
        data-customer-id="{{ $customerId }}"
        data-detail-url-template="{{ url('/api/khach-hang/__CUSTOMER_ID__') }}"
        data-store-url="{{ url('/api/khach-hang') }}"
        data-update-url-template="{{ url('/api/khach-hang/__CUSTOMER_ID__') }}"
        data-index-url="{{ route('reception.customers.index') }}"
        data-csrf-token="{{ csrf_token() }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.querySelector('[data-ui-only-form]');
                const submitButton = form?.querySelector('button[type="submit"]');
                const config = document.getElementById('customerFormConfig');
                const isEdit = config?.dataset.isEdit === '1';
                const customerId = config?.dataset.customerId || '';
                const csrfToken = config?.dataset.csrfToken || '';
                const indexUrl = config?.dataset.indexUrl || '';
                const detailUrlTemplate = config?.dataset.detailUrlTemplate || '';
                const storeUrl = config?.dataset.storeUrl || '';
                const updateUrlTemplate = config?.dataset.updateUrlTemplate || '';

                const alertBox = document.getElementById('customerFormAlert');
                const fields = {
                    id: document.getElementById('customerId'),
                    name: document.getElementById('customerName'),
                    birthday: document.getElementById('customerBirthday'),
                    gender: document.getElementById('customerGender'),
                    phone: document.getElementById('customerPhone'),
                    cccd: document.getElementById('customerCccd'),
                    province: document.getElementById('customerProvince'),
                    district: document.getElementById('customerDistrict'),
                    addressLine: document.getElementById('customerAddressLine'),
                    points: document.getElementById('customerPoints'),
                    status: document.getElementById('customerStatus'),
                };

                const errorMap = {
                    TenKH: ['name', 'customerNameError'],
                    SoDienThoai: ['phone', 'customerPhoneError'],
                    CCCD: ['cccd', 'customerCccdError'],
                    NgaySinh: ['birthday', 'customerBirthdayError'],
                    GioiTinh: ['gender', 'customerGenderError'],
                    DiaChi: ['addressLine', 'customerAddressLineError'],
                };

                function setAlert(type, message) {
                    if (!alertBox) return;
                    alertBox.className = `alert alert-${type} mb-4`;
                    alertBox.textContent = message;
                }

                function clearAlert() {
                    if (!alertBox) return;
                    alertBox.className = 'alert d-none mb-4';
                    alertBox.textContent = '';
                }

                function clearErrors() {
                    Object.values(errorMap).forEach(([fieldKey, errorId]) => {
                        fields[fieldKey]?.classList.remove('is-invalid');
                        const errorElement = document.getElementById(errorId);
                        if (errorElement) errorElement.textContent = '';
                    });
                }

                function setFieldError(fieldName, message) {
                    const mapping = errorMap[fieldName];
                    if (!mapping) return;

                    const [fieldKey, errorId] = mapping;
                    fields[fieldKey]?.classList.add('is-invalid');
                    const errorElement = document.getElementById(errorId);
                    if (errorElement) errorElement.textContent = message;
                }

                function applyServerErrors(errors) {
                    Object.entries(errors || {}).forEach(([fieldName, messages]) => {
                        setFieldError(fieldName, Array.isArray(messages) ? messages[0] : String(messages));
                    });
                }

                function setLoading(isLoading) {
                    if (!submitButton) return;
                    submitButton.disabled = isLoading;
                    submitButton.textContent = isLoading
                        ? (isEdit ? 'Đang lưu...' : 'Đang tạo...')
                        : (isEdit ? 'Lưu thay đổi' : 'Tạo mới');
                }

                function normalizeDigits(input, maxLength) {
                    input?.addEventListener('input', () => {
                        input.value = input.value.replace(/\D+/g, '').slice(0, maxLength);
                    });
                }

                function splitAddress(address) {
                    const parts = String(address || '').split(',').map((item) => item.trim()).filter(Boolean);
                    fields.addressLine.value = parts[0] || '';
                    fields.district.value = parts[1] || '';
                    fields.province.value = parts[2] || '';
                }

                function buildAddress() {
                    return [
                        fields.addressLine.value.trim(),
                        fields.district.value,
                        fields.province.value,
                    ].filter(Boolean).join(', ');
                }

                function populateCustomer(customer) {
                    fields.id.value = customer?.MaKH || '--';
                    fields.name.value = customer?.TenKH || '';
                    fields.birthday.value = customer?.NgaySinh ? String(customer.NgaySinh).split(' ')[0] : '';
                    fields.gender.value = customer?.GioiTinh !== undefined && customer?.GioiTinh !== null ? String(customer.GioiTinh) : '';
                    fields.phone.value = customer?.SoDienThoai || '';
                    fields.cccd.value = customer?.CCCD || '';
                    fields.points.value = customer?.DIEM ?? 0;
                    fields.status.value = customer?.taiKhoan?.TrangThai !== undefined && customer?.taiKhoan?.TrangThai !== null
                        ? String(customer.taiKhoan.TrangThai)
                        : '1';
                    splitAddress(customer?.DiaChi || '');
                }

                function validatePayload() {
                    clearErrors();
                    clearAlert();

                    let valid = true;
                    const name = fields.name.value.trim();
                    const phone = fields.phone.value.trim();
                    const cccd = fields.cccd.value.trim();

                    if (name.length < 2) {
                        setFieldError('TenKH', 'Vui lòng nhập tên khách hàng.');
                        valid = false;
                    }

                    if (!fields.birthday.value) {
                        setFieldError('NgaySinh', 'Vui lòng chọn ngày sinh.');
                        valid = false;
                    }

                    if (!fields.gender.value) {
                        setFieldError('GioiTinh', 'Vui lòng chọn giới tính.');
                        valid = false;
                    }

                    if (!/^0\d{9}$/.test(phone)) {
                        setFieldError('SoDienThoai', 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.');
                        valid = false;
                    }

                    if (cccd && !/^\d{12}$/.test(cccd)) {
                        setFieldError('CCCD', 'CCCD phải gồm đúng 12 chữ số.');
                        valid = false;
                    }

                    return valid;
                }

                async function loadCustomer() {
                    if (!isEdit || !customerId) return;

                    const response = await fetch(detailUrlTemplate.replace('__CUSTOMER_ID__', encodeURIComponent(customerId)), {
                        headers: { Accept: 'application/json' },
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải thông tin khách hàng.');
                    }

                    populateCustomer(await response.json());
                }

                normalizeDigits(fields.phone, 10);
                normalizeDigits(fields.cccd, 12);

                form?.addEventListener('submit', async (event) => {
                    event.preventDefault();

                    if (!validatePayload()) return;

                    const payload = {
                        TenKH: fields.name.value.trim().replace(/\s+/g, ' '),
                        SoDienThoai: fields.phone.value.trim(),
                        CCCD: fields.cccd.value.trim() || null,
                        NgaySinh: fields.birthday.value,
                        GioiTinh: fields.gender.value,
                        DiaChi: buildAddress() || null,
                    };

                    const requestUrl = isEdit
                        ? updateUrlTemplate.replace('__CUSTOMER_ID__', encodeURIComponent(customerId))
                        : storeUrl;

                    try {
                        setLoading(true);
                        clearAlert();
                        clearErrors();

                        const response = await fetch(requestUrl, {
                            method: isEdit ? 'PUT' : 'POST',
                            headers: {
                                Accept: 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify(payload),
                        });
                        const responseData = await response.json().catch(() => ({}));

                        if (response.status === 422) {
                            applyServerErrors(responseData.errors || {});
                            setAlert('danger', 'Vui lòng kiểm tra lại thông tin đã nhập.');
                            return;
                        }

                        if (!response.ok) {
                            throw new Error(responseData.message || 'Không thể lưu thông tin khách hàng.');
                        }

                        setAlert('success', isEdit ? 'Cập nhật khách hàng thành công.' : 'Tạo khách hàng thành công.');

                        window.setTimeout(() => {
                            window.location.href = indexUrl;
                        }, 700);
                    } catch (error) {
                        setAlert('danger', error.message || 'Không thể lưu thông tin khách hàng.');
                    } finally {
                        setLoading(false);
                    }
                });

                (async () => {
                    try {
                        await loadCustomer();
                    } catch (error) {
                        setAlert('danger', error.message || 'Không thể tải thông tin khách hàng.');
                    }
                })();
            });
        </script>
    @endpush
</x-receptionist.form-page>
