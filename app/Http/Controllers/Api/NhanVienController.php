<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NhanVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NhanVienController extends Controller
{
    // 1. Lấy danh sách nhân viên kèm thông tin tài khoản
    // get /api/nhan-vien
    public function index()
    {
        $nhanViens = NhanVien::with('taiKhoan')->get();
        return response()->json($nhanViens, 200);
    }

    // 2. Thêm mới nhân viên
    // post /api/nhan-vien 
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'TenNV' => 'required|string|max:100',
            'MaTK'  => 'required|exists:TaiKhoan,MaTK|unique:NhanVien,MaTK',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $nhanVien = NhanVien::create($request->all());
        return response()->json([
            'message' => 'Thêm nhân viên thành công',
            'data' => $nhanVien
        ], 201);
    }

    // 3. Xem chi tiết nhân viên và các hóa đơn họ đã lập
    // get /api/nhan-vien/id
    public function show($id)
    {
        // Thêm 'hoaDons' vào with nếu bạn muốn xem lịch sử làm việc của NV này
        $nhanVien = NhanVien::with(['taiKhoan', 'hoaDons'])->find($id);

        if (!$nhanVien) {
            return response()->json(['message' => 'Không tìm thấy nhân viên'], 404);
        }

        return response()->json($nhanVien, 200);
    }

    // 4. Cập nhật thông tin nhân viên
    //put /api/nhan-vien/id
    public function update(Request $request, $id)
    {
        $nhanVien = NhanVien::find($id);
        if (!$nhanVien) {
            return response()->json(['message' => 'Không tìm thấy nhân viên'], 404);
        }

        // Nếu cập nhật MaTK thì cũng cần kiểm tra unique, ngoại trừ chính nó
        $validator = Validator::make($request->all(), [
            'TenNV' => 'sometimes|string|max:100',
            'MaTK'  => 'sometimes|exists:TaiKhoan,MaTK|unique:NhanVien,MaTK,' . $id . ',MaNV',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $nhanVien->update($request->all());
        return response()->json(['message' => 'Cập nhật thành công', 'data' => $nhanVien], 200);
    }

    // 5. Xóa nhân viên
    // delete /api/nhan-vien/id
    public function destroy($id)
    {
        $nhanVien = NhanVien::find($id);
        if (!$nhanVien) {
            return response()->json(['message' => 'Không tìm thấy nhân viên'], 404);
        }

        $nhanVien->delete();
        return response()->json(['message' => 'Đã xóa nhân viên'], 200);
    }
}