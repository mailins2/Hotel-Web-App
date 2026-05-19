<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TaiKhoan;
use App\Services\Guards\TaiKhoanDeletionGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TaiKhoanController extends Controller
{
    public function __construct(
        private TaiKhoanDeletionGuard $guard
    ) {
    }

    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => TaiKhoan::with(['khachHang', 'nhanVien'])->get(),
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Email' => 'required|email|unique:TaiKhoan,Email',
            'MatKhau' => 'required|min:6',
            'LoaiTaiKhoan' => 'required|in:0,1,2,3,4',
            'TrangThai' => 'nullable|in:0,1',
            'MaKH' => 'nullable|exists:KhachHang,MaKH|unique:TaiKhoan,MaKH',
            'MaNV' => 'nullable|exists:NhanVien,MaNV|unique:TaiKhoan,MaNV',
        ]);

        $this->validateOwnerByRole($validator, $request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $owner = $this->resolveOwnerPayload($request);

        $taiKhoan = TaiKhoan::create([
            'Email' => $request->Email,
            'MatKhau' => Hash::make($request->MatKhau),
            'LoaiTaiKhoan' => $request->LoaiTaiKhoan,
            'TrangThai' => $request->TrangThai ?? 1,
            'MaKH' => $owner['MaKH'],
            'MaNV' => $owner['MaNV'],
        ]);

        return response()->json([
            'message' => 'Tạo tài khoản thành công',
            'data' => $taiKhoan->load(['khachHang', 'nhanVien']),
        ], 201);
    }

    public function show($id)
    {
        $taiKhoan = TaiKhoan::with(['khachHang', 'nhanVien'])->find($id);

        if (!$taiKhoan) {
            return response()->json(['message' => 'Không tìm thấy tài khoản'], 404);
        }

        return response()->json($taiKhoan, 200);
    }

    public function update(Request $request, $id)
    {
        $taiKhoan = TaiKhoan::with(['khachHang', 'nhanVien'])->find($id);

        if (!$taiKhoan) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }

        $validator = Validator::make($request->all(), [
            'Email' => 'sometimes|email|unique:TaiKhoan,Email,' . $id . ',MaTK',
            'MatKhau' => 'sometimes|nullable|min:6',
            'LoaiTaiKhoan' => 'sometimes|in:0,1,2,3,4',
            'TrangThai' => 'sometimes|in:0,1',
            'MaKH' => 'sometimes|nullable|exists:KhachHang,MaKH|unique:TaiKhoan,MaKH,' . $id . ',MaTK',
            'MaNV' => 'sometimes|nullable|exists:NhanVien,MaNV|unique:TaiKhoan,MaNV,' . $id . ',MaTK',
        ]);

        $this->validateOwnerByRole($validator, $request, $taiKhoan);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['Email', 'LoaiTaiKhoan', 'TrangThai']);

        if ($request->filled('MatKhau')) {
            $data['MatKhau'] = Hash::make($request->MatKhau);
        }

        if ($request->has('MaKH') || $request->has('MaNV') || $request->has('LoaiTaiKhoan')) {
            $owner = $this->resolveOwnerPayload($request, $taiKhoan);
            $data['MaKH'] = $owner['MaKH'];
            $data['MaNV'] = $owner['MaNV'];
        }

        $taiKhoan->update($data);

        return response()->json([
            'message' => 'Cập nhật thành công',
            'data' => $taiKhoan->fresh(['khachHang', 'nhanVien']),
        ], 200);
    }

    public function destroy($id)
    {
        $taiKhoan = TaiKhoan::with(['khachHang', 'nhanVien'])->find($id);

        if (!$taiKhoan) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }
        $decision = $this->guard->resolveAction($taiKhoan);

        if (($decision['action'] ?? 'delete') === 'deactivate') {
            $taiKhoan->update(['TrangThai' => 0]);

            return response()->json([
                'message' => $decision['message'],
                'action' => 'deactivated',
                'data' => $taiKhoan->fresh(['khachHang', 'nhanVien']),
            ], 200);
        }

        $taiKhoan->delete();

        return response()->json([
            'message' => 'Đã xóa tài khoản',
            'action' => 'deleted',
        ], 200);
    }

    private function validateOwnerByRole($validator, Request $request, ?TaiKhoan $taiKhoan = null): void
    {
        $validator->after(function ($validator) use ($request, $taiKhoan) {
            $role = (int) ($request->input('LoaiTaiKhoan', $taiKhoan?->LoaiTaiKhoan ?? -1));
            $maKH = $request->has('MaKH') ? $request->input('MaKH') : $taiKhoan?->MaKH;
            $maNV = $request->has('MaNV') ? $request->input('MaNV') : $taiKhoan?->MaNV;

            if ($maKH && $maNV) {
                $validator->errors()->add('owner', 'Tài khoản chỉ được liên kết với khách hàng hoặc nhân viên.');
                return;
            }

            if ($role === 0 && $maNV) {
                $validator->errors()->add('MaNV', 'Tài khoản khách hàng không được liên kết nhân viên.');
            }

            if (in_array($role, [1, 2, 3, 4], true) && $maKH) {
                $validator->errors()->add('MaKH', 'Tài khoản nhân viên/admin không được liên kết khách hàng.');
            }
        });
    }

    private function resolveOwnerPayload(Request $request, ?TaiKhoan $taiKhoan = null): array
    {
        $role = (int) ($request->input('LoaiTaiKhoan', $taiKhoan?->LoaiTaiKhoan ?? -1));
        $maKH = $request->has('MaKH') ? $request->input('MaKH') : $taiKhoan?->MaKH;
        $maNV = $request->has('MaNV') ? $request->input('MaNV') : $taiKhoan?->MaNV;

        if ($role === 0) {
            return ['MaKH' => $maKH, 'MaNV' => null];
        }

        if (in_array($role, [1, 2, 3, 4], true)) {
            return ['MaKH' => null, 'MaNV' => $maNV];
        }

        return ['MaKH' => null, 'MaNV' => null];
    }
}
