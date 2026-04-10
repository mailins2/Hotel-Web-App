<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuDungDichVu;
use Illuminate\Http\Request;

class SuDungDichVuController extends Controller
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
    // GET /api/su-dung-dich-vu
    public function index()
    {
        $data = SuDungDichVu::with('dichVu')->get();

        return $this->success($data, 'Danh sách sử dụng dịch vụ');
    }

    // =========================
    // GET /api/su-dung-dich-vu/{id}
    public function show($id)
    {
        $item = SuDungDichVu::with('dichVu')->find($id);

        if (!$item) {
            return $this->error('Không tìm thấy', 404);
        }

        return $this->success($item, 'Chi tiết');
    }

    // =========================
    // GET /api/su-dung-dich-vu/dat-phong/{id}
    public function byDatPhong($id)
    {
        $data = SuDungDichVu::with('dichVu')
            ->where('MaDatPhong', $id)
            ->get();

        return $this->success($data, 'Dịch vụ theo đơn');
    }

    // =========================
    // POST /api/su-dung-dich-vu
    public function store(Request $request)
    {
        $data = $request->validate([
            'MaDatPhong' => 'required|exists:DatPhong,MaDatPhong',
            'MaDV' => 'required|exists:DichVu,MaDV',
            'SoLuong' => 'required|integer|min:1'
        ]);

        // nếu đã có thì tăng số lượng
        $existing = SuDungDichVu::where('MaDatPhong', $data['MaDatPhong'])
            ->where('MaDV', $data['MaDV'])
            ->first();

        if ($existing) {
            $existing->increment('SoLuong', $data['SoLuong']);
            return $this->success($existing, 'Tăng số lượng dịch vụ');
        }

        $new = SuDungDichVu::create([
            ...$data,
            'ThoiGian' => now()
        ]);

        return $this->success($new, 'Thêm dịch vụ thành công', 201);
    }

    // =========================
    // PUT /api/su-dung-dich-vu/{id}
    public function update(Request $request, $id)
    {
        $item = SuDungDichVu::find($id);

        if (!$item) {
            return $this->error('Không tìm thấy', 404);
        }

        $data = $request->validate([
            'SoLuong' => 'required|integer|min:1'
        ]);

        $item->update($data);

        return $this->success($item, 'Cập nhật thành công');
    }

    // =========================
    // DELETE /api/su-dung-dich-vu/{id}
    public function destroy($id)
    {
        $item = SuDungDichVu::find($id);

        if (!$item) {
            return $this->error('Không tìm thấy', 404);
        }

        $item->delete();

        return $this->success(null, 'Xóa thành công');
    }
}