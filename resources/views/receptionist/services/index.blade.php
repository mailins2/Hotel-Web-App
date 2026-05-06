<x-app-layout :assets="['animation']">
    <style>
        .rs-shell { padding-top: 4.5rem; }
        .rs-hero, .rs-card {
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
        .rs-card { padding: 1.4rem; }
        .rs-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        .rs-add-button {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
        }
        .rs-add-button svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }
        .rs-dialog {
            width: min(640px, calc(100vw - 2rem));
            border: none;
            border-radius: 24px;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 28px 70px rgba(73, 18, 15, 0.22);
        }
        .rs-dialog::backdrop {
            background: rgba(73, 18, 15, 0.28);
            backdrop-filter: blur(2px);
        }
        .rs-dialog-body {
            padding: 1.5rem;
            background: linear-gradient(180deg, #fffaf3 0%, #fff 100%);
        }
        .rs-dialog-title {
            margin: 0 0 1rem;
            color: #6f1d01;
            font-size: 1.35rem;
            font-weight: 700;
        }
        .rs-dialog-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1rem;
        }
        .rs-dialog-actions .btn {
            flex: 1 1 180px;
        }
    </style>

    <div class="rs-shell">
        <div class="rs-hero">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="mb-2">Dịch vụ</h1>
                    <p class="text-muted mb-0">Danh sách dịch vụ đang sử dụng theo từng đặt phòng</p>
                </div>
                <div class="d-flex gap-2">
                    <button id="openServiceDialogButton" type="button" class="btn btn-primary rs-add-button">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 5V19" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                            <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                        </svg>
                        Thêm dịch vụ
                    </button>
                </div>
            </div>
        </div>

        <div class="rs-card">
            <div class="rs-toolbar">
                <h5 class="mb-0">Danh sách dịch vụ đang sử dụng</h5>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Mã sử dụng</th>
                            <th>Tên khách hàng</th>
                            <th>Mã dịch vụ</th>
                            <th>Tên dịch vụ</th>
                            <th>Loại dịch vụ</th>
                            <th>Số lượng</th>
                            <th>Thời gian</th>
                            <th style="min-width: 120px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>SD001</td>
                            <td>Trần Bảo Ngọc</td>
                            <td>DV010</td>
                            <td>Mini bar</td>
                            <td>Dịch vụ ăn uống</td>
                            <td>2</td>
                            <td>07/04/2026 18:20</td>
                            <td>
                                @include('hotel-management.partials.action-icons', [
                                    'showUrl' => route('reception.services.show', ['serviceUsageId' => 'SD001']),
                                    'editUrl' => null,
                                    'showDelete' => false,
                                ])
                            </td>
                        </tr>
                        <tr>
                            <td>SD002</td>
                            <td>Trần Bảo Ngọc</td>
                            <td>DV021</td>
                            <td>Nước suối Evian</td>
                            <td>Dịch vụ ăn uống</td>
                            <td>1</td>
                            <td>08/04/2026 09:15</td>
                            <td>
                                @include('hotel-management.partials.action-icons', [
                                    'showUrl' => route('reception.services.show', ['serviceUsageId' => 'SD002']),
                                    'editUrl' => null,
                                    'showDelete' => false,
                                ])
                            </td>
                        </tr>
                        <tr> 
                            <td>SD003</td>
                            <td>Đỗ Thanh Tùng</td>
                            <td>DV018</td>
                            <td>Giặt ủi</td>
                            <td>Dịch vụ phòng</td>
                            <td>3</td>
                            <td>09/04/2026 20:45</td>
                            <td>
                                @include('hotel-management.partials.action-icons', [
                                    'showUrl' => route('reception.services.show', ['serviceUsageId' => 'SD003']),
                                    'editUrl' => null,
                                    'showDelete' => false,
                                ])
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <dialog id="serviceRegisterDialog" class="rs-dialog">
        <div class="rs-dialog-body">
            <h3 class="rs-dialog-title">Đăng ký dịch vụ</h3>
            <form data-ui-only-form>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Mã sử dụng</label>
                        <input type="text" class="form-control" value="SD004">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mã đặt phòng</label>
                        <input type="text" class="form-control" value="9005">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Loại dịch vụ</label>
                        <select class="form-select">
                            <option>Dịch vụ ăn uống</option>
                            <option>Dịch vụ phòng</option>
                            <option>Dịch vụ giải trí</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tên dịch vụ</label>
                        <select class="form-select">
                            <option>Bún bò</option>
                            <option>Cafe sữa</option>
                            <option>Cơm tấm</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Số lượng</label>
                        <input type="number" class="form-control" value="1" min="1">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ngày sử dụng</label>
                        <input type="date" class="form-control" value="2026-04-09">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Giờ sử dụng</label>
                        <input type="text" class="form-control" value="20:45" placeholder="HH:MM">
                    </div>
                </div>

                <div class="rs-dialog-actions">
                    <button type="submit" class="btn btn-primary">Lưu đăng ký</button>
                    <button id="closeServiceDialogButton" type="button" class="btn btn-light">Đóng</button>
                </div>
            </form>
        </div>
    </dialog>

    <script>
        const openServiceDialogButton = document.getElementById('openServiceDialogButton');
        const closeServiceDialogButton = document.getElementById('closeServiceDialogButton');
        const serviceRegisterDialog = document.getElementById('serviceRegisterDialog');

        openServiceDialogButton.addEventListener('click', () => {
            serviceRegisterDialog.showModal();
        });

        closeServiceDialogButton.addEventListener('click', () => {
            serviceRegisterDialog.close();
        });

        document.querySelector('[data-ui-only-form]')?.addEventListener('submit', (event) => {
            event.preventDefault();
            serviceRegisterDialog.close();
        });
    </script>
</x-app-layout>
