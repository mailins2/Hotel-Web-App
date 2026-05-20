<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hinh;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class HinhController extends Controller
{
    public function index()
    {
        $hinhs = Hinh::with(['loaiPhongs', 'dichVus'])
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('MaLoaiPhong')
                        ->whereHas('loaiPhongs');
                })
                    ->orWhere(function ($q) {
                        $q->whereNotNull('MaDV')
                            ->whereHas('dichVus');
                    })
                    ->orWhere(function ($q) {
                        $q->whereNull('MaLoaiPhong')
                            ->whereNull('MaDV');
                    });
            })
            ->get();
        return response()->json($hinhs, 200);
    }

    public function store(Request $request)
    {
        $validator = $this->makeValidator($request, false);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $payload = $this->buildPayload($request);
        } catch (\Throwable $exception) {
            return response()->json([
                'message' => 'Khong the upload hinh anh. Vui long kiem tra cau hinh Cloudinary hoac thu lai sau.',
            ], 500);
        }

        $hinh = Hinh::create($payload);

        return response()->json([
            'message' => 'Thêm hình ảnh thành công',
            'data' => $hinh,
        ], 201);
    }

    public function show($id)
    {
        $hinh = Hinh::with(['loaiPhongs', 'dichVus'])->find($id);

        if (! $hinh) {
            return response()->json(['message' => 'Không tìm thấy hình ảnh'], 404);
        }

        return response()->json($hinh, 200);
    }

    public function update(Request $request, $id)
    {
        $hinh = Hinh::find($id);

        if (! $hinh) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }

        $validator = Validator::make($request->all(), [
            'Url'         => 'sometimes|string|max:500',
            'MaLoaiPhong' => [
                'nullable',
                Rule::exists('LoaiPhong', 'MaLoaiPhong'),
            ],
            'MaDV'        => [
                'nullable',
                Rule::exists('DichVu', 'MaDV'),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

                $validator = $this->makeValidator($request, true);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payload = $this->buildPayload($request, $hinh);
        $hinh->update($payload);

        return response()->json([
            'message' => 'Cập nhật thành công',
            'data' => $hinh->fresh(),
        ], 200);
    }

    public function destroy($id)
    {
        $hinh = Hinh::find($id);

        if (! $hinh) {
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }

        $hinh->delete();

        return response()->json(['message' => 'Đã xóa hình ảnh'], 200);
    }

    private function makeValidator(Request $request, bool $isUpdate)
    {
        $validator = Validator::make($request->all(), [
            'Url' => [$isUpdate ? 'sometimes' : 'nullable', 'nullable', 'string', 'max:500'],
            'image' => [$isUpdate ? 'sometimes' : 'nullable', 'nullable', 'image', 'max:5120'],
            'MaLoaiPhong' => [$isUpdate ? 'sometimes' : 'nullable', 'nullable', 'exists:LoaiPhong,MaLoaiPhong'],
            'MaDV' => [$isUpdate ? 'sometimes' : 'nullable', 'nullable', 'exists:DichVu,MaDV'],
        ]);

        $validator->after(function ($validator) use ($request, $isUpdate) {
            $hasUrl = is_string($request->input('Url')) && trim($request->input('Url')) !== '';
            $hasFile = $request->hasFile('image');

            if (! $isUpdate && ! $hasUrl && ! $hasFile) {
                $validator->errors()->add('image', 'Vui lòng cung cấp file ảnh hoặc URL.');
            }

            if (! $request->filled('MaLoaiPhong') && ! $request->filled('MaDV')) {
                $validator->errors()->add('MaLoaiPhong', 'Vui lòng liên kết ảnh với loại phòng hoặc dịch vụ.');
            }
        });

        return $validator;
    }

    private function buildPayload(Request $request, ?Hinh $currentImage = null): array
    {
        $payload = [];

        if ($request->has('MaLoaiPhong')) {
            $payload['MaLoaiPhong'] = $request->input('MaLoaiPhong') ?: null;
        } elseif (! $currentImage) {
            $payload['MaLoaiPhong'] = null;
        }

        if ($request->has('MaDV')) {
            $payload['MaDV'] = $request->input('MaDV') ?: null;
        } elseif (! $currentImage) {
            $payload['MaDV'] = null;
        }

        if ($request->hasFile('image')) {
            $uploaded = $this->uploadToCloudinary(
                $request->file('image'),
                $this->resolveUploadFolder($request, $currentImage)
            );

            $payload['Url'] = $uploaded['secure_url'];
        } elseif ($request->has('Url')) {
            $payload['Url'] = trim((string) $request->input('Url'));
        }

        return $payload;
    }

    private function resolveUploadFolder(Request $request, ?Hinh $currentImage = null): string
    {
        $maLoaiPhong = $request->input('MaLoaiPhong') ?: $currentImage?->MaLoaiPhong;
        $maDV = $request->input('MaDV') ?: $currentImage?->MaDV;

        return match (true) {
            ! empty($maLoaiPhong) => 'hotel-web-app/room-types',
            ! empty($maDV) => 'hotel-web-app/services',
            default => 'hotel-web-app/images',
        };
    }

    private function uploadToCloudinary(UploadedFile $file, string $folder): array
    {
        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
        ]);

        return $cloudinary->uploadApi()->upload($file->getRealPath(), [
            'folder' => $folder,
        ])->getArrayCopy();
    }
}
