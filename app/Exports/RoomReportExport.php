<?php

namespace App\Exports;

use App\Exports\Concerns\WithReportMetadata;
use App\Services\Reports\RoomReportService;
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

class RoomReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles, WithTitle, WithCustomStartCell, WithEvents
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
        return app(RoomReportService::class)
            ->rows($this->from, $this->to)
            ->map(fn (array $row) => [
                $row['room_id'],
                $row['room_number'],
                $row['room_type'],
                $row['room_price'],
                $row['current_status'],
                $row['booking_count'],
                $row['rented_days'],
                $row['room_revenue'],
                $row['occupancy_rate'] === '' ? '' : $row['occupancy_rate'] / 100,
            ]);
    }

    public function headings(): array
    {
        return [
            'Mã phòng',
            'Số phòng',
            'Loại phòng',
            'Giá phòng',
            'Tình trạng hiện tại',
            'Số lượt đặt',
            'Số ngày được thuê',
            'Doanh thu phòng',
            'Công suất theo kỳ',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_PERCENTAGE_00,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $highestRow = $sheet->getHighestRow();

        for ($row = 10; $row <= $highestRow; $row++) {
            if ($sheet->getCell('A' . $row)->getValue() === 'Tổng cộng') {
                $sheet->getStyle('A' . $row . ':I' . $row)->getFont()->setBold(true);
                $sheet->getStyle('A' . $row . ':I' . $row)->getFill()
                    ->setFillType('solid')
                    ->getStartColor()
                    ->setARGB('FFFFF1E8');
            }
        }

        return [];
    }

    public function title(): string
    {
        return 'Báo cáo phòng';
    }

    protected function reportTitle(): string
    {
        return 'BÁO CÁO PHÒNG';
    }

    protected function reportColumnRange(): string
    {
        return 'A:I';
    }
}
