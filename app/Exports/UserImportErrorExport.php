<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserImportErrorExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected $invalidRows;

    public function __construct($invalidRows)
    {
        $this->invalidRows = collect($invalidRows);
    }

    public function collection()
    {
        return $this->invalidRows;
    }

    public function headings(): array
    {
        return [
            'Row',
            'First Name',
            'Middle Name',
            'Last Name',
            'Identity ID',
            'Email',
            'Role',
            'Error Reason',
        ];
    }

    public function map($row): array
    {
        return [
            $row['row_index'] ?? 'N/A',
            $row['data']['first_name'] ?? '',
            $row['data']['middle_name'] ?? '',
            $row['data']['last_name'] ?? '',
            $row['data']['identity_id'] ?? '',
            $row['data']['email'] ?? '',
            $row['data']['role'] ?? '',
            $row['error'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
