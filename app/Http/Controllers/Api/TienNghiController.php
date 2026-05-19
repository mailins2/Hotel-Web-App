<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TienNghi;
use App\Services\Guards\TienNghiDeletionGuard;
use Illuminate\Http\Request;

class TienNghiController extends Controller
{
    public function __construct(
        private TienNghiDeletionGuard $guard
    ) {
    }

    private function success($data = null, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    private function error(string $message = 'Error', int $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }

    public function index()
    {
        return $this->success(TienNghi::all(), 'Lấy danh sách tiện nghi thành công');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'TenTienNghi' => 'required|string|max:100',
        ]);

        $tienNghi = TienNghi::create($data);

        return $this->success($tienNghi, 'Tạo tiện nghi thành công', 201);
    }

    public function show($id)
    {
        $tienNghi = TienNghi::with('loaiPhongs')->find($id);

        if (!$tienNghi) {
            return $this->error('Không tìm thấy tiện nghi', 404);
        }

        return $this->success($tienNghi, 'Lấy chi tiết tiện nghi thành công');
    }

    public function update(Request $request, $id)
    {
        $tienNghi = TienNghi::find($id);

        if (!$tienNghi) {
            return $this->error('Không tìm thấy tiện nghi', 404);
        }

        $data = $request->validate([
            'TenTienNghi' => 'required|string|max:100',
        ]);

        $tienNghi->update($data);

        return $this->success($tienNghi, 'Cập nhật tiện nghi thành công');
    }

    public function destroy($id)
    {
        $tienNghi = TienNghi::find($id);

        if (!$tienNghi) {
            return $this->error('Không tìm thấy tiện nghi', 404);
        }

        $decision = $this->guard->canDelete($tienNghi);
        if (!$decision['allowed']) {
            return $this->error($decision['message'], 409);
        }

        $tienNghi->loaiPhongs()->detach();
        $tienNghi->delete();

        return $this->success(null, 'Xóa tiện nghi thành công');
    }
}
