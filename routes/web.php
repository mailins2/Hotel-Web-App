<?php

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
    Route::view('/rooms-single', 'customer.rooms-single')->name('rooms-single');
    Route::view('/rooms-booking', 'customer.rooms-booking')->name('rooms-booking');
    Route::view('/info-booking', 'customer.info-booking')->name('info-booking');
    Route::view('/profile', 'customer.profile')->name('profile');
    Route::view('/my-bookings', 'customer.my-bookings')->name('my-bookings');
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
Route::redirect('/admin/login', '/login');
Route::redirect('/reception/login', '/login');
Route::view('/forgot-password', 'auth.recoverpw')->name('auth.recoverpw');
Route::view('/new-password', 'auth.new-password')->name('auth.new-password');
Route::view('/sign-up', 'auth.register')->name('auth.signup');
Route::redirect('/sign-in', '/login')->name('auth.signin');
Route::view('/register/details', 'auth.register-details')->name('register.details');

/*
|--------------------------------------------------------------------------
| Landing
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/dashboard');
Route::redirect('/dashboard', '/customer')->name('dashboard');

/*
|--------------------------------------------------------------------------
| Admin UI
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'hotel-management.report')->name('dashboard');
});

Route::view('/terms-of-use', 'hotel-management.term-of-use')->name('pages.term-of-use');

/*
|--------------------------------------------------------------------------
| Hotel Management UI
|--------------------------------------------------------------------------
*/

Route::prefix('hotel')->name('hotel.')->group(function () {
    Route::view('/reports', 'hotel-management.report')->name('reports.index');
    Route::view('/{moduleKey}', 'hotel-management.index')->name('modules.index');
    Route::view('/{moduleKey}/create', 'hotel-management.form')->name('modules.create');
    Route::view('/{moduleKey}/{recordId}/edit', 'hotel-management.form')->name('modules.edit');
    Route::view('/{moduleKey}/{recordId}', 'hotel-management.show')->name('modules.show');
});

/*
|--------------------------------------------------------------------------
| Reception UI
|--------------------------------------------------------------------------
*/

Route::prefix('reception')->name('reception.')->group(function () {
    Route::view('/dashboard', 'receptionist.dashboard')->name('dashboard');

    Route::view('/customers', 'receptionist.list')->name('customers.index');
    Route::view('/customers/create', 'hotel-management.form')->name('customers.create');
    Route::view('/customers/{customerId}/edit', 'hotel-management.form')->name('customers.edit');
    Route::view('/customers/{customerId}', 'hotel-management.show')->name('customers.show');

    Route::view('/bookings', 'receptionist.list')->name('bookings.index');
    Route::view('/bookings/create', 'receptionist.booking-form')->name('bookings.create');
    Route::view('/bookings/{bookingId}/edit', 'hotel-management.form')->name('bookings.edit');
    Route::view('/bookings/{bookingId}', 'hotel-management.show')->name('bookings.show');

    Route::view('/check-ins/create', 'receptionist.check-in-form')->name('check-ins.create');
    Route::view('/invoices', 'receptionist.list')->name('invoices.index');
});
