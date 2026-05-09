<x-hotel-management.form-page
    :is-edit="request()->routeIs('hotel.promotions.edit')"
    :index-route="route('hotel.promotions.index')"
>
    <div class="col-12">
        <div id="promotion-form-alert" class="alert d-none mb-4" role="alert"></div>
    </div>

    <div class="form-group col-md-6" id="promotion-id-group">
        <label class="form-label">Mã khuyến mãi</label>
        <input
            type="text"
            class="form-control hm-readonly-input"
            id="promotion-id"
            value="--"
            readonly
        >
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Tên chương trình</label>
        <input
            type="text"
            class="form-control"
            id="promotion-name"
            placeholder="Nhập tên chương trình"
        >
        <div class="invalid-feedback" id="promotion-name-error"></div>
    </div>

    <div class="form-group col-md-12">
        <label class="form-label">Mô tả</label>
        <textarea
            class="form-control"
            id="promotion-description"
            rows="3"
            placeholder="Nhập mô tả khuyến mãi"
        ></textarea>
        <div class="invalid-feedback" id="promotion-description-error"></div>
    </div>

    <div class="form-group col-md-4">
        <label class="form-label">Điểm yêu cầu</label>
        <input
            type="number"
            class="form-control"
            id="promotion-points"
            min="0"
            value="0"
        >
        <div class="invalid-feedback" id="promotion-points-error"></div>
    </div>

    <div class="form-group col-md-4">
        <label class="form-label">Ngày bắt đầu</label>
        <input
            type="date"
            class="form-control"
            id="promotion-start"
        >
        <div class="invalid-feedback" id="promotion-start-error"></div>
    </div>

    <div class="form-group col-md-4">
        <label class="form-label">Ngày kết thúc</label>
        <input
            type="date"
            class="form-control"
            id="promotion-end"
        >
        <div class="invalid-feedback" id="promotion-end-error"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Phần trăm giảm giá</label>
        <input
            type="number"
            class="form-control"
            id="promotion-discount"
            min="0"
            max="100"
            step="0.01"
            value="0"
        >
        <div class="invalid-feedback" id="promotion-discount-error"></div>
    </div>

    <div
        id="promotion-form-config"
        data-is-edit="{{ request()->routeIs('hotel.promotions.edit') ? '1' : '0' }}"
        data-promotion-id="{{ request()->route('recordId') }}"
        data-create-url="{{ url('/api/khuyen-mai') }}"
        data-detail-url-template="{{ url('/api/khuyen-mai/__PROMOTION_ID__') }}"
        data-index-url="{{ route('hotel.promotions.index') }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const config = document.getElementById('promotion-form-config');
                const form = document.querySelector('[data-ui-only-form]');
                const submitButton = form ? form.querySelector('button[type="submit"]') : null;
                const isEdit = !!(config && config.dataset.isEdit === '1');
                const promotionId = config ? (config.dataset.promotionId || '') : '';
                const createUrl = config ? config.dataset.createUrl : '';
                const detailUrlTemplate = config ? config.dataset.detailUrlTemplate : '';
                const indexUrl = config ? config.dataset.indexUrl : '';

                const alertBox = document.getElementById('promotion-form-alert');
                const promotionIdGroup = document.getElementById('promotion-id-group');
                const promotionIdInput = document.getElementById('promotion-id');
                const promotionNameInput = document.getElementById('promotion-name');
                const promotionDescriptionInput = document.getElementById('promotion-description');
                const promotionPointsInput = document.getElementById('promotion-points');
                const promotionStartInput = document.getElementById('promotion-start');
                const promotionEndInput = document.getElementById('promotion-end');
                const promotionDiscountInput = document.getElementById('promotion-discount');

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
                        ['promotion-name', promotionNameInput],
                        ['promotion-description', promotionDescriptionInput],
                        ['promotion-points', promotionPointsInput],
                        ['promotion-start', promotionStartInput],
                        ['promotion-end', promotionEndInput],
                        ['promotion-discount', promotionDiscountInput]
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
                        TenKM: 'promotion-name',
                        MoTa: 'promotion-description',
                        Diem: 'promotion-points',
                        NgayBatDau: 'promotion-start',
                        NgayKetThuc: 'promotion-end',
                        PhanTramGiamGia: 'promotion-discount'
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
                    const startValue = promotionStartInput.value;
                    const endValue = promotionEndInput.value;
                    const discountValue = Number(promotionDiscountInput.value);

                    if (!promotionNameInput.value.trim()) {
                        setFieldError('TenKM', 'Vui lòng nhập tên chương trình.');
                        isValid = false;
                    }

                    if (promotionPointsInput.value === '' || Number(promotionPointsInput.value) < 0) {
                        setFieldError('Diem', 'Điểm yêu cầu phải lớn hơn hoặc bằng 0.');
                        isValid = false;
                    }

                    if (!startValue) {
                        setFieldError('NgayBatDau', 'Vui lòng chọn ngày bắt đầu.');
                        isValid = false;
                    }

                    if (!endValue) {
                        setFieldError('NgayKetThuc', 'Vui lòng chọn ngày kết thúc.');
                        isValid = false;
                    }

                    if (startValue && endValue && endValue < startValue) {
                        setFieldError('NgayKetThuc', 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.');
                        isValid = false;
                    }

                    if (promotionDiscountInput.value === '' || Number.isNaN(discountValue) || discountValue < 0 || discountValue > 100) {
                        setFieldError('PhanTramGiamGia', 'Phần trăm giảm giá phải từ 0 đến 100.');
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

                const populateForm = function (promotion) {
                    promotionIdInput.value = promotion && promotion.MaKM ? promotion.MaKM : '--';
                    promotionNameInput.value = promotion && promotion.TenKM ? promotion.TenKM : '';
                    promotionDescriptionInput.value = promotion && promotion.MoTa ? promotion.MoTa : '';
                    promotionPointsInput.value = promotion && promotion.Diem !== undefined && promotion.Diem !== null ? promotion.Diem : 0;
                    promotionStartInput.value = promotion && promotion.NgayBatDau ? String(promotion.NgayBatDau).slice(0, 10) : '';
                    promotionEndInput.value = promotion && promotion.NgayKetThuc ? String(promotion.NgayKetThuc).slice(0, 10) : '';
                    promotionDiscountInput.value = promotion && promotion.PhanTramGiamGia !== undefined && promotion.PhanTramGiamGia !== null
                        ? promotion.PhanTramGiamGia
                        : 0;
                };

                const loadPromotion = async function () {
                    if (!isEdit || !promotionId) {
                        return;
                    }

                    const response = await fetch(detailUrlTemplate.replace('__PROMOTION_ID__', promotionId), {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải thông tin khuyến mãi.');
                    }

                    const promotion = await response.json();
                    populateForm(promotion);
                };

                if (promotionIdGroup) {
                    promotionIdGroup.style.display = isEdit ? '' : 'none';
                }

                if (form) {
                    form.addEventListener('submit', async function (event) {
                        event.preventDefault();

                        if (!validateForm()) {
                            return;
                        }

                        const payload = {
                            TenKM: promotionNameInput.value.trim(),
                            MoTa: promotionDescriptionInput.value.trim(),
                            Diem: Number(promotionPointsInput.value),
                            NgayBatDau: promotionStartInput.value,
                            NgayKetThuc: promotionEndInput.value,
                            PhanTramGiamGia: Number(promotionDiscountInput.value)
                        };

                        const requestUrl = isEdit
                            ? detailUrlTemplate.replace('__PROMOTION_ID__', promotionId)
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
                                throw new Error(responseData.message || 'Không thể lưu khuyến mãi.');
                            }

                            setAlert('success', isEdit ? 'Cập nhật khuyến mãi thành công.' : 'Tạo khuyến mãi thành công.');

                            const savedPromotion = responseData && responseData.data ? responseData.data : null;
                            if (!isEdit && savedPromotion && savedPromotion.MaKM) {
                                promotionIdInput.value = savedPromotion.MaKM;
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

                loadPromotion().catch(function (error) {
                    setAlert('danger', error.message);
                });
            });
        </script>
    @endpush
</x-hotel-management.form-page>
