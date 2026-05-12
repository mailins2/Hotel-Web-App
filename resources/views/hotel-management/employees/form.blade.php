<x-hotel-management.form-page
    :is-edit="request()->routeIs('hotel.employees.edit')"
    :index-route="route('hotel.employees.index')"
>
    <div class="col-12">
        <div id="employee-form-alert" class="alert d-none mb-4" role="alert"></div>
    </div>

    <div class="form-group col-md-6" id="employee-id-group">
        <label class="form-label">Mã nhân viên</label>
        <input
            type="text"
            class="form-control hm-readonly-input"
            id="employee-id"
            value="--"
            readonly
        >
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Tên nhân viên</label>
        <input
            type="text"
            class="form-control"
            id="employee-name"
            placeholder="Nhập tên nhân viên"
        >
        <div class="invalid-feedback" id="employee-name-error"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Mã tài khoản</label>
        <select class="form-select" id="employee-account-id">
            <option value="">Chọn tài khoản</option>
        </select>
        <div class="form-text" id="employee-account-hint">Danh sách các tài khoản chưa được gắn cho nhân viên khác</div>
        <div class="invalid-feedback" id="employee-account-id-error"></div>
    </div>

    <div
        id="employee-form-config"
        data-is-edit="{{ request()->routeIs('hotel.employees.edit') ? '1' : '0' }}"
        data-employee-id="{{ request()->route('recordId') }}"
        data-create-url="{{ url('/api/nhan-vien') }}"
        data-detail-url-template="{{ url('/api/nhan-vien/__EMPLOYEE_ID__') }}"
        data-index-url="{{ route('hotel.employees.index') }}"
        data-accounts-url="{{ url('/api/tai-khoan') }}"
        data-employees-url="{{ url('/api/nhan-vien') }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const config = document.getElementById('employee-form-config');
                const form = document.querySelector('[data-ui-only-form]');
                const submitButton = form ? form.querySelector('button[type="submit"]') : null;
                const isEdit = !!(config && config.dataset.isEdit === '1');
                const employeeId = config ? (config.dataset.employeeId || '') : '';
                const createUrl = config ? config.dataset.createUrl : '';
                const detailUrlTemplate = config ? config.dataset.detailUrlTemplate : '';
                const indexUrl = config ? config.dataset.indexUrl : '';
                const accountsUrl = config ? config.dataset.accountsUrl : '';
                const employeesUrl = config ? config.dataset.employeesUrl : '';

                const alertBox = document.getElementById('employee-form-alert');
                const employeeIdGroup = document.getElementById('employee-id-group');
                const employeeIdInput = document.getElementById('employee-id');
                const employeeNameInput = document.getElementById('employee-name');
                const employeeAccountInput = document.getElementById('employee-account-id');
                const accountHint = document.getElementById('employee-account-hint');

                let accounts = [];
                let employees = [];
                let currentEmployee = null;

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
                    [
                        ['employee-name', employeeNameInput],
                        ['employee-account-id', employeeAccountInput]
                    ].forEach(function (item) {
                        const key = item[0];
                        const field = item[1];
                        const errorElement = document.getElementById(`${key}-error`);

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
                        TenNV: 'employee-name',
                        MaTK: 'employee-account-id'
                    };

                    const key = keyMap[fieldName];
                    if (!key) {
                        return;
                    }

                    const field = document.getElementById(key);
                    const errorElement = document.getElementById(`${key}-error`);

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

                const validateForm = function () {
                    clearFieldErrors();
                    clearAlert();

                    let isValid = true;

                    if (!employeeNameInput.value.trim()) {
                        setFieldError('TenNV', 'Vui lòng nhập tên nhân viên.');
                        isValid = false;
                    }

                    if (!employeeAccountInput.value) {
                        setFieldError('MaTK', 'Vui lòng chọn tài khoản.');
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

                const renderAccountOptions = function () {
                    const usedAccountIds = new Set(
                        employees
                            .filter(function (employee) {
                                return !currentEmployee || String(employee.MaNV || '') !== String(currentEmployee.MaNV || '');
                            })
                            .map(function (employee) {
                                return String(employee.MaTK || '');
                            })
                    );

                    const currentAccountId = currentEmployee && currentEmployee.MaTK !== undefined && currentEmployee.MaTK !== null
                        ? String(currentEmployee.MaTK)
                        : '';

                    const availableAccounts = accounts.filter(function (account) {
                        const accountId = String(account.MaTK || '');
                        const accountType = Number(account && account.LoaiTaiKhoan !== undefined ? account.LoaiTaiKhoan : -1);
                        const isEmployeeAccount = accountType !== 0;

                        return isEmployeeAccount && (accountId === currentAccountId || !usedAccountIds.has(accountId));
                    });

                    employeeAccountInput.innerHTML = '<option value="">Chọn tài khoản</option>' + availableAccounts.map(function (account) {
                        const accountId = account && account.MaTK ? account.MaTK : '--';
                        const accountEmail = account && account.Email ? account.Email : '--';
                        return `<option value="${accountId}">${accountId} - ${accountEmail} (${mapAccountType(account.LoaiTaiKhoan)})</option>`;
                    }).join('');

                    if (currentAccountId) {
                        employeeAccountInput.value = currentAccountId;
                    }

                    if (accountHint) {
                        accountHint.textContent = availableAccounts.length
                            ? 'Danh sách các tài khoản chưa được gắn cho nhân viên khác.'
                            : 'Hiện chưa có tài khoản trống để gắn cho nhân viên.';
                    }
                };

                const populateForm = function (employee) {
                    currentEmployee = employee;
                    employeeIdInput.value = employee && employee.MaNV ? employee.MaNV : '--';
                    employeeNameInput.value = employee && employee.TenNV ? employee.TenNV : '';
                    renderAccountOptions();
                };

                const loadDependencies = async function () {
                    const responses = await Promise.all([
                        fetch(accountsUrl, { headers: { 'Accept': 'application/json' } }),
                        fetch(employeesUrl, { headers: { 'Accept': 'application/json' } })
                    ]);

                    if (!responses[0].ok) {
                        throw new Error('Không thể tải danh sách tài khoản.');
                    }

                    if (!responses[1].ok) {
                        throw new Error('Không thể tải danh sách nhân viên.');
                    }

                    const accountPayload = await responses[0].json();
                    accounts = Array.isArray(accountPayload && accountPayload.data) ? accountPayload.data : [];
                    employees = await responses[1].json();

                    renderAccountOptions();
                };

                const loadEmployee = async function () {
                    if (!isEdit || !employeeId) {
                        return;
                    }

                    const response = await fetch(detailUrlTemplate.replace('__EMPLOYEE_ID__', employeeId), {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải thông tin nhân viên.');
                    }

                    const employee = await response.json();
                    populateForm(employee);
                };

                if (employeeIdGroup) {
                    employeeIdGroup.style.display = isEdit ? '' : 'none';
                }

                if (form) {
                    form.addEventListener('submit', async function (event) {
                        event.preventDefault();

                        if (!validateForm()) {
                            return;
                        }

                        const payload = {
                            TenNV: employeeNameInput.value.trim(),
                            MaTK: employeeAccountInput.value
                        };

                        const requestUrl = isEdit
                            ? detailUrlTemplate.replace('__EMPLOYEE_ID__', employeeId)
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
                                    'Content-Type': 'application/json'
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
                                throw new Error(responseData.message || 'Không thể lưu nhân viên.');
                            }

                            setAlert('success', isEdit ? 'Cập nhật nhân viên thành công.' : 'Tạo nhân viên thành công.');

                            if (!isEdit && responseData && responseData.data && responseData.data.MaNV) {
                                employeeIdInput.value = responseData.data.MaNV;
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
                        await loadEmployee();
                    } catch (error) {
                        setAlert('danger', error.message);
                    }
                })();
            });
        </script>
    @endpush
</x-hotel-management.form-page>
