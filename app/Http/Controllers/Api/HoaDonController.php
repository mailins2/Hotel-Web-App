<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HoaDon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HoaDonController extends Controller
{
    // 1. Lấy danh sách hóa đơn kèm thông tin liên quan
    public function index()
    {
        $hoaDons = HoaDon::with(['datPhong', 'nhanVien', 'khuyenMai'])->get();
        return response()->json($hoaDons, 200);
    }

    // 2. Lập hóa đơn mới (Khi khách check-out)
    // Thêm mới hóa đơn
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MaDatPhong' => 'required|exists:DatPhong,MaDatPhong|unique:HoaDon,MaDatPhong',
            'NgayLapHD'  => 'required|date',
            'MaKM'       => 'nullable|exists:KhuyenMai,MaKM',
            'TongTien'   => 'required|numeric|min:0',
            'MaNV'       => 'required|exists:NhanVien,MaNV',
            'TrangThai'  => 'required|in:0,1', // 0: Chưa thanh toán, 1: Đã thanh toán
            'DaThanhToan'=> 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $hoaDon = HoaDon::create($request->all());
        return response()->json(['message' => 'Lập hóa đơn thành công', 'data' => $hoaDon], 201);
    }

    // 3. Xem chi tiết hóa đơn và các lần thanh toán/chi tiết dịch vụ
    public function show($id)
    {
        $hoaDon = HoaDon::with(['datPhong', 'nhanVien', 'khuyenMai', 'chiTietHoaDons', 'thanhToans'])
                        ->find($id);

        if (!$hoaDon) {
            return response()->json(['message' => 'Không tìm thấy hóa đơn'], 404);
        }

        return response()->json($hoaDon, 200);
    }

    // 4. Cập nhật trạng thái thanh toán
    public function update(Request $request, $id)
    {
        $hoaDon = HoaDon::find($id);
        if (!$hoaDon) {
            return response()->json(['message' => 'Không tìm thấy hóa đơn'], 404);
        }

        // 1. Thêm Validation để bảo vệ dữ liệu
        $validator = Validator::make($request->all(), [
            'TrangThai'   => 'sometimes|in:0,1', // Chỉ cho phép 0 hoặc 1
            'DaThanhToan' => 'sometimes|numeric|min:0',
            'MaKM'        => 'sometimes|nullable|exists:KhuyenMai,MaKM',
            'TongTien'    => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Cập nhật dữ liệu
        // Dùng $request->only để đảm bảo họ không sửa được MaHD hoặc MaDatPhong
        $hoaDon->update($request->only(['TrangThai', 'DaThanhToan', 'MaKM', 'TongTien']));
        
        return response()->json([
            'message' => 'Cập nhật hóa đơn thành công',
            'data' => $hoaDon
        ], 200);
    }

    // 5. Xóa hóa đơn (Chỉ nên dùng khi nhập sai hoàn toàn)
    public function destroy($id)
    {
        $hoaDon = HoaDon::find($id);
        if (!$hoaDon) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }
        $hoaDon->delete();
        return response()->json(['message' => 'Đã xóa hóa đơn'], 200);
    }
}