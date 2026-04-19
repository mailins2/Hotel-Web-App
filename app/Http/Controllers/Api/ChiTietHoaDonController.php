<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChiTietHoaDon;
use App\Models\HoaDon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChiTietHoaDonController extends Controller
{
    // =========================
    // 🔹 1. GET ALL (admin)
    public function index()
    {
        $data = ChiTietHoaDon::with([
            'hoaDon.datPhong',
            'loaiPhong',
            'suDung.dichVu',
            'denBu'
        ])->get();

        return response()->json($data);
    }

    // =========================
    // 🔹 2. GET ONE
    public function show($id)
    {
        $data = ChiTietHoaDon::with([
            'hoaDon.datPhong',
            'loaiPhong',
            'suDung.dichVu',
            'denBu'
        ])->find($id);

        if (!$data) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }

        return response()->json($data);
    }

    // =========================
    // 🔹 3. UPDATE (chỉ DV / đền bù)
    public function update(Request $request, $id)
    {
        $chiTiet = ChiTietHoaDon::with('hoaDon.datPhong', 'suDung')->find($id);

        if (!$chiTiet) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }

        // ❌ không cho sửa nếu đã thanh toán
        if ($chiTiet->hoaDon->TrangThai == 1) {
            return response()->json([
                'message' => 'Hóa đơn đã thanh toán, không thể chỉnh sửa'
            ], 400);
        }

        // ❌ không cho sửa nếu đã checkout
        if ($chiTiet->hoaDon->datPhong->TinhTrang == 2) {
            return response()->json([
                'message' => 'Đã check-out, không thể chỉnh sửa'
            ], 400);
        }

        // ❌ không cho sửa phòng
        if ($chiTiet->MaLoaiPhong) {
            return response()->json([
                'message' => 'Không được sửa chi tiết phòng'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'SoLuong' => 'sometimes|integer|min:1',
            'MoTa' => 'nullable|string|max:200'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // 🔥 update chi tiết
            $chiTiet->update($request->only(['SoLuong', 'MoTa']));

            // 🔥 sync dịch vụ gốc
            if ($chiTiet->MaSuDung && $chiTiet->suDung) {
                $chiTiet->suDung->update([
                    'SoLuong' => $chiTiet->SoLuong
                ]);
            }

            // 🔥 update tổng tiền
            $this->updateTongTien($chiTiet->MaHD);

            DB::commit();

            return response()->json([
                'message' => 'Cập nhật thành công',
                'data' => $chiTiet
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Lỗi server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // =========================
    // 🔹 4. DELETE (optional)
    public function destroy($id)
    {
        $chiTiet = ChiTietHoaDon::with('hoaDon.datPhong')->find($id);

        if (!$chiTiet) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }

        // ❌ không cho xóa nếu đã thanh toán
        if ($chiTiet->hoaDon->TrangThai == 1) {
            return response()->json([
                'message' => 'Hóa đơn đã thanh toán, không thể xóa'
            ], 400);
        }

        // ❌ không cho xóa nếu đã checkout
        if ($chiTiet->hoaDon->datPhong->TinhTrang == 2) {
            return response()->json([
                'message' => 'Đã check-out, không thể xóa'
            ], 400);
        }

        // ❌ không cho xóa phòng
        if ($chiTiet->MaLoaiPhong) {
            return response()->json([
                'message' => 'Không được xóa chi tiết phòng'
            ], 400);
        }

        DB::beginTransaction();

        try {
            $maHD = $chiTiet->MaHD;

            $chiTiet->delete();

            // 🔥 update tổng tiền
            $this->updateTongTien($maHD);

            DB::commit();

            return response()->json([
                'message' => 'Đã xóa'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Lỗi server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // =========================
    // 🔥 PRIVATE: TÍNH TỔNG TIỀN
    private function updateTongTien($maHD)
    {
        $hoaDon = HoaDon::with('khuyenMai')->find($maHD);

        if (!$hoaDon) return;

        $tong = ChiTietHoaDon::where('MaHD', $maHD)
            ->sum(DB::raw('SoLuong * DonGia'));

        // 👉 áp dụng khuyến mãi
        if ($hoaDon->MaKM && $hoaDon->khuyenMai) {
            $tong -= ($tong * $hoaDon->khuyenMai->PhanTramGiamGia / 100);
        }

        $hoaDon->update(['TongTien' => $tong]);
    }
}