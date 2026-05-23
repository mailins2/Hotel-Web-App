<?php

namespace App\Exports\Concerns;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

trait WithReportMetadata
{
    public function startCell(): string
    {
        return 'A9';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $range = $this->reportColumnRange();
                $headerRange = $this->reportRowRange(9);
                $tableRange = $this->reportRowRange(9, $sheet->getHighestRow());

                $sheet->mergeCells($this->reportRowRange(1));
                $sheet->mergeCells($this->reportRowRange(2));
                $sheet->mergeCells($this->reportRowRange(3));
                $sheet->mergeCells($this->reportRowRange(4));
                $sheet->mergeCells($this->reportRowRange(5));
                $sheet->mergeCells($this->reportRowRange(7));

                $sheet->setCellValue('A1', 'Tên khách sạn: ' . $this->hotelName());
                $sheet->setCellValue('A2', 'Địa chỉ: ' . $this->hotelAddress());
                $sheet->setCellValue('A3', 'Người xuất: ' . $this->exportedBy());
                $sheet->setCellValue('A4', 'Thời gian xuất: ' . Carbon::now()->format('d/m/Y H:i:s'));
                $sheet->setCellValue('A5', 'Khoảng thời gian báo cáo: ' . $this->formatReportDate($this->from) . ' đến ' . $this->formatReportDate($this->to));
                $sheet->setCellValue('A7', $this->reportTitle());

                $sheet->getStyle('A1:A5')->getFont()->setBold(true);
                $sheet->getStyle($this->reportRowRange(7))->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle($this->reportRowRange(7))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
                $sheet->getStyle($headerRange)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFFF6600');
                $sheet->getStyle($headerRange)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle($tableRange)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);
                $sheet->getStyle($tableRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()
                    ->setARGB('FFD9D9D9');
                $sheet->getRowDimension(7)->setRowHeight(28);
                $sheet->getRowDimension(9)->setRowHeight(24);
            },
        ];
    }

    private function hotelName(): string
    {
        return env('HOTEL_NAME', 'Peach Valley Hotel');
    }

    private function hotelAddress(): string
    {
        return env('HOTEL_ADDRESS', '26K đường Yersin, Đà Lạt, Lâm Đồng');
    }

    private function exportedBy(): string
    {
        return $this->exportedBy ?: 'Admin';
    }

    private function formatReportDate(string $date): string
    {
        return Carbon::parse($date)->format('d/m/Y');
    }

    private function reportRowRange(int $row, ?int $endRow = null): string
    {
        [$startColumn, $endColumn] = explode(':', $this->reportColumnRange(), 2);

        return $startColumn . $row . ':' . $endColumn . ($endRow ?? $row);
    }

    abstract protected function reportTitle(): string;

    abstract protected function reportColumnRange(): string;
}
