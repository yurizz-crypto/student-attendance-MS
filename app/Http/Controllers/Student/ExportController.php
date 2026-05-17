<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Enrollment;
use App\Models\Semester;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    public function export(Request $request)
    {
        $student = Auth::user();

        $activeSemester = Semester::where('is_active', true)->first();

        // Fetch student's enrollments for the active semester
        $enrollments = Enrollment::where('student_id', $student->id)
            ->whereHas('classSection', function ($q) use ($activeSemester) {
                if ($activeSemester) {
                    $q->where('semester_id', $activeSemester->id);
                }
            })
            ->with('classSection.subject')
            ->get();

        // Fetch all attendance records for these classes
        $classIds = $enrollments->pluck('class_section_id')->toArray();

        $records = AttendanceRecord::where('student_id', $student->id)
            ->whereHas('session', function ($q) use ($classIds) {
                $q->whereIn('class_section_id', $classIds);
            })
            ->with('session.classSection.subject')
            ->orderByDesc('created_at')
            ->get();

        $subjectStats = [];
        foreach ($enrollments as $enrollment) {
            $class = $enrollment->classSection;
            $classRecords = $records->where('session.class_section_id', $class->id);

            $total = $classRecords->count();
            $present = $classRecords->where('status', 'present')->count();
            $late = $classRecords->where('status', 'late')->count();
            $excused = $classRecords->where('status', 'excused')->count();
            $absent = $classRecords->where('status', 'absent')->count();

            $attended = $present + $late + $excused;
            $rate = $total > 0 ? round(($attended / $total) * 100) : 100;

            $subjectStats[] = [
                'code' => $class->subject->code ?? 'N/A',
                'name' => $class->subject->name ?? 'Unknown',
                'section' => $class->name,
                'total' => $total,
                'present' => $present,
                'late' => $late,
                'excused' => $excused,
                'absent' => $absent,
                'rate' => $rate.'%',
            ];
        }

        $pdf = Pdf::loadView('exports.pdf.student-report', [
            'student' => $student,
            'semester' => $activeSemester ? $activeSemester->name : 'All Semesters',
            'subjectStats' => $subjectStats,
            'recentRecords' => $records->take(20), // Last 20 records
            'date' => now()->format('F d, Y'),
        ]);

        return $pdf->download('My_Attendance_Report.pdf');
    }
}
