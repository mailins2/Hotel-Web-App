<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoaiPhong;
use Illuminate\Http\Request;

class LoaiPhongController extends Controller
{
    private function fullData()
    {
        return ['phongs', 'tienNghis', 'bangGias', 'hinhs'];
    }

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

    // GET /api/loai-phong
    public function index()
    {
        $data = LoaiPhong::with($this->fullData())->get();

        return $this->success($data, 'Lấy danh sách loại phòng thành công');
    }

    // POST /api/loai-phong
    public function store(Request $request)
    {
        $data = $request->validate([
            'TenLoaiPhong' => 'required|string|max:50',
            'Mota' => 'nullable|string',
            'SoNguoiToiDa' => 'required|integer|min:1'
        ]);

        $loaiPhong = LoaiPhong::create($data);

        $result = LoaiPhong::with($this->fullData())
            ->find($loaiPhong->MaLoaiPhong);

        return $this->success($result, 'Tạo loại phòng thành công', 201);
    }

    // GET /api/loai-phong/{id}
    public function show($id)
    {
        $loaiPhong = LoaiPhong::with($this->fullData())->find($id);

        if (!$loaiPhong) {
            return $this->error('Không tìm thấy loại phòng', 404);
        }

        return $this->success($loaiPhong, 'Lấy chi tiết thành công');
    }

    // PUT /api/loai-phong/{id}
    public function update(Request $request, $id)
    {
        $loaiPhong = LoaiPhong::find($id);

        if (!$loaiPhong) {
            return $this->error('Không tìm thấy loại phòng', 404);
        }

        $data = $request->validate([
            'TenLoaiPhong' => 'required|string|max:50',
            'Mota' => 'nullable|string',
            'SoNguoiToiDa' => 'required|integer|min:1'
        ]);

        $loaiPhong->update($data);

        $result = LoaiPhong::with($this->fullData())->find($id);

        return $this->success($result, 'Cập nhật thành công');
    }

    // DELETE /api/loai-phong/{id}
    public function destroy($id)
    {
        $loaiPhong = LoaiPhong::find($id);

        if (!$loaiPhong) {
            return $this->error('Không tìm thấy loại phòng', 404);
        }

        $loaiPhong->delete();

        return $this->success(null, 'Xóa thành công');
    }

    // POST /api/loai-phong/{id}/tien-nghi
    public function updateTienNghi(Request $request, $id)
    {
        $request->validate([
            'tienNghiIds' => 'present|array'
        ]);

        $loaiPhong = LoaiPhong::find($id);

        if (!$loaiPhong) {
            return $this->error('Không tìm thấy loại phòng', 404);
        }

        $ids = $request->tienNghiIds;

        //  CASE: mảng rỗng
        if (empty($ids)) {
            $loaiPhong->tienNghis()->detach();

            return $this->success(null, 'Đã xóa toàn bộ tiện nghi');
        }

        //  CASE: có dữ liệu
        $loaiPhong->tienNghis()->sync($ids);

        $result = LoaiPhong::with($this->fullData())->find($id);

        return $this->success($result, 'Cập nhật tiện nghi thành công');
    }

    // POST api/loai-phong/{id}/tien-nghi/{tienNghiId}
    public function addTienNghi($id, $tienNghiId)
    {
        $loaiPhong = LoaiPhong::find($id);

        if (!$loaiPhong) {
            return $this->error('Không tìm thấy loại phòng', 404);
        }

        $exists = $loaiPhong->tienNghis()
            ->wherePivot('MaTienNghi', $tienNghiId)
            ->exists();

        if ($exists) {
            return $this->error('Tiện nghi đã tồn tại', 409);
        }

        $loaiPhong->tienNghis()->attach($tienNghiId);

        return $this->success(null, 'Thêm tiện nghi thành công');
    }

    // DELETE api/loai-phong/{id}/tien-nghi/{tienNghiId}
    public function removeTienNghi($id, $tienNghiId)
    {
        $loaiPhong = LoaiPhong::find($id);

        if (!$loaiPhong) {
            return $this->error('Không tìm thấy loại phòng', 404);
        }

        $loaiPhong->tienNghis()->detach($tienNghiId);

        return $this->success(null, 'Xóa tiện nghi thành công');
    }
}