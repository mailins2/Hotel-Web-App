<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DatPhong;
use App\Models\ChiTietDatPhong;
use App\Models\Phong;
use App\Models\HoaDon;
use App\Models\ChiTietHoaDon;
use App\Models\KhachHang;
use App\Models\LuuTru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class DatPhongController extends Controller
{
    // =========================
    // ðŸ”¹ HELPER FUNCTIONS
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

    private function updateBookingDetailsStatus(int $bookingId, int $status): void
    {
        ChiTietDatPhong::where('MaDatPhong', $bookingId)->update(['TrangThai' => $status]);
    }

    // =========================
    // ðŸ”¹ GET ALL
    // =========================
    public function index()
    {
        return $this->success(
            DatPhong::with('chiTietDatPhong.phong')->get()
        );
    }

    // =========================
    // ðŸ”¹ CREATE (ÄÃƒ FIX RACE CONDITION)
    // =========================
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        $maxAttempts = 3;
        
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            try {
                DB::beginTransaction();
                $bookingData = $data;
                $bookingData['MaKH'] = $this->resolveCustomerId($bookingData);

                // 1. Táº¡o DatPhong trÆ°á»›c
                $datPhong = $this->createDatPhong($bookingData);
                
                // 2. Atomic assign rooms
                $assignedRoomIds = $this->atomicAssignRooms($bookingData, $datPhong);
                
                // 3. Kiá»ƒm tra Ä‘á»§ phÃ²ng
                if (count($assignedRoomIds) < $bookingData['SoLuong']) {
                    throw new \Exception('KhÃ´ng Ä‘á»§ phÃ²ng trá»‘ng');
                }

                // 4. Táº¡o hÃ³a Ä‘Æ¡n
                $hoaDon = $this->createHoaDon($datPhong);
                
                // 5. Load phÃ²ng vÃ  tÃ­nh tiá»n
                $datPhong->load('chiTietDatPhong.phong');
                $this->addTienPhong($datPhong, $hoaDon);

                DB::commit();

                return $this->success([
                    'datPhong' => $datPhong->fresh('chiTietDatPhong.phong'),
                    'hoaDon' => $hoaDon->fresh('chiTietHoaDons.loaiPhong.khuyenMai'),
                    'hold_expires_at' => now()->addMinutes(15)->format('Y-m-d H:i:s'), // THÃŠM
                    'hold_remaining_seconds' => 900 // THÃŠM
                ], 'Äáº·t phÃ²ng thÃ nh cÃ´ng . Vui lÃ²ng thanh toÃ¡n Ä‘áº·t cá»c trong vÃ²ng 15 phÃºt !');

            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                
                if ($attempt < $maxAttempts - 1 && in_array($e->getCode(), ['40001', '23000', '1213'])) {
                    usleep(rand(100000, 500000));
                    continue;
                }
                
                return $this->error('Lá»—i database: ' . $e->getMessage(), 500);
                
            } catch (\Exception $e) {
                DB::rollBack();
                
                if ($attempt < $maxAttempts - 1 && 
                    (str_contains($e->getMessage(), 'KhÃ´ng Ä‘á»§ phÃ²ng') || 
                     str_contains($e->getMessage(), 'vá»«a bá»‹ Ä‘áº·t'))) {
                    usleep(rand(100000, 300000));
                    continue;
                }
                
                return $this->error($e->getMessage(), 500);
            }
        }

        return $this->error('KhÃ´ng thá»ƒ Ä‘áº·t phÃ²ng sau ' . $maxAttempts . ' láº§n thá»­. Vui lÃ²ng thá»­ láº¡i sau.');
    }

    // =========================
    // ðŸ”¹ ATOMIC ASSIGN ROOMS (FIX RACE CONDITION)
    // =========================
    private function atomicAssignRooms($data, $datPhong)
    {
        $assignedRooms = [];
        if (!empty($data['LoaiPhongs']) && is_array($data['LoaiPhongs'])) {
            $ngayNhan = $data['NgayNhanPhong'];
            $ngayTra = $data['NgayTraPhong'];

            foreach ($data['LoaiPhongs'] as $loaiPhong) {
                $maLoaiPhong = $loaiPhong['MaLoaiPhong'];
                $soLuong = $loaiPhong['SoLuong'];

                $availableRooms = DB::table('Phong as p')
                    ->where('p.MaLoaiPhong', $maLoaiPhong)
                    ->whereNotExists(function ($query) use ($ngayNhan, $ngayTra) {
                        $query->select(DB::raw(1))
                            ->from('ChiTietDatPhong as ctdp')
                            ->join('DatPhong as dp', 'dp.MaDatPhong', '=', 'ctdp.MaDatPhong')
                            ->whereColumn('ctdp.MaPhong', 'p.MaPhong')
                            ->where('ctdp.TrangThai', '!=', ChiTietDatPhong::CANCELLED)
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
                    throw new \Exception('Khong du phong trong');
                }

                foreach ($availableRooms as $room) {
                    try {
                        ChiTietDatPhong::create([
                            'MaDatPhong' => $datPhong->MaDatPhong,
                            'MaPhong' => $room->MaPhong,
                            'TrangThai' => ChiTietDatPhong::BOOKED,
                        ]);
                        $assignedRooms[] = $room->MaPhong;
                    } catch (\Illuminate\Database\QueryException $e) {
                        if ($e->getCode() == 23000) {
                            throw new \Exception('Phong ' . $room->MaPhong . ' vua bi dat boi nguoi khac');
                        }
                        throw $e;
                    }
                }
            }

            return $assignedRooms;
        }
        $maLoaiPhong = $data['MaLoaiPhong'];
        $ngayNhan = $data['NgayNhanPhong'];
        $ngayTra = $data['NgayTraPhong'];
        $soLuong = $data['SoLuong'];
        
        // Láº¥y phÃ²ng trá»‘ng vá»›i FOR UPDATE
        $availableRooms = DB::table('Phong as p')
            ->where('p.MaLoaiPhong', $maLoaiPhong)
            ->whereNotExists(function ($query) use ($ngayNhan, $ngayTra) {
                $query->select(DB::raw(1))
                    ->from('ChiTietDatPhong as ctdp')
                    ->join('DatPhong as dp', 'dp.MaDatPhong', '=', 'ctdp.MaDatPhong')
                    ->whereColumn('ctdp.MaPhong', 'p.MaPhong')
                    ->where('ctdp.TrangThai', '!=', ChiTietDatPhong::CANCELLED)
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
            throw new \Exception('KhÃ´ng Ä‘á»§ phÃ²ng trá»‘ng');
        }
        
        foreach ($availableRooms as $room) {
            try {
                ChiTietDatPhong::create([
                    'MaDatPhong' => $datPhong->MaDatPhong,
                    'MaPhong' => $room->MaPhong,
                    'TrangThai' => ChiTietDatPhong::BOOKED,
                ]);
                $assignedRooms[] = $room->MaPhong;
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() == 23000) {
                    throw new \Exception('PhÃ²ng ' . $room->MaPhong . ' vá»«a bá»‹ Ä‘áº·t bá»Ÿi ngÆ°á»i khÃ¡c');
                }
                throw $e;
            }
        }
        
        return $assignedRooms;
    }

    // =========================
    // ðŸ”¹ VALIDATE REQUEST
    // =========================
    private function validateRequest($request)
    {
       if ($request->has('LoaiPhongs')) {
           $data = $request->validate([
                'MaKH' => 'nullable|exists:KhachHang,MaKH',
                'TenKH' => 'required_without:MaKH|string|max:100',
                'SoDienThoai' => 'required_without:MaKH|string|max:15',
                'NgayNhanPhong' => [
                    'required',
                    'date',
                    'after_or_equal:today',
                    'before_or_equal:' . now()->addYear()->toDateString()
                ],
                'NgayTraPhong' => 'required|date|after:NgayNhanPhong',
                'LoaiPhongs' => 'required|array|min:1',
                'LoaiPhongs.*.MaLoaiPhong' => [
                    'required',
                    Rule::exists('LoaiPhong', 'MaLoaiPhong'),
                ],
                'LoaiPhongs.*.SoLuong' => 'required|integer|min:1',
            ], [
                'NgayNhanPhong.after_or_equal' => 'KhÃƒÂ´ng thÃ¡Â»Æ’ Ã„â€˜Ã¡ÂºÂ·t phÃƒÂ²ng trong quÃƒÂ¡ khÃ¡Â»Â©',
                'NgayNhanPhong.before_or_equal' => 'ChÃ¡Â»â€° Ã„â€˜Ã†Â°Ã¡Â»Â£c Ã„â€˜Ã¡ÂºÂ·t tÃ¡Â»â€˜i Ã„â€˜a 1 nÃ„Æ’m tÃ¡Â»â€ºi',
                'NgayTraPhong.after' => 'NgÃƒÂ y trÃ¡ÂºÂ£ phÃ¡ÂºÂ£i sau ngÃƒÂ y nhÃ¡ÂºÂ­n'
            ]);

           $data['SoLuong'] = collect($data['LoaiPhongs'])->sum('SoLuong');

           return $data;
       }

       return $request->validate([
            'MaKH' => 'nullable|exists:KhachHang,MaKH',
            'TenKH' => 'required_without:MaKH|string|max:100',
            'SoDienThoai' => 'required_without:MaKH|string|max:15',
            'NgayNhanPhong' => [
                'required',
                'date',
                'after_or_equal:today',
                'before_or_equal:' . now()->addYear()->toDateString() 
            ],
            'NgayTraPhong' => 'required|date|after:NgayNhanPhong',
            'MaLoaiPhong' => [
                'required',
                Rule::exists('LoaiPhong', 'MaLoaiPhong'),
            ],
            'SoLuong' => 'required|integer|min:1'
        ], [
            'NgayNhanPhong.after_or_equal' => 'KhÃ´ng thá»ƒ Ä‘áº·t phÃ²ng trong quÃ¡ khá»©',
            'NgayNhanPhong.before_or_equal' => 'Chá»‰ Ä‘Æ°á»£c Ä‘áº·t tá»‘i Ä‘a 1 nÄƒm tá»›i',
            'NgayTraPhong.after' => 'NgÃ y tráº£ pháº£i sau ngÃ y nháº­n'
        ]);
    }

    // =========================
    // ðŸ”¹ CREATE DAT PHONG
    // =========================
    private function resolveCustomerId(array $data)
    {
        if (!empty($data['MaKH'])) {
            return $data['MaKH'];
        }

        $phone = preg_replace('/\D+/', '', $data['SoDienThoai'] ?? '');

        if ($phone === '') {
            throw new \InvalidArgumentException('Vui long nhap so dien thoai.');
        }

        $existingCustomer = KhachHang::where('SoDienThoai', $phone)->first();

        if ($existingCustomer) {
            return $existingCustomer->MaKH;
        }

        return KhachHang::create([
            'MaTK' => null,
            'TenKH' => $data['TenKH'],
            'SoDienThoai' => $phone,
            'CCCD' => null,
            'NgaySinh' => '1970-01-01',
            'GioiTinh' => 2,
            'DiaChi' => null,
        ])->MaKH;
    }

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
    // ðŸ”¹ CREATE HOA DON
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
    // ðŸ”¹ GET MUA (SEASON)
    // =========================
    private function getMua($ngay)
    {
        return 1;
    }

    // =========================
    // ðŸ”¹ ADD TIEN PHONG
    // =========================
    private function addTienPhong($datPhong, $hoaDon)
    {
        $start = Carbon::parse($datPhong->NgayNhanPhong);
        $end = Carbon::parse($datPhong->NgayTraPhong);
        $soDem = max(1, $start->diffInDays($end));
        $tongTien = 0;

        $datPhong->loadMissing('chiTietDatPhong.phong.loaiPhong.khuyenMai');
        $roomTypeGroups = $datPhong->chiTietDatPhong->groupBy(fn ($ct) => $ct->phong->MaLoaiPhong);

        foreach ($roomTypeGroups as $maLoaiPhong => $items) {
            $roomType = $items->first()?->phong?->loaiPhong;
            $giaPhong = (float) ($roomType?->giaSauKhuyenMai($start) ?? 0);

            if ($giaPhong <= 0) {
                throw new \Exception('KhÃ´ng tÃ¬m tháº¥y giÃ¡ phÃ²ng');
            }

            $donGia = $giaPhong * $soDem;

            $soLuong = $items->count();

            ChiTietHoaDon::create([
                'MaHD' => $hoaDon->MaHD,
                'MaLoaiPhong' => $maLoaiPhong,
                'SoLuong' => $soLuong,
                'DonGia' => $donGia
            ]);

            $tongTien += $soLuong * $donGia;
        }

        $hoaDon->update(['TongTien' => $tongTien]);
    }

    // =========================
    // ðŸ”¹ SHOW
    // =========================
    public function show($id)
    {
        $data = DatPhong::with([
            'khachHang.taiKhoan',
            'hoaDon.chiTietHoaDons.loaiPhong.khuyenMai',
            'hoaDon.thanhToans',
            'chiTietDatPhong.phong.loaiPhong.khuyenMai',
        ])->find($id);
        if (!$data) {
            return $this->error('KhÃ´ng tÃ¬m tháº¥y', 404);
        }
        return $this->success($data);
    }

    // =========================
    // ðŸ”¹ CONFIRM BOOKING
    // =========================
    public function confirm($id)
    {
        $datPhong = DatPhong::find($id);
        if (!$datPhong) {
            return $this->error('KhÃ´ng tÃ¬m tháº¥y', 404);
        }

        if ($datPhong->TinhTrang != 0) {
            return $this->error('KhÃ´ng há»£p lá»‡', 400);
        }

        DB::beginTransaction();
        try {
            $datPhong->update(['TinhTrang' => 1]);
            $this->updateBookingDetailsStatus((int) $id, ChiTietDatPhong::BOOKED);
            HoaDon::where('MaDatPhong', $id)->update(['TrangThai' => 1]);
            DB::commit();
            return $this->success(null, 'XÃ¡c nháº­n thÃ nh cÃ´ng');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }

    // =========================
    // ðŸ”¹ CHECK-IN
    // =========================
    public function checkIn(Request $request, $id)
    {
        $data = $request->validate([
            'MaPhong' => [
                'required',
                Rule::exists('Phong', 'MaPhong'),
            ],
            'KhachLuuTru' => ['required', 'array', 'min:1'],
            'KhachLuuTru.*.TenKhach' => ['nullable', 'string', 'max:100'],
            'KhachLuuTru.*.NgaySinh' => ['nullable', 'date', 'before_or_equal:today'],
            'KhachLuuTru.*.CCCD' => ['nullable', 'string', 'regex:/^\d{12}$/'],
            'KhachLuuTru.*.SoDienThoai' => ['nullable', 'string', 'regex:/^0\d{9}$/'],
            'KhachLuuTru.*.VaiTro' => ['nullable', Rule::in(['adult', 'child'])],
        ]);

        $guests = collect($data['KhachLuuTru'])
            ->filter(function ($guest) {
                return collect($guest)->contains(function ($value) {
                    return $value !== null && trim((string) $value) !== '';
                });
            })
            ->values();

        foreach ($guests as $index => $guest) {
            $guestNumber = $index + 1;

            if (
                empty(trim((string) ($guest['TenKhach'] ?? '')))
                || empty($guest['NgaySinh'])
                || empty(trim((string) ($guest['CCCD'] ?? '')))
            ) {
                return $this->error("Vui lòng nhập đầy đủ họ tên, ngày sinh và CCCD cho khách {$guestNumber}.", 422);
            }

            if (Carbon::parse($guest['NgaySinh'])->age >= 18 && empty(trim((string) ($guest['SoDienThoai'] ?? '')))) {
                return $this->error("Vui lòng nhập số điện thoại cho khách người lớn {$guestNumber}.", 422);
            }
            if (($guest['VaiTro'] ?? 'adult') === 'child' && Carbon::parse($guest['NgaySinh'])->age >= 12) {
                return $this->error("Trẻ em phải dưới 12 tuổi cho khách {$guestNumber}.", 422);
            }
        }

        $hasAdultGuest = $guests->contains(function ($guest) {
            if (empty($guest['NgaySinh'])) {
                return false;
            }

            return Carbon::parse($guest['NgaySinh'])->age >= 18;
        });

        if (!$hasAdultGuest) {
            return $this->error('Cần có ít nhất một khách đủ 18 tuổi trở lên để check-in.', 422);
        }

        $datPhong = DatPhong::with('chiTietDatPhong')->find($id);
        if (!$datPhong) {
            return $this->error('Không tìm thấy', 404);
        }

        if ((int) $datPhong->TinhTrang !== DatPhong::CONFIRMED) {
            return $this->error('Đặt phòng chưa được xác nhận hoặc đã nhận phòng', 400);
        }

        $roomDetail = $datPhong->chiTietDatPhong
            ->first(fn ($detail) => (int) $detail->MaPhong === (int) $data['MaPhong']);

        if (!$roomDetail) {
            return $this->error('Phòng không thuộc đặt phòng này', 422);
        }

        if ((int) $roomDetail->TrangThai !== ChiTietDatPhong::BOOKED) {
            return $this->error('Phòng này không còn ở trạng thái chờ nhận', 422);
        }

        if (LuuTru::where('MaDatPhong', $datPhong->MaDatPhong)->where('MaPhong', $data['MaPhong'])->exists()) {
            return $this->error('Phòng này đã được nhận trước đó', 409);
        }

        DB::beginTransaction();
        try {
            foreach ($guests as $guest) {
                LuuTru::create([
                    'TenKhach' => trim((string) ($guest['TenKhach'] ?? '')) ?: 'Khách lưu trú',
                    'NgaySinh' => $guest['NgaySinh'],
                    'CCCD' => trim((string) $guest['CCCD']),
                    'SoDienThoai' => $guest['SoDienThoai'] ?? null,
                    'MaPhong' => $data['MaPhong'],
                    'MaDatPhong' => $datPhong->MaDatPhong,
                ]);
            }

            $roomDetail->update(['TrangThai' => ChiTietDatPhong::CHECKED_IN]);

            Phong::where('MaPhong', $data['MaPhong'])->update(['TinhTrang' => 2]);

            $checkedInRoomCount = ChiTietDatPhong::where('MaDatPhong', $datPhong->MaDatPhong)
                ->where('TrangThai', ChiTietDatPhong::CHECKED_IN)
                ->count();
            $totalRoomCount = ChiTietDatPhong::where('MaDatPhong', $datPhong->MaDatPhong)
                ->where('TrangThai', '!=', ChiTietDatPhong::CANCELLED)
                ->count();

            if ($checkedInRoomCount >= $totalRoomCount) {
                $datPhong->update(['TinhTrang' => DatPhong::CHECKED_IN]);
            }

            DB::commit();

            return $this->success([
                'datPhong' => $datPhong->fresh('chiTietDatPhong.phong'),
                'checkedInRoomCount' => $checkedInRoomCount,
                'totalRoomCount' => $totalRoomCount,
            ], 'Check-in thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }

    // =========================
    // ðŸ”¹ CHECK-OUT
    // =========================
    public function checkOut(Request $request, $id)
    {
        $datPhong = DatPhong::with('chiTietDatPhong')->find($id);
        if (!$datPhong) {
            return $this->error('KhÃ´ng tÃ¬m tháº¥y', 404);
        }

        if ($datPhong->TinhTrang != 2) {
            return $this->error('ChÆ°a check-in', 400);
        }

        DB::beginTransaction();
        try {
            $hoaDon = HoaDon::where('MaDatPhong', $id)->first();
            if (!$hoaDon) {
                return $this->error('KhÃ´ng cÃ³ hÃ³a Ä‘Æ¡n', 404);
            }

            $hoaDon->update(['TrangThai' => 2]);
            $datPhong->update(['TinhTrang' => 3]);
            
            foreach ($datPhong->chiTietDatPhong as $ct) {
                $ct->update(['TrangThai' => ChiTietDatPhong::CHECKED_OUT]);
                Phong::where('MaPhong', $ct->MaPhong)->update(['TinhTrang' => 0]);
            }

            DB::commit();
            return $this->success(null, 'Check-out thÃ nh cÃ´ng');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }
    //há»§y booking
    

    public function cancel($id)
    {
        $datPhong = DatPhong::find($id);

        if (!$datPhong) {
            return $this->error('KhÃ´ng tÃ¬m tháº¥y booking', 404);
        }

        // Chá»‰ Ä‘Æ°á»£c há»§y khi Ä‘ang HOLD hoáº·c CONFIRMED
        if (!in_array($datPhong->TinhTrang, [0, 1])) {
            return $this->error('KhÃ´ng thá»ƒ há»§y booking nÃ y', 400);
        }

        if (ChiTietDatPhong::where('MaDatPhong', $datPhong->MaDatPhong)
            ->where('TrangThai', ChiTietDatPhong::CHECKED_IN)
            ->exists()) {
            return $this->error('Khong the huy booking da co phong nhan.', 400);
        }

        DB::beginTransaction();
        try {
            // Cáº­p nháº­t booking
            $datPhong->update(['TinhTrang' => 4]); // Cancelled
            $this->updateBookingDetailsStatus((int) $id, ChiTietDatPhong::CANCELLED);

            // Cáº­p nháº­t hÃ³a Ä‘Æ¡n
            HoaDon::where('MaDatPhong', $id)
                ->update(['TrangThai' => 3]); // Cancelled

            DB::commit();

            return $this->success(null, 'ÄÃ£ há»§y booking thÃ nh cÃ´ng');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }

    // =========================
    // ðŸ”¹ CHANGE ROOM
    // =========================
    public function changeRoom(Request $request, $id)
    {
        $request->validate([
            'oldPhong' => 'required|exists:Phong,MaPhong',
            'newPhong' => [
                'required',
                Rule::exists('Phong', 'MaPhong'),
            ],
        ]);

        if (!Phong::where('MaPhong', $request->newPhong)
            ->whereHas('loaiPhong')
            ->exists()) {
            return $this->error('PhÃƒÂ²ng mÃ¡Â»â€ºi khÃƒÂ´ng khÃ¡ÂºÂ£ dÃ¡Â»Â¥ng', 422);
        }

        $datPhong = DatPhong::find($id);
        if (!$datPhong) {
            return $this->error('KhÃ´ng tÃ¬m tháº¥y', 404);
        }

        if ($datPhong->TinhTrang != 2) {
            return $this->error('Chá»‰ Ä‘Æ°á»£c Ä‘á»•i khi Ä‘ang á»Ÿ', 400);
        }

        $isBusy = DB::table('ChiTietDatPhong')
            ->join('DatPhong', 'DatPhong.MaDatPhong', '=', 'ChiTietDatPhong.MaDatPhong')
            ->where('ChiTietDatPhong.MaPhong', $request->newPhong)
            ->where('ChiTietDatPhong.TrangThai', '!=', ChiTietDatPhong::CANCELLED)
            ->where(function ($q) use ($datPhong) {
                $q->where('NgayNhanPhong', '<', $datPhong->NgayTraPhong)
                  ->where('NgayTraPhong', '>', $datPhong->NgayNhanPhong);
            })
            ->whereIn('TinhTrang', [1, 2])
            ->exists();

        if ($isBusy) {
            return $this->error('PhÃ²ng má»›i Ä‘Ã£ Ä‘Æ°á»£c Ä‘áº·t', 400);
        }

        $ct = ChiTietDatPhong::where('MaDatPhong', $id)
            ->where('MaPhong', $request->oldPhong)
            ->first();

        if (!$ct) {
            return $this->error('KhÃ´ng tÃ¬m tháº¥y phÃ²ng cÅ©', 404);
        }

        DB::transaction(function () use ($ct, $request) {
            Phong::where('MaPhong', $ct->MaPhong)->update(['TinhTrang' => 0]);
            Phong::where('MaPhong', $request->newPhong)->update(['TinhTrang' => 2]);
            $ct->update(['MaPhong' => $request->newPhong]);
        });

        return $this->success($ct, 'Äá»•i phÃ²ng thÃ nh cÃ´ng');
    }

    // =========================
    // ðŸ”¹ ADD ROOM
    // =========================
    public function addRoom(Request $request, $id)
    {
        $request->validate([
            'MaPhong' => [
                'required',
                Rule::exists('Phong', 'MaPhong'),
            ],
        ]);

        if (!Phong::where('MaPhong', $request->MaPhong)
            ->whereHas('loaiPhong')
            ->exists()) {
            return $this->error('PhÃƒÂ²ng khÃƒÂ´ng khÃ¡ÂºÂ£ dÃ¡Â»Â¥ng', 422);
        }

        $datPhong = DatPhong::find($id);
        if (!$datPhong) {
            return $this->error('KhÃ´ng tÃ¬m tháº¥y', 404);
        }

        if ($datPhong->TinhTrang != 2) {
            return $this->error('Chá»‰ Ä‘Æ°á»£c thÃªm khi Ä‘ang á»Ÿ', 400);
        }

        $isBusy = DB::table('ChiTietDatPhong')
            ->join('DatPhong', 'DatPhong.MaDatPhong', '=', 'ChiTietDatPhong.MaDatPhong')
            ->where('ChiTietDatPhong.MaPhong', $request->MaPhong)
            ->where('ChiTietDatPhong.TrangThai', '!=', ChiTietDatPhong::CANCELLED)
            ->where(function ($q) use ($datPhong) {
                $q->where('NgayNhanPhong', '<', $datPhong->NgayTraPhong)
                  ->where('NgayTraPhong', '>', $datPhong->NgayNhanPhong);
            })
            ->whereIn('TinhTrang', [1, 2])
            ->exists();

        if ($isBusy) {
            return $this->error('PhÃ²ng Ä‘Ã£ Ä‘Æ°á»£c Ä‘áº·t', 400);
        }

        DB::transaction(function () use ($request, $id, $datPhong) {
            ChiTietDatPhong::create([
                'MaDatPhong' => $id,
                'MaPhong' => $request->MaPhong,
                'TrangThai' => ChiTietDatPhong::CHECKED_IN,
            ]);
            $datPhong->increment('SoLuong');
            Phong::where('MaPhong', $request->MaPhong)->update(['TinhTrang' => 2]);
        });

        return $this->success(null, 'ThÃªm phÃ²ng thÃ nh cÃ´ng');
    }

    // =========================
    // ðŸ”¹ REMOVE ROOM
    // =========================
    public function removeRoom($id, $maPhong)
    {
        $datPhong = DatPhong::find($id);
        if (!$datPhong) {
            return $this->error('KhÃ´ng tÃ¬m tháº¥y', 404);
        }

        if ($datPhong->TinhTrang != 2) {
            return $this->error('Chá»‰ Ä‘Æ°á»£c xÃ³a khi Ä‘ang á»Ÿ', 400);
        }

        if ($datPhong->SoLuong <= 1) {
            return $this->error('KhÃ´ng thá»ƒ xÃ³a háº¿t phÃ²ng', 400);
        }

        $ct = ChiTietDatPhong::where('MaDatPhong', $id)
            ->where('MaPhong', $maPhong)
            ->first();

        if (!$ct) {
            return $this->error('KhÃ´ng tÃ¬m tháº¥y phÃ²ng', 404);
        }

        DB::transaction(function () use ($ct, $datPhong) {
            Phong::where('MaPhong', $ct->MaPhong)->update(['TinhTrang' => 0]);
            $ct->delete();
            $datPhong->decrement('SoLuong');
        });

        return $this->success(null, 'ÄÃ£ xÃ³a phÃ²ng');
    }

    /**
     * Lấy lịch sử đặt phòng của khách hàng
     * GET /api/khach-hang/{maKH}/dat-phong
     */
    public function lichSuDatPhong($maKH)
{
    $datPhongs = DatPhong::with([
            'chiTietDatPhong.phong.loaiPhong.khuyenMai', // 👈 THÊM khuyenMai
            'hoaDon.khuyenMai', // 👈 THÊM khuyenMai của hóa đơn
        ])
        ->where('MaKH', $maKH)
        ->orderBy('NgayDat', 'desc')
        ->get()
        ->map(function ($dp) {
            $tongTienGoc = 0;
            $tongTienSauGiam = 0;
            $daThanhToan = 0;
            $trangThaiHD = null;
            $maHD = null;
            $maKM = null;
            $phanTramGiam = 0;
            $tenKM = null;
            
            if ($dp->hoaDon) {
                $maHD = $dp->hoaDon->MaHD;
                $daThanhToan = (float) $dp->hoaDon->DaThanhToan;
                $trangThaiHD = (int) $dp->hoaDon->TrangThai;
                $maKM = $dp->hoaDon->MaKM;
                
                // 🔥 Tính tổng tiền gốc (chưa giảm)
                $tongTienGoc = (float) $dp->hoaDon->TongTien;
                
                // 🔥 Nếu có khuyến mãi, tính tiền sau giảm
                if ($dp->hoaDon->khuyenMai) {
                    $phanTramGiam = (float) $dp->hoaDon->khuyenMai->PhanTramGiamGia;
                    $tenKM = $dp->hoaDon->khuyenMai->TenKM;
                    $tongTienSauGiam = $tongTienGoc * (1 - $phanTramGiam / 100);
                } else {
                    $tongTienSauGiam = $tongTienGoc;
                }
            }

            // Tính số tiền còn lại
            $conLai = $tongTienSauGiam - $daThanhToan;
            if ($conLai < 0) $conLai = 0;

            return [
                'MaDatPhong' => $dp->MaDatPhong,
                'MaKH' => $dp->MaKH,
                'NgayDat' => $dp->NgayDat,
                'NgayNhanPhong' => $dp->NgayNhanPhong,
                'NgayTraPhong' => $dp->NgayTraPhong,
                'SoLuong' => $dp->SoLuong,
                'TinhTrang' => (int) $dp->TinhTrang,
                'TinhTrangText' => $this->getTinhTrangText($dp->TinhTrang),
                'hoa_don' => [
                    'MaHD' => $maHD,
                    'TongTien' => $tongTienSauGiam, // 👈 Tiền sau giảm
                    'TongTienGoc' => $tongTienGoc,   // 👈 Tiền gốc (chưa giảm)
                    'DaThanhToan' => $daThanhToan,
                    'ConLai' => $conLai,
                    'TrangThai' => $trangThaiHD,
                    'TrangThaiText' => $this->getTrangThaiHDText($trangThaiHD),
                    'MaKM' => $maKM,
                    'PhanTramGiam' => $phanTramGiam, // 👈 % giảm
                    'TenKM' => $tenKM,               // 👈 Tên khuyến mãi
                ],
                'phongs' => $dp->chiTietDatPhong->map(function ($ct) {
                    $phong = $ct->phong;
                    $loaiPhong = $phong?->loaiPhong;
                    $giaPhong = $loaiPhong ? (float) $loaiPhong->GiaPhong : 0;
                    
                    return [
                        'MaPhong' => $phong->MaPhong ?? 0,
                        'SoPhong' => $phong->SoPhong ?? '',
                        'TenLoaiPhong' => $loaiPhong->TenLoaiPhong ?? '',
                        'GiaPhong' => $giaPhong,
                        'MaLoaiPhong' => $loaiPhong->MaLoaiPhong ?? 0,
                    ];
                })->values(),
                'tongPhong' => $dp->chiTietDatPhong->count(),
                'soDem' => max(1, Carbon::parse($dp->NgayNhanPhong)->diffInDays(Carbon::parse($dp->NgayTraPhong))),
            ];
        });

    return $this->success($datPhongs, 'Lấy lịch sử đặt phòng thành công');
}

    private function getTinhTrangText($tinhTrang)
    {
        return match ((int) $tinhTrang) {
            0 => 'Chờ xác nhận',
            1 => 'Đã xác nhận',
            2 => 'Đang ở',
            3 => 'Đã trả phòng',
            4 => 'Đã hủy',
            default => 'Không xác định',
        };
    }

    private function getTrangThaiHDText($trangThai)
    {
        return match ((int) $trangThai) {
            0 => 'Chưa thanh toán',
            1 => 'Đã thanh toán',
            2 => 'Đã hoàn tất',
            3 => 'Đã hủy',
            default => 'Không xác định',
        };
    }
}
