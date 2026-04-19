<?php

namespace App\Http\Controllers\Api;

# 

use App\Http\Controllers\Controller;
use App\Models\KhachHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KhachHangController extends Controller
{
    // 1. Lấy danh sách khách hàng
    //get /api/khach-hang
    public function index()
    {
        $khachHangs = KhachHang::with('taiKhoan')->get();
        return response()->json($khachHangs, 200);
    }

    // 2. Thêm mới khách hàng
    //post /api/khach-hang
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Đổi từ required thành nullable
            'MaTK'         => 'nullable|exists:TaiKhoan,MaTK|unique:KhachHang,MaTK',
            'TenKH'        => 'required|string|max:100',
            'SoDienThoai'  => 'required|unique:KhachHang,SoDienThoai',
            'CCCD'         => 'required|unique:KhachHang,CCCD',
            'NgaySinh'     => 'required|date',
            'GioiTinh'     => 'required|in:0,1',
            'DiaChi'       => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $khachHang = KhachHang::create($request->all());
        return response()->json([
            'message' => 'Thêm khách hàng thành công',
            'data' => $khachHang
        ], 201);
    }

    // 3. Xem chi tiết 1 khách hàng
    // get /api/khach-hang/id
    public function show($id)
    {
        $khachHang = KhachHang::with(['taiKhoan', 'datPhongs'])->find($id);

        if (!$khachHang) {
            return response()->json(['message' => 'Không tìm thấy khách hàng'], 404);
        }

        return response()->json($khachHang, 200);
    }

    // 4. Cập nhật thông tin
    // put /api/khach-hang
    public function update(Request $request, $id)
    {
        $khachHang = KhachHang::find($id);
        if (!$khachHang) {
            return response()->json(['message' => 'Không tìm thấy khách hàng'], 404);
        }

        $khachHang->update($request->all());
        return response()->json(['message' => 'Cập nhật thành công', 'data' => $khachHang], 200);
    }

    // 5. Xóa khách hàng
    //delete /api/khach-hang/{id}
    public function destroy($id)
    {
        $khachHang = KhachHang::find($id);
        if (!$khachHang) {
            return response()->json(['message' => 'Không tìm thấy khách hàng'], 404);
        }

        $khachHang->delete();
        return response()->json(['message' => 'Đã xóa khách hàng'], 200);
    }
}