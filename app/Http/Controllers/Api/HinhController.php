<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hinh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HinhController extends Controller
{
    // 1. Lấy toàn bộ danh sách hình ảnh
    public function index()
    {
        $hinhs = Hinh::with(['loaiPhongs', 'dichVus'])->get();
        return response()->json($hinhs, 200);
    }

    // 2. Thêm mới hình ảnh (Lưu URL)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Url'         => 'required|string|max:100',
            'MaLoaiPhong' => 'nullable|exists:LoaiPhong,MaLoaiPhong',
            'MaDV'        => 'nullable|exists:DichVu,MaDV',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $hinh = Hinh::create($request->all());
        return response()->json([
            'message' => 'Thêm hình ảnh thành công',
            'data' => $hinh
        ], 201);
    }

    // 3. Xem chi tiết 1 hình ảnh
    public function show($id)
    {
        $hinh = Hinh::with(['loaiPhongs', 'dichVus'])->find($id);
        if (!$hinh) {
            return response()->json(['message' => 'Không tìm thấy hình ảnh'], 404);
        }
        return response()->json($hinh, 200);
    }

    // 4. Cập nhật URL hoặc liên kết
    public function update(Request $request, $id)
    {
        $hinh = Hinh::find($id);
        if (!$hinh) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }

        $hinh->update($request->only(['Url', 'MaLoaiPhong', 'MaDV']));
        return response()->json(['message' => 'Cập nhật thành công', 'data' => $hinh], 200);
    }

    // 5. Xóa hình ảnh
    public function destroy($id)
    {
        $hinh = Hinh::find($id);
        if (!$hinh) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }
        $hinh->delete();
        return response()->json(['message' => 'Đã xóa hình ảnh'], 200);
    }
}