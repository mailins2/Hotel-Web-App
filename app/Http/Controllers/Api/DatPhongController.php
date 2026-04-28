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
use Carbon\Carbon;
use App\Models\BangGia;

class DatPhongController extends Controller
{
    // =========================
    // 🔹 HELPER FUNCTIONS
    // =========================
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
    // =========================
    public function index()
    {
        return $this->success(
            DatPhong::with('chiTietDatPhong.phong')->get()
        );
    }

    // =========================
    // 🔹 CREATE (ĐÃ FIX RACE CONDITION)
    // =========================
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        $maxAttempts = 3;
        
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            try {
                DB::beginTransaction();

                // 1. Tạo DatPhong trước
                $datPhong = $this->createDatPhong($data);
                
                // 2. Atomic assign rooms
                $assignedRoomIds = $this->atomicAssignRooms($data, $datPhong);
                
                // 3. Kiểm tra đủ phòng
                if (count($assignedRoomIds) < $data['SoLuong']) {
                    throw new \Exception('Không đủ phòng trống');
                }

                // 4. Tạo hóa đơn
                $hoaDon = $this->createHoaDon($datPhong);
                
                // 5. Load phòng và tính tiền
                $datPhong->load('chiTietDatPhong.phong');
                $this->addTienPhong($datPhong, $hoaDon);

                DB::commit();

                return $this->success([
                    'datPhong' => $datPhong->fresh('chiTietDatPhong.phong'),
                    'hoaDon' => $hoaDon->fresh('chiTietHoaDons.loaiPhong'),
                    'hold_expires_at' => now()->addMinutes(15)->format('Y-m-d H:i:s'), // THÊM
                    'hold_remaining_seconds' => 900 // THÊM
                ], 'Đặt phòng thành công . Vui lòng thanh toán đặt cọc trong vòng 15 phút !');

            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                
                if ($attempt < $maxAttempts - 1 && in_array($e->getCode(), ['40001', '23000', '1213'])) {
                    usleep(rand(100000, 500000));
                    continue;
                }
                
                return $this->error('Lỗi database: ' . $e->getMessage(), 500);
                
            } catch (\Exception $e) {
                DB::rollBack();
                
                if ($attempt < $maxAttempts - 1 && 
                    (str_contains($e->getMessage(), 'Không đủ phòng') || 
                     str_contains($e->getMessage(), 'vừa bị đặt'))) {
                    usleep(rand(100000, 300000));
                    continue;
                }
                
                return $this->error($e->getMessage(), 500);
            }
        }

        return $this->error('Không thể đặt phòng sau ' . $maxAttempts . ' lần thử. Vui lòng thử lại sau.');
    }

    // =========================
    // 🔹 ATOMIC ASSIGN ROOMS (FIX RACE CONDITION)
    // =========================
    private function atomicAssignRooms($data, $datPhong)
    {
        $assignedRooms = [];
        $maLoaiPhong = $data['MaLoaiPhong'];
        $ngayNhan = $data['NgayNhanPhong'];
        $ngayTra = $data['NgayTraPhong'];
        $soLuong = $data['SoLuong'];
        
        // Lấy phòng trống với FOR UPDATE
        $availableRooms = DB::table('Phong as p')
            ->where('p.MaLoaiPhong', $maLoaiPhong)
            ->whereNotExists(function ($query) use ($ngayNhan, $ngayTra) {
                $query->select(DB::raw(1))
                    ->from('ChiTietDatPhong as ctdp')
                    ->join('DatPhong as dp', 'dp.MaDatPhong', '=', 'ctdp.MaDatPhong')
                    ->whereColumn('ctdp.MaPhong', 'p.MaPhong')
                    ->where('dp.NgayNhanPhong', '<', $ngayTra)
                    ->where('dp.NgayTraPhong', '>', $ngayNhan)
                    ->where(function ($q) {
                        $q->whereIn('dp.TinhTrang', [1, 2])
                          ->orWhere(function ($q2) {
                              $q2->where('dp.TinhTrang', 0)
                                 ->where('dp.NgayDat', '>=', now()->subMinutes(15));
                          });
                    });
            })
            ->orderBy('p.MaPhong')
            ->limit($soLuong)
            ->lockForUpdate()
            ->get();
        
        if ($availableRooms->count() < $soLuong) {
            throw new \Exception('Không đủ phòng trống');
        }
        
        foreach ($availableRooms as $room) {
            try {
                ChiTietDatPhong::create([
                    'MaDatPhong' => $datPhong->MaDatPhong,
                    'MaPhong' => $room->MaPhong
                ]);
                $assignedRooms[] = $room->MaPhong;
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() == 23000) {
                    throw new \Exception('Phòng ' . $room->MaPhong . ' vừa bị đặt bởi người khác');
                }
                throw $e;
            }
        }
        
        return $assignedRooms;
    }

    // =========================
    // 🔹 VALIDATE REQUEST
    // =========================
    private function validateRequest($request)
    {
       return $request->validate([
            'MaKH' => 'required|exists:KhachHang,MaKH',
            'NgayNhanPhong' => [
                'required',
                'date',
                'after_or_equal:today',
                'before_or_equal:' . now()->addYear()->toDateString() 
            ],
            'NgayTraPhong' => 'required|date|after:NgayNhanPhong',
            'MaLoaiPhong' => 'required|exists:LoaiPhong,MaLoaiPhong',
            'SoLuong' => 'required|integer|min:1'
        ], [
            'NgayNhanPhong.after_or_equal' => 'Không thể đặt phòng trong quá khứ',
            'NgayNhanPhong.before_or_equal' => 'Chỉ được đặt tối đa 1 năm tới',
            'NgayTraPhong.after' => 'Ngày trả phải sau ngày nhận'
        ]);
    }

    // =========================
    // 🔹 CREATE DAT PHONG
    // =========================
    private function createDatPhong($data)
    {
        return DatPhong::create([
            'MaKH' => $data['MaKH'],
            'NgayDat' => now(),
            'NgayNhanPhong' => $data['NgayNhanPhong'],
            'NgayTraPhong' => $data['NgayTraPhong'],
            'SoLuong' => $data['SoLuong'],
            'TinhTrang' => 0
        ]);
    }

    // =========================
    // 🔹 CREATE HOA DON
    // =========================
    private function createHoaDon($datPhong)
    {
        return HoaDon::create([
            'MaDatPhong' => $datPhong->MaDatPhong,
            'NgayLapHD' => now(),
            'TongTien' => 0,
            'TrangThai' => 0
        ]);
    }

    // =========================
    // 🔹 GET MUA (SEASON)
    // =========================
    private function getMua($ngay)
    {
        $date = Carbon::parse($ngay);
        $thang = $date->month;

        $holidays = ['01-01', '04-30', '05-01', '09-02'];
        if (in_array($date->format('m-d'), $holidays)) {
            return 3;
        }

        if ($thang == 1 || $thang == 2) {
            return 3;
        }

        if ($thang >= 6 && $thang <= 8) {
            return 3;
        }

        if ($thang >= 9 && $thang <= 11) {
            return 1;
        }

        if ($date->isWeekend()) {
            return 2;
        }

        return 2;
    }

    // =========================
    // 🔹 ADD TIEN PHONG
    // =========================
    private function addTienPhong($datPhong, $hoaDon)
    {
        $start = Carbon::parse($datPhong->NgayNhanPhong);
        $end = Carbon::parse($datPhong->NgayTraPhong);
        $tongTien = 0;

        foreach ($datPhong->chiTietDatPhong as $ct) {
            $currentDate = $start->copy();
            $tongPhong = 0;

            while ($currentDate < $end) {
                $mua = $this->getMua($currentDate);
                $bangGia = BangGia::where('MaLoaiPhong', $ct->phong->MaLoaiPhong)
                    ->where('Mua', $mua)
                    ->first();

                if (!$bangGia) {
                    throw new \Exception('Không có giá cho ngày ' . $currentDate->toDateString());
                }

                $tongPhong += $bangGia->GiaPhong;
                $currentDate->addDay();
            }

            ChiTietHoaDon::create([
                'MaHD' => $hoaDon->MaHD,
                'MaLoaiPhong' => $ct->phong->MaLoaiPhong,
                'SoLuong' => 1,
                'DonGia' => $tongPhong
            ]);

            $tongTien += $tongPhong;
        }

        $hoaDon->update(['TongTien' => $tongTien]);
    }

    // =========================
    // 🔹 SHOW
    // =========================
    public function show($id)
    {
        $data = DatPhong::with('chiTietDatPhong.phong')->find($id);
        if (!$data) {
            return $this->error('Không tìm thấy', 404);
        }
        return $this->success($data);
    }

    // =========================
    // 🔹 CONFIRM BOOKING
    // =========================
    public function confirm($id)
    {
        $datPhong = DatPhong::find($id);
        if (!$datPhong) {
            return $this->error('Không tìm thấy', 404);
        }

        if ($datPhong->TinhTrang != 0) {
            return $this->error('Không hợp lệ', 400);
        }

        DB::beginTransaction();
        try {
            $datPhong->update(['TinhTrang' => 1]);
            HoaDon::where('MaDatPhong', $id)->update(['TrangThai' => 1]);
            DB::commit();
            return $this->success(null, 'Xác nhận thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }

    // =========================
    // 🔹 CHECK-IN
    // =========================
    public function checkIn($id)
    {
        $datPhong = DatPhong::with('chiTietDatPhong')->find($id);
        if (!$datPhong) {
            return $this->error('Không tìm thấy', 404);
        }

        if ($datPhong->TinhTrang != 1) {
            return $this->error('Chưa xác nhận', 400);
        }

        DB::beginTransaction();
        try {
            $datPhong->update(['TinhTrang' => 2]);
            foreach ($datPhong->chiTietDatPhong as $ct) {
                Phong::where('MaPhong', $ct->MaPhong)->update(['TinhTrang' => 2]);
            }
            DB::commit();
            return $this->success($datPhong, 'Check-in thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }

    // =========================
    // 🔹 CHECK-OUT
    // =========================
    public function checkOut(Request $request, $id)
    {
        $datPhong = DatPhong::with('chiTietDatPhong')->find($id);
        if (!$datPhong) {
            return $this->error('Không tìm thấy', 404);
        }

        if ($datPhong->TinhTrang != 2) {
            return $this->error('Chưa check-in', 400);
        }

        DB::beginTransaction();
        try {
            $hoaDon = HoaDon::where('MaDatPhong', $id)->first();
            if (!$hoaDon) {
                return $this->error('Không có hóa đơn', 404);
            }

            $hoaDon->update(['TrangThai' => 2]);
            $datPhong->update(['TinhTrang' => 3]);
            
            foreach ($datPhong->chiTietDatPhong as $ct) {
                Phong::where('MaPhong', $ct->MaPhong)->update(['TinhTrang' => 0]);
            }

            DB::commit();
            return $this->success(null, 'Check-out thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }
    //hủy booking
    

    public function cancel($id)
    {
        $datPhong = DatPhong::find($id);

        if (!$datPhong) {
            return $this->error('Không tìm thấy booking', 404);
        }

        // Chỉ được hủy khi đang HOLD hoặc CONFIRMED
        if (!in_array($datPhong->TinhTrang, [0, 1])) {
            return $this->error('Không thể hủy booking này', 400);
        }

        DB::beginTransaction();
        try {
            // Cập nhật booking
            $datPhong->update(['TinhTrang' => 4]); // Cancelled

            // Xóa chi tiết đặt phòng
            ChiTietDatPhong::where('MaDatPhong', $id)->delete();

            // Cập nhật hóa đơn
            HoaDon::where('MaDatPhong', $id)
                ->update(['TrangThai' => 3]); // Cancelled

            DB::commit();

            return $this->success(null, 'Đã hủy booking thành công');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }

    // =========================
    // 🔹 CHANGE ROOM
    // =========================
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

        if ($datPhong->TinhTrang != 2) {
            return $this->error('Chỉ được đổi khi đang ở', 400);
        }

        $isBusy = DB::table('ChiTietDatPhong')
            ->join('DatPhong', 'DatPhong.MaDatPhong', '=', 'ChiTietDatPhong.MaDatPhong')
            ->where('ChiTietDatPhong.MaPhong', $request->newPhong)
            ->where(function ($q) use ($datPhong) {
                $q->where('NgayNhanPhong', '<', $datPhong->NgayTraPhong)
                  ->where('NgayTraPhong', '>', $datPhong->NgayNhanPhong);
            })
            ->whereIn('TinhTrang', [1, 2])
            ->exists();

        if ($isBusy) {
            return $this->error('Phòng mới đã được đặt', 400);
        }

        $ct = ChiTietDatPhong::where('MaDatPhong', $id)
            ->where('MaPhong', $request->oldPhong)
            ->first();

        if (!$ct) {
            return $this->error('Không tìm thấy phòng cũ', 404);
        }

        DB::transaction(function () use ($ct, $request) {
            Phong::where('MaPhong', $ct->MaPhong)->update(['TinhTrang' => 0]);
            Phong::where('MaPhong', $request->newPhong)->update(['TinhTrang' => 2]);
            $ct->update(['MaPhong' => $request->newPhong]);
        });

        return $this->success($ct, 'Đổi phòng thành công');
    }

    // =========================
    // 🔹 ADD ROOM
    // =========================
    public function addRoom(Request $request, $id)
    {
        $request->validate([
            'MaPhong' => 'required|exists:Phong,MaPhong'
        ]);

        $datPhong = DatPhong::find($id);
        if (!$datPhong) {
            return $this->error('Không tìm thấy', 404);
        }

        if ($datPhong->TinhTrang != 2) {
            return $this->error('Chỉ được thêm khi đang ở', 400);
        }

        $isBusy = DB::table('ChiTietDatPhong')
            ->join('DatPhong', 'DatPhong.MaDatPhong', '=', 'ChiTietDatPhong.MaDatPhong')
            ->where('ChiTietDatPhong.MaPhong', $request->MaPhong)
            ->where(function ($q) use ($datPhong) {
                $q->where('NgayNhanPhong', '<', $datPhong->NgayTraPhong)
                  ->where('NgayTraPhong', '>', $datPhong->NgayNhanPhong);
            })
            ->whereIn('TinhTrang', [1, 2])
            ->exists();

        if ($isBusy) {
            return $this->error('Phòng đã được đặt', 400);
        }

        DB::transaction(function () use ($request, $id, $datPhong) {
            ChiTietDatPhong::create([
                'MaDatPhong' => $id,
                'MaPhong' => $request->MaPhong
            ]);
            $datPhong->increment('SoLuong');
            Phong::where('MaPhong', $request->MaPhong)->update(['TinhTrang' => 2]);
        });

        return $this->success(null, 'Thêm phòng thành công');
    }

    // =========================
    // 🔹 REMOVE ROOM
    // =========================
    public function removeRoom($id, $maPhong)
    {
        $datPhong = DatPhong::find($id);
        if (!$datPhong) {
            return $this->error('Không tìm thấy', 404);
        }

        if ($datPhong->TinhTrang != 2) {
            return $this->error('Chỉ được xóa khi đang ở', 400);
        }

        if ($datPhong->SoLuong <= 1) {
            return $this->error('Không thể xóa hết phòng', 400);
        }

        $ct = ChiTietDatPhong::where('MaDatPhong', $id)
            ->where('MaPhong', $maPhong)
            ->first();

        if (!$ct) {
            return $this->error('Không tìm thấy phòng', 404);
        }

        DB::transaction(function () use ($ct, $datPhong) {
            Phong::where('MaPhong', $ct->MaPhong)->update(['TinhTrang' => 0]);
            $ct->delete();
            $datPhong->decrement('SoLuong');
        });

        return $this->success(null, 'Đã xóa phòng');
    }
}