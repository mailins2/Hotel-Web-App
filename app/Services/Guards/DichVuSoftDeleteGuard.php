<?php

namespace App\Services\Guards;

use App\Models\DichVu;

class DichVuSoftDeleteGuard extends AbstractSoftDeleteGuard
{
    public function canSoftDelete(DichVu $dichVu): array
    {
        return $this->allow();
    }

    public function canForceDelete(DichVu $dichVu): array
    {
        $usageCount = $dichVu->suDungs()->count();

        if ($usageCount > 0) {
            return $this->deny("Không thể xóa vĩnh viễn dịch vụ vì vẫn còn {$usageCount} lượt sử dụng dịch vụ tham chiếu tới dịch vụ này.");
        }

        return $this->allow();
    }
}
