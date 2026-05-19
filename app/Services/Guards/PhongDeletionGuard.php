<?php

namespace App\Services\Guards;

use App\Models\Phong;

class PhongDeletionGuard extends AbstractDeletionGuard
{
    public function canDelete(Phong $phong): array
    {
        $bookingDetailCount = $phong->chiTietDatPhong()->count();

        if ($bookingDetailCount > 0) {
            return $this->deny("Phòng đã có {$bookingDetailCount} chi tiết đặt phòng nên không thể xóa.");
        }

        $stayCount = $phong->luuTrus()->count();
        if ($stayCount > 0) {
            return $this->deny("Phòng đã có {$stayCount} lượt lưu trú nên không thể xóa.");
        }

        return $this->allow();
    }
}
