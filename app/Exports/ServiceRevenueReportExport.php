<?php

namespace App\Exports;

use App\Exports\Concerns\WithReportMetadata;
use App\Services\Reports\ServiceRevenueReportService;
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

class ServiceRevenueReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles, WithTitle, WithCustomStartCell, WithEvents
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
        return app(ServiceRevenueReportService::class)
            ->rows($this->from, $this->to)
            ->map(fn (array $row) => [
                $row['service_id'],
                $row['service_name'],
                $row['service_type'],
                $row['unit_price'],
                $row['total_quantity'],
                $row['revenue'],
                $row['revenue_rate'] / 100,
                $row['usage_count'],
                $row['booking_ids'],
            ]);
    }

    public function headings(): array
    {
        return [
            'Mã dịch vụ',
            'Tên dịch vụ',
            'Loại dịch vụ',
            'Đơn giá',
            'Tổng số lượng',
            'Doanh thu',
            'Tỷ lệ doanh thu',
            'Số lượt sử dụng',
            'Mã đặt phòng',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_PERCENTAGE_00,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Báo cáo dịch vụ';
    }

    protected function reportTitle(): string
    {
        return 'BÁO CÁO DOANH THU DỊCH VỤ';
    }

    protected function reportColumnRange(): string
    {
        return 'A:I';
    }
}
