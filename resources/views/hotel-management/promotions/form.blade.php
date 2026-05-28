<x-hotel-management.form-page
    :is-edit="request()->routeIs('hotel.promotions.edit')"
    :index-route="route('hotel.promotions.index')"
>
    <style>
        .hm-image-input-row {
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            margin-bottom: 0.75rem;
            padding: 0.9rem;
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 0.75rem;
            background: #fff;
        }

        .hm-image-input-body {
            flex: 1;
            display: grid;
            gap: 0.65rem;
        }

        .hm-image-preview {
            width: 120px;
            height: 88px;
            border-radius: 0.65rem;
            border: 1px solid rgba(0, 0, 0, 0.08);
            object-fit: cover;
            background: #f4f5f7;
            display: block;
        }

        .hm-image-preview.is-hidden {
            display: none;
        }

        .hm-image-meta {
            font-size: 0.9rem;
            color: #6c757d;
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

        .hm-date-display-wrap {
            position: relative;
        }

        .hm-date-display-wrap .form-control {
            padding-right: 44px;
        }

        .hm-date-picker-button {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #111827;
            padding: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .hm-date-picker-button svg {
            width: 20px;
            height: 20px;
        }

        .hm-native-date-picker {
            position: absolute;
            right: 10px;
            top: 50%;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }
    </style>

    <div class="col-12">
        <div id="promotion-form-alert" class="alert d-none mb-4" role="alert"></div>
    </div>

    <div class="form-group col-md-6" id="promotion-id-group">
        <label class="form-label">Mã khuyến mãi</label>
        <input
            type="text"
            class="form-control"
            id="promotion-id"
            maxlength="10"
            placeholder="Nhập mã khuyến mãi"
            required
        >
        <div class="invalid-feedback" id="promotion-id-error"></div>
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
        <div class="hm-date-display-wrap">
            <input
                type="text"
                class="form-control"
                id="promotion-start"
                inputmode="numeric"
                maxlength="10"
                placeholder="dd/mm/yyyy"
            >
            <button type="button" class="hm-date-picker-button" id="promotion-start-picker-button" aria-label="Chọn ngày bắt đầu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </button>
            <input type="date" id="promotion-start-picker" class="hm-native-date-picker" tabindex="-1" aria-hidden="true">
        </div>
        <div class="invalid-feedback" id="promotion-start-error"></div>
    </div>

    <div class="form-group col-md-4">
        <label class="form-label">Ngày kết thúc</label>
        <div class="hm-date-display-wrap">
            <input
                type="text"
                class="form-control"
                id="promotion-end"
                inputmode="numeric"
                maxlength="10"
                placeholder="dd/mm/yyyy"
            >
            <button type="button" class="hm-date-picker-button" id="promotion-end-picker-button" aria-label="Chọn ngày kết thúc">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </button>
            <input type="date" id="promotion-end-picker" class="hm-native-date-picker" tabindex="-1" aria-hidden="true">
        </div>
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

    <div class="form-group col-md-6">
        <label class="form-label">Loại khuyến mãi</label>
        <select class="form-select" id="promotion-type">
            <option value="0">Chung</option>
            <option value="1">Hội viên</option>
        </select>
        <div class="invalid-feedback" id="promotion-type-error"></div>
    </div>

    <div class="form-group col-md-12">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <label class="form-label mb-0">Ảnh khuyến mãi</label>
            <button type="button" class="btn btn-sm btn-light hm-image-add-button" id="promotion-add-image-button">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 5V19" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                    <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                </svg>
                Thêm ô ảnh
            </button>
        </div>
        <div id="promotion-image-inputs"></div>
        <div class="form-text" id="promotion-images-hint">Tối đa 5 ảnh cho mỗi khuyến mãi.</div>
        <div class="invalid-feedback d-block" id="promotion-images-error"></div>
    </div>

    <div
        id="promotion-form-config"
        data-is-edit="{{ request()->routeIs('hotel.promotions.edit') ? '1' : '0' }}"
        data-promotion-id="{{ request()->route('recordId') }}"
        data-create-url="{{ url('/api/khuyen-mai') }}"
        data-detail-url-template="{{ url('/api/khuyen-mai/__PROMOTION_ID__') }}"
        data-image-create-url="{{ url('/api/hinh-anh') }}"
        data-image-detail-url-template="{{ url('/api/hinh-anh/__IMAGE_ID__') }}"
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
                const imageCreateUrl = config ? config.dataset.imageCreateUrl : '';
                const imageDetailUrlTemplate = config ? config.dataset.imageDetailUrlTemplate : '';
                const indexUrl = config ? config.dataset.indexUrl : '';

                const alertBox = document.getElementById('promotion-form-alert');
                const promotionIdInput = document.getElementById('promotion-id');
                const promotionNameInput = document.getElementById('promotion-name');
                const promotionDescriptionInput = document.getElementById('promotion-description');
                const promotionPointsInput = document.getElementById('promotion-points');
                const promotionStartInput = document.getElementById('promotion-start');
                const promotionStartPickerButton = document.getElementById('promotion-start-picker-button');
                const promotionStartPicker = document.getElementById('promotion-start-picker');
                const promotionEndInput = document.getElementById('promotion-end');
                const promotionEndPickerButton = document.getElementById('promotion-end-picker-button');
                const promotionEndPicker = document.getElementById('promotion-end-picker');
                const promotionDiscountInput = document.getElementById('promotion-discount');
                const promotionTypeInput = document.getElementById('promotion-type');
                const imageInputsContainer = document.getElementById('promotion-image-inputs');
                const addImageButton = document.getElementById('promotion-add-image-button');
                const imageHint = document.getElementById('promotion-images-hint');

                const maxImageBytes = 5 * 1024 * 1024;
                let removedExistingImageIds = [];
                let isSubmitting = false;

                const setAlert = function (type, message) {
                    if (!alertBox) return;
                    alertBox.className = `alert alert-${type} mb-4`;
                    alertBox.textContent = message;
                };

                const clearAlert = function () {
                    if (!alertBox) return;
                    alertBox.className = 'alert d-none mb-4';
                    alertBox.textContent = '';
                };

                const compressImage = function (file, delay) {
                    return new Promise(function (resolve, reject) {
                        if (!file.type.startsWith('image/')) {
                            reject(new Error('File is not an image'));
                            return;
                        }

                        const originalName = String(file.name || 'image.jpg')
                            .replace(/\.[^.]+$/, '')
                            .substring(0, 50) + '.jpg';

                        const processImage = function () {
                            const reader = new FileReader();
                            reader.onload = function (event) {
                                const img = new Image();
                                img.onload = function () {
                                    try {
                                        const canvas = document.createElement('canvas');
                                        let width = img.width;
                                        let height = img.height;
                                        const maxWidth = 1200;
                                        const maxHeight = 900;

                                        if (width > maxWidth || height > maxHeight) {
                                            const ratio = Math.min(maxWidth / width, maxHeight / height);
                                            width = Math.round(width * ratio);
                                            height = Math.round(height * ratio);
                                        }

                                        canvas.width = width;
                                        canvas.height = height;
                                        const ctx = canvas.getContext('2d');
                                        if (!ctx) {
                                            reject(new Error('Cannot get canvas context'));
                                            return;
                                        }

                                        ctx.drawImage(img, 0, 0, width, height);

                                        canvas.toBlob(function (blob) {
                                            if (!blob) {
                                                reject(new Error('Failed to compress image'));
                                                return;
                                            }

                                            const compressedFile = new File([blob], originalName, {
                                                type: 'image/jpeg',
                                                lastModified: Date.now(),
                                            });

                                            resolve(compressedFile);
                                        }, 'image/jpeg', 0.65);
                                    } catch (error) {
                                        reject(error);
                                    }
                                };

                                img.onerror = function () {
                                    reject(new Error('Failed to load image'));
                                };

                                img.onabort = function () {
                                    reject(new Error('Image loading aborted'));
                                };

                                img.src = event.target.result;
                            };

                            reader.onerror = function () {
                                reject(new Error('Failed to read file'));
                            };

                            reader.readAsDataURL(file);
                        };

                        if (delay) {
                            setTimeout(processImage, delay);
                        } else {
                            processImage();
                        }
                    });
                };

                const clearFieldErrors = function () {
                    [
                        ['promotion-id', promotionIdInput],
                        ['promotion-name', promotionNameInput],
                        ['promotion-description', promotionDescriptionInput],
                        ['promotion-points', promotionPointsInput],
                        ['promotion-start', promotionStartInput],
                        ['promotion-end', promotionEndInput],
                        ['promotion-discount', promotionDiscountInput],
                        ['promotion-type', promotionTypeInput],
                    ].forEach(function (item) {
                        const field = item[1];
                        const errorElement = document.getElementById(`${item[0]}-error`);
                        field?.classList.remove('is-invalid');
                        if (errorElement) errorElement.textContent = '';
                    });

                    const imagesError = document.getElementById('promotion-images-error');
                    if (imagesError) imagesError.textContent = '';
                };

                const setFieldError = function (fieldName, message) {
                    const keyMap = {
                        MaKM: 'promotion-id',
                        TenKM: 'promotion-name',
                        MoTa: 'promotion-description',
                        Diem: 'promotion-points',
                        NgayBatDau: 'promotion-start',
                        NgayKetThuc: 'promotion-end',
                        PhanTramGiamGia: 'promotion-discount',
                        LoaiKM: 'promotion-type',
                        HinhAnh: 'promotion-images',
                        image: 'promotion-images',
                        images: 'promotion-images',
                    };
                    const key = keyMap[fieldName];
                    if (!key) return;

                    const field = document.getElementById(key);
                    const errorElement = document.getElementById(`${key}-error`);
                    field?.classList.add('is-invalid');
                    if (errorElement) errorElement.textContent = message;
                };

                const applyServerErrors = function (errors) {
                    Object.keys(errors || {}).forEach(function (fieldName) {
                        const messages = errors[fieldName];
                        if (Array.isArray(messages) && messages.length) {
                            setFieldError(fieldName.split('.')[0], messages[0]);
                        }
                    });
                };

                const firstServerError = function (errors) {
                    const firstKey = Object.keys(errors || {})[0];
                    const messages = firstKey ? errors[firstKey] : null;

                    return Array.isArray(messages) && messages.length ? messages[0] : '';
                };

                const parseJsonOrText = async function (response) {
                    const text = await response.text().catch(function () {
                        return '';
                    });

                    if (!text) {
                        return {};
                    }

                    try {
                        return JSON.parse(text);
                    } catch (error) {
                        return { message: text };
                    }
                };

                const formatFileSize = function (bytes) {
                    const mb = bytes / (1024 * 1024);
                    return `${mb.toFixed(mb >= 10 ? 0 : 1)}MB`;
                };

                const formatDateForDisplay = function (dateValue) {
                    const value = String(dateValue || '').slice(0, 10);
                    const parts = value.split('-');

                    if (parts.length !== 3) {
                        return '';
                    }

                    return `${parts[2]}/${parts[1]}/${parts[0]}`;
                };

                const parseDisplayDate = function (dateValue) {
                    const match = String(dateValue || '').trim().match(/^(\d{2})\/(\d{2})\/(\d{4})$/);

                    if (!match) {
                        return '';
                    }

                    const [, day, month, year] = match;
                    const parsed = new Date(Number(year), Number(month) - 1, Number(day));

                    if (
                        parsed.getFullYear() !== Number(year)
                        || parsed.getMonth() !== Number(month) - 1
                        || parsed.getDate() !== Number(day)
                    ) {
                        return '';
                    }

                    return `${year}-${month}-${day}`;
                };

                const bindDateDisplayInput = function (input, picker, button) {
                    const openPicker = function () {
                        if (picker && typeof picker.showPicker === 'function') {
                            picker.showPicker();
                        } else {
                            picker?.click();
                        }
                    };

                    input?.addEventListener('input', function () {
                        const digits = input.value.replace(/\D+/g, '').slice(0, 8);
                        const parts = [digits.slice(0, 2), digits.slice(2, 4), digits.slice(4, 8)].filter(Boolean);
                        input.value = parts.join('/');
                        if (picker) {
                            picker.value = parseDisplayDate(input.value);
                        }
                    });

                    input?.addEventListener('click', function () {
                        openPicker();
                    });

                    button?.addEventListener('click', function () {
                        openPicker();
                    });

                    picker?.addEventListener('change', function () {
                        input.value = formatDateForDisplay(picker.value);
                    });
                };

                const updateImageUiState = function () {
                    const imageCount = imageInputsContainer.querySelectorAll('[data-promotion-image-row]').length;
                    if (addImageButton) addImageButton.disabled = imageCount >= 5;
                    if (imageHint) imageHint.textContent = `Đang sử dụng ${imageCount}/5 ô ảnh.`;
                };

                const setRowPreview = function (preview, meta, src, metaText) {
                    if (preview) {
                        preview.src = src || '';
                        preview.classList.toggle('is-hidden', !src);
                    }
                    if (meta) meta.textContent = metaText || '';
                };

                const createImageRow = function (existingImage) {
                    const row = document.createElement('div');
                    row.className = 'hm-image-input-row';
                    row.setAttribute('data-promotion-image-row', '1');
                    row.dataset.rowType = existingImage && existingImage.Id ? 'existing' : 'new';

                    if (existingImage && existingImage.Id) {
                        row.dataset.imageId = String(existingImage.Id);
                    }

                    const body = document.createElement('div');
                    body.className = 'hm-image-input-body';

                    const preview = document.createElement('img');
                    preview.className = 'hm-image-preview is-hidden';
                    preview.alt = 'Xem trước';

                    const meta = document.createElement('div');
                    meta.className = 'hm-image-meta';

                    body.appendChild(preview);
                    body.appendChild(meta);

                    if (existingImage && existingImage.Url) {
                        setRowPreview(preview, meta, existingImage.Url, 'Ảnh hiện tại');
                    } else {
                        const input = document.createElement('input');
                        input.type = 'file';
                        input.accept = 'image/*';
                        input.multiple = true;
                        input.className = 'form-control';
                        input.setAttribute('data-promotion-image-file', '1');
                        input.addEventListener('change', async function (event) {
                            const files = Array.from(event.target.files || []);
                            if (!files.length) {
                                setRowPreview(preview, meta, '', '');
                                return;
                            }

                            const invalidFile = files.find(function (file) {
                                return !file.type || !file.type.startsWith('image/');
                            });

                            if (invalidFile) {
                                input.value = '';
                                setRowPreview(preview, meta, '', '');
                                setFieldError('images', 'Vui long chon dung file anh.');
                                return;
                            }

                            const processedFiles = [];
                            const compressionThreshold = 1024 * 1024;

                            for (let i = 0; i < files.length; i++) {
                                const file = files[i];
                                try {
                                    if (file.size > compressionThreshold) {
                                        const delay = i * 300;
                                        const compressed = await compressImage(file, delay);
                                        processedFiles.push(compressed);
                                    } else {
                                        processedFiles.push(file);
                                    }
                                } catch (error) {
                                    input.value = '';
                                    setRowPreview(preview, meta, '', '');
                                    setFieldError('images', `${file.name}: Loi xu ly anh - ${error.message}`);
                                    return;
                                }
                            }

                            try {
                                const dataTransfer = new DataTransfer();
                                processedFiles.forEach(function (file) {
                                    dataTransfer.items.add(file);
                                });
                                input.files = dataTransfer.files;
                            } catch (error) {
                                console.warn('DataTransfer error, files stored locally:', error);
                            }

                            setRowPreview(
                                preview,
                                meta,
                                URL.createObjectURL(processedFiles[0]),
                                processedFiles.length > 1 ? `${processedFiles.length} anh da chon` : processedFiles[0].name
                            );
                        });
                        body.appendChild(input);
                    }

                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'btn btn-sm btn-light text-danger';
                    removeButton.title = 'Xóa ảnh';
                    removeButton.textContent = 'Xóa';
                    removeButton.addEventListener('click', function () {
                        if (row.dataset.imageId) {
                            removedExistingImageIds.push(row.dataset.imageId);
                        }

                        row.remove();
                        updateImageUiState();

                        if (!imageInputsContainer.querySelector('[data-promotion-image-row]') && !isEdit) {
                            createImageRow();
                        }
                    });

                    row.appendChild(body);
                    row.appendChild(removeButton);
                    imageInputsContainer.appendChild(row);
                    updateImageUiState();
                };

                const ensureAtLeastOneImageRow = function () {
                    if (!imageInputsContainer.querySelector('[data-promotion-image-row]')) {
                        createImageRow();
                    }
                };

                const getSelectedFiles = function () {
                    return Array.from(imageInputsContainer.querySelectorAll('[data-promotion-image-row]'))
                        .filter(function (row) {
                            return row.dataset.rowType === 'new';
                        })
                        .flatMap(function (row) {
                            const input = row.querySelector('[data-promotion-image-file]');
                            return Array.from(input && input.files ? input.files : []).map(function (file) {
                                return { file };
                            });
                        })
                        .filter(function (item) {
                            return !!item.file;
                        });
                };

                const validateForm = function () {
                    clearFieldErrors();
                    clearAlert();

                    let isValid = true;
                    const startValue = parseDisplayDate(promotionStartInput.value);
                    const endValue = parseDisplayDate(promotionEndInput.value);
                    const discountValue = Number(promotionDiscountInput.value);
                    const promotionIdValue = promotionIdInput.value.trim();
                    const totalRows = imageInputsContainer.querySelectorAll('[data-promotion-image-row]').length;
                    const selectedFiles = getSelectedFiles();
                    const existingImageCount = imageInputsContainer.querySelectorAll('[data-promotion-image-row][data-row-type="existing"]').length;
                    const totalImageCount = existingImageCount + selectedFiles.length;

                    if (!isEdit && !promotionIdValue) {
                        setFieldError('MaKM', 'Vui lòng nhập mã khuyến mãi.');
                        isValid = false;
                    } else if (!isEdit && promotionIdValue.length > 10) {
                        setFieldError('MaKM', 'Mã khuyến mãi tối đa 10 ký tự.');
                        isValid = false;
                    }

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

                    if (totalRows > 5) {
                        setFieldError('HinhAnh', 'Chỉ được chọn tối đa 5 ảnh.');
                        isValid = false;
                    }

                    if (totalImageCount > 5) {
                        setFieldError('HinhAnh', 'Chi duoc chon toi da 5 anh.');
                        isValid = false;
                    }

                    return isValid;
                };

                const setLoadingState = function (isLoading) {
                    if (!submitButton) return;
                    submitButton.disabled = isLoading;
                    submitButton.textContent = isLoading
                        ? (isEdit ? 'Đang lưu...' : 'Đang tạo...')
                        : (isEdit ? 'Lưu thay đổi' : 'Tạo mới');
                };

                const populateForm = function (promotion) {
                    promotionIdInput.value = promotion && promotion.MaKM ? promotion.MaKM : '';
                    promotionNameInput.value = promotion && promotion.TenKM ? promotion.TenKM : '';
                    promotionDescriptionInput.value = promotion && promotion.MoTa ? promotion.MoTa : '';
                    promotionPointsInput.value = promotion && promotion.Diem !== undefined && promotion.Diem !== null ? promotion.Diem : 0;
                    promotionStartInput.value = promotion && promotion.NgayBatDau ? formatDateForDisplay(promotion.NgayBatDau) : '';
                    promotionStartPicker.value = parseDisplayDate(promotionStartInput.value);
                    promotionEndInput.value = promotion && promotion.NgayKetThuc ? formatDateForDisplay(promotion.NgayKetThuc) : '';
                    promotionEndPicker.value = parseDisplayDate(promotionEndInput.value);
                    promotionDiscountInput.value = promotion && promotion.PhanTramGiamGia !== undefined && promotion.PhanTramGiamGia !== null
                        ? promotion.PhanTramGiamGia
                        : 0;
                    promotionTypeInput.value = promotion && promotion.LoaiKM !== undefined && promotion.LoaiKM !== null
                        ? String(promotion.LoaiKM)
                        : '0';

                    imageInputsContainer.innerHTML = '';
                    removedExistingImageIds = [];

                    if (promotion && Array.isArray(promotion.hinhs) && promotion.hinhs.length) {
                        promotion.hinhs.slice(0, 5).forEach(function (image) {
                            createImageRow(image);
                        });
                    } else {
                        ensureAtLeastOneImageRow();
                    }
                };

                const syncImages = async function (savedPromotionId) {
                    const deleteQueue = Array.from(new Set(removedExistingImageIds));
                    const selectedFiles = getSelectedFiles();

                    for (const imageId of deleteQueue) {
                        const deleteResponse = await fetch(
                            imageDetailUrlTemplate.replace('__IMAGE_ID__', imageId),
                            {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                },
                            }
                        );

                        if (!deleteResponse.ok) {
                            throw new Error('Khong the xoa anh khuyen mai cu.');
                        }
                    }

                    for (const item of selectedFiles) {
                        const formData = new FormData();
                        formData.append('image', item.file);
                        formData.append('MaKM', savedPromotionId);

                        const createImageResponse = await fetch(imageCreateUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                            },
                            body: formData,
                        });

                        const createImageData = await parseJsonOrText(createImageResponse);

                        if (createImageResponse.status === 422) {
                            applyServerErrors(createImageData.errors || {});
                            throw new Error(`${item.file.name}: ${firstServerError(createImageData.errors) || createImageData.message || 'Khong the luu anh khuyen mai.'}`);
                        }

                        if (createImageResponse.status === 413) {
                            throw new Error(`${item.file.name}: File qua lon hoac server tu choi request upload anh (413).`);
                        }

                        if (!createImageResponse.ok) {
                            throw new Error(`${item.file.name}: HTTP ${createImageResponse.status} - ${createImageData.message || 'Khong the luu anh khuyen mai.'}`);
                        }
                    }
                };

                const loadPromotion = async function () {
                    if (!isEdit || !promotionId) {
                        ensureAtLeastOneImageRow();
                        return;
                    }

                    const response = await fetch(detailUrlTemplate.replace('__PROMOTION_ID__', promotionId), {
                        headers: { 'Accept': 'application/json' }
                    });

                    const payload = await response.json().catch(function () {
                        return {};
                    });

                    if (!response.ok || payload.success === false) {
                        throw new Error(payload.message || 'Không thể tải thông tin khuyến mãi.');
                    }

                    populateForm(payload.data || null);
                };

                if (promotionIdInput) {
                    promotionIdInput.readOnly = isEdit;
                    promotionIdInput.disabled = isEdit;
                    promotionIdInput.classList.toggle('hm-readonly-input', isEdit);
                    promotionIdInput.placeholder = isEdit ? '' : 'Nhập mã khuyến mãi';
                }

                bindDateDisplayInput(promotionStartInput, promotionStartPicker, promotionStartPickerButton);
                bindDateDisplayInput(promotionEndInput, promotionEndPicker, promotionEndPickerButton);

                if (addImageButton) {
                    addImageButton.addEventListener('click', function () {
                        const currentCount = imageInputsContainer.querySelectorAll('[data-promotion-image-row]').length;
                        if (currentCount >= 5) return;
                        createImageRow();
                    });
                }

                if (form) {
                    form.addEventListener('submit', async function (event) {
                        event.preventDefault();

                        if (isSubmitting) return;
                        if (!validateForm()) return;

                        const payload = {
                            TenKM: promotionNameInput.value.trim(),
                            MoTa: promotionDescriptionInput.value.trim(),
                            Diem: Number(promotionPointsInput.value),
                            NgayBatDau: parseDisplayDate(promotionStartInput.value),
                            NgayKetThuc: parseDisplayDate(promotionEndInput.value),
                            PhanTramGiamGia: Number(promotionDiscountInput.value),
                            LoaiKM: Number(promotionTypeInput.value),
                        };

                        if (!isEdit) {
                            payload.MaKM = promotionIdInput.value.trim();
                        }

                        const requestUrl = isEdit
                            ? detailUrlTemplate.replace('__PROMOTION_ID__', promotionId)
                            : createUrl;

                        try {
                            isSubmitting = true;
                            setLoadingState(true);
                            clearAlert();
                            clearFieldErrors();

                            const response = await fetch(requestUrl, {
                                method: isEdit ? 'PUT' : 'POST',
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

                            if (response.status === 413) {
                                setAlert('danger', 'Server tu choi request voi ma 413. Vui long tai lai trang va thu lai; neu van loi, kiem tra Content-Length trong tab Network.');
                                return;
                            }

                            if (!response.ok) {
                                throw new Error(responseData.message || 'Không thể lưu khuyến mãi.');
                            }

                            const savedPromotion = responseData && responseData.data ? responseData.data : null;
                            const savedPromotionId = savedPromotion && savedPromotion.MaKM
                                ? savedPromotion.MaKM
                                : (isEdit ? promotionId : promotionIdInput.value.trim());

                            if (!savedPromotionId) {
                                throw new Error('Không xác định được khuyến mãi vừa lưu.');
                            }

                            let imageErrorMessage = '';

                            try {
                                await syncImages(savedPromotionId);
                            } catch (imageError) {
                                imageErrorMessage = imageError.message;
                            }

                            if (!isEdit) {
                                promotionIdInput.value = savedPromotionId;
                            }

                            if (imageErrorMessage) {
                                setAlert('warning', `${isEdit ? 'Cập nhật' : 'Tạo'} khuyến mãi thành công, nhưng xử lý ảnh gặp lỗi: ${imageErrorMessage}`);
                            } else {
                                setAlert('success', isEdit ? 'Cập nhật khuyến mãi thành công.' : 'Tạo khuyến mãi thành công.');
                            }

                            window.setTimeout(function () {
                                window.location.href = indexUrl;
                            }, imageErrorMessage ? 1500 : 900);
                        } catch (error) {
                            setAlert('danger', error.message);
                        } finally {
                            isSubmitting = false;
                            setLoadingState(false);
                        }
                    });
                }

                loadPromotion().catch(function (error) {
                    setAlert('danger', error.message);
                    ensureAtLeastOneImageRow();
                });
            });
        </script>
    @endpush
</x-hotel-management.form-page>
