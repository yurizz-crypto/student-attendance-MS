<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FacultyClassExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected $studentStats;

    public function __construct($studentStats)
    {
        $this->studentStats = collect($studentStats);
    }

    public function collection()
    {
        return $this->studentStats;
    }

    public function headings(): array
    {
        return [
            'Student ID',
            'Name',
            'Email',
            'Total Sessions',
            'Present',
            'Late',
            'Excused',
            'Absences',
            'Attendance Rate',
        ];
    }

    public function map($stat): array
    {
        return [
            $stat['id'],
            $stat['name'],
            $stat['email'],
            $stat['total_sessions'],
            $stat['present'],
            $stat['late'],
            $stat['excused'],
            $stat['absences'],
            $stat['rate'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
