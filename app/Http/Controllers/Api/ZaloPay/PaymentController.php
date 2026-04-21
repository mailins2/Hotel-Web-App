<?php

namespace App\Http\Controllers\Api\ZaloPay;

use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        try {
            // 1. Lấy cấu hình từ hệ thống
            $app_id = env('ZALOPAY_APP_ID');
            $key1 = env('ZALOPAY_KEY1');
            $endpoint = env('ZALOPAY_ENDPOINT');

            // 2. Lấy dữ liệu trực tiếp từ Input (JSON gửi lên)
            // Ưu tiên lấy từ request, nếu không có mới dùng giá trị dự phòng
            $amount = (int)$request->input('amount');
            $app_user = $request->input('app_user');
            $description = $request->input('description');
            
            // app_time luôn phải lấy thời gian thực hiện tại (miligiây)
            $app_time = (int)round(microtime(true) * 1000);
            
            // app_trans_id: Ưu tiên lấy từ input nếu bạn muốn tự quản lý mã hóa đơn
            // Nếu không có, tự sinh theo format bắt buộc: yymmdd_uniqueid
            $app_trans_id = $request->input('app_trans_id', date("ymd") . "_" . uniqid());

            // 3. Xử lý embed_data và item
            // Bạn có thể gửi redirect_url từ phía Frontend lên để linh hoạt
            $embed_data = json_encode([
                'redirecturl' => $request->input('redirect_url', 'https://peach-valley-hotel.up.railway.app/login'),
                'preferred_payment_method' => ['vietqr'] // Ưu tiên hiển thị VietQR nếu bạn muốn
            ], JSON_UNESCAPED_SLASHES);
            
            $item = json_encode([], JSON_UNESCAPED_SLASHES);

            // 4. Tạo chữ ký MAC (Sử dụng chính xác các biến đã lấy ở trên)
            // Thứ tự quan trọng: app_id|app_trans_id|app_user|amount|app_time|embed_data|item
            $data = $app_id . "|" . $app_trans_id . "|" . $app_user . "|" . $amount . "|" . $app_time . "|" . $embed_data . "|" . $item;
            $mac = hash_hmac("sha256", $data, $key1);

            // 5. Gom dữ liệu gửi đi API ZaloPay
            $params = [
                "app_id" => (int)$app_id,
                "app_user" => $app_user,
                "app_trans_id" => $app_trans_id,
                "app_time" => $app_time,
                "amount" => $amount,
                "item" => $item,
                "embed_data" => $embed_data,
                "description" => $description,
                "bank_code" => $request->input('bank_code', ""), // Có thể truyền "vietqr" trực tiếp ở đây
                "mac" => $mac
            ];

            // 6. Gửi Request đến ZaloPay
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                            ->post($endpoint, $params);

            if ($response->failed()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể kết nối đến hệ thống ZaloPay'
                ], 500);
            }

            $result = $response->json();

            // 7. Trả kết quả về cho Frontend
            if (isset($result['return_code']) && $result['return_code'] == 1) {
                return response()->json([
                    'status' => 'success',
                    'order_url' => $result['order_url'], // Link để redirect người dùng
                    'app_trans_id' => $app_trans_id      // Trả lại ID để Frontend theo dõi
                ]);
            }

            return response()->json([
                'status' => 'fail',
                'message' => $result['return_message'] ?? 'Giao dịch thất bại',
                'sub_message' => $result['sub_return_message'] ?? ''
            ], 400);

        } catch (\Exception $e) {
            Log::error("ZaloPay Create Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function callback(Request $request) 
    {
        $key2 = env('ZALOPAY_KEY2');
        $dataStr = $request->get('data');
        $requestMac = $request->get('mac');

        // Xác thực dữ liệu từ ZaloPay gửi về
        $mac = hash_hmac("sha256", $dataStr, $key2);

        if (strcmp($mac, $requestMac) !== 0) {
            return response()->json(["return_code" => -1, "return_message" => "mac not equal"]);
        }

        // Dữ liệu thanh toán thành công
        $dataJson = json_decode($dataStr, true);
        
        // TODO: Viết code cập nhật Database của bạn tại đây
        // Ví dụ: Order::where('order_code', $dataJson['app_trans_id'])->update(['status' => 'paid']);

        return response()->json(["return_code" => 1, "return_message" => "success"]);
    }
}