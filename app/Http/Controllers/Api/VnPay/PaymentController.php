<?php

namespace App\Http\Controllers\Api\VnPay;

use App\Http\Controllers\Controller;
use App\Models\DatPhong;
use App\Models\HoaDon;
use App\Models\ThanhToan;
use App\Services\CustomerPointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1000'],
            'description' => ['required', 'string', 'max:255'],
            'redirect_url' => ['nullable', 'url'],
            'dat_phong_ids' => ['required', 'array', 'min:1'],
            'dat_phong_ids.*' => ['integer', 'exists:DatPhong,MaDatPhong'],
            'bank_code' => ['required', 'string', Rule::in(['VNBANK', 'INTCARD'])],
        ]);

        $tmnCode = config('services.vnpay.tmn_code');
        $hashSecret = config('services.vnpay.hash_secret');
        $paymentUrl = config('services.vnpay.payment_url');
        $returnUrl = config('services.vnpay.return_url') ?: url('/api/vnpay-return');

        if (!$tmnCode || !$hashSecret || !$paymentUrl) {
            return response()->json([
                'status' => 'error',
                'message' => 'Thieu cau hinh VNPAY trong .env',
            ], 500);
        }

        $datPhongIds = array_values(array_unique($data['dat_phong_ids']));
        $txnRef = $this->makeTxnRef($datPhongIds[0]);
        $now = now('Asia/Ho_Chi_Minh');

        $params = [
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'pay',
            'vnp_TmnCode' => $tmnCode,
            'vnp_Amount' => (string) ($data['amount'] * 100),
            'vnp_CurrCode' => 'VND',
            'vnp_TxnRef' => $txnRef,
            'vnp_OrderInfo' => $this->normalizeOrderInfo($data['description']),
            'vnp_OrderType' => 'other',
            'vnp_Locale' => 'vn',
            'vnp_ReturnUrl' => $returnUrl,
            'vnp_IpAddr' => $request->ip() ?: '127.0.0.1',
            'vnp_CreateDate' => $now->format('YmdHis'),
            'vnp_ExpireDate' => $now->copy()->addMinutes(15)->format('YmdHis'),
        ];

        $params['vnp_BankCode'] = $data['bank_code'];

        $secureHash = $this->makeSecureHash($params, $hashSecret);
        $paymentUrl = rtrim($paymentUrl, '?') . '?' . $this->buildQuery($params) . '&vnp_SecureHash=' . $secureHash;

        Cache::put("vnpay:txn:{$txnRef}", [
            'dat_phong_ids' => $datPhongIds,
            'amount' => $data['amount'],
            'redirect_url' => $data['redirect_url'] ?? url('/customer'),
        ], now()->addHours(2));

        return response()->json([
            'status' => 'success',
            'payment_url' => $paymentUrl,
            'txn_ref' => $txnRef,
        ]);
    }

    public function ipn(Request $request)
    {
        $data = $request->query();
        $hashSecret = config('services.vnpay.hash_secret');

        if (!$hashSecret || !$this->isValidSignature($data, $hashSecret)) {
            return response()->json([
                'RspCode' => '97',
                'Message' => 'Invalid signature',
            ]);
        }

        $txnRef = $data['vnp_TxnRef'] ?? null;
        $mapping = $txnRef ? Cache::get("vnpay:txn:{$txnRef}") : null;

        if (!$txnRef || !$mapping || empty($mapping['dat_phong_ids'])) {
            return response()->json([
                'RspCode' => '01',
                'Message' => 'Order not found',
            ]);
        }

        if ((int) ($data['vnp_Amount'] ?? 0) !== (int) (($mapping['amount'] ?? 0) * 100)) {
            return response()->json([
                'RspCode' => '04',
                'Message' => 'Invalid amount',
            ]);
        }

        if (($data['vnp_ResponseCode'] ?? null) !== '00' || ($data['vnp_TransactionStatus'] ?? null) !== '00') {
            return response()->json([
                'RspCode' => '00',
                'Message' => 'Payment failed ignored',
            ]);
        }

        try {
            $this->confirmBookings($mapping, $data, $txnRef);

            Cache::put("vnpay:txn:{$txnRef}", array_merge($mapping, [
                'paid' => true,
                'paid_at' => now()->toDateTimeString(),
            ]), now()->addHours(2));
        } catch (\Throwable $e) {
            Log::error('VNPAY confirm booking failed', [
                'txn_ref' => $txnRef,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'RspCode' => '99',
                'Message' => 'Unknown error',
            ]);
        }

        return response()->json([
            'RspCode' => '00',
            'Message' => 'Confirm Success',
        ]);
    }

    // public function return(Request $request)
    // {
    //     $data = $request->query();
    //     $txnRef = $data['vnp_TxnRef'] ?? null;
    //     $mapping = $txnRef ? Cache::get("vnpay:txn:{$txnRef}") : null;
    //     $redirectUrl = $mapping['redirect_url'] ?? url('/customer');
    //     $status = 'failed';

    //     if (config('services.vnpay.hash_secret') && !$this->isValidSignature($data, config('services.vnpay.hash_secret'))) {
    //         return redirect()->to($redirectUrl . (str_contains($redirectUrl, '?') ? '&' : '?') . 'vnpay=invalid');
    //     }

    //     $isSuccessful = ($data['vnp_ResponseCode'] ?? null) === '00'
    //         && (!isset($data['vnp_TransactionStatus']) || $data['vnp_TransactionStatus'] === '00');

    //     if ($isSuccessful) {
    //         if (!$txnRef || !$mapping || empty($mapping['dat_phong_ids'])) {
    //             $status = 'missing_order';
    //         } elseif ((int) ($data['vnp_Amount'] ?? 0) !== (int) (($mapping['amount'] ?? 0) * 100)) {
    //             $status = 'amount_mismatch';
    //         } else {
    //             try {
    //                 if (empty($mapping['paid'])) {
    //                     $this->confirmBookings($mapping, $data, $txnRef);

    //                     Cache::put("vnpay:txn:{$txnRef}", array_merge($mapping, [
    //                         'paid' => true,
    //                         'paid_at' => now()->toDateTimeString(),
    //                     ]), now()->addHours(2));
    //                 }

    //                 $status = 'success';
    //             } catch (\Throwable $e) {
    //                 Log::error('VNPAY return confirm booking failed', [
    //                     'txn_ref' => $txnRef,
    //                     'message' => $e->getMessage(),
    //                 ]);

    //                 $status = 'confirm_failed';
    //             }
    //         }
    //     }

    //     $separator = str_contains($redirectUrl, '?') ? '&' : '?';
    //     $query = http_build_query([
    //         'vnpay' => $status,
    //         'txn_ref' => $txnRef,
    //     ]);

    //     return redirect()->to($redirectUrl . $separator . $query);
    // }
    //==================
    public function return(Request $request)
    {
        $data = $request->query();
        $txnRef = $data['vnp_TxnRef'] ?? null;
        $mapping = $txnRef ? Cache::get("vnpay:txn:{$txnRef}") : null;
        $redirectUrl = $mapping['redirect_url'] ?? url('/customer');
        $status = 'failed';

        if (config('services.vnpay.hash_secret') && !$this->isValidSignature($data, config('services.vnpay.hash_secret'))) {
            return redirect()->to($redirectUrl . (str_contains($redirectUrl, '?') ? '&' : '?') . 'vnpay=invalid');
        }

        $isSuccessful = ($data['vnp_ResponseCode'] ?? null) === '00'
            && (!isset($data['vnp_TransactionStatus']) || $data['vnp_TransactionStatus'] === '00');

        if ($isSuccessful) {
            if (!$txnRef || !$mapping || empty($mapping['dat_phong_ids'])) {
                $status = 'missing_order';
            } elseif ((int) ($data['vnp_Amount'] ?? 0) !== (int) (($mapping['amount'] ?? 0) * 100)) {
                $status = 'amount_mismatch';
            } else {
                try {
                    if (empty($mapping['paid'])) {
                        $this->confirmBookings($mapping, $data, $txnRef);

                        Cache::put("vnpay:txn:{$txnRef}", array_merge($mapping, [
                            'paid' => true,
                            'paid_at' => now()->toDateTimeString(),
                        ]), now()->addHours(2));
                    }

                    $status = 'success';
                } catch (\Throwable $e) {
                    Log::error('VNPAY return confirm booking failed', [
                        'txn_ref' => $txnRef,
                        'message' => $e->getMessage(),
                    ]);

                    $status = 'confirm_failed';
                }
            }
        }

        // ✅ Tạo query string
        $query = http_build_query([
            'vnpay' => $status,
            'txn_ref' => $txnRef,
        ]);

        // ✅ Trả về trang web có script tự mở app
        return view('payment.vnpay-result', [
            'status' => $status,
            'txnRef' => $txnRef,
            'redirectUrl' => $redirectUrl . '?' . $query,
            'deepLink' => 'peachvalley://vnpay-result?status=' . $status . '&txn_ref=' . $txnRef,
        ]);
    }

    private function confirmBookings(array $mapping, array $data, string $txnRef): void
    {
        DB::transaction(function () use ($mapping, $data, $txnRef) {
            $datPhongIds = $mapping['dat_phong_ids'];
            $remainingAmount = (float) ($mapping['amount'] ?? 0);

            foreach ($datPhongIds as $id) {
                $datPhong = DatPhong::lockForUpdate()->find($id);

                if (!$datPhong) {
                    throw new \RuntimeException("Booking {$id} not found");
                }

                if (!in_array((int) $datPhong->TinhTrang, [DatPhong::HOLD, DatPhong::CONFIRMED], true)) {
                    throw new \RuntimeException("Booking {$id} is not payable");
                }

                $hoaDon = HoaDon::where('MaDatPhong', $id)->lockForUpdate()->first();

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

                $maGiaoDich = count($datPhongIds) > 1 ? "{$txnRef}:{$id}" : $txnRef;
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
                        'NhaCungCap' => 'VNPAY',
                        'DinhDanhNguoiThanhToan' => $data['vnp_BankCode'] ?? null,
                        'MaGiaoDich' => $maGiaoDich,
                        'MaGiaoDichCongThanhToan' => isset($data['vnp_TransactionNo']) ? (string) $data['vnp_TransactionNo'] : null,
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
    }

    private function makeTxnRef(int $bookingId): string
    {
        return 'DP' . $bookingId . now('Asia/Ho_Chi_Minh')->format('His') . random_int(100, 999);
    }

    private function normalizeOrderInfo(string $value): string
    {
        $normalized = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
        $normalized = preg_replace('/[^A-Za-z0-9 .:_-]/', '', $normalized) ?: 'Thanh toan dat phong Peach Valley';

        return substr($normalized, 0, 255);
    }

    private function isValidSignature(array $params, string $hashSecret): bool
    {
        $secureHash = $params['vnp_SecureHash'] ?? '';
        unset($params['vnp_SecureHash'], $params['vnp_SecureHashType']);

        return hash_equals($secureHash, $this->makeSecureHash($params, $hashSecret));
    }

    private function makeSecureHash(array $params, string $hashSecret): string
    {
        ksort($params);

        return hash_hmac('sha512', $this->buildQuery($params), $hashSecret);
    }

    private function buildQuery(array $params): string
    {
        ksort($params);

        return collect($params)
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->map(fn ($value, $key) => urlencode($key) . '=' . urlencode((string) $value))
            ->implode('&');
    }
}
