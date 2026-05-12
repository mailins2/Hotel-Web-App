<x-hotel-management.index-page
    title="Quản lý đánh giá"
    subtitle="Danh sách quản lý đánh giá"
    :show-create-button="false"
>
    <x-slot:filters>
        <div class="col-md-3">
            <label class="form-label">Từ ngày</label>
            <input type="date" class="form-control" data-review-start>
        </div>
        <div class="col-md-3">
            <label class="form-label">Đến ngày</label>
            <input type="date" class="form-control" data-review-end>
        </div>
        <div class="col-md-3">
            <label class="form-label">Số sao</label>
            <div class="hm-select-wrap">
                <select class="form-select" data-review-stars>
                    <option value="">Tất cả số sao</option>
                    <option value="5">5 sao</option>
                    <option value="4">4 sao</option>
                    <option value="3">3 sao</option>
                    <option value="2">2 sao</option>
                    <option value="1">1 sao</option>
                </select>
            </div>
        </div>
    </x-slot:filters>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Mã ĐG</th>
                <th>Mã đặt phòng</th>
                <th>Số sao</th>
                <th>Mô tả</th>
                <th>Ngày đánh giá</th>
                <!-- <th style="min-width: 180px;">Thao tác</th> -->
            </tr>
        </thead>
        <tbody id="review-table-body">
            <tr>
                <td colspan="6" class="text-center text-muted py-4">Đang tải dữ liệu đánh giá...</td>
            </tr>
        </tbody>
    </table>

    <div
        id="review-index-config"
        data-show-url-template="{{ route('hotel.reviews.show', ['recordId' => '__REVIEW_ID__']) }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableBody = document.getElementById('review-table-body');
                const startInput = document.querySelector('[data-review-start]');
                const endInput = document.querySelector('[data-review-end]');
                const starSelect = document.querySelector('[data-review-stars]');
                const filterPanel = document.querySelector('.hm-filter-panel');
                const config = document.getElementById('review-index-config');
                const applyButton = filterPanel ? filterPanel.querySelector('.btn.btn-primary') : null;
                const resetButton = filterPanel ? filterPanel.querySelector('.btn.btn-light') : null;
                const showUrlTemplate = config ? config.dataset.showUrlTemplate : '';

                let reviews = [];

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

                const renderRows = function (rows) {
                    if (!rows.length) {
                        tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Không có đánh giá phù hợp.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = rows.map(function (review) {
                        const showUrl = showUrlTemplate.replace('__REVIEW_ID__', review.MaDG);

                        return `
                            <tr class="hm-clickable-row" data-hm-row-link="${showUrl}" tabindex="0">
                                <td>${review.MaDG || '--'}</td>
                                <td>${review.MaDatPhong || '--'}</td>
                                <td>${review.Sao ? `${review.Sao} sao` : '--'}</td>
                                <td>${review.MoTa || '--'}</td>
                                <td>${formatDate(review.NgayDanhGia)}</td>
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
                    const starValue = (starSelect ? starSelect.value : '') || '';

                    const filtered = reviews.filter(function (review) {
                        const reviewDate = review.NgayDanhGia || '';
                        const matchesStart = !startValue || reviewDate >= startValue;
                        const matchesEnd = !endValue || reviewDate <= endValue;
                        const matchesStars = !starValue || String(review.Sao || '') === starValue;
                        return matchesStart && matchesEnd && matchesStars;
                    });

                    if (pagination) {
                        pagination.setItems(filtered);
                        return;
                    }

                    renderRows(filtered);
                };

                const loadReviews = async function () {
                    try {
                        const response = await fetch('/api/danh-gia', {
                            headers: { 'Accept': 'application/json' }
                        });

                        if (!response.ok) {
                            throw new Error('Không thể tải danh sách đánh giá.');
                        }

                        const payload = await response.json();
                        reviews = (Array.isArray(payload) ? payload : []).slice().sort(function (left, right) {
                            return compareRecordIdDesc(left, right, 'MaDG');
                        });
                        applyFilters();
                    } catch (error) {
                        tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">${error.message}</td></tr>`;
                    }
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
                        if (starSelect) {
                            starSelect.value = '';
                        }
                        applyFilters();
                    });
                }

                loadReviews();
            });
        </script>
    @endpush
</x-hotel-management.index-page>
