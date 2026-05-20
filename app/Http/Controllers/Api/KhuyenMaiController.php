<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KhuyenMai;
use App\Services\Guards\KhuyenMaiDeletionGuard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KhuyenMaiController extends Controller
{
    public function __construct(
        private KhuyenMaiDeletionGuard $guard
    ) {
    }

    private function success($data = null, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    private function error(string $message = 'Error', int $code = 400, array $errors = [])
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== []) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $code);
    }

    public function index()
    {
        return $this->success(KhuyenMai::all(), 'Lấy danh sách khuyến mãi thành công');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MaKM'             => 'sometimes|required|string|max:10|unique:KhuyenMai,MaKM',
            'TenKM'            => 'required|string|max:100',
            'MoTa'             => 'nullable|string|max:200',
            'Diem'             => 'nullable|integer|min:0',
            'NgayBatDau'       => 'required|date',
            'NgayKetThuc'      => 'required|date|after_or_equal:NgayBatDau',
            'PhanTramGiamGia'  => 'required|numeric|between:0,100',
            'LoaiKM'           => 'sometimes|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return $this->error('Dữ liệu không hợp lệ', 422, $validator->errors()->toArray());
        }

        $payload = $validator->validated();
        $khuyenMai = DB::transaction(function () use ($payload) {
            $payload['MaKM'] = $payload['MaKM'] ?? $this->generatePromotionId();
            $payload['LoaiKM'] = $payload['LoaiKM'] ?? 0;

            return KhuyenMai::create($payload);
        });

        return response()->json([
            'message' => 'Tạo khuyến mãi thành công',
            'data' => $khuyenMai
        ], 201);
    }

    // 3. Xem chi tiết 1 khuyến mãi
    //get /api/khuyen-mai/id
    public function show($id)
    {
        $khuyenMai = KhuyenMai::with('khoKhuyenMai')->find($id);

        if (!$khuyenMai) {
            return $this->error('Không tìm thấy khuyến mãi', 404);
        }

        return $this->success($khuyenMai, 'Lấy chi tiết khuyến mãi thành công');
    }

   public function update(Request $request, $id)
        {
            $khuyenMai = KhuyenMai::find($id);

            if (!$khuyenMai) {
                return response()->json([
                    'message' => 'Không tìm thấy khuyến mãi'
                ], 404);
            }

            // Validate đầy đủ các field có thể update
            $validator = Validator::make($request->all(), [
                'TenKM' => 'sometimes|string|max:100',
                'MoTa' => 'sometimes|string|max:200',
                'Diem' => 'sometimes|integer|min:0',
                'NgayBatDau' => 'sometimes|date',
                'NgayKetThuc' => 'sometimes|date|after_or_equal:NgayBatDau',
                'PhanTramGiamGia' => 'sometimes|numeric|between:0,100',
                'LoaiKM' => 'sometimes|integer|in:0,1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            // Chỉ update field cho phép (anti-hack 💀)
            $khuyenMai->update($request->only([
                'TenKM',
                'MoTa',
                'Diem',
                'NgayBatDau',
                'NgayKetThuc',
                'PhanTramGiamGia',
                'LoaiKM'
            ]));

            return response()->json([
                'message' => 'Cập nhật thành công',
                'data' => $khuyenMai
            ], 200);
        }

    public function destroy($id)
    {
        $khuyenMai = KhuyenMai::find($id);

        if (!$khuyenMai) {
            return $this->error('Không tìm thấy khuyến mãi', 404);
        }

        $decision = $this->guard->canDelete($khuyenMai);
        if (!$decision['allowed']) {
            return $this->error($decision['message'], 409);
        }

        $khuyenMai->delete();

        return $this->success(null, 'Xóa khuyến mãi thành công');
    }

    public function getActivePromotions()
    {
        $today = Carbon::today();

        $active = KhuyenMai::where('NgayBatDau', '<=', $today)
            ->where('NgayKetThuc', '>=', $today)
            ->get();

        return $this->success($active, 'Lấy danh sách khuyến mãi còn hạn thành công');
    }

    private function generatePromotionId(): string
    {
        $latestId = KhuyenMai::where('MaKM', 'like', 'KM%')
            ->orderByRaw('CAST(SUBSTRING(MaKM, 3) AS UNSIGNED) DESC')
            ->lockForUpdate()
            ->value('MaKM');

        $nextNumber = $latestId ? ((int) substr($latestId, 2)) + 1 : 1;

        do {
            $candidate = 'KM' . str_pad((string) $nextNumber, 8, '0', STR_PAD_LEFT);
            $nextNumber++;
        } while (KhuyenMai::whereKey($candidate)->exists());

        return $candidate;
    }

    
}
