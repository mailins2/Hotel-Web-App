<x-hotel-management.trash-page
    title="Thùng rác loại phòng"
    subtitle="Danh sách loại phòng đã xóa"
    :index-route="route('hotel.room-types.index')"
>
    <style>
        .hm-room-type-trash-table {
            table-layout: fixed;
            width: 100%;
        }

        .hm-room-type-trash-table th:nth-child(3),
        .hm-room-type-trash-table td:nth-child(3) {
            width: 30%;
        }

        .hm-room-type-trash-truncate {
            display: block;
            width: 100%;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>

    <table class="table table-striped align-middle hm-room-type-trash-table">
        <thead>
            <tr>
                <th>Mã loại</th>
                <th>Tên loại phòng</th>
                <th>Mô tả</th>
                <th>Người lớn</th>
                <th>Trẻ em</th>
                <th>Ngày xóa</th>
                <th style="min-width: 220px;">Thao tác</th>
            </tr>
        </thead>
        <tbody id="room-type-trash-table-body">
            <tr>
                <td colspan="7" class="text-center text-muted py-4">Đang tải dữ liệu thùng rác...</td>
            </tr>
        </tbody>
    </table>

    <div
        id="room-type-trash-config"
        data-trash-url="{{ url('/api/loai-phong/trash') }}"
        data-restore-url-template="{{ url('/api/loai-phong/__ROOM_TYPE_ID__/restore') }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableBody = document.getElementById('room-type-trash-table-body');
                const config = document.getElementById('room-type-trash-config');
                const trashUrl = config ? config.dataset.trashUrl : '';
                const restoreUrlTemplate = config ? config.dataset.restoreUrlTemplate : '';
                let roomTypes = [];

                const escapeHtml = function (value) {
                    return String(value ?? '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;');
                };

                const formatDateTime = function (value) {
                    if (!value) {
                        return '--';
                    }

                    const date = new Date(value);
                    return Number.isNaN(date.getTime()) ? value : date.toLocaleString('vi-VN');
                };

                const renderRows = function () {
                    if (!roomTypes.length) {
                        tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Thùng rác đang trống.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = roomTypes.map(function (roomType) {
                        return `
                            <tr>
                                <td>${escapeHtml(roomType.MaLoaiPhong || '--')}</td>
                                <td>${escapeHtml(roomType.TenLoaiPhong || '--')}</td>
                                <td>
                                    <div class="hm-room-type-trash-truncate" title="${escapeHtml(roomType.Mota || '--')}">
                                        ${escapeHtml(roomType.Mota || '--')}
                                    </div>
                                </td>
                                <td>${escapeHtml(roomType.NguoiLon ?? '--')}</td>
                                <td>${escapeHtml(roomType.TreEm ?? 0)}</td>
                                <td>${escapeHtml(formatDateTime(roomType.deleted_at))}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-sm btn-success" data-restore-id="${escapeHtml(roomType.MaLoaiPhong)}">Khôi phục</button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('');
                };

                const loadTrash = async function () {
                    try {
                        const response = await fetch(trashUrl, {
                            headers: { Accept: 'application/json' }
                        });
                        const payload = await response.json().catch(function () { return {}; });

                        if (!response.ok || payload.success === false) {
                            throw new Error(payload.message || 'Không thể tải dữ liệu thùng rác.');
                        }

                        roomTypes = Array.isArray(payload.data) ? payload.data : [];
                        renderRows();
                    } catch (error) {
                        tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">${escapeHtml(error.message)}</td></tr>`;
                    }
                };

                document.addEventListener('click', async function (event) {
                    const restoreButton = event.target.closest('[data-restore-id]');

                    if (!restoreButton) {
                        return;
                    }

                    event.preventDefault();

                    const recordId = restoreButton.getAttribute('data-restore-id') || '';
                    const confirmed = window.confirm(`Khôi phục loại phòng ${recordId}?`);

                    if (!confirmed) {
                        return;
                    }

                    restoreButton.disabled = true;

                    try {
                        const url = restoreUrlTemplate.replace('__ROOM_TYPE_ID__', encodeURIComponent(recordId));
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: { Accept: 'application/json' }
                        });
                        const payload = await response.json().catch(function () { return {}; });

                        if (!response.ok || payload.success === false) {
                            throw new Error(payload.message || 'Không thể xử lý bản ghi.');
                        }

                        await loadTrash();
                    } catch (error) {
                        window.alert(error.message);
                    } finally {
                        restoreButton.disabled = false;
                    }
                });

                loadTrash();
            });
        </script>
    @endpush
</x-hotel-management.trash-page>
