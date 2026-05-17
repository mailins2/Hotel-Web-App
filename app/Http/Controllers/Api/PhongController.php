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
<<<<<<< HEAD
            'data' => $data
        ], $code);
=======
            'data' => $data,
        ], $code)->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
>>>>>>> 30842b8e36cd2b49b8229635044cc99eae2cf000
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
        $today = Carbon::today()->toDateString();

        $query = Phong::with([
            'loaiPhong.khuyenMai',
            'chiTietDatPhong.datPhong' => function ($q) use ($today) {
                $q->where('NgayNhanPhong', '<=', $today)
                    ->where('NgayTraPhong', '>=', $today)
                    ->whereIn('TinhTrang', [0, 1, 2]);
            },
        ]);

        if ($request->MaLoaiPhong) {
            $query->where('MaLoaiPhong', $request->MaLoaiPhong);
        }

        $data = $query->get()->map(function ($phong) {
            $datPhongHienTai = $this->resolveDatPhongHienTai($phong);

            $phong->TinhTrangGoc = (int) $phong->TinhTrang;
            $phong->TinhTrangHienTai = $this->resolveTinhTrangHienTai($phong);
            $phong->MaDatPhongHienTai = $datPhongHienTai?->MaDatPhong;
            $phong->DatPhongHienTai = $datPhongHienTai;
            $phong->TinhTrang = $phong->TinhTrangHienTai;
            unset($phong->chiTietDatPhong);

            return $phong;
        });

        if ($request->TinhTrang !== null) {
            $data = $data
                ->where('TinhTrangHienTai', (int) $request->TinhTrang)
                ->values();
        }

        return $this->success($data, 'Lấy danh sách phòng thành công');
    }

<<<<<<< HEAD
=======
    public function trash()
    {
        $data = Phong::onlyTrashed()->with('loaiPhong.khuyenMai')->get();

        return $this->success($data, 'Lấy danh sách phòng trong thùng rác thành công');
    }

