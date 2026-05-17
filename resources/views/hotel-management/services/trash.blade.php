<x-hotel-management.trash-page
    title="Thùng rác dịch vụ"
    subtitle="Danh sách dịch vụ đã xóa"
    :index-route="route('hotel.services.index')"
>
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Mã dịch vụ</th>
                <th>Tên dịch vụ</th>
                <th>Giá dịch vụ</th>
                <th>Loại dịch vụ</th>
                <th>Ngày xóa</th>
                <th style="min-width: 220px;">Thao tác</th>
            </tr>
        </thead>
        <tbody id="service-trash-table-body">
            <tr>
                <td colspan="6" class="text-center text-muted py-4">Đang tải dữ liệu thùng rác...</td>
            </tr>
        </tbody>
    </table>

    <div
        id="service-trash-config"
        data-trash-url="{{ url('/api/dich-vu/trash') }}"
        data-restore-url-template="{{ url('/api/dich-vu/__SERVICE_ID__/restore') }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableBody = document.getElementById('service-trash-table-body');
                const config = document.getElementById('service-trash-config');
                const trashUrl = config ? config.dataset.trashUrl : '';
                const restoreUrlTemplate = config ? config.dataset.restoreUrlTemplate : '';
                let services = [];

                const serviceTypes = {
                    '1': 'Dịch vụ ăn uống',
                    '2': 'Dịch vụ phòng',
                    '3': 'Dịch vụ giải trí',
                };

                const escapeHtml = function (value) {
                    return String(value ?? '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;');
                };

                const formatCurrency = function (value) {
                    const number = Number(value);
                    return Number.isFinite(number) ? `${number.toLocaleString('vi-VN')} VNĐ` : '--';
                };

                const formatDateTime = function (value) {
                    if (!value) {
                        return '--';
                    }
                    const date = new Date(value);
                    return Number.isNaN(date.getTime()) ? value : date.toLocaleString('vi-VN');
                };

                const renderRows = function () {
                    if (!services.length) {
                        tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Thùng rác đang trống.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = services.map(function (service) {
                        return `
                            <tr>
                                <td>${escapeHtml(service.MaDV || '--')}</td>
                                <td>${escapeHtml(service.TenDV || '--')}</td>
                                <td>${escapeHtml(formatCurrency(service.GiaDV))}</td>
                                <td>${escapeHtml(serviceTypes[String(service.LoaiDV)] || '--')}</td>
                                <td>${escapeHtml(formatDateTime(service.deleted_at))}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-sm btn-success" data-restore-id="${escapeHtml(service.MaDV)}">Khôi phục</button>
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

                        services = Array.isArray(payload.data) ? payload.data : [];
                        renderRows();
                    } catch (error) {
                        tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">${escapeHtml(error.message)}</td></tr>`;
                    }
                };

                document.addEventListener('click', async function (event) {
                    const restoreButton = event.target.closest('[data-restore-id]');

                    if (!restoreButton) {
                        return;
                    }

                    event.preventDefault();

                    const recordId = restoreButton.getAttribute('data-restore-id') || '';
                    const confirmed = window.confirm(`Khôi phục dịch vụ ${recordId}?`);

                    if (!confirmed) {
                        return;
                    }

                    restoreButton.disabled = true;

                    try {
                        const url = restoreUrlTemplate.replace('__SERVICE_ID__', encodeURIComponent(recordId));
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
