<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KhachHang;
use App\Models\TaiKhoan;
use App\Services\Guards\KhachHangDeletionGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KhachHangController extends Controller
{
    public function __construct(
        private KhachHangDeletionGuard $guard
    ) {
    }

    public function index()
    {
        return response()->json(KhachHang::with('taiKhoan')->get(), 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MaTK' => 'nullable|exists:TaiKhoan,MaTK',
            'TenKH' => 'required|string|max:100',
            'SoDienThoai' => 'required|string|max:15|unique:KhachHang,SoDienThoai',
            'CCCD' => 'nullable|string|max:20|unique:KhachHang,CCCD',
            'NgaySinh' => 'required|date',
            'GioiTinh' => 'required|in:0,1,2',
            'DiaChi' => 'nullable|string|max:200',
            'DIEM' => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $accountId = $data['MaTK'] ?? null;
        unset($data['MaTK']);

        $khachHang = DB::transaction(function () use ($data, $accountId) {
            $khachHang = KhachHang::create($data);

            if ($accountId) {
                TaiKhoan::where('MaTK', $accountId)->update([
                    'MaKH' => $khachHang->MaKH,
                    'MaNV' => null,
                ]);
            }

            return $khachHang->load('taiKhoan');
        });

        return response()->json([
            'message' => 'Thêm khách hàng thành công',
            'data' => $khachHang,
        ], 201);
    }

    public function show($id)
    {
        $khachHang = KhachHang::with(['taiKhoan', 'datPhongs'])->find($id);

        if (!$khachHang) {
            return response()->json(['message' => 'Không tìm thấy khách hàng'], 404);
        }

        return response()->json($khachHang, 200);
    }

    public function update(Request $request, $id)
    {
        $khachHang = KhachHang::find($id);

        if (!$khachHang) {
            return response()->json(['message' => 'Không tìm thấy khách hàng'], 404);
        }

        $validator = Validator::make($request->all(), [
            'MaTK' => 'sometimes|nullable|exists:TaiKhoan,MaTK',
            'TenKH' => 'sometimes|string|max:100',
            'SoDienThoai' => 'sometimes|string|max:15|unique:KhachHang,SoDienThoai,' . $id . ',MaKH',
            'CCCD' => 'sometimes|nullable|string|max:20|unique:KhachHang,CCCD,' . $id . ',MaKH',
            'NgaySinh' => 'sometimes|date',
            'GioiTinh' => 'sometimes|in:0,1,2',
            'DiaChi' => 'sometimes|nullable|string|max:200',
            'DIEM' => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $hasAccountInput = array_key_exists('MaTK', $data);
        $accountId = $data['MaTK'] ?? null;
        unset($data['MaTK']);

        DB::transaction(function () use ($khachHang, $data, $hasAccountInput, $accountId) {
            $khachHang->update($data);

            if ($hasAccountInput) {
                TaiKhoan::where('MaKH', $khachHang->MaKH)->update(['MaKH' => null]);

                if ($accountId) {
                    TaiKhoan::where('MaTK', $accountId)->update([
                        'MaKH' => $khachHang->MaKH,
                        'MaNV' => null,
                    ]);
                }
            }
        });

        return response()->json([
            'message' => 'Cập nhật thành công',
            'data' => $khachHang->fresh('taiKhoan'),
        ], 200);
    }

    public function destroy($id)
    {
        $khachHang = KhachHang::find($id);

        if (!$khachHang) {
            return response()->json(['message' => 'Không tìm thấy khách hàng'], 404);
        }
        $decision = $this->guard->canDelete($khachHang);
        if (!$decision['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $decision['message'],
            ], 409);
        }

        DB::transaction(function () use ($khachHang) {
            TaiKhoan::where('MaKH', $khachHang->MaKH)->delete();
            DB::table('KhoKhuyenMai')->where('MaKH', $khachHang->MaKH)->delete();
            $khachHang->delete();
        });

        return response()->json([
            'message' => 'Đã xóa khách hàng',
            'action' => 'deleted',
        ], 200);
    }
}
