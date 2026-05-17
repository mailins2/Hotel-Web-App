<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NhanVien;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NhanVienController extends Controller
{
    public function index()
    {
        return response()->json(NhanVien::with('taiKhoan')->get(), 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'TenNV' => 'required|string|max:100',
            'ChucVu' => 'nullable|integer|in:0,1',
            'MaTK' => 'nullable|exists:TaiKhoan,MaTK',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $accountId = $data['MaTK'] ?? null;
        unset($data['MaTK']);

        $nhanVien = DB::transaction(function () use ($data, $accountId) {
            $nhanVien = NhanVien::create($data);

            if ($accountId) {
                TaiKhoan::where('MaTK', $accountId)->update([
                    'MaKH' => null,
                    'MaNV' => $nhanVien->MaNV,
                ]);
            }

            return $nhanVien->load('taiKhoan');
        });

        return response()->json([
            'message' => 'Thêm nhân viên thành công',
            'data' => $nhanVien,
        ], 201);
    }

    public function show($id)
    {
        $nhanVien = NhanVien::with(['taiKhoan', 'hoaDons'])->find($id);

        if (!$nhanVien) {
            return response()->json(['message' => 'Không tìm thấy nhân viên'], 404);
        }

        return response()->json($nhanVien, 200);
    }

    public function update(Request $request, $id)
    {
        $nhanVien = NhanVien::find($id);

        if (!$nhanVien) {
            return response()->json(['message' => 'Không tìm thấy nhân viên'], 404);
        }

        $validator = Validator::make($request->all(), [
            'TenNV' => 'sometimes|string|max:100',
            'ChucVu' => 'sometimes|nullable|integer|in:0,1',
            'MaTK' => 'sometimes|nullable|exists:TaiKhoan,MaTK',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $hasAccountInput = array_key_exists('MaTK', $data);
        $accountId = $data['MaTK'] ?? null;
        unset($data['MaTK']);

        DB::transaction(function () use ($nhanVien, $data, $hasAccountInput, $accountId) {
            $nhanVien->update($data);

            if ($hasAccountInput) {
                TaiKhoan::where('MaNV', $nhanVien->MaNV)->update(['MaNV' => null]);

                if ($accountId) {
                    TaiKhoan::where('MaTK', $accountId)->update([
                        'MaKH' => null,
                        'MaNV' => $nhanVien->MaNV,
                    ]);
                }
            }
        });

        return response()->json([
            'message' => 'Cập nhật thành công',
            'data' => $nhanVien->fresh('taiKhoan'),
        ], 200);
    }

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
