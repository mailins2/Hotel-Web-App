<x-hotel-management.index-page
    title="Quản lý phòng"
    subtitle="Danh sách quản lý phòng tại khách sạn"
    :create-route="route('hotel.rooms.create')"
>
    <x-slot:filters>
        <div class="col-md-3">
            <label class="form-label">Tìm kiếm</label>
            <input type="text" class="form-control" placeholder="Tìm theo số phòng, loại phòng" data-room-search>
        </div>
        <div class="col-md-3">
            <label class="form-label">Tình trạng</label>
            <div class="hm-select-wrap">
                <select class="form-select" data-room-status>
                    <option value="">Tất cả tình trạng</option>
                    <option value="0">Trống</option>
                    <option value="1">Đã đặt</option>
                    <option value="2">Đang sử dụng</option>
                    <option value="3">Đang dọn dẹp</option>
                </select>
            </div>
        </div>
    </x-slot:filters>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Mã phòng</th>
                <th>Số phòng</th>
                <th>Loại phòng</th>
                <th>Tình trạng</th>
                <th style="min-width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody id="room-table-body">
            <tr>
                <td colspan="5" class="text-center text-muted py-4">Đang tải dữ liệu phòng...</td>
            </tr>
        </tbody>
    </table>

    <div
        id="room-index-config"
        data-show-url-template="{{ route('hotel.rooms.show', ['recordId' => '__ROOM_ID__']) }}"
        data-edit-url-template="{{ route('hotel.rooms.edit', ['recordId' => '__ROOM_ID__']) }}"
        data-delete-url-template="{{ url('/api/phong/__ROOM_ID__') }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableBody = document.getElementById('room-table-body');
                const searchInput = document.querySelector('[data-room-search]');
                const statusSelect = document.querySelector('[data-room-status]');
                const filterPanel = document.querySelector('.hm-filter-panel');
                const config = document.getElementById('room-index-config');
                const applyButton = filterPanel ? filterPanel.querySelector('.btn.btn-primary') : null;
                const resetButton = filterPanel ? filterPanel.querySelector('.btn.btn-light') : null;
                const showUrlTemplate = config ? config.dataset.showUrlTemplate : '';
                const editUrlTemplate = config ? config.dataset.editUrlTemplate : '';
                const deleteUrlTemplate = config ? config.dataset.deleteUrlTemplate : '';

                let rooms = @json($rooms ?? []);

                const compareRecordIdDesc = function (left, right, fieldName) {
                    const leftValue = left && left[fieldName] !== undefined && left[fieldName] !== null ? String(left[fieldName]) : '';
                    const rightValue = right && right[fieldName] !== undefined && right[fieldName] !== null ? String(right[fieldName]) : '';
                    const leftNumber = Number(leftValue);
                    const rightNumber = Number(rightValue);

                    if (!Number.isNaN(leftNumber) && !Number.isNaN(rightNumber)) {
                        return rightNumber - leftNumber;
                    }

                    return rightValue.localeCompare(leftValue, undefined, { numeric: true, sensitivity: 'base' });
                };

                const mapStatus = function (status) {
                    switch (Number(status)) {
                        case 0:
                            return { label: 'Trống', badgeClass: 'success' };
                        case 1:
                            return { label: 'Đã đặt', badgeClass: 'warning' };
                        case 2:
                            return { label: 'Đang sử dụng', badgeClass: 'info' };
                        case 3:
                            return { label: 'Đang dọn dẹp', badgeClass: 'muted' };
                        default:
                            return { label: 'Không xác định', badgeClass: 'muted' };
                    }
                };

                const buildDeleteUrl = function (roomId) {
                    return String(deleteUrlTemplate || '').replace('__ROOM_ID__', encodeURIComponent(roomId || ''));
                };

                const renderRows = function (rows) {
                    if (!rows.length) {
                        tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Không có phòng phù hợp.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = rows.map(function (room) {
                        const status = mapStatus(room.TinhTrang);
                        const showUrl = showUrlTemplate.replace('__ROOM_ID__', room.MaPhong);
                        const editUrl = editUrlTemplate.replace('__ROOM_ID__', room.MaPhong);
                        const roomType = room && room.loai_phong ? room.loai_phong : null;

                        return `
                            <tr class="hm-clickable-row" data-hm-row-link="${showUrl}" tabindex="0">
                                <td>${room.MaPhong || '--'}</td>
                                <td>${room.SoPhong || '--'}</td>
                                <td>${roomType && roomType.TenLoaiPhong ? roomType.TenLoaiPhong : '--'}</td>
                                <td><span class="hm-badge hm-badge--${status.badgeClass}">${status.label}</span></td>
                                <td>
                                    <div class="hm-action-group">
                                        <a href="${editUrl}" class="btn btn-sm btn-warning btn-icon" title="Chỉnh sửa">
                                            <span class="btn-inner">
                                                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M13.7476 20H21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path d="M16.8392 3.41187C17.6212 2.62988 18.8891 2.62988 19.6711 3.41187L20.5881 4.32887C21.3701 5.11087 21.3701 6.37875 20.5881 7.16075L8.14912 19.5998C7.65512 20.0938 7.04312 20.4538 6.37112 20.6478L3 21L3.352 17.6289C3.546 16.9569 3.906 16.3448 4.4 15.8508L16.8392 3.41187Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                </svg>
                                            </span>
                                        </a>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-danger btn-icon"
                                            title="Xóa"
                                            data-delete-room-id="${room.MaPhong || ''}"
                                        >
                                            <span class="btn-inner">
                                                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M19 7L18.132 18.142C18.0578 19.0948 17.2636 19.8333 16.308 19.8333H7.692C6.73635 19.8333 5.9422 19.0948 5.868 18.142L5 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path d="M4 7H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                                                    <path d="M9 7V4.8C9 4.35817 9.35817 4 9.8 4H14.2C14.6418 4 15 4.35817 15 4.8V7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path d="M10 11V16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                                                    <path d="M14 11V16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                                                </svg>
                                            </span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('');
                };

                const pagination = typeof window.createHmPagination === 'function'
                    ? window.createHmPagination({
                        container: document.querySelector('[data-hm-pagination]'),
                        pageSize: 10,
                        onPageChange: renderRows
                    })
                    : null;

                const applyFilters = function () {
                    const keyword = ((searchInput ? searchInput.value : '') || '').trim().toLowerCase();
                    const statusValue = (statusSelect ? statusSelect.value : '') || '';

                    const filtered = rooms.filter(function (room) {
                        const roomType = room && room.loai_phong ? room.loai_phong : null;
                        const matchesKeyword = !keyword
                            || String(room && room.SoPhong ? room.SoPhong : '').toLowerCase().includes(keyword)
                            || String(roomType && roomType.TenLoaiPhong ? roomType.TenLoaiPhong : '').toLowerCase().includes(keyword);

                        const matchesStatus = statusValue === ''
                            || String(room && room.TinhTrang !== undefined ? room.TinhTrang : '') === statusValue;

                        return matchesKeyword && matchesStatus;
                    });

                    if (pagination) {
                        pagination.setItems(filtered);
                        return;
                    }

                    renderRows(filtered);
                };

                const loadRooms = function () {
                    rooms = (Array.isArray(rooms) ? rooms : []).slice().sort(function (left, right) {
                        return compareRecordIdDesc(left, right, 'MaPhong');
                    });
                    applyFilters();
                };

                if (applyButton) {
                    applyButton.remove();
                }

                if (filterPanel) {
                    const filterForm = filterPanel.querySelector('form');
                    if (filterForm) {
                        filterForm.addEventListener('submit', function (event) {
                            event.preventDefault();
                            applyFilters();
                        });
                    }
                }

                if (searchInput) {
                    searchInput.addEventListener('input', applyFilters);
                }

                if (statusSelect) {
                    statusSelect.addEventListener('change', applyFilters);
                }

                if (resetButton) {
                    resetButton.addEventListener('click', function () {
                        if (searchInput) {
                            searchInput.value = '';
                        }
                        if (statusSelect) {
                            statusSelect.value = '';
                        }
                        applyFilters();
                    });
                }

                document.addEventListener('click', async function (event) {
                    const deleteButton = event.target && event.target.closest
                        ? event.target.closest('[data-delete-room-id]')
                        : null;

                    if (!deleteButton) {
                        return;
                    }

                    event.preventDefault();
                    event.stopPropagation();

                    const roomId = deleteButton.getAttribute('data-delete-room-id') || '';

                    if (!roomId) {
                        return;
                    }

                    const confirmed = await window.hmConfirmDeletion({
                        title: 'Xóa phòng?',
                        message: 'Bạn muốn xóa phòng này?',
                        recordLabel: 'Mã phòng: ' + roomId,
                        note: 'Phòng đã có đặt phòng hoặc lưu trú sẽ không thể xóa.',
                    });

                    if (!confirmed) {
                        return;
                    }

                    const originalDisabledState = deleteButton.disabled;
                    deleteButton.disabled = true;

                    try {
                        const response = await fetch(buildDeleteUrl(roomId), {
                            method: 'DELETE',
                            headers: { Accept: 'application/json' }
                        });

                        const payload = await response.json().catch(function () {
                            return {};
                        });

                        if (!response.ok || payload.success === false) {
                            throw new Error(payload && payload.message ? payload.message : 'Không thể xóa phòng.');
                        }

                        rooms = rooms.filter(function (room) {
                            return String(room.MaPhong || '') !== String(roomId);
                        });
                        loadRooms();
                        window.hmShowToast({
                            type: 'success',
                            title: 'Đã xóa',
                            message: payload.message || 'Đã xóa phòng thành công.',
                        });
                    } catch (error) {
                        window.hmShowToast({
                            type: 'danger',
                            title: 'Không thể xóa',
                            message: error.message,
                        });
                    } finally {
                        deleteButton.disabled = originalDisabledState;
                    }
                });

                loadRooms();
            });
        </script>
    @endpush
</x-hotel-management.index-page>
