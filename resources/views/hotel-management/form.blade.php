<x-app-layout :assets="$assets ?? []">
    @php
        $isAccountModule = $moduleKey === 'accounts';
        $isCustomerModule = $moduleKey === 'customers';
        $accountType = (string) ($record['LoaiTaiKhoan'] ?? old('LoaiTaiKhoan', ''));
        $isCustomerAccount = $isAccountModule && $isEdit && $accountType === '0';
        $isEmployeeAccount = $isAccountModule && $isEdit && $accountType === '1';
        $customerAddressOptions = $module['address_options'] ?? [];
        $formAction = $formAction ?? (
            $isEdit
            ? route('hotel.modules.update', ['moduleKey' => $moduleKey, 'recordId' => $record[$module['primary_key']]])
            : route('hotel.modules.store', ['moduleKey' => $moduleKey])
        );
        $backUrl = $backUrl ?? route('hotel.modules.index', ['moduleKey' => $moduleKey]);
    @endphp

    <style>
        .hm-readonly-input {
            background-color: #f3f4f6;
            color: #6b7280;
            border-color: #cbd5e1;
            cursor: not-allowed;
        }

        .hm-service-image-input {
            height: 220px;
            border: 1px dashed #cbd5e1;
            border-radius: 16px;
            text-align: center;
            padding: 140px 20px 20px;
            font-size: 1rem;
        }

        .hm-service-image-hint {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .hm-service-image-label {
            color: #64748b;
        }

        .hm-helper-text {
            color: #9ca3af !important;
        }
    </style>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 pb-4">
                    <div class="header-title">
                        <h4 class="card-title mb-1">{{ $isEdit ? 'Chỉnh sửa' : 'Thêm mới' }} {{ $module['singular'] }}</h4>
                        <p class="mb-0 text-muted">{{ $module['description'] }}</p>
                    </div>
                    <a href="{{ $backUrl }}" class="btn btn-sm btn-primary" style="padding: 10px;">Quay lại danh sách</a>
                </div>
                <div class="card-body">
                    <form action="{{ $formAction }}" method="POST">
                        @csrf
                        @if($isEdit)
                            @method('PATCH')
                        @endif

                        <div class="row">
                            @foreach($module['fields'] as $fieldKey => $field)
                                @php
                                    $value = old($fieldKey, $record[$fieldKey] ?? '');
                                    $inputType = $field['type'] ?? 'text';
                                    $shouldHideField = false;
                                    $isCreateEditableCodeField = !$isEdit
                                        && ($field['readonly'] ?? false)
                                        && \Illuminate\Support\Str::startsWith($fieldKey, 'Ma');
                                    $isLockedAccountIdentityField = ($isCustomerAccount || $isEmployeeAccount) && in_array($fieldKey, ['MaTK', 'Email'], true);
                                    $isReadonlyLinkedFullName = $isAccountModule && $fieldKey === 'HoTen' && ($isCustomerAccount || $isEmployeeAccount);
                                    $shouldHideLinkedFullName = $isAccountModule && $fieldKey === 'HoTen' && (!$isEdit || $value === '');
                                    $shouldSkipField = $shouldHideField || $shouldHideLinkedFullName;
                                    $isReadonlyField = (($field['readonly'] ?? false) && !$isCreateEditableCodeField) || $isLockedAccountIdentityField || $isReadonlyLinkedFullName;
                                    $inputPlaceholder = $fieldKey === 'MatKhau' && $isEmployeeAccount
                                        ? 'Nhập mật khẩu mới nếu muốn thay đổi'
                                        : $field['label'];
                                    $isFieldRequired = ($field['required'] ?? false) && !($fieldKey === 'MatKhau' && $isEmployeeAccount);
                                    $helperText = null;

                                    if ($isCustomerAccount) {
                                        $shouldHideField = in_array($fieldKey, ['MatKhau', 'LoaiTaiKhoan'], true);
                                    } elseif ($isEmployeeAccount) {
                                        $shouldHideField = in_array($fieldKey, ['LoaiTaiKhoan'], true);
                                    }

                                    $shouldSkipField = $shouldHideField || $shouldHideLinkedFullName;

                                    if ($isLockedAccountIdentityField || $isReadonlyLinkedFullName) {
                                        $helperText = 'Admin không được sửa trường này.';
                                    } elseif ($fieldKey === 'MatKhau' && $isEmployeeAccount) {
                                        $helperText = 'Để trống nếu không muốn đổi mật khẩu hiện tại.';
                                    }
                                @endphp
                                @if($shouldSkipField)
                                    @continue
                                @endif
                                <div class="form-group col-md-6">
                                    <label class="form-label" for="{{ $fieldKey }}">
                                        {{ $field['label'] }}
                                        @if($field['required'] ?? false)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>

                                    @if($moduleKey === 'services' && $fieldKey === 'ServiceImage')
                                        <div style="position: relative;">
                                            <input
                                                type="text"
                                                @class([
                                                    'form-control',
                                                    'hm-service-image-input',
                                                    'hm-readonly-input' => $isReadonlyField,
                                                ])
                                                id="{{ $fieldKey }}"
                                                name="{{ $fieldKey }}"
                                                value="{{ $value }}"
                                                placeholder=""
                                                {{ $isFieldRequired ? 'required' : '' }}
                                                {{ $isReadonlyField ? 'readonly' : '' }}
                                            >
                                            @if($value === '')
                                                <div
                                                    class="d-flex flex-column align-items-center justify-content-center text-muted hm-service-image-hint"
                                                >
                                                    <svg width="42" height="42" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: 10px;">
                                                        <path d="M12 16V8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <path d="M8.5 11.5L12 8L15.5 11.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <path d="M20 16.5C20 18.433 18.433 20 16.5 20H7.5C5.567 20 4 18.433 4 16.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                                                        <path d="M16.5 4H7.5C5.567 4 4 5.567 4 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" opacity="0.35"></path>
                                                        <path d="M20 7.5C20 5.567 18.433 4 16.5 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" opacity="0.35"></path>
                                                    </svg>
                                                    <div class="fw-semibold hm-service-image-label">Tải ảnh hoặc nhập đường dẫn</div>
                                                </div>
                                            @endif
                                        </div>
                                    @elseif($isCustomerModule && $fieldKey === 'DiaChi')
                                        @php
                                            $streetValue = old('DiaChiDuong', $record['DiaChiDuong'] ?? '');
                                            $provinceValue = old('DiaChiTinh', $record['DiaChiTinh'] ?? '');
                                            $districtValue = old('DiaChiHuyen', $record['DiaChiHuyen'] ?? '');
                                            $fullAddressValue = old('DiaChi', $value);
                                            $selectedDistrictOptions = $customerAddressOptions[$provinceValue] ?? [];
                                        @endphp
                                        <div
                                            class="customer-address-group"
                                            data-address-options='@json($customerAddressOptions)'
                                        >
                                            <input
                                                type="hidden"
                                                id="{{ $fieldKey }}"
                                                name="{{ $fieldKey }}"
                                                value="{{ $fullAddressValue }}"
                                                {{ ($field['required'] ?? false) ? 'required' : '' }}
                                            >
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label mb-2" for="DiaChiDuong">Số nhà, tên đường</label>
                                                    <input
                                                        type="text"
                                                        class="form-control customer-address-street"
                                                        id="DiaChiDuong"
                                                        name="DiaChiDuong"
                                                        value="{{ $streetValue }}"
                                                        placeholder="Ví dụ: 12 Nguyễn Huệ"
                                                    >
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label mb-2" for="DiaChiTinh">Tỉnh / Thành phố</label>
                                                    <select class="form-control customer-address-province" id="DiaChiTinh" name="DiaChiTinh">
                                                        <option value="">Chọn tỉnh / thành phố</option>
                                                        @foreach($customerAddressOptions as $provinceName => $districts)
                                                            <option value="{{ $provinceName }}" {{ $provinceValue === $provinceName ? 'selected' : '' }}>
                                                                {{ $provinceName }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label mb-2" for="DiaChiHuyen">Quận / Huyện</label>
                                                    <select class="form-control customer-address-district" id="DiaChiHuyen" name="DiaChiHuyen">
                                                        <option value="">Chọn quận / huyện</option>
                                                        @foreach($selectedDistrictOptions as $districtName)
                                                            <option value="{{ $districtName }}" {{ $districtValue === $districtName ? 'selected' : '' }}>
                                                                {{ $districtName }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($inputType === 'textarea')
                                        <textarea
                                            class="form-control"
                                            id="{{ $fieldKey }}"
                                            name="{{ $fieldKey }}"
                                            rows="3"
                                            {{ ($field['required'] ?? false) ? 'required' : '' }}
                                            {{ ($field['readonly'] ?? false) ? 'readonly' : '' }}
                                        >{{ $value }}</textarea>
                                    @elseif($inputType === 'select')
                                        <select class="form-control" id="{{ $fieldKey }}" name="{{ $fieldKey }}" {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                            @foreach($field['options'] as $optionValue => $optionLabel)
                                                <option value="{{ $optionValue }}" {{ (string) $value === (string) $optionValue ? 'selected' : '' }}>
                                                    {{ $optionLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input
                                            type="{{ $inputType }}"
                                            @class([
                                                'form-control',
                                                'hm-readonly-input' => $isReadonlyField,
                                            ])
                                            id="{{ $fieldKey }}"
                                            name="{{ $fieldKey }}"
                                            value="{{ $inputType === 'password' ? '' : $value }}"
                                            placeholder="{{ $inputPlaceholder }}"
                                            {{ isset($field['step']) ? 'step=' . $field['step'] : '' }}
                                            {{ $isFieldRequired ? 'required' : '' }}
                                            {{ $isReadonlyField ? 'readonly' : '' }}
                                        >
                                        @if($helperText)
                                            <small class="text-muted d-block mt-2 hm-helper-text">{{ $helperText }}</small>
                                        @endif
                                        @if($moduleKey === 'services' && $fieldKey === 'ServiceImage' && $value !== '')
                                            @php
                                                $imagePreviewUrl = \Illuminate\Support\Str::startsWith($value, ['http://', 'https://'])
                                                    ? $value
                                                    : asset(ltrim($value, '/'));
                                            @endphp
                                            <div class="mt-3">
                                                <img
                                                    src="{{ $imagePreviewUrl }}"
                                                    alt="Xem trước ảnh dịch vụ"
                                                    class="img-fluid rounded border"
                                                    style="max-height: 200px; object-fit: cover;"
                                                >
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <button type="submit" class="btn btn-primary" style="padding: 10px;">
                            {{ $isEdit ? 'Lưu thay đổi' : 'Tạo mới' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($isCustomerModule)
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const addressGroupElement = document.querySelector('.customer-address-group');

                    if (!addressGroupElement) {
                        return;
                    }

                    const options = JSON.parse(addressGroupElement.dataset.addressOptions || '{}');
                    const streetInputElement = addressGroupElement.querySelector('.customer-address-street');
                    const provinceSelectElement = addressGroupElement.querySelector('.customer-address-province');
                    const districtSelectElement = addressGroupElement.querySelector('.customer-address-district');
                    const fullAddressInputElement = addressGroupElement.querySelector('input[type="hidden"][name="DiaChi"]');

                    if (!streetInputElement || !provinceSelectElement || !districtSelectElement || !fullAddressInputElement) {
                        return;
                    }

                    const updateDistrictOptions = function (selectedProvince, selectedDistrict) {
                        const districts = Array.isArray(options[selectedProvince]) ? options[selectedProvince] : [];

                        districtSelectElement.innerHTML = '<option value="">Chọn quận / huyện</option>';

                        districts.forEach(function (districtName) {
                            const optionElement = document.createElement('option');
                            optionElement.value = districtName;
                            optionElement.textContent = districtName;

                            if (districtName === selectedDistrict) {
                                optionElement.selected = true;
                            }

                            districtSelectElement.appendChild(optionElement);
                        });
                    };

                    const syncFullAddress = function () {
                        const street = streetInputElement.value.trim();
                        const district = districtSelectElement.value.trim();
                        const province = provinceSelectElement.value.trim();

                        fullAddressInputElement.value = [street, district, province].filter(Boolean).join(', ');
                    };

                    updateDistrictOptions(provinceSelectElement.value, districtSelectElement.value);
                    syncFullAddress();

                    streetInputElement.addEventListener('input', syncFullAddress);

                    provinceSelectElement.addEventListener('change', function () {
                        updateDistrictOptions(provinceSelectElement.value, '');
                        syncFullAddress();
                    });

                    districtSelectElement.addEventListener('change', syncFullAddress);
                });
            </script>
        @endpush
    @endif
</x-app-layout>
