<x-hotel-management.index-page
    title="Quản lý tiện nghi phòng"
    subtitle="Danh sách tiện nghi trong hệ thống"
    :create-route="route('hotel.room-amenities.create')"
    :trash-route="route('hotel.room-amenities.trash')"
>
    <x-slot:filters>
        <div class="col-md-4">
            <label class="form-label">Tên tiện nghi</label>
            <div class="hm-select-wrap">
                <input
                    type="text"
                    class="form-control"
                    placeholder="Tìm theo mã hoặc tên tiện nghi"
                    data-room-amenity-search
                >
            </div>
        </div>
    </x-slot:filters>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Mã tiện nghi</th>
                <th>Tên tiện nghi</th>
                <th style="min-width: 140px;">Thao tác</th>
                <th style="min-width: 220px;">Thêm tiện nghi phòng</th>
            </tr>
        </thead>
        <tbody id="room-amenity-table-body">
            <tr>
                <td colspan="4" class="text-center text-muted py-4">Đang tải danh sách tiện nghi...</td>
            </tr>
        </tbody>
    </table>

    <div
        id="room-amenity-index-config"
        data-show-url-base="{{ route('hotel.room-amenities.show', ['recordId' => '__ROOM_AMENITY_ID__']) }}"
        data-edit-url-base="{{ route('hotel.room-amenities.edit', ['recordId' => '__ROOM_AMENITY_ID__']) }}"
        data-assign-url-base="{{ route('hotel.room-amenities.assign', ['recordId' => '__ROOM_AMENITY_ID__']) }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableBody = document.getElementById('room-amenity-table-body');
                const searchInput = document.querySelector('[data-room-amenity-search]');
                const filterPanel = document.querySelector('.hm-filter-panel');
                const applyButton = filterPanel ? filterPanel.querySelector('.btn.btn-primary') : null;
                const resetButton = filterPanel ? filterPanel.querySelector('.btn.btn-light') : null;
                const config = document.getElementById('room-amenity-index-config');
                const showUrlBase = config ? config.dataset.showUrlBase : '';
                const editUrlBase = config ? config.dataset.editUrlBase : '';
                const assignUrlBase = config ? config.dataset.assignUrlBase : '';

                let amenities = [];

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

                const compareTextDesc = function (left, right) {
                    return compareTextAsc(right, left);
                };

                const buildRowUrl = function (baseUrl, amenityId) {
                    return String(baseUrl || '').replace('__ROOM_AMENITY_ID__', encodeURIComponent(amenityId || ''));
                };

                const renderRows = function (rows) {
                    if (!rows.length) {
                        tableBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">Không có tiện nghi phù hợp.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = rows.map(function (amenity) {
                        const amenityId = amenity.MaTienNghi || '';
                        const showUrl = buildRowUrl(showUrlBase, amenityId);
                        const editUrl = buildRowUrl(editUrlBase, amenityId);
                        const assignUrl = buildRowUrl(assignUrlBase, amenityId);

                        return `
                            <tr class="hm-clickable-row" data-hm-row-link="${escapeHtml(showUrl)}" tabindex="0">
                                <td>${escapeHtml(amenityId || '--')}</td>
                                <td>${escapeHtml(amenity.TenTienNghi || '--')}</td>
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
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-danger btn-icon"
                                            title="Xóa"
                                            data-delete-room-amenity-id="${escapeHtml(amenityId)}"
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
                                <td>
                                    <a href="${escapeHtml(assignUrl)}" class="btn btn-sm btn-primary" title="Thêm tiện nghi phòng">
                                        Thêm tiện nghi phòng
                                    </a>
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
                    const keyword = String(searchInput ? searchInput.value : '').trim().toLowerCase();
                    const filteredRows = amenities.filter(function (amenity) {
                        return keyword === ''
                            || String(amenity.TenTienNghi || '').toLowerCase().includes(keyword)
                            || String(amenity.MaTienNghi || '').toLowerCase().includes(keyword);
                    });

                    if (pagination) {
                        pagination.setItems(filteredRows);
                        return;
                    }

                    renderRows(filteredRows);
                };

                const loadRoomAmenities = async function () {
                    try {
                        const response = await fetch('/api/tien-nghi', {
                            headers: { Accept: 'application/json' }
                        });

                        if (!response.ok) {
                            throw new Error('Không thể tải danh sách tiện nghi.');
                        }

                        const payload = await response.json();
                        const items = Array.isArray(payload.data) ? payload.data : [];

                        amenities = items.sort(function (left, right) {
                            return compareTextDesc(left.MaTienNghi, right.MaTienNghi);
                        });

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

                    if (!amenityId || !window.confirm(`Xóa tiện nghi ${amenityId} khỏi hệ thống?`)) {
                        return;
                    }

                    const originalDisabledState = deleteButton.disabled;
                    deleteButton.disabled = true;

                    try {
                        const response = await fetch(`/api/tien-nghi/${encodeURIComponent(amenityId)}`, {
                            method: 'DELETE',
                            headers: { Accept: 'application/json' }
                        });

                        const payload = await response.json().catch(function () {
                            return {};
                        });

                        if (!response.ok || payload.success === false) {
                            throw new Error(payload && payload.message ? payload.message : 'Không thể xóa tiện nghi.');
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
