<?php

namespace App\Http\Controllers\Faculty;

use App\Exports\FacultyClassExport;
use App\Http\Controllers\Controller;
use App\Models\ClassSection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $studentStats = [];
        $studentIds = $enrolledStudents->pluck('id')->toArray();

        // Use DB aggregations instead of in-memory collection methods for handling large datasets
        $attendanceStats = DB::table('attendance_records')
            ->join('attendance_sessions', 'attendance_records.attendance_session_id', '=', 'attendance_sessions.id')
            ->where('attendance_sessions.class_section_id', $classSection->id)
            ->whereIn('attendance_records.student_id', $studentIds)
            ->select('attendance_records.student_id', 'attendance_records.status', DB::raw('count(*) as count'))
            ->groupBy('attendance_records.student_id', 'attendance_records.status')
            ->get()
            ->groupBy('student_id');

        foreach ($enrolledStudents as $student) {
            $stats = $attendanceStats->get($student->id, collect());

            $present = $stats->firstWhere('status', 'present')->count ?? 0;
            $late = $stats->firstWhere('status', 'late')->count ?? 0;
            $excused = $stats->firstWhere('status', 'excused')->count ?? 0;
            $absent = $stats->firstWhere('status', 'absent')->count ?? 0;

            $total = $present + $late + $excused + $absent;
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
