<x-hotel-management.trash-page
    title="Thùng rác phòng"
    subtitle="Danh sách phòng đã xóa mềm"
    :index-route="route('hotel.rooms.index')"
>
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Mã phòng</th>
                <th>Số phòng</th>
                <th>Loại phòng</th>
                <th>Tình trạng</th>
                <th>Ngày xóa</th>
                <th style="min-width: 220px;">Thao tác</th>
            </tr>
        </thead>
        <tbody id="room-trash-table-body">
            <tr>
                <td colspan="6" class="text-center text-muted py-4">Đang tải dữ liệu thùng rác...</td>
            </tr>
        </tbody>
    </table>

    <div
        id="room-trash-config"
        data-trash-url="{{ url('/api/phong/trash') }}"
        data-restore-url-template="{{ url('/api/phong/__ROOM_ID__/restore') }}"
        data-force-delete-url-template="{{ url('/api/phong/__ROOM_ID__/force-delete') }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableBody = document.getElementById('room-trash-table-body');
                const config = document.getElementById('room-trash-config');
                const trashUrl = config ? config.dataset.trashUrl : '';
                const restoreUrlTemplate = config ? config.dataset.restoreUrlTemplate : '';
                const forceDeleteUrlTemplate = config ? config.dataset.forceDeleteUrlTemplate : '';
                let rooms = [];

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

                const mapStatus = function (status) {
                    switch (Number(status)) {
                        case 0: return 'Trống';
                        case 1: return 'Đã đặt';
                        case 2: return 'Đang sử dụng';
                        case 3: return 'Đang dọn dẹp';
                        default: return '--';
                    }
                };

                const renderRows = function () {
                    if (!rooms.length) {
                        tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Thùng rác đang trống.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = rooms.map(function (room) {
                        const roomType = room && room.loai_phong ? room.loai_phong : room.loaiPhong;

                        return `
                            <tr>
                                <td>${escapeHtml(room.MaPhong || '--')}</td>
                                <td>${escapeHtml(room.SoPhong || '--')}</td>
                                <td>${escapeHtml(roomType && roomType.TenLoaiPhong ? roomType.TenLoaiPhong : '--')}</td>
                                <td>${escapeHtml(mapStatus(room.TinhTrang))}</td>
                                <td>${escapeHtml(formatDateTime(room.deleted_at))}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-sm btn-success" data-restore-id="${escapeHtml(room.MaPhong)}">Khôi phục</button>
                                        <button type="button" class="btn btn-sm btn-danger" data-force-delete-id="${escapeHtml(room.MaPhong)}">Xóa vĩnh viễn</button>
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

                        rooms = Array.isArray(payload.data) ? payload.data : [];
                        renderRows();
                    } catch (error) {
                        tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">${escapeHtml(error.message)}</td></tr>`;
                    }
                };

                document.addEventListener('click', async function (event) {
                    const restoreButton = event.target.closest('[data-restore-id]');
                    const forceDeleteButton = event.target.closest('[data-force-delete-id]');
                    const targetButton = restoreButton || forceDeleteButton;

                    if (!targetButton) {
                        return;
                    }

                    event.preventDefault();

                    const recordId = targetButton.getAttribute(restoreButton ? 'data-restore-id' : 'data-force-delete-id') || '';
                    const confirmed = window.confirm(
                        restoreButton
                            ? `Khôi phục phòng ${recordId}?`
                            : `Xóa vĩnh viễn phòng ${recordId}?`
                    );

                    if (!confirmed) {
                        return;
                    }

                    targetButton.disabled = true;

                    try {
                        const url = restoreButton
                            ? restoreUrlTemplate.replace('__ROOM_ID__', encodeURIComponent(recordId))
                            : forceDeleteUrlTemplate.replace('__ROOM_ID__', encodeURIComponent(recordId));
                        const response = await fetch(url, {
                            method: restoreButton ? 'POST' : 'DELETE',
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
                        targetButton.disabled = false;
                    }
                });

                loadTrash();
            });
        </script>
    @endpush
</x-hotel-management.trash-page>
