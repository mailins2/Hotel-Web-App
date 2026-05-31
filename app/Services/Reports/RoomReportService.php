<?php

namespace App\Services\Reports;

use App\Models\ChiTietDatPhong;
use App\Models\LoaiPhong;
use App\Models\Phong;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RoomReportService
{
    public function rows(string $from, string $to): Collection
    {
        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate = Carbon::parse($to)->startOfDay();
        $endExclusive = $toDate->copy()->addDay();
        $daysInPeriod = max(1, $fromDate->diffInDays($endExclusive));

        $rows = Phong::with([
            'loaiPhong.khuyenMai',
            'chiTietDatPhong.datPhong.hoaDon',
        ])
            ->orderBy('MaLoaiPhong')
            ->orderBy('SoPhong')
            ->get()
            ->map(function (Phong $room) use ($fromDate, $endExclusive, $daysInPeriod) {
                $rentedDays = 0;
                $roomRevenue = 0;
                $bookingIds = [];

                $room->chiTietDatPhong
                    ->filter(fn ($detail) => (int) ($detail->TrangThai ?? -1) !== ChiTietDatPhong::CANCELLED)
                    ->each(function (ChiTietDatPhong $detail) use ($fromDate, $endExclusive, &$rentedDays, &$roomRevenue, &$bookingIds, $room) {
                        $booking = $detail->datPhong;

                        if (!$booking || !$booking->NgayNhanPhong || !$booking->NgayTraPhong) {
                            return;
                        }

                        $checkIn = Carbon::parse($booking->NgayNhanPhong)->startOfDay();
                        $checkOut = Carbon::parse($booking->NgayTraPhong)->startOfDay();

                        if ($checkIn >= $endExclusive || $checkOut <= $fromDate) {
                            return;
                        }

                        $start = $checkIn->greaterThan($fromDate) ? $checkIn : $fromDate;
                        $end = $checkOut->lessThan($endExclusive) ? $checkOut : $endExclusive;
                        $overlapDays = max(0, $start->diffInDays($end));

                        if ($overlapDays <= 0) {
                            return;
                        }

                        $bookingIds[$booking->MaDatPhong] = true;
                        $rentedDays += $overlapDays;
                        $roomRevenue += $overlapDays * (float) ($room->loaiPhong?->giaSauKhuyenMai($booking->NgayNhanPhong) ?? 0);
                    });

                return [
                    'room_id' => $room->MaPhong,
                    'room_number' => $room->SoPhong,
                    'room_type' => $room->loaiPhong?->TenLoaiPhong ?? 'Chưa phân loại',
                    'room_price' => round((float) ($room->loaiPhong?->GiaPhong ?? 0)),
                    'current_status' => $this->roomStatusLabel($this->currentRoomStatus($room)),
                    'booking_count' => count($bookingIds),
                    'rented_days' => $rentedDays,
                    'room_revenue' => round($roomRevenue),
                    'occupancy_rate' => round($rentedDays / $daysInPeriod * 100, 2),
                    'is_total' => false,
                ];
            });

        return $this->appendRoomTypeTotals($rows);
    }

    private function appendRoomTypeTotals(Collection $rows): Collection
    {
        return $rows
            ->groupBy('room_type')
            ->flatMap(function (Collection $items, string $roomType) {
                return $items->push([
                    'room_id' => 'Tổng cộng',
                    'room_number' => '',
                    'room_type' => $roomType,
                    'room_price' => '',
                    'current_status' => '',
                    'booking_count' => $items->sum('booking_count'),
                    'rented_days' => $items->sum('rented_days'),
                    'room_revenue' => $items->sum('room_revenue'),
                    'occupancy_rate' => '',
                    'is_total' => true,
                ]);
            })
            ->values();
    }

    private function roomTypePriceForPeriod(?LoaiPhong $roomType, Carbon $fromDate, Carbon $endExclusive): float
    {
        $basePrice = round((float) ($roomType?->GiaPhong ?? 0));
        $promotion = $roomType?->khuyenMai;

        if (!$roomType || !$promotion || !$promotion->NgayBatDau || !$promotion->NgayKetThuc) {
            return $basePrice;
        }

        $promotionStart = Carbon::parse($promotion->NgayBatDau)->startOfDay();
        $promotionEndExclusive = Carbon::parse($promotion->NgayKetThuc)->startOfDay()->addDay();

        if ($promotionStart >= $endExclusive || $promotionEndExclusive <= $fromDate) {
            return $basePrice;
        }

        return round($roomType->giaSauKhuyenMai($promotionStart));
    }

    private function roomStatusLabel(int $status): string
    {
        return match ($status) {
            0 => 'Trống',
            1 => 'Đã đặt',
            2 => 'Đang sử dụng',
            3 => 'Đang dọn dẹp',
            default => 'Không xác định',
        };
    }

    private function currentRoomStatus(Phong $room): int
    {
        $today = Carbon::today()->startOfDay();

        $detailsToday = $room->chiTietDatPhong
            ->filter(function (ChiTietDatPhong $detail) use ($today) {
                $booking = $detail->datPhong;

                return $booking
                    && (int) ($detail->TrangThai ?? -1) !== ChiTietDatPhong::CANCELLED
                    && in_array((int) ($booking->TinhTrang ?? -1), [0, 1, 2], true)
                    && Carbon::parse($booking->NgayNhanPhong)->startOfDay()->lte($today)
                    && Carbon::parse($booking->NgayTraPhong)->startOfDay()->gte($today);
            });

        if ($detailsToday->contains(fn ($detail) => (int) $detail->TrangThai === ChiTietDatPhong::CHECKED_IN)) {
            return 2;
        }

        if ($detailsToday->contains(fn ($detail) => (int) $detail->TrangThai === ChiTietDatPhong::BOOKED)) {
            return 1;
        }

        return 0;
    }
}
