<?php

use App\Http\Controllers\HotelManagementController;
use App\Http\Controllers\MockAuthController;
use App\Http\Controllers\ReceptionistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

Route::view('/customer', 'customer.index')->name('customer.home');
Route::view('/customer/promotion', 'customer.promotion')->name('customer.promotion');
Route::view('/customer/blog-single', 'customer.blog-single')->name('customer.blog-single');
Route::view('/customer/services', 'customer.services')->name('customer.services');
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
        ->route('customer.services')
        ->with('service_booking_saved', 'Yêu cầu của quý khách đã được ghi nhận.');
})->name('customer.service-booking.store');
Route::view('/customer/rooms', 'customer.rooms')->name('customer.rooms');
Route::view('/customer/rooms-single', 'customer.rooms-single')->name('customer.rooms-single');
Route::view('/customer/rooms-booking', 'customer.rooms-booking')->name('customer.rooms-booking');
Route::view('/customer/info-booking', 'customer.info-booking')->name('customer.info-booking');
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
        'street_address' => ['required', 'string', 'max:120', 'regex:/^[0-9\pL\s.\/-]+$/u'],
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
Route::get('/customer/promotion-wallet', function () {
    if (! isMockAuthenticated()) {
        return redirect()->route('login');
    }

    return view('customer.promotion-wallet');
})->name('customer.promotion-wallet');
Route::redirect('/customer/room-single.html', '/customer/rooms-single');
Route::redirect('/room-single.html', '/customer/rooms-single');
Route::redirect('/customer/about', '/customer/promotion');
Route::redirect('/customer/restaurant', '/customer/services');
Route::redirect('/customer/rooms-search', '/customer/rooms-booking');
Route::redirect('/customer/booking', '/customer/info-booking');
Route::redirect('/', '/dashboard');