>>>>>>> 30842b8e36cd2b49b8229635044cc99eae2cf000
    private function resolveTinhTrangHienTai(Phong $phong): int
    {
        if (in_array((int) $phong->TinhTrang, [2, 3], true)) {
            return (int) $phong->TinhTrang;
        }

        $bookingsToday = $phong->chiTietDatPhong
            ->pluck('datPhong')
            ->filter();

        if ($bookingsToday->contains(fn ($booking) => (int) $booking->TinhTrang === 2)) {
            return 2;
        }

        if ($bookingsToday->contains(fn ($booking) => in_array((int) $booking->TinhTrang, [0, 1], true))) {
            return 1;
        }

        return 0;
    }

    private function resolveDatPhongHienTai(Phong $phong)
    {
        return $phong->chiTietDatPhong
            ->pluck('datPhong')
            ->filter()
            ->sortByDesc(fn ($booking) => match ((int) $booking->TinhTrang) {
                2 => 3,
                1 => 2,
                0 => 1,
                default => 0,
            })
            ->first();
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
        $today = Carbon::today()->toDateString();

        $phong = Phong::with([
            'loaiPhong.khuyenMai',
            'chiTietDatPhong.datPhong' => function ($q) use ($today) {
                $q->where('NgayNhanPhong', '<=', $today)
                    ->where('NgayTraPhong', '>=', $today)
                    ->whereIn('TinhTrang', [0, 1, 2]);
            },
        ])->find($id);

        if (!$phong) {
            return $this->error('Không tìm thấy phòng', 404);
        }

        $datPhongHienTai = $this->resolveDatPhongHienTai($phong);

        $phong->TinhTrangGoc = (int) $phong->TinhTrang;
        $phong->TinhTrangHienTai = $this->resolveTinhTrangHienTai($phong);
        $phong->MaDatPhongHienTai = $datPhongHienTai?->MaDatPhong;
        $phong->DatPhongHienTai = $datPhongHienTai;
        $phong->TinhTrang = $phong->TinhTrangHienTai;
        unset($phong->chiTietDatPhong);

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

<<<<<<< HEAD
        return $this->success(null, 'Xóa phòng thành công');
=======
        return $this->success(null, 'Đã chuyển phòng vào thùng rác');
    }

    public function restore($id)
    {
        $phong = Phong::onlyTrashed()->with('loaiPhong.khuyenMai')->find($id);

        if (!$phong) {
            return $this->error('Không tìm thấy phòng trong thùng rác', 404);
        }

        $phong->restore();

        return $this->success($phong->fresh(['loaiPhong.khuyenMai']), 'Khôi phục phòng thành công');
    }

    public function forceDelete($id)
    {
        $phong = Phong::onlyTrashed()->find($id);

        if (!$phong) {
            return $this->error('Không tìm thấy phòng trong thùng rác', 404);
        }

        $decision = $this->guard->canForceDelete($phong);
        if (!$decision['allowed']) {
            return $this->error($decision['message'], 409);
        }

        $phong->forceDelete();

        return $this->success(null, 'Xóa vĩnh viễn phòng thành công');
>>>>>>> 30842b8e36cd2b49b8229635044cc99eae2cf000
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

        // ✅ Load đầy đủ: hinhs, bangGias, tienNghis
        $phongs = Phong::with([
<<<<<<< HEAD
                'loaiPhong.hinhs',
                'loaiPhong.bangGias',
                'loaiPhong.tienNghis',
=======
                'loaiPhong:MaLoaiPhong,TenLoaiPhong,NguoiLon,TreEm,GiaPhong,MaKM'
>>>>>>> 30842b8e36cd2b49b8229635044cc99eae2cf000
            ])
            ->select('MaPhong', 'SoPhong', 'TinhTrang', 'MaLoaiPhong')
            ->where('TinhTrang', '!=', 2)
            ->whereDoesntHave('chiTietDatPhong.datPhong', function ($q) use ($checkIn, $checkOut) {
                $q->whereIn('TinhTrang', [0, 1, 2])
                    ->where('NgayNhanPhong', '<', $checkOut)
                    ->where('NgayTraPhong', '>', $checkIn);
            })
            ->get();

        // ✅ Gom nhóm + trả về object đầy đủ
        $ketQua = $phongs
            ->groupBy('MaLoaiPhong')
            ->filter(function ($rooms) use ($nguoiLon, $treEm, $soPhong, $tongKhach) {
                $loaiPhong = $rooms->first()?->loaiPhong;

                if (!$loaiPhong || $rooms->count() < $soPhong) {
                    return false;
                }

                $sucChuaNguoiLon = max((int) $loaiPhong->NguoiLon, 0);
                $sucChuaTreEm = max((int) $loaiPhong->TreEm, 0);
                $sucChuaTong = max($sucChuaNguoiLon + $sucChuaTreEm, 1);

                return ($sucChuaNguoiLon * $soPhong) >= $nguoiLon
                    && ($sucChuaTreEm * $soPhong) >= $treEm
                    && ($sucChuaTong * $soPhong) >= $tongKhach;
            })
            ->map(function ($rooms) use ($soPhong) {
                $loaiPhong = $rooms->first()->loaiPhong;
                $soPhongTrong = $rooms->where('TinhTrang', 0)->count();

                return [
                    'MaLoaiPhong' => $loaiPhong->MaLoaiPhong,
                    'TenLoaiPhong' => $loaiPhong->TenLoaiPhong,
                    'Mota' => $loaiPhong->Mota ?? '',
                    'NguoiLon' => (int) $loaiPhong->NguoiLon,
                    'TreEm' => (int) $loaiPhong->TreEm,
                    'soPhongTrong' => $soPhongTrong,
                    'tongPhong' => $rooms->count(),
                    'soPhongCanThiet' => $soPhong,
                    // ✅ Ảnh
                    'hinhs' => $loaiPhong->hinhs->map(fn($h) => [
                        'Id' => $h->Id,
                        'Url' => $h->Url,
                    ])->values(),
                    // ✅ Giá
                    'bang_gias' => $loaiPhong->bangGias->map(fn($bg) => [
                        'MaLoaiPhong' => $bg->MaLoaiPhong,
                        'Mua' => $bg->Mua,
                        'GiaPhong' => $bg->GiaPhong,
                    ])->values(),
                    // ✅ Giá thấp nhất (nếu có, nếu không có thì trả về 0)
                    'giaThapNhat' => $loaiPhong->bangGias->min('GiaPhong') ?? 0,
                    // ✅ Tiện nghi
                    'tien_nghis' => $loaiPhong->tienNghis->map(fn($tn) => [
                        'MaTienNghi' => $tn->MaTienNghi,
                        'TenTienNghi' => $tn->TenTienNghi,
                    ])->values(),
                    // Danh sách phòng
                    'danhSachPhong' => $rooms->map(fn($p) => [
                        'MaPhong' => $p->MaPhong,
                        'SoPhong' => $p->SoPhong,
                        'TinhTrang' => $p->TinhTrang,
                    ])->values(),
                ];
            })
            ->values();

        return $this->success($ketQua, 'Tìm phòng thành công');
    }
}
