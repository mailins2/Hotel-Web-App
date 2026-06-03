<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DatPhong;
use App\Models\ChiTietDatPhong;
use App\Models\Phong;
use App\Models\HoaDon;
use App\Models\ChiTietHoaDon;
use App\Models\KhachHang;
use App\Models\KhuyenMai;
use App\Models\KhoKhuyenMai;
use App\Models\LuuTru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

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

    private function updateBookingDetailsStatus(int $bookingId, int $status): void
    {
        ChiTietDatPhong::where('MaDatPhong', $bookingId)->update(['TrangThai' => $status]);
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
                $bookingData = $data;
                $bookingData['MaKH'] = $this->resolveCustomerId($bookingData);

                if (!empty($bookingData['MaKM'])) {
                    $this->validatePromotionForCustomer((int) $bookingData['MaKH'], (string) $bookingData['MaKM']);
                }

                // 1. Tạo DatPhong trước
                $datPhong = $this->createDatPhong($bookingData);

                // 2. Atomic assign rooms
                $assignedRoomIds = $this->atomicAssignRooms($bookingData, $datPhong);

                // 3. Kiểm tra đủ phòng
                if (count($assignedRoomIds) < $bookingData['SoLuong']) {
                    throw new \Exception('Không đủ phòng trống');
                }

                // 4. Tạo hóa đơn
                $hoaDon = $this->createHoaDon($datPhong);

                // 5. Load phòng và tính tiền
                $datPhong->load('chiTietDatPhong.phong');
                if (isset($bookingData['MaKM'])) {
                    $hoaDon->update(['MaKM' => $bookingData['MaKM']]);
                }

                $this->addTienPhong($datPhong, $hoaDon);

                DB::commit();

                return $this->success([
                    'datPhong' => $datPhong->fresh('chiTietDatPhong.phong'),
                    'hoaDon' => $hoaDon->fresh('chiTietHoaDons.loaiPhong.khuyenMai'),
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

                if ($e instanceof \InvalidArgumentException) {
                    return $this->error($e->getMessage(), 422);
                }

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
                    throw new \Exception('Không đủ phòng trống');
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

        // Lấy phòng trống với FOR UPDATE
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
            throw new \Exception('Không đủ phòng trống');
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
       if ($request->has('LoaiPhongs')) {
           $data = $request->validate([
                'MaKH' => 'nullable|exists:KhachHang,MaKH',
                'MaKM' => 'nullable|string|exists:KhuyenMai,MaKM', 
                'TenKH' => 'required_without:MaKH|string|max:100',
                'SoDienThoai' => 'required_without:MaKH|string|max:15',
                'NgayNhanPhong' => [
                    'required',
                    'date',
                    'after_or_equal:today',
                    'before_or_equal:' . now()->addYear()->toDateString()
                ],
                'NgayTraPhong' => 'required|date|after:NgayNhanPhong',
                'TinhTrang' => 'sometimes|integer|in:0,1',
                'LoaiPhongs' => 'required|array|min:1',
                'LoaiPhongs.*.MaLoaiPhong' => [
                    'required',
                    Rule::exists('LoaiPhong', 'MaLoaiPhong'),
                ],
                'LoaiPhongs.*.SoLuong' => 'required|integer|min:1',
            ], [
                'NgayNhanPhong.after_or_equal' => 'Không thể đặt phòng trong quá khứ',
                'NgayNhanPhong.before_or_equal' => 'Chỉ được đặt tối đa 1 năm tới',
                'NgayTraPhong.after' => 'Ngày trả phải sau ngày nhận'
            ]);

           $data['SoLuong'] = collect($data['LoaiPhongs'])->sum('SoLuong');

           return $data;
       }

       return $request->validate([
            'MaKH' => 'nullable|exists:KhachHang,MaKH',
            'MaKM' => 'nullable|string|exists:KhuyenMai,MaKM',
            'TenKH' => 'required_without:MaKH|string|max:100',
            'SoDienThoai' => 'required_without:MaKH|string|max:15',
            'NgayNhanPhong' => [
                'required',
                'date',
                'after_or_equal:today',
                'before_or_equal:' . now()->addYear()->toDateString()
            ],
            'NgayTraPhong' => 'required|date|after:NgayNhanPhong',
            'TinhTrang' => 'sometimes|integer|in:0,1',
            'MaLoaiPhong' => [
                'required',
                Rule::exists('LoaiPhong', 'MaLoaiPhong'),
            ],
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

    private function validatePromotionForCustomer(int $customerId, string $promotionId): KhuyenMai
    {
        $promotion = KhuyenMai::where('MaKM', $promotionId)
            ->whereDate('NgayBatDau', '<=', now()->toDateString())
            ->whereDate('NgayKetThuc', '>=', now()->toDateString())
            ->first();

        if (!$promotion) {
            throw new \InvalidArgumentException('Ma khuyen mai khong con han hoac khong hop le.');
        }

        $ownsPromotion = KhoKhuyenMai::where('MaKH', $customerId)
            ->where('MaKM', $promotionId)
            ->where('TrangThai', 0)
            ->exists();

        if (!$ownsPromotion) {
            throw new \InvalidArgumentException('Ma khuyen mai khong thuoc kho cua khach hang hoac da duoc su dung.');
        }

        return $promotion;
    }

    private function createDatPhong($data)
    {
        return DatPhong::create([
            'MaKH' => $data['MaKH'],
            'NgayDat' => now(),
            'NgayNhanPhong' => $data['NgayNhanPhong'],
            'NgayTraPhong' => $data['NgayTraPhong'],
            'SoLuong' => $data['SoLuong'],
            'TinhTrang' => $data['TinhTrang'] ?? DatPhong::HOLD,
            'MaKM' => $data['MaKM'] ?? null,
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
            'TrangThai' => 0,
            'MaKM' => $datPhong->MaKM ?? null,
        ]);
    }

    // =========================
    // 🔹 GET MUA (SEASON)
    // =========================
    private function getMua($ngay)
    {
        return 1;
    }

    // =========================
    // 🔹 ADD TIEN PHONG
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
                throw new \Exception('Không tìm thấy giá phòng');
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
         if ($hoaDon->MaKM) {
                $voucher = \App\Models\KhuyenMai::find($hoaDon->MaKM);
                if ($voucher && $voucher->PhanTramGiamGia > 0) {
                    $tongTien = $tongTien * (1 - $voucher->PhanTramGiamGia / 100);
                }
            }

        $hoaDon->update(['TongTien' => $tongTien]);
    }

    // =========================
    // 🔹 SHOW
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
            $this->updateBookingDetailsStatus((int) $id, ChiTietDatPhong::BOOKED);
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
    // 🔹 CHECK-OUT
    // =========================
    public function checkOut(Request $request, $id)
    {
        $data = $request->validate([
            'MaNV' => ['required', 'integer', 'exists:NhanVien,MaNV'],
        ]);

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

            $hoaDon->update([
                'TrangThai' => 1,
                'MaNV' => $data['MaNV'] ?? $hoaDon->MaNV,
            ]);
            $datPhong->update(['TinhTrang' => 3]);

            foreach ($datPhong->chiTietDatPhong as $ct) {
                $ct->update(['TrangThai' => ChiTietDatPhong::CHECKED_OUT]);
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

        if (ChiTietDatPhong::where('MaDatPhong', $datPhong->MaDatPhong)
            ->where('TrangThai', ChiTietDatPhong::CHECKED_IN)
            ->exists()) {
            return $this->error('Khong the huy booking da co phong nhan.', 400);
        }

        DB::beginTransaction();
        try {
            // Cập nhật booking
            $datPhong->update(['TinhTrang' => 4]); // Cancelled
            $this->updateBookingDetailsStatus((int) $id, ChiTietDatPhong::CANCELLED);

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
            'newPhong' => [
                'required',
                Rule::exists('Phong', 'MaPhong'),
            ],
        ]);

        if (!Phong::where('MaPhong', $request->newPhong)
            ->whereHas('loaiPhong')
            ->exists()) {
            return $this->error('Phòng mới không khả dụng', 422);
        }

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
            ->where('ChiTietDatPhong.TrangThai', '!=', ChiTietDatPhong::CANCELLED)
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
            'MaPhong' => [
                'required',
                Rule::exists('Phong', 'MaPhong'),
            ],
        ]);

        if (!Phong::where('MaPhong', $request->MaPhong)
            ->whereHas('loaiPhong')
            ->exists()) {
            return $this->error('Phòng không khả dụng', 422);
        }

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
            ->where('ChiTietDatPhong.TrangThai', '!=', ChiTietDatPhong::CANCELLED)
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
                'MaPhong' => $request->MaPhong,
                'TrangThai' => ChiTietDatPhong::CHECKED_IN,
            ]);
            $datPhong->increment('SoLuong');
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
            $ct->delete();
            $datPhong->decrement('SoLuong');
        });

        return $this->success(null, 'Đã xóa phòng');
    }

    /**
     * Lấy lịch sử đặt phòng của khách hàng
     * GET /api/khach-hang/{maKH}/dat-phong
     */
   public function lichSuDatPhong($maKH)
{
    $datPhongs = DatPhong::with([
            'danhGia',
            'chiTietDatPhong.phong.loaiPhong.khuyenMai',
            'hoaDon.khuyenMai',
            'hoaDon.chiTietHoaDons.loaiPhong',
            'hoaDon.chiTietHoaDons.suDungDichVu.dichVu',
            'hoaDon.thanhToans',
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
            $chiTietTien = [];
            $dichVuSuDung = [];
            
            if ($dp->hoaDon) {
                $maHD = $dp->hoaDon->MaHD;
                $trangThaiHD = (int) $dp->hoaDon->TrangThai;
                $maKM = $dp->hoaDon->MaKM;
                
                foreach ($dp->hoaDon->chiTietHoaDons as $cthd) {
                    $giaSauKM = (float) $cthd->DonGia;
                    $soLuong = (int) $cthd->SoLuong;
                    
                    // 🔥 CỘNG DỒN TẤT CẢ (phòng + dịch vụ)
                    $tongTienGoc += $giaSauKM * $soLuong;
                    
                    // Tiền phòng
                    if ($cthd->MaLoaiPhong && $cthd->loaiPhong) {
                        $giaGocPhong = (float) $cthd->loaiPhong->GiaPhong;
                        
                        $chiTietTien[] = [
                            'loai' => 'phong',
                            'ten' => $cthd->loaiPhong->TenLoaiPhong,
                            'giaGoc' => $giaGocPhong,
                            'giaSauKM' => $giaSauKM,
                            'soLuong' => $soLuong,
                            'thanhTien' => $giaSauKM * $soLuong,
                            'kmPhong' => $cthd->loaiPhong->khuyenMai ? [
                                'ten' => $cthd->loaiPhong->khuyenMai->TenKM,
                                'phanTram' => (float) $cthd->loaiPhong->khuyenMai->PhanTramGiamGia,
                            ] : null,
                        ];
                    }
                    
                    // Dịch vụ
                    if ($cthd->MaSuDung && $cthd->suDungDichVu && $cthd->suDungDichVu->dichVu) {
                        $dv = $cthd->suDungDichVu->dichVu;
                        $dichVuSuDung[] = [
                            'ten' => $dv->TenDV,
                            'gia' => (float) $dv->GiaDV,
                            'soLuong' => $soLuong,
                            'thanhTien' => $giaSauKM * $soLuong,
                            'thoiGian' => $cthd->suDungDichVu->ThoiGian,
                        ];
                        
                        $chiTietTien[] = [
                            'loai' => 'dichvu',
                            'ten' => $dv->TenDV,
                            'gia' => (float) $dv->GiaDV,
                            'soLuong' => $soLuong,
                            'thanhTien' => $giaSauKM * $soLuong,
                        ];
                    }
                }
                
                // 🔥 Đã thanh toán (giới hạn không vượt quá tổng)
                $daThanhToan = min((float) $dp->hoaDon->DaThanhToan, $tongTienGoc);
                
                // Tính tiền sau giảm KM voucher
                if ($dp->hoaDon->khuyenMai) {
                    $phanTramGiam = (float) $dp->hoaDon->khuyenMai->PhanTramGiamGia;
                    $tenKM = $dp->hoaDon->khuyenMai->TenKM;
                    $tongTienSauGiam = $tongTienGoc * (1 - $phanTramGiam / 100);
                } else {
                    $tongTienSauGiam = $tongTienGoc;
                }
            }

            // 🔥 Còn nợ = Tổng sau giảm - Đã thanh toán
            $conLai = max(0, $tongTienSauGiam - $daThanhToan);

            return [
                'MaDatPhong' => $dp->MaDatPhong,
                'MaKH' => $dp->MaKH,
                'NgayDat' => $dp->NgayDat,
                'NgayNhanPhong' => $dp->NgayNhanPhong,
                'NgayTraPhong' => $dp->NgayTraPhong,
                'SoLuong' => $dp->SoLuong,
                'TinhTrang' => (int) $dp->TinhTrang,
                'TinhTrangText' => $this->getTinhTrangText($dp->TinhTrang),
                'soDem' => max(1, Carbon::parse($dp->NgayNhanPhong)->diffInDays(Carbon::parse($dp->NgayTraPhong))),
                'da_danh_gia' => $dp->danhGia != null,
                'SaoDanhGia' => $dp->danhGia->Sao ?? null,
                'chi_tiet_tien' => $chiTietTien,
                'dich_vu' => $dichVuSuDung,
                'hoa_don' => [
                    'MaHD' => $maHD,
                    'TongTienGoc' => $tongTienGoc,
                    'TongTien' => $tongTienSauGiam,
                    'DaThanhToan' => $daThanhToan,
                    'ConLai' => $conLai,
                    'TrangThai' => $trangThaiHD,
                    'TrangThaiText' => $this->getTrangThaiHDText($trangThaiHD),
                    'MaKM' => $maKM,
                    'PhanTramGiam' => $phanTramGiam,
                    'TenKM' => $tenKM,
                ],
                'phongs' => $dp->chiTietDatPhong->map(function ($ct) {
                    $phong = $ct->phong;
                    $loaiPhong = $phong?->loaiPhong;
                    return [
                        'MaCTDP' => $ct->MaCTDP,
                        'MaPhong' => $phong->MaPhong ?? 0,
                        'SoPhong' => $phong->SoPhong ?? '',
                        'TenLoaiPhong' => $loaiPhong->TenLoaiPhong ?? '',
                        'GiaGoc' => (float) ($loaiPhong->GiaPhong ?? 0),
                        'GiaSauKM' => $loaiPhong ? $loaiPhong->giaSauKhuyenMai() : 0,
                        'MaLoaiPhong' => $loaiPhong->MaLoaiPhong ?? 0,
                    ];
                })->values(),
                'tongPhong' => $dp->chiTietDatPhong->count(),
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
