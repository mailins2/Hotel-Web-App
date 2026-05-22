<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KhachHangController;
use App\Http\Controllers\Api\LoaiPhongController;
use App\Http\Controllers\Api\PhongController;
use App\Http\Controllers\Api\TienNghiController;
use App\Http\Controllers\Api\DatPhongController;
use App\Http\Controllers\Api\DichVuController;
use App\Http\Controllers\Api\SuDungDichVuController;
use App\Http\Controllers\Api\TaiKhoanController;
use App\Http\Controllers\Api\NhanVienController;
use App\Http\Controllers\Api\DenBuHuHongController;
use App\Http\Controllers\Api\KhuyenMaiController;
use App\Http\Controllers\Api\KhoKhuyenMaiController;
use App\Http\Controllers\Api\LuuTruController;
use App\Http\Controllers\Api\HoaDonController;
use App\Http\Controllers\Api\ChiTietHoaDonController;
use App\Http\Controllers\Api\ThanhToanController;
use App\Http\Controllers\Api\DanhGiaController;
use App\Http\Controllers\Api\HinhController;
use App\Http\Controllers\Api\ZaloPay\PaymentController;
use App\Http\Controllers\Api\VnPay\PaymentController as VnPayPaymentController;
use App\Http\Controllers\Api\AuthMobileController;
use App\Http\Controllers\Api\ForgotPasswordController;

Route::apiResource('tien-nghi', TienNghiController::class);

Route::apiResource('khachhangs', KhachHangController::class);

Route::apiResource('loai-phong', LoaiPhongController::class);

Route::post('/loai-phong/{id}/tien-nghi', [LoaiPhongController::class, 'updateTienNghi']);
Route::post('/loai-phong/{id}/tien-nghi/{tienNghiId}', [LoaiPhongController::class, 'addTienNghi']);
Route::delete('/loai-phong/{id}/tien-nghi/{tienNghiId}', [LoaiPhongController::class, 'removeTienNghi']);



Route::get('/phong/tim-kiem', [PhongController::class, 'timKiemPhong']);
Route::apiResource('phong', PhongController::class);

// Lịch sử đặt phòng của khách hàng
Route::get('/khach-hang/{maKH}/dat-phong', [DatPhongController::class, 'lichSuDatPhong']);
Route::post('dat-phong/{id}/change-room', [DatPhongController::class, 'changeRoom']);
Route::post('dat-phong/{id}/add-room', [DatPhongController::class, 'addRoom']);
Route::delete('dat-phong/{id}/remove-room/{maPhong}', [DatPhongController::class, 'removeRoom']);

Route::post('dat-phong/{id}/check-in', [DatPhongController::class, 'checkIn']);
Route::post('dat-phong/{id}/check-out', [DatPhongController::class, 'checkOut']);
Route::apiResource('dat-phong', DatPhongController::class);

Route::apiResource('dich-vu', \App\Http\Controllers\Api\DichVuController::class);

Route::get(
    'su-dung-dich-vu/dat-phong/{id}',
    [\App\Http\Controllers\Api\SuDungDichVuController::class, 'byDatPhong']
);
Route::apiResource('su-dung-dich-vu', \App\Http\Controllers\Api\SuDungDichVuController::class);

Route::apiResource('tai-khoan', TaiKhoanController::class);

Route::apiResource('nhan-vien', NhanVienController::class);

Route::apiResource('khach-hang', KhachHangController::class);

Route::apiResource('den-bu', DenBuHuHongController::class);

Route::apiResource('luu-tru', LuuTruController::class);

Route::apiResource('khuyen-mai', KhuyenMaiController::class);



// 🔥 Đổi mã bằng điểm (mobile app)
Route::post('kho-khuyen-mai/doi-bang-diem', [KhoKhuyenMaiController::class, 'doiBangDiem']);

// 🔥 Sử dụng mã khi thanh toán (mobile app)
Route::post('kho-khuyen-mai/su-dung', [KhoKhuyenMaiController::class, 'suDung']);

// 🔥 Kiểm tra điểm trước khi đổi
Route::get('kho-khuyen-mai/kiem-tra-diem/{maKH}/{maKM}', [KhoKhuyenMaiController::class, 'kiemTraDiem']);

Route::get('kho-khuyen-mai/khach-hang/{maKH}', [KhoKhuyenMaiController::class, 'showByKhachHang']);
Route::put('kho-khuyen-mai/update-status', [KhoKhuyenMaiController::class, 'updateStatus']);
Route::apiResource('kho-khuyen-mai', KhoKhuyenMaiController::class);

Route::apiResource('hoa-don', HoaDonController::class);


Route::apiResource('chi-tiet-hoa-don', ChiTietHoaDonController::class);

Route::get('thanh-toan', [ThanhToanController::class, 'index']);
Route::post('thanh-toan', [ThanhToanController::class, 'store']);
Route::get('thanh-toan/{id}', [ThanhToanController::class, 'show']);
Route::get('thanh-toan/hoa-don/{maHD}', [ThanhToanController::class, 'getByHoaDon']);

Route::get('danh-gia/loai-phong/{maLoaiPhong}', [DanhGiaController::class, 'filterByLoaiPhong']);
Route::apiResource('danh-gia', DanhGiaController::class);

Route::apiResource('hinh-anh', HinhController::class);

// Route để thực hiện tạo đơn hàng và lấy link thanh toán
Route::post('/zalopay-payment', [PaymentController::class, 'createPayment']);

// Route để ZaloPay gọi về (Callback) báo kết quả thanh toán
Route::post('/zalopay-callback', [PaymentController::class, 'callback']);

Route::post('/vnpay-payment', [VnPayPaymentController::class, 'createPayment']);
Route::get('/vnpay-ipn', [VnPayPaymentController::class, 'ipn']);
Route::get('/vnpay-return', [VnPayPaymentController::class, 'return']);

// route mobile app

// Auth Mobile
Route::post('/mobile/login', [AuthMobileController::class, 'login']);
Route::post('/mobile/register/step1', [AuthMobileController::class, 'registerStepOne']);
Route::post('/mobile/register/step2', [AuthMobileController::class, 'registerStepTwo']);
Route::get('/mobile/provinces', [AuthMobileController::class, 'getProvinces']);
Route::get('/mobile/districts/{provinceCode}', [AuthMobileController::class, 'getDistricts']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/mobile/logout', [AuthMobileController::class, 'logout']);
     Route::get('/mobile/user-profile', [AuthMobileController::class, 'getUserProfile']);
});

Route::get('/vnpay/check-status/{txnRef}', function ($txnRef) {
    $mapping = Cache::get("vnpay:txn:{$txnRef}");
    
    return response()->json([
        'status' => 'success',
        'data' => [
            'txn_ref' => $txnRef,
            'paid' => $mapping['paid'] ?? false,
            'paid_at' => $mapping['paid_at'] ?? null,
            'amount' => $mapping['amount'] ?? 0,
        ],
    ]);
});
// Thêm dòng này vào sau các route của DatPhongController
Route::post('/dat-phong/{id}/cancel', [DatPhongController::class, 'cancel']);

Route::get('danh-gia/kiem-tra/{maDatPhong}', [DanhGiaController::class, 'kiemTraDanhGia']);
// Quên mật khẩu
Route::post('quen-mat-khau/gui-otp', [ForgotPasswordController::class, 'sendOTP']);
Route::post('quen-mat-khau/xac-nhan-otp', [ForgotPasswordController::class, 'verifyOTP']);
Route::post('quen-mat-khau/dat-lai-mat-khau', [ForgotPasswordController::class, 'resetPassword']);