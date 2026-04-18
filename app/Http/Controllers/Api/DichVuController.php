<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DichVu;
use Illuminate\Http\Request;

class DichVuController extends Controller
{
    private function success($data = null, $message = 'Success', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    private function error($message = 'Error', $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $code);
    }

    // =========================
    // GET /api/dich-vu
    public function index()
    {
        return $this->success(
            DichVu::all(),
            'Danh sách dịch vụ'
        );
    }

    // =========================
    // GET /api/dich-vu/{id}
    public function show($id)
    {
        $dv = DichVu::find($id);

        if (!$dv) {
            return $this->error('Không tìm thấy dịch vụ', 404);
        }

        return $this->success($dv, 'Chi tiết dịch vụ');
    }

    // =========================
    // POST /api/dich-vu
    public function store(Request $request)
    {
        $data = $request->validate([
            'TenDV' => 'required|string|max:100',
            'GiaDV' => 'required|numeric|min:0',
            'LoaiDV' => 'required|in:1,2,3'
        ]);

        $dv = DichVu::create($data);

        return $this->success($dv, 'Tạo dịch vụ thành công', 201);
    }

    // =========================
    // PUT /api/dich-vu/{id}
    public function update(Request $request, $id)
    {
        $dv = DichVu::find($id);

        if (!$dv) {
            return $this->error('Không tìm thấy dịch vụ', 404);
        }

        $data = $request->validate([
            'TenDV' => 'required|string|max:100',
            'GiaDV' => 'required|numeric|min:0',
            'LoaiDV' => 'required|in:1,2,3'
        ]);

        $dv->update($data);

        return $this->success($dv, 'Cập nhật thành công');
    }

    // =========================
    // DELETE /api/dich-vu/{id}
    public function destroy($id)
    {
        $dv = DichVu::find($id);

        if (!$dv) {
            return $this->error('Không tìm thấy dịch vụ', 404);
        }

        $dv->delete();

        return $this->success(null, 'Xóa dịch vụ thành công');
    }
}