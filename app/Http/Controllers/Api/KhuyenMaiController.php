<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KhuyenMai;
use App\Services\Guards\KhuyenMaiSoftDeleteGuard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KhuyenMaiController extends Controller
{
    public function __construct(
        private KhuyenMaiSoftDeleteGuard $guard
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

    public function trash()
    {
        return $this->success(
            KhuyenMai::onlyTrashed()->with('khoKhuyenMai')->get(),
            'Lấy danh sách khuyến mãi trong thùng rác thành công'
        );
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'TenKM' => 'required|string|max:100',
            'MoTa' => 'nullable|string|max:200',
            'Diem' => 'required|integer|min:0',
            'NgayBatDau' => 'required|date',
            'NgayKetThuc' => 'required|date|after_or_equal:NgayBatDau',
            'PhanTramGiamGia' => 'required|numeric|between:0,100',
        ]);

        if ($validator->fails()) {
            return $this->error('Dữ liệu không hợp lệ', 422, $validator->errors()->toArray());
        }

        $khuyenMai = KhuyenMai::create($request->all());

        return $this->success($khuyenMai, 'Tạo khuyến mãi thành công', 201);
    }

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
            return $this->error('Không tìm thấy khuyến mãi', 404);
        }

        $validator = Validator::make($request->all(), [
            'TenKM' => 'sometimes|string|max:100',
            'MoTa' => 'sometimes|string|max:200',
            'Diem' => 'sometimes|integer|min:0',
            'NgayBatDau' => 'sometimes|date',
            'NgayKetThuc' => 'sometimes|date|after_or_equal:NgayBatDau',
            'PhanTramGiamGia' => 'sometimes|numeric|between:0,100',
        ]);

        if ($validator->fails()) {
            return $this->error('Dữ liệu không hợp lệ', 422, $validator->errors()->toArray());
        }

        $khuyenMai->update($request->only([
            'TenKM',
            'MoTa',
            'Diem',
            'NgayBatDau',
            'NgayKetThuc',
            'PhanTramGiamGia',
        ]));

        return $this->success($khuyenMai, 'Cập nhật khuyến mãi thành công');
    }

    public function destroy($id)
    {
        $khuyenMai = KhuyenMai::find($id);

        if (!$khuyenMai) {
            return $this->error('Không tìm thấy khuyến mãi', 404);
        }

        $decision = $this->guard->canSoftDelete($khuyenMai);
        if (!$decision['allowed']) {
            return $this->error($decision['message'], 409);
        }

        $khuyenMai->delete();

        return $this->success(null, 'Đã chuyển khuyến mãi vào thùng rác');
    }

    public function restore($id)
    {
        $khuyenMai = KhuyenMai::onlyTrashed()->find($id);

        if (!$khuyenMai) {
            return $this->error('Không tìm thấy khuyến mãi trong thùng rác', 404);
        }

        $khuyenMai->restore();

        return $this->success(
            KhuyenMai::with('khoKhuyenMai')->find($id),
            'Khôi phục khuyến mãi thành công'
        );
    }

    public function forceDelete($id)
    {
        $khuyenMai = KhuyenMai::onlyTrashed()->find($id);

        if (!$khuyenMai) {
            return $this->error('Không tìm thấy khuyến mãi trong thùng rác', 404);
        }

        $decision = $this->guard->canForceDelete($khuyenMai);
        if (!$decision['allowed']) {
            return $this->error($decision['message'], 409);
        }

        $khuyenMai->forceDelete();

        return $this->success(null, 'Xóa vĩnh viễn khuyến mãi thành công');
    }

    public function getActivePromotions()
    {
        $today = Carbon::today();

        $active = KhuyenMai::where('NgayBatDau', '<=', $today)
            ->where('NgayKetThuc', '>=', $today)
            ->get();

        return $this->success($active, 'Lấy danh sách khuyến mãi còn hạn thành công');
    }
}
