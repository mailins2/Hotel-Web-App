<x-hotel-management.form-page
    :is-edit="request()->routeIs('hotel.customers.edit')"
    :index-route="route('hotel.customers.index')"
>
    @php
        $today = $today ?? now()->toDateString();
        $provinces = $provinces ?? [];
        $communes = $communes ?? [];
    @endphp

    <div class="col-12">
        <div id="customer-form-alert" class="alert d-none mb-4" role="alert"></div>
    </div>

    <div class="form-group col-md-6" id="customer-id-group">
        <label class="form-label">Mã khách hàng</label>
        <input type="text" class="form-control hm-readonly-input" id="customer-id" value="--" readonly>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Tên khách hàng</label>
        <input
            type="text"
            class="form-control"
            id="customer-name"
            placeholder="Nguyễn Minh An"
        >
        <div class="invalid-feedback" id="customer-name-error"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Ngày sinh</label>
        <input
            type="date"
            class="form-control"
            id="customer-birthday"
            max="{{ $today }}"
        >
        <div class="invalid-feedback" id="customer-birthday-error"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Giới tính</label>
        <select class="form-select" id="customer-gender">
            <option value="">Chọn giới tính</option>
            <option value="1">Nam</option>
            <option value="0">Nữ</option>
            <option value="2">Khác</option>
        </select>
        <div class="invalid-feedback" id="customer-gender-error"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Số điện thoại</label>
        <input
            type="text"
            class="form-control"
            id="customer-phone"
            placeholder="0901234567"
            inputmode="numeric"
            maxlength="10"
            data-text-filter="digits"
        >
        <div class="invalid-feedback" id="customer-phone-error"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">CCCD</label>
        <input
            type="text"
            class="form-control"
            id="customer-cccd"
            placeholder="012345678901"
            inputmode="numeric"
            maxlength="12"
            data-text-filter="digits"
        >
        <div class="invalid-feedback" id="customer-cccd-error"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Tỉnh / Thành phố</label>
        <select class="form-select" id="customer-province">
            <option value="">Chọn tỉnh/thành phố</option>
            @foreach ($provinces as $province)
                <option value="{{ $province['code'] }}">{{ $province['name'] }}</option>
            @endforeach
        </select>
        <div class="invalid-feedback" id="customer-province-error"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Phường / Xã</label>
        <select class="form-select" id="customer-district" disabled>
            <option value="">Chọn phường/xã</option>
        </select>
        <div class="invalid-feedback" id="customer-district-error"></div>
    </div>

    <div class="form-group col-md-12">
        <label class="form-label">Số nhà và tên đường</label>
        <input
            type="text"
            class="form-control"
            id="customer-address-line"
            placeholder="26 Đường Yersin"
            maxlength="120"
        >
        <input id="customer-address" type="hidden" value="">
        <div class="invalid-feedback" id="customer-address-line-error"></div>
    </div>

    <div
        id="customer-form-config"
        data-is-edit="{{ request()->routeIs('hotel.customers.edit') ? '1' : '0' }}"
        data-customer-id="{{ request()->route('recordId') }}"
        data-create-url="{{ route('hotel.customers.store') }}"
        data-update-url-template="{{ route('hotel.customers.update', ['recordId' => '__CUSTOMER_ID__']) }}"
        data-detail-url-template="{{ url('/api/khach-hang/__CUSTOMER_ID__') }}"
        data-index-url="{{ route('hotel.customers.index') }}"
        data-customers-url="{{ url('/api/khach-hang') }}"
        data-communes='@json($communes)'
        data-csrf-token="{{ csrf_token() }}"
        hidden
    ></div>

    @push('scripts')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">
        <style>
            .ts-wrapper.customer-address-select .ts-control {
                min-height: 47px;
                padding: 0.625rem 0.875rem;
                border-color: #e7d3cb;
                border-radius: 0.375rem;
                color: #4b5563;
                box-shadow: none;
            }

            .ts-wrapper.customer-address-select.focus .ts-control {
                border-color: #c97952;
                box-shadow: 0 0 0 0.2rem rgba(201, 121, 82, 0.18);
            }

            .ts-wrapper.customer-address-select .ts-control input {
                color: #4b5563;
            }

            .ts-wrapper.customer-address-select .ts-dropdown {
                border-color: #e7d3cb;
                box-shadow: 0 14px 32px rgba(74, 52, 40, 0.12);
            }

            .ts-wrapper.customer-address-select .option.active,
            .ts-wrapper.customer-address-select .option:hover {
                background: rgba(201, 121, 82, 0.12);
                color: #7c3f28;
            }
        </style>
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const config = document.getElementById('customer-form-config');
                const form = document.querySelector('[data-ui-only-form]');
                const submitButton = form ? form.querySelector('button[type="submit"]') : null;
                const isEdit = !!(config && config.dataset.isEdit === '1');
                const customerId = config ? (config.dataset.customerId || '') : '';
                const createUrl = config ? config.dataset.createUrl : '';
                const updateUrlTemplate = config ? config.dataset.updateUrlTemplate : '';
                const detailUrlTemplate = config ? config.dataset.detailUrlTemplate : '';
                const indexUrl = config ? config.dataset.indexUrl : '';
                const customersUrl = config ? config.dataset.customersUrl : '';
                const csrfToken = config ? config.dataset.csrfToken : '';
                const communesData = JSON.parse(config ? (config.dataset.communes || '{}') : '{}');

                const alertBox = document.getElementById('customer-form-alert');
                const customerIdGroup = document.getElementById('customer-id-group');
                const customerIdInput = document.getElementById('customer-id');
                const customerNameInput = document.getElementById('customer-name');
                const customerBirthdayInput = document.getElementById('customer-birthday');
                const customerGenderInput = document.getElementById('customer-gender');
                const customerPhoneInput = document.getElementById('customer-phone');
                const customerCccdInput = document.getElementById('customer-cccd');
                const customerProvinceInput = document.getElementById('customer-province');
                const customerDistrictInput = document.getElementById('customer-district');
                const customerAddressLineInput = document.getElementById('customer-address-line');
                const customerAddressInput = document.getElementById('customer-address');

                const filters = {
                    digits: /[^0-9]/g,
                };

                let customers = [];
                let currentCustomer = null;
                let selectedDistrict = '';
                let provinceSelect = null;
                let districtSelect = null;

                const compareRecordIdDesc = function (left, right, fieldName) {
                    const leftValue = left && left[fieldName] !== undefined && left[fieldName] !== null ? String(left[fieldName]) : '';
                    const rightValue = right && right[fieldName] !== undefined && right[fieldName] !== null ? String(right[fieldName]) : '';
                    const leftNumber = Number(leftValue);
                    const rightNumber = Number(rightValue);

                    if (!Number.isNaN(leftNumber) && !Number.isNaN(rightNumber)) {
                        return rightNumber - leftNumber;
                    }

                    return rightValue.localeCompare(leftValue, undefined, { numeric: true, sensitivity: 'base' });
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
                        'customer-name',
                        'customer-birthday',
                        'customer-gender',
                        'customer-phone',
                        'customer-cccd',
                        'customer-address-line'
                    ].forEach(function (fieldId) {
                        const field = document.getElementById(fieldId);
                        const errorElement = document.getElementById(`${fieldId}-error`);

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
                        full_name: 'customer-name',
                        TenKH: 'customer-name',
                        birthday: 'customer-birthday',
                        NgaySinh: 'customer-birthday',
                        gender: 'customer-gender',
                        GioiTinh: 'customer-gender',
                        phone: 'customer-phone',
                        SoDienThoai: 'customer-phone',
                        cccd: 'customer-cccd',
                        CCCD: 'customer-cccd',
                        address_line: 'customer-address-line',
                        address: 'customer-address-line',
                        DiaChi: 'customer-address-line',
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

                const setLoadingState = function (isLoading) {
                    if (!submitButton) {
                        return;
                    }

                    submitButton.disabled = isLoading;
                    submitButton.textContent = isLoading
                        ? (isEdit ? 'Đang lưu...' : 'Đang tạo...')
                        : (isEdit ? 'Lưu thay đổi' : 'Tạo mới');
                };

                const calculateAge = function (dateString) {
                    const today = new Date();
                    const birthDate = new Date(dateString);
                    let age = today.getFullYear() - birthDate.getFullYear();
                    const monthDiff = today.getMonth() - birthDate.getMonth();

                    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                        age--;
                    }

                    return age;
                };

                const createAddressSelects = function () {
                    if (!window.TomSelect) {
                        return;
                    }

                    const baseOptions = {
                        create: false,
                        allowEmptyOption: true,
                        maxItems: 1,
                        sortField: {
                            field: '$order',
                            direction: 'asc'
                        },
                        searchField: ['text'],
                        render: {
                            no_results: function () {
                                return '<div class="no-results px-3 py-2">Không tìm thấy kết quả</div>';
                            }
                        }
                    };

                    provinceSelect = new TomSelect(customerProvinceInput, {
                        ...baseOptions,
                        placeholder: 'Chọn tỉnh/thành phố'
                    });

                    districtSelect = new TomSelect(customerDistrictInput, {
                        ...baseOptions,
                        placeholder: 'Chọn phường/xã'
                    });

                    provinceSelect.wrapper.classList.add('customer-address-select');
                    districtSelect.wrapper.classList.add('customer-address-select');

                    if (customerDistrictInput.disabled) {
                        districtSelect.disable();
                    }
                };

                const refreshDistrictSelect = function () {
                    if (!districtSelect) {
                        return;
                    }

                    const currentValue = customerDistrictInput.value || '';
                    const disabled = customerDistrictInput.disabled;

                    districtSelect.clear(true);
                    districtSelect.clearOptions();

                    Array.from(customerDistrictInput.options).forEach(function (option) {
                        if (!option.value) {
                            return;
                        }

                        districtSelect.addOption({
                            value: option.value,
                            text: option.textContent
                        });
                    });

                    if (disabled) {
                        districtSelect.disable();
                    } else {
                        districtSelect.enable();
                    }

                    if (currentValue) {
                        districtSelect.setValue(currentValue, true);
                    }

                    districtSelect.refreshOptions(false);
                };

                const setSelectValue = function (select, instance, value) {
                    select.value = value || '';

                    if (instance) {
                        instance.setValue(value || '', true);
                    }
                };

                const syncFullAddress = function () {
                    const selectedProvinceName = customerProvinceInput.value
                        ? (customerProvinceInput.options[customerProvinceInput.selectedIndex] || {}).textContent || ''
                        : '';
                    const selectedDistrictName = customerDistrictInput.value
                        ? (customerDistrictInput.options[customerDistrictInput.selectedIndex] || {}).textContent || ''
                        : '';
                    const parts = [
                        customerAddressLineInput.value.trim(),
                        selectedDistrictName.trim(),
                        selectedProvinceName.trim(),
                    ].filter(Boolean);

                    customerAddressInput.value = parts.join(', ');
                };

                const renderDistricts = function () {
                    const provinceCode = customerProvinceInput.value;

                    customerDistrictInput.innerHTML = '<option value="">Chọn phường/xã</option>';

                    if (!provinceCode) {
                        customerDistrictInput.disabled = true;
                        refreshDistrictSelect();
                        syncFullAddress();
                        return;
                    }

                    const items = communesData[provinceCode] || [];

                    items.forEach(function (item) {
                        const option = document.createElement('option');
                        option.value = item.code;
                        option.textContent = item.name;
                        customerDistrictInput.appendChild(option);
                    });

                    customerDistrictInput.disabled = items.length === 0;

                    if (selectedDistrict && items.some(function (item) { return item.code === selectedDistrict; })) {
                        customerDistrictInput.value = selectedDistrict;
                    }

                    refreshDistrictSelect();
                    syncFullAddress();
                };

                const parseExistingAddress = function (address) {
                    customerAddressLineInput.value = '';
                    setSelectValue(customerProvinceInput, provinceSelect, '');
                    customerDistrictInput.innerHTML = '<option value="">Chọn phường/xã</option>';
                    customerDistrictInput.disabled = true;
                    refreshDistrictSelect();
                    selectedDistrict = '';

                    if (!address) {
                        syncFullAddress();
                        return;
                    }

                    const parts = String(address).split(',').map(function (item) {
                        return item.trim();
                    }).filter(Boolean);

                    if (parts.length < 3) {
                        customerAddressLineInput.value = String(address);
                        customerAddressInput.value = String(address);
                        return;
                    }

                    const provinceName = parts[parts.length - 1];
                    const districtName = parts[parts.length - 2];
                    const addressLine = parts.slice(0, -2).join(', ');
                    const matchedProvince = Array.from(customerProvinceInput.options).find(function (option) {
                        return option.textContent.trim() === provinceName;
                    });

                    customerAddressLineInput.value = addressLine;

                    if (!matchedProvince || !matchedProvince.value) {
                        customerAddressInput.value = String(address);
                        return;
                    }

                    setSelectValue(customerProvinceInput, provinceSelect, matchedProvince.value);

                    const districtItems = communesData[matchedProvince.value] || [];
                    const matchedDistrict = districtItems.find(function (item) {
                        return item.name === districtName;
                    });

                    selectedDistrict = matchedDistrict ? matchedDistrict.code : '';
                    renderDistricts();

                    if (matchedDistrict) {
                        setSelectValue(customerDistrictInput, districtSelect, matchedDistrict.code);
                    }

                    syncFullAddress();
                };

                const validateForm = function () {
                    clearFieldErrors();
                    clearAlert();

                    let isValid = true;

                    if (!customerNameInput.value.trim()) {
                        setFieldError('full_name', 'Vui lòng nhập họ và tên.');
                        isValid = false;
                    } else if (customerNameInput.value.trim().length < 2 || customerNameInput.value.trim().length > 60) {
                        setFieldError('full_name', 'Họ và tên phải có từ 2 đến 60 ký tự.');
                        isValid = false;
                    } else if (!/^[\p{L}\p{M}\s]+$/u.test(customerNameInput.value.trim())) {
                        setFieldError('full_name', 'Họ và tên chỉ được gồm chữ cái và khoảng trắng.');
                        isValid = false;
                    }

                    if (!customerGenderInput.value) {
                        setFieldError('gender', 'Vui lòng chọn giới tính.');
                        isValid = false;
                    }

                    if (!customerPhoneInput.value.trim()) {
                        setFieldError('phone', 'Vui lòng nhập số điện thoại.');
                        isValid = false;
                    } else if (!/^0[0-9]{9}$/.test(customerPhoneInput.value.trim())) {
                        setFieldError('phone', 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.');
                        isValid = false;
                    }

                    if (customerCccdInput.value.trim() && !/^[0-9]{12}$/.test(customerCccdInput.value.trim())) {
                        setFieldError('cccd', 'CCCD phải gồm đúng 12 chữ số.');
                        isValid = false;
                    }

                    if (!customerBirthdayInput.value) {
                        setFieldError('birthday', 'Vui lòng chọn ngày sinh.');
                        isValid = false;
                    } else if (calculateAge(customerBirthdayInput.value) < 18) {
                        setFieldError('birthday', 'Khách hàng phải đủ 18 tuổi.');
                        isValid = false;
                    }

                    if (customerAddressLineInput.value.trim()) {
                        if (customerAddressLineInput.value.trim().length < 4 || customerAddressLineInput.value.trim().length > 120) {
                            setFieldError('address_line', 'Số nhà và tên đường phải có từ 4 đến 120 ký tự.');
                            isValid = false;
                        } else if (!/^[\p{L}\p{M}0-9\s./,-]+$/u.test(customerAddressLineInput.value.trim())) {
                            setFieldError('address_line', 'Số nhà và tên đường chỉ được gồm chữ, số và ký tự . / , -');
                            isValid = false;
                        }
                    }

                    return isValid;
                };

                const populateForm = function (customer) {
                    currentCustomer = customer;
                    customerIdInput.value = customer && customer.MaKH ? customer.MaKH : '--';
                    customerNameInput.value = customer && customer.TenKH ? customer.TenKH : '';
                    customerBirthdayInput.value = customer && customer.NgaySinh ? String(customer.NgaySinh).split(' ')[0] : '';
                    customerGenderInput.value = customer && customer.GioiTinh !== undefined && customer.GioiTinh !== null
                        ? String(customer.GioiTinh)
                        : '';
                    customerPhoneInput.value = customer && customer.SoDienThoai ? customer.SoDienThoai : '';
                    customerCccdInput.value = customer && customer.CCCD ? customer.CCCD : '';
                    parseExistingAddress(customer && customer.DiaChi ? customer.DiaChi : '');
                };

                const loadDependencies = async function () {
                    const response = await fetch(customersUrl, { headers: { 'Accept': 'application/json' } });

                    if (!response.ok) {
                        throw new Error('Không thể tải danh sách khách hàng.');
                    }

                    const customerPayload = await response.json();
                    customers = Array.isArray(customerPayload)
                        ? customerPayload.slice().sort(function (left, right) {
                            return compareRecordIdDesc(left, right, 'MaKH');
                        })
                        : [];

                    renderDistricts();
                };

                const loadCustomer = async function () {
                    if (!isEdit || !customerId) {
                        return;
                    }

                    const response = await fetch(detailUrlTemplate.replace('__CUSTOMER_ID__', customerId), {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải thông tin khách hàng.');
                    }

                    const customer = await response.json();
                    populateForm(customer);
                };

                createAddressSelects();

                document.querySelectorAll('[data-text-filter="digits"]').forEach(function (input) {
                    input.addEventListener('input', function () {
                        const filter = filters[input.dataset.textFilter];
                        if (filter) {
                            input.value = input.value.replace(filter, '');
                        }
                    });
                });

                customerNameInput.addEventListener('blur', function () {
                    customerNameInput.value = customerNameInput.value.replace(/\s+/g, ' ').trim();
                });

                customerAddressLineInput.addEventListener('blur', function () {
                    customerAddressLineInput.value = customerAddressLineInput.value.replace(/\s+/g, ' ').trim();
                    syncFullAddress();
                });

                customerProvinceInput.addEventListener('change', function () {
                    selectedDistrict = '';
                    renderDistricts();
                });

                customerDistrictInput.addEventListener('change', function () {
                    selectedDistrict = customerDistrictInput.value;
                    syncFullAddress();
                });

                customerAddressLineInput.addEventListener('input', syncFullAddress);
                customerAddressLineInput.addEventListener('change', syncFullAddress);

                if (customerIdGroup) {
                    customerIdGroup.style.display = isEdit ? '' : 'none';
                }

                if (form) {
                    form.addEventListener('submit', async function (event) {
                        event.preventDefault();

                        syncFullAddress();

                        if (!validateForm()) {
                            return;
                        }

                        const payload = {
                            full_name: customerNameInput.value.trim(),
                            phone: customerPhoneInput.value.trim(),
                            cccd: customerCccdInput.value.trim() || null,
                            birthday: customerBirthdayInput.value,
                            gender: customerGenderInput.value,
                            province: customerProvinceInput.value || '',
                            district: customerDistrictInput.value || '',
                            address_line: customerAddressLineInput.value.trim(),
                            address: customerAddressInput.value || null
                        };

                        const requestUrl = isEdit
                            ? updateUrlTemplate.replace('__CUSTOMER_ID__', customerId)
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
                                    'X-CSRF-TOKEN': csrfToken
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
                                throw new Error(responseData.message || 'Không thể lưu khách hàng.');
                            }

                            setAlert('success', isEdit ? 'Cập nhật khách hàng thành công.' : 'Tạo khách hàng thành công.');

                            if (!isEdit && responseData && responseData.data && responseData.data.MaKH) {
                                customerIdInput.value = responseData.data.MaKH;
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
                        await loadCustomer();
                    } catch (error) {
                        setAlert('danger', error.message);
                    }
                })();
            });
        </script>
    @endpush
</x-hotel-management.form-page>
