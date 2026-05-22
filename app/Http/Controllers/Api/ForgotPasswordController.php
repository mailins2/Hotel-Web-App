<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    const OTP_DEFAULT = '123456'; // 👈 OTP MẶC ĐỊNH

    // Bước 1: Gửi OTP (luôn trả về 123456)
    public function sendOTP(Request $request)
    {
        $request->validate(['phone' => 'required|regex:/^0[0-9]{9}$/']);

        $khachHang = \App\Models\KhachHang::where('SoDienThoai', $request->phone)->first();
        if (!$khachHang) {
            return response()->json(['message' => 'Số điện thoại không tồn tại trong hệ thống'], 404);
        }

        $taiKhoan = TaiKhoan::where('MaKH', $khachHang->MaKH)->first();
        if (!$taiKhoan) {
            return response()->json(['message' => 'Tài khoản không tồn tại'], 404);
        }

        return response()->json([
            'message' => 'Mã OTP đã được gửi',
            'otp' => self::OTP_DEFAULT, // 👈 Trả về OTP để test
        ]);
    }

    // Bước 2: Xác nhận OTP
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^0[0-9]{9}$/',
            'otp' => 'required|string|size:6',
        ]);

        // 👈 Chỉ cần so sánh với OTP mặc định
        if ($request->otp !== self::OTP_DEFAULT) {
            return response()->json(['message' => 'Mã OTP không đúng'], 400);
        }

        return response()->json(['message' => 'Xác nhận OTP thành công']);
    }

    // Bước 3: Đặt lại mật khẩu
    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^0[0-9]{9}$/',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Kiểm tra OTP lần nữa
        if ($request->otp !== self::OTP_DEFAULT) {
            return response()->json(['message' => 'Mã OTP không hợp lệ'], 400);
        }

        $khachHang = \App\Models\KhachHang::where('SoDienThoai', $request->phone)->first();
        if (!$khachHang) {
            return response()->json(['message' => 'Không tìm thấy khách hàng'], 404);
        }

        $taiKhoan = TaiKhoan::where('MaKH', $khachHang->MaKH)->first();
        if (!$taiKhoan) {
            return response()->json(['message' => 'Tài khoản không tồn tại'], 404);
        }

        $taiKhoan->update(['MatKhau' => Hash::make($request->password)]);

        return response()->json(['message' => 'Đặt lại mật khẩu thành công']);
    }
}