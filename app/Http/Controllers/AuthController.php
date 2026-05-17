<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\KhachHangController;
use App\Http\Controllers\Api\TaiKhoanController;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $taiKhoan = TaiKhoan::with(['khachHang', 'nhanVien'])
            ->where('Email', $credentials['email'])
            ->first();

        if (!$taiKhoan || !Hash::check($credentials['password'], $taiKhoan->MatKhau)) {
            return back()
                ->withErrors(['login' => 'Email hoặc mật khẩu không đúng.'])
                ->withInput($request->only('email'));
        }

        if ((int) $taiKhoan->TrangThai !== 1) {
            return back()
                ->withErrors(['login' => 'Tài khoản đã bị khóa hoặc ngừng hoạt động.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();
        $request->session()->put('auth_account', [
            'MaTK' => $taiKhoan->MaTK,
            'Email' => $taiKhoan->Email,
            'LoaiTaiKhoan' => $taiKhoan->LoaiTaiKhoan,
            'MaKH' => $taiKhoan->khachHang?->MaKH,
            'MaNV' => $taiKhoan->nhanVien?->MaNV,
            'ChucVu' => $taiKhoan->nhanVien?->ChucVu,
            'Ten' => $taiKhoan->khachHang?->TenKH ?? $taiKhoan->nhanVien?->TenNV,
        ]);

        $redirectTo = $request->input('redirect');

        if ($this->canRedirectToIntended($redirectTo, (int) $taiKhoan->LoaiTaiKhoan)) {
            return redirect()->to($redirectTo);
        }

        return redirect()->route(match ((int) $taiKhoan->LoaiTaiKhoan) {
            1 => 'reception.dashboard',
            2 => 'admin.dashboard',
            default => 'customer.home',
        });
    }

    public function logout(Request $request)
    {
        $request->session()->forget('auth_account');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Đăng xuất thành công.');
    }

    private function canRedirectToIntended(mixed $redirectTo, int $accountType): bool
    {
        if (!is_string($redirectTo) || trim($redirectTo) === '') {
            return false;
        }

        $appUrl = rtrim(url('/'), '/');
        $targetUrl = str_starts_with($redirectTo, '/')
            ? $appUrl . '/' . ltrim($redirectTo, '/')
            : $redirectTo;

        if ($targetUrl !== $appUrl && !str_starts_with($targetUrl, $appUrl . '/')) {
            return false;
        }

        $path = '/' . ltrim(parse_url($targetUrl, PHP_URL_PATH) ?: '', '/');

        return match ($accountType) {
            0 => str_starts_with($path, '/customer'),
            1 => str_starts_with($path, '/reception'),
            2 => str_starts_with($path, '/admin') || str_starts_with($path, '/hotel'),
            default => false,
        };
    }

    public function registerStepOne(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required', 'same:password'],
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password_confirmation.required' => 'Vui lòng xác nhận mật khẩu.',
            'password_confirmation.same' => 'Mật khẩu xác nhận không khớp.',
        ]);

        $request->session()->put('registration.step1', [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);
        return redirect()->route('register.details');
    }

    public function showRegisterDetails()
    {
        $addressData = $this->getCachedAddressData();

        return view('auth.register-details', [
            'provinces' => $addressData['provinces'],
            'communes' => $addressData['communes'],
            'today' => now()->toDateString(),
        ]);
    }

    public function getDistricts($provinceCode)
    {
        $addressData = $this->getCachedAddressData();

        return response()->json([
            'districts' => $addressData['communes'][$provinceCode] ?? [],
        ]);
    }

    private function getCachedAddressData(): array
    {
        $cacheKey = 'address-kit.2025-07-01.all';

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $provincesResponse = Http::timeout(8)
                ->retry(2, 200)
                ->get('https://production.cas.so/address-kit/2025-07-01/provinces');

            $provinces = collect($provincesResponse->json('provinces', []))
                ->map(fn($province) => [
                    'code' => $province['code'],
                    'name' => $province['name'],
                ])
                ->toArray();

            $communeResponses = Http::pool(fn($pool) => collect($provinces)
                ->map(fn($province) => $pool
                    ->as($province['code'])
                    ->timeout(8)
                    ->get("https://production.cas.so/address-kit/2025-07-01/provinces/{$province['code']}/communes"))
                ->all());

            $communes = [];

            foreach ($provinces as $province) {
                $response = $communeResponses[$province['code']] ?? null;

                $communes[$province['code']] = collect($response?->json('communes', []) ?? [])
                    ->map(fn($commune) => [
                        'code' => $commune['code'],
                        'name' => $commune['name'],
                    ])
                    ->toArray();
            }

            $addressData = [
                'provinces' => $provinces,
                'communes' => $communes,
            ];

            Cache::put($cacheKey, $addressData, now()->addDays(30));

            return $addressData;
        } catch (Throwable $e) {
            Log::warning('Address data fetch failed', [
                'message' => $e->getMessage(),
            ]);

            return [
                'provinces' => [],
                'communes' => [],
            ];
        }
    }

    public function registerStepTwo(Request $request)
    {
        $request->validate([
            'full_name' => ['required', 'string', 'min:2', 'max:60', 'regex:/^[\pL\s]+$/u'],
            'gender' => ['required', 'in:0,1,2'],
            'phone' => ['required', 'regex:/^0[0-9]{9}$/'],
            'cccd' => ['required', 'regex:/^[0-9]{12}$/'],
            'birthday' => ['required', 'date', 'before_or_equal:today'],
            'province' => ['nullable', 'string'],
            'district' => ['nullable', 'string'],
            'address_line' => ['nullable', 'string', 'min:4', 'max:120', 'regex:/^[0-9\pL\s.\/,\-]+$/u'],
            'address' => ['nullable', 'string', 'max:255'],
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên.',
            'full_name.min' => 'Họ và tên phải có ít nhất 2 ký tự.',
            'full_name.max' => 'Họ và tên không được vượt quá 60 ký tự.',
            'full_name.regex' => 'Họ và tên chỉ được gồm chữ cái và khoảng trắng.',
            'gender.required' => 'Vui lòng chọn giới tính.',
            'gender.in' => 'Giới tính không hợp lệ.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.',
            'cccd.required' => 'Vui lòng nhập CCCD.',
            'cccd.regex' => 'CCCD phải gồm đúng 12 chữ số.',
            'birthday.required' => 'Vui lòng chọn ngày sinh.',
            'birthday.date' => 'Ngày sinh không hợp lệ.',
            'birthday.before_or_equal' => 'Ngày sinh không được lớn hơn ngày hiện tại.',
            'address_line.min' => 'Số nhà và tên đường phải có ít nhất 4 ký tự.',
            'address_line.max' => 'Số nhà và tên đường không được vượt quá 120 ký tự.',
            'address_line.regex' => 'Số nhà và tên đường chỉ được gồm chữ, số và ký tự . / , -',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
        ]);

        // Merge step 1 + step 2 into session
        $address = $request->filled('address') ? trim($request->input('address')) : null;

        $registration = $request->session()->get('registration.step1', []);
        $registration = array_merge($registration, [
            'full_name' => $request->input('full_name'),
            'gender' => $request->input('gender'),
            'phone' => $request->input('phone'),
            'cccd' => $request->input('cccd'),
            'birthday' => $request->input('birthday'),
            'address' => $address,
        ]);
        $request->session()->put('registration', $registration);

        try {
            DB::beginTransaction();

            $storeKhachHangRequest = new Request([
                'TenKH' => $registration['full_name'],
                'SoDienThoai' => $registration['phone'],
                'CCCD' => $registration['cccd'],
                'NgaySinh' => $registration['birthday'],
                'GioiTinh' => $registration['gender'],
                'DiaChi' => $registration['address'],
            ]);

            $storeKhachHangResponse = app(KhachHangController::class)->store($storeKhachHangRequest);

            if ($storeKhachHangResponse->getStatusCode() !== 201) {
                DB::rollBack();
                $request->session()->forget('registration');

                return redirect()
                    ->route('auth.signup')
                    ->withErrors(['register' => 'Không thể tạo thông tin khách hàng. Số điện thoại hoặc CCCD có thể đã được sử dụng.'])
                    ->withInput(['email' => $registration['email'] ?? null]);
            }

            $khachHangPayload = $storeKhachHangResponse->getData(true);
            $maKhachHang = $khachHangPayload['data']['MaKH'] ?? null;

            $storeTaiKhoanRequest = new Request([
                'Email' => $registration['email'] ?? null,
                'MatKhau' => $registration['password'] ?? null,
                'LoaiTaiKhoan' => 0,
                'TrangThai' => 1,
                'MaKH' => $maKhachHang,
            ]);

            $storeTaiKhoanResponse = app(TaiKhoanController::class)->store($storeTaiKhoanRequest);

            if ($storeTaiKhoanResponse->getStatusCode() !== 201) {
                DB::rollBack();
                $request->session()->forget('registration');

                return redirect()
                    ->route('auth.signup')
                    ->withErrors(['register' => 'Không thể tạo tài khoản. Email có thể đã được sử dụng.'])
                    ->withInput(['email' => $registration['email'] ?? null]);
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Register account creation failed', [
                'email' => $registration['email'] ?? null,
                'message' => $e->getMessage(),
            ]);

            $request->session()->forget('registration');

            return redirect()
                ->route('auth.signup')
                ->withErrors(['register' => 'Đăng ký thất bại. Vui lòng thử lại.'])
                ->withInput(['email' => $registration['email'] ?? null]);
        }

        $request->session()->forget('registration');

        return redirect()
            ->route('login')
            ->with('success', 'Đăng ký thành công. Vui lòng đăng nhập.');
    }

}
