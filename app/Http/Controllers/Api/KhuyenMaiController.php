<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hinh;
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
        return $this->success(KhuyenMai::with('hinhs')->get(), 'Lay danh sach khuyen mai thanh cong');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MaKM' => 'sometimes|required|string|max:10|unique:KhuyenMai,MaKM',
            'TenKM' => 'required|string|max:100',
            'MoTa' => 'nullable|string|max:200',
            'Diem' => 'nullable|integer|min:0',
            'NgayBatDau' => 'required|date',
            'NgayKetThuc' => 'required|date|after_or_equal:NgayBatDau',
            'PhanTramGiamGia' => 'required|numeric|between:0,100',
            'LoaiKM' => 'sometimes|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return $this->error('Du lieu khong hop le', 422, $validator->errors()->toArray());
        }

        $payload = $validator->validated();

        $khuyenMai = DB::transaction(function () use ($payload) {
            $payload['MaKM'] = $payload['MaKM'] ?? $this->generatePromotionId();
            $payload['LoaiKM'] = $payload['LoaiKM'] ?? 0;

            return KhuyenMai::create($payload)->load('hinhs');
        });

        return response()->json([
            'message' => 'Tao khuyen mai thanh cong',
            'data' => $khuyenMai,
        ], 201);
    }

    public function show($id)
    {
        $khuyenMai = KhuyenMai::with(['khoKhuyenMai', 'hinhs'])->find($id);

        if (!$khuyenMai) {
            return $this->error('Khong tim thay khuyen mai', 404);
        }

        return $this->success($khuyenMai, 'Lay chi tiet khuyen mai thanh cong');
    }

    public function update(Request $request, $id)
    {
        $khuyenMai = KhuyenMai::find($id);

        if (!$khuyenMai) {
            return $this->error('Khong tim thay khuyen mai', 404);
        }

        $validator = Validator::make($request->all(), [
            'TenKM' => 'sometimes|string|max:100',
            'MoTa' => 'sometimes|nullable|string|max:200',
            'Diem' => 'sometimes|nullable|integer|min:0',
            'NgayBatDau' => 'sometimes|date',
            'NgayKetThuc' => 'sometimes|date|after_or_equal:NgayBatDau',
            'PhanTramGiamGia' => 'sometimes|numeric|between:0,100',
            'LoaiKM' => 'sometimes|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return $this->error('Du lieu khong hop le', 422, $validator->errors()->toArray());
        }

        $khuyenMai->update($validator->validated());

        return response()->json([
            'message' => 'Cap nhat thanh cong',
            'data' => $khuyenMai->fresh('hinhs'),
        ], 200);
    }

    public function destroy($id)
    {
        $khuyenMai = KhuyenMai::find($id);

        if (!$khuyenMai) {
            return $this->error('Khong tim thay khuyen mai', 404);
        }

        $decision = $this->guard->canDelete($khuyenMai);
        if (!$decision['allowed']) {
            return $this->error($decision['message'], 409);
        }

        $khuyenMai->delete();

        return $this->success(null, 'Xoa khuyen mai thanh cong');
    }

    public function getActivePromotions()
    {
        $today = Carbon::today();

        $active = KhuyenMai::where('NgayBatDau', '<=', $today)
            ->where('NgayKetThuc', '>=', $today)
            ->with('hinhs')
            ->get();

        return $this->success($active, 'Lay danh sach khuyen mai con han thanh cong');
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
