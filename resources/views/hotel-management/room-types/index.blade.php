<x-hotel-management.index-page
    title="Quản lý loại phòng"
    subtitle="Danh sách quản lý các loại phòng tại khách sạn"
    :create-route="route('hotel.room-types.create')"
    :trash-route="route('hotel.room-types.trash')"
>
    <style>
        .hm-room-type-table {
            table-layout: fixed;
            width: 100%;
        }

        .hm-room-type-table th:nth-child(3),
        .hm-room-type-table td:nth-child(3) {
            width: 42%;
        }

        .hm-truncate-cell {
            display: block;
            width: 100%;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>

    <x-slot:filters>
        <div class="col-md-3">
            <label class="form-label">Loại phòng</label>
            <div class="hm-select-wrap">
                <select class="form-select" data-room-type-filter>
                    <option value="">Tất cả</option>
                </select>
            </div>
        </div>
    </x-slot:filters>

    <table class="table table-striped align-middle hm-room-type-table">
        <thead>
            <tr>
                <th>Mã loại</th>
                <th>Tên loại phòng</th>
                <th>Mô tả</th>
                <th>Người lớn</th>
                <th>Trẻ em</th>
                <th style="min-width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody id="room-type-table-body">
            <tr>
                <td colspan="6" class="text-center text-muted py-4">Đang tải dữ liệu loại phòng...</td>
            </tr>
        </tbody>
    </table>

    <div
        id="room-type-index-config"
        data-show-url-template="{{ route('hotel.room-types.show', ['recordId' => '__ROOM_TYPE_ID__']) }}"
        data-edit-url-template="{{ route('hotel.room-types.edit', ['recordId' => '__ROOM_TYPE_ID__']) }}"
        data-delete-url-template="{{ url('/api/loai-phong/__ROOM_TYPE_ID__') }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableBody = document.getElementById('room-type-table-body');
                const typeSelect = document.querySelector('[data-room-type-filter]');
                const filterPanel = document.querySelector('.hm-filter-panel');
                const config = document.getElementById('room-type-index-config');
                const applyButton = filterPanel ? filterPanel.querySelector('.btn.btn-primary') : null;
                const resetButton = filterPanel ? filterPanel.querySelector('.btn.btn-light') : null;
                const showUrlTemplate = config ? config.dataset.showUrlTemplate : '';
                const editUrlTemplate = config ? config.dataset.editUrlTemplate : '';
                const deleteUrlTemplate = config ? config.dataset.deleteUrlTemplate : '';

                let roomTypes = @json($roomTypes ?? []);

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

                const escapeHtml = function (value) {
                    return String(value ?? '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;');
                };

                const buildDeleteUrl = function (roomTypeId) {
                    return String(deleteUrlTemplate || '').replace('__ROOM_TYPE_ID__', encodeURIComponent(roomTypeId || ''));
                };

                const renderRows = function (rows) {
                    if (!rows.length) {
                        tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Không có loại phòng phù hợp.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = rows.map(function (roomType) {
                        const showUrl = showUrlTemplate.replace('__ROOM_TYPE_ID__', roomType.MaLoaiPhong);
                        const editUrl = editUrlTemplate.replace('__ROOM_TYPE_ID__', roomType.MaLoaiPhong);
                        const childrenValue = roomType && roomType.TreEm !== undefined && roomType.TreEm !== null && String(roomType.TreEm).trim() !== ''
                            ? roomType.TreEm
                            : 0;

                        return `
                            <tr class="hm-clickable-row" data-hm-row-link="${showUrl}" tabindex="0">
                                <td>${escapeHtml(roomType.MaLoaiPhong || '--')}</td>
                                <td>${escapeHtml(roomType.TenLoaiPhong || '--')}</td>
                                <td>
                                    <div class="hm-truncate-cell" title="${escapeHtml(roomType.Mota || '--')}">
                                        ${escapeHtml(roomType.Mota || '--')}
                                    </div>
                                </td>
                                <td>${escapeHtml(roomType.NguoiLon || '--')}</td>
                                <td>${escapeHtml(childrenValue)}</td>
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
                                            data-delete-room-type-id="${escapeHtml(roomType.MaLoaiPhong)}"
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

                const populateFilterOptions = function () {
                    const options = roomTypes
                        .map(function (item) { return item.TenLoaiPhong; })
                        .filter(Boolean);

                    const uniqueOptions = Array.from(new Set(options));
                    typeSelect.innerHTML = '<option value="">Tất cả</option>' + uniqueOptions.map(function (name) {
                        return `<option value="${escapeHtml(name)}">${escapeHtml(name)}</option>`;
                    }).join('');
                };

                const applyFilters = function () {
                    const typeValue = (typeSelect ? typeSelect.value : '') || '';

                    const filtered = roomTypes.filter(function (roomType) {
                        return typeValue === '' || String(roomType.TenLoaiPhong || '') === typeValue;
                    });

                    if (pagination) {
                        pagination.setItems(filtered);
                        return;
                    }

                    renderRows(filtered);
                };

                const loadRoomTypes = function () {
                    roomTypes = (Array.isArray(roomTypes) ? roomTypes : []).slice().sort(function (left, right) {
                        return compareRecordIdDesc(left, right, 'MaLoaiPhong');
                    });
                    populateFilterOptions();
                    applyFilters();
                };

                if (applyButton) {
                    applyButton.addEventListener('click', applyFilters);
                }

                if (resetButton) {
                    resetButton.addEventListener('click', function () {
                        if (typeSelect) {
                            typeSelect.value = '';
                        }
                        applyFilters();
                    });
                }

                document.addEventListener('click', async function (event) {
                    const deleteButton = event.target && event.target.closest
                        ? event.target.closest('[data-delete-room-type-id]')
                        : null;

                    if (!deleteButton) {
                        return;
                    }

                    event.preventDefault();
                    event.stopPropagation();

                    const roomTypeId = deleteButton.getAttribute('data-delete-room-type-id') || '';

                    if (!roomTypeId || !window.confirm(`Xóa loại phòng ${roomTypeId}?`)) {
                        return;
                    }

                    const originalDisabledState = deleteButton.disabled;
                    deleteButton.disabled = true;

                    try {
                        const response = await fetch(buildDeleteUrl(roomTypeId), {
                            method: 'DELETE',
                            headers: { Accept: 'application/json' }
                        });

                        const payload = await response.json().catch(function () {
                            return {};
                        });

                        if (!response.ok || payload.success === false) {
                            throw new Error(payload && payload.message ? payload.message : 'Không thể xóa loại phòng.');
                        }

                        roomTypes = roomTypes.filter(function (roomType) {
                            return String(roomType.MaLoaiPhong || '') !== String(roomTypeId);
                        });
                        loadRoomTypes();
                    } catch (error) {
                        window.alert(error.message);
                    } finally {
                        deleteButton.disabled = originalDisabledState;
                    }
                });

                loadRoomTypes();
            });
        </script>
    @endpush
</x-hotel-management.index-page>
