<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountManagementController;
use App\Http\Controllers\CustomerManagementController;
use App\Models\DatPhong;
use App\Models\DanhGia;
use App\Models\DichVu;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\KhuyenMai;
use App\Models\LoaiPhong;
use App\Models\NhanVien;
use App\Models\Phong;
use App\Models\TaiKhoan;
use App\Models\ThanhToan;
use App\Models\TienNghi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customer UI
|--------------------------------------------------------------------------
*/

Route::prefix('customer')->name('customer.')->group(function () {
    Route::view('/', 'customer.index')->name('home');
    Route::view('/promotion', 'customer.promotion')->name('promotion');
    Route::view('/blog-single', 'customer.blog-single')->name('blog-single');
    Route::get('/services', function () {
        $services = DichVu::with('hinhs')
            ->orderBy('LoaiDV')
            ->orderBy('MaDV')
            ->get();

        $serviceGroups = [
            DichVu::TYPE_FOOD_AND_BEVERAGE => [
                'eyebrow' => 'Dịch vụ ăn uống',
                'title' => 'Thực đơn của khách sạn',
                'fallbackImages' => [
                    'customers/images/menu-1.jpg',
                    'customers/images/menu-2.jpg',
                    'customers/images/menu-3.jpg',
                    'customers/images/menu-4.jpg',
                    'customers/images/menu-5.jpg',
                    'customers/images/menu-6.jpg',
                ],
            ],
            DichVu::TYPE_ENTERTAINMENT => [
                'eyebrow' => 'Dịch vụ giải trí',
                'title' => 'Loại hình giải trí của khách sạn',
                'fallbackImages' => [
                    'customers/images/dv_spa.jpg',
                    'customers/images/dv_golf.jpg',
                    'customers/images/view1.jpg',
                    'customers/images/home4.jpg',
                ],
            ],
            DichVu::TYPE_ROOM_SERVICE => [
                'eyebrow' => 'Dịch vụ phòng',
                'title' => 'Dịch vụ phòng của khách sạn',
                'fallbackImages' => [
                    'customers/images/room-1.jpg',
                    'customers/images/room-2.jpg',
                    'customers/images/room-3.jpg',
                    'customers/images/room-4.jpg',
                ],
            ],
        ];
        $authAccount = session('auth_account', []);
        $isCustomerAccount = (int) ($authAccount['LoaiTaiKhoan'] ?? -1) === 0;
        $customerId = $isCustomerAccount ? ($authAccount['MaKH'] ?? null) : null;

        if ($isCustomerAccount && !$customerId && !empty($authAccount['MaTK'])) {
            $customerId = \App\Models\KhachHang::where('MaTK', $authAccount['MaTK'])->value('MaKH');
        }

        $serviceBookingOptions = collect();

        if ($customerId) {
            $serviceBookingOptions = DatPhong::with('chiTietDatPhong.phong')
                ->where('MaKH', $customerId)
                ->where('TinhTrang', DatPhong::CHECKED_IN)
                ->orderByDesc('MaDatPhong')
                ->get()
                ->map(function (DatPhong $booking) {
                    $roomNumbers = $booking->chiTietDatPhong
                        ->map(fn ($detail) => $detail->phong?->SoPhong)
                        ->filter()
                        ->values()
                        ->implode(', ');

                    return [
                        'id' => (string) $booking->MaDatPhong,
                        'label' => $roomNumbers
                            ? "#{$booking->MaDatPhong} - {$roomNumbers}"
                            : "#{$booking->MaDatPhong}",
                        'checkIn' => \Illuminate\Support\Carbon::parse($booking->NgayNhanPhong)->toDateString(),
                        'checkOut' => \Illuminate\Support\Carbon::parse($booking->NgayTraPhong)->toDateString(),
                    ];
                })
                ->values();
        }

        return view('customer.services', [
            'servicesByType' => $services->groupBy('LoaiDV'),
            'serviceGroups' => $serviceGroups,
            'serviceOptions' => $services->map(fn (DichVu $service) => [
                'id' => (string) $service->MaDV,
                'name' => $service->TenDV,
                'type' => (string) $service->LoaiDV,
                'price' => (float) $service->GiaDV,
            ])->values(),
            'serviceBookingOptions' => $serviceBookingOptions,
        ]);
    })->name('services');
    Route::view('/rooms', 'customer.rooms')->name('rooms');
    Route::view('/room/{id}', 'customer.rooms-single')->name('room.detail');
    Route::view('/rooms-single', 'customer.rooms-single')->name('rooms-single');
    Route::view('/rooms-booking', 'customer.rooms-booking')->name('rooms-booking');
    Route::get('/info-booking', function () {
        $authAccount = session('auth_account', []);
        $isCustomerAccount = (int) ($authAccount['LoaiTaiKhoan'] ?? -1) === 0;
        $bookingCustomer = null;

        if ($isCustomerAccount && !empty($authAccount['MaKH'])) {
            $bookingCustomer = \App\Models\KhachHang::with('taiKhoan')->find($authAccount['MaKH']);
        } elseif ($isCustomerAccount && !empty($authAccount['MaTK'])) {
            $bookingCustomer = \App\Models\KhachHang::with('taiKhoan')
                ->where('MaTK', $authAccount['MaTK'])
                ->first();
        }

        return view('customer.info-booking', [
            'bookingAccount' => $authAccount,
            'bookingCustomer' => $bookingCustomer,
        ]);
    })->name('info-booking');
    Route::get('/profile', function () {
        $authAccount = session('auth_account', []);
        $profileCustomer = null;

        if (!empty($authAccount['MaKH'])) {
            $profileCustomer = \App\Models\KhachHang::with('taiKhoan')->find($authAccount['MaKH']);
        } elseif (!empty($authAccount['MaTK'])) {
            $profileCustomer = \App\Models\KhachHang::with('taiKhoan')
                ->where('MaTK', $authAccount['MaTK'])
                ->first();
        }

        return view('customer.profile', [
            'profileAccount' => $authAccount,
            'profileCustomer' => $profileCustomer,
        ]);
    })->middleware('account.role:0')->name('profile');
    Route::get('/my-bookings', function () {
        $authAccount = session('auth_account', []);
        $customerId = $authAccount['MaKH'] ?? null;

        if (!$customerId && !empty($authAccount['MaTK'])) {
            $customerId = \App\Models\KhachHang::where('MaTK', $authAccount['MaTK'])->value('MaKH');
        }

        $customerBookings = collect();

        if ($customerId) {
            $expiredBookingIds = \App\Models\DatPhong::where('MaKH', $customerId)
                ->whereIn('TinhTrang', [\App\Models\DatPhong::HOLD, \App\Models\DatPhong::CONFIRMED])
                ->whereDate('NgayNhanPhong', '<', now()->toDateString())
                ->pluck('MaDatPhong');

            if ($expiredBookingIds->isNotEmpty()) {
                \Illuminate\Support\Facades\DB::transaction(function () use ($expiredBookingIds) {
                    \App\Models\DatPhong::whereIn('MaDatPhong', $expiredBookingIds)
                        ->update(['TinhTrang' => \App\Models\DatPhong::CANCELLED]);

                    \App\Models\HoaDon::whereIn('MaDatPhong', $expiredBookingIds)
                        ->update(['TrangThai' => 3]);
                });
            }

            $customerBookings = \App\Models\DatPhong::with([
                'khachHang.taiKhoan',
                'hoaDon.chiTietHoaDons.loaiPhong',
                'hoaDon.thanhToans',
                'chiTietDatPhong.phong.loaiPhong',
            ])
                ->where('MaKH', $customerId)
                ->orderByDesc('NgayDat')
                ->orderByDesc('MaDatPhong')
                ->get();
        }

        return view('customer.my-bookings', [
            'customerBookings' => $customerBookings,
        ]);
    })->middleware('account.role:0')->name('my-bookings');
    Route::post('/my-bookings/{booking}/cancel', function ($booking) {
        if (!session()->has('auth_account')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để hủy đặt phòng.',
            ], 401);
        }

        $authAccount = session('auth_account', []);
        $customerId = $authAccount['MaKH'] ?? null;

        if (!$customerId && !empty($authAccount['MaTK'])) {
            $customerId = \App\Models\KhachHang::where('MaTK', $authAccount['MaTK'])->value('MaKH');
        }

        $datPhong = \App\Models\DatPhong::where('MaDatPhong', $booking)
            ->where('MaKH', $customerId)
            ->first();

        if (!$datPhong) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đặt phòng.',
            ], 404);
        }

        if (!in_array((int) $datPhong->TinhTrang, [\App\Models\DatPhong::HOLD, \App\Models\DatPhong::CONFIRMED], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy đặt phòng này.',
            ], 400);
        }

        if (!\Illuminate\Support\Carbon::parse($datPhong->NgayNhanPhong)->startOfDay()->isFuture()) {
            return response()->json([
                'success' => false,
                'message' => 'Đặt phòng đã quá hạn hủy.',
            ], 400);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($datPhong) {
            $datPhong->update(['TinhTrang' => \App\Models\DatPhong::CANCELLED]);

            \App\Models\HoaDon::where('MaDatPhong', $datPhong->MaDatPhong)
                ->update(['TrangThai' => 3]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Đã hủy đặt phòng thành công.',
        ]);
    })->middleware('account.role:0')->name('my-bookings.cancel');
    Route::view('/promotion-wallet', 'customer.promotion-wallet')->middleware('account.role:0')->name('promotion-wallet');

    Route::redirect('/room-single.html', '/customer/rooms-single');
    Route::redirect('/about', '/customer/promotion');
    Route::redirect('/restaurant', '/customer/services');
    Route::redirect('/rooms-search', '/customer/rooms-booking');
    Route::redirect('/booking', '/customer/info-booking');
});

