<x-hotel-management.form-page
    :is-edit="false"
    :index-route="route('hotel.room-amenities.index')"
>
    <style>
        .hm-room-type-select-list {
            display: grid;
            gap: 0.85rem;
        }

        .hm-room-type-select-row {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .hm-room-type-select-row .hm-select-wrap {
            flex: 1 1 auto;
        }

        .hm-room-type-add-row {
            margin-bottom: 0.85rem;
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
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <label class="form-label mb-0" for="assign-room-type-select-0">Tên loại phòng</label>
            <button type="button" class="btn btn-sm btn-outline-primary hm-room-type-add-row" id="add-room-type-row-button">
                Thêm loại phòng
            </button>
        </div>
        <div id="assign-room-type-list" class="hm-room-type-select-list">
            <div class="hm-room-type-select-row" data-room-type-row>
                <div class="hm-select-wrap">
                    <select id="assign-room-type-select-0" class="form-select" data-room-type-select>
                        <option value="">Đang tải loại phòng...</option>
                    </select>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" data-remove-room-type-row hidden>
                    Bỏ
                </button>
            </div>
        </div>
        <div class="invalid-feedback d-block" id="assign-room-type-id-error"></div>
    </div>

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
                const addRowButton = document.getElementById('add-room-type-row-button');
                const roomTypeError = document.getElementById('assign-room-type-id-error');
                const submitButton = form ? form.querySelector('button[type="submit"]') : null;

                let assignedRoomTypeIds = [];
                let availableRoomTypes = [];
                let rowCounter = 1;

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
                    roomTypeList.querySelectorAll('[data-room-type-select]').forEach(function (select) {
                        select.classList.remove('is-invalid');
                    });
                };

                const buildOptionsHtml = function () {
                    return '<option value="">Chọn loại phòng</option>' + availableRoomTypes.map(function (roomType) {
                        const roomTypeId = roomType && roomType.MaLoaiPhong ? roomType.MaLoaiPhong : '';
                        const roomTypeName = roomType && roomType.TenLoaiPhong ? roomType.TenLoaiPhong : 'Loại phòng';

                        return `<option value="${escapeHtml(roomTypeId)}">${escapeHtml(roomTypeName)} (${escapeHtml(roomTypeId)})</option>`;
                    }).join('');
                };

                const refreshRemoveButtons = function () {
                    const rows = roomTypeList.querySelectorAll('[data-room-type-row]');

                    rows.forEach(function (row, index) {
                        const removeButton = row.querySelector('[data-remove-room-type-row]');

                        if (removeButton) {
                            removeButton.hidden = rows.length === 1;
                            removeButton.disabled = rows.length === 1;
                        }

                        const select = row.querySelector('[data-room-type-select]');
                        if (select && !select.id) {
                            select.id = `assign-room-type-select-${index}`;
                        }
                    });
                };

                const addRoomTypeRow = function (selectedValue) {
                    const row = document.createElement('div');
                    row.className = 'hm-room-type-select-row';
                    row.setAttribute('data-room-type-row', '');
                    row.innerHTML = `
                        <div class="hm-select-wrap">
                            <select id="assign-room-type-select-${rowCounter}" class="form-select" data-room-type-select>
                                ${buildOptionsHtml()}
                            </select>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-remove-room-type-row>
                            Bỏ
                        </button>
                    `;

                    rowCounter += 1;
                    roomTypeList.appendChild(row);

                    const select = row.querySelector('[data-room-type-select]');
                    if (select && selectedValue) {
                        select.value = selectedValue;
                    }

                    refreshRemoveButtons();
                };

                const populateRoomTypeRows = function () {
                    const rows = roomTypeList.querySelectorAll('[data-room-type-row]');

                    if (!availableRoomTypes.length) {
                        roomTypeList.innerHTML = `
                            <div class="hm-room-type-select-row" data-room-type-row>
                                <div class="hm-select-wrap">
                                    <select id="assign-room-type-select-0" class="form-select" data-room-type-select disabled>
                                        <option value="">Tiện nghi này đã có ở tất cả loại phòng</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-remove-room-type-row hidden disabled>
                                    Bỏ
                                </button>
                            </div>
                        `;

                        if (addRowButton) {
                            addRowButton.disabled = true;
                        }

                        if (submitButton) {
                            submitButton.disabled = true;
                        }

                        return;
                    }

                    rows.forEach(function (row) {
                        const select = row.querySelector('[data-room-type-select]');
                        if (select) {
                            const currentValue = select.value;
                            select.innerHTML = buildOptionsHtml();
                            if (currentValue) {
                                select.value = currentValue;
                            }
                        }
                    });

                    if (addRowButton) {
                        addRowButton.disabled = false;
                    }

                    if (submitButton) {
                        submitButton.disabled = false;
                    }

                    refreshRemoveButtons();
                };

                const getSelectedRoomTypeIds = function () {
                    return Array.from(roomTypeList.querySelectorAll('[data-room-type-select]'))
                        .map(function (select) { return String(select.value || '').trim(); })
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

                    populateRoomTypeRows();
                };

                form.addEventListener('submit', async function (event) {
                    event.preventDefault();
                    clearValidation();

                    const selectedRoomTypeIds = getSelectedRoomTypeIds();
                    const uniqueRoomTypeIds = Array.from(new Set(selectedRoomTypeIds));

                    if (!selectedRoomTypeIds.length) {
                        roomTypeList.querySelector('[data-room-type-select]')?.classList.add('is-invalid');
                        roomTypeError.textContent = 'Vui lòng chọn ít nhất một loại phòng.';
                        return;
                    }

                    if (selectedRoomTypeIds.length !== uniqueRoomTypeIds.length) {
                        roomTypeError.textContent = 'Không thể chọn trùng một loại phòng nhiều lần.';
                        roomTypeList.querySelectorAll('[data-room-type-select]').forEach(function (select) {
                            if (select.value && selectedRoomTypeIds.filter(function (value) { return value === select.value; }).length > 1) {
                                select.classList.add('is-invalid');
                            }
                        });
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

                addRowButton?.addEventListener('click', function () {
                    addRoomTypeRow('');
                });

                roomTypeList.addEventListener('click', function (event) {
                    const removeButton = event.target && event.target.closest
                        ? event.target.closest('[data-remove-room-type-row]')
                        : null;

                    if (!removeButton) {
                        return;
                    }

                    const row = removeButton.closest('[data-room-type-row]');

                    if (row) {
                        row.remove();
                        refreshRemoveButtons();
                    }
                });

                setSubmittingState(false);

                loadAmenity()
                    .then(loadRoomTypes)
                    .catch(function (error) {
                        amenityIdInput.value = '--';
                        amenityNameInput.value = '--';
                        roomTypeList.innerHTML = `
                            <div class="hm-room-type-select-row" data-room-type-row>
                                <div class="hm-select-wrap">
                                    <select id="assign-room-type-select-0" class="form-select" data-room-type-select disabled>
                                        <option value="">Không tải được loại phòng</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-remove-room-type-row hidden disabled>
                                    Bỏ
                                </button>
                            </div>
                        `;
                        roomTypeError.textContent = error.message;

                        if (addRowButton) {
                            addRowButton.disabled = true;
                        }

                        if (submitButton) {
                            submitButton.disabled = true;
                        }
                    });
            });
        </script>
    @endpush
</x-hotel-management.form-page>
