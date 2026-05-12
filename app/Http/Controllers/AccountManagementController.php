<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\TaiKhoanController;
use App\Http\Requests\ManagedAccountRequest;
use App\Models\KhachHang;
use App\Models\NhanVien;
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
            $this->syncLinkedRecord(
                (int) $account['MaTK'],
                (int) $payload['LoaiTaiKhoan'],
                $request->integer('customer_id'),
                $request->integer('employee_id')
            );

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
            $this->syncLinkedRecord(
                (int) $recordId,
                (int) $payload['LoaiTaiKhoan'],
                $request->filled('customer_id') ? $request->integer('customer_id') : null,
                $request->filled('employee_id') ? $request->integer('employee_id') : null
            );

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

    private function syncLinkedRecord(int $accountId, int $accountType, ?int $customerId, ?int $employeeId): void
    {
        KhachHang::query()->where('MaTK', $accountId)->update(['MaTK' => null]);
        NhanVien::query()->where('MaTK', $accountId)->update(['MaTK' => null]);

        if ($accountType === 0) {
            if ($customerId === null) {
                return;
            }

            KhachHang::query()
                ->where('MaKH', $customerId)
                ->update(['MaTK' => $accountId]);

            return;
        }

        if ($employeeId === null) {
            return;
        }

        NhanVien::query()
            ->where('MaNV', $employeeId)
            ->update(['MaTK' => $accountId]);
    }

    private function reloadAccount(int $accountId)
    {
        return \App\Models\TaiKhoan::with(['khachHang', 'nhanVien'])->find($accountId);
    }
}
