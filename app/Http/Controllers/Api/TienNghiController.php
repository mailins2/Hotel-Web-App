<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TienNghi;
use Illuminate\Http\Request;

class TienNghiController extends Controller
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

    // GET /api/tien-nghi
    public function index()
    {
        $data = TienNghi::all();

        return $this->success($data, 'Lấy danh sách tiện nghi thành công');
    }

    // POST /api/tien-nghi
    public function store(Request $request)
    {
        $data = $request->validate([
            'TenTienNghi' => 'required|string|max:100'
        ]);

        $tienNghi = TienNghi::create($data);

        return $this->success($tienNghi, 'Tạo tiện nghi thành công', 201);
    }
    // tiện nghi có trong những loại phòng nào ?
    // GET /api/tien-nghi/{id}
    public function show($id)
    {
        $tienNghi = TienNghi::with('loaiPhongs')->find($id);

        if (!$tienNghi) {
            return $this->error('Không tìm thấy tiện nghi', 404);
        }

        return $this->success($tienNghi, 'Lấy chi tiết tiện nghi thành công');
    }

    // PUT /api/tien-nghi/{id}
    public function update(Request $request, $id)
    {
        $tienNghi = TienNghi::find($id);

        if (!$tienNghi) {
            return $this->error('Không tìm thấy tiện nghi', 404);
        }

        $data = $request->validate([
            'TenTienNghi' => 'required|string|max:100'
        ]);

        $tienNghi->update($data);

        return $this->success($tienNghi, 'Cập nhật tiện nghi thành công');
    }

    // DELETE /api/tien-nghi/{id}
    public function destroy($id)
    {
        $tienNghi = TienNghi::find($id);

        if (!$tienNghi) {
            return $this->error('Không tìm thấy tiện nghi', 404);
        }

        $tienNghi->delete();

        return $this->success(null, 'Xóa tiện nghi thành công');
    }
}