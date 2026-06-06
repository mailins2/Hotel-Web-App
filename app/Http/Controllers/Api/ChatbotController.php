<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChiTietDatPhong;
use App\Models\DatPhong;
use App\Models\DichVu;
use App\Models\KhuyenMai;
use App\Models\LoaiPhong;
use App\Models\Phong;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function message(Request $request)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:1', 'max:600'],
        ]);

        $provider = strtolower((string) config('services.ai.provider', 'gemini'));
        $apiKey = $provider === 'openai'
            ? config('services.openai.key')
            : config('services.gemini.key');

        if (blank($apiKey)) {
            return response()->json([
                'success' => true,
                'answer' => 'Mình đã nhận câu hỏi của bạn, nhưng tính năng AI chưa được cấu hình. Bạn có thể hỏi các câu về đặt phòng, nhận/trả phòng, thanh toán, khuyến mãi, dịch vụ hoặc liên hệ khách sạn.',
            ]);
        }

        try {
            $answer = $provider === 'openai'
                ? $this->askOpenAi($validated['message'], $apiKey)
                : $this->askGemini($validated['message'], $apiKey);

            return response()->json([
                'success' => true,
                'answer' => $answer !== ''
                    ? $answer
                    : 'Mình chưa có đủ thông tin để trả lời chính xác. Bạn có thể hỏi rõ hơn hoặc liên hệ khách sạn để được hỗ trợ.',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Customer chatbot AI exception', [
                'provider' => $provider,
                'message' => $e->getMessage(),
            ]);

            if (str_contains($e->getMessage(), 'AI_RATE_LIMIT')) {
                return response()->json([
                    'success' => true,
                    'answer' => 'Hiện tại AI đang bị giới hạn lượt phản hồi trong thời gian ngắn. Bạn vui lòng thử lại sau khoảng 1 phút, hoặc chọn các câu hỏi gợi ý để mình trả lời nhanh bằng dữ liệu có sẵn.',
                ]);
            }

            return response()->json([
                'success' => true,
                'answer' => 'Hiện tại mình chưa kết nối được AI. Bạn có thể hỏi lại sau hoặc liên hệ Peach Valley qua số 0987098120 để được hỗ trợ nhanh hơn.',
            ]);
        }
    }

    private function askGemini(string $message, string $apiKey): string
    {
        $model = config('services.gemini.model', 'gemini-1.5-flash');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        $response = Http::acceptJson()
            ->timeout(25)
            ->withQueryParameters(['key' => $apiKey])
            ->post($url, [
                'systemInstruction' => [
                    'parts' => [
                        ['text' => $this->systemPrompt()],
                    ],
                ],
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            [
                                'text' => "Dữ liệu tham khảo của hệ thống:\n" . $this->systemContext($message)
                                    . "\n\nCâu hỏi của khách hàng: " . $message,
                            ],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.65,
                ],
            ]);

        if ($response->failed()) {
            Log::warning('Customer chatbot Gemini request failed', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            if ($response->status() === 429) {
                throw new \RuntimeException('AI_RATE_LIMIT: Gemini quota or rate limit exceeded.');
            }

            throw new \RuntimeException('Gemini request failed.');
        }

        $parts = $response->json('candidates.0.content.parts', []);

        if (!is_array($parts)) {
            return '';
        }

        return trim(collect($parts)
            ->pluck('text')
            ->filter()
            ->implode("\n"));
    }

    private function askOpenAi(string $message, string $apiKey): string
    {
        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(25)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => config('services.openai.model', 'gpt-4o-mini'),
                'temperature' => 0.65,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->systemPrompt(),
                    ],
                    [
                        'role' => 'user',
                        'content' => "Dữ liệu tham khảo của hệ thống:\n" . $this->systemContext($message)
                            . "\n\nCâu hỏi của khách hàng: " . $message,
                    ],
                ],
            ]);

        if ($response->failed()) {
            Log::warning('Customer chatbot OpenAI request failed', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            if ($response->status() === 429) {
                throw new \RuntimeException('AI_RATE_LIMIT: OpenAI quota or rate limit exceeded.');
            }

            throw new \RuntimeException('OpenAI request failed.');
        }

        return trim((string) $response->json('choices.0.message.content', ''));
    }

    private function systemPrompt(): string
    {
        return implode("\n", [
            'Bạn là chatbot hỗ trợ khách hàng của khách sạn Peach Valley.',
            'Luôn trả lời bằng tiếng Việt có dấu, giọng thân thiện, ngắn gọn và rõ ràng.',
            'Văn phong nên tự nhiên như nhân viên lễ tân đang hỗ trợ khách: ấm áp, mềm mại, không khô cứng như văn bản quy định.',
            'Có thể xưng "mình" hoặc "Peach Valley" tùy ngữ cảnh; ưu tiên cách nói gần gũi như "Dạ, trường hợp này..." hoặc "Bạn có thể..." khi phù hợp.',
            'Tránh lặp lại nguyên văn dữ liệu hệ thống; hãy diễn đạt lại thành câu dễ hiểu cho khách.',
            'Trả lời thành câu hoàn chỉnh, không bỏ dở câu ở cuối phản hồi.',
            'Định dạng câu trả lời dễ đọc: đoạn mở đầu ngắn, sau đó xuống dòng; nếu có nhiều ý thì dùng danh sách ngắn với dấu "- " hoặc "1. 2. 3.".',
            'Không dùng Markdown trang trí như **in đậm**, bảng, tiêu đề lớn hoặc ký hiệu phức tạp.',
            'Chỉ tư vấn, giải thích và hướng dẫn thao tác trên website; không tự tạo, sửa, hủy đặt phòng hoặc xác nhận thanh toán thay khách.',
            'Trang "Phòng" chỉ dùng để xem và lọc danh sách loại phòng theo tên, giá, sức chứa hoặc tiện nghi; không dùng để kiểm tra phòng trống theo ngày.',
            'Muốn kiểm tra phòng trống theo ngày, hãy hướng dẫn khách dùng form tìm kiếm ở trang chủ hoặc trang kết quả đặt phòng /customer/rooms-booking với ngày nhận, ngày trả, số khách và số phòng.',
            'Nếu khách hỏi còn phòng trống không nhưng chưa cung cấp ngày nhận, ngày trả và số khách, hãy hỏi lại các thông tin đó; không khẳng định còn phòng khi chưa có dữ liệu cụ thể.',
            'Nếu dữ liệu tham khảo có mục truy_van_phong_trong, phải ưu tiên kết quả truy vấn thật đó khi trả lời.',
            'Nếu dữ liệu tham khảo có mục kien_thuc_khach_hang, hãy dùng nó để trả lời các câu hỏi về chính sách, điều kiện đặt phòng, hủy phòng, thanh toán, tích điểm, khuyến mãi và dịch vụ.',
            'Nếu khách hỏi thông tin không có trong dữ liệu, hãy nói chưa có thông tin chính xác và hướng dẫn khách liên hệ khách sạn.',
            'Nếu khách hỏi về tình trạng đặt phòng cá nhân, hóa đơn hoặc thông tin riêng tư, hãy nhắc khách đăng nhập và xem trong tài khoản hoặc liên hệ khách sạn.',
            'Thông tin liên hệ: 0987098120, info@peachvalley.vn, 26K đường Yersin, Đà Lạt, Lâm Đồng.',
        ]);
    }

    private function hotelContext(): string
    {
        $today = Carbon::today()->toDateString();

        $rooms = LoaiPhong::with('khuyenMai')
            ->select(['MaLoaiPhong', 'TenLoaiPhong', 'Mota', 'NguoiLon', 'TreEm', 'GiaPhong', 'MaKM'])
            ->orderBy('MaLoaiPhong')
            ->limit(12)
            ->get()
            ->map(function (LoaiPhong $room) {
                return [
                    'loai_phong' => $room->TenLoaiPhong,
                    'suc_chua' => trim(((int) $room->NguoiLon) . ' người lớn, ' . ((int) $room->TreEm) . ' trẻ em'),
                    'gia_goc' => number_format((float) $room->GiaPhong, 0, ',', '.') . ' VND/đêm',
                    'gia_hien_tai' => number_format((float) $room->GiaGiam, 0, ',', '.') . ' VND/đêm',
                    'mo_ta' => $room->Mota,
                ];
            })
            ->values()
            ->all();

        $services = DichVu::select(['TenDV', 'GiaDV', 'LoaiDV'])
            ->orderBy('LoaiDV')
            ->orderBy('MaDV')
            ->limit(20)
            ->get()
            ->map(fn (DichVu $service) => [
                'ten_dich_vu' => $service->TenDV,
                'nhom' => $service->LoaiDVText,
                'gia' => number_format((float) $service->GiaDV, 0, ',', '.') . ' VND',
            ])
            ->values()
            ->all();

        $promotions = KhuyenMai::select(['MaKM', 'TenKM', 'PhanTramGiamGia', 'NgayBatDau', 'NgayKetThuc'])
            ->whereDate('NgayBatDau', '<=', $today)
            ->whereDate('NgayKetThuc', '>=', $today)
            ->orderByDesc('PhanTramGiamGia')
            ->limit(10)
            ->get()
            ->map(fn (KhuyenMai $promotion) => [
                'ma' => $promotion->MaKM,
                'ten' => $promotion->TenKM,
                'giam' => ((float) $promotion->PhanTramGiamGia) . '%',
                'hieu_luc' => $promotion->NgayBatDau . ' - ' . $promotion->NgayKetThuc,
            ])
            ->values()
            ->all();

        return json_encode([
            'ngay_hien_tai' => $today,
            'gio_nhan_phong' => '14:00',
            'gio_tra_phong' => '12:00',
            'trang_phong' => 'Trang Phòng chỉ hiển thị danh sách loại phòng và cho lọc theo tên, mô tả, tiện nghi, giá, sức chứa; trang này không kiểm tra phòng trống theo ngày.',
            'cach_tim_phong_trong' => 'Dùng form tìm kiếm ở trang chủ hoặc trang /customer/rooms-booking, nhập ngày nhận, ngày trả, số phòng và số khách, sau đó bấm Tìm kiếm.',
            'cach_dat_phong' => 'Sau khi tìm kiếm ở trang kết quả đặt phòng, chọn số lượng phòng phù hợp rồi bấm Đặt ngay để sang trang nhập thông tin đặt phòng.',
            'loai_phong' => $rooms,
            'dich_vu' => $services,
            'khuyen_mai_dang_ap_dung' => $promotions,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function systemContext(string $message): string
    {
        $context = json_decode($this->hotelContext(), true) ?: [];

        $context['flow_he_thong'] = [
            'customer_home' => 'Trang chủ có form chọn ngày nhận, ngày trả, số khách và số phòng để chuyển sang trang kết quả đặt phòng.',
            'customer_rooms' => 'Trang /customer/rooms chỉ xem danh sách loại phòng và lọc theo tên, mô tả, tiện nghi, giá, sức chứa; không kiểm tra phòng trống theo ngày.',
            'customer_rooms_booking' => 'Trang /customer/rooms-booking là nơi tìm phòng trống theo ngày nhận, ngày trả, số người lớn, trẻ em và số phòng. Trang này gọi API /api/phong/tim-kiem.',
            'customer_info_booking' => 'Trang /customer/info-booking là bước nhập thông tin và thanh toán sau khi khách đã chọn phòng.',
        ];

        $context['kien_thuc_khach_hang'] = $this->buildKnowledgeContext($message);

        $availability = $this->buildAvailabilityContext($message);

        if ($availability) {
            $context['truy_van_phong_trong'] = $availability;
        }

        return json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function buildKnowledgeContext(string $message): array
    {
        $knowledge = config('customer_knowledge', []);
        $normalized = $this->normalizeVietnamese($message);
        $selectedKeys = ['khach_san', 'bao_mat_rieng_tu'];

        $keywordMap = [
            'luong_dat_phong' => ['dat phong', 'tim phong', 'phong trong', 'chon phong', 'flow', 'quy trinh', 'lich su', 'xem lai', 'hom truoc', 'tai khoan', 'dang ky', 'dang nhap'],
            'chinh_sach_dat_phong' => ['chinh sach dat', 'dieu kien dat', 'dat coc', 'thanh toan truoc', 'giu phong', '15 phut'],
            'chinh_sach_huy_phong' => ['huy', 'cancel', 'hoan tien', 'phi huy'],
            'thanh_toan' => ['thanh toan', 'vnpay', 'zalopay', 'qr', 'hoa don'],
            'khuyen_mai_va_tich_diem' => ['tich diem', 'diem', 'voucher', 'khuyen mai', 'ma giam', 'doi diem'],
            'dich_vu' => ['dich vu', 'spa', 'giat ui', 'an uong', 'phuc vu'],
        ];

        foreach ($keywordMap as $key => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($normalized, $keyword)) {
                    $selectedKeys[] = $key;
                    break;
                }
            }
        }

        if (count($selectedKeys) === 2) {
            $selectedKeys = array_keys($knowledge);
        }

        return collect($selectedKeys)
            ->unique()
            ->filter(fn (string $key) => array_key_exists($key, $knowledge))
            ->mapWithKeys(fn (string $key) => [$key => $knowledge[$key]])
            ->all();
    }

    private function buildAvailabilityContext(string $message): ?array
    {
        if (!$this->isAvailabilityQuestion($message)) {
            return null;
        }

        $dates = $this->extractDates($message);
        $guestInfo = $this->extractGuestInfo($message);

        if (count($dates) < 2) {
            return [
                'co_hoi_phong_trong' => true,
                'du_thong_tin_de_truy_van' => false,
                'thieu' => ['ngay_nhan_phong', 'ngay_tra_phong'],
                'huong_dan' => 'Hỏi khách cung cấp ngày nhận, ngày trả, số người lớn, trẻ em và số phòng cần đặt.',
            ];
        }

        $checkIn = $dates[0];
        $checkOut = $dates[1];

        if ($checkOut->lessThanOrEqualTo($checkIn)) {
            return [
                'co_hoi_phong_trong' => true,
                'du_thong_tin_de_truy_van' => false,
                'loi' => 'Ngày trả phòng phải sau ngày nhận phòng.',
            ];
        }

        $adults = max((int) ($guestInfo['adults'] ?? 1), 1);
        $children = max((int) ($guestInfo['children'] ?? 0), 0);
        $rooms = max((int) ($guestInfo['rooms'] ?? 1), 1);

        $availableRooms = $this->queryAvailableRooms($checkIn, $checkOut, $adults, $children, $rooms);

        return [
            'co_hoi_phong_trong' => true,
            'du_thong_tin_de_truy_van' => true,
            'ngay_nhan_phong' => $checkIn->toDateString(),
            'ngay_tra_phong' => $checkOut->toDateString(),
            'so_dem' => $checkIn->diffInDays($checkOut),
            'nguoi_lon' => $adults,
            'tre_em' => $children,
            'so_phong_can_dat' => $rooms,
            'so_loai_phong_phu_hop' => count($availableRooms),
            'ket_qua' => $availableRooms,
            'url_tim_kiem' => url('/customer/rooms-booking?' . http_build_query([
                'checkIn' => $checkIn->toDateString(),
                'checkOut' => $checkOut->toDateString(),
                'NguoiLon' => $adults,
                'TreEm' => $children,
                'SoPhong' => $rooms,
            ])),
        ];
    }

    private function isAvailabilityQuestion(string $message): bool
    {
        $normalized = $this->normalizeVietnamese($message);

        return str_contains($normalized, 'phong')
            && (
                str_contains($normalized, 'trong')
                || str_contains($normalized, 'con')
                || str_contains($normalized, 'san')
                || str_contains($normalized, 'tim phong')
                || str_contains($normalized, 'dat phong')
            );
    }

    private function extractDates(string $message): array
    {
        $dates = [];
        $normalized = $this->normalizeVietnamese($message);

        if (preg_match('/\b(hom nay|today)\b/u', $normalized)) {
            $dates[] = Carbon::today()->startOfDay();
        }

        if (preg_match('/\b(ngay mai|mai|tomorrow)\b/u', $normalized)) {
            $dates[] = Carbon::tomorrow()->startOfDay();
        }

        preg_match_all('/\b(\d{1,2})[\/.-](\d{1,2})[\/.-](\d{4})\b/u', $message, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            try {
                $dates[] = Carbon::createFromFormat('d/m/Y', "{$match[1]}/{$match[2]}/{$match[3]}")->startOfDay();
            } catch (\Throwable $e) {
                // Ignore invalid date fragments.
            }
        }

        preg_match_all('/\b(\d{4})-(\d{1,2})-(\d{1,2})\b/u', $message, $isoMatches, PREG_SET_ORDER);

        foreach ($isoMatches as $match) {
            try {
                $dates[] = Carbon::createFromFormat('Y-m-d', "{$match[1]}-{$match[2]}-{$match[3]}")->startOfDay();
            } catch (\Throwable $e) {
                // Ignore invalid date fragments.
            }
        }

        preg_match_all('/\b(\d{1,2})[\/.-](\d{1,2})(?![\/.-]\d{2,4})\b/u', $message, $shortMatches, PREG_SET_ORDER);

        foreach ($shortMatches as $match) {
            try {
                $date = Carbon::createFromFormat('d/m/Y', "{$match[1]}/{$match[2]}/" . Carbon::today()->year)->startOfDay();

                if ($date->isPast()) {
                    $date->addYear();
                }

                $dates[] = $date;
            } catch (\Throwable $e) {
                // Ignore invalid date fragments.
            }
        }

        return collect($dates)
            ->unique(fn (Carbon $date) => $date->toDateString())
            ->values()
            ->all();
    }

    private function extractGuestInfo(string $message): array
    {
        $normalized = $this->normalizeVietnamese($message);
        $result = [
            'adults' => 1,
            'children' => 0,
            'rooms' => 1,
        ];

        if (preg_match('/(\d+)\s*(?:nguoi lon|ng lon|nl|adult)/u', $normalized, $match)) {
            $result['adults'] = (int) $match[1];
        } elseif (preg_match('/(\d+)\s*(?:nguoi|khach)/u', $normalized, $match)) {
            $result['adults'] = (int) $match[1];
        }

        if (preg_match('/(\d+)\s*(?:tre em|tre|child)/u', $normalized, $match)) {
            $result['children'] = (int) $match[1];
        }

        if (preg_match('/(\d+)\s*(?:phong|room)/u', $normalized, $match)) {
            $result['rooms'] = (int) $match[1];
        }

        return $result;
    }

    private function queryAvailableRooms(Carbon $checkIn, Carbon $checkOut, int $adults, int $children, int $rooms): array
    {
        $totalGuests = $adults + $children;

        return Phong::with([
            'loaiPhong' => function ($query) {
                $query->with(['khuyenMai', 'tienNghis']);
            },
        ])
            ->select('MaPhong', 'SoPhong', 'MaLoaiPhong')
            ->whereDoesntHave('chiTietDatPhong', function ($query) use ($checkIn, $checkOut) {
                $query
                    ->where('TrangThai', '!=', ChiTietDatPhong::CANCELLED)
                    ->whereHas('datPhong', function ($bookingQuery) use ($checkIn, $checkOut) {
                        $bookingQuery
                            ->where('NgayNhanPhong', '<', $checkOut->toDateString())
                            ->where('NgayTraPhong', '>', $checkIn->toDateString())
                            ->where(function ($statusQuery) {
                                $statusQuery
                                    ->whereIn('TinhTrang', [DatPhong::CONFIRMED, DatPhong::CHECKED_IN])
                                    ->orWhere(function ($holdQuery) {
                                        $holdQuery
                                            ->where('TinhTrang', DatPhong::HOLD)
                                            ->where('NgayDat', '>=', now()->subMinutes(15));
                                    });
                            });
                    });
            })
            ->get()
            ->groupBy('MaLoaiPhong')
            ->filter(function ($roomItems) use ($adults, $children, $rooms, $totalGuests) {
                $roomType = $roomItems->first()?->loaiPhong;

                if (!$roomType || $roomItems->count() < $rooms) {
                    return false;
                }

                $adultCapacity = max((int) $roomType->NguoiLon, 0);
                $childCapacity = max((int) $roomType->TreEm, 0);
                $totalCapacity = max($adultCapacity + $childCapacity, 1);

                return ($adultCapacity * $rooms) >= $adults
                    && ($childCapacity * $rooms) >= $children
                    && ($totalCapacity * $rooms) >= $totalGuests;
            })
            ->map(function ($roomItems) {
                $roomType = $roomItems->first()->loaiPhong;
                $currentPrice = (float) ($roomType->GiaGiam ?? $roomType->GiaPhong ?? 0);

                return [
                    'ma_loai_phong' => $roomType->MaLoaiPhong,
                    'ten_loai_phong' => $roomType->TenLoaiPhong,
                    'so_phong_trong' => $roomItems->count(),
                    'suc_chua_moi_phong' => [
                        'nguoi_lon' => (int) $roomType->NguoiLon,
                        'tre_em' => (int) $roomType->TreEm,
                    ],
                    'gia_hien_tai' => number_format($currentPrice, 0, ',', '.') . ' VND/đêm',
                    'khuyen_mai' => $roomType->khuyenMai
                        ? [
                            'ten' => $roomType->khuyenMai->TenKM,
                            'giam' => ((float) $roomType->khuyenMai->PhanTramGiamGia) . '%',
                        ]
                        : null,
                    'tien_nghi' => $roomType->tienNghis
                        ->pluck('TenTienNghi')
                        ->filter()
                        ->values()
                        ->take(8)
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }

    private function normalizeVietnamese(string $value): string
    {
        $normalized = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        return strtolower($normalized !== false ? $normalized : $value);
    }
}
