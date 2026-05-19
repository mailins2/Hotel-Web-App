<?php

namespace App\Services\Guards;

use App\Models\KhuyenMai;

class KhuyenMaiDeletionGuard extends AbstractDeletionGuard
{
    public function canDelete(KhuyenMai $khuyenMai): array
    {
        $blockingMessages = [];

        $walletCount = $khuyenMai->khoKhuyenMai()->count();
        if ($walletCount > 0) {
            $blockingMessages[] = "{$walletCount} kho khuyến mãi";
        }

        $invoiceCount = $khuyenMai->hoaDons()->count();
        if ($invoiceCount > 0) {
            $blockingMessages[] = "{$invoiceCount} hóa đơn";
        }

        if ($blockingMessages !== []) {
            return $this->deny(
                'Khuyến mãi đang được dùng trong ' . implode(', ', $blockingMessages) . '.'
            );
        }

        return $this->allow();
    }
}
