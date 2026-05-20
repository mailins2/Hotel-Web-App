<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChiTietHoaDon;
use App\Models\ChiTietDatPhong;
use App\Models\DichVu;
use App\Models\HoaDon;
use App\Models\SuDungDichVu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SuDungDichVuController extends Controller
{
    private function success($data = null, $message = 'Success', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    private function error($message = 'Error', $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }

    public function index()
    {
        $data = SuDungDichVu::with([
            'dichVu',
            'chiTietDatPhong.phong',
            'chiTietDatPhong.datPhong.khachHang',
        ])->get();

        return $this->success($data, 'Danh sach su dung dich vu');
    }

    public function show($id)
    {
        $item = SuDungDichVu::with([
            'dichVu',
            'chiTietDatPhong.phong',
            'chiTietDatPhong.datPhong.khachHang',
        ])->find($id);

        if (!$item) {
            return $this->error('Khong tim thay', 404);
        }

        return $this->success($item, 'Chi tiet');
    }

    public function byDatPhong($id)
    {
        $data = SuDungDichVu::with([
            'dichVu',
            'chiTietDatPhong.phong',
        ])
            ->whereHas('chiTietDatPhong', function ($query) use ($id) {
                $query->where('MaDatPhong', $id);
            })
            ->get();

        return $this->success($data, 'Dich vu theo don');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'MaCTDP' => 'required|exists:ChiTietDatPhong,MaCTDP',
            'ThoiGian' => 'nullable|date',
            'MaDV' => [
                'required_without:items',
                Rule::exists('DichVu', 'MaDV'),
            ],
            'SoLuong' => 'required_without:items|integer|min:1',
            'items' => 'sometimes|array|min:1',
            'items.*.MaDV' => [
                'required',
                Rule::exists('DichVu', 'MaDV'),
            ],
            'items.*.SoLuong' => 'required|integer|min:1',
        ]);

        $items = collect($data['items'] ?? [[
            'MaDV' => $data['MaDV'],
            'SoLuong' => $data['SoLuong'],
        ]])->map(fn ($item) => [
            'MaDV' => (int) $item['MaDV'],
            'SoLuong' => (int) $item['SoLuong'],
        ])->values();

        if ($items->isEmpty()) {
            return $this->error('Vui long chon dich vu', 422);
        }

        try {
            $result = DB::transaction(function () use ($data, $items) {
                $chiTietDatPhong = ChiTietDatPhong::with([
                    'datPhong' => function ($query) {
                        $query->select('MaDatPhong', 'NgayNhanPhong', 'NgayTraPhong', 'TinhTrang');
                    },
                    'phong',
                ])->find($data['MaCTDP']);

                $datPhong = $chiTietDatPhong?->datPhong;

                if (!$chiTietDatPhong || !$datPhong || (int) $chiTietDatPhong->TrangThai !== ChiTietDatPhong::CHECKED_IN) {
                    throw new \RuntimeException('Chi co the dat dich vu cho phong da check-in.');
                }

                $serviceDate = isset($data['ThoiGian'])
                    ? \Illuminate\Support\Carbon::parse($data['ThoiGian'])->toDateString()
                    : now()->toDateString();
                $minDate = max(
                    \Illuminate\Support\Carbon::parse($datPhong->NgayNhanPhong)->toDateString(),
                    now()->toDateString()
                );
                $maxDate = \Illuminate\Support\Carbon::parse($datPhong->NgayTraPhong)->toDateString();

                if ($serviceDate < $minDate || $serviceDate > $maxDate) {
                    throw new \RuntimeException('Ngay su dung phai nam trong thoi gian luu tru.');
                }

                $hoaDon = HoaDon::where('MaDatPhong', $datPhong->MaDatPhong)
                    ->lockForUpdate()
                    ->first(['MaHD', 'MaDatPhong', 'MaKM', 'TongTien', 'DaThanhToan', 'TrangThai']);

                if (!$hoaDon) {
                    throw new \RuntimeException('Khong tim thay hoa don cua dat phong.');
                }

                $createdServiceIds = [];
                $createdDetailIds = [];
                $addedSubtotal = 0;
                $thoiGian = isset($data['ThoiGian'])
                    ? \Illuminate\Support\Carbon::parse($data['ThoiGian'])->toDateTimeString()
                    : now();
                $services = DichVu::whereIn('MaDV', $items->pluck('MaDV')->unique()->values())
                    ->get(['MaDV', 'TenDV', 'GiaDV'])
                    ->keyBy('MaDV');

                foreach ($items as $item) {
                    $dichVu = $services->get($item['MaDV']);

                    if (!$dichVu) {
                        throw new \RuntimeException('Dich vu khong hop le.');
                    }

                    $suDung = SuDungDichVu::create([
                        'MaCTDP' => $chiTietDatPhong->MaCTDP,
                        'MaDV' => $dichVu->MaDV,
                        'SoLuong' => $item['SoLuong'],
                        'ThoiGian' => $thoiGian,
                    ]);

                    $chiTiet = ChiTietHoaDon::create([
                        'MaHD' => $hoaDon->MaHD,
                        'MaSuDung' => $suDung->MaSuDung,
                        'MoTa' => $dichVu->TenDV,
                        'SoLuong' => $item['SoLuong'],
                        'DonGia' => $dichVu->GiaDV,
                    ]);

                    $createdServiceIds[] = $suDung->MaSuDung;
                    $createdDetailIds[] = $chiTiet->MaCTHD;
                    $addedSubtotal += (float) $dichVu->GiaDV * (int) $item['SoLuong'];
                }

                $discountRate = 0;

                if ($hoaDon->MaKM) {
                    $discountRate = (float) DB::table('KhuyenMai')
                        ->where('MaKM', $hoaDon->MaKM)
                        ->value('PhanTramGiamGia');
                }

                $daThanhToan = (float) $hoaDon->DaThanhToan;
                $addedTotal = $addedSubtotal * (1 - ($discountRate / 100));
                $tongTien = (float) $hoaDon->TongTien + $addedTotal;

                $hoaDon->update([
                    'TongTien' => $tongTien,
                    'TrangThai' => $daThanhToan >= $tongTien ? 1 : 0,
                ]);

                return [
                    'suDungDichVuIds' => $createdServiceIds,
                    'chiTietHoaDonIds' => $createdDetailIds,
                    'MaHD' => $hoaDon->MaHD,
                    'TongTien' => $tongTien,
                    'DaThanhToan' => $daThanhToan,
                    'ConNo' => max($tongTien - $daThanhToan, 0),
                ];
            });
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\Throwable $e) {
            return $this->error('Khong the dat dich vu. Vui long thu lai.', 500);
        }

        return $this->success($result, 'Dat dich vu thanh cong', 201);
    }

    public function update(Request $request, $id)
    {
        $item = SuDungDichVu::find($id);

        if (!$item) {
            return $this->error('Khong tim thay', 404);
        }

        $data = $request->validate([
            'SoLuong' => 'required|integer|min:1',
        ]);

        $item->update($data);

        return $this->success($item, 'Cap nhat thanh cong');
    }

    public function destroy($id)
    {
        $item = SuDungDichVu::find($id);

        if (!$item) {
            return $this->error('Khong tim thay', 404);
        }

        $item->delete();

        return $this->success(null, 'Xoa thanh cong');
    }
}
