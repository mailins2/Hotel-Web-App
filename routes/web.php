<?php

use App\Http\Controllers\HotelManagementController;
use App\Http\Controllers\ReceptionistController;
use Illuminate\Support\Facades\Route;

Route::view('/customer', 'customer.index')->name('customer.home');
Route::view('/customer/promotion', 'customer.promotion')->name('customer.promotion');
Route::view('/customer/blog-single', 'customer.blog-single')->name('customer.blog-single');
Route::view('/customer/services', 'customer.services')->name('customer.services');
Route::post('/customer/service-booking', function () {
    return redirect()->route('customer.services');
})->name('customer.service-booking.store');
Route::view('/customer/rooms', 'customer.rooms')->name('customer.rooms');
Route::view('/customer/rooms-single', 'customer.rooms-single')->name('customer.rooms-single');
Route::view('/customer/rooms-booking', 'customer.rooms-booking')->name('customer.rooms-booking');
Route::view('/customer/info-booking', 'customer.info-booking')->name('customer.info-booking');
Route::view('/customer/profile', 'customer.profile')->name('customer.profile');
Route::post('/customer/profile', function () {
    return redirect()->route('customer.profile');
})->name('customer.profile.update');
Route::view('/customer/my-bookings', 'customer.my-bookings')->name('customer.my-bookings');
Route::view('/customer/promotion-wallet', 'customer.promotion-wallet')->name('customer.promotion-wallet');
Route::redirect('/customer/room-single.html', '/customer/rooms-single');
Route::redirect('/room-single.html', '/customer/rooms-single');
Route::redirect('/customer/about', '/customer/promotion');
Route::redirect('/customer/restaurant', '/customer/services');
Route::redirect('/customer/rooms-search', '/customer/rooms-booking');
Route::redirect('/customer/booking', '/customer/info-booking');
Route::redirect('/', '/dashboard');

Route::view('/login', 'auth.login')->name('login');
Route::post('/login', function () {
    return redirect()
        ->route('customer.home')
        ->with('status', 'Dang nhap dang o che do giao dien. Backend se xu ly logic that khi tich hop.');
});
Route::redirect('/admin/login', '/login');
Route::redirect('/reception/login', '/login');
Route::get('/auth/google', function () {
    return redirect()
        ->route('customer.home')
        ->with('status', 'Dang nhap Google dang o che do giao dien. Backend se noi OAuth sau.');
})->name('auth.google');
Route::view('/forgot-password', 'auth.recoverpw')->name('auth.recoverpw');
Route::post('/forgot-password', function () {
    return redirect()
        ->route('auth.new-password')
        ->with('status', 'Lien ket dat lai mat khau se do backend xu ly khi tich hop.');
})->name('auth.recoverpw.submit');
Route::view('/new-password', 'auth.new-password')->name('auth.new-password');
Route::post('/new-password', function () {
    return redirect()
        ->route('login')
        ->with('status', 'Mat khau moi se duoc backend cap nhat khi tich hop.');
})->name('auth.new-password.store');
Route::view('/sign-up', 'auth.register')->name('auth.signup');
Route::redirect('/sign-in', '/login')->name('auth.signin');
Route::post('/register', function () {
    return redirect()->route('register.details');
})->name('register');
Route::view('/register/details', 'auth.register-details')->name('register.details');
Route::post('/register/details', function () {
    return redirect()
        ->route('login')
        ->with('status', 'Thong tin dang ky da duoc ghi nhan o giao dien mau.');
})->name('register.details.store');
Route::redirect('/dashboard', '/customer')->name('dashboard');
Route::get('/admin/dashboard', [HotelManagementController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/terms-of-use', [HotelManagementController::class, 'termOfUse'])->name('pages.term-of-use');
Route::post('/logout', function () {
    return redirect()
        ->route('login')
        ->with('status', 'Dang xuat mau da hoan tat.');
})->name('logout');

Route::prefix('reception')->name('reception.')->group(function () {
    Route::get('/dashboard', [ReceptionistController::class, 'dashboard'])->name('dashboard');
    Route::get('/customers', [ReceptionistController::class, 'customers'])->name('customers.index');
    Route::get('/customers/create', [ReceptionistController::class, 'customerCreate'])->name('customers.create');
    Route::post('/customers', [ReceptionistController::class, 'customerStore'])->name('customers.store');
    Route::get('/customers/{customerId}', [ReceptionistController::class, 'customerShow'])->name('customers.show');
    Route::get('/customers/{customerId}/edit', [ReceptionistController::class, 'customerEdit'])->name('customers.edit');
    Route::match(['put', 'patch'], '/customers/{customerId}', [ReceptionistController::class, 'customerUpdate'])->name('customers.update');
    Route::get('/bookings', [ReceptionistController::class, 'bookings'])->name('bookings.index');
    Route::get('/bookings/create', [ReceptionistController::class, 'bookingCreate'])->name('bookings.create');
    Route::post('/bookings', [ReceptionistController::class, 'bookingStore'])->name('bookings.store');
    Route::get('/bookings/{bookingId}', [ReceptionistController::class, 'bookingShow'])->name('bookings.show');
    Route::get('/bookings/{bookingId}/edit', [ReceptionistController::class, 'bookingEdit'])->name('bookings.edit');
    Route::match(['put', 'patch'], '/bookings/{bookingId}', [ReceptionistController::class, 'bookingUpdate'])->name('bookings.update');
    Route::get('/check-ins/create', [ReceptionistController::class, 'checkInCreate'])->name('check-ins.create');
    Route::post('/check-ins', [ReceptionistController::class, 'checkInStore'])->name('check-ins.store');
    Route::get('/invoices', [ReceptionistController::class, 'invoices'])->name('invoices.index');
});

Route::prefix('hotel')->name('hotel.')->group(function () {
    Route::get('/reports', [HotelManagementController::class, 'report'])->name('reports.index');
    Route::get('/{moduleKey}', [HotelManagementController::class, 'index'])->name('modules.index');
    Route::get('/{moduleKey}/create', [HotelManagementController::class, 'create'])->name('modules.create');
    Route::post('/{moduleKey}', [HotelManagementController::class, 'store'])->name('modules.store');
    Route::get('/{moduleKey}/{recordId}/edit', [HotelManagementController::class, 'edit'])->name('modules.edit');
    Route::get('/{moduleKey}/{recordId}', [HotelManagementController::class, 'show'])->name('modules.show');
    Route::match(['put', 'patch'], '/{moduleKey}/{recordId}', [HotelManagementController::class, 'update'])->name('modules.update');
    Route::delete('/{moduleKey}/{recordId}', [HotelManagementController::class, 'destroy'])->name('modules.destroy');
});
