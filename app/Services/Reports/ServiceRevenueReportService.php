<?php

namespace App\Services\Reports;

use App\Models\DichVu;
use App\Models\SuDungDichVu;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ServiceRevenueReportService
{
    public function rows(string $from, string $to): Collection
    {
        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate = Carbon::parse($to)->endOfDay();

        $usages = SuDungDichVu::with(['dichVu', 'chiTietDatPhong'])
            ->whereBetween('ThoiGian', [$fromDate, $toDate])
            ->whereHas('dichVu')
            ->get();

        $totalRevenue = $usages->sum(function (SuDungDichVu $usage) {
            return (float) ($usage->SoLuong ?? 0) * (float) ($usage->dichVu?->GiaDV ?? 0);
        });

        return $usages
            ->groupBy('MaDV')
            ->map(function (Collection $items) use ($totalRevenue) {
                /** @var SuDungDichVu $first */
                $first = $items->first();
                $service = $first->dichVu;
                $quantity = $items->sum(fn (SuDungDichVu $usage) => (int) ($usage->SoLuong ?? 0));
                $unitPrice = (float) ($service?->GiaDV ?? 0);
                $revenue = $items->sum(function (SuDungDichVu $usage) {
                    return (float) ($usage->SoLuong ?? 0) * (float) ($usage->dichVu?->GiaDV ?? 0);
                });
                $bookingIds = $items
                    ->map(fn (SuDungDichVu $usage) => $usage->chiTietDatPhong?->MaDatPhong)
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values();

                return [
                    'service_id' => $service?->MaDV,
                    'service_name' => $service?->TenDV ?? 'Dịch vụ',
                    'service_type' => $this->serviceTypeLabel((int) ($service?->LoaiDV ?? 0)),
                    'unit_price' => round($unitPrice),
                    'total_quantity' => $quantity,
                    'revenue' => round($revenue),
                    'revenue_rate' => $totalRevenue > 0 ? round($revenue / $totalRevenue * 100, 2) : 0,
                    'usage_count' => $items->count(),
                    'booking_ids' => $bookingIds->isNotEmpty() ? $bookingIds->implode(', ') : 'Không có',
                ];
            })
            ->sortByDesc('revenue')
            ->values();
    }

    private function serviceTypeLabel(int $type): string
    {
        return match ($type) {
            DichVu::TYPE_FOOD_AND_BEVERAGE => 'Dịch vụ ăn uống',
            DichVu::TYPE_ROOM_SERVICE => 'Dịch vụ phòng',
            DichVu::TYPE_ENTERTAINMENT => 'Dịch vụ giải trí',
            default => 'Khác',
        };
    }
}
