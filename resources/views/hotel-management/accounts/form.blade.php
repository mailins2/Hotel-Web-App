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
            <option value="2">Nhân viên kinh doanh</option>
            <option value="2">Kế toán</option>
        </select>
        <div class="invalid-feedback" id="account-type-error"></div>
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

   

    <div class="form-group col-md-6">
        <label class="form-label">Trạng thái</label>
        <select class="form-select" id="account-status">
            <option value="1">Hoạt động</option>
            <option value="0">Không hoạt động</option>
        </select>
        <div class="invalid-feedback" id="account-status-error"></div>
    </div>

    <div
        id="account-form-config"
        data-is-edit="{{ request()->routeIs('hotel.accounts.edit') ? '1' : '0' }}"
        data-account-id="{{ request()->route('recordId') }}"
        data-create-url="{{ url('/api/tai-khoan') }}"
        data-update-url-template="{{ url('/api/tai-khoan/__ACCOUNT_ID__') }}"
        data-index-url="{{ route('hotel.accounts.index') }}"
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
                const indexUrl = config ? config.dataset.indexUrl : '';

                const alertBox = document.getElementById('account-form-alert');
                const accountIdGroup = document.getElementById('account-id-group');
                const accountIdInput = document.getElementById('account-id');
                const emailInput = document.getElementById('account-email');
                const passwordInput = document.getElementById('account-password');
                const passwordConfirmationInput = document.getElementById('account-password-confirmation');
                const typeInput = document.getElementById('account-type');
                const statusInput = document.getElementById('account-status');
                const passwordHint = document.getElementById('account-password-hint');

                const fieldMap = {
                    Email: emailInput,
                    MatKhau: passwordInput,
                    MatKhauConfirmation: passwordConfirmationInput,
                    LoaiTaiKhoan: typeInput,
                    TrangThai: statusInput
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
                        const errorElement = document.getElementById(`account-${fieldName === 'LoaiTaiKhoan' ? 'type' : fieldName === 'TrangThai' ? 'status' : fieldName === 'MatKhau' ? 'password' : fieldName === 'MatKhauConfirmation' ? 'password-confirmation' : 'email'}-error`);

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
                        TrangThai: 'status'
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
                };

                const loadAccount = async function () {
                    if (!isEdit || !accountId) {
                        return;
                    }

                    try {
                        const response = await fetch(updateUrlTemplate.replace('__ACCOUNT_ID__', accountId), {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Không thể tải thông tin tài khoản.');
                        }

                        const account = await response.json();
                        populateForm(account);
                    } catch (error) {
                        setAlert('danger', error.message);
                    }
                };

                if (accountIdGroup) {
                    accountIdGroup.style.display = isEdit ? '' : 'none';
                }

                if (passwordHint && isEdit) {
                    passwordHint.textContent = 'Để trống nếu không muốn đổi mật khẩu.';
                }

                if (form) {
                    form.addEventListener('submit', async function (event) {
                        event.preventDefault();

                        if (!validateForm()) {
                            return;
                        }

                        const payload = {
                            Email: emailInput.value.trim(),
                            LoaiTaiKhoan: typeInput.value,
                            TrangThai: statusInput.value
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

                loadAccount();
            });
        </script>
    @endpush
</x-hotel-management.form-page>
