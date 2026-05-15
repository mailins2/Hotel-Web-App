<?php

namespace App\Services\Guards;

use App\Models\KhuyenMai;

class KhuyenMaiSoftDeleteGuard extends AbstractSoftDeleteGuard
{
    public function canSoftDelete(KhuyenMai $khuyenMai): array
    {
        return $this->allow();
    }

    public function canForceDelete(KhuyenMai $khuyenMai): array
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
                'Không thể xóa vĩnh viễn khuyến mãi vì vẫn còn liên kết với ' . implode(', ', $blockingMessages) . '.'
            );
        }

        return $this->allow();
    }
}
