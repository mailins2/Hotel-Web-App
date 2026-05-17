<x-hotel-management.index-page
    title="Quản lý khuyến mãi"
    subtitle="Danh sách chương trình khuyến mãi tại khách sạn"
    :create-route="route('hotel.promotions.create')"
    :trash-route="route('hotel.promotions.trash')"
>
    <x-slot:filters>
        <div class="col-md-3">
            <label class="form-label">Ngày bắt đầu từ</label>
            <input type="date" class="form-control" data-promotion-start>
        </div>
        <div class="col-md-3">
            <label class="form-label">Ngày kết thúc đến</label>
            <input type="date" class="form-control" data-promotion-end>
        </div>
    </x-slot:filters>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Mã khuyến mãi</th>
                <th>Tên khuyến mãi</th>
                <th>Điểm</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
                <th>Giảm giá</th>
                <th style="min-width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody id="promotion-table-body">
            <tr>
                <td colspan="7" class="text-center text-muted py-4">Đang tải dữ liệu khuyến mãi...</td>
            </tr>
        </tbody>
    </table>

    <div
        id="promotion-index-config"
        data-show-url-template="{{ route('hotel.promotions.show', ['recordId' => '__PROMOTION_ID__']) }}"
        data-edit-url-template="{{ route('hotel.promotions.edit', ['recordId' => '__PROMOTION_ID__']) }}"
        data-delete-url-template="{{ url('/api/khuyen-mai/__PROMOTION_ID__') }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableBody = document.getElementById('promotion-table-body');
                const startInput = document.querySelector('[data-promotion-start]');
                const endInput = document.querySelector('[data-promotion-end]');
                const filterPanel = document.querySelector('.hm-filter-panel');
                const config = document.getElementById('promotion-index-config');
                const applyButton = filterPanel ? filterPanel.querySelector('.btn.btn-primary') : null;
                const resetButton = filterPanel ? filterPanel.querySelector('.btn.btn-light') : null;
                const showUrlTemplate = config ? config.dataset.showUrlTemplate : '';
                const editUrlTemplate = config ? config.dataset.editUrlTemplate : '';
                const deleteUrlTemplate = config ? config.dataset.deleteUrlTemplate : '';

                let promotions = @json($promotions ?? []);

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

                const formatDate = function (value) {
                    if (!value) {
                        return '--';
                    }

                    const parts = String(value).split('-');
                    return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : value;
                };

                const buildDeleteUrl = function (promotionId) {
                    return String(deleteUrlTemplate || '').replace('__PROMOTION_ID__', encodeURIComponent(promotionId || ''));
                };

                const renderRows = function (rows) {
                    if (!rows.length) {
                        tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Không có khuyến mãi phù hợp.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = rows.map(function (promotion) {
                        const showUrl = showUrlTemplate.replace('__PROMOTION_ID__', promotion.MaKM);
                        const editUrl = editUrlTemplate.replace('__PROMOTION_ID__', promotion.MaKM);

                        return `
                            <tr class="hm-clickable-row" data-hm-row-link="${showUrl}" tabindex="0">
                                <td>${promotion.MaKM || '--'}</td>
                                <td>${promotion.TenKM || '--'}</td>
                                <td>${promotion.Diem || 0}</td>
                                <td>${formatDate(promotion.NgayBatDau)}</td>
                                <td>${formatDate(promotion.NgayKetThuc)}</td>
                                <td>${promotion.PhanTramGiamGia ? `${Number(promotion.PhanTramGiamGia)}%` : '--'}</td>
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
                                            data-delete-promotion-id="${promotion.MaKM || ''}"
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
                    const startValue = startInput && startInput.value ? startInput.value : '';
                    const endValue = endInput && endInput.value ? endInput.value : '';

                    const filtered = promotions.filter(function (promotion) {
                        const promotionStart = promotion.NgayBatDau || '';
                        const promotionEnd = promotion.NgayKetThuc || '';
                        const matchesStart = !startValue || promotionStart >= startValue;
                        const matchesEnd = !endValue || promotionEnd <= endValue;
                        return matchesStart && matchesEnd;
                    });

                    if (pagination) {
                        pagination.setItems(filtered);
                        return;
                    }

                    renderRows(filtered);
                };

                const loadPromotions = function () {
                    promotions = (Array.isArray(promotions) ? promotions : []).slice().sort(function (left, right) {
                        return compareRecordIdDesc(left, right, 'MaKM');
                    });
                    applyFilters();
                };

                if (applyButton) {
                    applyButton.addEventListener('click', applyFilters);
                }

                if (resetButton) {
                    resetButton.addEventListener('click', function () {
                        if (startInput) {
                            startInput.value = '';
                        }
                        if (endInput) {
                            endInput.value = '';
                        }
                        applyFilters();
                    });
                }

                document.addEventListener('click', async function (event) {
                    const deleteButton = event.target && event.target.closest
                        ? event.target.closest('[data-delete-promotion-id]')
                        : null;

                    if (!deleteButton) {
                        return;
                    }

                    event.preventDefault();
                    event.stopPropagation();

                    const promotionId = deleteButton.getAttribute('data-delete-promotion-id') || '';

                    if (!promotionId || !window.confirm(`Xóa khuyến mãi ${promotionId}?`)) {
                        return;
                    }

                    const originalDisabledState = deleteButton.disabled;
                    deleteButton.disabled = true;

                    try {
                        const response = await fetch(buildDeleteUrl(promotionId), {
                            method: 'DELETE',
                            headers: { Accept: 'application/json' }
                        });

                        const payload = await response.json().catch(function () {
                            return {};
                        });

                        if (!response.ok || payload.success === false) {
                            throw new Error(payload && payload.message ? payload.message : 'Không thể xóa khuyến mãi.');
                        }

                        promotions = promotions.filter(function (promotion) {
                            return String(promotion.MaKM || '') !== String(promotionId);
                        });
                        loadPromotions();
                    } catch (error) {
                        window.alert(error.message);
                    } finally {
                        deleteButton.disabled = originalDisabledState;
                    }
                });

                loadPromotions();
            });
        </script>
    @endpush
</x-hotel-management.index-page>
