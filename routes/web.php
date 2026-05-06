<?php

use App\Http\Controllers\AuthController;
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
    Route::view('/services', 'customer.services')->name('services');
    Route::view('/rooms', 'customer.rooms')->name('rooms');
    Route::view('/room/{id}', 'customer.rooms-single')->name('room.detail');
    Route::view('/rooms-single', 'customer.rooms-single')->name('rooms-single');
    Route::view('/rooms-booking', 'customer.rooms-booking')->name('rooms-booking');
    Route::get('/info-booking', function () {
        if (!session()->has('auth_account')) {
            return redirect()->route('login', [
                'redirect' => route('customer.info-booking'),
            ]);
        }

        $authAccount = session('auth_account', []);
        $bookingCustomer = null;

        if (!empty($authAccount['MaKH'])) {
            $bookingCustomer = \App\Models\KhachHang::with('taiKhoan')->find($authAccount['MaKH']);
        } elseif (!empty($authAccount['MaTK'])) {
            $bookingCustomer = \App\Models\KhachHang::with('taiKhoan')
                ->where('MaTK', $authAccount['MaTK'])
                ->first();
        }

        return view('customer.info-booking', [
            'bookingAccount' => $authAccount,
            'bookingCustomer' => $bookingCustomer,
        ]);
    })->name('info-booking');
    Route::view('/profile', 'customer.profile')->name('profile');
    Route::get('/my-bookings', function () {
        if (!session()->has('auth_account')) {
            return redirect()->route('login', [
                'redirect' => route('customer.my-bookings'),
            ]);
        }

        $authAccount = session('auth_account', []);
        $customerId = $authAccount['MaKH'] ?? null;

        if (!$customerId && !empty($authAccount['MaTK'])) {
            $customerId = \App\Models\KhachHang::where('MaTK', $authAccount['MaTK'])->value('MaKH');
        }

        $customerBookings = collect();

        if ($customerId) {
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
    })->name('my-bookings');
    Route::view('/promotion-wallet', 'customer.promotion-wallet')->name('promotion-wallet');

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

Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'hotel-management.report')->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Hotel Management UI
|--------------------------------------------------------------------------
*/

Route::prefix('hotel')->name('hotel.')->group(function () {
    Route::view('/reports', 'hotel-management.report')->name('reports.index');

    $hotelManagementViews = [
        'accounts' => 'hotel-management.accounts',
        'customers' => 'hotel-management.customers',
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
            Route::view('/', $viewBase . '.index')->name('index');
            Route::view('/create', $viewBase . '.form')->name('create');
            Route::view('/{recordId}/edit', $viewBase . '.form')->name('edit');
            if ($module === 'services') {
                Route::view('/food-and-beverage', $viewBase . '.food-and-beverage')->name('food-and-beverage');
                Route::view('/room-service', $viewBase . '.room-service')->name('room-service');
                Route::view('/entertainment', $viewBase . '.entertainment')->name('entertainment');
            }
            Route::view('/{recordId}', $viewBase . '.show')->name('show');
        });
    }
});

/*
|--------------------------------------------------------------------------
| Reception UI
|--------------------------------------------------------------------------
*/

Route::prefix('reception')->name('reception.')->group(function () {
    Route::view('/dashboard', 'receptionist.dashboard')->name('dashboard');
    Route::view('/booking-details/{bookingId}', 'receptionist.booking-detail')->name('booking-detail');

    Route::view('/customers', 'receptionist.customers.index')->name('customers.index');
    Route::view('/customers/create', 'receptionist.customers.form')->name('customers.create');
    Route::view('/customers/{customerId}/edit', 'receptionist.customers.form')->name('customers.edit');
    Route::view('/customers/{customerId}', 'receptionist.customers.show')->name('customers.show');

    Route::view('/bookings', 'receptionist.bookings.index')->name('bookings.index');
    Route::view('/bookings/create', 'receptionist.bookings.form')->name('bookings.create');
    Route::view('/bookings/{bookingId}/edit', 'receptionist.bookings.form')->name('bookings.edit');
    Route::view('/bookings/{bookingId}', 'receptionist.bookings.show')->name('bookings.show');

    Route::view('/services', 'receptionist.services.index')->name('services.index');
    Route::view('/services/{serviceUsageId}', 'receptionist.services.show')->name('services.show');
    Route::view('/check-ins/create', 'receptionist.check-in-form')->name('check-ins.create');
    Route::view('/check-outs/create', 'receptionist.check-out-form')->name('check-outs.create');
    Route::view('/payments', 'receptionist.payments.index')->name('payments.index');
    Route::view('/payments/create', 'receptionist.payments.form')->name('payments.create');
    Route::view('/payments/{paymentId}', 'receptionist.payments.show')->name('payments.show');
    Route::view('/invoices', 'receptionist.invoices.index')->name('invoices.index');
    Route::view('/invoices/{invoiceId}/edit', 'receptionist.invoices.form')->name('invoices.edit');
    Route::view('/invoices/{invoiceId}', 'receptionist.invoices.show')->name('invoices.show');
});
