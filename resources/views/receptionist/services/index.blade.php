@php
    $services = collect($services ?? []);
    $serviceRoomOptions = collect($serviceRoomOptions ?? []);
    $serviceOptions = $services->map(fn ($service) => [
        'id' => (string) $service->MaDV,
        'name' => $service->TenDV,
        'type' => (string) $service->LoaiDV,
        'price' => (float) ($service->GiaDV ?? 0),
    ])->values();
    $serviceTypeLabels = [
        \App\Models\DichVu::TYPE_FOOD_AND_BEVERAGE => 'Dịch vụ ăn uống',
        \App\Models\DichVu::TYPE_ROOM_SERVICE => 'Dịch vụ phòng',
        \App\Models\DichVu::TYPE_ENTERTAINMENT => 'Dịch vụ giải trí',
    ];
    $servicesByType = $services->groupBy(fn ($service) => (int) ($service->LoaiDV ?? 0));
    $formatMoney = fn ($amount) => number_format((float) ($amount ?? 0), 0, ',', '.') . ' VNĐ';
@endphp

<x-app-layout :assets="['animation']">
    <style>
        .rs-shell {
            padding-top: 4.5rem;
        }

        .rs-hero,
        .rs-card {
            background: #fff;
            border: 1px solid rgba(166, 98, 43, 0.15);
            border-radius: 28px;
            box-shadow: 0 18px 40px rgba(120, 74, 44, 0.08);
        }

        .rs-hero {
            padding: 1.8rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(180deg, #fff7ef 0%, #fff 55%, #f7fbff 100%);
        }

        .rs-card {
            padding: 1.4rem;
        }

        .rs-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .rs-service-type {
            border: 1px solid rgba(166, 98, 43, 0.12);
            border-radius: 24px;
            background: #fff;
            overflow: hidden;
            transition: border-color 0.18s ease, box-shadow 0.18s ease;
        }

        .rs-service-type.is-open {
            height: 420px;
            border-color: rgba(166, 98, 43, 0.24);
            box-shadow: 0 18px 36px rgba(120, 74, 44, 0.08);
        }

        .rs-type-summary {
            width: 100%;
            border: none;
            padding: 1.15rem;
            background: transparent;
            text-align: left;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .rs-type-summary:hover {
            background: rgba(255, 247, 239, 0.55);
        }

        .rs-type-name {
            color: #2f190f;
            font-size: 1.25rem;
            font-weight: 700;
            line-height: 1.25;
        }

        .rs-type-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
            margin-top: 0.75rem;
        }

        .rs-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2rem;
            padding: 0.3rem 0.8rem;
            border-radius: 999px;
            color: #7b5a46;
            background: #f8f4ef;
            border: 1px solid rgba(166, 98, 43, 0.12);
            font-size: 0.82rem;
            font-weight: 700;
            line-height: 1;
            white-space: nowrap;
        }

        .rs-toggle-icon {
            color: #8b5e3c;
            font-size: 1.4rem;
            font-weight: 700;
            transition: transform 0.18s ease;
        }

        .rs-type-actions {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            flex-shrink: 0;
        }

        .rs-order-button {
            border: 1px solid rgba(122, 57, 42, 0.22);
            border-radius: 8px;
            padding: 0.5rem 0.9rem;
            background: #fff;
            color: #6f1d01;
            font-weight: 700;
            white-space: nowrap;
        }

        .rs-order-button:hover,
        .rs-order-button:focus {
            background: #6f1d01;
            color: #fff;
        }

        .rs-service-type.is-open .rs-toggle-icon {
            transform: rotate(180deg);
        }

        .rs-service-list {
            display: none;
            padding: 0 1.15rem 1.15rem;
            max-height: 290px;
            overflow-y: auto;
            overscroll-behavior: contain;
        }

        .rs-service-type.is-open .rs-service-list {
            display: block;
        }

        .rs-service-list::-webkit-scrollbar {
            width: 8px;
        }

        .rs-service-list::-webkit-scrollbar-track {
            background: rgba(166, 98, 43, 0.08);
            border-radius: 999px;
        }

        .rs-service-list::-webkit-scrollbar-thumb {
            background: rgba(166, 98, 43, 0.35);
            border-radius: 999px;
        }

        .rs-service-stack {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.85rem;
        }

        .rs-service-item {
            min-width: 0;
            padding: 1rem;
            border: 1px solid rgba(15, 118, 110, 0.16);
            border-radius: 20px;
            background: linear-gradient(180deg, #f6fefc 0%, #fff 100%);
        }

        .rs-service-name {
            color: #6f1d01;
            font-size: 1.05rem;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .rs-service-code {
            margin-bottom: 0.25rem;
            color: #0f766e;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .rs-service-price {
            margin-top: 0.7rem;
            color: #2f190f;
            font-size: 1rem;
            font-weight: 700;
        }

        .rs-empty {
            padding: 1.25rem;
            border: 1px dashed rgba(166, 98, 43, 0.22);
            border-radius: 20px;
            color: #8d6e57;
            background: #fffaf4;
        }

        .rs-modal[hidden] {
            display: none;
        }

        .rs-success-modal[hidden] {
            display: none;
        }

        .rs-modal {
            position: fixed;
            inset: 0;
            z-index: 1050;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .rs-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(73, 18, 15, 0.28);
            backdrop-filter: blur(2px);
        }

        .rs-modal-dialog {
            position: relative;
            z-index: 1;
            width: min(980px, calc(100vw - 2rem));
            max-height: calc(100vh - 2rem);
            display: grid;
            grid-template-columns: 240px minmax(0, 1fr);
            overflow: hidden;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 28px 70px rgba(73, 18, 15, 0.22);
        }

        .rs-modal-side {
            padding: 2rem 1.5rem;
            background: linear-gradient(180deg, #6f1d01 0%, #9a4b24 100%);
            color: #fff;
        }

        .rs-modal-side h2 {
            color: #fff;
            font-size: 1.35rem;
            font-weight: 800;
        }

        .rs-modal-side p {
            margin: 0;
            color: rgba(255, 255, 255, 0.82);
        }

        .rs-modal-form {
            position: relative;
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
            padding: 2rem;
        }

        .rs-modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            border: none;
            background: transparent;
            color: #6f1d01;
            font-size: 1.8rem;
            line-height: 1;
        }

        .rs-modal-heading {
            margin-bottom: 1.25rem;
            padding-right: 2rem;
        }

        .rs-modal-heading h3 {
            margin: 0 0 0.35rem;
            color: #6f1d01;
            font-size: 1.4rem;
            font-weight: 800;
        }

        .rs-modal-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .rs-modal-field label,
        .rs-food-list label {
            display: block;
            margin-bottom: 0.4rem;
            color: #7b5a46;
            font-weight: 700;
        }

        .rs-modal-field input,
        .rs-modal-field select,
        .rs-food-row input,
        .rs-food-row select {
            width: 100%;
            min-height: 2.8rem;
            border: 1px solid rgba(166, 98, 43, 0.16);
            border-radius: 10px;
            padding: 0.65rem 0.8rem;
            color: #4d2c1d;
            background: #fffdfb;
        }

        .rs-food-list {
            display: grid;
            gap: 0.85rem;
        }

        .rs-food-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 120px 140px auto;
            gap: 0.75rem;
            align-items: end;
        }

        .rs-food-line-total {
            color: #8c4a34;
            font-weight: 800;
            padding-bottom: 0.75rem;
            white-space: nowrap;
        }

        .rs-food-add,
        .rs-food-remove {
            min-height: 2.4rem;
            border-radius: 8px;
            border: 1px solid rgba(122, 57, 42, 0.22);
            padding: 0 0.9rem;
            font-weight: 800;
        }

        .rs-food-add {
            background: #6f1d01;
            color: #fff;
        }

        .rs-food-remove {
            background: #fff;
            color: #6f1d01;
            margin-bottom: 0.7rem;
        }

        .rs-modal-status {
            margin-top: 1rem;
            color: #b91c1c;
            font-weight: 700;
        }

        .rs-modal-total {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 1rem;
            padding: 0.9rem 1rem;
            border-radius: 10px;
            background: rgba(140, 74, 52, 0.08);
            color: #4f2b21;
            font-weight: 800;
        }

        .rs-modal-total strong {
            color: #8c4a34;
            font-size: 1.1rem;
        }

        .rs-modal-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .rs-modal-actions .btn {
            flex: 1;
        }

        .rs-modal-submit {
            position: relative;
        }

        .rs-modal-submit.is-loading {
            color: transparent !important;
            pointer-events: none;
        }

        .rs-modal-submit.is-loading::after {
            content: "";
            position: absolute;
            inset: 0;
            width: 1.35rem;
            height: 1.35rem;
            margin: auto;
            border: 3px solid rgba(255, 255, 255, 0.45);
            border-top-color: #fff;
            border-radius: 999px;
            animation: rs-spin 0.75s linear infinite;
        }

        @keyframes rs-spin {
            to {
                transform: rotate(360deg);
            }
        }

        .rs-success-modal {
            position: fixed;
            inset: 0;
            z-index: 1060;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .rs-success-dialog {
            position: relative;
            z-index: 1;
            width: min(520px, calc(100vw - 2rem));
            padding: 2rem;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 28px 70px rgba(73, 18, 15, 0.22);
        }

        .rs-success-dialog h3 {
            margin: 0 0 0.5rem;
            color: #6f1d01;
            font-size: 1.5rem;
            font-weight: 800;
        }

        .rs-success-dialog p {
            margin: 0;
            color: #7b5a46;
            font-weight: 600;
        }

        .rs-success-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .rs-success-actions .btn {
            min-height: 2.8rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 991.98px) {
            .rs-service-stack {
                grid-template-columns: 1fr;
            }

            .rs-modal-dialog {
                grid-template-columns: 1fr;
            }

            .rs-food-row {
                grid-template-columns: 1fr;
            }

            .rs-food-line-total {
                padding-bottom: 0;
            }
        }

        @media (max-width: 767.98px) {
            .rs-type-summary {
                align-items: flex-start;
            }
        }
    </style>

    <div class="rs-shell">
        <div class="rs-hero">
            <div>
                <h1 class="mb-2">Dịch vụ khách sạn</h1>
                <p class="text-muted mb-0">Danh sách dịch vụ đang cung cấp, phân nhóm theo loại dịch vụ.</p>
            </div>
        </div>

        <div class="rs-card">
            <div class="rs-list">
                @foreach($serviceTypeLabels as $typeId => $typeLabel)
                    @php
                        $typeServices = $servicesByType->get($typeId, collect());
                        $minPrice = $typeServices->min('GiaDV');
                    @endphp
                    <section class="rs-service-type {{ $loop->first ? 'is-open' : '' }}" data-service-type-card>
                        <button type="button" class="rs-type-summary" data-service-type-toggle aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                            <div>
                                <div class="rs-type-name">{{ $typeLabel }}</div>
                                <div class="rs-type-meta">
                                    <span class="rs-chip">{{ $typeServices->count() }} dịch vụ</span>
                                    <span class="rs-chip">Từ {{ $minPrice !== null ? $formatMoney($minPrice) : '--' }}</span>
                                </div>
                            </div>
                            <span class="rs-type-actions">
                                <span class="rs-order-button" role="button" tabindex="0" data-order-service-type="{{ $typeId }}">Đặt dịch vụ</span>
                                <span class="rs-toggle-icon">⌄</span>
                            </span>
                        </button>

                        <div class="rs-service-list">
                            @if($typeServices->isNotEmpty())
                                <div class="rs-service-stack">
                                    @foreach($typeServices as $service)
                                        <article class="rs-service-item">
                                            <div class="rs-service-code">Mã DV: {{ $service->MaDV }}</div>
                                            <div class="rs-service-name">{{ $service->TenDV }}</div>
                                            <div class="rs-service-price">{{ $formatMoney($service->GiaDV) }}</div>
                                        </article>
                                    @endforeach
                                </div>
                            @else
                                <div class="rs-empty">Chưa có dịch vụ thuộc loại này.</div>
                            @endif
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    </div>

    <div
        class="rs-modal"
        data-service-order-modal
        data-service-options='@json($serviceOptions)'
        data-room-options='@json($serviceRoomOptions)'
        data-store-url="{{ url('/api/su-dung-dich-vu') }}"
        hidden
    >
        <div class="rs-modal-backdrop" data-service-order-close></div>
        <div class="rs-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="serviceOrderTitle">
            <aside class="rs-modal-side">
                <h2>Yêu cầu dịch vụ</h2>
                <p>Chọn phòng, dịch vụ và thời gian sử dụng để ghi nhận vào hóa đơn của khách.</p>
            </aside>

            <form class="rs-modal-form" data-service-order-form>
                <button type="button" class="rs-modal-close" data-service-order-close aria-label="Đóng">&times;</button>
                <div class="rs-modal-heading">
                    <h3 id="serviceOrderTitle" data-service-order-title>Đăng ký dịch vụ</h3>
                    <p class="text-muted mb-0">Vui lòng cung cấp thông tin chi tiết.</p>
                </div>

                <div class="rs-modal-grid">
                    <div class="rs-modal-field">
                        <label>Số phòng</label>
                        <select data-service-room required>
                            @forelse($serviceRoomOptions as $roomOption)
                                <option
                                    value="{{ $roomOption['id'] }}"
                                    data-booking-id="{{ $roomOption['bookingId'] ?? '' }}"
                                    data-room-id="{{ $roomOption['roomId'] ?? '' }}"
                                    data-room-number="{{ $roomOption['roomNumber'] ?? '' }}"
                                    data-check-out="{{ $roomOption['checkOut'] ?? '' }}"
                                    data-detail-url="{{ route('reception.booking-detail', ['bookingDetailId' => $roomOption['id']]) }}"
                                >
                                    {{ $roomOption['label'] }}
                                </option>
                            @empty
                                <option value="" disabled>Chưa có phòng đang check-in</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="rs-modal-field">
                        <label>Loại dịch vụ</label>
                        <select data-service-type-select required>
                            @foreach($serviceTypeLabels as $typeId => $typeLabel)
                                <option value="{{ $typeId }}">{{ $typeLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-3" data-service-single-block>
                    <div class="rs-modal-grid">
                        <div class="rs-modal-field">
                            <label>Tên dịch vụ</label>
                            <select data-service-name-select required></select>
                            <p class="small fw-bold mt-2 mb-0 text-muted" data-service-single-price></p>
                        </div>
                        <div class="rs-modal-field">
                            <label data-service-quantity-label>Số lượng dịch vụ</label>
                            <input type="number" min="1" max="50" value="1" data-service-quantity-input required>
                        </div>
                    </div>
                </div>

                <div class="mt-3" data-service-food-block hidden>
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                        <label class="mb-0 fw-bold">Món ăn đã chọn</label>
                        <button type="button" class="rs-food-add" data-service-food-add-row>Thêm món</button>
                    </div>
                    <div class="rs-food-list" data-service-food-list></div>
                </div>

                <div class="rs-modal-grid mt-3">
                    <div class="rs-modal-field">
                        <label>Ngày sử dụng</label>
                        <input type="date" data-service-date required>
                    </div>
                    <div class="rs-modal-field">
                        <label>Giờ sử dụng</label>
                        <div class="d-flex gap-2">
                            <select data-service-hour required>
                                @for($hour = 0; $hour <= 23; $hour++)
                                    <option value="{{ str_pad((string) $hour, 2, '0', STR_PAD_LEFT) }}">{{ str_pad((string) $hour, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                            <select data-service-minute required>
                                @for($minute = 0; $minute <= 59; $minute++)
                                    <option value="{{ str_pad((string) $minute, 2, '0', STR_PAD_LEFT) }}">{{ str_pad((string) $minute, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <div class="rs-modal-status" data-service-order-status hidden></div>
                <div class="rs-modal-total">
                    <span>Tổng tiền dịch vụ</span>
                    <strong data-service-order-total>0 VNĐ</strong>
                </div>
                <div class="rs-modal-actions">
                    <button type="button" class="btn btn-light" data-service-order-close>Đóng</button>
                    <button type="submit" class="btn btn-primary rs-modal-submit" data-service-order-submit @disabled($serviceRoomOptions->isEmpty())>Hoàn tất đăng ký</button>
                </div>
            </form>
        </div>
    </div>

    <div class="rs-success-modal" data-service-order-success-modal hidden>
        <div class="rs-modal-backdrop" data-service-order-success-close></div>
        <div class="rs-success-dialog" role="dialog" aria-modal="true" aria-labelledby="serviceOrderSuccessTitle">
            <button type="button" class="rs-modal-close" data-service-order-success-close aria-label="Đóng">&times;</button>
            <h3 id="serviceOrderSuccessTitle">Đăng ký dịch vụ thành công</h3>
            <p>Hệ thống đã ghi nhận dịch vụ cho phòng đang ở.</p>
            <div class="rs-success-actions">
                <button type="button" class="btn btn-light" data-service-order-success-close>Đóng</button>
                <a href="#" class="btn btn-primary" data-service-order-detail-link>Xem chi tiết phòng</a>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-service-type-toggle]').forEach((toggle) => {
            toggle.addEventListener('click', () => {
                const card = toggle.closest('[data-service-type-card]');
                if (!card) {
                    return;
                }

                const isOpen = card.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        });

        document.querySelectorAll('[data-order-service-type]').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.stopPropagation();
                window.openServiceOrderModal?.(button.dataset.orderServiceType || '');
            });
            button.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    event.stopPropagation();
                    window.openServiceOrderModal?.(button.dataset.orderServiceType || '');
                }
            });
        });

        (() => {
            const modal = document.querySelector('[data-service-order-modal]');
            if (!modal) {
                return;
            }

            const services = JSON.parse(modal.dataset.serviceOptions || '[]');
            const storeUrl = modal.dataset.storeUrl;
            const form = modal.querySelector('[data-service-order-form]');
            const title = modal.querySelector('[data-service-order-title]');
            const roomSelect = modal.querySelector('[data-service-room]');
            const typeSelect = modal.querySelector('[data-service-type-select]');
            const nameSelect = modal.querySelector('[data-service-name-select]');
            const quantityInput = modal.querySelector('[data-service-quantity-input]');
            const quantityLabel = modal.querySelector('[data-service-quantity-label]');
            const singleBlock = modal.querySelector('[data-service-single-block]');
            const singlePrice = modal.querySelector('[data-service-single-price]');
            const foodBlock = modal.querySelector('[data-service-food-block]');
            const foodList = modal.querySelector('[data-service-food-list]');
            const addFoodButton = modal.querySelector('[data-service-food-add-row]');
            const dateInput = modal.querySelector('[data-service-date]');
            const hourSelect = modal.querySelector('[data-service-hour]');
            const minuteSelect = modal.querySelector('[data-service-minute]');
            const totalEl = modal.querySelector('[data-service-order-total]');
            const statusEl = modal.querySelector('[data-service-order-status]');
            const submitButton = modal.querySelector('[data-service-order-submit]');
            const successModal = document.querySelector('[data-service-order-success-modal]');
            const detailLink = successModal?.querySelector('[data-service-order-detail-link]');

            const typeTitles = {
                '1': 'Đăng ký dịch vụ ăn uống',
                '2': 'Đăng ký dịch vụ phòng',
                '3': 'Đăng ký dịch vụ giải trí',
            };

            const formatMoney = (value) => `${Number(value || 0).toLocaleString('vi-VN')} VNĐ`;
            const pad = (value) => String(value).padStart(2, '0');
            const todayValue = () => {
                const today = new Date();
                return `${today.getFullYear()}-${pad(today.getMonth() + 1)}-${pad(today.getDate())}`;
            };
            const getServicesByType = (type) => services.filter((service) => String(service.type) === String(type));
            const getServiceById = (id) => services.find((service) => String(service.id) === String(id));
            const setStatus = (message = '') => {
                statusEl.textContent = message;
                statusEl.hidden = !message;
            };

            const setSubmitting = (isSubmitting) => {
                if (!submitButton) {
                    return;
                }

                submitButton.disabled = isSubmitting || !roomSelect.value;
                submitButton.classList.toggle('is-loading', isSubmitting);
            };

            const openSuccessModal = () => {
                const selectedRoom = roomSelect.selectedOptions[0];
                const detailUrl = selectedRoom?.dataset.detailUrl || '';

                if (detailLink) {
                    detailLink.href = detailUrl || '#';
                    detailLink.hidden = !detailUrl;
                }

                if (successModal) {
                    successModal.hidden = false;
                }
            };

            const closeSuccessModal = () => {
                if (successModal) {
                    successModal.hidden = true;
                }
            };

            const parseLocalDateTime = (dateValue, hourValue = '00', minuteValue = '00') => {
                if (!dateValue) {
                    return null;
                }

                const date = new Date(`${dateValue}T${hourValue}:${minuteValue}:00`);
                return Number.isNaN(date.getTime()) ? null : date;
            };

            const getServiceWindow = () => {
                const selectedRoom = roomSelect.selectedOptions[0];
                const checkOut = selectedRoom?.dataset.checkOut || '';
                if (!checkOut) {
                    return null;
                }

                const start = new Date();
                start.setSeconds(0, 0);
                const end = parseLocalDateTime(checkOut, '14', '00');

                return end ? { start, end } : null;
            };

            const updateTimeOptions = () => {
                const windowRange = getServiceWindow();
                if (!windowRange) {
                    return;
                }

                const toDateValue = (date) => `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
                const minDate = toDateValue(windowRange.start);
                const maxDate = toDateValue(windowRange.end);

                dateInput.min = minDate;
                dateInput.max = maxDate;

                if (!dateInput.value || dateInput.value < minDate) {
                    dateInput.value = minDate;
                }

                if (dateInput.value > maxDate) {
                    dateInput.value = maxDate;
                }

                const selectedDate = dateInput.value;
                const minHour = selectedDate === minDate ? windowRange.start.getHours() : 0;
                const minMinute = selectedDate === minDate ? windowRange.start.getMinutes() : 0;
                const maxHour = selectedDate === maxDate ? windowRange.end.getHours() : 23;
                const maxMinute = selectedDate === maxDate ? windowRange.end.getMinutes() : 59;

                Array.from(hourSelect.options).forEach((option) => {
                    const hour = Number(option.value);
                    const isInvalid = hour < minHour || hour > maxHour;
                    option.disabled = isInvalid;
                    option.hidden = isInvalid;
                });

                if (hourSelect.selectedOptions[0]?.disabled || hourSelect.selectedOptions[0]?.hidden) {
                    const firstEnabledHour = Array.from(hourSelect.options).find((option) => !option.disabled && !option.hidden);
                    if (firstEnabledHour) {
                        hourSelect.value = firstEnabledHour.value;
                    }
                }

                const selectedHour = Number(hourSelect.value);

                Array.from(minuteSelect.options).forEach((option) => {
                    const minute = Number(option.value);
                    const isInvalid = (selectedHour === minHour && minute < minMinute)
                        || (selectedHour === maxHour && minute > maxMinute);
                    option.disabled = isInvalid;
                    option.hidden = isInvalid;
                });

                if (minuteSelect.selectedOptions[0]?.disabled || minuteSelect.selectedOptions[0]?.hidden) {
                    const firstEnabledMinute = Array.from(minuteSelect.options).find((option) => !option.disabled && !option.hidden);
                    if (firstEnabledMinute) {
                        minuteSelect.value = firstEnabledMinute.value;
                    }
                }
            };

            const validateServiceWindow = () => {
                const windowRange = getServiceWindow();
                if (!windowRange) {
                    setStatus('');
                    return true;
                }

                if (windowRange.start > windowRange.end) {
                    setStatus('Đã quá thời gian đặt dịch vụ cho phòng này.');
                    return false;
                }

                const selectedTime = parseLocalDateTime(dateInput.value, hourSelect.value, minuteSelect.value);
                if (!selectedTime || selectedTime < windowRange.start || selectedTime > windowRange.end) {
                    setStatus('Chỉ được đặt dịch vụ từ thời điểm hiện tại đến 14:00 ngày trả phòng.');
                    return false;
                }

                setStatus('');
                return true;
            };

            const buildFoodOptions = (selectedId = '') => getServicesByType('1').map((service) => {
                const selected = String(service.id) === String(selectedId) ? ' selected' : '';
                return `<option value="${service.id}"${selected}>${service.name} - ${formatMoney(service.price)}</option>`;
            }).join('');

            const updateTotal = () => {
                const type = String(typeSelect.value || '');
                let total = 0;

                if (type === '1') {
                    foodList.querySelectorAll('[data-service-food-row]').forEach((row) => {
                        const service = getServiceById(row.querySelector('[data-service-food-select]')?.value || '');
                        const quantity = Math.max(Number(row.querySelector('[data-service-food-qty]')?.value || 0), 0);
                        const lineTotal = Number(service?.price || 0) * quantity;
                        row.querySelector('[data-service-food-line-total]').textContent = formatMoney(lineTotal);
                        total += lineTotal;
                    });
                    singlePrice.textContent = '';
                } else {
                    const service = getServiceById(nameSelect.value || '');
                    const quantity = Math.max(Number(quantityInput.value || 0), 0);
                    const unitPrice = Number(service?.price || 0);
                    total = unitPrice * quantity;
                    singlePrice.textContent = service ? `Đơn giá: ${formatMoney(unitPrice)}` : '';
                }

                totalEl.textContent = formatMoney(total);
            };

            const renderServiceOptions = (selectedId = '') => {
                nameSelect.innerHTML = '';
                getServicesByType(typeSelect.value).forEach((service) => {
                    const option = document.createElement('option');
                    option.value = service.id;
                    option.textContent = `${service.name} - ${formatMoney(service.price)}`;
                    option.selected = String(service.id) === String(selectedId);
                    nameSelect.append(option);
                });
                updateTotal();
            };

            const refreshFoodRemoveButtons = () => {
                const rows = Array.from(foodList.querySelectorAll('[data-service-food-row]'));
                rows.forEach((row) => {
                    const removeButton = row.querySelector('[data-service-food-remove]');
                    removeButton.hidden = rows.length <= 1;
                    removeButton.disabled = rows.length <= 1;
                });
            };

            const createFoodRow = (selectedId = '', quantity = 1) => {
                const row = document.createElement('div');
                row.className = 'rs-food-row';
                row.setAttribute('data-service-food-row', '');
                row.innerHTML = `
                    <div>
                        <label>Tên món ăn</label>
                        <select data-service-food-select required>${buildFoodOptions(selectedId)}</select>
                    </div>
                    <div>
                        <label>Số lượng</label>
                        <input data-service-food-qty type="number" min="1" max="50" value="${quantity}" required>
                    </div>
                    <div class="rs-food-line-total" data-service-food-line-total>0 VNĐ</div>
                    <button type="button" class="rs-food-remove" data-service-food-remove>Bỏ món</button>
                `;
                foodList.append(row);
                refreshFoodRemoveButtons();
                updateTotal();
            };

            const setMode = (type, selectedId = '') => {
                const isFood = String(type) === '1';
                title.textContent = typeTitles[String(type)] || 'Đăng ký dịch vụ';
                singleBlock.hidden = isFood;
                foodBlock.hidden = !isFood;
                nameSelect.disabled = isFood;
                nameSelect.required = !isFood;
                quantityInput.disabled = isFood;
                quantityInput.required = !isFood;
                quantityInput.value = 1;
                quantityLabel.textContent = String(type) === '2' ? 'Số lượng dịch vụ' : 'Số lượng người';

                if (isFood) {
                    foodList.innerHTML = '';
                    createFoodRow(selectedId);
                } else {
                    renderServiceOptions(selectedId);
                }

                updateTotal();
            };

            const openModal = (type) => {
                if (!roomSelect.value) {
                    setStatus('Chưa có phòng đang check-in để đặt dịch vụ.');
                } else {
                    setStatus('');
                }

                const now = new Date();
                dateInput.value = dateInput.value || todayValue();
                hourSelect.value = pad(now.getHours());
                minuteSelect.value = pad(now.getMinutes());
                typeSelect.value = String(type || typeSelect.value || '1');
                setMode(typeSelect.value);
                updateTimeOptions();
                validateServiceWindow();
                modal.hidden = false;
            };

            const closeModal = () => {
                modal.hidden = true;
                setStatus('');
            };

            const buildPayload = () => {
                const payload = {
                    MaCTDP: roomSelect.value || '',
                    ThoiGian: `${dateInput.value}T${hourSelect.value}:${minuteSelect.value}`,
                };

                if (String(typeSelect.value) === '1') {
                    payload.items = Array.from(foodList.querySelectorAll('[data-service-food-row]'))
                        .map((row) => ({
                            MaDV: row.querySelector('[data-service-food-select]')?.value || '',
                            SoLuong: Number(row.querySelector('[data-service-food-qty]')?.value || 0),
                        }))
                        .filter((item) => item.MaDV && item.SoLuong > 0);
                } else {
                    payload.MaDV = nameSelect.value || '';
                    payload.SoLuong = Number(quantityInput.value || 0);
                }

                return payload;
            };

            window.openServiceOrderModal = openModal;

            modal.querySelectorAll('[data-service-order-close]').forEach((button) => {
                button.addEventListener('click', closeModal);
            });
            successModal?.querySelectorAll('[data-service-order-success-close]').forEach((button) => {
                button.addEventListener('click', closeSuccessModal);
            });
            typeSelect.addEventListener('change', () => setMode(typeSelect.value));
            roomSelect.addEventListener('change', () => {
                setSubmitting(false);
                updateTimeOptions();
                validateServiceWindow();
            });
            dateInput.addEventListener('input', () => {
                updateTimeOptions();
                validateServiceWindow();
            });
            dateInput.addEventListener('change', () => {
                updateTimeOptions();
                validateServiceWindow();
            });
            hourSelect.addEventListener('change', () => {
                updateTimeOptions();
                validateServiceWindow();
            });
            minuteSelect.addEventListener('change', validateServiceWindow);
            nameSelect.addEventListener('change', updateTotal);
            quantityInput.addEventListener('input', updateTotal);
            quantityInput.addEventListener('change', updateTotal);
            addFoodButton.addEventListener('click', () => createFoodRow());
            foodList.addEventListener('input', updateTotal);
            foodList.addEventListener('change', updateTotal);
            foodList.addEventListener('click', (event) => {
                const removeButton = event.target.closest('[data-service-food-remove]');
                if (!removeButton) {
                    return;
                }

                removeButton.closest('[data-service-food-row]')?.remove();
                refreshFoodRemoveButtons();
                updateTotal();
            });

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                setStatus('');

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const payload = buildPayload();
                if (!payload.MaCTDP || (!payload.MaDV && !payload.items?.length)) {
                    setStatus('Vui lòng chọn phòng và dịch vụ.');
                    return;
                }

                if (!validateServiceWindow()) {
                    return;
                }

                setSubmitting(true);

                try {
                    const response = await fetch(storeUrl, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });
                    const result = await response.json().catch(() => null);

                    if (!response.ok || result?.success === false) {
                        throw new Error(result?.message || 'Không thể đặt dịch vụ.');
                    }

                    closeModal();
                    openSuccessModal();
                } catch (error) {
                    setStatus(error.message || 'Không thể đặt dịch vụ.');
                } finally {
                    setSubmitting(false);
                }
            });
        })();
    </script>
</x-app-layout>
