<x-hotel-management.form-page
    :is-edit="request()->routeIs('hotel.rooms.edit')"
    :index-route="route('hotel.rooms.index')"
>
    <div class="col-12">
        <div id="room-form-alert" class="alert d-none mb-4" role="alert"></div>
    </div>

    <div class="form-group col-md-6" id="room-id-group">
        <label class="form-label">Mã phòng</label>
        <input
            type="text"
            class="form-control hm-readonly-input"
            id="room-id"
            value="--"
            readonly
        >
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Số phòng</label>
        <input
            type="text"
            class="form-control"
            id="room-number"
            placeholder="Nhập số phòng"
        >
        <div class="invalid-feedback" id="room-number-error"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Loại phòng</label>
        <select class="form-select" id="room-type-id">
            <option value="">Chọn loại phòng</option>
        </select>
        <div class="invalid-feedback" id="room-type-id-error"></div>
    </div>

    <div class="form-group col-md-6">
        <label class="form-label">Tình trạng</label>
        <select class="form-select" id="room-status">
            <option value="0">Trống</option>
            <option value="1">Đã đặt</option>
            <option value="2">Đang sử dụng</option>
            <option value="3">Đang dọn dẹp</option>
        </select>
        <div class="invalid-feedback" id="room-status-error"></div>
    </div>

    <div
        id="room-form-config"
        data-is-edit="{{ request()->routeIs('hotel.rooms.edit') ? '1' : '0' }}"
        data-room-id="{{ request()->route('recordId') }}"
        data-create-url="{{ url('/api/phong') }}"
        data-detail-url-template="{{ url('/api/phong/__ROOM_ID__') }}"
        data-room-types-url="{{ url('/api/loai-phong') }}"
        data-index-url="{{ route('hotel.rooms.index') }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const config = document.getElementById('room-form-config');
                const form = document.querySelector('[data-ui-only-form]');
                const submitButton = form ? form.querySelector('button[type="submit"]') : null;
                const isEdit = !!(config && config.dataset.isEdit === '1');
                const roomId = config ? (config.dataset.roomId || '') : '';
                const createUrl = config ? config.dataset.createUrl : '';
                const detailUrlTemplate = config ? config.dataset.detailUrlTemplate : '';
                const roomTypesUrl = config ? config.dataset.roomTypesUrl : '';
                const indexUrl = config ? config.dataset.indexUrl : '';

                const alertBox = document.getElementById('room-form-alert');
                const roomIdGroup = document.getElementById('room-id-group');
                const roomIdInput = document.getElementById('room-id');
                const roomNumberInput = document.getElementById('room-number');
                const roomTypeIdInput = document.getElementById('room-type-id');
                const roomStatusInput = document.getElementById('room-status');

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
                        ['room-number', roomNumberInput],
                        ['room-type-id', roomTypeIdInput],
                        ['room-status', roomStatusInput]
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
                        SoPhong: 'room-number',
                        MaLoaiPhong: 'room-type-id',
                        TinhTrang: 'room-status'
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

                    if (!roomNumberInput.value.trim()) {
                        setFieldError('SoPhong', 'Vui lòng nhập số phòng.');
                        isValid = false;
                    }

                    if (!roomTypeIdInput.value) {
                        setFieldError('MaLoaiPhong', 'Vui lòng chọn loại phòng.');
                        isValid = false;
                    }

                    if (roomStatusInput.value === '') {
                        setFieldError('TinhTrang', 'Vui lòng chọn tình trạng.');
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

                const populateRoomTypeOptions = function (roomTypes) {
                    roomTypeIdInput.innerHTML = '<option value="">Chọn loại phòng</option>' + roomTypes.map(function (roomType) {
                        const roomTypeId = roomType && roomType.MaLoaiPhong ? roomType.MaLoaiPhong : '';
                        const roomTypeName = roomType && roomType.TenLoaiPhong ? roomType.TenLoaiPhong : '--';
                        return `<option value="${roomTypeId}">${roomTypeId} - ${roomTypeName}</option>`;
                    }).join('');
                };

                const populateForm = function (room) {
                    const roomData = room && room.data ? room.data : room;
                    roomIdInput.value = roomData && roomData.MaPhong ? roomData.MaPhong : '--';
                    roomNumberInput.value = roomData && roomData.SoPhong ? roomData.SoPhong : '';
                    roomTypeIdInput.value = roomData && roomData.MaLoaiPhong !== undefined && roomData.MaLoaiPhong !== null
                        ? String(roomData.MaLoaiPhong)
                        : '';
                    roomStatusInput.value = roomData && roomData.TinhTrang !== undefined && roomData.TinhTrang !== null
                        ? String(roomData.TinhTrang)
                        : '0';
                };

                const loadRoomTypes = async function () {
                    const response = await fetch(roomTypesUrl, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải danh sách loại phòng.');
                    }

                    const payload = await response.json();
                    const roomTypes = Array.isArray(payload && payload.data) ? payload.data : [];
                    populateRoomTypeOptions(roomTypes);
                };

                const loadRoom = async function () {
                    if (!isEdit || !roomId) {
                        return;
                    }

                    const response = await fetch(detailUrlTemplate.replace('__ROOM_ID__', roomId), {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải thông tin phòng.');
                    }

                    const room = await response.json();
                    populateForm(room);
                };

                if (roomIdGroup) {
                    roomIdGroup.style.display = isEdit ? '' : 'none';
                }

                if (form) {
                    form.addEventListener('submit', async function (event) {
                        event.preventDefault();

                        if (!validateForm()) {
                            return;
                        }

                        const payload = {
                            SoPhong: roomNumberInput.value.trim(),
                            MaLoaiPhong: roomTypeIdInput.value,
                            TinhTrang: Number(roomStatusInput.value)
                        };

                        const requestUrl = isEdit
                            ? detailUrlTemplate.replace('__ROOM_ID__', roomId)
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
                                throw new Error(responseData.message || 'Không thể lưu phòng.');
                            }

                            setAlert('success', isEdit ? 'Cập nhật phòng thành công.' : 'Tạo phòng thành công.');

                            const savedRoom = responseData && responseData.data ? responseData.data : null;
                            if (!isEdit && savedRoom && savedRoom.MaPhong) {
                                roomIdInput.value = savedRoom.MaPhong;
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
                        await loadRoomTypes();
                        await loadRoom();
                    } catch (error) {
                        setAlert('danger', error.message);
                    }
                })();
            });
        </script>
    @endpush
</x-hotel-management.form-page>
