<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ThanhToan;
use App\Models\HoaDon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ThanhToanController extends Controller
{
    // =========================
    // 🔹 GET ALL
    public function index()
    {
        return response()->json(
            ThanhToan::with('hoaDon')->latest()->get()
        );
    }

    // =========================
    // 🔹 GET ONE
    public function show($id)
    {
        $data = ThanhToan::with('hoaDon')->find($id);

        if (!$data) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }

        return response()->json($data);
    }

    // =========================
    // 🔹 CREATE (THANH TOÁN)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MaHD' => 'required|exists:HoaDon,MaHD',
            'SoTien' => 'required|numeric|min:1',
            'PhuongThuc' => 'required|in:1,2',
            'LoaiThanhToan' => 'required|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $hoaDon = HoaDon::with('thanhToans', 'datPhong')
                ->find($request->MaHD);

            if (!$hoaDon) {
                return response()->json(['message' => 'Không tìm thấy hóa đơn'], 404);
            }

            // ❌ đã thanh toán xong
            if ($hoaDon->TrangThai == 1) {
                return response()->json([
                    'message' => 'Hóa đơn đã thanh toán xong'
                ], 400);
            }

            // ❌ chưa checkout mà thanh toán cuối
            if (
                $request->LoaiThanhToan == 1 &&
                $hoaDon->datPhong->TinhTrang != 2
            ) {
                return response()->json([
                    'message' => 'Chưa check-out, không thể thanh toán cuối'
                ], 400);
            }

            // 🔥 tổng đã thanh toán
            $daThanhToan = $hoaDon->thanhToans->sum('SoTien');

            // 🔥 số còn nợ
            $conNo = $hoaDon->TongTien - $daThanhToan;

            // ❌ không cho thanh toán dư
            if ($request->SoTien > $conNo) {
                return response()->json([
                    'message' => 'Số tiền vượt quá số nợ',
                    'conNo' => $conNo
                ], 400);
            }

            // 🔥 tạo thanh toán
            $thanhToan = ThanhToan::create([
                'MaHD' => $request->MaHD,
                'SoTien' => $request->SoTien,
                'PhuongThuc' => $request->PhuongThuc,
                'LoaiThanhToan' => $request->LoaiThanhToan
            ]);

            // 🔥 cập nhật lại tổng thanh toán
            $daThanhToanMoi = $daThanhToan + $request->SoTien;

            // 🔥 nếu thanh toán checkout và đủ tiền → DONE
            if (
                $request->LoaiThanhToan == 1 &&
                $daThanhToanMoi >= $hoaDon->TongTien
            ) {
                $hoaDon->update(['TrangThai' => 1]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Thanh toán thành công',
                'data' => $thanhToan,
                'TongTien' => $hoaDon->TongTien,
                'DaThanhToan' => $daThanhToanMoi,
                'ConNo' => $hoaDon->TongTien - $daThanhToanMoi
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Lỗi server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // =========================
    // 🔹 GET BY HOÁ ĐƠN
    public function getByHoaDon($maHD)
    {
        return response()->json(
            ThanhToan::where('MaHD', $maHD)->get()
        );
    }
}