Route::redirect('/room-single.html', '/customer/rooms-single');

/*
|--------------------------------------------------------------------------
| Auth UI
|--------------------------------------------------------------------------
*/

Route::view('/login', 'auth.login')->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::redirect('/admin/login', '/login');
Route::redirect('/reception/login', '/login');
Route::view('/forgot-password', 'auth.recoverpw')->name('auth.recoverpw');
Route::view('/new-password', 'auth.new-password')->name('auth.new-password');
Route::view('/sign-up', 'auth.register')->name('auth.signup');
Route::post('/register/step-1', [AuthController::class, 'registerStepOne'])->name('register.step1');
Route::get('/register/details', [AuthController::class, 'showRegisterDetails'])->name('register.details');
Route::get('/api/districts/{provinceCode}', [AuthController::class, 'getDistricts'])->name('api.districts');
Route::post('/register/step-2', [AuthController::class, 'registerStepTwo'])->name('register.step2');
Route::redirect('/sign-in', '/login')->name('auth.signin');

/*
|--------------------------------------------------------------------------
| Landing
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/dashboard');
Route::redirect('/dashboard', '/customer')->name('dashboard');

/*
|--------------------------------------------------------------------------
| Home admin UI
|--------------------------------------------------------------------------
*/

Route::middleware('account.role:2')->prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'hotel-management.report')->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Hotel Management UI
|--------------------------------------------------------------------------
*/

