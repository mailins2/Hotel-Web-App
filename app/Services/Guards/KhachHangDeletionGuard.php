<?php

namespace App\Services\Guards;

use App\Models\KhachHang;

class KhachHangDeletionGuard extends AbstractDeletionGuard
{
    public function canDelete(KhachHang $khachHang): array
    {
        $bookingCount = $khachHang->datPhongs()->count();

        if ($bookingCount > 0) {
            return $this->deny("Khách hàng đã có {$bookingCount} đơn đặt phòng nên không thể xóa.");
        }

        return $this->allow();
    }
}
