<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DatPhong;
use App\Models\ChiTietDatPhong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatPhongController extends Controller
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

    // =========================================
    // GET /api/dat-phong
    public function index()
    {
        $data = DatPhong::with('chiTietDatPhong')->get();
        return $this->success($data, 'Lấy danh sách đặt phòng thành công');
    }

    // =========================================
    // GET /api/dat-phong/{id}
    public function show($id)
    {
        $datPhong = DatPhong::with('chiTietDatPhong')->find($id);

        if (!$datPhong) {
            return $this->error('Không tìm thấy đơn đặt phòng', 404);
        }

        return $this->success($datPhong, 'Lấy chi tiết đặt phòng thành công');
    }

    // =========================================
    // POST /api/dat-phong
    public function store(Request $request)
    {
        $data = $request->validate([
            'MaKH' => 'required|exists:KhachHang,MaKH',
            'NgayNhanPhong' => 'required|date',
            'NgayTraPhong' => 'required|date|after:NgayNhanPhong',
            'phongs' => 'required|array|min:1'
        ]);

        DB::beginTransaction();

        try {
            //  check phòng đã bị đặt chưa
            $exists = ChiTietDatPhong::whereIn('MaPhong', $data['phongs'])
                ->whereHas('datPhong', function ($q) use ($data) {
                    $q->where('NgayNhanPhong', '<', $data['NgayTraPhong'])
                      ->where('NgayTraPhong', '>', $data['NgayNhanPhong']);
                })->exists();

            if ($exists) {
                return $this->error('Có phòng đã được đặt trong khoảng thời gian này', 400);
            }

            $datPhong = DatPhong::create([
                'MaKH' => $data['MaKH'],
                'NgayDat' => now(),
                'NgayNhanPhong' => $data['NgayNhanPhong'],
                'NgayTraPhong' => $data['NgayTraPhong'],
                'TinhTrang' => 0
            ]);

            foreach ($data['phongs'] as $maPhong) {
                ChiTietDatPhong::create([
                    'MaDatPhong' => $datPhong->MaDatPhong,
                    'MaPhong' => $maPhong,
                    'SoLuong' => 1
                ]);
            }

            DB::commit();

            return $this->success(
                $datPhong->load('chiTietDatPhong'),
                'Đặt phòng thành công',
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }

    // =========================================
    // PUT /api/dat-phong/{id}
    public function update(Request $request, $id)
    {
        $datPhong = DatPhong::find($id);

        if (!$datPhong) {
            return $this->error('Không tìm thấy đơn đặt phòng', 404);
        }

        $data = $request->validate([
            'NgayNhanPhong' => 'required|date',
            'NgayTraPhong' => 'required|date|after:NgayNhanPhong',
            'phongs' => 'required|array|min:1'
        ]);

        DB::beginTransaction();

        try {
            // check trùng lịch (trừ chính nó)
            $exists = ChiTietDatPhong::whereIn('MaPhong', $data['phongs'])
                ->whereHas('datPhong', function ($q) use ($data, $id) {
                    $q->where('MaDatPhong', '!=', $id)
                      ->where('NgayNhanPhong', '<', $data['NgayTraPhong'])
                      ->where('NgayTraPhong', '>', $data['NgayNhanPhong']);
                })->exists();

            if ($exists) {
                return $this->error('Có phòng bị trùng lịch', 400);
            }

            $datPhong->update([
                'NgayNhanPhong' => $data['NgayNhanPhong'],
                'NgayTraPhong' => $data['NgayTraPhong']
            ]);

            // xoá chi tiết cũ
            ChiTietDatPhong::where('MaDatPhong', $id)->delete();

            // thêm lại
            foreach ($data['phongs'] as $maPhong) {
                ChiTietDatPhong::create([
                    'MaDatPhong' => $id,
                    'MaPhong' => $maPhong,
                    'SoLuong' => 1
                ]);
            }

            DB::commit();

            return $this->success(
                $datPhong->load('chiTietDatPhong'),
                'Cập nhật đặt phòng thành công'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }

    // =========================================
    // DELETE /api/dat-phong/{id}
    public function destroy($id)
    {
        $datPhong = DatPhong::find($id);

        if (!$datPhong) {
            return $this->error('Không tìm thấy đơn đặt phòng', 404);
        }

        DB::beginTransaction();

        try {
            ChiTietDatPhong::where('MaDatPhong', $id)->delete();
            $datPhong->delete();

            DB::commit();

            return $this->success(null, 'Xóa đặt phòng thành công');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }
    //====
    //POST /api/dat-phong/{id}/phong
    public function addPhong(Request $request, $id)
    {
        $datPhong = DatPhong::find($id);

        if (!$datPhong) {
            return $this->error('Không tìm thấy đơn đặt phòng', 404);
        }

        $data = $request->validate([
            'MaPhong' => 'required|exists:Phong,MaPhong',
            'SoLuong' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();

        try {
            $exists = ChiTietDatPhong::where('MaPhong', $data['MaPhong'])
                ->whereHas('datPhong', function ($q) use ($datPhong) {
                    $q->where('MaDatPhong', '!=', $datPhong->MaDatPhong)
                    ->where('NgayNhanPhong', '<', $datPhong->NgayTraPhong)
                    ->where('NgayTraPhong', '>', $datPhong->NgayNhanPhong);
                })->exists();

            if ($exists) {
                return $this->error('Phòng đã được đặt trong khoảng thời gian này', 400);
            }

            $already = ChiTietDatPhong::where('MaDatPhong', $id)
                ->where('MaPhong', $data['MaPhong'])
                ->first();

            if ($already) {
                $already->increment('SoLuong', $data['SoLuong']);
            } else {
                ChiTietDatPhong::create([
                    'MaDatPhong' => $id,
                    'MaPhong' => $data['MaPhong'],
                    'SoLuong' => $data['SoLuong']
                ]);
            }

            DB::commit();

            return $this->success(
                $datPhong->load('chiTietDatPhong'),
                'Thêm phòng vào đơn thành công'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }
    //====
    //DELETE /api/dat-phong/{id}/phong/{maPhong}
    public function removePhong($id, $maPhong)
    {
        $datPhong = DatPhong::find($id);

        if (!$datPhong) {
            return $this->error('Không tìm thấy đơn đặt phòng', 404);
        }

        $deleted = ChiTietDatPhong::where('MaDatPhong', $id)
            ->where('MaPhong', $maPhong)
            ->delete();

        if (!$deleted) {
            return $this->error('Phòng không tồn tại trong đơn', 404);
        }

        return $this->success(
            $datPhong->load('chiTietDatPhong'),
            'Xóa phòng khỏi đơn thành công'
        );
    }
}