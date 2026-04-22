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
                ['label' => 'Äáº·t phĂ²ng', 'tone' => 'sunrise', 'icon' => 'booking'],
                ['label' => 'Nháº­n phĂ²ng', 'tone' => 'teal', 'icon' => 'check-in'],
                ['label' => 'Tráº£ phĂ²ng', 'tone' => 'amber', 'icon' => 'check-out'],
                ['label' => 'Äá»•i phĂ²ng', 'tone' => 'slate', 'icon' => 'switch-room'],
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
                'title' => 'Quáº£n lĂ½ khĂ¡ch hĂ ng',
                'description' => 'Theo dĂµi há»“ sÆ¡ lÆ°u trĂº cá»§a khĂ¡ch hĂ ng táº¡i khĂ¡ch sáº¡n',
                'create_button' => [
                    'label' => 'ThĂªm khĂ¡ch hĂ ng',
                    'url' => route('reception.customers.create'),
                ],
                'summary_cards' => [
                    ['label' => 'KhĂ¡ch Ä‘ang hoáº¡t Ä‘á»™ng', 'value' => $allCustomers->where('TrangThai', 1)->count(), 'tone' => 'sunrise'],
                    ['label' => 'KhĂ¡ch VIP', 'value' => $allCustomers->filter(fn (array $customer) => (int) ($customer['Diem'] ?? 0) >= 100)->count(), 'tone' => 'amber'],
                    ['label' => 'Cáº§n liĂªn há»‡ láº¡i', 'value' => $allCustomers->where('TrangThai', 0)->count(), 'tone' => 'slate'],
                ],
                'filters' => [
                    [
                        'key' => 'search',
                        'label' => 'TĂ¬m nhanh',
                        'type' => 'text',
                        'placeholder' => 'TĂªn khĂ¡ch, CCCD, sá»‘ Ä‘iá»‡n thoáº¡i...',
                    ],
                    [
                        'key' => 'status',
                        'label' => 'Tráº¡ng thĂ¡i',
                        'type' => 'select',
                        'options' => [
                            '' => 'Táº¥t cáº£ tráº¡ng thĂ¡i',
                            '1' => 'Hoáº¡t Ä‘á»™ng',
                            '0' => 'KhĂ´ng hoáº¡t Ä‘á»™ng',
                        ],
                    ],
                ],
                'columns' => [
                    ['key' => 'MaKH', 'label' => 'MĂ£ KH'],
                    ['key' => 'TenKH', 'label' => 'KhĂ¡ch hĂ ng'],
                    ['key' => 'DiaChi', 'label' => 'Äá»‹a chá»‰'],
                    ['key' => 'SoDienThoai', 'label' => 'Äiá»‡n thoáº¡i'],
                    ['key' => 'CCCD', 'label' => 'CCCD'],
                    ['key' => 'NgaySinh', 'label' => 'NgĂ y sinh'],
                    ['key' => 'GioiTinhText', 'label' => 'Giá»›i tĂ­nh'],
                    ['key' => 'Diem', 'label' => 'Äiá»ƒm'],
                    ['key' => 'TrangThai', 'label' => 'Tráº¡ng thĂ¡i'],
                ],
                'row_actions' => [
                    'primary_key' => 'MaKH',
                    'parameter_name' => 'customerId',
                    'show_route' => 'reception.customers.show',
                    'edit_route' => 'reception.customers.edit',
                ],
                'rows' => $customers->values()->all(),
                'empty_text' => 'KhĂ´ng cĂ³ khĂ¡ch hĂ ng nĂ o khá»›p bá»™ lá»c Ä‘ang chá»n.',
                'table_note' => 'Dá»¯ liá»‡u Ä‘ang á»Ÿ cháº¿ Ä‘á»™ demo, phĂ¹ há»£p Ä‘á»ƒ dá»±ng giao diá»‡n vĂ  trĂ¬nh diá»…n luá»“ng tiáº¿p nháº­n khĂ¡ch.',
                'badge_maps' => [
                    'TrangThai' => [
                        '1' => ['label' => 'Hoáº¡t Ä‘á»™ng', 'class' => 'rd-badge rd-badge--success'],
                        '0' => ['label' => 'KhĂ´ng hoáº¡t Ä‘á»™ng', 'class' => 'rd-badge rd-badge--muted'],
                    ],
                ],
            ],
        ]);
    }

    public function customerShow(string $customerId): View
    {
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
        return redirect()
            ->route('reception.customers.index')
            ->with('success', 'Da ghi nhan tao khach hang o giao dien mau.');
    }

    public function customerUpdate(Request $request, string $customerId): RedirectResponse
    {
        return redirect()
            ->route('reception.customers.show', ['customerId' => $customerId])
            ->with('success', 'Da ghi nhan cap nhat khach hang o giao dien mau.');
    }

    public function bookings(Request $request): View
    {
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
                'title' => 'Quáº£n lĂ½ Ä‘áº·t phĂ²ng',
                'description' => 'Quáº£n lĂ½ thĂ´ng tin Ä‘áº·t phĂ²ng cá»§a khĂ¡ch hĂ ng táº¡i khĂ¡ch sáº¡n',
                'create_button' => [
                    'label' => 'ThĂªm Ä‘áº·t phĂ²ng',
                    'url' => route('reception.bookings.create'),
                ],
                'summary_cards' => [
                    ['label' => 'ÄĂƒ Äáº¶T', 'value' => $allBookings->where('TinhTrang', 0)->count(), 'tone' => 'sunrise'],
                    ['label' => 'ÄANG Sá»¬ Dá»¤NG', 'value' => $allBookings->where('TinhTrang', 1)->count(), 'tone' => 'teal'],
                    ['label' => 'ÄĂƒ TRáº¢ PHĂ’NG', 'value' => $allBookings->where('TinhTrang', 3)->count(), 'tone' => 'amber'],
                ],
                'filters' => [
                    [
                        'key' => 'search',
                        'label' => 'TĂ¬m booking',
                        'type' => 'text',
                        'placeholder' => 'MĂ£ Ä‘áº·t phĂ²ng, mĂ£ khĂ¡ch hĂ ng, tĂªn khĂ¡ch...',
                    ],
                    [
                        'key' => 'status',
                        'label' => 'Tráº¡ng thĂ¡i',
                        'type' => 'select',
                        'options' => [
                            '' => 'Táº¥t cáº£ tráº¡ng thĂ¡i',
                            '0' => 'ÄĂ£ Ä‘áº·t',
                            '1' => 'Äang sá»­ dá»¥ng',
                            '2' => 'ÄĂ£ há»§y',
                            '3' => 'ÄĂ£ tráº£ phĂ²ng',
                        ],
                    ],
                ],
                'columns' => [
                    ['key' => 'MaDatPhong', 'label' => 'MĂ£ Ä‘áº·t'],
                    ['key' => 'MaKH', 'label' => 'MĂ£ KH'],
                    ['key' => 'TenKH', 'label' => 'KhĂ¡ch hĂ ng'],
                    ['key' => 'SoPhong', 'label' => 'PhĂ²ng'],
                    ['key' => 'LoaiPhong', 'label' => 'Loáº¡i phĂ²ng'],
                    ['key' => 'NgayDat', 'label' => 'NgĂ y Ä‘áº·t'],
                    ['key' => 'NgayNhanPhong', 'label' => 'Nháº­n phĂ²ng'],
                    ['key' => 'NgayTraPhong', 'label' => 'Tráº£ phĂ²ng'],
                    ['key' => 'SoLuong', 'label' => 'Sá»‘ lÆ°á»£ng ngÆ°á»i á»Ÿ'],
                    ['key' => 'TinhTrang', 'label' => 'Tráº¡ng thĂ¡i'],
                ],
                'row_actions' => [
                    'primary_key' => 'MaDatPhong',
                    'parameter_name' => 'bookingId',
                    'show_route' => 'reception.bookings.show',
                    'edit_route' => 'reception.bookings.edit',
                ],
                'rows' => $bookings->values()->all(),
                'empty_text' => 'KhĂ´ng tĂ¬m tháº¥y booking phĂ¹ há»£p vá»›i tá»« khĂ³a hoáº·c tráº¡ng thĂ¡i Ä‘Ă£ chá»n.',
                'table_note' => 'Báº¡n cĂ³ thá»ƒ thay bá»™ dá»¯ liá»‡u demo nĂ y báº±ng báº£ng DatPhong tháº­t sau mĂ  khĂ´ng pháº£i Ä‘á»•i láº¡i cáº¥u trĂºc giao diá»‡n.',
                'badge_maps' => [
                    'TinhTrang' => [
                        '0' => ['label' => 'ÄĂ£ Ä‘áº·t', 'class' => 'rd-badge rd-badge--warning'],
                        '1' => ['label' => 'Äang sá»­ dá»¥ng', 'class' => 'rd-badge rd-badge--success'],
                        '2' => ['label' => 'ÄĂ£ há»§y', 'class' => 'rd-badge rd-badge--danger'],
                        '3' => ['label' => 'ÄĂ£ tráº£ phĂ²ng', 'class' => 'rd-badge rd-badge--muted'],
                    ],
                ],
            ],
        ]);
    }

    public function bookingShow(string $bookingId): View
    {
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
        return view('receptionist.booking-form', [
            'assets' => ['animation'],
            'page' => $this->bookingPagePayload($request),
        ]);
    }

    public function bookingEdit(string $bookingId): View
    {
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
        return redirect()
            ->route('reception.bookings.create')
            ->with('success', 'Da ghi nhan tao booking o giao dien mau.');
    }

    public function checkInCreate(Request $request): View
    {
        return view('receptionist.check-in-form', [
            'assets' => ['animation'],
            'page' => $this->checkInPagePayload($request),
        ]);
    }

    public function checkInStore(Request $request): RedirectResponse
    {
        return redirect()
            ->route('reception.check-ins.create')
            ->with('success', 'Da ghi nhan check-in o giao dien mau.');
    }

    public function bookingUpdate(Request $request, string $bookingId): RedirectResponse
    {
        return redirect()
            ->route('reception.bookings.show', ['bookingId' => $bookingId])
            ->with('success', 'Da ghi nhan cap nhat booking o giao dien mau.');
    }

    public function invoices(Request $request): View
    {
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
                'title' => 'Quáº£n lĂ½ hĂ³a Ä‘Æ¡n',
                'description' => 'Theo dĂµi cĂ´ng ná»£ cĂ²n láº¡i, tráº¡ng thĂ¡i thanh toĂ¡n vĂ  ai Ä‘ang phá»¥ trĂ¡ch hĂ³a Ä‘Æ¡n táº¡i quáº§y lá»… tĂ¢n.',
                'summary_cards' => [
                    ['label' => 'ChÆ°a thanh toĂ¡n', 'value' => $allInvoices->where('TrangThai', 0)->count(), 'tone' => 'amber'],
                    ['label' => 'ÄĂ£ thanh toĂ¡n', 'value' => $allInvoices->where('TrangThai', 1)->count(), 'tone' => 'teal'],
                    ['label' => 'CĂ´ng ná»£ cĂ²n láº¡i', 'value' => $this->formatCurrency((float) $allInvoices->sum('ConLai')), 'tone' => 'slate'],
                ],
                'filters' => [
                    [
                        'key' => 'search',
                        'label' => 'TĂ¬m hĂ³a Ä‘Æ¡n',
                        'type' => 'text',
                        'placeholder' => 'MĂ£ hĂ³a Ä‘Æ¡n, mĂ£ Ä‘áº·t phĂ²ng, nhĂ¢n viĂªn...',
                    ],
                    [
                        'key' => 'status',
                        'label' => 'Thanh toĂ¡n',
                        'type' => 'select',
                        'options' => [
                            '' => 'Táº¥t cáº£ tráº¡ng thĂ¡i',
                            '0' => 'ChÆ°a thanh toĂ¡n',
                            '1' => 'ÄĂ£ thanh toĂ¡n',
                        ],
                    ],
                ],
                'columns' => [
                    ['key' => 'MaHD', 'label' => 'MĂ£ HÄ'],
                    ['key' => 'MaDatPhong', 'label' => 'MĂ£ Ä‘áº·t phĂ²ng'],
                    ['key' => 'NgayLapHD', 'label' => 'NgĂ y láº­p'],
                    ['key' => 'TenNV', 'label' => 'NhĂ¢n viĂªn'],
                    ['key' => 'TongTien', 'label' => 'Tá»•ng tiá»n'],
                    ['key' => 'DaThanhToan', 'label' => 'ÄĂ£ thanh toĂ¡n'],
                    ['key' => 'ConLai', 'label' => 'CĂ²n láº¡i'],
                    ['key' => 'TrangThai', 'label' => 'Tráº¡ng thĂ¡i'],
                ],
                'rows' => $invoices->values()->all(),
                'empty_text' => 'KhĂ´ng cĂ³ hĂ³a Ä‘Æ¡n nĂ o phĂ¹ há»£p vá»›i bá»™ lá»c hiá»‡n táº¡i.',
                'table_note' => 'Pháº§n cĂ²n láº¡i Ä‘Æ°á»£c tĂ­nh trá»±c tiáº¿p tá»« dá»¯ liá»‡u máº«u: Tá»•ng tiá»n trá»« sá»‘ Ä‘Ă£ thanh toĂ¡n.',
                'badge_maps' => [
                    'TrangThai' => [
                        '0' => ['label' => 'ChÆ°a thanh toĂ¡n', 'class' => 'rd-badge rd-badge--warning'],
                        '1' => ['label' => 'ÄĂ£ thanh toĂ¡n', 'class' => 'rd-badge rd-badge--success'],
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
            'title' => 'Quáº£n lĂ½ Ä‘áº·t phĂ²ng',
            'singular' => 'Ä‘áº·t phĂ²ng',
            'description' => 'ThĂ´ng tin Ä‘áº·t phĂ²ng theo Ä‘Ăºng cáº¥u trĂºc báº£ng DatPhong trong CSDL.',
            'primary_key' => 'MaDatPhong',
            'fields' => [
                'MaDatPhong' => ['label' => 'MĂ£ Ä‘áº·t phĂ²ng', 'type' => 'number', 'readonly' => true],
                'MaKH' => ['label' => 'MĂ£ khĂ¡ch hĂ ng', 'type' => 'select', 'options' => $customerOptions, 'required' => true],
                'TenKH' => ['label' => 'KhĂ¡ch hĂ ng', 'type' => 'text', 'readonly' => true],
                'SoDienThoai' => ['label' => 'Sá»‘ Ä‘iá»‡n thoáº¡i', 'type' => 'text', 'readonly' => true],
                'NgayDat' => ['label' => 'NgĂ y Ä‘áº·t', 'type' => 'date', 'required' => true],
                'NgayNhanPhong' => ['label' => 'NgĂ y nháº­n phĂ²ng', 'type' => 'date', 'required' => true],
                'NgayTraPhong' => ['label' => 'NgĂ y tráº£ phĂ²ng', 'type' => 'date', 'required' => true],
                'SoLuong' => ['label' => 'Sá»‘ lÆ°á»£ng ngÆ°á»i á»Ÿ', 'type' => 'number', 'required' => true],
                'SoPhong' => ['label' => 'PhĂ²ng', 'type' => 'text', 'readonly' => true],
                'LoaiPhong' => ['label' => 'Loáº¡i phĂ²ng', 'type' => 'text', 'readonly' => true],
                'TinhTrang' => [
                    'label' => 'Tráº¡ng thĂ¡i',
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
            'title' => 'Quáº£n lĂ½ Ä‘áº·t phĂ²ng',
            'singular' => 'Ä‘áº·t phĂ²ng',
            'description' => 'Táº¡o booking má»›i cho khĂ¡ch táº¡i quáº§y lá»… tĂ¢n.',
            'primary_key' => 'MaDatPhong',
            'fields' => [
                'MaKH' => ['label' => 'MĂ£ khĂ¡ch hĂ ng', 'type' => 'select', 'options' => $customerOptions, 'required' => true],
                'TenKH' => ['label' => 'KhĂ¡ch hĂ ng', 'type' => 'text', 'required' => true],
                'SoDienThoai' => ['label' => 'Sá»‘ Ä‘iá»‡n thoáº¡i', 'type' => 'text', 'required' => true],
                'SoPhong' => ['label' => 'PhĂ²ng', 'type' => 'select', 'options' => $roomOptions, 'required' => true],
                'LoaiPhong' => ['label' => 'Loáº¡i phĂ²ng', 'type' => 'select', 'options' => $roomTypeOptions, 'required' => true],
                'NgayNhanPhong' => ['label' => 'Nháº­n phĂ²ng', 'type' => 'date', 'required' => true],
                'NgayTraPhong' => ['label' => 'Tráº£ phĂ²ng', 'type' => 'date', 'required' => true],
                'SoNguoi' => ['label' => 'Sá»‘ ngÆ°á»i', 'type' => 'number', 'required' => true],
                'TienCoc' => ['label' => 'Tiá»n cá»c', 'type' => 'number', 'required' => true],
                'TrangThai' => [
                    'label' => 'Tráº¡ng thĂ¡i',
                    'type' => 'select',
                    'required' => true,
                    'options' => [
                        'da-dat' => 'ÄĂ£ Ä‘áº·t',
                        'dang-su-dung' => 'Äang sá»­ dá»¥ng',
                        'da-huy' => 'ÄĂ£ há»§y',
                        'da-tra-phong' => 'ÄĂ£ tráº£ phĂ²ng',
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
            'MaKH' => 'khĂ¡ch hĂ ng',
            'NgayDat' => 'ngĂ y Ä‘áº·t',
            'NgayNhanPhong' => 'ngĂ y nháº­n phĂ²ng',
            'NgayTraPhong' => 'ngĂ y tráº£ phĂ²ng',
            'room_ids' => 'phĂ²ng',
            'room_ids.*' => 'phĂ²ng',
        ]);
    }

    protected function bookingStatusOptions(): array
    {
        return [
            0 => 'ÄĂ£ Ä‘áº·t',
            1 => 'Äang sá»­ dá»¥ng',
            2 => 'ÄĂ£ há»§y',
            3 => 'ÄĂ£ tráº£ phĂ²ng',
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
            'title' => 'Äáº·t PhĂ²ng',
            'description' => 'Táº¡o Ä‘Æ¡n Ä‘áº·t phĂ²ng trá»±c tiáº¿p khi khĂ¡ch hĂ ng Ä‘áº¿n Ä‘áº·t phĂ²ng táº¡i lá»… tĂ¢n',
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
            'title' => 'Nháº­n PhĂ²ng',
            'description' => 'XĂ¡c nháº­n khĂ¡ch Ä‘áº¿n, chuyá»ƒn booking sang tráº¡ng thĂ¡i Ä‘ang lÆ°u trĂº vĂ  cáº­p nháº­t phĂ²ng sang Ä‘ang sá»­ dá»¥ng.',
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
                        'label' => trim(sprintf('#%s â€¢ %s', $customer->MaKH, (string) ($customer->TenKH ?? ''))),
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
                'label' => trim(sprintf('#%s â€¢ %s', $customer['MaKH'] ?? '', $customer['TenKH'] ?? '')),
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
                        'type_name' => (string) ($room->loaiPhong?->TenLoaiPhong ?? 'ChÆ°a phĂ¢n loáº¡i'),
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
                    'type_name' => (string) ($room['TenLoaiPhong'] ?? 'ChÆ°a phĂ¢n loáº¡i'),
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
                    'type_name' => (string) ($room->loaiPhong?->TenLoaiPhong ?? 'ChÆ°a phĂ¢n loáº¡i'),
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
            1 => 'ÄĂ£ nháº­n phĂ²ng',
            default => 'Chá» nháº­n phĂ²ng',
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
            '0' => ['label' => 'PhĂ²ng trá»‘ng', 'short_label' => 'Trá»‘ng', 'tone' => 'empty'],
            '1' => ['label' => 'PhĂ²ng Ä‘Ă£ Ä‘áº·t', 'short_label' => 'ÄĂ£ Ä‘áº·t', 'tone' => 'booked'],
            '2' => ['label' => 'PhĂ²ng Ä‘ang sá»­ dá»¥ng', 'short_label' => 'Äang sá»­ dá»¥ng', 'tone' => 'using'],
            '3' => ['label' => 'PhĂ²ng Ä‘ang dá»n dáº¹p', 'short_label' => 'Äang dá»n dáº¹p', 'tone' => 'cleaning'],
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

                return $floor > 0 ? 'Táº§ng ' . $floor : 'Khu khĂ¡c';
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
            'TP.HCM' => ['Quáº­n 1', 'Quáº­n 3', 'Quáº­n 7', 'Quáº­n 10', 'BĂ¬nh Tháº¡nh', 'GĂ² Váº¥p', 'Thá»§ Äá»©c'],
            'HĂ  Ná»™i' => ['Ba ÄĂ¬nh', 'HoĂ n Kiáº¿m', 'Äá»‘ng Äa', 'Hai BĂ  TrÆ°ng', 'Cáº§u Giáº¥y', 'Thanh XuĂ¢n', 'Nam Tá»« LiĂªm'],
            'ÄĂ  Náºµng' => ['Háº£i ChĂ¢u', 'Thanh KhĂª', 'SÆ¡n TrĂ ', 'NgÅ© HĂ nh SÆ¡n', 'LiĂªn Chiá»ƒu', 'Cáº©m Lá»‡'],
            'Cáº§n ThÆ¡' => ['Ninh Kiá»u', 'BĂ¬nh Thá»§y', 'CĂ¡i RÄƒng', 'Ă” MĂ´n', 'Thá»‘t Ná»‘t'],
            'Háº£i PhĂ²ng' => ['Há»“ng BĂ ng', 'NgĂ´ Quyá»n', 'LĂª ChĂ¢n', 'Háº£i An', 'Kiáº¿n An', 'DÆ°Æ¡ng Kinh'],
            'KhĂ¡nh HĂ²a' => ['Nha Trang', 'Cam Ranh', 'Ninh HĂ²a', 'DiĂªn KhĂ¡nh', 'Váº¡n Ninh'],
        ];
    }

    protected function customerGenderLabel(mixed $gender): string
    {
        return match ((string) $gender) {
            '0' => 'Ná»¯',
            '1' => 'Nam',
            '2' => 'KhĂ¡c',
            default => 'ChÆ°a cáº­p nháº­t',
        };
    }

    protected function formatCurrency(float $amount): string
    {
        return number_format($amount, 0, ',', '.') . ' VNÄ';
    }
}

