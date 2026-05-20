<?php

namespace App\Services\Guards;

use App\Models\LoaiPhong;
use Illuminate\Support\Facades\DB;

class LoaiPhongDeletionGuard extends AbstractDeletionGuard
{
    public function canDelete(LoaiPhong $loaiPhong): array
    {
        $blockingMessages = [];
        $roomIds = $loaiPhong->phongs()->pluck('MaPhong');

        $bookingDetailCount = DB::table('ChiTietDatPhong')
            ->whereIn('MaPhong', $roomIds)
            ->count();
        if ($bookingDetailCount > 0) {
            $blockingMessages[] = "{$bookingDetailCount} chi tiết đặt phòng";
        }

        $stayCount = DB::table('LuuTru')
            ->whereIn('MaPhong', $roomIds)
            ->count();
        if ($stayCount > 0) {
            $blockingMessages[] = "{$stayCount} lưu trú";
        }

        if ($blockingMessages !== []) {
            return $this->deny(
                'Loại phòng đã có dữ liệu ' . implode(', ', $blockingMessages) . ' nên không thể xóa.'
            );
        }

        return $this->allow();
    }
}
