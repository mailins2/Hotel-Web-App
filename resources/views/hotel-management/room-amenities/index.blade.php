<x-hotel-management.index-page
    title="Quản lý tiện nghi phòng"
    subtitle="Danh sách tiện nghi trong hệ thống"
    :create-route="route('hotel.room-amenities.create')"
>
    <style>
        .hm-assign-amenity-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            min-height: 38px;
            padding: 0.5rem 0.78rem;
            border: 1px solid rgba(111, 29, 1, 0.22);
            border-radius: 8px;
            background: #fff7ed;
            color: #6f1d01 !important;
            font-size: 0.82rem;
            font-weight: 500;
            line-height: 1.2;
            text-transform: none;
            white-space: nowrap;
            box-shadow: 0 8px 20px -18px rgba(111, 29, 1, 0.45);
            transition: background-color 0.18s ease, border-color 0.18s ease, color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
        }

        .hm-assign-amenity-button svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        .hm-assign-amenity-button:hover,
        .hm-assign-amenity-button:focus {
            border-color: #6f1d01;
            background: #6f1d01;
            color: #fff !important;
            box-shadow: 0 10px 24px -16px rgba(111, 29, 1, 0.72);
            transform: translateY(-1px);
        }

        .hm-assign-amenity-button:focus-visible {
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(111, 29, 1, 0.18);
        }
    </style>

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
            @forelse(($amenities ?? collect()) as $amenity)
                @php
                    $amenityId = $amenity->MaTienNghi;
                    $searchText = collect([
                        $amenity->MaTienNghi,
                        $amenity->TenTienNghi,
                    ])->filter()->implode(' ');
                @endphp
                <tr
                    class="hm-clickable-row"
                    data-room-amenity-row
                    data-search="{{ \Illuminate\Support\Str::lower($searchText) }}"
                    data-hm-row-link="{{ route('hotel.room-amenities.show', ['recordId' => $amenityId]) }}"
                    tabindex="0"
                >
                    <td>{{ $amenityId }}</td>
                    <td>{{ $amenity->TenTienNghi ?? '--' }}</td>
                    <td>
                        <div class="hm-action-group">
                            <a href="{{ route('hotel.room-amenities.edit', ['recordId' => $amenityId]) }}" class="btn btn-sm btn-warning btn-icon" title="Chỉnh sửa">
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
                                data-delete-room-amenity-id="{{ $amenityId }}"
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
                        <a href="{{ route('hotel.room-amenities.assign', ['recordId' => $amenityId]) }}" class="hm-assign-amenity-button" title="Thêm tiện nghi phòng">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 5V19" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                            </svg>
                            <span>Thêm tiện nghi phòng</span>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">Chưa có dữ liệu tiện nghi.</td>
                </tr>
            @endforelse
            <tr class="d-none" data-room-amenity-filter-empty>
                <td colspan="4" class="text-center text-muted py-4">Không có tiện nghi phù hợp.</td>
            </tr>
        </tbody>
    </table>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const searchInput = document.querySelector('[data-room-amenity-search]');
                const filterPanel = document.querySelector('.hm-filter-panel');
                const applyButton = filterPanel ? filterPanel.querySelector('.btn.btn-primary') : null;
                const resetButton = filterPanel ? filterPanel.querySelector('.btn.btn-light') : null;
                const filterEmpty = document.querySelector('[data-room-amenity-filter-empty]');
                let rows = Array.from(document.querySelectorAll('[data-room-amenity-row]'));
                let filteredRows = rows;

                const renderRows = function (pageRows) {
                    rows.forEach(function (row) {
                        row.classList.toggle('d-none', !pageRows.includes(row));
                    });

                    if (filterEmpty) {
                        filterEmpty.classList.toggle('d-none', filteredRows.length > 0 || rows.length === 0);
                    }
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
                    filteredRows = rows.filter(function (row) {
                        return keyword === '' || String(row.dataset.search || '').includes(keyword);
                    });

                    if (pagination) {
                        pagination.setItems(filteredRows);
                        return;
                    }

                    renderRows(filteredRows);
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

                    if (!amenityId) {
                        return;
                    }

                    const confirmed = await window.hmConfirmDeletion({
                        title: 'Xóa tiện nghi?',
                        message: 'Bạn muốn xóa tiện nghi này?',
                        recordLabel: 'Mã tiện nghi: ' + amenityId,
                        note: 'Tiện nghi sẽ được gỡ khỏi các loại phòng đang dùng.',
                    });

                    if (!confirmed) {
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

                        const row = deleteButton.closest('[data-room-amenity-row]');
                        if (row) {
                            row.remove();
                            rows = rows.filter(function (item) {
                                return item !== row;
                            });
                        }
                        applyFilters();
                        window.hmShowToast({
                            type: 'success',
                            title: 'Đã xóa',
                            message: payload.message || 'Đã xóa tiện nghi thành công.',
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

                applyFilters();
            });
        </script>
    @endpush
</x-hotel-management.index-page>
