<?php

namespace App\Services\Guards;

use App\Models\LoaiPhong;

class LoaiPhongSoftDeleteGuard extends AbstractSoftDeleteGuard
{
    public function canSoftDelete(LoaiPhong $loaiPhong): array
    {
        $activeRoomCount = $loaiPhong->phongs()->count();

        if ($activeRoomCount > 0) {
            return $this->deny("Không thể đưa loại phòng vào thùng rác vì vẫn còn {$activeRoomCount} phòng đang sử dụng loại phòng này.");
        }

        return $this->allow();
    }

    public function canForceDelete(LoaiPhong $loaiPhong): array
    {
        $blockingMessages = [];

        $roomCount = $loaiPhong->phongs()->withTrashed()->count();
        if ($roomCount > 0) {
            $blockingMessages[] = "{$roomCount} phòng";
        }

        $invoiceDetailCount = $loaiPhong->chiTietHoaDons()->count();
        if ($invoiceDetailCount > 0) {
            $blockingMessages[] = "{$invoiceDetailCount} chi tiết hóa đơn";
        }

        if ($blockingMessages !== []) {
            return $this->deny(
                'Không thể xóa vĩnh viễn loại phòng vì vẫn còn liên kết với ' . implode(', ', $blockingMessages) . '.'
            );
        }

        return $this->allow();
    }
}
