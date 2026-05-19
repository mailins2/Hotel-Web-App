<?php

namespace App\Services\Guards;

use App\Models\NhanVien;

class NhanVienDeletionGuard extends AbstractDeletionGuard
{
    public function resolveAction(NhanVien $nhanVien): array
    {
        $invoiceCount = $nhanVien->hoaDons()->count();

        if ($invoiceCount > 0) {
            return [
                'action' => 'deactivate',
                'allowed' => true,
                'message' => "Nhân viên đã có {$invoiceCount} hóa đơn. Đã khóa tài khoản liên quan.",
            ];
        }

        return [
            'action' => 'delete',
            'allowed' => true,
            'message' => '',
        ];
    }
}