Route::middleware('account.role:2')->prefix('hotel')->name('hotel.')->group(function () {
    Route::view('/reports', 'hotel-management.report')->name('reports.index');
    Route::get('/room-amenities', function () {
        $amenities = TienNghi::orderByDesc('MaTienNghi')->get();

        return view('hotel-management.room-amenities.index', [
            'amenities' => $amenities,
        ]);
    })->name('room-amenities.index');
    Route::view('/room-amenities/trash', 'hotel-management.room-amenities.trash')->name('room-amenities.trash');
    Route::view('/room-amenities/create', 'hotel-management.room-amenities.form')->name('room-amenities.create');
    Route::view('/room-amenities/{recordId}/edit', 'hotel-management.room-amenities.form')->name('room-amenities.edit');
    Route::view('/room-amenities/{recordId}/assign', 'hotel-management.room-amenities.assign')->name('room-amenities.assign');
    Route::get('/room-amenities/{recordId}', function ($recordId) {
        return view('hotel-management.room-amenities.show', [
            'amenity' => TienNghi::findOrFail($recordId),
        ]);
    })->name('room-amenities.show');
    Route::prefix('accounts')->name('accounts.')->group(function () {
        Route::post('/', [AccountManagementController::class, 'store'])->name('store');
        Route::put('/{recordId}', [AccountManagementController::class, 'update'])->name('update');
    });
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', function () {
            $bookings = DatPhong::with('khachHang')
                ->orderByDesc('MaDatPhong')
                ->get();

            return view('hotel-management.bookings.index', [
                'bookings' => $bookings,
            ]);
        })->name('index');
        Route::get('/{recordId}', function ($recordId) {
            $booking = DatPhong::with([
                'khachHang.taiKhoan',
                'chiTietDatPhong.phong.loaiPhong.bangGias',
                'hoaDon.thanhToans',
                'hoaDon.khuyenMai',
            ])->findOrFail($recordId);

            return view('hotel-management.bookings.show', [
                'booking' => $booking,
            ]);
        })->name('show');
    });
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', function () {
            $customers = KhachHang::with('taiKhoan')
                ->orderByDesc('MaKH')
                ->get();

            return view('hotel-management.customers.index', [
                'customers' => $customers,
            ]);
        })->name('index');
        Route::get('/create', [CustomerManagementController::class, 'create'])->name('create');
        Route::post('/', [CustomerManagementController::class, 'store'])->name('store');
        Route::get('/{recordId}/edit', [CustomerManagementController::class, 'edit'])->name('edit');
        Route::put('/{recordId}', [CustomerManagementController::class, 'update'])->name('update');
        Route::get('/{recordId}', function ($recordId) {
            return view('hotel-management.customers.show', [
                'customer' => KhachHang::findOrFail($recordId),
            ]);
        })->name('show');
    });

    $hotelManagementViews = [
        'accounts' => 'hotel-management.accounts',
        'employees' => 'hotel-management.employees',
        'room-types' => 'hotel-management.room-types',
        'price-lists' => 'hotel-management.price-lists',
        'rooms' => 'hotel-management.rooms',
        'services' => 'hotel-management.services',
        'promotions' => 'hotel-management.promotions',
        'invoices' => 'hotel-management.invoices',
        'payments' => 'hotel-management.payments',
        'reviews' => 'hotel-management.reviews',
    ];

    foreach ($hotelManagementViews as $module => $viewBase) {
        Route::prefix($module)->name($module . '.')->group(function () use ($viewBase, $module) {
            Route::get('/', function () use ($viewBase, $module) {
                $viewData = match ($module) {
                    'accounts' => [
                        'accounts' => TaiKhoan::with(['khachHang', 'nhanVien'])
                            ->orderByDesc('MaTK')
                            ->get(),
                    ],
                    'employees' => [
                        'employees' => NhanVien::with('taiKhoan')
                            ->orderByDesc('MaNV')
                            ->get(),
                    ],
                    'room-types' => [
                        'roomTypes' => LoaiPhong::orderByDesc('MaLoaiPhong')->get(),
                    ],
                    'rooms' => [
                        'rooms' => Phong::with('loaiPhong')
                            ->orderByDesc('MaPhong')
                            ->get(),
                    ],
                    'services' => [
                        'services' => DichVu::with('hinhs')
                            ->orderByDesc('MaDV')
                            ->get(),
                    ],
                    'promotions' => [
                        'promotions' => KhuyenMai::orderByDesc('MaKM')->get(),
                    ],
                    'invoices' => [
                        'invoices' => HoaDon::with('nhanVien')
                            ->orderByDesc('MaHD')
                            ->get(),
                    ],
                    'payments' => [
                        'payments' => ThanhToan::with('hoaDon.datPhong.khachHang')
                            ->orderByDesc('MaTT')
                            ->get(),
                    ],
                    'reviews' => [
                        'reviews' => DanhGia::orderByDesc('MaDG')->get(),
                    ],
                    default => [],
                };

                return view($viewBase . '.index', $viewData);
            })->name('index');
            Route::view('/trash', $viewBase . '.trash')->name('trash');
            Route::view('/create', $viewBase . '.form')->name('create');
            Route::view('/{recordId}/edit', $viewBase . '.form')->name('edit');
            if ($module === 'services') {
                Route::get('/food-and-beverage', function () use ($viewBase) {
                    return view($viewBase . '.food-and-beverage', [
                        'services' => DichVu::with('hinhs')->orderByDesc('MaDV')->get(),
                    ]);
                })->name('food-and-beverage');
                Route::get('/room-service', function () use ($viewBase) {
                    return view($viewBase . '.room-service', [
                        'services' => DichVu::with('hinhs')->orderByDesc('MaDV')->get(),
                    ]);
                })->name('room-service');
                Route::get('/entertainment', function () use ($viewBase) {
                    return view($viewBase . '.entertainment', [
                        'services' => DichVu::with('hinhs')->orderByDesc('MaDV')->get(),
                    ]);
                })->name('entertainment');
            }
            Route::get('/{recordId}', function ($recordId) use ($viewBase, $module) {
                $viewData = match ($module) {
                    'accounts' => [
                        'account' => TaiKhoan::with(['khachHang', 'nhanVien'])->findOrFail($recordId),
                    ],
                    'employees' => [
                        'employee' => NhanVien::with('taiKhoan')->findOrFail($recordId),
                    ],
                    'room-types' => [
                        'roomType' => LoaiPhong::with(['tienNghis', 'hinhs'])->findOrFail($recordId),
                    ],
                    'rooms' => [
                        'room' => Phong::with([
                            'loaiPhong',
                            'chiTietDatPhong.datPhong' => function ($query) {
                                $today = Carbon::today()->toDateString();

                                $query->where('NgayNhanPhong', '<=', $today)
                                    ->where('NgayTraPhong', '>=', $today)
                                    ->whereIn('TinhTrang', [
                                        DatPhong::HOLD,
                                        DatPhong::CONFIRMED,
                                        DatPhong::CHECKED_IN,
                                    ]);
                            },
                        ])->findOrFail($recordId),
                    ],
                    'services' => [
                        'service' => DichVu::with('hinhs')->findOrFail($recordId),
                    ],
                    'promotions' => [
                        'promotion' => KhuyenMai::findOrFail($recordId),
                    ],
                    'invoices' => [
                        'invoice' => HoaDon::with([
                            'nhanVien',
                            'datPhong.khachHang',
                            'datPhong.chiTietDatPhong.phong.loaiPhong',
                            'khuyenMai',
                            'thanhToans',
                            'chiTietHoaDons.loaiPhong',
                            'chiTietHoaDons.suDung.dichVu',
                            'chiTietHoaDons.denBu',
                        ])->findOrFail($recordId),
                    ],
                    'payments' => [
                        'payment' => ThanhToan::with('hoaDon.datPhong.khachHang')->findOrFail($recordId),
                    ],
                    'reviews' => [
                        'review' => DanhGia::with([
                            'datPhong.khachHang',
                            'datPhong.chiTietDatPhong.phong.loaiPhong',
                        ])->findOrFail($recordId),
                    ],
                    default => [],
                };

                return view($viewBase . '.show', $viewData);
            })->name('show');
        });
    }
});

