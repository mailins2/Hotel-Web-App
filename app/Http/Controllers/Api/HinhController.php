<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hinh;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class HinhController extends Controller
{
    public function index()
    {
        $hinhs = Hinh::with(['loaiPhongs', 'dichVus', 'khuyenMai'])
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
                        $q->whereNotNull('MaKM')
                            ->whereHas('khuyenMai');
                    })
                    ->orWhere(function ($q) {
                        $q->whereNull('MaLoaiPhong')
                            ->whereNull('MaDV')
                            ->whereNull('MaKM');
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
            Log::error('Cloudinary image upload failed', [
                'message' => $exception->getMessage(),
                'MaLoaiPhong' => $request->input('MaLoaiPhong'),
                'MaDV' => $request->input('MaDV'),
                'MaKM' => $request->input('MaKM'),
            ]);

            return response()->json([
                'message' => app()->hasDebugModeEnabled()
                    ? 'Khong the upload hinh anh: ' . $exception->getMessage()
                    : 'Khong the upload hinh anh. Vui long kiem tra cau hinh Cloudinary hoac thu lai sau.',
            ], 500);
        }

        $hinh = Hinh::create($payload);

        return response()->json([
            'message' => 'Them hinh anh thanh cong',
            'data' => $hinh->load(['loaiPhongs', 'dichVus', 'khuyenMai']),
        ], 201);
    }

    public function show($id)
    {
        $hinh = Hinh::with(['loaiPhongs', 'dichVus', 'khuyenMai'])->find($id);

        if (! $hinh) {
            return response()->json(['message' => 'Khong tim thay hinh anh'], 404);
        }

        return response()->json($hinh, 200);
    }

    public function update(Request $request, $id)
    {
        $hinh = Hinh::find($id);

        if (! $hinh) {
            return response()->json(['message' => 'Khong tim thay hinh anh'], 404);
        }

        $validator = $this->makeValidator($request, true, $hinh);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $payload = $this->buildPayload($request, $hinh);
        } catch (\Throwable $exception) {
            Log::error('Cloudinary image update failed', [
                'message' => $exception->getMessage(),
                'image_id' => $hinh->Id,
                'MaLoaiPhong' => $request->input('MaLoaiPhong'),
                'MaDV' => $request->input('MaDV'),
                'MaKM' => $request->input('MaKM'),
            ]);

            return response()->json([
                'message' => app()->hasDebugModeEnabled()
                    ? 'Khong the upload hinh anh: ' . $exception->getMessage()
                    : 'Khong the upload hinh anh. Vui long kiem tra cau hinh Cloudinary hoac thu lai sau.',
            ], 500);
        }

        $hinh->update($payload);

        return response()->json([
            'message' => 'Cap nhat hinh anh thanh cong',
            'data' => $hinh->fresh(['loaiPhongs', 'dichVus', 'khuyenMai']),
        ], 200);
    }

    public function destroy($id)
    {
        $hinh = Hinh::find($id);

        if (! $hinh) {
            return response()->json(['message' => 'Khong tim thay hinh anh'], 404);
        }

        $hinh->delete();

        return response()->json(['message' => 'Da xoa hinh anh'], 200);
    }

    private function makeValidator(Request $request, bool $isUpdate, ?Hinh $currentImage = null)
    {
        $validator = Validator::make($request->all(), [
            'Url' => [$isUpdate ? 'sometimes' : 'nullable', 'nullable', 'string', 'max:500'],
            'image' => [$isUpdate ? 'sometimes' : 'nullable', 'nullable', 'image', 'max:5120'],
            'MaLoaiPhong' => [$isUpdate ? 'sometimes' : 'nullable', 'nullable', Rule::exists('LoaiPhong', 'MaLoaiPhong')],
            'MaDV' => [$isUpdate ? 'sometimes' : 'nullable', 'nullable', Rule::exists('DichVu', 'MaDV')],
            'MaKM' => [$isUpdate ? 'sometimes' : 'nullable', 'nullable', 'string', 'max:10', Rule::exists('KhuyenMai', 'MaKM')],
        ]);

        $validator->after(function ($validator) use ($request, $isUpdate, $currentImage) {
            $hasUrl = is_string($request->input('Url')) && trim($request->input('Url')) !== '';
            $hasFile = $request->hasFile('image');
            $hasOwner = $request->filled('MaLoaiPhong')
                || $request->filled('MaDV')
                || $request->filled('MaKM')
                || ($isUpdate && (
                    ! empty($currentImage?->MaLoaiPhong)
                    || ! empty($currentImage?->MaDV)
                    || ! empty($currentImage?->MaKM)
                ));

            if (! $isUpdate && ! $hasUrl && ! $hasFile) {
                $validator->errors()->add('image', 'Vui long cung cap file anh hoac URL.');
            }

            if (! $hasOwner) {
                $validator->errors()->add('MaLoaiPhong', 'Vui long lien ket anh voi loai phong, dich vu hoac khuyen mai.');
            }
        });

        return $validator;
    }

    private function buildPayload(Request $request, ?Hinh $currentImage = null): array
    {
        $payload = [];

        foreach (['MaLoaiPhong', 'MaDV', 'MaKM'] as $field) {
            if ($request->has($field)) {
                $payload[$field] = $request->input($field) ?: null;
            } elseif (! $currentImage) {
                $payload[$field] = null;
            }
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
        $maKM = $request->input('MaKM') ?: $currentImage?->MaKM;

        return match (true) {
            ! empty($maLoaiPhong) => 'hotel-web-app/room-types',
            ! empty($maDV) => 'hotel-web-app/services',
            ! empty($maKM) => 'hotel-web-app/promotions',
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
