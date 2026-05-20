<x-hotel-management.form-page
    :is-edit="request()->routeIs('hotel.room-amenities.edit')"
    :index-route="route('hotel.room-amenities.index')"
>
    @if (request()->routeIs('hotel.room-amenities.edit'))
        <div class="col-md-6 mb-4">
            <label for="room-amenity-id" class="form-label">Mã tiện nghi</label>
            <input
                type="text"
                id="room-amenity-id"
                class="form-control hm-readonly-input"
                readonly
                disabled
                value="Đang tải..."
            >
        </div>
    @endif

    <div class="col-md-6 mb-4">
        <label for="room-amenity-name" class="form-label">Tên tiện nghi</label>
        <input
            type="text"
            id="room-amenity-name"
            class="form-control"
            maxlength="100"
            placeholder="Nhập tên tiện nghi"
        >
        <div class="invalid-feedback d-block" id="room-amenity-name-error"></div>
    </div>

    <div
        id="room-amenity-form-config"
        data-room-amenity-id="{{ request()->route('recordId') }}"
        data-is-edit="{{ request()->routeIs('hotel.room-amenities.edit') ? 'true' : 'false' }}"
        data-update-url-template="/api/tien-nghi/__ROOM_AMENITY_ID__"
        data-store-url="/api/tien-nghi"
        data-index-url="{{ route('hotel.room-amenities.index') }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const config = document.getElementById('room-amenity-form-config');
                const currentAmenityId = config ? config.dataset.roomAmenityId : '';
                const isEdit = config ? config.dataset.isEdit === 'true' : false;
                const updateUrlTemplate = config ? config.dataset.updateUrlTemplate : '';
                const storeUrl = config ? config.dataset.storeUrl : '';
                const indexUrl = config ? config.dataset.indexUrl : '';
                const form = document.querySelector('[data-ui-only-form]');
                const idInput = document.getElementById('room-amenity-id');
                const nameInput = document.getElementById('room-amenity-name');
                const nameError = document.getElementById('room-amenity-name-error');
                const submitButton = form ? form.querySelector('button[type="submit"]') : null;

                const setSubmittingState = function (isSubmitting) {
                    if (!submitButton) {
                        return;
                    }

                    submitButton.disabled = isSubmitting;
                    submitButton.textContent = isSubmitting
                        ? (isEdit ? 'Đang lưu...' : 'Đang tạo...')
                        : (isEdit ? 'Lưu thay đổi' : 'Tạo mới');
                };

                const clearValidation = function () {
                    nameInput.classList.remove('is-invalid');
                    nameError.textContent = '';
                };

                const loadAmenity = async function () {
                    if (!isEdit) {
                        return;
                    }

                    const response = await fetch(`/api/tien-nghi/${encodeURIComponent(currentAmenityId)}`, {
                        headers: { Accept: 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải thông tin tiện nghi.');
                    }

                    const payload = await response.json();
                    const amenity = payload && payload.data ? payload.data : null;

                    if (idInput) {
                        idInput.value = amenity && amenity.MaTienNghi ? amenity.MaTienNghi : '--';
                    }

                    nameInput.value = amenity && amenity.TenTienNghi ? amenity.TenTienNghi : '';
                };

                form.addEventListener('submit', async function (event) {
                    event.preventDefault();
                    clearValidation();

                    const amenityName = String(nameInput.value || '').trim();

                    if (amenityName.length === 0) {
                        nameInput.classList.add('is-invalid');
                        nameError.textContent = 'Vui lòng nhập tên tiện nghi.';
                        return;
                    }

                    setSubmittingState(true);

                    try {
                        const targetUrl = isEdit
                            ? updateUrlTemplate.replace('__ROOM_AMENITY_ID__', encodeURIComponent(currentAmenityId))
                            : storeUrl;
                        const response = await fetch(targetUrl, {
                            method: isEdit ? 'PUT' : 'POST',
                            headers: {
                                Accept: 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                TenTienNghi: amenityName
                            })
                        });

                        const payload = await response.json().catch(function () {
                            return {};
                        });

                        if (!response.ok || payload.success === false) {
                            throw new Error(payload && payload.message ? payload.message : 'Không thể lưu tiện nghi.');
                        }

                        window.location.href = indexUrl;
                    } catch (error) {
                        nameInput.classList.add('is-invalid');
                        nameError.textContent = error.message;
                    } finally {
                        setSubmittingState(false);
                    }
                });

                loadAmenity().catch(function (error) {
                    if (idInput) {
                        idInput.value = '--';
                    }

                    nameInput.classList.add('is-invalid');
                    nameError.textContent = error.message;
                });
            });
        </script>
    @endpush
</x-hotel-management.form-page>
