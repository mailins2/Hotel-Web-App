<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KhoKhuyenMai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KhoKhuyenMaiController extends Controller
{
    // 1. Lấy danh sách tất cả kho khuyến mãi (có kèm thông tin khách và tên KM)
    public function index()
    {
        $kho = KhoKhuyenMai::with(['khachHang', 'khuyenMai'])->get();
        return response()->json($kho, 200);
    }

    // 2. Cấp khuyến mãi cho khách hàng (Tặng mã)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MaKM' => 'required|exists:KhuyenMai,MaKM',
            'MaKH' => 'required|exists:KhachHang,MaKH',
            'TrangThai' => 'nullable|integer|in:0,1' // 0: Chưa dùng, 1: Đã dùng
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Kiểm tra xem khách hàng đã có mã này trong kho chưa (tránh trùng khóa chính)
        $exists = KhoKhuyenMai::where('MaKM', $request->MaKM)
                              ->where('MaKH', $request->MaKH)
                              ->exists();
        
        if ($exists) {
            return response()->json(['message' => 'Khách hàng này đã sở hữu mã khuyến mãi này rồi'], 400);
        }

        $item = KhoKhuyenMai::create($request->all());
        return response()->json(['message' => 'Cấp mã khuyến mãi thành công', 'data' => $item], 201);
    }

    // 3. Lấy danh sách khuyến mãi của riêng 1 khách hàng
    //get /api/kho-khuyen-mai/khach-hang/id
    public function showByKhachHang($maKH)
    {
        $danhSach = KhoKhuyenMai::with('khuyenMai')
            ->where('MaKH', $maKH)
            ->get();

        return response()->json($danhSach, 200);
    }

    // 4. Cập nhật trạng thái (Sử dụng mã khuyến mãi)
    //put api/kho-khuyen-mai/update-status
    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MaKM' => 'required',
            'MaKH' => 'required',
            'TrangThai' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $item = KhoKhuyenMai::where('MaKM', $request->MaKM)
                            ->where('MaKH', $request->MaKH)
                            ->first();

        if (!$item) {
            return response()->json(['message' => 'Không tìm thấy bản ghi'], 404);
        }

        $item->update(['TrangThai' => $request->TrangThai]);
        return response()->json(['message' => 'Cập nhật trạng thái thành công'], 200);
    }
}