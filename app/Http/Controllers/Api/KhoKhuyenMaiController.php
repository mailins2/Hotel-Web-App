<?php

namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Models\KhoKhuyenMai;
    use App\Models\KhuyenMai;      // 👈 Thêm
    use App\Models\KhachHang;      // 👈 Thêm
    use Carbon\Carbon;             // 👈 Thêm
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;  // 👈 Thêm
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Validation\Rule;

class KhoKhuyenMaiController extends Controller
{
    // 1. Lấy danh sách tất cả kho khuyến mãi (có kèm thông tin khách và tên KM)
    public function index()
    {
        $kho = KhoKhuyenMai::with(['khachHang', 'khuyenMai'])
            ->whereHas('khuyenMai')
            ->get();
        return response()->json($kho, 200);
    }

    // 2. Cấp khuyến mãi cho khách hàng (Tặng mã)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MaKM' => [
                'required',
                'string',
                'max:10',
                Rule::exists('KhuyenMai', 'MaKM'),
            ],
            'MaKH' => 'required|exists:KhachHang,MaKH',
            'TrangThai' => 'nullable|integer|in:0,1' // 0: Chưa dùng, 1: Đã dùng
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Kiểm tra xem khách hàng đã có mã này trong kho chưa (tránh trùng khóa chính)
        $exists = KhoKhuyenMai::where('MaKM', $request->MaKM)
                              ->where('MaKH', $request->MaKH)
                              ->exists();
        
        if ($exists) {
            return response()->json(['message' => 'Khách hàng này đã sở hữu mã khuyến mãi này rồi'], 400);
        }

        $item = KhoKhuyenMai::create($request->all());
        return response()->json(['message' => 'Cấp mã khuyến mãi thành công', 'data' => $item], 201);
    }

    // 3. Lấy danh sách khuyến mãi của riêng 1 khách hàng
    //get /api/kho-khuyen-mai/khach-hang/id
    public function showByKhachHang($maKH)
    {
        $danhSach = KhoKhuyenMai::with('khuyenMai')
            ->whereHas('khuyenMai')
            ->where('MaKH', $maKH)
            ->get();

        return response()->json($danhSach, 200);
    }

    // 4. Cập nhật trạng thái (Sử dụng mã khuyến mãi)
    //put api/kho-khuyen-mai/update-status
    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MaKM' => 'required|string|max:10',
            'MaKH' => 'required',
            'TrangThai' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $item = KhoKhuyenMai::where('MaKM', $request->MaKM)
                            ->where('MaKH', $request->MaKH)
                            ->first();

        if (!$item) {
            return response()->json(['message' => 'Không tìm thấy bản ghi'], 404);
        }

        $item->update(['TrangThai' => $request->TrangThai]);
        return response()->json(['message' => 'Cập nhật trạng thái thành công'], 200);
    }
