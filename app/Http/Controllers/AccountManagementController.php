<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\TaiKhoanController;
use App\Http\Requests\ManagedAccountRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AccountManagementController extends Controller
{
    public function store(ManagedAccountRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $payload = $this->mapAccountPayload($request->validated(), false);
            $response = app(TaiKhoanController::class)->store(
                $this->buildApiRequest('/api/tai-khoan', 'POST', $payload)
            );

            $account = $this->extractAccountFromResponse($response);

            return response()->json([
                'message' => 'Tạo tài khoản thành công.',
                'data' => $this->reloadAccount((int) $account['MaTK']),
            ], 201);
        });
    }

    public function update(ManagedAccountRequest $request, $recordId)
    {
        return DB::transaction(function () use ($request, $recordId) {
            $payload = $this->mapAccountPayload($request->validated(), true);
            $response = app(TaiKhoanController::class)->update(
                $this->buildApiRequest("/api/tai-khoan/{$recordId}", 'PUT', $payload),
                $recordId
            );

            $account = $this->extractAccountFromResponse($response);

            return response()->json([
                'message' => 'Cập nhật tài khoản thành công.',
                'data' => $this->reloadAccount((int) ($account['MaTK'] ?? $recordId)),
            ], 200);
        });
    }

    private function mapAccountPayload(array $validated, bool $isEdit): array
    {
        $payload = [
            'Email' => $validated['Email'],
            'LoaiTaiKhoan' => $validated['LoaiTaiKhoan'],
            'TrangThai' => $validated['TrangThai'],
            'MaKH' => (int) $validated['LoaiTaiKhoan'] === 0 ? ($validated['customer_id'] ?? null) : null,
            'MaNV' => (int) $validated['LoaiTaiKhoan'] === 0 ? null : ($validated['employee_id'] ?? null),
        ];

        if (!$isEdit || !empty($validated['MatKhau'])) {
            $payload['MatKhau'] = $validated['MatKhau'];
        }

        return $payload;
    }

    private function buildApiRequest(string $uri, string $method, array $payload): Request
    {
        $request = Request::create($uri, $method, $payload);
        $request->headers->set('Accept', 'application/json');

        return $request;
    }

    private function extractAccountFromResponse($response): array
    {
        $statusCode = $response->getStatusCode();
        $payload = $response->getData(true);

        if ($statusCode === 422) {
            $validator = Validator::make([], []);
            foreach (($payload['errors'] ?? []) as $field => $messages) {
                foreach ((array) $messages as $message) {
                    $validator->errors()->add($field, $message);
                }
            }

            throw new HttpResponseException(response()->json([
                'errors' => $validator->errors(),
            ], 422));
        }

        if ($statusCode >= 400) {
            throw new HttpResponseException(response()->json([
                'message' => $payload['message'] ?? 'Không thể lưu tài khoản.',
            ], $statusCode));
        }

        return $payload['data'] ?? [];
    }

    private function reloadAccount(int $accountId)
    {
        return \App\Models\TaiKhoan::with(['khachHang', 'nhanVien'])->find($accountId);
    }
}
