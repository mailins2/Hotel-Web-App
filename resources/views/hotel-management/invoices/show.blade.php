<style>
    .hm-invoice-sheet {
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
        margin-bottom: 2rem;
    }

    .hm-invoice-grid {
        display: grid;
        gap: 1.2rem;
    }

    .hm-invoice-grid--top {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .hm-invoice-grid--double {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .hm-invoice-card {
        padding: 1.3rem;
        border: 1px solid #f1dfd0;
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.96);
        box-shadow: 0 18px 40px -38px rgba(120, 65, 26, 0.35);
    }

    .hm-invoice-card-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.8rem;
        margin-bottom: 1rem;
    }

    .hm-invoice-card-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
        color: #111827;
    }

    .hm-invoice-card-note {
        font-size: 0.86rem;
        color: #9a3412;
    }

    .hm-invoice-info-grid {
        display: grid;
        gap: 0.9rem;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .hm-invoice-info-item {
        padding: 1rem;
        border: 1px solid #f7e6d7;
        border-radius: 18px;
        background: #fffdfa;
        min-height: 86px;
    }

    .hm-invoice-label {
        margin-bottom: 0.45rem;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #b45309;
    }

    .hm-invoice-value {
        font-size: 1rem;
        font-weight: 600;
        line-height: 1.5;
        color: #0f172a;
        word-break: break-word;
    }

    .hm-invoice-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.45rem 0.82rem;
        border-radius: 999px;
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .hm-invoice-status--warning { background: #fef3c7; color: #9a3412; }
    .hm-invoice-status--success { background: #dcfce7; color: #166534; }
    .hm-invoice-status--danger { background: #fee2e2; color: #b91c1c; }
    .hm-invoice-status--muted { background: #eceff3; color: #475569; }

    .hm-invoice-room-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
    }

    .hm-invoice-room-tag {
        display: inline-flex;
        align-items: center;
        padding: 0.52rem 0.78rem;
        border-radius: 999px;
        background: #fff1e7;
        border: 1px solid #f7d6bd;
        color: #9a3412;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .hm-invoice-summary {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

    .hm-invoice-summary-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        font-size: 0.95rem;
        color: #334155;
    }

    .hm-invoice-summary-row strong {
        color: #0f172a;
        text-align: right;
    }

    .hm-invoice-summary-row--accent {
        color: #9a3412;
    }

    .hm-invoice-summary-row--accent strong {
        color: #9a3412;
    }

    .hm-invoice-summary-row--grand {
        padding-top: 0.9rem;
        border-top: 1px dashed #e7cdb8;
        font-size: 1.05rem;
        font-weight: 700;
    }

    .hm-invoice-summary-row--grand strong {
        font-size: 1.25rem;
    }

    .hm-invoice-summary-callout {
        margin-top: 1rem;
        padding: 1rem;
        border-radius: 18px;
        border: 1px solid #f3d7c2;
        background: #fff5ec;
        color: #9a3412;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .hm-invoice-table-wrap {
        overflow-x: auto;
    }

    .hm-invoice-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 720px;
    }

    .hm-invoice-table thead th {
        padding: 0.9rem 0.95rem;
        border-bottom: 1px solid #f1dfd0;
        background: #fff8f2;
        color: #9a3412;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .hm-invoice-table tbody td {
        padding: 0.95rem;
        border-bottom: 1px solid #f8eadd;
        vertical-align: top;
        color: #0f172a;
    }

    .hm-invoice-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .hm-invoice-item-title {
        margin: 0;
        font-size: 0.96rem;
        font-weight: 700;
        color: #111827;
    }

    .hm-invoice-item-meta {
        margin-top: 0.28rem;
        font-size: 0.85rem;
        line-height: 1.45;
        color: #64748b;
    }

    .hm-invoice-chip {
        display: inline-flex;
        align-items: center;
        padding: 0.42rem 0.72rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .hm-invoice-chip--room { background: #ede9fe; color: #6d28d9; }
    .hm-invoice-chip--service { background: #dbeafe; color: #1d4ed8; }
    .hm-invoice-chip--compensation { background: #fee2e2; color: #b91c1c; }
    .hm-invoice-chip--other { background: #e2e8f0; color: #475569; }

    .hm-invoice-num {
        text-align: right;
        white-space: nowrap;
        font-weight: 600;
    }

    .hm-invoice-list {
        display: grid;
        gap: 0.85rem;
    }

    .hm-invoice-list-item {
        padding: 1rem;
        border: 1px solid #f5e5d8;
        border-radius: 18px;
        background: #fffdfa;
    }

    .hm-invoice-list-head {
        display: flex;
        justify-content: space-between;
        gap: 0.9rem;
        align-items: flex-start;
    }

    .hm-invoice-list-title {
        margin: 0;
        font-size: 0.96rem;
        font-weight: 700;
        color: #111827;
    }

    .hm-invoice-list-total {
        white-space: nowrap;
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
    }

    .hm-invoice-list-meta {
        margin-top: 0.45rem;
        display: grid;
        gap: 0.35rem;
        color: #64748b;
        font-size: 0.86rem;
        line-height: 1.45;
    }

    .hm-invoice-empty {
        padding: 1rem;
        border-radius: 18px;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        color: #64748b;
        text-align: center;
    }

    .main-content .content-inner {
        padding-bottom: 1.5rem;
    }

    .main-content .footer {
        position: relative;
        z-index: 2;
        margin-top: 0.5rem;
    }

    @media (max-width: 1199.98px) {
        .hm-invoice-grid--top,
        .hm-invoice-grid--double {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px) {
        .hm-invoice-info-grid {
            grid-template-columns: 1fr;
        }

        .hm-invoice-list-head {
            flex-direction: column;
        }
    }
</style>

<x-hotel-management.show-page
    title="Chi tiết hóa đơn"
    subtitle="Thông tin chi tiết hóa đơn"
    :index-route="route('hotel.invoices.index')"
>
    <div class="col-12">
        <div id="invoice-show-alert" class="alert d-none mb-4" role="alert"></div>

        <div class="hm-invoice-sheet">
            <div class="hm-invoice-grid hm-invoice-grid--top">
                <div class="hm-invoice-card">
                    <div class="hm-invoice-card-head">
                        <h3 class="hm-invoice-card-title">Thông tin hóa đơn</h3>
                        <span class="hm-invoice-card-note">Thông tin cơ bản</span>
                    </div>

                    <div class="hm-invoice-info-grid">
                        <div class="hm-invoice-info-item">
                            <div class="hm-invoice-label">Mã hóa đơn</div>
                            <div class="hm-invoice-value" id="invoice-info-id">Đang tải...</div>
                        </div>
                        <div class="hm-invoice-info-item">
                            <div class="hm-invoice-label">Mã đặt phòng</div>
                            <div class="hm-invoice-value" id="invoice-info-booking">Đang tải...</div>
                        </div>
                        <div class="hm-invoice-info-item">
                            <div class="hm-invoice-label">Ngày lập</div>
                            <div class="hm-invoice-value" id="invoice-info-date">Đang tải...</div>
                        </div>
                        <div class="hm-invoice-info-item">
                            <div class="hm-invoice-label">Nhân viên phụ trách</div>
                            <div class="hm-invoice-value" id="invoice-info-employee">Đang tải...</div>
                        </div>
                    </div>
                </div>

                <div class="hm-invoice-card">
                    <div class="hm-invoice-card-head">
                        <h3 class="hm-invoice-card-title">Thông tin lưu trú</h3>
                        <span class="hm-invoice-card-note">Theo đặt phòng</span>
                    </div>

                    <div class="hm-invoice-info-grid">
                        <div class="hm-invoice-info-item">
                            <div class="hm-invoice-label">Ngày nhận phòng</div>
                            <div class="hm-invoice-value" id="invoice-check-in">Đang tải...</div>
                        </div>
                        <div class="hm-invoice-info-item">
                            <div class="hm-invoice-label">Ngày trả phòng</div>
                            <div class="hm-invoice-value" id="invoice-check-out">Đang tải...</div>
                        </div>
                        <div class="hm-invoice-info-item">
                            <div class="hm-invoice-label">Số đêm</div>
                            <div class="hm-invoice-value" id="invoice-stay-duration">Đang tải...</div>
                        </div>
                        <div class="hm-invoice-info-item">
                            <div class="hm-invoice-label">Khuyến mãi</div>
                            <div class="hm-invoice-value" id="invoice-promotion">Đang tải...</div>
                        </div>
                        <div class="hm-invoice-info-item" style="grid-column: 1 / -1;">
                            <div class="hm-invoice-label">Loại phòng trong hóa đơn</div>
                            <div class="hm-invoice-room-tags" id="invoice-room-tags">
                                <span class="hm-invoice-room-tag">Đang tải...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hm-invoice-card">
                    <div class="hm-invoice-card-head">
                        <h3 class="hm-invoice-card-title">Tổng kết thanh toán</h3>
                        <span class="hm-invoice-card-note">Từ hóa đơn và thanh toán</span>
                    </div>

                    <div class="hm-invoice-summary">
                        <div class="hm-invoice-summary-row">
                            <span>Trạng thái</span>
                            <strong><span id="invoice-status-badge" class="hm-invoice-status hm-invoice-status--muted">Đang tải</span></strong>
                        </div>
                        <div class="hm-invoice-summary-row">
                            <span>Tạm tính</span>
                            <strong id="invoice-subtotal">--</strong>
                        </div>
                        <div class="hm-invoice-summary-row">
                            <span>Tiền phòng</span>
                            <strong id="invoice-room-subtotal">--</strong>
                        </div>
                        <div class="hm-invoice-summary-row">
                            <span>Dịch vụ</span>
                            <strong id="invoice-service-subtotal">--</strong>
                        </div>
                        <div class="hm-invoice-summary-row">
                            <span>Đền bù / phát sinh</span>
                            <strong id="invoice-compensation-subtotal">--</strong>
                        </div>
                        <div class="hm-invoice-summary-row hm-invoice-summary-row--accent">
                            <span>Giảm giá</span>
                            <strong id="invoice-discount">--</strong>
                        </div>
                        <div class="hm-invoice-summary-row">
                            <span>Đã thanh toán</span>
                            <strong id="invoice-paid-total">--</strong>
                        </div>
                        <div class="hm-invoice-summary-row hm-invoice-summary-row--grand">
                            <span>Còn lại</span>
                            <strong id="invoice-remaining-total">--</strong>
                        </div>
                    </div>

                    <div class="hm-invoice-summary-callout" id="invoice-summary-note">
                        Đang tải dữ liệu hóa đơn.
                    </div>
                </div>
            </div>

            <div class="hm-invoice-card">
                <div class="hm-invoice-card-head">
                    <h3 class="hm-invoice-card-title">Chi tiết tính tiền</h3>
                    <span class="hm-invoice-card-note" id="invoice-line-count">0 dòng</span>
                </div>

                <div class="hm-invoice-table-wrap">
                    <table class="hm-invoice-table">
                        <thead>
                            <tr>
                                <th>Hạng mục</th>
                                <th>Nội dung</th>
                                <th>SL</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody id="invoice-line-items">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Đang tải chi tiết hóa đơn...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="hm-invoice-grid hm-invoice-grid--double">
                <div class="hm-invoice-card">
                    <div class="hm-invoice-card-head">
                        <h3 class="hm-invoice-card-title">Dịch vụ sử dụng</h3>
                        <span class="hm-invoice-card-note" id="invoice-service-count">0 dịch vụ</span>
                    </div>

                    <div id="invoice-service-items" class="hm-invoice-list">
                        <div class="hm-invoice-empty">Chưa có dịch vụ sử dụng.</div>
                    </div>
                </div>

                <div class="hm-invoice-card">
                    <div class="hm-invoice-card-head">
                        <h3 class="hm-invoice-card-title">Đền bù / hư hỏng</h3>
                        <span class="hm-invoice-card-note" id="invoice-compensation-count">0 khoản</span>
                    </div>

                    <div id="invoice-compensation-items" class="hm-invoice-list">
                        <div class="hm-invoice-empty">Chưa có khoản đền bù phát sinh.</div>
                    </div>
                </div>
            </div>

        </div>

        <div id="invoice-show-config" data-invoice-id="{{ request()->route('recordId') }}" hidden></div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', async function () {
                const config = document.getElementById('invoice-show-config');
                const invoiceId = config ? config.dataset.invoiceId : '';
                const alertBox = document.getElementById('invoice-show-alert');

                const setAlert = function (type, message) {
                    if (!alertBox) {
                        return;
                    }

                    alertBox.className = 'alert alert-' + type + ' mb-4';
                    alertBox.textContent = message;
                };

                const clearAlert = function () {
                    if (!alertBox) {
                        return;
                    }

                    alertBox.className = 'alert d-none mb-4';
                    alertBox.textContent = '';
                };

                const setText = function (id, value) {
                    const element = document.getElementById(id);
                    if (element) {
                        element.textContent = value;
                    }
                };

                const escapeHtml = function (value) {
                    return String(value === null || value === undefined ? '' : value)
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;');
                };

                const getRelation = function (record) {
                    if (!record) {
                        return null;
                    }

                    for (let index = 1; index < arguments.length; index += 1) {
                        const key = arguments[index];
                        if (record[key]) {
                            return record[key];
                        }
                    }

                    return null;
                };

                const getArrayRelation = function (record) {
                    const relation = getRelation.apply(null, arguments);
                    return Array.isArray(relation) ? relation : [];
                };

                const formatDate = function (value) {
                    if (!value) {
                        return '--';
                    }

                    const normalized = String(value).split(/[ T]/)[0];
                    const parts = normalized.split('-');
                    return parts.length === 3 ? (parts[2] + '/' + parts[1] + '/' + parts[0]) : String(value);
                };

                const formatDateTime = function (value) {
                    if (!value) {
                        return '--';
                    }

                    const segments = String(value).split(' ');
                    const formattedDate = formatDate(segments[0]);
                    return segments[1] ? (formattedDate + ' ' + segments[1]) : formattedDate;
                };

                const formatCurrency = function (value) {
                    const number = Number(value);
                    if (Number.isNaN(number)) {
                        return '--';
                    }

                    return number.toLocaleString('vi-VN') + ' VNĐ';
                };

                const getNightCount = function (checkIn, checkOut) {
                    if (!checkIn || !checkOut) {
                        return '--';
                    }

                    const start = new Date(checkIn);
                    const end = new Date(checkOut);
                    const diff = end.getTime() - start.getTime();

                    if (!Number.isFinite(diff) || diff <= 0) {
                        return '--';
                    }

                    return Math.round(diff / (1000 * 60 * 60 * 24)) + ' đêm';
                };

                const mapInvoiceStatus = function (value) {
                    switch (Number(value)) {
                        case 0:
                            return { label: 'Chưa thanh toán', className: 'warning' };
                        case 1:
                            return { label: 'Đã thanh toán', className: 'success' };
                        case 3:
                            return { label: 'Đã hủy', className: 'danger' };
                        default:
                            return { label: 'Không xác định', className: 'muted' };
                    }
                };

                const mapServiceType = function (service) {
                    if (!service) {
                        return 'Khác';
                    }

                    if (service.LoaiDVText) {
                        return service.LoaiDVText;
                    }

                    switch (Number(service.LoaiDV)) {
                        case 1:
                            return 'Dịch vụ ăn uống';
                        case 2:
                            return 'Dịch vụ phòng';
                        case 3:
                            return 'Dịch vụ giải trí';
                        default:
                            return 'Khác';
                    }
                };

                const buildLineItems = function (invoice) {
                    const detailLines = getArrayRelation(invoice, 'chiTietHoaDons', 'chi_tiet_hoa_dons');
                    const roomBuckets = {};
                    const otherItems = [];

                    detailLines.forEach(function (line) {
                        const quantity = Math.max(1, Number(line && line.SoLuong ? line.SoLuong : 1));
                        const unitPrice = Number(line && line.DonGia ? line.DonGia : 0);
                        const total = quantity * unitPrice;

                        if (line && line.MaLoaiPhong && !line.MaSuDung && !line.MaDenBu) {
                            const roomType = getRelation(line, 'loaiPhong', 'loai_phong');
                            const roomTypeId = String(line.MaLoaiPhong);
                            const bucketKey = roomTypeId + '-' + unitPrice;

                            if (!roomBuckets[bucketKey]) {
                                roomBuckets[bucketKey] = {
                                    categoryLabel: 'Phòng',
                                    categoryClass: 'room',
                                    description: roomType && roomType.TenLoaiPhong ? roomType.TenLoaiPhong : 'Tiền phòng',
                                    meta: 'Chi phí lưu trú',
                                    quantity: 0,
                                    unitPrice: unitPrice,
                                    total: 0,
                                };
                            }

                            roomBuckets[bucketKey].quantity += quantity;
                            roomBuckets[bucketKey].total += total;
                            return;
                        }

                        if (line && line.MaSuDung) {
                            const usage = getRelation(line, 'suDung', 'su_dung');
                            const service = getRelation(usage, 'dichVu', 'dich_vu');
                            const metaParts = [];

                            if (usage && usage.ThoiGian) {
                                metaParts.push('Thời gian sử dụng: ' + formatDateTime(usage.ThoiGian));
                            }

                            if (line.MoTa) {
                                metaParts.push('Ghi chú: ' + String(line.MoTa));
                            }

                            otherItems.push({
                                categoryLabel: 'Dịch vụ',
                                categoryClass: 'service',
                                description: service && service.TenDV ? service.TenDV : 'Dịch vụ phát sinh',
                                meta: metaParts.join(' • ') || 'Chi phí dịch vụ',
                                quantity: quantity,
                                unitPrice: unitPrice,
                                total: total,
                            });
                            return;
                        }

                        if (line && line.MaDenBu) {
                            const compensation = getRelation(line, 'denBu', 'den_bu');
                            const metaParts = [];

                            if (compensation && compensation.MoTa) {
                                metaParts.push('Mô tả: ' + String(compensation.MoTa));
                            }

                            if (line.MoTa) {
                                metaParts.push('Ghi chú: ' + String(line.MoTa));
                            }

                            otherItems.push({
                                categoryLabel: 'Đền bù',
                                categoryClass: 'compensation',
                                description: 'Khoản đền bù / hư hỏng',
                                meta: metaParts.join(' • ') || 'Phát sinh trong quá trình lưu trú',
                                quantity: quantity,
                                unitPrice: unitPrice,
                                total: total,
                            });
                            return;
                        }

                        otherItems.push({
                            categoryLabel: 'Khác',
                            categoryClass: 'other',
                            description: line && line.MoTa ? String(line.MoTa) : 'Hạng mục khác',
                            meta: 'Chi tiết hóa đơn',
                            quantity: quantity,
                            unitPrice: unitPrice,
                            total: total,
                        });
                    });

                    return Object.keys(roomBuckets).map(function (key) {
                        return roomBuckets[key];
                    }).concat(otherItems);
                };

                const buildServiceItems = function (invoice) {
                    return getArrayRelation(invoice, 'chiTietHoaDons', 'chi_tiet_hoa_dons')
                        .filter(function (line) {
                            return line && line.MaSuDung;
                        })
                        .map(function (line) {
                            const usage = getRelation(line, 'suDung', 'su_dung');
                            const service = getRelation(usage, 'dichVu', 'dich_vu');
                            const quantity = Math.max(1, Number(line.SoLuong || 1));
                            const unitPrice = Number(line.DonGia || 0);

                            return {
                                id: line.MaCTHD || '--',
                                usageId: line.MaSuDung || '--',
                                name: service && service.TenDV ? service.TenDV : 'Dịch vụ phát sinh',
                                serviceType: mapServiceType(service),
                                usedAt: usage && usage.ThoiGian ? formatDateTime(usage.ThoiGian) : '--',
                                quantity: quantity,
                                unitPrice: unitPrice,
                                total: quantity * unitPrice,
                                note: line.MoTa || '--',
                            };
                        });
                };

                const buildCompensationItems = function (invoice) {
                    return getArrayRelation(invoice, 'chiTietHoaDons', 'chi_tiet_hoa_dons')
                        .filter(function (line) {
                            return line && line.MaDenBu;
                        })
                        .map(function (line) {
                            const compensation = getRelation(line, 'denBu', 'den_bu');
                            const quantity = Math.max(1, Number(line.SoLuong || 1));
                            const unitPrice = Number(line.DonGia || 0);

                            return {
                                id: line.MaCTHD || '--',
                                compensationId: line.MaDenBu || '--',
                                description: compensation && compensation.MoTa ? compensation.MoTa : 'Khoản đền bù / hư hỏng',
                                quantity: quantity,
                                unitPrice: unitPrice,
                                total: quantity * unitPrice,
                                note: line.MoTa || '--',
                            };
                        });
                };

                const renderRoomTags = function (invoice) {
                    const detailLines = getArrayRelation(invoice, 'chiTietHoaDons', 'chi_tiet_hoa_dons');
                    const container = document.getElementById('invoice-room-tags');
                    const roomTypeNames = [];

                    detailLines.forEach(function (line) {
                        if (!line || !line.MaLoaiPhong || line.MaSuDung || line.MaDenBu) {
                            return;
                        }

                        const roomType = getRelation(line, 'loaiPhong', 'loai_phong');
                        const roomTypeName = roomType && roomType.TenLoaiPhong ? String(roomType.TenLoaiPhong) : 'Phòng';

                        if (!roomTypeNames.includes(roomTypeName)) {
                            roomTypeNames.push(roomTypeName);
                        }
                    });

                    if (container) {
                        if (!roomTypeNames.length) {
                            container.innerHTML = '<span class="hm-invoice-room-tag">Chưa có thông tin loại phòng</span>';
                        } else {
                            container.innerHTML = roomTypeNames.map(function (name) {
                                return '<span class="hm-invoice-room-tag">' + escapeHtml(name) + '</span>';
                            }).join('');
                        }
                    }

                    return roomTypeNames;
                };

                const renderLineItems = function (items) {
                    const tableBody = document.getElementById('invoice-line-items');

                    setText('invoice-line-count', items.length + ' dòng');
                    setText('invoice-summary-lines', String(items.length));

                    if (!tableBody) {
                        return;
                    }

                    if (!items.length) {
                        tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Hóa đơn chưa có dòng chi tiết.</td></tr>';
                        return;
                    }

                    tableBody.innerHTML = items.map(function (item) {
                        return '<tr>' +
                            '<td><span class="hm-invoice-chip hm-invoice-chip--' + escapeHtml(item.categoryClass) + '">' + escapeHtml(item.categoryLabel) + '</span></td>' +
                            '<td>' +
                                '<p class="hm-invoice-item-title">' + escapeHtml(item.description) + '</p>' +
                                '<div class="hm-invoice-item-meta">' + escapeHtml(item.meta || '--') + '</div>' +
                            '</td>' +
                            '<td class="hm-invoice-num">' + escapeHtml(String(item.quantity)) + '</td>' +
                            '<td class="hm-invoice-num">' + escapeHtml(formatCurrency(item.unitPrice)) + '</td>' +
                            '<td class="hm-invoice-num">' + escapeHtml(formatCurrency(item.total)) + '</td>' +
                        '</tr>';
                    }).join('');
                };

                const renderServiceItems = function (items) {
                    const container = document.getElementById('invoice-service-items');
                    setText('invoice-service-count', items.length + ' dịch vụ');

                    if (!container) {
                        return;
                    }

                    if (!items.length) {
                        container.innerHTML = '<div class="hm-invoice-empty">Chưa có dịch vụ sử dụng.</div>';
                        return;
                    }

                    container.innerHTML = items.map(function (item) {
                        return '<div class="hm-invoice-list-item">' +
                            '<div class="hm-invoice-list-head">' +
                                '<div>' +
                                    '<p class="hm-invoice-list-title">' + escapeHtml(item.name) + '</p>' +
                                    '<div class="hm-invoice-item-meta">Mã sử dụng: ' + escapeHtml(String(item.usageId)) + ' · Loại: ' + escapeHtml(item.serviceType) + '</div>' +
                                '</div>' +
                                '<div class="hm-invoice-list-total">' + escapeHtml(formatCurrency(item.total)) + '</div>' +
                            '</div>' +
                            '<div class="hm-invoice-list-meta">' +
                                '<div>Thời gian: ' + escapeHtml(item.usedAt) + '</div>' +
                                '<div>Số lượng: ' + escapeHtml(String(item.quantity)) + ' · Đơn giá: ' + escapeHtml(formatCurrency(item.unitPrice)) + '</div>' +
                                '<div>Ghi chú: ' + escapeHtml(item.note) + '</div>' +
                            '</div>' +
                        '</div>';
                    }).join('');
                };

                const renderCompensationItems = function (items) {
                    const container = document.getElementById('invoice-compensation-items');
                    setText('invoice-compensation-count', items.length + ' khoản');

                    if (!container) {
                        return;
                    }

                    if (!items.length) {
                        container.innerHTML = '<div class="hm-invoice-empty">Chưa có khoản đền bù phát sinh.</div>';
                        return;
                    }

                    container.innerHTML = items.map(function (item) {
                        return '<div class="hm-invoice-list-item">' +
                            '<div class="hm-invoice-list-head">' +
                                '<div>' +
                                    '<p class="hm-invoice-list-title">' + escapeHtml(item.description) + '</p>' +
                                    '<div class="hm-invoice-item-meta">Mã đền bù: ' + escapeHtml(String(item.compensationId)) + ' · Mã chi tiết: ' + escapeHtml(String(item.id)) + '</div>' +
                                '</div>' +
                                '<div class="hm-invoice-list-total">' + escapeHtml(formatCurrency(item.total)) + '</div>' +
                            '</div>' +
                            '<div class="hm-invoice-list-meta">' +
                                '<div>Số lượng: ' + escapeHtml(String(item.quantity)) + ' · Đơn giá: ' + escapeHtml(formatCurrency(item.unitPrice)) + '</div>' +
                                '<div>Ghi chú: ' + escapeHtml(item.note) + '</div>' +
                            '</div>' +
                        '</div>';
                    }).join('');
                };

                const renderInvoice = function (payload) {
                    const invoice = payload && payload.hoaDon ? payload.hoaDon : null;

                    if (!invoice) {
                        throw new Error('Không tìm thấy hóa đơn.');
                    }

                    const booking = getRelation(invoice, 'datPhong', 'dat_phong');
                    const employee = getRelation(invoice, 'nhanVien', 'nhan_vien');
                    const promotion = getRelation(invoice, 'khuyenMai', 'khuyen_mai');
                    const lineItems = buildLineItems(invoice);
                    const serviceItems = buildServiceItems(invoice);
                    const compensationItems = buildCompensationItems(invoice);
                    const roomTypeNames = renderRoomTags(invoice);
                    const subtotal = lineItems.reduce(function (sum, item) {
                        return sum + Number(item.total || 0);
                    }, 0);
                    const roomSubtotal = lineItems.reduce(function (sum, item) {
                        return item.categoryClass === 'room' ? sum + Number(item.total || 0) : sum;
                    }, 0);
                    const serviceSubtotal = lineItems.reduce(function (sum, item) {
                        return item.categoryClass === 'service' ? sum + Number(item.total || 0) : sum;
                    }, 0);
                    const compensationSubtotal = lineItems.reduce(function (sum, item) {
                        return item.categoryClass === 'compensation' ? sum + Number(item.total || 0) : sum;
                    }, 0);
                    const grandTotal = Number(payload.TongTien !== undefined ? payload.TongTien : (invoice.TongTien || 0));
                    const paidTotal = Number(payload.DaThanhToan || 0);
                    const remainingTotal = Number(payload.ConNo !== undefined ? payload.ConNo : (grandTotal - paidTotal));
                    const discountValue = Math.max(0, subtotal - grandTotal);
                    const status = mapInvoiceStatus(invoice.TrangThai);

                    clearAlert();

                    setText('invoice-info-id', invoice.MaHD || '--');
                    setText('invoice-info-booking', booking && booking.MaDatPhong ? booking.MaDatPhong : '--');
                    setText('invoice-info-date', formatDate(invoice.NgayLapHD));
                    setText('invoice-info-employee', employee && employee.TenNV ? employee.TenNV : '--');

                    setText('invoice-check-in', booking ? formatDate(booking.NgayNhanPhong) : '--');
                    setText('invoice-check-out', booking ? formatDate(booking.NgayTraPhong) : '--');
                    setText('invoice-stay-duration', booking ? getNightCount(booking.NgayNhanPhong, booking.NgayTraPhong) : '--');
                    setText(
                        'invoice-promotion',
                        promotion && promotion.TenKM
                            ? promotion.TenKM + (promotion.PhanTramGiamGia ? ' (' + Number(promotion.PhanTramGiamGia) + '%)' : '')
                            : 'Không áp dụng'
                    );

                    renderLineItems(lineItems);
                    renderServiceItems(serviceItems);
                    renderCompensationItems(compensationItems);

                    setText('invoice-subtotal', formatCurrency(subtotal));
                    setText('invoice-room-subtotal', formatCurrency(roomSubtotal));
                    setText('invoice-service-subtotal', formatCurrency(serviceSubtotal));
                    setText('invoice-compensation-subtotal', formatCurrency(compensationSubtotal));
                    setText('invoice-discount', discountValue > 0 ? '- ' + formatCurrency(discountValue) : '0 VNĐ');
                    setText('invoice-paid-total', formatCurrency(paidTotal));
                    setText('invoice-remaining-total', formatCurrency(remainingTotal));
                    setText(
                        'invoice-summary-note',
                        remainingTotal > 0
                            ? 'Khách vẫn còn công nợ ' + formatCurrency(remainingTotal) + '. Kiểm tra kỹ các dịch vụ và khoản đền bù trước khi chốt.'
                            : 'Khoản thanh toán đã khớp với tổng hóa đơn. Có thể dùng màn hình này để đối soát dịch vụ và đền bù.'
                    );

                    const statusBadge = document.getElementById('invoice-status-badge');
                    if (statusBadge) {
                        statusBadge.className = 'hm-invoice-status hm-invoice-status--' + status.className;
                        statusBadge.textContent = status.label;
                    }
                };

                try {
                    clearAlert();

                    const response = await fetch('/api/hoa-don/' + invoiceId, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Không thể tải chi tiết hóa đơn.');
                    }

                    const payload = await response.json();
                    renderInvoice(payload);
                } catch (error) {
                    setAlert('danger', error.message);

                    const statusBadge = document.getElementById('invoice-status-badge');
                    if (statusBadge) {
                        statusBadge.className = 'hm-invoice-status hm-invoice-status--danger';
                        statusBadge.textContent = 'Lỗi tải dữ liệu';
                    }

                    setText('invoice-summary-note', error.message);
                    document.getElementById('invoice-line-items').innerHTML =
                        '<tr><td colspan="5" class="text-center text-danger py-4">' + escapeHtml(error.message) + '</td></tr>';
                    document.getElementById('invoice-service-items').innerHTML =
                        '<div class="hm-invoice-empty">' + escapeHtml(error.message) + '</div>';
                    document.getElementById('invoice-compensation-items').innerHTML =
                        '<div class="hm-invoice-empty">' + escapeHtml(error.message) + '</div>';
                }
            });
        </script>
    @endpush
</x-hotel-management.show-page>
