<?php

namespace App\Services\Reports;

use App\Models\HoaDon;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RevenueReportService
{
    public function rows(string $from, string $to, string $period = 'day'): Collection
    {
        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate = Carbon::parse($to)->endOfDay();
        $period = $this->normalizePeriod($period);
        $buckets = $this->buildBuckets($fromDate, $toDate, $period);
        $invoices = $this->invoiceItems($from, $to);

        foreach ($invoices as $invoice) {
            $invoiceDate = $invoice['date'] ? Carbon::parse($invoice['date']) : null;

            if (!$invoiceDate) {
                continue;
            }

            $bucketKey = $this->bucketKey($invoiceDate, $period);

            if (!isset($buckets[$bucketKey])) {
                continue;
            }

            $bucket = &$buckets[$bucketKey];

            $bucket['invoice_ids'][$invoice['invoice_id']] = true;

            if (!empty($invoice['booking_id'])) {
                $bucket['booking_ids'][$invoice['booking_id']] = true;
            }

            $bucket['room_revenue'] += $invoice['room'];
            $bucket['service_revenue'] += $invoice['service'];
            $bucket['compensation'] += $invoice['compensation'];
            $bucket['discount'] += $invoice['discount'];
            $bucket['total_revenue'] += $invoice['total'];
            $bucket['paid'] += $invoice['paid'];
            $bucket['debt'] += $invoice['debt'];

            foreach ($invoice['payment_methods'] as $method => $amount) {
                $bucket['payment_methods'][$method] = ($bucket['payment_methods'][$method] ?? 0) + $amount;
            }

            unset($bucket);
        }

        $rows = collect(array_values($buckets))->map(function (array $bucket) {
            return [
                'date' => $bucket['label'],
                'invoice_count' => count($bucket['invoice_ids']),
                'completed_booking_count' => count($bucket['booking_ids']),
                'room_revenue' => round($bucket['room_revenue']),
                'service_revenue' => round($bucket['service_revenue']),
                'compensation' => round($bucket['compensation']),
                'discount' => round($bucket['discount']),
                'total_revenue' => round($bucket['total_revenue']),
                'paid' => round($bucket['paid']),
                'debt' => round($bucket['debt']),
                'main_payment_method' => $this->mainPaymentMethod($bucket['payment_methods']),
            ];
        });

        return $this->appendTotalRow($rows);
    }

    public function invoiceItems(?string $from = null, ?string $to = null): Collection
    {
        $query = HoaDon::with([
            'chiTietHoaDons',
            'thanhToans',
            'datPhong',
        ])
            ->whereNotNull('NgayLapHD')
            ->where('TrangThai', '!=', 3);

        if ($from && $to) {
            $query->whereBetween('NgayLapHD', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ]);
        }

        return $query
            ->orderBy('NgayLapHD')
            ->get()
            ->map(function (HoaDon $invoice) {
                $invoiceParts = $this->invoiceParts($invoice);
                $successfulPayments = $invoice->thanhToans
                    ->where('TrangThaiGiaoDich', 1);
                $paid = $successfulPayments
                    ->sum(fn ($payment) => (float) ($payment->SoTien ?? 0));
                $paymentMethods = [];

                foreach ($successfulPayments as $payment) {
                    $methodLabel = $this->paymentMethodLabel((int) ($payment->PhuongThuc ?? 0));
                    $paymentMethods[$methodLabel] = ($paymentMethods[$methodLabel] ?? 0)
                        + max((float) ($payment->SoTien ?? 0), 0);
                }

                return [
                    'date' => Carbon::parse($invoice->NgayLapHD)->toDateString(),
                    'invoice_id' => $invoice->MaHD,
                    'booking_id' => $invoice->MaDatPhong,
                    'total' => $invoiceParts['invoice_total'],
                    'room' => $invoiceParts['room_gross'],
                    'service' => $invoiceParts['service_gross'],
                    'compensation' => $invoiceParts['compensation_gross'],
                    'discount' => $invoiceParts['discount'],
                    'paid' => $paid,
                    'debt' => max($invoiceParts['invoice_total'] - $paid, 0),
                    'main_payment_method' => $this->mainPaymentMethod($paymentMethods),
                    'payment_methods' => $paymentMethods,
                ];
            })
            ->values();
    }

    private function invoiceParts($invoice): array
    {
        $details = $invoice->chiTietHoaDons ?? collect();
        $roomGross = $details
            ->filter(fn ($detail) => !empty($detail->MaLoaiPhong))
            ->sum(fn ($detail) => (float) ($detail->SoLuong ?? 1) * (float) ($detail->DonGia ?? 0));
        $serviceGross = $details
            ->filter(fn ($detail) => !empty($detail->MaSuDung))
            ->sum(fn ($detail) => (float) ($detail->SoLuong ?? 1) * (float) ($detail->DonGia ?? 0));
        $compensationGross = $details
            ->filter(fn ($detail) => !empty($detail->MaDenBu))
            ->sum(fn ($detail) => (float) ($detail->SoLuong ?? 1) * (float) ($detail->DonGia ?? 0));
        $invoiceTotal = max((float) ($invoice->TongTien ?? 0), 0);
        $grossTotal = $roomGross + $serviceGross + $compensationGross;

        return [
            'room_gross' => $roomGross,
            'service_gross' => $serviceGross,
            'compensation_gross' => $compensationGross,
            'discount' => max($grossTotal - $invoiceTotal, 0),
            'invoice_total' => $invoiceTotal,
        ];
    }

    private function buildBuckets(Carbon $fromDate, Carbon $toDate, string $period): array
    {
        $buckets = [];
        $cursor = $fromDate->copy()->startOfDay();

        if ($period === 'month') {
            $cursor = $cursor->startOfMonth();
        } elseif ($period === 'quarter') {
            $cursor = $cursor->startOfQuarter();
        } elseif ($period === 'year') {
            $cursor = $cursor->startOfYear();
        }

        while ($cursor <= $toDate) {
            $key = $this->bucketKey($cursor, $period);
            $buckets[$key] = [
                'label' => $this->bucketLabel($cursor, $period),
                'invoice_ids' => [],
                'booking_ids' => [],
                'room_revenue' => 0,
                'service_revenue' => 0,
                'compensation' => 0,
                'discount' => 0,
                'total_revenue' => 0,
                'paid' => 0,
                'debt' => 0,
                'payment_methods' => [],
            ];

            $cursor = match ($period) {
                'month' => $cursor->copy()->addMonthNoOverflow()->startOfMonth(),
                'quarter' => $cursor->copy()->addQuarter()->startOfQuarter(),
                'year' => $cursor->copy()->addYear()->startOfYear(),
                default => $cursor->copy()->addDay(),
            };
        }

        return $buckets;
    }

    private function bucketKey(Carbon $date, string $period): string
    {
        return match ($period) {
            'month' => $date->format('Y-m'),
            'quarter' => $date->format('Y') . '-Q' . $date->quarter,
            'year' => $date->format('Y'),
            default => $date->toDateString(),
        };
    }

    private function bucketLabel(Carbon $date, string $period): string
    {
        return match ($period) {
            'month' => 'Tháng ' . $date->format('m/Y'),
            'quarter' => 'Quý ' . $date->quarter . '/' . $date->format('Y'),
            'year' => 'Năm ' . $date->format('Y'),
            default => $date->format('d/m/Y'),
        };
    }

    private function normalizePeriod(string $period): string
    {
        return in_array($period, ['day', 'month', 'quarter', 'year'], true) ? $period : 'day';
    }

    private function paymentMethodLabel(int $method): string
    {
        return match ($method) {
            1 => 'Thẻ',
            2 => 'QR Code',
            default => 'Khác',
        };
    }

    private function mainPaymentMethod(array $methods): string
    {
        if (!$methods) {
            return '';
        }

        arsort($methods);

        return array_key_first($methods);
    }

    private function appendTotalRow(Collection $rows): Collection
    {
        if ($rows->isEmpty()) {
            return $rows;
        }

        return $rows->push([
            'date' => 'Tổng cộng',
            'invoice_count' => $rows->sum('invoice_count'),
            'completed_booking_count' => $rows->sum('completed_booking_count'),
            'room_revenue' => $rows->sum('room_revenue'),
            'service_revenue' => $rows->sum('service_revenue'),
            'compensation' => $rows->sum('compensation'),
            'discount' => $rows->sum('discount'),
            'total_revenue' => $rows->sum('total_revenue'),
            'paid' => $rows->sum('paid'),
            'debt' => $rows->sum('debt'),
            'main_payment_method' => '',
        ]);
    }
}
