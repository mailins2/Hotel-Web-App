<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DatPhong;
use App\Models\HoaDon;
use App\Models\ThanhToan;
use App\Services\CustomerPointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ThanhToanController extends Controller
{
    public function index()
    {
        return response()->json(
            ThanhToan::with('hoaDon')->latest('MaTT')->get()
        );
    }

    public function show($id)
    {
        $thanhToan = ThanhToan::with('hoaDon')->find($id);

        if (!$thanhToan) {
            return response()->json(['message' => 'Không tìm thấy thanh toán'], 404);
        }

        return response()->json($thanhToan);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MaHD' => 'required|exists:HoaDon,MaHD',
            'SoTien' => 'required|numeric|min:1',
            'PhuongThuc' => 'required|in:1,2',
            'LoaiThanhToan' => 'required|in:0,1',
            'NhaCungCap' => 'sometimes|string|max:30',
            'DinhDanhNguoiThanhToan' => 'nullable|string|max:120',
            'MaGiaoDich' => 'nullable|string|max:60|unique:ThanhToan,MaGiaoDich',
            'MaGiaoDichCongThanhToan' => 'nullable|string|max:80',
            'TrangThaiGiaoDich' => 'sometimes|integer|in:0,1,2',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $hoaDon = HoaDon::with('datPhong')
                ->lockForUpdate()
                ->find($request->MaHD);

            if (!$hoaDon) {
                DB::rollBack();
                return response()->json(['message' => 'Không tìm thấy hóa đơn'], 404);
            }

            if ((int) $hoaDon->TrangThai === 1) {
                DB::rollBack();
                return response()->json(['message' => 'Hóa đơn đã thanh toán xong'], 400);
            }

            if (
                (int) $request->LoaiThanhToan === 1 &&
                (!$hoaDon->datPhong || (int) $hoaDon->datPhong->TinhTrang !== DatPhong::CHECKED_OUT)
            ) {
                DB::rollBack();
                return response()->json(['message' => 'Chưa check-out, không thể thanh toán cuối'], 400);
            }

            $daThanhToan = max(
                (float) $hoaDon->DaThanhToan,
                (float) ThanhToan::where('MaHD', $hoaDon->MaHD)->sum('SoTien')
            );
            $conNo = max((float) $hoaDon->TongTien - $daThanhToan, 0);

            if ((float) $request->SoTien > $conNo) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Số tiền vượt quá số nợ',
                    'conNo' => $conNo,
                ], 400);
            }

            $thanhToan = ThanhToan::create([
                'MaHD' => $hoaDon->MaHD,
                'SoTien' => $request->SoTien,
                'PhuongThuc' => $request->PhuongThuc,
                'LoaiThanhToan' => $request->LoaiThanhToan,
                'NhaCungCap' => $request->input('NhaCungCap', 'manual'),
                'DinhDanhNguoiThanhToan' => $request->input('DinhDanhNguoiThanhToan'),
                'MaGiaoDich' => $request->input('MaGiaoDich'),
                'MaGiaoDichCongThanhToan' => $request->input('MaGiaoDichCongThanhToan'),
                'TrangThaiGiaoDich' => $request->input('TrangThaiGiaoDich', 1),
            ]);

            $daThanhToanMoi = min(
                (float) $hoaDon->TongTien,
                $daThanhToan + (float) $request->SoTien
            );

            $hoaDon->update([
                'DaThanhToan' => $daThanhToanMoi,
                'TrangThai' => $daThanhToanMoi >= (float) $hoaDon->TongTien ? 1 : 0,
            ]);

            $diemCong = app(CustomerPointService::class)
                ->addPointsForPayment($hoaDon, (float) $request->SoTien);

            DB::commit();

            return response()->json([
                'message' => 'Thanh toán thành công',
                'data' => $thanhToan->fresh('hoaDon'),
                'TongTien' => $hoaDon->TongTien,
                'DaThanhToan' => $daThanhToanMoi,
                'ConNo' => max((float) $hoaDon->TongTien - $daThanhToanMoi, 0),
                'DiemCong' => $diemCong,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Lỗi server',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getByHoaDon($maHD)
    {
        if (!HoaDon::where('MaHD', $maHD)->exists()) {
            return response()->json(['message' => 'Không tìm thấy hóa đơn'], 404);
        }

        return response()->json(
            ThanhToan::where('MaHD', $maHD)->latest('MaTT')->get()
        );
    }
}
