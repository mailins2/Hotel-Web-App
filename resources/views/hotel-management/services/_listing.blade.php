@php
    $pageTitle = $pageTitle ?? 'Quản lý dịch vụ';
    $pageSubtitle = $pageSubtitle ?? 'Danh sách quản lý dịch vụ tại khách sạn';
    $defaultType = isset($defaultType) ? (string) $defaultType : '';

    $typeTitles = [
        '' => 'Tất cả dịch vụ',
        '1' => 'Dịch vụ ăn uống',
        '2' => 'Dịch vụ phòng',
        '3' => 'Dịch vụ giải trí',
    ];
@endphp

<x-hotel-management.index-page
    :title="$pageTitle"
    :subtitle="$pageSubtitle"
    :create-route="route('hotel.services.create')"
>
    <x-slot:filters>
        <div class="col-md-4">
            <label class="form-label">Tên dịch vụ</label>
            <input
                type="text"
                class="form-control"
                placeholder="Tìm theo tên dịch vụ"
                data-service-search
            >
        </div>
    </x-slot:filters>

    <x-slot:beforeTable>
        <div class="mb-4">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a
                    href="{{ route('hotel.services.index') }}"
                    class="btn btn-sm {{ $defaultType === '' ? 'btn-primary' : 'btn-light' }}"
                    style="padding: 8px 14px;"
                >Tất cả dịch vụ</a>
                <a
                    href="{{ route('hotel.services.food-and-beverage') }}"
                    class="btn btn-sm {{ $defaultType === '1' ? 'btn-primary' : 'btn-light' }}"
                    style="padding: 8px 14px;"
                >Dịch vụ ăn uống</a>
                <a
                    href="{{ route('hotel.services.room-service') }}"
                    class="btn btn-sm {{ $defaultType === '2' ? 'btn-primary' : 'btn-light' }}"
                    style="padding: 8px 14px;"
                >Dịch vụ phòng</a>
                <a
                    href="{{ route('hotel.services.entertainment') }}"
                    class="btn btn-sm {{ $defaultType === '3' ? 'btn-primary' : 'btn-light' }}"
                    style="padding: 8px 14px;"
                >Dịch vụ giải trí</a>
            </div>
        </div>
    </x-slot:beforeTable>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Mã dịch vụ</th>
                <th>Tên dịch vụ</th>
                <th>Giá dịch vụ</th>
                <th>Loại dịch vụ</th>
                <th>Ảnh dịch vụ</th>
                <th style="min-width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody id="service-table-body">
            <tr>
                <td colspan="6" class="text-center text-muted py-4">Đang tải dữ liệu dịch vụ...</td>
            </tr>
        </tbody>
    </table>

    <div
        id="service-index-config"
        data-show-url-template="{{ route('hotel.services.show', ['recordId' => '__SERVICE_ID__']) }}"
        data-edit-url-template="{{ route('hotel.services.edit', ['recordId' => '__SERVICE_ID__']) }}"
        data-delete-url-template="{{ url('/api/dich-vu/__SERVICE_ID__') }}"
        data-placeholder-image="https://placehold.co/600x600/f3f4f6/9ca3af?text=Service"
        data-default-service-type="{{ $defaultType }}"
        hidden
    ></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableBody = document.getElementById('service-table-body');
                const searchInput = document.querySelector('[data-service-search]');
                const filterPanel = document.querySelector('.hm-filter-panel');
                const applyButton = filterPanel ? filterPanel.querySelector('.btn.btn-primary') : null;
                const resetButton = filterPanel ? filterPanel.querySelector('.btn.btn-light') : null;
                const config = document.getElementById('service-index-config');
                const showUrlTemplate = config ? config.dataset.showUrlTemplate : '';
                const editUrlTemplate = config ? config.dataset.editUrlTemplate : '';
                const deleteUrlTemplate = config ? config.dataset.deleteUrlTemplate : '';
                const placeholderImage = config ? config.dataset.placeholderImage : '';
                const defaultType = config ? config.dataset.defaultServiceType || '' : '';

                const serviceTypes = {
                    '1': 'Dịch vụ ăn uống',
                    '2': 'Dịch vụ phòng',
                    '3': 'Dịch vụ giải trí'
                };

                const sectionOrder = [
                    { key: '1', title: 'Dịch vụ ăn uống' },
                    { key: '2', title: 'Dịch vụ phòng' },
                    { key: '3', title: 'Dịch vụ giải trí' }
                ];

                let services = @json($services ?? []);
                let images = [];

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

                const escapeHtml = function (value) {
                    return String(value || '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;');
                };

                const formatCurrency = function (value) {
                    const number = Number(value);
                    if (!Number.isFinite(number)) {
                        return '--';
                    }
                    return number.toLocaleString('vi-VN') + ' VNĐ';
                };

                const getTypeLabel = function (typeValue) {
                    return serviceTypes[String(typeValue)] || 'Khác';
                };

                const getImageUrl = function (serviceId) {
                    const matchedService = services.find(function (service) {
                        return String(service.MaDV || '') === String(serviceId || '');
                    });
                    const serviceImages = matchedService && Array.isArray(matchedService.hinhs) ? matchedService.hinhs : [];
                    if (serviceImages.length && serviceImages[0].Url) {
                        return serviceImages[0].Url;
                    }

                    const matchedImage = images.find(function (image) {
                        return String(image.MaDV || '') === String(serviceId || '');
                    });

                    return matchedImage && matchedImage.Url ? matchedImage.Url : placeholderImage;
                };

                const buildImageTag = function (imageUrl, altText, extraClass, extraStyle) {
                    return `
                        <img
                            src="${escapeHtml(imageUrl)}"
                            alt="${escapeHtml(altText)}"
                            class="${extraClass}"
                            style="${extraStyle}"
                            onerror="this.onerror=null;this.src='${escapeHtml(placeholderImage)}';"
                        >
                    `;
                };

                const buildDeleteUrl = function (serviceId) {
                    return String(deleteUrlTemplate || '').replace('__SERVICE_ID__', encodeURIComponent(serviceId || ''));
                };

                const renderTable = function (rows) {
                    if (!rows.length) {
                        tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Không có dịch vụ phù hợp.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = rows.map(function (service) {
                        const showUrl = showUrlTemplate.replace('__SERVICE_ID__', service.MaDV);
                        const editUrl = editUrlTemplate.replace('__SERVICE_ID__', service.MaDV);
                        const imageUrl = getImageUrl(service.MaDV);

                        return `
                            <tr class="hm-clickable-row" data-hm-row-link="${showUrl}" tabindex="0">
                                <td>${escapeHtml(service.MaDV || '--')}</td>
                                <td>${escapeHtml(service.TenDV || '--')}</td>
                                <td>${escapeHtml(formatCurrency(service.GiaDV))}</td>
                                <td>${escapeHtml(getTypeLabel(service.LoaiDV))}</td>
                                <td>
                                    ${buildImageTag(
                                        imageUrl,
                                        `Ảnh dịch vụ ${service.TenDV || ''}`,
                                        'rounded border bg-light',
                                        'width: 72px; height: 72px; object-fit: cover; object-position: center;'
                                    )}
                                </td>
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
                                            data-delete-service-id="${service.MaDV || ''}"
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
                        onPageChange: renderTable
                    })
                    : null;

                const applyFilters = function () {
                    const searchValue = searchInput ? searchInput.value.trim().toLowerCase() : '';

                    const filtered = services.filter(function (service) {
                        const name = String(service.TenDV || '').toLowerCase();
                        const id = String(service.MaDV || '').toLowerCase();
                        const matchesSearch = !searchValue || name.includes(searchValue) || id.includes(searchValue);
                        const matchesType = !defaultType || String(service.LoaiDV || '') === defaultType;
                        return matchesSearch && matchesType;
                    });

                    if (pagination) {
                        pagination.setItems(filtered);
                        return;
                    }

                    renderTable(filtered);
                };

                const loadData = function () {
                    services = (Array.isArray(services) ? services : []).slice().sort(function (left, right) {
                        return compareRecordIdDesc(left, right, 'MaDV');
                    });
                    applyFilters();
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

                document.addEventListener('click', async function (event) {
                    const deleteButton = event.target && event.target.closest
                        ? event.target.closest('[data-delete-service-id]')
                        : null;

                    if (!deleteButton) {
                        return;
                    }

                    event.preventDefault();
                    event.stopPropagation();

                    const serviceId = deleteButton.getAttribute('data-delete-service-id') || '';

                    if (!serviceId) {
                        return;
                    }

                    const confirmed = await window.hmConfirmDeletion({
                        title: 'Xóa dịch vụ?',
                        message: 'Bạn muốn xóa dịch vụ này?',
                        recordLabel: 'Mã dịch vụ: ' + serviceId,
                        note: 'Dịch vụ đã phát sinh lượt sử dụng sẽ không thể xóa.',
                    });

                    if (!confirmed) {
                        return;
                    }

                    const originalDisabledState = deleteButton.disabled;
                    deleteButton.disabled = true;

                    try {
                        const response = await fetch(buildDeleteUrl(serviceId), {
                            method: 'DELETE',
                            headers: { Accept: 'application/json' }
                        });

                        const payload = await response.json().catch(function () {
                            return {};
                        });

                        if (!response.ok || payload.success === false) {
                            throw new Error(payload && payload.message ? payload.message : 'Không thể xóa dịch vụ.');
                        }

                        services = services.filter(function (service) {
                            return String(service.MaDV || '') !== String(serviceId);
                        });
                        loadData();
                        window.hmShowToast({
                            type: 'success',
                            title: 'Đã xóa',
                            message: payload.message || 'Đã xóa dịch vụ thành công.',
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

                loadData();
            });
        </script>
    @endpush
</x-hotel-management.index-page>
