<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DenBuHuHong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DenBuHuHongController extends Controller
{
    //get /api/den-bu
    // 1. Lấy danh sách các khoản đền bù
    public function index()
    {
        $denBus = DenBuHuHong::with('datPhong')->get();
        return response()->json($denBus, 200);
    }

    // 2. Tạo mới một khoản đền bù (Thường gọi khi check-out)
    //post /api/den-bu
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MaDatPhong'  => 'required|exists:DatPhong,MaDatPhong|unique:DenBuHuHong,MaDatPhong',
            'MoTa'        => 'nullable|string|max:200',
            'TienDenBu'   => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $denBu = DenBuHuHong::create($request->all());

        return response()->json([
            'message' => 'Đã ghi nhận khoản đền bù hư hỏng',
            'data' => $denBu
        ], 201);
    }
    // get /api/den-bu/id
    // 3. Xem chi tiết 1 khoản đền bù
    public function show($id)
    {
        $denBu = DenBuHuHong::with(['datPhong', 'chiTietHoaDon'])->find($id);

        if (!$denBu) {
            return response()->json(['message' => 'Không tìm thấy thông tin đền bù'], 404);
        }

        return response()->json($denBu, 200);
    }

    // 4. Cập nhật mô tả hoặc số tiền
    //put /api/den-bu/id
    public function update(Request $request, $id)
    {
        $denBu = DenBuHuHong::find($id);
        if (!$denBu) {
            return response()->json(['message' => 'Không tìm thấy thông tin'], 404);
        }

        $validator = Validator::make($request->all(), [
            'MoTa'        => 'sometimes|string|max:200',
            'TienDenBu'   => 'sometimes|numeric|min:0',
            'MaDatPhong'  => 'sometimes|exists:DatPhong,MaDatPhong|unique:DenBuHuHong,MaDatPhong,' . $id . ',MaDenBu',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $denBu->update($request->all());
        return response()->json(['message' => 'Cập nhật thành công', 'data' => $denBu], 200);
    }

    // 5. Xóa (Nếu nhập nhầm)
    //delete /api/den-bu/id
    public function destroy($id)
    {
        $denBu = DenBuHuHong::find($id);
        if (!$denBu) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }

        $denBu->delete();
        return response()->json(['message' => 'Đã xóa khoản đền bù'], 200);
    }
}