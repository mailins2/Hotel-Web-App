<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TaiKhoanController extends Controller
{
    /**
     * Lấy danh sách tất cả tài khoản
     */
    public function index()
    {
        $dsTaiKhoan = TaiKhoan::all();
        return response()->json([
            'status' => 'success',
            'data'   => $dsTaiKhoan
        ], 200);
    }

    /**
     * Lưu tài khoản mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Email'        => 'required|email|unique:TaiKhoan,Email',
            'MatKhau'      => 'required|min:6',
            'LoaiTaiKhoan' => 'required|in:0,1,2',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $taiKhoan = TaiKhoan::create([
            'Email'        => $request->Email,
            'MatKhau'      => Hash::make($request->MatKhau), // Mã hóa mật khẩu
            'LoaiTaiKhoan' => $request->LoaiTaiKhoan,
            'TrangThai'    => $request->TrangThai ?? 1,
        ]);

        return response()->json([
            'message' => 'Tạo tài khoản thành công',
            'data'    => $taiKhoan
        ], 201);
    }

    /**
     * Xem chi tiết một tài khoản kèm thông tin Khách hàng/Nhân viên (nếu có)
     */
    public function show($id)
    {
        $taiKhoan = TaiKhoan::with(['khachHang', 'nhanVien'])->find($id);

        if (!$taiKhoan) {
            return response()->json(['message' => 'Không tìm thấy tài khoản'], 404);
        }

        return response()->json($taiKhoan, 200);
    }

    /**
     * Cập nhật thông tin tài khoản
     */
    public function update(Request $request, $id)
    {
        $taiKhoan = TaiKhoan::find($id);
        if (!$taiKhoan) return response()->json(['message' => 'Không tìm thấy'], 404);

        $validator = Validator::make($request->all(), [
            'Email' => 'email|unique:TaiKhoan,Email,' . $id . ',MaTK',
            'LoaiTaiKhoan' => 'in:0,1,2',
            'TrangThai' => 'in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Chỉ cập nhật các trường được gửi lên
        $data = $request->only(['Email', 'LoaiTaiKhoan', 'TrangThai']);
        
        if ($request->has('MatKhau')) {
            $data['MatKhau'] = Hash::make($request->MatKhau);
        }

        $taiKhoan->update($data);

        return response()->json([
            'message' => 'Cập nhật thành công',
            'data'    => $taiKhoan
        ], 200);
    }

    /**
     * Xóa tài khoản
     */
    public function destroy($id)
    {
        $taiKhoan = TaiKhoan::find($id);
        if (!$taiKhoan) return response()->json(['message' => 'Không tìm thấy'], 404);

        $taiKhoan->delete();

        return response()->json(['message' => 'Đã xóa tài khoản'], 200);
    }
}