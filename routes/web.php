<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountManagementController;
use App\Http\Controllers\CustomerManagementController;
use App\Exports\BookingReportExport;
use App\Exports\PaymentReportExport;
use App\Exports\RoomReportExport;
use App\Exports\RevenueReportExport;
use App\Exports\ServiceRevenueReportExport;
use App\Models\ChiTietDatPhong;
use App\Http\Controllers\Customer\PromotionController;
use App\Models\DatPhong;
use App\Models\DanhGia;
use App\Models\DichVu;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\KhuyenMai;
use App\Models\LoaiPhong;
use App\Models\LuuTru;
use App\Models\NhanVien;
use App\Models\Phong;
use App\Models\SuDungDichVu;
use App\Models\TaiKhoan;
use App\Models\ThanhToan;
use App\Models\TienNghi;
use App\Services\Reports\RevenueReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelWriter;

/*
|--------------------------------------------------------------------------
| Customer UI
|--------------------------------------------------------------------------
*/

$loadCustomerAddressData = function () {
    $cacheKey = 'address-kit.2025-07-01.all';

    if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
        return \Illuminate\Support\Facades\Cache::get($cacheKey);
    }

    try {
        $provincesResponse = \Illuminate\Support\Facades\Http::timeout(8)
            ->retry(2, 200)
            ->get('https://production.cas.so/address-kit/2025-07-01/provinces');

        $provinces = collect($provincesResponse->json('provinces', []))
            ->map(fn ($province) => [
                'code' => $province['code'],
                'name' => $province['name'],
            ])
            ->toArray();

        $communeResponses = \Illuminate\Support\Facades\Http::pool(fn ($pool) => collect($provinces)
            ->map(fn ($province) => $pool
                ->as($province['code'])
                ->timeout(8)
                ->get("https://production.cas.so/address-kit/2025-07-01/provinces/{$province['code']}/communes"))
            ->all());

        $communes = [];

        foreach ($provinces as $province) {
            $response = $communeResponses[$province['code']] ?? null;

            $communes[$province['code']] = collect($response?->json('communes', []) ?? [])
                ->map(fn ($commune) => [
                    'code' => $commune['code'],
                    'name' => $commune['name'],
                ])
                ->toArray();
        }

        $addressData = [
            'provinces' => $provinces,
            'communes' => $communes,
        ];

        \Illuminate\Support\Facades\Cache::put($cacheKey, $addressData, now()->addDays(30));

        return $addressData;
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('Address data fetch failed in customer profile', [
            'message' => $e->getMessage(),
        ]);

        return [
            'provinces' => [],
            'communes' => [],
        ];
    }
};

