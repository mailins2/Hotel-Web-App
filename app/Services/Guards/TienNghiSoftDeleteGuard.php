<?php

namespace App\Services\Guards;

use App\Models\TienNghi;

class TienNghiSoftDeleteGuard extends AbstractSoftDeleteGuard
{
    public function canSoftDelete(TienNghi $tienNghi): array
    {
        return $this->allow();
    }

    public function canForceDelete(TienNghi $tienNghi): array
    {
        $roomTypeCount = $tienNghi->loaiPhongs()->withTrashed()->count();

        if ($roomTypeCount > 0) {
            return $this->deny("Không thể xóa vĩnh viễn tiện nghi vì vẫn còn {$roomTypeCount} loại phòng đang gắn tiện nghi này.");
        }

        return $this->allow();
    }
}
