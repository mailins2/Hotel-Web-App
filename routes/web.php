<?php

use App\Http\Controllers\HotelManagementController;
use App\Http\Controllers\MockAuthController;
use App\Http\Controllers\ReceptionistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/customer', 'customer.index')->name('customer.home');
Route::view('/customer/about', 'customer.about')->name('customer.about');
Route::view('/customer/blog', 'customer.blog')->name('customer.blog');
Route::view('/customer/blog-single', 'customer.blog-single')->name('customer.blog-single');
Route::view('/customer/contact', 'customer.contact')->name('customer.contact');
Route::view('/customer/restaurant', 'customer.restaurant')->name('customer.restaurant');
Route::post('/customer/service-booking', function (Request $request) {
    if (! isMockAuthenticated()) {
        return redirect()->route('login');
    }

    $serviceTypes = collect(config('hotel-management.modules.services.records', []))
        ->pluck('LoaiDV')
        ->unique()
        ->map(fn ($type) => (string) $type)
        ->implode(',');
    $serviceIds = collect(config('hotel-management.modules.services.records', []))
        ->pluck('MaDV')
        ->map(fn ($id) => (string) $id)
        ->implode(',');
    $user = mockUser() ?? [];
    $account = collect(config('hotel-management.modules.accounts.records', []))->firstWhere('Email', $user['email'] ?? '');
    $customer = $account
        ? collect(config('hotel-management.modules.customers.records', []))->firstWhere('MaTK', $account['MaTK'] ?? null)
        : null;
    $bookingIds = collect(config('hotel-management.reception.bookings.records', []))
        ->where('MaKH', $customer['MaKH'] ?? null)
        ->pluck('MaDatPhong')
        ->map(fn ($id) => (string) $id)
        ->implode(',');

    $validated = $request->validate([
        'MaDatPhong' => ['required', 'integer', 'in:' . $bookingIds],
        'LoaiDV' => ['required', 'integer', 'in:' . $serviceTypes],
        'MaDV' => ['required', 'integer', 'in:' . $serviceIds],
        'SoLuong' => ['required', 'integer', 'min:1', 'max:20'],
        'ThoiGian' => ['required', 'date', 'after_or_equal:today'],
        'GhiChu' => ['nullable', 'string', 'max:255'],
    ]);

    $service = collect(config('hotel-management.modules.services.records', []))
        ->firstWhere('MaDV', (int) $validated['MaDV']);

    if (! $service || (int) $service['LoaiDV'] !== (int) $validated['LoaiDV']) {
        return back()
            ->withErrors(['MaDV' => 'Dịch vụ không thuộc đúng loại đã chọn.'])
            ->withInput();
    }

    $requests = $request->session()->get('service_bookings', []);
    $requests[] = [
        'MaSuDung' => count($requests) + 1,
        'MaDatPhong' => (int) $validated['MaDatPhong'],
        'MaDV' => (int) $validated['MaDV'],
        'SoLuong' => (int) $validated['SoLuong'],
        'ThoiGian' => $validated['ThoiGian'],
        'GhiChu' => $validated['GhiChu'] ?? null,
    ];

    $request->session()->put('service_bookings', $requests);

    return redirect()
        ->route('customer.restaurant')
        ->with('service_booking_saved', 'Yêu cầu của quý khách đã được ghi nhận.');
})->name('customer.service-booking.store');
Route::view('/customer/rooms', 'customer.rooms')->name('customer.rooms');
Route::view('/customer/rooms-single', 'customer.rooms-single')->name('customer.rooms-single');
Route::view('/customer/rooms-search', 'customer.rooms-search')->name('customer.rooms-search');
Route::view('/customer/booking', 'customer.booking')->name('customer.booking');
Route::view('/customer/payment', 'customer.payment')->name('customer.payment');
Route::get('/customer/profile', function () {
    if (! isMockAuthenticated()) {
        return redirect()->route('login');
    }

    return view('customer.profile');
})->name('customer.profile');
Route::post('/customer/profile', function (Request $request) {
    if (! isMockAuthenticated()) {
        return redirect()->route('login');
    }

    $validated = $request->validate([
        'display_name' => ['required', 'string', 'min:2', 'max:60', 'regex:/^[\pL\s]+$/u'],
        'phone' => ['required', 'regex:/^0[0-9]{9}$/'],
        'cccd' => ['required', 'regex:/^[0-9]{12}$/'],
        'birthday' => ['required', 'date', 'before_or_equal:today'],
        'gender' => ['required', 'in:0,1,2'],
        'province' => ['required', 'in:TP. Hồ Chí Minh,Hà Nội,Lâm Đồng,Đà Nẵng'],
        'district' => ['required', 'string', 'max:80'],
        'street' => ['required', 'string', 'max:80', 'regex:/^[0-9\pL\s.\/-]+$/u'],
        'house_number' => ['required', 'string', 'max:20', 'regex:/^[0-9\pL\s.\/-]+$/u'],
        'address' => ['required', 'string', 'max:255'],
    ]);

    $request->session()->put('customer_profile', $validated);

    return redirect()
        ->route('customer.profile')
        ->with('profile_saved', 'Thông tin đã được cập nhật.');
})->name('customer.profile.update');
Route::get('/customer/my-bookings', function () {
    if (! isMockAuthenticated()) {
        return redirect()->route('login');
    }

    return view('customer.my-bookings');
})->name('customer.my-bookings');
Route::redirect('/customer/room-single.html', '/customer/rooms-single');
Route::redirect('/room-single.html', '/customer/rooms-single');
Route::redirect('/', '/dashboard');

Route::get('/login', [MockAuthController::class, 'create'])->name('login');
Route::post('/login', [MockAuthController::class, 'store']);
Route::redirect('/admin/login', '/login');
Route::redirect('/reception/login', '/login');
Route::get('/auth/google', [MockAuthController::class, 'google'])->name('auth.google');
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
