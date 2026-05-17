<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoaiPhong;
use App\Models\TienNghi;
use App\Services\Guards\LoaiPhongSoftDeleteGuard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LoaiPhongController extends Controller
{
    public function __construct(
        private LoaiPhongSoftDeleteGuard $guard
    ) {
    }

    private function fullData(): array
    {
        return ['phongs', 'tienNghis', 'khuyenMai', 'hinhs'];
    }

    private function success($data = null, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code)->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
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
        $data = LoaiPhong::with($this->fullData())->get();

        return $this->success($data, 'Lấy danh sách loại phòng thành công');
    }

    public function trash()
    {
        $data = LoaiPhong::onlyTrashed()->with($this->fullData())->get();

        return $this->success($data, 'Lấy danh sách loại phòng trong thùng rác thành công');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'TenLoaiPhong' => 'required|string|max:50',
            'Mota' => 'nullable|string',
            'NguoiLon' => 'required|integer|min:1',
            'TreEm' => 'required|integer|min:0',
            'GiaPhong' => 'required|numeric|min:0',
            'MaKM' => [
                'nullable',
                'string',
                'max:10',
                Rule::exists('KhuyenMai', 'MaKM')->whereNull('deleted_at'),
            ],
        ]);

        $loaiPhong = LoaiPhong::create($data);
        $result = LoaiPhong::with($this->fullData())->find($loaiPhong->MaLoaiPhong);

        return $this->success($result, 'Tạo loại phòng thành công', 201);
    }

    public function show($id)
    {
        $loaiPhong = LoaiPhong::with($this->fullData())
            ->withCount(['phongs as soLuongPhong'])
            ->find($id);

        if (!$loaiPhong) {
            return $this->error('Không tìm thấy loại phòng', 404);
        }

        return $this->success($loaiPhong, 'Lấy chi tiết loại phòng thành công');
    }

    public function update(Request $request, $id)
    {
        $loaiPhong = LoaiPhong::find($id);

        if (!$loaiPhong) {
            return $this->error('Không tìm thấy loại phòng', 404);
        }

        $data = $request->validate([
            'TenLoaiPhong' => 'required|string|max:50',
            'Mota' => 'nullable|string',
            'NguoiLon' => 'required|integer|min:1',
            'TreEm' => 'required|integer|min:0',
            'GiaPhong' => 'required|numeric|min:0',
            'MaKM' => [
                'nullable',
                'string',
                'max:10',
                Rule::exists('KhuyenMai', 'MaKM')->whereNull('deleted_at'),
            ],
        ]);

        $loaiPhong->update($data);

        return $this->success(
            LoaiPhong::with($this->fullData())->find($id),
            'Cập nhật loại phòng thành công'
        );
    }

    public function destroy($id)
    {
        $loaiPhong = LoaiPhong::find($id);

        if (!$loaiPhong) {
            return $this->error('Không tìm thấy loại phòng', 404);
        }

        $decision = $this->guard->canSoftDelete($loaiPhong);
        if (!$decision['allowed']) {
            return $this->error($decision['message'], 409);
        }

        $loaiPhong->delete();

        return $this->success(null, 'Đã chuyển loại phòng vào thùng rác');
    }

    public function restore($id)
    {
        $loaiPhong = LoaiPhong::onlyTrashed()->find($id);

        if (!$loaiPhong) {
            return $this->error('Không tìm thấy loại phòng trong thùng rác', 404);
        }

        $loaiPhong->restore();

        return $this->success(
            LoaiPhong::with($this->fullData())->find($id),
            'Khôi phục loại phòng thành công'
        );
    }

    public function forceDelete($id)
    {
        $loaiPhong = LoaiPhong::onlyTrashed()->find($id);

        if (!$loaiPhong) {
            return $this->error('Không tìm thấy loại phòng trong thùng rác', 404);
        }

        $decision = $this->guard->canForceDelete($loaiPhong);
        if (!$decision['allowed']) {
            return $this->error($decision['message'], 409);
        }

        $loaiPhong->forceDelete();

        return $this->success(null, 'Xóa vĩnh viễn loại phòng thành công');
    }

    public function updateTienNghi(Request $request, $id)
    {
        $request->validate([
            'tienNghiIds' => 'present|array',
            'tienNghiIds.*' => Rule::exists('TienNghi', 'MaTienNghi')->whereNull('deleted_at'),
        ]);

        $loaiPhong = LoaiPhong::find($id);

        if (!$loaiPhong) {
            return $this->error('Không tìm thấy loại phòng', 404);
        }

        $ids = $request->tienNghiIds;

        if (empty($ids)) {
            $loaiPhong->tienNghis()->detach();

            return $this->success(null, 'Đã xóa toàn bộ tiện nghi của loại phòng');
        }

        $loaiPhong->tienNghis()->sync($ids);

        return $this->success(
            LoaiPhong::with($this->fullData())->find($id),
            'Cập nhật tiện nghi thành công'
        );
    }

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
            return $this->error('Tiện nghi đã tồn tại trên loại phòng này', 409);
        }

        if (!TienNghi::where('MaTienNghi', $tienNghiId)->exists()) {
            return $this->error('KhÃ´ng tÃ¬m tháº¥y tiá»‡n nghi', 404);
        }

        $loaiPhong->tienNghis()->attach($tienNghiId);

        return $this->success(null, 'Thêm tiện nghi vào loại phòng thành công');
    }

    public function removeTienNghi($id, $tienNghiId)
    {
        $loaiPhong = LoaiPhong::find($id);

        if (!$loaiPhong) {
            return $this->error('Không tìm thấy loại phòng', 404);
        }

        $loaiPhong->tienNghis()->detach($tienNghiId);

        return $this->success(null, 'Gỡ tiện nghi khỏi loại phòng thành công');
    }
}
