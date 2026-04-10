<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KhuyenMai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class KhuyenMaiController extends Controller
{
    // 1. Lấy danh sách khuyến mãi (Có thể lọc các KM còn hạn)
    public function index()
    {
        $khuyenMais = KhuyenMai::all();
        return response()->json($khuyenMais, 200);
    }

    // 2. Tạo chương trình khuyến mãi mới
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'TenKM'            => 'required|string|max:100',
            'MoTa'             => 'nullable|string|max:200',
            'Diem'             => 'required|integer|min:0', // Điểm cần để đổi hoặc điểm tặng
            'NgayBatDau'       => 'required|date',
            'NgayKetThuc'      => 'required|date|after_or_equal:NgayBatDau',
            'PhanTramGiamGia'  => 'required|numeric|between:0,100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $khuyenMai = KhuyenMai::create($request->all());

        return response()->json([
            'message' => 'Tạo khuyến mãi thành công',
            'data' => $khuyenMai
        ], 201);
    }

    // 3. Xem chi tiết 1 khuyến mãi
    public function show($id)
    {
        $khuyenMai = KhuyenMai::with('khoKhuyenMai')->find($id);

        if (!$khuyenMai) {
            return response()->json(['message' => 'Không tìm thấy khuyến mãi'], 404);
        }

        return response()->json($khuyenMai, 200);
    }

    // 4. Cập nhật khuyến mãi
    public function update(Request $request, $id)
    {
        $khuyenMai = KhuyenMai::find($id);
        if (!$khuyenMai) {
            return response()->json(['message' => 'Không tìm thấy khuyến mãi'], 404);
        }

        $validator = Validator::make($request->all(), [
            'NgayKetThuc'      => 'sometimes|date|after_or_equal:NgayBatDau',
            'PhanTramGiamGia'  => 'sometimes|numeric|between:0,100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $khuyenMai->update($request->all());
        return response()->json(['message' => 'Cập nhật thành công', 'data' => $khuyenMai], 200);
    }

    // 5. Xóa khuyến mãi
    public function destroy($id)
    {
        $khuyenMai = KhuyenMai::find($id);
        if (!$khuyenMai) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }

        $khuyenMai->delete();
        return response()->json(['message' => 'Đã xóa khuyến mãi'], 200);
    }
    // 6. Lấy danh sách khuyến mãi còn hạn
    public function getActivePromotions()
    {
        $today = Carbon::today();
        $active = KhuyenMai::where('NgayBatDau', '<=', $today)
                        ->where('NgayKetThuc', '>=', $today)
                        ->get();
        return response()->json($active, 200);
    }
}