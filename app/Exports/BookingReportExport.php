<?php

namespace App\Exports;

use App\Exports\Concerns\WithReportMetadata;
use App\Services\Reports\BookingReportService;
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

class BookingReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles, WithTitle, WithCustomStartCell, WithEvents
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
        return app(BookingReportService::class)
            ->rows($this->from, $this->to)
            ->map(fn (array $row) => [
                $row['booking_id'],
                $row['booking_date'],
                $row['customer_name'],
                $row['phone'],
                $row['check_in'],
                $row['check_out'],
                $row['room_count'],
                $row['room_list'],
                $row['estimated_total'],
                $row['status'],
            ]);
    }

    public function headings(): array
    {
        return [
            'Mã đặt phòng',
            'Ngày đặt',
            'Khách hàng',
            'SĐT',
            'Ngày nhận',
            'Ngày trả',
            'Số lượng phòng',
            'Danh sách phòng',
            'Tổng tiền dự kiến',
            'Trạng thái',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Báo cáo đặt phòng';
    }

    protected function reportTitle(): string
    {
        return 'BÁO CÁO ĐẶT PHÒNG';
    }

    protected function reportColumnRange(): string
    {
        return 'A:J';
    }
}
