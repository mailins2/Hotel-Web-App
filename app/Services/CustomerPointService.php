<?php

namespace App\Services;

use App\Models\HoaDon;

class CustomerPointService
{
    private const VND_PER_POINT = 100000;

    public function addPointsForPayment(HoaDon $hoaDon, float $amount): int
    {
        $points = (int) floor($amount / self::VND_PER_POINT);

        if ($points <= 0) {
            return 0;
        }

        $hoaDon->loadMissing('datPhong.khachHang');
        $khachHang = $hoaDon->datPhong?->khachHang;

        if (!$khachHang) {
            return 0;
        }

        $khachHang->increment('DIEM', $points);

        return $points;
    }
}
