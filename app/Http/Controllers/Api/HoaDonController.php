<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HoaDon;
use App\Models\DatPhong;
use App\Models\ChiTietHoaDon;
use App\Models\BangGia;
use App\Models\SuDungDichVu;
use App\Models\DichVu;
use App\Models\DenBuHuHong;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class HoaDonController extends Controller
{
    // =========================
    // 🔹 1. GET ALL
    public function index()
    {
        $data = HoaDon::with(['datPhong', 'nhanVien', 'khuyenMai'])
            ->latest('MaHD')
            ->get();

        return response()->json($data);
    }

    // =========================
    // 🔹 2. CREATE (CHECKOUT)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MaDatPhong' => 'required|exists:DatPhong,MaDatPhong|unique:HoaDon,MaDatPhong',
            'MaKM' => 'nullable|exists:KhuyenMai,MaKM',
            'MaNV' => 'required|exists:NhanVien,MaNV'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // 🔥 tạo hóa đơn
            $hoaDon = HoaDon::create([
                'MaDatPhong' => $request->MaDatPhong,
                'NgayLapHD' => now(),
                'MaKM' => $request->MaKM,
                'MaNV' => $request->MaNV,
                'TrangThai' => 0,
                'TongTien' => 0
            ]);

            // 🔥 lấy đặt phòng
            $datPhong = DatPhong::with('chiTietDatPhong.phong')
                ->find($request->MaDatPhong);

            // =====================
            // 🏨 PHÒNG
            // =====================
            foreach ($datPhong->chiTietDatPhong as $ct) {

                $bangGia = BangGia::where('MaLoaiPhong', $ct->phong->MaLoaiPhong)->first();

                if (!$bangGia) {
                    throw new \Exception('Không tìm thấy giá phòng');
                }

                ChiTietHoaDon::create([
                    'MaHD' => $hoaDon->MaHD,
                    'MaLoaiPhong' => $ct->phong->MaLoaiPhong,
                    'SoLuong' => 1,
                    'DonGia' => $bangGia->GiaPhong
                ]);
            }

            // =====================
            // 🍹 DỊCH VỤ
            // =====================
            $dichVus = SuDungDichVu::where('MaDatPhong', $request->MaDatPhong)->get();

            foreach ($dichVus as $dv) {
                $dichVu = DichVu::find($dv->MaDV);

                if (!$dichVu) continue;

                ChiTietHoaDon::create([
                    'MaHD' => $hoaDon->MaHD,
                    'MaSuDung' => $dv->MaSuDung,
                    'SoLuong' => $dv->SoLuong,
                    'DonGia' => $dichVu->GiaDV
                ]);
            }

            // =====================
            // 💥 ĐỀN BÙ
            // =====================
            $denBus = DenBuHuHong::where('MaDatPhong', $request->MaDatPhong)->get();

            foreach ($denBus as $db) {
                ChiTietHoaDon::create([
                    'MaHD' => $hoaDon->MaHD,
                    'MaDenBu' => $db->MaDenBu,
                    'SoLuong' => 1,
                    'DonGia' => $db->TienDenBu
                ]);
            }

            // =====================
            // 💰 TÍNH TỔNG
            // =====================
            $tong = ChiTietHoaDon::where('MaHD', $hoaDon->MaHD)
                ->sum(DB::raw('SoLuong * DonGia'));

            // 👉 áp dụng khuyến mãi
            if ($hoaDon->MaKM) {
                $km = $hoaDon->khuyenMai;
                if ($km) {
                    $tong -= ($tong * $km->PhanTramGiamGia / 100);
                }
            }

            $hoaDon->update(['TongTien' => $tong]);

            DB::commit();

            return response()->json([
                'message' => 'Tạo hóa đơn thành công',
                'data' => $hoaDon
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
    // 🔹 3. SHOW DETAIL
    public function show($id)
    {
        $hoaDon = HoaDon::with([
            'datPhong',
            'nhanVien',
            'khuyenMai',
            'chiTietHoaDons.loaiPhong',
            'chiTietHoaDons.suDung.dichVu',
            'chiTietHoaDons.denBu',
            'thanhToans'
        ])->find($id);

        if (!$hoaDon) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }

        // 🔥 tính tiền realtime
        $daThanhToan = $hoaDon->thanhToans->sum('SoTien');
        $conNo = $hoaDon->TongTien - $daThanhToan;

        return response()->json([
            'hoaDon' => $hoaDon,
            'TongTien' => $hoaDon->TongTien,
            'DaThanhToan' => $daThanhToan,
            'ConNo' => $conNo
        ]);
    }

    // =========================
    // 🔹 4. DELETE
    public function destroy($id)
    {
        $hoaDon = HoaDon::find($id);

        if (!$hoaDon) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }

        $hoaDon->delete();

        return response()->json(['message' => 'Đã xóa']);
    }
    //
    // =========================
    // 5. UPDATE (áp dụng khuyến mãi / trạng thái)
    public function update(Request $request, $id)
    {
        $hoaDon = HoaDon::with('khuyenMai')->find($id);

        if (!$hoaDon) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }

        // ❌ không cho sửa nếu đã thanh toán xong
        if ($hoaDon->TrangThai == 1) {
            return response()->json([
                'message' => 'Hóa đơn đã thanh toán, không thể chỉnh sửa'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'MaKM' => 'nullable|exists:KhuyenMai,MaKM',
            'TrangThai' => 'sometimes|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // 👉 update MaKM nếu có
            if ($request->has('MaKM')) {
                $hoaDon->MaKM = $request->MaKM;
            }

            // 👉 update trạng thái nếu có
            if ($request->has('TrangThai')) {
                $hoaDon->TrangThai = $request->TrangThai;
            }

            // =====================
            // 🔥 TÍNH LẠI TỔNG TIỀN
            // =====================
            $tong = ChiTietHoaDon::where('MaHD', $hoaDon->MaHD)
                ->sum(DB::raw('SoLuong * DonGia'));

            // 👉 áp dụng khuyến mãi
            if ($hoaDon->MaKM) {
                $km = \App\Models\KhuyenMai::find($hoaDon->MaKM);
                if ($km) {
                    $tong -= ($tong * $km->PhanTramGiamGia / 100);
                }
            }

            $hoaDon->TongTien = $tong;
            $hoaDon->save();

            DB::commit();

            return response()->json([
                'message' => 'Cập nhật hóa đơn thành công',
                'data' => $hoaDon
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Lỗi server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}