<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Phong;
use App\Services\Guards\PhongSoftDeleteGuard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PhongController extends Controller
{
    public function __construct(
        private PhongSoftDeleteGuard $guard
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

    public function index(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $query = Phong::with([
            'loaiPhong',
            'chiTietDatPhong.datPhong' => function ($q) use ($today) {
                $q->where('NgayNhanPhong', '<=', $today)
                    ->where('NgayTraPhong', '>=', $today)
                    ->whereIn('TinhTrang', [0, 1, 2]);
            },
        ])->whereHas('loaiPhong', function ($q) {
            $q->whereNull('LoaiPhong.deleted_at');
        });

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

    public function trash()
    {
        $data = Phong::onlyTrashed()->with('loaiPhong')->get();

        return $this->success($data, 'Lấy danh sách phòng trong thùng rác thành công');
    }

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

    public function store(Request $request)
    {
        $data = $request->validate([
            'SoPhong' => 'required|unique:Phong,SoPhong',
            'MaLoaiPhong' => [
                'required',
                Rule::exists('LoaiPhong', 'MaLoaiPhong')->whereNull('deleted_at'),
            ],
            'TinhTrang' => 'required|integer',
        ]);

        $phong = Phong::create($data);

        return $this->success($phong, 'Tạo phòng thành công', 201);
    }

    public function show($id)
    {
        $today = Carbon::today()->toDateString();

        $phong = Phong::with([
            'loaiPhong',
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

    public function update(Request $request, $id)
    {
        $phong = Phong::find($id);

        if (!$phong) {
            return $this->error('Không tìm thấy phòng', 404);
        }

        $data = $request->validate([
            'SoPhong' => 'required|unique:Phong,SoPhong,' . $id . ',MaPhong',
            'MaLoaiPhong' => [
                'required',
                Rule::exists('LoaiPhong', 'MaLoaiPhong')->whereNull('deleted_at'),
            ],
            'TinhTrang' => 'required|integer',
        ]);

        $phong->update($data);

        return $this->success($phong, 'Cập nhật phòng thành công');
    }

    public function destroy($id)
    {
        $phong = Phong::find($id);

        if (!$phong) {
            return $this->error('Không tìm thấy phòng', 404);
        }

        $decision = $this->guard->canSoftDelete($phong);
        if (!$decision['allowed']) {
            return $this->error($decision['message'], 409);
        }

        $phong->delete();

        return $this->success(null, 'Đã chuyển phòng vào thùng rác');
    }

    public function restore($id)
    {
        $phong = Phong::onlyTrashed()->with('loaiPhong')->find($id);

        if (!$phong) {
            return $this->error('Không tìm thấy phòng trong thùng rác', 404);
        }

        $phong->restore();

        return $this->success($phong->fresh(['loaiPhong']), 'Khôi phục phòng thành công');
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
    }

    public function timKiemPhong(Request $request)
    {
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
            ->where('TinhTrang', '!=', 2)
            ->whereHas('loaiPhong', function ($q) {
                $q->whereNull('LoaiPhong.deleted_at');
            })
            ->whereDoesntHave('chiTietDatPhong.datPhong', function ($q) use ($checkIn, $checkOut) {
                $q->whereIn('TinhTrang', [0, 1, 2])
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
