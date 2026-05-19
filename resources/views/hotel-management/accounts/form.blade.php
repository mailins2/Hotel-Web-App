<x-hotel-management.form-page
    :is-edit="request()->routeIs('hotel.accounts.edit')"
    :index-route="route('hotel.accounts.index')"
>
    <div class="col-12">
        <div id="account-form-alert" class="alert d-none mb-4" role="alert"></div>
    </div>

    <div class="form-group col-md-6" id="account-id-group">
        <label class="form-label">Mã tài khoản</label>
        <input
            type="text"
            class="form-control hm-readonly-input"
            id="account-id"
            value="--"
            readonly
        >
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Email</label>
        <input
            type="email"
            class="form-control"
            id="account-email"
            placeholder="Nhập email"
        >
        <div class="invalid-feedback" id="account-email-error"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Loại tài khoản</label>
        <select class="form-select" id="account-type">
            <option value="">Chọn loại tài khoản</option>
            <option value="0">Khách hàng</option>
            <option value="1">Nhân viên</option>
            <option value="2">Quản lý</option>
            <option value="3">Kế toán</option>
            <option value="4">Nhân viên kinh doanh</option>
        </select>
        <div class="invalid-feedback" id="account-type-error"></div>
    </div>

    <div class="form-group col-md-6 d-none" id="account-customer-group">
        <label class="form-label">Mã khách hàng</label>
        <select class="form-select" id="account-customer-id">
            <option value="">Chọn khách hàng</option>
        </select>
        <div class="form-text" id="account-customer-hint">Chỉ hiển thị khách hàng chưa có tài khoản.</div>
        <div class="invalid-feedback" id="account-customer-id-error"></div>
    </div>

    <div class="form-group col-md-6 d-none" id="account-employee-group">
        <label class="form-label">Mã nhân viên</label>
        <select class="form-select" id="account-employee-id">
            <option value="">Chọn nhân viên</option>
        </select>
        <div class="form-text" id="account-employee-hint">Chỉ hiển thị nhân viên chưa có tài khoản.</div>
        <div class="invalid-feedback" id="account-employee-id-error"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Trạng thái</label>
        <select class="form-select" id="account-status">
            <option value="1">Hoạt động</option>
            <option value="0">Không hoạt động</option>
        </select>
        <div class="invalid-feedback" id="account-status-error"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Mật khẩu</label>
        <input
            type="password"
            class="form-control"
            id="account-password"
            placeholder="Nhập mật khẩu"
        >
        <div class="form-text" id="account-password-hint">Mật khẩu tối thiểu 6 ký tự.</div>
        <div class="invalid-feedback" id="account-password-error"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Xác nhận mật khẩu</label>
        <input
            type="password"
            class="form-control"
            id="account-password-confirmation"
            placeholder="Nhập lại mật khẩu"
        >
        <div class="invalid-feedback" id="account-password-confirmation-error"></div>
    </div>


    <div
        id="account-form-config"
        data-is-edit="{{ request()->routeIs('hotel.accounts.edit') ? '1' : '0' }}"
        data-account-id="{{ request()->route('recordId') }}"
        data-create-url="{{ route('hotel.accounts.store') }}"
        data-update-url-template="{{ route('hotel.accounts.update', ['recordId' => '__ACCOUNT_ID__']) }}"
        data-detail-url-template="{{ url('/api/tai-khoan/__ACCOUNT_ID__') }}"
        data-index-url="{{ route('hotel.accounts.index') }}"
        data-customers-url="{{ url('/api/khach-hang') }}"
        data-employees-url="{{ url('/api/nhan-vien') }}"
        data-csrf-token="{{ csrf_token() }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const config = document.getElementById('account-form-config');
                const form = document.querySelector('[data-ui-only-form]');
                const submitButton = form ? form.querySelector('button[type="submit"]') : null;
                const isEdit = !!(config && config.dataset.isEdit === '1');
                const accountId = config ? (config.dataset.accountId || '') : '';
                const createUrl = config ? config.dataset.createUrl : '';
                const updateUrlTemplate = config ? config.dataset.updateUrlTemplate : '';
                const detailUrlTemplate = config ? config.dataset.detailUrlTemplate : '';
                const indexUrl = config ? config.dataset.indexUrl : '';
                const customersUrl = config ? config.dataset.customersUrl : '';
                const employeesUrl = config ? config.dataset.employeesUrl : '';
                const csrfToken = config ? config.dataset.csrfToken : '';

                const alertBox = document.getElementById('account-form-alert');
                const accountIdGroup = document.getElementById('account-id-group');
                const accountIdInput = document.getElementById('account-id');
                const emailInput = document.getElementById('account-email');
                const typeInput = document.getElementById('account-type');
                const customerGroup = document.getElementById('account-customer-group');
                const customerInput = document.getElementById('account-customer-id');
                const customerHint = document.getElementById('account-customer-hint');
                const employeeGroup = document.getElementById('account-employee-group');
                const employeeInput = document.getElementById('account-employee-id');
                const employeeHint = document.getElementById('account-employee-hint');
                const passwordInput = document.getElementById('account-password');
                const passwordConfirmationInput = document.getElementById('account-password-confirmation');
                const statusInput = document.getElementById('account-status');
                const passwordHint = document.getElementById('account-password-hint');

                const fieldMap = {
                    Email: emailInput,
                    MatKhau: passwordInput,
                    MatKhauConfirmation: passwordConfirmationInput,
                    LoaiTaiKhoan: typeInput,
                    TrangThai: statusInput,
                    customer_id: customerInput,
                    employee_id: employeeInput,
                    MaKH: customerInput,
                    MaNV: employeeInput,
                };

                let customers = [];
                let employees = [];
                let currentAccount = null;
                let selectedCustomerId = '';
                let selectedEmployeeId = '';

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

                const setAlert = function (type, message) {
                    if (!alertBox) {
                        return;
                    }

                    alertBox.className = `alert alert-${type} mb-4`;
                    alertBox.textContent = message;
                };

                const clearAlert = function () {
                    if (!alertBox) {
                        return;
                    }

                    alertBox.className = 'alert d-none mb-4';
                    alertBox.textContent = '';
                };

                const clearFieldErrors = function () {
                    Object.keys(fieldMap).forEach(function (fieldName) {
                        const field = fieldMap[fieldName];
                        const suffixMap = {
                            Email: 'email',
                            MatKhau: 'password',
                            MatKhauConfirmation: 'password-confirmation',
                            LoaiTaiKhoan: 'type',
                            TrangThai: 'status',
                            customer_id: 'customer-id',
                            employee_id: 'employee-id',
                            MaKH: 'customer-id',
                            MaNV: 'employee-id',
                        };
                        const errorElement = document.getElementById(`account-${suffixMap[fieldName]}-error`);

                        if (field) {
                            field.classList.remove('is-invalid');
                        }

                        if (errorElement) {
                            errorElement.textContent = '';
                        }
                    });
                };

                const setFieldError = function (fieldName, message) {
                    const keyMap = {
                        Email: 'email',
                        MatKhau: 'password',
                        MatKhauConfirmation: 'password-confirmation',
                        LoaiTaiKhoan: 'type',
                        TrangThai: 'status',
                        customer_id: 'customer-id',
                        employee_id: 'employee-id',
                        MaKH: 'customer-id',
                        MaNV: 'employee-id',
                    };

                    const field = fieldMap[fieldName];
                    const errorElement = document.getElementById(`account-${keyMap[fieldName]}-error`);

                    if (field) {
                        field.classList.add('is-invalid');
                    }

                    if (errorElement) {
                        errorElement.textContent = message;
                    }
                };

                const applyServerErrors = function (errors) {
                    if (!errors) {
                        return;
                    }

                    Object.keys(errors).forEach(function (fieldName) {
                        const messages = errors[fieldName];
                        if (Array.isArray(messages) && messages.length) {
                            setFieldError(fieldName, messages[0]);
                        }
                    });
                };

                const syncLinkFieldVisibility = function () {
                    const typeValue = typeInput.value;
                    const isCustomerType = typeValue === '0';
                    const shouldShowEmployee = typeValue !== '' && !isCustomerType;

                    if (customerGroup) {
                        customerGroup.classList.toggle('d-none', !isCustomerType);
                    }

                    if (employeeGroup) {
                        employeeGroup.classList.toggle('d-none', !shouldShowEmployee);
                    }
                };

                const renderCustomerOptions = function () {
                    const currentLinkedCustomerId = currentAccount && (currentAccount.khachHang || currentAccount.khach_hang)
                        ? String((currentAccount.khachHang || currentAccount.khach_hang).MaKH || '')
                        : '';

                    const availableCustomers = customers.filter(function (customer) {
                        const customerId = String(customer.MaKH || '');
                        const linkedAccount = customer.taiKhoan || customer.tai_khoan || null;
                        const linkedAccountId = linkedAccount && linkedAccount.MaTK !== undefined && linkedAccount.MaTK !== null
                            ? String(linkedAccount.MaTK)
                            : '';

                        return !linkedAccountId || linkedAccountId === String(accountId || '') || customerId === currentLinkedCustomerId;
                    });

                    customerInput.innerHTML = '<option value="">Chọn khách hàng</option>' + availableCustomers.map(function (customer) {
                        const customerId = customer.MaKH || '--';
                        const customerName = customer.TenKH || '--';
                        return `<option value="${customerId}">${customerId} - ${customerName}</option>`;
                    }).join('');

                    if (selectedCustomerId) {
                        customerInput.value = selectedCustomerId;
                    }

                    if (customerHint) {
                        customerHint.textContent = availableCustomers.length
                            ? 'Danh sách khách hàng chưa có tài khoản.'
                            : 'Hiện chưa có khách hàng trống để gắn tài khoản.';
                    }
                };

                const renderEmployeeOptions = function () {
                    const currentLinkedEmployeeId = currentAccount && (currentAccount.nhanVien || currentAccount.nhan_vien)
                        ? String((currentAccount.nhanVien || currentAccount.nhan_vien).MaNV || '')
                        : '';

                    const availableEmployees = employees.filter(function (employee) {
                        const employeeId = String(employee.MaNV || '');
                        const linkedAccount = employee.taiKhoan || employee.tai_khoan || null;
                        const linkedAccountId = linkedAccount && linkedAccount.MaTK !== undefined && linkedAccount.MaTK !== null
                            ? String(linkedAccount.MaTK)
                            : '';

                        return !linkedAccountId || linkedAccountId === String(accountId || '') || employeeId === currentLinkedEmployeeId;
                    });

                    employeeInput.innerHTML = '<option value="">Chọn nhân viên</option>' + availableEmployees.map(function (employee) {
                        const employeeId = employee.MaNV || '--';
                        const employeeName = employee.TenNV || '--';
                        return `<option value="${employeeId}">${employeeId} - ${employeeName}</option>`;
                    }).join('');

                    if (selectedEmployeeId) {
                        employeeInput.value = selectedEmployeeId;
                    }

                    if (employeeHint) {
                        employeeHint.textContent = availableEmployees.length
                            ? 'Danh sách nhân viên chưa có tài khoản.'
                            : 'Hiện chưa có nhân viên trống để gắn tài khoản.';
                    }
                };

                const validateForm = function () {
                    clearFieldErrors();
                    clearAlert();

                    let isValid = true;
                    const emailValue = emailInput.value.trim();
                    const passwordValue = passwordInput.value;
                    const passwordConfirmationValue = passwordConfirmationInput.value;
                    const typeValue = typeInput.value;

                    if (!emailValue) {
                        setFieldError('Email', 'Vui lòng nhập email.');
                        isValid = false;
                    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
                        setFieldError('Email', 'Email không hợp lệ.');
                        isValid = false;
                    }

                    if (!typeValue) {
                        setFieldError('LoaiTaiKhoan', 'Vui lòng chọn loại tài khoản.');
                        isValid = false;
                    }

                    if (!isEdit && passwordValue.length < 6) {
                        setFieldError('MatKhau', 'Mật khẩu phải có ít nhất 6 ký tự.');
                        isValid = false;
                    }

                    if (isEdit && passwordValue && passwordValue.length < 6) {
                        setFieldError('MatKhau', 'Mật khẩu phải có ít nhất 6 ký tự.');
                        isValid = false;
                    }

                    if ((!isEdit || passwordValue || passwordConfirmationValue) && passwordValue !== passwordConfirmationValue) {
                        setFieldError('MatKhauConfirmation', 'Mật khẩu xác nhận không khớp.');
                        isValid = false;
                    }

                    if (typeValue === '0' && !customerInput.value) {
                        setFieldError('customer_id', 'Vui lòng chọn khách hàng.');
                        isValid = false;
                    }

                    if (typeValue !== '' && typeValue !== '0' && !employeeInput.value) {
                        setFieldError('employee_id', 'Vui lòng chọn nhân viên.');
                        isValid = false;
                    }

                    return isValid;
                };

                const setLoadingState = function (isLoading) {
                    if (!submitButton) {
                        return;
                    }

                    submitButton.disabled = isLoading;
                    submitButton.textContent = isLoading
                        ? (isEdit ? 'Đang lưu...' : 'Đang tạo...')
                        : (isEdit ? 'Lưu thay đổi' : 'Tạo mới');
                };

                const populateForm = function (account) {
                    currentAccount = account;
                    accountIdInput.value = account && account.MaTK ? account.MaTK : '--';
                    emailInput.value = account && account.Email ? account.Email : '';
                    typeInput.value = account && account.LoaiTaiKhoan !== undefined && account.LoaiTaiKhoan !== null
                        ? String(account.LoaiTaiKhoan)
                        : '';
                    statusInput.value = account && account.TrangThai !== undefined && account.TrangThai !== null
                        ? String(account.TrangThai)
                        : '1';
                    passwordInput.value = '';
                    passwordConfirmationInput.value = '';

                    const linkedCustomer = account && (account.khachHang || account.khach_hang) ? (account.khachHang || account.khach_hang) : null;
                    const linkedEmployee = account && (account.nhanVien || account.nhan_vien) ? (account.nhanVien || account.nhan_vien) : null;

                    selectedCustomerId = linkedCustomer && linkedCustomer.MaKH ? String(linkedCustomer.MaKH) : '';
                    selectedEmployeeId = linkedEmployee && linkedEmployee.MaNV ? String(linkedEmployee.MaNV) : '';

                    renderCustomerOptions();
                    renderEmployeeOptions();
                    syncLinkFieldVisibility();
                };

                const loadDependencies = async function () {
                    const responses = await Promise.all([
                        fetch(customersUrl, { headers: { 'Accept': 'application/json' } }),
                        fetch(employeesUrl, { headers: { 'Accept': 'application/json' } }),
                    ]);

                    if (!responses[0].ok) {
                        throw new Error('Không thể tải danh sách khách hàng.');
                    }

                    if (!responses[1].ok) {
                        throw new Error('Không thể tải danh sách nhân viên.');
                    }

                    customers = await responses[0].json();
                    employees = await responses[1].json();

                    renderCustomerOptions();
                    renderEmployeeOptions();
                };

                const loadAccount = async function () {
                    if (!isEdit || !accountId) {
                        syncLinkFieldVisibility();
                        return;
                    }

                    const response = await fetch(detailUrlTemplate.replace('__ACCOUNT_ID__', accountId), {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải thông tin tài khoản.');
                    }

                    const account = await response.json();
                    populateForm(account);
                };

                if (accountIdGroup) {
                    accountIdGroup.style.display = isEdit ? '' : 'none';
                }

                if (passwordHint && isEdit) {
                    passwordHint.textContent = 'Để trống nếu không muốn đổi mật khẩu.';
                }

                typeInput.addEventListener('change', function () {
                    if (typeInput.value === '0') {
                        selectedEmployeeId = '';
                        employeeInput.value = '';
                    } else {
                        selectedCustomerId = '';
                        customerInput.value = '';
                    }

                    syncLinkFieldVisibility();
                    clearFieldErrors();
                });

                customerInput.addEventListener('change', function () {
                    selectedCustomerId = customerInput.value;
                });

                employeeInput.addEventListener('change', function () {
                    selectedEmployeeId = employeeInput.value;
                });

                if (form) {
                    form.addEventListener('submit', async function (event) {
                        event.preventDefault();

                        if (!validateForm()) {
                            return;
                        }

                        const payload = {
                            Email: emailInput.value.trim(),
                            LoaiTaiKhoan: typeInput.value,
                            TrangThai: statusInput.value,
                            customer_id: typeInput.value === '0' ? (customerInput.value || null) : null,
                            employee_id: typeInput.value !== '0' ? (employeeInput.value || null) : null,
                        };

                        if (!isEdit || passwordInput.value) {
                            payload.MatKhau = passwordInput.value;
                        }

                        const requestUrl = isEdit
                            ? updateUrlTemplate.replace('__ACCOUNT_ID__', accountId)
                            : createUrl;
                        const requestMethod = isEdit ? 'PUT' : 'POST';

                        try {
                            setLoadingState(true);
                            clearAlert();
                            clearFieldErrors();

                            const response = await fetch(requestUrl, {
                                method: requestMethod,
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                },
                                body: JSON.stringify(payload)
                            });

                            const responseData = await response.json().catch(function () {
                                return {};
                            });

                            if (response.status === 422) {
                                applyServerErrors(responseData.errors || {});
                                setAlert('danger', 'Vui lòng kiểm tra lại thông tin đã nhập.');
                                return;
                            }

                            if (!response.ok) {
                                throw new Error(responseData.message || 'Không thể lưu tài khoản.');
                            }

                            setAlert('success', isEdit ? 'Cập nhật tài khoản thành công.' : 'Tạo tài khoản thành công.');

                            if (!isEdit && responseData && responseData.data && responseData.data.MaTK) {
                                accountIdInput.value = responseData.data.MaTK;
                            }

                            window.setTimeout(function () {
                                window.location.href = indexUrl;
                            }, 900);
                        } catch (error) {
                            setAlert('danger', error.message);
                        } finally {
                            setLoadingState(false);
                        }
                    });
                }

                (async function init() {
                    try {
                        await loadDependencies();
                        await loadAccount();
                        syncLinkFieldVisibility();
                    } catch (error) {
                        setAlert('danger', error.message);
                    }
                })();
            });
        </script>
    @endpush
</x-hotel-management.form-page>
