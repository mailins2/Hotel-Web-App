<?php

namespace App\Http\Controllers\Api\ZaloPay;

use App\Http\Controllers\Controller;
use App\Models\DatPhong;
use App\Models\HoaDon;
use App\Models\ThanhToan;
use App\Services\CustomerPointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        try {
            $data = $request->validate([
                'amount' => ['required', 'integer', 'min:1000'],
                'app_user' => ['required', 'string', 'max:50'],
                'description' => ['required', 'string', 'max:256'],
                'redirect_url' => ['nullable', 'url'],
                'app_trans_id' => ['nullable', 'string', 'max:40'],
                'dat_phong_ids' => ['required', 'array', 'min:1'],
                'dat_phong_ids.*' => ['integer', 'exists:DatPhong,MaDatPhong'],
            ]);

            $appId = config('services.zalopay.app_id');
            $key1 = config('services.zalopay.key1');
            $endpoint = config('services.zalopay.endpoint');

            if (!$appId || !$key1 || !$endpoint) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Thieu cau hinh ZaloPay trong .env',
                ], 500);
            }

            $appTime = (int) round(microtime(true) * 1000);
            $appTransId = $data['app_trans_id'] ?? date('ymd') . '_' . uniqid();
            $datPhongIds = array_values(array_unique($data['dat_phong_ids']));

            $embedData = json_encode([
                'redirecturl' => $data['redirect_url'] ?? url('/customer/my-bookings'),
                'preferred_payment_method' => ['vietqr'],
                'dat_phong_ids' => $datPhongIds,
                'amount' => $data['amount'],
            ], JSON_UNESCAPED_SLASHES);
            $item = json_encode([], JSON_UNESCAPED_SLASHES);
            $callbackUrl = config('services.zalopay.callback_url') ?: url('/api/zalopay-callback');

            if (!filter_var($callbackUrl, FILTER_VALIDATE_URL)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ZALOPAY_CALLBACK_URL khong hop le, vi du https://abc123.ngrok-free.app/api/zalopay-callback',
                    'callback_url' => $callbackUrl,
                ], 500);
            }

            $macInput = $appId . '|' . $appTransId . '|' . $data['app_user'] . '|' . $data['amount'] . '|' . $appTime . '|' . $embedData . '|' . $item;
            $mac = hash_hmac('sha256', $macInput, $key1);

            $params = [
                'app_id' => (int) $appId,
                'app_user' => $data['app_user'],
                'app_trans_id' => $appTransId,
                'app_time' => $appTime,
                'amount' => $data['amount'],
                'item' => $item,
                'embed_data' => $embedData,
                'description' => $data['description'],
                'bank_code' => '',
                'callback_url' => $callbackUrl,
                'mac' => $mac,
            ];

            Cache::put("zalopay:dat_phong_ids:{$appTransId}", [
                'dat_phong_ids' => $datPhongIds,
                'amount' => $data['amount'],
                'callback_url' => $callbackUrl,
            ], now()->addHours(2));

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post($endpoint, $params);

            if ($response->failed()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Khong the ket noi den he thong ZaloPay',
                ], 500);
            }

            $result = $response->json();

            if (($result['return_code'] ?? null) == 1) {
                return response()->json([
                    'status' => 'success',
                    'order_url' => $result['order_url'] ?? null,
                    'qr_code' => $result['qr_code'] ?? null,
                    'order_token' => $result['order_token'] ?? null,
                    'app_trans_id' => $appTransId,
                ]);
            }

            Log::warning('ZaloPay create payment failed', [
                'app_trans_id' => $appTransId,
                'callback_url' => $callbackUrl,
                'response' => $result,
            ]);

            return response()->json([
                'status' => 'fail',
                'message' => $result['return_message'] ?? 'Giao dich that bai',
                'sub_message' => $result['sub_return_message'] ?? '',
            ], 400);
        } catch (\Throwable $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }

            Log::error('ZaloPay Create Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        $key2 = config('services.zalopay.key2');
        $dataStr = $request->get('data');
        $requestMac = $request->get('mac');

        if (!$key2 || !$dataStr || !$requestMac) {
            Log::warning('ZaloPay callback missing required fields');

            return response()->json([
                'return_code' => -1,
                'return_message' => 'missing callback data',
            ]);
        }

        $mac = hash_hmac('sha256', $dataStr, $key2);

        if (strcmp($mac, $requestMac) !== 0) {
            return response()->json([
                'return_code' => -1,
                'return_message' => 'mac not equal',
            ]);
        }

        $data = json_decode($dataStr, true);
        if (!is_array($data)) {
            Log::warning('ZaloPay callback invalid JSON');

            return response()->json([
                'return_code' => -1,
                'return_message' => 'invalid callback data',
            ]);
        }

        $appTransId = $data['app_trans_id'] ?? null;
        $mapping = $appTransId ? Cache::get("zalopay:dat_phong_ids:{$appTransId}") : null;
        $embedData = json_decode($data['embed_data'] ?? '{}', true);

        if (!$mapping && is_array($embedData)) {
            $mapping = [
                'dat_phong_ids' => $embedData['dat_phong_ids'] ?? [],
                'amount' => $embedData['amount'] ?? $data['amount'] ?? null,
            ];

            Log::warning('ZaloPay callback recovered booking mapping from embed_data', [
                'app_trans_id' => $appTransId,
                'dat_phong_ids' => $mapping['dat_phong_ids'],
            ]);
        }

        $datPhongIds = $mapping['dat_phong_ids'] ?? [];

        Log::info('ZaloPay callback received', [
            'app_trans_id' => $appTransId,
            'zp_trans_id' => $data['zp_trans_id'] ?? null,
            'amount' => $data['amount'] ?? null,
        ]);

        if (!$appTransId || empty($datPhongIds)) {
            Log::warning('ZaloPay callback missing booking mapping', [
                'app_trans_id' => $appTransId,
            ]);

            return response()->json([
                'return_code' => -1,
                'return_message' => 'booking mapping not found',
            ]);
        }

        if (isset($data['amount']) && (int) $data['amount'] !== (int) ($mapping['amount'] ?? 0)) {
            Log::warning('ZaloPay callback amount mismatch', [
                'app_trans_id' => $appTransId,
                'callback_amount' => $data['amount'],
                'expected_amount' => $mapping['amount'] ?? null,
            ]);

            return response()->json([
                'return_code' => -1,
                'return_message' => 'amount mismatch',
            ]);
        }

        if (!empty($mapping['paid'])) {
            return response()->json([
                'return_code' => 1,
                'return_message' => 'success',
            ]);
        }

        try {
            DB::transaction(function () use ($datPhongIds, $mapping, $data, $appTransId) {
                $remainingAmount = (float) ($mapping['amount'] ?? 0);

                foreach ($datPhongIds as $id) {
                    $datPhong = DatPhong::with('khachHang.taiKhoan')
                        ->lockForUpdate()
                        ->find($id);

                    if (!$datPhong) {
                        throw new \RuntimeException("Booking {$id} not found");
                    }

                    if (!in_array((int) $datPhong->TinhTrang, [DatPhong::HOLD, DatPhong::CONFIRMED], true)) {
                        throw new \RuntimeException("Booking {$id} is not hold");
                    }

                    $hoaDon = HoaDon::where('MaDatPhong', $id)
                        ->lockForUpdate()
                        ->first();

                    if (!$hoaDon) {
                        throw new \RuntimeException("Invoice for booking {$id} not found");
                    }

                    $paymentAmount = min(
                        (float) ($mapping['amount'] ?? $hoaDon->TongTien),
                        max((float) $hoaDon->TongTien - (float) $hoaDon->DaThanhToan, 0)
                    );

                    if (count($datPhongIds) > 1) {
                        $paymentAmount = min($paymentAmount, $remainingAmount);
                    }

                    $maGiaoDich = count($datPhongIds) > 1 ? "{$appTransId}:{$id}" : $appTransId;
                    $paymentRecorded = ThanhToan::where('MaGiaoDich', $maGiaoDich)->exists();

                    if ($paymentRecorded) {
                        $paymentAmount = 0;
                    }

                    if ($paymentAmount > 0) {
                        ThanhToan::create([
                            'MaHD' => $hoaDon->MaHD,
                            'SoTien' => $paymentAmount,
                            'PhuongThuc' => 2,
                            'LoaiThanhToan' => 0,
                            'NhaCungCap' => 'ZaloPay',
                            'DinhDanhNguoiThanhToan' => $data['merchant_user_id']
                                ?? $data['zp_user_id']
                                ?? $data['app_user']
                                ?? null,
                            'MaGiaoDich' => $maGiaoDich,
                            'MaGiaoDichCongThanhToan' => isset($data['zp_trans_id']) ? (string) $data['zp_trans_id'] : null,
                            'TrangThaiGiaoDich' => 1,
                        ]);

                        app(CustomerPointService::class)->addPointsForPayment($hoaDon, $paymentAmount);
                    }

                    $remainingAmount = max($remainingAmount - $paymentAmount, 0);

                    $newPaidAmount = min((float) $hoaDon->TongTien, (float) $hoaDon->DaThanhToan + $paymentAmount);

                    $hoaDon->update([
                        'DaThanhToan' => $newPaidAmount,
                        'TrangThai' => $newPaidAmount >= (float) $hoaDon->TongTien ? 1 : 0,
                    ]);

                    if ((int) $datPhong->TinhTrang === DatPhong::HOLD) {
                        $datPhong->update(['TinhTrang' => DatPhong::CONFIRMED]);
                    }
                }
            });

            Cache::put("zalopay:dat_phong_ids:{$appTransId}", array_merge($mapping, [
                'paid' => true,
                'paid_at' => now()->toDateTimeString(),
            ]), now()->addHours(2));

        } catch (\Throwable $e) {
            Log::error('ZaloPay confirm booking failed', [
                'app_trans_id' => $appTransId,
                'dat_phong_ids' => $datPhongIds,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'return_code' => -1,
                'return_message' => 'confirm booking failed',
            ]);
        }

        return response()->json([
            'return_code' => 1,
            'return_message' => 'success',
        ]);
    }
}
