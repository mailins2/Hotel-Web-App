<x-hotel-management.index-page
    title="Quản lý bảng giá"
    subtitle="Danh sách thông tin bảng giá theo loại phòng"
    :create-route="route('hotel.price-lists.create')"
>
    <x-slot:filters>
        <div class="col-md-3">
            <label class="form-label">Loại phòng</label>
            <div class="hm-select-wrap">
                <select class="form-select" data-price-room-type>
                    <option value="">Tất cả loại phòng</option>
                    <option value="Phòng Standard">Phòng Standard</option>
                    <option value="Phòng Superior">Phòng Superior</option>
                    <option value="Phòng Suite">Phòng Suite</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label">Mùa</label>
            <div class="hm-select-wrap">
                <select class="form-select" data-price-season>
                    <option value="">Tất cả mùa</option>
                    <option value="Mùa 1">Mùa 1</option>
                    <option value="Mùa 2">Mùa 2</option>
                    <option value="Mùa 3">Mùa 3</option>
                </select>
            </div>
        </div>
    </x-slot:filters>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Mã loại phòng</th>
                <th>Mùa</th>
                <th>Tên loại phòng</th>
                <th>Giá phòng</th>
                <th style="min-width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody id="price-list-table-body">
            <tr class="hm-clickable-row" data-hm-row-link="{{ route('hotel.price-lists.show', ['recordId' => 1]) }}" tabindex="0">
                <td>1</td>
                <td>Mùa 1</td>
                <td>Phòng Standard</td>
                <td>300.000 VNĐ</td>
                <td>
                    @include('hotel-management.partials.action-icons', [
                        'editUrl' => route('hotel.price-lists.edit', ['recordId' => 1]),
                        'showDelete' => true,
                    ])
                </td>
            </tr>
            <tr class="hm-clickable-row" data-hm-row-link="{{ route('hotel.price-lists.show', ['recordId' => 2]) }}" tabindex="0">
                <td>1</td>
                <td>Mùa 2</td>
                <td>Phòng Standard</td>
                <td>400.000 VNĐ</td>
                <td>
                    @include('hotel-management.partials.action-icons', [
                        'editUrl' => route('hotel.price-lists.edit', ['recordId' => 2]),
                        'showDelete' => true,
                    ])
                </td>
            </tr>
            <tr class="hm-clickable-row" data-hm-row-link="{{ route('hotel.price-lists.show', ['recordId' => 3]) }}" tabindex="0">
                <td>2</td>
                <td>Mùa 1</td>
                <td>Phòng Superior</td>
                <td>500.000 VNĐ</td>
                <td>
                    @include('hotel-management.partials.action-icons', [
                        'editUrl' => route('hotel.price-lists.edit', ['recordId' => 3]),
                        'showDelete' => true,
                    ])
                </td>
            </tr>
            <tr class="hm-clickable-row" data-hm-row-link="{{ route('hotel.price-lists.show', ['recordId' => 4]) }}" tabindex="0">
                <td>9</td>
                <td>Mùa 3</td>
                <td>Phòng Suite</td>
                <td>3.500.000 VNĐ</td>
                <td>
                    @include('hotel-management.partials.action-icons', [
                        'editUrl' => route('hotel.price-lists.edit', ['recordId' => 4]),
                        'showDelete' => true,
                    ])
                </td>
            </tr>
        </tbody>
    </table>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableBody = document.getElementById('price-list-table-body');
                const roomTypeSelect = document.querySelector('[data-price-room-type]');
                const seasonSelect = document.querySelector('[data-price-season]');
                const filterPanel = document.querySelector('.hm-filter-panel');
                const applyButton = filterPanel ? filterPanel.querySelector('.btn.btn-primary') : null;
                const resetButton = filterPanel ? filterPanel.querySelector('.btn.btn-light') : null;

                if (!tableBody) {
                    return;
                }

                const priceRows = Array.from(tableBody.querySelectorAll('tr')).map(function (row) {
                    const cells = row.querySelectorAll('td');

                    return {
                        roomTypeName: cells[2] ? cells[2].textContent.trim() : '',
                        season: cells[1] ? cells[1].textContent.trim() : '',
                        html: row.outerHTML
                    };
                });

                const renderRows = function (rows) {
                    if (!rows.length) {
                        tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Không có bảng giá phù hợp.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = rows.map(function (row) {
                        return row.html;
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
                    const roomTypeValue = roomTypeSelect ? roomTypeSelect.value : '';
                    const seasonValue = seasonSelect ? seasonSelect.value : '';

                    const filtered = priceRows.filter(function (row) {
                        const matchesRoomType = !roomTypeValue || row.roomTypeName === roomTypeValue;
                        const matchesSeason = !seasonValue || row.season === seasonValue;
                        return matchesRoomType && matchesSeason;
                    });

                    if (pagination) {
                        pagination.setItems(filtered);
                        return;
                    }

                    renderRows(filtered);
                };

                if (applyButton) {
                    applyButton.addEventListener('click', applyFilters);
                }

                if (resetButton) {
                    resetButton.addEventListener('click', function () {
                        if (roomTypeSelect) {
                            roomTypeSelect.value = '';
                        }

                        if (seasonSelect) {
                            seasonSelect.value = '';
                        }

                        applyFilters();
                    });
                }

                applyFilters();
            });
        </script>
    @endpush
</x-hotel-management.index-page>
