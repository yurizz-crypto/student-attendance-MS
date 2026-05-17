<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceSessionsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(protected $sessions) {}

    public function title(): string
    {
        return 'Attendance Sessions';
    }

    public function collection()
    {
        return $this->sessions;
    }

    public function headings(): array
    {
        return ['Session ID', 'Class', 'Subject', 'Date', 'Start Time', 'End Time', 'Status'];
    }

    public function map($session): array
    {
        return [
            $session->id,
            $session->classSection?->name ?? '—',
            $session->classSection?->subject?->name ?? '—',
            $session->date,
            $session->start_time,
            $session->end_time,
            ucfirst($session->status ?? ''),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}
