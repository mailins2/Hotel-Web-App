<x-hotel-management.index-page
    title="Quản lý tiện nghi phòng"
    subtitle="Danh sách tiện nghi cho từng loại phòng"
    :show-create-button="false"
>
    <x-slot:filters>
        <div class="col-md-4">
            <label class="form-label">Tên tiện nghi</label>
            <div class="hm-select-wrap">
                <input
                    type="text"
                    class="form-control"
                    placeholder="Tìm theo tên tiện nghi"
                    data-room-amenity-search
                >
            </div>
        </div>

        <div class="col-md-3">
            <label class="form-label">Mã loại phòng</label>
            <div class="hm-select-wrap">
                <select class="form-select" data-room-amenity-type-filter>
                    <option value="">Tất cả</option>
                </select>
            </div>
        </div>
    </x-slot:filters>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Mã tiện nghi</th>
                <th>Mã loại phòng</th>
                <th>Tên tiện nghi</th>
                <th style="min-width: 220px;">Thao tác</th>
            </tr>
        </thead>
        <tbody id="room-amenity-table-body">
            <tr>
                <td colspan="4" class="text-center text-muted py-4">Đang tải danh sách tiện nghi phòng...</td>
            </tr>
        </tbody>
    </table>

    <div
        id="room-amenity-index-config"
        data-show-url-template="{{ route('hotel.room-amenities.show', ['recordId' => '__ROOM_AMENITY_ID__']) }}"
        data-edit-url-template="{{ route('hotel.room-amenities.edit', ['recordId' => '__ROOM_AMENITY_ID__']) }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableBody = document.getElementById('room-amenity-table-body');
                const searchInput = document.querySelector('[data-room-amenity-search]');
                const roomTypeSelect = document.querySelector('[data-room-amenity-type-filter]');
                const filterPanel = document.querySelector('.hm-filter-panel');
                const applyButton = filterPanel ? filterPanel.querySelector('.btn.btn-primary') : null;
                const resetButton = filterPanel ? filterPanel.querySelector('.btn.btn-light') : null;
                const config = document.getElementById('room-amenity-index-config');
                const showUrlTemplate = config ? config.dataset.showUrlTemplate : '';
                const editUrlTemplate = config ? config.dataset.editUrlTemplate : '';

                let amenityRows = [];

                const escapeHtml = function (value) {
                    return String(value || '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;');
                };

                const compareTextAsc = function (left, right) {
                    return String(left || '').localeCompare(String(right || ''), undefined, {
                        numeric: true,
                        sensitivity: 'base'
                    });
                };

                const compareRows = function (left, right) {
                    const amenityCompare = compareTextAsc(left.MaTienNghi, right.MaTienNghi);

                    if (amenityCompare !== 0) {
                        return amenityCompare;
                    }

                    return compareTextAsc(left.MaLoaiPhong, right.MaLoaiPhong);
                };

                const renderRows = function (rows) {
                    if (!rows.length) {
                        tableBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">Không có tiện nghi phòng phù hợp.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = rows.map(function (row) {
                        const amenityId = row.MaTienNghi || '';
                        const showUrl = showUrlTemplate.replace('__ROOM_AMENITY_ID__', amenityId);
                        const editUrl = editUrlTemplate.replace('__ROOM_AMENITY_ID__', amenityId);

                        return `
                            <tr class="hm-clickable-row" data-hm-row-link="${escapeHtml(showUrl)}" tabindex="0">
                                <td>${escapeHtml(row.MaTienNghi || '--')}</td>
                                <td>${escapeHtml(row.MaLoaiPhong || '--')}</td>
                                <td>${escapeHtml(row.TenTienNghi || '--')}</td>
                                <td>
                                    <div class="hm-action-group">
                                        <a href="${escapeHtml(editUrl)}" class="btn btn-sm btn-warning btn-icon" title="Chỉnh sửa">
                                            <span class="btn-inner">
                                                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M13.7476 20H21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path d="M16.8392 3.41187C17.6212 2.62988 18.8891 2.62988 19.6711 3.41187L20.5881 4.32887C21.3701 5.11087 21.3701 6.37875 20.5881 7.16075L8.14912 19.5998C7.65512 20.0938 7.04312 20.4538 6.37112 20.6478L3 21L3.352 17.6289C3.546 16.9569 3.906 16.3448 4.4 15.8508L16.8392 3.41187Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                </svg>
                                            </span>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger btn-icon" title="Xóa" data-delete-room-amenity-id="${escapeHtml(amenityId)}">
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

                const populateRoomTypeOptions = function () {
                    const roomTypeCodes = amenityRows
                        .map(function (item) { return item.MaLoaiPhong; })
                        .filter(function (value) { return value && value !== '--'; });

                    const uniqueRoomTypeCodes = Array.from(new Set(roomTypeCodes))
                        .sort(compareTextAsc);

                    roomTypeSelect.innerHTML = '<option value="">Tất cả</option>' + uniqueRoomTypeCodes.map(function (code) {
                        return `<option value="${escapeHtml(code)}">${escapeHtml(code)}</option>`;
                    }).join('');
                };

                const applyFilters = function () {
                    const keyword = String(searchInput ? searchInput.value : '').trim().toLowerCase();
                    const roomTypeCode = String(roomTypeSelect ? roomTypeSelect.value : '').trim();

                    const filteredRows = amenityRows.filter(function (row) {
                        const matchesKeyword = keyword === ''
                            || String(row.TenTienNghi || '').toLowerCase().includes(keyword)
                            || String(row.MaTienNghi || '').toLowerCase().includes(keyword);
                        const matchesRoomType = roomTypeCode === '' || String(row.MaLoaiPhong || '') === roomTypeCode;

                        return matchesKeyword && matchesRoomType;
                    });

                    if (pagination) {
                        pagination.setItems(filteredRows);
                        return;
                    }

                    renderRows(filteredRows);
                };

                const normalizeAmenityRows = function (amenity) {
                    const roomTypes = Array.isArray(amenity.loai_phongs)
                        ? amenity.loai_phongs
                        : (Array.isArray(amenity.loaiPhongs) ? amenity.loaiPhongs : []);

                    if (!roomTypes.length) {
                        return [{
                            MaTienNghi: amenity.MaTienNghi || '--',
                            MaLoaiPhong: '--',
                            TenTienNghi: amenity.TenTienNghi || '--'
                        }];
                    }

                    return roomTypes.map(function (roomType) {
                        return {
                            MaTienNghi: amenity.MaTienNghi || '--',
                            MaLoaiPhong: roomType && roomType.MaLoaiPhong ? roomType.MaLoaiPhong : '--',
                            TenTienNghi: amenity.TenTienNghi || '--'
                        };
                    });
                };

                const loadRoomAmenities = async function () {
                    try {
                        const response = await fetch('/api/tien-nghi', {
                            headers: { 'Accept': 'application/json' }
                        });

                        if (!response.ok) {
                            throw new Error('Không thể tải danh sách tiện nghi phòng.');
                        }

                        const payload = await response.json();
                        const amenities = Array.isArray(payload.data) ? payload.data : [];

                        const amenityDetails = await Promise.all(amenities.map(async function (amenity) {
                            const amenityId = amenity && amenity.MaTienNghi ? amenity.MaTienNghi : null;

                            if (!amenityId) {
                                return amenity;
                            }

                            try {
                                const detailResponse = await fetch(`/api/tien-nghi/${encodeURIComponent(amenityId)}`, {
                                    headers: { 'Accept': 'application/json' }
                                });

                                if (!detailResponse.ok) {
                                    return amenity;
                                }

                                const detailPayload = await detailResponse.json();
                                return detailPayload && detailPayload.data ? detailPayload.data : amenity;
                            } catch (error) {
                                return amenity;
                            }
                        }));

                        amenityRows = amenityDetails
                            .flatMap(normalizeAmenityRows)
                            .sort(compareRows);

                        populateRoomTypeOptions();
                        applyFilters();
                    } catch (error) {
                        tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger py-4">${escapeHtml(error.message)}</td></tr>`;
                    }
                };

                if (applyButton) {
                    applyButton.addEventListener('click', applyFilters);
                }

                if (resetButton) {
                    resetButton.addEventListener('click', function () {
                        if (searchInput) {
                            searchInput.value = '';
                        }

                        if (roomTypeSelect) {
                            roomTypeSelect.value = '';
                        }

                        applyFilters();
                    });
                }

                if (searchInput) {
                    searchInput.addEventListener('keydown', function (event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            applyFilters();
                        }
                    });
                }

                document.addEventListener('click', async function (event) {
                    const deleteButton = event.target && event.target.closest
                        ? event.target.closest('[data-delete-room-amenity-id]')
                        : null;

                    if (!deleteButton) {
                        return;
                    }

                    event.preventDefault();

                    const amenityId = deleteButton.getAttribute('data-delete-room-amenity-id') || '';

                    if (!amenityId) {
                        return;
                    }

                    if (!window.confirm(`Xóa tiện nghi ${amenityId}? Thao tác này sẽ xóa luôn tiện nghi khỏi hệ thống.`)) {
                        return;
                    }

                    const originalDisabledState = deleteButton.disabled;
                    deleteButton.disabled = true;

                    try {
                        const response = await fetch(`/api/tien-nghi/${encodeURIComponent(amenityId)}`, {
                            method: 'DELETE',
                            headers: { 'Accept': 'application/json' }
                        });

                        const payload = await response.json().catch(function () {
                            return {};
                        });

                        if (!response.ok || payload.success === false) {
                            const message = payload && payload.message ? payload.message : 'Không thể xóa tiện nghi.';
                            throw new Error(message);
                        }

                        await loadRoomAmenities();
                    } catch (error) {
                        window.alert(error.message);
                    } finally {
                        deleteButton.disabled = originalDisabledState;
                    }
                });

                loadRoomAmenities();
            });
        </script>
    @endpush
</x-hotel-management.index-page>
