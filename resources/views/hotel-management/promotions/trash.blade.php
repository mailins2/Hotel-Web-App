<x-hotel-management.trash-page
    title="Thùng rác khuyến mãi"
    subtitle="Danh sách khuyến mãi đã xóa mềm"
    :index-route="route('hotel.promotions.index')"
>
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Mã khuyến mãi</th>
                <th>Tên khuyến mãi</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
                <th>Giảm giá</th>
                <th>Ngày xóa</th>
                <th style="min-width: 220px;">Thao tác</th>
            </tr>
        </thead>
        <tbody id="promotion-trash-table-body">
            <tr>
                <td colspan="7" class="text-center text-muted py-4">Đang tải dữ liệu thùng rác...</td>
            </tr>
        </tbody>
    </table>

    <div
        id="promotion-trash-config"
        data-trash-url="{{ url('/api/khuyen-mai/trash') }}"
        data-restore-url-template="{{ url('/api/khuyen-mai/__PROMOTION_ID__/restore') }}"
        data-force-delete-url-template="{{ url('/api/khuyen-mai/__PROMOTION_ID__/force-delete') }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableBody = document.getElementById('promotion-trash-table-body');
                const config = document.getElementById('promotion-trash-config');
                const trashUrl = config ? config.dataset.trashUrl : '';
                const restoreUrlTemplate = config ? config.dataset.restoreUrlTemplate : '';
                const forceDeleteUrlTemplate = config ? config.dataset.forceDeleteUrlTemplate : '';
                let promotions = [];

                const escapeHtml = function (value) {
                    return String(value ?? '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;');
                };

                const formatDate = function (value) {
                    if (!value) {
                        return '--';
                    }
                    const parts = String(value).split('-');
                    return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : value;
                };

                const formatDateTime = function (value) {
                    if (!value) {
                        return '--';
                    }
                    const date = new Date(value);
                    return Number.isNaN(date.getTime()) ? value : date.toLocaleString('vi-VN');
                };

                const renderRows = function () {
                    if (!promotions.length) {
                        tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Thùng rác đang trống.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = promotions.map(function (promotion) {
                        return `
                            <tr>
                                <td>${escapeHtml(promotion.MaKM || '--')}</td>
                                <td>${escapeHtml(promotion.TenKM || '--')}</td>
                                <td>${escapeHtml(formatDate(promotion.NgayBatDau))}</td>
                                <td>${escapeHtml(formatDate(promotion.NgayKetThuc))}</td>
                                <td>${escapeHtml(promotion.PhanTramGiamGia ? `${Number(promotion.PhanTramGiamGia)}%` : '--')}</td>
                                <td>${escapeHtml(formatDateTime(promotion.deleted_at))}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-sm btn-success" data-restore-id="${escapeHtml(promotion.MaKM)}">Khôi phục</button>
                                        <button type="button" class="btn btn-sm btn-danger" data-force-delete-id="${escapeHtml(promotion.MaKM)}">Xóa vĩnh viễn</button>
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

                        promotions = Array.isArray(payload.data) ? payload.data : [];
                        renderRows();
                    } catch (error) {
                        tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">${escapeHtml(error.message)}</td></tr>`;
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
                            ? `Khôi phục khuyến mãi ${recordId}?`
                            : `Xóa vĩnh viễn khuyến mãi ${recordId}?`
                    );

                    if (!confirmed) {
                        return;
                    }

                    targetButton.disabled = true;

                    try {
                        const url = restoreButton
                            ? restoreUrlTemplate.replace('__PROMOTION_ID__', encodeURIComponent(recordId))
                            : forceDeleteUrlTemplate.replace('__PROMOTION_ID__', encodeURIComponent(recordId));
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
