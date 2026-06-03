<?php

namespace App\Services\Guards;

use App\Models\TaiKhoan;

class TaiKhoanDeletionGuard extends AbstractDeletionGuard
{
    public function resolveAction(TaiKhoan $taiKhoan): array
    {
        $blockingMessages = [];

        $customerBookingCount = $taiKhoan->khachHang?->datPhongs()->count() ?? 0;
        if ($customerBookingCount > 0) {
            $blockingMessages[] = "{$customerBookingCount} đặt phòng của khách hàng";
        }

        $employeeInvoiceCount = $taiKhoan->nhanVien?->hoaDons()->count() ?? 0;
        if ($employeeInvoiceCount > 0) {
            $blockingMessages[] = "{$employeeInvoiceCount} hóa đơn nhân viên đã xử lý";
        }

        if ($blockingMessages !== []) {
            return [
                'action' => 'deactivate',
                'allowed' => true,
                'message' => 'Tài khoản có dữ liệu liên quan: ' . implode(', ', $blockingMessages) . '. Tài khoản đã bị khóa, không bị xóa khỏi hệ thống.',
            ];
        }

        return [
            'action' => 'delete',
            'allowed' => true,
            'message' => '',
        ];
    }
}
