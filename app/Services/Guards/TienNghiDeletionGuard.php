<?php

namespace App\Services\Guards;

use App\Models\TienNghi;

class TienNghiDeletionGuard extends AbstractDeletionGuard
{
    public function canDelete(TienNghi $tienNghi): array
    {
        return $this->allow();
    }
}
