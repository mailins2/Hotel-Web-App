<x-hotel-management.form-page
    :is-edit="request()->routeIs('hotel.services.edit')"
    :index-route="route('hotel.services.index')"
>
    <style>
        .hm-service-image-preview {
            width: 180px;
            max-width: 100%;
            height: 130px;
            border-radius: 0.75rem;
            border: 1px solid rgba(0, 0, 0, 0.08);
            object-fit: cover;
            background: #f4f5f7;
            display: block;
            margin-top: 0.75rem;
        }

        .hm-service-image-preview.is-hidden {
            display: none;
        }

        .hm-service-image-note {
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>

    <div class="col-12">
        <div id="service-form-alert" class="alert d-none mb-4" role="alert"></div>
    </div>

    <div class="form-group col-md-6" id="service-id-group">
        <label class="form-label">Mã dịch vụ</label>
        <input
            type="text"
            class="form-control hm-readonly-input"
            id="service-id"
            value="--"
            readonly
        >
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Tên dịch vụ</label>
        <input
            type="text"
            class="form-control"
            id="service-name"
            placeholder="Nhập tên dịch vụ"
        >
        <div class="invalid-feedback" id="service-name-error"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Giá dịch vụ</label>
        <input
            type="number"
            min="0"
            class="form-control"
            id="service-price"
            placeholder="Nhập giá dịch vụ"
        >
        <div class="invalid-feedback" id="service-price-error"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Loại dịch vụ</label>
        <div class="hm-select-wrap">
            <select class="form-select" id="service-type">
                <option value="">Chọn loại dịch vụ</option>
                <option value="1">Dịch vụ ăn uống</option>
                <option value="2">Dịch vụ phòng</option>
                <option value="3">Dịch vụ giải trí</option>
            </select>
        </div>
        <div class="invalid-feedback" id="service-type-error"></div>
    </div>

    <div class="form-group col-md-12">
        <label class="form-label">Ảnh dịch vụ</label>
        <input
            type="file"
            class="form-control"
            id="service-image-file"
            accept="image/*"
        >
        <img
            src=""
            alt="Xem trước ảnh dịch vụ"
            id="service-image-preview"
            class="hm-service-image-preview is-hidden"
        >
        <div class="hm-service-image-note" id="service-image-note">Chọn 1 ảnh từ máy tính để upload lên Cloudinary.</div>
        <div class="invalid-feedback d-block" id="service-image-file-error"></div>
    </div>

    <div
        id="service-form-config"
        data-is-edit="{{ request()->routeIs('hotel.services.edit') ? '1' : '0' }}"
        data-service-id="{{ request()->route('recordId') }}"
        data-create-url="{{ url('/api/dich-vu') }}"
        data-update-url-template="{{ url('/api/dich-vu/__SERVICE_ID__') }}"
        data-image-index-url="{{ url('/api/hinh-anh') }}"
        data-image-detail-url-template="{{ url('/api/hinh-anh/__IMAGE_ID__') }}"
        data-index-url="{{ route('hotel.services.index') }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const config = document.getElementById('service-form-config');
                const form = document.querySelector('[data-ui-only-form]');
                const submitButton = form ? form.querySelector('button[type="submit"]') : null;
                const isEdit = !!(config && config.dataset.isEdit === '1');
                const serviceId = config ? (config.dataset.serviceId || '') : '';
                const createUrl = config ? config.dataset.createUrl : '';
                const updateUrlTemplate = config ? config.dataset.updateUrlTemplate : '';
                const imageIndexUrl = config ? config.dataset.imageIndexUrl : '';
                const imageDetailUrlTemplate = config ? config.dataset.imageDetailUrlTemplate : '';
                const indexUrl = config ? config.dataset.indexUrl : '';

                const alertBox = document.getElementById('service-form-alert');
                const serviceIdGroup = document.getElementById('service-id-group');
                const serviceIdInput = document.getElementById('service-id');
                const nameInput = document.getElementById('service-name');
                const priceInput = document.getElementById('service-price');
                const typeInput = document.getElementById('service-type');
                const imageFileInput = document.getElementById('service-image-file');
                const imagePreview = document.getElementById('service-image-preview');
                const imageNote = document.getElementById('service-image-note');

                let currentImageId = null;
                let currentImageUrl = '';

                const fieldMap = {
                    TenDV: nameInput,
                    GiaDV: priceInput,
                    LoaiDV: typeInput,
                    image: imageFileInput,
                };

                const errorKeyMap = {
                    TenDV: 'name',
                    GiaDV: 'price',
                    LoaiDV: 'type',
                    image: 'image-file',
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
                        const errorElement = document.getElementById(`service-${errorKeyMap[fieldName]}-error`);

                        if (field) {
                            field.classList.remove('is-invalid');
                        }

                        if (errorElement) {
                            errorElement.textContent = '';
                        }
                    });
                };

                const setFieldError = function (fieldName, message) {
                    const field = fieldMap[fieldName];
                    const errorElement = document.getElementById(`service-${errorKeyMap[fieldName]}-error`);

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
                        if (Array.isArray(messages) && messages.length && errorKeyMap[fieldName]) {
                            setFieldError(fieldName, messages[0]);
                        }
                    });
                };

                const updatePreview = function (src, note) {
                    if (!imagePreview) {
                        return;
                    }

                    imagePreview.src = src || '';
                    imagePreview.classList.toggle('is-hidden', !src);

                    if (imageNote) {
                        imageNote.textContent = note || 'Chọn 1 ảnh từ máy tính để upload lên Cloudinary.';
                    }
                };

                const validateForm = function () {
                    clearFieldErrors();
                    clearAlert();

                    let isValid = true;
                    const nameValue = nameInput.value.trim();
                    const priceValue = priceInput.value.trim();
                    const typeValue = typeInput.value;

                    if (!nameValue) {
                        setFieldError('TenDV', 'Vui lòng nhập tên dịch vụ.');
                        isValid = false;
                    }

                    if (!priceValue) {
                        setFieldError('GiaDV', 'Vui lòng nhập giá dịch vụ.');
                        isValid = false;
                    } else if (Number(priceValue) < 0) {
                        setFieldError('GiaDV', 'Giá dịch vụ phải lớn hơn hoặc bằng 0.');
                        isValid = false;
                    }

                    if (!typeValue) {
                        setFieldError('LoaiDV', 'Vui lòng chọn loại dịch vụ.');
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

                const populateForm = function (service, image) {
                    serviceIdInput.value = service && service.MaDV ? service.MaDV : '--';
                    nameInput.value = service && service.TenDV ? service.TenDV : '';
                    priceInput.value = service && service.GiaDV !== undefined && service.GiaDV !== null ? service.GiaDV : '';
                    typeInput.value = service && service.LoaiDV !== undefined && service.LoaiDV !== null ? String(service.LoaiDV) : '';

                    currentImageId = image && image.Id ? image.Id : null;
                    currentImageUrl = image && image.Url ? image.Url : '';

                    if (currentImageUrl) {
                        updatePreview(currentImageUrl, 'Ảnh hiện tại. Chọn file mới nếu muốn thay thế.');
                    } else {
                        updatePreview('', 'Chọn 1 ảnh từ máy tính để upload lên Cloudinary.');
                    }
                };

                const syncImage = async function (targetServiceId) {
                    const file = imageFileInput && imageFileInput.files && imageFileInput.files[0]
                        ? imageFileInput.files[0]
                        : null;

                    if (!file) {
                        return;
                    }

                    const formData = new FormData();
                    formData.append('image', file);
                    formData.append('MaDV', targetServiceId);

                    if (isEdit && currentImageId) {
                        formData.append('_method', 'PUT');
                    }

                    const imageResponse = await fetch(
                        isEdit && currentImageId
                            ? imageDetailUrlTemplate.replace('__IMAGE_ID__', currentImageId)
                            : imageIndexUrl,
                        {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                            },
                            body: formData,
                        }
                    );

                    const imageData = await imageResponse.json().catch(function () {
                        return {};
                    });

                    if (imageResponse.status === 422) {
                        applyServerErrors(imageData.errors || {});
                        throw new Error('Không thể lưu ảnh dịch vụ.');
                    }

                    if (!imageResponse.ok) {
                        throw new Error(imageData.message || 'Không thể lưu ảnh dịch vụ.');
                    }

                    const savedImage = imageData && imageData.data ? imageData.data : null;
                    if (savedImage && savedImage.Id) {
                        currentImageId = savedImage.Id;
                        currentImageUrl = savedImage.Url || currentImageUrl;
                    }
                };

                const loadService = async function () {
                    if (!isEdit || !serviceId) {
                        return;
                    }

                    try {
                        const [serviceResponse, imageResponse] = await Promise.all([
                            fetch(updateUrlTemplate.replace('__SERVICE_ID__', serviceId), {
                                headers: { 'Accept': 'application/json' },
                            }),
                            fetch(imageIndexUrl, {
                                headers: { 'Accept': 'application/json' },
                            }),
                        ]);

                        if (!serviceResponse.ok) {
                            throw new Error('Không thể tải thông tin dịch vụ.');
                        }

                        if (!imageResponse.ok) {
                            throw new Error('Không thể tải ảnh dịch vụ.');
                        }

                        const servicePayload = await serviceResponse.json();
                        const imagePayload = await imageResponse.json();
                        const service = servicePayload && servicePayload.data ? servicePayload.data : null;
                        const images = Array.isArray(imagePayload) ? imagePayload : [];
                        const matchedImage = images.find(function (image) {
                            return String(image.MaDV || '') === String(serviceId);
                        }) || null;

                        populateForm(service, matchedImage);
                    } catch (error) {
                        setAlert('danger', error.message);
                    }
                };

                if (serviceIdGroup) {
                    serviceIdGroup.style.display = isEdit ? '' : 'none';
                }

                if (imageFileInput) {
                    imageFileInput.addEventListener('change', function (event) {
                        const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;

                        if (!file) {
                            updatePreview(currentImageUrl, currentImageUrl
                                ? 'Ảnh hiện tại. Chọn file mới nếu muốn thay thế.'
                                : 'Chọn 1 ảnh từ máy tính để upload lên Cloudinary.');
                            return;
                        }

                        updatePreview(URL.createObjectURL(file), file.name);
                    });
                }

                if (form) {
                    form.addEventListener('submit', async function (event) {
                        event.preventDefault();

                        if (!validateForm()) {
                            return;
                        }

                        const payload = {
                            TenDV: nameInput.value.trim(),
                            GiaDV: priceInput.value.trim(),
                            LoaiDV: typeInput.value,
                        };

                        const requestUrl = isEdit
                            ? updateUrlTemplate.replace('__SERVICE_ID__', serviceId)
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
                                },
                                body: JSON.stringify(payload),
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
                                throw new Error(responseData.message || 'Không thể lưu dịch vụ.');
                            }

                            const savedService = responseData && responseData.data ? responseData.data : null;
                            const savedServiceId = savedService && savedService.MaDV ? savedService.MaDV : serviceId;
                            let imageErrorMessage = '';

                            if (savedServiceId) {
                                try {
                                    await syncImage(savedServiceId);
                                } catch (imageError) {
                                    imageErrorMessage = imageError.message;
                                }
                            }

                            serviceIdInput.value = savedServiceId || '--';

                            if (imageErrorMessage) {
                                setAlert('warning', `${isEdit ? 'Cập nhật' : 'Tạo'} dịch vụ thành công, nhưng xử lý ảnh gặp lỗi.`);
                            } else {
                                setAlert('success', isEdit ? 'Cập nhật dịch vụ thành công.' : 'Tạo dịch vụ thành công.');
                            }

                            window.setTimeout(function () {
                                window.location.href = indexUrl;
                            }, imageErrorMessage ? 1400 : 900);
                        } catch (error) {
                            setAlert('danger', error.message);
                        } finally {
                            setLoadingState(false);
                        }
                    });
                }

                loadService();
            });
        </script>
    @endpush
</x-hotel-management.form-page>
