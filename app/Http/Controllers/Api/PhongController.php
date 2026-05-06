<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Phong;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
    // kiểm tra phòng trống và số lượng người ở 
    //GET /api/phong/tim-kiem?checkIn=2026-04-25&checkOut=2026-04-27&NguoiLon=2&TreEm=1

    public function timKiemPhong(Request $request)
    {
        // Validate
        $data = $request->validate([
            'checkIn' => 'required|date|after_or_equal:today',
            'checkOut' => 'required|date|after:checkIn',
            'soNguoi' => 'sometimes|integer|min:1',
            'NguoiLon' => 'required_without:soNguoi|integer|min:1',
            'TreEm' => 'sometimes|integer|min:0',
            'SoPhong' => 'sometimes|integer|min:1',
        ]);

        $checkIn = $data['checkIn'];
        $checkOut = $data['checkOut'];
        $nguoiLon = $data['NguoiLon'] ?? $data['soNguoi'];
        $treEm = $data['TreEm'] ?? 0;
        $soPhong = $data['SoPhong'] ?? 1;
        $tongKhach = $nguoiLon + $treEm;

        $phongs = Phong::with([
                'loaiPhong:MaLoaiPhong,TenLoaiPhong,NguoiLon,TreEm'
            ])
            ->select('MaPhong', 'SoPhong', 'TinhTrang', 'MaLoaiPhong')

            // chỉ loại phòng bảo trì
            ->where('TinhTrang', '!=', 2)

            // loại phòng bị trùng lịch
            ->whereDoesntHave('chiTietDatPhong.datPhong', function ($q) use ($checkIn, $checkOut) {
                $q->whereIn('TinhTrang', [0,1,2]) // HOLD + CONFIRM + ĐANG Ở
                ->where('NgayNhanPhong', '<', $checkOut)
                ->where('NgayTraPhong', '>', $checkIn);
            })

            ->get();

        $phongs = $phongs
            ->groupBy('MaLoaiPhong')
            ->filter(function ($rooms) use ($nguoiLon, $treEm, $soPhong, $tongKhach) {
                $loaiPhong = $rooms->first()?->loaiPhong;

                if (!$loaiPhong || $rooms->count() < $soPhong) {
                    return false;
                }

                $sucChuaNguoiLon = max((int) $loaiPhong->NguoiLon, 0);
                $sucChuaTreEm = max((int) $loaiPhong->TreEm, 0);
                $sucChuaTong = max($sucChuaNguoiLon + $sucChuaTreEm, $sucChuaNguoiLon, 1);

                return ($sucChuaNguoiLon * $soPhong) >= $nguoiLon
                    && ($sucChuaTreEm * $soPhong) >= $treEm
                    && ($sucChuaTong * $soPhong) >= $tongKhach;
            })
            ->flatten(1)
            ->values();

        return $this->success($phongs, 'Tìm phòng thành công');
    }
}
