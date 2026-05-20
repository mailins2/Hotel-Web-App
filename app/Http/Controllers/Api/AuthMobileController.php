<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthMobileController extends Controller
{
    // ========== ĐĂNG NHẬP ==========
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $taiKhoan = TaiKhoan::with(['khachHang', 'nhanVien'])
            ->where('Email', $request->email)
            ->first();

        if (!$taiKhoan || !Hash::check($request->password, $taiKhoan->MatKhau)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email hoặc mật khẩu không đúng.'
            ], 401);
        }

        if ((int) $taiKhoan->TrangThai !== 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tài khoản đã bị khóa hoặc ngừng hoạt động.'
            ], 403);
        }

        $token = $taiKhoan->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Đăng nhập thành công.',
            'token' => $token,
            'user' => [
                'MaTK' => $taiKhoan->MaTK,
                'Email' => $taiKhoan->Email,
                'LoaiTaiKhoan' => $taiKhoan->LoaiTaiKhoan,
                'MaKH' => $taiKhoan->khachHang?->MaKH,
                'MaNV' => $taiKhoan->nhanVien?->MaNV,
                'Ten' => $taiKhoan->khachHang?->TenKH ?? $taiKhoan->nhanVien?->TenNV,
            ]
        ]);
    }

    // ========== ĐĂNG KÝ BƯỚC 1 ==========
    public function registerStepOne(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $exists = TaiKhoan::where('Email', $request->email)->exists();
        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email này đã được sử dụng.'
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Xác thực bước 1 thành công.',
        ]);
    }

    // ========== ĐĂNG KÝ BƯỚC 2 ==========
    public function registerStepTwo(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => ['required', 'email'],
        'password' => ['required', 'string', 'min:8'],
        'full_name' => ['required', 'string', 'min:2', 'max:60'],
        'gender' => ['required', 'in:0,1,2'],
        'phone' => ['required', 'regex:/^0[0-9]{9}$/'],
        'cccd' => ['required', 'regex:/^[0-9]{12}$/'],
        'birthday' => ['required', 'date', 'before_or_equal:today'],
        'address' => ['nullable', 'string', 'max:255'],
    ], [
        'email.required' => 'Vui lòng nhập email.',
        'email.email' => 'Email không đúng định dạng.',
        'password.required' => 'Vui lòng nhập mật khẩu.',
        'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
        'full_name.required' => 'Vui lòng nhập họ và tên.',
        'full_name.min' => 'Họ và tên phải có ít nhất 2 ký tự.',
        'full_name.max' => 'Họ và tên không được vượt quá 60 ký tự.',
        'gender.required' => 'Vui lòng chọn giới tính.',
        'gender.in' => 'Giới tính không hợp lệ.',
        'phone.required' => 'Vui lòng nhập số điện thoại.',
        'phone.regex' => 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.',
        'cccd.required' => 'Vui lòng nhập CCCD.',
        'cccd.regex' => 'CCCD phải gồm đúng 12 chữ số.',
        'birthday.required' => 'Vui lòng chọn ngày sinh.',
        'birthday.date' => 'Ngày sinh không hợp lệ.',
        'birthday.before_or_equal' => 'Ngày sinh không được lớn hơn ngày hiện tại.',
        'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => $validator->errors()->first()
        ], 422);
    }

    // Kiểm tra email
    if (TaiKhoan::where('Email', $request->email)->exists()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Email này đã được sử dụng.'
        ], 422);
    }

    //  Kiểm tra SĐT và CCCD
    $khachHangExists = \App\Models\KhachHang::where('SoDienThoai', $request->phone)
        ->orWhere('CCCD', $request->cccd)
        ->first();
    
    if ($khachHangExists) {
        $message = $khachHangExists->SoDienThoai === $request->phone 
            ? 'Số điện thoại này đã được sử dụng.' 
            : 'CCCD này đã được sử dụng.';
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], 422);
    }

    try {
        DB::beginTransaction();

        //  BƯỚC 1: Tạo KhachHang TRƯỚC
        $khachHang = \App\Models\KhachHang::create([
            'TenKH' => $request->full_name,
            'SoDienThoai' => $request->phone,
            'CCCD' => $request->cccd,
            'NgaySinh' => $request->birthday,
            'GioiTinh' => $request->gender,
            'DiaChi' => $request->address ?? '',
        ]);

        //  BƯỚC 2: Tạo TaiKhoan SAU (với MaKH đã có)
        $taiKhoan = TaiKhoan::create([
            'Email' => $request->email,
            'MatKhau' => Hash::make($request->password),
            'LoaiTaiKhoan' => 0,
            'TrangThai' => 1,
            'MaKH' => $khachHang->MaKH, // 👈 GÁN MaKH NGAY
        ]);

        DB::commit();

        // Tạo token
        $token = $taiKhoan->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Đăng ký thành công.',
            'token' => $token,
            'user' => [
                'MaTK' => $taiKhoan->MaTK,
                'Email' => $taiKhoan->Email,
                'LoaiTaiKhoan' => $taiKhoan->LoaiTaiKhoan,
                'MaKH' => $khachHang->MaKH,
                'Ten' => $khachHang->TenKH,
            ]
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('Đăng ký thất bại: ' . $e->getMessage());
        
        return response()->json([
            'status' => 'error',
            'message' => 'Đăng ký thất bại. Vui lòng thử lại.'
        ], 500);
    }
}

    // ========== ĐĂNG XUẤT ==========
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Đăng xuất thành công.'
        ]);
    }

    // ========== LẤY TỈNH/THÀNH PHỐ ==========
    public function getProvinces()
    {
        $addressData = $this->getCachedAddressData();

        return response()->json([
            'status' => 'success',
            'data' => $addressData['provinces'],
        ]);
    }

    // ========== LẤY QUẬN/HUYỆN ==========
    public function getDistricts($provinceCode)
    {
        $addressData = $this->getCachedAddressData();

        return response()->json([
            'status' => 'success',
            'data' => $addressData['communes'][$provinceCode] ?? [],
        ]);
    }

    // ========== HELPER: Cache địa chỉ ==========
    private function getCachedAddressData(): array
    {
        $cacheKey = 'address-kit.2025-07-01.all';

        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            return \Illuminate\Support\Facades\Cache::get($cacheKey);
        }

        try {
            $provincesResponse = \Illuminate\Support\Facades\Http::timeout(8)
                ->retry(2, 200)
                ->get('https://production.cas.so/address-kit/2025-07-01/provinces');

            $provinces = collect($provincesResponse->json('provinces', []))
                ->map(fn($p) => ['code' => $p['code'], 'name' => $p['name']])
                ->toArray();

            $communeResponses = \Illuminate\Support\Facades\Http::pool(fn($pool) => collect($provinces)
                ->map(fn($p) => $pool->as($p['code'])->timeout(8)
                    ->get("https://production.cas.so/address-kit/2025-07-01/provinces/{$p['code']}/communes"))
                ->all());

            $communes = [];
            foreach ($provinces as $province) {
                $response = $communeResponses[$province['code']] ?? null;
                $communes[$province['code']] = collect($response?->json('communes', []) ?? [])
                    ->map(fn($c) => ['code' => $c['code'], 'name' => $c['name']])
                    ->toArray();
            }

            $addressData = ['provinces' => $provinces, 'communes' => $communes];
            \Illuminate\Support\Facades\Cache::put($cacheKey, $addressData, now()->addDays(30));

            return $addressData;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Address data fetch failed', ['message' => $e->getMessage()]);
            return ['provinces' => [], 'communes' => []];
        }
    }
    // ========== LẤY THÔNG TIN USER HIỆN TẠI ==========
