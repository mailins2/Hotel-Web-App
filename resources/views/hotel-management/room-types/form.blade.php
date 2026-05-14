<x-hotel-management.form-page
    :is-edit="request()->routeIs('hotel.room-types.edit')"
    :index-route="route('hotel.room-types.index')"
>
    <style>
        .hm-image-input-row {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .hm-image-input-row .form-control {
            flex: 1;
        }

        .hm-image-add-button {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
        }

        .hm-image-add-button svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }
    </style>

    <div class="col-12">
        <div id="room-type-form-alert" class="alert d-none mb-4" role="alert"></div>
    </div>

    <div class="form-group col-md-6" id="room-type-id-group">
        <label class="form-label">Mã loại phòng</label>
        <input
            type="text"
            class="form-control hm-readonly-input"
            id="room-type-id"
            value="--"
            readonly
        >
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Tên loại phòng</label>
        <input
            type="text"
            class="form-control"
            id="room-type-name"
            placeholder="Nhập tên loại phòng"
        >
        <div class="invalid-feedback" id="room-type-name-error"></div>
    </div>

    <div class="form-group col-md-12">
        <label class="form-label">Mô tả</label>
        <textarea
            class="form-control"
            id="room-type-description"
            rows="3"
            placeholder="Nhập mô tả loại phòng"
        ></textarea>
        <div class="invalid-feedback" id="room-type-description-error"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Người lớn</label>
        <input
            type="number"
            class="form-control"
            id="room-type-adults"
            min="1"
            value="1"
        >
        <div class="invalid-feedback" id="room-type-adults-error"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Trẻ em</label>
        <input
            type="number"
            class="form-control"
            id="room-type-children"
            min="0"
            value="0"
        >
        <div class="invalid-feedback" id="room-type-children-error"></div>
    </div>

    <div class="form-group col-md-12">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <label class="form-label mb-0">Ảnh phòng</label>
            <button type="button" class="btn btn-sm btn-light hm-image-add-button" id="room-type-add-image-button">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 5V19" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                    <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                </svg>
                Thêm ảnh
            </button>
        </div>
        <div id="room-type-image-inputs"></div>
        <div class="form-text" id="room-type-images-hint">Nhập tối đa 5 link ảnh phòng.</div>
        <div class="invalid-feedback d-block" id="room-type-images-error"></div>
    </div>

    <div
        id="room-type-form-config"
        data-is-edit="{{ request()->routeIs('hotel.room-types.edit') ? '1' : '0' }}"
        data-room-type-id="{{ request()->route('recordId') }}"
        data-create-url="{{ url('/api/loai-phong') }}"
        data-detail-url-template="{{ url('/api/loai-phong/__ROOM_TYPE_ID__') }}"
        data-image-create-url="{{ url('/api/hinh-anh') }}"
        data-image-detail-url-template="{{ url('/api/hinh-anh/__IMAGE_ID__') }}"
        data-index-url="{{ route('hotel.room-types.index') }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const config = document.getElementById('room-type-form-config');
                const form = document.querySelector('[data-ui-only-form]');
                const submitButton = form ? form.querySelector('button[type="submit"]') : null;
                const isEdit = !!(config && config.dataset.isEdit === '1');
                const roomTypeId = config ? (config.dataset.roomTypeId || '') : '';
                const createUrl = config ? config.dataset.createUrl : '';
                const detailUrlTemplate = config ? config.dataset.detailUrlTemplate : '';
                const imageCreateUrl = config ? config.dataset.imageCreateUrl : '';
                const imageDetailUrlTemplate = config ? config.dataset.imageDetailUrlTemplate : '';
                const indexUrl = config ? config.dataset.indexUrl : '';

                const alertBox = document.getElementById('room-type-form-alert');
                const roomTypeIdGroup = document.getElementById('room-type-id-group');
                const roomTypeIdInput = document.getElementById('room-type-id');
                const roomTypeNameInput = document.getElementById('room-type-name');
                const roomTypeDescriptionInput = document.getElementById('room-type-description');
                const roomTypeAdultsInput = document.getElementById('room-type-adults');
                const roomTypeChildrenInput = document.getElementById('room-type-children');
                const imageInputsContainer = document.getElementById('room-type-image-inputs');
                const addImageButton = document.getElementById('room-type-add-image-button');
                const imageHint = document.getElementById('room-type-images-hint');

                let currentImages = [];

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
                        ['room-type-name', roomTypeNameInput],
                        ['room-type-description', roomTypeDescriptionInput],
                        ['room-type-adults', roomTypeAdultsInput],
                        ['room-type-children', roomTypeChildrenInput]
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

                    const imagesError = document.getElementById('room-type-images-error');
                    if (imagesError) {
                        imagesError.textContent = '';
                    }
                };

                const setFieldError = function (fieldName, message) {
                    const keyMap = {
                        TenLoaiPhong: 'room-type-name',
                        Mota: 'room-type-description',
                        NguoiLon: 'room-type-adults',
                        TreEm: 'room-type-children',
                        HinhAnh: 'room-type-images'
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

                const updateImageUiState = function () {
                    const rows = imageInputsContainer.querySelectorAll('[data-room-type-image-row]');
                    const imageCount = rows.length;

                    if (addImageButton) {
                        addImageButton.disabled = imageCount >= 5;
                    }

                    if (imageHint) {
                        imageHint.textContent = `Đang dùng ${imageCount}/5 ô nhập link ảnh.`;
                    }
                };

                const createImageRow = function (value) {
                    const row = document.createElement('div');
                    row.className = 'hm-image-input-row';
                    row.setAttribute('data-room-type-image-row', '1');

                    const input = document.createElement('input');
                    input.type = 'text';
                    input.className = 'form-control';
                    input.placeholder = 'Nhập đường dẫn ảnh minh họa';
                    input.value = value || '';

                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'btn btn-sm btn-light text-danger';
                    removeButton.setAttribute('data-room-type-remove-image', '1');
                    removeButton.title = 'Xóa ảnh';
                    removeButton.textContent = 'Xóa';

                    if (removeButton) {
                        removeButton.addEventListener('click', function () {
                            row.remove();
                            updateImageUiState();
                        });
                    }

                    row.appendChild(input);
                    row.appendChild(removeButton);
                    imageInputsContainer.appendChild(row);
                    updateImageUiState();
                };

                const ensureAtLeastOneImageRow = function () {
                    if (!imageInputsContainer.querySelector('[data-room-type-image-row]')) {
                        createImageRow('');
                    }
                };

                const getImageInputValues = function () {
                    return Array.from(imageInputsContainer.querySelectorAll('[data-room-type-image-row] input'))
                        .map(function (input) {
                            return input.value.trim();
                        })
                        .filter(function (value) {
                            return value !== '';
                        });
                };

                const validateForm = function () {
                    clearFieldErrors();
                    clearAlert();

                    let isValid = true;
                    const imageValues = getImageInputValues();

                    if (!roomTypeNameInput.value.trim()) {
                        setFieldError('TenLoaiPhong', 'Vui lòng nhập tên loại phòng.');
                        isValid = false;
                    }

                    if (!roomTypeAdultsInput.value || Number(roomTypeAdultsInput.value) < 1) {
                        setFieldError('NguoiLon', 'Người lớn phải lớn hơn hoặc bằng 1.');
                        isValid = false;
                    }

                    if (roomTypeChildrenInput.value === '' || Number(roomTypeChildrenInput.value) < 0) {
                        setFieldError('TreEm', 'Trẻ em phải lớn hơn hoặc bằng 0.');
                        isValid = false;
                    }

                    if (imageValues.length > 5) {
                        setFieldError('HinhAnh', 'Chỉ được nhập tối đa 5 ảnh.');
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

                const getImageUrl = function (image) {
                    if (!image) {
                        return '';
                    }

                    return image.Url || image.url || image.DuongDan || image.duong_dan || '';
                };

                const populateForm = function (roomType) {
                    roomTypeIdInput.value = roomType && roomType.MaLoaiPhong ? roomType.MaLoaiPhong : '--';
                    roomTypeNameInput.value = roomType && roomType.TenLoaiPhong ? roomType.TenLoaiPhong : '';
                    roomTypeDescriptionInput.value = roomType && roomType.Mota ? roomType.Mota : '';
                    roomTypeAdultsInput.value = roomType && roomType.NguoiLon !== undefined && roomType.NguoiLon !== null ? roomType.NguoiLon : 1;
                    roomTypeChildrenInput.value = roomType && roomType.TreEm !== undefined && roomType.TreEm !== null ? roomType.TreEm : 0;

                    imageInputsContainer.innerHTML = '';
                    currentImages = roomType && Array.isArray(roomType.hinhs) ? roomType.hinhs.slice(0, 5) : [];

                    if (currentImages.length) {
                        currentImages.forEach(function (image) {
                            createImageRow(getImageUrl(image));
                        });
                    } else {
                        ensureAtLeastOneImageRow();
                    }
                };

                const syncImages = async function (savedRoomTypeId) {
                    const desiredUrls = getImageInputValues().slice(0, 5);
                    const existingImages = Array.isArray(currentImages) ? currentImages.slice(0, 5) : [];
                    const totalSteps = Math.max(existingImages.length, desiredUrls.length);

                    for (let index = 0; index < totalSteps; index += 1) {
                        const existingImage = existingImages[index] || null;
                        const desiredUrl = desiredUrls[index] || '';

                        if (existingImage && desiredUrl) {
                            if (getImageUrl(existingImage) !== desiredUrl) {
                                const updateResponse = await fetch(
                                    imageDetailUrlTemplate.replace('__IMAGE_ID__', existingImage.Id),
                                    {
                                        method: 'PUT',
                                        headers: {
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            Url: desiredUrl,
                                            MaLoaiPhong: savedRoomTypeId
                                        })
                                    }
                                );

                                if (!updateResponse.ok) {
                                    throw new Error('Không thể cập nhật ảnh phòng.');
                                }
                            }
                        } else if (existingImage && !desiredUrl) {
                            const deleteResponse = await fetch(
                                imageDetailUrlTemplate.replace('__IMAGE_ID__', existingImage.Id),
                                {
                                    method: 'DELETE',
                                    headers: {
                                        'Accept': 'application/json'
                                    }
                                }
                            );

                            if (!deleteResponse.ok) {
                                throw new Error('Không thể xóa ảnh phòng.');
                            }
                        } else if (!existingImage && desiredUrl) {
                            const createImageResponse = await fetch(imageCreateUrl, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    Url: desiredUrl,
                                    MaLoaiPhong: savedRoomTypeId
                                })
                            });

                            if (!createImageResponse.ok) {
                                throw new Error('Không thể lưu ảnh phòng.');
                            }
                        }
                    }
                };

                const loadRoomType = async function () {
                    if (!isEdit || !roomTypeId) {
                        ensureAtLeastOneImageRow();
                        return;
                    }

                    const response = await fetch(detailUrlTemplate.replace('__ROOM_TYPE_ID__', roomTypeId), {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải thông tin loại phòng.');
                    }

                    const payload = await response.json();
                    const roomType = payload && payload.data ? payload.data : null;

                    if (!roomType) {
                        throw new Error('Không tìm thấy dữ liệu loại phòng.');
                    }

                    populateForm(roomType);
                };

                if (roomTypeIdGroup) {
                    roomTypeIdGroup.style.display = isEdit ? '' : 'none';
                }

                if (addImageButton) {
                    addImageButton.addEventListener('click', function () {
                        const currentCount = imageInputsContainer.querySelectorAll('[data-room-type-image-row]').length;
                        if (currentCount >= 5) {
                            return;
                        }
                        createImageRow('');
                    });
                }

                if (form) {
                    form.addEventListener('submit', async function (event) {
                        event.preventDefault();

                        if (!validateForm()) {
                            return;
                        }

                        const payload = {
                            TenLoaiPhong: roomTypeNameInput.value.trim(),
                            Mota: roomTypeDescriptionInput.value,
                            NguoiLon: Number(roomTypeAdultsInput.value),
                            TreEm: Number(roomTypeChildrenInput.value)
                        };

                        const requestUrl = isEdit
                            ? detailUrlTemplate.replace('__ROOM_TYPE_ID__', roomTypeId)
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
                                throw new Error(responseData.message || 'Không thể lưu loại phòng.');
                            }

                            const savedRoomType = responseData && responseData.data ? responseData.data : null;
                            const savedRoomTypeId = savedRoomType && savedRoomType.MaLoaiPhong
                                ? savedRoomType.MaLoaiPhong
                                : roomTypeId;

                            if (!savedRoomTypeId) {
                                throw new Error('Không xác định được loại phòng vừa lưu.');
                            }

                            let imageErrorMessage = '';

                            try {
                                await syncImages(savedRoomTypeId);
                            } catch (imageError) {
                                imageErrorMessage = imageError.message;
                            }

                            if (!isEdit) {
                                roomTypeIdInput.value = savedRoomTypeId;
                            }

                            if (imageErrorMessage) {
                                setAlert('warning', `${isEdit ? 'Cập nhật' : 'Tạo'} loại phòng thành công, nhưng có lỗi khi lưu ảnh: ${imageErrorMessage}`);
                            } else {
                                setAlert('success', isEdit ? 'Cập nhật loại phòng thành công.' : 'Tạo loại phòng thành công.');
                            }

                            window.setTimeout(function () {
                                window.location.href = indexUrl;
                            }, imageErrorMessage ? 1500 : 900);
                        } catch (error) {
                            setAlert('danger', error.message);
                        } finally {
                            setLoadingState(false);
                        }
                    });
                }

                (async function init() {
                    try {
                        await loadRoomType();
                    } catch (error) {
                        setAlert('danger', error.message);
                        ensureAtLeastOneImageRow();
                    }
                })();
            });
        </script>
    @endpush
</x-hotel-management.form-page>