Route::prefix('customer')->name('customer.')->group(function () use ($loadCustomerAddressData) {
    Route::get('/', function () {
        $homeReviewModels = DanhGia::with([
            'datPhong.khachHang',
            'datPhong.chiTietDatPhong.phong.loaiPhong',
        ])
            ->orderByDesc('MaDG')
            ->get();

        $mapHomeReview = function (DanhGia $review) {
            $booking = $review->datPhong;
            $roomNames = $booking?->chiTietDatPhong
                ?->map(fn ($detail) => $detail->phong?->loaiPhong?->TenLoaiPhong)
                ->filter()
                ->unique()
                ->values()
                ->implode(', ');

            return [
                'customerName' => $booking?->khachHang?->TenKH ?: 'Khách hàng Peach Valley',
                'date' => $review->NgayDanhGia ? Carbon::parse($review->NgayDanhGia) : null,
                'description' => $review->MoTa ?: 'Khách hàng đã đánh giá trải nghiệm tại Peach Valley.',
                'rating' => max(1, min(5, (int) ($review->Sao ?? 5))),
                'roomName' => $roomNames ?: 'Peach Valley',
            ];
        };

        $homeReviews = $homeReviewModels->map($mapHomeReview)->values();
        $homeCarouselReviews = $homeReviewModels->take(5)->map($mapHomeReview)->values();
        $homeReviewAverage = $homeReviewModels->isNotEmpty()
            ? round((float) $homeReviewModels->avg('Sao'), 1)
            : 0;
        $homeReviewDistribution = collect(range(5, 1))->mapWithKeys(function ($star) use ($homeReviewModels) {
            $count = $homeReviewModels->where('Sao', $star)->count();
            $percent = $homeReviewModels->isNotEmpty()
                ? round(($count / $homeReviewModels->count()) * 100)
                : 0;

            return [$star => $percent];
        });
        $homeReviewRooms = $homeReviews
            ->pluck('roomName')
            ->filter(fn ($roomName) => $roomName && $roomName !== 'Peach Valley')
            ->unique()
            ->values();

        return view('customer.index', [
            'homeCarouselReviews' => $homeCarouselReviews,
            'homeReviews' => $homeReviews,
            'homeReviewAverage' => $homeReviewAverage,
            'homeReviewDistribution' => $homeReviewDistribution,
            'homeReviewRooms' => $homeReviewRooms,
        ]);
    })->name('home');
    Route::get('/promotion', [PromotionController::class, 'index'])->name('promotion');
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
            $serviceBookingOptions = DatPhong::with([
                'chiTietDatPhong' => function ($query) {
                    $query->where('TrangThai', \App\Models\ChiTietDatPhong::CHECKED_IN);
                },
                'chiTietDatPhong.phong',
            ])
                ->where('MaKH', $customerId)
                ->whereHas('chiTietDatPhong', function ($query) {
                    $query->where('TrangThai', \App\Models\ChiTietDatPhong::CHECKED_IN);
                })
                ->orderByDesc('MaDatPhong')
                ->get()
                ->filter(function (DatPhong $booking) {
                    return \Illuminate\Support\Carbon::parse($booking->NgayTraPhong)->setTime(14, 0)->gte(now());
                })
                ->flatMap(function (DatPhong $booking) {
                    return $booking->chiTietDatPhong
                        ->map(function ($detail) use ($booking) {
                            $roomNumber = $detail->phong?->SoPhong;

                            return [
                                'id' => (string) $detail->MaCTDP,
                                'bookingId' => (string) $booking->MaDatPhong,
                                'roomId' => (string) $detail->MaPhong,
                                'roomNumber' => $roomNumber ? (string) $roomNumber : '',
                                'label' => $roomNumber
                                    ? "#{$booking->MaDatPhong} - Phòng {$roomNumber}"
                                    : "#{$booking->MaDatPhong}",
                                'checkIn' => \Illuminate\Support\Carbon::parse($booking->NgayNhanPhong)->toDateString(),
                                'checkOut' => \Illuminate\Support\Carbon::parse($booking->NgayTraPhong)->toDateString(),
                            ];
                        });
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

        $bookingPromotions = collect();

        if ($bookingCustomer) {
            $today = Carbon::today()->toDateString();

            $bookingPromotions = \App\Models\KhoKhuyenMai::with('khuyenMai')
                ->where('MaKH', $bookingCustomer->MaKH)
                ->where('TrangThai', 0)
                ->whereHas('khuyenMai', function ($query) use ($today) {
                    $query
                        ->whereDate('NgayBatDau', '<=', $today)
                        ->whereDate('NgayKetThuc', '>=', $today);
                })
                ->get()
                ->map(function ($walletItem) {
                    $promotion = $walletItem->khuyenMai;

                    return [
                        'code' => (string) $walletItem->MaKM,
                        'name' => (string) ($promotion?->TenKM ?? $walletItem->MaKM),
                        'discountPercent' => (float) ($promotion?->PhanTramGiamGia ?? 0),
                        'expiresAt' => $promotion?->NgayKetThuc,
                    ];
                })
                ->sortByDesc('discountPercent')
                ->values();
        }

        return view('customer.info-booking', [
            'bookingAccount' => $authAccount,
            'bookingCustomer' => $bookingCustomer,
            'bookingPromotions' => $bookingPromotions,
        ]);
    })->name('info-booking');
    Route::get('/profile', function () use ($loadCustomerAddressData) {
        $authAccount = session('auth_account', []);
        $profileCustomer = null;

        if (!empty($authAccount['MaKH'])) {
            $profileCustomer = \App\Models\KhachHang::with('taiKhoan')->find($authAccount['MaKH']);
        } elseif (!empty($authAccount['MaTK'])) {
            $profileCustomer = \App\Models\KhachHang::with('taiKhoan')
                ->where('MaTK', $authAccount['MaTK'])
                ->first();
        }

        $addressData = $loadCustomerAddressData();

        return view('customer.profile', [
            'profileAccount' => $authAccount,
            'profileCustomer' => $profileCustomer,
            'provinces' => $addressData['provinces'],
            'communes' => $addressData['communes'],
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
                ->whereDoesntHave('chiTietDatPhong', function ($query) {
                    $query->where('TrangThai', \App\Models\ChiTietDatPhong::CHECKED_IN);
                })
                ->whereDate('NgayNhanPhong', '<', now()->toDateString())
                ->pluck('MaDatPhong');

            if ($expiredBookingIds->isNotEmpty()) {
                \Illuminate\Support\Facades\DB::transaction(function () use ($expiredBookingIds) {
                    \App\Models\DatPhong::whereIn('MaDatPhong', $expiredBookingIds)
                        ->update(['TinhTrang' => \App\Models\DatPhong::CANCELLED]);

                    \App\Models\ChiTietDatPhong::whereIn('MaDatPhong', $expiredBookingIds)
                        ->update(['TrangThai' => \App\Models\ChiTietDatPhong::CANCELLED]);

                    \App\Models\HoaDon::whereIn('MaDatPhong', $expiredBookingIds)
                        ->update(['TrangThai' => 3]);
                });
            }

            $customerBookings = \App\Models\DatPhong::with([
                'khachHang.taiKhoan',
                'hoaDon.khuyenMai',
                'hoaDon.chiTietHoaDons.loaiPhong',
                'hoaDon.thanhToans',
                'chiTietDatPhong.phong.loaiPhong',
                'danhGia',
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

            \App\Models\ChiTietDatPhong::where('MaDatPhong', $datPhong->MaDatPhong)
                ->update(['TrangThai' => \App\Models\ChiTietDatPhong::CANCELLED]);

            \App\Models\HoaDon::where('MaDatPhong', $datPhong->MaDatPhong)
                ->update(['TrangThai' => 3]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Đã hủy đặt phòng thành công.',
        ]);
    })->middleware('account.role:0')->name('my-bookings.cancel');
    Route::get('/promotion-wallet', function () {
        $authAccount = session('auth_account');
        $customerId = $authAccount['MaKH'] ?? null;

        if (empty($customerId) && !empty($authAccount['MaTK'])) {
            $customerId = \App\Models\TaiKhoan::where('MaTK', $authAccount['MaTK'])->value('MaKH');
        }

        $customer = $customerId
            ? \App\Models\KhachHang::select(['MaKH', 'DIEM'])->find($customerId)
            : null;

        $promotionWalletItems = $customerId
            ? \App\Models\KhoKhuyenMai::with('khuyenMai')
                ->where('MaKH', $customerId)
                ->whereHas('khuyenMai')
                ->get()
                ->sort(function ($left, $right) {
                    $statusCompare = ((int) ($left->TrangThai ?? 0)) <=> ((int) ($right->TrangThai ?? 0));

                    if ($statusCompare !== 0) {
                        return $statusCompare;
                    }

                    return strcmp((string) ($right->MaKM ?? ''), (string) ($left->MaKM ?? ''));
                })
                ->values()
            : collect();

        return view('customer.promotion-wallet', [
            'customer' => $customer,
            'promotionWalletItems' => $promotionWalletItems,
        ]);
    })->middleware('account.role:0')->name('promotion-wallet');

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
Route::view('/verify-otp', 'auth.verify-otp')->name('auth.verify-otp');
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

$buildReportData = function () {
    $reportDefaultTo = Carbon::today();
    $reportDefaultFrom = $reportDefaultTo->copy()->subMonth();
    $today = $reportDefaultTo->toDateString();
    $serviceRevenueToday = $reportDefaultTo->toDateString();
    $reportDefaultFromDate = $reportDefaultFrom->toDateString();
    $reportDefaultToDate = $reportDefaultTo->toDateString();
    $serviceRevenueItems = SuDungDichVu::with(['dichVu', 'chiTietHoaDon'])
        ->whereHas('dichVu')
        ->get()
        ->map(function (SuDungDichVu $usage) {
            $service = $usage->dichVu;
            $invoiceDetail = $usage->chiTietHoaDon->first();
            $quantity = max(0, (int) ($invoiceDetail?->SoLuong ?? $usage->SoLuong ?? 0));
            $unitPrice = (float) ($invoiceDetail?->DonGia ?? $service?->GiaDV ?? 0);

            return [
                'date' => $usage->ThoiGian ? Carbon::parse($usage->ThoiGian)->toDateString() : null,
                'type' => (string) ((int) ($service?->LoaiDV ?? 0)),
                'type_label' => $service?->LoaiDVText ?? 'Khác',
                'service_id' => $service?->MaDV,
                'service_name' => $service?->TenDV ?? 'Dịch vụ',
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'revenue' => $quantity * $unitPrice,
            ];
        })
        ->filter(fn (array $item) => $item['date'] !== null)
        ->values();
    $revenueItems = app(RevenueReportService::class)->invoiceItems();

    $stayingGuestCount = LuuTru::whereHas('datPhong', function ($query) use ($today) {
            $query
                ->whereDate('NgayNhanPhong', '<=', $today)
                ->whereDate('NgayTraPhong', '>=', $today)
                ->whereIn('TinhTrang', [
                    DatPhong::CONFIRMED,
                    DatPhong::CHECKED_IN,
                ]);
        })
        ->whereExists(function ($query) {
            $query
                ->select(DB::raw(1))
                ->from('ChiTietDatPhong')
                ->whereColumn('ChiTietDatPhong.MaDatPhong', 'LuuTru.MaDatPhong')
                ->whereColumn('ChiTietDatPhong.MaPhong', 'LuuTru.MaPhong')
                ->where('ChiTietDatPhong.TrangThai', ChiTietDatPhong::CHECKED_IN);
        })
        ->count();
    $todayBookingCount = DatPhong::whereDate('NgayDat', $reportDefaultTo->toDateString())
        ->where('TinhTrang', '!=', DatPhong::CANCELLED)
        ->count();
    $averageRating = round((float) DanhGia::whereBetween('NgayDanhGia', [
        $reportDefaultFrom->copy()->startOfDay(),
        $reportDefaultTo->copy()->endOfDay(),
    ])->avg('Sao'), 1);
    $roomStatusItems = Phong::with([
            'loaiPhong',
            'chiTietDatPhong' => function ($query) {
                $query->where('TrangThai', '!=', ChiTietDatPhong::CANCELLED);
            },
            'chiTietDatPhong.datPhong' => function ($query) use ($today) {
                $query
                    ->where('NgayNhanPhong', '<=', $today)
                    ->where('NgayTraPhong', '>=', $today)
                    ->whereIn('TinhTrang', [
                        DatPhong::CONFIRMED,
                        DatPhong::CHECKED_IN,
                    ]);
            },
        ])
        ->get()
        ->map(function (Phong $room) {
            $activeDetails = $room->chiTietDatPhong->filter(fn ($detail) => $detail->datPhong);
            $status = match (true) {
                $activeDetails->contains(fn ($detail) => (int) $detail->TrangThai === ChiTietDatPhong::CHECKED_IN) => 2,
                $activeDetails->contains(fn ($detail) => (int) $detail->TrangThai === ChiTietDatPhong::BOOKED
                    && (int) $detail->datPhong->TinhTrang === DatPhong::CONFIRMED) => 1,
                default => 0,
            };

            return [
                'room_id' => $room->MaPhong,
                'room_number' => $room->SoPhong,
                'room_type_id' => $room->MaLoaiPhong ? (string) $room->MaLoaiPhong : '',
                'room_type_name' => $room->loaiPhong?->TenLoaiPhong ?? 'Chưa phân loại',
                'status' => $status,
            ];
        })
        ->values();
    $roomCount = $roomStatusItems->count();
    $roomUsingCount = $roomStatusItems->where('status', 2)->count();
    $roomEmptyCount = $roomStatusItems->where('status', 0)->count();
    $roomTypeOptions = LoaiPhong::orderBy('TenLoaiPhong')
        ->get(['MaLoaiPhong', 'TenLoaiPhong']);
    $roomCapacityItems = Phong::select('MaPhong', 'MaLoaiPhong')
        ->get()
        ->map(fn (Phong $room) => [
            'room_id' => $room->MaPhong,
            'room_type_id' => $room->MaLoaiPhong ? (string) $room->MaLoaiPhong : '',
        ])
        ->values();
    $roomOccupancyItems = ChiTietDatPhong::with(['datPhong', 'phong.loaiPhong.khuyenMai'])
        ->where('TrangThai', ChiTietDatPhong::CHECKED_IN)
        ->whereHas('datPhong')
        ->whereHas('phong.loaiPhong')
        ->get()
        ->map(function (ChiTietDatPhong $detail) {
            $booking = $detail->datPhong;
            $room = $detail->phong;
            $roomType = $room?->loaiPhong;

            return [
                'detail_id' => $detail->MaCTDP,
                'room_id' => $detail->MaPhong,
                'room_type_id' => $room?->MaLoaiPhong ? (string) $room->MaLoaiPhong : '',
                'room_type_name' => $roomType?->TenLoaiPhong ?? 'Loại phòng',
                'check_in' => $booking?->NgayNhanPhong ? Carbon::parse($booking->NgayNhanPhong)->toDateString() : null,
                'check_out' => $booking?->NgayTraPhong ? Carbon::parse($booking->NgayTraPhong)->toDateString() : null,
                'nightly_price' => (float) ($roomType?->giaSauKhuyenMai($booking?->NgayNhanPhong) ?? 0),
            ];
        })
        ->filter(fn (array $item) => $item['check_in'] !== null && $item['check_out'] !== null)
        ->values();

    return [
        'customerCount' => $stayingGuestCount,
        'stayingGuestCount' => $stayingGuestCount,
        'todayBookingCount' => $todayBookingCount,
        'averageRating' => $averageRating,
        'roomCount' => $roomCount,
        'roomUsingCount' => $roomUsingCount,
        'roomEmptyCount' => $roomEmptyCount,
        'serviceRevenueItems' => $serviceRevenueItems,
        'serviceRevenueToday' => $serviceRevenueToday,
        'reportDefaultFromDate' => $reportDefaultFromDate,
        'reportDefaultToDate' => $reportDefaultToDate,
        'revenueItems' => $revenueItems,
        'roomStatusItems' => $roomStatusItems,
        'roomTypeOptions' => $roomTypeOptions,
        'roomCapacityItems' => $roomCapacityItems,
        'roomOccupancyItems' => $roomOccupancyItems,
    ];
};

$reportExporterName = function (): string {
    $authAccount = session('auth_account', []);

    if (!empty($authAccount['MaNV'])) {
        $employee = NhanVien::find($authAccount['MaNV']);

        if ($employee?->TenNV) {
            return $employee->TenNV;
        }
    }

    return $authAccount['Ten'] ?? 'Admin';
};

/*
|--------------------------------------------------------------------------
| Home admin UI
|--------------------------------------------------------------------------
*/

Route::middleware('account.role:2')->prefix('admin')->name('admin.')->group(function () use ($buildReportData) {
    Route::get('/dashboard', function () use ($buildReportData) {
        return view('hotel-management.report', $buildReportData());
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Hotel Management UI
|--------------------------------------------------------------------------
*/

Route::middleware('account.role:2')->prefix('hotel')->name('hotel.')->group(function () use ($buildReportData, $reportExporterName) {
    Route::get('/reports', function () use ($buildReportData) {
        return view('hotel-management.report', $buildReportData());
    })->name('reports.index');
    Route::get('/reports/export/revenue', function (Request $request) use ($reportExporterName) {
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'period' => ['nullable', 'in:day,month,quarter,year'],
            'format' => ['nullable', 'in:xlsx,csv'],
        ]);

        $from = Carbon::parse($validated['from'])->toDateString();
        $to = Carbon::parse($validated['to'])->toDateString();
        $period = $validated['period'] ?? 'day';
        $format = $validated['format'] ?? 'xlsx';
        $writerType = $format === 'csv' ? ExcelWriter::CSV : ExcelWriter::XLSX;
        $filename = sprintf('bao-cao-doanh-thu-%s-%s.%s', $from, $to, $format);

        return Excel::download(new RevenueReportExport($from, $to, $period, $reportExporterName()), $filename, $writerType);
    })->name('reports.export.revenue');
    Route::get('/reports/export/bookings', function (Request $request) use ($reportExporterName) {
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'format' => ['nullable', 'in:xlsx,csv'],
        ]);

        $from = Carbon::parse($validated['from'])->toDateString();
        $to = Carbon::parse($validated['to'])->toDateString();
        $format = $validated['format'] ?? 'xlsx';
        $writerType = $format === 'csv' ? ExcelWriter::CSV : ExcelWriter::XLSX;
        $filename = sprintf('bao-cao-booking-%s-%s.%s', $from, $to, $format);

        return Excel::download(new BookingReportExport($from, $to, $reportExporterName()), $filename, $writerType);
    })->name('reports.export.bookings');
    Route::get('/reports/export/rooms', function (Request $request) use ($reportExporterName) {
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'format' => ['nullable', 'in:xlsx,csv'],
        ]);

        $from = Carbon::parse($validated['from'])->toDateString();
        $to = Carbon::parse($validated['to'])->toDateString();
        $format = $validated['format'] ?? 'xlsx';
        $writerType = $format === 'csv' ? ExcelWriter::CSV : ExcelWriter::XLSX;
        $filename = sprintf('bao-cao-phong-%s-%s.%s', $from, $to, $format);

        return Excel::download(new RoomReportExport($from, $to, $reportExporterName()), $filename, $writerType);
    })->name('reports.export.rooms');
    Route::get('/reports/export/payments', function (Request $request) use ($reportExporterName) {
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'format' => ['nullable', 'in:xlsx,csv'],
        ]);

        $from = Carbon::parse($validated['from'])->toDateString();
        $to = Carbon::parse($validated['to'])->toDateString();
        $format = $validated['format'] ?? 'xlsx';
        $writerType = $format === 'csv' ? ExcelWriter::CSV : ExcelWriter::XLSX;
        $filename = sprintf('bao-cao-thanh-toan-%s-%s.%s', $from, $to, $format);

        return Excel::download(new PaymentReportExport($from, $to, $reportExporterName()), $filename, $writerType);
    })->name('reports.export.payments');
    Route::get('/reports/export/services', function (Request $request) use ($reportExporterName) {
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'format' => ['nullable', 'in:xlsx,csv'],
        ]);

        $from = Carbon::parse($validated['from'])->toDateString();
        $to = Carbon::parse($validated['to'])->toDateString();
        $format = $validated['format'] ?? 'xlsx';
        $writerType = $format === 'csv' ? ExcelWriter::CSV : ExcelWriter::XLSX;
        $filename = sprintf('bao-cao-dich-vu-%s-%s.%s', $from, $to, $format);

        return Excel::download(new ServiceRevenueReportExport($from, $to, $reportExporterName()), $filename, $writerType);
    })->name('reports.export.services');
    Route::get('/room-amenities', function () {
        $amenities = TienNghi::orderByDesc('MaTienNghi')->get();

        return view('hotel-management.room-amenities.index', [
            'amenities' => $amenities,
        ]);
    })->name('room-amenities.index');
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
            $bookings = DatPhong::with(['khachHang', 'chiTietDatPhong'])
                ->orderByDesc('MaDatPhong')
                ->get();

            return view('hotel-management.bookings.index', [
                'bookings' => $bookings,
            ]);
        })->name('index');
        Route::get('/{recordId}', function ($recordId) {
            $booking = DatPhong::with([
                'khachHang.taiKhoan',
                'chiTietDatPhong.phong.loaiPhong.khuyenMai',
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
                        'accounts' => TaiKhoan::with([
                            'khachHang' => fn ($query) => $query->withCount('datPhongs'),
                            'nhanVien' => fn ($query) => $query->withCount('hoaDons'),
                        ])
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
                        'rooms' => Phong::with([
                            'loaiPhong',
                            'chiTietDatPhong' => function ($query) {
                                $query->whereNotIn('TrangThai', [
                                    \App\Models\ChiTietDatPhong::CANCELLED,
                                    \App\Models\ChiTietDatPhong::CLEANED,
                                ]);
                            },
                            'chiTietDatPhong.datPhong' => function ($query) {
                                $today = Carbon::today()->toDateString();

                                $query->where('NgayNhanPhong', '<=', $today)
                                    ->where('NgayTraPhong', '>=', $today)
                                    ->whereIn('TinhTrang', [
                                        DatPhong::HOLD,
                                        DatPhong::CONFIRMED,
                                        DatPhong::CHECKED_IN,
                                        DatPhong::CHECKED_OUT,
                                    ]);
                            },
                        ])
                            ->orderByDesc('MaPhong')
                            ->get()
                            ->map(function (Phong $room) {
                                $activeDetails = $room->chiTietDatPhong
                                    ->filter(fn ($detail) => $detail->datPhong)
                                    ->sortByDesc(fn ($detail) => match ((int) $detail->TrangThai) {
                                        \App\Models\ChiTietDatPhong::CHECKED_IN => 3,
                                        \App\Models\ChiTietDatPhong::CHECKED_OUT => 2,
                                        \App\Models\ChiTietDatPhong::BOOKED => match ((int) $detail->datPhong->TinhTrang) {
                                            DatPhong::CONFIRMED => 2,
                                            DatPhong::HOLD => 1,
                                            default => 0,
                                        },
                                        default => 0,
                                    });

                                $status = match (true) {
                                    $activeDetails->contains(fn ($detail) => (int) $detail->TrangThai === \App\Models\ChiTietDatPhong::CHECKED_IN) => 2,
                                    $activeDetails->contains(fn ($detail) => (int) $detail->TrangThai === \App\Models\ChiTietDatPhong::CHECKED_OUT) => 3,
                                    $activeDetails->contains(fn ($detail) => (int) $detail->TrangThai === \App\Models\ChiTietDatPhong::BOOKED
                                        && in_array((int) $detail->datPhong->TinhTrang, [DatPhong::HOLD, DatPhong::CONFIRMED], true)) => 1,
                                    default => 0,
                                };

                                $room->TinhTrang = $status;
                                $room->TinhTrangHienTai = $status;
                                $room->MaDatPhongHienTai = $activeDetails->first()?->datPhong?->MaDatPhong;
                                unset($room->chiTietDatPhong);

                                return $room;
                            }),
                    ],
                    'services' => [
                        'services' => DichVu::with('hinhs')
                            ->orderByDesc('MaDV')
                            ->get(),
                    ],
                    'promotions' => [
                        'promotions' => KhuyenMai::with('hinhs')->orderByDesc('MaKM')->get(),
                    ],
                    'invoices' => [
                        'invoices' => HoaDon::with(['nhanVien', 'datPhong.khachHang'])
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
                            'chiTietDatPhong' => function ($query) {
                                $query->where('TrangThai', '!=', \App\Models\ChiTietDatPhong::CANCELLED);
                            },
                            'chiTietDatPhong.datPhong' => function ($query) {
                                $today = Carbon::today()->toDateString();

                                $query->where('NgayNhanPhong', '<=', $today)
                                    ->where('NgayTraPhong', '>=', $today)
                                    ->whereIn('TinhTrang', [
                                        DatPhong::HOLD,
                                        DatPhong::CONFIRMED,
                                        DatPhong::CHECKED_IN,
                                        DatPhong::CHECKED_OUT,
                                    ]);
                            },
                        ])->findOrFail($recordId),
                    ],
                    'services' => [
                        'service' => DichVu::with('hinhs')->findOrFail($recordId),
                    ],
                    'promotions' => [
                        'promotion' => KhuyenMai::with('hinhs')->findOrFail($recordId),
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
                            'chiTietHoaDons.suDung.chiTietDatPhong.phong',
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

$loadReceptionAddressData = function (): array {
    $cacheKey = 'address-kit.2025-07-01.all';

    if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
        return \Illuminate\Support\Facades\Cache::get($cacheKey);
    }

    try {
        $provincesResponse = \Illuminate\Support\Facades\Http::timeout(8)
            ->retry(2, 200)
            ->get('https://production.cas.so/address-kit/2025-07-01/provinces');

        $provinces = collect($provincesResponse->json('provinces', []))
            ->map(fn ($province) => [
                'code' => $province['code'],
                'name' => $province['name'],
            ])
            ->toArray();

        $communeResponses = \Illuminate\Support\Facades\Http::pool(fn ($pool) => collect($provinces)
            ->map(fn ($province) => $pool
                ->as($province['code'])
                ->timeout(8)
                ->get("https://production.cas.so/address-kit/2025-07-01/provinces/{$province['code']}/communes"))
            ->all());

        $communes = [];

        foreach ($provinces as $province) {
            $response = $communeResponses[$province['code']] ?? null;

            $communes[$province['code']] = collect($response?->json('communes', []) ?? [])
                ->map(fn ($commune) => [
                    'code' => $commune['code'],
                    'name' => $commune['name'],
                ])
                ->toArray();
        }

        $addressData = [
            'provinces' => $provinces,
            'communes' => $communes,
        ];

        \Illuminate\Support\Facades\Cache::put($cacheKey, $addressData, now()->addDays(30));

        return $addressData;
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('Address data fetch failed in reception customer form', [
            'message' => $e->getMessage(),
        ]);

        return [
            'provinces' => [],
            'communes' => [],
        ];
    }
};

Route::middleware('account.role:1')->prefix('reception')->name('reception.')->group(function () use ($loadReceptionAddressData) {
    Route::get('/dashboard', function () {
        $today = Carbon::today()->toDateString();

        $rooms = Phong::select(['MaPhong', 'SoPhong', 'MaLoaiPhong'])
            ->with([
            'loaiPhong:MaLoaiPhong,TenLoaiPhong',
            'chiTietDatPhong' => function ($query) {
                $query
                    ->select(['MaCTDP', 'MaDatPhong', 'MaPhong', 'TrangThai'])
                    ->whereNotIn('TrangThai', [
                        \App\Models\ChiTietDatPhong::CANCELLED,
                        \App\Models\ChiTietDatPhong::CLEANED,
                    ]);
            },
            'chiTietDatPhong.datPhong' => function ($query) use ($today) {
                $query
                    ->select(['MaDatPhong', 'MaKH', 'NgayNhanPhong', 'NgayTraPhong', 'TinhTrang'])
                    ->where('NgayNhanPhong', '<=', $today)
                    ->where('NgayTraPhong', '>=', $today)
                    ->whereIn('TinhTrang', [
                        DatPhong::HOLD,
                        DatPhong::CONFIRMED,
                        DatPhong::CHECKED_IN,
                        DatPhong::CHECKED_OUT,
                    ]);
            },
            'chiTietDatPhong.datPhong.khachHang:MaKH,TenKH',
        ])
            ->orderBy('SoPhong')
            ->get()
            ->map(function (Phong $room) use ($today) {
                $activeDetails = $room->chiTietDatPhong
                    ->filter(fn ($detail) => $detail->datPhong)
                    ->sortByDesc(fn ($detail) => match ((int) $detail->TrangThai) {
                        \App\Models\ChiTietDatPhong::CHECKED_IN => 3,
                        \App\Models\ChiTietDatPhong::CHECKED_OUT => 2,
                        \App\Models\ChiTietDatPhong::BOOKED => match ((int) $detail->datPhong->TinhTrang) {
                            DatPhong::CONFIRMED => 2,
                            DatPhong::HOLD => 1,
                            default => 0,
                        },
                        default => 0,
                    });

                $activeDetail = $activeDetails->first();
                $activeBooking = $activeDetail?->datPhong;
                $status = match (true) {
                    $activeDetails->contains(fn ($detail) => (int) $detail->TrangThai === \App\Models\ChiTietDatPhong::CHECKED_IN) => 'using',
                    $activeDetails->contains(fn ($detail) => (int) $detail->TrangThai === \App\Models\ChiTietDatPhong::CHECKED_OUT) => 'cleaning',
                    $activeDetails->contains(fn ($detail) => (int) $detail->TrangThai === \App\Models\ChiTietDatPhong::BOOKED
                        && in_array((int) $detail->datPhong->TinhTrang, [DatPhong::HOLD, DatPhong::CONFIRMED], true)) => 'booked',
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
                $isCheckInDueToday = $activeBooking?->NgayNhanPhong
                    && Carbon::parse($activeBooking->NgayNhanPhong)->toDateString() === $today
                    && $status === 'booked';
                $isCheckOutDueToday = $activeBooking?->NgayTraPhong
                    && Carbon::parse($activeBooking->NgayTraPhong)->toDateString() === $today
                    && $status === 'using';

                return [
                    'id' => $room->MaPhong,
                    'number' => $roomNumber,
                    'floor' => $floor > 0 ? $floor : 'Khác',
                    'type' => $room->loaiPhong?->TenLoaiPhong,
                    'status' => $status,
                    'statusLabel' => $statusLabel,
                    'bookingDetailId' => $activeDetail?->MaCTDP,
                    'bookingId' => $activeBooking?->MaDatPhong,
                    'guestName' => $activeBooking?->khachHang?->TenKH,
                    'isDueToday' => $isCheckInDueToday || $isCheckOutDueToday,
                    'dueType' => $isCheckInDueToday ? 'check-in' : ($isCheckOutDueToday ? 'check-out' : null),
                    'dueLabel' => $isCheckInDueToday ? 'Đến hạn nhận phòng' : ($isCheckOutDueToday ? 'Đến hạn trả phòng' : ''),
                ];
            });

        return view('receptionist.dashboard', [
            'roomFloors' => $rooms->groupBy('floor')->sortKeys(),
        ]);
    })->name('dashboard');
    Route::get('/booking-details/{bookingDetailId}', function ($bookingDetailId) {
        $bookingDetail = \App\Models\ChiTietDatPhong::with([
            'phong.loaiPhong',
            'suDungDichVu.dichVu',
            'datPhong.khachHang.taiKhoan',
            'datPhong.hoaDon.khuyenMai',
            'datPhong.hoaDon.thanhToans',
            'datPhong.hoaDon.chiTietHoaDons.loaiPhong',
            'datPhong.hoaDon.chiTietHoaDons.suDung.dichVu',
            'datPhong.hoaDon.chiTietHoaDons.suDung.chiTietDatPhong.phong',
            'datPhong.hoaDon.chiTietHoaDons.denBu',
            'datPhong.luuTrus',
        ])->findOrFail($bookingDetailId);

        $booking = $bookingDetail->datPhong;
        $booking->setRelation('chiTietDatPhong', collect([$bookingDetail]));
        $booking->setRelation('luuTrus', ($booking->luuTrus ?? collect())
            ->where('MaPhong', (int) $bookingDetail->MaPhong)
            ->values());
        $booking->setRelation('suDungDichVu', ($bookingDetail->suDungDichVu ?? collect())->values());

        return view('receptionist.booking-detail', [
            'booking' => $booking,
            'selectedRoomDetail' => $bookingDetail,
        ]);
    })->name('booking-detail');

    Route::get('/customers', function () {
        $customers = KhachHang::select(['MaKH', 'TenKH', 'SoDienThoai', 'CCCD', 'NgaySinh', 'GioiTinh'])
            ->with('taiKhoan:MaTK,MaKH,Email,TrangThai')
            ->orderByDesc('MaKH')
            ->get();

        return view('receptionist.customers.index', [
            'customers' => $customers,
        ]);
    })->name('customers.index');
    Route::get('/customers/create', function () use ($loadReceptionAddressData) {
        $addressData = $loadReceptionAddressData();

        return view('receptionist.customers.form', [
            'provinces' => $addressData['provinces'],
            'communes' => $addressData['communes'],
        ]);
    })->name('customers.create');
    Route::get('/customers/{customerId}/edit', function ($customerId) use ($loadReceptionAddressData) {
        $customer = KhachHang::with('taiKhoan:MaTK,MaKH,Email,TrangThai')->findOrFail($customerId);
        $addressData = $loadReceptionAddressData();

        return view('receptionist.customers.form', [
            'customer' => $customer,
            'provinces' => $addressData['provinces'],
            'communes' => $addressData['communes'],
        ]);
    })->name('customers.edit');
    Route::get('/customers/{customerId}', function ($customerId) {
        $customer = KhachHang::with('taiKhoan:MaTK,MaKH,Email,TrangThai')->findOrFail($customerId);

        return view('receptionist.customers.show', [
            'customer' => $customer,
        ]);
    })->name('customers.show');

    Route::get('/bookings', function () {
        $bookings = DatPhong::select(['MaDatPhong', 'MaKH', 'NgayNhanPhong', 'NgayTraPhong', 'TinhTrang'])
            ->with([
            'khachHang:MaKH,TenKH,SoDienThoai',
            'chiTietDatPhong:MaCTDP,MaDatPhong,MaPhong,TrangThai',
            'chiTietDatPhong.phong:MaPhong,SoPhong,MaLoaiPhong',
            'chiTietDatPhong.phong.loaiPhong:MaLoaiPhong,TenLoaiPhong',
        ])
            ->orderByDesc('MaDatPhong')
            ->get();

        return view('receptionist.bookings.index', [
            'bookings' => $bookings,
        ]);
    })->name('bookings.index');
    Route::view('/bookings/create', 'receptionist.bookings.form')->name('bookings.create');
    Route::view('/bookings/{bookingId}/edit', 'receptionist.bookings.form')->name('bookings.edit');
    Route::get('/bookings/{bookingId}', function ($bookingId) {
        $booking = DatPhong::select(['MaDatPhong', 'MaKH', 'NgayDat', 'NgayNhanPhong', 'NgayTraPhong', 'SoLuong', 'TinhTrang'])
            ->with([
                'khachHang:MaKH,TenKH,SoDienThoai,CCCD,NgaySinh,GioiTinh,DiaChi',
                'chiTietDatPhong:MaCTDP,MaDatPhong,MaPhong,TrangThai',
                'chiTietDatPhong.phong:MaPhong,SoPhong,MaLoaiPhong',
                'chiTietDatPhong.phong.loaiPhong:MaLoaiPhong,TenLoaiPhong,NguoiLon,TreEm',
                'luuTrus:MaLuuTru,MaDatPhong,MaPhong,TenKhach,NgaySinh,SoDienThoai,CCCD',
                'luuTrus.phong:MaPhong,SoPhong',
                'hoaDon:MaHD,MaDatPhong',
            ])
            ->findOrFail($bookingId);

        return view('receptionist.bookings.show', [
            'booking' => $booking,
        ]);
    })->name('bookings.show');

    Route::get('/services', function () {
        $serviceRoomOptions = \App\Models\ChiTietDatPhong::select(['MaCTDP', 'MaDatPhong', 'MaPhong', 'TrangThai'])
            ->with([
            'phong:MaPhong,SoPhong',
            'datPhong:MaDatPhong,MaKH,NgayNhanPhong,NgayTraPhong,TinhTrang',
            'datPhong.khachHang:MaKH,TenKH',
        ])
            ->where('TrangThai', \App\Models\ChiTietDatPhong::CHECKED_IN)
            ->orderBy('MaDatPhong')
            ->orderBy('MaPhong')
            ->get()
            ->filter(function ($detail) {
                return $detail->datPhong?->NgayTraPhong
                    && \Illuminate\Support\Carbon::parse($detail->datPhong->NgayTraPhong)->setTime(14, 0)->gte(now());
            })
            ->map(function ($detail) {
                $booking = $detail->datPhong;
                $roomNumber = $detail->phong?->SoPhong;
                $customerName = $booking?->khachHang?->TenKH;

                return [
                    'id' => (string) $detail->MaCTDP,
                    'bookingId' => (string) $detail->MaDatPhong,
                    'roomId' => (string) $detail->MaPhong,
                    'roomNumber' => $roomNumber ? (string) $roomNumber : '',
                    'customerName' => $customerName ?: '',
                    'checkIn' => $booking?->NgayNhanPhong ? \Illuminate\Support\Carbon::parse($booking->NgayNhanPhong)->toDateString() : '',
                    'checkOut' => $booking?->NgayTraPhong ? \Illuminate\Support\Carbon::parse($booking->NgayTraPhong)->toDateString() : '',
                    'label' => trim(
                        ($roomNumber ? "Phòng {$roomNumber}" : "Phòng #{$detail->MaPhong}")
                        . " - Đặt phòng #{$detail->MaDatPhong}"
                        . ($customerName ? " - {$customerName}" : '')
                    ),
                    'label' => $roomNumber ? (string) $roomNumber : (string) $detail->MaPhong,
                ];
            })
            ->values();

        $serviceUsageItems = \App\Models\SuDungDichVu::select(['MaSuDung', 'MaCTDP', 'MaDV', 'SoLuong', 'ThoiGian'])
            ->with([
            'dichVu:MaDV,TenDV,LoaiDV,GiaDV',
            'chiTietDatPhong:MaCTDP,MaDatPhong,MaPhong,TrangThai',
            'chiTietDatPhong.phong:MaPhong,SoPhong',
            'chiTietDatPhong.datPhong:MaDatPhong,MaKH,TinhTrang',
            'chiTietDatPhong.datPhong.khachHang:MaKH,TenKH',
        ])
            ->whereHas('chiTietDatPhong.datPhong', function ($query) {
                $query->where('TinhTrang', DatPhong::CHECKED_IN);
            })
            ->orderByDesc('ThoiGian')
            ->orderByDesc('MaSuDung')
            ->get();

        return view('receptionist.services.index', [
            'services' => DichVu::with('hinhs')
                ->orderBy('LoaiDV')
                ->orderBy('TenDV')
                ->get(),
            'serviceRoomOptions' => $serviceRoomOptions,
            'serviceUsageItems' => $serviceUsageItems,
        ]);
    })->name('services.index');
    Route::view('/services/{serviceUsageId}', 'receptionist.services.show')->name('services.show');
    Route::get('/check-ins/create', function () {
        $today = Carbon::today();

        $checkInBookings = DatPhong::select(['MaDatPhong', 'MaKH', 'NgayNhanPhong', 'NgayTraPhong', 'SoLuong', 'TinhTrang'])
            ->with([
            'khachHang:MaKH,TenKH,SoDienThoai',
            'chiTietDatPhong' => function ($query) {
                $query
                    ->select(['MaCTDP', 'MaDatPhong', 'MaPhong', 'TrangThai'])
                    ->whereIn('TrangThai', [
                    \App\Models\ChiTietDatPhong::BOOKED,
                    \App\Models\ChiTietDatPhong::CHECKED_IN,
                ]);
            },
            'chiTietDatPhong.phong:MaPhong,SoPhong,MaLoaiPhong',
            'chiTietDatPhong.phong.loaiPhong:MaLoaiPhong,TenLoaiPhong,NguoiLon,TreEm',
        ])
            ->where('TinhTrang', DatPhong::CONFIRMED)
            ->whereDate('NgayNhanPhong', '<=', $today->toDateString())
            ->whereDate('NgayTraPhong', '>=', $today->toDateString())
            ->orderBy('NgayNhanPhong')
            ->orderBy('MaDatPhong')
            ->get()
            ->map(function (DatPhong $booking) {
                $booking->setRelation('chiTietDatPhong', $booking->chiTietDatPhong
                    ->sortBy(fn ($detail) => ((int) $detail->TrangThai * 1000000) + (int) $detail->MaPhong)
                    ->values());
                return $booking;
            })
            ->filter(fn (DatPhong $booking) => $booking->chiTietDatPhong
                ->contains(fn ($detail) => (int) $detail->TrangThai === \App\Models\ChiTietDatPhong::BOOKED))
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
    Route::get('/check-outs/create', function () {
        $today = Carbon::today();
        try {
            $selectedDate = request('checkout_date')
                ? Carbon::parse(request('checkout_date'))->startOfDay()
                : $today->copy();
        } catch (\Throwable $e) {
            $selectedDate = $today->copy();
        }
        if ($selectedDate->lt($today)) {
            $selectedDate = $today->copy();
        }
        $searchKeyword = trim((string) request('checkout_search', ''));
        $searchBookingId = preg_replace('/\D+/', '', $searchKeyword);
        $checkedInRoomDetailsFilter = function ($query) {
            $query->where('TrangThai', \App\Models\ChiTietDatPhong::CHECKED_IN);
        };
        $activeCheckOutBookingFilter = function ($query) use ($checkedInRoomDetailsFilter) {
            $query
                ->where(function ($statusQuery) use ($checkedInRoomDetailsFilter) {
                    $statusQuery
                        ->where('TinhTrang', DatPhong::CHECKED_IN)
                        ->orWhereHas('chiTietDatPhong', $checkedInRoomDetailsFilter);
                })
                ->whereHas('chiTietDatPhong', $checkedInRoomDetailsFilter);
        };

        $checkOutBookings = DatPhong::select([
            'MaDatPhong',
            'MaKH',
            'NgayNhanPhong',
            'NgayTraPhong',
            'SoLuong',
            'TinhTrang',
        ])
            ->with([
            'khachHang:MaKH,TenKH,SoDienThoai',
            'chiTietDatPhong' => function ($query) {
                $query
                    ->select(['MaCTDP', 'MaDatPhong', 'MaPhong', 'TrangThai'])
                    ->where('TrangThai', \App\Models\ChiTietDatPhong::CHECKED_IN);
            },
            'chiTietDatPhong.phong:MaPhong,SoPhong,MaLoaiPhong',
            'chiTietDatPhong.phong.loaiPhong:MaLoaiPhong,TenLoaiPhong,GiaPhong,MaKM',
            'chiTietDatPhong.phong.loaiPhong.khuyenMai:MaKM,NgayBatDau,NgayKetThuc,PhanTramGiamGia',
            'hoaDon:MaHD,MaDatPhong,TongTien,DaThanhToan',
            'luuTrus:MaLuuTru,MaDatPhong,MaPhong,TenKhach,NgaySinh,SoDienThoai,CCCD',
        ])
            ->where($activeCheckOutBookingFilter)
            ->whereDate('NgayTraPhong', $selectedDate->toDateString())
            ->when($searchKeyword !== '', function ($query) use ($searchKeyword, $searchBookingId) {
                $query->where(function ($searchQuery) use ($searchKeyword, $searchBookingId) {
                    $searchQuery->whereHas('khachHang', function ($customerQuery) use ($searchKeyword) {
                        $customerQuery->where('TenKH', 'like', '%' . $searchKeyword . '%');
                    });

                    if ($searchBookingId !== '') {
                        $searchQuery->orWhere('MaDatPhong', 'like', '%' . $searchBookingId . '%');
                    }
                });
            })
            ->orderBy('NgayTraPhong')
            ->orderBy('MaDatPhong')
            ->get()
            ->map(function (DatPhong $booking) {
                $booking->setRelation('chiTietDatPhong', $booking->chiTietDatPhong
                    ->sortBy(fn ($detail) => (int) ($detail->phong?->SoPhong ?? $detail->MaPhong))
                    ->values());

                return $booking;
            })
            ->filter(fn (DatPhong $booking) => $booking->chiTietDatPhong->isNotEmpty())
            ->values();

        $upcomingCheckOutCount = DatPhong::where($activeCheckOutBookingFilter)
            ->whereDate('NgayTraPhong', '>=', $today->toDateString())
            ->count();

        $todayCheckOutBookings = DatPhong::select(['MaDatPhong', 'NgayTraPhong', 'TinhTrang'])
            ->with([
            'chiTietDatPhong' => function ($query) {
                $query
                    ->select(['MaCTDP', 'MaDatPhong', 'MaPhong', 'TrangThai'])
                    ->where('TrangThai', \App\Models\ChiTietDatPhong::CHECKED_IN);
            },
        ])
            ->where($activeCheckOutBookingFilter)
            ->whereDate('NgayTraPhong', $today->toDateString())
            ->get()
            ->filter(fn (DatPhong $booking) => $booking->chiTietDatPhong->isNotEmpty())
            ->values();

        return view('receptionist.check-out-form', [
            'checkOutBookings' => $checkOutBookings,
            'checkOutFilters' => [
                'date' => $selectedDate->toDateString(),
                'search' => $searchKeyword,
                'minDate' => $today->toDateString(),
            ],
            'checkOutStats' => [
                'upcoming' => $upcomingCheckOutCount,
                'today' => $todayCheckOutBookings->count(),
                'roomsFreeing' => $todayCheckOutBookings->sum(fn (DatPhong $booking) => $booking->chiTietDatPhong->count()),
            ],
        ]);
    })->name('check-outs.create');
    Route::get('/payments', function () {
        $payments = ThanhToan::select([
            'MaTT',
            'MaHD',
            'SoTien',
            'PhuongThuc',
            'LoaiThanhToan',
            'NgayThanhToan',
            'NhaCungCap',
            'DinhDanhNguoiThanhToan',
            'MaGiaoDich',
            'MaGiaoDichCongThanhToan',
            'TrangThaiGiaoDich',
        ])
            ->with([
            'hoaDon:MaHD,MaDatPhong,TrangThai',
            'hoaDon.datPhong:MaDatPhong,MaKH',
            'hoaDon.datPhong.khachHang:MaKH,TenKH',
        ])
            ->orderByDesc('MaTT')
            ->get();

        return view('receptionist.payments.index', [
            'payments' => $payments,
        ]);
    })->name('payments.index');
    Route::get('/payments/create', function () {
        $formatDate = fn ($value) => $value ? Carbon::parse($value)->format('d/m/Y') : '--';
        $formatTime = fn ($value) => $value ? Carbon::parse($value)->format('H:i') : '--';
        $moneyValue = fn ($value) => (float) ($value ?? 0);
        $getNights = function (DatPhong $booking) {
            if (!$booking->NgayNhanPhong || !$booking->NgayTraPhong) {
                return 0;
            }

            return max(1, Carbon::parse($booking->NgayNhanPhong)->diffInDays(Carbon::parse($booking->NgayTraPhong)));
        };

        $bookingId = request('booking');
        $invoiceId = request('invoice');

        $query = DatPhong::with([
            'khachHang',
            'chiTietDatPhong.phong.loaiPhong.khuyenMai',
            'chiTietDatPhong.suDungDichVu.dichVu',
            'hoaDon.chiTietHoaDons.loaiPhong.khuyenMai',
            'hoaDon.chiTietHoaDons.suDung.dichVu',
            'hoaDon.chiTietHoaDons.suDung.chiTietDatPhong.phong',
            'hoaDon.chiTietHoaDons.denBu',
            'hoaDon.thanhToans',
        ])
            ->where(function ($query) {
                $query->where('TinhTrang', DatPhong::CHECKED_IN)
                    ->orWhereHas('chiTietDatPhong', function ($detailQuery) {
                        $detailQuery->where('TrangThai', \App\Models\ChiTietDatPhong::CHECKED_IN);
                    });
            });

        if ($bookingId) {
            $query->where('MaDatPhong', $bookingId);
        }

        if ($invoiceId) {
            $query->whereHas('hoaDon', function ($invoiceQuery) use ($invoiceId) {
                $invoiceQuery->where('MaHD', $invoiceId);
            });
        }

        $booking = $query
            ->orderBy('NgayTraPhong')
            ->orderBy('MaDatPhong')
            ->first();

        $paymentData = [
            'invoiceId' => '--',
            'bookingId' => '--',
            'customer' => '--',
            'phone' => '',
            'stay' => '--',
            'stayPeriod' => '--',
            'invoiceDate' => $formatDate(now()),
            'checkoutDate' => $formatDate(now()),
            'checkoutTime' => $formatTime(now()),
            'roomNumbersByType' => [],
            'roomSummaryItems' => [],
            'roomItems' => [],
            'serviceItems' => [],
            'compensationItems' => [],
            'roomTotal' => 0,
            'serviceAmount' => 0,
            'compensationAmount' => 0,
            'totalAmount' => 0,
            'paidAmount' => 0,
            'amountDue' => 0,
        ];

        if ($booking) {
            $invoice = $booking->hoaDon;
            $nights = $getNights($booking);
            $customer = $booking->khachHang;
            $details = $invoice?->chiTietHoaDons ?? collect();

            $roomNumbersByType = $booking->chiTietDatPhong
                ->filter(fn ($detail) => $detail?->phong?->MaLoaiPhong)
                ->groupBy(fn ($detail) => (string) $detail->phong->MaLoaiPhong)
                ->map(fn ($items) => $items
                    ->map(fn ($detail) => $detail?->phong?->SoPhong)
                    ->filter()
                    ->values()
                    ->implode(', '));

            $roomSummaryItems = $booking->chiTietDatPhong
                ->groupBy(fn ($detail) => (string) ($detail->phong?->MaLoaiPhong ?? $detail->MaPhong))
                ->map(function ($items) {
                    $firstDetail = $items->first();
                    $roomType = $firstDetail?->phong?->loaiPhong;
                    $roomNumbers = $items
                        ->map(fn ($detail) => $detail?->phong?->SoPhong)
                        ->filter()
                        ->values()
                        ->implode(', ');

                    return [
                        'roomTypeId' => (string) ($roomType?->MaLoaiPhong ?? $firstDetail?->MaPhong),
                        'type' => $roomType?->TenLoaiPhong ?? 'Loại phòng',
                        'roomNumbers' => $roomNumbers ?: '--',
                    ];
                })
                ->values();

            $roomItems = $details
                ->filter(fn ($detail) => $detail->MaLoaiPhong)
                ->map(function ($detail) use ($nights, $moneyValue, $roomNumbersByType) {
                    $quantity = max(1, (int) ($detail->SoLuong ?? 1));
                    $lineTotal = $moneyValue($detail->DonGia) * $quantity;
                    $unitPrice = $nights > 0 ? ($lineTotal / $quantity / $nights) : $moneyValue($detail->DonGia);

                    return [
                        'roomTypeId' => (string) $detail->MaLoaiPhong,
                        'type' => $detail->loaiPhong?->TenLoaiPhong ?? $detail->MoTa ?? 'Loại phòng',
                        'roomNumbers' => $roomNumbersByType->get((string) $detail->MaLoaiPhong, '--') ?: '--',
                        'quantity' => $quantity,
                        'unitPrice' => $unitPrice,
                        'nights' => $nights,
                        'total' => $lineTotal,
                    ];
                })
                ->values();

            if ($roomItems->isEmpty()) {
                $roomItems = $booking->chiTietDatPhong
                    ->groupBy(fn ($detail) => $detail->phong?->MaLoaiPhong ?? $detail->MaPhong)
                    ->map(function ($items) use ($nights, $moneyValue) {
                        $roomType = $items->first()?->phong?->loaiPhong;
                        $unitPrice = $moneyValue($roomType?->GiaGiam ?? $roomType?->GiaPhong);
                        $roomNumbers = $items
                            ->map(fn ($detail) => $detail?->phong?->SoPhong)
                            ->filter()
                            ->values()
                            ->implode(', ');

                        return [
                            'roomTypeId' => (string) ($roomType?->MaLoaiPhong ?? $items->first()?->MaPhong),
                            'type' => $roomType?->TenLoaiPhong ?? 'Loại phòng',
                            'roomNumbers' => $roomNumbers ?: '--',
                            'quantity' => $items->count(),
                            'unitPrice' => $unitPrice,
                            'nights' => $nights,
                            'total' => $unitPrice * $items->count() * $nights,
                        ];
                    })
                    ->values();
            }

            $invoiceDetailsByUsage = $details
                ->filter(fn ($detail) => $detail->MaSuDung)
                ->keyBy(fn ($detail) => (string) $detail->MaSuDung);

            $serviceItems = $booking->chiTietDatPhong
                ->flatMap(function ($roomDetail) use ($invoiceDetailsByUsage, $moneyValue) {
                    return ($roomDetail->suDungDichVu ?? collect())->map(function ($usage) use ($roomDetail, $invoiceDetailsByUsage, $moneyValue) {
                        $service = $usage->dichVu;
                        $invoiceDetail = $invoiceDetailsByUsage->get((string) $usage->MaSuDung);
                        $roomNumber = $roomDetail->phong?->SoPhong;
                        $quantity = max(1, (int) ($usage->SoLuong ?? $invoiceDetail?->SoLuong ?? 1));
                        $unitPrice = $moneyValue($invoiceDetail?->DonGia ?? $service?->GiaDV);

                        return [
                            'name' => trim($invoiceDetail?->MoTa ?: ($service?->TenDV ?? 'Dịch vụ')),
                            'roomNumber' => $roomNumber ? (string) $roomNumber : '',
                            'type' => $service?->LoaiDVText ?? 'Dịch vụ',
                            'quantity' => $quantity,
                            'unitPrice' => $unitPrice,
                            'price' => $unitPrice * $quantity,
                            'time' => $usage->ThoiGian ? Carbon::parse($usage->ThoiGian)->format('d/m/Y H:i') : '--',
                        ];
                    });
                })
                ->values();

            $compensationItems = $details
                ->filter(fn ($detail) => $detail->MaDenBu)
                ->map(fn ($detail) => [
                    'description' => $detail->MoTa ?: ($detail->denBu?->MoTa ?? 'Đền bù'),
                    'amount' => $moneyValue($detail->DonGia) * max(1, (int) ($detail->SoLuong ?? 1)),
                ])
                ->values();

            $roomTotal = $roomItems->sum('total');
            $serviceAmount = $serviceItems->sum('price');
            $compensationAmount = $compensationItems->sum('amount');
            $totalAmount = $moneyValue($invoice?->TongTien) ?: ($roomTotal + $serviceAmount + $compensationAmount);
            $paidAmount = $moneyValue($invoice?->DaThanhToan ?? $invoice?->thanhToans?->sum('SoTien'));

            $paymentData = [
                'invoiceId' => $invoice?->MaHD ? 'HD' . $invoice->MaHD : 'HD' . $booking->MaDatPhong,
                'bookingId' => (string) $booking->MaDatPhong,
                'customer' => $customer?->TenKH ?? '--',
                'phone' => $customer?->SoDienThoai ?? '',
                'stay' => "{$nights} đêm - {$booking->chiTietDatPhong->count()} phòng",
                'stayPeriod' => $formatDate($booking->NgayNhanPhong) . ' - ' . $formatDate($booking->NgayTraPhong),
                'invoiceDate' => $formatDate($invoice?->NgayLapHD ?? now()),
                'checkoutDate' => $formatDate(now()),
                'checkoutTime' => $formatTime(now()),
                'roomNumbersByType' => $roomNumbersByType->toArray(),
                'roomSummaryItems' => $roomSummaryItems,
                'roomItems' => $roomItems,
                'serviceItems' => $serviceItems,
                'compensationItems' => $compensationItems,
                'roomTotal' => $roomTotal,
                'serviceAmount' => $serviceAmount,
                'compensationAmount' => $compensationAmount,
                'totalAmount' => $totalAmount,
                'paidAmount' => $paidAmount,
                'amountDue' => max($totalAmount - $paidAmount, 0),
            ];
        }

        return view('receptionist.payments.form', [
            'paymentData' => $paymentData,
        ]);
    })->name('payments.create');
    Route::get('/payments/{paymentId}', function ($paymentId) {
        $payment = ThanhToan::with('hoaDon.datPhong.khachHang')->findOrFail($paymentId);

        return view('receptionist.payments.show', [
            'payment' => $payment,
        ]);
    })->name('payments.show');
    Route::get('/invoices', function () {
        $invoices = HoaDon::select(['MaHD', 'MaDatPhong', 'NgayLapHD', 'TongTien', 'DaThanhToan', 'MaNV', 'TrangThai'])
            ->with([
            'datPhong:MaDatPhong,MaKH,NgayTraPhong',
            'datPhong.khachHang:MaKH,TenKH',
            'nhanVien:MaNV,TenNV',
            'thanhToans:MaTT,MaHD,NgayThanhToan',
        ])
            ->orderByDesc('MaHD')
            ->get();

        return view('receptionist.invoices.index', [
            'invoices' => $invoices,
        ]);
    })->name('invoices.index');
    Route::view('/invoices/{invoiceId}/edit', 'receptionist.invoices.form')->name('invoices.edit');
    Route::get('/invoices/{invoiceId}', function ($invoiceId) {
        $invoice = HoaDon::with([
            'datPhong.khachHang',
            'datPhong.chiTietDatPhong.phong.loaiPhong',
            'nhanVien',
            'khuyenMai',
            'chiTietHoaDons.loaiPhong',
            'chiTietHoaDons.suDung.dichVu',
            'chiTietHoaDons.suDung.chiTietDatPhong.phong',
            'chiTietHoaDons.denBu',
            'thanhToans',
        ])->findOrFail($invoiceId);

        return view('receptionist.invoices.show', [
            'invoice' => $invoice,
        ]);
    })->name('invoices.show');
});
//==================
Route::get('/payment/vnpay-return', function (Request $request) {
    $status = $request->get('status', 'failed');
    $txnRef = $request->get('txn_ref', '');
    $redirectUrl = url('/customer/my-bookings');
    $query = http_build_query([
        'vnpay' => $status,
        'txn_ref' => $txnRef,
    ]);

    return view('payment.vnpay-result', [
        'status' => $status,
        'txnRef' => $txnRef,
        'redirectUrl' => $redirectUrl . '?' . $query,
        'deepLink' => 'peachvalley://vnpay-result?status=' . $status . '&txn_ref=' . $txnRef,
    ]);
});
