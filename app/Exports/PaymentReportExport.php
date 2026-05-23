<?php

namespace App\Exports;

use App\Exports\Concerns\WithReportMetadata;
use App\Services\Reports\PaymentReportService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles, WithTitle, WithCustomStartCell, WithEvents
{
    use WithReportMetadata;

    public function __construct(
        private readonly string $from,
        private readonly string $to,
        private readonly string $exportedBy = 'Admin',
    ) {
    }

    public function collection(): Collection
    {
        return app(PaymentReportService::class)
            ->rows($this->from, $this->to)
            ->map(fn (array $row) => [
                $row['payment_id'],
                $row['invoice_id'],
                $row['payment_date'],
                $row['customer_name'],
                $row['amount'],
                $row['method'],
                $row['payment_type'],
                $row['provider'],
                $row['transaction_status'],
            ]);
    }

    public function headings(): array
    {
        return [
            'Mã thanh toán',
            'Mã HĐ',
            'Ngày thanh toán',
            'Khách hàng',
            'Số tiền',
            'Phương thức',
            'Loại thanh toán',
            'Nhà cung cấp',
            'Trạng thái giao dịch',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $highestRow = $sheet->getHighestRow();

        if ($highestRow > 9) {
            $sheet->getStyle('A' . $highestRow . ':I' . $highestRow)->getFont()->setBold(true);
        }

        return [];
    }

    public function title(): string
    {
        return 'Báo cáo thanh toán';
    }

    protected function reportTitle(): string
    {
        return 'BÁO CÁO THANH TOÁN';
    }

    protected function reportColumnRange(): string
    {
        return 'A:I';
    }
}
