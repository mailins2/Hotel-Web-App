<x-hotel-management.form-page
    :is-edit="false"
    :index-route="route('hotel.room-amenities.index')"
>
    <style>
        .hm-room-type-checkbox-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.85rem;
            flex-wrap: wrap;
        }

        .hm-room-type-checkbox-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 0.85rem;
        }

        .hm-room-type-checkbox-item {
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            padding: 0.85rem 1rem;
            border: 1px solid #d7c1b2;
            border-radius: 0.85rem;
            background: #fff;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        .hm-room-type-checkbox-item:hover {
            border-color: #b76433;
            box-shadow: 0 0 0 0.2rem rgba(183, 100, 51, 0.12);
        }

        .hm-room-type-checkbox-item input[type="checkbox"] {
            margin-top: 0.25rem;
            flex: 0 0 auto;
        }

        .hm-room-type-checkbox-item .form-check-input:checked {
            background-color: #9b3d0f;
            border-color: #9b3d0f;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M4 10l4 4 8-8'/%3e%3c/svg%3e");
        }

        .hm-room-type-checkbox-item .form-check-input:focus {
            border-color: #b76433;
            box-shadow: 0 0 0 0.2rem rgba(183, 100, 51, 0.16);
        }

        .hm-room-type-checkbox-item label {
            display: grid;
            gap: 0.15rem;
            margin: 0;
            cursor: pointer;
            color: #2f1b12;
            font-weight: 600;
        }

        .hm-room-type-checkbox-item.is-selected {
            border-color: #9b3d0f;
            background: #fff6ef;
            box-shadow: 0 0 0 0.2rem rgba(155, 61, 15, 0.1);
        }

        .hm-room-type-checkbox-empty {
            padding: 0.95rem 1rem;
            border: 1px dashed #d7c1b2;
            border-radius: 0.85rem;
            background: #fff;
            color: #8a6c5b;
        }

        .hm-room-type-checkbox-list.is-invalid {
            padding: 0.65rem;
            border: 1px solid #dc3545;
            border-radius: 0.85rem;
            background: rgba(220, 53, 69, 0.03);
        }
    </style>

    <div class="col-md-6 mb-4">
        <label for="assign-room-amenity-id" class="form-label">Mã tiện nghi</label>
        <input
            type="text"
            id="assign-room-amenity-id"
            class="form-control hm-readonly-input"
            readonly
            value="Đang tải..."
        >
    </div>

    <div class="col-md-6 mb-4">
        <label for="assign-room-amenity-name" class="form-label">Tên tiện nghi</label>
        <input
            type="text"
            id="assign-room-amenity-name"
            class="form-control hm-readonly-input"
            readonly
            value="Đang tải..."
        >
    </div>

    <div class="col-12 mb-4">
        <div class="hm-room-type-checkbox-toolbar">
            <label class="form-label mb-0">Danh sách loại phòng</label>
            <button type="button" class="btn btn-sm btn-outline-primary" id="toggle-all-room-types-button">
                Chọn tất cả
            </button>
        </div>
        <div id="assign-room-type-list" class="hm-room-type-checkbox-list">
            <div class="hm-room-type-checkbox-empty">Đang tải loại phòng...</div>
        </div>
        <div class="mt-2 text-muted small" id="assign-room-type-selection-summary">
            Chưa chọn loại phòng nào.
        </div>
        <div class="invalid-feedback d-block" id="assign-room-type-id-error"></div>
    </div>

    <template id="assign-room-type-checkbox-template">
        <div class="hm-room-type-checkbox-item" data-room-type-item>
            <input type="checkbox" class="form-check-input" data-room-type-checkbox>
            <label data-room-type-label></label>
        </div>
    </template>

    <div
        id="room-amenity-assign-config"
        data-room-amenity-id="{{ request()->route('recordId') }}"
        data-index-url="{{ route('hotel.room-amenities.index') }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const config = document.getElementById('room-amenity-assign-config');
                const amenityId = config ? config.dataset.roomAmenityId : '';
                const indexUrl = config ? config.dataset.indexUrl : '';
                const form = document.querySelector('[data-ui-only-form]');
                const amenityIdInput = document.getElementById('assign-room-amenity-id');
                const amenityNameInput = document.getElementById('assign-room-amenity-name');
                const roomTypeList = document.getElementById('assign-room-type-list');
                const toggleAllButton = document.getElementById('toggle-all-room-types-button');
                const roomTypeError = document.getElementById('assign-room-type-id-error');
                const selectionSummary = document.getElementById('assign-room-type-selection-summary');
                const checkboxTemplate = document.getElementById('assign-room-type-checkbox-template');
                const submitButton = form ? form.querySelector('button[type="submit"]') : null;

                let assignedRoomTypeIds = [];
                let availableRoomTypes = [];

                const escapeHtml = function (value) {
                    return String(value || '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;');
                };

                const setSubmittingState = function (isSubmitting) {
                    if (!submitButton) {
                        return;
                    }

                    submitButton.disabled = isSubmitting;
                    submitButton.textContent = isSubmitting ? 'Đang thêm...' : 'Thêm tiện nghi vào loại phòng';
                };

                const clearValidation = function () {
                    roomTypeError.textContent = '';
                    roomTypeList.classList.remove('is-invalid');
                };

                const getRoomTypeLabel = function (roomType) {
                    const roomTypeId = roomType && roomType.MaLoaiPhong ? String(roomType.MaLoaiPhong).trim() : '';
                    const roomTypeName = roomType && roomType.TenLoaiPhong ? String(roomType.TenLoaiPhong).trim() : 'Loại phòng';

                    return `#${roomTypeId} - ${roomTypeName}`;
                };

                const getSelectedCheckboxes = function () {
                    return Array.from(roomTypeList.querySelectorAll('[data-room-type-checkbox]:checked'));
                };

                const setUnavailableState = function (message) {
                    roomTypeList.innerHTML = `<div class="hm-room-type-checkbox-empty">${escapeHtml(message)}</div>`;

                    if (selectionSummary) {
                        selectionSummary.textContent = message;
                    }

                    if (toggleAllButton) {
                        toggleAllButton.disabled = true;
                        toggleAllButton.textContent = 'Chọn tất cả';
                    }

                    if (submitButton) {
                        submitButton.disabled = true;
                    }
                };

                const syncSelectionState = function () {
                    const checkboxes = Array.from(roomTypeList.querySelectorAll('[data-room-type-checkbox]'));
                    const selectedCount = checkboxes.filter(function (checkbox) { return checkbox.checked; }).length;
                    const totalCount = checkboxes.length;

                    checkboxes.forEach(function (checkbox) {
                        const item = checkbox.closest('[data-room-type-item]');
                        if (item) {
                            item.classList.toggle('is-selected', checkbox.checked);
                        }
                    });

                    if (selectionSummary) {
                        selectionSummary.textContent = totalCount === 0
                            ? 'Không còn loại phòng nào để gán thêm tiện nghi này.'
                            : (selectedCount
                                ? `Đã chọn ${selectedCount}/${totalCount} loại phòng.`
                                : 'Chưa chọn loại phòng nào.');
                    }

                    if (toggleAllButton) {
                        toggleAllButton.disabled = totalCount === 0;
                        toggleAllButton.textContent = totalCount > 0 && selectedCount === totalCount
                            ? 'Bỏ chọn tất cả'
                            : 'Chọn tất cả';
                    }
                };

                const populateRoomTypeCheckboxes = function () {
                    roomTypeList.innerHTML = '';

                    if (!availableRoomTypes.length) {
                        setUnavailableState('Tiện nghi này đã có ở tất cả loại phòng.');
                        return;
                    }

                    availableRoomTypes.forEach(function (roomType, index) {
                        const fragment = checkboxTemplate.content.cloneNode(true);
                        const checkbox = fragment.querySelector('[data-room-type-checkbox]');
                        const label = fragment.querySelector('[data-room-type-label]');
                        const roomTypeId = roomType && roomType.MaLoaiPhong ? String(roomType.MaLoaiPhong).trim() : '';
                        const checkboxId = `assign-room-type-checkbox-${index}`;

                        if (checkbox) {
                            checkbox.id = checkboxId;
                            checkbox.value = roomTypeId;
                        }

                        if (label) {
                            label.setAttribute('for', checkboxId);
                            label.textContent = getRoomTypeLabel(roomType);
                        }

                        roomTypeList.appendChild(fragment);
                    });

                    if (submitButton) {
                        submitButton.disabled = false;
                    }

                    syncSelectionState();
                };

                const getSelectedRoomTypeIds = function () {
                    return getSelectedCheckboxes()
                        .map(function (checkbox) { return String(checkbox.value || '').trim(); })
                        .filter(Boolean);
                };

                const loadAmenity = async function () {
                    const response = await fetch(`/api/tien-nghi/${encodeURIComponent(amenityId)}`, {
                        headers: { Accept: 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải thông tin tiện nghi.');
                    }

                    const payload = await response.json();
                    const amenity = payload && payload.data ? payload.data : null;
                    const roomTypes = Array.isArray(amenity && amenity.loaiPhongs)
                        ? amenity.loaiPhongs
                        : (Array.isArray(amenity && amenity.loai_phongs) ? amenity.loai_phongs : []);

                    assignedRoomTypeIds = roomTypes
                        .map(function (roomType) {
                            return roomType && roomType.MaLoaiPhong ? String(roomType.MaLoaiPhong) : '';
                        })
                        .filter(Boolean);

                    amenityIdInput.value = amenity && amenity.MaTienNghi ? amenity.MaTienNghi : '--';
                    amenityNameInput.value = amenity && amenity.TenTienNghi ? amenity.TenTienNghi : '--';
                };

                const loadRoomTypes = async function () {
                    const response = await fetch('/api/loai-phong', {
                        headers: { Accept: 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải danh sách loại phòng.');
                    }

                    const payload = await response.json();
                    const roomTypes = Array.isArray(payload.data) ? payload.data : [];

                    availableRoomTypes = roomTypes.filter(function (roomType) {
                        return !assignedRoomTypeIds.includes(String(roomType && roomType.MaLoaiPhong ? roomType.MaLoaiPhong : ''));
                    });

                    populateRoomTypeCheckboxes();
                };

                form?.addEventListener('submit', async function (event) {
                    event.preventDefault();
                    clearValidation();

                    const selectedRoomTypeIds = getSelectedRoomTypeIds();
                    const uniqueRoomTypeIds = Array.from(new Set(selectedRoomTypeIds));

                    if (!selectedRoomTypeIds.length) {
                        roomTypeList.classList.add('is-invalid');
                        roomTypeError.textContent = 'Vui lòng chọn ít nhất một loại phòng.';
                        return;
                    }

                    setSubmittingState(true);

                    try {
                        for (const roomTypeId of uniqueRoomTypeIds) {
                            const response = await fetch(`/api/loai-phong/${encodeURIComponent(roomTypeId)}/tien-nghi/${encodeURIComponent(amenityId)}`, {
                                method: 'POST',
                                headers: { Accept: 'application/json' }
                            });

                            const payload = await response.json().catch(function () {
                                return {};
                            });

                            if (response.status === 409) {
                                throw new Error(payload && payload.message ? payload.message : 'Một trong các loại phòng đã có tiện nghi này.');
                            }

                            if (!response.ok || payload.success === false) {
                                throw new Error(payload && payload.message ? payload.message : 'Không thể thêm tiện nghi vào loại phòng.');
                            }
                        }

                        window.location.href = indexUrl;
                    } catch (error) {
                        roomTypeError.textContent = error.message;
                    } finally {
                        setSubmittingState(false);
                    }
                });

                toggleAllButton?.addEventListener('click', function () {
                    const checkboxes = Array.from(roomTypeList.querySelectorAll('[data-room-type-checkbox]'));
                    const shouldSelectAll = checkboxes.some(function (checkbox) { return !checkbox.checked; });

                    checkboxes.forEach(function (checkbox) {
                        checkbox.checked = shouldSelectAll;
                    });

                    clearValidation();
                    syncSelectionState();
                });

                roomTypeList.addEventListener('change', function (event) {
                    if (event.target instanceof HTMLInputElement && event.target.matches('[data-room-type-checkbox]')) {
                        clearValidation();
                        syncSelectionState();
                    }
                });

                setSubmittingState(false);

                loadAmenity()
                    .then(loadRoomTypes)
                    .catch(function (error) {
                        amenityIdInput.value = '--';
                        amenityNameInput.value = '--';
                        setUnavailableState('Không tải được danh sách loại phòng.');
                        roomTypeError.textContent = error.message;
                    });
            });
        </script>
    @endpush
</x-hotel-management.form-page>
