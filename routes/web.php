<?php

use App\Http\Controllers\HotelManagementController;
use App\Http\Controllers\MockAuthController;
use App\Http\Controllers\ReceptionistController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/login', [MockAuthController::class, 'create'])->name('login');
Route::post('/login', [MockAuthController::class, 'store']);
Route::view('/forgot-password', 'auth.recoverpw')->name('auth.recoverpw');
Route::view('/sign-up', 'auth.register')->name('auth.signup');
Route::get('/sign-in', function () {
    return redirect()->route('login');
})->name('auth.signin');
Route::post('/register', function () {
    return redirect()->route('login')->with('status', 'Đăng ký thành công.');
})->name('register');
Route::get('/dashboard', [MockAuthController::class, 'dashboardRedirect'])->name('dashboard');
Route::get('/admin/dashboard', [HotelManagementController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/terms-of-use', [HotelManagementController::class, 'termOfUse'])->name('pages.term-of-use');
Route::post('/logout', function () {
    $session = request()->session();

    $session->forget('mock_auth');
    $session->invalidate();
    $session->regenerateToken();

    return redirect()->route('login');
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
