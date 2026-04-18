<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DanhGia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DanhGiaController extends Controller
{
    // 1. Lấy danh sách tất cả đánh giá
    public function index()
    {
        $danhGias = DanhGia::with('datPhong.khachHang')->get();
        return response()->json($danhGias, 200);
    }

    // 2. Gửi đánh giá mới
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MaDatPhong'  => 'required|exists:DatPhong,MaDatPhong|unique:DanhGia,MaDatPhong',
            'Sao'         => 'required|integer|min:1|max:5',
            'MoTa'        => 'nullable|string|max:200',
            'NgayDanhGia' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $danhGia = DanhGia::create($request->all());
        return response()->json([
            'message' => 'Cảm ơn bạn đã đánh giá dịch vụ!',
            'data' => $danhGia
        ], 201);
    }

    // 3. Xem chi tiết một đánh giá
    public function show($id)
    {
        $danhGia = DanhGia::with('datPhong')->find($id);
        if (!$danhGia) {
            return response()->json(['message' => 'Không tìm thấy đánh giá'], 404);
        }
        return response()->json($danhGia, 200);
    }

    // 4. Cập nhật đánh giá (Khách muốn sửa lại nội dung)
    public function update(Request $request, $id)
    {
        $danhGia = DanhGia::find($id);
        if (!$danhGia) {
            return response()->json(['message' => 'Không tìm thấy đánh giá'], 404);
        }

        $validator = Validator::make($request->all(), [
            'Sao'  => 'sometimes|integer|min:1|max:5',
            'MoTa' => 'sometimes|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $danhGia->update($request->only(['Sao', 'MoTa']));
        return response()->json(['message' => 'Cập nhật đánh giá thành công', 'data' => $danhGia], 200);
    }

    // 5. Xóa đánh giá
    public function destroy($id)
    {
        $danhGia = DanhGia::find($id);
        if (!$danhGia) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }
        $danhGia->delete();
        return response()->json(['message' => 'Đã xóa đánh giá'], 200);
    }
}