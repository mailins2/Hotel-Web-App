<?php

namespace App\Services\Reports;

use App\Models\DatPhong;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BookingReportService
{
    public function rows(string $from, string $to): Collection
    {
        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate = Carbon::parse($to)->endOfDay();

        return DatPhong::with(['khachHang', 'chiTietDatPhong.phong', 'hoaDon'])
            ->whereBetween('NgayDat', [$fromDate, $toDate])
            ->orderBy('NgayDat')
            ->orderBy('MaDatPhong')
            ->get()
            ->map(function (DatPhong $booking) {
                $rooms = $booking->chiTietDatPhong
                    ->map(fn ($detail) => $detail->phong?->SoPhong)
                    ->filter()
                    ->unique()
                    ->values();

                return [
                    'booking_id' => $booking->MaDatPhong,
                    'booking_date' => $booking->NgayDat ? Carbon::parse($booking->NgayDat)->format('d/m/Y') : '',
                    'customer_name' => $booking->khachHang?->TenKH ?? 'Khách lẻ',
                    'phone' => $booking->khachHang?->SoDienThoai ?? '',
                    'check_in' => $booking->NgayNhanPhong ? Carbon::parse($booking->NgayNhanPhong)->format('d/m/Y') : '',
                    'check_out' => $booking->NgayTraPhong ? Carbon::parse($booking->NgayTraPhong)->format('d/m/Y') : '',
                    'room_count' => $rooms->count() ?: (int) ($booking->SoLuong ?? 0),
                    'room_list' => $rooms->implode(', '),
                    'estimated_total' => round((float) ($booking->hoaDon?->TongTien ?? 0)),
                    'status' => $this->bookingStatusLabel((int) ($booking->TinhTrang ?? -1)),
                ];
            });
    }

    private function bookingStatusLabel(int $status): string
    {
        return match ($status) {
            DatPhong::HOLD => 'Đang giữ chỗ',
            DatPhong::CONFIRMED => 'Đã xác nhận',
            DatPhong::CHECKED_IN => 'Đang ở',
            DatPhong::CHECKED_OUT => 'Đã trả phòng',
            DatPhong::CANCELLED => 'Đã hủy',
            default => 'Không xác định',
        };
    }
}
