<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChiTietDatPhong;
use App\Models\Phong;
use App\Services\Guards\PhongDeletionGuard;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PhongController extends Controller
{
    public function __construct(
        private PhongDeletionGuard $guard
    ) {
    }

    private function success($data = null, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code)->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
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
            'chiTietDatPhong' => function ($q) {
                $q->where('TrangThai', '!=', ChiTietDatPhong::CANCELLED);
            },
            'chiTietDatPhong.datPhong' => function ($q) use ($today) {
                $q->where('NgayNhanPhong', '<=', $today)
                    ->where('NgayTraPhong', '>=', $today)
                    ->whereIn('TinhTrang', [0, 1, 2]);
            },
        ])->whereHas('loaiPhong');

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

    private function resolveTinhTrangHienTai(Phong $phong): int
    {
        if ((int) $phong->TinhTrang === 3) {
            return (int) $phong->TinhTrang;
        }

        $detailsToday = $phong->chiTietDatPhong
            ->filter(fn ($detail) => $detail->datPhong);

        if ($detailsToday->contains(fn ($detail) => (int) $detail->TrangThai === ChiTietDatPhong::CHECKED_IN)) {
            return 2;
        }

        if ($detailsToday->contains(fn ($detail) => (int) $detail->TrangThai === ChiTietDatPhong::BOOKED
            && in_array((int) $detail->datPhong->TinhTrang, [0, 1], true))) {
            return 1;
        }

        return 0;
    }

    private function resolveDatPhongHienTai(Phong $phong)
    {
        return $phong->chiTietDatPhong
            ->filter(fn ($detail) => $detail->datPhong)
            ->sortByDesc(fn ($detail) => match ((int) $detail->TrangThai) {
                ChiTietDatPhong::CHECKED_IN => 3,
                ChiTietDatPhong::BOOKED => match ((int) $detail->datPhong->TinhTrang) {
                    1 => 2,
                    0 => 1,
                    default => 0,
                },
                default => 0,
            })
            ->first()
            ?->datPhong;
    }

    // POST /api/phong
    public function store(Request $request)
    {
        $data = $request->validate([
            'SoPhong' => 'required|unique:Phong,SoPhong',
            'MaLoaiPhong' => [
                'required',
                Rule::exists('LoaiPhong', 'MaLoaiPhong'),
            ],
            'TinhTrang' => 'required|integer',
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
            'chiTietDatPhong' => function ($q) {
                $q->where('TrangThai', '!=', ChiTietDatPhong::CANCELLED);
            },
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
            'MaLoaiPhong' => [
                'required',
                Rule::exists('LoaiPhong', 'MaLoaiPhong'),
            ],
            'TinhTrang' => 'required|integer',
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

        $decision = $this->guard->canDelete($phong);
        if (!$decision['allowed']) {
            return $this->error($decision['message'], 409);
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

    // ✅ Load đầy đủ quan hệ (giống API loai-phong)
    $phongs = Phong::with([
            'loaiPhong' => function ($q) {
                $q->with(['khuyenMai', 'hinhs', 'tienNghis']);
            }
        ])
        ->select('MaPhong', 'SoPhong', 'TinhTrang', 'MaLoaiPhong')
        ->where('TinhTrang', '!=', 2)
        ->whereDoesntHave('chiTietDatPhong.datPhong', function ($q) use ($checkIn, $checkOut) {
            $q->whereIn('TinhTrang', [0, 1, 2])
                ->where('NgayNhanPhong', '<', $checkOut)
                ->where('NgayTraPhong', '>', $checkIn);
        })
        ->get();

    // ✅ Gom nhóm + trả về object đồng bộ với API loai-phong
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
            
            // 🔥 Tính giá (đồng bộ với API loai-phong)
            $giaPhong = (float) $loaiPhong->GiaPhong;
            $khuyenMai = $loaiPhong->khuyenMai;
            
            // Tính giá sau giảm nếu có khuyến mãi hợp lệ
            $giaGiam = null;
            if ($khuyenMai && $this->isKhuyenMaiHopLe($khuyenMai)) {
                $giaGiam = $giaPhong * (1 - $khuyenMai->PhanTramGiamGia / 100);
            }

            return [
                // 🔥 Giống hệt API loai-phong
                'MaLoaiPhong' => $loaiPhong->MaLoaiPhong,
                'TenLoaiPhong' => $loaiPhong->TenLoaiPhong,
                'Mota' => $loaiPhong->Mota ?? '',
                'NguoiLon' => (int) $loaiPhong->NguoiLon,
                'TreEm' => (int) $loaiPhong->TreEm,
                'GiaPhong' => $giaPhong,
                'GiaGiam' => $giaGiam ?? $giaPhong, // Nếu ko có KM thì = giá gốc
                'MaKM' => $loaiPhong->MaKM,
                
                // 🔥 Khuyến mãi
                'khuyen_mai' => $khuyenMai ? [
                    'MaKM' => $khuyenMai->MaKM,
                    'TenKM' => $khuyenMai->TenKM,
                    'MoTa' => $khuyenMai->MoTa,
                    'Diem' => $khuyenMai->Diem,
                    'NgayBatDau' => $khuyenMai->NgayBatDau,
                    'NgayKetThuc' => $khuyenMai->NgayKetThuc,
                    'PhanTramGiamGia' => $khuyenMai->PhanTramGiamGia,
                    'LoaiKM' => $khuyenMai->LoaiKM,
                ] : null,
                
                // 🔥 Số phòng
                'soPhongTrong' => $soPhongTrong,
                'tongPhong' => $rooms->count(),
                
                // 🔥 Ảnh
                'hinhs' => $loaiPhong->hinhs->map(fn($h) => [
                    'Id' => $h->Id,
                    'Url' => $h->Url,
                ])->values(),
                
                // 🔥 Tiện nghi
                'tien_nghis' => $loaiPhong->tienNghis->map(fn($tn) => [
                    'MaTienNghi' => $tn->MaTienNghi,
                    'TenTienNghi' => $tn->TenTienNghi,
                ])->values(),
                
                // 🔥 Danh sách phòng
                'phongs' => $rooms->map(fn($p) => [
                    'MaPhong' => $p->MaPhong,
                    'SoPhong' => $p->SoPhong,
                    'TinhTrang' => $p->TinhTrang,
                ])->values(),
            ];
        })
        ->values();

    return $this->success($ketQua, 'Tìm phòng thành công');
}

// 🔥 Helper: Kiểm tra khuyến mãi còn hạn
private function isKhuyenMaiHopLe($khuyenMai)
{
    if (!$khuyenMai) return false;
    
    $today = Carbon::today();
    $ngayBatDau = $khuyenMai->NgayBatDau ? Carbon::parse($khuyenMai->NgayBatDau) : null;
    $ngayKetThuc = $khuyenMai->NgayKetThuc ? Carbon::parse($khuyenMai->NgayKetThuc) : null;
    
    if ($ngayBatDau && $today->lt($ngayBatDau)) return false;
    if ($ngayKetThuc && $today->gt($ngayKetThuc)) return false;
    
    return true;
}
}
