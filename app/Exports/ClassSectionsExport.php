<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClassSectionsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(protected $classes) {}

    public function title(): string
    {
        return 'My Classes';
    }

    public function collection()
    {
        return $this->classes;
    }

    public function headings(): array
    {
        return ['Class ID', 'Class Name', 'Subject', 'Semester', 'Schedule', 'Created'];
    }

    public function map($class): array
    {
        return [
            $class->id,
            $class->name,
            $class->subject?->name ?? '—',
            $class->semester?->name ?? '—',
            $class->schedule_details ?? '—',
            $class->created_at?->format('Y-m-d') ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}