Route::get('/login', [MockAuthController::class, 'create'])->name('login');
Route::post('/login', [MockAuthController::class, 'store']);
Route::redirect('/admin/login', '/login');
Route::redirect('/reception/login', '/login');
Route::get('/auth/google', [MockAuthController::class, 'google'])->name('auth.google');
Route::view('/forgot-password', 'auth.recoverpw')->name('auth.recoverpw');
Route::post('/forgot-password', function (Request $request) {
    $validated = $request->validate([
        'email' => ['required', 'email', 'max:255'],
    ], [
        'email.required' => 'Vui lòng nhập email.',
        'email.email' => 'Email không đúng định dạng.',
    ]);

    $request->session()->put('password_reset_email', mb_strtolower(trim((string) $validated['email'])));

    return redirect()->route('auth.new-password');
})->name('auth.recoverpw.submit');
Route::view('/new-password', 'auth.new-password')->name('auth.new-password');
Route::post('/new-password', function (Request $request) {
    $validated = $request->validate([
        'password' => ['required', 'string', 'min:8'],
        'password_confirmation' => ['required', 'same:password'],
    ], [
        'password.required' => 'Vui lòng nhập mật khẩu mới.',
        'password.min' => 'Mật khẩu mới cần ít nhất 8 ký tự.',
        'password_confirmation.required' => 'Vui lòng xác nhận mật khẩu mới.',
        'password_confirmation.same' => 'Xác nhận mật khẩu không khớp.',
    ]);

    $registeredCustomer = $request->session()->get('registered_customer_profile', []);
    $resetEmail = (string) $request->session()->get('password_reset_email', '');

    if (
        is_array($registeredCustomer) &&
        $resetEmail !== '' &&
        mb_strtolower((string) ($registeredCustomer['email'] ?? '')) === $resetEmail
    ) {
        $registeredCustomer['password_hash'] = Hash::make((string) $validated['password']);
        $request->session()->put('registered_customer_profile', $registeredCustomer);
    }

    $request->session()->forget('password_reset_email');

    return redirect()
        ->route('login')
        ->with('status', 'Mật khẩu mới đã được cập nhật. Vui lòng đăng nhập lại.');
})->name('auth.new-password.store');
Route::view('/sign-up', 'auth.register')->name('auth.signup');
Route::get('/sign-in', function () {
    return redirect()->route('login');
})->name('auth.signin');
Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'email' => ['required', 'email', 'max:255'],
        'password' => ['required', 'string', 'min:8'],
        'password_confirmation' => ['required', 'same:password'],
    ], [
        'email.required' => 'Vui lòng nhập email.',
        'email.email' => 'Email không đúng định dạng.',
        'password.required' => 'Vui lòng nhập mật khẩu.',
        'password.min' => 'Mật khẩu cần ít nhất 8 ký tự.',
        'password_confirmation.required' => 'Vui lòng xác nhận mật khẩu.',
        'password_confirmation.same' => 'Xác nhận mật khẩu không khớp.',
    ]);

    $request->session()->put('pending_customer_registration', [
        'email' => mb_strtolower(trim((string) $validated['email'])),
        'password_hash' => Hash::make((string) $validated['password']),
    ]);

    return redirect()->route('register.details');
})->name('register');
Route::get('/register/details', function (Request $request) {
    if (! $request->session()->has('pending_customer_registration')) {
        return redirect()->route('auth.signup');
    }

    return view('auth.register-details', [
        'pendingRegistration' => $request->session()->get('pending_customer_registration', []),
    ]);
})->name('register.details');
Route::post('/register/details', function (Request $request) {
    $addressOptions = [
        'TP.HCM' => ['Quận 1', 'Quận 3', 'Quận 7', 'Quận 10', 'Bình Thạnh', 'Gò Vấp', 'Thủ Đức'],
        'Hà Nội' => ['Ba Đình', 'Hoàn Kiếm', 'Đống Đa', 'Cầu Giấy', 'Thanh Xuân', 'Tây Hồ'],
        'Đà Nẵng' => ['Hải Châu', 'Thanh Khê', 'Sơn Trà', 'Ngũ Hành Sơn', 'Liên Chiểu', 'Cẩm Lệ'],
        'Lâm Đồng' => ['Đà Lạt', 'Bảo Lộc', 'Đức Trọng', 'Lạc Dương', 'Di Linh'],
        'Cần Thơ' => ['Ninh Kiều', 'Bình Thủy', 'Cái Răng', 'Ô Môn', 'Thốt Nốt'],
    ];

    if (! $request->session()->has('pending_customer_registration')) {
        return redirect()->route('auth.signup');
    }

    $province = (string) $request->input('province', '');
    $districtOptions = $addressOptions[$province] ?? [];

    $validated = $request->validate([
        'full_name' => ['required', 'string', 'min:2', 'max:60', 'regex:/^[\pL\s]+$/u'],
        'gender' => ['required', Rule::in(['0', '1', '2'])],
        'phone' => ['required', 'regex:/^0[0-9]{9}$/'],
        'cccd' => ['required', 'regex:/^[0-9]{12}$/'],
        'birthday' => ['required', 'date', 'before_or_equal:today'],
        'province' => ['required', Rule::in(array_keys($addressOptions))],
        'district' => ['required', Rule::in($districtOptions)],
        'address_line' => ['required', 'string', 'min:4', 'max:120', 'regex:/^[0-9\pL\s.\/,-]+$/u'],
    ], [
        'full_name.required' => 'Vui lòng nhập họ và tên.',
        'full_name.regex' => 'Họ và tên chỉ gồm chữ cái và khoảng trắng.',
        'gender.required' => 'Vui lòng chọn giới tính.',
        'phone.required' => 'Vui lòng nhập số điện thoại.',
        'phone.regex' => 'Số điện thoại gồm 10 chữ số và bắt đầu bằng 0.',
        'cccd.required' => 'Vui lòng nhập CCCD.',
        'cccd.regex' => 'CCCD gồm đúng 12 chữ số.',
        'birthday.required' => 'Vui lòng chọn ngày sinh.',
        'birthday.before_or_equal' => 'Ngày sinh không được sau hôm nay.',
        'province.required' => 'Vui lòng chọn tỉnh/thành phố.',
        'province.in' => 'Tỉnh/thành phố không hợp lệ.',
        'district.required' => 'Vui lòng chọn quận/huyện.',
        'district.in' => 'Quận/huyện không thuộc tỉnh/thành phố đã chọn.',
        'address_line.required' => 'Vui lòng nhập số nhà và tên đường.',
        'address_line.min' => 'Số nhà và tên đường cần ít nhất 4 ký tự.',
        'address_line.regex' => 'Số nhà và tên đường chỉ gồm chữ, số, khoảng trắng và ký tự . / , -',
    ]);

    $validated['address'] = implode(', ', array_filter([
        trim((string) $validated['address_line']),
        trim((string) $validated['district']),
        trim((string) $validated['province']),
    ]));

    $pending = $request->session()->get('pending_customer_registration', []);
    $request->session()->put('registered_customer_profile', array_merge($pending, $validated));
    $request->session()->forget('pending_customer_registration');

    return redirect()
        ->route('login')
        ->with('status', 'Đăng ký thành công. Vui lòng đăng nhập để tiếp tục.');
})->name('register.details.store');
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
