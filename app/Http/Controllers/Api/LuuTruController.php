<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LuuTru;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LuuTruController extends Controller
{
    private function relations(): array
    {
        return ['phong.loaiPhong', 'datPhong.khachHang'];
    }

    private function success($data = null, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    private function error(string $message = 'Error', int $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }

    public function index(Request $request)
    {
        $query = LuuTru::with($this->relations())->latest('MaLuuTru');

        if ($request->filled('MaDatPhong')) {
            $query->where('MaDatPhong', $request->input('MaDatPhong'));
        }

        if ($request->filled('MaPhong')) {
            $query->where('MaPhong', $request->input('MaPhong'));
        }

        return $this->success($query->get(), 'Lấy danh sách lưu trú thành công');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        $luuTru = LuuTru::create($data);

        return $this->success(
            LuuTru::with($this->relations())->find($luuTru->MaLuuTru),
            'Tạo thông tin lưu trú thành công',
            201
        );
    }

    public function show($id)
    {
        $luuTru = LuuTru::with($this->relations())->find($id);

        if (!$luuTru) {
            return $this->error('Không tìm thấy thông tin lưu trú', 404);
        }

        return $this->success($luuTru, 'Lấy chi tiết lưu trú thành công');
    }

    public function update(Request $request, $id)
    {
        $luuTru = LuuTru::find($id);

        if (!$luuTru) {
            return $this->error('Không tìm thấy thông tin lưu trú', 404);
        }

        $data = $request->validate($this->rules(true));
        $luuTru->update($data);

        return $this->success(
            LuuTru::with($this->relations())->find($id),
            'Cập nhật thông tin lưu trú thành công'
        );
    }

    public function destroy($id)
    {
        $luuTru = LuuTru::find($id);

        if (!$luuTru) {
            return $this->error('Không tìm thấy thông tin lưu trú', 404);
        }

        $luuTru->delete();

        return $this->success(null, 'Đã xóa thông tin lưu trú');
    }

    private function rules(bool $isUpdate = false): array
    {
        $required = $isUpdate ? 'sometimes' : 'required';

        return [
            'TenKhach' => [$required, 'string', 'max:100'],
            'NgaySinh' => [$required, 'date'],
            'CCCD' => [$required, 'string', 'max:20'],
            'SoDienThoai' => ['nullable', 'string', 'max:15'],
            'MaPhong' => [
                $required,
                Rule::exists('Phong', 'MaPhong')->whereNull('deleted_at'),
            ],
            'MaDatPhong' => [
                $required,
                Rule::exists('DatPhong', 'MaDatPhong'),
            ],
        ];
    }
}
