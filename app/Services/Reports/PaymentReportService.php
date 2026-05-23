<?php

namespace App\Services\Reports;

use App\Models\ThanhToan;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PaymentReportService
{
    public function rows(string $from, string $to): Collection
    {
        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate = Carbon::parse($to)->endOfDay();

        $rows = ThanhToan::with('hoaDon.datPhong.khachHang')
            ->whereBetween('NgayThanhToan', [$fromDate, $toDate])
            ->orderBy('NgayThanhToan')
            ->orderBy('MaTT')
            ->get()
            ->map(fn (ThanhToan $payment) => [
                'payment_id' => $payment->MaTT,
                'invoice_id' => $payment->MaHD,
                'payment_date' => $payment->NgayThanhToan ? Carbon::parse($payment->NgayThanhToan)->format('d/m/Y H:i') : '',
                'customer_name' => $payment->hoaDon?->datPhong?->khachHang?->TenKH ?? 'Khách lẻ',
                'amount' => round((float) ($payment->SoTien ?? 0)),
                'method' => $this->paymentMethodLabel((int) ($payment->PhuongThuc ?? 0)),
                'payment_type' => $this->paymentTypeLabel((int) ($payment->LoaiThanhToan ?? -1)),
                'provider' => $payment->NhaCungCap ?? 'manual',
                'transaction_status' => $this->transactionStatusLabel((int) ($payment->TrangThaiGiaoDich ?? 1)),
            ]);

        if ($rows->isEmpty()) {
            return $rows;
        }

        return $rows->push([
            'payment_id' => 'Tổng cộng',
            'invoice_id' => '',
            'payment_date' => '',
            'customer_name' => '',
            'amount' => $rows->sum('amount'),
            'method' => '',
            'payment_type' => '',
            'provider' => '',
            'transaction_status' => '',
        ]);
    }

    private function paymentMethodLabel(int $method): string
    {
        return match ($method) {
            1 => 'Thẻ',
            2 => 'QR Code',
            default => 'Khác',
        };
    }

    private function paymentTypeLabel(int $type): string
    {
        return match ($type) {
            0 => 'Đặt cọc',
            1 => 'Thanh toán checkout',
            default => 'Không xác định',
        };
    }

    private function transactionStatusLabel(int $status): string
    {
        return match ($status) {
            0 => 'Đang xử lý',
            1 => 'Thành công',
            2 => 'Thất bại',
            default => 'Không xác định',
        };
    }
}
