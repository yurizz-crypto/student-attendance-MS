<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DynamicReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected $rows;

    protected $headers;

    public function __construct($rows, $headers)
    {
        $this->rows = collect($rows);
        $this->headers = $headers;
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function map($row): array
    {
        // $row is an associative array, we just need to return its values in the order of headers
        return array_values($row);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
