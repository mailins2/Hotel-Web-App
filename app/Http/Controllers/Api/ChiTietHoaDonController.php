<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChiTietHoaDon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChiTietHoaDonController extends Controller
{
    // 1. Lấy danh sách tất cả chi tiết (hiếm khi dùng nhưng cần để quản trị)
    public function index()
    {
        $chiTiets = ChiTietHoaDon::with(['hoaDon', 'denBu', 'suDung', 'loaiPhong'])->get();
        return response()->json($chiTiets, 200);
    }

    // 2. Thêm một mục chi phí vào hóa đơn
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MaHD'        => 'required|exists:HoaDon,MaHD',
            'MaLoaiPhong' => 'nullable|exists:LoaiPhong,MaLoaiPhong',
            'MaSuDung'    => 'nullable|exists:SuDungDichVu,MaSuDung',
            'MaDenBu'     => 'nullable|exists:DenBuHuHong,MaDenBu',
            'MoTa'        => 'nullable|string|max:200',
            'SoLuong'     => 'required|integer|min:1',
            'DonGia'      => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $chiTiet = ChiTietHoaDon::create($request->all());
        return response()->json([
            'message' => 'Đã thêm chi tiết hóa đơn thành công',
            'data' => $chiTiet
        ], 201);
    }

    // 3. Xem các chi tiết của một hóa đơn cụ thể
    public function showByHoaDon($maHD)
    {
        $chiTiets = ChiTietHoaDon::with(['denBu', 'suDung', 'loaiPhong'])
            ->where('MaHD', $maHD)
            ->get();

        if ($chiTiets->isEmpty()) {
            return response()->json(['message' => 'Hóa đơn này chưa có chi tiết nào'], 404);
        }

        return response()->json($chiTiets, 200);
    }

    // 4. Cập nhật chi tiết (ví dụ sửa số lượng hoặc đơn giá)
    public function update(Request $request, $id)
    {
        $chiTiet = ChiTietHoaDon::find($id);
        if (!$chiTiet) {
            return response()->json(['message' => 'Không tìm thấy chi tiết này'], 404);
        }

        $chiTiet->update($request->only(['MoTa', 'SoLuong', 'DonGia']));
        return response()->json(['message' => 'Cập nhật thành công', 'data' => $chiTiet], 200);
    }

    // 5. Xóa một chi tiết khỏi hóa đơn
    public function destroy($id)
    {
        $chiTiet = ChiTietHoaDon::find($id);
        if (!$chiTiet) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }
        $chiTiet->delete();
        return response()->json(['message' => 'Đã xóa chi tiết hóa đơn'], 200);
    }
}