<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Phong;
use Illuminate\Http\Request;

class PhongController extends Controller
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

    // GET /api/phong
    public function index(Request $request)
    {
        $query = Phong::with('loaiPhong');

        if ($request->MaLoaiPhong) {
            $query->where('MaLoaiPhong', $request->MaLoaiPhong);
        }

        if ($request->TinhTrang !== null) {
            $query->where('TinhTrang', $request->TinhTrang);
        }

        $data = $query->get();

        return $this->success($data, 'Lấy danh sách phòng thành công');
    }

    // POST /api/phong
    public function store(Request $request)
    {
        $data = $request->validate([
            'SoPhong' => 'required|unique:Phong,SoPhong',
            'MaLoaiPhong' => 'required|exists:LoaiPhong,MaLoaiPhong',
            'TinhTrang' => 'required|integer'
        ]);

        $phong = Phong::create($data);

        return $this->success($phong, 'Tạo phòng thành công', 201);
    }

    // GET /api/phong/{id}
    public function show($id)
    {
        $phong = Phong::with('loaiPhong')->find($id);

        if (!$phong) {
            return $this->error('Không tìm thấy phòng', 404);
        }

        return $this->success($phong, 'Lấy chi tiết phòng thành công');
    }

    // PUT /api/phong/{id}
    public function update(Request $request, $id)
    {
        $phong = Phong::find($id);

        if (!$phong) {
            return $this->error('Không tìm thấy phòng', 404);
        }

        $data = $request->validate([
            'SoPhong' => 'required|unique:Phong,SoPhong,' . $id . ',MaPhong',
            'MaLoaiPhong' => 'required|exists:LoaiPhong,MaLoaiPhong',
            'TinhTrang' => 'required|integer'
        ]);

        $phong->update($data);

        return $this->success($phong, 'Cập nhật phòng thành công');
    }

    // DELETE /api/phong/{id}
    public function destroy($id)
    {
        $phong = Phong::find($id);

        if (!$phong) {
            return $this->error('Không tìm thấy phòng', 404);
        }

        $phong->delete();

        return $this->success(null, 'Xóa phòng thành công');
    }
    //=============================================================================
    // kiểm tra phòng trống 
    //GET /api/phong/trong?checkIn=2026-04-10&checkOut=2026-04-12
  public function phongTrong(Request $request)
    {
        $data = $request->validate([
            'checkIn' => 'required|date',
            'checkOut' => 'required|date|after:checkIn'
        ]);

        $checkIn = $data['checkIn'];
        $checkOut = $data['checkOut'];

        $phongs = Phong::with([
                'loaiPhong.tienNghis',
                'loaiPhong.bangGias'
            ])
            ->where('TinhTrang', 0)
            ->whereDoesntHave('chiTietDatPhong.datPhong', function ($q) use ($checkIn, $checkOut) {
                $q->whereIn('TinhTrang', [0,1])
                ->where('NgayNhanPhong', '<', $checkOut)
                ->where('NgayTraPhong', '>', $checkIn);
            })
            ->get();

        return response()->json($phongs);
    }
}