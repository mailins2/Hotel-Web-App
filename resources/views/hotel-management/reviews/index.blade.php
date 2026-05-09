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
                <th style="min-width: 180px;">Thao tác</th>
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
                            <tr>
                                <td>${review.MaDG || '--'}</td>
                                <td>${review.MaDatPhong || '--'}</td>
                                <td>${review.Sao ? `${review.Sao} sao` : '--'}</td>
                                <td>${review.MoTa || '--'}</td>
                                <td>${formatDate(review.NgayDanhGia)}</td>
                                <td>
                                    <div class="hm-action-group">
                                        <a href="${showUrl}" class="btn btn-sm btn-icon text-white" style="background-color: #22c55e; border-color: #22c55e;" title="Xem chi tiết">
                                            <span class="btn-inner">
                                                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M2 12C3.73 8.11 7.52 5.5 12 5.5C16.48 5.5 20.27 8.11 22 12C20.27 15.89 16.48 18.5 12 18.5C7.52 18.5 3.73 15.89 2 12Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                                </svg>
                                            </span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('');
                };

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

                        reviews = await response.json();
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
