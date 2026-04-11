<?php

namespace App\Http\Controllers;

use App\Models\ChiTietDatPhong;
use App\Models\DatPhong;
use App\Models\KhachHang;
use App\Models\LoaiPhong;
use App\Models\Phong;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class ReceptionistController extends Controller
{
    public function dashboard(): View
    {
        $this->requireReceptionistRole();

        $customers = collect(config('hotel-management.modules.customers.records', []));
        $rooms = $this->roomRecords();
        $bookings = $this->bookingRecords();
        $invoices = $this->invoiceRecords();
        $today = now()->toDateString();
        $roomStatusPalette = $this->roomStatusPalette();

        $arrivalsToday = $bookings->where('NgayNhanPhong', $today)->values();
        $departuresToday = $bookings->where('NgayTraPhong', $today)->values();
        $pendingInvoices = $invoices->where('TrangThai', 0)->values();
        $occupiedRooms = $rooms->where('TinhTrang', 2)->count();
        return view('receptionist.dashboard', [
            'assets' => ['animation'],
            'brand' => config('hotel-management.reception.brand', []),
            'summaryCards' => [
                ['label' => 'Đặt phòng', 'tone' => 'sunrise', 'icon' => 'booking'],
                ['label' => 'Nhận phòng', 'tone' => 'teal', 'icon' => 'check-in'],
                ['label' => 'Trả phòng', 'tone' => 'amber', 'icon' => 'check-out'],
                ['label' => 'Đổi phòng', 'tone' => 'slate', 'icon' => 'switch-room'],
            ],
            'arrivalsToday' => $arrivalsToday->take(4)->all(),
            'departuresToday' => $departuresToday->take(4)->all(),
            'pendingInvoices' => $pendingInvoices
                ->map(fn (array $invoice) => array_merge($invoice, [
                    'ConLai' => max(((float) ($invoice['TongTien'] ?? 0)) - ((float) ($invoice['DaThanhToan'] ?? 0)), 0),
                ]))
                ->take(4)
                ->all(),
            'roomStatus' => collect($roomStatusPalette)
                ->map(fn (array $status, string $code) => [
                    'label' => $status['short_label'],
                    'value' => $rooms->where('TinhTrang', (int) $code)->count(),
                    'tone' => $status['tone'],
                ])
                ->values()
                ->all(),
            'roomMapFloors' => $this->buildRoomMapFloors($rooms, $roomStatusPalette),
            'customerHighlights' => [
                'active' => $customers->where('TrangThai', 1)->count(),
                'vip' => $customers->filter(fn (array $customer) => (int) ($customer['Diem'] ?? 0) >= 100)->count(),
                'new_today' => $arrivalsToday->count(),
            ],
        ]);
    }

    public function customers(Request $request): View
    {
        $this->requireReceptionistRole();

        $customers = $this->customerRecords()->map(function (array $customer) {
            $customer['GioiTinhText'] = $this->customerGenderLabel($customer['GioiTinh'] ?? null);

            return $customer;
        });

        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');

        if ($search !== '') {
            $searchValue = mb_strtolower($search);
            $customers = $customers->filter(function (array $customer) use ($searchValue) {
                $haystacks = [
                    $customer['TenKH'] ?? '',
                    $customer['SoDienThoai'] ?? '',
                    $customer['CCCD'] ?? '',
                    $customer['DiaChi'] ?? '',
                ];

                foreach ($haystacks as $value) {
                    if (str_contains(mb_strtolower((string) $value), $searchValue)) {
                        return true;
                    }
                }

                return false;
            });
        }

        if ($status !== '') {
            $customers = $customers->where('TrangThai', (int) $status);
        }

        $allCustomers = $this->customerRecords();

        return view('receptionist.list', [
            'assets' => ['animation'],
            'page' => [
                'eyebrow' => 'Front Desk',
                'hide_eyebrow' => true,
                'hide_demo_note' => true,
                'hide_toolbar_intro' => true,
                'hide_summary_cards' => true,
                'title' => 'Quản lý khách hàng',
                'description' => 'Theo dõi hồ sơ lưu trú của khách hàng tại khách sạn',
                'create_button' => [
                    'label' => 'Thêm khách hàng',
                    'url' => route('reception.customers.create'),
                ],
                'summary_cards' => [
                    ['label' => 'Khách đang hoạt động', 'value' => $allCustomers->where('TrangThai', 1)->count(), 'tone' => 'sunrise'],
                    ['label' => 'Khách VIP', 'value' => $allCustomers->filter(fn (array $customer) => (int) ($customer['Diem'] ?? 0) >= 100)->count(), 'tone' => 'amber'],
                    ['label' => 'Cần liên hệ lại', 'value' => $allCustomers->where('TrangThai', 0)->count(), 'tone' => 'slate'],
                ],
                'filters' => [
                    [
                        'key' => 'search',
                        'label' => 'Tìm nhanh',
                        'type' => 'text',
                        'placeholder' => 'Tên khách, CCCD, số điện thoại...',
                    ],
                    [
                        'key' => 'status',
                        'label' => 'Trạng thái',
                        'type' => 'select',
                        'options' => [
                            '' => 'Tất cả trạng thái',
                            '1' => 'Hoạt động',
                            '0' => 'Không hoạt động',
                        ],
                    ],
                ],
                'columns' => [
                    ['key' => 'MaKH', 'label' => 'Mã KH'],
                    ['key' => 'TenKH', 'label' => 'Khách hàng'],
                    ['key' => 'DiaChi', 'label' => 'Địa chỉ'],
                    ['key' => 'SoDienThoai', 'label' => 'Điện thoại'],
                    ['key' => 'CCCD', 'label' => 'CCCD'],
                    ['key' => 'NgaySinh', 'label' => 'Ngày sinh'],
                    ['key' => 'GioiTinhText', 'label' => 'Giới tính'],
                    ['key' => 'Diem', 'label' => 'Điểm'],
                    ['key' => 'TrangThai', 'label' => 'Trạng thái'],
                ],
                'row_actions' => [
                    'primary_key' => 'MaKH',
                    'parameter_name' => 'customerId',
                    'show_route' => 'reception.customers.show',
                    'edit_route' => 'reception.customers.edit',
                ],
                'rows' => $customers->values()->all(),
                'empty_text' => 'Không có khách hàng nào khớp bộ lọc đang chọn.',
                'table_note' => 'Dữ liệu đang ở chế độ demo, phù hợp để dựng giao diện và trình diễn luồng tiếp nhận khách.',
                'badge_maps' => [
                    'TrangThai' => [
                        '1' => ['label' => 'Hoạt động', 'class' => 'rd-badge rd-badge--success'],
                        '0' => ['label' => 'Không hoạt động', 'class' => 'rd-badge rd-badge--muted'],
                    ],
                ],
            ],
        ]);
    }

    public function customerShow(string $customerId): View
    {
        $this->requireReceptionistRole();

        $module = $this->customerModule();
        $record = $this->findCustomerRecord($customerId);

        return view('hotel-management.show', [
            'assets' => ['animation'],
            'moduleKey' => 'customers',
            'module' => $module,
            'record' => $record,
            'backUrl' => route('reception.customers.index'),
            'editUrl' => route('reception.customers.edit', ['customerId' => $record['MaKH']]),
        ]);
    }

    public function customerCreate(): View
    {
        $this->requireReceptionistRole();

        return view('hotel-management.form', [
            'assets' => ['animation'],
            'moduleKey' => 'customers',
            'module' => $this->customerModule(),
            'record' => [],
            'isEdit' => false,
            'formAction' => route('reception.customers.store'),
            'backUrl' => route('reception.customers.index'),
        ]);
    }

    public function customerEdit(string $customerId): View
    {
        $this->requireReceptionistRole();

        $record = $this->findCustomerRecord($customerId);

        return view('hotel-management.form', [
            'assets' => ['animation'],
            'moduleKey' => 'customers',
            'module' => $this->customerModule(),
            'record' => $record,
            'isEdit' => true,
            'formAction' => route('reception.customers.update', ['customerId' => $record['MaKH']]),
            'backUrl' => route('reception.customers.index'),
        ]);
    }

    public function customerStore(Request $request): RedirectResponse
    {
        $this->requireReceptionistRole();

        $this->validateCustomerRequest($request);

        return redirect()
            ->route('reception.customers.index')
            ->with('success', 'Đã tạo khách hàng trong giao diện mẫu.');
    }

    public function customerUpdate(Request $request, string $customerId): RedirectResponse
    {
        $this->requireReceptionistRole();

        $record = $this->findCustomerRecord($customerId);
        $this->validateCustomerRequest($request);

        return redirect()
            ->route('reception.customers.show', ['customerId' => $record['MaKH']])
            ->with('success', 'Đã cập nhật khách hàng trong giao diện mẫu.');
    }

    public function bookings(Request $request): View
    {
        $this->requireReceptionistRole();

        $bookings = $this->bookingRecords();
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));

        if ($search !== '') {
            $searchValue = mb_strtolower($search);
            $bookings = $bookings->filter(function (array $booking) use ($searchValue) {
                $haystacks = [
                    $booking['MaDatPhong'] ?? '',
                    $booking['MaKH'] ?? '',
                    $booking['TenKH'] ?? '',
                    $booking['SoPhong'] ?? '',
                    $booking['SoDienThoai'] ?? '',
                ];

                foreach ($haystacks as $value) {
                    if (str_contains(mb_strtolower((string) $value), $searchValue)) {
                        return true;
                    }
                }

                return false;
            });
        }

        if ($status !== '') {
            $bookings = $bookings->where('TinhTrang', (int) $status);
        }

        $allBookings = $this->bookingRecords();

        return view('receptionist.list', [
            'assets' => ['animation'],
            'page' => [
                'eyebrow' => 'Front Desk',
                'hide_eyebrow' => true,
                'hide_demo_note' => true,
                'hide_toolbar_intro' => true,
                'title' => 'Quản lý đặt phòng',
                'description' => 'Quản lý thông tin đặt phòng của khách hàng tại khách sạn',
                'create_button' => [
                    'label' => 'Thêm đặt phòng',
                    'url' => route('reception.bookings.create'),
                ],
                'summary_cards' => [
                    ['label' => 'ĐÃ ĐẶT', 'value' => $allBookings->where('TinhTrang', 0)->count(), 'tone' => 'sunrise'],
                    ['label' => 'ĐANG SỬ DỤNG', 'value' => $allBookings->where('TinhTrang', 1)->count(), 'tone' => 'teal'],
                    ['label' => 'ĐÃ TRẢ PHÒNG', 'value' => $allBookings->where('TinhTrang', 3)->count(), 'tone' => 'amber'],
                ],
                'filters' => [
                    [
                        'key' => 'search',
                        'label' => 'Tìm booking',
                        'type' => 'text',
                        'placeholder' => 'Mã đặt phòng, mã khách hàng, tên khách...',
                    ],
                    [
                        'key' => 'status',
                        'label' => 'Trạng thái',
                        'type' => 'select',
                        'options' => [
                            '' => 'Tất cả trạng thái',
                            '0' => 'Đã đặt',
                            '1' => 'Đang sử dụng',
                            '2' => 'Đã hủy',
                            '3' => 'Đã trả phòng',
                        ],
                    ],
                ],
                'columns' => [
                    ['key' => 'MaDatPhong', 'label' => 'Mã đặt'],
                    ['key' => 'MaKH', 'label' => 'Mã KH'],
                    ['key' => 'TenKH', 'label' => 'Khách hàng'],
                    ['key' => 'SoPhong', 'label' => 'Phòng'],
                    ['key' => 'LoaiPhong', 'label' => 'Loại phòng'],
                    ['key' => 'NgayDat', 'label' => 'Ngày đặt'],
                    ['key' => 'NgayNhanPhong', 'label' => 'Nhận phòng'],
                    ['key' => 'NgayTraPhong', 'label' => 'Trả phòng'],
                    ['key' => 'SoLuong', 'label' => 'Số lượng người ở'],
                    ['key' => 'TinhTrang', 'label' => 'Trạng thái'],
                ],
                'row_actions' => [
                    'primary_key' => 'MaDatPhong',
                    'parameter_name' => 'bookingId',
                    'show_route' => 'reception.bookings.show',
                    'edit_route' => 'reception.bookings.edit',
                ],
                'rows' => $bookings->values()->all(),
                'empty_text' => 'Không tìm thấy booking phù hợp với từ khóa hoặc trạng thái đã chọn.',
                'table_note' => 'Bạn có thể thay bộ dữ liệu demo này bằng bảng DatPhong thật sau mà không phải đổi lại cấu trúc giao diện.',
                'badge_maps' => [
                    'TinhTrang' => [
                        '0' => ['label' => 'Đã đặt', 'class' => 'rd-badge rd-badge--warning'],
                        '1' => ['label' => 'Đang sử dụng', 'class' => 'rd-badge rd-badge--success'],
                        '2' => ['label' => 'Đã hủy', 'class' => 'rd-badge rd-badge--danger'],
                        '3' => ['label' => 'Đã trả phòng', 'class' => 'rd-badge rd-badge--muted'],
                    ],
                ],
            ],
        ]);
    }

    public function bookingShow(string $bookingId): View
    {
        $this->requireReceptionistRole();

        $record = $this->findBookingRecord($bookingId);

        return view('hotel-management.show', [
            'assets' => ['animation'],
            'moduleKey' => 'reception-bookings',
            'module' => $this->bookingModule(),
            'record' => $record,
            'backUrl' => route('reception.bookings.index'),
            'editUrl' => route('reception.bookings.edit', ['bookingId' => $record['MaDatPhong']]),
        ]);
    }

    public function bookingCreate(Request $request): View
    {
        $this->requireReceptionistRole();

        return view('receptionist.booking-form', [
            'assets' => ['animation'],
            'page' => $this->bookingPagePayload($request),
        ]);
    }

    public function bookingEdit(string $bookingId): View
    {
        $this->requireReceptionistRole();

        $record = $this->findBookingRecord($bookingId);

        return view('hotel-management.form', [
            'assets' => ['animation'],
            'moduleKey' => 'reception-bookings',
            'module' => $this->bookingModule(),
            'record' => $record,
            'isEdit' => true,
            'formAction' => route('reception.bookings.update', ['bookingId' => $record['MaDatPhong']]),
            'backUrl' => route('reception.bookings.index'),
        ]);
    }

    public function bookingStore(Request $request): RedirectResponse
    {
        $this->requireReceptionistRole();

        $validated = $this->validateBookingCreationRequest($request);
        $booking = null;

        DB::transaction(function () use ($validated, &$booking) {
            $roomIds = collect($validated['room_ids'])
                ->map(fn (mixed $roomId) => (int) $roomId)
                ->unique()
                ->values();

            $availableRoomCount = Phong::query()
                ->whereIn('MaPhong', $roomIds)
                ->where('TinhTrang', 0)
                ->count();

            if ($availableRoomCount !== $roomIds->count()) {
                throw ValidationException::withMessages([
                    'room_ids' => 'Một hoặc nhiều phòng vừa được cập nhật trạng thái. Vui lòng chọn lại phòng còn trống.',
                ]);
            }

            $selectedRooms = Phong::query()
                ->with('loaiPhong')
                ->whereIn('MaPhong', $roomIds)
                ->get();

            $totalCapacity = $selectedRooms->sum(
                fn (Phong $room) => (int) ($room->loaiPhong?->SoNguoiToiDa ?? 0)
            );

            if ($totalCapacity < (int) $validated['SoLuong']) {
                throw ValidationException::withMessages([
                    'SoLuong' => 'Số lượng người ở vượt quá sức chứa của các phòng đã chọn.',
                ]);
            }

            $booking = DatPhong::query()->create([
                'MaKH' => (int) $validated['MaKH'],
                'NgayDat' => $validated['NgayDat'],
                'NgayNhanPhong' => $validated['NgayNhanPhong'],
                'NgayTraPhong' => $validated['NgayTraPhong'],
                'SoLuong' => (int) $validated['SoLuong'],
                'TinhTrang' => 0,
            ]);

            $roomIds->each(function (int $roomId) use ($booking) {
                ChiTietDatPhong::query()->create([
                    'MaDatPhong' => $booking->MaDatPhong,
                    'MaPhong' => $roomId,
                ]);
            });

            Phong::query()
                ->whereIn('MaPhong', $roomIds)
                ->update(['TinhTrang' => 1]);
        });

        return redirect()
            ->route('reception.bookings.create', ['created' => $booking?->MaDatPhong])
            ->with('success', 'Đã tạo đặt phòng thành công và giữ phòng theo booking mới.');
    }

    public function checkInCreate(Request $request): View
    {
        $this->requireReceptionistRole();

        return view('receptionist.check-in-form', [
            'assets' => ['animation'],
            'page' => $this->checkInPagePayload($request),
        ]);
    }

    public function checkInStore(Request $request): RedirectResponse
    {
        $this->requireReceptionistRole();

        $validated = $request->validate([
            'booking_id' => ['required', 'integer', Rule::exists('DatPhong', 'MaDatPhong')],
        ], [], [
            'booking_id' => 'booking nhận phòng',
        ]);

        $booking = DatPhong::query()
            ->with('chiTietDatPhongs')
            ->findOrFail((int) $validated['booking_id']);

        if ((int) $booking->TinhTrang !== 0) {
            throw ValidationException::withMessages([
                'booking_id' => 'Booking này không còn ở trạng thái chờ nhận phòng.',
            ]);
        }

        $roomIds = $booking->chiTietDatPhongs
            ->pluck('MaPhong')
            ->filter()
            ->map(fn (mixed $roomId) => (int) $roomId)
            ->unique()
            ->values();

        if ($roomIds->isEmpty()) {
            throw ValidationException::withMessages([
                'booking_id' => 'Booking chưa có phòng được gán, chưa thể nhận phòng.',
            ]);
        }

        DB::transaction(function () use ($booking, $roomIds) {
            $booking->update(['TinhTrang' => 1]);

            Phong::query()
                ->whereIn('MaPhong', $roomIds)
                ->update(['TinhTrang' => 2]);
        });

        return redirect()
            ->route('reception.check-ins.create', ['checked_in' => $booking->MaDatPhong])
            ->with('success', 'Đã xác nhận nhận phòng và cập nhật trạng thái các phòng liên quan.');
    }

    public function bookingUpdate(Request $request, string $bookingId): RedirectResponse
    {
        $this->requireReceptionistRole();

        $record = $this->findBookingRecord($bookingId);
        $this->validateBookingRequest($request);

        return redirect()
            ->route('reception.bookings.show', ['bookingId' => $record['MaDatPhong']])
            ->with('success', 'Đã cập nhật đặt phòng trong giao diện mẫu.');
    }

    public function invoices(Request $request): View
    {
        $this->requireReceptionistRole();

        $invoices = $this->invoiceRecords()->map(function (array $invoice) {
            $invoice['ConLai'] = max(((float) ($invoice['TongTien'] ?? 0)) - ((float) ($invoice['DaThanhToan'] ?? 0)), 0);

            return $invoice;
        });

        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));

        if ($search !== '') {
            $searchValue = mb_strtolower($search);
            $invoices = $invoices->filter(function (array $invoice) use ($searchValue) {
                $haystacks = [
                    $invoice['MaHD'] ?? '',
                    $invoice['MaDatPhong'] ?? '',
                    $invoice['TenNV'] ?? '',
                ];

                foreach ($haystacks as $value) {
                    if (str_contains(mb_strtolower((string) $value), $searchValue)) {
                        return true;
                    }
                }

                return false;
            });
        }

        if ($status !== '') {
            $invoices = $invoices->where('TrangThai', (int) $status);
        }

        $allInvoices = $this->invoiceRecords()->map(function (array $invoice) {
            $invoice['ConLai'] = max(((float) ($invoice['TongTien'] ?? 0)) - ((float) ($invoice['DaThanhToan'] ?? 0)), 0);

            return $invoice;
        });

        return view('receptionist.list', [
            'assets' => ['animation'],
            'page' => [
                'eyebrow' => 'Front Desk',
                'title' => 'Quản lý hóa đơn',
                'description' => 'Theo dõi công nợ còn lại, trạng thái thanh toán và ai đang phụ trách hóa đơn tại quầy lễ tân.',
                'summary_cards' => [
                    ['label' => 'Chưa thanh toán', 'value' => $allInvoices->where('TrangThai', 0)->count(), 'tone' => 'amber'],
                    ['label' => 'Đã thanh toán', 'value' => $allInvoices->where('TrangThai', 1)->count(), 'tone' => 'teal'],
                    ['label' => 'Công nợ còn lại', 'value' => $this->formatCurrency((float) $allInvoices->sum('ConLai')), 'tone' => 'slate'],
                ],
                'filters' => [
                    [
                        'key' => 'search',
                        'label' => 'Tìm hóa đơn',
                        'type' => 'text',
                        'placeholder' => 'Mã hóa đơn, mã đặt phòng, nhân viên...',
                    ],
                    [
                        'key' => 'status',
                        'label' => 'Thanh toán',
                        'type' => 'select',
                        'options' => [
                            '' => 'Tất cả trạng thái',
                            '0' => 'Chưa thanh toán',
                            '1' => 'Đã thanh toán',
                        ],
                    ],
                ],
                'columns' => [
                    ['key' => 'MaHD', 'label' => 'Mã HĐ'],
                    ['key' => 'MaDatPhong', 'label' => 'Mã đặt phòng'],
                    ['key' => 'NgayLapHD', 'label' => 'Ngày lập'],
                    ['key' => 'TenNV', 'label' => 'Nhân viên'],
                    ['key' => 'TongTien', 'label' => 'Tổng tiền'],
                    ['key' => 'DaThanhToan', 'label' => 'Đã thanh toán'],
                    ['key' => 'ConLai', 'label' => 'Còn lại'],
                    ['key' => 'TrangThai', 'label' => 'Trạng thái'],
                ],
                'rows' => $invoices->values()->all(),
                'empty_text' => 'Không có hóa đơn nào phù hợp với bộ lọc hiện tại.',
                'table_note' => 'Phần còn lại được tính trực tiếp từ dữ liệu mẫu: Tổng tiền trừ số đã thanh toán.',
                'badge_maps' => [
                    'TrangThai' => [
                        '0' => ['label' => 'Chưa thanh toán', 'class' => 'rd-badge rd-badge--warning'],
                        '1' => ['label' => 'Đã thanh toán', 'class' => 'rd-badge rd-badge--success'],
                    ],
                ],
            ],
        ]);
    }

    protected function bookingRecords(): Collection
    {
        try {
            $records = DatPhong::query()
                ->with(['khachHang', 'chiTietDatPhongs.phong.loaiPhong'])
                ->orderByDesc('MaDatPhong')
                ->get()
                ->map(fn (DatPhong $booking) => $this->mapDatabaseBooking($booking));

            if ($records->isNotEmpty()) {
                return $records;
            }
        } catch (Throwable $exception) {
            // Fall back to demo config when DB is unavailable in local/demo environments.
        }

        return collect(config('hotel-management.reception.bookings.records', []))
            ->map(fn (array $booking) => $this->mapConfigBooking($booking));
    }

    protected function findBookingRecord(string $bookingId): array
    {
        return $this->bookingRecords()
            ->first(fn (array $booking) => (string) ($booking['MaDatPhong'] ?? '') === (string) $bookingId)
            ?? abort(404);
    }

    protected function bookingModule(): array
    {
        $customerOptions = $this->customerRecords()
            ->mapWithKeys(fn (array $customer) => [
                (string) ($customer['MaKH'] ?? '') => trim(((string) ($customer['MaKH'] ?? '')) . ' - ' . ((string) ($customer['TenKH'] ?? ''))),
            ])
            ->all();

        return [
            'title' => 'Quản lý đặt phòng',
            'singular' => 'đặt phòng',
            'description' => 'Thông tin đặt phòng theo đúng cấu trúc bảng DatPhong trong CSDL.',
            'primary_key' => 'MaDatPhong',
            'fields' => [
                'MaDatPhong' => ['label' => 'Mã đặt phòng', 'type' => 'number', 'readonly' => true],
                'MaKH' => ['label' => 'Mã khách hàng', 'type' => 'select', 'options' => $customerOptions, 'required' => true],
                'TenKH' => ['label' => 'Khách hàng', 'type' => 'text', 'readonly' => true],
                'SoDienThoai' => ['label' => 'Số điện thoại', 'type' => 'text', 'readonly' => true],
                'NgayDat' => ['label' => 'Ngày đặt', 'type' => 'date', 'required' => true],
                'NgayNhanPhong' => ['label' => 'Ngày nhận phòng', 'type' => 'date', 'required' => true],
                'NgayTraPhong' => ['label' => 'Ngày trả phòng', 'type' => 'date', 'required' => true],
                'SoLuong' => ['label' => 'Số lượng người ở', 'type' => 'number', 'required' => true],
                'SoPhong' => ['label' => 'Phòng', 'type' => 'text', 'readonly' => true],
                'LoaiPhong' => ['label' => 'Loại phòng', 'type' => 'text', 'readonly' => true],
                'TinhTrang' => [
                    'label' => 'Trạng thái',
                    'type' => 'select',
                    'required' => true,
                    'options' => $this->bookingStatusOptions(),
                ],
            ],
        ];

        $rooms = $this->roomRecords();
        $customerOptions = $this->customerRecords()
            ->mapWithKeys(fn (array $customer) => [
                (string) ($customer['MaKH'] ?? '') => trim(((string) ($customer['MaKH'] ?? '')) . ' - ' . ((string) ($customer['TenKH'] ?? ''))),
            ])
            ->all();
        $roomOptions = $rooms
            ->mapWithKeys(fn (array $room) => [
                (string) ($room['SoPhong'] ?? '') => (string) ($room['SoPhong'] ?? ''),
            ])
            ->all();
        $roomTypeOptions = $rooms
            ->pluck('TenLoaiPhong', 'TenLoaiPhong')
            ->filter(fn (mixed $value) => is_string($value) && $value !== '')
            ->all();

        return [
            'title' => 'Quản lý đặt phòng',
            'singular' => 'đặt phòng',
            'description' => 'Tạo booking mới cho khách tại quầy lễ tân.',
            'primary_key' => 'MaDatPhong',
            'fields' => [
                'MaKH' => ['label' => 'Mã khách hàng', 'type' => 'select', 'options' => $customerOptions, 'required' => true],
                'TenKH' => ['label' => 'Khách hàng', 'type' => 'text', 'required' => true],
                'SoDienThoai' => ['label' => 'Số điện thoại', 'type' => 'text', 'required' => true],
                'SoPhong' => ['label' => 'Phòng', 'type' => 'select', 'options' => $roomOptions, 'required' => true],
                'LoaiPhong' => ['label' => 'Loại phòng', 'type' => 'select', 'options' => $roomTypeOptions, 'required' => true],
                'NgayNhanPhong' => ['label' => 'Nhận phòng', 'type' => 'date', 'required' => true],
                'NgayTraPhong' => ['label' => 'Trả phòng', 'type' => 'date', 'required' => true],
                'SoNguoi' => ['label' => 'Số người', 'type' => 'number', 'required' => true],
                'TienCoc' => ['label' => 'Tiền cọc', 'type' => 'number', 'required' => true],
                'TrangThai' => [
                    'label' => 'Trạng thái',
                    'type' => 'select',
                    'required' => true,
                    'options' => [
                        'da-dat' => 'Đã đặt',
                        'dang-su-dung' => 'Đang sử dụng',
                        'da-huy' => 'Đã hủy',
                        'da-tra-phong' => 'Đã trả phòng',
                    ],
                ],
            ],
        ];
    }

    protected function validateBookingRequest(Request $request): void
    {
        $request->validate([
            'MaKH' => ['required', 'numeric'],
            'NgayDat' => ['required', 'date'],
            'NgayNhanPhong' => ['required', 'date'],
            'NgayTraPhong' => ['required', 'date', 'after_or_equal:NgayNhanPhong'],
            'SoLuong' => ['required', 'integer', 'min:1'],
            'TinhTrang' => ['required', 'integer', Rule::in(array_map('intval', array_keys($this->bookingStatusOptions())))],
        ]);
    }

    protected function validateBookingCreationRequest(Request $request): array
    {
        return $request->validate([
            'MaKH' => ['required', 'integer', Rule::exists('KhachHang', 'MaKH')],
            'NgayDat' => ['required', 'date'],
            'NgayNhanPhong' => ['required', 'date', 'after_or_equal:NgayDat'],
            'NgayTraPhong' => ['required', 'date', 'after:NgayNhanPhong'],
            'SoLuong' => ['required', 'integer', 'min:1'],
            'room_ids' => ['required', 'array', 'min:1'],
            'room_ids.*' => ['required', 'integer', Rule::exists('Phong', 'MaPhong')],
        ], [], [
            'MaKH' => 'khách hàng',
            'NgayDat' => 'ngày đặt',
            'NgayNhanPhong' => 'ngày nhận phòng',
            'NgayTraPhong' => 'ngày trả phòng',
            'room_ids' => 'phòng',
            'room_ids.*' => 'phòng',
        ]);
    }

    protected function bookingStatusOptions(): array
    {
        return [
            0 => 'Đã đặt',
            1 => 'Đang sử dụng',
            2 => 'Đã hủy',
            3 => 'Đã trả phòng',
        ];
    }

    protected function bookingPagePayload(Request $request): array
    {
        $customers = $this->bookingCustomers();
        $rooms = $this->availableBookingRooms();
        $roomTypes = $this->bookingRoomTypes($rooms);
        $createdBooking = $this->databaseBookingSnapshot($request->query('created'));
        $pendingCheckIns = $this->pendingCheckInRecords();

        return [
            'title' => 'Đặt Phòng',
            'description' => 'Tạo đơn đặt phòng trực tiếp khi khách hàng đến đặt phòng tại lễ tân',
            'customers' => $customers->values()->all(),
            'rooms' => $rooms->values()->all(),
            'room_types' => $roomTypes->values()->all(),
            'created_booking' => $createdBooking,
            'stats' => [
                'customer_count' => $customers->count(),
                'available_room_count' => $rooms->count(),
                'pending_check_in_count' => $pendingCheckIns->count(),
            ],
            'defaults' => [
                'booking_date' => now()->toDateString(),
                'check_in_date' => now()->toDateString(),
                'check_out_date' => now()->addDay()->toDateString(),
            ],
        ];
    }

    protected function checkInPagePayload(Request $request): array
    {
        $pendingBookings = $this->pendingCheckInRecords();
        $selectedBookingId = (string) ($request->query('booking') ?: old('booking_id') ?: ($pendingBookings->first()['MaDatPhong'] ?? ''));

        return [
            'title' => 'Nhận Phòng',
            'description' => 'Xác nhận khách đến, chuyển booking sang trạng thái đang lưu trú và cập nhật phòng sang đang sử dụng.',
            'bookings' => $pendingBookings->values()->all(),
            'selected_booking_id' => $selectedBookingId,
            'checked_in_booking' => $this->databaseBookingSnapshot($request->query('checked_in')),
            'stats' => [
                'pending_count' => $pendingBookings->count(),
                'arrival_today_count' => $pendingBookings->where('NgayNhanPhong', now()->toDateString())->count(),
                'occupied_room_count' => $this->roomRecords()->where('TinhTrang', 2)->count(),
            ],
        ];
    }

    protected function bookingCustomers(): Collection
    {
        try {
            $customers = KhachHang::query()
                ->orderBy('TenKH')
                ->get()
                ->map(function (KhachHang $customer) {
                    $points = (int) ($customer->DIEM ?? $customer->Diem ?? 0);

                    return [
                        'id' => (int) $customer->MaKH,
                        'name' => (string) ($customer->TenKH ?? ''),
                        'phone' => (string) ($customer->SoDienThoai ?? ''),
                        'cccd' => (string) ($customer->CCCD ?? ''),
                        'address' => (string) ($customer->DiaChi ?? ''),
                        'points' => $points,
                        'label' => trim(sprintf('#%s • %s', $customer->MaKH, (string) ($customer->TenKH ?? ''))),
                    ];
                });

            if ($customers->isNotEmpty()) {
                return $customers;
            }
        } catch (Throwable $exception) {
            // Fall back to demo config when DB is unavailable in local/demo environments.
        }

        return $this->customerRecords()->map(function (array $customer) {
            $points = (int) ($customer['DIEM'] ?? $customer['Diem'] ?? 0);

            return [
                'id' => (int) ($customer['MaKH'] ?? 0),
                'name' => (string) ($customer['TenKH'] ?? ''),
                'phone' => (string) ($customer['SoDienThoai'] ?? ''),
                'cccd' => (string) ($customer['CCCD'] ?? ''),
                'address' => (string) ($customer['DiaChi'] ?? ''),
                'points' => $points,
                'label' => trim(sprintf('#%s • %s', $customer['MaKH'] ?? '', $customer['TenKH'] ?? '')),
            ];
        });
    }

    protected function availableBookingRooms(): Collection
    {
        try {
            $rooms = Phong::query()
                ->with('loaiPhong')
                ->where('TinhTrang', 0)
                ->orderBy('SoPhong')
                ->get()
                ->map(function (Phong $room) {
                    return [
                        'id' => (int) $room->MaPhong,
                        'number' => (string) ($room->SoPhong ?? ''),
                        'type_id' => (int) ($room->MaLoaiPhong ?? 0),
                        'type_name' => (string) ($room->loaiPhong?->TenLoaiPhong ?? 'Chưa phân loại'),
                        'capacity' => (int) ($room->loaiPhong?->SoNguoiToiDa ?? 0),
                        'description' => (string) ($room->loaiPhong?->Mota ?? ''),
                    ];
                });

            if ($rooms->isNotEmpty()) {
                return $rooms;
            }
        } catch (Throwable $exception) {
            // Fall back to demo config when DB is unavailable in local/demo environments.
        }

        return $this->roomRecords()
            ->where('TinhTrang', 0)
            ->map(function (array $room) {
                return [
                    'id' => (int) ($room['MaPhong'] ?? 0),
                    'number' => (string) ($room['SoPhong'] ?? ''),
                    'type_id' => (int) ($room['MaLoaiPhong'] ?? 0),
                    'type_name' => (string) ($room['TenLoaiPhong'] ?? 'Chưa phân loại'),
                    'capacity' => (int) ($room['SoNguoiToiDa'] ?? 0),
                    'description' => (string) ($room['Mota'] ?? ''),
                ];
            });
    }

    protected function bookingRoomTypes(Collection $rooms): Collection
    {
        try {
            $roomTypes = LoaiPhong::query()
                ->orderBy('TenLoaiPhong')
                ->get()
                ->map(function (LoaiPhong $roomType) use ($rooms) {
                    $availableCount = $rooms->where('type_id', (int) $roomType->MaLoaiPhong)->count();

                    return [
                        'id' => (int) $roomType->MaLoaiPhong,
                        'name' => (string) ($roomType->TenLoaiPhong ?? ''),
                        'capacity' => (int) ($roomType->SoNguoiToiDa ?? 0),
                        'description' => (string) ($roomType->Mota ?? ''),
                        'available_count' => $availableCount,
                    ];
                });

            if ($roomTypes->isNotEmpty()) {
                return $roomTypes;
            }
        } catch (Throwable $exception) {
            // Fall back to grouped room data when DB is unavailable.
        }

        return $rooms
            ->groupBy('type_id')
            ->map(function (Collection $groupedRooms) {
                $firstRoom = $groupedRooms->first();

                return [
                    'id' => (int) ($firstRoom['type_id'] ?? 0),
                    'name' => (string) ($firstRoom['type_name'] ?? ''),
                    'capacity' => (int) ($firstRoom['capacity'] ?? 0),
                    'description' => (string) ($firstRoom['description'] ?? ''),
                    'available_count' => $groupedRooms->count(),
                ];
            })
            ->values();
    }

    protected function mapConfigBooking(array $booking): array
    {
        $status = (int) ($booking['TinhTrang'] ?? match ((string) ($booking['TrangThai'] ?? '')) {
            'dang-su-dung' => 1,
            'da-huy' => 2,
            'da-tra-phong' => 3,
            default => 0,
        });

        return [
            'MaDatPhong' => (int) ($booking['MaDatPhong'] ?? 0),
            'MaKH' => (int) ($booking['MaKH'] ?? 0),
            'TenKH' => (string) ($booking['TenKH'] ?? ''),
            'SoDienThoai' => (string) ($booking['SoDienThoai'] ?? ''),
            'NgayDat' => (string) ($booking['NgayDat'] ?? ''),
            'NgayNhanPhong' => (string) ($booking['NgayNhanPhong'] ?? ''),
            'NgayTraPhong' => (string) ($booking['NgayTraPhong'] ?? ''),
            'SoLuong' => (int) ($booking['SoLuong'] ?? $booking['SoNguoi'] ?? 0),
            'TinhTrang' => $status,
            'TinhTrangLabel' => $this->bookingStatusLabel($status),
            'SoPhong' => (string) ($booking['SoPhong'] ?? ''),
            'LoaiPhong' => (string) ($booking['LoaiPhong'] ?? ''),
            'Rooms' => [],
            'SoLuongPhong' => 0,
            'SucChua' => 0,
            'SoDem' => $this->bookingNightCount((string) ($booking['NgayNhanPhong'] ?? ''), (string) ($booking['NgayTraPhong'] ?? '')),
        ];
    }

    protected function pendingCheckInRecords(): Collection
    {
        try {
            $records = DatPhong::query()
                ->with(['khachHang', 'chiTietDatPhongs.phong.loaiPhong'])
                ->where('TinhTrang', 0)
                ->orderBy('NgayNhanPhong')
                ->orderBy('MaDatPhong')
                ->get()
                ->map(fn (DatPhong $booking) => $this->mapDatabaseBooking($booking));

            if ($records->isNotEmpty()) {
                return $records;
            }
        } catch (Throwable $exception) {
            // If DB is unavailable we simply show no pending bookings on the custom check-in page.
        }

        return collect();
    }

    protected function databaseBookingSnapshot(mixed $bookingId): ?array
    {
        if ($bookingId === null || $bookingId === '') {
            return null;
        }

        try {
            $booking = DatPhong::query()
                ->with(['khachHang', 'chiTietDatPhongs.phong.loaiPhong'])
                ->find((int) $bookingId);

            return $booking ? $this->mapDatabaseBooking($booking) : null;
        } catch (Throwable $exception) {
            return null;
        }
    }

    protected function mapDatabaseBooking(DatPhong $booking): array
    {
        $rooms = $booking->chiTietDatPhongs
            ->map(function (ChiTietDatPhong $detail) {
                $room = $detail->phong;

                if ($room === null) {
                    return null;
                }

                return [
                    'id' => (int) $room->MaPhong,
                    'number' => (string) ($room->SoPhong ?? ''),
                    'type_name' => (string) ($room->loaiPhong?->TenLoaiPhong ?? 'Chưa phân loại'),
                    'capacity' => (int) ($room->loaiPhong?->SoNguoiToiDa ?? 0),
                ];
            })
            ->filter()
            ->values();

        return [
            'MaDatPhong' => (int) $booking->MaDatPhong,
            'MaKH' => (int) $booking->MaKH,
            'TenKH' => (string) ($booking->khachHang?->TenKH ?? ''),
            'SoDienThoai' => (string) ($booking->khachHang?->SoDienThoai ?? ''),
            'CCCD' => (string) ($booking->khachHang?->CCCD ?? ''),
            'DiaChi' => (string) ($booking->khachHang?->DiaChi ?? ''),
            'Diem' => (int) ($booking->khachHang?->DIEM ?? $booking->khachHang?->Diem ?? 0),
            'NgayDat' => (string) ($booking->NgayDat ?? ''),
            'NgayNhanPhong' => (string) ($booking->NgayNhanPhong ?? ''),
            'NgayTraPhong' => (string) ($booking->NgayTraPhong ?? ''),
            'SoLuong' => (int) ($booking->SoLuong ?? 0),
            'TinhTrang' => (int) ($booking->TinhTrang ?? 0),
            'TinhTrangLabel' => $this->bookingStatusLabel((int) ($booking->TinhTrang ?? 0)),
            'Rooms' => $rooms->all(),
            'SoPhong' => $rooms->pluck('number')->implode(', '),
            'LoaiPhong' => $rooms->pluck('type_name')->unique()->implode(', '),
            'SoLuongPhong' => $rooms->count(),
            'SucChua' => $rooms->sum('capacity'),
            'SoDem' => $this->bookingNightCount((string) ($booking->NgayNhanPhong ?? ''), (string) ($booking->NgayTraPhong ?? '')),
        ];
    }

    protected function bookingStatusLabel(int $status): string
    {
        $statusOptions = $this->bookingStatusOptions();

        if (isset($statusOptions[$status])) {
            return $statusOptions[$status];
        }
        return match ($status) {
            1 => 'Đã nhận phòng',
            default => 'Chờ nhận phòng',
        };
    }

    protected function bookingNightCount(string $checkInDate, string $checkOutDate): int
    {
        try {
            $checkIn = \Carbon\Carbon::createFromFormat('Y-m-d', $checkInDate);
            $checkOut = \Carbon\Carbon::createFromFormat('Y-m-d', $checkOutDate);

            return max($checkIn->diffInDays($checkOut), 1);
        } catch (Throwable $exception) {
            return 1;
        }
    }

    protected function invoiceRecords(): Collection
    {
        $invoices = collect(config('hotel-management.modules.invoices.records', []));
        $employees = collect(config('hotel-management.modules.employees.records', []))
            ->keyBy(fn (array $employee) => (string) ($employee['MaNV'] ?? ''));

        return $invoices->map(function (array $invoice) use ($employees) {
            $employeeId = (string) ($invoice['MaNV'] ?? '');
            $invoice['TenNV'] = Arr::get($employees->get($employeeId), 'TenNV', '');

            return $invoice;
        });
    }

    protected function customerRecords(): Collection
    {
        return collect(config('hotel-management.modules.customers.records', []))
            ->map(fn (array $customer) => $this->normalizeCustomerAddressRecord($customer, $this->customerAddressOptions()));
    }

    protected function customerModule(): array
    {
        $module = config('hotel-management.modules.customers', []);
        $addressOptions = $this->customerAddressOptions();

        unset($module['fields']['MaTK']);
        $module['list_columns'] = array_values(array_filter(
            $module['list_columns'] ?? [],
            static fn (string $columnKey) => $columnKey !== 'MaTK'
        ));
        $module['address_options'] = $addressOptions;
        $module['records'] = $this->customerRecords()->all();

        return $module;
    }

    protected function findCustomerRecord(string $customerId): array
    {
        return $this->customerRecords()
            ->first(fn (array $customer) => (string) ($customer['MaKH'] ?? '') === (string) $customerId)
            ?? abort(404);
    }

    protected function validateCustomerRequest(Request $request): void
    {
        $this->prepareCustomerAddressRequest($request);

        $request->validate([
            'TenKH' => ['required', 'string'],
            'DiaChi' => ['required', 'string'],
            'SoDienThoai' => ['required', 'string'],
            'CCCD' => ['required', 'string'],
            'NgaySinh' => ['required', 'date'],
            'GioiTinh' => ['nullable', 'string'],
            'Diem' => ['nullable', 'numeric'],
            'TrangThai' => ['required', 'string'],
        ]);
    }

    protected function roomRecords(): Collection
    {
        try {
            $rooms = Phong::query()
                ->with('loaiPhong:MaLoaiPhong,TenLoaiPhong')
                ->get()
                ->map(function (Phong $room) {
                    return [
                        'MaPhong' => $room->MaPhong,
                        'SoPhong' => (string) $room->SoPhong,
                        'MaLoaiPhong' => $room->MaLoaiPhong,
                        'TenLoaiPhong' => $room->loaiPhong?->TenLoaiPhong ?? '',
                        'SoNguoiToiDa' => (int) ($room->loaiPhong?->SoNguoiToiDa ?? 0),
                        'Mota' => (string) ($room->loaiPhong?->Mota ?? ''),
                        'TinhTrang' => (int) $room->TinhTrang,
                    ];
                });

            if ($rooms->isNotEmpty()) {
                return $rooms;
            }
        } catch (Throwable $exception) {
            // Fall back to demo config when DB is unavailable in local/demo environments.
        }

        return collect(config('hotel-management.modules.rooms.records', []));
    }

    protected function roomStatusPalette(): array
    {
        return [
            '0' => ['label' => 'Phòng trống', 'short_label' => 'Trống', 'tone' => 'empty'],
            '1' => ['label' => 'Phòng đã đặt', 'short_label' => 'Đã đặt', 'tone' => 'booked'],
            '2' => ['label' => 'Phòng đang sử dụng', 'short_label' => 'Đang sử dụng', 'tone' => 'using'],
            '3' => ['label' => 'Phòng đang dọn dẹp', 'short_label' => 'Đang dọn dẹp', 'tone' => 'cleaning'],
        ];
    }

    protected function buildRoomMapFloors(Collection $rooms, array $roomStatusPalette): array
    {
        return $rooms
            ->map(function (array $room) use ($roomStatusPalette) {
                $statusCode = (string) ($room['TinhTrang'] ?? 0);
                $status = $roomStatusPalette[$statusCode] ?? $roomStatusPalette['0'];

                return [
                    'room_number' => (string) ($room['SoPhong'] ?? ''),
                    'room_type' => (string) ($room['TenLoaiPhong'] ?? ''),
                    'status_label' => $status['short_label'],
                    'tone' => $status['tone'],
                    'floor_sort' => $this->extractFloorNumber((string) ($room['SoPhong'] ?? '')),
                ];
            })
            ->sort(function (array $first, array $second) {
                $floorComparison = $first['floor_sort'] <=> $second['floor_sort'];

                if ($floorComparison !== 0) {
                    return $floorComparison;
                }

                return strnatcmp($first['room_number'], $second['room_number']);
            })
            ->groupBy(function (array $room) {
                $floor = $room['floor_sort'];

                return $floor > 0 ? 'Tầng ' . $floor : 'Khu khác';
            })
            ->map(function (Collection $floorRooms, string $floorLabel) {
                return [
                    'label' => $floorLabel,
                    'rooms' => $floorRooms
                        ->map(fn (array $room) => Arr::except($room, ['floor_sort']))
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }

    protected function extractFloorNumber(string $roomNumber): int
    {
        if (preg_match('/(\d{3,})/', $roomNumber, $matches) === 1) {
            return (int) substr($matches[1], 0, 1);
        }

        if (preg_match('/(\d+)/', $roomNumber, $matches) === 1) {
            return (int) $matches[1];
        }

        return 0;
    }

    protected function prepareCustomerAddressRequest(Request $request): void
    {
        $street = trim((string) $request->input('DiaChiDuong', ''));
        $district = trim((string) $request->input('DiaChiHuyen', ''));
        $province = trim((string) $request->input('DiaChiTinh', ''));

        if ($street === '' && $district === '' && $province === '') {
            return;
        }

        $request->merge([
            'DiaChi' => $this->composeCustomerAddress($street, $district, $province),
        ]);
    }

    protected function normalizeCustomerAddressRecord(array $record, array $addressOptions): array
    {
        $street = trim((string) ($record['DiaChiDuong'] ?? ''));
        $district = trim((string) ($record['DiaChiHuyen'] ?? ''));
        $province = trim((string) ($record['DiaChiTinh'] ?? ''));

        if ($street === '' && $district === '' && $province === '') {
            $parts = $this->parseCustomerAddress((string) ($record['DiaChi'] ?? ''), $addressOptions);
            $street = $parts['street'];
            $district = $parts['district'];
            $province = $parts['province'];
        }

        $record['DiaChiDuong'] = $street;
        $record['DiaChiHuyen'] = $district;
        $record['DiaChiTinh'] = $province;
        $record['DiaChi'] = $this->composeCustomerAddress($street, $district, $province);

        return $record;
    }

    protected function parseCustomerAddress(string $fullAddress, array $addressOptions): array
    {
        $segments = array_values(array_filter(array_map('trim', explode(',', $fullAddress)), static fn ($segment) => $segment !== ''));

        if ($segments === []) {
            return [
                'street' => '',
                'district' => '',
                'province' => '',
            ];
        }

        if (count($segments) === 1) {
            return [
                'street' => $segments[0],
                'district' => '',
                'province' => '',
            ];
        }

        if (count($segments) === 2) {
            return [
                'street' => $segments[0],
                'district' => '',
                'province' => $segments[1],
            ];
        }

        $province = (string) array_pop($segments);
        $district = (string) array_pop($segments);
        $street = implode(', ', $segments);

        if (!isset($addressOptions[$province])) {
            return [
                'street' => $street,
                'district' => $district,
                'province' => $province,
            ];
        }

        return [
            'street' => $street,
            'district' => $district,
            'province' => $province,
        ];
    }

    protected function composeCustomerAddress(string $street, string $district, string $province): string
    {
        return implode(', ', array_values(array_filter([
            trim($street),
            trim($district),
            trim($province),
        ], static fn ($value) => $value !== '')));
    }

    protected function customerAddressOptions(): array
    {
        return [
            'TP.HCM' => ['Quận 1', 'Quận 3', 'Quận 7', 'Quận 10', 'Bình Thạnh', 'Gò Vấp', 'Thủ Đức'],
            'Hà Nội' => ['Ba Đình', 'Hoàn Kiếm', 'Đống Đa', 'Hai Bà Trưng', 'Cầu Giấy', 'Thanh Xuân', 'Nam Từ Liêm'],
            'Đà Nẵng' => ['Hải Châu', 'Thanh Khê', 'Sơn Trà', 'Ngũ Hành Sơn', 'Liên Chiểu', 'Cẩm Lệ'],
            'Cần Thơ' => ['Ninh Kiều', 'Bình Thủy', 'Cái Răng', 'Ô Môn', 'Thốt Nốt'],
            'Hải Phòng' => ['Hồng Bàng', 'Ngô Quyền', 'Lê Chân', 'Hải An', 'Kiến An', 'Dương Kinh'],
            'Khánh Hòa' => ['Nha Trang', 'Cam Ranh', 'Ninh Hòa', 'Diên Khánh', 'Vạn Ninh'],
        ];
    }

    protected function customerGenderLabel(mixed $gender): string
    {
        return match ((string) $gender) {
            '0' => 'Nữ',
            '1' => 'Nam',
            '2' => 'Khác',
            default => 'Chưa cập nhật',
        };
    }

    protected function formatCurrency(float $amount): string
    {
        return number_format($amount, 0, ',', '.') . ' VNĐ';
    }
}
