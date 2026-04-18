<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ThanhToan;
use App\Models\HoaDon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ThanhToanController extends Controller
{
    // 1. Lấy danh sách lịch sử thanh toán
    public function index()
    {
        $payments = ThanhToan::with('hoaDon')->get();
        return response()->json($payments, 200);
    }

    // 2. Thực hiện thanh toán (Ghi nhận giao dịch)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MaHD'          => 'required|exists:HoaDon,MaHD',
            'SoTien'        => 'required|numeric|min:0',
            'PhuongThuc'    => 'required|in:1,2', // 1: Thẻ, 2: QRCode
            'LoaiThanhToan' => 'required|in:0,1', // 0: Đặt cọc, 1: Checkout
            'NgayThanhToan' => 'nullable|date_format:Y-m-d H:i:s'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Sử dụng Transaction để đảm bảo tính toàn vẹn dữ liệu
        try {
            return DB::transaction(function () use ($request) {
                // Tạo bản ghi thanh toán
                $thanhToan = ThanhToan::create($request->all());

                // Tự động cập nhật số tiền đã trả vào bảng HoaDon
                $hoaDon = HoaDon::find($request->MaHD);
                $hoaDon->increment('DaThanhToan', $request->SoTien);

                // Nếu là thanh toán Checkout (Loai=1), có thể tự động chuyển trạng thái HD
                if ($request->LoaiThanhToan == 1 && $hoaDon->DaThanhToan >= $hoaDon->TongTien) {
                    $hoaDon->update(['TrangThai' => 1]);
                }

                return response()->json([
                    'message' => 'Ghi nhận thanh toán thành công',
                    'data' => $thanhToan
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi xử lý giao dịch', 'error' => $e->getMessage()], 500);
        }
    }

    // 3. Xem chi tiết một giao dịch thanh toán
    public function show($id)
    {
        $payment = ThanhToan::with('hoaDon')->find($id);
        if (!$payment) {
            return response()->json(['message' => 'Không tìm thấy giao dịch'], 404);
        }
        return response()->json($payment, 200);
    }

    // 4. Lấy các lần thanh toán của một Hóa đơn cụ thể
    public function getByHoaDon($maHD)
    {
        $payments = ThanhToan::where('MaHD', $maHD)->get();
        return response()->json($payments, 200);
    }
}