// ========== LẤY THÔNG TIN USER HIỆN TẠI ==========
public function getUserProfile(Request $request)
{
    $user = $request->user();
    
    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Không tìm thấy người dùng'
        ], 401);
    }
    
    $user->load(['khachHang', 'nhanVien']);
    
    return response()->json([
        'status' => 'success',
        'data' => [
            'MaTK' => $user->MaTK,
            'Email' => $user->Email,
            'LoaiTaiKhoan' => $user->LoaiTaiKhoan,
            'MaKH' => $user->khachHang?->MaKH,
            'MaNV' => $user->nhanVien?->MaNV,
            'Ten' => $user->khachHang?->TenKH ?? $user->nhanVien?->TenNV,
            'DiemTichLuy' => $user->khachHang?->DIEM ?? 0,  // 🔥 SỬA: DIEM (viết hoa)
            'SoDienThoai' => $user->khachHang?->SoDienThoai ?? $user->nhanVien?->SoDienThoai,
            'DiaChi' => $user->khachHang?->DiaChi ?? '',      // 🔥 THÊM
            'CCCD' => $user->khachHang?->CCCD ?? '',           // 🔥 THÊM
            'NgaySinh' => $user->khachHang?->NgaySinh ?? '',   // 🔥 THÊM
            'GioiTinh' => $user->khachHang?->GioiTinh ?? 2,    // 🔥 THÊM
        ]
    ]);
}
}