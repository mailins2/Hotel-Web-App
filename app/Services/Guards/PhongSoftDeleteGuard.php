<?php

namespace App\Services\Guards;

use App\Models\DatPhong;
use App\Models\ChiTietDatPhong;
use App\Models\Phong;

class PhongSoftDeleteGuard extends AbstractSoftDeleteGuard
{
    public function canSoftDelete(Phong $phong): array
    {
        $activeBookingCount = $phong->chiTietDatPhong()
            ->where('TrangThai', '!=', ChiTietDatPhong::CANCELLED)
            ->whereHas('datPhong', function ($query) {
                $query->whereIn('TinhTrang', [
                    DatPhong::HOLD,
                    DatPhong::CONFIRMED,
                    DatPhong::CHECKED_IN,
                ]);
            })
            ->count();

        if ($activeBookingCount > 0) {
            return $this->deny("Không thể đưa phòng vào thùng rác vì vẫn còn {$activeBookingCount} đặt phòng đang sử dụng phòng này.");
        }

        return $this->allow();
    }

    public function canForceDelete(Phong $phong): array
    {
        $bookingDetailCount = $phong->chiTietDatPhong()->count();

        if ($bookingDetailCount > 0) {
            return $this->deny("Không thể xóa vĩnh viễn phòng vì vẫn còn {$bookingDetailCount} chi tiết đặt phòng tham chiếu tới phòng này.");
        }

        return $this->allow();
    }
}