/*
|--------------------------------------------------------------------------
| Reception UI
|--------------------------------------------------------------------------
*/

Route::middleware('account.role:1')->prefix('reception')->name('reception.')->group(function () {
    Route::get('/dashboard', function () {
        $today = Carbon::today()->toDateString();

        $rooms = Phong::with([
            'loaiPhong',
            'chiTietDatPhong.datPhong' => function ($query) use ($today) {
                $query->where('NgayNhanPhong', '<=', $today)
                    ->where('NgayTraPhong', '>=', $today)
                    ->whereIn('TinhTrang', [
                        DatPhong::HOLD,
                        DatPhong::CONFIRMED,
                        DatPhong::CHECKED_IN,
                    ]);
            },
            'chiTietDatPhong.datPhong.khachHang',
        ])
            ->orderBy('SoPhong')
            ->get()
            ->map(function (Phong $room) {
                $activeBookings = $room->chiTietDatPhong
                    ->pluck('datPhong')
                    ->filter()
                    ->sortByDesc(fn ($booking) => match ((int) $booking->TinhTrang) {
                        DatPhong::CHECKED_IN => 3,
                        DatPhong::CONFIRMED => 2,
                        DatPhong::HOLD => 1,
                        default => 0,
                    });

                $activeBooking = $activeBookings->first();
                $status = match (true) {
                    (int) $room->TinhTrang === 3 => 'cleaning',
                    $activeBookings->contains(fn ($booking) => (int) $booking->TinhTrang === DatPhong::CHECKED_IN) => 'using',
                    $activeBookings->contains(fn ($booking) => in_array((int) $booking->TinhTrang, [DatPhong::HOLD, DatPhong::CONFIRMED], true)) => 'booked',
                    default => 'empty',
                };

                $statusLabel = [
                    'empty' => 'Trống',
                    'booked' => 'Đã đặt',
                    'using' => 'Đang sử dụng',
                    'cleaning' => 'Đang dọn dẹp',
                ][$status];

                $roomNumber = (string) $room->SoPhong;
                preg_match('/\d+/', $roomNumber, $matches);
                $floor = isset($matches[0]) ? (int) substr($matches[0], 0, 1) : 0;

                return [
                    'id' => $room->MaPhong,
                    'number' => $roomNumber,
                    'floor' => $floor > 0 ? $floor : 'Khác',
                    'type' => $room->loaiPhong?->TenLoaiPhong,
                    'status' => $status,
                    'statusLabel' => $statusLabel,
                    'bookingId' => $activeBooking?->MaDatPhong,
                    'guestName' => $activeBooking?->khachHang?->TenKH,
                ];
            });

        return view('receptionist.dashboard', [
            'roomFloors' => $rooms->groupBy('floor')->sortKeys(),
        ]);
    })->name('dashboard');
    Route::get('/booking-details/{bookingId}', function ($bookingId) {
        $booking = DatPhong::with([
            'khachHang.taiKhoan',
            'chiTietDatPhong.phong.loaiPhong',
            'hoaDon.khuyenMai',
            'hoaDon.thanhToans',
            'hoaDon.chiTietHoaDons.loaiPhong',
            'hoaDon.chiTietHoaDons.suDung.dichVu',
            'hoaDon.chiTietHoaDons.denBu',
            'suDungDichVu.dichVu',
        ])->findOrFail($bookingId);

        return view('receptionist.booking-detail', [
            'booking' => $booking,
        ]);
    })->name('booking-detail');

    Route::get('/customers', function () {
        $customers = KhachHang::with('taiKhoan')
            ->orderByDesc('MaKH')
            ->get();

        return view('receptionist.customers.index', [
            'customers' => $customers,
        ]);
    })->name('customers.index');
    Route::view('/customers/create', 'receptionist.customers.form')->name('customers.create');
    Route::view('/customers/{customerId}/edit', 'receptionist.customers.form')->name('customers.edit');
    Route::view('/customers/{customerId}', 'receptionist.customers.show')->name('customers.show');

    Route::get('/bookings', function () {
        $bookings = DatPhong::with([
            'khachHang',
            'chiTietDatPhong.phong.loaiPhong',
        ])
            ->orderByDesc('MaDatPhong')
            ->get();

        return view('receptionist.bookings.index', [
            'bookings' => $bookings,
        ]);
    })->name('bookings.index');
    Route::view('/bookings/create', 'receptionist.bookings.form')->name('bookings.create');
    Route::view('/bookings/{bookingId}/edit', 'receptionist.bookings.form')->name('bookings.edit');
    Route::view('/bookings/{bookingId}', 'receptionist.bookings.show')->name('bookings.show');

    Route::view('/services', 'receptionist.services.index')->name('services.index');
    Route::view('/services/{serviceUsageId}', 'receptionist.services.show')->name('services.show');
    Route::get('/check-ins/create', function () {
        $today = Carbon::today();

        $checkInBookings = DatPhong::with([
            'khachHang',
            'chiTietDatPhong.phong.loaiPhong',
            'luuTrus',
        ])
            ->where('TinhTrang', DatPhong::CONFIRMED)
            ->whereDate('NgayNhanPhong', $today->toDateString())
            ->orderBy('NgayNhanPhong')
            ->orderBy('MaDatPhong')
            ->get()
            ->map(function (DatPhong $booking) {
                $checkedRoomIds = $booking->luuTrus
                    ->pluck('MaPhong')
                    ->map(fn ($value) => (int) $value)
                    ->unique();

                $booking->setRelation(
                    'chiTietDatPhong',
                    $booking->chiTietDatPhong
                        ->reject(fn ($detail) => $checkedRoomIds->contains((int) $detail->MaPhong))
                        ->values()
                );

                return $booking;
            })
            ->filter(fn (DatPhong $booking) => $booking->chiTietDatPhong->isNotEmpty())
            ->values();

        return view('receptionist.check-in-form', [
            'checkInBookings' => $checkInBookings,
            'checkInStats' => [
                'waiting' => $checkInBookings->count(),
                'arrivalsToday' => $checkInBookings
                    ->filter(fn (DatPhong $booking) => Carbon::parse($booking->NgayNhanPhong)->isSameDay($today))
                    ->count(),
                'checkedIn' => DatPhong::where('TinhTrang', DatPhong::CHECKED_IN)->count(),
            ],
        ]);
    })->name('check-ins.create');
    Route::view('/check-outs/create', 'receptionist.check-out-form')->name('check-outs.create');
    Route::get('/payments', function () {
        $payments = ThanhToan::with([
            'hoaDon.datPhong.khachHang',
        ])
            ->orderByDesc('MaTT')
            ->get();

        return view('receptionist.payments.index', [
            'payments' => $payments,
        ]);
    })->name('payments.index');
    Route::view('/payments/create', 'receptionist.payments.form')->name('payments.create');
    Route::view('/payments/{paymentId}', 'receptionist.payments.show')->name('payments.show');
    Route::get('/invoices', function () {
        $invoices = HoaDon::with([
            'datPhong.khachHang',
            'nhanVien',
            'thanhToans',
        ])
            ->orderByDesc('MaHD')
            ->get();

        return view('receptionist.invoices.index', [
            'invoices' => $invoices,
        ]);
    })->name('invoices.index');
    Route::view('/invoices/{invoiceId}/edit', 'receptionist.invoices.form')->name('invoices.edit');
    Route::view('/invoices/{invoiceId}', 'receptionist.invoices.show')->name('invoices.show');
});
