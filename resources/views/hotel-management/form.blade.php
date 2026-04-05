<x-app-layout :assets="$assets ?? []">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 pb-4">
                    <div class="header-title">
                        <h4 class="card-title mb-1">{{ $isEdit ? 'Chỉnh sửa' : 'Thêm mới' }} {{ $module['singular'] }}</h4>
                        <p class="mb-0 text-muted">{{ $module['description'] }}</p>
                    </div>
                    <a href="{{ route('hotel.' . $moduleKey . '.index') }}" class="btn btn-sm btn-primary" style="padding: 10px;">Quay lại danh sách</a>
                </div>
                <div class="card-body">
                    @php
                        $isAccountModule = $moduleKey === 'accounts';
                        $accountType = (string) ($record['LoaiTaiKhoan'] ?? old('LoaiTaiKhoan', ''));
                        $isCustomerAccount = $isAccountModule && $isEdit && $accountType === '0';
                        $isEmployeeAccount = $isAccountModule && $isEdit && $accountType === '1';
                    @endphp

                    <form action="{{ $isEdit ? route('hotel.' . $moduleKey . '.update', ['recordId' => $record[$module['primary_key']]]) : route('hotel.' . $moduleKey . '.store') }}" method="POST">
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
                                    $isLockedAccountIdentityField = ($isCustomerAccount || $isEmployeeAccount) && in_array($fieldKey, ['MaTK', 'Email'], true);

                                    if ($isCustomerAccount) {
                                        $shouldHideField = in_array($fieldKey, ['MatKhau', 'LoaiTaiKhoan'], true);
                                    } elseif ($isEmployeeAccount) {
                                        $shouldHideField = in_array($fieldKey, ['LoaiTaiKhoan'], true);
                                    }
                                @endphp
                                @continue($shouldHideField)
                                <div class="form-group col-md-6">
                                    <label class="form-label" for="{{ $fieldKey }}">
                                        {{ $field['label'] }}
                                        @if($field['required'] ?? false)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>

                                    @if($inputType === 'textarea')
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
                                            class="form-control"
                                            id="{{ $fieldKey }}"
                                            name="{{ $fieldKey }}"
                                            value="{{ $inputType === 'password' ? '' : $value }}"
                                            placeholder="{{ $fieldKey === 'MatKhau' && $isEmployeeAccount ? 'Nhập mật khẩu mới nếu muốn thay đổi' : $field['label'] }}"
                                            style="{{ $isLockedAccountIdentityField ? 'background-color: #f3f4f6; color: #6b7280; border-color: #cbd5e1; cursor: not-allowed;' : '' }}"
                                            {{ isset($field['step']) ? 'step=' . $field['step'] : '' }}
                                            {{ (($field['required'] ?? false) && !($fieldKey === 'MatKhau' && $isEmployeeAccount)) ? 'required' : '' }}
                                            {{ (($field['readonly'] ?? false) || $isLockedAccountIdentityField) ? 'readonly' : '' }}
                                        >
                                        @if($isLockedAccountIdentityField)
                                            <small class="text-muted d-block mt-2" style="color: #9ca3af !important;">Admin không được chỉnh sửa trường này.</small>
                                        @endif
                                        @if($fieldKey === 'MatKhau' && $isEmployeeAccount)
                                            <small class="text-muted d-block mt-2">Để trống nếu không muốn đổi mật khẩu hiện tại.</small>
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
</x-app-layout>
