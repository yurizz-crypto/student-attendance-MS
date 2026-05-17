<?php

namespace App\Http\Controllers\Faculty;

use App\Exports\FacultyClassExport;
use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\ClassSection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function export(Request $request, ClassSection $classSection)
    {
        abort_if($classSection->faculty_id !== Auth::id(), 403, 'Unauthorized');

        $format = $request->query('format', 'csv');

        // Fetch data
        $classSection->load('subject', 'semester');

        $enrolledStudents = $classSection->enrollments()->with('student')->get()->pluck('student')->sortBy('last_name');

        $records = AttendanceRecord::whereHas('session', function ($q) use ($classSection) {
            $q->where('class_section_id', $classSection->id);
        })->get();

        $studentStats = [];
        foreach ($enrolledStudents as $student) {
            $studentRecords = $records->where('student_id', $student->id);
            $total = $studentRecords->count();

            // In a real system, you might separate excused vs present vs late.
            // For a basic report, we combine present+late+excused into 'attended' to match standard rate calculations.
            $present = $studentRecords->where('status', 'present')->count();
            $late = $studentRecords->where('status', 'late')->count();
            $excused = $studentRecords->where('status', 'excused')->count();
            $absent = $studentRecords->where('status', 'absent')->count();

            $attended = $present + $late + $excused;
            $rate = $total > 0 ? round(($attended / $total) * 100) : 100;

            $studentStats[] = [
                'id' => $student->identity_id ?? 'N/A',
                'name' => $student->last_name.', '.$student->first_name,
                'email' => $student->email,
                'total_sessions' => $total,
                'present' => $present,
                'late' => $late,
                'excused' => $excused,
                'absences' => $absent,
                'rate' => $rate.'%',
            ];
        }

        $filename = 'Class_Report_'.str_replace(' ', '_', $classSection->subject->code ?? $classSection->id);

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('exports.pdf.faculty-class-report', [
                'classSection' => $classSection,
                'studentStats' => $studentStats,
                'date' => now()->format('F d, Y'),
                'facultyName' => Auth::user()->first_name.' '.Auth::user()->last_name,
            ]);

            return $pdf->download($filename.'.pdf');
        } elseif ($format === 'excel') {
            return Excel::download(new FacultyClassExport($studentStats), $filename.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }

        // Default to CSV using Maatwebsite Excel
        return Excel::download(new FacultyClassExport($studentStats), $filename.'.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
