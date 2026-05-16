<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DichVu;
use App\Services\Guards\DichVuSoftDeleteGuard;
use Illuminate\Http\Request;

class DichVuController extends Controller
{
    public function __construct(
        private DichVuSoftDeleteGuard $guard
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
        return $this->success(DichVu::all(), 'Lấy danh sách dịch vụ thành công');
    }

    public function trash()
    {
        return $this->success(
            DichVu::onlyTrashed()->get(),
            'Lấy danh sách dịch vụ trong thùng rác thành công'
        );
    }

    public function show($id)
    {
        $dv = DichVu::find($id);

        if (!$dv) {
            return $this->error('Không tìm thấy dịch vụ', 404);
        }

        return $this->success($dv, 'Lấy chi tiết dịch vụ thành công');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'TenDV' => 'required|string|max:100',
            'GiaDV' => 'required|numeric|min:0',
            'LoaiDV' => 'required|in:1,2,3,4',
        ]);

        $dv = DichVu::create($data);

        return $this->success($dv, 'Tạo dịch vụ thành công', 201);
    }

    public function update(Request $request, $id)
    {
        $dv = DichVu::find($id);

        if (!$dv) {
            return $this->error('Không tìm thấy dịch vụ', 404);
        }

        $data = $request->validate([
            'TenDV' => 'required|string|max:100',
            'GiaDV' => 'required|numeric|min:0',
            'LoaiDV' => 'required|in:1,2,3',
        ]);

        $dv->update($data);

        return $this->success($dv, 'Cập nhật dịch vụ thành công');
    }

    public function destroy($id)
    {
        $dv = DichVu::find($id);

        if (!$dv) {
            return $this->error('Không tìm thấy dịch vụ', 404);
        }

        $decision = $this->guard->canSoftDelete($dv);
        if (!$decision['allowed']) {
            return $this->error($decision['message'], 409);
        }

        $dv->delete();

        return $this->success(null, 'Đã chuyển dịch vụ vào thùng rác');
    }

    public function restore($id)
    {
        $dv = DichVu::onlyTrashed()->find($id);

        if (!$dv) {
            return $this->error('Không tìm thấy dịch vụ trong thùng rác', 404);
        }

        $dv->restore();

        return $this->success($dv->fresh(), 'Khôi phục dịch vụ thành công');
    }

    public function forceDelete($id)
    {
        $dv = DichVu::onlyTrashed()->find($id);

        if (!$dv) {
            return $this->error('Không tìm thấy dịch vụ trong thùng rác', 404);
        }

        $decision = $this->guard->canForceDelete($dv);
        if (!$decision['allowed']) {
            return $this->error($decision['message'], 409);
        }

        $dv->forceDelete();

        return $this->success(null, 'Xóa vĩnh viễn dịch vụ thành công');
    }
}
