<?php

namespace App\Exports;

use App\Exports\Concerns\WithReportMetadata;
use App\Services\Reports\RevenueReportService;
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

class RevenueReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles, WithTitle, WithCustomStartCell, WithEvents
{
    use WithReportMetadata;

    public function __construct(
        private readonly string $from,
        private readonly string $to,
        private readonly string $period = 'day',
        private readonly string $exportedBy = 'Admin',
    ) {
    }

    public function collection(): Collection
    {
        return app(RevenueReportService::class)
            ->rows($this->from, $this->to, $this->period)
            ->map(fn (array $row) => [
                $row['date'],
                $row['invoice_count'],
                $row['completed_booking_count'],
                $row['room_revenue'],
                $row['service_revenue'],
                $row['compensation'],
                $row['discount'],
                $row['total_revenue'],
                $row['paid'],
                $row['debt'],
                $row['main_payment_method'],
            ]);
    }

    public function headings(): array
    {
        return [
            'Ngày',
            'Số hóa đơn',
            'Số booking hoàn thành',
            'Doanh thu tiền phòng',
            'Doanh thu dịch vụ',
            'Tiền đền bù',
            'Tổng giảm giá',
            'Tổng doanh thu',
            'Đã thanh toán',
            'Công nợ',
            'Phương thức thanh toán chính',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $highestRow = $sheet->getHighestRow();

        if ($highestRow > 9) {
            $sheet->getStyle('A' . $highestRow . ':K' . $highestRow)->getFont()->setBold(true);
        }

        return [];
    }

    public function title(): string
    {
        return 'Báo cáo doanh thu';
    }

    protected function reportTitle(): string
    {
        return 'BÁO CÁO DOANH THU';
    }

    protected function reportColumnRange(): string
    {
        return 'A:K';
    }
}
