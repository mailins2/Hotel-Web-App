<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChiTietHoaDon;
use App\Models\DatPhong;
use App\Models\DenBuHuHong;
use App\Models\HoaDon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DenBuHuHongController extends Controller
{
    public function index()
    {
        $denBus = DenBuHuHong::with('datPhong')->get();

        return response()->json($denBus, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MaDatPhong' => 'required|exists:DatPhong,MaDatPhong',
            'MoTa' => 'nullable|string|max:200',
            'TienDenBu' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $result = DB::transaction(function () use ($request) {
                $datPhong = DatPhong::find($request->MaDatPhong);

                if (!$datPhong || (int) $datPhong->TinhTrang === DatPhong::CHECKED_OUT) {
                    throw new \RuntimeException('Chỉ có thể thêm đền bù trước khi check-out.');
                }

                $hoaDon = HoaDon::where('MaDatPhong', $request->MaDatPhong)
                    ->lockForUpdate()
                    ->first();

                if (!$hoaDon) {
                    throw new \RuntimeException('Không tìm thấy hóa đơn của đặt phòng.');
                }

                $denBu = DenBuHuHong::updateOrCreate(
                    ['MaDatPhong' => $request->MaDatPhong],
                    [
                        'MoTa' => $request->MoTa,
                        'TienDenBu' => $request->TienDenBu,
                    ]
                );

                $chiTiet = ChiTietHoaDon::updateOrCreate(
                    [
                        'MaHD' => $hoaDon->MaHD,
                        'MaDenBu' => $denBu->MaDenBu,
                    ],
                    [
                        'MoTa' => $denBu->MoTa,
                        'SoLuong' => 1,
                        'DonGia' => $denBu->TienDenBu,
                    ]
                );

                $this->recalculateInvoiceTotal($hoaDon);

                return [
                    'denBu' => $denBu->fresh(),
                    'chiTietHoaDon' => $chiTiet->fresh(),
                    'hoaDon' => $hoaDon->fresh(['chiTietHoaDons.denBu', 'thanhToans']),
                ];
            });
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã ghi nhận khoản đền bù hư hỏng',
            'data' => $result,
        ], 201);
    }

    public function show($id)
    {
        $denBu = DenBuHuHong::with(['datPhong', 'chiTietHoaDon'])->find($id);

        if (!$denBu) {
            return response()->json(['message' => 'Không tìm thấy thông tin đền bù'], 404);
        }

        return response()->json($denBu, 200);
    }

    public function update(Request $request, $id)
    {
        $denBu = DenBuHuHong::with('chiTietHoaDon.hoaDon')->find($id);
        if (!$denBu) {
            return response()->json(['message' => 'Không tìm thấy thông tin'], 404);
        }

        $validator = Validator::make($request->all(), [
            'MoTa' => 'sometimes|nullable|string|max:200',
            'TienDenBu' => 'sometimes|numeric|min:0',
            'MaDatPhong' => 'sometimes|exists:DatPhong,MaDatPhong|unique:DenBuHuHong,MaDatPhong,' . $id . ',MaDenBu',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $denBu->update($request->only(['MaDatPhong', 'MoTa', 'TienDenBu']));

        if ($denBu->chiTietHoaDon) {
            $denBu->chiTietHoaDon->update([
                'MoTa' => $denBu->MoTa,
                'DonGia' => $denBu->TienDenBu,
                'SoLuong' => 1,
            ]);

            if ($denBu->chiTietHoaDon->hoaDon) {
                $this->recalculateInvoiceTotal($denBu->chiTietHoaDon->hoaDon);
            }
        }

        return response()->json(['success' => true, 'message' => 'Cập nhật thành công', 'data' => $denBu], 200);
    }

    public function destroy($id)
    {
        $denBu = DenBuHuHong::with('chiTietHoaDon.hoaDon')->find($id);
        if (!$denBu) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }

        $hoaDon = $denBu->chiTietHoaDon?->hoaDon;
        $denBu->chiTietHoaDon?->delete();
        $denBu->delete();

        if ($hoaDon) {
            $this->recalculateInvoiceTotal($hoaDon);
        }

        return response()->json(['success' => true, 'message' => 'Đã xóa khoản đền bù'], 200);
    }

    private function recalculateInvoiceTotal(HoaDon $hoaDon): void
    {
        $tong = ChiTietHoaDon::where('MaHD', $hoaDon->MaHD)
            ->sum(DB::raw('SoLuong * DonGia'));

        if ($hoaDon->MaKM && $hoaDon->khuyenMai) {
            $tong -= ($tong * $hoaDon->khuyenMai->PhanTramGiamGia / 100);
        }

        $daThanhToan = (float) $hoaDon->DaThanhToan;

        $hoaDon->update([
            'TongTien' => $tong,
            'TrangThai' => $daThanhToan >= $tong ? 1 : 0,
        ]);
    }
}
