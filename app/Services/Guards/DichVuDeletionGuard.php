<?php

namespace App\Services\Guards;

use App\Models\DichVu;

class DichVuDeletionGuard extends AbstractDeletionGuard
{
    public function canDelete(DichVu $dichVu): array
    {
        $usageCount = $dichVu->suDungs()->count();

        if ($usageCount > 0) {
            return $this->deny("Dịch vụ đã có {$usageCount} lượt sử dụng nên không thể xóa.");
        }

        return $this->allow();
    }
}
