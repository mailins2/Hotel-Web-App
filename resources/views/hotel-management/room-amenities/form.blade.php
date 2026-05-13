<x-hotel-management.form-page
    :is-edit="true"
    :index-route="route('hotel.room-amenities.index')"
>
    <div class="col-md-6 mb-4">
        <label for="room-amenity-id" class="form-label">Mã tiện nghi</label>
        <input type="text" id="room-amenity-id" class="form-control hm-readonly-input" readonly value="Đang tải...">
    </div>

    <div class="col-md-6 mb-4">
        <label for="room-amenity-name" class="form-label">Tên tiện nghi</label>
        <input type="text" id="room-amenity-name" class="form-control" maxlength="100" placeholder="Nhập tên tiện nghi">
        <div class="invalid-feedback" id="room-amenity-name-error"></div>
    </div>

    <div class="col-md-12 mb-4">
        <div class="border rounded p-3 h-100 bg-light">
            <div class="text-muted small mb-2">Các loại phòng đang gắn</div>
            <div id="room-amenity-linked-room-types" class="fw-semibold">Đang tải...</div>
        </div>
    </div>

    <div id="room-amenity-form-config" data-room-amenity-id="{{ request()->route('recordId') }}" hidden></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const config = document.getElementById('room-amenity-form-config');
                const amenityId = config ? config.dataset.roomAmenityId : '';
                const form = document.querySelector('[data-ui-only-form]');
                const idInput = document.getElementById('room-amenity-id');
                const nameInput = document.getElementById('room-amenity-name');
                const nameError = document.getElementById('room-amenity-name-error');
                const linkedRoomTypes = document.getElementById('room-amenity-linked-room-types');
                const submitButton = form ? form.querySelector('button[type="submit"]') : null;

                const setSubmittingState = function (isSubmitting) {
                    if (!submitButton) {
                        return;
                    }

                    submitButton.disabled = isSubmitting;
                    submitButton.textContent = isSubmitting ? 'Đang lưu...' : 'Lưu thay đổi';
                };

                const clearValidation = function () {
                    nameInput.classList.remove('is-invalid');
                    nameError.textContent = '';
                };

                const renderRoomTypes = function (roomTypes) {
                    if (!Array.isArray(roomTypes) || !roomTypes.length) {
                        linkedRoomTypes.textContent = 'Chưa có loại phòng nào được gán.';
                        return;
                    }

                    linkedRoomTypes.textContent = roomTypes.map(function (roomType) {
                        const code = roomType && roomType.MaLoaiPhong ? roomType.MaLoaiPhong : '--';
                        const name = roomType && roomType.TenLoaiPhong ? roomType.TenLoaiPhong : 'Loại phòng';
                        return `${code} - ${name}`;
                    }).join(', ');
                };

                const loadAmenity = async function () {
                    const response = await fetch(`/api/tien-nghi/${encodeURIComponent(amenityId)}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải thông tin tiện nghi phòng.');
                    }

                    const payload = await response.json();
                    const amenity = payload && payload.data ? payload.data : null;
                    const roomTypes = Array.isArray(amenity && amenity.loai_phongs)
                        ? amenity.loai_phongs
                        : (Array.isArray(amenity && amenity.loaiPhongs) ? amenity.loaiPhongs : []);

                    idInput.value = amenity && amenity.MaTienNghi ? amenity.MaTienNghi : '--';
                    nameInput.value = amenity && amenity.TenTienNghi ? amenity.TenTienNghi : '';
                    renderRoomTypes(roomTypes);
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
                        const response = await fetch(`/api/tien-nghi/${encodeURIComponent(amenityId)}`, {
                            method: 'PUT',
                            headers: {
                                'Accept': 'application/json',
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
                            const message = payload && payload.message ? payload.message : 'Không thể cập nhật tiện nghi.';
                            throw new Error(message);
                        }

                        window.location.href = `{{ route('hotel.room-amenities.show', ['recordId' => '__ROOM_AMENITY_ID__']) }}`.replace('__ROOM_AMENITY_ID__', encodeURIComponent(amenityId));
                    } catch (error) {
                        nameInput.classList.add('is-invalid');
                        nameError.textContent = error.message;
                    } finally {
                        setSubmittingState(false);
                    }
                });

                loadAmenity().catch(function (error) {
                    idInput.value = '--';
                    nameInput.value = '';
                    linkedRoomTypes.textContent = '--';
                    nameInput.classList.add('is-invalid');
                    nameError.textContent = error.message;
                });
            });
        </script>
    @endpush
</x-hotel-management.form-page>