/**
     * POST /api/kho-khuyen-mai/doi-bang-diem
     * Đổi mã khuyến mãi bằng điểm (dành cho mobile app)
     */
    public function doiBangDiem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MaKM' => [
                'required',
                'string',
                'max:10',
                Rule::exists('KhuyenMai', 'MaKM'),
            ],
            'MaKH' => 'required|exists:KhachHang,MaKH',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $khuyenMai = KhuyenMai::find($request->MaKM);
        $khachHang = KhachHang::find($request->MaKH);

        if (!$khachHang) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khách hàng'
            ], 404);
        }

        // 🔥 Kiểm tra mã có yêu cầu điểm không
        $diemCan = max(0, (int) ($khuyenMai->Diem ?? 0));

        // 🔥 Kiểm tra đủ điểm không
        if ((int) $khachHang->DIEM < $diemCan) {
            return response()->json([
                'success' => false,
                'message' => "Bạn cần {$khuyenMai->Diem} điểm để đổi mã này. Hiện bạn có {$khachHang->DIEM} điểm",
                'data' => [
                    'diemHienTai' => (int) $khachHang->DIEM,
                    'diemCan' => $diemCan,
                    'diemThieu' => (int) ($diemCan - $khachHang->DIEM)
                ]
            ], 400);
        }

        // 🔥 Kiểm tra khuyến mãi còn hạn không
        $now = Carbon::now();
        if ($khuyenMai->NgayBatDau && $now->lt(Carbon::parse($khuyenMai->NgayBatDau))) {
            return response()->json([
                'success' => false,
                'message' => 'Mã khuyến mãi chưa có hiệu lực'
            ], 400);
        }

        if ($khuyenMai->NgayKetThuc && $now->gt(Carbon::parse($khuyenMai->NgayKetThuc))) {
            return response()->json([
                'success' => false,
                'message' => 'Mã khuyến mãi đã hết hạn'
            ], 400);
        }

        // Kiểm tra đã có mã này chưa
        $exists = KhoKhuyenMai::where('MaKM', $request->MaKM)
            ->where('MaKH', $request->MaKH)
            ->first();

        if ($exists) {
            // Nếu đã sử dụng hoặc hết hạn -> cho đổi lại
            if ((int) $exists->TrangThai === 1 || (int) $exists->TrangThai === 2) {
                $exists->delete();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã có mã này trong kho rồi (chưa sử dụng)'
                ], 400);
            }
        }

        // 🔥 Thực hiện đổi mã trong transaction
        try {
            DB::beginTransaction();

            // Trừ điểm khách hàng
            $khachHang->DIEM = (int) $khachHang->DIEM - $diemCan;
            $khachHang->save();

            // Lưu vào kho
            $khoKM = KhoKhuyenMai::create([
                'MaKM' => $request->MaKM,
                'MaKH' => $request->MaKH,
                'TrangThai' => 0 // Chưa sử dụng
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đổi mã khuyến mãi thành công! Bạn đã dùng {$diemCan} điểm",
                'data' => [
                    'kho_khuyen_mai' => $khoKM,
                    'maKM' => $khuyenMai->MaKM,
                    'tenKM' => $khuyenMai->TenKM,
                    'phanTramGiamGia' => $khuyenMai->PhanTramGiamGia,
                    'diemDaDung' => $diemCan,
                    'diemConLai' => (int) $khachHang->DIEM,
                    'ngayHetHan' => $khuyenMai->NgayKetThuc,
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi đổi mã: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/kho-khuyen-mai/su-dung
     * Sử dụng mã khuyến mãi khi thanh toán (dành cho mobile app)
     */
    public function suDung(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MaKM' => [
                'required',
                'string',
                'max:10',
                Rule::exists('KhuyenMai', 'MaKM'),
            ],
            'MaKH' => 'required|exists:KhachHang,MaKH',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $khoKM = KhoKhuyenMai::where('MaKM', $request->MaKM)
            ->where('MaKH', $request->MaKH)
            ->first();

        if (!$khoKM) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa có mã khuyến mãi này trong kho'
            ], 400);
        }

        // Kiểm tra trạng thái
        if ((int) $khoKM->TrangThai === 1) {
            return response()->json([
                'success' => false,
                'message' => 'Mã khuyến mãi đã được sử dụng'
            ], 400);
        }

        if ((int) $khoKM->TrangThai === 2) {
            return response()->json([
                'success' => false,
                'message' => 'Mã khuyến mãi đã hết hạn'
            ], 400);
        }

        // Kiểm tra hạn sử dụng
        $khuyenMai = KhuyenMai::find($request->MaKM);
        $now = Carbon::now();

        if ($khuyenMai->NgayKetThuc && $now->gt(Carbon::parse($khuyenMai->NgayKetThuc))) {
            // Tự động cập nhật hết hạn
            $khoKM->TrangThai = 2;
            $khoKM->save();
            
            return response()->json([
                'success' => false,
                'message' => 'Mã khuyến mãi đã hết hạn'
            ], 400);
        }

        // Đánh dấu đã sử dụng
        $khoKM->TrangThai = 1;
        $khoKM->save();

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã khuyến mãi thành công',
            'data' => [
                'MaKM' => $khuyenMai->MaKM,
                'TenKM' => $khuyenMai->TenKM,
                'PhanTramGiamGia' => $khuyenMai->PhanTramGiamGia,
            ]
        ], 200);
    }

    /**
     * GET /api/kho-khuyen-mai/kiem-tra-diem/{maKH}/{maKM}
     * Kiểm tra điểm trước khi đổi
     */
    public function kiemTraDiem($maKH, $maKM)
    {
        $khachHang = KhachHang::find($maKH);
        $khuyenMai = KhuyenMai::find($maKM);

        if (!$khachHang || !$khuyenMai) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'diemHienTai' => (int) $khachHang->DIEM,
                'diemCan' => (int) $khuyenMai->Diem,
                'duDiem' => (int) $khachHang->DIEM >= (int) $khuyenMai->Diem,
                'diemThieu' => max(0, (int) $khuyenMai->Diem - (int) $khachHang->DIEM),
            ]
        ], 200);
    }


}
