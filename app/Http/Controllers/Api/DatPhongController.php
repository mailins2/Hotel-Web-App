<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DatPhong;
use App\Models\ChiTietDatPhong;
use App\Models\Phong;
use App\Models\HoaDon;
use App\Models\ChiTietHoaDon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatPhongController extends Controller
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

    // =========================
    // 🔹 GET ALL
    public function index()
    {
        return $this->success(
            DatPhong::with('chiTietDatPhong.phong')->get()
        );
    }

    // =========================
    // 🔹 CREATE (AUTO ASSIGN + TẠO HÓA ĐƠN)
    public function store(Request $request)
    {
        $data = $request->validate([
            'MaKH' => 'required|exists:KhachHang,MaKH',
            'NgayNhanPhong' => 'required|date',
            'NgayTraPhong' => 'required|date|after:NgayNhanPhong',
            'MaLoaiPhong' => 'required|exists:LoaiPhong,MaLoaiPhong',
            'SoLuong' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();

        try {
            // 🔥 tìm phòng trống
            $phongTrong = Phong::where('MaLoaiPhong', $data['MaLoaiPhong'])
                ->whereNotIn('MaPhong', function ($query) use ($data) {
                    $query->select('MaPhong')
                        ->from('ChiTietDatPhong')
                        ->join('DatPhong', 'DatPhong.MaDatPhong', '=', 'ChiTietDatPhong.MaDatPhong')
                        ->where('NgayNhanPhong', '<', $data['NgayTraPhong'])
                        ->where('NgayTraPhong', '>', $data['NgayNhanPhong']);
                })
                ->limit($data['SoLuong'])
                ->get();

            if ($phongTrong->count() < $data['SoLuong']) {
                DB::rollBack();
                return $this->error('Không đủ phòng trống');
            }

            // 🔥 tạo đặt phòng
            $datPhong = DatPhong::create([
                'MaKH' => $data['MaKH'],
                'NgayDat' => now(),
                'NgayNhanPhong' => $data['NgayNhanPhong'],
                'NgayTraPhong' => $data['NgayTraPhong'],
                'SoLuong' => $data['SoLuong'],
                'TinhTrang' => 0
            ]);

            // 🔥 gán phòng
            foreach ($phongTrong as $p) {
                ChiTietDatPhong::create([
                    'MaDatPhong' => $datPhong->MaDatPhong,
                    'MaPhong' => $p->MaPhong
                ]);
            }

            // 🔥 TẠO HÓA ĐƠN NGAY
            $hoaDon = HoaDon::create([
                'MaDatPhong' => $datPhong->MaDatPhong,
                'NgayLapHD' => now(),
                'MaNV' => null,
                'TrangThai' => 0,
                'TongTien' => 0,
                'DaThanhToan' => 0
            ]);

            DB::commit();

            return $this->success([
                'datPhong' => $datPhong->load('chiTietDatPhong.phong'),
                'hoaDon' => $hoaDon
            ], 'Đặt phòng thành công', 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }

    // =========================
    // 🔹 SHOW
    public function show($id)
    {
        $data = DatPhong::with('chiTietDatPhong.phong')->find($id);

        if (!$data) {
            return $this->error('Không tìm thấy', 404);
        }

        return $this->success($data);
    }

    // =========================
    // 🔹 CHECK-IN
    public function checkIn($id)
    {
        $datPhong = DatPhong::find($id);

        if (!$datPhong) {
            return $this->error('Không tìm thấy', 404);
        }

        if ($datPhong->TinhTrang != 0) {
            return $this->error('Không hợp lệ');
        }

        $datPhong->update(['TinhTrang' => 1]);

        return $this->success($datPhong, 'Check-in thành công');
    }

    // =========================
    // 🔹 CHECK-OUT (🔥 CHỐT TIỀN)
    public function checkOut(Request $request, $id)
{
    // 🔥 validate nhân viên
    $request->validate([
        'MaNV' => 'required|exists:NhanVien,MaNV'
    ]);

    $datPhong = DatPhong::with('chiTietDatPhong.phong')->find($id);

    if (!$datPhong) {
        return $this->error('Không tìm thấy', 404);
    }

    // ❌ chưa check-in
    if ($datPhong->TinhTrang == 0) {
        return $this->error('Chưa check-in');
    }

    DB::beginTransaction();

    try {
        $hoaDon = HoaDon::with('khuyenMai', 'chiTietHoaDons')
            ->where('MaDatPhong', $id)
            ->first();

        if (!$hoaDon) {
            return $this->error('Không tìm thấy hóa đơn');
        }

        // ❌ đã checkout rồi
        if ($datPhong->TinhTrang == 2) {
            return $this->error('Đã check-out');
        }

        // ❌ đã thanh toán xong
        if ($hoaDon->TrangThai == 1) {
            return $this->error('Hóa đơn đã thanh toán');
        }

        // 🔥 gán nhân viên checkout
        $hoaDon->update([
            'MaNV' => $request->MaNV
        ]);
        $bangGia = BangGia::where('MaLoaiPhong', $ct->phong->MaLoaiPhong)
            ->where('Mua', 1) // hoặc logic mùa
            ->first();

        $donGia = $bangGia ? $bangGia->Gia : 0;

        // ❗ tránh add phòng nhiều lần
        if (
            !$hoaDon->chiTietHoaDons()
                ->whereNotNull('MaLoaiPhong')
                ->exists()
        ) {
            foreach ($datPhong->chiTietDatPhong as $ct) {
               ChiTietHoaDon::create([
                    'MaHD' => $hoaDon->MaHD,
                    'MaLoaiPhong' => $ct->phong->MaLoaiPhong,
                    'SoLuong' => $soNgay,
                    'DonGia' => $donGia
                ]);
            }
        }

        // 🔥 tính tổng tiền
        $tong = ChiTietHoaDon::where('MaHD', $hoaDon->MaHD)
            ->sum(DB::raw('SoLuong * DonGia'));

        // 🔥 áp dụng khuyến mãi
        if ($hoaDon->MaKM && $hoaDon->khuyenMai) {
            $tong -= ($tong * $hoaDon->khuyenMai->PhanTramGiamGia / 100);
        }

        $hoaDon->update([
            'TongTien' => $tong
        ]);

        // 🔥 update trạng thái đặt phòng
        $datPhong->update(['TinhTrang' => 2]);

        DB::commit();

        return $this->success([
            'datPhong' => $datPhong,
            'hoaDon' => $hoaDon->load('chiTietHoaDons')
        ], 'Check-out thành công');

    } catch (\Exception $e) {
        DB::rollBack();
        return $this->error($e->getMessage(), 500);
    }
}

    // =========================
    // 🔹 CHANGE ROOM
    public function changeRoom(Request $request, $id)
    {
        $request->validate([
            'oldPhong' => 'required|exists:Phong,MaPhong',
            'newPhong' => 'required|exists:Phong,MaPhong'
        ]);

        $datPhong = DatPhong::find($id);

        if (!$datPhong) {
            return $this->error('Không tìm thấy', 404);
        }

        if ($datPhong->TinhTrang != 1) {
            return $this->error('Chỉ được đổi khi đang ở');
        }

        $ct = ChiTietDatPhong::where('MaDatPhong', $id)
            ->where('MaPhong', $request->oldPhong)
            ->first();

        if (!$ct) {
            return $this->error('Không tìm thấy phòng cũ');
        }

        $ct->update(['MaPhong' => $request->newPhong]);

        return $this->success($ct, 'Đổi phòng thành công');
    }

    // =========================
    // 🔹 ADD ROOM
    public function addRoom(Request $request, $id)
    {
        $request->validate([
            'MaPhong' => 'required|exists:Phong,MaPhong'
        ]);

        $datPhong = DatPhong::find($id);

        if (!$datPhong) {
            return $this->error('Không tìm thấy', 404);
        }

        if ($datPhong->TinhTrang != 1) {
            return $this->error('Chỉ được thêm khi đang ở');
        }

        $ct = ChiTietDatPhong::create([
            'MaDatPhong' => $id,
            'MaPhong' => $request->MaPhong
        ]);

        $datPhong->increment('SoLuong');

        return $this->success($ct, 'Thêm phòng thành công');
    }

    // =========================
    // 🔹 REMOVE ROOM
    public function removeRoom($id, $maPhong)
    {
        $datPhong = DatPhong::find($id);

        if (!$datPhong) {
            return $this->error('Không tìm thấy', 404);
        }

        if ($datPhong->TinhTrang != 1) {
            return $this->error('Chỉ được xóa khi đang ở');
        }

        $ct = ChiTietDatPhong::where('MaDatPhong', $id)
            ->where('MaPhong', $maPhong)
            ->first();

        if (!$ct) {
            return $this->error('Không tìm thấy phòng');
        }

        $ct->delete();

        $datPhong->decrement('SoLuong');

        return $this->success(null, 'Đã xóa phòng');
    }
}