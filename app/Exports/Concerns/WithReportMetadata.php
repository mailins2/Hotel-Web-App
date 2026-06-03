<?php

namespace App\Exports\Concerns;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

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
                $highestRow = $sheet->getHighestRow();
                $tableRange = $this->reportRowRange(9, $highestRow);

                $sheet->mergeCells($this->reportRowRange(1));
                $sheet->mergeCells($this->reportRowRange(2));
                $sheet->mergeCells($this->reportRowRange(3));
                $sheet->mergeCells($this->reportRowRange(4));
                $sheet->mergeCells($this->reportRowRange(5));
                $sheet->mergeCells($this->reportRowRange(7));

                $sheet->setCellValue('A1', $this->hotelName());
                $sheet->setCellValue('A2', 'Địa chỉ: ' . $this->hotelAddress());
                $sheet->setCellValue('A3', 'Người xuất: ' . $this->exportedBy());
                $sheet->setCellValue('A4', 'Thời gian xuất: ' . Carbon::now()->format('d/m/Y H:i:s'));
                $sheet->setCellValue('A5', 'Khoảng thời gian báo cáo: ' . $this->formatReportDate($this->from) . ' đến ' . $this->formatReportDate($this->to));
                $sheet->setCellValue('A7', $this->reportTitle());

                $sheet->getParent()->getDefaultStyle()->getFont()
                    ->setName('Segoe UI')
                    ->setSize(10);
                $sheet->setShowGridlines(false);
                $sheet->getTabColor()->setARGB('FF2F5D3A');

                $sheet->getStyle($range)->getAlignment()->setWrapText(true);
                $sheet->getStyle($this->reportRowRange(1, 5))->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFF7FAF5');
                $sheet->getStyle('A1:A5')->getFont()->setBold(true)->getColor()->setARGB('FF263A2A');
                $sheet->getStyle('A1:A5')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle($this->reportRowRange(7))->getFont()
                    ->setBold(true)
                    ->setSize(18)
                    ->getColor()
                    ->setARGB('FF1F2D23');
                $sheet->getStyle($this->reportRowRange(7))->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle($headerRange)->getFont()
                    ->setBold(true)
                    ->setSize(11)
                    ->getColor()
                    ->setARGB('FFFFFFFF');
                $sheet->getStyle($headerRange)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FF2F5D3A');
                $sheet->getStyle($headerRange)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle($headerRange)->getBorders()->getBottom()
                    ->setBorderStyle(Border::BORDER_MEDIUM)
                    ->getColor()
                    ->setARGB('FF1F3E28');
                $sheet->getStyle($headerRange)->getBorders()->getTop()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()
                    ->setARGB('FFB8C6B8');
                $this->uppercaseHeaderRow($sheet);
                $sheet->getStyle($tableRange)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);
                $sheet->getStyle($tableRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()
                    ->setARGB('FFFFFFFF');
                $sheet->getStyle($tableRange)->getBorders()->getOutline()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()
                    ->setARGB('FFD6DDD2');
                $this->styleReportRows($sheet, $highestRow);
                $sheet->getRowDimension(7)->setRowHeight(28);
                $sheet->getRowDimension(9)->setRowHeight(30);
            },
        ];
    }

    private function hotelName(): string
    {
        return 'Khách sạn Peach Valley';
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

    private function styleReportRows(Worksheet $sheet, int $highestRow): void
    {
        if ($highestRow < 10) {
            return;
        }

        for ($row = 10; $row <= $highestRow; $row++) {
            $fillColor = ($row - 10) % 2 === 0 ? 'FFF7F7F5' : 'FFFFFFFF';

            $sheet->getStyle($this->reportRowRange($row))->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB($fillColor);
            $sheet->getRowDimension($row)->setRowHeight(23);
        }

        $sheet->getStyle($this->reportRowRange(10, $highestRow))->getFont()
            ->getColor()
            ->setARGB('FF263A2A');

        $lastColumn = explode(':', $this->reportColumnRange(), 2)[1];
        $lastColumnIndex = Coordinate::columnIndexFromString($lastColumn);

        for ($column = 1; $column <= $lastColumnIndex; $column++) {
            $letter = Coordinate::stringFromColumnIndex($column);
            $sheet->getColumnDimension($letter)->setAutoSize(true);
        }
    }

    private function uppercaseHeaderRow(Worksheet $sheet): void
    {
        $lastColumn = explode(':', $this->reportColumnRange(), 2)[1];
        $lastColumnIndex = Coordinate::columnIndexFromString($lastColumn);

        for ($column = 1; $column <= $lastColumnIndex; $column++) {
            $cell = Coordinate::stringFromColumnIndex($column) . '9';
            $value = $sheet->getCell($cell)->getValue();

            if (is_string($value)) {
                $sheet->setCellValue($cell, $this->uppercaseVietnamese($value));
            }
        }
    }

    private function uppercaseVietnamese(string $value): string
    {
        $lowercaseVietnamese = 'àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ';
        $uppercaseVietnamese = 'ÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴÈÉẸẺẼÊỀẾỆỂỄÌÍỊỈĨÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠÙÚỤỦŨƯỪỨỰỬỮỲÝỴỶỸĐ';
        $lowercaseChars = preg_split('//u', $lowercaseVietnamese, -1, PREG_SPLIT_NO_EMPTY);
        $uppercaseChars = preg_split('//u', $uppercaseVietnamese, -1, PREG_SPLIT_NO_EMPTY);
        $map = array_combine($lowercaseChars, $uppercaseChars);

        return strtoupper(strtr($value, $map));
    }

    abstract protected function reportTitle(): string;

    abstract protected function reportColumnRange(): string;
}
